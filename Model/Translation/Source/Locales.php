<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Translation\Source;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManager;

class Locales implements OptionSourceInterface
{
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManager $storeManager,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $stores = $this->storeManager->getStores(true);
        ksort($stores);
        $result = [];
        foreach ($stores as $store) {
            $locale = $this->scopeConfig->getValue(Data::XML_PATH_DEFAULT_LOCALE, 'stores', $store->getId());

            if (isset($result[$locale])) {
                continue;
            }
            $result[$locale] = ['value' => $locale, 'label' => $locale];
        }

        return $result;
    }
}
