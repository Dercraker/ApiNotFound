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
  #[AnnotationGroups(['getAllErrors', 'getError', 'getPicture'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[AnnotationGroups(['getAllErrors', 'getError', 'getPicture'])]
  private ?string $Code = null;

  #[ORM\Column(length: 255)]
  #[AnnotationGroups(['getAllErrors', 'getError', 'getPicture'])]
  private ?string $Message = null;

  #[ORM\Column]
  private ?bool $status = null;

  #[ORM\OneToMany(mappedBy: 'pictureLink', targetEntity: Pictures::class, orphanRemoval: true)]
  #[AnnotationGroups(['getAllErrors', 'getError', 'getPicture'])]
  private Collection $pictures;

  public function __construct()
  {
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

  public function getMessage(): ?string
  {
    return $this->Message;
  }

  public function setMessage(string $Message): self
  {
    $this->Message = $Message;

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
      $picture->setPictureLink($this);
    }

    return $this;
  }

  public function removePicture(Pictures $picture): self
  {
    if ($this->pictures->removeElement($picture)) {
      // set the owning side to null (unless already changed)
      if ($picture->getPictureLink() === $this) {
        $picture->setPictureLink(null);
      }
    }

    return $this;
  }
}
