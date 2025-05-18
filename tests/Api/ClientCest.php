<?php

namespace Tests\Api;

use App\Domain\Entity\Client;
use App\Domain\Repository\ClientRepositoryInterface;
use Tests\Support\ApiTester;

class ClientCest
{
    private array $sampleClient = [
        'name' => 'Test Client',
        'age' => 30,
        'region' => 'PR',
        'city' => 'Prague',
        'income' => 2000,
        'score' => 700,
        'pin' => 'TEST-12345',
        'email' => 'test@example.com',
        'phone' => '+42012345678'
    ];

    public function _before(ApiTester $I)
    {
        // Очищаем данные клиентов перед каждым тестом
        $repository = $I->grabService(ClientRepositoryInterface::class);
        $reflection = new \ReflectionObject($repository);
        $property = $reflection->getProperty('clients');
        $property->setAccessible(true);
        $property->setValue($repository, []);
    }

    // Тест на получение списка клиентов
    public function getClientsListTest(ApiTester $I)
    {
        // Создаем тестового клиента
        $this->createTestClient($I);

        // Делаем запрос на получение списка клиентов
        $I->sendGet('/clients');
        
        // Проверяем, что получен успешный ответ
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        // Проверяем, что в списке есть наш клиент
        $I->seeResponseContainsJson([
            [
                'name' => 'Test Client',
                'pin' => 'TEST-12345',
            ]
        ]);
    }

    // Тест на получение информации о клиенте по PIN
    public function getClientByPinTest(ApiTester $I)
    {
        // Создаем тестового клиента
        $this->createTestClient($I);

        // Делаем запрос на получение клиента по PIN
        $I->sendGet('/clients/TEST-12345');
        
        // Проверяем, что получен успешный ответ
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        
        // Проверяем содержимое ответа
        $I->seeResponseContainsJson([
            'name' => 'Test Client',
            'age' => 30,
            'region' => 'PR',
            'city' => 'Prague',
            'income' => 2000,
            'score' => 700,
            'pin' => 'TEST-12345',
            'email' => 'test@example.com',
            'phone' => '+42012345678'
        ]);
    }

    // Тест на получение несуществующего клиента
    public function getNonExistentClientTest(ApiTester $I)
    {
        $I->sendGet('/clients/NON-EXISTENT');
        
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'Клиент не найден']);
    }

    // Тест на создание нового клиента
    public function createClientTest(ApiTester $I)
    {
        // Отправляем запрос на создание клиента
        $I->sendPost('/clients', $this->sampleClient);
        
        // Проверяем результат создания
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => true,
            'client' => [
                'name' => 'Test Client',
                'pin' => 'TEST-12345',
            ],
        ]);
        
        // Проверяем, что клиент сохранен в репозитории
        $repository = $I->grabService(ClientRepositoryInterface::class);
        $client = $repository->findByPin('TEST-12345');
        $I->assertTrue($client !== null, 'Клиент должен быть создан');
        $I->assertTrue($client->getName() === 'Test Client', 'Имя клиента должно быть корректным');
    }

    // Тест на создание клиента с неверными данными
    public function createClientWithInvalidDataTest(ApiTester $I)
    {
        $invalidClient = $this->sampleClient;
        $invalidClient['pin'] = ''; // Пустой PIN
        
        $I->sendPost('/clients', $invalidClient);
        
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['error' => 'Неверные данные клиента']);
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
    }
} 