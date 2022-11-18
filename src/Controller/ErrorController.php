<?php

namespace App\Controller;

use App\Entity\Error;
use App\Repository\ErrorRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

#[Route('/api')]
#[OA\Tag(name: 'Error')]
#[Security(name: 'Bearer')]
class ErrorController extends AbstractController
{
  /**
   * Liste l'ensemble des erreurs
   * @method GET getAllerrors()
   *
   * @param ErrorRepository $repository
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
  #[OA\Response(
    response: 200,
    description: 'Retourne l\'ensemble des erreurs',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Error::class,
          groups: ['GetAllErrors']

        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/errors', name: 'error.getAll', methods: ['GET'])]
  public function getAllerrors(ErrorRepository $repository, SerializerInterface $serializerInterface): JsonResponse
  {
    $errors = $repository->findAll();

    $errors = array_filter($errors, function ($error) {
      return $error->isStatus() == true;
    });
    $context = SerializationContext::create()->setGroups(['GetAllErrors']);
    $jsonErrors = $serializerInterface->serialize($errors, 'json', $context);
    return new JsonResponse($jsonErrors, Response::HTTP_OK, [], true);
  }

  /**
   * Retourne une erreur avec l'ensemble de ses images et messages
   * 
   * @method GET getError()
   *
   * @param Error $error
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
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
    response: 200,
    description: 'Retourne une erreur avec l\'ensemble de ses images et messages',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Error::class,
          groups: ['GetError']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/error/{errorId}', name: 'error.get', methods: ['GET'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function getError(Error $error, SerializerInterface $serializerInterface): JsonResponse
  {
    if ($error->isStatus() == false) {
      return new JsonResponse("Error not found", Response::HTTP_NOT_FOUND, [], true);
    }
    $context = SerializationContext::create()->setGroups(['GetError']);
    $jsonError = $serializerInterface->serialize($error, 'json', $context);
    return new JsonResponse($jsonError, Response::HTTP_OK, [], true);
  }

  /**
   * Retourne une erreur aléatoire avec l'ensemble de ses images et messages
   * 
   * @method GET getRandomError()
   *
   * @param Error $error
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
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
    response: 200,
    description: 'Retourne une erreur aléatoire avec l\'ensemble de ses images et messages',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Error::class,
          groups: ['GetError']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request',
  )]
  #[Route('/error/random/{errorId}', name: 'errorRandom.get', methods: ['GET'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function getRandomError(Error $error, SerializerInterface $serializerInterface): JsonResponse
  {
    if ($error->isStatus() == false) {
      return new JsonResponse("Error not found", Response::HTTP_NOT_FOUND, [], true);
    }

    $messages = $error->getMessages()->toArray();
    $randomMessage = $messages[array_rand($messages)];

    $pictures = $error->getPictures()->toArray();
    $randomPicture = $pictures[array_rand($pictures)];

    $error->clearAllPictures();
    $error->clearAllMessages();
    $error->addMessage($randomMessage)
      ->addPicture($randomPicture);

    $context = SerializationContext::create()->setGroups(['GetError']);
    $jsonError = $serializerInterface->serialize($error, 'json', $context);
    return new JsonResponse($jsonError, Response::HTTP_OK, [], true);
  }

  /**
   * Retourne une erreur par son code avec l'ensemble de ses images et messages
   * 
   * @method GET getErrorByCode()
   *
   * @param Error $error
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'errorCode',
    in: 'path',
    description: 'Code de l\'erreur',
    required: true,
    example: 404,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 200,
    description: 'Retourne une erreur avec l\'ensemble de ses images et messages',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Error::class,
          groups: ['GetError']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/error/code/{errorCode}', name: 'error.getByCode', methods: ['GET'])]
  public function getErrorByCode(string $errorCode, ErrorRepository $repository, SerializerInterface $serializerInterface): JsonResponse
  {
    $error = $repository->findByErrorCode($errorCode);
    $error = $repository->findByErrorCode($errorCode);
    if ($error->isStatus() == false) {
      return new JsonResponse("Error not found", Response::HTTP_NOT_FOUND, [], true);
    }
    $context = SerializationContext::create()->setGroups(['GetError']);
    $jsonError = $serializerInterface->serialize($error, 'json', $context);
    return new JsonResponse($jsonError, Response::HTTP_OK, [], true);
  }

  /**
   * Create a new error
   * @method POST createError()
   *
   * @param Request $request
   * @param EntityManagerInterface $em
   * @param SerializerInterface $serializer
   * @param UrlGeneratorInterface $urlGenerator
   * 
   * @return JsonResponse
   */

  #[OA\Parameter(
    name: 'errorCode',
    in: 'path',
    description: 'Code de l\'erreur',
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
          type: Error::class,
          groups: ['GetError']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[OA\Response(
    response: 401,
    description: 'Retourné si vous ne pouvez pas effectuer la request',
  )]
  #[Route('/error/', name: 'error.create', methods: ['POST'])]
  #[IsGranted('ROLE_ADMIN', message: 'Pfff..., tu est trop inférieur pour faire ça (╯‵□′)╯︵┻━┻')]
  public function createError(Request $request, MessagesRepository $messageRepo, EntityManagerInterface $em, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
  {
    $error = $serializer->deserialize(
      $request->getContent(),
      Error::class,
      'json'
    );
    $error->setStatus(true);


    $content = $request->toArray();
    $message = $messageRepo->find($content['idMessage']) ?? -1;
    $error->addMessage($message);

    $fails = $validator->validate($error);

    if ($fails->count() > 0) return new JsonResponse($serializer->serialize($fails, 'json'), Response::HTTP_BAD_REQUEST, [], true);

    $em->persist($error);
    $em->flush();

    $context = SerializationContext::create()->setGroups(['GetError']);
    $jsonError = $serializer->serialize($error, 'json', $context);
    $location = $urlGenerator->generate('error.get', ['errorId' => $error->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    return new JsonResponse($jsonError, Response::HTTP_CREATED, ['location' => $location], true);
  }

  /**
   * Route qui permet de modifier une erreur
   * @method PUT updateError()
   * 
   * @param Error $error
   * @param Request $request
   * @param MessageRepository $messageRepo
   * @param EntityManagerInterface $em
   * @param SerializerInterface $serializer
   * @param UrlGeneratorInterface $urlGenerator
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'errorCode',
    in: 'path',
    description: 'Code de l\'erreur',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 200,
    description: 'Modification de l\'erreur effectué',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Error::class,
          groups: ['GetError']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/error/{errorId}', name: 'error.update', methods: ['PUT'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function updateError(Error $error, Request $request, MessagesRepository $messageRepo, EntityManagerInterface $em, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
  {

    $content = $request->toArray();
    $error->setStatus(true)
      ->setCode($content["Code"]);

    $error->clearAllMessages();
    foreach ($content['idMessage'] as $messageId) {
      $message = $messageRepo->find($messageId);
      $error->addMessage($message);
    }

    $em->persist($error);
    $em->flush();

    $context = SerializationContext::create()->setGroups(['GetError']);
    $jsonError = $serializer->serialize($error, 'json', $context);
    $location = $urlGenerator->generate('error.get', ['errorId' => $error->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    return new JsonResponse($jsonError, Response::HTTP_OK, ['location' => $location], true);
  }

  /**
   * Route qui permet de désactiver une erreur
   * @method DELETE disableError()
   * 
   * @param int $errorId
   * @param EntityManagerInterface $em
   * 
   * @return JsonResponse
   */
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
    response: 204,
    description: 'Désactivation de l\'erreur effectué'
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/error/{errorId}', name: 'error.disable', methods: ['DELETE'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function disableError(Error $error, EntityManagerInterface $em): JsonResponse
  {
    $error->setStatus(false);
    $messages = $error->getMessages();
    foreach ($messages as $message) {
      $message->setStatus(false);
    }
    $em->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }

  /**
   * Route qui permet de delete une erreur
   * @method DELETE deleteError()
   * 
   * @param int $errorId
   * @param EntityManagerInterface $em
   * 
   * @return JsonResponse
   */
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
    response: 204,
    description: 'Suppression de l\'erreur effectué'
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/error/delete/{errorId}', name: 'error.delete', methods: ['DELETE'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function deleteError(Error $error, EntityManagerInterface $em): JsonResponse
  {
    $messages = $error->getMessages();
    foreach ($messages as $message) {
      $em->remove($message);
    }
    $em->remove($error);
    $em->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
