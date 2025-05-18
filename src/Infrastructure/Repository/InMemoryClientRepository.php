<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Client;
use App\Domain\Repository\ClientRepositoryInterface;

class InMemoryClientRepository implements ClientRepositoryInterface
{
    /**
     * @var Client[]
     */
    private array $clients = [];
    
    /**
     * @param Client[] $initialClients
     */
    public function __construct(array $initialClients = [])
    {
        foreach ($initialClients as $client) {
            $this->save($client);
        }
    }
    
    #[\Override]
    public function findByPin(string $pin): ?Client
    {
        return $this->clients[$pin] ?? null;
    }
    
    #[\Override]
    public function save(Client $client): void
    {
        $this->clients[$client->getPin()] = $client;
    }
    
    #[\Override]
    public function findAll(): array
    {
        return array_values($this->clients);
    }
} 