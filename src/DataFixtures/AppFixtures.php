<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Error;
use App\Entity\Messages;
use App\Entity\Pictures;
use App\Repository\MessagesRepository;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {

    //Get Json data from file and decode it
    $json = file_get_contents(__DIR__ . '/codes.json');
    $codesList = json_decode($json, true);

    $listMessage = [];
    for ($i = 0; $i < count($codesList); $i++) {
      $code = $codesList[$i];

      $message = new Messages();
      $message->setMessage($code['message'])
        ->setStatus(true);
      $listMessage[] = $message;
      $manager->persist($message);

      $error = new Error();
      $error->setCode($code['code'])
        ->addMessage($message)
        ->setStatus(true);
      $manager->persist($error);
    }


    $manager->flush();
  }
}
