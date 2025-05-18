<?php

namespace App\Application\Service;

use App\Application\DTO\ClientDTO;
use App\Domain\Entity\Client;
use App\Domain\Repository\ClientRepositoryInterface;

final class ClientService
{
    private ClientRepositoryInterface $clientRepository;
    
    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function createClient(ClientDTO $clientDTO): Client
    {
        $client = new Client(
            $clientDTO->name,
            $clientDTO->age,
            $clientDTO->region,
            $clientDTO->city,
            $clientDTO->income,
            $clientDTO->score,
            $clientDTO->pin,
            $clientDTO->email,
            $clientDTO->phone
        );
        
        $this->clientRepository->save($client);
        
        return $client;
    }
    
    public function getClientByPin(string $pin): ?Client
    {
        return $this->clientRepository->findByPin($pin);
    }
    
    public function getAllClients(): array
    {
        return $this->clientRepository->findAll();
    }
} 