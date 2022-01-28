<?php
declare(strict_types=1);

namespace Phpro\Translations\Plugin;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Model\Export\MetadataProvider as Subject;
use Phpro\Translations\Model\Import\Translations;

/**
 * This plugin make sure the translations CSV export is conform the import:
 * - Make sure only the fields are exported which are needed for a proper import via Magento.
 * - Make sure the CSV column names are the same during import validation.
 */
class UpdateMetadataFields
{
    const UI_COMPONENT_NAME = 'phpro_translations_listing';

    public function afterGetFields(Subject $subject, $result, UiComponentInterface $component)
    {
        if (self::UI_COMPONENT_NAME === $component->getName()) {
            return [
                Translations::COL_KEY,
                Translations::COL_TRANSLATION,
                Translations::COL_LOCALE
            ];
        }

        return $result;
    }

    public function afterGetHeaders(Subject $subject, $result, UiComponentInterface $component)
    {
        if (self::UI_COMPONENT_NAME === $component->getName()) {
            return [
                Translations::HEADER_KEY,
                Translations::HEADER_TRANSLATION,
                Translations::HEADER_LOCALE,
            ];
        }

        return $result;
    }
}
