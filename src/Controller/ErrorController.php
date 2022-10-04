<?php

namespace App\Controller;

use App\Repository\ErrorRepository;
use App\Entity\Error;
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
    $jsonError = $serializerInterface->serialize($error, 'json');
    return new JsonResponse($jsonError, Response::HTTP_OK, ['accept' => 'json'], true);
  }
}
