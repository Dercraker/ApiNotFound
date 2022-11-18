<?php

namespace App\Controller;

use App\Entity\Pictures;
use App\Entity\Error;
use App\Repository\ErrorRepository;
use App\Repository\PictureRepository;
use App\Repository\PicturesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[OA\Tag(name: 'Picture')]
#[Security(name: 'Bearer')]
class PictureController extends AbstractController
{
  /**
   * It takes a picture, serializes it, and returns it as a JSON response
   * 
   * @method GET getPicture()
   * 
   * @param Pictures pictures The entity that will be returned
   * @param SerializerInterface serializerInterface This is the serializer that will be used to
   * serialize the entity.
   * @param UrlGeneratorInterface urlGenerator This is the service that generates the URL for the
   * picture.
   * 
   * @return JsonResponse A JsonResponse object.
   */
  #[OA\Parameter(
    name: 'pictureId',
    in: 'path',
    description: 'Id de l\'image',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 200,
    description: 'Retourne une image',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Pictures::class,
          groups: ['GetPicture']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/api/pictures/{pictureId}', name: 'pictures.get', methods: ['GET'])]
  #[ParamConverter('pictures', options: ['id' => 'pictureId'])]
  public function getPicture(Pictures $pictures, SerializerInterface $serializerInterface, Request $request): JsonResponse
  {

    $RlLocation = $pictures->getPublicPath() . '/' . $pictures->getRealPath();
    $location = $request->getUriForPath('/');
    $location = $location . str_replace('/assets', 'assets', $RlLocation);

    if ($pictures->isStatus() == false) {
      return new JsonResponse("Picture not found", Response::HTTP_NOT_FOUND, [], true);
    }

    $context = SerializationContext::create()->setGroups(['GetPicture']);
    $jsonPicture = $serializerInterface->serialize($pictures, 'json', $context);
    return new JsonResponse(
      $jsonPicture,
      Response::HTTP_OK,
      ['accept' => 'json', 'location' => $location],
      true
    );
  }

  /**
   * It takes a picture, serializes it, and returns it as a JSON response
   *
   * @method GET getPictureByErrorCode()
   * 
   * @param string $pictureCode
   * @param ErrorRepository $errorRepository
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'pictureCode',
    in: 'path',
    description: 'Code de l\'image',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 200,
    description: 'Retourne une image',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Pictures::class,
          groups: ['GetPicture']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/api/pictures/code/{pictureCode}', name: 'pictures.getByErrorCode', methods: ['GET'])]
  #[ParamConverter('pictures', options: ['id' => 'pictureCode'])]
  public function getPictureByErrorCode(string $pictureCode, ErrorRepository $errorRepository, SerializerInterface $serializerInterface): JsonResponse
  {
    $error = $errorRepository->findOneBy(['Code' => $pictureCode]);
    if ($error == null) {
      return new JsonResponse("Picture not found", Response::HTTP_NOT_FOUND, [], true);
    }

    $pictures = $error->getPictures()->toArray();

    $context = SerializationContext::create()->setGroups(['GetPicture']);
    $jsonPicture = $serializerInterface->serialize($pictures, 'json', $context);
    return new JsonResponse(
      $jsonPicture,
      Response::HTTP_OK,
      [],
      true
    );
  }


  /**
   * It creates a new picture, saves it in the database and returns the picture's data in JSON format
   *
   * @method POST createPicture()
   * 
   * @param Request request The request object.
   * @param EntityManagerInterface em The entity manager
   * @param SerializerInterface serializer The serializer service
   * @param UrlGeneratorInterface urlGenerator This is the service that generates URLs for us.
   * 
   * @return JsonResponse A JsonResponse object with the following properties:
   * - The serialized picture object
   * - The HTTP status code
   * - The headers
   * - The option to return the response as an array
   */
  #[OA\Parameter(
    name: 'pictureCode',
    in: 'path',
    description: 'Code de l\'image',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 201,
    description: 'Création réussie',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Pictures::class,
          groups: ['GetPicture']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/api/picture/add/{errorId}', name: 'picture.create', methods: ['POST'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function createPicture(Error $error, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
  {
    $picture = new Pictures();
    $file = $request->files->get('file');
    $picture->setFile($file)
      ->setRealName($file->getClientOriginalName())
      ->setMimeType($file->getClientMimeType())
      ->setPublicPath('/assets/pictures')
      ->setStatus(true)
      ->setError($error)
      ->setUploadDate(new \DateTime());


    $em->persist($picture);
    $em->flush();

    $context = SerializationContext::create()->setGroups(['GetPicture']);
    $jsonPicture = $serializer->serialize($picture, 'json', $context);
    $location = $urlGenerator->generate('pictures.get', ['pictureId' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);



    return new JsonResponse($jsonPicture, Response::HTTP_CREATED, ['accept' => 'json', 'location' => $location], true);
  }

  /**
   * It modifies a new picture, saves it in the database and returns the picture's data in JSON format
   *
   * @method PUT updatePicture()
   * 
   * @param Pictures $picture
   * @param Error $error
   * @param Request $request
   * @param PicturesRepository $pictureRepo
   * @param EntityManagerInterface $em
   * @param SerializerInterface $serializer
   * @param UrlGeneratorInterface $urlGenerator
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'pictureCode',
    in: 'path',
    description: 'Code de l\'image',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Parameter(
    name: 'errorId',
    in: 'path',
    description: 'Id de l\'erreur',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 201,
    description: 'Modification réussie',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Pictures::class,
          groups: ['GetPicture']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/api/picture/{pictureId}/changeError/{errorId}', name: 'picture.update', methods: ['PUT'])]
  #[ParamConverter('picture', options: ['id' => 'pictureId'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function updatePicture(Pictures $picture, Error $error, Request $request, PicturesRepository $pictureRepo, EntityManagerInterface $em, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
  {
    $picture->setError($error);
    $em->persist($picture);
    $em->flush();

    $location = $urlGenerator->generate('pictures.get', ['pictureId' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

    $context = SerializationContext::create()->setGroups(['getPicture']);
    $jsonPicture = $serializer->serialize($picture, 'json', $context);
    return new JsonResponse($jsonPicture, Response::HTTP_CREATED, ['location' => $location], true);
  }

  /**
   * Disable a picture
   *
   * @method DELETE disablePicture()
   * 
   * @param Pictures $picture
   * @param EntityManagerInterface $em
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'pictureId',
    in: 'path',
    description: 'Id de l\'image',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 204,
    description: 'Désactivation réussie'
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/api/picture/{pictureId}', name: 'picture.disable', methods: ['DELETE'])]
  #[ParamConverter('picture', options: ['id' => 'pictureId'])]
  public function disablePicture(Pictures $picture, EntityManagerInterface $em): JsonResponse
  {
    $picture->setStatus(false);
    $em->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }

  /**
   * Delete a picture
   * 
   * @method DELETE deletePicture()
   *
   * @param Pictures $picture
   * @param EntityManagerInterface $em
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'pictureId',
    in: 'path',
    description: 'Id de l\'image',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 204,
    description: 'Suppression réussie'
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/api/picture/delete/{pictureId}', name: 'picture.delete', methods: ['DELETE'])]
  #[ParamConverter('picture', options: ['id' => 'pictureId'])]
  public function deletePicture(Pictures $picture, EntityManagerInterface $em): JsonResponse
  {
    $em->remove($picture);
    $em->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
