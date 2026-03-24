<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */
class AdminNdkCustomFieldsController extends ModuleAdminController
{
    public $bootstrap = true;
    protected $position_identifier = 'id_attribute_group';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'ndk_customization_field';
        $this->className = 'NdkCf';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->_defaultOrderBy = 'position';
        $this->_default_pagination = '30';
        $this->module = Module::getInstanceByName('ndk_advanced_custom_fields');
        parent::__construct();
        $this->types = $this->module->types;

        $this->open_statuses = [
            ['id' => 0, 'name' => $this->l('default')],
            ['id' => 1, 'name' => $this->l('opened')],
            ['id' => 2, 'name' => $this->l('closed')],
            ['id' => 3, 'name' => $this->l('hidden')],
        ];

        if (!Configuration::get('NDKCF_BOT_LOADED')) {
            Configuration::updateValue('NDKCF_BOT_DOMAIN', 'docs.ndk-design');
            Configuration::updateValue('NDKCF_BOT_EXT', 'fr');
            Configuration::updateValue('NDKCF_BOT_LOADED', true);
        }

        $this->identifier = 'id_ndk_customization_field';
        Shop::addTableAssociation($this->table, ['type' => 'shop']);

        $this->fieldImageSettings = [
            ['name' => 'image', 'dir' => 'scenes/ndkcf/'],
            ['name' => 'picto', 'dir' => 'scenes/ndkcf/pictos/'],
            ['name' => 'mask', 'dir' => 'scenes/ndkcf/mask/'],
            ['name' => 'thumb', 'dir' => 'scenes/ndkcf/thumbs'],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ],
            'add_prod_cat' => [
                'text' => $this->l('Add entire category'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('popup'),
            ],
        ];

        $t_array = [];
        foreach ($this->types as $row) {
            $t_array[$row['id_type']] = $row['name'];
        }

        /*$this->fields_list['picto'] = array(
                 'title' => $this->l('picto'),
                 'align' => 'center',
                 'width' => 70,
                 'orderby' => false,
                 'filter' => false,
                 'search' => false,
                 'callback' => 'getPicto',
                 'image' => 'scenes/ndkcf/pictos/',
        );*/

        $this->fields_list = [
            /*'id_ndk_customization_field' => array(
               'title' => $this->l('picto'),
                  'align' => 'center',
                  'width' => 70,
                  'orderby' => false,
                  'filter' => false,
                  'search' => false,
                  'callback' => 'getPicto',
                  'image' => 'scenes/ndkcf/pictos/',

            ),*/
            'id_ndk_customization_field' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
            ],
            'admin_name' => [
                'title' => $this->l('Admin name'),
                'filter_key' => 'b!admin_name',
            ],

            'name' => [
                'title' => $this->l('Public name'),
                'filter_key' => 'b!name',
            ],

            'price' => [
                'title' => $this->l('Price'),
                'filter_key' => 'a!price',
            ],
            // 'price_per_caracter' => [
            //     'title' => $this->l('Price/chars.'),
            //     'filter_key' => 'a!price_per_caracter',
            // ],
            'type' => [
                'title' => $this->l('Type'),
                'type' => 'select',
                'list' => $t_array,
                'filter_key' => 'a!type',
                'callback' => 'getTypeName',
            ],
            /*'required' => array(
               'title' => $this->l('Required'),
               'active' => 'required',
               'type' => 'bool',
               'class' => 'fixed-width-xs',
               'align' => 'center',
               'orderby' => false
            ),
            'is_visual' => array(
               'title' => $this->l('Visual effect'),
               'active' => 'is_visual',
               'type' => 'bool',
               'class' => 'fixed-width-xs',
               'align' => 'center',
               'orderby' => false
            ),*/
            /*'resizeable' => array(
               'title' => $this->l('Resizeable'),
               'active' => 'resizeable',
               'type' => 'bool',
               'class' => 'fixed-width-xs',
               'align' => 'center',
               'orderby' => false
            ),
            'draggable' => array(
               'title' => $this->l('Draggable'),
               'active' => 'draggable',
               'type' => 'bool',
               'class' => 'fixed-width-xs',
               'align' => 'center',
               'orderby' => false
            ),
            'rotateable' => array(
               'title' => $this->l('Rotateable'),
               'active' => 'rotateable',
               'type' => 'bool',
               'class' => 'fixed-width-xs',
               'align' => 'center',
               'orderby' => false
            ),*/
            'required' => [
                'title' => $this->l('Required'),
                'active' => 'required',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'orderby' => false,
            ],
            'position' => [
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs editable-value set_positionndk_customization_field',
            ],
            'ref_position' => [
                'title' => $this->l('Reference position'),
                'filter_key' => 'a!ref_position',
                'align' => 'center',
                'class' => 'fixed-width-xs editable-value set_ref_positionndk_customization_field',
            ],
            'zindex' => [
                'title' => $this->l('Z-index'),
                'filter_key' => 'a!zindex',
                'align' => 'center',
                'class' => 'fixed-width-xs editable-value set_zindexndk_customization_field',
            ],
        ];

        $this->addJquery();
        //$this->addJs(_MODULE_DIR_.'ndk_advanced_custom_fields/views/js/admin.js' );
        $this->addCSS(
            _MODULE_DIR_.'ndk_advanced_custom_fields/views/css/admin.css',
            'all',
            false
        );

        //Media::addJsDef(['tryThisFilter' => $this->l('↑ Try this filter ↑')]);
        if (!Tools::getValue('id_ndk_customization_field')) {
            $this->_select =
                'a.*, pl.name as pname, cl.name as cname, gl.name as groupname, g.id_ndk_customization_field_group ';
            $this->_join =
                '
            LEFT JOIN `'.
                _DB_PREFIX_.
                'product` p ON (FIND_IN_SET( p.`id_product`, a.`products`)) 
            LEFT JOIN `'.
                _DB_PREFIX_.
                'product_lang` pl ON (pl.`id_product`= p.`id_product` AND pl.`id_lang` = '.
                (int) Context::getContext()->language->id.
                ') 
            
            LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_group` g ON (FIND_IN_SET( a.`id_ndk_customization_field`, g.`fields`)) 
            LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field_group_lang` gl ON (gl.`id_ndk_customization_field_group`= g.`id_ndk_customization_field_group` AND gl.`id_lang` = '.
                (int) Context::getContext()->language->id.
                ') 
            
            LEFT JOIN `'.
                _DB_PREFIX_.
                'category` c ON (FIND_IN_SET( c.`id_category`, a.`categories`)) 
            LEFT JOIN `'.
                _DB_PREFIX_.
                'category_lang` cl ON (cl.`id_category`= c.`id_category` AND cl.`id_lang` = '.
                (int) Context::getContext()->language->id.
                ')';

            $this->_orderBy = 'a.position';
            $this->_group = 'GROUP BY a.id_ndk_customization_field';

            //$this->_orderWay = 'DESC';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->query('SET SQL_BIG_SELECTS=1');

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
                '
                     SELECT DISTINCT a.id_ndk_customization_field, a.products, pl.`name` 
                     FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field` a
                     LEFT JOIN `'.
                    _DB_PREFIX_.
                    'product` p ON (FIND_IN_SET( p.`id_product`, a.`products`)) 
                     LEFT JOIN `'.
                    _DB_PREFIX_.
                    'product_lang` pl ON (pl.`id_product`= p.`id_product` AND pl.`id_lang` = '.
                    (int) Context::getContext()->language->id.
                    ') 
                     WHERE TRIM(IFNULL(pl.name,\'\')) <> \'\' ORDER BY pl.name ASC '
            );

            $result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
                '
                     SELECT DISTINCT a.id_ndk_customization_field, a.categories, cl.name 
                     FROM '.
                    _DB_PREFIX_.
                    'ndk_customization_field a
                     LEFT JOIN `'.
                    _DB_PREFIX_.
                    'category` c ON (FIND_IN_SET( c.`id_category`, a.`categories`)) 
                     LEFT JOIN `'.
                    _DB_PREFIX_.
                    'category_lang` cl ON (cl.`id_category`= c.`id_category` AND cl.`id_lang` = '.
                    (int) Context::getContext()->language->id.
                    ')
                     WHERE TRIM(IFNULL(cl.name,\'\')) <> \'\' ORDER BY cl.name ASC '
            );

            $result3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
                '
                     SELECT DISTINCT a.id_ndk_customization_field, g.id_ndk_customization_field_group, gl.`name` 
                     FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field` a
                     LEFT JOIN `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_group` g ON (FIND_IN_SET( a.`id_ndk_customization_field`, g.`fields`)) 
                     LEFT JOIN `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_group_lang` gl ON (gl.`id_ndk_customization_field_group`= g.`id_ndk_customization_field_group` AND gl.`id_lang` = '.
                    (int) Context::getContext()->language->id.
                    ') 
                     WHERE TRIM(IFNULL(gl.name,\'\')) <> \'\'ORDER BY gl.name ASC '
            );

            $products_array = [];
            foreach ($result as $row) {
                if ('' != $row['name']) {
                    $products_array[$row['products']] = $row['name'];
                }
            }

            $products_array = array_unique($products_array);
            asort($products_array);

            $categories_array = [];
            foreach ($result2 as $row2) {
                $categories_array[$row2['categories']] = $row2['name'];
            }

            asort($categories_array);

            $groups_array = [];
            foreach ($result3 as $row3) {
                $groups_array[$row3['id_ndk_customization_field_group']] =
                    $row3['name'];
            }

            $groups_array = array_unique($groups_array);
            asort($groups_array);

            $part1 = array_slice($this->fields_list, 0, 3);
            $part2 = array_slice($this->fields_list, 3);
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

            $part1['groupname'] = [
                'title' => $this->l('Group'),
                'type' => 'select',
                'list' => $groups_array,
                'filter_key' => 'g!id_ndk_customization_field_group',
                'order_key' => 'groupname',
                'class' => 'chosen',
            ];

            $this->fields_list = array_merge($part1, $part2);
        }

        if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/')) {
            mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/', 0777);
        }

        if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/thumbs/')) {
            mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/thumbs/', 0777);
        }

        if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pictos/')) {
            mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pictos/', 0777);
        }

        if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/mask/')) {
            mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/mask/', 0777);
        }

        parent::__construct();
    }

    public function renderList()
    {
        if (Tools::getIsset($this->_filter) && '' == trim($this->_filter)) {
            $this->_filter = $this->original_filter;
        }

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('duplicate');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function smartyRegisterFunctionNdk(
        $smarty,
        $type,
        $function,
        $params,
        $lazy = true
    ) {
        if (!Tools::getIsset($smarty->registered_plugins[$type][$function])) {
            smartyRegisterFunction($smarty, $type, $function, $params, $lazy);
        }
    }

    public function init()
    {
        $this->smartyRegisterFunctionNdk(
            $this->context->smarty,
            'modifier',
            'classname',
            ['NdkCf', 'smartyClassname']
        );

        if (Tools::getIsset('default_valuendk_customization_field_value')) {
            $this->setDefaultValue(
                (int) Tools::getValue('id_ndk_customization_field_value'),
                (int) Tools::getValue('id_ndk_customization_field')
            );
        }

        if (Tools::getIsset('set_positionndk_customization_field')) {
            $this->setPosition(
                (int) Tools::getValue('id_ndk_customization_field'),
                (int) Tools::getValue('set_positionndk_customization_field')
            );
        }

        if (Tools::getIsset('set_ref_positionndk_customization_field')) {
            $this->setRefPosition(
                (int) Tools::getValue('id_ndk_customization_field'),
                (int) Tools::getValue('set_ref_positionndk_customization_field')
            );
        }

        if (Tools::getIsset('set_zindexndk_customization_field')) {
            $this->setZindex(
                (int) Tools::getValue('id_ndk_customization_field'),
                (int) Tools::getValue('set_zindexndk_customization_field')
            );
        }

        if (Tools::isSubmit('updatendk_customization_field_value')) {
            $this->display = 'editndk_customization_field_value';
        } elseif (Tools::isSubmit('submitAddndk_customization_field_value')) {
            $this->display = 'editndk_customization_field_value';
        } elseif (Tools::isSubmit('add_ndk_customization_field')) {
            $this->display = 'add';
        }

        parent::init();
    }

    public function processSave()
    {
        $this->clearAllCache();
        if ('add' == $this->display || 'edit' == $this->display) {
            $this->identifier = 'id_ndk_customization_field';
        }

        if (!$this->id_object) {
            return $this->processAdd();
        } else {
            return $this->processUpdate();
        }
    }

    public function processAdd()
    {
        $this->clearAllCache();
        if ('ndk_customization_field_value' == $this->table) {
            $object = new $this->className();
            foreach (Language::getLanguages(false) as $language) {
                if (
                    $object->isValues(
                        (int) Tools::getValue('ndk_customization_field'),
                        Tools::getValue('value_'.$language['id_lang']),
                        $language['id_lang']
                    )
                ) {
                    $this->errors['name_'.$language['id_lang']] = sprintf(
                        Tools::displayError(
                            'The field value "%1$s" already exist for %2$s language'
                        ),
                        Tools::getValue('name_'.$language['id_lang']),
                        $language['name']
                    );
                }
            }

            if (!empty($this->errors)) {
                return $object;
            }
        }

        $object = parent::processAdd();

        // if ($this->table == "ndk_customization_field") {
        //     if ($object->required == 1) {
        //         $id_products = [];
        //     }
        // }

        if (
            Tools::isSubmit('submitAdd'.$this->table.'AndStay') &&
            !count($this->errors)
        ) {
            if ('add' == $this->display) {
                $this->redirect_after =
                    self::$currentIndex.
                    '&'.
                    $this->identifier.
                    '=&conf=3&update'.
                    $this->table.
                    '&token='.
                    $this->token;
            } else {
                $this->redirect_after =
                    self::$currentIndex.
                    '&id_ndk_customization_field='.
                    (int) Tools::getValue('id_ndk_customization_field').
                    '&conf=3&update'.
                    $this->table.
                    '&token='.
                    $this->token;
            }
        } else {
            $this->redirect_after =
                self::$currentIndex.
                '&'.
                $this->identifier.
                '=&id_ndk_customization_field='.
                (int) Tools::getValue('id_ndk_customization_field').
                '&conf=3&viewndk_customization_field&token='.
                $this->token;
        }

        if (count($this->errors)) {
            $this->setTypeValues();
        }

        return $object;
    }

    public function processUpdate()
    {
        $object = parent::processUpdate();
        $this->clearAllCache();

        if (
            Tools::isSubmit('submitAdd'.$this->table.'AndStay') &&
            !count($this->errors)
        ) {
            if ('add' == $this->display) {
                $this->redirect_after =
                    self::$currentIndex.
                    '&'.
                    $this->identifier.
                    '=&conf=3&update'.
                    $this->table.
                    '&token='.
                    $this->token;
            } else {
                $this->redirect_after =
                    self::$currentIndex.
                    '&'.
                    $this->identifier.
                    '=&id_ndk_customization_field='.
                    (int) Tools::getValue('id_ndk_customization_field').
                    '&conf=3&update'.
                    $this->table.
                    '&token='.
                    $this->token;
            }
        } else {
            $this->redirect_after =
                self::$currentIndex.
                '&'.
                $this->identifier.
                '=&id_ndk_customization_field='.
                (int) Tools::getValue('id_ndk_customization_field').
                '&conf=3&viewndk_customization_field&token='.
                $this->token;
        }

        if (count($this->errors)) {
            $this->setTypeValues();
        }

        if (
            Tools::isSubmit('updatendk_customization_field_value') ||
            Tools::isSubmit('deletendk_customization_field_value') ||
            Tools::isSubmit('submitAddndk_customization_field_value') ||
            Tools::isSubmit('submitBulkdeletendk_customization_field_value')
        ) {
            return $object;
        }
    }

    public function processPosition()
    {
        $this->clearAllCache();
        if (Tools::getIsset('ndk_customization_field')) {
            $object = new Ndkcf(
                (int) Tools::getValue('id_ndk_customization_field')
            );
            self::$currentIndex =
                self::$currentIndex.'&viewndk_customization_field';
        } else {
            $object = new Ndkcf(
                (int) Tools::getValue('id_ndk_customization_field')
            );
        }

        if (!Validate::isLoadedObject($object)) {
            $this->errors[] =
                Tools::displayError(
                    'An error occurred while updating the status for an object.'
                ).
                ' <b>'.
                $this->table.
                '</b> '.
                Tools::displayError('(cannot load object)');
        } elseif (
            !$object->updatePosition(
                (int) Tools::getValue('way'),
                (int) Tools::getValue('position')
            )
        ) {
            $this->errors[] = Tools::displayError(
                'Failed to update the position.'
            );
        } else {
            $id_identifier_str = ($id_identifier = (int) Tools::getValue(
                $this->identifier
            ))
                ? '&'.$this->identifier.'='.$id_identifier
                : '';
            $redirect =
                self::$currentIndex.
                '&'.
                $this->table.
                'Orderby=position&'.
                $this->table.
                'Orderway=asc&conf=5'.
                $id_identifier_str.
                '&token='.
                $this->token;
            $this->redirect_after = $redirect;
        }

        return $object;
    }

    public function initContent()
    {
        // toolbar (save, cancel, new, ..)
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        if ('edit' == $this->display || 'add' == $this->display) {
            if (!($this->object = $this->loadObject(true))) {
                return;
            }
            $this->content .= $this->renderForm();
        } elseif ('editndk_customization_field_value' == $this->display) {
            if (
                !($this->object = new NdkCfValues(
                    (int) Tools::getValue('id_ndk_customization_field_value')
                ))
            ) {
                return;
            }

            $this->content .= $this->renderFormValues();
        } elseif ('view' != $this->display && !$this->ajax) {
            $this->content .= $this->renderList();
            $this->content .= $this->renderOptions();
        } elseif ('view' == $this->display && !$this->ajax) {
            $this->content = $this->renderView();
        }

        $this->context->smarty->assign([
            'table' => $this->table,
            'current' => self::$currentIndex,
            'token' => $this->token,
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ]);
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_ndk_customization_field'] = [
                'href' => self::$currentIndex.
                    '&addndk_customization_field&token='.
                    $this->token,
                'desc' => $this->l('Add new field', null, null, false),
                'icon' => 'process-icon-new',
            ];
            $this->page_header_toolbar_btn['new_value'] = [
                'href' => self::$currentIndex.
                    '&updatendk_customization_field_value&id_ndk_customization_field='.
                    (int) Tools::getValue('id_ndk_customization_field').
                    '&token='.
                    $this->token,
                'desc' => $this->l('Add new value', null, null, false),
                'icon' => 'process-icon-new',
            ];
        }

        if ('view' == $this->display) {
            $this->page_header_toolbar_btn['new_value'] = [
                'href' => self::$currentIndex.
                    '&updatendk_customization_field_value&id_ndk_customization_field='.
                    (int) Tools::getValue('id_ndk_customization_field').
                    '&token='.
                    $this->token,
                'desc' => $this->l('Add new value', null, null, false),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar()
    {
        switch ($this->display) {
            // @todo defining default buttons
            case 'add':
            case 'edit':
            case 'editndk_customization_field_value':
                // Default save button - action dynamically handled in javascript
                $this->toolbar_btn['save'] = [
                    'href' => '#',
                    'desc' => $this->l('Save'),
                ];

                if (
                    'editndk_customization_field_value' == $this->display &&
                    !$this->id_ndk_customization_field_value
                ) {
                    $this->toolbar_btn['save-and-stay'] = [
                        'short' => 'SaveAndStay',
                        'href' => '#',
                        'desc' => $this->l(
                            'Save then add another value',
                            null,
                            null,
                            false
                        ),
                        'force_desc' => true,
                    ];
                }

                $this->toolbar_btn['back'] = [
                    'href' => self::$currentIndex.'&token='.$this->token,
                    'desc' => $this->l('Back to list', null, null, false),
                ];
                break;
            case 'view':
                $this->toolbar_btn['newndk_customization_field_value'] = [
                    'href' => self::$currentIndex.
                        '&updatendk_customization_field_value&id_ndk_customization_field='.
                        (int) Tools::getValue('id_ndk_customization_field').
                        '&token='.
                        $this->token,
                    'desc' => $this->l('Add New field', null, null, false),
                    'class' => 'toolbar-new',
                ];

                $this->toolbar_btn['back'] = [
                    'href' => self::$currentIndex.'&token='.$this->token,
                    'desc' => $this->l('Back to list', null, null, false),
                ];
                break;
            default:
                // list
                $this->toolbar_btn['new'] = [
                    'href' => self::$currentIndex.
                        '&add'.
                        $this->table.
                        '&token='.
                        $this->token,
                    'desc' => $this->l('Add New Values', null, null, false),
                ];
        }
    }

    public function initToolbarTitle()
    {
        $bread_extended = $this->breadcrumbs;

        switch ($this->display) {
            case 'edit':
                $bread_extended[] = $this->l('Edit New Value');
                break;

            case 'add':
                $bread_extended[] = $this->l('Add New Value');
                break;

            case 'view':
                if (Tools::getIsset('viewndk_customization_field')) {
                    if ($id = Tools::getValue('id_ndk_customization_field')) {
                        if (
                            Validate::isLoadedObject(
                                $obj = new NdkCF((int) $id)
                            )
                        ) {
                            $bread_extended[] =
                                $obj->name[$this->context->employee->id_lang];
                        }
                    }
                } else {
                    $bread_extended[] =
                        $this->value[$this->context->employee->id_lang];
                }
                break;

            case 'editndk_customization_field_value':
                if ($this->id_ndk_customization_field_value) {
                    if ($id = Tools::getValue('id_ndk_customization_field')) {
                        if (
                            Validate::isLoadedObject(
                                $obj = new NdkCf((int) $id)
                            )
                        ) {
                            $bread_extended[] =
                                '<a href="'.
                                Context::getContext()->link->getAdminLink(
                                    'AdminNdkCustomFields'
                                ).
                                '&id_ndk_customization_field='.
                                $id.
                                '&viewndk_customization_field">'.
                                $obj->name[$this->context->employee->id_lang].
                                '</a>';
                        }
                        if (
                            Validate::isLoadedObject(
                                $obj = new NdkCfValues(
                                    (int) $this->id_ndk_customization_field_value
                                )
                            )
                        ) {
                            $bread_extended[] = sprintf(
                                $this->l('Edit: %s'),
                                $obj->value[$this->context->employee->id_lang]
                            );
                        }
                    } else {
                        $bread_extended[] = $this->l('Edit Value');
                    }
                } else {
                    $bread_extended[] = $this->l('Add New Value');
                }
                break;
        }

        if (count($bread_extended) > 0) {
            $this->addMetaTitle($bread_extended[count($bread_extended) - 1]);
        }

        $this->toolbar_title = $bread_extended;
    }

    public function getList(
        $id_lang,
        $order_by = null,
        $order_way = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = false
    ) {
        parent::getList(
            $id_lang,
            $order_by,
            $order_way,
            $start,
            $limit,
            $id_lang_shop
        );

        $nb_items = count($this->_list);
        for ($i = 0; $i < $nb_items; ++$i) {
            Db::getInstance(_PS_USE_SQL_SLAVE_)->query('SET SQL_BIG_SELECTS=1');

            $item = &$this->_list[$i];

            $query = new DbQuery();
            $query->select(
                'COUNT(a.id_ndk_customization_field_value) as count_values'
            );
            $query->from('ndk_customization_field_value', 'a');
            $query->join(
                Shop::addSqlAssociation('ndk_customization_field_value', 'a')
            );
            $query->where(
                'a.id_ndk_customization_field ='.
                    (int) $item['id_ndk_customization_field']
            );
            $query->orderBy('count_values DESC');
            $item['count_values'] = (int) Db::getInstance(
                _PS_USE_SQL_SLAVE_
            )->getValue($query);
            unset($query);
        }
    }

    public function processBulkDelete()
    {
        $this->clearAllCache();

        if (Tools::getIsset('valueBox')) {
            //$this->className = 'NdkCfValues';
            //$this->table = 'ndk_customization_field_value';
            $this->boxes = Tools::getValue($this->table.'Box');
        }
        foreach (Tools::getValue($this->table.'Box') as $id) {
            if (Validate::isLoadedObject($object = new NdkCf($id))) {
                $childs = $object->getValuesId();
                foreach ($childs as $child) {
                    $value = new NdkCfValues($child['id']);
                    if (Validate::isLoadedObject($value)) {
                        //on supprime les images
                        $this->deleteImagesProper($value->id);
                        $value->delete();
                    }
                }
            }
        }
        $result = parent::processBulkDelete();
        // Restore vars
        $this->className = 'NdkCf';
        $this->table = 'ndk_customization_field';

        return $result;
    }

    public function renderFormValues()
    {
        $fields = NdkCf::getFieldsLight(0);
        // Override var of Controller
        $this->table = 'ndk_customization_field_value';
        $this->className = 'NdkCfValues';
        $this->lang = true;
        $parent = new NdkCf(
            (int) Tools::getValue('id_ndk_customization_field')
        );
        $additionnals = '';
        $this->show_form_cancel_button = true;

        require_once _PS_MODULE_DIR_.
            'ndk_advanced_custom_fields/models/ndkCfSpecificPrice.php';
        $specificPriceBlock = '';

        $specificPriceBlock = $this->context->smarty->fetch(
            _PS_ROOT_DIR_.
                '/modules/ndk_advanced_custom_fields/views/templates/admin/specific_prices.tpl'
        );

        $parent_values = NdkCf::getTargetsChilds(
            (int) Tools::getValue('id_ndk_customization_field')
        );
        array_unshift($parent_values, [
            'id' => '0',
            'value' => $this->l('none'),
        ]);

        if ($obj = $this->loadObject(true)) {
            if ('' != $obj->options) {
                $options = Tools::jsonDecode($obj->options, true);
                foreach ($options as $k => $v) {
                    $this->fields_value['false_options['.$k.']'] = $v;
                }
            }
        }
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Values'),
                'icon' => 'icon-info-sign',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Field'),
                    'name' => 'id_ndk_customization_field',
                    'required' => true,
                    'options' => [
                        'query' => $fields,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l(
                        'Choose the parent field for this value.'
                    ),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Parent value'),
                    'name' => 'id_parent_value',
                    'required' => false,
                    'class' => 'chosen',
                    'options' => [
                        'query' => $parent_values,
                        'id' => 'id',
                        'name' => 'value',
                    ],
                    'hint' => $this->l(
                        'Choose the parent value from this field for this value.'
                    ),
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Value'),
                    'name' => 'value',
                    'required' => true,
                    'lang' => true,
                    'class' => 'autoFillValue',
                    'hint' => $this->l(
                            'Set the displayed value name. Invalid characters:'
                        ).' <>;=#{}',
                    'desc' => '<script type="text/javascript">var parentType = '.
                        $parent->type.
                        ';</script><span class="visible-22 visible-26 visible-27 visible-101">'.
                        $this->l(
                            '(FOR COMBINATION FIELDS : ) If you want to load current product automatically, set value : [:SELF] '
                        ).
                        '</span>',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'required' => false,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Reference'),
                    'name' => 'reference',
                    'lang' => false,
                    'hint' => $this->l('Used for construct custom reference'),
                    'desc' => $this->l(
                        'To use current main product image set [:product_image] (for type "image" only)'
                    ),
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Position'),
                    'name' => 'position',
                    'size' => 8,
                    'desc' => $this->l('Position in values list'),
                ],

                [
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'size' => 48,
                    'form_group_class' => 'hidden-0',
                    'autoload_rte' => true,
                    'hint' => $this->l(
                        'A small description or notice for your value'
                    ),
                    'desc' => '<span class="hidden-0  "></span>',
                ],
                [
                    'type' => 'text',
                    'form_group_class' => 'visible-field hidden-10 hidden-5 hidden-14 hidden-0 hidden-18 hidden-19 hidden-21 hidden-8',
                    'label' => $this->l('Price (tax excl.)'),
                    'class' => 'price-input '.
                        (in_array($parent->type, [17, 22, 24, 26])
                            ? 'makeItFree'
                            : ''),
                    'name' => 'price',
                    'hint' => $this->l(
                        'The specific price for this value (will override the defaut field price if specified).'
                    ),
                    'desc' => $specificPriceBlock /*'<span class="visible-field"><span class="visible-field"><span class="hidden-field visible-17 visible-24 visible-22 visible-26 visible-27 visible-101  hidden-18 hidden-19 hidden-21">'.$this->l('Will override all combinations prices.').'</span></span></span>'*/,
                    'form_group_class' => in_array($parent->type, [
                        0,
                        7,
                        10,
                        13,
                    ])
                        ? 'hidden hidden-field'
                        : '',
                ],

                /*array(
                     'type' => 'switch',
                     'label' => $this->l('Default value:'),
                     'name' => 'default_value',
                     'required' => false,
                     'hint' => $this->l('Do you want to set this value active by default ?'),
                     'class' => 't visible-field hidden-10 hidden-5 hidden-14',
                     'is_bool' => true,
                     'values' => array(
                        array(
                           'id' => 'active_on',
                           'value' => 1,
                           'label' => $this->l('Yes')
                        ),
                        array(
                           'id' => 'active_off',
                           'value' => 0,
                           'label' => $this->l('No')
                        )
                     )
                  ),*/
                [
                    'type' => 'select',
                    'multiple' => false,
                    'label' => $this->l('Force Taxe'),
                    'name' => 'false_options[force_tax_rule]',
                    'class' => 'chosen serialize_input',
                    'form_group_class' => (in_array($parent->type, [1, 2, 4, 16]) ? 'visible-field' : 'hidden-field'),
                    'options' => [
                        'query' => array_merge([['id_tax_rules_group' => '', 'name' => $this->l('keep')]], TaxRulesGroup::getTaxRulesGroups()),
                        'id' => 'id_tax_rules_group',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('will force global taxe for all bundle (product and options)'),
                ],
                [
                    'type' => 'select',
                    'multiple' => false,
                    'label' => $this->l('Force Carrier'),
                    'name' => 'false_options[force_carrier]',
                    'class' => 'chosen serialize_input',
                    'form_group_class' => (in_array($parent->type, [1, 2, 4, 16]) ? 'visible-field' : 'hidden-field'),
                    'options' => [
                        'query' => array_merge([['id_carrier' => '', 'name' => $this->l('keep')]], Carrier::getCarriers($this->context->employee->id_lang)),
                        'id' => 'id_reference',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('will force carrier for all bundle (product and options)'),
                ],

                [
                    'type' => 'switch',
                    'label' => $this->l('Set quantity:'),
                    'name' => 'set_quantity',
                    'required' => false,
                    'hint' => $this->l(
                        'Do you want to set available quantity for this values ?'
                    ),
                    'class' => 't visible-field hidden-10 hidden-5 hidden-14 hidden-0 hidden-17 hidden-24 hidden-22 hidden-26 hidden-18 hidden-19 hidden-21 hidden-8',
                    'is_bool' => true,
                    'form_group_class' => 'toggle_qtty-form-group visible-field hidden-10 hidden-5 hidden-14 hidden-0 hidden-17 hidden-24 hidden-22 hidden-26 hidden-18 hidden-19 hidden-21 hidden-8',
                    'desc' => '<span class="hidden-5 hidden-14 hidden-0 hidden-17 hidden-24 hidden-22 hidden-26 hidden-6 hidden-10 hidden-13 hidden-14 hidden-15 hidden-20 hidden-18 hidden-19 hidden-21"></span>',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],

                [
                    'type' => 'text',
                    'form_group_class' => 'qtty-form-group visible-field hidden-10 hidden-5 hidden-17 hidden-24 hidden-22 hidden-26 hidden-14 hidden-0 hidden-20 hidden-18 hidden-19 hidden-21 hidden-8',
                    'label' => $this->l('Quantity'),
                    'name' => 'quantity',
                    'hint' => $this->l(
                        'Specify quantity available if set to yes.'
                    ),
                ],
            ],
        ];

        $this->addJqueryPlugin(['autocomplete', 'tagify']);
        //$this->addJs(_MODULE_DIR_.'ndk_advanced_custom_fields/views/js/admin.js' );
        $obj = $this->loadObject(true);

        $this->fields_form['submit'] = [
            'title' => $this->l('Save'),
        ];

        $this->fields_form['buttons'] = [
            'save-and-stay' => [
                'title' => $this->l('Save then add another value'),
                'name' => 'submitAdd'.$this->table.'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save',
            ],
        ];

        $this->fields_value[
            'id_ndk_customization_field'
        ] = (int) Tools::getValue('id_ndk_customization_field');

        // Override var of Controller
        $this->table = 'ndk_customization_field_value';
        $this->className = 'NdkCfValues';
        $this->identifier = 'id_ndk_customization_field_value';
        $this->lang = true;
        $this->tpl_folder = 'values/';

        // Create object Field
        if (
            !($obj = new NdkCfValues((int) Tools::getValue($this->identifier)))
        ) {
            return;
        }

        if (
            file_exists(_PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.svg')
        ) {
            $additionnals = $this->renderNdkImage(
                'img/scenes/ndkcf/'.$obj->id.'.svg'
            );
        } else {
            $additionnals = $this->renderNdkImage(
                'img/scenes/ndkcf/'.$obj->id.'.jpg'
            );
        }

        $texture = '';
        if (
            file_exists(
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'-texture.jpg'
            )
        ) {
            $texture = $this->renderNdkImage(
                'img/scenes/ndkcf/'.$obj->id.'-texture.jpg'
            );
        }

        //influences_restrictions
        if ('' != $parent->influences && 0 != $parent->influences) {
            if ($obj = $this->loadObject(true)) {
                $selected_influences_restrictions = explode(
                    ',',
                    $obj->influences_restrictions
                );
                foreach ($selected_influences_restrictions as $k => $v) {
                    if ('0' == $v) {
                        unset($selected_influences_restrictions[$k]);
                    }
                }
                $this->fields_value[
                    'influences_restrictions[]'
                ] = $selected_influences_restrictions;

                $selected_influences_obligations = explode(
                    ',',
                    $obj->influences_obligations
                );
                foreach ($selected_influences_obligations as $k => $v) {
                    if ('0' == $v) {
                        unset($selected_influences_obligations[$k]);
                    }
                }
                $this->fields_value[
                    'influences_obligations[]'
                ] = $selected_influences_obligations;
            }

            if ('' != $obj->step_quantity) {
                $steps = explode(';', $obj->step_quantity);
                foreach ($steps as $step) {
                    $influences_fields = NdkCf::getInfluencesFields(
                        $parent->influences,
                        $step
                    );
                    $influences_fields_o = $influences_fields;
                    foreach ($influences_fields as $influences_field) {
                        array_unshift($influences_field['values'], [
                            'id' => 'all-'.
                                $influences_field[
                                    'id_ndk_customization_field'
                                ].
                                '['.
                                $step.
                                ']',
                            'name' => $this->l('entire field'),
                        ]);
                        $this->fields_form['input'][] = [
                            'type' => 'select',
                            'label' => $this->l('Disable values for').
                                ' '.
                                $influences_field['admin_name'].
                                ' '.
                                $this->l('if quantity = ').
                                $step,
                            'multiple' => true,
                            'name' => 'influences_restrictions[]',
                            'class' => 'visible-field hidden-0 visual-child chosen hidden-8 ',
                            'form_group_class' => 'visible-field hidden-0 visual-child hidden-8',
                            'options' => [
                                'query' => $influences_field['values'],
                                'id' => 'id',
                                'name' => 'name',
                            ],
                            'desc' => $this->l(
                                    'You can specify on which values (might be created first) will be disabled for the field :'
                                ).
                                ' '.
                                $influences_field['admin_name'].
                                ' '.
                                $this->l('if quantity = ').
                                $step,
                        ];
                    }
                    foreach ($influences_fields_o as $influences_field) {
                        if (sizeof($influences_field['values']) > 0) {
                            array_unshift($influences_field['values'], [
                                'id' => '',
                                'name' => $this->l('none'),
                            ]);
                            $this->fields_form['input'][] = [
                                'type' => 'select',
                                'label' => $this->l('Auto select values for').
                                    ' '.
                                    $influences_field['admin_name'].
                                    ' '.
                                    $this->l('if quantity = ').
                                    $step,
                                'multiple' => false,
                                'name' => 'influences_obligations[]',
                                'class' => 'visible-field hidden-0 visual-child chosen hidden-8',
                                'form_group_class' => 'visible-field hidden-0 visual-child chosen hidden-8',
                                'options' => [
                                    'query' => $influences_field['values'],
                                    'id' => 'id',
                                    'name' => 'name',
                                ],
                                'desc' => $this->l(
                                        'You can specify on which value (might be created first) will be selected for the field :'
                                    ).
                                    ' '.
                                    $influences_field['admin_name'].
                                    ' '.
                                    $this->l('if quantity = ').
                                    $step,
                            ];
                        }
                    }
                }
            } else {
                $influences_fields = NdkCf::getInfluencesFields(
                    $parent->influences
                );
                $influences_fields_o = $influences_fields;
                foreach ($influences_fields as $influences_field) {
                    array_unshift($influences_field['values'], [
                        'id' => 'all-'.
                            $influences_field['id_ndk_customization_field'],
                        'name' => $this->l('entire field'),
                    ]);
                    array_unshift($influences_field['values'], [
                        'id' => '',
                        'name' => $this->l('none'),
                    ]);
                    $this->fields_form['input'][] = [
                        'type' => 'select',
                        'label' => $this->l('Disable values for').
                            ' '.
                            $influences_field['admin_name'],
                        'multiple' => true,
                        'name' => 'influences_restrictions[]',
                        'class' => 'visible-field hidden-0 visual-child chosen hidden-8',
                        'form_group_class' => 'visible-field hidden-0 visual-child  hidden-8',
                        'options' => [
                            'query' => $influences_field['values'],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'desc' => $this->l(
                                'You can specify on which values (might be created first) will be disabled for the field :'
                            ).
                            ' '.
                            $influences_field['admin_name'],
                    ];
                }
                foreach ($influences_fields_o as $influences_field) {
                    if (sizeof($influences_field['values']) > 0) {
                        array_unshift($influences_field['values'], [
                            'id' => '',
                            'name' => $this->l('none'),
                        ]);
                        $this->fields_form['input'][] = [
                            'type' => 'select',
                            'label' => $this->l('Auto select values for').
                                ' '.
                                $influences_field['admin_name'],
                            'multiple' => false,
                            'name' => 'influences_obligations[]',
                            'class' => 'visible-field hidden-0 visual-child chosen hidden-8',
                            'form_group_class' => 'visible-field hidden-0 visual-child hidden-8',
                            'options' => [
                                'query' => $influences_field['values'],
                                'id' => 'id',
                                'name' => 'name',
                            ],
                            'desc' => $this->l(
                                    'You can specify on which value (might be created first) will be selected for the field :'
                                ).
                                ' '.
                                $influences_field['admin_name'],
                        ];
                    }
                }
            }
        }

        if (0 == $parent->type) {
            $this->fields_form['input'][] = [
                'type' => 'text',
                'label' => $this->l('Text mask:'),
                'name' => 'textmask',
                'lang' => true,
                'hint' => $this->l('You can set a pattern for your text field'),
            ];
        }

        if (
            0 == $parent->type ||
            1 == $parent->type ||
            10 == $parent->type ||
            11 == $parent->type ||
            5 == $parent->type ||
            3 == $parent->type ||
            2 == $parent->type ||
            14 == $parent->type ||
            15 == $parent->type ||
            16 == $parent->type ||
            23 == $parent->type ||
            24 == $parent->type ||
            17 == $parent->type ||
            29 == $parent->type ||
            30 == $parent->type
        ) {
            $this->fields_form['input'][] = [
                'type' => 'file',
                'label' => $this->l('Image:'),
                'name' => 'image',
                'display_image' => true,
                'desc' => $additionnals,
                'hint' => $this->l(
                    'You can use svg if you want allow user to change image color'
                ),
            ];
            if (
                2 == $parent->type ||
                29 == $parent->type ||
                30 == $parent->type
            ) {
                $this->fields_form['input'][] = [
                    'type' => 'file',
                    'label' => $this->l('Vignette: (don’t use for SVG files)'),
                    'name' => 'texture',
                    'display_image' => true,
                    'desc' => $texture,
                ];
            }
        }

        $this->fields_form['input'][] = [
            'type' => 'tags',
            'label' => $this->l('Tags:'),
            'name' => 'tags',
            'lang' => true,
            'desc' => $this->l(
                'Tag your images to set filters (press enter after typing your tag)'
            ),
        ];

        if (3 == $parent->type || 25 == $parent->type) {
            $this->fields_form['input'][] = [
                'type' => 'color',
                'label' => $this->l('Couleur:'),
                'name' => 'color',
            ];
        }

        if (
            3 == $parent->type ||
            25 == $parent->type ||
            30 == $parent->type ||
            29 == $parent->type
        ) {
            $this->fields_form['input'][] = [
                'type' => 'file',
                'label' => $this->l('Texture:'),
                'name' => 'texture',
                'display_image' => true,
                'desc' => $texture,
            ];
        }
        if (
            11 == $parent->type ||
            17 == $parent->type ||
            24 == $parent->type ||
            22 == $parent->type ||
            26 == $parent->type ||
            8 == $parent->type ||
            27 == $parent->type ||
            101 == $parent->type
        ) {
            $this->fields_form['input'][] = [
                'type' => 'text',
                'form_group_class' => 'visible-field hidden-10 hidden-5',
                'label' => $this->l('Minimal Quantity'),
                'name' => 'quantity_min',
                'hint' => $this->l('Specify minimal allowed quantity.'),
            ];
        }

        if (
            11 == $parent->type ||
            17 == $parent->type ||
            24 == $parent->type ||
            22 == $parent->type ||
            26 == $parent->type ||
            27 == $parent->type ||
            101 == $parent->type
        ) {
            $this->fields_form['input'][] = [
                'type' => 'text',
                'form_group_class' => 'visible-field hidden-10 hidden-5',
                'label' => $this->l('Maximal Quantity'),
                'name' => 'quantity_max',
                'hint' => $this->l('Specify maximal allowed quantity.'),
            ];
            $this->fields_form['input'][] = [
                'type' => 'textarea',
                'form_group_class' => 'visible-field hidden-10 hidden-5 hidden-8',
                'label' => $this->l('Step quantities'),
                'name' => 'step_quantity',
                'hint' => $this->l(
                    'enter array of quantities separated by ";" eg. 2;4;6;8;10'
                ),
            ];
        }

        if (8 == $parent->type) {
            $this->fields_form['input'][] = [
                'type' => 'text',
                'form_group_class' => 'visible-field hidden-10 hidden-5',
                'label' => $this->l('Maximal Quantity'),
                'name' => 'quantity_max',
                'hint' => $this->l('Specify maximal allowed quantity.'),
            ];
            $this->fields_form['input'][] = [
                'type' => 'text',
                'form_group_class' => 'visible-field ',
                'label' => $this->l('Step quantities'),
                'name' => 'step_quantity',
            ];
        }

        $boxprod = '<div class="clear clearfix prodlist">';

        if ($this->className = 'NdkCfValues') {
            $objprods = $obj->excludes_products;
        } else {
            $objprods = $obj->products;
        }

        $objprods = NdkCf::getproductsLight($objprods);
        $this->context->smarty->assign(['objprods' => $objprods]);
        $searchbox = $this->context->smarty->fetch(
            _PS_ROOT_DIR_.
                '/modules/ndk_advanced_custom_fields/views/templates/admin/prodbox.tpl'
        );

        $lightProds = null;
        if ($this->className = 'NdkCfValues') {
            $prodID = $obj->id_product_value;
        } else {
            $prodID = false;
        }

        if ($prodID) {
            $lightProds = NdkCf::getproductsLight($prodID);
        }

        $this->context->smarty->assign(['objprods' => $lightProds]);
        $searchboxID = $this->context->smarty->fetch(
            _PS_ROOT_DIR_.
                '/modules/ndk_advanced_custom_fields/views/templates/admin/prodbox.tpl'
        );

        if (
            17 == $parent->type ||
            22 == $parent->type ||
            24 == $parent->type ||
            26 == $parent->type ||
            27 == $parent->type ||
            101 == $parent->type
        ) {
            $this->fields_form['input'][] = [
                'type' => 'text',
                'label' => $this->l('Choose a product'),
                'name' => 'id_product_value',
                'id' => 'id_product_value',
                'size' => 48,
                'class' => 'product-result only_one',
                'hint' => $this->l('product to se for this value'),
                'desc' => $searchboxID,
            ];
        }

        if (21 == $parent->type) {
            $this->fields_form['input'][] = [
                'type' => 'select',
                'label' => $this->l('input type:'),
                'name' => 'input_type',
                'options' => [
                    'query' => [
                        ['id_type' => 'text', 'name' => $this->l('text input')],
                        [
                            'id_type' => 'select',
                            'name' => $this->l('select input'),
                        ],
                        [
                            'id_type' => 'number',
                            'name' => $this->l('number input'),
                        ],
                    ],
                    'id' => 'id_type',
                    'name' => 'name',
                ],
                'hint' => $this->l('input type for this value'),
            ];
        }

        if (20 == $parent->type) {
            $this->fields_form['input'][] = [
                'type' => 'file',
                'label' => $this->l('MP3 File:'),
                'name' => 'mp3',
                'display_image' => false,
            ];
        }

        $this->fields_form['input'][] = [
            'type' => 'text',
            'label' => $this->l('Excludes products'),
            'name' => 'excludes_products',
            'form_group_class' => 'hidden-14 hidden-0 product-result hidden-18 hidden-19 hidden-21 hidden-8',
            'id' => 'products',
            'class' => 'product-result',
            'size' => 48,
            'hint' => $this->l(
                'Value will not be displayed for theses products'
            ),
            'desc' => $searchbox,
        ];

        $selected_cat = [];
        if ($obj->id) {
            $cats = explode(',', $obj->excludes_categories);
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
            ->setInputName('excludes_categories'); //Set the name of input. The option "name" of $fields_form doesn't seem to work with "categories_select" type
        $categoryTree = $tree->render();

        $this->fields_form['input'][] = [
            'type' => 'categories_select',
            'label' => $this->l('Excludes Categories'),
            'desc' => $this->l(
                'Value will not be displayed for theses categories of product'
            ),
            'name' => 'excludes_categories',
            'category_tree' => $categoryTree, //This is the category_tree called in form.tpl
        ];

        return parent::renderForm();
    }

    public function renderView()
    {
        if ($id = Tools::getValue('id_ndk_customization_field')) {
            $this->table = 'ndk_customization_field_value';
            $this->className = 'NdkCfValues';
            $this->identifier = 'id_ndk_customization_field_value';
            $this->position_identifier = 'id_ndk_customization_field_value';
            $this->position_group_identifier = 'id_ndk_customization_field';
            $this->list_id = 'ndk_customization_field_value';
            $this->lang = true;

            $this->_select =
                'a.*, a.id_ndk_customization_field_value as id_ndk_customization_field_value, a.id_ndk_customization_field_value as svg';
            $this->_group = 'GROUP BY a.id_ndk_customization_field_value';
            $this->context->smarty->assign([
                'current' => self::$currentIndex.
                    '&id_ndk_customization_field='.
                    (int) $id.
                    '&viewndk_customization_field',
            ]);
            if (Tools::getIsset('duplicatendk_customization_field_value')) {
                $this->processDuplicateValue();

                Tools::redirectAdmin(
                    self::$currentIndex.
                        '&id_ndk_customization_field='.
                        (int) Tools::getValue('id_ndk_customization_field').
                        '&viewndk_customization_field&token='.
                        $this->token
                );
            }

            if (!Validate::isLoadedObject($obj = new NdkCf((int) $id))) {
                $this->errors[] =
                    Tools::displayError(
                        'An error occurred while updating the status for an object.'
                    ).
                    ' <b>'.
                    $this->table.
                    '</b> '.
                    Tools::displayError('(cannot load object)');

                return;
            }

            if (99 == $obj->type) {
                Tools::redirectAdmin(
                    self::$currentIndex.
                        '&id_ndk_customization_field='.
                        (int) Tools::getValue('id_ndk_customization_field').
                        '&conf=3&update'.
                        $this->table.
                        '&token='.
                        $this->token
                );
            }

            $this->name = $obj->name;
            $this->fields_list = [
                'id_ndk_customization_field_value' => [
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ],
                'value' => [
                    'title' => $this->l('Value'),
                    'width' => 'auto',
                    'filter_key' => 'b!value',
                    'lang' => true,
                ],
                'type' => [
                    'title' => $this->l('Type'),
                    'width' => 'auto',
                    'filter_key' => 'a!type',
                    'lang' => false,
                ],
                'id_parent_value' => [
                    'title' => $this->l('Parent'),
                    'width' => 'auto',
                    'filter_key' => 'a!id_parent_value',
                    'lang' => false,
                ],
                'reference' => [
                    'title' => $this->l('Reference'),
                    'width' => 'auto',
                    'filter_key' => 'a!reference',
                    'lang' => false,
                ],
                'price' => [
                    'title' => $this->l('Price'),
                    'width' => 'auto',
                    'filter_key' => 'a!price',
                    'lang' => true,
                ],
                'set_quantity' => [
                    'title' => $this->l('Use quantity?'),
                    'active' => 'set_quantity',
                    'type' => 'bool',
                    'class' => 'fixed-width-xs',
                    'align' => 'center',
                    'orderby' => false,
                ],
                'default_value' => [
                    'title' => $this->l('Default value'),
                    'active' => 'default_value',
                    'type' => 'bool',
                    'class' => 'fixed-width-xs',
                    'align' => 'center',
                    'orderby' => false,
                ],
                'quantity' => [
                    'title' => $this->l('quantity'),
                    'width' => 'auto',
                    'filter_key' => 'a!quantity',
                ],
                'position' => [
                    'title' => $this->l('position'),
                    'width' => 'auto',
                    'filter_key' => 'a!position',
                ],
            ];

            $this->fields_list['svg'] = [
                'title' => $this->l('Image'),
                'align' => 'center',
                //"image" => "scenes/ndkcf/",
                'width' => 70,
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'getImage',
            ];

            // $this->fields_list["image"] = [
            //     "title" => $this->l('Image'),
            //     "align" => "center",
            //     "width" => 70,
            //     "orderby" => false,
            //     "filter" => false,
            //     "search" => false,
            //     "callback" => "getImage",
            // ];

            $this->fields_list['color'] = [
                'title' => $this->l('Couleur'),
                'align' => 'center',
                'cache' => false,
                'width' => 70,
                'orderby' => false,
                'filter' => false,
                'search' => false,
            ];

            $this->addRowAction('edit');
            $this->addRowAction('delete');
            $this->addRowAction('duplicate');

            $this->_where = 'AND a.`id_ndk_customization_field` = '.(int) $id;
            $this->_orderBy = 'id_ndk_customization_field_value';

            self::$currentIndex =
                self::$currentIndex.
                '&id_ndk_customization_field='.
                (int) $id.
                '&viewndk_customization_field';
            //$this->processFilter();
            return parent::renderList();

            /*if (Tools::getIsset('submitFilterndk_customization_field'))
             {
                 $this->redirect_after = self::$currentIndex.'&token='.$this->token;
             }*/
        }
    }

    public static function getImage($id_ndk_customization_field_value)
    {
        $image = '';
        if (
            file_exists(
                _PS_IMG_DIR_.
                    'scenes/'.
                    'ndkcf/'.
                    $id_ndk_customization_field_value.
                    '.svg'
            )
        ) {
            $image =
                '<img class="imgm img-thumbnail" width="45" height="auto" src="/img/scenes/ndkcf/'.
                $id_ndk_customization_field_value.
                '.svg?t='.
                time().
                '" />';
        } elseif (
            file_exists(
                _PS_IMG_DIR_.
                    'scenes/'.
                    'ndkcf/'.
                    $id_ndk_customization_field_value.
                    '.jpg'
            )
        ) {
            $image =
                '<img class="imgm img-thumbnail" width="45" height="auto" src="/img/scenes/ndkcf/thumbs/'.
                $id_ndk_customization_field_value.
                '-'.
                Tools::stripslashes(Configuration::get('NDK_IMAGE_SIZE')).
                '.jpg?t='.
                time().
                '" />';
        }

        return $image;
    }

    public static function getPicto($id_ndk_customization_field)
    {
        $image = '';
        if (
            file_exists(
                _PS_IMG_DIR_.
                    'scenes/'.
                    'ndkcf/pictos/'.
                    $id_ndk_customization_field.
                    '.jpg'
            )
        ) {
            $image =
                '<img class="imgm img-thumbnail" width="45" height="auto" src="/img/scenes/ndkcf/pictos/'.
                $id_ndk_customization_field.
                '.jpg"/>';
        }

        return $image;
    }

    protected function postImage($id)
    {
        $obj = $this->loadObject(true);
        if ($obj->id && isset($_FILES['image'])) {
            $name = $_FILES['image']['name'];
            if ('' != $name) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                if ('svg' == $ext) {
                    $base_img_path =
                        _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.svg';
                    $tmp_name = $_FILES['image']['tmp_name'];
                    move_uploaded_file($tmp_name, $base_img_path);
                } elseif ('glb' == $ext) {
                    $base_img_path =
                        _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.glb';
                    $tmp_name = $_FILES['image']['tmp_name'];
                    move_uploaded_file($tmp_name, $base_img_path);
                } else {
                    $base_img_path =
                        _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.jpg';
                    $tmp_name = $_FILES['image']['tmp_name'];
                    array_map('unlink', glob(str_replace('.jpg', '*.webp', $base_img_path)));
                    move_uploaded_file($tmp_name, $base_img_path);
                }
            }
        }

        if ($obj->id && isset($_FILES['texture'])) {
            $name = $_FILES['texture']['name'];
            if ('' != $name) {
                $base_texture_path =
                    _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$id.'-texture.jpg';
                $tmp_name_texture = $_FILES['texture']['tmp_name'];
                array_map('unlink', glob(str_replace('.jpg', '*.webp', $base_texture_path)));
                move_uploaded_file($tmp_name_texture, $base_texture_path);
            }
        }

        if ($obj->id && isset($_FILES['picto'])) {
            $name = $_FILES['picto']['name'];
            if ('' != $name) {
                $base_img_path_picto =
                    _PS_IMG_DIR_.
                    'scenes/'.
                    'ndkcf/pictos/'.
                    $obj->id.
                    '.jpg';
                $tmp_name_picto = $_FILES['picto']['tmp_name'];

                array_map('unlink', glob(str_replace('.jpg', '*.webp', $base_img_path_picto)));

                move_uploaded_file($tmp_name_picto, $base_img_path_picto);
            }
        }

        if ($obj->id && isset($_FILES['mask'])) {
            $name = $_FILES['mask']['name'];
            if ('' != $name) {
                $base_img_path_mask =
                    _PS_IMG_DIR_.
                    'scenes/'.
                    'ndkcf/mask/'.
                    $obj->id.
                    '.jpg';
                $tmp_name_mask = $_FILES['mask']['tmp_name'];
                array_map('unlink', glob(str_replace('.jpg', '*.webp', $base_img_path_mask)));

                move_uploaded_file($tmp_name_mask, $base_img_path_mask);
            }
        }

        if ($obj->id && isset($_FILES['csv'])) {
            $name = $_FILES['csv']['name'];
            if ('' != $name) {
                $base_img_path_csv =
                    _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.csv';
                if (file_exists($base_img_path_csv)) {
                    @unlink($base_img_path_csv);
                }
                $tmp_name_csv = $_FILES['csv']['tmp_name'];
                move_uploaded_file($tmp_name_csv, $base_img_path_csv);
                ndkCf::recordCsv($obj->id);
            }
        }

        if ($obj->id && isset($_FILES['mp3'])) {
            $name = $_FILES['mp3']['name'];
            if ('' != $name) {
                $base_img_path_mp3 =
                    _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.mp3';
                if (file_exists($base_img_path_mp3)) {
                    @unlink($base_img_path_mp3);
                }
                $tmp_name_mp3 = $_FILES['mp3']['tmp_name'];
                move_uploaded_file($tmp_name_mp3, $base_img_path_mp3);
            }
        }

        NdkCf::generateThumbs($obj->id);

        return parent::postImage($obj->id);
    }

    protected function afterImageUpload()
    {
        /* Generate image with differents size */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        if ($obj->id && isset($_FILES['image'])) {
            $name = $_FILES['image']['name'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            //var_dump($ext);
            if ('svg' == $ext) {
                $base_img_path =
                    _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.svg';
                $tmp_name = $_FILES['image']['tmp_name'];
                move_uploaded_file($tmp_name, $base_img_path);
            } else {
                $base_img_path =
                    _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$obj->id.'.jpg';
            }

            NdkCf::generateThumbs($obj->id);
        }

        return true;
    }

    public function renderForm()
    {
        $this->table = 'ndk_customization_field';
        $this->identifier = 'id_ndk_customization_field';

        $features = Feature::getFeatures($this->context->employee->id_lang);
        array_unshift($features, [
            'id_feature' => 0,
            'name' => $this->l('none'),
        ]);
        $targets = Ndkcf::getTargetsFields();

        array_unshift($targets, ['id' => 0, 'name' => $this->l('none')]);
        $obj = $this->loadObject(true);
        $influences = Ndkcf::getFieldsLight(
            Tools::getIsset($obj) ? $obj->id : 0
        );

        $quantity_link_fields = Ndkcf::getFieldsLight(
            Tools::getIsset($obj) ? $obj->id : 0,
            '11,23,17,24,22,26,27, 101'
        );
        array_unshift($quantity_link_fields, [
            'id' => '0',
            'name' => $this->l('none'),
        ]);

        require_once _PS_MODULE_DIR_.
            'ndk_advanced_custom_fields/models/ndkCfSpecificPrice.php';
        $specificPriceBlock = '';

        $specificPriceBlock = $this->context->smarty->fetch(
            _PS_ROOT_DIR_.
                '/modules/ndk_advanced_custom_fields/views/templates/admin/specific_prices.tpl'
        );
        //$specificPriceBlock = '';

        $values_from_fields = Ndkcf::getFieldsLight(
            Tools::getIsset($obj) ? $obj->id : 0
        );
        array_unshift($values_from_fields, [
            'id' => '0',
            'name' => $this->l('none'),
        ]);

        array_unshift($influences, ['id' => '', 'name' => $this->l('none')]);
        if ($obj = $this->loadObject(true)) {
            $selected_influences = explode(',', $obj->influences);
            $this->fields_value['influences[]'] = $selected_influences;
        }
        $my_options = Tools::jsonDecode($obj->options, true);
        $image_to_map_desc = '';
        $image_to_map_desc .= '<script type="text/javascript">
            startingData = new Array();';
        if (!isset($my_options['zone_mode'])) {
            $my_options['zone_mode'] = 'percent';
        }
        $image_to_map_desc .=
            'startingData[0] = new Array('.
            '"zone",'.
            $obj->id.
            ','.
            $obj->x_axis.
            ','.
            $obj->y_axis.
            ','.
            $obj->zone_width.
            ','.
            $obj->zone_height.
            ',\''.
            $my_options['zone_mode'].
            '\');';

        $image_to_map_desc .= '</script>';
        $nbs = [];
        for ($i = 1; $i < 30; ++$i) {
            $nbs[] = ['id' => $i, 'name' => $i];
        }

        $effects = [
            [
                'id_effect' => 'concavMe',
                'val' => 'concavMe',
                'name' => $this->l('metallic concav'),
            ],
            [
                'id_effect' => 'applatMe',
                'val' => 'applatMe',
                'name' => $this->l('normal'),
            ],
            [
                'id_effect' => 'outlineMe',
                'val' => 'outlineMe',
                'name' => $this->l('outline'),
            ],
            [
                'id_effect' => 'convexMe',
                'val' => 'convexMe',
                'name' => $this->l('metallic convex'),
            ],
        ];
        $alignments = [
            ['id' => 'left', 'val' => 'left', 'name' => $this->l('left')],
            ['id' => 'center', 'val' => 'center', 'name' => $this->l('center')],
            ['id' => 'right', 'val' => 'right', 'name' => $this->l('right')],
        ];

        $color_effects = [
            [
                'id_effect' => 'normal',
                'val' => 'normal',
                'name' => $this->l('normal'),
            ],
            [
                'id_effect' => 'multiply',
                'val' => 'applatMe',
                'name' => $this->l('multiply'),
            ],
            [
                'id_effect' => 'overlay',
                'val' => 'applatMe',
                'name' => $this->l('overlay'),
            ],
            [
                'id_effect' => 'darken',
                'val' => 'applatMe',
                'name' => $this->l('darken'),
            ],
            [
                'id_effect' => 'lighten',
                'val' => 'applatMe',
                'name' => $this->l('lighten'),
            ],
            [
                'id_effect' => 'hard-light',
                'val' => 'applatMe',
                'name' => $this->l('Hard light'),
            ],
            [
                'id_effect' => 'soft-light',
                'val' => 'applatMe',
                'name' => $this->l('Soft light'),
            ],
            [
                'id_effect' => 'color-burn',
                'val' => 'applatMe',
                'name' => $this->l('Color Burn'),
            ],
            [
                'id_effect' => 'color',
                'val' => 'applatMe',
                'name' => $this->l('color'),
            ],
            [
                'id_effect' => 'exclusion',
                'val' => 'applatMe',
                'name' => $this->l('exclusion'),
            ],
            [
                'id_effect' => 'hue',
                'val' => 'applatMe',
                'name' => $this->l('hue'),
            ],
            [
                'id_effect' => 'saturation',
                'val' => 'applatMe',
                'name' => $this->l('saturation'),
            ],
            [
                'id_effect' => 'luminosity',
                'val' => 'applatMe',
                'name' => $this->l('luminosity'),
            ],
        ];

        $typedesc =
            '<span class="hidden-note visible-note-0">'.
            $this->l(
                'Note : you can set the number of lines for your text fIelds'
            ).
            '</span>'.
            '<span class="hidden-note visible-note-1">'.
            $this->l(
                'You will have to create values once field saved, it will show a selector with value you will define'
            ).
            '</span>'.
            '<span class="hidden-note visible-note-2">'.
            $this->l(
                'You will have to create values once field saved, and add one image per value'
            ).
            '</span>'.
            '<span class="hidden-note visible-note-3">'.
            $this->l(
                'You will have to create values once field saved, and add one image per value if you want a visual effect'
            ).
            '</span>'.
            '<span class="hidden-note visible-note-4">'.
            $this->l('You will have to create values once field saved').
            '</span>'.
            '<span class="hidden-note visible-note-5">'.
            $this->l(
                'Type "Mask" is not used for customization calculation, but allow you to delimit design with transparent png, wich is appended as a layer.'
            ).
            '</span>'.
            '<span class="hidden-note visible-note-10">'.
            $this->l(
                'Type "View" is not used for customization calculation, but allow you to delimit design with arbitrary selection. Once a view is created, you can link you fields to this. exemple : you will create a selection of image for a tee-shirt, you can link this field to the view named "front" and draw a rectangle on this view to delimit position of this image'
            ).
            '</span>';

        if ($obj = $this->loadObject(true)) {
            $selected_aligments = explode(';', $obj->alignments);
            $this->fields_value['alignments[]'] = $selected_aligments;
            $selected_effects = explode(';', $obj->effects);
            $this->fields_value['effects[]'] = $selected_effects;
            if ('' != $obj->options) {
                $options = Tools::jsonDecode($obj->options, true);
                foreach ($options as $k => $v) {
                    $this->fields_value['false_options['.$k.']'] = $v;
                }
            }
        }

        $additionnals_picto = '';
        if (
            file_exists(
                _PS_IMG_DIR_.'scenes/'.'ndkcf/pictos/'.$obj->id.'.jpg'
            )
        ) {
            $additionnals_picto = $this->renderNdkImage(
                'img/scenes/ndkcf/pictos/'.$obj->id.'.jpg'
            );
        }

        $additionnals_mask = '';
        if (
            file_exists(
                _PS_IMG_DIR_.'scenes/'.'ndkcf/mask/'.$obj->id.'.jpg'
            )
        ) {
            $additionnals_mask = $this->renderNdkImage(
                'img/scenes/ndkcf/mask/'.$obj->id.'.jpg',
                'mask'
            );
        }
        //mise à jour csv
        if (
            file_exists(
                _PS_IMG_DIR_.'scenes/'.'ndkcf/'.(int) $obj->id.'.csv'
            )
        ) {
            $reqs = Db::getInstance()->getRow(
                'SELECT COUNT(id_ndk_customization_field) as nb FROM '.
                    _DB_PREFIX_.
                    'ndk_customization_field_csv WHERE id_ndk_customization_field = '.
                    (int) $obj->id
            );
            if ($reqs['nb'] < 1) {
                Ndkcf::recordCsv($obj->id);
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
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'form_group_class' => 'ndk-main',
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Admin name'),
                    'class' => 'adminName',
                    'name' => 'admin_name',
                    'lang' => true,
                    'required' => true,
                    'size' => 48,
                    'hint' => $this->l('Invalid characters:').' <>;=#{}',
                    'form_group_class' => 'ndk-main',
                ],

                [
                    'type' => 'textarea',
                    'label' => $this->l('Notice'),
                    'name' => 'notice',
                    'lang' => true,
                    'size' => 48,
                    'autoload_rte' => true,
                    'hint' => $this->l(
                        'A small description or notice for your field, will be used as title for gift pdf'
                    ),
                    'form_group_class' => 'ndk-main',
                ],

                [
                    'type' => 'textarea',
                    'label' => $this->l('Tootlip'),
                    'name' => 'tooltip',
                    'lang' => true,
                    'size' => 48,
                    'autoload_rte' => true,
                    'hint' => $this->l(
                        'Additionnal information to show in tooltip'
                    ),
                    'form_group_class' => 'ndk-main',
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Type'),
                    'name' => 'type',
                    'hint' => $this->l('Select the type of field.'),
                    'required' => true,
                    'options' => [
                        'query' => $this->types,
                        'id' => 'id_type',
                        'name' => 'name',
                    ],
                    'desc' => $typedesc,
                    'form_group_class' => 'ndk-main',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Picto:'),
                    'name' => 'picto',
                    'display_image' => true,
                    'desc' => $additionnals_picto,
                    'hint' => $this->l('image to show in field title'),
                    'form_group_class' => 'ndk-design',
                ],

                [
                    'type' => 'file',
                    'label' => $this->l('Mask image:'),
                    'name' => 'mask',
                    'display_image' => true,
                    'desc' => $additionnals_mask,
                    'hint' => $this->l('image to use as clipping mask'),
                    'class' => 'hidden-field visible-3 visible-12 visible-6',
                    'form_group_class' => 'ndk-visual hidden-field visible-3 visible-12 visible-6',
                ],

                [
                    'type' => 'select',
                    'class' => '',
                    'label' => $this->l('Lines number (for text only)'),
                    'name' => 'nb_lines',
                    'options' => [
                        'query' => $nbs,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'form_group_class' => 'ndk-text',
                ],

                [
                    'type' => 'text',
                    'label' => $this->l(
                        'Maximum of characters (per line if text, total if textarea)'
                    ),
                    'name' => 'maxlength',
                    'class' => '',
                    'form_group_class' => 'ndk-text',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Fonts'),
                    'name' => 'fonts',
                    'lang' => false,
                    'size' => 48,
                    'autoload_rte' => false,
                    'hint' => $this->l(
                        'Google fonts for this field (for text only), if not specified, global configuration will be used'
                    ),
                    'class' => '',
                    'form_group_class' => 'ndk-text',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Colors'),
                    'name' => 'colors',
                    'lang' => false,
                    'size' => 48,
                    'autoload_rte' => false,
                    'hint' => $this->l(
                        'Colors (separated with ;) for this field (for text only), if not specified, global configuration will be used'
                    ),
                    'class' => '',
                    'form_group_class' => 'ndk-text ndk-design',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('purpose stroke colors :'),
                    'name' => 'stroke_color',
                    'required' => false,
                    'desc' => '<span class="">'.
                        $this->l(
                            'colors will be purposed as stroke colors to'
                        ).
                        '</span>',
                    'class' => 't',
                    'form_group_class' => 'ndk-text',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('sizes'),
                    'name' => 'sizes',
                    'lang' => false,
                    'size' => 48,
                    'autoload_rte' => false,
                    'hint' => $this->l(
                        'Sizes in px (separated with ;) for this field (for text only), if not specified, global configuration will be used'
                    ),
                    'form_group_class' => 'ndk-text ',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Visual effect:'),
                    'name' => 'is_visual',
                    'required' => false,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-11 hidden-23  hidden-20 hidden-8 hidden-99 visible-28">'.
                        $this->l('The element will be show on product image').
                        '</span>',
                    'class' => 't visible-field hidden-10 hidden-5 hidden-11 hidden-23 hidden-8 hidden-99 ',
                    'form_group_class' => 'ndk-visual visible-field hidden-10 hidden-5 hidden-11 hidden-23 hidden-8 hidden-99 ',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Target'),
                    'name' => 'target',
                    'class' => 'visible-field hidden-10 visual-child hidden-99  chosen',
                    'required' => true,
                    'options' => [
                        'query' => $targets,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'form_group_class' => 'ndk-design hidden-10 visual-child hidden-99 visible-field',
                    'desc' => $this->l(
                            'You can specify on which view (might be created first) the visual effect will appear, and delimit (or not) an area for this'
                        ).
                        '<script>var svgPath = "'.
                        $obj->svg_path.
                        '"; var target_child = parseInt('.
                        ($obj->target_child > 0 ? $obj->target_child : 0).
                        ')</script>'.
                        $image_to_map_desc,
                ],
                [
                    'type' => 'select',
                    'class' => 'chosen',
                    'label' => $this->l('Get values from'),
                    'name' => 'values_from_id',
                    'options' => [
                        'query' => $values_from_fields,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l(
                        'You can load values from other field to win time'
                    ),
                    'form_group_class' => 'ndk-main visible-field',
                ],

                [
                    'type' => 'select',
                    'class' => 'hidden-field visible-0  visible-1 visible-2 visible-3 visible-4 visible-6 visible-7 visible-13 visible-14 visible-15 visible-16 visible-25 chosen',
                    'label' => $this->l('Quantity link'),
                    'name' => 'quantity_link',
                    'options' => [
                        'query' => $quantity_link_fields,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l(
                        'You can link this field to a quantifiable field, so its price will be multiplied by the quantity selected in the linked field'
                    ),
                    'form_group_class' => 'ndk-main hidden-field visible-0  visible-1 visible-2 visible-3 visible-4 visible-6 visible-7 visible-13 visible-14 visible-15 visible-8 visible-16 visible-25 visible-11 visible-23',
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Influences'),
                    'multiple' => true,
                    'name' => 'influences[]',
                    'class' => 'chosen',
                    'form_group_class' => 'ndk-main visible-field hidden-0 hidden-5 hidden-13 hidden-10 hidden-13 hidden-14 hidden-15 visual-child chosen hidden-8 hidden-99',
                    'required' => true,
                    'options' => [
                        'query' => $influences,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l(
                        'You can specify a field to influence it according to current creating field values'
                    ),
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Join feature'),
                    'name' => 'feature',
                    'class' => 'hidden-field visible-1 visible-4  hidden-11 hidden-23 hidden-99 hidden-28',
                    'form_group_class' => 'ndk-main hidden-field visible-1 visible-4  hidden-11 hidden-23 hidden-99 hidden-28',
                    'required' => true,
                    'options' => [
                        'query' => $features,
                        'id' => 'id_feature',
                        'name' => 'name',
                    ],
                    'desc' => $this->l(
                        'If you link to a feature, the default value will be the product feature value (only for select and checkbox fields).'
                    ),
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Price (tax excl.)'),
                    'class' => 'visible-field price-input',
                    'form_group_class' => 'ndk-main',
                    'name' => 'price',
                    'hint' => $this->l(
                        'This will be the default price for this fields and all values.'
                    ),
                    'size' => 8,
                    'desc' => '<div class="hidden-18 hidden-19 hidden-21 hidden-19 hidden-5 hidden-10"><div><div class="hidden-field visible-17 visible-24 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28">'
                        .$specificPriceBlock.
                        '</div><div class="hidden-field visible-8 ">'.
                        $this->l('Enter price per unit.').
                        '</div></div></div>',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Unit (eg.m2)'),
                    'class' => 'hidden-field visible-8',
                    'form_group_class' => 'ndk-main hidden-field visible-8',
                    'name' => 'unit',
                    'desc' => $this->l('Enter unit used for dimensions'),
                ],

                [
                    'type' => 'switch',
                    'label' => $this->l('Preserve ratio:'),
                    'name' => 'preserve_ratio',
                    'required' => false,
                    'desc' => '<span class="hidden-field visible-8">'.
                        $this->l(
                            'if yes ratio will be calculated on min quantity for each value'
                        ).
                        '</span>',
                    'class' => 't hidden-field visible-8',
                    'form_group_class' => 'ndk-main hidden-field visible-8',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],

                [
                    'type' => 'text',
                    'class' => 'hidden-field visible-11 visible-17 visible-16 visible-24 visible-23 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28 visible-29 visible-30 visible-14',
                    'form_group_class' => 'ndk-crossselling ndk-main hidden-field visible-11 visible-17 visible-24 visible-23 visible-22 visible-26 visible-27 visible-101 visible-29 visible-30  hidden-99 hidden-28 visible-16 visible-14',
                    'label' => $this->l('Minimal Quantity'),
                    'name' => 'quantity_min',
                    'hint' => $this->l('Specify minimal allowed quantity.'),
                ],
                [
                    'type' => 'text',
                    'class' => 'hidden-field visible-11 visible-17 visible-16 visible-24 visible-23 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28 visible-29 visible-30 visible-14',
                    'form_group_class' => 'ndk-crossselling ndk-main hidden-field visible-11 visible-17 visible-24 visible-23 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28 visible-29 visible-30 visible-14 visible-16 visible-14',
                    'label' => $this->l('Maximal Quantity'),
                    'name' => 'quantity_max',
                    'hint' => $this->l('Specify maximal allowed quantity.'),
                ],
                [
                    'type' => 'text',
                    'class' => 'hidden-field visible-11 visible-17  visible-24 visible-23 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28',
                    'form_group_class' => 'ndk-crossselling hidden-field visible-11 visible-17 visible-24 visible-23 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28',
                    'label' => $this->l('Minimal Weight').
                        ' ('.
                        Configuration::get('PS_WEIGHT_UNIT').
                        ')',
                    'name' => 'weight_min',
                    'hint' => $this->l('Specify minimal allowed weight.').
                        ' ('.
                        Configuration::get('PS_WEIGHT_UNIT').
                        ')',
                ],
                [
                    'type' => 'text',
                    'class' => 'hidden-field visible-11 visible-17 visible-24 visible-23 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28',
                    'form_group_class' => 'ndk-crossselling hidden-field visible-11 visible-17 visible-24 visible-23 visible-22 visible-26 visible-27 visible-101  hidden-99 hidden-28',
                    'label' => $this->l('Maximal Weight').
                        ' ('.
                        Configuration::get('PS_WEIGHT_UNIT').
                        ')',
                    'name' => 'weight_max',
                    'hint' => $this->l('Specify maximal allowed weight.').
                        ' ('.
                        Configuration::get('PS_WEIGHT_UNIT').
                        ')',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Price type'),
                    'name' => 'price_type',
                    'class' => 'visible-field visible-1 visible-2 visible-3 visible-25 visible-4 visible-14 visible-16 visible-18 visible-19 hidden-99 hidden-28',
                    'form_group_class' => 'ndk-main visible-field visible-1 visible-2 visible-3 visible-25 visible-4 visible-14 visible-16 visible-18 visible-19 hidden-99 hidden-28',
                    'required' => true,
                    'options' => [
                        'query' => [
                            [
                                'id_price_type' => 'amount',
                                'name' => $this->l('amount'),
                            ],
                            [
                                'id_price_type' => 'percent',
                                'name' => $this->l('percent'),
                            ],
                        ],
                        'id' => 'id_price_type',
                        'name' => 'name',
                    ],
                    'desc' => $this->l(
                        'Choose if field increase of amount or percent.'
                    ),
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Price per caracter (tax excl.)'),
                    'class' => '',
                    'form_group_class' => 'ndk-text',
                    'name' => 'price_per_caracter',
                    'hint' => $this->l(
                        'only for text field, leave empty if you don’t want use it.'
                    ),
                    'size' => 8,
                ],

                [
                    'type' => 'radio',
                    'label' => $this->l('Show price:'),
                    'name' => 'show_price',
                    'required' => false,
                    'desc' => '',
                    'class' => 't visible-field',
                    'form_group_class' => 'ndk-main visible-field',
                    'is_bool' => false,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                        [
                            'id' => 'active_on',
                            'value' => 2,
                            'label' => $this->l('Only on Overview'),
                        ],
                    ],
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Position'),
                    'form_group_class' => 'ndk-design visible-field',
                    'name' => 'position',
                    'size' => 8,
                    'desc' => $this->l('Position in field list'),
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Reference position'),
                    'name' => 'ref_position',
                    'form_group_class' => 'ndk-main visible-field',
                    'size' => 8,
                    'desc' => $this->l(
                        'Position in reference and registered field'
                    ),
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Z index'),
                    'form_group_class' => 'ndk-visual hidden-11 hidden-23 hidden-8 hidden-99',
                    'class' => 'hidden-11 hidden-23 hidden-8 hidden-99 ',
                    'name' => 'zindex',
                    'size' => 8,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-11 hidden-23 hidden-17 hidden-24 hidden-20">'.
                        $this->l('Layer position on image').
                        '</span>',
                ],

                [
                    'type' => 'text',
                    'label' => $this->l('Validity (in days)'),
                    'class' => 'hidden-field visible-12 hidden-99 hidden-28',
                    'form_group_class' => 'ndk-main hidden-field visible-12 hidden-99 hidden-28',
                    'name' => 'validity',
                    'size' => 8,
                    'desc' => $this->l('Validity i days if it‘s a gift'),
                ],

                [
                    'type' => 'switch',
                    'label' => $this->l('Required:'),
                    'name' => 'required',
                    'required' => false,
                    'class' => 't visible-field hidden-10 hidden-  hidden-99 hidden-28',
                    'form_group_class' => 'ndk-main visible-field hidden-10 hidden-  hidden-99 hidden-28',
                    'is_bool' => true,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-11 hidden-23 hidden-17 hidden-24 hidden-22 hidden-26 hidden-20  hidden-99 hidden-28"></span>',

                    'values' => [
                        [
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Purpose text'),
                    'name' => 'false_options[purpose_text]',
                    'class' => 'serialize_input ',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 'on',
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 'off',
                            'label' => $this->l('No'),
                        ],
                    ],
                    'form_group_class' => 'ndk-design serialize_input hidden-field visible-14',
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Purpose text (1 ligne, no backspace)'),
                    'name' => 'false_options[purpose_textsimple]',
                    'class' => 'serialize_input ',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 'on',
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 'off',
                            'label' => $this->l('No'),
                        ],
                    ],
                    'form_group_class' => 'ndk-design serialize_input hidden-field visible-14',
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Purpose upload'),
                    'name' => 'false_options[purpose_upload]',
                    'class' => 'serialize_input ',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 'on',
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 'off',
                            'label' => $this->l('No'),
                        ],
                    ],
                    'form_group_class' => 'ndk-design serialize_input hidden-field visible-14',
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Purpose images'),
                    'name' => 'false_options[purpose_images]',
                    'class' => 'serialize_input ',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 'on',
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 'off',
                            'label' => $this->l('No'),
                        ],
                    ],
                    'form_group_class' => 'ndk-design serialize_input hidden-field visible-14',
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Open status:'),
                    'name' => 'open_status',
                    'required' => false,
                    'class' => 't visible-field hidden-10 hidden-  hidden-99 ',
                    'form_group_class' => 'ndk-design  visible-field hidden-10 hidden-  hidden-99 hidden-28',
                    'is_bool' => false,
                    'desc' => '<span class="visible-field hidden-99 ">'.
                        $this->l(
                            'Do you want field to be opened on page load ?'
                        ).
                        '</span>',

                    'options' => [
                        'query' => $this->open_statuses,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('3D material correspondance'),
                    'name' => 'false_options[material_name]',
                    'class' => 'serialize_input',
                    'form_group_class' => 'ndk-design',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Custom class'),
                    'name' => 'false_options[custom_class]',
                    'class' => 'serialize_input',
                    'form_group_class' => 'ndk-design',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Max width (%)'),
                    'desc' => $this->l('Set the max height (in % of zone) for element'),
                    'name' => 'false_options[resize-max-width]',
                    'class' => 'serialize_input',
                    'form_group_class' => 'ndk-design',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Max height (%)'),
                    'desc' => $this->l('Set the max height (in % of zone) for element'),
                    'name' => 'false_options[resize-max-height]',
                    'class' => 'serialize_input',
                    'form_group_class' => 'ndk-design',
                ],
                
                [
                    'type' => 'switch',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'label' => $this->l('Fill with placeholder'),
                    'name' => 'false_options[fill_placeholder]',
                    'desc' => $this->l(
                        'Will automatically fill text box with placeholder.'
                    ),
                    'class' => 't ',
                    'form_group_class' => 'ndk-text serialize_input',
                ],

                // [
                //     'type' => 'switch',
                //     'values' => [
                //         [
                //             'id' => 'active_on',
                //             'value' => 1,
                //             'label' => $this->l('Yes'),
                //         ],
                //         [
                //             'id' => 'active_off',
                //             'value' => 0,
                //             'label' => $this->l('No'),
                //         ],
                //     ],
                //     'label' => $this->l('Hide on page load'),
                //     'name' => 'false_options[hidden_on_load]',
                //     'desc' => $this->l(
                //         'Will automatically hide field on page load.'
                //     ),
                //     'class' => 't ',
                //     'form_group_class' => 'ndk-design serialize_input',
                // ],

                [
                    'type' => 'switch',
                    'label' => $this->l('Recommend:'),
                    'name' => 'recommend',
                    'required' => false,
                    'class' => 't visible-field hidden-10 hidden-  hidden-99 ',
                    'form_group_class' => 'ndk-main  visible-field hidden-10 hidden-  hidden-99 ',
                    'is_bool' => true,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-99 ">'.
                        $this->l(
                            'If yes module will recommend to fill this field without stop process'
                        ).
                        '</span>',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],

                [
                    'type' => 'file',
                    'label' => $this->l('CSV file:'),
                    'name' => 'csv',
                    'display_image' => true,
                    'desc' => '<span class="hidden-field visible-18 visible-19 visible-21 hidden-99 hidden-28">'.
                        $this->l('Add here your csv price range files').
                        '</span>',
                    'class' => 'hidden-field visible-18 visible-19 visible-21',
                    'form_group_class' => 'ndk-main visible-18 visible-19 visible-21',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Configurator (for text only) :'),
                    'name' => 'configurator',
                    'required' => false,
                    'desc' => '<span class="">'.
                        $this->l(
                            'Allow customer choose text color, size and font'
                        ).
                        '</span>',
                    'class' => 't',
                    'form_group_class' => 'ndk-text',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Effects (for text only) :'),
                    'name' => 'effects[]',
                    'required' => false,
                    'multiple' => true,
                    'desc' => '<span class="">'.
                        $this->l('text effects').
                        '</span>',
                    'class' => 't visual-child chosen',
                    'form_group_class' => 'ndk-text ',
                    'options' => [
                        'query' => $effects,
                        'id' => 'id_effect',
                        'name' => 'name',
                    ],
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Aligments (for text only) :'),
                    'name' => 'alignments[]',
                    'required' => false,
                    'multiple' => true,
                    'desc' => '<span class="">'.
                        $this->l('text alignments available').
                        '</span>',
                    'class' => 't visual-child chosen',
                    'form_group_class' => 'ndk-text',
                    'options' => [
                        'query' => $alignments,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],

                [
                    'type' => 'select',
                    'label' => $this->l('Effects (mix blend mode) :'),
                    'name' => 'color_effect',
                    'required' => false,
                    'multiple' => false,
                    'desc' => $this->l('color effect'),
                    'class' => 't visual-child chosen ',
                    'form_group_class' => 'ndk-design',
                    'options' => [
                        'query' => $color_effects,
                        'id' => 'id_effect',
                        'name' => 'name',
                    ],
                ],

                [
                    'type' => 'switch',
                    'label' => $this->l('Resizeable :'),
                    'name' => 'resizeable',
                    'required' => false,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-11 hidden-23  hidden-22 hidden-26 hidden-18 hidden-19 hidden-21 hidden-19 hidden-20 hidden-8 hidden-99 ">'.
                        $this->l('Allow customer to resize element').
                        '</span>',
                    'class' => 't visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99 ',
                    'form_group_class' => 'ndk-visual visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99 ',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Draggable :'),
                    'name' => 'draggable',
                    'required' => false,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-11 hidden-23  hidden-22 hidden-26 hidden-18 hidden-19 hidden-21 hidden-19 hidden-20 hidden-8 hidden-99">'.
                        $this->l('Allow customer to move element').
                        '</span>',
                    'class' => 't visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99 ',
                    'form_group_class' => 'ndk-visual visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99 ',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Rotateable :'),
                    'name' => 'rotateable',
                    'required' => false,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-11 hidden-23 hidden-22 hidden-26 hidden-18 hidden-19 hidden-21 hidden-19 hidden-20 hidden-8 hidden-99">'.
                        $this->l('Allow customer to rotate element').
                        '</span>',
                    'class' => 't visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99',
                    'form_group_class' => 'ndk-visual visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'label' => $this->l('Clippable'),
                    'name' => 'false_options[clippable]',
                    'desc' => $this->l('Will allow to crop on the fly.'),
                    'class' => 't visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99',
                    'form_group_class' => 'ndk-visual visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99 serialize_input',
                ],
                [
                    'type' => 'switch',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                    'label' => $this->l('Curvable (text only)'),
                    'name' => 'false_options[curvable]',
                    'desc' => $this->l(
                        'Will allow to curve text on the fly. (experimental)'
                    ),
                    'class' => 't visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99',
                    'form_group_class' => 'ndk-text visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99 serialize_input',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Orientable :'),
                    'name' => 'orienteable',
                    'required' => false,
                    'desc' => '<span class="visible-field hidden-5 hidden-10 hidden-11 hidden-23  hidden-22 hidden-26 hidden-18 hidden-19 hidden-21 hidden-19 hidden-20 hidden-8 hidden-99 ">'.
                        $this->l('Allow customer to change orientation').
                        '</span>',
                    'class' => 't visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99',
                    'form_group_class' => 'ndk-visual visible-field hidden-10 hidden-5 visual-child  hidden-11 hidden-23 hidden-99 ',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
            ],
        ];

        $line_spacings = [];
        for ($i = -100; $i <= 100; ++$i) {
            $line_spacings[] = ['id' => $i, 'name' => $i];
        }
        $this->fields_form['input'][] = [
            'type' => 'select',
            'label' => $this->l('Line spacing'),
            'name' => 'false_options[text_line_spacing]',
            'class' => 'serialize_input',
            'options' => [
                'query' => $line_spacings,
                'id' => 'id',
                'name' => 'name',
            ],
            'form_group_class' => 'ndk-text',
        ];

        $this->fields_form['input'][] = [
            'type' => 'select',
            'label' => $this->l('Vertical alignment'),
            'name' => 'false_options[text_vertical_align]',
            'class' => 'serialize_input',
            'options' => [
                'query' => [
                    ['id' => 'top', 'name' => $this->l('Top')],
                    ['id' => 'bottom', 'name' => $this->l('Bottom')],
                    ['id' => 'middle', 'name' => $this->l('Middle')],
                ],
                'id' => 'id',
                'name' => 'name',
            ],
            'form_group_class' => 'ndk-text',
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
                'form_group_class' => 'ndk-main',
            ];
        }

        $this->fields_form['submit'] = [
            'title' => $this->l('Save'),
        ];
        $this->fields_form['buttons'] = [
            'save-and-stay' => [
                'title' => $this->l('Save and stay'),
                'name' => 'submitAdd'.$this->table.'AndStay',
                'type' => 'submit',
                'class' => 'btn btn-default pull-right',
                'icon' => 'process-icon-save',
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

        $this->addJqueryPlugin(['autocomplete', 'imgareaselect']);
        //$this->addJs(_MODULE_DIR_.'ndk_advanced_custom_fields/views/js/admin.js' );
        $this->addCSS(
            _MODULE_DIR_.'ndk_advanced_custom_fields/views/css/admin.css',
            'all',
            false
        );
        $obj = $this->loadObject(true);

        $lightProds = NdkCf::getproductsLight($obj->products);
        $this->context->smarty->assign(['objprods' => $lightProds]);
        $searchbox = $this->context->smarty->fetch(
            _PS_ROOT_DIR_.
                '/modules/ndk_advanced_custom_fields/views/templates/admin/prodbox.tpl'
        );

        $this->fields_form['input'][] = [
            'type' => 'text',
            'hint' => $this->l('Activate this field for theses products.'),
            'label' => $this->l('Products'),
            'name' => 'products',
            'class' => 'product-result :not(.always-visible)',
            'form_group_class' => 'ndk-main always-visible',
            'size' => 48,
            'desc' => $this->l('Activate this field for theses products.').
                $searchbox,
        ];

        $this->fields_form['input'][] = [
            'type' => 'categories_select',
            'label' => $this->l('Categories'),
            'desc' => $this->l('Activate this field for theses categories.'),
            'name' => 'categories',
            'form_group_class' => 'ndk-main always-visible',
            'category_tree' => $categoryTree, //This is the category_tree called in form.tpl
        ];

        return parent::renderForm();
    }

    public function postProcess()
    {
        $selected_cat = [];
        if (Tools::isSubmit('categories')) {
            foreach (Tools::getValue('categories') as $row) {
                $selected_cat[] = $row;
            }
            $_POST['categories'] = implode(',', $selected_cat);
        } else {
            $_POST['categories'] = '';
        }

        if (Tools::isSubmit('excludes_categories')) {
            foreach (Tools::getValue('excludes_categories') as $row) {
                $selected_cat[] = $row;
            }

            $_POST['excludes_categories'] = implode(',', $selected_cat);
        } else {
            $_POST['excludes_categories'] = '';
        }

        if (Tools::isSubmit('influences')) {
            $_POST['influences'] = implode(
                ',',
                array_filter(Tools::getValue('influences'))
            );
            //$this->purgeInfluences((int)Tools::getValue('id_ndk_customization_field'), Tools::getValue('influences'));
        }

        if (Tools::isSubmit('influences_restrictions')) {
            $_POST['influences_restrictions'] = implode(
                ',',
                array_filter(Tools::getValue('influences_restrictions'))
            );
        }

        if (Tools::isSubmit('influences_obligations')) {
            $_POST['influences_obligations'] = implode(
                ',',
                array_filter(Tools::getValue('influences_obligations'))
            );
        }

        if (Tools::isSubmit('effects')) {
            $_POST['effects'] = implode(
                ';',
                array_filter(Tools::getValue('effects'))
            );
        }

        if (Tools::isSubmit('alignments')) {
            $_POST['alignments'] = implode(
                ';',
                array_filter(Tools::getValue('alignments'))
            );
        }

        if (
            Tools::getValue('default_value') &&
            (int) Tools::getValue('default_value') > 0
        ) {
            $this->setDefaultValue(
                (int) Tools::getValue('id_ndk_customization_field_value'),
                (int) Tools::getValue('id_ndk_customization_field')
            );
        }

        if (
            !Tools::getValue($this->identifier) &&
            Tools::getValue('id_ndk_customization_field_value') &&
            !Tools::getValue('ndk_customization_field_valueOrderby')
        ) {
            // Override var of Controller
            $this->table = 'ndk_customization_field_value';
            $this->className = 'NdkCfValues';
            $this->identifier = 'id_ndk_customization_field_value';
        }

        if (
            Tools::getValue('updatendk_customization_field_value') ||
            Tools::getValue('deletendk_customization_field_value') ||
            Tools::getValue('submitAddndk_customization_field_value')
        ) {
            if (
                !($object = new NdkCfValues(
                    (int) Tools::getValue($this->identifier)
                ))
            ) {
                $this->errors[] =
                    Tools::displayError(
                        'An error occurred while updating the status for an object.'
                    ).
                    ' <b>'.
                    $this->table.
                    '</b> '.
                    Tools::displayError('(cannot load object)');
            }

            if (
                Tools::getValue('deletndk_customization_field_value') &&
                Tools::getValue('id_ndk_customization_field_value')
            ) {
                if (!$object->delete()) {
                    $this->errors[] = Tools::displayError(
                        'Failed to delete the value.'
                    );
                } else {
                    Tools::redirectAdmin(
                        self::$currentIndex.
                            '&conf=1&token='.
                            Tools::getAdminTokenLite('AdminNdkCustomFields')
                    );
                }
            }
        }

        if (Tools::getValue('way')) {
            $_POST['id_ndk_customization_field'] = Tools::getValue('id');
        }

        if (Tools::getValue('submitDel'.$this->table)) {
            if ('1' === $this->tabAccess['delete']) {
                if (Tools::getValue($this->table.'Box')) {
                    $object = new $this->className();
                    if (
                        $object->deleteSelection(
                            Tools::getValue($this->table.'Box')
                        )
                    ) {
                        Tools::redirectAdmin(
                            self::$currentIndex.
                                '&conf=2'.
                                '&token='.
                                $this->token
                        );
                    }
                    $this->errors[] = Tools::displayError(
                        'An error occurred while deleting this selection.'
                    );
                } else {
                    $this->errors[] = Tools::displayError(
                        'You must select at least one element to delete.'
                    );
                }
            } else {
                $this->errors[] = Tools::displayError(
                    'You do not have permission to delete this.'
                );
            }
            // clean position after delete
        } elseif (Tools::isSubmit('submitAdd'.$this->table)) {
            $id_ndk_customization_field = (int) Tools::getValue(
                'id_ndk_customization_field'
            );
            // Adding last position to the attribute if not exist
            if ($id_ndk_customization_field <= 0) {
                $sql =
                    'SELECT `position`+1
                           FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field`
                           WHERE `id_ndk_customization_field` = '.
                    (int) Tools::getValue('id_ndk_customization_field').
                    '
                           ORDER BY position DESC';
                // set the position of the new group attribute in $_POST for postProcess() method
                $_POST['position'] = Db::getInstance(
                    _PS_USE_SQL_SLAVE_
                )->getValue($sql);
            }
            //$_POST['id_parent'] = 0;

            // clean \n\r characters
            foreach ($_POST as $key => $value) {
                if (preg_match('/^name_/Ui', $key)) {
                    $_POST[$key] = str_replace(
                        '\n',
                        '',
                        str_replace('\r', '', $value)
                    );
                }
            }

            $this->processSave($this->token);
        //parent::postProcess();
        } elseif (Tools::isSubmit('duplicate'.$this->table)) {
            $this->processDuplicate();
        } elseif (
            (Tools::getIsset($_POST) && count($_POST) > 0) ||
            Tools::isSubmit('submitReset'.$this->list_id)
        ) {
            //$_POST['page'] = (int)Tools::getValue('submitFilter'.$this->list_id);
            $_POST['submitFilter'.$this->list_id] = false;
            parent::postProcess();
        } elseif (Tools::getValue('submitFilterndk_customization_field')) {
            parent::postProcess();
            $this->redirect_after =
                self::$currentIndex.'&token='.$this->token;
        } else {
            parent::postProcess();
        }
    }

    /**
     * @param mixed $id_ndk_customization_field
     * @param mixed $influences
     *
     * @return void
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public static function purgeInfluences(
        $id_ndk_customization_field,
        $influences
    ) {
        $field = new NdkCf((int) $id_ndk_customization_field);
        $values = $field->getValues();
        $influences = explode(',', $influences);
        foreach ($values as $value) {
            if (
                !empty($value['influences_restrictions']) &&
                '' != $value['influences_restrictions']
            ) {
                $array_restrictions = [];
                if ('' != $influences) {
                    foreach ($influences as $influence) {
                        $restrictions = explode(
                            ',',
                            $value['influences_restrictions']
                        );
                        foreach ($restrictions as $restriction) {
                            $restriction_id = explode('-', $restriction);
                            if (isset($restriction_id[1])) {
                                if ($restriction_id[1] == $influence) {
                                    $array_restrictions[] = $restriction;
                                }
                            }
                        }
                    }
                }
                $valObj = new NdkCfValues((int) $value['id']);
                $valObj->influences_restrictions = implode(
                    ',',
                    $array_restrictions
                );
                $valObj->save();
            }

            if (
                !empty($value['influences_obligations']) &&
                '' != $value['influences_obligations']
            ) {
                $array_obligations = [];
                if ('' != $influences) {
                    foreach ($influences as $influence) {
                        $obligations = explode(
                            ',',
                            $value['influences_obligations']
                        );
                        foreach ($obligations as $obligation) {
                            $obligation_id = explode('-', $obligation);
                            if ($obligation_id[1] == $influence) {
                                $array_obligations[] = $obligation;
                            }
                        }
                    }
                }
                $valObj = new NdkCfValues((int) $value['id']);
                $valObj->influences_restrictions = implode(
                    ',',
                    $array_restrictions
                );
                $valObj->influences_obligations = implode(
                    ',',
                    $influences_obligations
                );
                $valObj->save();
            }
        }
    }

    public static function setDefaultValue(
        $id_ndk_customization_field_value,
        $id_ndk_customization_field
    ) {
        AdminNdkCustomFieldsController::clearAllCache();
        $value = new NdkCfValues((int) $id_ndk_customization_field_value);
        $field = new NdkCf((int) $id_ndk_customization_field);

        if (
            0 == $value->default_value &&
            29 != $field->type &&
            30 != $field->type
        ) {
            Db::getInstance()->execute(
                'UPDATE '.
                    _DB_PREFIX_.
                    'ndk_customization_field_value  SET default_value =0 WHERE id_ndk_customization_field = '.
                    (int) $id_ndk_customization_field
            );
        }

        Db::getInstance()->execute(
            'UPDATE '.
                _DB_PREFIX_.
                'ndk_customization_field_value  SET default_value = 
         case
            when default_value = 0 then 1
         else 0
            end 
         WHERE id_ndk_customization_field_value = '.
                (int) $id_ndk_customization_field_value
        );
    }

    public static function setPosition($id_ndk_customization_field, $position)
    {
        Db::getInstance()->execute(
            'UPDATE '.
                _DB_PREFIX_.
                'ndk_customization_field  SET position ='.
                (int) $position.
                ' WHERE id_ndk_customization_field = '.
                (int) $id_ndk_customization_field
        );
        Db::getInstance()->Execute(
            'TRUNCATE TABLE `'._DB_PREFIX_.'ndk_customization_field_cache`'
        );
    }

    public static function setRefPosition(
        $id_ndk_customization_field,
        $position
    ) {
        Db::getInstance()->execute(
            'UPDATE '.
                _DB_PREFIX_.
                'ndk_customization_field  SET ref_position ='.
                (int) $position.
                ' WHERE id_ndk_customization_field = '.
                (int) $id_ndk_customization_field
        );
    }

    public static function setZindex($id_ndk_customization_field, $zindex)
    {
        Db::getInstance()->execute(
            'UPDATE '.
                _DB_PREFIX_.
                'ndk_customization_field  SET zindex ='.
                (int) $zindex.
                ' WHERE id_ndk_customization_field = '.
                (int) $id_ndk_customization_field
        );
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $id_ndk_customization_field = (int) Tools::getValue(
            'id_ndk_customization_field'
        );
        $positions = Tools::getValue('ndk_customization_field');

        $new_positions = [];
        foreach ($positions as $k => $v) {
            if (4 == count(explode('_', $v))) {
                $new_positions[] = $v;
            }
        }

        foreach ($new_positions as $position => $value) {
            $pos = explode('_', $value);

            if (
                Tools::getIsset($pos[2]) &&
                (int) $pos[2] === $id_ndk_customization_field
            ) {
                if ($ndk_customization_field = new Ndkcf((int) $pos[2])) {
                    if (
                        Tools::getIsset($position) &&
                        $ndk_customization_field->updatePosition(
                            $way,
                            $position
                        )
                    ) {
                        echo 'ok position '.
                            (int) $position.
                            ' for field group '.
                            (int) $pos[2].
                            '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update the '.
                            (int) $ndk_customization_field.
                            ' field group to position '.
                            (int) $position.
                            ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "The ('.
                        (int) $id_ndk_customization_field.
                        ') field group cannot be loaded."}';
                }

                break;
            }
        }
    }

    public function initProcess()
    {
        $this->setTypeValues();

        if (Tools::getIsset('query_ajax_request')) {
            return $this->displayAjaxNdkAction();
        }

        if (
            Tools::getIsset('viewndk_customization_field') ||
            Tools::getIsset('submitFilterndk_customization_field_value')
        ) {
            $this->list_id = 'ndk_customization_field_value';

            if (
                Tools::getIsset($_POST) &&
                Tools::getIsset($_POST['submitReset'.$this->list_id])
            ) {
                $this->processResetFilters();
            }
        } else {
            $this->list_id = 'ndk_customization_field';
        }

        parent::initProcess();

        if ('ndk_customization_field_value' == $this->table) {
            $this->display = 'editndk_customization_field_value';
            $this->id_ndk_customization_field_value = (int) Tools::getValue(
                'id_ndk_customization_field_value'
            );
        }
    }

    protected function setTypeValues()
    {
        if (
            Tools::isSubmit('updatendk_customization_field_value') ||
            Tools::isSubmit('deletendk_customization_field_value') ||
            Tools::isSubmit('submitAddndk_customization_field_value') ||
            Tools::isSubmit('submitBulkdeletendk_customization_field_value')
        ) {
            $this->table = 'ndk_customization_field_value';
            $this->className = 'NdkCfValues';
            $this->identifier = 'id_ndk_customization_field_value';
        }
    }

    public function addMetaTitle($entry)
    {
        // Only add entry if the meta title was not forced.
        if (is_array($this->meta_title)) {
            $this->meta_title[] = $entry;
        }
    }

    public function processDelete()
    {
        $this->clearAllCache();
        if (
            'NdkCf' == $this->className &&
            Validate::isLoadedObject($object = $this->loadObject())
        ) {
            $childs = $object->getValuesId();
            foreach ($childs as $child) {
                $value = new NdkCfValues($child['id']);
                if (Validate::isLoadedObject($value)) {
                    //on supprime les images
                    $this->deleteImagesProper($value->id);
                    $value->delete();
                }
            }
        }

        parent::processDelete();
    }

    public static function deleteImagesProper($id_value)
    {
        $base_img_path =
            _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$id_value.'.jpg';
        $base_svg_path =
            _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$id_value.'.svg';
        $base_csv_path =
            _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$id_value.'.csv';
        $base_texture_path =
            _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$id_value.'-texture.jpg';
        if (file_exists($base_img_path)) {
            unlink($base_img_path);
        }

        if (file_exists($base_texture_path)) {
            unlink($base_texture_path);
        }

        if (file_exists($base_svg_path)) {
            unlink($base_svg_path);
        }

        if (file_exists($base_csv_path)) {
            unlink($base_csv_path);
            Db::getInstance()->execute(
                '
               DELETE FROM `'.
                    _DB_PREFIX_.
                    'ndk_customization_field_csv`
               WHERE id_ndk_customization_field = '.
                    (int) $id_value
            );
        }

        $images_types = ImageType::getImagesTypes('products');
        foreach ($images_types as $k => $image_type) {
            $thumb_path =
                _PS_IMG_DIR_.
                'scenes/'.
                'ndkcf/thumbs/'.
                $id_value.
                '-'.
                Tools::stripslashes($image_type['name']).
                '.jpg';

            if (file_exists($thumb_path)) {
                unlink($thumb_path);
            }
        }
    }

    public function processDuplicate()
    {
        $this->clearAllCache();
        $id = (int) Tools::getValue($this->identifier);

        if (isset($id) && !empty($id)) {
            $object = new $this->className($id);
            if (Validate::isLoadedObject($object)) {
                $object_new = $object->duplicateObject();
                $childs = $object->getValuesId();
                foreach ($childs as $child) {
                    $value = new NdkCfValues($child['id']);
                    if (Validate::isLoadedObject($value)) {
                        $value_new = $value->duplicateObject();
                        $value_new->id_ndk_customization_field =
                            $object_new->id;
                        $value_new->update();
                        NdkCfValues::duplicateSpecificPrice(
                            $id,
                            $object_new->id,
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
                        NdkCfValues::duplicateMP3($value->id, $value_new->id);
                    }
                }
            }
        }
    }

    public function processDuplicateValue()
    {
        $this->clearAllCache();
        $id = (int) Tools::getValue($this->identifier);
        $value = new NdkCfValues($id);
        if (Validate::isLoadedObject($value)) {
            $value_new = $value->duplicateObject();
            $value_new->update();
            NdkCfValues::duplicateSpecificPrice(
                $value->id_ndk_customization_field,
                $value_new->id_ndk_customization_field,
                $value->id,
                $value_new->id
            );
            NdkCfValues::duplicateImages($value->id, $value_new->id);
            NdkCfValues::duplicateImagesSvg($value->id, $value_new->id);
            NdkCfValues::duplicateMP3($value->id, $value_new->id);
        }
    }

    protected function clearAllCache()
    {
        Db::getInstance()->Execute(
            'TRUNCATE TABLE `'._DB_PREFIX_.'ndk_customization_field_cache`'
        );
    }

    public function getTruncatedValue($value)
    {
        return Tools::truncate($value, 120);
    }

    public function getTypeName($type)
    {
        foreach ($this->types as $row) {
            if ($row['id_type'] == $type) {
                return $row['name'];
            }
        }
    }

    public function displayAjaxNdkAction()
    {
        $additionnals = '';

        if (
            Tools::getValue('action') &&
            'saveSpecificPrice' == Tools::getValue('action')
        ) {
            $values = Tools::getValue('specificprice');
            if (
                (int) $values['id_ndk_customization_field_specific_price'] > 0
            ) {
                $specificPrice = new NdkCfSpecificPrice(
                    (int) $values['id_ndk_customization_field_specific_price']
                );
            } else {
                $specificPrice = new NdkCfSpecificPrice();
            }
            $specificPrice->id_ndk_customization_field =
                (int) $values['id_ndk_customization_field'];
            $specificPrice->id_ndk_customization_field_value =
                (int) $values['id_ndk_customization_field_value'];
            $specificPrice->reduction = $values['reduction'];
            $specificPrice->reduction_type = $values['reduction_type'];
            $specificPrice->from_quantity = (int) $values['from_quantity'];
            if ($specificPrice->save()) {
                $this->ajaxRender($specificPrice->id);
            }
        }

        if (
            Tools::getValue('action') &&
            'deleteSpecificPrice' == Tools::getValue('action')
        ) {
            $values = Tools::getValue('specificprice');
            if (
                (int) $values['id_ndk_customization_field_specific_price'] > 0
            ) {
                $specificPrice = new NdkCfSpecificPrice(
                    (int) $values['id_ndk_customization_field_specific_price']
                );
            }
            $specificPrice->delete();
        }

        if (
            Tools::getValue('action') &&
            'deleteFile' == Tools::getValue('action')
        ) {
            $file = _PS_ROOT_DIR_.Tools::getValue('file');
            @unlink($file);
            $thumb_file = str_replace(
                '/img/scenes/ndkcf/',
                '/img/scenes/ndkcf/thumbs/',
                $file
            );
            @unlink($thumb_file);
        }

        if (Tools::getValue('getTargetChild')) {
            $childs = NdkCf::getTargetsChilds(Tools::getValue('id_target'));

            if (sizeof($childs) > 0) {
                //array_push($childs, array('id'=>0, 'value' =>'all'));
                $return =
                    '<div id="target_zoning"><select id="target_child" class=" fixed-width-xl" name="target_child" onchange="getTargets();"><option value="">--</option>';
                foreach ($childs as $child) {
                    $return .=
                        '<option '.
                        (Tools::getValue('target_child') == $child['id']
                            ? 'selected="selected"'
                            : '').
                        ' value="'.
                        $child['id'].
                        '">'.
                        $child['value'].
                        '</option>';
                }
                $return .= '</select>';
                if (Tools::getValue('target_child')) {
                    if (
                        file_exists(
                            _PS_ROOT_DIR_.
                                '/img/scenes/ndkcf/'.
                                Tools::getValue('target_child').
                                '.svg'
                        )
                    ) {
                        $additionnals =
                            '<p><select data-value="'.
                            Tools::getValue('svg_path').
                            '" id="svg_path" class=" fixed-width-xl" name="svg_path"><option value="">SVG PATH</option></select></p>';
                        $additionnals .=
                            '<div id="zone_container" class="ndk-design"><div id="large_scene_image">'.
                            str_replace(
                                ']>',
                                '',
                                Tools::file_get_contents(
                                    _PS_ROOT_DIR_.
                                        '/img/scenes/ndkcf/'.
                                        Tools::getValue('target_child').
                                        '.svg'
                                )
                            ).
                            '</div></div>';
                    } else {
                        //$additionnals ='<img id="large_scene_image" src="'._PS_ROOT_DIR_.'/img/scenes/ndkcf/thumbs/'.Tools::getValue('target_child').'-'.Configuration::get('NDK_IMAGE_SIZE').'.jpg" />';
                        $additionnals =
                            '<div id="zone_container" class="ndk-design"><img id="large_scene_image" src="../img/scenes/ndkcf/'.
                            Tools::getValue('target_child').
                            '.jpg" /></div>';
                        $additionnals .=
                            '<input type="hidden" name="svg_path" value=""/>';
                    }
                    $additionnals .=
                        '<p><input name="x_axis"/><input name="y_axis"/><input name="zone_width"/><input name="zone_height"/><input id="zone_mode" class="serialize_input" name="false_options[zone_mode]" value="percent"/></p>';
                }
                $return .= $additionnals;
                $return .= '</div>';

                $this->ajaxRender($return);
            }
        }

        if ('ajaxGetProducts' == Tools::getValue('action')) {
            $query = Tools::getValue('q', false);
            if (!$query || '' == $query || Tools::strlen($query) < 1) {
                exit();
            }

            if ($pos = strpos($query, ' (ref:')) {
                $query = Tools::substr($query, 0, $pos);
            }

            $excludeIds = Tools::getValue('excludeIds', false);
            if ($excludeIds && 'NaN' != $excludeIds) {
                $excludeIds = implode(
                    ',',
                    array_map('intval', explode(',', $excludeIds))
                );
            } else {
                $excludeIds = '';
            }

            // Excluding downloadable products from packs because download from pack is not supported
            $forceJson = 'true' == Tools::getValue('forceJson');
            $disableCombination =
                'true' == Tools::getValue('disableCombination');
            $excludeVirtuals = 'true' == Tools::getValue('excludeVirtuals');
            $exclude_packs = 'true' == Tools::getValue('exclude_packs');

            $context = Context::getContext();

            $sql =
                'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`
                        FROM `'.
                _DB_PREFIX_.
                'product` p
                        '.
                Shop::addSqlAssociation('product', 'p').
                '
                        LEFT JOIN `'.
                _DB_PREFIX_.
                'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.
                (int) $context->language->id.
                Shop::addSqlRestrictionOnLang('pl').
                ')
                        LEFT JOIN `'.
                _DB_PREFIX_.
                'image_shop` image_shop
                            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.
                (int) $context->shop->id.
                ')
                        LEFT JOIN `'.
                _DB_PREFIX_.
                'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.
                (int) $context->language->id.
                ')
                        WHERE (pl.name LIKE \'%'.
                pSQL($query).
                '%\' OR p.reference LIKE \'%'.
                pSQL($query).
                '%\')'.
                (!empty($excludeIds)
                    ? ' AND p.id_product NOT IN ('.$excludeIds.') '
                    : ' ').
                ($excludeVirtuals
                    ? 'AND NOT EXISTS (SELECT 1 FROM `'.
                        _DB_PREFIX_.
                        'product_download` pd WHERE (pd.id_product = p.id_product))'
                    : '').
                ($exclude_packs
                    ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)'
                    : '').
                ' GROUP BY p.id_product';

            $items = Db::getInstance()->executeS($sql);

            if ($items && ($disableCombination || $excludeIds)) {
                $results = [];
                foreach ($items as $item) {
                    if (!$forceJson) {
                        $item['name'] = str_replace(
                            '|',
                            '&#124;',
                            $item['name']
                        );
                        $results[] =
                            trim($item['name']).
                            (!empty($item['reference'])
                                ? ' (ref: '.$item['reference'].')'
                                : '').
                            '|'.
                            (int) $item['id_product'];
                    } else {
                        $results[] = [
                            'id' => $item['id_product'],
                            'name' => $item['name'].
                                (!empty($item['reference'])
                                    ? ' (ref: '.$item['reference'].')'
                                    : ''),
                            'ref' => !empty($item['reference'])
                                ? $item['reference']
                                : '',
                            'image' => str_replace(
                                'http://',
                                Tools::getShopProtocol(),
                                $context->link->getImageLink(
                                    $item['link_rewrite'],
                                    $item['id_image'],
                                    ImageType::getFormattedName('home')
                                )
                            ),
                        ];
                    }
                }

                if (!$forceJson) {
                    $this->ajaxRender(implode("\n", $results));
                } else {
                    $this->ajaxRender(json_encode($results));
                }
            } elseif ($items) {
                // packs
                $results = [];
                foreach ($items as $item) {
                    // check if product have combination
                    if (
                        Combination::isFeatureActive() &&
                        $item['cache_default_attribute']
                    ) {
                        $sql =
                            'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name,
                                        a.`id_attribute`
                                    FROM `'.
                            _DB_PREFIX_.
                            'product_attribute` pa
                                    '.
                            Shop::addSqlAssociation('product_attribute', 'pa').
                            '
                                    LEFT JOIN `'.
                            _DB_PREFIX_.
                            'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
                                    LEFT JOIN `'.
                            _DB_PREFIX_.
                            'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                                    LEFT JOIN `'.
                            _DB_PREFIX_.
                            'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
                                    LEFT JOIN `'.
                            _DB_PREFIX_.
                            'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.
                            (int) $context->language->id.
                            ')
                                    LEFT JOIN `'.
                            _DB_PREFIX_.
                            'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.
                            (int) $context->language->id.
                            ')
                                    LEFT JOIN `'.
                            _DB_PREFIX_.
                            'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
                                    WHERE pa.`id_product` = '.
                            (int) $item['id_product'].
                            '
                                    GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                                    ORDER BY pa.`id_product_attribute`';

                        $combinations = Db::getInstance()->executeS($sql);
                        if (!empty($combinations)) {
                            foreach ($combinations as $k => $combination) {
                                $results[$combination['id_product_attribute']][
                                    'id'
                                ] = $item['id_product'];
                                $results[$combination['id_product_attribute']][
                                    'id_product_attribute'
                                ] = $combination['id_product_attribute'];
                                !empty(
                                    $results[
                                        $combination['id_product_attribute']
                                    ]['name']
                                )
                                    ? ($results[
                                        $combination['id_product_attribute']
                                    ]['name'] .=
                                        ' '.
                                        $combination['group_name'].
                                        '-'.
                                        $combination['attribute_name'])
                                    : ($results[
                                        $combination['id_product_attribute']
                                    ]['name'] =
                                        $item['name'].
                                        ' '.
                                        $combination['group_name'].
                                        '-'.
                                        $combination['attribute_name']);
                                if (!empty($combination['reference'])) {
                                    $results[
                                        $combination['id_product_attribute']
                                    ]['ref'] = $combination['reference'];
                                } else {
                                    $results[
                                        $combination['id_product_attribute']
                                    ]['ref'] = !empty($item['reference'])
                                        ? $item['reference']
                                        : '';
                                }
                                if (
                                    empty(
                                        $results[
                                            $combination['id_product_attribute']
                                        ]['image']
                                    )
                                ) {
                                    $results[
                                        $combination['id_product_attribute']
                                    ]['image'] = str_replace(
                                        'http://',
                                        Tools::getShopProtocol(),
                                        $context->link->getImageLink(
                                            $item['link_rewrite'],
                                            $combination['id_image'],
                                            ImageType::getFormattedName('home')
                                        )
                                    );
                                }
                            }
                        } else {
                            $results[] = [
                                'id' => $item['id_product'],
                                'name' => $item['name'],
                                'ref' => !empty($item['reference'])
                                    ? $item['reference']
                                    : '',
                                'image' => str_replace(
                                    'http://',
                                    Tools::getShopProtocol(),
                                    $context->link->getImageLink(
                                        $item['link_rewrite'],
                                        $item['id_image'],
                                        ImageType::getFormattedName('home')
                                    )
                                ),
                            ];
                        }
                    } else {
                        $results[] = [
                            'id' => $item['id_product'],
                            'name' => $item['name'],
                            'ref' => !empty($item['reference'])
                                ? $item['reference']
                                : '',
                            'image' => str_replace(
                                'http://',
                                Tools::getShopProtocol(),
                                $context->link->getImageLink(
                                    $item['link_rewrite'],
                                    $item['id_image'],
                                    ImageType::getFormattedName('home')
                                )
                            ),
                        ];
                    }
                }
                $this->ajaxRender(json_encode(array_values($results)));
            } else {
                $this->ajaxRender(json_encode([]));
            }
        }

        if (
            Tools::getValue('action') &&
            'modifyPost' == Tools::getValue('action')
        ) {
            $myvalues = [];
            $values = Tools::getValue('values');
            $values = str_replace(
                Tools::getValue('input_name'),
                '',
                Tools::getValue('values')
            );
            $values = Tools::jsonDecode($values, true);
            //var_dump($values);
            foreach ($values as $k => $v) {
                $new_k = str_replace(['[', ']'], '', $k);
                $myvalues[$new_k] = $v;
            }
            //$values = $myvalues;
            //var_dump($values);

            switch (Tools::getValue('function')) {
                case 'serialize':
                    $this->ajaxRender(
                        str_replace(['[', ']'], '', serialize($myvalues))
                    );
                    break;
                case 'implode':
                    $selected_values = [];
                    foreach ($values as $row) {
                        $selected_values[] = $row;
                    }
                    if (is_array($selected_values[0])) {
                        $this->ajaxRender(implode(',', $selected_values[0]));
                    } else {
                        $this->ajaxRender(implode(',', $selected_values));
                    }
                    break;
                default:
                    return true;
            }
        }
    }

    public function renderNdkImage($src, $class = '')
    {
        $this->context->smarty->assign([
            'src' => __PS_BASE_URI__.$src,
            'class' => $class,
        ]);

        return $this->context->smarty->fetch(
            _PS_ROOT_DIR_.
                '/modules/ndk_advanced_custom_fields/views/templates/admin/render-image.tpl'
        );
    }

    protected function ajaxRender(
        $value = null,
        $controller = null,
        $method = null
    ) {
        if (null === $controller) {
            $controller = get_class($this);
        }

        if (null === $method) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $method = $bt[1]['function'];
        }

        /* @deprecated deprecated since 1.6.1.1 */
        Hook::exec('actionAjaxDieBefore', [
            'controller' => $controller,
            'method' => $method,
            'value' => $value,
        ]);

        /*
         * @deprecated deprecated since 1.6.1.1
         * use 'actionAjaxDie'.$controller.$method.'Before' instead
         */
        Hook::exec('actionBeforeAjaxDie'.$controller.$method, [
            'value' => $value,
        ]);
        Hook::exec('actionAjaxDie'.$controller.$method.'Before', [
            'value' => $value,
        ]);
        //header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

        exit($value);
    }
}
