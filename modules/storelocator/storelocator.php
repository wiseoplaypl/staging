<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2022
*  @license   Single domainn
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('MAX_LINE_SIZE')) {
    define('MAX_LINE_SIZE', 0);
}
if (!defined('MAX_COLUMNS')) {
    define('MAX_COLUMNS', 12);
}

require_once _PS_MODULE_DIR_ . 'storelocator/classes/Locator.php';

require_once _PS_MODULE_DIR_ . 'storelocator/classes/OrderStoreInvoice.php';

require_once _PS_MODULE_DIR_ . 'storelocator/classes/AdminStoreSlips.php';

class Storelocator extends Module
{
    public $fields = array();

    public $translations = array();

    public $map_themes = array();

    public $weekdays = array();

    protected $id_shop = null;

    protected $id_shop_group = null;

    protected $storeHooks = array(
        'displayHome',
        'ModuleRoutes',
        'displayHeader',
        'displayStoreMap',
        'displayAdminOrder',
        'displayLeftColumn',
        'displayProductTab',
        'displayRightColumn',
        'displayFooterProduct',
        'displayBackOfficeHeader',
        'displayProductTabContent',
        'actionValidateOrder',
        'displayOrderDetail',
        'displayPDFDeliverySlip',
        'displayOverrideTemplate',
        'actionOrderStatusPostUpdate',
        'actionAdminStoresFormModifier',
        'actionAdminStoresControllerSaveAfter',
        'actionAdminStoresListingFieldsModifier',
        'actionAdminControllerInitAfter'
    );

    public function __construct()
    {
        $this->name = 'storelocator';
        $this->tab = 'front_office_features';
        $this->version = '2.1.2';
        $this->author = 'FMM Modules';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = 'f936a6cc644828c2adf1311513c76c86';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';

        parent::__construct();

        $this->displayName = $this->l('Store Locator');

        $this->description = $this->l('Adds extra features to your store locator.');

        $this->fields = $this->getStoreFields();

        $this->map_themes = $this->getMapThemes();

        $this->translations = $this->getTranslateableFields();

        $this->weekdays = $this->getWeekDays();

        if ($this->id_shop === null || !Shop::isFeatureActive()) {
            $this->id_shop = Shop::getContextShopID();
        } else {
            $this->id_shop = $this->context->shop->id;
        }
        if ($this->id_shop_group === null || !Shop::isFeatureActive()) {
            $this->id_shop_group = Shop::getContextShopGroupID();
        } else {
            $this->id_shop_group = $this->context->shop->id_shop_group;
        }
    }

    public function install()
    {
        Configuration::updateValue('FMESL_SEPARATOR', ',');
        Configuration::updateValue('FME_DISTANCE_UNIT', 'km');
        Configuration::updateValue('FMESL_ZOOM_VALUE', 10);

        include dirname(__FILE__) . '/sql/install.php';

        if (parent::install() &&
            $this->registerHook($this->storeHooks) &&
            $this->installTab('AdminStoreLocatorParent', 'Store Locator', 'store') &&
            $this->installTab('AdminStoreLocator', 'Stores', 'location_on', 'AdminStoreLocatorParent') &&
            $this->installTab('AdminStoreSlip', 'Store Slip', 'receipt', 'AdminStoreLocatorParent') &&
            $this->installTab('AdminStoreSettings', 'Settings', 'settings', 'AdminStoreLocatorParent')) {
            $this->copyDirectory(_PS_MODULE_DIR_ . 'storelocator/views/templates/admin/override-stores', _PS_OVERRIDE_DIR_ . 'controllers/admin/templates');
            $this->renameIndex();
            return true;
        }
        return false;
    }

    public function delFiles()
    {
        @unlink(_PS_OVERRIDE_DIR_ . 'controllers/admin/AdminStoresController.php');
        @unlink(_PS_OVERRIDE_DIR_ . 'controllers/front/StoresController.php');
        @unlink(_PS_ROOT_DIR_ . '/themes/' . _THEME_NAME_ . '/cms/stores_17.tpl');
        @unlink(_PS_ROOT_DIR_ . '/themes/' . _THEME_NAME_ . '/cms/stores_splittheme_17.tpl');
        return true;
    }

    public function renameIndex()
    {
        //Rename Cache Index
        if (file_exists(_PS_CACHE_DIR_ . 'class_index.php')) {
            rename(_PS_CACHE_DIR_ . 'class_index.php', _PS_CACHE_DIR_ . 'class_index' . rand(pow(10, 3 - 1), pow(10, 3) - 1) . '.php');
        }

        return true;
    }

    public function uninstall()
    {
        //If Uninstallation required for Module
        if (parent::uninstall() &&
            $this->uninstallTab() &&
            $this->removeConfigValues() &&
            $this->deleteDir(_PS_OVERRIDE_DIR_ . 'controllers/admin/templates/stores') &&
            $this->renameCache()) {
            if ($this->removeStoreAddresses()) {
                include dirname(__FILE__) . '/sql/uninstall.php';
            }
            return true;
        }
        return false;
    }

    public function renameCache()
    {
        if (file_exists(_PS_CACHE_DIR_ . 'class_index.php')) {
            rename(_PS_CACHE_DIR_ . 'class_index.php', _PS_CACHE_DIR_ . rand(pow(10, 3 - 1), pow(10, 3) - 1) . '__class_index.php');
        }
        return true;
    }

    protected function removeConfigValues()
    {
        Configuration::deleteByName('FMESL_PAGE_TITLE');
        Configuration::deleteByName('PS_STORES_CENTER_LAT');
        Configuration::deleteByName('PS_STORES_CENTER_LONG');
        Configuration::deleteByName('FMESL_KEY');
        Configuration::deleteByName('FMESL_ZOOM_VALUE');
        Configuration::deleteByName('FMESL_SEPARATOR');
        Configuration::deleteByName('FME_DISTANCE_UNIT');
        Configuration::deleteByName('FMESL_MAIN_MAP_THEME');
        Configuration::deleteByName('FMESL_HOME_MAP');
        Configuration::deleteByName('FMESL_HOME_MAP_THEME');
        Configuration::deleteByName('FMESL_LEFTCOLUMN_MAP');
        Configuration::deleteByName('FMESL_LEFTCOLUMN_MAP_THEME');
        Configuration::deleteByName('FMESL_RIGHTCOLUMN_MAP');
        Configuration::deleteByName('FMESL_RIGHTCOLUMN_MAP_THEME');
        Configuration::deleteByName('FMESL_TABSTATE');
        Configuration::deleteByName('FMESL_TAB');
        Configuration::deleteByName('FMESL_TABHEAD');
        Configuration::deleteByName('FMESL_USER');
        Configuration::deleteByName('FMESL_SBP');
        Configuration::deleteByName('FMESL_RESET');
        Configuration::deleteByName('FMESL_LAYOUT');
        Configuration::deleteByName('FMESL_STORE_EMAIL');
        Configuration::deleteByName('FMESL_STORE_FAX');
        Configuration::deleteByName('FMESL_STORE_NOTE');
        Configuration::deleteByName('FMESL_DISPLAY_STORE_MAP');
        Configuration::deleteByName('FMESL_PICKUP_TIME');
        Configuration::deleteByName('FMESL_PICKUP_DATE');
        Configuration::deleteByName('FMESL_PICKUP_STORE');
        Configuration::deleteByName('FMESL_DEFAULT_STORE');
        Configuration::deleteByName('FMESL_DEFAULT_CARRIER');
        return true;
    }

    public function getContent()
    {
        //$this->registerHook('actionAdminStoresListingFieldsModifier');
        //$this->registerHook('actionAdminControllerInitAfter');
        if (Tools::isSubmit('import_contacts')) {
            $link = $this->context->link->getAdminLink('AdminStores');
            $Index = $this->context->link->getAdminLink('AdminModules', false);
            $url = $Index . '&configure=storelocator&token=' . Tools::getAdminTokenLite('AdminModules') . '&tab_module=front_office_features&module_name=storelocator';
            $this->context->smarty->assign(array(
                'url' => $link,
                'version' => _PS_VERSION_,
                'fields' => $this->fields,
                'moduleLink' => $url,
            ));
            return $this->postProcess() . $this->display($this->_path, 'views/templates/admin/config.tpl');
        } else {
            return $this->postProcess() . $this->renderForm();
        }
    }

    public function renderForm()
    {
        $stores = array();
        $carriers = array();
        $stores = Locator::getAllStores();
        $carriers = Carrier::getCarriers($this->context->language->id);

        if (!isset($stores) || !$stores) {
            $stores = array(
                array(
                    'id_store' => 0,
                    'name' => $this->l('None'),
                ),
            );
        }

        if (!isset($carriers) || !$carriers) {
            $carriers = array(
                array(
                    'id_carrier' => 0,
                    'name' => $this->l('No carrier found'),
                ),
            );
        }

        if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '<') == true) {
            $image_url = _PS_IMG_DIR_ . 'logo_stores.gif';
            $image_url = ImageManager::thumbnail($image_url, 'logo_stores.gif', 30, 'gif', true, false);
            $warning = $this->context->controller->warnings[] = $this->l('You can get Google API key from ') . '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . $this->l('HERE') . '</a>';
        } else {
            $image_url = _PS_IMG_DIR_ . 'logo_stores.png';
            $image_url = ImageManager::thumbnail($image_url, 'logo_stores.png', 30, 'png', true, false);
            $warning = $this->displayWarning($this->l('You can get Google API key from ') . '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . $this->l('HERE') . '</a>');
        }

        $radio = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=') == true) ? 'switch' : 'radio';
        $dist_type = array(
            array(
                'id' => 'mi',
                'name' => $this->l('mile'),
            ),
            array(
                'id' => 'km',
                'name' => $this->l('kilometer'),
            ),
        );

        $str_layout = array(
            array(
                'id' => 0,
                'name' => $this->l('1 Column'),
            ),
            array(
                'id' => 2,
                'name' => $this->l('2 Column Right'),
            ),
            array(
                'id' => 1,
                'name' => $this->l('2 Column Left'),
            ),
            array(
                'id' => 3,
                'name' => $this->l('3 Column'),
            ),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'tabs' => array(
                    'general' => $this->l('General'),
                    'map_settings' => $this->l('Map Settings'),
                    'store_settings' => $this->l('Store Settings'),
                    'time_settings' => $this->l('Pick Up Settings'),
                    'csv_import' => $this->l('CSV Import'),
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Store Page Title:'),
                        'name' => 'FMESL_PAGE_TITLE',
                        'hint' => $this->l('Forbidden characters: &amp;lt;&amp;gt;;=#{}'),
                        'tab' => 'general',
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Map Page Layout'),
                        'name' => 'FMESL_LAYOUT_THEME',
                        'values' => array(
                            array(
                                'id' => 'fmesl_lay_1',
                                'value' => 0,
                                'label' => $this->l('Classic'),
                            ),
                            array(
                                'id' => 'fmesl_lay_2',
                                'value' => 1,
                                'label' => $this->l('Split Column - List on Left'),
                            ),
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show Link to Map Page'),
                        'name' => 'FMESL_MAP_LINK',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Option only available in Split Column design.'),
                        'values' => array(
                            array(
                                'id' => 'FMESL_MAP_LINK_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'FMESL_MAP_LINK_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                        'tab' => 'general',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Default Store'),
                        'name' => 'FMESL_DEFAULT_STORE',
                        'options' => array(
                            'query' => $stores,
                            'id' => 'id_store',
                            'name' => 'name',
                        ),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Carrier'),
                        'name' => 'FMESL_DEFAULT_CARRIER',
                        'options' => array(
                            'query' => $carriers,
                            'id' => 'id_carrier',
                            'name' => 'name',
                        ),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Latitude by default:'),
                        'name' => 'PS_STORES_CENTER_LAT',
                        'col' => 4,
                        'required' => true,
                        'hint' => $this->l('Current view of map.'),
                        'placeholder' => 25.765005,
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Longitude by default:'),
                        'name' => 'PS_STORES_CENTER_LONG',
                        'col' => 4,
                        'required' => true,
                        'hint' => $this->l('Current view of map.'),
                        'placeholder' => -80.24379700,
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Google API Key:'),
                        'name' => 'FMESL_KEY',
                        'required' => true,
                        'hint' => $this->l('See above warning to find key'),
                        'tab' => 'general',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Default Zoom Level:'),
                        'name' => 'FMESL_ZOOM_VALUE',
                        'col' => 2,
                        'required' => false,
                        'desc' => $this->l('Zoom range is from 0 which is lowest and 21 is the highest.'),
                        'hint' => $this->l('Default zoom level view of map. Use from 0 to 20.'),
                        'placeholder' => 10,
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('Store detail page Zoom Level:'),
                        'name' => 'FMESL__STORE_DETAIL_ZOOM_VALUE',
                        'col' => 2,
                        'required' => false,
                        'desc' => $this->l('Zoom range is from 0 which is lowest and 21 is the highest.'),
                        'hint' => $this->l('Default zoom level view of map. Use from 0 to 20.'),
                        'placeholder' => 10,
                        'tab' => 'general',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => false,
                        'label' => $this->l('CSV Separator:'),
                        'name' => 'FMESL_SEPARATOR',
                        'col' => 2,
                        'desc' => $this->l('This will be only used during import of stores through CSV file.'),
                        'hint' => $this->l('Use , ; : | default is comma'),
                        'tab' => 'csv_import',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Distance Unit:'),
                        'name' => 'FME_DISTANCE_UNIT',
                        'options' => array(
                            'query' => $dist_type,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Map Theme for Store Main Page:'),
                        'name' => 'FMESL_MAIN_MAP_THEME',
                        'options' => array(
                            'query' => $this->map_themes,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show Map on Home Page'),
                        'name' => 'FMESL_HOME_MAP',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_HOME_MAP_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_HOME_MAP_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Map Theme for Home Page:'),
                        'name' => 'FMESL_HOME_MAP_THEME',
                        'options' => array(
                            'query' => $this->map_themes,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show Map on Left Column'),
                        'name' => 'FMESL_LEFTCOLUMN_MAP',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_LEFTCOLUMN_MAP_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_LEFTCOLUMN_MAP_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Map Theme for Left Column:'),
                        'name' => 'FMESL_LEFTCOLUMN_MAP_THEME',
                        'options' => array(
                            'query' => $this->map_themes,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show Map on Right Column'),
                        'name' => 'FMESL_RIGHTCOLUMN_MAP',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_RIGHTCOLUMN_MAP_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_RIGHTCOLUMN_MAP_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Map Theme for Right Column:'),
                        'name' => 'FMESL_RIGHTCOLUMN_MAP_THEME',
                        'options' => array(
                            'query' => $this->map_themes,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show Tab on Product page?'),
                        'name' => 'FMESL_TABSTATE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_TABSTATE_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_TABSTATE_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'general',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Tab Title:'),
                        'name' => 'FMESL_TAB',
                        'hint' => $this->l('In case if you want to show stores block on product.'),
                        'tab' => 'general',
                    ),
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Tab Heading:'),
                        'name' => 'FMESL_TABHEAD',
                        'hint' => $this->l('In case if you want to show stores block on product.'),
                        'tab' => 'general',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Autolocate User Location?'),
                        'name' => 'FMESL_USER',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_USER_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_USER_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Allow Search by Product?'),
                        'name' => 'FMESL_SBP',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_SBP_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_SBP_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'map_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show Reset Button?'),
                        'name' => 'FMESL_RESET',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_RESET_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_RESET_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'map_settings',
                    ),
                    (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) ? array(
                        'type' => 'select',
                        'label' => $this->l('Store Page Layout:'),
                        'name' => 'FMESL_LAYOUT',
                        'options' => array(
                            'query' => $str_layout,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                        'tab' => 'store_settings',
                    ) : array('type' => 'hidden', 'name' => 'FMESL_LAYOUT'),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show store email on store popup'),
                        'name' => 'FMESL_STORE_EMAIL',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_STORE_EMAIL_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_STORE_EMAIL_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show store fax number on store popup'),
                        'name' => 'FMESL_STORE_FAX',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_STORE_FAX_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_STORE_FAX_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Show store note on store popup'),
                        'name' => 'FMESL_STORE_NOTE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'FMESL_STORE_NOTE_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'FMESL_STORE_NOTE_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Use below icon for all stores?'),
                        'name' => 'FMESL_GLOBAL_ICON',
                        'values' => array(
                            array(
                                'id' => 'fmesl_ch_1',
                                'value' => 0,
                                'label' => $this->l('Use below icon'),
                            ),
                            array(
                                'id' => 'fmesl_ch_2',
                                'value' => 1,
                                'label' => $this->l('Each store has own icon'),
                            ),
                        ),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Store Map Icon'),
                        'name' => 'img',
                        'display_image' => true,
                        'image' => $image_url ? $image_url : false,
                        'size' => 300,
                        'hint' => $this->l('Upload an Image type PNG from your computer.'),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => 'html',
                        'name' => 'FMESL_DISPLAY_STORE_MAP',
                        'label' => '',
                        'html_content' => '<code>{hook h=\'displayStoreMap\'}</code>',
                        'desc' => $this->l('Just copy and paste the above hook code in your template file to display stores anywhere in your website.'),
                        'tab' => 'store_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Enable Pickup from Store'),
                        'name' => 'FMESL_PICKUP_STORE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Enable customers to select pickup from stores.'),
                        'values' => array(
                            array(
                                'id' => 'FMESL_PICKUP_STORE_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'FMESL_PICKUP_STORE_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                        'tab' => 'time_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Allow Pickup Date Selection'),
                        'name' => 'FMESL_PICKUP_DATE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Allow customers to select pickup date.'),
                        'values' => array(
                            array(
                                'id' => 'FMESL_PICKUP_DATE_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'FMESL_PICKUP_DATE_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                        'tab' => 'time_settings',
                    ),
                    array(
                        'type' => $radio,
                        'label' => $this->l('Allow Pickup Time Selection'),
                        'name' => 'FMESL_PICKUP_TIME',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'desc' => $this->l('Allow customers to select pickup time. Your store hours will be used for selection.'),
                        'values' => array(
                            array(
                                'id' => 'FMESL_PICKUP_TIME_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id' => 'FMESL_PICKUP_TIME_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                        'tab' => 'time_settings',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $warning . $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        $fields = array();
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $fields['FMESL_TAB'][$lang['id_lang']] = Tools::getValue(
                'FMESL_TAB_' . $lang['id_lang'],
                Configuration::get(
                    'FMESL_TAB',
                    $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            );
            $fields['FMESL_TABHEAD'][$lang['id_lang']] = Tools::getValue(
                'FMESL_TABHEAD_' . $lang['id_lang'],
                Configuration::get(
                    'FMESL_TABHEAD',
                    $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            );
            $fields['FMESL_PAGE_TITLE'][$lang['id_lang']] = Tools::getValue(
                'FMESL_PAGE_TITLE_' . $lang['id_lang'],
                Configuration::get(
                    'FMESL_PAGE_TITLE',
                    $lang['id_lang'],
                    $this->id_shop_group,
                    $this->id_shop
                )
            );
        }

        $fields['FMESL_KEY'] = Configuration::get('FMESL_KEY', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_SEPARATOR'] = Configuration::get('FMESL_SEPARATOR', null, $this->id_shop_group, $this->id_shop);
        $fields['FME_DISTANCE_UNIT'] = Configuration::get('FME_DISTANCE_UNIT', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_TABSTATE'] = (int) Configuration::get('FMESL_TABSTATE', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_USER'] = (int) Configuration::get('FMESL_USER', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_SBP'] = (int) Configuration::get('FMESL_SBP', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_RESET'] = (int) Configuration::get('FMESL_RESET', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_LAYOUT'] = (int) Configuration::get('FMESL_LAYOUT', null, $this->id_shop_group, $this->id_shop);
        $fields['PS_STORES_CENTER_LONG'] = Configuration::get('PS_STORES_CENTER_LONG', null, $this->id_shop_group, $this->id_shop);
        $fields['PS_STORES_CENTER_LAT'] = Configuration::get('PS_STORES_CENTER_LAT', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_STORE_EMAIL'] = Configuration::get('FMESL_STORE_EMAIL', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_STORE_FAX'] = Configuration::get('FMESL_STORE_FAX', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_STORE_NOTE'] = Configuration::get('FMESL_STORE_NOTE', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_ZOOM_VALUE'] = Configuration::get('FMESL_ZOOM_VALUE', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_MAIN_MAP_THEME'] = Configuration::get('FMESL_MAIN_MAP_THEME', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_HOME_MAP'] = Configuration::get('FMESL_HOME_MAP', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_HOME_MAP_THEME'] = Configuration::get('FMESL_HOME_MAP_THEME', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_LEFTCOLUMN_MAP'] = Configuration::get('FMESL_LEFTCOLUMN_MAP', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_LEFTCOLUMN_MAP_THEME'] = Configuration::get('FMESL_LEFTCOLUMN_MAP_THEME', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_RIGHTCOLUMN_MAP'] = Configuration::get('FMESL_RIGHTCOLUMN_MAP', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_RIGHTCOLUMN_MAP_THEME'] = Configuration::get('FMESL_RIGHTCOLUMN_MAP_THEME', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_GLOBAL_ICON'] = (int) Configuration::get('FMESL_GLOBAL_ICON', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_LAYOUT_THEME'] = (int) Configuration::get('FMESL_LAYOUT_THEME', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL__STORE_DETAIL_ZOOM_VALUE'] = (int) Configuration::get('FMESL__STORE_DETAIL_ZOOM_VALUE', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_MAP_LINK'] = (int) Configuration::get('FMESL_MAP_LINK', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_DEFAULT_STORE'] = (int) Configuration::get('FMESL_DEFAULT_STORE', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_DEFAULT_CARRIER'] = (int) Configuration::get('FMESL_DEFAULT_CARRIER', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_PICKUP_TIME'] = (int) Configuration::get('FMESL_PICKUP_TIME', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_PICKUP_DATE'] = (int) Configuration::get('FMESL_PICKUP_DATE', null, $this->id_shop_group, $this->id_shop);
        $fields['FMESL_PICKUP_STORE'] = (int) Configuration::get('FMESL_PICKUP_STORE', null, $this->id_shop_group, $this->id_shop);
        return $fields;
    }

    private function postProcess()
    {
        $languages = Language::getLanguages(false);
        $values = array();
        // If Needed to Update the Settings
        if (Tools::isSubmit('submit' . $this->name)) {
            foreach ($languages as $lang) {
                $values['FMESL_TAB'][$lang['id_lang']] = Tools::getValue('FMESL_TAB_' . $lang['id_lang']);
                $values['FMESL_TABHEAD'][$lang['id_lang']] = Tools::getValue('FMESL_TABHEAD_' . $lang['id_lang']);
                $values['FMESL_PAGE_TITLE'][$lang['id_lang']] = Tools::getValue('FMESL_PAGE_TITLE_' . $lang['id_lang']);
            }

            Configuration::updateValue(
                'FMESL_TAB',
                $values['FMESL_TAB'],
                true,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_TABHEAD',
                $values['FMESL_TABHEAD'],
                true,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_PAGE_TITLE',
                $values['FMESL_PAGE_TITLE'],
                true,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'PS_DISTANCE_UNIT',
                Tools::getValue('FME_DISTANCE_UNIT'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FME_DISTANCE_UNIT',
                Tools::getValue('FME_DISTANCE_UNIT'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_LAYOUT',
                (int)
                Tools::getValue('FMESL_LAYOUT'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_TABSTATE',
                (int)
                Tools::getValue('FMESL_TABSTATE'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_USER',
                (int)
                Tools::getValue('FMESL_USER'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_RESET',
                (int)
                Tools::getValue('FMESL_RESET'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_SBP',
                (int)
                Tools::getValue('FMESL_SBP'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_ZOOM_VALUE',
                (int)
                Tools::getValue('FMESL_ZOOM_VALUE'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_KEY',
                Tools::getValue('FMESL_KEY'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_SEPARATOR',
                Tools::getValue('FMESL_SEPARATOR'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'PS_STORES_CENTER_LAT',
                Tools::getValue('PS_STORES_CENTER_LAT'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_STORE_EMAIL',
                Tools::getValue('FMESL_STORE_EMAIL'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_STORE_FAX',
                Tools::getValue('FMESL_STORE_FAX'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_STORE_NOTE',
                Tools::getValue('FMESL_STORE_NOTE'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'PS_STORES_CENTER_LONG',
                Tools::getValue('PS_STORES_CENTER_LONG'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_MAIN_MAP_THEME',
                Tools::getValue('FMESL_MAIN_MAP_THEME'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_HOME_MAP',
                Tools::getValue('FMESL_HOME_MAP'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_HOME_MAP_THEME',
                Tools::getValue('FMESL_HOME_MAP_THEME'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_LEFTCOLUMN_MAP',
                Tools::getValue('FMESL_LEFTCOLUMN_MAP'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_LEFTCOLUMN_MAP_THEME',
                Tools::getValue('FMESL_LEFTCOLUMN_MAP_THEME'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_RIGHTCOLUMN_MAP',
                Tools::getValue('FMESL_RIGHTCOLUMN_MAP'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_RIGHTCOLUMN_MAP_THEME',
                Tools::getValue('FMESL_RIGHTCOLUMN_MAP_THEME'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_GLOBAL_ICON',
                (int) Tools::getValue('FMESL_GLOBAL_ICON'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_LAYOUT_THEME',
                (int) Tools::getValue('FMESL_LAYOUT_THEME'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL__STORE_DETAIL_ZOOM_VALUE',
                (int) Tools::getValue('FMESL__STORE_DETAIL_ZOOM_VALUE'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_MAP_LINK',
                (int) Tools::getValue('FMESL_MAP_LINK'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_DEFAULT_STORE',
                Tools::getValue('FMESL_DEFAULT_STORE'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_DEFAULT_CARRIER',
                Tools::getValue('FMESL_DEFAULT_CARRIER'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_PICKUP_TIME',
                Tools::getValue('FMESL_PICKUP_TIME'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_PICKUP_DATE',
                Tools::getValue('FMESL_PICKUP_DATE'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );
            Configuration::updateValue(
                'FMESL_PICKUP_STORE',
                Tools::getValue('FMESL_PICKUP_STORE'),
                false,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );

            $file = Tools::fileAttachment('img');
            if (!empty($file['name'])) {
                $file['type'] = explode('/', $file['mime']);
                $file['type'] = end($file['type']);
                if (ImageManager::validateUpload($file, Tools::convertBytes(ini_get('upload_max_filesize')))) {
                    return $this->displayWarning($this->l('Image size exceeds limit in your PrestaShop settings'));
                } else {
                    ImageManager::resize($file['tmp_name'], _PS_IMG_DIR_ . 'logo_stores.png', 30, 30, 'png', false);
                    ImageManager::resize($file['tmp_name'], _PS_IMG_DIR_ . 'tmp/logo_stores.png', 30, 30, 'png', false);
                    if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '<') == true) {
                        ImageManager::resize($file['tmp_name'], _PS_IMG_DIR_ . 'logo_stores.gif', 30, 30, 'gif', false);
                        ImageManager::resize($file['tmp_name'], _PS_IMG_DIR_ . 'tmp/logo_stores.gif', 30, 30, 'gif', false);
                    }
                }
            }
            return $this->displayConfirmation($this->l('The settings have been updated.'));
        } elseif (Tools::isSubmit('importStoreContacts')) {
            $this->renderCSV();
        } elseif (Tools::isSubmit('importContacts')) {
            $this->processCSV();
        }
    }

    protected function processCSV()
    {
        if ($_FILES['csv'] && $_FILES['csv']['name']) {
            $separator = Configuration::get('FMESL_SEPARATOR', null, $this->id_shop_group, $this->id_shop);
            $separator = ($separator) ? $separator : ',';
            $file = $_FILES['csv']['tmp_name'];
            $handle = $this->openCSV($file);
            $nb_column = $this->getNbrColumn($handle, $separator);
            $data = array();
            $content = array();

            ini_set('auto_detect_line_endings', true);

            $rows = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $no_of_rows = count($rows);

            $data['head'] = explode($separator, $rows[0]);
            for ($i = 1; $i < $no_of_rows; $i++) {
                $data['content'][$i] = explode($separator, $rows[$i]);
            }

            $no_of_cols = count($data['head']);
            for ($i = 1; $i < $no_of_rows; $i++) {
                for ($j = 0; $j < $no_of_cols; $j++) {
                    $content[$i - 1][$data['head'][$j]] = $data['content'][$i][$j];
                }
            }

            $defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
            $this->context->smarty->assign(array(
                'data' => $data,
                'content' => $content,
                'fields' => $this->fields,
                'nb_column' => $nb_column,
                'no_of_rows' => $no_of_rows,
                'no_of_cols' => $no_of_cols,
                'MAX_COLUMNS' => MAX_COLUMNS,
                'defaultLanguage' => $defaultLanguage,
                'languages' => Language::getLanguages(false),
                'form_action' => Tools::safeOutput($_SERVER['REQUEST_URI']),
            ));

            die(Tools::jsonEncode($this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/csv_form.tpl'
            )));
        }
    }

    protected function renderCSV()
    {
        $cols_no = Tools::getValue('nb_cols');
        $rows_no = Tools::getValue('nb_rows');

        //limiting to display maximum columns
        if ($cols_no > MAX_COLUMNS) {
            $cols_no = MAX_COLUMNS;
        }

        $col_head = array();
        $col_N = array();
        $required_fields = 0;
        for ($i = 0; $i < $cols_no; $i++) {
            $col_head[] = Tools::getValue('head_' . $i);
            $col_N[$col_head[$i]] = Tools::getValue('col_' . $i);

            if ($col_head[$i] == 'address1' || $col_head[$i] == 'city' || $col_head[$i] == 'name') {
                $required_fields++;
            }
        }

        // checking mandatory fields i.e. address,city,zipcode,id_state,id_country,latitude,longitude
        if ($required_fields < 3) {
            // redirect to controller and display error message;
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminStores') . '&required_fields_error');
        } else {
            $skip = 0;
            $inserted = 0;
            // optional fields
            $name = '';
            $fax = '';
            $phone = '';
            $enabled = 0;
            // required fields
            $address = '';
            $city = '';
            $zipcode = '';
            $id_state = 0;
            $latitude = '';
            $longitude = '';

            $id_country = Configuration::get('PS_COUNTRY_DEFAULT');
            for ($i = 0; $i < $rows_no - 1; $i++) {
                // assigning values to optional fields
                if (isset($col_N['name'][$i])) {
                    $name = $col_N['name'][$i];
                }
                if (isset($col_N['fax'][$i])) {
                    $fax = $col_N['fax'][$i];
                }
                if (isset($col_N['phone'][$i])) {
                    $phone = $col_N['phone'][$i];
                }
                if (isset($col_N['active'][$i])) {
                    $enabled = $col_N['active'][$i];
                }
                if (isset($col_N['id_state'][$i])) {
                    $id_state = $col_N['id_state'][$i];
                }
                // assigning values to required fields
                $address = $col_N['address1'][$i];
                $city = $col_N['city'][$i];
                $zipcode = $col_N['postcode'][$i];

                // getting latitude and longitude from a valid Google API
                $apiKey = Configuration::get(
                    'FMESL_KEY',
                    null,
                    $this->id_shop_group,
                    $this->id_shop
                );
                $id_state = 0;
                $co = (!empty(trim($apiKey)))? array() : $this->getLatLong($address, $apiKey);
                if (isset($co) && isset($co['lat']) && isset($co['long'])) {
                    $latitude = (float) $co['lat'];
                    $longitude = (float) $co['long'];
                    $id_country = (int) Country::getByIso($co['iso']);
                    $id_state = (isset($co['iso_state']) && $co['iso_state']) ? State::getIdByIso($co['iso_state'], $id_country) : 0;
                } else {
                    for ($i = 0; $i < $rows_no - 1; $i++) {
                        if (isset($col_N['latitude'][$i])) {
                            $latitude = (float) $col_N['latitude'][$i];
                        }
                        if (isset($col_N['longitude'][$i])) {
                            $longitude = (float) $col_N['longitude'][$i];
                        }
                    }
                }
                if ($id_country && !empty($address) && !empty($city) && !empty($name)) {
                    $store = new Store();
                    $store->fax = pSQL($fax);
                    $store->city = pSQL($city);
                    $store->phone = pSQL($phone);
                    $store->active = pSQL($enabled);
                    $store->postcode = pSQL($zipcode);
                    $store->id_state = pSQL($id_state);
                    $store->latitude = pSQL($latitude);
                    $store->longitude = pSQL($longitude);
                    $store->id_country = pSQL($id_country);
                    $store->id_state = pSQL($id_state);

                    if (true === Tools::version_compare(_PS_VERSION_, '1.7.3.0', '>=')) {
                        $store->name = array();
                        $store->address1 = array();
                        foreach (Language::getLanguages(false) as $lang) {
                            $store->name[$lang['id_lang']] = pSQL($name);
                            $store->address1[$lang['id_lang']] = pSQL($address);
                        }
                    } else {
                        $store->name = pSQL($name);
                        $store->address1 = pSQL($address);
                    }

                    $requiredErrors = $store->validateFieldsRequiredDatabase();

                    if ((isset($requiredErrors) && $requiredErrors) || !$store->add()) {
                        $skip++;
                    } else {
                        $inserted++;
                    }
                } else {
                    $skip++;
                }
            }

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminStores', false) . '&'. http_build_query(array(
                'info' => 1,
                'skip' => $skip,
                'inserted' => $inserted,
                'token' => Tools::getAdminTokenLite('AdminStores')
            )));
        }
    }

    protected function getLatLong($address, $apiKey)
    {
        set_time_limit(0);

        $address = str_replace(' ', '+', $address);
        $geocode = Tools::file_get_contents(sprintf(
            'https://maps.google.com/maps/api/geocode/json?key=%s&address=%s&sensor=false',
            $apiKey,
            urlencode($address)
        ));
        $geo = Tools::jsonDecode($geocode);
        
        $result = array();
        if ($geo->status = 'OK') {
            $latitude = $geo->results[0]->geometry->location->lat;
            $longitude = $geo->results[0]->geometry->location->lng;

            $iso_country = $this->context->country->iso_code;
            $iso_state = '';
            foreach ($geo->results[0]->address_components as $comp) {
                if ($comp->types[0] == 'country') {
                    $iso_country = $comp->short_name;
                }
                if ($comp->types[0] == 'administrative_area_level_1') {
                    $iso_state = $comp->short_name;
                }
            }
            $result = array('lat' => $latitude, 'long' => $longitude, 'iso' => $iso_country, 'iso_state' => $iso_state);
        }
        return $result;
    }

    protected function openCSV($file)
    {
        // open csv file
        $handle = false;
        if (is_file($file) && is_readable($file)) {
            $handle = fopen($file, 'r');
        }

        if (!$handle) {
            $this->errors[] = Tools::displayError('Cannot read the .CSV file');
        }

        $this->rewindBomAware($handle);

        for ($i = 0; $i < (int) Tools::getValue('skip'); ++$i) {
            fgetcsv($handle, MAX_LINE_SIZE, $this->separator);
        }
        return $handle;
    }

    protected function getNbrColumn($handle, $glue = ',')
    {
        if (!is_resource($handle)) {
            return false;
        }
        $tmp = fgetcsv($handle, MAX_LINE_SIZE, $glue);
        $this->rewindBomAware($handle);
        return count($tmp);
    }

    protected static function rewindBomAware($handle)
    {
        // A rewind wrapper that skips BOM signature wrongly
        if (!is_resource($handle)) {
            return false;
        }
        rewind($handle);
        if ((fread($handle, 3)) != "\xEF\xBB\xBF") {
            rewind($handle);
        }
    }
/**
    public function setMediaFiles()
    {
        $api_key = Configuration::get('FMESL_KEY', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id);
        $base_url = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_;
        $this->context->controller->addJS($base_url . __PS_BASE_URI__ . 'modules/storelocator/views/js/jquery.easy-autocomplete.min.js');
        $this->context->controller->addCSS($base_url . __PS_BASE_URI__ . 'modules/storelocator/views/css/easy-autocomplete.min.css');

        Media::addJsDef(array('placeholder_label' => $this->translations['placeholder_label']));
        Media::addJsDef(array('search_url' => $this->context->link->getModuleLink('storelocator', 'articlesearch', array(), true)));
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $this->context->controller->addCSS($base_url . __PS_BASE_URI__ . 'modules/storelocator/views/css/stores.css');
        } else {
            $this->context->controller->addCSS($this->_path . 'views/css/stores.css');
        }
        if (!Configuration::get('PS_STORES_SIMPLIFIED') && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) {
            $this->context->controller->addJqueryUI(array(
                'ui.core',
                'ui.widget',
            ));

            // prevent loading default stores.js file
            $this->context->controller->removeJS(_PS_THEME_DIR_.'js/stores.js');
            // use storelocatore stores.js file instead
            $this->context->controller->addJS($this->_path.'views/js/stores.js');
        } elseif (!Configuration::get('PS_STORES_SIMPLIFIED') && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->context->controller->addJS($this->_path .'views/js/stores_17.js');
        }
        $default_country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $this->context->controller->addJS('http' . ((Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 's' : '') . '://maps.google.com/maps/api/js?key=' . $api_key . '&amp;region=' . Tools::substr($default_country->iso_code, 0, 2));
        }
    }
*/
	
	public function setMediaFiles()
{
    // ✅ Solo cargar assets del Store Locator en sus páginas
    $controller = Dispatcher::getInstance()->getController();
    if (!in_array($controller, array('stores', 'storedetails'))) {
        return;
    }

    $api_key = Configuration::get(
        'FMESL_KEY',
        null,
        Context::getContext()->shop->id_shop_group,
        Context::getContext()->shop->id
    );

    $base_url = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'))
        ? _PS_BASE_URL_SSL_
        : _PS_BASE_URL_;

    // Autocomplete (solo lo usamos en /stores)
    $this->context->controller->addJS($base_url . __PS_BASE_URI__ . 'modules/storelocator/views/js/jquery.easy-autocomplete.min.js');
    $this->context->controller->addCSS($base_url . __PS_BASE_URI__ . 'modules/storelocator/views/css/easy-autocomplete.min.css');

    // JS variables
    Media::addJsDef(array(
        'placeholder_label' => $this->translations['placeholder_label'],
        'search_url'        => $this->context->link->getModuleLink('storelocator', 'articlesearch', array(), true),
    ));

    // CSS
    if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
        $this->context->controller->addCSS($base_url . __PS_BASE_URI__ . 'modules/storelocator/views/css/stores.css');
    } else {
        $this->context->controller->addCSS($this->_path . 'views/css/stores.css');
    }

    // Stores JS (según versión)
    if (!Configuration::get('PS_STORES_SIMPLIFIED') && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
        $this->context->controller->addJqueryUI(array('ui.core', 'ui.widget'));

        // prevent loading default stores.js file
        $this->context->controller->removeJS(_PS_THEME_DIR_ . 'js/stores.js');

        // use storelocator stores.js file instead
        $this->context->controller->addJS($this->_path . 'views/js/stores.js');
    } elseif (!Configuration::get('PS_STORES_SIMPLIFIED') && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
        $this->context->controller->addJS($this->_path . 'views/js/stores_17.js');
    }

    // Google Maps API (solo en /stores)
    if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
        $default_country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $region = Tools::substr($default_country->iso_code, 0, 2);

        // ✅ Usa HTTPS y '&' normal (no '&amp;')
        $this->context->controller->addJS(
            'https://maps.google.com/maps/api/js?key=' . urlencode($api_key) . '&region=' . urlencode($region)
        );
    }
}

	
	
	
	
    protected function getTemplateJsVariables()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $medium_size = Image::getSize(ImageType::getFormattedName('medium'));
        } else {
            $medium_size = Image::getSize(ImageType::getFormatedName('medium'));
        }
        $def_zoom = (int) Configuration::get('FMESL_ZOOM_VALUE', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id);
        $def_zoom = ($def_zoom <= 0) ? 10 : $def_zoom;
        $lang_id = (int) $this->context->language->id;
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $api_key = Configuration::get('FMESL_KEY', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id);
        $_http = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https' : 'http';
        $default_country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));

        $isPickupStore = Configuration::get(
            'FMESL_PICKUP_STORE',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        );
        $isPickupDateEnabled = (bool) Configuration::get(
            'FMESL_PICKUP_DATE',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        );

        if (true === (bool) $isPickupStore) {
            $this->context->controller->addCss($this->_path . 'views/css/zabuto_calendar.css');
            $this->context->controller->addJs(array(
                $this->_path . 'views/js/zabuto_calendar.min.js',
                $this->_path . 'views/js/store_cal.js',
            ));

            if (true === (bool) $isPickupDateEnabled) {
                $this->context->controller->addJs(array(
                    $this->_path . 'views/js/moment.min.js',
                    $this->_path . 'views/js/flatpickr/flatpickr.js',
                    $this->_path . 'views/js/flatpickr//l10n/' . $this->context->language->iso_code . '.js',
                ));
                $this->context->controller->addCSS(array(
                    $this->_path . 'views/css/flatpickr/flatpickr.css',
                    $this->_path . 'views/css/flatpickr/material_blue.css',
                ));
            }
        }

        $currentZone = Configuration::get('PS_TIMEZONE');
        $zoneTime = new DateTime(date('Y-m-d'), new DateTimeZone($currentZone));
        $year = $zoneTime->format('Y');
        // current month
        $month = $zoneTime->format('m');

        // next month
        $month += 1;

        // get last day of current month
        $lastday = (int)(date('%d', mktime(0, 0, 0, ($month >= 12 ? 1 : $month + 1), 0, ($month >= 12 ? $year + 1 : $year))));
        // generate date for last day of next month
        $lastDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($lastday, 2, '0', STR_PAD_LEFT);

        $this->assignStores();
        $this->context->smarty->assign(array(
            'calYear' => date('Y'),
            'maxDate' => $lastDate,
            'mediumSize' => $medium_size,
            'protocol_link' => $protocol_link,
            'default_store' => $this->getDefaultStore(),
            'preselectedPickupTime' => $this->getPickupTime(),
            'preselectedPickupDate' => $this->getPickupDate(),
            'st_page' => Dispatcher::getInstance()->getController(),
            'defaultLat' => (float) Configuration::get('PS_STORES_CENTER_LAT', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'defaultLong' => (float) Configuration::get('PS_STORES_CENTER_LONG', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'default_carrier' => (int) Configuration::get('FMESL_DEFAULT_CARRIER', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'searchUrl' => $this->context->link->getModuleLink($this->name, 'storefinder', array(), true),
            'logo_store' => Configuration::get('PS_STORES_ICON', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_STORE_EMAIL' => (int) Configuration::get('FMESL_STORE_EMAIL', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_STORE_FAX' => (int) Configuration::get('FMESL_STORE_FAX', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_STORE_NOTE' => (int) Configuration::get('FMESL_STORE_NOTE', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_PICKUP_TIME' => (int) Configuration::get('FMESL_PICKUP_TIME', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_PICKUP_DATE' => (int) $isPickupDateEnabled,
            'FMESL_PICKUP_STORE' => (int) $isPickupStore,
            'FMESL_USER' => (int) Configuration::get('FMESL_USER', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_RESET' => (int) Configuration::get('FMESL_RESET', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_SBP' => (int) Configuration::get('FMESL_SBP', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_GLOBAL_ICON' => (int) Configuration::get('FMESL_GLOBAL_ICON', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_LAYOUT_THEME' => (int) Configuration::get('FMESL_LAYOUT_THEME', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'FMESL_MAP_LINK' => (int) Configuration::get('FMESL_MAP_LINK', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'fmm_sl_zoom' => (int) $def_zoom,
            'fmm_sl_pageheading' => Configuration::get('FMESL_PAGE_TITLE', $lang_id, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id),
            'img_store_dir' => _THEME_STORE_DIR_,
            'img_ps_dir' => $protocol_link . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
            'cookie' => $this->context->cookie,
            'api_key' => $api_key,
            'http' => $_http,
            'region' => Tools::substr($default_country->iso_code, 0, 2),
            'sl_url' => $this->context->link->getPageLink('search'),
            'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
            'base_dir_ssl' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
            'force_ssl' => $force_ssl,
            'iso_lang' => $this->context->language->iso_code,
        ));
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path . 'views/css/admin.css');
        $this->processStoreController();
    }

    /**
     * hookActionAdminControllerInitAfter - for PS 1.7.7.x
     * @return void
     */
    public function hookActionAdminControllerInitAfter()
    {
        $this->processStoreController();
    }

    public function hookActionAdminStoresControllerSaveAfter()
    {
        $id_store = (int) Tools::getValue('id_store');
        $linkRewrite = Tools::str2url(trim(Tools::getValue('link_rewrite')));
        $file = Tools::fileAttachment('img');

        if (isset($linkRewrite) && !Validate::isLinkRewrite($linkRewrite)) {
            $this->context->controller->errors[] = $this->l('Invalid Link Rewrite value.');
        }

        // pushing store link_rewrite into DB
        Locator::pushStoreContact($id_store, array('link_rewrite' => $linkRewrite));

        //Adding Store icon relative to each store
        if (!empty($file['name'])) {
            $file['type'] = explode('/', $file['mime']);
            $file['type'] = end($file['type']);
            if (ImageManager::validateUpload($file, Tools::convertBytes(ini_get('upload_max_filesize')))) {
                return $this->context->controller->warnings[] = $this->l('Icon image size exceeds limit in your PrestaShop settings');
            } else {
                ImageManager::resize($file['tmp_name'], _PS_IMG_DIR_ . 'st/icon_' . $id_store . '.png', 30, 30, 'png', false);
            }
        }
    }

    protected function processStoreController()
    {
        if ('AdminStores' == Dispatcher::getInstance()->getController()) {
            if (Tools::getIsset('required_fields_error')) {
                $this->context->controller->errors[] = $this->l('Cannot import csv data.Missing destination required(*) field(s)');
            } elseif (Tools::getIsset('info')) {
                $inserted = Tools::getValue('inserted');
                $skip = Tools::getValue('skip');
                $this->context->controller->informations[] = sprintf(
                    $this->l('CSV data imported. Inserted records : %d - Skipped/Invalid records : %d'),
                    $inserted,
                    $skip
                );
            } elseif (Tools::isSubmit('submitAddstore')) {
                $id_store = Tools::getValue('id_store');
                $relatedProducts = Tools::getValue('storeprods');

                if (isset($relatedProducts) && $relatedProducts) {
                    $relatedProducts = join(',', array_map('intval', $relatedProducts));
                }
                // pushing store products into DB
                Locator::pushStoreContact($id_store, array('related_products' => $relatedProducts));
            }
        }
    }

    public function hookModuleRoutes()
    {
        $url_rewrite_store = Meta::getMetaByPage('stores', $this->context->language->id);
        $url_rewrite_store = $url_rewrite_store['url_rewrite'];
        return array(
            'module-' . $this->name . '-articlesearch' => array(
                'controller' => 'articlesearch',
                'rule' => 'find-product',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
            'module-' . $this->name . '-storefinder' => array(
                'controller' => 'storefinder',
                'rule' => 'storefinder',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
            'module-' . $this->name . '-storeslip' => array(
                'controller' => 'storeslip',
                'rule' => 'pickup-slip',
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
            'module-' . $this->name . '-detail' => array(
                'controller' => 'storedetails',
                'rule' => $url_rewrite_store . '{/:id}-{rewrite}',
                'keywords' => array(
                    'id' => array('regexp' => '[0-9]+', 'param' => 'id'),
                    'rewrite' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'rewrite'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
        );
    }

/*    public function hookDisplayHeader()
    {
        $controller = Dispatcher::getInstance()->getController();
        $isSelection = (in_array($controller, array('order', 'order-opc', 'orderopc', 'checkout', 'cart')))? true : false;
        Media::addJsDef(array('is_store_selction' => $isSelection));
        if ($controller != 'storedetails') {
            $this->setMediaFiles();
            $this->getTemplateJsVariables();
            return $this->display($this->_path, 'views/templates/hook/js_variables.tpl');
        }
    }
	
	*/
	
public function hookDisplayHeader()
{
    $controller = Dispatcher::getInstance()->getController();

    $isSelection = in_array($controller, array('order', 'order-opc', 'orderopc', 'checkout', 'cart'));
    Media::addJsDef(array('is_store_selction' => $isSelection));

    // ✅ cortar SOLO Home
    if ($controller === 'index') {
        return;
    }

    $this->setMediaFiles();
    $this->getTemplateJsVariables();
    return $this->display($this->_path, 'views/templates/hook/js_variables.tpl');
}


    public function hookDisplayProductTab()
    {
        //For Left Column
        $this->context->smarty->assign(array(
            'FMESL_TAB' => Configuration::get(
                'FMESL_TAB',
                $this->context->language->id,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'FMESL_TABHEAD' => Configuration::get(
                'FMESL_TABHEAD',
                $this->context->language->id,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'FMESL_TABSTATE' => (int) Configuration::get(
                'FMESL_TABSTATE',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'PS_VERSION' => _PS_VERSION_,
        ));
        return $this->display(__FILE__, 'tabs.tpl');
    }

    public function hookDisplayProductTabContent()
    {
        $id_product = (int) Tools::getValue('id_product');
        $stores = $this->getProductStores($id_product);
        $this->context->smarty->assign(array(
            'stores' => $stores,
            'id_product' => $id_product,
            'FMESL_TABSTATE' => (int) Configuration::get(
                'FMESL_TABSTATE',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
        ));
        return $this->display(__FILE__, 'tabcontent.tpl');
    }

    public function hookDisplayLeftColumn()
    {
        if (Configuration::get('FMESL_LEFTCOLUMN_MAP', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id)) {
            $this->context->smarty->assign(
                'map_theme',
                $this->getMapStyles(Configuration::get(
                    'FMESL_LEFTCOLUMN_MAP_THEME',
                    null,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                ))
            );
            return $this->getHookMap('leftcolumn');
        }
    }

    public function hookDisplayRightColumn()
    {
        if (Configuration::get('FMESL_RIGHTCOLUMN_MAP', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id)) {
            $this->context->smarty->assign(
                'map_theme',
                $this->getMapStyles(Configuration::get(
                    'FMESL_RIGHTCOLUMN_MAP_THEME',
                    null,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                ))
            );
            return $this->getHookMap('rightcolumn');
        }
    }

    public function hookDisplayHome()
    {
        if (Configuration::get('FMESL_HOME_MAP', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id)) {
            $this->context->smarty->assign(
                'map_theme',
                $this->getMapStyles(Configuration::get(
                    'FMESL_HOME_MAP_THEME',
                    null,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                ))
            );
            return $this->getHookMap('home');
        }
    }

    public function hookDisplayStoreMap()
    {
        $this->context->smarty->assign(
            'map_theme',
            $this->getMapStyles(Configuration::get(
                'FMESL_HOME_MAP_THEME',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ))
        );
        return $this->getHookMap();
    }

    public function hookActionValidateOrder($params)
    {
        $loc = new Locator;
        $order = $params['order'];
        $isPickupAllowed = (bool) Configuration::get(
            'FMESL_PICKUP_STORE',
            null,
            $order->id_shop_group,
            $order->id_shop
        );

        $defaultCarrier = (int) Configuration::get(
            'FMESL_DEFAULT_CARRIER',
            null,
            $order->id_shop_group,
            $order->id_shop
        );

        if ($isPickupAllowed && $defaultCarrier == $order->id_carrier) {
            $data = array('id_order' => $order->id);
            $additionalData = $loc->getStoreByCart($order->id_cart);
            if (!$loc->getIdStoreByOrder($order->id)) {
                $data['id_store'] = (int) $this->getDefaultStore($order->id_cart);
            }
            $data['email_alert'] = $additionalData['email_alert'];
            $data['pickup_date'] = $additionalData['pickup_date'];
            $data['id_carrier'] = $order->id_carrier;
            
            $loc->updateStoreByCart($order->id_cart, $data);
        }
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $order = new Order($params['id_order']);
        $rederenceOrders = Order::getByReference($order->reference);
        $isPickupAllowed = (bool) Configuration::get(
            'FMESL_PICKUP_STORE',
            null,
            $order->id_shop_group,
            $order->id_shop
        );

        $defaultCarrier = (int) Configuration::get(
            'FMESL_DEFAULT_CARRIER',
            null,
            $order->id_shop_group,
            $order->id_shop
        );

        if ($isPickupAllowed) {
            foreach ($rederenceOrders->getResults() as $order) {
                if ($defaultCarrier == $order->id_carrier && ($id_store = Locator::getIdStoreByOrder($order->id))) {
                    if (($id_address = Locator::getStoreAddressId($id_store))) {
                        $order->id_address_delivery = $id_address;
                        $order->save();
                        Locator::updateCustomizationAddress(
                            $order->id_cart,
                            array('id_address_delivery' => (int) $id_address)
                        );
                    }
                }
            }
        }
    }

    public function hookDisplayPDFDeliverySlip($params)
    {
        if ($params['object']->id_order && Validate::isLoadedObject($order = new Order($params['object']->id_order))) {
            $pickupData = Locator::getStoreByOrder((int) $order->id, $order->id_lang);
            if (isset($pickupData) && $pickupData) {
                $this->context->smarty->assign(array(
                    'pickup_data' => $pickupData,
                ));
                return $this->display(dirname(__FILE__), 'views/templates/admin/pdf/store-pickup.tpl');
            }
        }
    }

    public function hookDisplayOverrideTemplate($params)
    {
        $controller = Dispatcher::getInstance()->getController();
        if ('stores' === $controller) {
            Media::addJsDef(array('map_theme' => $this->getMapStyles(Configuration::get('FMESL_MAIN_MAP_THEME'))));
            $layout_theme = (int) Configuration::get(
                'FMESL_LAYOUT_THEME',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            );

            if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                if ($layout_theme > 0) {
                    return 'module:storelocator/views/templates/front/stores_splittheme_17.tpl';
                } else {
                    return 'module:storelocator/views/templates/front/stores_17.tpl';
                }
            } else {
                if ($layout_theme > 0) {
                    return dirname(__FILE__) . '/views/templates/front/stores_splittheme.tpl';
                } else {
                    return dirname(__FILE__) . '/views/templates/front/stores.tpl';
                }
            }
        }
    }

    public function hookDisplayFooterProduct()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $lang_id = $this->context->language->id;
            $id_product = (int) Tools::getValue('id_product');
            $stores = $this->getProductStores($id_product);
            $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $this->context->smarty->assign(array(
                'stores' => $stores,
                'id_product' => $id_product,
                'FMESL_TABSTATE' => (int) Configuration::get('FMESL_TABSTATE'),
                'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
                'base_dir_ssl' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
                'force_ssl' => $force_ssl,
                'default_store' => (int) $this->getDefaultStore(),
                'FMESL_TABHEAD' => Configuration::get(
                    'FMESL_TAB',
                    $lang_id,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                ),
                'FMESL_TABHEAD_SUB' => Configuration::get(
                    'FMESL_TABHEAD',
                    $lang_id,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                ),
                'default_carrier' => (int) Configuration::get(
                    'FMESL_DEFAULT_CARRIER',
                    null,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                ),
            ));
            return $this->display(__FILE__, 'product_stores.tpl');
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        $id_order = $params['id_order'];
        if ($id_order && Validate::isLoadedObject($order = new Order($id_order))) {
            $pickupData = Locator::getStoreByOrder((int) $order->id, $order->id_lang);
            if (isset($pickupData) && $pickupData) {
                $linkPdf = $this->context->link->getAdminLink('AdminPdf') . '&' . http_build_query(array(
                    'submitAction' => 'generateDeliverySlipPDF',
                    'id_order' => (int) $order->id,
                ));
                $this->context->smarty->assign(array(
                    'storeOrder' => $order,
                    'linkPdf' => $linkPdf,
                    'pickup_data' => $pickupData,
                ));
                return $this->display(dirname(__FILE__), 'views/templates/admin/store-pickup-details.tpl');
            }
        }
    }

    public function hookDisplayOrderDetail($params)
    {
        $order = $params['order'];
        if (Validate::isLoadedObject($order)) {
            $pickupData = Locator::getStoreByOrder((int) $order->id, $order->id_lang);
            if (isset($pickupData) && $pickupData) {
                $linkPdf = $this->context->link->getModuleLink(
                    $this->name,
                    'storeslip',
                    array(
                        'submitAction' => 'generatePickupSlipPDF',
                        'id_order' => (int) $order->id,
                    )
                );
                $this->context->smarty->assign(array(
                    'storeOrder' => $order,
                    'linkPdf' => $linkPdf,
                    'pickup_data' => $pickupData,
                ));
                return $this->display(dirname(__FILE__), 'views/templates/admin/store-pickup-details.tpl');
            }
        }
    }

    public function hookActionAdminStoresListingFieldsModifier()
    {
        $this->context->controller->page_header_toolbar_btn = array(
            'export' => array(
            'href' => $this->context->link->getAdminLink('AdminStores') . '&exportstore&token=' . Tools::getAdminTokenLite('AdminStores'),
            'desc' => $this->l('Export'),
        ),
            'import' => array(
            'href' => $this->getStoreLocatorLink() . '&import_contacts',
            'desc' => $this->l('Import'),
            ),
        );

        $this->context->smarty->assign(array(
            'toolbar_btn' => $this->context->controller->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->context->controller->page_header_toolbar_btn,
        ));
    }

    public function hookActionAdminStoresFormModifier($params)
    {
        $hasImage = false;
        $storeIcon = false;
        $id_store = (int) Tools::getValue('id_store');

        $inputSlice = array();
        $middleSlice = array(
            array(
                'type' => 'text',
                'label' => $this->l('Link Rewrite'),
                'name' => 'link_rewrite',
                'lang' => false,
                'size' => 33,
                'required' => false,
                'hint' => sprintf($this->l('Allowed characters: letters, numbers and %s'), '-'),
                'desc' => $this->l('Store link for each page without any space. For example') . '<span style="color:blue;"> dade-county</span>',
            ));

        foreach ($params['fields'][0]['form']['input'] as $index => $input) {
            // pushing link_rewrite after name
            if ('name' == $input['name']) { // || preg_match('/name_/i', $input['name'])) {
                // slicing form array
                $topSlice = array_slice($params['fields'][0]['form']['input'], 0, $index + 1);
                $bottomSlice = array_slice($params['fields'][0]['form']['input'], $index + 1, count($params['fields'][0]['form']['input']) - 1);

                // merging all slices
                $inputSlice = array_merge($topSlice, $middleSlice, $bottomSlice);

                $params['fields'][0]['form']['input'] = $inputSlice;
            }

            if ('image' == $input['name']) {
                $hasImage = true;
            }

            if ('img' == $input['name']) {
                $storeIcon = true;
            }
        }

        $params['fields_value']['ps_version'] = (Tools::version_compare(_PS_VERSION_, '1.7.3.0', '>=') == true) ? true : false;

        // pushing store products field to form fields
        $params['fields'][0]['form']['input'][] = array(
            'type' => 'storeproducts',
            'label' => $this->l('Select Products:'),
            'name' => 'storeprods[]',
            'values' => Locator::getAllProds(),
            'title' => $this->l('Select Products:'),
            'desc' => $this->l('Please select products. Use Browser search functionality to search for product - Ctrl+F'),
        );

        // setting store products field value
        $relatedStoreProds = Locator::getAllRelatedProds($id_store);
        if (isset($relatedStoreProds) && $relatedStoreProds) {
            foreach ($relatedStoreProds as $value) {
                $params['fields_value'][$value] = true;
            }
        }

        // setting link_rewrite field value
        $params['fields_value']['link_rewrite'] = Locator::getFieldMissingValue($id_store, 'link_rewrite');

        // pushing store icon field to form fields
        if (!$storeIcon) {
            $icon_image_url = _PS_IMG_DIR_ . 'st/icon_' . $id_store . '.png';
            $icon_image_url = ImageManager::thumbnail($icon_image_url, 'icon_' . $id_store . '.png', 30, 'png', true, true);
            $params['fields'][0]['form']['input'][] = array(
                'type' => 'file',
                'label' => $this->l('Store Map Icon'),
                'name' => 'img',
                'display_image' => true,
                'image' => $icon_image_url ? $icon_image_url : false,
                'size' => 300,
                'hint' => $this->l('Upload an Image type PNG from your computer.'),
                'desc' => '<span style="color:#3586ae">' . $this->l('Note: ') . '</span>' . $this->l('Please see module configuration page to enable this icon.'),
            );
        }

        // pushing store image field to form fields
        if (!$hasImage) {
            $image = _PS_STORE_IMG_DIR_ . $id_store . '.' . $this->context->controller->imageType;
            $image_url = ImageManager::thumbnail(
                $image,
                'store_' . (int) $id_store . '.' . $this->context->controller->imageType,
                350,
                $this->context->controller->imageType,
                true,
                true
            );
            $image_size = file_exists($image) ? filesize($image) / 1000 : false;
            $params['fields'][0]['form']['input'][] = array(
                'type' => 'file',
                'label' => $this->l('Picture'),
                'name' => 'image',
                'display_image' => true,
                'image' => $image_url ? $image_url : false,
                'size' => $image_size,
                'hint' => $this->l('Storefront picture.'),
            );
        }
    }

    public function getHookMap($hook_type = 'home', $show_cal = false)
    {
        if (!extension_loaded('Dom')) {
            return $this->errors[] = $this->l('PHP "Dom" extension has not been loaded.');
        }

        if (Configuration::get('PS_STORES_SIMPLIFIED')) {
            $this->assignStoresSimplified();
        } else {
            $this->assignStores();
        }

        $this->context->smarty->assign(array(
            'show_cal' => $show_cal,
            'hook_type' => $hook_type,
            'selectedpickupTime' => $this->getPickupTime(),
            'selectedpickupDate' => $this->getPickupDate(),
            'default_store' => (int) $this->getDefaultStore(),
            'FMESL_SBP' => (int) Configuration::get(
                'FMESL_SBP',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'pickupTime' => (int) Configuration::get(
                'FMESL_PICKUP_TIME',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'pickupEnabled' => (int) Configuration::get(
                'FMESL_PICKUP_STORE',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'pickupDate' => (int) Configuration::get(
                'FMESL_PICKUP_DATE',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'default_carrier' => (int) Configuration::get(
                'FMESL_DEFAULT_CARRIER',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
        ));
        return $this->display($this->_path, 'views/templates/hook/stores.tpl');
    }

    public function getProductStores($id_product)
    {
        $id_product = ($id_product) ? $id_product : (int) Tools::getValue('id_product');
        return Locator::getStoresByProduct($id_product);
    }

    public function renderStoreWorkingHours($store)
    {
        $days = array();
        $days[1] = $this->l('Monday');
        $days[2] = $this->l('Tuesday');
        $days[3] = $this->l('Wednesday');
        $days[4] = $this->l('Thursday');
        $days[5] = $this->l('Friday');
        $days[6] = $this->l('Saturday');
        $days[7] = $this->l('Sunday');
        $days_datas = array();
        $hours = array();
        if ($store['hours'] && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) {
            $hours = Tools::unSerialize($store['hours']);
            if (is_array($hours)) {
                $hours = array_filter($hours);
            }
        } elseif ($store['hours'] && Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            // unicode lang character(s) issue - fix
            $hours = json_decode($store['hours'], true);
            if (is_array($hours)) {
                foreach ($hours as &$h) {
                    $h = array_shift($h);
                }
            }
        }

        if (!empty($hours)) {
            for ($i = 1; $i < 8; $i++) {
                if (isset($hours[(int) $i - 1])) {
                    $hours_datas = array();
                    $hours_datas['hours'] = $hours[(int) $i - 1];
                    $hours_datas['day'] = $days[$i];
                    $days_datas[] = $hours_datas;
                }
            }
            $version_check = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
            $this->context->smarty->assign('days_datas', $days_datas);
            $this->context->smarty->assign('id_country', $store['id_country']);
            $this->context->smarty->assign('ver_ps', (int) $version_check);
            return $this->context->smarty->fetch($this->local_path . 'views/templates/front/store_infos.tpl');
        }
        return false;
    }

    public function processStoreAddress($store)
    {
        $ignore_field = array(
            'firstname',
            'lastname',
        );

        $out_datas = array();

        $address_datas = AddressFormat::getOrderedAddressFields($store['id_country'], false, true);
        $state = (isset($store['id_state'])) ? new State($store['id_state']) : null;

        foreach ($address_datas as $data_line) {
            $data_fields = explode(' ', $data_line);
            $addr_out = array();

            $data_fields_mod = false;
            foreach ($data_fields as $field_item) {
                $field_item = trim($field_item);
                if (!in_array($field_item, $ignore_field) && !empty($store[$field_item])) {
                    $addr_out[] = ($field_item == 'city' && $state && isset($state->iso_code) && Tools::strlen($state->iso_code)) ?
                    $store[$field_item] . ', ' . $state->iso_code : $store[$field_item];
                    $data_fields_mod = true;
                }
            }
            if ($data_fields_mod) {
                $out_datas[] = implode(' ', $addr_out);
            }
        }

        $out = implode('<br />', $out_datas);
        return $out;
    }

    public function processStoreAddressShort($store)
    {
        $ignore_field = array(
            'firstname',
            'lastname',
            'phone',
            'fax',
            'email',
            'note',
        );

        $out_datas = array();

        $address_datas = AddressFormat::getOrderedAddressFields($store['id_country'], false, true);
        $state = (isset($store['id_state'])) ? new State($store['id_state']) : null;

        foreach ($address_datas as $data_line) {
            $data_fields = explode(' ', $data_line);
            $addr_out = array();

            $data_fields_mod = false;
            foreach ($data_fields as $field_item) {
                $field_item = trim($field_item);
                if (!in_array($field_item, $ignore_field) && !empty($store[$field_item])) {
                    $addr_out[] = ($field_item == 'city' && $state && isset($state->iso_code) && Tools::strlen($state->iso_code)) ?
                    ', ' . $store[$field_item] . ', ' . $state->iso_code : $store[$field_item];
                    $data_fields_mod = true;
                }
            }
            if ($data_fields_mod) {
                $out_datas[] = implode(' ', $addr_out);
            }
        }

        $out = implode('<br />', $out_datas);
        return $out;
    }

    public function getStores()
    {
        $all = (int) Tools::getValue('all', 1);
        $distance = (int) Tools::getValue('radius', 100);

        $stores = Locator::getStores($distance, $all);
        if (isset($stores) && $stores) {
            foreach ($stores as &$store) {
                $params = array('id' => $store['id_store'], 'rewrite' => $store['link_rewrite']);
                $store['link_rewrite'] = $this->context->link->getModuleLink($this->name, 'detail', $params);
            }
        }
        return $stores;
    }

    public function assignStoresSimplified()
    {
        $stores = Locator::loadAllStores();
        if (isset($stores) && $stores) {
            foreach ($stores as &$store) {
                $store['has_picture'] = file_exists(_PS_STORE_IMG_DIR_ . (int) ($store['id_store']) . '.jpg');
                if ($working_hours = $this->renderStoreWorkingHours($store)) {
                    $store['working_hours'] = $working_hours;
                }
            }
        }

        $this->context->smarty->assign(array(
            'simplifiedStoresDiplay' => true,
            'stores' => $stores,
            'default_store' => (int) $this->getDefaultStore(),
            'default_carrier' => (int) Configuration::get(
                'FMESL_DEFAULT_CARRIER',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
        ));
    }

    public function assignStores()
    {
        $distanceUnit = Configuration::get('PS_DISTANCE_UNIT');
        if (!in_array($distanceUnit, array('km', 'mi'))) {
            $distanceUnit = 'km';
        }

        $this->context->smarty->assign(array(
            'distance_unit' => $distanceUnit,
            'simplifiedStoresDiplay' => false,
            'stores' => $this->getStores(),
            'default_store' => (int) $this->getDefaultStore(),
            'FMESL_LAYOUT' => (int) Configuration::get(
                'FMESL_LAYOUT',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'default_carrier' => (int) Configuration::get(
                'FMESL_DEFAULT_CARRIER',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            ),
            'hasStoreIcon' => file_exists(_PS_IMG_DIR_ . Configuration::get(
                'PS_STORES_ICON',
                null,
                Context::getContext()->shop->id_shop_group,
                Context::getContext()->shop->id
            )),
        ));
    }

    public function loadAllStores()
    {
        return Locator::loadAllStores();
    }

    public function getMapStyles($theme = 'FMESL_MAP_STYLE_DEFAULT')
    {
        $mapThemes = array(
            'FMESL_MAP_STYLE_DEFAULT' => '[{"featureType":"administrative.country","elementType":"geometry.fill","stylers":[{"saturation":"-35"}]}]',
            'FMESL_MAP_STYLE_COBALT' => '[{"featureType":"all","elementType":"all","stylers":[{"invert_lightness":true},{"saturation":10},{"lightness":30},{"gamma":0.5},{"hue":"#435158"}]}]',
            'FMESL_MAP_STYLE_BROWNS' => '[{"elementType":"geometry","stylers":[{"hue":"#ff4400"},{"saturation":-68},{"lightness":-4},{"gamma":0.72}]},{"featureType":"road","elementType":"labels.icon"},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"hue":"#0077ff"},{"gamma":3.1}]},{"featureType":"water","stylers":[{"hue":"#00ccff"},{"gamma":0.44},{"saturation":-33}]},{"featureType":"poi.park","stylers":[{"hue":"#44ff00"},{"saturation":-23}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"hue":"#007fff"},{"gamma":0.77},{"saturation":65},{"lightness":99}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"gamma":0.11},{"weight":5.6},{"saturation":99},{"hue":"#0091ff"},{"lightness":-86}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"lightness":-48},{"hue":"#ff5e00"},{"gamma":1.2},{"saturation":-23}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"saturation":-64},{"hue":"#ff9100"},{"lightness":16},{"gamma":0.47},{"weight":2.7}]}]',
            'FMESL_MAP_STYLE_MIDNIGHT' => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"lightness":13}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#144b53"},{"lightness":14},{"weight":1.4}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#08304b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#0c4152"},{"lightness":5}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#0b434f"},{"lightness":25}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#0b3d51"},{"lightness":16}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"transit","elementType":"all","stylers":[{"color":"#146474"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#021019"}]}]',
            'FMESL_MAP_STYLE_GREYSCALE' => '[{"featureType":"all","elementType":"geometry.fill","stylers":[{"weight":"2.00"}]},{"featureType":"all","elementType":"geometry.stroke","stylers":[{"color":"#9c9c9c"}]},{"featureType":"all","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#eeeeee"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#7b7b7b"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#c8d7d4"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#070707"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]}]',
            'FMESL_MAP_STYLE_NIGHTMODE' => '[{"elementType": "geometry", "stylers": [{"color": "#242f3e"}]},{"elementType": "labels.text.stroke", "stylers": [{"color": "#242f3e"}]},{"elementType": "labels.text.fill", "stylers": [{"color": "#746855"}]},{"featureType": "administrative.locality","elementType": "labels.text.fill","stylers": [{"color": "#d59563"}]},{"featureType": "poi","elementType": "labels.text.fill","stylers": [{"color": "#d59563"}]},{"featureType": "poi.park","elementType": "geometry","stylers": [{"color": "#263c3f"}]},{"featureType": "poi.park","elementType": "labels.text.fill","stylers": [{"color": "#6b9a76"}]},{"featureType": "road","elementType": "geometry","stylers": [{"color": "#38414e"}]},{"featureType": "road","elementType": "geometry.stroke","stylers": [{"color": "#212a37"}]},{"featureType": "road","elementType": "labels.text.fill","stylers": [{"color": "#9ca5b3"}]},{"featureType": "road.highway","elementType": "geometry","stylers": [{"color": "#746855"}]},{"featureType": "road.highway","elementType": "geometry.stroke","stylers": [{"color": "#1f2835"}]},{"featureType": "road.highway","elementType": "labels.text.fill","stylers": [{"color": "#f3d19c"}]},{"featureType": "transit","elementType": "geometry","stylers": [{"color": "#2f3948"}]},{"featureType": "transit.station","elementType": "labels.text.fill","stylers": [{"color": "#d59563"}]},{"featureType": "water","elementType": "geometry","stylers": [{"color": "#17263c"}]},{"featureType": "water","elementType": "labels.text.fill","stylers": [{"color": "#515c6d"}]},{"featureType": "water","elementType": "labels.text.stroke","stylers": [{"color": "#17263c"}]}]',
            'FMESL_MAP_STYLE_SKETCH' => '[{"featureType":"all","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"gamma":0.01},{"lightness":20}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"saturation":-31},{"lightness":-33},{"weight":2},{"gamma":0.8}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#050505"}]},{"featureType":"administrative.locality","elementType":"labels.text.stroke","stylers":[{"color":"#fef3f3"},{"weight":"3.01"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"color":"#0a0a0a"},{"visibility":"off"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.stroke","stylers":[{"color":"#fffbfb"},{"weight":"3.01"},{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"lightness":30},{"saturation":30}]},{"featureType":"poi","elementType":"geometry","stylers":[{"saturation":20}]},{"featureType":"poi.attraction","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"lightness":20},{"saturation":-20}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":10},{"saturation":-30}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"saturation":25},{"lightness":25}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#a1a1a1"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#292929"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#202020"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"labels.icon","stylers":[{"visibility":"simplified"},{"hue":"#0006ff"},{"saturation":"-100"},{"lightness":"13"},{"gamma":"0.00"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#686868"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"visibility":"off"},{"color":"#8d8d8d"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#353535"},{"lightness":"6"}]},{"featureType":"road.arterial","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"weight":"3.45"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"#d0d0d0"}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"lightness":"2"},{"visibility":"on"},{"color":"#999898"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#383838"}]},{"featureType":"road.local","elementType":"labels.text.stroke","stylers":[{"color":"#faf8f8"}]},{"featureType":"water","elementType":"all","stylers":[{"lightness":-20}]}]',
            'FMESL_MAP_STYLE_YELLOW' => '[{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#0096aa"},{"weight":"0.30"},{"saturation":"-75"},{"lightness":"5"},{"gamma":"1"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#0096aa"},{"saturation":"-75"},{"lightness":"5"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"color":"#ffe146"},{"visibility":"on"},{"weight":"6"},{"saturation":"-28"},{"lightness":"0"}]},{"featureType":"administrative","elementType":"labels.icon","stylers":[{"visibility":"on"},{"color":"#e6007e"},{"weight":"1"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#ffe146"},{"saturation":"-28"},{"lightness":"0"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"color":"#0096aa"},{"visibility":"simplified"},{"saturation":"-75"},{"lightness":"5"},{"gamma":"1"}]},{"featureType":"road","elementType":"labels.text","stylers":[{"visibility":"on"},{"color":"#ffe146"},{"weight":8},{"saturation":"-28"},{"lightness":"0"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"visibility":"on"},{"color":"#0096aa"},{"weight":8},{"lightness":"5"},{"gamma":"1"},{"saturation":"-75"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"simplified"},{"color":"#0096aa"},{"saturation":"-75"},{"lightness":"5"},{"gamma":"1"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#0096aa"},{"saturation":"-75"},{"lightness":"5"},{"gamma":"1"}]},{"featureType":"water","elementType":"labels.text","stylers":[{"visibility":"simplified"},{"color":"#ffe146"},{"saturation":"-28"},{"lightness":"0"}]},{"featureType":"water","elementType":"labels.icon","stylers":[{"visibility":"off"}]}]',
        );
        return $mapThemes[$theme];
    }

    public function getStore($id)
    {
        $id_lang = (int) $this->context->language->id;
        $stores = Locator::getStoreById($id, $id_lang);
        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        if (isset($stores) && $stores) {
            foreach ($stores as &$store) {
                $store['has_picture'] = file_exists(_PS_STORE_IMG_DIR_ . (int) ($store['id_store']) . '.jpg');
                $store['picture'] = $protocol_link . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_ . 'st/' . (int) $store['id_store'] . '.jpg';
                $store['hours'] = Locator::getMissingStoreField($store['id_store'], 'hours', $id_lang);
                if ($working_hours = $this->renderStoreWorkingHours($store)) {
                    $store['working_hours'] = $working_hours;
                }
                $store['name'] = Locator::getMissingStoreField($store['id_store'], 'name', $id_lang);
                $store['address1'] = Locator::getMissingStoreField($store['id_store'], 'address1', $id_lang);
                $store['address2'] = Locator::getMissingStoreField($store['id_store'], 'address2', $id_lang);
                $store['note'] = Locator::getMissingStoreField($store['id_store'], 'note', $id_lang);
                $store['address'] = Tools::safeOutput($this->processStoreAddressShort($store), false);
            }
            $stores = end($stores);
        }
        return $stores;
    }

    public function getAllStores($id_product)
    {
        return Locator::getAllStores($id_product);
    }

    public function installTab($controllerClassName, $tabName, $icon = null, $tabParentControllerName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $controllerClassName;
        $tab->name = array();

        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') && !empty($icon)) {
            $tab->icon = $icon;
        }

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        $tab->id_parent = $tabParentControllerName ? (int) Tab::getIdFromClassName($tabParentControllerName) : 0;
        $tab->module = $this->name;
        return (bool) $tab->add();
    }

    public function uninstallTab()
    {
        $tabs = array(
            'AdminStoreLocator',
            'AdminStoreSettings',
            'AdminStoreLocatorParent',
            'AdminStoreSlip',
        );

        $res = true;
        foreach ($tabs as $tabClass) {
            $tab = Tab::getInstanceFromClassName($tabClass);
            $res &= $tab->delete();
        }
        return $res;
    }

    protected function getMapThemes()
    {
        return array(
            array(
                'id' => 'FMESL_MAP_STYLE_DEFAULT',
                'name' => $this->l('Default'),
            ),
            array(
                'id' => 'FMESL_MAP_STYLE_BROWNS',
                'name' => $this->l('Browns'),
            ),
            array(
                'id' => 'FMESL_MAP_STYLE_COBALT',
                'name' => $this->l('Cobalt'),
            ),
            array(
                'id' => 'FMESL_MAP_STYLE_GREYSCALE',
                'name' => $this->l('Greyscale'),
            ),
            array(
                'id' => 'FMESL_MAP_STYLE_MIDNIGHT',
                'name' => $this->l('Midnight'),
            ),
            array(
                'id' => 'FMESL_MAP_STYLE_NIGHTMODE',
                'name' => $this->l('Nightmode'),
            ),
            array(
                'id' => 'FMESL_MAP_STYLE_SKETCH',
                'name' => $this->l('Sketch'),
            ),
            array(
                'id' => 'FMESL_MAP_STYLE_YELLOW',
                'name' => $this->l('Yellow'),
            ),
        );
    }

    protected function getTranslateableFields()
    {
        return array(
            'monday' => $this->l('Monday'),
            'tuesday' => $this->l('Tuesday'),
            'wednesday' => $this->l('Wednesday'),
            'thursday' => $this->l('Thursday'),
            'friday' => $this->l('Friday'),
            'saturday' => $this->l('Saturday'),
            'sunday' => $this->l('Sunday'),
            'placeholder_label' => $this->l('Start typing here'),
            'invalid_request' => $this->l('Invalid Request.You cannot directly access this page.'),
            'store_selection_success' => $this->l('Store selection is saved successfully.'),
            'store_inactive' => $this->l('Your selected store is not available. Please select another store.'),
            'invalid_pickup_date' => $this->l('Pickup date is invalid.'),
            'saved_pickup_date' => $this->l('Pickup date is saved successfully.'),
            'saved_pickup_date_error' => $this->l('Unfortunatley, we encountered an error while saving data.'),
        );
    }

    protected function getStoreFields()
    {
        return array(
            'id_store' => $this->l('ID'),
            'name' => $this->l('Name') . '*',
            'address1' => $this->l('Address') . '*',
            'city' => $this->l('City') . '*',
            'postcode' => $this->l('Zip code') . '*',
            'id_state' => $this->l('ID State'),
            'id_country' => $this->l('ID Country'),
            'phone' => $this->l('Phone'),
            'fax' => $this->l('Fax'),
            'active' => $this->l('Enabled'),
            'latitude' => $this->l('Latitude'),
            'longitude' => $this->l('Longitude'),
        );
    }

    public function getStoreLocatorLink()
    {
        return $this->context->link->getAdminLink('AdminModules') . '&' . http_build_query(array(
            'configure' => $this->name,
            'tab_module' => $this->tab,
            'module_name' => $this->name,
        ));
    }

    public function createTables()
    {
        $result = true;
        foreach (array('storelocator_address', 'storelocator_cart') as $table) {
            if (false === Locator::tableExists($table)) {
                $result &= Locator::createTable($table);
            }
        }
        return $result;
    }

    public function getDefaultStore($id_cart = null)
    {
        if (!$id_cart) {
            $id_cart = (int) Context::getContext()->cart->id;
        }

        $id_store = (int) Configuration::get(
            'FMESL_DEFAULT_STORE',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        if ($id_cart) {
            $preselectedStore = Locator::getStoreByCart($id_cart);
            if (isset($preselectedStore) &&
                $preselectedStore &&
                isset($preselectedStore['id_store']) &&
                $preselectedStore['id_store']) {
                $id_store = (int) $preselectedStore['id_store'];
            }
        }
        return $id_store;
    }

    public function getPickupDate()
    {
        $pickupDate = null;
        if (isset(Context::getContext()->cart)) {
            $preselectedStore = Locator::getStoreByCart(Context::getContext()->cart->id);
            if (isset($preselectedStore) && $preselectedStore) {
                if (isset($preselectedStore['pickup_date']) && false !== strtotime($preselectedStore['pickup_date'])) {
                    $pickupDate = date('Y-m-d', strtotime($preselectedStore['pickup_date']));
                }
            }
        }
        return $pickupDate;
    }

    public function getPickupTime()
    {
        $pickupTime = null;
        if (isset(Context::getContext()->cart)) {
            $preselectedStore = Locator::getStoreByCart(Context::getContext()->cart->id);
            if (isset($preselectedStore) && $preselectedStore) {
                if (isset($preselectedStore['pickup_date']) && false !== strtotime($preselectedStore['pickup_date'])) {
                    $pickupTime = date('H:i', strtotime($preselectedStore['pickup_date']));
                }
            }
        }
        return $pickupTime;
    }

    protected function getWeekDays()
    {
        return array(
            'monday' => $this->l('Monday'),
            'tuesday' => $this->l('Tuesday'),
            'wednesday' => $this->l('Wednesday'),
            'thursday' => $this->l('Thursday'),
            'friday' => $this->l('Friday'),
            'saturday' => $this->l('Saturday'),
            'sunday' => $this->l('Sunday'),
        );
    }

    public function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        if (true === Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            @mkdir(_PS_OVERRIDE_DIR_.'controllers');
            @mkdir(_PS_OVERRIDE_DIR_.'controllers/admin');
            @mkdir(_PS_OVERRIDE_DIR_.'controllers/admin/templates');
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    Tools::copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }

    public function removeStoreAddresses()
    {
        if (($addresses = Locator::getAllStoreAddresses())) {
            foreach ($addresses as $id_address) {
                if ($id_address && Validate::isLoadedObject($address = new Address((int) $id_address))) {
                    $address->delete();
                }
            }
        }
        return true;
    }

    public static function deleteDir($dirname)
    {
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }

        while ($file = readdir($dir_handle)) {
            if ($file != '.' && $file != '..') {
                if (!is_dir($dirname . DIRECTORY_SEPARATOR . $file)) {
                    @unlink($dirname . DIRECTORY_SEPARATOR . $file);
                } else {
                    self::deleteDir($dirname . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir_handle);
        @rmdir($dirname);
        return true;
    }
}
