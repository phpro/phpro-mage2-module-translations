<?php
declare(strict_types=1);

namespace Phpro\Translations\Api;

interface TranslationDataManagementInterface
{
    /**
     * Add the translation key for all enabled locales of the Magento instance.
     *
     * If default translation is not set, the translation key will be used as default translation.
     *
     * @param string $translationKey
     * @param null|string $defaultTranslation
     */
    public function prepare(string $translationKey, ?string $defaultTranslation = null);

    /**
     * Add a translation for given locales.
     *
     * @param string $translationKey
     * @param string $translationValue
     * @param array $locales
     */
    public function create(string $translationKey, string $translationValue, array $locales = []);

    /**
     * Delete a translation for given translation key and locale(s).
     *
     * In case no locales are given, all enabled locales will be used.
     *
     * @param string $translationKey
     * @param array|null $locales
     */
    public function delete(string $translationKey, ?array $locales = []);
}
