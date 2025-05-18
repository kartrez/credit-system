<?php

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreditTest extends TestCase
{
    private Credit $credit;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;
    
    protected function setUp(): void
    {
        $this->startDate = new DateTimeImmutable('2023-01-01');
        $this->endDate = new DateTimeImmutable('2023-12-31');
        
        $this->credit = new Credit(
            'Test Loan',
            5000.0,
            10.0,
            $this->startDate,
            $this->endDate
        );
    }
    
    public function testGetName(): void
    {
        $this->assertEquals('Test Loan', $this->credit->getName());
    }
    
    public function testGetAmount(): void
    {
        $this->assertEquals(5000.0, $this->credit->getAmount());
    }
    
    public function testGetAndSetRate(): void
    {
        $this->assertEquals(10.0, $this->credit->getRate());
        
        $this->credit->setRate(12.5);
        $this->assertEquals(12.5, $this->credit->getRate());
    }
    
    public function testGetStartDate(): void
    {
        $this->assertSame($this->startDate, $this->credit->getStartDate());
    }
    
    public function testGetEndDate(): void
    {
        $this->assertSame($this->endDate, $this->credit->getEndDate());
    }
    
    public function testGetAndSetClient(): void
    {
        $this->assertNull($this->credit->getClient());
        
        $client = new Client(
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
        
        $this->credit->setClient($client);
        $this->assertSame($client, $this->credit->getClient());
    }
} 