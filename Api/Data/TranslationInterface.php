<?php declare(strict_types=1);


namespace Phpro\Translations\Api\Data;

interface TranslationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    public const STRING = 'string';
    public const LOCALE = 'locale';
    public const TRANSLATE = 'translate';
    public const FRONTEND = 'frontend';
    public const KEY_ID = 'key_id';

    /**
     * @return int|null
     */
    public function getKeyId();

    /**
     * @param $keyId
     * @return mixed
     */
    public function setKeyId($keyId);

    /**
     * @return string|null
     */
    public function getString();

    /**
     * @param $string
     * @return mixed
     */
    public function setString($string);

    /**
     * @return string|null
     */
    public function getLocale();

    /**
     * @param $locale
     * @return mixed
     */
    public function setLocale($locale);

    /**
     * @return string|null
     */
    public function getTranslate();

    /**
     * @param $translate
     * @return mixed
     */
    public function setTranslate($translate);

    /**
     * @return boolean|null
     */
    public function getFrontend();

    /**
     * @param $frontend
     * @return mixed
     */
    public function setFrontend($frontend);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Phpro\Translations\Api\Data\TranslationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Phpro\Translations\Api\Data\TranslationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        TranslationExtensionInterface $extensionAttributes
    );
}
