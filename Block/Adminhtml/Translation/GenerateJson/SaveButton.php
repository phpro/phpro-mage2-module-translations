<?php
declare(strict_types=1);

namespace Phpro\Translations\Block\Adminhtml\Translation\GenerateJson;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Phpro\Translations\Block\Adminhtml\Translation\Edit\GenericButton;

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
            'label' => __('Generate translations files'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
