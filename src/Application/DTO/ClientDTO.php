<?php

namespace App\Application\DTO;

class ClientDTO
{
    public string $name;
    public int $age;
    public string $region;
    public string $city;
    public float $income;
    public int $score;
    public string $pin;
    public string $email;
    public string $phone;
    
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'] ?? '';
        $dto->age = (int)($data['age'] ?? 0);
        $dto->region = $data['region'] ?? '';
        $dto->city = $data['city'] ?? '';
        $dto->income = (float)($data['income'] ?? 0);
        $dto->score = (int)($data['score'] ?? 0);
        $dto->pin = $data['pin'] ?? '';
        $dto->email = $data['email'] ?? '';
        $dto->phone = $data['phone'] ?? '';
        
        return $dto;
    }
    

} 