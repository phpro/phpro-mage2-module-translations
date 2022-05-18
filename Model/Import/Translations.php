<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Phpro\Translations\Model\Validator\LocaleValidator;

class Translations extends AbstractEntity
{
    private const ENTITY_CODE = 'translations';
    private const TABLE = 'translation';

    public const COL_KEY = 'string';
    public const COL_TRANSLATION = 'translate';
    public const COL_LOCALE = 'locale';

    public const HEADER_KEY = 'key';
    public const HEADER_TRANSLATION = 'translation';
    public const HEADER_LOCALE = 'locale';

    /**
     * @var string[]
     */
    protected $validColumnNames = [
        self::HEADER_KEY,
        self::HEADER_TRANSLATION,
        self::HEADER_LOCALE,
    ];

    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * @var int
     */
    protected $countTotal = 0;

    /**
     * @var LocaleValidator
     */
    private $localeValidator;

    /**
     * Translations constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param ImportHelper $importExportData
     * @param Data $importData
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param LocaleValidator $localeValidator
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        LocaleValidator $localeValidator
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->localeValidator = $localeValidator;
        $this->initMessageTemplates();
    }

    /**
     * Row validation
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum): bool
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $translationKey = $rowData[self::HEADER_KEY] ?? '';
        $locale = $rowData[self::HEADER_LOCALE] ?? '';

        if ('' === trim($translationKey)) {
            $this->addRowError('KeyIsRequired', $rowNum);
        }

        try {
            $this->localeValidator->validate($locale);
        } catch (\Exception $e) {
            $this->addRowError('LocaleNotSupported', $rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_CODE;
    }

    /**
     * @return string
     */
    public function getCreatedItemsCount()
    {
        return 'n/a';
    }

    /**
     * @return string
     */
    public function getDeletedItemsCount()
    {
        return 'n/a';
    }

    /**
     * @return string
     */
    public function getUpdatedItemsCount()
    {
        return sprintf(
            '%s <strong>created/updated</strong>, Total: %s',
            $this->countItemsUpdated,
            $this->countTotal
        );
    }

    /**
     * Import data
     *
     * @throws \Exception
     * @return bool
     */
    protected function _importData(): bool
    {
        if (Import::BEHAVIOR_APPEND === $this->getBehavior()) {
            $this->saveAndReplaceEntity();
            return true;
        }

        throw new \Exception(
            sprintf(
                'Behavior %s not supported. Only add/update is supported.',
                $this->getBehavior()
            )
        );
    }

    /**
     * Save and replace entities
     *
     * @return void
     */
    private function saveAndReplaceEntity(): void
    {
        $behavior = $this->getBehavior();
        $this->countTotal = 0;
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $row) {
                if (!$this->validateRow($row, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $entityList[] = [
                    self::COL_KEY => $row[self::HEADER_KEY],
                    self::COL_TRANSLATION => $row[self::HEADER_TRANSLATION],
                    self::COL_LOCALE => $row[self::HEADER_LOCALE],
                ];
                $this->countTotal++;
            }

            $this->countTotal++;
            if (Import::BEHAVIOR_APPEND === $behavior) {
                $this->countItemsUpdated += $this->insertOnDuplicate($entityList);
            }
        }
    }

    /**
     * Save entities
     *
     * @param array $entityList
     * @return int
     */
    private function insertOnDuplicate(array $entityList): int
    {
        if ($entityList) {
            $tableName = $this->_connection->getTableName(static::TABLE);
            return (int) $this->_connection->insertOnDuplicate(
                $tableName,
                $entityList,
                [
                    self::COL_TRANSLATION,
                    self::COL_LOCALE,
                ]
            );
        }

        return 0;
    }

    /**
     * Init error messages
     */
    private function initMessageTemplates(): void
    {
        $this->addMessageTemplate('KeyIsRequired', __('The key cannot be empty.'));
        $this->addMessageTemplate('LocaleNotSupported', __('The locale is not supported.'));
    }
}
