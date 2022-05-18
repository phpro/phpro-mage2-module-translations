<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Phpro\Translations\Api\Data\TranslationInterface;

class Translation extends AbstractExtensibleObject implements TranslationInterface
{
    /**
     * @return int|null
     */
    public function getKeyId()
    {
        return $this->_get(self::KEY_ID);
    }

    /**
     * @param $keyId
     * @return mixed
     */
    public function setKeyId($keyId)
    {
        return $this->setData(self::KEY_ID, $keyId);
    }

    /**
     * @return string|null
     */
    public function getString()
    {
        return $this->_get(self::STRING);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function setString($string)
    {
        return $this->setData(self::STRING, $string);
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        return $this->_get(self::LOCALE);
    }

    /**
     * @param $locale
     * @return mixed
     */
    public function setLocale($locale)
    {
        return $this->setData(self::LOCALE, $locale);
    }

    /**
     * @return string|null
     */
    public function getTranslate()
    {
        return $this->_get(self::TRANSLATE);
    }

    /**
     * @param $translate
     * @return mixed
     */
    public function setTranslate($translate)
    {
        return $this->setData(self::TRANSLATE, $translate);
    }

    /**
     * @return bool
     */
    public function getFrontend()
    {
        return (boolean) $this->_get(self::FRONTEND);
    }

    /**
     * @param $translate
     * @return mixed
     */
    public function setFrontend($translate)
    {
        return $this->setData(self::FRONTEND, $translate);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Phpro\Translations\Api\Data\TranslationExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Phpro\Translations\Api\Data\TranslationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Phpro\Translations\Api\Data\TranslationExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
