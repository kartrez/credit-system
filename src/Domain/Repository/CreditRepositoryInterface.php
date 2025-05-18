<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Credit;
use App\Domain\Entity\Client;

interface CreditRepositoryInterface
{
    public function save(Credit $credit): void;
    
    public function findByClient(Client $client): array;
    
    public function findAll(): array;
} 