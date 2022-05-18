<?php
declare(strict_types=1);

namespace Phpro\Translations\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Phpro\Translations\Api\Data\TranslationInterface;
use Phpro\Translations\Api\Data\TranslationInterfaceFactory;
use Phpro\Translations\Api\Data\TranslationSearchResultsInterfaceFactory;
use Phpro\Translations\Api\TranslationRepositoryInterface;
use Phpro\Translations\Model\ResourceModel\Translation as ResourceTranslation;
use Phpro\Translations\Model\ResourceModel\Translation\CollectionFactory as TranslationCollectionFactory;

class TranslationRepository implements TranslationRepositoryInterface
{
    /**
     * @var ResourceTranslation
     */
    private $resource;
    /**
     * @var TranslationFactory
     */
    private $translationFactory;
    /**
     * @var TranslationInterfaceFactory
     */
    private $dataTranslationFactory;
    /**
     * @var TranslationCollectionFactory
     */
    private $translationCollectionFactory;
    /**
     * @var TranslationSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;
    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * TranslationRepository constructor.
     *
     * @param ResourceTranslation $resource
     * @param TranslationFactory $translationFactory
     * @param TranslationInterfaceFactory $dataTranslationFactory
     * @param TranslationCollectionFactory $translationCollectionFactory
     * @param TranslationSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceTranslation $resource,
        TranslationFactory $translationFactory,
        TranslationInterfaceFactory $dataTranslationFactory,
        TranslationCollectionFactory $translationCollectionFactory,
        TranslationSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->translationFactory = $translationFactory;
        $this->dataTranslationFactory = $dataTranslationFactory;
        $this->translationCollectionFactory = $translationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }


    /**
     * @param TranslationInterface $translation
     * @return TranslationInterface
     */
    public function save(TranslationInterface $translation)
    {
        $translationData = $this->extensibleDataObjectConverter->toNestedArray(
            $translation,
            [],
            TranslationInterface::class
        );

        $translationModel = $this->translationFactory->create()->setData($translationData);

        try {
            $this->resource->save($translationModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the translation: %1',
                $exception->getMessage()
            ));
        }
        return $translationModel->getDataModel();
    }

    /**
     * @inheritdoc
     */
    public function getById($translationId)
    {
        $translation = $this->translationFactory->create();
        $this->resource->load($translation, $translationId);
        if (!$translation->getId()) {
            throw new NoSuchEntityException(__('Translation with id "%1" does not exist.', $translationId));
        }
        return $translation->getDataModel();
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->translationCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            TranslationInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function deleteByTranslationKeyAndLocales(string $translationKey, array $locales)
    {
        try {
            $this->resource->deleteByTranslationKeyAndLocales($translationKey, $locales);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Translations: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($translationId)
    {
        try {
            $translation = $this->translationFactory->create();
            $this->resource->load($translation, $translationId);
            if (!$translation->getId()) {
                throw new NoSuchEntityException(__('Translation with id "%1" does not exist.', $translationId));
            }
            $translation->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Translation: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }
}
