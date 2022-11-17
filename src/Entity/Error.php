<?php

namespace App\Entity;

use App\Repository\ErrorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ErrorRepository::class)]
class Error
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['GetAllErrors', 'GetError', 'GetPicture', 'GetMessage'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['GetAllErrors', 'GetError', 'GetPicture', 'GetMessage'])]
  #[Assert\NotNull(message: "can not be null :/")]
  private int $Code = -1;

  #[ORM\Column]
  private ?bool $status = null;

  #[ORM\OneToMany(mappedBy: 'Error', targetEntity: Pictures::class)]
  #[Groups(['GetError', 'GetMessage'])]
  private Collection $pictures;

  #[Groups(['GetAllErrors', 'GetError'])]
  #[ORM\OneToMany(mappedBy: 'Error', targetEntity: Message::class, orphanRemoval: true)]
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
   * @return Collection<int, Pictures>
   */
  public function getPictures(): Collection
  {
    return $this->pictures;
  }

  public function addPicture(Pictures $picture): self
  {
    if (!$this->pictures->contains($picture)) {
      $this->pictures->add($picture);
      $picture->setError($this);
    }

    return $this;
  }

  public function removePicture(Pictures $picture): self
  {
    if ($this->pictures->removeElement($picture)) {
      // set the owning side to null (unless already changed)
      if ($picture->getError() === $this) {
        $picture->setError(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Message>
   */
  public function getMessages(): Collection
  {
    return $this->messages;
  }

  public function addMessage(Message $message): self
  {
    if (!$this->messages->contains($message)) {
      $this->messages->add($message);
      $message->setError($this);
    }

    return $this;
  }

  public function removeMessage(Message $message): self
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
