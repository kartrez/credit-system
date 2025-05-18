<?php

namespace App\Infrastructure\Service;

use App\Domain\Entity\Client;
use App\Domain\Repository\ClientRepositoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

final class DataInitializer
{
    private ClientRepositoryInterface $clientRepository;
    
    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function initialize(): void
    {
        // Пример клиента из Праги
        $clientPrague = new Client(
            'Petr Pavel',
            35,
            'PR',
            'Prague',
            1500,
            600,
            '123-45-6789',
            'petr.pavel@example.com',
            '+420123456789'
        );
        $this->clientRepository->save($clientPrague);
        
        // Пример клиента из Брно
        $clientBrno = new Client(
            'Jana Novakova',
            42,
            'BR',
            'Brno',
            2000,
            700,
            '234-56-7890',
            'jana.novakova@example.com',
            '+420234567890'
        );
        $this->clientRepository->save($clientBrno);
        
        // Пример клиента из Остравы
        $clientOstrava = new Client(
            'Martin Dvorak',
            28,
            'OS',
            'Ostrava',
            1200,
            550,
            '345-67-8901',
            'martin.dvorak@example.com',
            '+420345678901'
        );
        $this->clientRepository->save($clientOstrava);
        
        // Клиент с недостаточным доходом
        $clientLowIncome = new Client(
            'Tomas Malik',
            50,
            'PR',
            'Prague',
            800,
            520,
            '456-78-9012',
            'tomas.malik@example.com',
            '+420456789012'
        );
        $this->clientRepository->save($clientLowIncome);
        
        // Клиент с низким кредитным рейтингом
        $clientLowScore = new Client(
            'Eva Kralova',
            33,
            'BR',
            'Brno',
            1800,
            450,
            '567-89-0123',
            'eva.kralova@example.com',
            '+420567890123'
        );
        $this->clientRepository->save($clientLowScore);
    }
} 