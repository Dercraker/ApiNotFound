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
  #[Groups(['GetMessage', 'GetAllMessages'])]
  #[ORM\Column]
  private ?int $id = null;

  #[Groups(['GetMessage', 'GetAllMessages'])]
  #[ORM\Column(length: 255)]
  private ?string $Text = null;

  #[ORM\Column]
  private ?bool $Status = null;

  #[ORM\ManyToOne(inversedBy: 'messages')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Error $Error = null;

  #[Groups(['GetMessage', 'GetAllMessages'])]
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
