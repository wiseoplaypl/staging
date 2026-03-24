<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * This function updates your module from previous versions to the version 1.1,
 * useful when you modify your database, or register a new hook ...
 * Don't forget to create one file per version.
 */
function upgrade_module_0_3_0($module)
{
    Configuration::updateValue('PS_HUBSPOT_MIGRATION_MANDATORY', 1);
    Configuration::updateValue('PS_HUBSPOT_MIGRATION_UPDATED_LEGACY', 1);
    Configuration::updateValue('PS_HUBSPOT_MIGRATION_DATE_END', date('Y-m-d H:m'));
    Configuration::updateValue('PS_HUBSPOT_DEAL_DATE_FILTER', '2023-03-01', false, '', '');
    Configuration::updateValue('PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED', 'on', false, '', '');

    $module->registerHook('actionObjectProductInCartDeleteAfter');

    $invisible_tab = new Tab();
    $invisible_tab->active = 1;
    $invisible_tab->class_name = 'AdminHSMigration';
    $invisible_tab->name = [];
    foreach (Language::getLanguages(true) as $lang) {
        $invisible_tab->name[$lang['id_lang']] = $module->l('AdminHSMigration');
    }
    $invisible_tab->id_parent = -1;
    $invisible_tab->module = $module->name;
    $invisible_tab->add();

    return true;
}
