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

use CE\CoreXResponsiveXFilesXFrontend as FrontendFile;

/**
 * Elementor responsive.
 *
 * Elementor responsive handler class is responsible for setting up Elementor
 * responsive breakpoints.
 *
 * @since 1.0.0
 */
class CoreXResponsiveXResponsive
{
    /**
     * The Elementor breakpoint prefix.
     */
    const BREAKPOINT_OPTION_PREFIX = 'elementor_viewport_';

    /**
     * Default breakpoints.
     *
     * Holds the default responsive breakpoints.
     *
     * @since 1.0.0
     *
     * @var array Default breakpoints
     */
    private static $default_breakpoints = [
        'xs' => 0,
        'sm' => 480,
        'md' => 768,
        'lg' => 1025,
        'xl' => 1440,
        'xxl' => 1600,
    ];

    /**
     * Editable breakpoint keys.
     *
     * Holds the editable breakpoint keys.
     *
     * @since 1.0.0
     *
     * @var array Editable breakpoint keys
     */
    private static $editable_breakpoints_keys = [
        'md',
        'lg',
    ];

    /**
     * Get default breakpoints.
     *
     * Retrieve the default responsive breakpoints.
     *
     * @since 1.0.0
     *
     * @return array Default breakpoints
     */
    public static function getDefaultBreakpoints()
    {
        return self::$default_breakpoints;
    }

    /**
     * Get editable breakpoints.
     *
     * Retrieve the editable breakpoints.
     *
     * @since 1.0.0
     *
     * @return array Editable breakpoints
     */
    public static function getEditableBreakpoints($id_shop = null)
    {
        return array_intersect_key(self::getBreakpoints($id_shop), array_flip(self::$editable_breakpoints_keys));
    }

    /**
     * Get breakpoints.
     *
     * Retrieve the responsive breakpoints.
     *
     * @since 1.0.0
     *
     * @return array Responsive breakpoints
     */
    public static function getBreakpoints($id_shop = null)
    {
        $self = __CLASS__;

        return array_reduce(
            array_keys(self::$default_breakpoints),
            function ($new_array, $breakpoint_key) use ($self, $id_shop) {
                if (!in_array($breakpoint_key, $self::${'editable_breakpoints_keys'})) {
                    $new_array[$breakpoint_key] = $self::${'default_breakpoints'}[$breakpoint_key];
                } else {
                    $saved_option = \Configuration::get($self::BREAKPOINT_OPTION_PREFIX . $breakpoint_key, null, null, $id_shop);

                    $new_array[$breakpoint_key] = $saved_option ? (int) $saved_option : $self::${'default_breakpoints'}[$breakpoint_key];
                }

                return $new_array;
            },
            []
        );
    }

    /**
     * @since 2.1.0
     */
    public static function hasCustomBreakpoints($id_shop = null)
    {
        static $hasCustomBreakpoints = [];

        if (!isset($hasCustomBreakpoints[$id_shop])) {
            $hasCustomBreakpoints[$id_shop] = (bool) array_diff(self::$default_breakpoints, self::getBreakpoints($id_shop));
        }

        return $hasCustomBreakpoints[$id_shop];
    }

    /**
     * @since 2.1.0
     */
    public static function getStylesheetTemplatesPath()
    {
        return _CE_ASSETS_PATH_ . 'css/';
    }

    /**
     * @since 2.1.0
     */
    public static function compileStylesheetTemplates($id_shop = null)
    {
        foreach (self::getStylesheetTemplates($id_shop) as $file_name => $template_path) {
            $file = new FrontendFile($file_name, $template_path);

            $file->update();
        }
    }

    /**
     * @since 2.1.0
     */
    private static function getStylesheetTemplates($id_shop = null)
    {
        // $templates_paths = glob(self::getStylesheetTemplatesPath() . '*.css');
        $stylesheet_path = self::getStylesheetTemplatesPath();
        $templates_paths = [
            $stylesheet_path . 'frontend.css',
            $stylesheet_path . 'frontend.min.css',
            $stylesheet_path . 'frontend-rtl.css',
            $stylesheet_path . 'frontend-rtl.min.css',
            $stylesheet_path . 'conditionals/lightbox.css',
            $stylesheet_path . 'conditionals/lightbox.min.css',
        ];

        foreach (glob(self::getStylesheetTemplatesPath() . 'widget-*.css') as $widget_path) {
            if (strpos(file_get_contents($widget_path), '@media') !== false) {
                $templates_paths[] = $widget_path;
            }
        }

        $templates = [];
        $id_shop = (int) ($id_shop ?: $GLOBALS['context']->shop->id);

        foreach ($templates_paths as $template_path) {
            $file_name = "$id_shop-" . basename($template_path);

            $templates[$file_name] = $template_path;
        }
        // return apply_filters('elementor/core/responsive/get_stylesheet_templates', $templates, $id_shop);

        return $templates;
    }
}
