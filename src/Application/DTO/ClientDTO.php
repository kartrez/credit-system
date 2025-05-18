<?php

namespace App\Application\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ClientDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Имя клиента не может быть пустым')]
        #[Assert\Length(min: 3, max: 100, minMessage: 'Имя должно содержать минимум {{ limit }} символа', maxMessage: 'Имя не может быть длиннее {{ limit }} символов')]
        public ?string $name,
        
        #[Assert\NotBlank(message: 'Возраст не может быть пустым')]
        #[Assert\Range(min: 18, max: 100, notInRangeMessage: 'Возраст должен быть от {{ min }} до {{ max }} лет')]
        public ?int $age,
        
        #[Assert\NotBlank(message: 'Регион не может быть пустым')]
        #[Assert\Choice(choices: ['PR', 'BR', 'OS'], message: 'Регион должен быть одним из: PR, BR, OS')]
        public ?string $region,
        
        #[Assert\NotBlank(message: 'Город не может быть пустым')]
        public ?string $city,
        
        #[Assert\NotBlank(message: 'Доход не может быть пустым')]
        #[Assert\GreaterThan(value: 0, message: 'Доход должен быть больше нуля')]
        public ?float $income,
        
        #[Assert\NotBlank(message: 'Кредитный рейтинг не может быть пустым')]
        #[Assert\Range(min: 300, max: 850, notInRangeMessage: 'Кредитный рейтинг должен быть от {{ min }} до {{ max }}')]
        public ?int $score,
        
        #[Assert\NotBlank(message: 'PIN не может быть пустым')]
        #[Assert\Regex(pattern: '/^[A-Z0-9\-]+$/', message: 'PIN может содержать только заглавные буквы, цифры и дефисы')]
        public ?string $pin,
        
        #[Assert\NotBlank(message: 'Email не может быть пустым')]
        #[Assert\Email(message: 'Недействительный формат email')]
        public ?string $email,
        
        #[Assert\NotBlank(message: 'Телефон не может быть пустым')]
        #[Assert\Regex(pattern: '/^\+[0-9]{10,15}$/', message: 'Телефон должен быть в формате +XXXXXXXXXX')]
        public ?string $phone,
    ) {
    }
} 