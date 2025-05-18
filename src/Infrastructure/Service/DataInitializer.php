<?php

namespace App\Infrastructure\Service;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\Repository\CreditRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

final class DataInitializer
{
    private ClientRepositoryInterface $clientRepository;
    private CreditRepositoryInterface $creditRepository;
    
    public function __construct(
        ClientRepositoryInterface $clientRepository,
        CreditRepositoryInterface $creditRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->creditRepository = $creditRepository;
    }
    
    public function initialize(): void
    {
        $this->initializeClients();
        $this->initializeCredits();
    }
    
    private function initializeClients(): void
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
            'LOW-SCORE',
            'eva.kralova@example.com',
            '+420567890123'
        );
        $this->clientRepository->save($clientLowScore);
        
        // Тестовый клиент для API-тестов
        $testClient = new Client(
            'Test Client',
            30,
            'PR',
            'Prague',
            2000,
            700,
            'TEST-12345',
            'test@example.com',
            '+42012345678'
        );
        $this->clientRepository->save($testClient);
    }
    
    private function initializeCredits(): void
    {
        // Получение клиентов для привязки кредитов
        $clients = $this->clientRepository->findAll();
        
        if (empty($clients)) {
            return;
        }
        
        // Ипотечный кредит
        $mortgage = new Credit(
            'Mortgage Loan',
            120000,
            3.5,
            new DateTimeImmutable('2023-01-01'),
            new DateTimeImmutable('2043-01-01')
        );
        
        // Потребительский кредит
        $consumerLoan = new Credit(
            'Consumer Loan',
            5000,
            9.9,
            new DateTimeImmutable('2023-06-01'),
            new DateTimeImmutable('2026-06-01')
        );
        
        // Автокредит
        $carLoan = new Credit(
            'Car Loan',
            15000,
            6.5,
            new DateTimeImmutable('2023-03-15'),
            new DateTimeImmutable('2028-03-15')
        );
        
        // Студенческий кредит
        $studentLoan = new Credit(
            'Student Loan',
            8000,
            4.2,
            new DateTimeImmutable('2022-09-01'),
            new DateTimeImmutable('2030-09-01')
        );
        
        // Кредит на бизнес
        $businessLoan = new Credit(
            'Business Loan',
            50000,
            7.8,
            new DateTimeImmutable('2023-02-10'),
            new DateTimeImmutable('2033-02-10')
        );
        
        // Привязка кредитов к клиентам
        $mortgage->setClient($clients[0]); // Ипотека для клиента из Праги
        $consumerLoan->setClient($clients[1]); // Потребительский кредит для клиента из Брно
        $carLoan->setClient($clients[2]); // Автокредит для клиента из Остравы
        
        // Сохранение кредитов
        $this->creditRepository->save($mortgage);
        $this->creditRepository->save($consumerLoan);
        $this->creditRepository->save($carLoan);
        $this->creditRepository->save($studentLoan); // Кредит без клиента
        $this->creditRepository->save($businessLoan); // Кредит без клиента
    }
} 