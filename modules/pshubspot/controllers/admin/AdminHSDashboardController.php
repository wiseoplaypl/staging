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

use Tiralineas\PsHubspot\AccessToken;
use Tiralineas\PsHubspot\Navigator;

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'pshubspot/vendor/autoload.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/controllers/admin/AdminHSAbstractController.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsContact.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsProduct.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsDeal.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsDealAbandoned.php';

// @todo detect if multishop and force sync of each shop not all shops
class AdminHSDashboardController extends AdminHSAbstractController
{
    protected $slug = 'hs_dashboard';

    public function __construct()
    {
        parent::__construct();
        $this->meta_title = [$this->l('HubSpot')];
    }

    public function process()
    {
        if (\Tiralineas\PsHubspot\Connection::fetchAccessTokenFromCode()) {
            Navigator::toSetup();
        } elseif (Tools::getValue('PS_HUBSPOT_LICENSE')) {
            \Configuration::updateValue('PS_HUBSPOT_LICENSE', Tools::getValue('PS_HUBSPOT_LICENSE'), false, '', '');
            Tools::redirect(
                \Tiralineas\PsHubspot\Connection::getConnectAccountLink() . '&shop_uuid=' . Configuration::get('PS_HUBSPOT_SHOP_UUID', '', '', '')
            );
        }
    }

    public function renderView()
    {
        $hasLittleModuleInstalled = false;
        // DEPRECATED
        /*
        try {
            $module = Module::getInstanceByName('pshubspot_analytics');
            if ($module && isset($module->active) && $module->active) {
                $hasLittleModuleInstalled = true;
            }
        } catch (Exception $e) {
            //  Go
        }
        */

        /*********************
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

        $shopUuid = $accountsService->getShopUuid();

        if (
            !Configuration::get('PS_HUBSPOT_SHOP_UUID', '', '', '')
            || Configuration::get('PS_HUBSPOT_SHOP_UUID', '', '', '') != $shopUuid
        ) {
            Configuration::updateValue(
                'PS_HUBSPOT_SHOP_UUID',
                $shopUuid,
                false,
                '',
                ''
            );
        }

        try {
            Media::addJsDef([
                'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                    ->present($this->module->name),
            ]);

            // Retrieve the PrestaShop Account CDN
            $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());
            // Get if the account is linked
            $this->context->smarty->assign('psAccountIsLinked', $accountsService->isAccountLinked());
            // Shop ID
            $this->context->smarty->assign('psHubspotShopUuid', $shopUuid);
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();

            return '';
        }

        Context::getContext()->smarty->assign(
            [
                'PS_HUBSPOT_LICENSE' => \Configuration::get('PS_HUBSPOT_LICENSE', '', '', '') != '' ? \Configuration::get('PS_HUBSPOT_LICENSE', '', '', '') : '####-####-####-####',
                'connect_account_link' => \Tiralineas\PsHubspot\Connection::getConnectAccountLink() . '&shop_uuid=' . (Configuration::get('PS_HUBSPOT_SHOP_UUID', '', '', '', '')),
                'create_account_link' => \Tiralineas\PsHubspot\Connection::getCreateAccountLink(),
                'show_incompatibility_warning' => $hasLittleModuleInstalled,
                'settings_url' => Navigator::getSettingsUrl(),
            ]
        );

        if (!\Tiralineas\PsHubspot\Connection::isValidClientIdsStored()) {
            $this->display == 'Connect account';

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

            return $this->renderStart();
        }
        /*
        if (Configuration::get('PS_HUBSPOT_MIGRATION_MANDATORY')) {
            Navigator::toMigration();
        }
        */
        if (!Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', '')) {
            Configuration::updateValue('PS_HUBSPOT_SETUP_STEP', 2, false, '', '');
            Navigator::toSetup();
        }

        $this->display == 'Dashboard';

        return $this->renderDashboard();
    }

    private function renderStart()
    {
        Context::getContext()->smarty->assign(
            [
                'connected' => false,
            ]
        );

        return Context::getContext()->smarty->fetch($this->getTplPath('start'));
    }

    private function renderDashboard()
    {
        Context::getContext()->smarty->assign(
            [
                'setup_link' => Navigator::getSetupUrl(),
                'connected' => true,
                'shop_name' => Configuration::get('PS_SHOP_NAME'),
                'hub_id' => Configuration::get('PS_HUBSPOT_TOKEN_HUB_ID', '', '', ''),
                'access_token' => AccessToken::get(),
                'pipeline_id' => Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', ''),
                'secure_key' => Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', ''),
                'CONTACT_sync' => HsContact::syncCount(),
                'CONTACT_unsync' => HsContact::unSyncCount(),
                'PRODUCT_sync' => HsProduct::syncCount(),
                'PRODUCT_unsync' => HsProduct::unSyncCount(),
                'DEAL_sync' => HsDeal::syncCount(),
                'DEAL_unsync' => HsDeal::unSyncCount(),
                'DEALABANDONED_sync' => HsDealAbandoned::syncCount(),
                'DEALABANDONED_unsync' => HsDealAbandoned::unSyncCount(),
                'sync_endpoint' => Context::getContext()->link->getModuleLink(
                    'pshubspot',
                    'webhook',
                    [
                        'secure_key' => Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', ''),
                        'ajax' => true,
                    ],
                    true
                ),
                'PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED' => \Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED', '', '', ''),
                'PS_HUBSPOT_DEAL_DATE_FILTER' => \Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER', '', '', ''),
                'PS_HUBSPOT_DEALSABANDONED_SENIORITY' => \Configuration::get('PS_HUBSPOT_DEALSABANDONED_SENIORITY', '', '', ''),
            ]
        );

        return Context::getContext()->smarty->fetch($this->getTplPath('dashboard'));
    }
}
