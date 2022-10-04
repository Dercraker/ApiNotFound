<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Error;
use App\Entity\Pictures;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {

    //Get Json data from file and decode it
    $json = file_get_contents(__DIR__ . '/pictures.json');
    $picturesList = json_decode($json, true);

    // Itearate on picturesList and create new picture for each item
    for ($i = 0; $i < count($picturesList); $i++) {
      $value = $picturesList[$i];

      $picture = new Pictures();
      $picture->setCode($value['code'])
        ->setPictureLink($value['picture'])
        ->setStatus(true);
      $pictureList[] = $picture;
      $manager->persist($picture);
    }


    //Get Json data from file and decode it
    $json = file_get_contents(__DIR__ . '/codes.json');
    $errorsList = json_decode($json, true);

    // Itearate on data and create new Error entity for each item
    for ($i = 0; $i < count($errorsList); $i++) {
      $value = $errorsList[$i];

      $error = new Error();
      $error->setCode($value['status'])
        ->setMessage($value['message'])
        ->setStatus(true)
        ->addPicture($pictureList[array_rand($pictureList)]);
      $manager->persist($error);
    }


    $manager->flush();
  }
}
