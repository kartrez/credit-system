<?php

namespace App\Domain\Service;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;

interface NotificationServiceInterface
{
    public function notifyClientAboutCreditApproval(Client $client, Credit $credit, bool $approved, array $reasons = []): void;
} 