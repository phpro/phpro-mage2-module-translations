<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Translation\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManager;

class Locales implements OptionSourceInterface
{
    private const XML_PATH_LOCALE = 'general/locale/code';

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
        $stores = $this->storeManager->getStores();

        $result = [];
        foreach ($stores as $store) {
            $locale = $this->scopeConfig->getValue(self::XML_PATH_LOCALE, 'stores', $store->getId());
            $result[] = ['value' => $locale, 'label' => $store->getName()];
        }

        return $result;
    }
}
