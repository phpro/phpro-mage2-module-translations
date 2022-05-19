<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Phpro\Translations\Api\TranslationRepositoryInterface;
use Phpro\Translations\Controller\Adminhtml\Translation;

class Edit extends Translation
{
    public const ADMIN_RESOURCE = 'Phpro_Translations::Translation_update';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var TranslationRepositoryInterface
     */
    private $translationRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        TranslationRepositoryInterface $translationRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->translationRepository = $translationRepository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $translationId = $this->getRequest()->getParam('key_id');
            $translationDataModel = $this->translationRepository->getById($translationId);
            $this->coreRegistry->register('phpro_translations_translation', $translationDataModel);
            $resultPage = $this->resultPageFactory->create();

            $this->initPage($resultPage)->addBreadcrumb(__('Edit Translation'), __('Edit Translation'));
            $resultPage->getConfig()->getTitle()->prepend(__('Translations'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Translation %1', $translationDataModel->getKeyId()));

            return $resultPage;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong during loading translation.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
