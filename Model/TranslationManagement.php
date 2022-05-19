<?php
declare(strict_types=1);

namespace Phpro\Translations\Model;

use Phpro\Translations\Model\Data\TranslationFactory;
use Phpro\Translations\Model\Validator\LocaleValidator;

class TranslationManagement
{
    /**
     * @var LocaleValidator
     */
    private $localeValidator;
    /**
     * @var TranslationFactory
     */
    private $translationFactory;
    /**
     * @var TranslationRepository
     */
    private $repository;

    public function __construct(
        LocaleValidator $localeValidator,
        TranslationFactory $translationFactory,
        TranslationRepository $repository
    ) {
        $this->localeValidator = $localeValidator;
        $this->translationFactory = $translationFactory;
        $this->repository = $repository;
    }

    /**
     * @param string $translationKey
     * @param string $translationValue
     * @param string $locale
     * @param string $frontend
     * @throws \Exception
     */
    public function addTranslation(
        string $translationKey,
        string $translationValue,
        string $locale,
        string $frontend
    ): void {
        $this->localeValidator->validate($locale);

        /** @var Translation $translationModel */
        $translationModel = $this->translationFactory->create();

        $translationModel->setLocale($locale);
        $translationModel->setString($translationKey);
        $translationModel->setTranslate($translationValue);
        $translationModel->setFrontend($frontend);

        $this->repository->save($translationModel);
    }
}
