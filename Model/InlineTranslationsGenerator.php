<?php
declare(strict_types=1);

namespace Phpro\Translations\Model;

use Magento\Framework\App\State;
use Magento\Framework\App\View\Deployment\Version\StorageInterface;
use Magento\Framework\TranslateInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Translation\Model\Js\DataProviderInterface;
use Phpro\Translations\Model\Data\InlineGenerateStats;
use Phpro\Translations\Model\Data\InlineGenerateStatsCollection;
use Phpro\Translations\Model\Data\StoreThemePathCollection;
use Phpro\Translations\Model\InlineTranslations\FileManager;

class InlineTranslationsGenerator
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;
    /**
     * @var State
     */
    private $state;
    /**
     * @var TranslateInterface
     */
    private $translate;
    /**
     * @var FileManager
     */
    private $fileManager;
    /**
     * @var Emulation
     */
    private $emulation;
    /**
     * @var DesignInterface
     */
    private $viewDesign;
    /**
     * @var SystemStore
     */
    private $systemStore;

    public function __construct(
        DataProviderInterface $dataProvider,
        State $state,
        TranslateInterface $translate,
        FileManager $fileManager,
        Emulation $emulation,
        DesignInterface $viewDesign,
        SystemStore $systemStore
    ) {
        $this->dataProvider = $dataProvider;
        $this->state = $state;
        $this->translate = $translate;
        $this->fileManager = $fileManager;
        $this->emulation = $emulation;
        $this->viewDesign = $viewDesign;
        $this->systemStore = $systemStore;
    }

    /**
     * @return InlineGenerateStatsCollection
     */
    public function forAll(): InlineGenerateStatsCollection
    {
        $storeIds = [];
        foreach ($this->systemStore->getStoreCollection() as $store) {
            $storeIds[] = (int)$store->getId();
        }

        return $this->forStores($storeIds);
    }

    /**
     * @param array $storeIds
     * @return InlineGenerateStatsCollection
     */
    public function forStores(array $storeIds): InlineGenerateStatsCollection
    {
        $statsCollection = new InlineGenerateStatsCollection();
        foreach ($storeIds as $storeId) {
            $statsCollection->add(
                $this->generate((int)$storeId)
            );
        }

        return $statsCollection;
    }

    public function forStoresWithThemePath(StoreThemePathCollection $stores)
    {
        $statsCollection = new InlineGenerateStatsCollection();
        foreach ($stores as $store) {
            $statsCollection->add(
                $this->generate($store->getStoreId(), $store->getPath())
            );
        }

        return $statsCollection;
    }

    /**
     * @param int $storeId
     * @param string|null $themePath
     * @throws \Exception
     * @return InlineGenerateStats
     */
    private function generate(int $storeId, string $themePath = null): InlineGenerateStats
    {
        $translations = [];
        $area = 'frontend';

        $this->state->emulateAreaCode($area, function () use ($storeId, $area, &$translations, $themePath) {
            // We need the emulation start and stop for saving the translation json file to have the correct context
            $this->emulation->startEnvironmentEmulation($storeId, $area, true);
            $locale = $this->viewDesign->getLocale();
            if (null === $themePath) {
                $themePath = $this->viewDesign->getDesignTheme()->getThemePath();
            }
            // set locale and load translations string:
            $this->translate
                ->setLocale($locale)
                ->loadData($area, true);
            // find all translations for the frontend files (js en html templates):
            $translations = $this->dataProvider->getData($themePath);
            $this->fileManager->writeJsTranslationFileContent($translations, $themePath . '/' . $locale);
            $this->emulation->stopEnvironmentEmulation();
        });

        $storeInfo = $this->systemStore->getStoreNameWithWebsite($storeId);
        return new InlineGenerateStats($storeInfo, $storeId, count($translations));
    }
}
