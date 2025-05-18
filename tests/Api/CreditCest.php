<?php

namespace Tests\Api;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\Repository\CreditRepositoryInterface;
use DateTimeImmutable;
use Tests\Support\ApiTester;

class CreditCest
{
    private array $sampleClient = [
        'name' => 'Test Client',
        'age' => 30,
        'region' => 'BR',
        'city' => 'Brno',
        'income' => 2000,
        'score' => 700,
        'pin' => 'TEST-12345',
        'email' => 'test@example.com',
        'phone' => '+42012345678'
    ];

    private array $sampleCredit = [
        'name' => 'Test Loan',
        'amount' => 5000,
        'rate' => '10%',
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31'
    ];

    private array $sampleRejectedClient = [
        'name' => 'Low Score Client',
        'age' => 30,
        'region' => 'BR',
        'city' => 'Brno',
        'income' => 2000,
        'score' => 450,
        'pin' => 'LOW-SCORE',
        'email' => 'low-score@example.com',
        'phone' => '+42012345678'
    ];

    public function _before(ApiTester $I)
    {
        // Очищаем данные клиентов и кредитов перед каждым тестом
        $clientRepository = $I->grabService(ClientRepositoryInterface::class);
        $clientReflection = new \ReflectionObject($clientRepository);
        $clientProperty = $clientReflection->getProperty('clients');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($clientRepository, []);
        
        $creditRepository = $I->grabService(CreditRepositoryInterface::class);
        $creditReflection = new \ReflectionObject($creditRepository);
        $creditsProperty = $creditReflection->getProperty('credits');
        $creditsProperty->setAccessible(true);
        $creditsProperty->setValue($creditRepository, []);
        
        $clientCreditsProperty = $creditReflection->getProperty('clientCredits');
        $clientCreditsProperty->setAccessible(true);
        $clientCreditsProperty->setValue($creditRepository, []);
    }

    // Тест на получение списка кредитов
    public function getCreditsListTest(ApiTester $I)
    {
        // Создаем тестовый кредит
        $this->createTestCredit($I);

        // Делаем запрос на получение списка кредитов
        $I->sendGet('/credits');
        
        // Проверяем, что получен успешный ответ
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        // Проверяем, что в списке есть наш кредит
        $I->seeResponseContainsJson([
            [
                'name' => 'Test Loan',
                'amount' => 5000,
            ]
        ]);
    }

    // Тест на создание нового кредита
    public function createCreditTest(ApiTester $I)
    {
        $I->sendPost('/credits', $this->sampleCredit);
        
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'credit' => [
                'name' => 'Test Loan',
                'amount' => 5000,
                'rate' => '10%',
            ],
        ]);
    }

    // Тест на проверку возможности выдачи кредита (успешный случай)
    public function checkApprovalSuccessTest(ApiTester $I)
    {
        // Создаем тестового клиента
        $this->createTestClient($I);

        // Отправляем запрос на проверку кредита
        $I->sendPost('/credits/check-approval', [
            'client_pin' => 'TEST-12345',
            'credit' => $this->sampleCredit
        ]);
        
        // Проверяем, что получен успешный ответ
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        // Проверяем, что кредит одобрен
        $I->seeResponseContainsJson([
            'approved' => true,
            'client' => [
                'name' => 'Test Client',
                'pin' => 'TEST-12345',
            ],
        ]);
    }

    // Тест на проверку возможности выдачи кредита (отказ из-за низкого скоринга)
    public function checkApprovalRejectionTest(ApiTester $I)
    {
        // Создаем клиента с низким кредитным рейтингом
        $this->createRejectedClient($I);

        // Отправляем запрос на проверку кредита
        $I->sendPost('/credits/check-approval', [
            'client_pin' => 'LOW-SCORE',
            'credit' => $this->sampleCredit
        ]);
        
        // Проверяем, что получен успешный ответ (но с отказом в кредите)
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        // Проверяем, что кредит не одобрен
        $I->seeResponseContainsJson([
            'approved' => false,
            'client' => [
                'name' => 'Low Score Client',
                'pin' => 'LOW-SCORE',
            ],
        ]);
        
        // Проверяем наличие причины отказа
        $response = $I->grabResponse();
        $responseData = json_decode($response, true);
        $I->seeResponseJsonMatchesJsonPath('$.reasons');
    }

    // Тест на выдачу кредита (успешный случай)
    public function issueCreditSuccessTest(ApiTester $I)
    {
        // Создаем тестового клиента
        $this->createTestClient($I);

        // Отправляем запрос на выдачу кредита
        $I->sendPost('/credits/issue', [
            'client_pin' => 'TEST-12345',
            'credit' => $this->sampleCredit
        ]);
        
        // Проверяем, что получен успешный ответ
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        // Проверяем, что кредит одобрен и выдан
        $I->seeResponseContainsJson([
            'success' => true,
            'approved' => true,
            'client' => [
                'name' => 'Test Client',
                'pin' => 'TEST-12345',
            ],
            'credit' => [
                'name' => 'Test Loan',
                'amount' => 5000,
                'rate' => '10%',
            ],
        ]);
        
        // Проверяем, что кредит действительно создан и привязан к клиенту
        $creditRepository = $I->grabService(CreditRepositoryInterface::class);
        $clientRepository = $I->grabService(ClientRepositoryInterface::class);
        $client = $clientRepository->findByPin('TEST-12345');
        $credits = $creditRepository->findByClient($client);
        
        $I->assertTrue(count($credits) > 0, 'Кредит должен быть создан и привязан к клиенту');
    }

    // Тест на выдачу кредита (отказ)
    public function issueCreditRejectionTest(ApiTester $I)
    {
        // Создаем клиента с низким кредитным рейтингом
        $this->createRejectedClient($I);

        // Отправляем запрос на выдачу кредита
        $I->sendPost('/credits/issue', [
            'client_pin' => 'LOW-SCORE',
            'credit' => $this->sampleCredit
        ]);
        
        // Проверяем, что получен ответ о неуспешной обработке
        $I->seeResponseCodeIs(422); // Unprocessable Entity
        $I->seeResponseIsJson();
        
        // Проверяем, что кредит не одобрен
        $I->seeResponseContainsJson([
            'approved' => false,
        ]);
        
        // Проверяем наличие причины отказа
        $response = $I->grabResponse();
        $responseData = json_decode($response, true);
        $I->seeResponseJsonMatchesJsonPath('$.reasons');
        
        // Проверяем, что кредит не создан и не привязан к клиенту
        $creditRepository = $I->grabService(CreditRepositoryInterface::class);
        $clientRepository = $I->grabService(ClientRepositoryInterface::class);
        $client = $clientRepository->findByPin('LOW-SCORE');
        $credits = $creditRepository->findByClient($client);
        
        $I->assertTrue(count($credits) === 0, 'Кредит не должен быть создан и привязан к клиенту при отказе');
    }

    private function createTestClient(ApiTester $I)
    {
        $repository = $I->grabService(ClientRepositoryInterface::class);
        $client = new Client(
            $this->sampleClient['name'],
            $this->sampleClient['age'],
            $this->sampleClient['region'],
            $this->sampleClient['city'],
            $this->sampleClient['income'],
            $this->sampleClient['score'],
            $this->sampleClient['pin'],
            $this->sampleClient['email'],
            $this->sampleClient['phone']
        );
        $repository->save($client);
        return $client;
    }

    private function createRejectedClient(ApiTester $I)
    {
        $repository = $I->grabService(ClientRepositoryInterface::class);
        $client = new Client(
            $this->sampleRejectedClient['name'],
            $this->sampleRejectedClient['age'],
            $this->sampleRejectedClient['region'],
            $this->sampleRejectedClient['city'],
            $this->sampleRejectedClient['income'],
            $this->sampleRejectedClient['score'],
            $this->sampleRejectedClient['pin'],
            $this->sampleRejectedClient['email'],
            $this->sampleRejectedClient['phone']
        );
        $repository->save($client);
        return $client;
    }

    private function createTestCredit(ApiTester $I)
    {
        $repository = $I->grabService(CreditRepositoryInterface::class);
        $credit = new Credit(
            $this->sampleCredit['name'],
            $this->sampleCredit['amount'],
            (float)$this->sampleCredit['rate'],
            new DateTimeImmutable($this->sampleCredit['start_date']),
            new DateTimeImmutable($this->sampleCredit['end_date'])
        );
        $repository->save($credit);
        return $credit;
    }
} 