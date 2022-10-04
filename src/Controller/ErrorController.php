<?php

namespace App\Controller;

use App\Repository\ErrorRepository;
use App\Entity\Error;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ErrorController extends AbstractController
{
  /**
   * Route qui permet de ping l'api
   * @return JsonResponse
   */
  #[Route('/error', name: 'app_error', methods: ['GET'])]
  public function index(): JsonResponse
  {
    return $this->json([
      'message' => 'Welcome to your new controller!',
      'path' => 'src/Controller/ErrorController.php',
    ]);
  }

  /**
   * Route qui permet de récupérer les erreurs
   * @param ErrorRepository $errorRepository
   * @param SerializerInterface $serializer
   * @return JsonResponse
   */
  #[Route('/api/errors', name: 'error.getAll', methods: ['GET'])]
  public function getAllerrors(ErrorRepository $repository, SerializerInterface $serializerInterface): JsonResponse
  {
    $errors = $repository->findAll();
    $jsonErrors = $serializerInterface->serialize($errors, 'json', ['groups' => 'getAllErrors']);
    return new JsonResponse($jsonErrors, Response::HTTP_OK, [], true);
  }

  /**
   * Route qui permet de récupérer une erreur
   * @param int $errorID
   * @param ErrorRepository $errorRepository
   * @param SerializerInterface $serializer
   * @return JsonResponse
   */
  // #[Route('/api/error/{errorId}', name: 'error.get', methods: ['GET'])]
  // public function getError(int $errorId, ErrorRepository $repository, SerializerInterface $serializerInterface): JsonResponse
  // {
  //   $error = $repository->find($errorId);
  //   $jsonError = $serializerInterface->serialize($error, 'json');
  //   return $error ? new JsonResponse($jsonError, Response::HTTP_OK, [], true) :
  //     new JsonResponse(['message' => 'error not found'], Response::HTTP_NOT_FOUND);
  // }
  #[Route('/api/error/{errorId}', name: 'error.get', methods: ['GET'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function getError(Error $error, SerializerInterface $serializerInterface): JsonResponse
  {
    $jsonError = $serializerInterface->serialize($error, 'json', ['groups' => 'getError']);
    return new JsonResponse($jsonError, Response::HTTP_OK, [], true);
  }

  /**
   * Route qui permet de désactiver une erreur
   * @param int $errorId
   * @param EntityManagerInterface $entityManager
   * @return JsonResponse
   */
  #[Route('/api/error/{errorId}', name: 'error.disable', methods: ['DELETE'])]
  #[ParamConverter('error', options: ['id' => 'errorId'])]
  public function disableError(Error $error, EntityManagerInterface $entityManager): JsonResponse
  {
    $error->setStatus(false);
    $entityManager->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }

  /**
   * Route qui permet de delete une erreur
   * @param int $errorId
   * @param EntityManagerInterface $entityManager
   * @return JsonResponse
   */
  #[Route('/api/error/delete/{errorId}', name: 'error.delete', methods: ['DELETE'])]
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
