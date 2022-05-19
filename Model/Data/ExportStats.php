<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Data;

class ExportStats
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var int
     */
    private $totalRows;

    public function __construct(string $fileName, int $totalRows)
    {
        $this->fileName = $fileName;
        $this->totalRows = $totalRows;
    }

    /**
     * @param string $fileName
     * @param int $totalRows
     * @return ExportStats
     */
    public static function fromRawData(string $fileName, int $totalRows): ExportStats
    {
        return new self($fileName, $totalRows);
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return int
     */
    public function getTotalRows(): int
    {
        return $this->totalRows;
    }
}
