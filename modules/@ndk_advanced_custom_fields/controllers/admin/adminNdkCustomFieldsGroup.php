<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */

class AdminNdkCustomFieldsGroupController extends ModuleAdminController
{
    public $bootstrap = true;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'ndk_customization_field_group';
        $this->className = 'NdkCfGroup';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->_defaultOrderBy = 'id_ndk_customization_field_group';

        $this->identifier = 'id_ndk_customization_field_group';
        Shop::addTableAssociation($this->table, ['type' => 'shop']);
        parent::__construct();
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];

        $this->modes = [
            ['id' => 0, 'name' => $this->l('standard')],
            ['id' => 1, 'name' => $this->l('step by step')],
        ];

        $this->fields_list = [
            'id_ndk_customization_field_group' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
            ],
            'name' => [
                'title' => $this->l('Name'),
                'filter_key' => 'b!name',
            ],
        ];

        $this->_select = 'a.*, pl.name as pname, cl.name as cname ';
        $this->_join =
            '
                        LEFT JOIN `' .
            _DB_PREFIX_ .
            'product` p ON (FIND_IN_SET( p.`id_product`, a.`products`)) 
                        LEFT JOIN `' .
            _DB_PREFIX_ .
            'product_lang` pl ON (pl.`id_product`= p.`id_product` AND pl.`id_lang` = ' .
            (int) Context::getContext()->language->id .
            ') 
                        
                        LEFT JOIN `' .
            _DB_PREFIX_ .
            'category` c ON (FIND_IN_SET( c.`id_category`, a.`categories`)) 
                        LEFT JOIN `' .
            _DB_PREFIX_ .
            'category_lang` cl ON (cl.`id_category`= c.`id_category` AND cl.`id_lang` = ' .
            (int) Context::getContext()->language->id .
            ')';

        $this->_orderBy = 'a.id_ndk_customization_field_group';
        $this->_group = 'GROUP BY a.id_ndk_customization_field_group';

        //$this->_orderWay = 'DESC';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->query('SET SQL_BIG_SELECTS=1');

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
            '
                                 SELECT DISTINCT a.id_ndk_customization_field_group, a.products, pl.`name` 
                                 FROM `' .
                _DB_PREFIX_ .
                'ndk_customization_field_group` a
                                 LEFT JOIN `' .
                _DB_PREFIX_ .
                'product` p ON (FIND_IN_SET( p.`id_product`, a.`products`)) 
                                 LEFT JOIN `' .
                _DB_PREFIX_ .
                'product_lang` pl ON (pl.`id_product`= p.`id_product` AND pl.`id_lang` = ' .
                (int) Context::getContext()->language->id .
                ') 
                                 WHERE TRIM(IFNULL(pl.name,\'\')) <> \'\' ORDER BY pl.name ASC '
        );

        $result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
            '
                                 SELECT DISTINCT a.id_ndk_customization_field_group, a.categories, cl.name 
                                 FROM ' .
                _DB_PREFIX_ .
                'ndk_customization_field_group a
                                 LEFT JOIN `' .
                _DB_PREFIX_ .
                'category` c ON (FIND_IN_SET( c.`id_category`, a.`categories`)) 
                                 LEFT JOIN `' .
                _DB_PREFIX_ .
                'category_lang` cl ON (cl.`id_category`= c.`id_category` AND cl.`id_lang` = ' .
                (int) Context::getContext()->language->id .
                ')
                                 WHERE TRIM(IFNULL(cl.name,\'\')) <> \'\' ORDER BY cl.name ASC '
        );

        $products_array = [];
        foreach ($result as $row) {
            $products_array[$row['products']] = $row['name'];
        }

        $products_array = array_unique($products_array);

        $categories_array = [];
        foreach ($result2 as $row2) {
            $categories_array[$row2['categories']] = $row2['name'];
        }

        $part1 = array_slice($this->fields_list, 0, 2);
        $part2 = array_slice($this->fields_list, 2);
        $part1['products'] = [
            'title' => $this->l('Product'),
            'type' => 'select',
            'list' => $products_array,
            'filter_key' => 'p!id_product',
            'order_key' => 'pname',
            'callback' => 'getTruncatedValue',
        ];
        $part1['categories'] = [
            'title' => $this->l('Categories'),
            'type' => 'select',
            'list' => $categories_array,
            'filter_key' => 'c!id_category',
            'order_key' => 'cname',
            'callback' => 'getTruncatedValue',
        ];

        $this->fields_list = array_merge($part1, $part2);
        parent::__construct();
    }

    public function getTruncatedValue($value)
    {
        return Tools::truncate($value, 120);
    }

    public function renderList()
    {
        if (Tools::getIsset($this->_filter) && trim($this->_filter) == '') {
            $this->_filter = $this->original_filter;
        }

        $this->addRowAction('edit');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $this->table = 'ndk_customization_field_group';
        $this->identifier = 'id_ndk_customization_field_group';

        $obj = $this->loadObject(true);

        $fields = NdkcfGroup::getFieldsLight((int) $obj->id);
        if (is_array($fields)) {
            array_push($fields, ['id' => '', 'name' => $this->l('none')]);
        }
        if ($obj = $this->loadObject(true)) {
            $selected_fields = explode(',', $obj->fields);
            $this->fields_value['fields[]'] = $selected_fields;
        }

        $products = NdkcfGroup::getProductsLight();
        array_push($products, ['id' => '', 'name' => $this->l('none')]);
        if ($obj = $this->loadObject(true)) {
            $selected_products = explode(',', $obj->products);
            $this->fields_value['products[]'] = $selected_products;
            if ($obj->options != '') {
                if ($obj = $this->loadObject(true)) {
                    $options = Tools::jsonDecode($obj->options, true);

                    foreach ($options as $k => $v) {
                        $this->fields_value['false_options[' . $k . ']'] = $v;
                    }
                }
            }
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Ndk Advanced custom Field'),
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'class' => 'setAdminName',
                    'name' => 'name',
                    'lang' => true,
                    'size' => 48,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Mode'),
                    'name' => 'mode',
                    'class' => 'visible-field visual-child',
                    'required' => true,
                    'options' => [
                        'query' => $this->modes,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('Select mode for this group'),
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Fields'),
                    'multiple' => true,
                    'name' => 'fields[]',
                    'class' => 'visible-field visual-child chosen',
                    'required' => true,
                    'options' => [
                        'query' => $fields,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('Select fields for this group'),
                ],
                [
                    'label' => $this->l('Display popup ?'),
                    'name' => 'false_options[popup_mode]',
                    'type' => 'select',
                    'options' => [
                        'query' => [
                            [
                                'id' => 0,
                                'name' => $this->l(
                                    'keep default configuration'
                                ),
                            ],
                            [
                                'id' => 'standard',
                                'name' => $this->l('Standard'),
                            ],
                            ['id' => 'popup', 'name' => $this->l('Popup')],
                            [
                                'id' => 'popup_mobile',
                                'name' => $this->l(
                                    'Popup on mobile and tablet'
                                ),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'class' => 'visible-field serialize_input',
                ],
                /*array(
                    'type' => 'select',
                    'label' => $this->l('Products'),
                    'multiple' => true,
                    'name' => 'products[]',
                    'class' => 'visible-field visual-child chosen',
                    'required' => true,
                    'options' => array(
                       'query' => $products,
                       'id' => 'id',
                       'name' => 'name'
                       ),
                    'desc' => $this->l('Activate group for theses products')
                    ),*/
            ],
        ];

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $selected_cat = [];
        if ($obj->id) {
            $cats = explode(',', $obj->categories);
            foreach ($cats as $key => $value) {
                $selected_cat[] = $value;
            }
        }
        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('categories-tree'); //The string in param is the ID used by the generated tree
        $tree
            ->setUseCheckBox(true)
            ->setUseSearch(true)
            ->setAttribute('is_category_filter', (int) $root->id)
            ->setRootCategory((int) $root->id)
            ->setSelectedCategories($selected_cat)
            ->setInputName('categories'); //Set the name of input. The option "name" of $fields_form doesn't seem to work with "categories_select" type
        $categoryTree = $tree->render();

        $selected_prods = [];
        if ($obj->id) {
            $prods = explode(',', $obj->products);
            foreach ($prods as $key => $value) {
                $selected_prods[] = $value;
            }
        }
        if (Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE') != 'all') {
            $root = Category::getRootCategory();
            $tree = new HelperTreeCategories('categories-tree-product'); //The string in param is the ID used by the generated tree
            $tree
                ->setUseCheckBox(true)
                ->setUseSearch(true)
                //->setFullTree(true)
                //->setChildrenOnly(true)
                ->setAttribute('is_category_filter', (int) $root->id)
                ->setRootCategory((int) $root->id)
                ->setSelectedCategories($selected_prods)
                ->setInputName('products'); //Set the name of input. The option "name" of $fields_form doesn't seem to work with "categories_select" type
            $categoryTreeProduct = $tree->render();
        }

        $this->addJquery();
        $this->addJqueryPlugin(['autocomplete', 'imgareaselect']);
        $this->addJs(
            _MODULE_DIR_ . 'ndk_advanced_custom_fields/views/js/admin.js'
        );
        $this->addCSS(
            _MODULE_DIR_ . 'ndk_advanced_custom_fields/views/css/admin.css',
            'all',
            false
        );
        $obj = $this->loadObject(true);

        $this->fields_form['input'][] = [
            'type' => 'categories_select',
            'label' => $this->l('Categories'),
            'desc' => $this->l('Activate this field for theses categories.'),
            'name' => 'categories',
            'category_tree' => $categoryTree, //This is the category_tree called in form.tpl
        ];

        if (Configuration::get('NDK_CATEGORY_ASSOCIATION_MODE') != 'all') {
            $this->fields_form['input'][] = [
                'type' => 'categories_select',
                'label' => $this->l('Products'),
                'desc' => $this->l('Activate this field for theses products.'),
                'name' => 'products',
                'category_tree' => $categoryTreeProduct, //This is the category_tree called in form.tpl
            ];

            $objprods = $obj->products;
            $objprods = NdkCf::getproductsLight($objprods);
            $this->context->smarty->assign(['objprods' => $objprods]);
            $searchbox = $this->context->smarty->fetch(
                _PS_ROOT_DIR_ .
                    '/modules/ndk_advanced_custom_fields/views/templates/admin/prodbox.tpl'
            );
        } else {
            $objprods = $obj->products;
            $objprods = NdkCf::getproductsLight($objprods);
            $this->context->smarty->assign(['objprods' => $objprods]);
            $searchbox = $this->context->smarty->fetch(
                _PS_ROOT_DIR_ .
                    '/modules/ndk_advanced_custom_fields/views/templates/admin/prodbox.tpl'
            );
            $this->fields_form['input'][] = [
                'type' => 'text',
                'label' => $this->l('Products'),
                'name' => 'products',
                'form_group_class' => '',
                'id' => 'products',
                'class' => 'product-result',
                'size' => 48,
                'desc' => $searchbox,
            ];
        }

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $this->fields_form['submit'] = [
            'title' => $this->l('Save'),
        ];
        /*$this->fields_form['buttons'] = array(
           'save-and-stay' => array(
              'title' => $this->l('Save add stay'),
              'name' => 'submitAdd'.$this->table.'AndStay',
              'type' => 'submit',
              'class' => 'btn btn-default pull-right',
              'icon' => 'process-icon-save'
           )
         );*/

        return parent::renderForm();
    }

    public function processUpdateNdkCFields($fields)
    {
        require_once _PS_MODULE_DIR_ .
            'ndk_advanced_custom_fields/models/ndkCf.php';
        require_once _PS_MODULE_DIR_ .
            'ndk_advanced_custom_fields/models/ndkCfValues.php';
        $obj = $this->loadObject(true);
        $this->clearAllCache();

        foreach ($fields as $key => $value) {
            if ((int) $value > 0) {
                $f = new NdkCf((int) $value);
                if (Tools::isSubmit('categories')) {
                    $f->categories = $_POST['categories'];
                } else {
                    $f->categories = $obj->categories;
                }
                if (Tools::isSubmit('products')) {
                    $products_send = Tools::getValue('products');

                    if (!is_array($products_send)) {
                        $products_send = explode(',', $products_send);
                    }
                    $products = array_unique($products_send);
                    $f->products = implode(',', $products);
                } else {
                    $f->products = $obj->products;
                }
                $f->save();

                $this->processUpdateNdkCFieldsShops($f->id, $obj->id);
            }
        }
    }

    public function processUpdateNdkCFieldsShops($id_field, $id_group)
    {
        $this->clearAllCache();
        Db::getInstance()->execute(
            'DELETE FROM ' .
                _DB_PREFIX_ .
                'ndk_customization_field_shop WHERE id_ndk_customization_field = ' .
                (int) $id_field
        );
        $sql_shops =
            'SELECT id_shop FROM ' .
            _DB_PREFIX_ .
            'ndk_customization_field_group_shop WHERE id_ndk_customization_field_group = ' .
            (int) $id_group;
        $shops = Db::getInstance()->executeS($sql_shops);
        foreach ($shops as $shop) {
            Db::getInstance()->execute(
                'INSERT INTO ' .
                    _DB_PREFIX_ .
                    'ndk_customization_field_shop (id_ndk_customization_field, id_shop) VALUES (' .
                    (int) $id_field .
                    ', ' .
                    (int) $shop['id_shop'] .
                    ')'
            );
        }
    }

    public function postProcess()
    {
        $this->clearAllCache();
        $obj = $this->loadObject(true);
        if (
            Tools::isSubmit('submitAddndk_customization_field_group') ||
            Tools::isSubmit('submitAddndk_customization_field_groupAndStay')
        ) {
            $selected_cat = [];
            if (Tools::isSubmit('categories')) {
                foreach (Tools::getValue('categories') as $row) {
                    $selected_cat[] = $row;
                }
            }
            $selected_cat = array_unique($selected_cat);
            $_POST['categories'] = implode(',', $selected_cat);

            $selected_prod = [];
            if (Tools::isSubmit('products')) {
                foreach (Tools::getValue('products') as $row) {
                    $selected_prod[] = $row;
                }
            }
            $selected_prod = array_unique($selected_prod);
            $_POST['products'] = implode(',', $selected_prod);

            if (Tools::isSubmit('fields')) {
                $this->processUpdateNdkCFields(Tools::getValue('fields'));
                $_POST['fields'] = implode(',', Tools::getValue('fields'));
            } else {
                $this->processUpdateNdkCFields(explode(',', $obj->fields));
            }
        } elseif (Tools::isSubmit('duplicate' . $this->table)) {
            $this->processDuplicate();
        }
        parent::postProcess();
    }

    public function processDuplicate()
    {
        $this->clearAllCache();
        $id = (int) Tools::getValue($this->identifier);
        $fields_mapping = [];
        $values_mapping = [];
        if (isset($id) && !empty($id)) {
            $object = new $this->className($id);
            if (Validate::isLoadedObject($object)) {
                $object_new = $object->duplicateObject();
                $fields = explode(',', $object->fields);
                $new_fields = [];
                foreach ($fields as $key => $value) {
                    $f = new NdkCf((int) $value);
                    $f_new = $f->duplicateObject();
                    $childs = $f->getValuesId();
                    $new_fields[] = $f_new->id;
                    $fields_mapping[0] = 0;
                    $fields_mapping[(int) $f->id] = $f_new->id;
                    foreach ($childs as $child) {
                        $value = new NdkCfValues($child['id']);
                        if (Validate::isLoadedObject($value)) {
                            $value_new = $value->duplicateObject();
                            $value_new->id_ndk_customization_field = $f_new->id;
                            $value_new->update();
                            $values_mapping[0] = 0;
                            $values_mapping[(int) $value->id] = $value_new->id;
                            NdkCfValues::duplicateSpecificPrice(
                                $f->id,
                                $f_new->id,
                                $value->id,
                                $value_new->id
                            );
                            NdkCfValues::duplicateImages(
                                $value->id,
                                $value_new->id
                            );
                            NdkCfValues::duplicateImagesSvg(
                                $value->id,
                                $value_new->id
                            );
                            NdkCfValues::duplicateMP3(
                                $value->id,
                                $value_new->id
                            );
                        }
                    }
                    $this->processUpdateNdkCFieldsShops(
                        $f_new->id,
                        $object_new->id
                    );
                }
                $object_new->fields = implode(',', $new_fields);
                $object_new->update();

                if (count($new_fields) > 0) {
                    NdkCfGroup::restoreInfluences(
                        $new_fields,
                        $fields_mapping,
                        $values_mapping
                    );
                    NdkCfGroup::restoreTargets(
                        $new_fields,
                        $fields_mapping,
                        $values_mapping
                    );
                }
            }
        }
    }

    protected function clearAllCache()
    {
        Db::getInstance()->Execute(
            'TRUNCATE TABLE `' . _DB_PREFIX_ . 'ndk_customization_field_cache`'
        );
    }
}
