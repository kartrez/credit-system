<?php

namespace App\Application\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreditDTO
{
    public function __construct(
        #[Assert\NotBlank(message: 'Название кредита не может быть пустым')]
        #[Assert\Length(min: 3, max: 100, minMessage: 'Название кредита должно содержать минимум {{ limit }} символа', maxMessage: 'Название кредита не может быть длиннее {{ limit }} символов')]
        public ?string $name,
        
        #[Assert\NotBlank(message: 'Сумма кредита не может быть пустой')]
        #[Assert\GreaterThan(value: 0, message: 'Сумма кредита должна быть больше нуля')]
        public ?float $amount,
        
        #[Assert\NotBlank(message: 'Процентная ставка не может быть пустой')]
        #[Assert\GreaterThan(value: 0, message: 'Процентная ставка должна быть больше нуля')]
        public ?float $rate,
        
        #[Assert\NotBlank(message: 'Дата начала не может быть пустой')]
        #[Assert\Date(message: 'Недействительный формат даты начала')]
        public ?string $startDate,
        
        #[Assert\NotBlank(message: 'Дата окончания не может быть пустой')]
        #[Assert\Date(message: 'Недействительный формат даты окончания')]
        #[Assert\Expression(
            expression: 'this.startDate < this.endDate',
            message: 'Дата окончания должна быть позже даты начала'
        )]
        public ?string $endDate,
        
        #[Assert\Regex(pattern: '/^[A-Z0-9\-]+$/', message: 'PIN клиента может содержать только заглавные буквы, цифры и дефисы')]
        public ?string $clientPin = null,
    ) {}
} 