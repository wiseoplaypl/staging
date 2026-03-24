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

function upgrade_module_4_4_2($module)
{
	Configuration::updateValue('STRIPE_REFUND_ORDER_STATUS', (int)Configuration::get('PS_OS_REFUND'));
  Configuration::updateValue('STRIPE_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES'), '');
  $module->registerHook('actionEmailSendBefore');
  Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'stripejs_transaction` MODIFY `source_type` varchar(32)');
	include_once(dirname(__FILE__).'/../classes/StripejsInstall.php');
	$Stripejsinstaller =  new StripejsInstall();
	$Stripejsinstaller->createOS();
  return true;
}
