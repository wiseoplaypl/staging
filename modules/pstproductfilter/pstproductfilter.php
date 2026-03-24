<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2020 Presta.Site
 * @license   LICENSE.txt
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pstproductfilter/classes/PstProductSearch.php';
require_once _PS_MODULE_DIR_ . 'pstproductfilter/classes/PstProductColumn.php';
require_once _PS_MODULE_DIR_ . 'pstproductfilter/classes/PstProductFilterSet.php';
if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
    require_once _PS_MODULE_DIR_ . 'pstproductfilter/vendor/autoload.php';
}

class PstProductFilter extends Module
{
    protected $html;
    public $settings_prefix = 'PSTPF_';

    public $custom_columns;
    public $disable_auto_custom_columns;

    protected static $cache_filters_raw_data = [];
    protected static $cache_columns_raw_data = [];

    protected $filter_values_to_save = [];

    public function __construct()
    {
        $this->name = 'pstproductfilter';
        $this->tab = 'administration';
        $this->version = '1.3.6';
        $this->ps_versions_compliancy = ['min' => '1.7.5.0', 'max' => _PS_VERSION_];
        $this->author = 'PrestaSite';
        $this->bootstrap = true;
        $this->module_key = '5983bc0b09a3bad2fcb3c00d2af7ea05';

        parent::__construct();
        $this->loadSettings();

        $this->displayName = $this->l('Admin Product Filter and Extra Columns');
        $this->description = $this->l('Fast and flexible ajax filter for products in Back Office.');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Register hooks
        $this->installHooks();

        // Create tables
        $this->installDB();

        // default values:
        $this->installDefaultSettings();

        // Generate settings
        $this->loadSettings();

        return true;
    }

    public function installHooks()
    {
        $hooks = [
            'displayBackOfficeHeader',
            'actionAdminControllerSetMedia',
            'actionAdminProductsListingFieldsModifier',
            'actionAdminProductsListingResultsModifier',
            'pstProductFilter',
            'actionDispatcherAfter',
            'actionProductSave',
            'displayDashboardToolbarIcons',
            'displayAdminAfterHeader',
            'actionProductGridQueryBuilderModifier',
            'actionProductGridDefinitionModifier',
            'actionProductGridDataModifier',
        ];

        foreach ($hooks as $hook) {
            $this->registerHook($hook);
        }

        return true;
    }

    public function installDB()
    {
        $install_queries = $this->getDbTables();
        foreach ($install_queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    protected function getDbTables()
    {
        return [
            'pstproductfilter' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pstproductfilter` (
                `filter` VARCHAR(255),
                `active` TINYINT(1) DEFAULT 1,
                `position` INT(6) NOT NULL,
                `id_employee` INT(11) NOT NULL,
                `value` TEXT,
                `strict` TINYINT(1) DEFAULT 0,
                UNIQUE (`filter`, `id_employee`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'pstproductfilter_column' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pstproductfilter_column` (
                `column` VARCHAR(255),
                `active` TINYINT(1) DEFAULT 0,
                `position` INT(6) NOT NULL,
                `id_employee` INT(11) NOT NULL,
                UNIQUE (`column`, `id_employee`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'pstproductfilter_set' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pstproductfilter_set` (
                `id_pstproductfilter_set` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255),
                `order_by` VARCHAR(65),
                `order_way` VARCHAR(65),
                `admin_filter` LONGTEXT,
                PRIMARY KEY (`id_pstproductfilter_set`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'pstproductfilter_set_filter' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pstproductfilter_set_filter` (
                `id_pstproductfilter_set_filter` INT(11) NOT NULL AUTO_INCREMENT,
                `id_pstproductfilter_set` INT(11) NOT NULL,
                `filter` VARCHAR(255),
                `active` TINYINT(1) DEFAULT 0,
                `position` INT(6) NOT NULL,
                `value` TEXT,
                `strict` TINYINT(1) DEFAULT 0,
                PRIMARY KEY (`id_pstproductfilter_set_filter`),
                UNIQUE (`id_pstproductfilter_set`, `filter`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
            'pstproductfilter_set_column' => 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'pstproductfilter_set_column` (
                `id_pstproductfilter_set_column` INT(11) NOT NULL AUTO_INCREMENT,
                `id_pstproductfilter_set` INT(11) NOT NULL,
                `column` VARCHAR(255),
                `active` TINYINT(1) DEFAULT 0,
                `position` INT(6) NOT NULL,
                PRIMARY KEY (`id_pstproductfilter_set_column`),
                UNIQUE (`id_pstproductfilter_set`, `column`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        ];
    }

    protected function installDefaultSettings()
    {
        foreach ($this->getSettings() as $item) {
            if ($item['type'] == 'html') {
                continue;
            }
            $item_name = Tools::strtoupper($item['name']);
            if (isset($item['default']) && (Configuration::get($this->settings_prefix . $item_name) === false)) {
                if (isset($item['lang']) && $item['lang']) {
                    $lang_value = [];
                    $set = false;
                    foreach (Language::getLanguages() as $lang) {
                        $lang_value[$lang['id_lang']] = $item['default'];
                        if (Configuration::get($this->settings_prefix . $item_name, $lang['id_lang']) !== false) {
                            $set = true;
                        }
                    }
                    if (!$set && sizeof($lang_value)) {
                        Configuration::updateValue($this->settings_prefix . $item_name, $lang_value, true);
                    }
                } else {
                    Configuration::updateValue($this->settings_prefix . $item_name, $item['default']);
                }
            }
        }
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        // drop tables
        foreach ($this->getDbTables() as $table_name => $query) {
            Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . pSQL($table_name) . '`;');
        }
        // reset settings
        Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` LIKE "' . pSQL($this->settings_prefix) . '%";'
        );

        return true;
    }

    public function getContent()
    {
        $this->html = '';
        // check if the module is enabled
        if (!$this->active) {
            $this->html .= $this->displayWarning(
                $this->l('The module is deactivated. Please activate it for proper working.')
            );
        }
        // quick guide
        $this->html .= $this->renderQuickGuide();
        $this->html .= $this->postProcess();
        $this->html .= $this->renderForm();

        return $this->html;
    }

    protected function postProcess()
    {
        $html = '';
        $settings_updated = false;
        $errors = [];
        $conf = 6;

        // Check if this is an ajax call / PS1.5
        if ($this->getPSVersion() < 1.6 && Tools::getIsset('ajax')
            && Tools::getValue('ajax') && Tools::getValue('action')) {
            if (is_callable([$this, 'ajaxProcess' . Tools::getValue('action')])) {
                call_user_func([$this, 'ajaxProcess' . Tools::getValue('action')]);
            }
            exit;
        }

        if (Tools::getValue('show_settings')) {
            Configuration::updateValue($this->settings_prefix . 'SHOW_SETTINGS', 1);
        }

        if (Tools::isSubmit('submitModule')) {
            // saving settings:
            $settings = $this->getSettings();
            foreach ($settings as $item) {
                if ($item['type'] == 'html' || (isset($item['lang']) && $item['lang'] == true)) {
                    continue;
                }
                if (Tools::isSubmit($item['name'])) {
                    $validated = true;
                    $val_method = (isset($item['validate']) ? $item['validate'] : '');

                    if (Tools::strlen(Tools::getValue($item['name']))) {
                        // Validation:
                        if (Tools::strlen($val_method) && is_callable(['Validate', $val_method])) {
                            $validated =
                                call_user_func(['Validate', $val_method], Tools::getValue($item['name']));
                        }
                    }
                    if ($validated) {
                        Configuration::updateValue(
                            $this->settings_prefix . $item['name'],
                            Tools::getValue($item['name']),
                            true
                        );
                        $settings_updated = true;
                    } else {
                        $label = trim($item['label'], ':');
                        $errors[] = sprintf($this->l('The "%s" field is invalid'), $label);
                    }
                }
            }

            // update lang fields:
            $languages = Language::getLanguages();
            foreach ($settings as $item) {
                if (!(isset($item['lang']) && $item['lang'])) {
                    continue;
                }
                $val_method = (isset($item['validate']) ? $item['validate'] : '');
                $lang_value = [];
                foreach ($languages as $lang) {
                    if (Tools::isSubmit($item['name'] . '_' . $lang['id_lang'])) {
                        $validated = true;
                        if (Tools::strlen(Tools::getValue($item['name'] . '_' . $lang['id_lang']))) {
                            // Validation:
                            if (Tools::strlen($val_method) && is_callable(['Validate', $val_method])) {
                                $validated = call_user_func(
                                    ['Validate', $val_method],
                                    Tools::getValue($item['name'] . '_' . $lang['id_lang'])
                                );
                            }
                        }
                        if ($validated) {
                            $lang_value[$lang['id_lang']] = Tools::getValue($item['name'] . '_' . $lang['id_lang']);
                            $settings_updated = true;
                        } else {
                            $label = trim($item['label'], ':');
                            $errors[] = sprintf($this->l('The "%s" field is invalid'), $label);
                        }
                    }
                }
                if (sizeof($lang_value)) {
                    Configuration::updateValue($this->settings_prefix . $item['name'], $lang_value, true);
                }
            }
        }

        $this->loadSettings();

        if ($settings_updated && !sizeof($errors)) {
            Configuration::updateValue($this->settings_prefix . 'SETTINGS_UPDATED', time());
            $token = Tools::getAdminTokenLite('AdminModules');
            $redirect_url = 'index.php?tab=AdminModules&configure=' . $this->name . '&token=' . $token . '&conf=' . $conf;
            Tools::redirectAdmin($redirect_url);
        } elseif (sizeof($errors)) {
            foreach ($errors as $err) {
                $html .= $this->displayError($err);
            }
        }

        return $html;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
                Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') :
                0;
        $this->fields_form = [];

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => [],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $settings = $this->getSettings();
        $field_forms = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Advanced Settings'),
                        'icon' => 'icon-cogs',
                    ],
                    'input' => $settings,
                    'submit' => [
                        'title' => $this->l('Save'),
                    ],
                ],
            ],
        ];

        foreach ($field_forms as &$field_form) {
            if ($this->getPSVersion() == 1.5) {
                $field_form['form']['submit']['class'] = 'button';
            }
        }

        foreach ($settings as $item) {
            if ($item['type'] == 'html') {
                continue;
            }
            if (isset($item['lang']) && $item['lang']) {
                foreach (Language::getLanguages() as $language) {
                    $helper->tpl_vars['fields_value'][$item['name']][$language['id_lang']] = Configuration::get(
                        $this->settings_prefix . $item['name'],
                        $language['id_lang']
                    );
                }
            } else {
                $helper->tpl_vars['fields_value'][$item['name']] = Configuration::get(
                    $this->settings_prefix .
                    $item['name']
                );
            }
        }

        return $helper->generateForm($field_forms);
    }

    public function getSettings()
    {
        $settings = [
            [
                'name' => 'custom_columns',
                'type' => 'textarea',
                'label' => $this->l('Columns added manually or by other modules:'),
                'hint' => $this->l('Here you can specify columns added manually or by other modules so the module can manage them too.'),
                'desc' => $this->l('Format: "column_system_name|column_label". One per line.'),
            ],
            [
                'type' => $this->getPSVersion() == 1.5 ? 'radio' : 'switch',
                'name' => 'DISABLE_AUTO_CUSTOM_COLUMNS',
                'label' => $this->l('Disable custom column auto detection:'),
                'hint' => $this->l('Try to enable this option if the module detects non-standard columns incorrectly (columns added by other modules or manually). You might need to manually delete corresponding rows from the "Columns added manually or by other modules" option.'),
                'class' => 't',
                'values' => [
                    [
                        'id' => 'disable_auto_custom_columns_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'disable_auto_custom_columns_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
                'default' => 0,
                'validate' => 'isInt',
            ],
        ];

        if ($this->getPSVersion() < 1.6) {
            foreach ($settings as &$item) {
                $desc = isset($item['desc']) ? $item['desc'] : '';
                $hint = isset($item['hint']) ? $item['hint'] . '<br/>' : '';
                $item['desc'] = $hint . $desc;
                $item['hint'] = '';
            }
        }

        return $settings;
    }

    protected function loadSettings()
    {
        foreach ($this->getSettings() as $item) {
            if ($item['type'] == 'html') {
                continue;
            }

            $name = Tools::strtolower($item['name']);
            if (isset($item['lang']) && $item['lang']) {
                $this->$name = [];
                foreach (Language::getLanguages() as $language) {
                    $this->{$name}[$language['id_lang']] = Configuration::get(
                        $this->settings_prefix . $item['name'],
                        $language['id_lang']
                    );
                }
            } else {
                $this->$name = Configuration::get(
                    $this->settings_prefix .
                    $item['name']
                );
            }
        }
    }

    public function getPSVersion($without_dots = false)
    {
        $ps_version = _PS_VERSION_;
        $ps_version = Tools::substr($ps_version, 0, 3);

        if ($without_dots) {
            $ps_version = str_replace('.', '', $ps_version);
        }

        return (float) $ps_version;
    }

    public function hookPstProductFilter($params = [])
    {
        $routeName = '';
        if ($this->checkUseV2()) {
            $container = PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();
            $requestStack = $container->get('request_stack');
            $request = $requestStack->getCurrentRequest();
            if (null !== $request) {
                $routeName = $request->attributes->get('_route');
            }
        }

        if (Tools::getValue('ajax')
            || (!$this->checkUseV2() && Tools::getValue('controller') == 'AdminProducts'
                && !Tools::isSubmit('id_product') && !Tools::getValue('id_product'))
            || ($this->checkUseV2() && $routeName == 'admin_products_index')
        ) {
            try {
                $filters = $this->getFilters();
                $columns = $this->getColumns();

                if ($this->getActiveFilters()) {
                    $any_filter = true;
                } else {
                    $any_filter = false;
                }

                if ($this->checkUseV2()) {
                    $products_url =
                        $this->context->link->getAdminLink('AdminModules', true, ['route' => 'pstproductfilter_list_v2']);
                } else {
                    $products_url =
                        $this->context->link->getAdminLink('AdminModules', true, ['route' => 'pstproductfilter_list']);
                }

                $set = null;
                $id_set = $this->getIdCurrentFilterSet();
                if ($id_set) {
                    $set = new PstProductFilterSet($id_set);
                }

                $this->context->smarty->assign([
                    'psv' => $this->getPSVersion(),
                    'pstpf_use_symfony' => $this->checkUseSymfony(),
                    'pstpf_filters' => $filters,
                    'pstpf_any_filter_active' => $any_filter,
                    'pstpf_module' => $this,
                    'pstpf_columns' => $columns,
                    'pstpf_products_url' => $products_url,
                    'pstpf_sets' => PstProductFilterSet::getAvailableSets(),
                    'pstpf_current_set' => $set,
                    'pstpf_ps178_plus' => version_compare(_PS_VERSION_, '1.7.8.0', '>='),
                    'pstpf_ps81_plus' => (version_compare(_PS_VERSION_, '8.1.0', '>=') || $this->checkUseV2()),
                    'pstpf_admin_tpl_dir' => _PS_MODULE_DIR_ . $this->name . '/views/templates/hook',
                ]);

                return $this->display(__FILE__, 'filter.tpl');
            } catch (Exception $e) {
                return $this->displayError($e->getMessage());
            }
        }
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        if (Tools::getValue('controller') == 'AdminProducts' && !Tools::isSubmit('id_product')) {
            if (Tools::isSubmit('submitResetproduct')) {
                $this->resetFilters();
            }

            $token = Tools::getAdminTokenLite('AdminModules');
            $ajax_url = 'index.php?controller=AdminModules&configure=' . $this->name . '&token=' . $token;
            if ($this->checkUseSymfony()) {
                $ajax_url =
                    $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name]);
            }
            $products_token = Tools::getAdminTokenLite('AdminProducts');
            $products_ajax_url = 'index.php?controller=AdminProducts&token=' . $products_token;
            $locale = Tools::strtolower(Configuration::get('PS_LOCALE_LANGUAGE'))
                . '-' . Tools::strtoupper(Configuration::get('PS_LOCALE_COUNTRY'));
            if ($this->checkUseV2()) {
                $ajax_url_symfony =
                    $this->context->link->getAdminLink('AdminModules', true, ['route' => 'pstproductfilter_list_v2']);
            } else {
                $ajax_url_symfony =
                    $this->context->link->getAdminLink('AdminModules', true, ['route' => 'pstproductfilter_list']);
            }

            $this->context->smarty->assign([
                'pstpf_psv' => $this->getPSVersion(),
                'pstpf_ajax_url' => $ajax_url,
                'pstpf_product_ajax_url' => ($this->checkUseSymfony() ? $ajax_url_symfony : $products_ajax_url),
                'pstpf_locale' => $locale,
                'pstpf_currency_iso' => $this->context->currency->iso_code,
                'pstpf_order_by' => Tools::getValue('productOrderby', $this->context->cookie->productsorderOrderby),
                'pstpf_order_way' => Tools::getValue('productOrderway', $this->context->cookie->productsorderOrderway),
                'pstpf_use_symfony' => $this->checkUseSymfony(),
                'pstpf_use_v2' => $this->checkUseV2(),
                'pstpf_weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
            ]);

            return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/admin_header.tpl');
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $css_version = (version_compare(_PS_VERSION_, '8.0.0', '>=') ? '?' . $this->version : '');
        $js_version = '?' . $this->version;

        if (Tools::getValue('controller') == 'AdminProducts' && !Tools::isSubmit('id_product')) {
            $this->context->controller->addCSS([
                $this->_path . 'views/css/admin.css' . $css_version,
            ]);
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryUI(['ui.sortable', 'ui.slider']);
            $this->context->controller->addJS([
                $this->_path . 'views/js/URI.js' . $js_version,
                $this->_path . 'views/js/jquery.typeWatch.js' . $js_version,
                $this->_path . 'views/js/admin.js' . $js_version,
            ]);
            if ($this->checkUseV2()) {
                $this->context->controller->addCSS([
                    $this->_path . 'views/css/sumoselect.min.css' . $css_version,
                ]);
                $this->context->controller->addJS([
                    $this->_path . 'views/js/jquery.sumoselect.min.js' . $js_version,
                ]);
            }
            // add custom js if necessary
            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/js/custom.js')) {
                $this->context->controller->addJS($this->_path . 'views/js/custom.js' . $js_version);
            }
        } elseif (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS([
                $this->_path . 'views/css/admin.css' . $css_version,
            ]);
        }
    }

    public function hookActionAdminProductsListingFieldsModifier($params)
    {
        try {
            $this->processDisplayAjaxBefore();

            if ($this->checkUseSymfony()) {
                if (Tools::isSubmit('id_set')) {
                    $this->getColumns(); // load and save columns if changed filter set
                }

                $extra_columns = $this->getColumns(true);
                foreach ($extra_columns as $key => $col) {
                    if (isset($col['orderby']) && $col['orderby']
                        && isset($col['orderby_table']) && isset($col['orderby_field'])
                    ) {
                        $params['sql_select'][$key] = [
                            'table' => $this->convertTableKeyToV2($col['orderby_table']),
                            'field' => $col['orderby_field'],
                        ];
                    }
                }

                if (isset($params['sql_select'])) {
                    // when loading a filter set, also load sorting order
                    $id_set = (int) Tools::getValue('id_set');
                    if (Tools::isSubmit('id_set') && $id_set) {
                        $set = new PstProductFilterSet($id_set);
                        $this->context->cookie->productorderOrderby = $set->order_by;
                        $this->context->cookie->productorderOrderway = $set->order_way;
                        $params['sql_order'] = [$set->order_by . ' ' . $set->order_way];
                    }
                }
                
                if (isset($params['sql_where'])) {
                    $where = &$params['sql_where'];
                    $filters = $this->getActiveFilters(false);
                    $filters = (is_array($filters) ? array_filter($filters) : false);
                    if (!$filters) {
                        $filters = [];
                    }
                    // option "strict match"
                    $strict_settings = $this->getStrictSettings();

                    if ($filters) {
                        foreach ($filters as $filter_name => $value) {
                            $value = $this->prepareFilterValue($value);
                            $func = 'searchProductsBy' . Tools::ucfirst($filter_name);
                            $strict = !empty($strict_settings[$filter_name]);
                            if (is_callable([$this, $func])) {
                                $query = trim($this->$func($value, $strict));
                                $where[$filter_name] = $query;
                            } elseif (method_exists('PstProductSearch', $func)) {
                                try {
                                    $query = trim(PstProductSearch::getInstance()->$func($value, $strict));
                                } catch(Exception $e) {
                                    $query = '';
                                }
                                $where[$filter_name] = $query;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo $this->displayError($e->getMessage());
        }
    }

    public function hookActionAdminProductsListingResultsModifier($params)
    {
        try {
            $list = null;

            if ($this->checkUseSymfony()) {
                if (isset($params['products']) && is_array($params['products'])) {
                    $list = &$params['products'];
                }
            }

            if ($list !== null) {
                $columns = $this->getColumns(true, true);
                foreach ($list as &$row) {
                    if (isset($row['id_product']) && $row['id_product']) {
                        $product_object = new Product($row['id_product'], true, $this->context->language->id);
                        foreach ($columns as $col => $column_data) {
                            if ($column_data['active'] /* && !(isset($row[$col]) && $row[$col]) */) {
                                $func = 'displayProduct' . Tools::ucfirst(str_replace('pstpf_', '', $col));
                                $show = false;
                                if (is_callable([$this, $func])) {
                                    $value = $this->$func($product_object);
                                    $show = true;
                                } elseif (method_exists('PstProductColumn', $func)) {
                                    $value = PstProductColumn::getInstance()->$func($product_object);
                                    $show = true;
                                }
                                if ($show) {
                                    if (is_string($value)) {
                                        $value = trim($value);
                                    } elseif (is_array($value)) {
                                        $value = array_filter($value);
                                    }
                                    $row[$col] = ($value ? $value : '--');
                                }
                            }
                        }
                    }

                    // fix ps1.7.5 bug with price final display
                    if (version_compare(_PS_VERSION_, '1.7.6.0', '<')
                        && isset($row['id_product']) && isset($row['price_final'])
                    ) {
                        $row['price_final'] = $this->getPriceFinal($row['id_product']);
                    }

                    $filters = $this->getFilters();
                    // When using the attribute filter, try to show the filtered combinations prices:
                    if (!empty($filters['attribute']['value'])) {
                        $row = $this->showCombinationPrices($row, $filters);
                        $row = $this->showCombinationQty($row, $filters);
                    }
                }
            }
        } catch (Exception $e) {
            echo $this->displayError($e->getMessage());
        }
    }

    public function clearSmartyCache()
    {
        $directory = _PS_MODULE_DIR_ . $this->name . '/views/templates/hook/';
        $templates = array_diff(scandir($directory), ['..', '.']);
        foreach ($templates as &$template) {
            if (strpos($template, '.tpl') === false) {
                continue;
            }

            if (method_exists($this, '_clearCache')) {
                $this->_clearCache($template);
            }

            $template = basename($template, '.tpl');

            if ($this->getPSVersion() == 1.7 && method_exists($this, '_deferedClearCache')) {
                $this->_deferedClearCache($this->getTemplatePath($template), null, null);
            }
        }
        Configuration::updateValue($this->settings_prefix . 'SETTINGS_UPDATED', time());
    }

    public function displayWarning($text)
    {
        if (method_exists('Module', 'displayWarning')) {
            return parent::displayWarning($text);
        } elseif (method_exists('Module', 'adminDisplayWarning')) {
            return parent::adminDisplayWarning($text);
        } else {
            return $text;
        }
    }

    public function displayInformation($text)
    {
        if (method_exists('Module', 'displayInformation')) {
            return parent::displayInformation($text);
        } elseif (method_exists('Module', 'adminDisplayInformation')) {
            return parent::adminDisplayInformation($text);
        } else {
            return $text;
        }
    }

    protected function getCacheId($name = null)
    {
        return parent::getCacheId($name) . '_' . Configuration::get($this->settings_prefix . 'SETTINGS_UPDATED');
    }

    public function getFilters()
    {
        $id_set = (int) Tools::getValue('id_set');
        if (Tools::isSubmit('id_set')) {
            if ($id_set) {
                $this->setIdCurrentFilterSet($id_set);
            } else {
                // if submitted empty set
                $this->resetFilters();
            }
        } else {
            // if set ID is not submitted but some filter value has been changed, reset current set ID
            if (Tools::isSubmit('pstpf')
                && Tools::getValue('action') != 'renderSelectedFilters'
                && !Tools::isSubmit('keep_set')
            ) {
                $this->setIdCurrentFilterSet(0);
            }
        }

        // get the filter settings either from the filter set or from main settings
        $filter_values = [];
        if ($id_set) {
            $set = new PstProductFilterSet($id_set);
            $filter_settings = [];
            $filters = $set->getFilters();
            $filter_values = [];
            foreach ($filters as $key => $filter) {
                if ($filter['value']) {
                    $filter_values[$key] = $filter['value'];
                }
            }
        } else {
            $filter_settings = $this->getFilterSettings();
            $filters = $this->getFiltersRawData();
            // if submitted "empty" set, just reset the values
            if (Tools::isSubmit('id_set')) {
                $filter_values = [];
            } else {
                if (Tools::isSubmit('pstpf')) {
                    $filter_values = Tools::getValue('pstpf', []);
                    if (is_array($filter_values)) {
                        foreach ($filter_values as &$val) {
                            if (is_array($val)) {
                                $val = array_combine($val, $val);
                            }
                        }
                    }
                }

                // if no filters were submitted - either the module filters or the standard filters:
                if (!Tools::isSubmit('pstpf') && !Tools::isSubmit('product')) {
                    // get pre-set values
                    foreach ($filter_settings as $filter_name => $filter_row) {
                        $filter_values[$filter_name] = $filter_row['value'];
                    }
                }

                // get input data also from standard filter inputs
                $ps_filters = Tools::getValue('product');
                if ($ps_filters && is_array($ps_filters) && isset($ps_filters['filters'])) {
                    foreach ($filters as $filter_name => $data) {
                        if (isset($ps_filters['filters']['pstpf_' . $filter_name])
                            && !isset($filter_values[$filter_name])
                        ) {
                            $filter_values[$filter_name] = $ps_filters['filters']['pstpf_' . $filter_name];
                        }
                    }
                }
                // check filter_column_* input
                $array_type_fields = $this->getArrayTypeFields();
                foreach ($filters as $filter_name => $data) {
                    if (Tools::getValue('filter_column_pstpf_' . $filter_name)) {
                        if (in_array($filter_name, $array_type_fields)) {
                            $filter_values[$filter_name][Tools::getValue('filter_column_pstpf_' . $filter_name)] =
                                Tools::getValue('filter_column_pstpf_' . $filter_name);
                        } else {
                            $filter_values[$filter_name] = Tools::getValue('filter_column_pstpf_' . $filter_name);
                        }
                    }
                }
                
                // if were submitted only standard PS filters, load the previous module's filters to not lose anything
                if (!Tools::isSubmit('pstpf') && !Tools::isSubmit('pstpf_submit')) {
                    // get pre-set values
                    foreach ($filter_settings as $filter_name => $filter_row) {
                        // only skip the values taken from standard filters:
                        if (empty($filter_values[$filter_name])) {
                            $filter_values[$filter_name] =
                                $this->prepareFilterValue($filter_row['value']);
                        }
                    }
                }
            }
        }

        // check if resetting the default filters:
        $columns = $this->getColumnsRawData();
        $any_default_filters = false;
        $all_filters_empty = true;
        foreach ($columns as $column_name => $column_data) {
            // check if any filter submitted
            if (Tools::isSubmit('filter_column_' . $column_name)) {
                $any_default_filters = true;
                // if any submitted and not empty, it's definitely not reset
                if (Tools::getValue('filter_column_' . $column_name)) {
                    $all_filters_empty = false;
                    break;
                }
            }
        }
        $default_reset = false;
        // additionally check if submitted empty category filter
        if ($any_default_filters && $all_filters_empty
            && (Tools::isSubmit('filter_category') && !Tools::getValue('filter_category'))
        ) {
            $default_reset = true;
        }

        // build the filters data
        $this->filter_values_to_save = [];
        $i = 0;
        foreach ($filters as $key => &$data) {
            $data['value'] = $this->getFilterValue($filter_values, $key, $default_reset);
            if (isset($filter_settings[$key])) {
                $settings = $filter_settings[$key];
                $data['active'] = $settings['active'];
                $data['position'] = $settings['position'];
                $data['strict'] = (isset($settings['strict']) ? $settings['strict'] : 0);
            } else {
                $data['filter'] = $key;
            }

            $data['active'] = (isset($data['active']) ? $data['active'] : false);
            $data['position'] = (isset($data['position']) ? $data['position'] : $i);
            $data['strict'] = (isset($data['strict']) ? $data['strict'] : 0);

            // complete the value data by using the list data
            if ($data['type'] == 'dropdown') {
                if (isset($data['id_key'])) {
                    $data['value_dropdown'] = [];
                    foreach ($data['data'] as $row) {
                        if (!empty($data['optgroup'])) {
                            foreach ($row as $sub_row) {
                                if (isset($sub_row[$data['id_key']])) {
                                    $id = $sub_row[$data['id_key']];
                                    if (isset($data['value'][$id])) {
                                        $data['value_dropdown'][$id] = $sub_row;
                                    }
                                }
                            }
                        } else {
                            if (isset($row[$data['id_key']])) {
                                $id = $row[$data['id_key']];
                                if (isset($data['value'][$id])) {
                                    $data['value_dropdown'][$id] = $row;
                                }
                            }
                        }
                    }
                }
            }

            ++$i;
        }
        $this->saveFilterValues();

        uasort($filters, function ($a, $b) {
            if ($a['position'] == $b['position']) {
                return 0;
            }

            return ($a['position'] < $b['position']) ? -1 : 1;
        });

        if (!$filter_settings || count($filter_settings) < count($filters)) {
            $this->saveFilterSettings($filters);
        }

        return $filters;
    }

    public function getFiltersRawData()
    {
        // cache
        if (!empty(self::$cache_filters_raw_data)) {
            return self::$cache_filters_raw_data;
        }

        $id_lang = $this->context->language->id;
        $features = $this->getFeaturesForFilter();
        $feature_values = $this->getFeatureValuesForFilter($features);
        $attribute_groups = $this->getAttributeGroupsForFilter();
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $brands = $this->getManufacturersForFilter();
        $suppliers = $this->getSuppliersForFilter();

        $data = [
            'manufacturer' => [
                'label' => $this->l('Brand:'),
                'type' => 'dropdown',
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => true,
                'data' => $brands,
                'id_key' => 'id_manufacturer',
                'multiple' => true,
                'search' => true,
                'lazyload' => count($brands) > 50,
                'show_toggle_all' => true,
            ],
            'supplier' => [
                'label' => $this->l('Supplier:'),
                'type' => 'dropdown',
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => true,
                'data' => $suppliers,
                'id_key' => 'id_supplier',
                'multiple' => true,
                'search' => true,
                'lazyload' => count($suppliers) > 50,
                'show_toggle_all' => true,
                'show_strict_match' => true,
            ],
            'desc' => [
                'label' => $this->l('Description:'),
                'hint2' => $this->l('Any text in short and full product description'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => true,
            ],
            'name' => [
                'label' => $this->l('Name:'),
                'hint2' => $this->l('Product name in any language'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
            'feature' => [
                'label' => $this->l('Feature:'),
                'hint2' => $this->l('Search products having some specific feature'),
                'type' => 'dropdown',
                'search' => true,
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => true,
                'data' => $features,
                'id_key' => 'id_feature',
                'multiple' => true,
                'lazyload' => count($features) > 50,
                'show_toggle_all' => true,
                'show_strict_match' => true,
            ],
            'attributeGroup' => [
                'label' => $this->l('Attribute group:'),
                'hint2' => $this->l('Search products with combinations having attributes from specific attribute group'),
                'type' => 'dropdown',
                'search' => true,
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => true,
                'data' => $attribute_groups,
                'id_key' => 'id_attribute_group',
                'multiple' => true,
                'lazyload' => count($attribute_groups) > 50,
                'show_toggle_all' => true,
                'show_strict_match' => true,
            ],
            'ean13' => [
                'label' => $this->l('EAN-13 or JAN barcode:'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => true,
            ],
            'wholesalePrice' => [
                'label' => sprintf($this->l('Cost price, %s:'), $currency->sign),
                'hint2' => $this->l('Wholesale price'),
                'type' => 'range',
                'class' => 'pstpf_search_range pstpf_search_input',
                'active' => false,
                'min' => floor($this->getCostPriceMin()),
                'max' => ceil($this->getCostPriceMax()),
                'step' => 0.01,
            ],
            'featureValue' => [
                'label' => $this->l('Feature value:'),
                'type' => 'dropdown',
                'search' => true,
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => false,
                'data' => $feature_values,
                'id_key' => 'id_feature_value',
                'multiple' => true,
                'optgroup' => true,
                'lazyload' => count($features) > 15,
                'show_strict_match' => true,
            ],
            'attribute' => [
                'label' => $this->l('Attribute:'),
                'hint2' => $this->l('Search products with combinations having specific attributes'),
                'type' => 'dropdown',
                'search' => true,
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => false,
                'data' => $this->getAttributesForFilter($attribute_groups),
                'id_key' => 'id_attribute',
                'multiple' => true,
                'optgroup' => true,
                'lazyload' => count($attribute_groups) > 15,
                'show_strict_match' => true,
            ],
            'supplierReference' => [
                'label' => $this->l('Supplier reference:'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
            'reference' => [
                'label' => $this->l('Reference:'),
                'hint2' => $this->l('You can search by product and combination references'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
            'mpn' => [
                'label' => $this->l('MPN:'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
            'upc' => [
                'label' => $this->l('UPC barcode:'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
            'isbn' => [
                'label' => $this->l('ISBN'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
            'withCombinations' => [
                'label' => $this->l('With combinations:'),
                'type' => 'select_yn',
                'class' => 'pstpf_select pstpf_search_input',
                'active' => false,
            ],
            'withImages' => [
                'label' => $this->l('With images'),
                'hint2' => $this->l('Search products only with/without images'),
                'type' => 'select_yn',
                'class' => 'pstpf_select pstpf_search_input',
                'active' => false,
            ],
            'type' => [
                'label' => $this->l('Product type:'),
                'type' => 'dropdown',
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => false,
                'data' => [
                    ['id_type' => -99, 'name' => $this->l('Simple')],
                    ['id_type' => Product::PTYPE_PACK, 'name' => $this->l('Pack')],
                    ['id_type' => Product::PTYPE_VIRTUAL, 'name' => $this->l('Virtual')],
                ],
                'id_key' => 'id_type',
                'multiple' => true,
                'show_toggle_all' => true,
            ],
            'taxRule' => [
                'label' => $this->l('Tax rule:'),
                'type' => 'dropdown',
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => false,
                'search' => true,
                'data' => $this->getTaxRulesGroupsForFilter(),
                'id_key' => 'id_tax_rules_group',
                'multiple' => true,
                'show_toggle_all' => true,
            ],
            'weight' => [
                'label' => sprintf($this->l('Weight, %s:'), Configuration::get('PS_WEIGHT_UNIT')),
                'type' => 'range',
                'class' => 'pstpf_search_range pstpf_search_input',
                'active' => false,
                'min' => 0,
                'max' => ceil($this->getWeightMax()),
                'is_price_slider' => false,
                'step' => 0.01,
            ],
            'withDiscounts' => [
                'label' => $this->l('Active discounts'),
                'hint2' => $this->l('Search products only with/without active specific prices'),
                'type' => 'select_yn',
                'class' => 'pstpf_select pstpf_search_input',
                'active' => false,
            ],
            'allowOOS' => [
                'label' => $this->l('OOS orders'),
                'hint2' => $this->l('Allow orders when a product is out of stock'),
                'type' => 'select_yn',
                'class' => 'pstpf_select pstpf_search_input',
                'active' => false,
            ],
            'carrier' => [
                'label' => $this->l('Carrier:'),
                'hint2' => $this->l('Search products by available carriers'),
                'type' => 'dropdown',
                'search' => true,
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => false,
                'data' => Carrier::getCarriers($id_lang, false, false, false, null, Carrier::ALL_CARRIERS),
                'id_key' => 'id_reference',
                'multiple' => true,
                'show_toggle_all' => true,
                'show_strict_match' => true,
            ],
            'tag' => [
                'label' => $this->l('Tags:'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
            'condition' => [
                'label' => $this->l('Condition:'),
                'type' => 'dropdown',
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => false,
                'data' => [
                    ['id_condition' => 1, 'name' => $this->l('New')],
                    ['id_condition' => 2, 'name' => $this->l('Used')],
                    ['id_condition' => 3, 'name' => $this->l('Refurbished')],
                ],
                'id_key' => 'id_condition',
                'multiple' => true,
                'show_toggle_all' => true,
            ],
            'customizable' => [
                'label' => $this->l('Customizable:'),
                'type' => 'select_yn',
                'class' => 'pstpf_select pstpf_search_input',
                'active' => false,
            ],
            'hasAttachments' => [
                'label' => $this->l('With attachments:'),
                'type' => 'select_yn',
                'class' => 'pstpf_select pstpf_search_input',
                'active' => false,
            ],
            'numberOfSales' => [
                'label' => $this->l('Number of sales:'),
                'type' => 'range',
                'class' => 'pstpf_search_range pstpf_search_input',
                'active' => false,
                'min' => 0,
                'max' => ceil($this->getNumberOfSalesMax()),
                'is_price_slider' => false,
                'step' => 1,
            ],
            'visibility' => [
                'label' => $this->l('Visibility:'),
                'type' => 'dropdown',
                'class' => 'pstpf_search_status pstpf_search_input',
                'active' => false,
                'data' => $this->getVisibilityOptions('id_condition'),
                'id_key' => 'id_condition',
                'multiple' => true,
                'show_toggle_all' => true,
            ],
            'webOnly' => [
                'label' => $this->l('Web only'),
                'hint2' => $this->l('Products which are not sold in your retail store'),
                'type' => 'select_yn',
                'class' => 'pstpf_select pstpf_search_input',
                'active' => false,
            ],
            'stockLocation' => [
                'label' => $this->l('Stock location:'),
                'type' => 'text',
                'class' => 'pstpf_search_text pstpf_search_input',
                'active' => false,
            ],
        ];

        // MPN is not avail until ps1.7.7
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            unset($data['mpn']);
        }

        // save to cache
        self::$cache_filters_raw_data = $data;

        return $data;
    }

    protected function getFilterSettings()
    {
        $settings_raw = Db::getInstance()->executeS(
            'SELECT *
             FROM `' . _DB_PREFIX_ . 'pstproductfilter`
             WHERE `id_employee` = ' . (int) $this->context->employee->id
        );

        $settings = [];
        foreach ($settings_raw as $item) {
            $settings[$item['filter']] = [
                'active' => $item['active'],
                'position' => $item['position'],
                'value' => $item['value'],
                'strict' => $item['strict'],
            ];
        }

        return $settings;
    }

    protected function getColumnSettings($reset_positions = false)
    {
        $settings_raw = Db::getInstance()->executeS(
            'SELECT *
             FROM `' . _DB_PREFIX_ . 'pstproductfilter_column`
             WHERE `id_employee` = ' . (int) $this->context->employee->id
        );

        $settings = [];
        foreach ($settings_raw as $item) {
            $settings[$item['column']] = [
                'active' => $item['active'],
                'position' => $item['position'],
            ];
        }

        // some columns may be not saved yet in database. Check the full list of columns from the module settings:
        $all_columns = $this->getColumnsRawData();
        $i = 0;
        foreach ($all_columns as $col => $data) {
            if (!isset($settings[$col])) {
                $settings[$col] = [
                    'active' => $data['active'],
                    'position' => $i,
                ];
            }
            if ($reset_positions) {
                $settings[$col]['position'] = $i;
            }
            ++$i;
        }

        return $settings;
    }

    protected function saveFilterSettings($filters)
    {
        foreach ($filters as $filter) {
            if (isset($filter['filter'])) {
                Db::getInstance()->execute(
                    'INSERT INTO `' . _DB_PREFIX_ . 'pstproductfilter`
                     (`filter`, `active`, `position`, `id_employee`)
                     VALUES
                     ("' . pSQL($filter['filter']) . '", ' . (int) $filter['active'] . ',
                      ' . (int) $filter['position'] . ', ' . (int) $this->context->employee->id . ')
                     ON DUPLICATE KEY UPDATE
                     `active` = ' . (int) $filter['active'] . ', `position` = ' . (int) $filter['position']
                );
            }
        }
    }

    protected function saveColumnSettings($new_data)
    {
        $columns = $this->getColumnSettings();
        if (!$columns) {
            $columns = $new_data;
        }

        $i = 0;
        foreach ($columns as $column => $data) {
            if (isset($new_data[$column])) {
                $row = $new_data[$column];
            } else {
                $row = $data;
                $row['active'] = 0;
                $row['position'] = $i;
            }

            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'pstproductfilter_column`
                 (`column`, `active`, `position`, `id_employee`)
                 VALUES
                 ("' . pSQL($column) . '", ' . (int) $row['active'] . ',
                  ' . (int) $row['position'] . ', ' . (int) $this->context->employee->id . ')
                 ON DUPLICATE KEY UPDATE
                 `active` = ' . (int) $row['active'] . ', `position` = ' . (int) $row['position']
            );

            ++$i;
        }
    }

    public function ajaxProcessSaveFilterSettings()
    {
        $filters = Tools::getValue('filters');

        if ($filters && is_array($filters)) {
            $this->saveFilterSettings($filters);
            exit('1');
        }

        exit('0');
    }

    public function ajaxProcessSaveColumnSettings()
    {
        $columns = Tools::getValue('columns');

        if ($columns && is_array($columns)) {
            $this->saveColumnSettings($columns);
            exit('1');
        }

        exit('0');
    }

    public function renderFilterValue($input)
    {
        if (!(isset($input['value']) && $input['value'])) {
            return null;
        }

        $val = $input['value'];

        if ($input['type'] == 'select_yn') {
            $values = ['yes' => $this->l('yes'), 'no' => $this->l('no')];

            return isset($values[$val]) ? $values[$val] : $val;
        } elseif ($input['type'] == 'select') {
            if (is_array($val)) {
                $names = [];
                foreach ($val as $id_option) {
                    if (isset($input['options'][$id_option])) {
                        $names[] = $input['options'][$id_option];
                    }
                }

                return implode(', ', $names);
            } elseif (isset($input['options'][$val])) {
                return $input['options'][$val];
            }
        } elseif ($input['type'] == 'range') {
            $values = explode('-', $val);
            if (isset($input['is_price_slider']) && $input['is_price_slider']) {
                return Tools::displayPrice($values[0]) . ' - ' . Tools::displayPrice($values[1]);
            } else {
                return $values[0] . ' - ' . $values[1];
            }
        } elseif ($input['type'] == 'dropdown') {
            return sprintf($this->l('%s selected'), count($val));
        }

        return $val;
    }

    public function ajaxProcessRenderSelectedFilters()
    {
        $this->processDisplayAjaxBefore();
        $filters = $this->getFilters();

        if ($this->getActiveFilters()) {
            $any_filter = true;
        } else {
            $any_filter = false;
        }

        $this->context->smarty->assign([
            'psv' => $this->getPSVersion(),
            'pstpf_filters' => $filters,
            'pstpf_any_filter_active' => $any_filter,
            'pstpf_module' => $this,
        ]);

        $extra_html = '';
        if (!$this->active) {
            $extra_html .= $this->displayWarning(
                $this->l('The module is deactivated. Please activate it for proper working.')
            );
        }

        exit($this->display(__FILE__, '_selected_filters.tpl') . $extra_html);
    }

    public function getSelectOptions($data, $id_key, $name_key)
    {
        $result = [];

        if (is_array($data)) {
            foreach ($data as $row) {
                $result[$row[$id_key]] = $row[$name_key];
            }
        }

        return $result;
    }

    public function getColumns($only_pstpf = false, $only_active = false)
    {
        $reset_set = false;
        $id_set = (int) Tools::getValue('id_set');
        if (Tools::isSubmit('id_set')) {
            $this->setIdCurrentFilterSet($id_set);
            if (!$id_set) {
                $reset_set = true;
            }
        }

        // get the column settings either from the filter set or from main settings
        if ($id_set) {
            $set = new PstProductFilterSet($id_set);
            $column_settings = [];
            $columns = $set->getColumns($only_pstpf);
        } else {
            $column_settings = $this->getColumnSettings($reset_set);
            $columns = $this->getColumnsRawData($only_pstpf);
        }

        $default_positions = $this->getDefaultColumnPositions();
        $i = 0;
        foreach ($columns as $key => &$data) {
            if (isset($column_settings[$key])) {
                $settings = $column_settings[$key];
                $data['active'] = $settings['active'];
                $data['position'] = $settings['position'];
            } else {
                $data['column'] = $key;
            }

            if (!isset($data['active'])) {
                $data['active'] = false;
            }
            if (!isset($data['position'])) {
                $data['position'] = $i;
            }
            $data['default_position'] = $default_positions[$key];

            if ($only_active && !$data['active']) {
                unset($columns[$key]);
            }

            if ($data['active']) {
                if ($key == 'pstpf_manufacturer') {
                    $data['options'] = Manufacturer::getManufacturers();
                }
            }

            ++$i;
        }

        // sort columns by position
        uasort($columns, function ($a, $b) {
            return $a['position'] - $b['position'];
        });

        if (!$only_active && !$only_pstpf) {
            if (!$column_settings || count($column_settings) < count($columns) || $reset_set) {
                $this->saveColumnSettings($columns);
            }
        }

        return $columns;
    }

    public function getColumnsRawData($only_pstpf = false, $only_default = false)
    {
        // cache
        $cache_key = (int) $only_pstpf . '-' . (int) $only_default;
        if (!empty(self::$cache_columns_raw_data[$cache_key])) {
            return self::$cache_columns_raw_data[$cache_key];
        }

        $default_columns = [
            $this->getCorrectColumnKey('id_product') => [
                'name' => $this->lCol('ID', 'Admin.Global'),
                'active' => 1,
                'default' => true,
                'orderby' => true,
                'width' => '5rem',
                'is_label' => true,
                'filter_type' => 'range',
                'range_filter_min' => 0,
                'range_filter_max' => 1000000,
            ],
            $this->getCorrectColumnKey('image') => [
                'name' => $this->lCol('Image', 'Admin.Global'),
                'active' => 1,
                'default' => true,
                'tab' => 1,
            ],
            $this->getCorrectColumnKey('name') => [
                'name' => $this->lCol('Name', 'Admin.Global'),
                'active' => 1,
                'default' => true,
                'orderby' => true,
                'tab' => 1,
                'filter_type' => 'text',
                'filter_text' => $this->lCol('Search name', 'Admin.Catalog.Help'),
            ],
            $this->getCorrectColumnKey('reference') => [
                'name' => $this->lCol('Reference', 'Admin.Global'),
                'active' => 1,
                'default' => true,
                'orderby' => true,
                'width' => '9%',
                'filter_type' => 'text',
                'filter_text' => $this->lCol('Search ref.', 'Admin.Catalog.Help'),
            ],
            $this->getCorrectColumnKey('name_category') => [
                'name' => $this->lCol('Category', 'Admin.Catalog.Feature'),
                'active' => 1,
                'default' => true,
                'orderby' => true,
                'filter_type' => 'text',
                'filter_text' => $this->lCol('Search category', 'Admin.Catalog.Help'),
            ],
            $this->getCorrectColumnKey('price') => [
                'name' => $this->lCol('Price (tax excl.)', 'Admin.Catalog.Feature'),
                'active' => 1,
                'default' => true,
                'orderby' => true,
                'width' => '9%',
                'align' => 'center',
                'tab' => 2,
                'filter_type' => 'range',
                'range_filter_min' => 0,
                'range_filter_max' => 1000000,
            ],
            $this->getCorrectColumnKey('price_final') => [
                'name' => $this->lCol('Price (tax incl.)', 'Admin.Catalog.Feature'),
                'active' => 1,
                'default' => true,
                'width' => '9%',
                'align' => 'center',
                'tab' => 2,
            ],
            $this->getCorrectColumnKey('sav_quantity') => [
                'name' => $this->lCol('Quantity', 'Admin.Catalog.Feature'),
                'active' => 1,
                'default' => true,
                'orderby' => true,
                'width' => '9%',
                'align' => 'center',
                'tab' => 3,
                'filter_type' => 'range',
                'range_filter_min' => -1000000,
                'range_filter_max' => 1000000,
            ],
            $this->getCorrectColumnKey('active') => [
                'name' => $this->lCol('Status', 'Admin.Global'),
                'active' => 1,
                'default' => true,
                'orderby' => true,
                'align' => 'center',
                'filter_type' => 'active',
                'range_filter_min' => 0,
                'range_filter_max' => 1000000,
            ],
        ];

        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            unset($default_columns[$this->getCorrectColumnKey('sav_quantity')]);
        }

        if ($this->custom_columns) {
            $custom_columns = $this->getCustomColumns();
            if (is_array($custom_columns)) {
                foreach ($custom_columns as $column_data) {
                    $column_data = explode('|', $column_data);
                    if (is_array($column_data) && count($column_data) == 2) {
                        $default_columns[$column_data[0]] =
                            ['name' => $column_data[1], 'active' => 1, 'default' => true];
                    }
                }
            }
        }

        if ($only_default) {
            return $default_columns;
        }

        $module_columns = [
            'pstpf_manufacturer' => [
                'name' => $this->l('Brand'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'id_manufacturer',
                'filter_type' => 'dropdown',
                'option_id_param' => 'id_manufacturer',
            ],
            'pstpf_suppliers' => [
                'name' => $this->l('Suppliers'),
                'active' => 0,
                'orderby' => false,
            ],
            'pstpf_defaultSupplier' => [
                'name' => $this->l('Default supplier'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'id_supplier',
            ],
            'pstpf_supplierReferences' => [
                'name' => $this->l('Supplier ref.'),
                'active' => 0,
                'orderby' => false,
                'hint' => $this->l('List of supplier references of the product'),
            ],
            'pstpf_wholesalePrice' => [
                'name' => $this->l('Cost price'),
                'active' => 0,
                'align' => 'center',
                'tab' => 2,
                'orderby' => true,
                'orderby_table' => 'sa',
                'orderby_field' => 'wholesale_price',
                'order_key' => 'wholesale_price',
            ],
            'pstpf_publicPrice' => [
                'name' => $this->l('Public price'),
                'active' => 0,
                'align' => 'center',
                'tab' => 2,
                'orderby' => false,
            ],
            'pstpf_shortDesc' => [
                'name' => $this->l('Short desc.'),
                'active' => 0,
                'orderby' => false,
                'hint' => $this->l('Product short description'),
            ],
            'pstpf_references' => [
                'name' => $this->l('References'),
                'active' => 0,
                'default' => false,
                'hint' => $this->l('Including combinations'),
            ],
            'pstpf_ean13' => [
                'name' => $this->l('EAN-13 / JAN'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'ean13',
            ],
            'pstpf_mpn' => [
                'name' => $this->l('MPN'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'mpn',
            ],
            'pstpf_upc' => [
                'name' => $this->l('UPC barcode'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'upc',
            ],
            'pstpf_isbn' => [
                'name' => $this->l('ISBN'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'isbn',
            ],
            'pstpf_features' => [
                'name' => $this->l('Features'),
                'active' => 0,
                'orderby' => false,
                'width' => '15%',
                'preline' => true,
            ],
            'pstpf_featureValues' => [
                'name' => $this->l('Feature values'),
                'active' => 0,
                'orderby' => false,
                'width' => '15%',
                'preline' => true,
            ],
            'pstpf_attributes' => [
                'name' => $this->l('Attributes'),
                'active' => 0,
                'orderby' => false,
            ],
            'pstpf_hasCombinations' => [
                'name' => $this->l('Combinations'),
                'active' => 0,
                'orderby' => false,
                'align' => 'center',
                'tab' => 3,
                'badge_class' => 'badge-secondary',
                'hint' => $this->l('Number of combinations of the product'),
            ],
            'pstpf_combinations' => [
                'name' => $this->l('Combination list'),
                'active' => 0,
                'orderby' => false,
                'preline' => true,
            ],
            'pstpf_type' => [
                'name' => $this->l('Type'),
                'active' => 0,
                'orderby' => false,
                'badge_class' => 'badge-primary',
                'hint' => $this->l('Product type: regular, pack or virtual'),
            ],
            'pstpf_discounts' => [
                'name' => $this->l('Discounts'),
                'active' => 0,
                'orderby' => false,
                'tab' => 2,
                'hint' => $this->l('List of active discounts (specific prices) of the product'),
            ],
            'pstpf_avail' => [
                'name' => $this->l('Available'),
                'active' => 0,
                'orderby' => false,
                'type' => 'yes_no',
                'align' => 'center',
                'hint' => $this->l('Product is available for order when it is in stock OR orders are allowed when out of stock.'),
            ],
            'pstpf_oosOrders' => [
                'name' => $this->l('OOS orders'),
                'active' => 0,
                'orderby' => false,
                'type' => 'yes_no',
                'align' => 'center',
                'tab' => 3,
                'hint' => $this->l('Allow orders when out of stock'),
            ],
            'pstpf_carriers' => [
                'name' => $this->l('Carriers'),
                'active' => 0,
                'orderby' => false,
                'tab' => 4,
                'hint' => $this->l('List of carriers available for the product'),
            ],
            'pstpf_tags' => [
                'name' => $this->l('Tags'),
                'active' => 0,
                'orderby' => false,
                'type' => 'tags',
                'tab' => 6,
            ],
            'pstpf_customizable' => [
                'name' => $this->l('Customizable'),
                'active' => 0,
                'type' => 'yes_no',
                'align' => 'center',
                'orderby' => true,
                'orderby_table' => 'sa',
                'orderby_field' => 'customizable',
            ],
            'pstpf_hasAttachments' => [
                'name' => $this->l('Attachments'),
                'active' => 0,
                'type' => 'yes',
                'align' => 'center',
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'cache_has_attachments',
            ],
            'pstpf_saleNumber' => [
                'name' => $this->l('Number of sales'),
                'active' => 0,
                'orderby' => false,
            ],
            'pstpf_saleNumberDetailed' => [
                'name' => $this->l('Detailed number of sales'),
                'active' => 0,
                'orderby' => false,
                'hint' => $this->l('Number of sales by combinations'),
                'preline' => true,
            ],
            'pstpf_visibility' => [
                'name' => $this->l('Visibility'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'sa',
                'orderby_field' => 'visibility',
            ],
            'pstpf_weight' => [
                'name' => $this->l('Weight'),
                'active' => 0,
                'orderby' => true,
                'orderby_table' => 'p',
                'orderby_field' => 'weight',
            ],
            'pstpf_dimensions' => [
                'name' => $this->l('Dimensions'),
                'active' => 0,
                'orderby' => false,
                'hint' => $this->l('Package dimension'),
            ],
            'pstpf_taxRule' => [
                'name' => $this->l('Tax rule'),
                'active' => 0,
                'orderby' => false,
            ],
            'pstpf_condition' => [
                'name' => $this->l('Condition'),
                'active' => 0,
                'tab' => 6,
                'orderby' => true,
                'orderby_table' => 'sa',
                'orderby_field' => 'condition',
            ],
            'pstpf_title' => [
                'name' => $this->l('Meta title'),
                'active' => 0,
                'orderby' => false,
            ],
            'pstpf_webOnly' => [
                'name' => $this->l('Web only'),
                'active' => 0,
                'type' => 'yes',
                'tab' => 6,
                'align' => 'center',
                'hint' => $this->l('Products which are not sold in your retail store'),
                'orderby' => true,
                'orderby_table' => 'sa',
                'orderby_field' => 'online_only',
            ],
            'pstpf_stockLocation' => [
                'name' => $this->l('Stock location'),
                'active' => 0,
                'orderby' => false,
                'preline' => true,
            ],
            'pstpf_categories' => [
                'name' => $this->l('Categories'),
                'active' => 0,
                'orderby' => false,
            ],
            'pstpf_margin' => [
                'name' => $this->l('Margin'),
                'active' => 0,
                'align' => 'center',
                'tab' => 2,
                'hint' => $this->l('The difference between the cost of a product and the price at which it is sold, expressed as a percentage'),
            ],
        ];

        // MPN is not avail until ps1.7.7
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            unset($module_columns['pstpf_mpn']);
        }
        // Public price already exists in PS 1.6
        if (version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
            unset($module_columns['pstpf_publicPrice']);
        }

        if ($this->checkUseV2()) {
            // add native filters
            $brands_tmp = $this->getManufacturersForFilter();
            $brands = [];
            foreach ($brands_tmp as $brand) {
                $brands[$brand['name']] = $brand['id_manufacturer'];
            }
            // "brand" column
            $module_columns['pstpf_manufacturer']['search'] = true;
            $module_columns['pstpf_manufacturer']['native_filter_type'] = 'choice';
            $module_columns['pstpf_manufacturer']['choice_data'] = $brands;
            $module_columns['pstpf_manufacturer']['multiple'] = true;

            // "visibility" column
            $visibility_options = [];
            foreach ($this->getVisibilityOptions('id') as $visibilityOption) {
                $visibility_options[$visibilityOption['name']] = $visibilityOption['id'];
            }
            $module_columns['pstpf_visibility']['search'] = true;
            $module_columns['pstpf_visibility']['native_filter_type'] = 'choice';
            $module_columns['pstpf_visibility']['choice_data'] = $visibility_options;
            $module_columns['pstpf_visibility']['multiple'] = true;
        }

        if ($only_pstpf) {
            $columns = $module_columns;
        } else {
            $columns = array_merge($default_columns, $module_columns);
        }

        // save to cache
        self::$cache_columns_raw_data[$cache_key] = $columns;

        return $columns;
    }

    public function renderQuickGuide()
    {
        $this->context->smarty->assign([
            'psv' => $this->getPSVersion(),
            'psvd' => $this->getPSVersion(true),
            'module_path' => $this->_path,
            'pstpf_products_link' => $this->context->link->getAdminLink('AdminProducts'),
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/guide.tpl');
    }

    public function getRedirectAfter()
    {
        $redirect_after = '';
        $pstpf = Tools::getValue('pstpf');

        // get input data also from standard filter inputs
        foreach ($this->getFilters() as $filter_name => $data) {
            if (Tools::isSubmit('productFilter_pstpf_' . $filter_name)) {
                $pstpf[$filter_name] = Tools::getValue('productFilter_pstpf_' . $filter_name);
            }
        }

        if (is_array($pstpf) && count($pstpf)
            && Tools::getValue('submitFilterproduct') && !Tools::getValue('id_product')
        ) {
            foreach ($pstpf as $name => $val) {
                if (is_array($val)) {
                    foreach ($val as $subname => $subval) {
                        $name = urlencode("pstpf[$name][$subname]");
                        if ($subval) {
                            $val = urlencode($subval);
                            $redirect_after .= '&' . $name . '=' . $val;
                        }
                    }
                } else {
                    $name = urlencode("pstpf[$name]");
                    if ($val) {
                        $val = urlencode($val);
                        $redirect_after .= '&' . $name . '=' . $val;
                    }
                }
            }
        }

        return $redirect_after;
    }

    protected function getFilterValue($values, $key, $default_reset = false)
    {
        $array_type_fields = $this->getArrayTypeFields();
        $is_pagination_request = false;
        $product_input = Tools::getValue('product');
        if (is_array($product_input) && count($product_input) == 2
            && isset($product_input['offset']) && isset($product_input['limit'])
        ) {
            $is_pagination_request = true;
        }
        
        if (Tools::isSubmit('pstpf_submit')
            || Tools::isSubmit('submitResetproduct')
            || $default_reset
            || (Tools::isSubmit('product') && !$is_pagination_request && $values)
        ) {
            if (is_array($values) && isset($values[$key])) {
                // 1. If filters submitted, try to get the value from request
                $value = $values[$key];
                if (is_array($value)) {
                    $value = array_combine($value, $value); // make keys the same as values
                    $this->filter_values_to_save[$key] = implode(':', $value);
                } else {
                    $this->filter_values_to_save[$key] = $value;

                    // if value should be an array, but it's just an ID
                    // at least when using dropdown filters in the header of the list
                    if (in_array($key, $array_type_fields) && is_numeric($value)) {
                        $value = [$value => $value];
                    } elseif (in_array($key, $array_type_fields) && $this->checkMultiNumericStringFormat($value)) {
                        $value = $this->multiNumericStringToArray($value);
                    }
                }

                return $value;
            } else {
                // 2. If no value, reset the saved data and return empty value
                $this->filter_values_to_save[$key] = '';

                return '';
            }
        } elseif (isset($values[$key])) {
            // 3. If filters aren't specified in request (i.e. it's newly opened Products page):
            $val = $values[$key];
            if ($val) {
                if (is_string($val) && in_array($key, $array_type_fields)) {
                    $tmp = explode(':', $val);
                    $val = array_combine($tmp, $tmp); // make keys the same as values
                }

                return $val;
            }
        }

        return '';
    }

    protected function checkMultiNumericStringFormat($string)
    {
        $parts = explode(':', $string);

        // Check if there are at least 2 parts
        if (count($parts) < 2) {
            return false;
        }

        // Check if all parts are numeric
        foreach ($parts as $part) {
            if (!is_numeric($part)) {
                return false;
            }
        }

        return true;
    }

    protected function multiNumericStringToArray($string)
    {
        // First check if the string is valid
        $parts = explode(':', $string);

        if (count($parts) < 2) {
            return false;
        }

        foreach ($parts as $part) {
            if (!is_numeric($part)) {
                return false;
            }
        }

        // Convert to array where keys = values
        $result = [];
        foreach ($parts as $number) {
            $result[$number] = $number;
        }

        return $result;
    }

    protected function getActiveFilters($skip_hidden = true)
    {
        $all_filters = $this->getFilters();
        $active_filters = [];

        foreach ($all_filters as $key => $filter) {
            if (!$skip_hidden || !(isset($filter['hidden']) && $filter['hidden'])) {
                if ($filter['value'] !== '') {
                    $active_filters[$key] = $filter['value'];
                }
            }
        }

        return $active_filters;
    }

    public function processDisplayAjaxBefore()
    {
        $reset_filter = Tools::getValue('reset_filter');
        if ($reset_filter) {
            $this->resetFilters();
        }
    }

    public function ajaxProcessSaveFilterSet()
    {
        // get input data
        $set_name = Tools::getValue('filter_set');
        $values = Tools::getValue('pstpf');
        $filters = Tools::getValue('filters');
        $columns = Tools::getValue('columns');
        $strict_data = $values['strict'];
        $order_by = Tools::getValue('productOrderby', $this->context->cookie->productsorderOrderby);
        $order_way = Tools::getValue('productOrderway', $this->context->cookie->productsorderOrderway);
        try {
            $admin_filter = Db::getInstance()->getValue(
                'SELECT `filter`
                 FROM `' . _DB_PREFIX_ . 'admin_filter`
                 WHERE `filter_id` = "product"
                  AND `employee` = ' . (int) $this->context->employee->id . '
                  AND `shop` = ' . (int) $this->context->shop->id
            );
        } catch (Exception $e) {
            $admin_filter = '';
        }

        // prepare initial data
        $set = PstProductFilterSet::getByName($set_name);
        $initial_filters = $this->getFiltersRawData();
        $initial_columns = $this->getColumns();

        // build resulting data
        // filters:
        foreach ($initial_filters as $key => &$filter) {
            // default values
            $filter['active'] = false;
            $filter['position'] = 99;
            $filter['value'] = '';
            $filter['strict'] = (!empty($strict_data[$key]) ? 1 : 0);
            // set values
            if (isset($filters[$key])) {
                $filter['active'] = $filters[$key]['active'];
                $filter['position'] = $filters[$key]['position'];
                if ($values[$key]) {
                    if (is_array($values[$key])) {
                        $filter['value'] = implode(':', $values[$key]);
                    } else {
                        $filter['value'] = $values[$key];
                    }
                }
            }
        }
        // columns:
        foreach ($initial_columns as $key => &$column) {
            $column['active'] = 0;
            if (isset($columns[$key])) {
                $column['active'] = $columns[$key];
            }
        }

        // save data
        $set->filters = $initial_filters;
        $set->columns = $initial_columns;
        $set->order_by = $order_by;
        $set->order_way = $order_way;
        $set->admin_filter = $admin_filter;
        if ($set->save()) {
            $this->setIdCurrentFilterSet($set->id);
            exit('1');
        }

        exit($this->l('Saving error'));
    }

    public function ajaxProcessDeleteSet()
    {
        $id_set = Tools::getValue('id_set');
        $set = new PstProductFilterSet($id_set);

        if ($id_set && Validate::isLoadedObject($set)) {
            if ($this->getIdCurrentFilterSet() == $id_set) {
                $this->setIdCurrentFilterSet(0);
            }
            $set->delete();
        }
    }

    public function checkUseSymfony()
    {
        return version_compare(_PS_VERSION_, '1.7.0.0', '>=');
    }

    public function checkUseV2()
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            // If the option was finally removed:
            if (!defined('PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2')) {
                return true;
            }

            // If the option still exists:
            return PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()
                ->get('prestashop.core.admin.feature_flag.repository')
                ->isEnabled(PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);
        }

        return false;
    }

    public function ajaxProcessRenderFilterBlock()
    {
        if (Tools::isSubmit('id_set')) {
            $this->restoreAdminFiltersFromSet();
        }

        exit($this->hookPstProductFilter());
    }

    public function getArrayTypeFields()
    {
        $result = [];
        foreach ($this->getFiltersRawData() as $key => $data) {
            if (isset($data['data']) && is_array($data['data'])) {
                $result[] = $key;
            }
        }

        return $result;
    }

    public function resetFilters()
    {
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'pstproductfilter`
             SET `value` = "", `strict` = 0
             WHERE `id_employee` = ' . (int) $this->context->employee->id
        );
        $this->setIdCurrentFilterSet(0);

        $adminFiltersRepository = $this->get('prestashop.core.admin.admin_filter.repository');
        $employeeId = $this->context->employee->id;
        $shopId = $this->context->shop->id;

        $filterId = 'product';
        $adminFilter = $adminFiltersRepository->findByEmployeeAndFilterId($employeeId, $shopId, $filterId);

        if (isset($adminFilter)) {
            $adminFiltersRepository->unsetFilters($adminFilter);
        }
    }

    public function hookActionDispatcherAfter($params = [])
    {
        // reset module filters on resetting regular PS product filters
        if (Tools::strpos($_SERVER['REQUEST_URI'], 'reset_search/product') !== false
            || Tools::strpos($_SERVER['REQUEST_URI'], 'products-v2/reset_grid_search') !== false
        ) {
            $this->resetFilters();
        }
    }

    protected function convertOrderKeyToSymfony($order_key)
    {
        $result = '""';
        $order_key = explode('!', $order_key);

        switch ($order_key[0]) {
            case 'a':
                $result = 'o.`' . $order_key[1] . '`';
                break;
            case 'address':
                $result = 'a.`' . $order_key[1] . '`';
                break;
            case 'c':
                $result = 'cu.`' . $order_key[1] . '`';
                break;
        }

        return $result;
    }

    protected function convertTableKeyToV2($table_key)
    {
        if ($this->checkUseV2()) {
            switch ($table_key) {
                case 'sa':
                    $table_key = 'ps';
                    break;
                case 'sav':
                    $table_key = 'sa';
                    break;
            }
        }

        return $table_key;
    }

    public function lCol($str, $domain = 'Admin.Global')
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return $this->trans($str, [], $domain);
        } else {
            return $this->l($str);
        }
    }

    protected function getPriceFinal($id_product)
    {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        $price_final = Product::getPriceStatic(
            $id_product,
            true,
            false, // important to avoid a bug in ps1.7.5
            (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION'),
            null,
            false,
            false,
            1,
            true,
            null,
            null,
            null,
            $nothing,
            true,
            true
        );

        $price_final = Tools::displayPrice($price_final, $currency);

        return $price_final;
    }

    protected function getManufacturersForFilter()
    {
        $id_lang = $this->context->language->id;

        $manufacturers = Manufacturer::getManufacturers(false, $id_lang, false, false, false, false, true);
        $default = [
            [
                'id_manufacturer' => -99,
                'name' => $this->l('-- no brand --'),
            ],
        ];

        return array_merge($default, $manufacturers);
    }

    protected function getSuppliersForFilter()
    {
        $id_lang = $this->context->language->id;

        $suppliers = Supplier::getSuppliers(false, $id_lang, false);
        $default = [
            [
                'id_supplier' => -99,
                'name' => $this->l('-- no supplier --'),
            ],
        ];

        return array_merge($default, $suppliers);
    }

    protected function prepareFilterValue($value)
    {
        // trim the value if it's a string
        $value = (is_string($value) ? trim($value) : $value);
        // make sure the value is correct if it's an array
        $value = (is_array($value) && !count($value) ? [-1] : $value);

        // Replace "-99" by 0 so the module can search zero values in the DB
        // and at the same time can normally display filter inputs without confusing with real empty arrays
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if ($item == -99) {
                    $value[$key] = 0;
                }
            }
        }

        return $value;
    }

    protected function getCostPriceMin()
    {
        $cached_price_min = Configuration::get($this->settings_prefix . 'COST_PRICE_MIN');
        $cached_price_min_time = (int) Configuration::get($this->settings_prefix . 'COST_PRICE_MIN_TIME');

        if ($cached_price_min === false || (time() - $cached_price_min_time > 3600)) {
            $cached_price_min = Db::getInstance()->getValue(
                'SELECT MIN(`wholesale_price`)
                 FROM `' . _DB_PREFIX_ . 'product_shop`
                 WHERE `id_shop` IN (' . implode(', ', Shop::getContextListShopID()) . ') '
            );
            Configuration::updateValue($this->settings_prefix . 'COST_PRICE_MIN', $cached_price_min);
            Configuration::updateValue($this->settings_prefix . 'COST_PRICE_MIN_TIME', time());
        }

        return (float) $cached_price_min;
    }

    protected function getCostPriceMax()
    {
        $cached_price_max = Configuration::get($this->settings_prefix . 'COST_PRICE_MAX');
        $cached_price_max_time = (int) Configuration::get($this->settings_prefix . 'COST_PRICE_MAX_TIME');

        if ($cached_price_max === false || (time() - $cached_price_max_time > 3600)) {
            $cached_price_max = Db::getInstance()->getValue(
                'SELECT MAX(`wholesale_price`)
                 FROM `' . _DB_PREFIX_ . 'product_shop`
                 WHERE `id_shop` IN (' . implode(', ', Shop::getContextListShopID()) . ') '
            );
            Configuration::updateValue($this->settings_prefix . 'COST_PRICE_MAX', $cached_price_max);
            Configuration::updateValue($this->settings_prefix . 'COST_PRICE_MAX_TIME', time());
        }

        return (float) max($cached_price_max, 1); // do not return 0 to keep the slider usable
    }

    public function hookActionProductSave($params)
    {
        // reset the cache on any product save
        Configuration::deleteByName($this->settings_prefix . 'COST_PRICE_MIN');
        Configuration::deleteByName($this->settings_prefix . 'COST_PRICE_MIN_TIME');
        Configuration::deleteByName($this->settings_prefix . 'COST_PRICE_MAX');
        Configuration::deleteByName($this->settings_prefix . 'COST_PRICE_MAX_TIME');
        Configuration::deleteByName($this->settings_prefix . 'WEIGHT_MAX');
        Configuration::deleteByName($this->settings_prefix . 'WEIGHT_MAX_TIME');
    }

    protected function getFeaturesForFilter()
    {
        $idLang = $this->context->language->id;
        $withShop = true;

        $result = Db::getInstance()->executeS('
		SELECT DISTINCT f.id_feature, f.*, fl.*
		FROM `' . _DB_PREFIX_ . 'feature` f
		' . ($withShop ? Shop::addSqlAssociation('feature', 'f') : '') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (f.`id_feature` = fl.`id_feature`
		 AND fl.`id_lang` = ' . (int) $idLang . ')
		ORDER BY fl.`name` ASC');

        $default = [
            [
                'id_feature' => -99,
                'name' => $this->l('-- without features --'),
            ],
        ];
        $result = array_merge($default, $result);

        return $result;
    }

    protected function getFeatureValuesForFilter($features = [])
    {
        $idLang = $this->context->language->id;
        if (!$features) {
            $features = $this->getFeaturesForFilter();
        }

        $result = [];
        foreach ($features as $feature) {
            $values = FeatureValue::getFeatureValuesWithLang($idLang, $feature['id_feature'], true);
            foreach ($values as $value) {
                $result[$feature['name']][$value['id_feature_value']] = [
                    'id_feature_value' => $value['id_feature_value'],
                    'name' => $value['value'],
                ];
            }
        }

        return $result;
    }

    protected function getAttributeGroupsForFilter()
    {
        return Db::getInstance()->executeS(
            'SELECT agl.`id_attribute_group`, agl.`name`
             FROM `' . _DB_PREFIX_ . 'attribute_group_lang` agl' .
            Shop::addSqlAssociation('attribute_group', 'agl') . '
             WHERE `id_lang` = ' . (int) $this->context->language->id . '
             GROUP BY agl.`id_attribute_group`
             ORDER BY agl.`name` ASC'
        );
    }

    protected function getAttributesForFilter($attribute_groups = [])
    {
        $id_lang = $this->context->language->id;
        if (!$attribute_groups) {
            $attribute_groups = $this->getAttributeGroupsForFilter();
        }

        $result = [];
        foreach ($attribute_groups as $attribute_group) {
            $attributes = Db::getInstance()->executeS(
                'SELECT al.`id_attribute`, al.`name`
                 FROM `' . _DB_PREFIX_ . 'attribute` a' .
                Shop::addSqlAssociation('attribute', 'a') . '
                 LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                  ON a.`id_attribute` = al.`id_attribute`
                 WHERE a.`id_attribute_group` = ' . (int) $attribute_group['id_attribute_group'] . '
                  AND al.`id_lang` = ' . (int) $id_lang
            );
            foreach ($attributes as $attribute) {
                $result[$attribute_group['name']][$attribute['id_attribute']] = [
                    'id_attribute' => $attribute['id_attribute'],
                    'name' => $attribute['name'],
                ];
            }
        }

        return $result;
    }

    public function getProductTypeName($type)
    {
        switch ($type) {
            case Product::PTYPE_PACK:
                return $this->l('Pack');
                break;
            case Product::PTYPE_VIRTUAL:
                return $this->l('Virtual');
                break;
        }

        return ''; // simple
    }

    protected function getTaxRulesGroupsForFilter()
    {
        $result = TaxRulesGroup::getTaxRulesGroups(false);

        $default = [
            [
                'id_tax_rules_group' => -99,
                'name' => $this->l('-- no tax rule --'),
            ],
        ];
        $result = array_merge($default, $result);

        return $result;
    }

    protected function getWeightMax()
    {
        $cached_weight_max = Configuration::get($this->settings_prefix . 'WEIGHT_MAX');
        $cached_weight_max_time = (int) Configuration::get($this->settings_prefix . 'WEIGHT_MAX_TIME');

        if ($cached_weight_max === false || (time() - $cached_weight_max_time > 3600)) {
            $cached_weight_max = Db::getInstance()->getValue('SELECT MAX(`weight`) FROM `' . _DB_PREFIX_ . 'product`');
            Configuration::updateValue($this->settings_prefix . 'WEIGHT_MAX', $cached_weight_max);
            Configuration::updateValue($this->settings_prefix . 'WEIGHT_MAX_TIME', time());
        }

        return $cached_weight_max ? (float) $cached_weight_max : 1;
    }

    protected function getNumberOfSalesMax()
    {
        $current_order_count = Db::getInstance()->getValue('SELECT MAX(`id_order`) FROM `' . _DB_PREFIX_ . 'orders`');
        $cached_order_count = Configuration::get($this->settings_prefix . 'ORDER_COUNT');
        $cached_nsales_max = Configuration::get($this->settings_prefix . 'NSALES_MAX');
        $cached_nsales_max_time = (int) Configuration::get($this->settings_prefix . 'NSALES_MAX_TIME');

        if ($cached_order_count != $current_order_count
            || $cached_nsales_max === false
            || (time() - $cached_nsales_max_time > 3600)
        ) {
            $cached_nsales_max = Db::getInstance()->getValue(
                'SELECT SUM(`product_quantity`) as summ
                 FROM `' . _DB_PREFIX_ . 'order_detail`
                 WHERE 1 ' . (Shop::isFeatureActive()
                    ? ' AND `id_shop` IN (' . implode(',', array_map('intval', Shop::getContextListShopID())) . ')'
                    : '') . '
                 GROUP BY `product_id`
                 ORDER BY `summ` DESC'
            );
            Configuration::updateValue($this->settings_prefix . 'NSALES_MAX', $cached_nsales_max);
            Configuration::updateValue($this->settings_prefix . 'NSALES_MAX_TIME', time());
            Configuration::updateValue($this->settings_prefix . 'ORDER_COUNT', $current_order_count);
        }

        return (float) $cached_nsales_max;
    }

    public function hookDisplayDashboardToolbarIcons($params)
    {
        if (Tools::getValue('controller') == 'AdminProducts') {
            $link =
                $this->context->link->getAdminLink('AdminModules', true, ['route' => 'pstproductfilter_export']);
            $this->context->smarty->assign([
                'pstpf_export_link' => $link,
            ]);

            return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/export.tpl');
        }
    }

    public function checkAnyProductExist()
    {
        return Db::getInstance()->getValue('SELECT 1 FROM `' . _DB_PREFIX_ . 'ps_product`');
    }

    public function hookDisplayAdminAfterHeader($params = [])
    {
        if ($this->checkUseV2()) {
            return $this->hookPstProductFilter($params);
        }
    }

    public function hookDisplayAdminEndContent($params = [])
    {
        if ($this->checkUseSymfony()
            && Tools::getValue('controller') == 'AdminProducts' && !Tools::isSubmit('id_product')
        ) {
            return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/end_content.tpl');
        }
    }

    protected function getCustomColumns()
    {
        $custom_columns = [];
        if ($this->custom_columns) {
            $custom_columns = preg_split('/\r\n|[\r\n]/', $this->custom_columns);
        }

        return $custom_columns;
    }

    protected function getIdCurrentFilterSet()
    {
        $id_shop = $this->context->shop->id;
        $id_employee = $this->context->employee->id;

        return Configuration::get($this->settings_prefix . 'ID_CURRENT_SET_' . (int) $id_shop . '_' . (int) $id_employee);
    }

    protected function setIdCurrentFilterSet($id)
    {
        $id_shop = $this->context->shop->id;
        $id_employee = $this->context->employee->id;

        Configuration::updateValue($this->settings_prefix . 'ID_CURRENT_SET_' . (int) $id_shop . '_' . (int) $id_employee, $id);
    }

    protected function saveFilterValues()
    {
        if ($this->filter_values_to_save) {
            // if this is a moment when we are loading a set
            if ($id_set = Tools::getValue('id_set')) {
                $set = new PstProductFilterSet($id_set);
                $set_filters = $set->getFilters();
                $strict_data = [];
                foreach ($set_filters as $filter_name => $set_filter) {
                    $strict_data[$filter_name] = $set_filter['strict'];
                }
            } else {
                $input_pstpf = Tools::getValue('pstpf');
                $strict_data = (is_array($input_pstpf) && isset($input_pstpf['strict']) ? $input_pstpf['strict'] : []);
            }

            $query =
                'UPDATE `' . _DB_PREFIX_ . 'pstproductfilter`
                 SET `value` = CASE `filter` ';

            // save the "value" column
            $query_parts = [];
            foreach ($this->filter_values_to_save as $name => $value) {
                $value = (is_string($value) ? $value : implode(':', $value));
                $query_parts[] = 'WHEN "' . pSQL($name) . '" THEN "' . pSQL($value) . '"';
            }
            $query .= implode(PHP_EOL, $query_parts);
            $query .= ' END ';

            // save the "strict" column:
            $query .= ', `strict` = CASE `filter` ';
            $query_parts = [];
            foreach ($this->getFiltersRawData() as $name => $column_data) {
                $strict =
                    (!empty($strict_data[$name]) && !empty($column_data['show_strict_match']) ? $strict_data[$name] : 0);
                $query_parts[] = 'WHEN "' . pSQL($name) . '" THEN ' . (int) $strict;
            }
            $query .= implode(PHP_EOL, $query_parts);
            $query .= ' END ';

            $query .= ' WHERE `id_employee` = ' . (int) $this->context->employee->id;

            Db::getInstance()->execute($query);
        }
    }

    public function ajaxProcessLoadFilterData()
    {
        $filter_name = Tools::getValue('filter_name');
        $filters = $this->getFilters();

        if (!empty($filters[$filter_name])) {
            $input = $filters[$filter_name];

            $this->context->smarty->assign([
                'psv' => $this->getPSVersion(),
                'filter_name' => $filter_name,
                'input' => $input,
                'pstpf_module' => $this,
            ]);

            exit($this->display(__FILE__, '_dropdown_data.tpl'));
        }
    }

    /**
     * Extracts from all filter settings only "strict match" options
     *
     * @return array
     */
    protected function getStrictSettings()
    {
        $result = [];
        $settings = $this->getFilters();

        foreach ($settings as $filter_name => $row) {
            $result[$filter_name] = !empty($row['strict']);
        }

        return $result;
    }

    public function hookActionProductGridQueryBuilderModifier($params)
    {
        $this->processDisplayAjaxBefore();
        $searchQueryBuilder = $params['search_query_builder'];
        $countQueryBuilder = $params['count_query_builder'];
        $searchCriteria = $params['search_criteria'];

        if (Tools::isSubmit('id_set')) {
            $this->getColumns(); // load and save columns if changed filter set
        }

        $filters = $this->getActiveFilters(false);
        $filters = (is_array($filters) ? array_filter($filters) : false);
        if (!$filters) {
            $filters = [];
        }
        // option "strict match"
        $strict_settings = $this->getStrictSettings();

        if ($filters) {
            foreach ($filters as $filter_name => $value) {
                $value = $this->prepareFilterValue($value, $filter_name);
                $func = 'searchProductsBy' . Tools::ucfirst($filter_name);
                $strict = !empty($strict_settings[$filter_name]);

                $query = '';
                if (is_callable([$this, $func])) {
                    $query = trim($this->$func($value, $strict));
                } elseif (method_exists('PstProductSearch', $func)) {
                    try {
                        $query = trim(PstProductSearch::getInstance(true)->$func($value, $strict));
                    } catch(Exception $e) {
                        $query = '';
                    }
                }
                if ($query) {
                    $searchQueryBuilder->andWhere($query);
                    $countQueryBuilder->andWhere($query);
                }
            }
        }

        $module_columns = $this->getColumns(true, true);
        // Check if sorting is by a column that not anymore active
        $order_by = $searchCriteria->getOrderBy();
        if ($order_by && strpos($order_by, 'pstpf_') !== false && !isset($module_columns[$order_by])) {
            $all_module_columns = $this->getColumns(true);
            $module_columns[$order_by] = $all_module_columns[$order_by];
        }
        
        foreach ($module_columns as $column => $data) {
            if (isset($data['order_key']) && $data['order_key']) {
                // exception for product columns because they are processed by their own rules
                $searchQueryBuilder->addSelect(
                    $this->convertOrderKeyToSymfony($data['order_key']) . ' AS `' . pSQL($column) . '`'
                );

                if ($column === $searchCriteria->getOrderBy()) {
                    $order_key = $this->convertOrderKeyToSymfony($data['order_key']);
                    $searchQueryBuilder->orderBy($order_key, $searchCriteria->getOrderWay());
                }
            } else {
                $searchQueryBuilder->addSelect('"" AS `' . pSQL($column) . '`');
            }
        }
    }

    public function hookActionProductGridDefinitionModifier($params)
    {
        // Init filters and their values because this is the very first hook call on the page load
        $this->getFilters();

        $definition = $params['definition'];
        $grid_columns = $definition->getColumns();
        $grid_filters = $definition->getFilters();

        $columnData = [];
        foreach ($grid_columns->toArray() as $item) {
            if (isset($item['id'])) {
                $columnData[$item['id']] = $item;
            }
        }

        $definition->getGridActions()
            ->add(
                (new PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction('new_action'))
                    ->setName($this->l('Export active columns'))
                    ->setIcon('cloud_download')
                    ->setOptions([
                        'route' => 'pstproductfilter_export_v2',
                    ])
            )
        ;

        $custom_columns = $this->getCustomColumns();
        $standard_columns = $this->getColumnsRawData(false, true);
        $default_columns_data_tmp = $grid_columns->toArray();
        $default_columns_data = [];
        foreach ($default_columns_data_tmp as $item) {
            $column_id = $item['id'];
            $default_columns_data[$column_id] = $item;
            if (!$this->disable_auto_custom_columns) {
                if (!isset($standard_columns[$column_id]) && !isset($custom_columns[$column_id])) {
                    if (!empty($item['name']) && $column_id != 'actions' && $column_id != 'position') {
                        $this->custom_columns .= ($this->custom_columns ? "\r\n" : '');
                        $this->custom_columns .= $column_id . '|' . $item['name'];
                        Configuration::updateValue($this->settings_prefix . 'custom_columns', $this->custom_columns);
                    }
                }
            }
        }

        $module_columns = $this->getColumns();
        // remove existing columns to be able to add them in correct order
        foreach ($module_columns as $column => $data) {
            $column = $this->getCorrectColumnKey($column);
            $grid_columns->remove($column);
            //            $grid_filters->remove($column);
            unset($columnData[$column]);
        }

        foreach ($module_columns as $column => $data) {
            if (!$data['active'] && Tools::strpos($column, 'pstpf_') === false) {
                // remove unselected columns
                $grid_filters->remove($column);
            } elseif (!isset($columnData[$column]) && $data['active']) {
                $sortable = (isset($data['orderby']) && $data['orderby']);
                if (isset($data['default']) && $data['default'] && isset($default_columns_data[$column])) {
                    $dc = $default_columns_data[$column];
                    $newColumn = $this->createColumnByType($column, $dc['type']);
                    $newColumn->setName($dc['name'])->setOptions($dc['options']);
                    $grid_columns->addBefore('actions', $newColumn);
                } else {
                    // add selected columns
                    $newColumn = new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn($column);
                    $clickable = (isset($data['clickable']) ? $data['clickable'] : true);
                    $newColumn
                        ->setName($data['name'])
                        ->setOptions(['field' => $column, 'sortable' => $sortable, 'clickable' => $clickable]);
                    $grid_columns->addBefore('actions', $newColumn);
                    // don't add the filter if search is disabled for this column
                    if (!(isset($data['search']) && !$data['search'])) {
                        if (!empty($data['native_filter_type'])) {
                            if ($data['native_filter_type'] == 'choice' && !empty($data['choice_data'])) {
                                $newFilter = new PrestaShop\PrestaShop\Core\Grid\Filter\Filter(
                                    $column,
                                    Symfony\Component\Form\Extension\Core\Type\ChoiceType::class
                                );
                                $newFilter
                                    ->setTypeOptions([
                                        'choices' => $data['choice_data'],
                                        'expanded' => false,
                                        'multiple' => (isset($data['multiple']) ? $data['multiple'] : 'multiple'),
                                        'required' => false,
                                        'choice_translation_domain' => false,
                                    ])
                                    ->setAssociatedColumn($column);
                                $grid_filters->add($newFilter);
                            }
                        // otherwise, regular text filter
                        } else {
                            $newFilter = new PrestaShop\PrestaShop\Core\Grid\Filter\Filter(
                                $column,
                                Symfony\Component\Form\Extension\Core\Type\TextType::class
                            );
                            $newFilter->setTypeOptions(['required' => false])->setAssociatedColumn($column);
                            $grid_filters->add($newFilter);
                        }
                    }
                }
            }
        }
    }

    public function hookActionProductGridDataModifier($params)
    {
        if (isset($params['data'])) {
            $filters = $this->getFilters();
            $modifiedRecords = $params['data']->getRecords()->all();
            $columns = $this->getColumns(true, true);
            $custom_columns = $this->getCustomColumns(true);

            foreach ($modifiedRecords as &$row) {
                if (isset($row['id_product']) && $row['id_product']) {
                    $product_object = new Product($row['id_product'], true, $this->context->language->id);
                    foreach ($row as $col => &$value) {
                        // if it's the module column
                        if (isset($columns[$col])) {
                            $func = 'displayProduct' . Tools::ucfirst(str_replace('pstpf_', '', $col));
                            if (is_callable([$this, $func])) {
                                $value = $this->$func($product_object);
                            } elseif (method_exists('PstProductColumn', $func)) {
                                $value = PstProductColumn::getInstance()->$func($product_object);
                            }
                        }
                    }

                    // if there are some custom columns in the settings
                    // check if they represent some default properties of $product
                    if ($custom_columns) {
                        foreach ($custom_columns as $custom_name) {
                            // if this column wasn't added previously - manually or by other module:
                            if (!isset($row[$custom_name])) {
                                // check the order fields:
                                if (property_exists($product_object, $custom_name)) {
                                    if (is_numeric($product_object->{$custom_name})
                                        && Tools::strpos($product_object->{$custom_name}, '.') !== false
                                    ) {
                                        $row[$custom_name] = $this->displayPrice($product_object->{$custom_name});
                                    }
                                }
                            }
                        }
                    }

                    // When using the attribute filter, try to show the filtered combinations prices:
                    if (!empty($filters['attribute']['value'])) {
                        $row = $this->showCombinationPrices($row, $filters);
                        $row = $this->showCombinationQty($row, $filters);
                    }
                }
            }

            $params['data'] = new PrestaShop\PrestaShop\Core\Grid\Data\GridData(
                new PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection($modifiedRecords),
                $params['data']->getRecordsTotal(),
                $params['data']->getQuery()
            );
        }
    }

    protected function createColumnByType($name, $type)
    {
        $type = Tools::ucfirst(Tools::toCamelCase($type));
        $dirs = glob(_PS_ROOT_DIR_ . '/src/Core/Grid/Column/Type/*', GLOB_ONLYDIR);
        array_walk($dirs, function (&$value, $key) { $value = basename($value) . '\\'; });
        array_unshift($dirs, '');

        foreach ($dirs as $dir) {
            $class = 'PrestaShop\PrestaShop\Core\Grid\Column\Type\\' . $dir . $type . 'Column';
            if (class_exists($class)) {
                return new $class($name);
            }
        }

        return new PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn($name);
    }

    protected function getCorrectColumnKey($key)
    {
        if ($this->checkUseV2()) {
            $v2_keys = [
                'name_category' => 'category',
                'price' => 'final_price_tax_excluded',
                'price_tax_excluded' => 'final_price_tax_excluded',
                'price_final' => 'price_tax_included',
                'sav_quantity' => 'quantity',
            ];

            if (isset($v2_keys[$key])) {
                return $v2_keys[$key];
            }
        }

        return $key;
    }

    public function displayPrice($price)
    {
        if (!is_numeric($price)) {
            return $price;
        }

        if (method_exists('Tools', 'getContextLocale')) {
            $context = Context::getContext();
            $currency = $context->currency;

            if (is_int($currency)) {
                $currency = Currency::getCurrencyInstance($currency);
            }

            $locale = Tools::getContextLocale($context);
            $currencyCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;

            return $locale->formatPrice($price, $currencyCode);
        } else {
            return Tools::displayPrice($price);
        }
    }

    protected function showCombinationPrices($row, $filters)
    {
        $key_price_tax_incl = ($this->checkUseV2() ? 'price_tax_included' : 'price_final');
        $key_price_tax_excl = ($this->checkUseV2() ? 'price_tax_excluded' : 'price');

        // Get the filter value
        $attributes = $filters['attribute']['value'];
        if (is_array($attributes) && array_filter($attributes)) {
            $combinations = [];
            $combi_prices_incl = [];
            $combi_prices_excl = [];
            // Get combinations by the attributes
            $strict = !empty($filters['attribute']['strict']);
            $tmp_combinations = $this->getProductAttributesIds($row['id_product'], false, $attributes, $strict);
            $combinations = array_merge($combinations, $tmp_combinations);

            // Get the combination prices
            foreach ($combinations as $id_combination) {
                $combination_price_incl = $this->displayPrice(
                    Product::getPriceStatic($row['id_product'], true, $id_combination, 6, null, false, false)
                );

                if ($combination_price_incl != $row[$key_price_tax_incl]) {
                    $combi_prices_incl[] = $combination_price_incl;
                }

                $combination_price_excl = $this->displayPrice(
                    Product::getPriceStatic($row['id_product'], false, $id_combination, 6, null, false, false)
                );
                if ($combination_price_excl != $row[$key_price_tax_excl]) {
                    $combi_prices_excl[] = $combination_price_excl;
                }
            }

            // Display the prices
            // Tax incl.:
            $prices_incl = array_unique($combi_prices_incl);
            $prices_incl = implode(', ', $prices_incl);
            if ($prices_incl && $prices_incl != $row[$key_price_tax_incl]) {
                $row[$key_price_tax_incl] .= ' (' . $prices_incl . ')';
            }

            // Tax excl.:
            $prices_excl = array_unique($combi_prices_excl);
            $prices_excl = implode(', ', $prices_excl);
            if ($prices_excl && $prices_excl != $row[$key_price_tax_excl]) {
                $row[$key_price_tax_excl] .= ' (' . $prices_excl . ')';
            }
        }

        return $row;
    }

    protected function showCombinationQty($row, $filters)
    {
        $key = 'quantity';

        // Get the filter value
        $attributes = $filters['attribute']['value'];
        if (is_array($attributes) && array_filter($attributes)) {
            $combinations = [];
            $combi_qty = [];
            // Get combinations by the attributes
            $strict = !empty($filters['attribute']['strict']);
            $tmp_combinations = $this->getProductAttributesIds($row['id_product'], false, $attributes, $strict);
            $combinations = array_merge($combinations, $tmp_combinations);

            // Get the combination quantities
            foreach ($combinations as $id_combination) {
                $qty = StockAvailable::getQuantityAvailableByProduct($row['id_product'], $id_combination);

                if ($qty != $row[$key]) {
                    $combi_qty[] = $qty;
                }
            }

            // Display the quantities
            $combi_qty = array_unique($combi_qty);
            $combi_qty = implode(', ', $combi_qty);
            if (($combi_qty || $combi_qty === '0') && $combi_qty != $row[$key]) {
                $row[$key] .= ' (' . $combi_qty . ')';
            }
        }

        return $row;
    }

    protected function getProductAttributesIds($id_product, $shop_only = false, $attributes = [], $strict = false)
    {
        $ipas = Db::getInstance()->executeS('
            SELECT pac.`id_product_attribute`
            FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa 
                 ON pa.`id_product_attribute` = pac.`id_product_attribute`' .
            ($shop_only ? Shop::addSqlAssociation('product_attribute', 'pa') : '') . '
            WHERE pa.`id_product` = ' . (int) $id_product .
            ($attributes
                ? ' AND pac.`id_attribute` IN (' . implode(',', array_map('intval', $attributes)) . ') '
                : '') .
            ' GROUP BY pac.`id_product_attribute` ' .
            ($strict
                ? ' HAVING COUNT(DISTINCT pac.`id_attribute`) >= ' . count($attributes)
                : '')
        );

        return array_column($ipas, 'id_product_attribute');
    }

    protected function getVisibilityOptions($key)
    {
        return [
            [$key => 1, 'name' => $this->l('Everywhere')],
            [$key => 2, 'name' => $this->l('Catalog')],
            [$key => 3, 'name' => $this->l('Search')],
            [$key => 4, 'name' => $this->l('Nowhere')],
        ];
    }

    /**
     * Restore values of the default filters using data from a filter set
     *
     * @return void
     */
    public function restoreAdminFiltersFromSet()
    {
        $id_set = Tools::getValue('id_set');
        if ($id_set) {
            $set = new PstProductFilterSet($id_set);
            if ($set->admin_filter) {
                try {
                    Db::getInstance()->execute(
                        'UPDATE `' . _DB_PREFIX_ . 'admin_filter`
                         SET `filter` = "' . pSQL($set->admin_filter) . '"
                         WHERE `filter_id` = "product"
                          AND `employee` = ' . (int) $this->context->employee->id . '
                          AND `shop` = ' . (int) $this->context->shop->id
                    );
                } catch (Exception $e) {
                    // ignore
                }
            }
        }
    }

    protected function getDefaultColumnPositions()
    {
        $result = [];
        $columns = $this->getColumnsRawData();

        $i = 0;
        foreach ($columns as $col => $data) {
            $result[$col] = $i;
            ++$i;
        }

        return $result;
    }
}
