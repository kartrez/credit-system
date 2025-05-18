<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Repository\CreditRepositoryInterface;

class InMemoryCreditRepository implements CreditRepositoryInterface
{
    /**
     * @var Credit[]
     */
    private array $credits = [];
    
    /**
     * @var array<string, Credit[]>
     */
    private array $clientCredits = [];
    
    /**
     * @param Credit[] $initialCredits
     */
    public function __construct(array $initialCredits = [])
    {
        foreach ($initialCredits as $credit) {
            $this->save($credit);
        }
    }
    
    #[\Override]
    public function save(Credit $credit): void
    {
        $id = spl_object_hash($credit);
        $this->credits[$id] = $credit;
        
        $client = $credit->getClient();
        if ($client) {
            $clientPin = $client->getPin();
            if (!isset($this->clientCredits[$clientPin])) {
                $this->clientCredits[$clientPin] = [];
            }
            $this->clientCredits[$clientPin][$id] = $credit;
        }
    }
    
    #[\Override]
    public function findByClient(Client $client): array
    {
        $clientPin = $client->getPin();
        return $this->clientCredits[$clientPin] ?? [];
    }
    
    #[\Override]
    public function findAll(): array
    {
        return array_values($this->credits);
    }
} 