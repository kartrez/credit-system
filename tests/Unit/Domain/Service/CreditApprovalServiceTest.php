<?php

namespace App\Tests\Unit\Domain\Service;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\CreditApprovalRuleInterface;
use App\Domain\Service\CreditApprovalService;
use App\Domain\Service\CreditModifierInterface;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreditApprovalServiceTest extends TestCase
{
    private Client $client;
    private Credit $credit;
    
    protected function setUp(): void
    {
        $this->client = new Client(
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
        
        $this->credit = new Credit(
            'Test Loan',
            5000.0,
            10.0,
            new DateTimeImmutable('2023-01-01'),
            new DateTimeImmutable('2023-12-31')
        );
    }
    
    public function testApprovesWhenAllRulesPass(): void
    {
        // Создаем мок правила, которое всегда одобряет
        $rule = $this->createMock(CreditApprovalRuleInterface::class);
        $rule->method('check')->willReturn(true);
        
        $service = new CreditApprovalService([$rule]);
        $result = $service->check($this->client, $this->credit);
        
        $this->assertTrue($result['approved']);
        $this->assertEmpty($result['reasons']);
    }
    
    public function testRejectsWhenAnyRuleFails(): void
    {
        // Создаем мок правила, которое одобряет
        $approveRule = $this->createMock(CreditApprovalRuleInterface::class);
        $approveRule->method('check')->willReturn(true);
        
        // Создаем мок правила, которое отклоняет с причиной
        $rejectRule = $this->createMock(CreditApprovalRuleInterface::class);
        $rejectRule->method('check')->willReturn(false);
        $rejectRule->method('getFailureReason')->willReturn('Тестовая причина отказа');
        
        $service = new CreditApprovalService([$approveRule, $rejectRule]);
        $result = $service->check($this->client, $this->credit);
        
        $this->assertFalse($result['approved']);
        $this->assertNotEmpty($result['reasons']);
        $this->assertContains('Тестовая причина отказа', $result['reasons']);
    }
    
    public function testSetsClientOnCredit(): void
    {
        $rule = $this->createMock(CreditApprovalRuleInterface::class);
        $rule->method('check')->willReturn(true);
        
        $service = new CreditApprovalService([$rule]);
        $service->check($this->client, $this->credit);
        
        $this->assertSame($this->client, $this->credit->getClient());
    }
    
    public function testAppliesModificationsWhenApproved(): void
    {
        $rule = $this->createMock(CreditApprovalRuleInterface::class);
        $rule->method('check')->willReturn(true);
        
        $modifier = $this->createMock(CreditModifierInterface::class);
        $modifier->expects($this->once())
                ->method('modifyCredit')
                ->with($this->credit);
        
        $service = new CreditApprovalService([$rule], [$modifier]);
        $service->check($this->client, $this->credit);
    }
    
    public function testDoesNotApplyModificationsWhenRejected(): void
    {
        $rule = $this->createMock(CreditApprovalRuleInterface::class);
        $rule->method('check')->willReturn(false);
        $rule->method('getFailureReason')->willReturn('Тестовая причина отказа');
        
        $modifier = $this->createMock(CreditModifierInterface::class);
        $modifier->expects($this->never())
                ->method('modifyCredit');
        
        $service = new CreditApprovalService([$rule], [$modifier]);
        $service->check($this->client, $this->credit);
    }
    
    public function testAddsRuleDynamically(): void
    {
        $service = new CreditApprovalService();
        
        // Создаем мок правила, которое отклоняет с причиной
        $rule = $this->createMock(CreditApprovalRuleInterface::class);
        $rule->method('check')->willReturn(false);
        $rule->method('getFailureReason')->willReturn('Тестовая причина отказа');
        
        $service->addRule($rule);
        $result = $service->check($this->client, $this->credit);
        
        $this->assertFalse($result['approved']);
        $this->assertNotEmpty($result['reasons']);
    }
    
    public function testAddsModifierDynamically(): void
    {
        $rule = $this->createMock(CreditApprovalRuleInterface::class);
        $rule->method('check')->willReturn(true);
        
        $service = new CreditApprovalService([$rule]);
        
        $modifier = $this->createMock(CreditModifierInterface::class);
        $modifier->expects($this->once())
                ->method('modifyCredit')
                ->with($this->credit);
        
        $service->addModifier($modifier);
        $service->check($this->client, $this->credit);
    }
} 