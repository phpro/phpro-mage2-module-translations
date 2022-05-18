<?php
declare(strict_types=1);

namespace Phpro\Translations\Block\Adminhtml\Translation\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Get data for back (reset) button
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Translation'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
