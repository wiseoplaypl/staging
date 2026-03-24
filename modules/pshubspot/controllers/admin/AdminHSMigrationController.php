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

class AdminHSMigrationController extends AdminHSAbstractController
{
    protected $slug = 'hs_migration';

    public function __construct()
    {
        parent::__construct();
        $this->meta_title = [$this->l('Ecommerce Bridge Migration')];
        $this->context = Context::getContext();
        $this->bootstrap = true;
    }

    public function postProcess()
    {
    }

    public function process()
    {
        if (!\Tiralineas\PsHubspot\Connection::isValidClientIdsStored()) {
            Navigator::toSetup();
        }

        Navigator::toDashboard();
    }

    public function renderView()
    {
        \Tiralineas\PsHubspot\AccessToken::refresh();
        $limit = 50;

        Configuration::updateValue('PS_HUBSPOT_MIGRATION_CUSTOMERS', 999999, false, '', '');

        $date_migration_from = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_FROM', '', '', ''); // Desde cuando se migran, si hay filtro desde entonces sino desde siempre
        $date_migration_end = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_END', '', '', '');   // El date fin de la migración siempre será el momento de de inicio de migración, sino podría no acabar nunca de migrar al mirar el presente
        if (!$date_migration_from) {
            if (Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER', '', '', '')) {
                Configuration::updateValue('PS_HUBSPOT_MIGRATION_DATE_FROM', date('Y-m-d H:m', strtotime(Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER'))), false, '', '');
            } else {
                Configuration::updateValue('PS_HUBSPOT_MIGRATION_DATE_FROM', '2000-01-01', false, '', '');
            }

            $date_migration_from = Configuration::get('PS_HUBSPOT_MIGRATION_DATE_FROM', '', '', '');
        }

        if (!$date_migration_end) {
            Configuration::updateValue('PS_HUBSPOT_MIGRATION_DATE_END', date('Y-m-d H:m'));
            $date_migration_end = date('Y-m-d H:m');
        }

        $deal_to_migrate = HsDeal::getTotalToMigrate();
        $cart_to_migrate = HsDealAbandoned::getTotalToMigrate();
        $contact_to_migrate = 0; // HsContact::getTotalToMigrate();
        $product_to_migrate = HsProduct::getTotalToMigrate();

        if ((int) Configuration::get('PS_HUBSPOT_MIGRATION_PROPERTIES', null, null, 1)) {
            Configuration::updateValue('PS_HUBSPOT_MIGRATION_MANDATORY', 0);
            Navigator::toDashboard();
        }
        if (!$product_to_migrate && !$cart_to_migrate && !$contact_to_migrate && !$deal_to_migrate) {
            Configuration::updateValue('PS_HUBSPOT_MIGRATION_MANDATORY', 0);
            Navigator::toDashboard();
        }

        $total_orders = HsDeal::getPrepareForMigration(0, 999999);
        $total_carts = HsDealAbandoned::getPrepareForMigration(0, 999999);
        // $total_customers = HsContact::getPrepareForMigration(0, 999999);
        $total_products = HsProduct::getPrepareForMigration(0, 99999);

        Context::getContext()->smarty->assign(
            [
                'access_token' => AccessToken::get(),
                'PS_HUBSPOT_MIGRATION_PROPERTIES' => (int) Configuration::get('PS_HUBSPOT_MIGRATION_PROPERTIES', null, null, 1),
                'PS_HUBSPOT_MIGRATION_PRODUCTS' => (int) Configuration::get('PS_HUBSPOT_MIGRATION_PRODUCTS', null, null, 1),
                'PS_HUBSPOT_MIGRATION_CARTS' => (int) Configuration::get('PS_HUBSPOT_MIGRATION_CARTS', null, null, 1),
                'PS_HUBSPOT_MIGRATION_ORDERS' => (int) Configuration::get('PS_HUBSPOT_MIGRATION_ORDERS', null, null, 1),
                // 'PS_HUBSPOT_MIGRATION_CUSTOMERS' => (int)Configuration::get('PS_HUBSPOT_MIGRATION_CUSTOMERS', null, null, 1),
                'products_to_migrate' => $product_to_migrate,
                'carts_to_migrate' => $cart_to_migrate,
                'orders_to_migrate' => $deal_to_migrate,
                // 'customers_to_migrate' =>  $contact_to_migrate,
                'sync_endpoint' => Context::getContext()->link->getModuleLink(
                    'pshubspot',
                    'webhook',
                    [
                        'secure_key' => Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', ''),
                        'action' => 'migrateItems',
                        'ajax' => true,
                    ],
                    true
                ),
            ]
        );

        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . 'pshubspot/views/templates/admin/hs_migration/view.tpl');
    }
}
