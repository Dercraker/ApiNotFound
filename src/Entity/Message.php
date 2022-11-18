<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[Groups(['GetMessage', 'GetAllMessages', 'GetAllErrors', 'GetError', 'GetPicture'])]
  #[ORM\Column]
  private ?int $id = null;

  #[Groups(['GetMessage', 'GetAllMessages', 'GetAllErrors', 'GetError', 'GetPicture'])]
  #[ORM\Column(length: 255)]
  private ?string $Text = null;

  #[Groups(['GetAllMessages'])]
  #[ORM\Column]
  private ?bool $Status = null;

  #[Groups(['GetMessage'])]
  #[ORM\ManyToOne(inversedBy: 'messages')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Error $Error = null;

  #[Groups(['GetMessage'])]
  #[ORM\Column]
  private ?int $Code = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getText(): ?string
  {
    return $this->Text;
  }

  public function setText(string $Text): self
  {
    $this->Text = $Text;

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

  public function getError(): ?Error
  {
    return $this->Error;
  }

  public function setError(?Error $Error): self
  {
    $this->Error = $Error;

    return $this;
  }

  public function getCode(): ?int
  {
    return $this->Code;
  }

  public function setCode(int $Code): self
  {
    $this->Code = $Code;

    return $this;
  }
}
