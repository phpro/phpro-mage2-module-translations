<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Data;

class ImportStats
{
    /**
     * @var array
     */
    private $created = [];

    /**
     * @var array
     */
    private $skipped = [];

    /**
     * @var array
     */
    private $failed = [];

    public function addCreated(int $row, string $key)
    {
        $this->created[$row] = $key;
    }

    public function getCreatedCount(): int
    {
        return count($this->created);
    }

    public function addFailed(int $row, string $reason)
    {
        $this->failed[$row] = $reason;
    }

    public function getFailedCount(): int
    {
        return count($this->failed);
    }

    public function addSkipped(int $row, string $reason)
    {
        $this->skipped[$row] = $reason;
    }

    public function getSkippedCount(): int
    {
        return count($this->skipped);
    }
}
