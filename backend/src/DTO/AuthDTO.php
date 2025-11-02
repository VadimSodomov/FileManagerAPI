<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AuthDTO
{
    #[Assert\NotBlank(message: 'Email обязателен для заполнения')]
    #[Assert\Type('string')]
    #[Assert\Email(message: 'Формат email некорректный')]
    public string $email;

    #[Assert\NotBlank(message: 'Пароль обязателен для заполнения')]
    #[Assert\Type('string')]
    #[Assert\Length(min: 5, minMessage: 'Пароль не может быть менее 5 символов')]
    public string $password;

    public function __construct(
        string $email,
        string $password,
    )
    {
        $this->email = trim($email);
        $this->password = trim($password);
    }
}
