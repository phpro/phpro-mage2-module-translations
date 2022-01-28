<?php
declare(strict_types=1);

namespace Phpro\Translations\Api;

use Phpro\Translations\Model\Data\ExportStats;

interface ExportManagementInterface
{
    public function export(?array $locale): ExportStats;
}
