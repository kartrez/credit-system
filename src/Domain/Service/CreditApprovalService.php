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
     * @var CreditModifierInterface[]
     */
    private array $modifiers;
    
    /**
     * @param CreditApprovalRuleInterface[] $rules
     * @param CreditModifierInterface[] $modifiers
     */
    public function __construct(array $rules = [], array $modifiers = [])
    {
        $this->rules = $rules;
        $this->modifiers = $modifiers;
    }
    
    /**
     * Добавляет новое правило проверки кредита
     */
    public function addRule(CreditApprovalRuleInterface $rule): void
    {
        $this->rules[] = $rule;
    }
    
    /**
     * Добавляет новый модификатор кредита
     */
    public function addModifier(CreditModifierInterface $modifier): void
    {
        $this->modifiers[] = $modifier;
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
            foreach ($this->modifiers as $modifier) {
                $modifier->modifyCredit($credit);
            }
        }
        
        return $result;
    }
} 