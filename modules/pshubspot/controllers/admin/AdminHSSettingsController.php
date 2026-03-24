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

use Tiralineas\PsHubspot\Navigator;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/controllers/admin/AdminHSAbstractController.php';

class AdminHSSettingsController extends AdminHSAbstractController
{
    protected $slug = 'hs_settings';

    public function __construct()
    {
        parent::__construct();
        $this->meta_title = [$this->l('Settings')];
        $this->context = Context::getContext();
        $this->bootstrap = true;
    }

    public function postProcess()
    {
        if (((bool) Tools::isSubmit('ps-hubspot_save_settings')) == true) {
            $keys = [
                'PS_HUBSPOT_USE_TRACKING',
                // 'PS_HUBSPOT_LICENSE',
                'PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC',
            ];
            foreach ($keys as $key) {
                \Configuration::updateValue(
                    $key,
                    Tools::getValue($key),
                    false,
                    '',
                    ''
                );
            }
        }
    }

    public function process()
    {
        if (\Tiralineas\PsHubspot\Connection::fetchAccessTokenFromCode()) {
            Navigator::toSetup();
        }
    }

    public function renderView()
    {
        $id_lang = Context::getContext()->language->id;
        $getCustomerGroups = \Group::getGroups($id_lang);
        $getCustomerGroupsUnSync = explode(',', \Configuration::get('PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC')) ?: '';
        $customerGroupAvailable = [];
        $customerGroupUnSync = [];
        foreach ($getCustomerGroups as $key => $customerGroup) {
            if (!in_array($customerGroup['id_group'], $getCustomerGroupsUnSync)) {
                array_push($customerGroupAvailable, $customerGroup);
            } else {
                array_push($customerGroupUnSync, $customerGroup);
            }
        }

        Context::getContext()->smarty->assign(
            [
                'PS_HUBSPOT_USE_TRACKING' => \Configuration::get('PS_HUBSPOT_USE_TRACKING', '', '', ''),
                'PS_HUBSPOT_LICENSE' => \Configuration::get('PS_HUBSPOT_LICENSE', '', '', ''),
                'PS_CUSTOMER_GROUPS_AVAILABLE' => $customerGroupAvailable,
                'PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC' => $customerGroupUnSync,
            ]
        );

        /**********************
         * PrestaShop Account *
         * *******************/

        $accountsService = null;

        try {
            $accountsFacade = $this->module->getService('pshubspot.ps_accounts_facade');
            $accountsService = $accountsFacade->getPsAccountsService();
        } catch (\PrestaShop\PsAccountsInstaller\Installer\Exception\InstallerException $e) {
            $accountsInstaller = $this->module->getService('pshubspot.ps_accounts_installer');
            $accountsInstaller->install();
            $accountsFacade = $this->module->getService('pshubspot.ps_accounts_facade');
            $accountsService = $accountsFacade->getPsAccountsService();
        }

        try {
            Media::addJsDef([
                'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                    ->present($this->module->name),
            ]);

            // Retrieve the PrestaShop Account CDN
            $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();

            return '';
        }

        /**********************
         * PrestaShop Billing *
         * *******************/

        // Load the context for PrestaShop Billing
        $billingFacade = $this->module->getService('pshubspot.ps_billings_facade');
        $partnerLogo = $this->module->getLocalPath() . 'views/img/partnerLogo.png';

        try {
            // PrestaShop Billing
            Media::addJsDef(
                $billingFacade->present([
                    'logo' => $partnerLogo,
                    'tosLink' => 'https://www.tiralineas.digital/en/personal-data',
                    'privacyLink' => 'https://www.tiralineas.digital/en/personal-data',
                    'emailSupport' => 'soporte@tiralineas.digital',
                ])
            );

            $this->context->smarty->assign('urlBilling', 'https://unpkg.com/@prestashopcorp/billing-cdc/dist/bundle.js');
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();

            return '';
        }
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'pshubspot/views/css/customer_groups.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'pshubspot/views/js/jquery-ui.min.js');

        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . 'pshubspot/views/templates/admin/hs_settings/view.tpl');
    }
}
