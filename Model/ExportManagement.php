<?php
declare(strict_types=1);

namespace Phpro\Translations\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Phpro\Translations\Api\ExportManagementInterface;
use Phpro\Translations\Model\Data\ExportStats;

class ExportManagement implements ExportManagementInterface
{
    private const CSV_SUFFIX = '_export.csv';

    /**
     * @var Csv
     */
    private $csv;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var TranslationRepository
     */
    private $repository;
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * ExportManagement constructor.
     *
     * @param Csv $csv
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TranslationRepository $repository
     * @param DirectoryList $directoryList
     * @param TimezoneInterface $time
     */
    public function __construct(
        Csv $csv,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TranslationRepository $repository,
        DirectoryList $directoryList,
        TimezoneInterface $time
    ) {
        $this->csv = $csv;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->repository = $repository;
        $this->directoryList = $directoryList;
    }

    /**
     * @param array|null $locales
     * @return ExportStats
     */
    public function export(?array $locales): ExportStats
    {
        $this->csv->setDelimiter(',');
        $this->csv->setEnclosure('"');

        if (!empty($locales)) {
            $this->searchCriteriaBuilder->addFilter('locale', $locales, 'in');
        }

        $fileName = $this->getExportPath((empty($locales)) ? 'all' : implode('_', $locales));
        $result = $this->repository->getList($this->searchCriteriaBuilder->create());
        $this->csv->saveData($fileName, $this->formatResultsToCsvData($result));

        return ExportStats::fromRawData($fileName, $result->getTotalCount());
    }

    /**
     * @param \Magento\Framework\Api\SearchResultsInterface $result
     * @return \Generator
     */
    private function formatResultsToCsvData(\Magento\Framework\Api\SearchResultsInterface $result): \Generator
    {
        /**
         * @var \Phpro\Translations\Api\Data\TranslationInterface $item
         */
        foreach ($result->getItems() as $item) {
            yield [$item->getString(), $item->getTranslate(), $item->getLocale()];
        }
    }

    /**
     * @param string $suffix
     * @return string
     */
    private function getExportPath(string $suffix): string
    {
        $date = new \DateTime();
        $path = sprintf(
            '%s/translations',
            $this->directoryList->getPath(DirectoryList::VAR_DIR)
        );

        // phpcs:disable
        if (!file_exists($path)) {
            mkdir($path);
        }
        // phpcs:enable

        return sprintf(
            '%s/%s_export_%s.csv',
            $path,
            $date->format('Ymd_His'),
            $suffix
        );
    }
}
