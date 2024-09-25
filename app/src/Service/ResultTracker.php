<?php

declare(strict_types=1);

namespace App\Service;

class ResultTracker
{
    private int $successCount = 0;
    private int $failureCount = 0;

    public function incrementSuccess(): void
    {
        $this->successCount++;
    }

    public function incrementFailure(): void
    {
        $this->failureCount++;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailureCount(): int
    {
        return $this->failureCount;
    }
}
