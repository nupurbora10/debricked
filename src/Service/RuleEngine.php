<?php

namespace App\Service;

use App\Entity\ScanResult;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;

class RuleEngine
{
    private $notifier;

    public function __construct(NotifierInterface $notifier)
    {
        $this->notifier = $notifier;
    }

    public function evaluateRules(ScanResult $scanResult): void
    {
        if ($scanResult->getVulnerabilitiesCount() > 10) {
            $notification = new Notification('High vulnerability count!', ['email', 'slack']);
            $this->notifier->send($notification);
        }

    }
}