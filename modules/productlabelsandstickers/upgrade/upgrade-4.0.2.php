<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2019
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_0_2($module)
{
    $tab_class = 'AdminFmmStickers';
    $tab_module = 'productlabelsandstickers';

    $isRemovedTab = removeTab($tab_class);

    if ($isRemovedTab) {
        addTab($tab_class, 0, $tab_module, $module);
    }

    if (!columnExistBanner('banner_status')) {
        $isColCreated = Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickersbanners` ADD `banner_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0');

        if ($isColCreated) {
            $sql = 'SELECT s.*, sb.*, sr.title AS rule_title, sr.status AS rule_status FROM ' . _DB_PREFIX_ . 'fmm_stickersbanners s LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickersbanners_lang sb ON s.stickersbanners_id = sb.stickersbanners_id LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickers_rules sr ON s.stickersbanners_id = sr.stickerbanner_id WHERE sb.id_lang = ' . (int) Context::getContext()->language->id;

            $banner_stickers = Db::getInstance()->executeS($sql);
            if ($banner_stickers) {
                foreach ($banner_stickers as $key => $sticker) {
                    if ($sticker) {
                        $isbstickerupdated = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fmm_stickersbanners` SET `banner_status` = ' . (int) $sticker['rule_status'] . ' WHERE `stickersbanners_id` = ' . (int) $sticker['stickersbanners_id']
                        );
                    }
                }
            }
        }
    }

    if (!columnExist('sticker_size_home')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `sticker_size_home` varchar(255) default NULL');
    }

    if (!columnExist('font_size_listing')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `font_size_listing` varchar(255) default NULL');
    }

    if (!columnExist('font_size_product')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `font_size_product` varchar(255) default NULL');
    }

    if (!columnExist('home')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `home` int(11) default 0');
    }

    if (!columnExist('status')) {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `status` TINYINT default 0');
    }

    if (!columnExist('sticker_type')) {
        $isCreated = Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fmm_stickers` ADD `sticker_type` text');

        if ($isCreated) {
            $sql = 'SELECT s.*, sl.*, sr.title AS rule_title, sr.status AS rule_status FROM ' . _DB_PREFIX_ . 'fmm_stickers s LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickers_lang sl ON s.sticker_id = sl.sticker_id LEFT JOIN ' . _DB_PREFIX_ . 'fmm_stickers_rules sr ON s.sticker_id = sr.sticker_id WHERE sl.id_lang = ' . (int) Context::getContext()->language->id;

            $stickers = Db::getInstance()->executeS($sql);

            foreach ($stickers as $key => $sticker) {
                if ($sticker) {
                    if (!empty($sticker['title'])) {
                        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fmm_stickers` SET `sticker_type` = "text", `home` = 1, `status` = ' . (int) $sticker['rule_status'] . ', `sticker_size_home` = "' . pSQL($sticker['sticker_size_list']) . '" WHERE `sticker_id` = ' . (int) $sticker['sticker_id']
                        );
                    } elseif (!empty($sticker['sticker_image']) && empty($sticker['title']) && empty($sticker['color']) && empty($sticker['bg_color']) && empty($sticker['font'])) {
                        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'fmm_stickers` SET `sticker_type` = "image", `home` = 1, `status` = ' . (int) $sticker['rule_status'] . ', `sticker_size_home` = "' . pSQL($sticker['sticker_size_list']) . '" WHERE `sticker_id` = ' . (int) $sticker['sticker_id']
                        );
                    }
                }
            }
        }
    }

    return true;
}

function columnExist($column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . 'fmm_stickers"');
    if (isset($columns) && $columns) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}

function columnExistBanner($column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . 'fmm_stickersbanners"');
    if (isset($columns) && $columns) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}

function removeTab($tab_class)
{
    $idTab = Tab::getIdFromClassName($tab_class);
    if ($idTab != 0) {
        $tab = new Tab($idTab);
        if (!$tab->delete()) {
            return false;
        }
    }

    $idTab1 = Tab::getIdFromClassName('AdminStickers');
    if ($idTab1 != 0) {
        $tab_idTab1 = new Tab($idTab1);
        if (!$tab_idTab1->delete()) {
            return false;
        }
    }

    $idTab2 = Tab::getIdFromClassName('AdminStickersBanners');
    if ($idTab2 != 0) {
        $tab_idTab2 = new Tab($idTab2);
        if (!$tab_idTab2->delete()) {
            return false;
        }
    }

    $idTab3 = Tab::getIdFromClassName('AdminStickersRules');
    if ($idTab3 != 0) {
        $tab_idTab3 = new Tab($idTab3);
        if (!$tab_idTab3->delete()) {
            return false;
        }
    }

    $idTab4 = Tab::getIdFromClassName('AdminTextStickers');
    if ($idTab4 != 0) {
        $tab_idTab4 = new Tab($idTab4);
        if (!$tab_idTab4->delete()) {
            return false;
        }
    }

    return true;
}

function existsTab($tab_class)
{
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_tab AS id
        FROM `' . _DB_PREFIX_ . 'tab` t WHERE LOWER(t.`class_name`) = \'' . pSQL($tab_class) . '\'');
    if (count($result) == 0) {
        return false;
    }

    return true;
}

function addTab($tab_class, $id_parent, $tab_module, $module)
{
    $tab = new Tab();
    $tab->class_name = $tab_class;
    $tab->id_parent = $id_parent;
    $tab->module = $tab_module;
    $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Product labels and Stickers');
    if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
        $tab->icon = 'filter';
    }
    $tab->add();

    $fifthtab = new Tab();
    $fifthtab->class_name = 'AdminTextStickers';
    $fifthtab->id_parent = Tab::getIdFromClassName($tab_class);
    $fifthtab->module = $tab_module;
    $fifthtab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = html_entity_decode($module->l('Manage Text & Image Stickers'), ENT_QUOTES, 'UTF-8');
    if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
        $fifthtab->icon = 'filter';
    }
    $fifthtab->add();

    $thirdtab = new Tab();
    $thirdtab->class_name = 'AdminStickersBanners';
    $thirdtab->id_parent = Tab::getIdFromClassName($tab_class);
    $thirdtab->module = $tab_module;
    $thirdtab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $module->l('Manage Text Banners');
    if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
        $thirdtab->icon = 'filter';
    }
    $thirdtab->add();

    return true;
}
