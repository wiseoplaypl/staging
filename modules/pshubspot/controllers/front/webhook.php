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

class PsHubspotWebhookModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function displayAjax()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        if (Tools::getIsset('secure_key')) {
            $secure_key = Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', '');
            if (!empty($secure_key) && Tools::getValue('secure_key') === $secure_key) {
                if ($this->module->active) {
                    echo $this->module->hookActionWebhook();
                } else {
                    $this->ajaxDie('ERROR: ' . $this->module->name . " module is not active.\n");
                }
            } else {
                $this->ajaxDie("ERROR: Wrong secure key.\n");
            }
        } else {
            $this->ajaxDie("ERROR: No secure key.\n");
        }
    }
}
