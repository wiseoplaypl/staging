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

use CE\CoreXCommonXModulesXAjaxXModule as Ajax;
use CE\CoreXFilesXAssetsXSvgXSvgHandler as SvgHandler;
use CE\CoreXFilesXBase as Base;
use CE\CoreXFilesXCSSXGlobalCSS as GlobalCSS;
use CE\CoreXResponsiveXFilesXFrontend as Frontend;
use CE\CoreXResponsiveXResponsive as Responsive;

/**
 * Elementor files manager.
 *
 * Elementor files manager handler class is responsible for creating files.
 *
 * @since 1.2.0
 */
class CoreXFilesXManager
{
    private $files = [];

    /**
     * Files manager constructor.
     *
     * Initializing the Elementor files manager.
     *
     * @since 1.2.0
     */
    public function __construct()
    {
        // $this->registerActions();

        new SvgHandler();
        // new JsonHandler();
    }

    public function get($class, $args)
    {
        $id = $class . '-' . json_encode($args);

        if (!isset($this->files[$id])) {
            // Create an instance from dynamic args length.
            $this->files[$id] = new $class(...$args);
        }

        return $this->files[$id];
    }

    // public function onDeletePost($post_id)

    // public function onExportPostMeta($skip, $meta_key)

    /**
     * Clear cache.
     *
     * Delete all meta containing files data. And delete the actual
     * files from the upload directory.
     *
     * @since 1.2.0
     */
    public function clearCache()
    {
        if (\Shop::getContext() == \Shop::CONTEXT_ALL) {
            \Configuration::deleteByName(GlobalCSS::META_KEY);
            \Configuration::deleteByName(Frontend::META_KEY);
        } else {
            \Configuration::deleteFromContext(GlobalCSS::META_KEY);
            \Configuration::deleteFromContext(Frontend::META_KEY);
        }

        $db = \Db::getInstance();
        $path = Base::getBaseUploadsDir() . Base::DEFAULT_FILES_DIR;
        $kits = [];
        $id_shop_list = \Shop::getContextListShopID();
        $id_shop_list[] = 0;

        foreach ($id_shop_list as $id_shop) {
            $id_shop = (int) $id_shop;
            $id_kit = (int) \Configuration::get('elementor_active_kit', null, null, $id_shop);
            $kits[$id_kit] = $path . "kit-$id_kit.css";

            ($id = $id_shop) < 10 && $id = '0' . $id;
            $db->delete('ce_meta', '`name` = "_elementor_css" AND `id` LIKE "%' . pSQL($id) . '"');

            // Delete CSS files
            array_map('unlink', glob($path . "*$id.css", GLOB_NOSORT));
            array_map('unlink', glob($path . "$id_shop-*.css", GLOB_NOSORT));

            // Delete TPL files
            array_map('unlink', glob(_CE_TEMPLATES_ . "front/theme/catalog/_partials/miniatures/product-*17??$id.tpl", GLOB_NOSORT));

            // Regenerate custom frontend CSS files
            Responsive::hasCustomBreakpoints($id_shop) && Responsive::compileStylesheetTemplates($id_shop);
        }
        // Delete kit CSS files
        array_map('unlink', array_filter($kits, 'file_exists'));

        /*
         * Elementor clear files.
         *
         * Fires after Elementor clears files
         *
         * @since 2.1.0
         */
        do_action('elementor/core/files/clear_cache');
    }

    // public function registerAjaxActions(Ajax $ajax)

    // public function ajaxUnfilteredFilesUpload()

    // private function registerActions()
}
