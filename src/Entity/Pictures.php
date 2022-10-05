<?php

namespace App\Entity;

use App\Repository\PicturesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
/**
 * @Vich\Uploadable()
 */
class Pictures
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $realName = null;

  #[ORM\Column(length: 255)]
  private ?string $realPath = null;

  #[ORM\Column(length: 255)]
  private ?string $publicPath = null;

  #[ORM\Column(length: 255)]
  private ?string $mimeType = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $uploadDate = null;

  /**
   * @var File|null
   * @Vich\UploadableField(mapping="pictures", fileNameProperty="realpath")
   */
  private $file;

  #[ORM\Column]
  private ?bool $status = null;

  #[ORM\ManyToOne(inversedBy: 'pictures')]
  private ?Error $Error = null;


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getRealName(): ?string
  {
    return $this->realName;
  }

  public function setRealName(string $realName): self
  {
    $this->realName = $realName;

    return $this;
  }

  public function getRealPath(): ?string
  {
    return $this->realPath;
  }

  public function setRealPath(string $realPath): self
  {
    $this->realPath = $realPath;

    return $this;
  }

  public function getPublicPath(): ?string
  {
    return $this->publicPath;
  }

  public function setPublicPath(string $publicPath): self
  {
    $this->publicPath = $publicPath;

    return $this;
  }

  public function getMimeType(): ?string
  {
    return $this->mimeType;
  }

  public function setMimeType(string $mimeType): self
  {
    $this->mimeType = $mimeType;

    return $this;
  }

  public function getUploadDate(): ?\DateTimeInterface
  {
    return $this->uploadDate;
  }

  public function setUploadDate(\DateTimeInterface $uploadDate): self
  {
    $this->uploadDate = $uploadDate;

    return $this;
  }

  public function getFile(): ?File
  {
    return $this->file;
  }

  public function setFile(?File $file): self
  {
    $this->file = $file;
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

  public function getError(): ?Error
  {
      return $this->Error;
  }

  public function setError(?Error $Error): self
  {
      $this->Error = $Error;

      return $this;
  }
}
