<?php
declare(strict_types=1);

namespace Phpro\Translations\Api;

use Phpro\Translations\Model\Data\ExportStats;

interface ExportManagementInterface
{
    /**
     * Export function
     *
     * @param array|null $locale
     * @return ExportStats
     */
    public function export(?array $locale): ExportStats;
}
