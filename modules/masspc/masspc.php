<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class MassPc extends Module
{
    public $searchTool;

    public function __construct()
    {
        $this->name = 'masspc';
        $this->tab = 'administration';
        $this->version = '1.5.7';
        $this->author = 'MyPresta.eu';
        $this->displayName = $this->l('Mass products to categories');
        $this->description = $this->l('This module allows to assing / unasign products to categories in bulk');
        $this->mypresta_link = 'https://mypresta.eu/modules/administration-tools/mass-assign-move-products-to-categories.html';
        $this->bootstrap = true;
        $this->errors = false;
        $this->module_key = '87ab9c57883b99614ee87cacdac232fe';
        parent::__construct();
        $this->checkforupdates();
    }

    public function inconsistency($ret)
    {
        return;
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = masspcUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (masspcUpdate::version($this->version) < masspcUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = masspcUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (masspcUpdate::version($this->version) < masspcUpdate::version(masspcUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('backOfficeHeader') || !$this->installConfiguration()) {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        $this->searchTool = new searchToolmasspc($this->name, $this->tab);

        if (Tools::getValue('AddProductsCategoryBoxBulk', 'false') != 'false') {
            $products = array();
            foreach (explode(',', Tools::getValue('AddProductsCategoryBoxBulk')) AS $key) {
                $category = new Category($key, $this->context->language->id, $this->context->shop->id);
                $counter_category_products = $category->getProducts($this->context->language->id, 0, 0, 0, 0, true);
                $category_products = $category->getProducts($this->context->language->id, 0, $counter_category_products);
                foreach ($category_products AS $product) {
                    $products[$product['id_product']]['pname'] = $product['name'];
                    $products[$product['id_product']]['id_product'] = $product['id_product'];
                }
                unset($category, $category_products, $counter_category_products);
            }
            die(json_encode($products));
        }
        $this->postProcess();
        return $this->displayForm() . $this->checkforupdates(0, 1);
    }

    private function postProcess()
    {
        if (Tools::isSubmit('saveConfiguration')) {
            $this->MassUpdateAssociations();
            if ($this->errors == false) {
                Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&conf=4&token=' . Tools::getAdminTokenLite('AdminModules'));
            }
        }
    }

    private function MassUpdateProducts($products, $wtd, $categoriesTo)
    {
        if ($products != false && count($products) > 0) {
            foreach ($products AS $key => $value) {

                if ($wtd == 0) {
                    $product = new Product($value);
                } elseif ($wtd == 1) {
                    $product = new Product($value);
                    $product->addToCategories($categoriesTo);
                } elseif ($wtd == 2) {
                    $product = new Product($value);
                    foreach ($categoriesTo AS $catkey => $catval) {
                        $product->deleteCategory($catval);
                    }
                } elseif ($wtd == 3) {
                    $product = new Product($value);
                    $product->deleteCategories();
                    $product->addToCategories($categoriesTo);
                } elseif ($wtd == 4) {
                    $product = new Product($value);
                    $parents = array();

                    if ($categoriesTo != false) {
                        $product->addToCategories($categoriesTo);
                    }

                    foreach ($product->getCategories() as $cat) {
                        $category = new Category($cat, $this->context->language->id);
                        foreach ($category->getAllParents($this->context->language->id) AS $parent_category) {
                            if ($parent_category->id != 1 && !isset($parents[$parent_category->id])) {
                                $parents[$parent_category->id] = $parent_category->id;
                            }
                        }
                    }

                    if (count($parents) > 0) {
                        $product->addToCategories($parents);
                    }
                }

                if (Tools::getValue('change_def_cat', 'false') == '1') {
                    $this->massUpdateDefaultCat($product, Tools::getValue('categoryBoxDef'));
                }
            }
        }
    }

    private function massUpdateDefaultcat($product, $category)
    {
        $product->id_category_default = $category;
        $product->save();
    }

    private function getAllProductsFromCat($categories)
    {
        $array = array();
        if (count($categories) > 0 && $categories != false) {
            foreach ($categories AS $key => $value) {
                foreach (Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'category_product` WHERE id_category = ' . (int)$value . '') AS $kprod => $kval) {
                    $array[] = $kval['id_product'];
                }
            }
        }
        return $array;
    }

    private function getAllProductsFromFeature($fv)
    {
        $fv = explode(",", $fv);
        $array = array();
        if (count($fv) > 0 && $fv != false) {
            foreach ($fv AS $key => $value) {
                foreach (Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'feature_product` WHERE id_feature_value = ' . (int)$value) AS $fprod => $fval) {
                    $array[] = $fval['id_product'];
                }
            }
        }
        return $array;
    }

    private function getAllProductsFromAttribute($a)
    {
        $a = explode(",", $a);
        $array = array();
        if (count($a) > 0 && $a != false) {
            foreach ($a AS $key => $value) {
                foreach (Db::getInstance()->ExecuteS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac ON (pas.id_product_attribute = pac.id_product_attribute) WHERE pac.id_attribute="' . $value . '" GROUP BY pas.id_product') AS $arod => $aval) {
                    $array[] = $aval['id_product'];
                }
            }
        }
        return $array;
    }

    private function getAllProductsFromManufacturer($id_manufacturer)
    {
        $array = array();
        if ($id_manufacturer != false) {
            foreach (Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` WHERE id_manufacturer = ' . (int)$id_manufacturer) AS $kprod => $kval) {
                $array[] = $kval['id_product'];
            }
        }
        return $array;
    }

    private function MassUpdateManufacturers($id_manufacturer, $wtd, $categoriesTo)
    {
        $products = $this->getAllProductsFromManufacturer($id_manufacturer);
        $this->MassUpdateProducts($products, $wtd, $categoriesTo);
    }

    private function MassUpdateCategories($categories, $wtd, $categoriesTo)
    {
        $products = $this->getAllProductsFromCat($categories);
        $this->MassUpdateProducts($products, $wtd, $categoriesTo);
    }

    private function MassUpdateFeatures($fv, $wtd, $categoriesTo)
    {
        $products = $this->getAllProductsFromFeature($fv);
        $this->MassUpdateProducts($products, $wtd, $categoriesTo);
    }

    private function MassUpdateAttributes($a, $wtd, $categoriesTo)
    {
        $products = $this->getAllProductsFromAttribute($a);

        $this->MassUpdateProducts($products, $wtd, $categoriesTo);
    }

    private function MassUpdateAssociations()
    {
        if (Tools::getValue('MASSPC_TARGETPRODUCTS') == 'categories') {
            if (count(Tools::getValue('categoryBox')) > 0 && Tools::getValue('categoryBox', 'false') == 'false') {
                $this->errors[1] = $this->l('No products selected. Please go to step 1 and select products (or categories from which module will get products)');
            }
            $this->MassUpdateCategories(Tools::getValue('categoryBox'), Tools::getValue('wtd'), Tools::getValue('categoryBoxTo'));
        } elseif (Tools::getValue('MASSPC_TARGETPRODUCTS') == 'products') {
            if (count(Tools::getValue('masspc_products')) > 0 && Tools::getValue('masspc_products', 'false') == 'false') {
                $this->errors[1] = $this->l('No products selected. Please go to step 1 and select products (or categories from which module will get products)');
            }
            $this->MassUpdateProducts(Tools::getValue('masspc_products'), Tools::getValue('wtd'), Tools::getValue('categoryBoxTo'));
        } elseif (Tools::getValue('MASSPC_TARGETPRODUCTS') == 'manufacturers') {
            if (Tools::getValue('id_manufacturer', 'false') == 'false') {
                $this->errors[1] = $this->l('No products selected. Please go to step 1 and select products (or categories from which module will get products)');
            }
            $this->MassUpdateManufacturers(Tools::getValue('id_manufacturer'), Tools::getValue('wtd'), Tools::getValue('categoryBoxTo'));
        } elseif (Tools::getValue('MASSPC_TARGETPRODUCTS') == 'fv') {
            if (Tools::getValue('assign_fv', 'false') == 'false') {
                $this->errors[1] = $this->l('No features selected. Please go to step 1 and select products (or categories from which module will get products)');
            }
            $this->MassUpdateFeatures(Tools::getValue('assign_fv'), Tools::getValue('wtd'), Tools::getValue('categoryBoxTo'));
        } elseif (Tools::getValue('MASSPC_TARGETPRODUCTS') == 'attr') {
            if (Tools::getValue('assign_attr', 'false') == 'false') {
                $this->errors[1] = $this->l('No attributes selected. Please go to step 1 and select products (or categories from which module will get products)');
            }
            $this->MassUpdateAttributes(Tools::getValue('assign_attr'), Tools::getValue('wtd'), Tools::getValue('categoryBoxTo'));
        }

        if (count(Tools::getValue('categoryBoxTo')) > 0 && Tools::getValue('categoryBoxTo', 'false') == 'false' && Tools::getValue('wtd') != 0 && Tools::getValue('wtd') != 4) {
            $this->errors[2] = $this->l('No categories selected. Please go to step 3 and select categories');
        }
    }

    public function installConfiguration()
    {
        return true;
    }


    public function displayForm()
    {
        $token = Tools::getAdminTokenLite('AdminModules');
        $module_link = $this->context->link->getAdminLink('AdminModules', false);
        $url = $module_link . '&configure=masspc&token=' . $token . '&tab_module=administration&module_name=masspc';
        $category_tree = '';
        $root = Category::getRootCategory();

        if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            $tree = new HelperTreeCategories('associated-categories-tree', $this->l('categories'));
            $tree->setRootCategory($root->id);
            $tree->setUseCheckBox(true);
            $tree->setUseSearch(true);
            $category_tree = $tree->render();
            $tree2 = new HelperTreeCategories('associated-categories-tree-to', $this->l('categories'));
            $tree2->setRootCategory($root->id);
            $tree2->setInputName('categoryBoxTo');
            $tree2->setUseCheckBox(true);
            $tree2->setUseSearch(true);
            $category_tree_to = $tree2->render();
            $tree3 = new HelperTreeCategories('associated-categories-tree-def', $this->l('categories'));
            $tree3->setRootCategory($root->id);
            $tree3->setInputName('categoryBoxDef');
            $tree3->setUseCheckBox(false);
            $tree3->setUseSearch(true);
            $category_def = $tree3->render();
            $tree4 = new HelperTreeCategories('associated-categories-tree-bulk', $this->l('Categories'));
            $tree4->setRootCategory($root->id);
            $tree4->setInputName('categoryBoxBulk');
            $tree4->setUseCheckBox(true);
            $tree4->setUseSearch(true);
            $category_bulk = $tree4->render();
        } else {
            $tree = new Helper();
            $category_tree = $tree->renderCategoryTree(null, array(), 'categoryBox[]', true, true, array(), false, false);
            $category_tree_to = $tree->renderCategoryTree(null, array(), 'categoryBoxTo[]', true, true, array(), false, false);
            $category_def = $tree->renderCategoryTree(null, array(), 'categoryBoxDef[]', false, true, array(), false, false);
            $category_bulk = $tree->renderCategoryTree(null, array(), 'categoryBoxBulk[]', false, true, array(), false, false);
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Feature '),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('Search for feature'),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('By feature value'),
                        'name' => 'assign_fv',
                        'class' => 'assign_fv',
                        'desc' => $this->l('Search for feature value') . $this->searchTool->searchTool('feature_value', 'assign_fv', '', true, null),
                        'prefix' => $this->searchTool->searchTool('feature_value', 'assign_fv', ''),
                    ),
                ),
            ),
        );

        $fields_form2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Attribute '),
                    'icon' => 'icon-cogs'
                ),
                'description' => $this->l('Search for attribute'),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('By attribute'),
                        'name' => 'assign_attr',
                        'class' => 'assign_attr',
                        'desc' => $this->l('Search for attribute') . $this->searchTool->searchTool('attribute_value', 'assign_attr', '', true, null),
                        'prefix' => $this->searchTool->searchTool('attribute_value', 'assign_attr', ''),
                    ),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $features_form = $helper->generateForm(array($fields_form, $fields_form2));


        $this->context->smarty->assign(array(
            'msgTitle' => $this->l('Product added to the list'),
            'msgContents' => $this->l('Product added to the list of products'),
            'version' => _PS_VERSION_,
            'URL' => $url,
            'manufacturers' => Manufacturer::getManufacturers(false, Context::getContext()->language->id, false),
            'categories' => $category_tree,
            'categories_bulk' => $category_bulk,
            'categories_def' => $category_def,
            'categories_to' => $category_tree_to,
            'features_form' => $features_form,
            'errors' => $this->errors,
            'link' => $this->context->link
        ));
        return $this->searchTool->initTool() . $this->display(__FILE__, 'form.tpl');
    }

    public function getConfigFieldsValues()
    {
        return array(
            'assign_fv' => Tools::getValue('assign_fv', false),
            'assign_attr' => Tools::getValue('assign_attr', false),
        );
    }


    public function psversion($part = 1)
    {

        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }
}

class masspcUpdate extends masspc
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

if (file_exists(_PS_MODULE_DIR_ . 'masspc/lib/searchTool/searchTool.php')) {
    require_once _PS_MODULE_DIR_ . 'masspc/lib/searchTool/searchTool.php';
}


?>
