<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Translation extends AbstractDb
{
    /**
     * @param string $translationKey
     * @param array $locales
     * @throws \Exception
     * @return $this
     */
    public function deleteByTranslationKeyAndLocales(string $translationKey, array $locales)
    {
        $conn = $this->getConnection();
        $connection = $this->transactionManager->start($conn);
        $quotedLocales = array_map(function ($t) use ($conn) {
            return $conn->quote($t);
        }, $locales);

        try {
            $this->objectRelationProcessor->delete(
                $this->transactionManager,
                $connection,
                $this->getMainTable(),
                $conn->quoteInto(' string = ?', $translationKey)
                . ' AND locale IN (' . implode(',', $quotedLocales) . ')',
                []
            );
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }
        return $this;
    }
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('translation', 'key_id');
    }
}
