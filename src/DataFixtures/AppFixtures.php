<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Error;
use App\Entity\Message;
use App\Entity\Pictures;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  private $userPasswordHasher;
  public function __construct(UserPasswordHasherInterface $userPasswordHasher)
  {
    $this->faker = Factory::create('fr_FR');
    $this->userPasswordHasher = $userPasswordHasher;
  }



  public function load(ObjectManager $manager): void
  {
    //* Authentified Users
    for ($i = 0; $i < 10; $i++) {
      $userUser = new User();
      $password = $this->faker->password(2, 6);
      $userUser->setUsername($this->faker->userName() . '@' . $password)
        ->setRoles(["ROLE_USER"])
        ->setPassword($this->userPasswordHasher->hashPassword($userUser, $password));
      $manager->persist($userUser);
    }

    $adminUser = new User();
    $adminUser->setUsername("ADMIN")
      ->setRoles(["ROLE_ADMIN"])
      ->setPassword($this->userPasswordHasher->hashPassword($adminUser, "password"));
    $manager->persist($adminUser);

    //Get Json data from file and decode it
    $json = file_get_contents(__DIR__ . '/codes.json');
    $codesList = json_decode($json, true);


    $date = new DateTimeImmutable();


    for ($i = 0; $i < count($codesList); $i++) {
      $code = $codesList[$i];
      $message = new Message();
      $message->setText($code['message'])
        ->setCode($code['code'])
        ->setStatus(true);
      $manager->persist($message);

      $dogPicture = new Pictures();
      $dogPicture->setRealName($code['dogName'])
        ->setPublicPath('/assets/pictures')
        ->setMimeType('image/jpeg')
        ->setUploadDate($date)
        ->setRealPath($code['dogName'])
        ->setStatus(true);
      $manager->persist($dogPicture);

      $catPicture = new Pictures();
      $catPicture->setRealName($code['catName'])
        ->setPublicPath('/assets/pictures')
        ->setMimeType('image/jpeg')
        ->setUploadDate($date)
        ->setRealPath($code['catName'])
        ->setStatus(true);
      $manager->persist($catPicture);

      $error = new Error();
      $error->setCode($code['code'])
        ->addPicture($dogPicture)
        ->addPicture($catPicture)
        ->addMessage($message)
        ->setStatus(true);
      $manager->persist($error);
    }


    $manager->flush();
  }
}
