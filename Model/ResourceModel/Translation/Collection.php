<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\ResourceModel\Translation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Phpro\Translations\Model\Translation::class,
            \Phpro\Translations\Model\ResourceModel\Translation::class
        );
    }
}
