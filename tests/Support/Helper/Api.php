<?php

namespace Tests\Support\Helper;

use Codeception\Module;

class Api extends Module
{
    // Вспомогательные методы для API-тестов
    
    /**
     * Проверяет, что условие истинно
     * 
     * @param bool $condition Проверяемое условие
     * @param string $message Сообщение об ошибке
     */
    public function assertTrue($condition, $message = '')
    {
        \PHPUnit\Framework\Assert::assertTrue($condition, $message);
    }
} 