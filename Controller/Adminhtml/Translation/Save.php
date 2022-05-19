<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Phpro\Translations\Api\TranslationRepositoryInterface;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Phpro_Translations::Translation_save';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var TranslationRepositoryInterface
     */
    private $translationRepository;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        TranslationRepositoryInterface $translationRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->translationRepository = $translationRepository;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $translationId = $this->getRequest()->getParam('key_id');
        if (!$translationId || !$data) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $translationDataModel = $this->translationRepository->getById($translationId);

            // Fix issue: for some reason the "locale" of the backend-theme is changed if field was named "locale".
            $translationDataModel->setLocale($this->getRequest()->getParam('locale_field'));
            $translationDataModel->setString($this->getRequest()->getParam('string'));
            $translationDataModel->setTranslate($this->getRequest()->getParam('translate'));
            $translationDataModel->setFrontend($this->getRequest()->getParam('frontend'));

            $model = $this->translationRepository->save($translationDataModel);

            $this->messageManager->addSuccessMessage(__('The translation was successfully updated.'));
            $this->dataPersistor->clear('phpro_translations_translation');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['key_id' => $model->getKeyId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the translation.'));
        }

        $this->dataPersistor->set('phpro_translations_translation', $data);
        return $resultRedirect->setPath('*/*/edit', ['key_id' => $this->getRequest()->getParam('key_id')]);
    }
}
