<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['getAllMessages', 'getMessage', 'getError'])]
  private ?int $id = null;

  #[ORM\Column(type: Types::TEXT)]
  #[Groups(['getAllMessages', 'getMessage', 'getError', 'getPicture'])]
  private ?string $message = null;

  #[ORM\ManyToOne(inversedBy: 'messages')]
  #[ORM\JoinColumn(nullable: false)]
  #[Groups(['getAllMessages', 'getMessage'])]
  private ?Error $Error = null;

  #[ORM\Column]
  private ?bool $Status = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getMessage(): ?string
  {
    return $this->message;
  }

  public function setMessage(string $message): self
  {
    $this->message = $message;

    return $this;
  }

  public function getError(): ?Error
  {
    return $this->Error;
  }

  public function setError(?Error $Error): self
  {
    $this->Error = $Error;

    return $this;
  }

  public function isStatus(): ?bool
  {
    return $this->Status;
  }

  public function setStatus(bool $Status): self
  {
    $this->Status = $Status;

    return $this;
  }
}
