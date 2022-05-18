<?php
declare(strict_types=1);

namespace Phpro\Translations\ViewModel;

use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Phpro\Translations\Model\InlineTranslations\FileManager;

class JsTranslationConfig implements ArgumentInterface
{
    /**
     * @var FileManager
     */
    private $fileManager;
    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * JsTranslationConfig constructor.
     * @param FileManager $fileManager
     * @param DesignInterface $design
     */
    public function __construct(
        FileManager $fileManager,
        DesignInterface $design
    ) {
        $this->fileManager = $fileManager;
        $this->design = $design;
    }

    /**
     * @return string
     */
    public function getJsTranslationFilePath(): string
    {
        try {
            return $this->fileManager->getJsTranslationUrl(
                sprintf(
                    '%s/%s',
                    $this->design->getDesignTheme()->getThemePath(),
                    $this->design->getLocale()
                )
            );
        } catch (\Exception $e) {
            return '';
        }
    }
}
