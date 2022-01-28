<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Phpro\Translations\Api\Data\TranslationInterface;

class Translation extends AbstractExtensibleObject implements TranslationInterface
{
    public function getKeyId()
    {
        return $this->_get(self::KEY_ID);
    }

    public function setKeyId($keyId)
    {
        return $this->setData(self::KEY_ID, $keyId);
    }

    public function getString()
    {
        return $this->_get(self::STRING);
    }

    public function setString($string)
    {
        return $this->setData(self::STRING, $string);
    }

    public function getLocale()
    {
        return $this->_get(self::LOCALE);
    }

    public function setLocale($locale)
    {
        return $this->setData(self::LOCALE, $locale);
    }

    public function getTranslate()
    {
        return $this->_get(self::TRANSLATE);
    }

    public function setTranslate($translate)
    {
        return $this->setData(self::TRANSLATE, $translate);
    }

    public function getFrontend()
    {
        return (boolean) $this->_get(self::FRONTEND);
    }

    public function setFrontend($translate)
    {
        return $this->setData(self::FRONTEND, $translate);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Phpro\Translations\Api\Data\TranslationExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Phpro\Translations\Api\Data\TranslationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Phpro\Translations\Api\Data\TranslationExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
