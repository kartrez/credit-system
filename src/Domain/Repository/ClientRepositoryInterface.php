<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Client;

interface ClientRepositoryInterface
{
    public function findByPin(string $pin): ?Client;
    
    public function save(Client $client): void;
    
    public function findAll(): array;
} 