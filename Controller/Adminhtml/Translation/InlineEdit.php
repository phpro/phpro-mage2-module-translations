<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Phpro\Translations\Model\TranslationFactory;

class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @var TranslationFactory
     */
    private $translationFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        TranslationFactory $translationFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->translationFactory = $translationFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $entityId) {
                    $model = $this->translationFactory->create()->load($entityId);
                    try {
                        // phpcs:ignore
                        $model->setData(array_merge($model->getData(), $postItems[$entityId]));
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Translation ID: {$entityId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
