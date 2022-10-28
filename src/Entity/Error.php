<?php

namespace App\Entity;

use App\Repository\ErrorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;


// /**
//  * @Hateoas\Relation(
//  *    "up",
//  *    href = @Hateoas\Route(
//  *       "error.getAll"
//  *   )
//  *    exclude = @Hateoas\Exclusion(groups = {"getAllErrors"})
//  * )
//  * 
//  * @Hateoas\Relation(
//  *     "self",
//  *     href = @Hateoas\Route(
//  *         "error.getByCode",
//  *         parameters = { "errorCode" = "expr(object.getCode())" }, 
//  *     )  
//  *     exclusion = @Hateoas\Exclusion(groups = "getAllErrors")
//  * )
//  */
#[ORM\Entity(repositoryClass: ErrorRepository::class)]
class Error
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['getAllErrors', 'getError', 'getMessage', 'getPicture'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Groups(['getAllErrors', 'getError', 'getMessage', 'getPicture'])]
  #[Assert\NotNull(message: "can not be null :/")]
  private int $Code = -1;

  #[ORM\Column]
  private ?bool $status = null;

  #[ORM\OneToMany(mappedBy: 'Error', targetEntity: Messages::class)]
  #[Groups(['getError', 'getPicture'])]
  private ?Collection $messages = null;

  #[ORM\OneToMany(mappedBy: 'Error', targetEntity: Pictures::class)]
  #[Groups(['getError'])]
  private Collection $pictures;


  public function __construct()
  {
    $this->messages = new ArrayCollection();
    $this->pictures = new ArrayCollection();
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

  /**
   * remove all Messages on this error
   *
   * @return self
   */
  public function removeAllMessgaes(): self
  {
    foreach ($this->getMessages() as $message) {
      $this->removeMessage($message);
    }
    return $this;
  }

  /**
   * Add messages to the error by their id.
   * 
   * @param array messagesId array of message ids
   * 
   * @return self The object itself.
   */
  public function addMessageByIdArray(array $messagesIds): self
  {
    foreach ($messagesIds as $messageId) {
      $this->addMessage($messageId);
    }
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
}
