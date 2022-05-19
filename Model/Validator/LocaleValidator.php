<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Validator;

use Magento\Framework\Locale\ListsInterface;

class LocaleValidator
{
    /**
     * @var ListsInterface
     */
    private $localeLists;

    /**
     * @var array
     */
    private $locales = [];

    public function __construct(ListsInterface $localeLists)
    {
        $this->localeLists = $localeLists;
    }

    /**
     * @param string $locale
     * @throws \Exception
     */
    public function validate(string $locale)
    {
        if (!in_array($locale, $this->getSupportedLocales(), true)) {
            throw new \Exception(sprintf('Locale %s not supported by Magento', $locale));
        }
    }

    /**
     * @return array
     */
    private function getSupportedLocales(): array
    {
        if (!empty($this->locales)) {
            return $this->locales;
        }

        $supportedLocaleList = $this->localeLists->getOptionLocales();
        foreach ($supportedLocaleList as $supportedLocale) {
            $localeCode = $supportedLocale['value'];
            $this->locales[$localeCode] = $localeCode;
        }

        return $this->locales;
    }
}
