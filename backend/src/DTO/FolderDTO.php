<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FolderDTO
{
    #[Assert\NotBlank(message: 'Название папки обязательно для заполнения')]
    #[Assert\Type('string')]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $parent_id;

    public function __construct(
        string $name,
        int $parent_id,
    )
    {
        $this->name = trim($name);
        $this->parent_id = $parent_id;
    }
}
