<?php

namespace App\Domain\Entity;

use DateTimeInterface;

class Credit
{
    private string $name;
    private float $amount;
    private float $rate;
    private DateTimeInterface $startDate;
    private DateTimeInterface $endDate;
    private ?Client $client = null;

    public function __construct(
        string $name,
        float $amount,
        float $rate,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate
    ) {
        $this->name = $name;
        $this->amount = $amount;
        $this->rate = $rate;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeInterface
    {
        return $this->endDate;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
} 