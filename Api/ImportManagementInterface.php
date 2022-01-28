<?php
declare(strict_types=1);

namespace Phpro\Translations\Api;

use Phpro\Translations\Model\Data\ImportStats;

interface ImportManagementInterface
{
    /**
     * Import Magento translation CSV to the database.
     * Example structure: "Foo","Foo value",module,Magento_Catalog
     *
     * @param string $filePath
     * @param string $locale
     * @throws \Exception
     * @return ImportStats
     */
    public function importMagentoCsv(string $filePath, string $locale): ImportStats;

    /**
     * Import translation CSV (based on export) to the database.
     * Example structure: "Bar","Bar value",en_US
     *
     * @param string $filePath
     * @return ImportStats
     */
    public function importFull(string $filePath): ImportStats;
}
