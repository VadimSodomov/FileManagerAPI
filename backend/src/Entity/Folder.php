<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FolderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: FolderRepository::class)]
class Folder
{
    public const string ROOT_NAME_DEFAULT = 'Мое хранилище';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parent = null;

    #[ORM\Column]
    private ?\DateTime $cdate = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'folder')]
    private Collection $files;

    public function __construct()
    {
        $this->cdate = new \DateTime(timezone: new \DateTimeZone('Europe/Moscow'));
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public static function createRootFolder(User $user): Folder
    {
        return new self()->setUser($user)
            ->setName(self::ROOT_NAME_DEFAULT);
    }

    public function getCdate(): ?string
    {
        return $this->cdate?->format('Y-m-d H:i:s');
    }

    public function setCdate(\DateTime $cdate): static
    {
        $this->cdate = $cdate;

        return $this;
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setFolder($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getFolder() === $this) {
                $file->setFolder(null);
            }
        }

        return $this;
    }
}
