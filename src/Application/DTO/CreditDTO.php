<?php

namespace App\Application\DTO;

final class CreditDTO
{
    public string $name;
    public float $amount;
    public float $rate;
    public string $startDate;
    public string $endDate;
    public ?string $clientPin = null;
    
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'] ?? '';
        $dto->amount = (float)($data['amount'] ?? 0);
        
        // Безопасное преобразование процентной ставки
        $rateValue = $data['rate'] ?? '0%';
        if (is_string($rateValue)) {
            $dto->rate = (float)str_replace('%', '', $rateValue);
        } else {
            $dto->rate = (float)$rateValue;
        }
        
        $dto->startDate = $data['start_date'] ?? '';
        $dto->endDate = $data['end_date'] ?? '';
        $dto->clientPin = $data['client_pin'] ?? null;
        
        return $dto;
    }
    

} 