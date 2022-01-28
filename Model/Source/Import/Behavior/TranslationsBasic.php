<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Source\Import\Behavior;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

/**
 * Import behavior source model used for defining the behaviour during the import.
 */
class TranslationsBasic extends AbstractBehavior
{
    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_APPEND => __('Add/Update'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'translationsbasic';
    }

    /**
     * @inheritdoc
     */
    public function getNotes($entityCode)
    {
        $messages = ['translations' => [
            Import::BEHAVIOR_APPEND => __(
                "Add or update translations. Updates are based on translation key (first column of CSV)."
            ),
        ]];
        return isset($messages[$entityCode]) ? $messages[$entityCode] : [];
    }
}
