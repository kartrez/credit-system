<?php

namespace App\Tests\Unit\Application\Service;

use App\Application\DTO\ClientDTO;
use App\Application\Service\ClientService;
use App\Domain\Entity\Client;
use App\Domain\Repository\ClientRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ClientServiceTest extends TestCase
{
    private ClientRepositoryInterface $clientRepository;
    private ClientService $clientService;
    private ClientDTO $clientDTO;
    
    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->clientService = new ClientService($this->clientRepository);
        
        // Создаем тестовый DTO клиента
        $this->clientDTO = new ClientDTO();
        $this->clientDTO->name = 'Test User';
        $this->clientDTO->age = 30;
        $this->clientDTO->region = 'PR';
        $this->clientDTO->city = 'Prague';
        $this->clientDTO->income = 2000.0;
        $this->clientDTO->score = 700;
        $this->clientDTO->pin = 'TEST-12345';
        $this->clientDTO->email = 'test@example.com';
        $this->clientDTO->phone = '+42012345678';
    }
    
    public function testCreateClient(): void
    {
        // Проверяем, что репозиторий вызывается с клиентом
        $this->clientRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Client $client) {
                return $client->getName() === 'Test User' &&
                    $client->getAge() === 30 &&
                    $client->getRegion() === 'PR' &&
                    $client->getCity() === 'Prague' &&
                    $client->getIncome() === 2000.0 &&
                    $client->getScore() === 700 &&
                    $client->getPin() === 'TEST-12345' &&
                    $client->getEmail() === 'test@example.com' &&
                    $client->getPhone() === '+42012345678';
            }));
        
        $client = $this->clientService->createClient($this->clientDTO);
        
        // Проверяем, что сервис возвращает корректный объект клиента
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals('Test User', $client->getName());
        $this->assertEquals(30, $client->getAge());
        $this->assertEquals('PR', $client->getRegion());
        $this->assertEquals('Prague', $client->getCity());
        $this->assertEquals(2000.0, $client->getIncome());
        $this->assertEquals(700, $client->getScore());
        $this->assertEquals('TEST-12345', $client->getPin());
        $this->assertEquals('test@example.com', $client->getEmail());
        $this->assertEquals('+42012345678', $client->getPhone());
    }
    
    public function testGetClientByPin(): void
    {
        $expectedClient = new Client(
            'Test User',
            30,
            'PR',
            'Prague',
            2000.0,
            700,
            'TEST-12345',
            'test@example.com',
            '+42012345678'
        );
        
        // Репозиторий должен вернуть клиента по пину
        $this->clientRepository->expects($this->once())
            ->method('findByPin')
            ->with('TEST-12345')
            ->willReturn($expectedClient);
        
        $client = $this->clientService->getClientByPin('TEST-12345');
        
        $this->assertSame($expectedClient, $client);
    }
    
    public function testGetAllClients(): void
    {
        $expectedClients = [
            new Client(
                'Test User 1',
                30,
                'PR',
                'Prague',
                2000.0,
                700,
                'TEST-12345',
                'test1@example.com',
                '+42012345678'
            ),
            new Client(
                'Test User 2',
                40,
                'BR',
                'Brno',
                3000.0,
                800,
                'TEST-67890',
                'test2@example.com',
                '+42067890123'
            )
        ];
        
        // Репозиторий должен вернуть список клиентов
        $this->clientRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedClients);
        
        $clients = $this->clientService->getAllClients();
        
        $this->assertSame($expectedClients, $clients);
        $this->assertCount(2, $clients);
    }
} 