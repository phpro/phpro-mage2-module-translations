<?php
declare(strict_types=1);

namespace Phpro\Translations\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TranslationRepositoryInterface
{

    /**
     * Save Translation
     *
     * @param Data\TranslationInterface $translation
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return Data\TranslationInterface
     */
    public function save(
        Data\TranslationInterface $translation
    );

    /**
     * Retrieve Translation
     *
     * @param string $translationId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return Data\TranslationInterface
     */
    public function getById($translationId);

    /**
     * Retrieve Translation matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return Data\TranslationSearchResultsInterface
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete translation by given key and locales.
     *
     * @param string $translationKey
     * @param array $locales
     * @return bool true on success
     */
    public function deleteByTranslationKeyAndLocales(string $translationKey, array $locales);

    /**
     * Delete Translation by ID
     *
     * @param string $translationId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById($translationId);
}
