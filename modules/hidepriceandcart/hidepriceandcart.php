<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    FMM Modules
 *  @copyright FMM Modules 2021
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  @package   HIDEPRICEANDCART
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_MYSQL_ENGINE_')) {
    define('_MYSQL_ENGINE_', 'MyISAM');
}

class Hidepriceandcart extends Module
{
    public function __construct()
    {
        $this->name      = 'hidepriceandcart';
        $this->tab       = 'front_office_features';
        $this->version   = '1.1.1';
        $this->author    = 'FMM Modules';
        $this->bootstrap = true;
        parent::__construct();
        $this->module_key     = 'ef79e85fbd1c06f6576362b1f218c25b';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';
        $this->displayName    = $this->l('Hide Price & Add to Cart Button');
        $this->description    = $this->l('You can hide price and cart button on product pages and listing page.');
    }

    public function install()
    {
        $this->installConfiguration();

        if (!parent::install()
            || !$this->registerHook('displayProductListReviews')
            || !$this->registerHook('displayProductActions')
            || !$this->registerHook('header')
            || !$this->registerHook('actionAdminControllerSetMedia')
            || !$this->registerHook('displayProductButtons')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        $this->uninstallConfiguration();
        $this->unregisterHook('displayProductButtons');
        $this->unregisterHook('header');
        $this->unregisterHook('displayProductListReviews');
        $this->unregisterHook('actionAdminControllerSetMedia');
        parent::uninstall();
        return true;
    }

    public function getContent()
    {
        $this->registerHook('header');
        if (Tools::getValue('action') == 'getproducts') {
            $this->getSearchProducts();
            die;
        }
        if (Tools::isSubmit('submitConfiguration')) {
            $categories = (Tools::getValue('categoryBox')) ?
            implode(',', Tools::getValue('categoryBox')) : '';
            $hide_rules = (Tools::getValue('hide_rules_selected')) ?
            implode(',', Tools::getValue('hide_rules_selected')) : '';

            $products = (Tools::getValue('related_products')) ?
            implode(',', Tools::getValue('related_products')) : '';

            $excluded_products = (Tools::getValue('excluded_products')) ?
            implode(',', Tools::getValue('excluded_products')) : '';

            $cpgroups = (Tools::getValue('cpgroups')) ?
            implode(',', Tools::getValue('cpgroups')) : '';
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_ENABLE',
                (int) Tools::getValue('FMM_HIDEPRICEANDCART_ENABLE'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_RULES_LOGIN',
                (int) Tools::getValue('FMM_HIDEPRICEANDCART_RULES_LOGIN'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_ENABLE_CATEGORY_RULE',
                (int) Tools::getValue('FMM_ENABLE_CATEGORY_RULE'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_ENABLE_INC_PRO_RULE',
                (int) Tools::getValue('FMM_ENABLE_INC_PRO_RULE'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_ENABLE_EXC_PRO_RULE',
                (int) Tools::getValue('FMM_ENABLE_EXC_PRO_RULE'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_ENABLE_CUSTOMER_RULE',
                (int) Tools::getValue('FMM_ENABLE_CUSTOMER_RULE'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_PRICE_ENABLE',
                (int) Tools::getValue('FMM_HIDEPRICEANDCART_PRICE_ENABLE'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_CART_ENABLE',
                (int) Tools::getValue('FMM_HIDEPRICEANDCART_CART_ENABLE'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_CONTACT',
                (int) Tools::getValue('FMM_HIDEPRICEANDCART_CONTACT'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_BGCOLOR',
                (string) Tools::getValue('FMM_HIDEPRICEANDCART_BGCOLOR', '#FFA500'),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_RULES',
                (string) Tools::getValue(
                    'FMM_HIDEPRICEANDCART_RULES',
                    'notlogin'
                ),
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
                (string) $categories,
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'hide_rules',
                (string) $hide_rules,
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
                (string) $products,
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS',
                (string) $excluded_products,
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_RULES_GROUPS',
                (string) $cpgroups,
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            // saving multilingual content
            $hide_for_price_value = array('FMM_HIDEPRICEANDCART_VALUE' => array());
            foreach ($_POST as $key => $value) {
                if (preg_match('/FMM_HIDEPRICEANDCART_VALUE_/i', $key)) {
                    $id_lang                                                               = preg_split('/FMM_HIDEPRICEANDCART_VALUE_/i', $key);
                    $hide_for_price_value['FMM_HIDEPRICEANDCART_VALUE'][(int) $id_lang[1]] = $value;
                }
            }
            Configuration::updateValue(
                'FMM_HIDEPRICEANDCART_VALUE',
                $hide_for_price_value['FMM_HIDEPRICEANDCART_VALUE'],
                true,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $this->context->controller->confirmations[] = $this->l('Settings Saved Successfully');
        }
        return $this->renderForm();
    }

    public function installConfiguration()
    {
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_VALUE',
            array(
                $this->context->language->id => $this->l('Hide Price and Cart Button'),
            ),
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_ENABLE',
            1,
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_PRICE_ENABLE',
            1,
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_CART_ENABLE',
            1,
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_CONTACT',
            1,
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_BGCOLOR',
            '#00b1f0',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_RULES',
            'notlogin',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
            '',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
            '',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        Configuration::updateValue(
            'FMM_HIDEPRICEANDCART_RULES_GROUPS',
            '',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        return true;
    }

    public function uninstallConfiguration()
    {
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_VALUE');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_ENABLE');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_CONTACT');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_PRICE_ENABLE');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_PRICE_ENABLE');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_BGCOLOR');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_RULES_CATEGORIES');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_RULES_PRODUCTS');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_RULES_GROUPS');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_RULES');
        Configuration::deleteByName('FMM_HIDEPRICEANDCART_RULES_LOGIN');
        Configuration::deleteByName('FMM_ENABLE_CATEGORY_RULE');
        Configuration::deleteByName('FMM_ENABLE_INC_PRO_RULE');
        Configuration::deleteByName('FMM_ENABLE_EXC_PRO_RULE');
        Configuration::deleteByName('FMM_ENABLE_CUSTOMER_RULE');
        return true;
    }

    private function renderForm()
    {
        $switch     = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        $hide_rules = [
            [
                'id'   => 'FMM_HIDEPRICEANDCART_RULES_LOGIN',
                'name' => 'Customer login Rule',
            ],
            [
                'id'   => 'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
                'name' => 'Category Rule',
            ],
            [
                'id'   => 'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
                'name' => 'Include Product Rule',
            ],
            [
                'id'   => 'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS',
                'name' => 'Exclude Product Rule',
            ],
            [
                'id'   => 'FMM_HIDEPRICEANDCART_RULES_GROUPS',
                'name' => 'Group Rule',
            ],
        ];
        $fields_form = array(
            'form' => array(
                'tinymce' => true,
                'legend'  => array(
                    'title' => $this->l('Hide Price and Cart Button'),
                    'icon'  => 'icon-cogs',
                ),
                'input'   => array(
                    array(
                        'type'    => $switch,
                        'label'   => $this->l('Enable Module:'),
                        'desc'    => $this->l('Enable Module.'),
                        'name'    => 'FMM_HIDEPRICEANDCART_ENABLE',
                        'class'   => 't',
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_ENABLE_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_ENABLE_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type'    => $switch,
                        'label'   => $this->l('Hide Price:'),
                        'desc'    => $this->l(
                            'Enable option for hiding price and cart button on products.'
                        ),
                        'name'    => 'FMM_HIDEPRICEANDCART_PRICE_ENABLE',
                        'class'   => 't',
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_PRICE_ENABLE_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_PRICE_ENABLE_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type'    => $switch,
                        'label'   => $this->l('Hide Add to cart:'),
                        'desc'    => $this->l('Enable option to hide cart only.'),
                        'name'    => 'FMM_HIDEPRICEANDCART_CART_ENABLE',
                        'class'   => 't',
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_CART_ENABLE_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_CART_ENABLE_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),
                    array(
                        'type'  => 'color',
                        'label' => $this->l('Background Color :'),
                        'name'  => 'FMM_HIDEPRICEANDCART_BGCOLOR',
                    ),
                    array(
                        'type'    => $switch,
                        'label'   => $this->l('Display Contact Us:'),
                        'desc'    => $this->l('Display Contact Us Button'),
                        'name'    => 'FMM_HIDEPRICEANDCART_CONTACT',
                        'class'   => 't',
                        'is_bool' => true,
                        'values'  => array(
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_CONTACT_on',
                                'value' => 1,
                                'label' => $this->l('Yes'),
                            ),
                            array(
                                'id'    => 'FMM_HIDEPRICEANDCART_CONTACT_off',
                                'value' => 0,
                                'label' => $this->l('No'),
                            ),
                        ),
                    ),

                    array(
                        'type'         => 'textarea',
                        'lang'         => true,
                        'autoload_rte' => true,
                        'label'        => $this->l('Contact Us Button Text:'),
                        'name'         => 'FMM_HIDEPRICEANDCART_VALUE',
                    ),
                    array(
                        'type'  => 'text',
                        'label' => $this->l(' Rules for hiding'),
                        'name'  => 'FMM_HIDEPRICEANDCART_RULES',
                    ),
                    array(
                        'type'     => 'swap',
                        'label'    => $this->l('Add Rules For hidding with priority.'),
                        'name'     => 'hide_rules',
                        'desc'     => $this->l('Add the rules according to priority .Top most rule will have heighest prority. if the rule is not added here it will not be considerd'),
                        'required' => true,
                        'multiple' => true,
                        'options'  => array(
                            'query' => $hide_rules,
                            'id'    => 'id',
                            'name'  => 'name',
                        ),
                    ),
                ),
                'submit'  => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        $helper                           = new HelperForm();
        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $lang                             = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language    = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG'
        ) ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form     = array();
        $helper->identifier    = $this->identifier;
        $helper->table         = 'configuration';
        $helper->module        = $this;
        $helper->submit_action = 'submitConfiguration';
        $helper->currentIndex  = $this->context->link->getAdminLink('AdminModules', false) .
        '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' .
        $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $module_link   = $this->context->link->getAdminLink('AdminModules', false);
        $url           = $module_link . '&configure=' . $this->name . '&token=' . $helper->token .
        '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $id_lang                  = (int) $this->context->language->id;
        $login                    = Configuration::get('FMM_HIDEPRICEANDCART_RULES_LOGIN');
        $FMM_ENABLE_CATEGORY_RULE = Configuration::get('FMM_ENABLE_CATEGORY_RULE');
        $FMM_ENABLE_INC_PRO_RULE  = Configuration::get('FMM_ENABLE_INC_PRO_RULE');
        $FMM_ENABLE_EXC_PRO_RULE  = Configuration::get('FMM_ENABLE_EXC_PRO_RULE');
        $FMM_ENABLE_CUSTOMER_RULE = Configuration::get('FMM_ENABLE_CUSTOMER_RULE');
        $categories               = Category::getSimpleCategories($id_lang);
        $groups                   = Group::getGroups($this->context->language->id, $this->context->shop->id);
        $selected_cat             = (Configuration::get('FMM_HIDEPRICEANDCART_RULES_CATEGORIES')) ?
        explode(',', Configuration::get('FMM_HIDEPRICEANDCART_RULES_CATEGORIES')) : array();
        $products = (Configuration::get('FMM_HIDEPRICEANDCART_RULES_PRODUCTS')) ?
        explode(',', Configuration::get('FMM_HIDEPRICEANDCART_RULES_PRODUCTS')) : array();

        $excluded_products = (Configuration::get('FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS')) ?
        explode(',', Configuration::get('FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS')) : array();

        $cpGroups = (Configuration::get('FMM_HIDEPRICEANDCART_RULES_GROUPS')) ?
        explode(',', Configuration::get('FMM_HIDEPRICEANDCART_RULES_GROUPS')) : array();
        if (!empty($products)) {
            foreach ($products as &$product) {
                $product                       = new Product($product, true, (int) $this->context->language->id);
                $product->id_product_attribute = (int) Product::getDefaultAttribute(
                    $product->id
                ) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                $_cover = ((int) $product->id_product_attribute > 0) ?
                Product::getCombinationImageById(
                    (int) $product->id_product_attribute,
                    $this->context->language->id
                ) : Product::getCover($product->id);
                if (!is_array($_cover)) {
                    $_cover = Product::getCover($product->id);
                }
                $product->id_image = $_cover['id_image'];
            }
        }

        if (!empty($excluded_products)) {
            foreach ($excluded_products as &$product) {
                $product                       = new Product($product, true, (int) $this->context->language->id);
                $product->id_product_attribute = (int) Product::getDefaultAttribute(
                    $product->id
                ) > 0 ? (int) Product::getDefaultAttribute($product->id) : 0;
                $_cover = ((int) $product->id_product_attribute > 0) ?
                Product::getCombinationImageById(
                    (int) $product->id_product_attribute,
                    $this->context->language->id
                ) : Product::getCover($product->id);
                if (!is_array($_cover)) {
                    $_cover = Product::getCover($product->id);
                }
                $product->id_image = $_cover['id_image'];
            }
        }
        $data  = Configuration::get('FMM_HIDEPRICEANDCART_RULES');
        $ps_17 = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
        $this->context->smarty->assign('ps_17', $ps_17);
        $helper->tpl_vars = array(
            'fields_value'             => $this->getConfigFieldsValues(),
            'languages'                => $this->context->controller->getLanguages(),
            'id_language'              => $this->context->language->id,
            'categories'               => $categories,
            'products'                 => $products,
            'excluded_products'        => $excluded_products,
            'groups'                   => $groups,
            'cpGroups'                 => $cpGroups,
            'selected_cat'             => $selected_cat,
            'login'                    => $login,
            'FMM_ENABLE_CATEGORY_RULE' => $FMM_ENABLE_CATEGORY_RULE,
            'FMM_ENABLE_INC_PRO_RULE'  => $FMM_ENABLE_INC_PRO_RULE,
            'FMM_ENABLE_EXC_PRO_RULE'  => $FMM_ENABLE_EXC_PRO_RULE,
            'FMM_ENABLE_CUSTOMER_RULE' => $FMM_ENABLE_CUSTOMER_RULE,
            'data'                     => $data,
            'libpath'                  => $url . '&action=getproducts',
            'action_url'               => $url .
            '&action=getproducts&forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20',
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        $toShow = Configuration::get('hide_rules');
        if (empty($toShow)) {
            $toShow = array();
        } else {
            $toShow = explode(',', $toShow);
        }
        // $fields['hide_rules'] = $toShow;
        $languages = Language::getLanguages(false);
        $return    = array(
            'FMM_HIDEPRICEANDCART_ENABLE'           => (int) Tools::getValue(
                'FMM_HIDEPRICEANDCART_ENABLE',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_ENABLE',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ),
            'FMM_HIDEPRICEANDCART_PRICE_ENABLE'     => (int) Tools::getValue(
                'FMM_HIDEPRICEANDCART_PRICE_ENABLE',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_PRICE_ENABLE',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ),
            'hide_rules'                            => $toShow,

            'FMM_HIDEPRICEANDCART_CART_ENABLE'      => (int) Tools::getValue(
                'FMM_HIDEPRICEANDCART_CART_ENABLE',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_CART_ENABLE',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ),
            'FMM_HIDEPRICEANDCART_CONTACT'          => (int) Tools::getValue(
                'FMM_HIDEPRICEANDCART_CONTACT',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_CONTACT',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ),

            'FMM_HIDEPRICEANDCART_RULES'            => (int) Tools::getValue(
                'FMM_HIDEPRICEANDCART_RULES',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ),
            'FMM_HIDEPRICEANDCART_RULES_CATEGORIES' => pSQL(Tools::getValue(
                'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            )),
            'FMM_HIDEPRICEANDCART_RULES_PRODUCTS'   => pSQL(Tools::getValue(
                'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_PRODUCTS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            )),
            'FMM_HIDEPRICEANDCART_RULES_GROUPS'     => pSQL(Tools::getValue(
                'FMM_HIDEPRICEANDCART_RULES_GROUPS',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_GROUPS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            )),
            'FMM_HIDEPRICEANDCART_BGCOLOR'          => (string) Tools::getValue(
                'FMM_HIDEPRICEANDCART_BGCOLOR',
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_BGCOLOR',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            ));
        foreach ($languages as $lang) {
            $return['FMM_HIDEPRICEANDCART_VALUE'][(int) $lang['id_lang']] = Tools::getValue(
                'FMM_HIDEPRICEANDCART_VALUE' . (int) $lang['id_lang'],
                Configuration::get(
                    'FMM_HIDEPRICEANDCART_VALUE',
                    (int) $lang['id_lang'],
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )
            );
        }

        return $return;
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addJS($this->_path . 'views/js/fmmhidepricebo.js');
    }

    public function hookHeader()
    {
        $controller = Dispatcher::getInstance()->getController();

        $isEnable = (int) Configuration::get(
            'FMM_HIDEPRICEANDCART_ENABLE',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $is_price_enable = (int) Configuration::get(
            'FMM_HIDEPRICEANDCART_PRICE_ENABLE',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $is_cart_enable = (int) Configuration::get(
            'FMM_HIDEPRICEANDCART_CART_ENABLE',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        $is_contact_enable = (int) Configuration::get(
            'FMM_HIDEPRICEANDCART_CONTACT',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        if ($isEnable) {
            Media::addJsDef(
                array(
                    'fmm_is_price_enable'   => $is_price_enable,
                    'fmm_is_cart_enable'    => $is_cart_enable,
                    'fmm_is_contact_enable' => $is_contact_enable,
                    'fmm_message'           => '',
                    'fmm_bg_color'          => '',
                    'fmm_rule_applicable'   => false,
                    'fmm_controller'        => $controller,
                    'fmm_ps_version'        => _PS_VERSION_,
                    'fmm_contact_us'        => $this->context->link->getPageLink('contact'),
                    // 'fmm_contact_us_title'  => $this->l('Contact Us'),
                )
            );
            $this->context->controller->addJS($this->_path . '/views/js/fmmhideprice.js');
            $this->context->controller->addCSS($this->_path . '/views/css/fmmhideprice.css');
        }
    }

    public function hookDisplayProductActions($params)
    {
        $this->hookDisplayProductButtons($params);
    }

    public function hookDisplayProductButtons($params)
    {
        $isEnable = (int) Configuration::get(
            'FMM_HIDEPRICEANDCART_ENABLE',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        $message = (string) Configuration::get(
            'FMM_HIDEPRICEANDCART_VALUE',
            $this->context->language->id,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );
        $bg_color = (string) Configuration::get(
            'FMM_HIDEPRICEANDCART_BGCOLOR',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        $product_id = Tools::getValue('id_product');
        $rules      = Configuration::get('hide_rules');
        if (empty($rules)) {
            $rules = array();
        } else {
            $rules = explode(',', $rules);
        }
        $flag = false;

        foreach ($rules as $rule) {
            if ($rule == 'FMM_HIDEPRICEANDCART_RULES_LOGIN') {
                $hide      = Configuration::get('FMM_HIDEPRICEANDCART_RULES_LOGIN');
                $is_logged = $this->context->customer->isLogged();
                if ($hide == 1 && $is_logged == false) {
                    $flag = true;
                    break;
                }
            } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_CATEGORIES') {
                $selected_cat = (Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) ?
                explode(',', Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) : array();
                $cat_id = Product::getProductCategories($product_id);
                foreach ($cat_id as $cat_ids) {
                    if (isset($selected_cat) && in_array($cat_ids, $selected_cat)) {
                        $flag = true;
                        break;
                    }
                }
            } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_GROUPS') {
                $selected_groups = (Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_GROUPS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) ?
                explode(',', Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_GROUPS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) : array();
                $groups = Customer::getGroupsStatic($this->context->customer->id);
                foreach ($groups as $group_id) {
                    if (isset($selected_groups) && in_array($group_id, $selected_groups)) {
                        $flag = true;
                        break;
                    }
                }
            } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_PRODUCTS') {
                $products = (Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) ?
                explode(',', Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) : array();

                if (isset($products) && in_array($product_id, $products)) {
                    $flag = true;
                    break;
                }

            } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS') {
                $excluded_products = (Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) ?
                explode(',', Configuration::get(
                    'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                )) : array();

                if (isset($excluded_products) && in_array($product_id, $excluded_products)) {
                    $flag = false;
                    break;
                }

            }
            if ($flag == true) {
                break;
            }
        }
        $this->context->smarty->assign('message', str_replace('"', "", $message));
        $this->context->smarty->assign('bg_color', $bg_color);
        $this->context->smarty->assign('flag', $flag);

        if ($isEnable && $flag == true) {
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                return $this->display(__FILE__, 'productButtons17.tpl');
            } else {
                return $this->display(__FILE__, 'productButtons.tpl');
            }
        }
    }

    public function hookDisplayProductListReviews($params)
    {

        $controller = Dispatcher::getInstance()->getController();
        if ($controller != 'product') {

            $isEnable = Configuration::get(
                'FMM_HIDEPRICEANDCART_ENABLE',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $is_price_enable = (int) Configuration::get(
                'FMM_HIDEPRICEANDCART_PRICE_ENABLE',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $is_contact_enable = (int) Configuration::get(
                'FMM_HIDEPRICEANDCART_CONTACT',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            $message = Configuration::get(
                'FMM_HIDEPRICEANDCART_VALUE',
                $this->context->language->id,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $bg_color = (string) Configuration::get(
                'FMM_HIDEPRICEANDCART_BGCOLOR',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $product_id = $params['product']->id_product;
            } else {
                $product_id = $params['product']['id_product'];
            }
            $this->context->smarty->assign('message', $message);
            $this->context->smarty->assign('bg_color', $bg_color);
            $this->context->smarty->assign('is_price_enable', $is_price_enable);
            $this->context->smarty->assign('is_contact_enable', $is_contact_enable);

            $rules = Configuration::get('hide_rules');
            if (empty($rules)) {
                $rules = array();
            } else {
                $rules = explode(',', $rules);
            }
            $flag = false;

            foreach ($rules as $rule) {
                if ($rule == 'FMM_HIDEPRICEANDCART_RULES_LOGIN') {
                    $hide      = Configuration::get('FMM_HIDEPRICEANDCART_RULES_LOGIN');
                    $is_logged = $this->context->customer->isLogged();
                    if ($hide == 1 && $is_logged == false) {
                        $flag = true;
                        break;
                    }
                } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_CATEGORIES') {
                    $selected_cat = (Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) ?
                    explode(',', Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_CATEGORIES',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) : array();
                    $cat_id = Product::getProductCategories($product_id);
                    foreach ($cat_id as $cat_ids) {
                        if (isset($selected_cat) && in_array($cat_ids, $selected_cat)) {
                            $flag = true;
                            break;
                        }
                    }
                } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_GROUPS') {
                    $selected_groups = (Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_GROUPS',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) ?
                    explode(',', Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_GROUPS',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) : array();
                    $groups = Customer::getGroupsStatic($this->context->customer->id);
                    foreach ($groups as $group_id) {
                        if (isset($selected_groups) && in_array($group_id, $selected_groups)) {
                            $flag = true;
                            break;
                        }
                    }
                } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_PRODUCTS') {
                    $products = (Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) ?
                    explode(',', Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_PRODUCTS',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) : array();

                    if (isset($products) && in_array($product_id, $products)) {
                        $flag = true;
                        break;
                    }

                } elseif ($rule == 'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS') {
                    $excluded_products = (Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) ?
                    explode(',', Configuration::get(
                        'FMM_HIDEPRICEANDCART_RULES_EXC_PRODUCTS',
                        null,
                        $this->context->shop->id_shop_group,
                        $this->context->shop->id
                    )) : array();

                    if (isset($excluded_products) && in_array($product_id, $excluded_products)) {
                        $flag = false;
                        break;
                    }

                }
                if ($flag == true) {
                    break;
                }
            }
            $this->context->smarty->assign('flag', $flag);
            $this->context->smarty->assign('fmm_contact_us', $this->context->link->getPageLink('contact'));
            if ($isEnable) {
                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                    return $this->display(__FILE__, 'productListReviews_17.tpl');
                } else {
                    return $this->display(__FILE__, 'productListReviews.tpl');
                }
            }
        }
    }

    protected function getSearchProducts()
    {
        $query = Tools::getValue('q', false);
        if (!$query || $query == '' || Tools::strlen($query) < 1) {
            die(json_encode($this->l('Found Nothing.')));
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $forceJson          = Tools::getValue('forceJson', false);
        $disableCombination = Tools::getValue('disableCombination', false);
        $excludeVirtuals    = (bool) Tools::getValue('excludeVirtuals', true);
        $exclude_packs      = (bool) Tools::getValue('exclude_packs', true);

        $context = Context::getContext();

        $sql = 'SELECT
        p.`id_product`,
        pl.`link_rewrite`,
        p.`reference`,
        pl.`name`,
        image_shop.`id_image` id_image,
        il.`legend`,
        p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ .
        'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' .
        (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' .
        (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ .
        'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' .
        (int) $context->language->id . ')
                WHERE (pl.name LIKE \'%' . pSQL($query) .
        '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            ($excludeVirtuals ?
            'AND NOT EXISTS (
                SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : ''
        ) .
            ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
            ' GROUP BY p.id_product';

        $items = Db::getInstance()->executeS($sql);
        if ($items && ($disableCombination || $excludeIds)) {
            $results = array();
            foreach ($items as $item) {
                if (!$forceJson) {
                    $item['name'] = str_replace('|', '&#124;', $item['name']);
                    $results[]    = trim($item['name']) . (!empty($item['reference']) ? ' (ref: ' .
                        $item['reference'] . ')' : '') . '|' . (int) $item['id_product'];
                } else {
                    $cover     = Product::getCover($item['id_product']);
                    $results[] = array(
                        'id'    => $item['id_product'],
                        'name'  => $item['name'] . (!empty($item['reference']) ? ' (ref: ' .
                            $item['reference'] . ')' : ''),
                        'ref'   => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                (($item['id_image']) ? $item['id_image'] : $cover['id_image']),
                                $this->getFormatedName('home')
                            )
                        ),
                    );
                }
            }

            if (!$forceJson) {
                echo implode("\n", $results);
            } else {
                echo json_encode($results);
            }
        } elseif ($items) {
            // packs
            $results = array();
            foreach ($items as $item) {
                // check if product have combination
                if (Combination::isFeatureActive() && $item['cache_default_attribute']) {
                    $sql = 'SELECT
                    pa.`id_product_attribute`,
                    pa.`reference`,
                    ag.`id_attribute_group`,
                    pai.`id_image`,
                    agl.`name` AS group_name,
                    al.`name` AS attribute_name,
                                a.`id_attribute`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' .
                    (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'attribute_group_lang` agl ON (
                        ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' .
                    (int) $context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ .
                    'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                            WHERE pa.`id_product` = ' . (int) $item['id_product'] . '
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                    $combinations = Db::getInstance()->executeS($sql);
                    if (!empty($combinations)) {
                        foreach ($combinations as $combination) {
                            $cover                                                                 = Product::getCover($item['id_product']);
                            $results[$combination['id_product_attribute']]['id']                   = $item['id_product'];
                            $results[$combination['id_product_attribute']]['id_product_attribute'] = $combination[
                                'id_product_attribute'
                            ];
                            !empty($results[$combination['id_product_attribute']]['name']) ?
                            $results[$combination['id_product_attribute']]['name'] .= ' ' .
                            $combination['group_name'] . '-' . $combination['attribute_name']
                            : $results[$combination['id_product_attribute']]['name'] = $item['name'] .
                                ' ' . $combination['group_name'] . '-' . $combination['attribute_name'];
                            if (!empty($combination['reference'])) {
                                $results[$combination['id_product_attribute']]['ref'] = $combination['reference'];
                            } else {
                                $results[$combination['id_product_attribute']]['ref'] = !empty($item['reference']) ?
                                $item['reference'] : '';
                            }
                            if (empty($results[$combination['id_product_attribute']]['image'])) {
                                $results[$combination['id_product_attribute']]['image'] = str_replace(
                                    'http://',
                                    Tools::getShopProtocol(),
                                    $context->link->getImageLink(
                                        $item['link_rewrite'],
                                        (($combination['id_image']) ? $combination['id_image'] : $cover['id_image']),
                                        $this->getFormatedName('home')
                                    )
                                );
                            }
                        }
                    } else {
                        $results[] = array(
                            'id'    => $item['id_product'],
                            'name'  => $item['name'],
                            'ref'   => (!empty($item['reference']) ? $item['reference'] : ''),
                            'image' => str_replace(
                                'http://',
                                Tools::getShopProtocol(),
                                $context->link->getImageLink(
                                    $item['link_rewrite'],
                                    $item['id_image'],
                                    $this->getFormatedName('home')
                                )
                            ),
                        );
                    }
                } else {
                    $results[] = array(
                        'id'    => $item['id_product'],
                        'name'  => $item['name'],
                        'ref'   => (!empty($item['reference']) ? $item['reference'] : ''),
                        'image' => str_replace(
                            'http://',
                            Tools::getShopProtocol(),
                            $context->link->getImageLink(
                                $item['link_rewrite'],
                                $item['id_image'],
                                $this->getFormatedName('home')
                            )
                        ),
                    );
                }
            }
            echo json_encode(array_values($results));
        } else {
            echo json_encode(array());
        }
    }

    public function getFormatedName($name)
    {
        $theme_name              = Context::getContext()->shop->theme_name;
        $name_without_theme_name = str_replace(array('_' .
            $theme_name, $theme_name . '_'), '', $name);
        //check if the theme name is already in $name if yes only return $name
        if (strstr($name, $theme_name) && ImageType::getByNameNType($name, 'products')) {
            return $name;
        } elseif (ImageType::getByNameNType($name_without_theme_name . '_' .
            $theme_name, 'products')) {
            return $name_without_theme_name . '_' . $theme_name;
        } elseif (ImageType::getByNameNType($theme_name . '_' .
            $name_without_theme_name, 'products')) {
            return $theme_name . '_' . $name_without_theme_name;
        } else {
            return $name_without_theme_name . '_default';
        }
    }
}
