<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Phpro\Translations\Controller\Adminhtml\Translation;

class NewAction extends Translation
{
    const ADMIN_RESOURCE = 'Phpro_Translations::Translation_new_action';

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
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(__('New Translation'), __('New Translation'));
        $resultPage->getConfig()->getTitle()->prepend(__('Translations'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Translation'));

        return $resultPage;
    }
}
