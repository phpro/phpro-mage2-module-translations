<?php
declare(strict_types=1);

namespace Phpro\Translations\Controller\Adminhtml\Translation;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Phpro\Translations\Model\TranslationManagement;

class SaveNew extends Action
{
    private const ADMIN_RESOURCE = 'Phpro_Translations::Translation_save';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var TranslationManagement
     */
    private $translationManagement;

    /**
     * SaveNew constructor.
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param TranslationManagement $translationManagement
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        TranslationManagement $translationManagement
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->translationManagement = $translationManagement;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->createTranslations($this->getRequest());
            $this->dataPersistor->clear('phpro_translations_translation');
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the translation.'));
        }

        $this->dataPersistor->set('phpro_translations_translation', $data);
        return $resultRedirect->setPath('*/*/new');
    }

    /**
     * Create all translations for given locales.
     *
     * @param RequestInterface $request
     */
    private function createTranslations(RequestInterface $request): void
    {
        $locales = $request->getParam('locale_field');
        $translationKey = $this->getRequest()->getParam('string');
        $translationValue = $this->getRequest()->getParam('translate');
        $frontend = $this->getRequest()->getParam('frontend');
        $successSavedLocales = [];
        foreach ($locales as $locale) {
            try {
                $this->translationManagement->addTranslation(
                    $translationKey,
                    $translationValue,
                    $locale,
                    $frontend
                );
                $successSavedLocales[] = $locale;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    __('Translation save failed for locale %1: %2', $locale, $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Translation save failed for locale %1: unknown error.')
                );
            }
        }

        if (0 >= count($successSavedLocales)) {
            return;
        }

        $this->messageManager->addSuccessMessage(
            __('The translation was successfully created for locale(s) %1.', implode(', ', $successSavedLocales))
        );
    }
}
