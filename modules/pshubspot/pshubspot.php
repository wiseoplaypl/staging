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
require_once _PS_MODULE_DIR_ . 'pshubspot/vendor/autoload.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsContact.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsProduct.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsDeal.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsDealAbandoned.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/AvailableProperties.php';

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use Tiralineas\PsHubspot\Connection;
use Tiralineas\PsHubspot\Navigator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PsHubspot extends Module
{
    /**
     * @var ServiceContainer
     */
    private $container;

    public function __construct()
    {
        $this->name = 'pshubspot';
        $this->tab = 'others';
        $this->version = '1.1.4';
        $this->author = 'Tiralineas';
        $this->need_instance = 0;
        $this->module_key = 'f54c959d10ba6557665a289146c3c026';

        /*
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Hubspot Synchronization');
        $this->description = $this->l('Export Prestashop data to HubSpot');

        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        if (!Configuration::get('PS_HUBSPOT_CLIENT_ID', '', '', '')) {
            Configuration::updateValue('PS_HUBSPOT_CLIENT_ID', Tools::strtoupper(Tools::passwdGen(32)), false, '', '');
        }
        $this->dealing_with = [];

        if ($this->container === null) {
            $this->container = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('PS_HUBSPOT_APP_ID', '', false, '', '');
        Configuration::updateValue('PS_HUBSPOT_LICENSE', '####-####-####-####', false, '', '');
        Configuration::updateValue(
            'PS_HUBSPOT_UNIQUE_PROD_REF',
            self::areProductRefsUnique(),
            false,
            '',
            ''
        );
        Configuration::updateValue('PS_HUBSPOT_DEALSABANDONED_SENIORITY', 24 * 60, false, '', '');
        Configuration::updateValue('PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC', '', false, '', '');
        include dirname(__FILE__) . '/sql/install.php';

        $this->installNewDependencies();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('actionObjectAddAfter') &&
            $this->registerHook('actionObjectUpdateAfter') &&
            $this->registerHook('actionObjectProductInCartDeleteAfter') &&
            $this->getService('pshubspot.ps_accounts_installer')->install() &&
            $this->createTabs();
    }

    public function installNewDependencies()
    {
        if (version_compare(_PS_VERSION_, '1.7.5.0', '>=')) {
            // Test if MBO is installed
            $mboStatus = (new Prestashop\ModuleLibMboInstaller\Presenter())->present();

            if (!$mboStatus['isInstalled']) {
                try {
                    $mboInstaller = new Prestashop\ModuleLibMboInstaller\Installer(_PS_VERSION_);
                    /* @var boolean */
                    $mboInstaller->installModule();
                } catch (\Exception $e) {
                    // Some errors can happen, i.e during initialization or download of the module
                    $this->context->controller->errors[] = $e->getMessage();
                }
            }
        }

        // PrestaShop Integration Framework components
        $moduleManager = ModuleManagerBuilder::getInstance()->build();

        /* PS Account */
        if (!$moduleManager->isInstalled('ps_accounts')) {
            $moduleManager->install('ps_accounts');
        } else {
            if (!$moduleManager->isEnabled('ps_accounts')) {
                $moduleManager->enable('ps_accounts');
                $moduleManager->upgrade('ps_accounts');
            } else {
                $moduleManager->upgrade('ps_accounts');
            }
        }
    }

    public function uninstall()
    {
        Configuration::deleteByName('PS_HUBSPOT_APP_ID');
        Configuration::deleteByName('PS_HUBSPOT_CLIENT_ID');
        Configuration::deleteByName('PS_HUBSPOT_LICENSE');
        // DEPRECATED
        Configuration::deleteByName('PS_HUBSPOT_SECRET_ID');
        // ////////////
        Configuration::deleteByName('PS_HUBSPOT_NONCE');
        Configuration::deleteByName('PS_HUBSPOT_UNIQUE_PROD_REF');
        Configuration::deleteByName('PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED');
        Configuration::deleteByName('PS_HUBSPOT_DEAL_DATE_FILTER');
        Configuration::deleteByName('PS_HUBSPOT_CONTACT_DATE_FILTER_ENABLED');
        Configuration::deleteByName('PS_HUBSPOT_CONTACT_DATE_FILTER');
        Configuration::deleteByName('PS_HUBSPOT_USE_TRACKING');
        Configuration::deleteByName('PS_HUBSPOT_DEALSABANDONED_SENIORITY');
        // SURE?
        Configuration::deleteByName('PS_HUBSPOT_CLIENT_IDS_STORED');
        Configuration::deleteByName('PS_HUBSPOT_ACCESS_TOKEN');
        Configuration::deleteByName('PS_HUBSPOT_REFRESH_TOKEN');
        Configuration::deleteByName('PS_HUBSPOT_TOKEN_EXPIRY');
        Configuration::deleteByName('PS_HUBSPOT_SETUP_STEP');
        // ////////////

        // MIGRATION
        Configuration::deleteByName('PS_HUBSPOT_MIGRATION_MANDATORY');
        Configuration::deleteByName('PS_HUBSPOT_MIGRATION_DATE_FROM');

        Configuration::deleteByName('PS_HUBSPOT_MIGRATION_PROPERTIES');
        Configuration::deleteByName('PS_HUBSPOT_MIGRATION_PRODUCTS');
        Configuration::deleteByName('PS_HUBSPOT_MIGRATION_CARTS');
        Configuration::deleteByName('PS_HUBSPOT_MIGRATION_ORDERS');
        Configuration::deleteByName('PS_HUBSPOT_MIGRATION_CUSTOMERS');
        // ////////////
        Configuration::deleteByName('PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC');
        include dirname(__FILE__) . '/sql/uninstall.php';

        return parent::uninstall() &&
            $this->deleteTabs();
    }

    /**
     * Retrieve the service
     *
     * @param string $serviceName
     *
     * @return mixed
     */
    public function getService($serviceName)
    {
        return $this->container->getService($serviceName);
    }

    private function createTabs()
    {
        $res = true;
        $tabparent = 'AdminHubSpot';
        $id_parent = self::getIdFromClassName($tabparent);
        if (!$id_parent) {
            $tab = new Tab();
            $tab->active = 1;
            $tab->class_name = 'AdminHubSpot';
            $tab->name = [];
            $tab->id_parent = 0;
            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[$lang['id_lang']] = 'HubSpot';
            }
            $tab->module = $this->name;
            $res &= $tab->add();
            $id_parent = $tab->id;
        }
        $subtabs = [
            [
                'class' => 'AdminHSSetup',
                'name' => 'Set Up',
                'active' => 1,
            ],
            [
                'class' => 'AdminHSDashboard',
                'name' => 'Dashboard',
                'active' => 0,
            ],

            [
                'class' => 'AdminHSLists',
                'name' => 'Lists',
                'active' => 0,
            ],
            [
                'class' => 'AdminHSProperties',
                'name' => 'Properties',
                'active' => 0,
            ],
            [
                'class' => 'AdminHSWorkflows',
                'name' => 'Workflows',
                'active' => 0,
            ],
            [
                'class' => 'AdminHSContacts',
                'name' => 'Contacts',
                'active' => 0,
            ],
            [
                'class' => 'AdminHSDeals',
                'name' => 'Deals',
                'active' => 0,
            ],
            [
                'class' => 'AdminHSExtras',
                'name' => 'Resources',
                'active' => 0,
            ],
            [
                'class' => 'AdminHSSettings',
                'name' => 'Settings',
                'active' => 0,
            ],
            // array(
            //     'class'=>'AdminHSLink',
            //     'name'=>'Go to HubSpot'
            // ),
            [
                'class' => 'AdminHSSettings',
                'name' => 'Extra Settings',
                'active' => 0,
            ],
            // array(
            //     'class' => 'AdminHSMigration',
            //     'name' => 'Extra Settings',
            //     'active' => 0,
            // ),
            [
                'class' => 'AdminHSHelp',
                'name' => 'Help',
                'active' => 1,
            ],
        ];
        foreach ($subtabs as $subtab) {
            if (!self::getIdFromClassName($subtab['class'])) {
                $tab = new Tab();
                $tab->active = $subtab['active'];
                $tab->class_name = $subtab['class'];
                $tab->name = [];
                foreach (Language::getLanguages(false) as $lang) {
                    $tab->name[$lang['id_lang']] = $subtab['name'];
                }
                $tab->id_parent = $id_parent;
                $tab->module = $this->name;
                $res &= $tab->add();
            }
        }

        return $res;
    }

    protected static function getIdFromClassName($class_name)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                ->getValue(
                    'SELECT MIN(id_tab)
                        FROM `' . _DB_PREFIX_ . 'tab`
                        WHERE `class_name` = "' . pSQL($class_name) . '"'
                );
        }

        return (int) Tab::getIdFromClassName($class_name);
    }

    public function deleteTabs()
    {
        $id_tabs = [
            'AdminHSDashboard',
            'AdminHSSetup',
            'AdminHSLists',
            'AdminHSProperties',
            'AdminHSWorkflows',
            'AdminHSContacts',
            'AdminHSDeals',
            'AdminHSSettings',
            'AdminHSExtras',
            'AdminHSLink',
            'AdminHSHelp',
        ];
        foreach ($id_tabs as $id_tab) {
            $idtab = Tab::getIdFromClassName($id_tab);
            $tab = new Tab((int) $idtab);
            if (Validate::isLoadedObject($tab)) {
                $parentTabID = $tab->id_parent;
                $tab->delete();
                $tabCount = Tab::getNbTabs((int) $parentTabID);
                if ($tabCount == 0) {
                    $parentTab = new Tab((int) $parentTabID);
                    if (Validate::isLoadedObject($parentTab)) {
                        $parentTab->delete();
                    }
                }
            }
        }

        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        Navigator::toDashboard();
    }

    /**
     * Load the configuration form
     */
    public function hookActionWebhook()
    {
        $limit = 10;
        $done = false;
        $countFunction = 'syncCount';
        $unSyncCountFunction = 'unSyncCount';
        switch (Tools::getValue('action')) {
            case 'dryRunSync':
                $limit = (int) Tools::getValue('id', 0);
                $func = 'dryRunSync';
                break;
            case 'setToReSync':
                $limit = (int) Tools::getValue('limit', 0);
                $func = 'setToReSync';
                if ((string) Tools::getValue('idList', '')) {
                    $func = 'setToReSyncIds';
                    $limit = explode(',', (string) Tools::getValue('idList', ''));
                }
                if ((string) Tools::getValue('dateFrom', '')) {
                    $func = 'setToReSyncFrom';
                    $limit = Tools::getValue('dateFrom', '2023-03-01');
                }
                break;
            case 'fillData':
                exit('Please uncomment this line if you want to use this feature');
                $limit = (int) Tools::getValue('limit', 1);
                $this->fillData($limit);
                exit;
                break;
            case 'migrateItems':
                $limit = (int) Tools::getValue('limit', 10);
                $func = 'migrateItems';
                $countFunction = 'getMigratedTotal';
                $unSyncCountFunction = 'getTotalToMigrate';
                break;
            default:
                $limit = (int) Tools::getValue('limit', 10);
                $func = 'syncChunk';
        }

        switch (Tools::getValue('object_type')) {
            case 'CONTACT':
                $done = HsContact::$func($limit);
                break;
            case 'PRODUCT':
                $done = HsProduct::$func($limit);
                break;
            case 'DEAL':
                $done = HsDeal::$func($limit);
                break;
            case 'DEALABANDONED':
                $done = HsDealAbandoned::$func($limit);
                break;
            case 'PROPERTIES':
                $done = HsContact::migrateProperties();
                Configuration::updateValue('PS_HUBSPOT_MIGRATION_PROPERTIES', 1);
                break;
            default:
                HsContact::$func($limit);
                HsProduct::$func($limit);
                HsDeal::$func($limit);
                HsDealAbandoned::$func($limit);
        }

        $status = [
            'CONTACT_sync' => HsContact::$countFunction(),
            'CONTACT_unsync' => HsContact::$unSyncCountFunction(),
            'PRODUCT_sync' => HsProduct::$countFunction(),
            'PRODUCT_unsync' => HsProduct::$unSyncCountFunction(),
            'DEAL_sync' => HsDeal::$countFunction(),
            'DEAL_unsync' => HsDeal::$unSyncCountFunction(),
            'DEALABANDONED_sync' => HsDealAbandoned::$countFunction(),
            'DEALABANDONED_unsync' => HsDealAbandoned::$unSyncCountFunction(),
            'done' => $done,
        ];

        return json_encode($status);
    }

    public function hookActionObjectAddAfter($params)
    {
        $this->hookActionObjectUpdateAfter($params);
    }

    public function hookActionObjectUpdateAfter($params)
    {
        $class_map = [
            'Customer' => 'Contact',
            'Product' => 'Product',
            'Cart' => 'DealAbandoned',
            'Order' => 'Deal',
            'Group' => 'Group',
        ];
        $id = $params['object']->id;
        // Can't sync if there is no id
        if (!$id) {
            return;
        }
        $class_name = get_class($params['object']);
        if (!in_array($class_name, array_keys($class_map))) {
            return;
        }

        $actualContext = Context::getContext();

        // Daba error fatal a un cliente porque $actualContext->controller no es un objeto
        // Trying to get property 'controller_type' of non-object
        if (empty($actualContext->controller)
            || !is_object($actualContext->controller)
            || !isset($actualContext->controller->controller_type)) {
            return;
        }

        if (
            $actualContext->controller->controller_type == 'admin'
            && get_class($params['object']) == 'Cart'
        ) {
            return;
        }

        if (!Connection::isValidClientIdsStored()) {
            return;
        }

        $model = 'Hs' . $class_map[$class_name];
        // Prevent entering recurively in this hook
        if (isset($this->dealing_with[$model]) &&
            in_array($id, array_keys($this->dealing_with[$model]))
        ) {
            return;
        } else {
            $this->dealing_with[$model][$id] = 1;
        }

        require_once _PS_MODULE_DIR_ . 'pshubspot/model/' . $model . '.php';
        (new $model($id))->sync();
        unset($this->dealing_with[$model][$id]);
    }

    /**
     * Control the cart item remove
     */
    public function hookActionObjectProductInCartDeleteAfter($params)
    {
        $class_map = [
//            'Customer' => 'Contact',
//            'Product'  => 'Product',
            'Cart' => 'DealAbandoned', // mantengo el array para que la lógica se parezca, aunque no hiciese falta
//            'Order'    => 'Deal',
//            'Group'    => 'Group',
        ];

        $id = $params['id_cart'];
        $id_product = $params['id_product'];
        $id_product_feature = $params['id_product_attribute'];
        // Can't sync if there is no id
        if (!$id || !$id_product) {
            return;
        }
        $class_name = 'Cart';
        if (!in_array($class_name, array_keys($class_map))) {
            return; // d=====(￣▽￣*)b    no es necesario
        }
        $model = 'Hs' . $class_map[$class_name];

        if (isset($this->dealing_with[$model]) && in_array($id, array_keys($this->dealing_with[$model]))) {
            return;
        } else {
            $this->dealing_with[$model][$id] = 1;
        }
        require_once _PS_MODULE_DIR_ . 'pshubspot/model/' . $model . '.php';
        $cartObject = new $model($id);
        $nuevo_carrito = $cartObject->is_new();
        if ($cartObject->sync(!$nuevo_carrito) && $nuevo_carrito == false) {
            $cartObject->removeLineItem($id_product, $id_product_feature);
        }
        unset($this->dealing_with[$model][$id]);
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/back.css');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader($params)
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
        if (\Configuration::get('PS_HUBSPOT_USE_TRACKING') != 'on') {
            return;
        }
        $this->context->smarty->assign([
            'portalId' => \Configuration::get('PS_HUBSPOT_TOKEN_HUB_ID'),
        ]);

        return $this->display(__FILE__, 'views/templates/front/tracking.tpl');
    }

    private static function areProductRefsUnique()
    {
        return Db::getInstance()->getValue('SELECT max(n) from (SELECT count(reference) n from `' . _DB_PREFIX_ . 'product` group by reference) s') == 1;
    }

    public static function fillData($limit)
    {
        $faker = Faker\Factory::create('es_ES');

        for ($i = 0; $i < $limit; ++$i) {
            $getCustomers = Customer::getCustomers();
            $lastCustomerId = (int) end($getCustomers)['id_customer'] + 1;

            $customer = new Customer($lastCustomerId);
            $customer->id = $lastCustomerId;
            $customer->firstname = $faker->firstName();
            $customer->lastname = $faker->lastName();
            $customer->email = $faker->safeEmail();
            $customer->passwd = $faker->md5();
            $customer->add();

            $direccion = new Address();
            $direccion->id_country = 6;
            $direccion->alias = $faker->realText(16, 1);
            $direccion->id_state = $faker->numberBetween(353, 404);
            $direccion->lastname = $customer->lastname;
            $direccion->firstname = $customer->firstname;
            $direccion->address1 = $faker->streetAddress();
            $direccion->city = $faker->city();
            $direccion->postcode = $faker->postcode();
            $direccion->id_customer = $customer->id;
            $direccion->dni = $faker->regexify('[0-9]{8}[A-Z]{1}');
            $direccion->add();
        }

        for ($i = 0; $i < $limit; ++$i) {
            $getProducts = Product::getProducts(1, 1, 0, 'id_product', 'ASC');
            $lastProductId = (int) end($getProducts)['id_product'] + 1;
            $product = new Product($lastProductId);

            $product->id = $lastProductId;
            $product->name = $faker->catchPhrase();
            $product->description = $faker->paragraph();
            $product->price = $faker->randomFloat(2, 10, 300);
            $product->id_manufacturer = $faker->numberBetween(0, 3);
            $product->id_category_default = $faker->numberBetween(1, 8);
            $product->reference = $faker->regexify('[A-Z]{5}-[0-4]{2}');
            $product->indexed = 1;

            $product->add();
            $product->addToCategories($product->id_category_default);

            // Si no, no pone cantidad al producto
            StockAvailable::setQuantity($lastProductId, 0, $faker->numberBetween(15, 500), 1);
        }

        for ($i = 0; $i < $limit; ++$i) {
            $cart = new Cart();

            $agregarCantidadProductos = $faker->numberBetween(1, 10);
            $direccionEnvio = $faker->numberBetween(1, 5);
            $idCustomer = $getCustomers[$faker->numberBetween(1, count($getCustomers) - 1)];

            $cart->id_shop = 1;
            $cart->id_lang = 1;
            $cart->id_currency = 1;
            $cart->id_carrier = $faker->numberBetween(0, 3);
            $cart->id_address_delivery = $direccionEnvio;
            $cart->id_address_invoice = $direccionEnvio;
            $cart->id_customer = $idCustomer['id_customer'];
            $cart->add();

            $productoAlCarrito = $faker->numberBetween(1, count($getProducts) - 1);
            $cart->updateQty($agregarCantidadProductos, $getProducts[$productoAlCarrito]['id_product']);
            $productoAdicional = $faker->numberBetween(1, 2);
            if ($productoAdicional == 2) {
                $productoAlCarrito = $faker->numberBetween(1, count($getProducts) - 1);
                $cart->updateQty($agregarCantidadProductos, $getProducts[$productoAlCarrito]['id_product']);
            }
        }

        for ($i = 0; $i < $limit / 2; ++$i) {
            $order = new Order();

            do {
                $randomIndex = $faker->numberBetween(1, count($getCustomers) - 1);
                $randomCustomer = $getCustomers[$randomIndex];
                $getcartsFromCustomer = Cart::getCustomerCarts($randomCustomer['id_customer'], false);
                unset($getCustomers[$randomIndex]);
                sort($getCustomers);
            } while (!$getcartsFromCustomer);

            $direccionEnvio = $faker->numberBetween(0, 6);
            $getCarriers = Carrier::getCarriers(1);
            $cart = new Cart($getcartsFromCustomer[0]['id_cart']);

            $paymentsArray = ['Payment by check', 'Bank wire'];
            $moduleArray = ['ps_checkpayment', 'ps_wirepayment'];

            $order->id_address_delivery = $direccionEnvio;
            $order->id_address_invoice = $direccionEnvio;
            $order->id_cart = $getcartsFromCustomer[0]['id_cart'];
            $order->id_customer = $randomCustomer['id_customer'];
            $order->id_carrier = $getCarriers[$faker->numberBetween(0, count($getCarriers) - 1)]['id_carrier'];
            $order->payment = $paymentsArray[$faker->numberBetween(0, 1)];
            $order->module = $moduleArray[$faker->numberBetween(0, 1)];
            $order->id_currency = 1;
            $order->total_paid = $cart->getOrderTotal();
            $order->total_paid_real = $order->total_paid;
            $order->total_paid_tax_incl = $order->total_paid;
            $order->total_paid_tax_excl = $order->total_paid;
            $order->total_products = $order->total_paid;
            $order->total_products_wt = $order->total_paid;
            $order->conversion_rate = 1;
            $order->current_state = $faker->numberBetween(1, 13);
            $order->secure_key = $faker->md5();
            $order->id_shop = 1;
            $order->id_shop_group = 1;
            $order->reference = $faker->regexify('[A-Z]{9}');

            $order->add();
            // Asignar correctamente el estado del pedido
            $order->setCurrentState($order->current_state);

            // Añadir los productos al pedido
            $orderDetail = new OrderDetail();
            $orderDetail->createList($order, $cart, $order->current_state, $cart->getProducts());
        }
        // echo "Terminado!";
    }
}
