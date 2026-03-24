<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Function used to update your module from previous versions to the version 1.5.6,
 * Don't forget to create one file per version.
 */
function upgrade_module_3_5_6($module)
{
	require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfInstall.php';
	include(dirname(dirname(__FILE__)).'/sql/install.php');
	Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'ndk_customization_field_cache`');
	Configuration::updateValue('NDK_SHOW_IMG_TOOLTIP', '1');
	$module->registerHook('updateQuantity');
	$module->registerHook('actionAdminControllerSetMedia');
	$module->hookStandard();
	return true;
}
