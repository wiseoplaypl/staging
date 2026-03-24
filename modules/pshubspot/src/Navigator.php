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

namespace Tiralineas\PsHubspot;

use Context;
use Dispatcher;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Helper Class to deal with Plugin urls and redirects
 */
class Navigator
{
    public static function toDashboard()
    {
        self::toTab('AdminHSDashboard');
    }

    public static function toMigration()
    {
        self::toTab('AdminHSMigration');
    }

    public static function getDashboardUrl($absolute = false)
    {
        return self::getTabUrl('AdminHSDashboard', $absolute);
    }

    public static function toSetup()
    {
        self::toTab('AdminHSSetup');
    }

    public static function getSettingsUrl($absolute = false)
    {
        return self::getTabUrl('AdminHSSettings', $absolute);
    }

    public static function getSetupUrl($absolute = false, $step = '')
    {
        return self::getTabUrl('AdminHSSetup', $absolute, ['step' => $step]);
    }

    public static function getContacstUrl($absolute = false, $step = '')
    {
        return self::getTabUrl('AdminHSContacts', $absolute, ['step' => $step]);
    }

    private static function toTab($tab, $params = [])
    {
        Tools::redirectAdmin(
            self::getTabUrl($tab, false, $params)
        );
    }

    private static function getTabUrl($tab, $abslute = false, $params = [])
    {
        if (!Context::getContext()->employee) {
            return '';
        }
        $id_lang = Context::getContext()->language->id;
        $params['token'] = Tools::getAdminTokenLite($tab);
        $uri = Dispatcher::getInstance()->createUrl($tab, $id_lang, $params, false);
        if (__PS_BASE_URI__ != '/') {
            return ($abslute ? _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . DIRECTORY_SEPARATOR . basename(_PS_ADMIN_DIR_) . DIRECTORY_SEPARATOR : '') . $uri;
        } else {
            return ($abslute ? _PS_BASE_URL_SSL_ . DIRECTORY_SEPARATOR . basename(_PS_ADMIN_DIR_) . DIRECTORY_SEPARATOR : '') . $uri;
        }
    }
}
