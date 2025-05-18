<?php

namespace App\Tests\Unit\Domain\Service\Rules;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\Rules\AgeApprovalRule;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class AgeApprovalRuleTest extends TestCase
{
    private AgeApprovalRule $rule;
    private Credit $credit;
    
    protected function setUp(): void
    {
        $this->rule = new AgeApprovalRule();
        $this->credit = new Credit(
            'Test Loan',
            5000.0,
            10.0,
            new DateTimeImmutable('2023-01-01'),
            new DateTimeImmutable('2023-12-31')
        );
    }
    
    public function testApprovalWithValidAge(): void
    {
        $client = new Client(
            'Test User',
            30, // возраст в допустимом диапазоне
            'PR',
            'Prague',
            2000.0,
            700,
            'TEST-12345',
            'test@example.com',
            '+42012345678'
        );
        
        $result = $this->rule->check($client, $this->credit);
        
        $this->assertTrue($result);
        $this->assertEmpty($this->rule->getFailureReason());
    }
    
    public function testRejectionWithAgeTooLow(): void
    {
        $client = new Client(
            'Young User',
            17, // возраст меньше минимально допустимого (18)
            'PR',
            'Prague',
            2000.0,
            700,
            'TEST-12345',
            'young@example.com',
            '+42012345678'
        );
        
        $result = $this->rule->check($client, $this->credit);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->rule->getFailureReason());
    }
    
    public function testRejectionWithAgeTooHigh(): void
    {
        $client = new Client(
            'Old User',
            61, // возраст больше максимально допустимого (60)
            'PR',
            'Prague',
            2000.0,
            700,
            'TEST-12345',
            'old@example.com',
            '+42012345678'
        );
        
        $result = $this->rule->check($client, $this->credit);
        
        $this->assertFalse($result);
        $this->assertNotEmpty($this->rule->getFailureReason());
    }
    
    public function testModifyCreditDoesNothing(): void
    {
        $originalRate = $this->credit->getRate();
        
        $this->rule->modifyCredit($this->credit);
        
        // Правило не должно изменять кредит
        $this->assertEquals($originalRate, $this->credit->getRate());
    }
} 