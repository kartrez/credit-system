<?php

// Получаем список всех PHP-файлов в директории src
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('src')
);

$phpFiles = [];
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getPathname();
    }
}

// Обрабатываем каждый файл
foreach ($phpFiles as $file) {
    // Читаем содержимое файла
    $content = file_get_contents($file);
    
    // Фиксим файлы с начальным "final <?php"
    if (strpos($content, 'final <?php') === 0) {
        $content = str_replace('final <?php', '<?php', $content);
        
        // Добавляем final к нужному классу
        $content = preg_replace('/^(class\s+\w+)/', 'final $1', $content);
        
        // Записываем исправленное содержимое обратно в файл
        file_put_contents($file, $content);
        echo "Исправлен модификатор final: $file\n";
    }
    
    // Фиксим неправильные пространства имен
    if (strpos($content, 'App\Inffinal') !== false) {
        $content = str_replace('App\Inffinal rastructure', 'App\Infrastructure', $content);
        file_put_contents($file, $content);
        echo "Исправлен namespace: $file\n";
    }
    
    // Дополнительно проверяем отсутствие <?php в начале файла
    if (strpos($content, '<?php') !== 0) {
        // Если файл не начинается с <?php, добавляем его
        $pattern = '/^.*?(?=namespace|use|class)/s';
        $replacement = "<?php\n\n";
        $content = preg_replace($pattern, $replacement, $content);
        file_put_contents($file, $content);
        echo "Добавлен <?php в начало: $file\n";
    }
    
    // Исправляем проблемы с InvalidOperand (конкатенация со float)
    if (strpos($file, 'CreditController.php') !== false || 
        strpos($file, 'PragueRandomRejectRule.php') !== false) {
        
        // Исправляем конкатенацию float и строки
        $content = preg_replace('/(\$credit->getRate\(\)) \. (\'%\')/', '(string)$1 . $2', $content);
        
        // Исправляем конкатенацию с null|float
        $content = preg_replace('/(\(\$approvalResult\[\'approved\'\] \? \$credit->getRate\(\) : null\)) \. (\'%\')/', 
                              '(string)($approvalResult[\'approved\'] ? $credit->getRate() : \'\') . $2', $content);
        
        // Исправляем проблему с умножением в PragueRandomRejectRule
        $content = str_replace('(self::REJECTION_PROBABILITY * 100)', '(int)(self::REJECTION_PROBABILITY * 100)', $content);
        
        file_put_contents($file, $content);
        echo "Исправлены операции с типами: $file\n";
    }
}

echo "Готово!\n"; 