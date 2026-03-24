<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com & Elementor.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\CoreXBaseXPageBase as PageBase;

class CoreXDocumentTypesXPost extends PageBase
{
    /**
     * @since 2.0.8
     */
    public static function getProperties()
    {
        $properties = parent::getProperties();

        $properties['support_kit'] = true;

        return $properties;
    }

    /**
     * @since 2.0.0
     */
    public function getName()
    {
        return 'post';
    }

    /**
     * @since 2.0.0
     */
    public static function getTitle()
    {
        return __('Page');
    }
}
