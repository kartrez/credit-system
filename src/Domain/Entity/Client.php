<?php

namespace App\Domain\Entity;

class Client
{
    private string $name;
    private int $age;
    private string $region;
    private string $city;
    private float $income;
    private int $score;
    private string $pin;
    private string $email;
    private string $phone;

    public function __construct(
        string $name,
        int $age,
        string $region,
        string $city,
        float $income,
        int $score,
        string $pin,
        string $email,
        string $phone
    ) {
        $this->name = $name;
        $this->age = $age;
        $this->region = $region;
        $this->city = $city;
        $this->income = $income;
        $this->score = $score;
        $this->pin = $pin;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getIncome(): float
    {
        return $this->income;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getPin(): string
    {
        return $this->pin;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
} 