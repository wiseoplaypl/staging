<?php
/**
 * 2007-2025 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2025 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_7_1($module)
{
  Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'stripejs_transaction` ADD `risk_score` tinyint(3) NOT NULL DEFAULT "0" AFTER `three_d_secure`');
  Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'stripejs_transaction` ADD `risk_level` varchar(16) AFTER `risk_score`');
  return true;
}
