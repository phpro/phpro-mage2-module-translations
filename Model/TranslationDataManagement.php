<?php
declare(strict_types=1);

namespace Phpro\Translations\Model;

use Phpro\Translations\Api\TranslationDataManagementInterface;
use Phpro\Translations\Model\Data\TranslationFactory;
use Phpro\Translations\Model\Translation\Source\Locales as LocaleSource;
use Phpro\Translations\Model\Validator\LocaleValidator;
use Psr\Log\LoggerInterface;

class TranslationDataManagement implements TranslationDataManagementInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var LocaleValidator
     */
    private $localeValidator;
    /**
     * @var LocaleSource
     */
    private $localeSource;
    /**
     * @var TranslationFactory
     */
    private $translationFactory;
    /**
     * @var TranslationRepository
     */
    private $repository;
    /**
     * @var array
     */
    private $enabledLocales = [];
    /**
     * @var bool
     */
    private $throwException;

    /**
     * TranslationDataManagement constructor.
     *
     * @param LoggerInterface $logger
     * @param LocaleValidator $localeValidator
     * @param LocaleSource $localeSource
     * @param TranslationFactory $translationFactory
     * @param TranslationRepository $repository
     * @param bool $throwException
     */
    public function __construct(
        LoggerInterface $logger,
        LocaleValidator $localeValidator,
        LocaleSource $localeSource,
        TranslationFactory $translationFactory,
        TranslationRepository $repository,
        bool $throwException = false
    ) {
        $this->logger = $logger;
        $this->localeValidator = $localeValidator;
        $this->localeSource = $localeSource;
        $this->translationFactory = $translationFactory;
        $this->repository = $repository;
        $this->throwException = $throwException;
    }

    /**
     * @inheritdoc
     */
    public function prepare(string $translationKey, ?string $defaultTranslation = null)
    {
        $locales = $this->getEnabledLocales();
        $translationValue = (empty($defaultTranslation)) ? $translationKey : $defaultTranslation;

        foreach ($locales as $locale) {
            $this->insertTranslation($translationKey, $translationValue, $locale);
        }
    }

    /**
     * @inheritdoc
     */
    public function create(string $translationKey, string $translationValue, array $locales = [])
    {
        foreach ($locales as $locale) {
            $this->insertTranslation($translationKey, $translationValue, $locale);
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(string $translationKey, ?array $locales = [])
    {
        if (empty($locales)) {
            $locales = $this->getEnabledLocales();
        }

        try {
            $this->repository->deleteByTranslationKeyAndLocales($translationKey, $locales);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    '[PHPRO TRANSLATION] Cannot delete translation "%s" for locales "%s": %s',
                    $translationKey,
                    implode(',', $locales),
                    $e->getMessage()
                )
            );
            if ($this->throwException) {
                throw $e;
            }
        }
    }

    /**
     * @param string $translationKey
     * @param string $translationValue
     * @param string $locale
     * @throws \Exception
     */
    private function insertTranslation(string $translationKey, string $translationValue, string $locale)
    {
        try {
            $this->localeValidator->validate($locale);

            /** @var Translation $translationModel */
            $translationModel = $this->translationFactory->create();

            $translationModel->setLocale($locale);
            $translationModel->setString($translationKey);
            $translationModel->setTranslate($translationValue);

            $this->repository->save($translationModel);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    '[PHPRO TRANSLATION] Cannot insert translation "%s" for locale "%s": %s',
                    $translationKey,
                    $locale,
                    $e->getMessage()
                )
            );
            if ($this->throwException) {
                throw $e;
            }
        }
    }

    /**
     * @return array
     */
    private function getEnabledLocales(): array
    {
        if (!empty($this->enabledLocales)) {
            return $this->enabledLocales;
        }

        foreach ($this->localeSource->toOptionArray() as $locale) {
            $localeCode = $locale['value'];
            $this->enabledLocales[$localeCode] = $localeCode;
        }

        return $this->enabledLocales;
    }
}
