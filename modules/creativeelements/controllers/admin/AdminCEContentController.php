<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCEContentController extends ModuleAdminController
{
    public $bootstrap = true;

    public $table = 'ce_content';

    public $identifier = 'id_ce_content';

    public $className = 'CEContent';

    public $lang = true;

    protected $_defaultOrderBy = 'title';

    protected $hooks = [
        'displayHome',
        'displayTop',
        'displayBanner',
        'displayNav1',
        'displayNav2',
        'displayNavFullWidth',
        'displayTopColumn',
        'displayLeftColumn',
        'displayRightColumn',
        'displayFooterBefore',
        'displayFooter',
        'displayFooterAfter',
        'displayAfterBodyOpeningTag',
        'displayShoppingCart',
        'displayShoppingCartFooter',
        'displayFooterProduct',
        'displayNotFound',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->_select = 'sa.*';
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'ce_content_shop sa ON sa.`id_ce_content` = a.`id_ce_content` AND b.`id_shop` = sa.`id_shop`';
        $this->_where = 'AND sa.`id_shop` = ' . (int) $this->context->shop->id . ' AND a.`id_product` = 0';

        $this->fields_list = [
            'id_ce_content' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'class' => 'identifier-type fixed-width-xs',
                'align' => 'center',
            ],
            'title' => [
                'title' => $this->trans('Title', [], 'Admin.Global'),
            ],
            'hook' => [
                'title' => $this->trans('Position', [], 'Admin.Global'),
                'class' => 'fixed-width-xl',
            ],
            'date_add' => [
                'title' => $this->trans('Created on', [], 'Modules.Facetedsearch.Admin'),
                'filter_key' => 'sa!date_add',
                'class' => 'fixed-width-lg',
                'type' => 'datetime',
            ],
            'date_upd' => [
                'title' => $this->l('Modified on'),
                'filter_key' => 'sa!date_upd',
                'class' => 'fixed-width-lg',
                'type' => 'datetime',
            ],
            'active' => [
                'title' => $this->trans('Status', [], 'Admin.Global'),
                'filter_key' => 'sa!active',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
            ],
            'shortcode' => [
                'title' => $this->l('Shortcode'),
                'class' => 'ce-shortcode',
                'type' => 'editable',
                'orderby' => false,
                'search' => false,
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Notifications.Info'),
                'icon' => 'icon-trash text-danger',
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Info'),
            ],
        ];
    }

    public function ajaxProcessHideEditor()
    {
        $id = (int) Tools::getValue('id');
        $id_type = (int) Tools::getValue('idType');

        $uids = CE\UId::getBuiltList($id, $id_type, $this->context->shop->id);
        $res = empty($uids) ? $uids : array_keys($uids[$this->context->shop->id]);

        exit(json_encode($res));
    }

    public function ajaxProcessMigrate()
    {
        if ($ids = Tools::getValue('ids')) {
            require_once _CE_PATH_ . 'classes/CEMigrate.php';

            $done = [];

            foreach ($ids as $id) {
                CEMigrate::moveContent($id, $this->module) && $done[] = (int) $id;
            }
            $res = CEMigrate::removeIds('content', $done);

            exit(json_encode($res));
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        version_compare(_PS_VERSION_, '1.7.7', '<') && $this->addJquery();
        $this->js_files[] = _MODULE_DIR_ . 'creativeelements/views/lib/e-select2/js/e-select2.full.min.js?v=4.0.6-rc1';
        $this->css_files[_MODULE_DIR_ . 'creativeelements/views/lib/e-select2/css/e-select2.min.css?v=4.0.6-rc1'] = 'all';
    }

    public function initHeader()
    {
        if ((int) _PS_VERSION_ < 9) {
            parent::initHeader();
        } else {
            $getTabs = new ReflectionMethod($this, 'getTabs');
            $getTabs->setAccessible(true);
            $this->context->smarty->assign('tabs', $getTabs->invoke($this));
        }

        $display_suppliers = Configuration::get('PS_DISPLAY_SUPPLIERS');
        $display_manufacturers = version_compare(_PS_VERSION_, '1.7.7', '<') ? $display_suppliers : Configuration::get('PS_DISPLAY_MANUFACTURERS');
        $id_lang = $this->context->language->id;
        $link = $this->context->link;
        $tabs = &$this->context->smarty->tpl_vars['tabs']->value;

        foreach ($tabs as &$tab0) {
            foreach ($tab0['sub_tabs'] as &$tab1) {
                if ('AdminParentCEContent' === $tab1['class_name']) {
                    foreach ($tab1['sub_tabs'] as &$tab2) {
                        if ('AdminCEContent' === $tab2['class_name']) {
                            $sub_tabs = &$tab2['sub_tabs'];

                            $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminCEContent'));
                            $tab['name'] = $this->l('Contents');
                            $tab['current'] = true;
                            $tab['href'] = $link->getAdminLink('AdminCEContent');
                            $sub_tabs[] = $tab;

                            $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminCmsContent'));
                            $tab['current'] = '';
                            $tab['href'] = $link->getAdminLink('AdminCmsContent');
                            $sub_tabs[] = $tab;

                            $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminProducts'));
                            $tab['current'] = '';
                            $tab['href'] = $link->getAdminLink('AdminProducts');
                            $sub_tabs[] = $tab;

                            $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminCategories'));
                            $tab['current'] = '';
                            $tab['href'] = $link->getAdminLink('AdminCategories');
                            $sub_tabs[] = $tab;

                            if ($display_manufacturers) {
                                $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminManufacturers'));
                                $tab['current'] = '';
                                $tab['href'] = $link->getAdminLink('AdminManufacturers');
                                $sub_tabs[] = $tab;
                            }

                            if ($display_suppliers) {
                                $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminSuppliers'));
                                $tab['current'] = '';
                                $tab['href'] = $link->getAdminLink('AdminSuppliers');
                                $sub_tabs[] = $tab;
                            }

                            return;
                        }
                    }
                }
            }
        }
    }

    public function initModal()
    {
        (int) _PS_VERSION_ < 9 || self::$currentIndex = '?' . explode('?', self::$currentIndex, 2)[1];
        // Prevent modals
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['addce_content'] = [
                'href' => self::$currentIndex . '&addce_content&token=' . $this->token,
                'desc' => $this->trans('Add new', [], 'Admin.Actions'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();

        $this->show_page_header_toolbar = (int) _PS_VERSION_ < 9;
    }

    public function initToolBarTitle()
    {
        if ('add' === $this->display) {
            $this->page_header_toolbar_title = $this->trans('Add New', [], 'Admin.Actions');
        } elseif ('edit' === $this->display) {
            $this->page_header_toolbar_title = sprintf($this->l('Edit %s'), $this->l('Content'));
        } else {
            $this->page_header_toolbar_title = $this->l('Content Anywhere');
        }

        $this->toolbar_title[] = $this->l('Contents');
    }

    public function initContent()
    {
        $this->context->smarty->assign([
            'current_tab_level' => 3,
            'ce_hooks' => $this->hooks,
        ]);
        // Fix: Override directory missing on PS 9
        $this->context->smarty->getTemplateDir(1)
            || $this->context->smarty->addTemplateDir(_PS_OVERRIDE_DIR_ . 'controllers/admin/templates');

        parent::initContent();

        if (('edit' === $this->display || 'add' === $this->display) && Shop::getContextShopID() === null) {
            $this->displayWarning(
                $this->trans('You are in a multistore context: any modification will impact all your shops, or each shop of the active group.', [], 'Admin.Catalog.Notification')
            );
        }

        if ((int) _PS_VERSION_ > 8) {
            $this->initBreadcrumbs();
            $this->initHeader();

            if (_PS_MODE_DEV_) {
                // Fix: Ignore 'Undefined array key "route"' Warning comming from ps_mbo v5.2.0 displayDashboardTop hook
                $environment_class = '\PrestaShop\PrestaShop\Adapter\Environment';
                $GLOBALS['legacyContainer']->bind($environment_class, $environment_class, true);
                $environment = PrestaShop\PrestaShop\Adapter\ServiceLocator::get($environment_class);
                $debug = &Closure::bind(function &() {
                    return $this->isDebug;
                }, $environment, $environment)->__invoke();
                $debug = false;
            }
            $content = &$this->context->smarty->tpl_vars['content']->value;
            $content = $this->context->smarty->fetch('page_header_toolbar.tpl') . $content;

            isset($debug) && $debug = true;
        }
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        // Add Shortcodes
        if (!empty($this->_list)) {
            foreach ($this->_list as &$row) {
                $row['id'] = $row['id_ce_content'];
                $row['shortcode'] = "{hook h='$row[hook]'}";
            }
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $col = count(Language::getLanguages(false, false, true)) > 1 ? 9 : 7;

        version_compare(_PS_VERSION_, '1.7.8', '<') || --$col;

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Content'),
                'icon' => 'icon-edit',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Title', [], 'Admin.Global'),
                    'name' => 'title',
                    'lang' => true,
                    'col' => $col,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Position', [], 'Admin.Global'),
                    'name' => 'hook',
                    'required' => true,
                    'col' => 3,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content',
                    'lang' => true,
                    'col' => $col,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global'),
                        ],
                    ],
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                    'type' => 'submit',
                    'name' => "submitAdd{$this->table}",
                    'icon' => 'process-icon-save',
                    'class' => $btn_class = (int) _PS_VERSION_ < 8 ? 'pull-right' : 'btn-primary pull-right',
                ],
                [
                    'title' => $this->trans('Save and stay', [], 'Admin.Actions'),
                    'type' => 'submit',
                    'name' => "submitAdd{$this->table}AndStay",
                    'icon' => 'process-icon-save',
                    'class' => $btn_class,
                ],
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Shop association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        return parent::renderForm();
    }

    protected function l($string, $ctx = '', $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation($this->module, $string, $ctx, null, $addslashes, _CE_LOCALE_, false, $htmlentities);
    }
}
