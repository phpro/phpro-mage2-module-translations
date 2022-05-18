<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Translation\Source;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\OptionSourceInterface;

class Locales implements OptionSourceInterface
{
    private const XML_PATH_LOCALE = 'general/locale/code';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * Locales constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];
        $connection = $this->resourceConnection->getConnection();
        $bind = [':config_path' => self::XML_PATH_LOCALE];
        $select = $connection
            ->select()
            ->from($this->resourceConnection->getTableName('core_config_data'), 'value')
            ->distinct(true)
            ->where('path = :config_path');
        $rowSet = $connection->fetchAll($select, $bind);

        foreach ($rowSet as $row) {
            $result[] = ['value' => $row['value'], 'label' => $row['value']];
        }

        return $result;
    }
}
