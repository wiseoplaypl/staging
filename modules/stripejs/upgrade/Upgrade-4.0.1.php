<?php
/**
 * 2007-2021 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_0_1($module)
{
	// Registration order status
    if (!$module->createOS()) {
        return false;
    }
	
	$upgrade_sql = Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'stripejs_transaction` ADD `id_payment_intent` VARCHAR(100) NOT NULL AFTER `source_type`,
ADD `client_secret` varchar(120) NOT NULL AFTER `id_payment_intent`,ADD `stripe_cus_id` VARCHAR(120) AFTER `client_secret`,ADD `id_cart` int(10) unsigned NOT NULL AFTER `id_customer`,ADD `line1_check` tinyint(1) NOT NULL DEFAULT "0" AFTER `cc_last_digits`,
ADD `zip_check` tinyint(1) NOT NULL DEFAULT "0" AFTER `line1_check`,ADD `card_reuse` tinyint(1) NOT NULL DEFAULT "0" AFTER `cvc_check`') && 
Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripejs_transaction` SET `amount` = (`amount` * 100) WHERE `amount` > 0') &&
Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stripejs_transaction` SET `fee` = (`fee` * 100) WHERE `fee` > 0') &&
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'stripejs_transaction` MODIFY `amount` int(11) NOT NULL,
MODIFY `status` enum("paid","pending","uncaptured","failed","canceled") NOT NULL DEFAULT "pending",MODIFY `cc_exp` varchar(10) NOT NULL,MODIFY `fee` int(11) NOT NULL') && 
	Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'stripejs_transaction` DROP COLUMN `btc_address`,DROP COLUMN `btc_amount`');	
    if (!$upgrade_sql) {
        return false;
    }
    
    return true;
}
