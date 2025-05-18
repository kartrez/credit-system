<?php

namespace App\Infrastructure\Service;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\NotificationServiceInterface;
use Psr\Log\LoggerInterface;

class LoggerNotificationService implements NotificationServiceInterface
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    #[\Override]
    public function notifyClientAboutCreditApproval(Client $client, Credit $credit, bool $approved, array $reasons = []): void
    {
        $status = $approved ? 'одобрен' : 'отклонен';
        $message = sprintf(
            '[%s] Уведомление клиенту %s: Кредит %s.',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $client->getName(),
            $status
        );
        
        if (!$approved && !empty($reasons)) {
            $message .= ' Причины: ' . implode(', ', $reasons);
        }
        
        $this->logger->info($message);
    }
} 