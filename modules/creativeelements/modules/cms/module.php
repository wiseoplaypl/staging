<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\CoreXBaseXModule as BaseModule;
use CE\ModulesXCmsXControlsXSelectCategory as SelectCMSCategory;

class ModulesXCmsXModule extends BaseModule
{
    public function getName()
    {
        return 'cms';
    }

    public function registerControls($controls_manager)
    {
        $controls_manager->registerControl(SelectCMSCategory::CONTROL_TYPE, new SelectCMSCategory());
    }

    public function registerDocuments($documents)
    {
        $documents->registerDocumentType('cms-category', 'CE\ModulesXCmsXDocumentsXCategory');
    }

    public function __construct()
    {
        add_action('elementor/controls/controls_registered', [$this, 'registerControls']);
        add_action('elementor/documents/register', [$this, 'registerDocuments']);
    }

    public static function getSubPages($id_cms_category, $id_lang, $id_shop, $order_by = '', $order_way = '', $limit = 0, $offset = 0, $exclude_id = 0)
    {
        $offset && !$limit && $limit = 1000;
        $rows = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT c.`position`, cl.* FROM ' . _DB_PREFIX_ . 'cms c
            INNER JOIN ' . _DB_PREFIX_ . 'cms_lang cl ON c.`id_cms` = cl.`id_cms` AND cl.`id_lang` = ' . (int) $id_lang . ' AND cl.`id_shop` = ' . (int) $id_shop . '
            WHERE c.`id_cms_category` = ' . (int) $id_cms_category . ' AND c.`active` = 1' .
            ($exclude_id ? ' AND c.`id_cms` != ' . (int) $exclude_id : '') . '
            ORDER BY ' . bqSQL($order_by ?: 'position') . ' ' . ('DESC' === $order_way ? 'DESC ' : '') .
            ($limit ? 'LIMIT ' . (int) $limit : '') . ($offset ? ' OFFSET ' . (int) $offset : '')
        ) ?: [];
        foreach ($rows as &$row) {
            $row['link'] = Helper::$link->getCmsLink($row['id_cms'], $row['link_rewrite'], null, $id_lang, $id_shop);
        }

        return $rows;
    }

    public static function getSubCategories($id_cms_category, $id_lang, $id_shop, $order_by = '', $order_way = '', $limit = 0, $offset = 0)
    {
        $offset && !$limit && $limit = 1000;
        $rows = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT c.`position`, cl.* FROM ' . _DB_PREFIX_ . 'cms_category c
            INNER JOIN ' . _DB_PREFIX_ . 'cms_category_lang cl ON c.`id_cms_category` = cl.`id_cms_category` AND cl.`id_lang` = ' . (int) $id_lang . ' AND cl.`id_shop` = ' . (int) $id_shop . '
            WHERE c.`id_parent` = ' . (int) $id_cms_category . ' AND c.`active` = 1
            ORDER BY ' . bqSQL($order_by ?: 'position') . ' ' . ('DESC' === $order_way ? 'DESC ' : '') .
            ($limit ? 'LIMIT ' . (int) $limit : '') . ($offset ? ' OFFSET ' . (int) $offset : '')
        );
        foreach ($rows as &$row) {
            $row['name'] = \CMSCategory::hideCMSCategoryPosition($row['name']);
            $row['link'] = Helper::$link->getCMSCategoryLink($row['id_cms_category'], $row['link_rewrite'], $id_lang, $id_shop);
        }

        return $rows;
    }
}
