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

    /**
     * @param int $rowModel/Data/InlineGenerateStats.php
     * @param string $key
     */
    public function addCreated(int $row, string $key)
    {
        $this->created[$row] = $key;
    }

    /**
     * @return int
     */
    public function getCreatedCount(): int
    {
        return count($this->created);
    }

    /**
     * @param int $row
     * @param string $reason
     */
    public function addFailed(int $row, string $reason)
    {
        $this->failed[$row] = $reason;
    }

    /**
     * @return int
     */
    public function getFailedCount(): int
    {
        return count($this->failed);
    }

    /**
     * @param int $row
     * @param string $reason
     */
    public function addSkipped(int $row, string $reason)
    {
        $this->skipped[$row] = $reason;
    }

    /**
     * @return int
     */
    public function getSkippedCount(): int
    {
        return count($this->skipped);
    }
}
