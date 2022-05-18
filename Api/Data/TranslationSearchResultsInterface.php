<?php declare(strict_types=1);


namespace Phpro\Translations\Api\Data;

interface TranslationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Translation list.
     *
     * @return \Phpro\Translations\Api\Data\TranslationInterface[]
     */
    public function getItems();

    /**
     * Set translate list.
     * 
     * @param \Phpro\Translations\Api\Data\TranslationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
