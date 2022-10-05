<?php

namespace App\Controller;

use App\Entity\Pictures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PictureController extends AbstractController
{

  #[Route('/api/picture', name: 'picture.create', methods: ['POST'])]
  public function createPicture(Request $request, EntityManagerInterface $entityManager): JsonResponse
  {
    $picture = new Pictures();
    $file = $request->files->get('file');
    $picture->setFile($file)
      ->setRealName($file->getClientOriginalName())
      ->setMimeType($file->getClientMimeType())
      //TODO: mettre en variable le path
      ->setPublicPath('/assets/pictures')
      ->setStatus(true)
      ->setUploadDate(new \DateTime());

    $entityManager->persist($picture);
    $entityManager->flush();
    return new JsonResponse(null, Response::HTTP_OK, [], false);
  }
}
