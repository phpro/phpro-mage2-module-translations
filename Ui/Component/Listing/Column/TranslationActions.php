<?php
declare(strict_types=1);

namespace Phpro\Translations\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class TranslationActions extends Column
{
    private const URL_PATH_EDIT = 'phpro_translations/translation/edit';
    private const URL_PATH_DELETE = 'phpro_translations/translation/delete';
    private const URL_PATH_DETAILS = 'phpro_translations/translation/details';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['key_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'key_id' => $item['key_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'key_id' => $item['key_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' =>__('Delete %1', $item['key_id']),
                                'message' => __('Are you sure you want to delete record %1?', $item['key_id']),
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
