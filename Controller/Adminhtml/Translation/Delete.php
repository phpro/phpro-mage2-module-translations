<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action\Context;
use Phpro\Translations\Api\TranslationRepositoryInterface;
use Phpro\Translations\Controller\Adminhtml\Translation;

class Delete extends Translation
{
    const ADMIN_RESOURCE = 'Phpro_Translations::Translation_delete';

    /**
     * @var TranslationRepositoryInterface
     */
    private $translationRepository;

    public function __construct(
        Context $context,
        TranslationRepositoryInterface $translationRepository
    ) {
        parent::__construct($context);
        $this->translationRepository = $translationRepository;
    }

    /**
     * @inheridoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('key_id');
        if ($id) {
            try {
                $this->translationRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the Translation.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['key_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a Translation to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
