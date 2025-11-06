<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[Ignore]
    #[ORM\Column]
    private ?string $server_name = null;

    #[ORM\Column]
    private ?\DateTime $cdate = null;

    #[Ignore]
    #[ORM\Column]
    private ?string $mimeType = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Folder $folder = null;

    public function __construct()
    {
        $this->cdate = new \DateTime(timezone: new \DateTimeZone('Europe/Moscow'));
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

    #[Ignore]
    public function getServerName(): ?string
    {
        return $this->server_name;
    }

    public function setServerName(string $server_name): static
    {
        $this->server_name = $server_name;

        return $this;
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

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

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

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): static
    {
        $this->folder = $folder;

        return $this;
    }
}
