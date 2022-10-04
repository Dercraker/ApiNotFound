<?php

namespace App\Controller;

use App\Entity\Error;
use App\Repository\ErrorRepository;
use App\Repository\MessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/api')]
class ErrorController extends AbstractController
{
  /**
   * Route qui permet de récupérer les erreurs
   * @param ErrorRepository $errorRepository
   * @param SerializerInterface $serializer
   * @return JsonResponse
   */
  #[Route('/errors', name: 'error.getAll', methods: ['GET'])]
  public function getAllerrors(ErrorRepository $repository, SerializerInterface $serializerInterface): JsonResponse
  {
    $errors = $repository->findAll();
    $jsonErrors = $serializerInterface->serialize($errors, 'json', ['groups' => 'getAllErrors']);
    return new JsonResponse($jsonErrors, Response::HTTP_OK, [], true);
  }

  /**
   * Route qui permet de récupérer une erreur
   * @method GET getError()
   * 
   * @param int $errorID
   * @param ErrorRepository $errorRepository
   * @param SerializerInterface $serializer
   * 
   * @return JsonResponse
   */
  #[Route('/error/{errorId}', name: 'error.get', methods: ['GET'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function getError(Error $error, SerializerInterface $serializerInterface): JsonResponse
  {
    $jsonError = $serializerInterface->serialize($error, 'json', ['groups' => 'getError']);
    return new JsonResponse($jsonError, Response::HTTP_OK, [], true);
  }

  /**
   * Create a new error
   * @method POST createError()
   *
   * @param Request $request
   * @param EntityManagerInterface $entityManager
   * @param SerializerInterface $serializer
   * @param UrlGeneratorInterface $urlGenerator
   * 
   * @return JsonResponse
   */
  #[Route('/error/', name: 'error.create', methods: ['POST'])]
  public function createError(Request $request, MessagesRepository $messageRepo, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
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

    $entityManager->persist($error);
    $entityManager->flush();

    $jsonError = $serializer->serialize($error, 'json', ['groups' => 'getError']);
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
   * @param EntityManagerInterface $entityManager
   * @param SerializerInterface $serializer
   * @param UrlGeneratorInterface $urlGenerator
   * 
   * @return JsonResponse
   */
  #[Route('/error/{errorId}', name: 'error.update', methods: ['PUT'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function updateError(Error $error, Request $request, MessagesRepository $messageRepo, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse
  {
    $updateError = $serializer->deserialize(
      $request->getContent(),
      Error::class,
      'json',
      [AbstractNormalizer::OBJECT_TO_POPULATE => $error]
    );
    $updateError->setStatus(true);

    $content = $request->toArray();
    dd($content['idMessages']);

    $messagesToRemove = $error->getMessages();

    foreach ($messagesToRemove as $message) {
      $error->removeMessage($message);
    }

    foreach ($content['idMessages'] as $idMessage) {
      // $message = $messageRepo->find($idMessage) ?? -1;
      $error->addMessage($messageRepo->find($idMessage) ?? -1);
    }


    $message = $messageRepo->find($content['idMessages']) ?? -1;
    $error->addMessage($message);

    $entityManager->persist($error);
    $entityManager->flush();

    $jsonError = $serializer->serialize($error, 'json', ['groups' => 'getError']);
    $location = $urlGenerator->generate('error.get', ['errorId' => $error->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    return new JsonResponse($jsonError, Response::HTTP_OK, ['location' => $location], true);
  }

  /**
   * Route qui permet de désactiver une erreur
   * @method DELETE disableError()
   * 
   * @param int $errorId
   * @param EntityManagerInterface $entityManager
   * 
   * @return JsonResponse
   */
  #[Route('/error/{errorId}', name: 'error.disable', methods: ['DELETE'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function disableError(Error $error, EntityManagerInterface $entityManager): JsonResponse
  {
    $error->setStatus(false);
    $messages = $error->getMessages();
    foreach ($messages as $message) {
      $message->setStatus(false);
    }
    $entityManager->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }

  /**
   * Route qui permet de delete une erreur
   * @method DELETE deleteError()
   * 
   * @param int $errorId
   * @param EntityManagerInterface $entityManager
   * 
   * @return JsonResponse
   */
  #[Route('/error/delete/{errorId}', name: 'error.delete', methods: ['DELETE'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function deleteError(Error $error, EntityManagerInterface $entityManager): JsonResponse
  {
    $messages = $error->getMessages();
    foreach ($messages as $message) {
      $entityManager->remove($message);
    }
    $entityManager->remove($error);
    $entityManager->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
