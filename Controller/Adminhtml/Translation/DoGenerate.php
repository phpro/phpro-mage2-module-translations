<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\Cache\Type\Translate;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\PageCache\Model\Cache\Type;
use Phpro\Translations\Model\Data\InlineGenerateStatsCollection;
use Phpro\Translations\Model\InlineTranslationsGenerator;

class DoGenerate extends Action
{
    /**
     * @var InlineTranslationsGenerator
     */
    private $inlineTranslationsGenerator;
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    /**
     * DoGenerate constructor.
     *
     * @param Context $context
     * @param InlineTranslationsGenerator $inlineTranslationsGenerator
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        InlineTranslationsGenerator $inlineTranslationsGenerator,
        TypeListInterface $cacheTypeList
    ) {
        $this->inlineTranslationsGenerator = $inlineTranslationsGenerator;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!isset($data['storeviews'])) {
            return $resultRedirect->setPath('*/*/generatejson');
        }

        $statsCollection = $this->generatedTranslations(
            array_map(function ($storeId) {
                return (int)$storeId;
            }, $data['storeviews'])
        );

        $this->messageManager->addComplexSuccessMessage(
            'inlineGenerateSuccessMessage',
            [
                'statsItems' => $statsCollection->toArray()
            ]
        );

        $this->cacheTypeList->invalidate(Type::TYPE_IDENTIFIER);
        $this->cacheTypeList->invalidate(Block::TYPE_IDENTIFIER);
        $this->cacheTypeList->invalidate(Translate::TYPE_IDENTIFIER);

        return $resultRedirect->setPath('*/*/generatejson');
    }

    /**
     * @param array $storeIds
     * @return InlineGenerateStatsCollection
     */
    private function generatedTranslations(array $storeIds): InlineGenerateStatsCollection
    {
        if (isset($storeIds[0]) && (0 === $storeIds[0])) {
            return $this->inlineTranslationsGenerator->forAll();
        }

        return $this->inlineTranslationsGenerator->forStores($storeIds);
    }
}
