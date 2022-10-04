<?php

namespace App\Entity;

use App\Repository\ErrorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as AnnotationGroups;

#[ORM\Entity(repositoryClass: ErrorRepository::class)]
class Error
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[AnnotationGroups(['getAllErrors', 'getError', 'getMessage'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[AnnotationGroups(['getAllErrors', 'getError', 'getMessage'])]
  private ?string $Code = null;

  #[ORM\Column]
  private ?bool $status = null;

  #[ORM\OneToMany(mappedBy: 'Error', targetEntity: Messages::class)]
  #[AnnotationGroups(['getAllErrors', 'getError'])]
  private Collection $messages;


  public function __construct()
  {
    $this->pictures = new ArrayCollection();
    $this->messages = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getCode(): ?string
  {
    return $this->Code;
  }

  public function setCode(string $Code): self
  {
    $this->Code = $Code;

    return $this;
  }

  public function isStatus(): ?bool
  {
    return $this->status;
  }

  public function setStatus(bool $status): self
  {
    $this->status = $status;

    return $this;
  }

  /**
   * @return Collection<int, Messages>
   */
  public function getMessages(): Collection
  {
    return $this->messages;
  }

  public function addMessage(Messages $message): self
  {
    if (!$this->messages->contains($message)) {
      $this->messages->add($message);
      $message->setError($this);
    }

    return $this;
  }

  public function removeMessage(Messages $message): self
  {
    if ($this->messages->removeElement($message)) {
      // set the owning side to null (unless already changed)
      if ($message->getError() === $this) {
        $message->setError(null);
      }
    }

    return $this;
  }
}
