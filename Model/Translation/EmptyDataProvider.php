<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Translation;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;

class EmptyDataProvider extends AbstractDataProvider
{
    /**
     * @inheridoc
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }

    /**
     * @inheridoc
     */
    public function getData()
    {
        return [];
    }
}
