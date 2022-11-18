<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\ErrorRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api')]
#[OA\Tag(name: 'Message')]
#[Security(name: 'Bearer')]
class MessageController extends AbstractController
{
  /**
   * Liste l'ensemble des messages
   * 
   * @method GET getAllmessages()
   *
   * @param MessageRepository $repository
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
  #[OA\Response(
    response: 200,
    description: 'Retourne l\'ensemble des messages',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Message::class,
          groups: ['GetAllMessages']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/messages', name: 'message.getAll', methods: ['GET'])]
  public function getAllmessages(MessageRepository $repository, SerializerInterface $serializerInterface): JsonResponse
  {
    $messages = $repository->findAll();
    $messages = array_filter($messages, function ($messages) {
      return $messages->isStatus() == true;
    });
    $context = SerializationContext::create()->setGroups(['GetAllMessages']);
    $jsonMessages = $serializerInterface->serialize($messages, 'json', $context);
    return new JsonResponse($jsonMessages, Response::HTTP_OK, [], true);
  }

  /**
   * Retourne un message avec l'ensemble de ses images et erreurs
   *
   * @method GET getMessage()
   * 
   * @param Message $message
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'messageId',
    in: 'path',
    description: 'Id du message',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 200,
    description: 'Retourne un message avec l\'ensemble de ses images et erreurs',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Message::class,
          groups: ['GetMessage']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/message/{messageId}', name: 'message.get', methods: ['GET'])]
  #[ParamConverter('message', options: ['id' => 'messageId'])]
  public function getMessage(Message $message, SerializerInterface $serializerInterface): JsonResponse
  {
    if ($message->isStatus() == false) {
      return new JsonResponse("Error not found", Response::HTTP_NOT_FOUND, [], true);
    }
    $context = SerializationContext::create()->setGroups(['GetMessage']);
    $jsonError = $serializerInterface->serialize($message, 'json', $context);
    return new JsonResponse($jsonError, Response::HTTP_OK, [], true);
  }

  /**
   * Retourne un message avec l'ensemble de ses images et erreurs
   *
   * @method GET getMessage()
   * 
   * @param integer $messageCode
   * @param MessageRepository $repository
   * @param SerializerInterface $serializerInterface
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'messageCode',
    in: 'path',
    description: 'Code du message',
    required: true,
    example: 1,
    schema: new OA\Schema(
      type: 'integer',
      format: 'int64'
    )
  )]
  #[OA\Response(
    response: 200,
    description: 'Retourne un message avec l\'ensemble de ses images et erreurs',
    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(
        ref: new Model(
          type: Message::class,
          groups: ['GetMessage']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/message/code/{messageCode}', name: 'message.getByCode', methods: ['GET'])]
  public function getMessageByCode(int $messageCode, MessageRepository $repository, SerializerInterface $serializerInterface): JsonResponse
  {
    $message = $repository->findMessageByCode($messageCode);
    if ($message == null || $message->isStatus() == false) {
      return new JsonResponse("Error with id: " . strval($messageCode) . " not found", Response::HTTP_NOT_FOUND, [], true);
    }
    $context = SerializationContext::create()->setGroups(['GetMessage']);
    $jsonError = $serializerInterface->serialize($message, 'json', $context);
    return new JsonResponse($jsonError, Response::HTTP_OK, [], true);
  }

  /**
   * Create a new message
   *
   * @method POST createMessage()
   * 
   * @param Request $request
   * @param EntityManagerInterface $em
   * @param SerializerInterface $serializer
   * @param UrlGeneratorInterface $urlGenerator
   * @param ValidatorInterface $validator
   * @param ErrorRepository $errorRepository
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'messageCode',
    in: 'path',
    description: 'Code du message',
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
          type: Message::class,
          groups: ['GetMessage']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/message', name: 'message.create', methods: ['POST'])]
  #[IsGranted('ROLE_ADMIN', message: 'Pfff..., tu est trop inférieur pour faire ça (╯‵□′)╯︵┻━┻')]
  public function createMessage(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, ErrorRepository $errorRepository): JsonResponse
  {
    $message = $serializer->deserialize(
      $request->getContent(),
      Message::class,
      'json'
    );
    $error = $errorRepository->findByErrorCode($message->getCode());
    if ($error == null || $error->isStatus() == false) {
      return new JsonResponse("Error with id: " . strval($message->getCode()) . " not found", Response::HTTP_NOT_FOUND, [], true);
    }
    $message->setStatus(true)->setError($error);


    $fails = $validator->validate($message);

    if ($fails->count() > 0) return new JsonResponse($serializer->serialize($fails, 'json'), Response::HTTP_BAD_REQUEST, [], true);

    $em->persist($message);
    $em->flush();

    $context = SerializationContext::create()->setGroups(['GetMessage']);
    $jsonError = $serializer->serialize($message, 'json', $context);
    $location = $urlGenerator->generate('error.get', ['errorId' => $message->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    return new JsonResponse($jsonError, Response::HTTP_CREATED, ['location' => $location], true);
  }

  /**
   * modifie un message
   *
   * @method PUT updateMessage()
   * 
   * @param Message $message
   * @param Request $request
   * @param EntityManagerInterface $em
   * @param SerializerInterface $serializer
   * @param ErrorRepository $errorRepository
   * @param UrlGeneratorInterface $urlGenerator
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'messageCode',
    in: 'path',
    description: 'Code du message',
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
          type: Message::class,
          groups: ['GetMessage']
        )
      )
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Retourné lors d\'une erreur dans la request'
  )]
  #[Route('/message/{messageId}', name: 'message.update', methods: ['PUT'])]
  #[ParamConverter('message', options: ['id' => 'messageId'])]
  public function updateMessage(Message $message, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ErrorRepository $errorRepository, UrlGeneratorInterface $urlGenerator): JsonResponse
  {
    $content = $request->toArray();

    $error = $errorRepository->findByErrorCode($content["Code"]);
    if ($error == null || $error->isStatus() == false) {
      return new JsonResponse("Error with id: " . strval($content["Code"]) . " not found", Response::HTTP_NOT_FOUND, [], true);
    }
    if ($message == null || $message->isStatus() == false) {
      return new JsonResponse("Message with id: " . strval($message->getId()) . " not found", Response::HTTP_NOT_FOUND, [], true);
    }

    $message->setStatus(true)
      ->setCode($content["Code"])
      ->setText($content["Text"])
      ->setError($error);

    $em->persist($message);
    $em->flush();

    $context = SerializationContext::create()->setGroups(['GetMessage']);
    $jsonMessage = $serializer->serialize($message, 'json', $context);
    $location = $urlGenerator->generate('message.get', ['messageId' => $message->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    return new JsonResponse($jsonMessage, Response::HTTP_CREATED, ['location' => $location], true);
  }

  /**
   * Désactive un message
   * 
   * @method DELETE disableMessage()
   *
   * @param Message $message
   * @param EntityManagerInterface $em
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'messageId',
    in: 'path',
    description: 'Id du message',
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
  #[Route('/message/{messageId}', name: 'message.disable', methods: ['DELETE'])]
  #[ParamConverter('message', options: ['id' => 'messageId'])]
  public function disableMessage(Message $message, EntityManagerInterface $em): JsonResponse
  {
    $message->setStatus(false);
    $em->flush();

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }

  /**
   * Supprime un message
   *
   * @method DELETE deleteError()
   * 
   * @param Message $message
   * @param EntityManagerInterface $em
   * 
   * @return JsonResponse
   */
  #[OA\Parameter(
    name: 'messageId',
    in: 'path',
    description: 'Id du message',
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
  #[Route('/message/delete/{messageId}', name: 'message.delete', methods: ['DELETE'])]
  #[ParamConverter('message', options: ['id' => 'messageId'])]
  public function deleteError(Message $message, EntityManagerInterface $em): JsonResponse
  {
    $em->remove($message);
    $em->flush();
    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
