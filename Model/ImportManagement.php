<?php
declare(strict_types=1);

namespace Phpro\Translations\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\File\Csv;
use Phpro\Translations\Api\ImportManagementInterface;
use Phpro\Translations\Model\Data\ImportStats;
use Phpro\Translations\Model\Data\ImportStatsFactory;
use Phpro\Translations\Model\Data\Translation;
use Phpro\Translations\Model\Data\TranslationFactory;
use Phpro\Translations\Model\Validator\LocaleValidator;

class ImportManagement implements ImportManagementInterface
{
    /**
     * @var Csv
     */
    private $csv;
    /**
     * @var LocaleValidator
     */
    private $localeValidator;
    /**
     * @var TranslationFactory
     */
    private $translationFactory;
    /**
     * @var TranslationRepository
     */
    private $repository;
    /**
     * @var ImportStatsFactory
     */
    private $importStatsFactory;

    public function __construct(
        Csv $csv,
        LocaleValidator $localeValidator,
        TranslationFactory $translationFactory,
        TranslationRepository $repository,
        ImportStatsFactory $importStatsFactory
    ) {
        $this->csv = $csv;
        $this->localeValidator = $localeValidator;
        $this->translationFactory = $translationFactory;
        $this->repository = $repository;
        $this->importStatsFactory = $importStatsFactory;
    }

    public function importMagentoCsv(string $filePath, string $locale): ImportStats
    {
        $this->localeValidator->validate($locale);

        $this->csv->setDelimiter(',');
        $this->csv->setEnclosure('"');
        $csvData = $this->csv->getData($filePath);

        /** @var ImportStats $importStats */
        $importStats = $this->importStatsFactory->create();

        foreach ($csvData as $row => $data) {
            $rowNumber = $row + 1;
            try {
                if (2 > count($data)) {
                    throw new \Exception('Invalid CSV row');
                }
                if (empty($data[0]) || empty($data[1])) {
                    throw new \Exception('Empty values in CSV row');
                }
                $this->createTranslations($data[0], $data[1], $locale);
                $importStats->addCreated($rowNumber, $data[0]);
            } catch (\Exception $e) {
                $this->handleException($e, $importStats, $rowNumber);
            }
        }

        return $importStats;
    }

    public function importFull(string $filePath): ImportStats
    {
        $this->csv->setDelimiter(',');
        $this->csv->setEnclosure('"');
        $csvData = $this->csv->getData($filePath);

        /** @var ImportStats $importStats */
        $importStats = $this->importStatsFactory->create();

        foreach ($csvData as $row => $data) {
            $rowNumber = $row + 1;
            try {
                if (3 > count($data)) {
                    throw new \Exception('Invalid CSV row');
                }
                if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                    throw new \Exception('Empty values in CSV row');
                }
                $this->localeValidator->validate($data[2]);
                $this->createTranslations($data[0], $data[1], $data[2]);
                $importStats->addCreated($rowNumber, $data[0]);
            } catch (\Exception $e) {
                $this->handleException($e, $importStats, $rowNumber);
            }
        }

        return $importStats;
    }

    private function createTranslations(string $translationKey, string $translationValue, string $locale)
    {
        /** @var Translation $translationModel */
        $translationModel = $this->translationFactory->create();

        $translationModel->setLocale($locale);
        $translationModel->setString($translationKey);
        $translationModel->setTranslate($translationValue);

        $this->repository->save($translationModel);
    }

    private function handleException(\Exception $e, ImportStats $importStats, int $rowNumber)
    {
        if ($e instanceof CouldNotSaveException && (false !== strpos(strtolower($e->getMessage()), 'unique'))) {
            $importStats->addSkipped($rowNumber, 'Translation already exist.');
            return;
        }

        $importStats->addFailed($rowNumber, $e->getMessage());
    }
}
