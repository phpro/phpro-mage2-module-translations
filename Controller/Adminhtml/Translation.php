<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

abstract class Translation extends Action
{
    const ADMIN_RESOURCE = 'Phpro_Translations::translations';

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Phpro'), __('Phpro'))
            ->addBreadcrumb(__('Translations'), __('Translations'));
        return $resultPage;
    }
}
