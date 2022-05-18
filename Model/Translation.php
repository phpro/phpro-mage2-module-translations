<?php
declare(strict_types=1);

namespace Phpro\Translations\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Phpro\Translations\Api\Data\TranslationInterface;
use Phpro\Translations\Api\Data\TranslationInterfaceFactory;
use Phpro\Translations\Model\ResourceModel;

class Translation extends AbstractModel
{
    protected $_eventPrefix = 'phpro_translations_translation';
    /**
     * @var TranslationInterfaceFactory
     */
    private $translationDataFactory;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Translation constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param TranslationInterfaceFactory $translationDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Translation $resource
     * @param ResourceModel\Translation\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        TranslationInterfaceFactory $translationDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Translation $resource,
        ResourceModel\Translation\Collection $resourceCollection,
        array $data = []
    ) {
        $this->translationDataFactory = $translationDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return TranslationInterface
     */
    public function getDataModel()
    {
        $data = $this->getData();

        $translationDataObject = $this->translationDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $translationDataObject,
            $data,
            TranslationInterface::class
        );

        return $translationDataObject;
    }
}
