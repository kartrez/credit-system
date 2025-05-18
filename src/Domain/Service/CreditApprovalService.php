<?php

namespace App\Domain\Service;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;

final class CreditApprovalService
{
    /**
     * @var CreditApprovalRuleInterface[]
     */
    private array $rules;
    
    /**
     * @param CreditApprovalRuleInterface[] $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }
    
    /**
     * Добавляет новое правило проверки кредита
     */
    public function addRule(CreditApprovalRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }
    
    public function check(Client $client, Credit $credit): array
    {
        $credit->setClient($client);
        $result = [
            'approved' => true,
            'reasons' => [],
        ];
        
        foreach ($this->rules as $rule) {
            if (!$rule->check($client, $credit)) {
                $result['approved'] = false;
                $result['reasons'][] = $rule->getFailureReason();
            }
        }
        
        // Если кредит одобрен, применяем все модификации
        if ($result['approved']) {
            foreach ($this->rules as $rule) {
                $rule->modifyCredit($credit);
            }
        }
        
        return $result;
    }
} 