<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Phpro\Translations\Controller\Adminhtml\Translation;

class GenerateJson extends Translation
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @inheridoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            __('Generate Frontend Translations'),
            __('Generate Frontend Translations')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Translations'));
        $resultPage->getConfig()->getTitle()->prepend(__('Generate Frontend Translations'));
        return $resultPage;
    }
}
