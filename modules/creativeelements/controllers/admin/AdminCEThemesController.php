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

class AdminCEThemesController extends ModuleAdminController
{
    public $bootstrap = true;

    protected $type;

    protected $action_link;

    protected $hasManufacturers;

    protected $hasSuppliers;

    protected $_defaultOrderBy = 'title';

    public function __construct()
    {
        parent::__construct();

        if ($type = Tools::getValue('type')) {
            if ('template' === $type) {
                unset($this->context->cookie->submitFilterce_theme);
                unset($this->context->cookie->cethemesce_themeFilter_type);
            } else {
                $this->context->cookie->submitFilterce_theme = 1;
                // Fix: Remove Listing Page from Page tab
                $this->context->cookie->cethemesce_themeFilter_type = 'page' === $type ? 'page-' : $type;
            }
        }

        $this->type = $type ?: $this->context->cookie->cethemesce_themeFilter_type;
        $this->hasSuppliers = Configuration::get('PS_DISPLAY_SUPPLIERS');
        $this->hasManufacturers = version_compare(_PS_VERSION_, '1.7.7', '<') ? $this->hasSuppliers : Configuration::get('PS_DISPLAY_MANUFACTURERS');

        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        if ('kit' === $this->type) {
            $this->table = 'ce_template';
            $this->identifier = 'id_ce_template';
            $this->className = 'CETemplate';
            $this->action_link = CESmarty::get(_CE_TEMPLATES_ . 'admin/admin.tpl', 'ce_action_link');
            $this->_where = "AND a.`type` = 'kit'";

            $this->fields_list = [
                'id_ce_template' => [
                    'title' => $this->trans('ID', [], 'Admin.Global'),
                    'class' => 'identifier-type fixed-width-xs',
                    'align' => 'center',
                ],
                'title' => [
                    'title' => $this->trans('Title', [], 'Admin.Global'),
                ],
                'type' => [
                    'title' => $this->trans('Type', [], 'Admin.Catalog.Feature'),
                    'class' => 'fixed-width-lg',
                    'type' => 'select',
                    'list' => [
                        'kit' => $this->l('Theme Style'),
                    ],
                    'filter_key' => 'type',
                ],
                'date_add' => [
                    'title' => $this->trans('Created on', [], 'Modules.Facetedsearch.Admin'),
                    'class' => 'fixed-width-lg',
                    'type' => 'datetime',
                ],
                'date_upd' => [
                    'title' => $this->l('Modified on'),
                    'class' => 'fixed-width-lg',
                    'type' => 'datetime',
                ],
                'active' => [
                    'title' => $this->trans('Status', [], 'Admin.Global'),
                    'class' => 'fixed-width-xs',
                    'align' => 'center',
                    'active' => 'status',
                    'type' => 'bool',
                ],
            ];
        } else {
            $this->table = 'ce_theme';
            $this->identifier = 'id_ce_theme';
            $this->className = 'CETheme';
            $this->lang = true;

            $this->_select = 'sa.*';
            $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'ce_theme_shop sa ON sa.`id_ce_theme` = a.`id_ce_theme` AND b.`id_shop` = sa.`id_shop`';
            $this->_where = 'AND sa.id_shop = ' . (int) $id_shop;
            $this->hasManufacturers || $this->_where .= ' AND a.`type` != "page-manufacturers" AND a.`type` != "listing-manufacturer"';
            $this->hasSuppliers || $this->_where .= ' AND a.`type` != "page-suppliers" AND a.`type` != "listing-supplier"';

            $tab = '&emsp;';
            $this->fields_list = [
                'id_ce_theme' => [
                    'title' => $this->trans('ID', [], 'Admin.Global'),
                    'class' => 'identifier-type fixed-width-xs',
                    'align' => 'center',
                ],
                'title' => [
                    'title' => $this->trans('Title', [], 'Admin.Global'),
                ],
                'type' => [
                    'title' => $this->trans('Type', [], 'Admin.Catalog.Feature'),
                    'class' => 'fixed-width-lg',
                    'type' => 'select',
                    'list' => [
                        'header' => $this->l('Header'),
                        'footer' => $this->l('Footer'),
                        'page-' => $this->trans('Page', [], 'Admin.Global'),
                        'page-index' => $tab . $this->l('Home Page'),
                        'page-contact' => $tab . $this->l('Contact Page'),
                        'page-authentication' => $tab . $this->l('Login Page'),
                        'page-password' => $tab . $this->l('Password Page'),
                        'page-registration' => $tab . $this->l('Registration Page'),
                        'page-manufacturers' => $tab . $this->l('Brands Page'),
                        'page-suppliers' => $tab . $this->l('Suppliers Page'),
                        'page-not-found' => $tab . $this->l('404 Page'),
                        'prod' => $this->trans('Product', [], 'Admin.Global'),
                        'product' => $tab . $this->l('Product Page'),
                        'product-quick-view' => $tab . $this->l('Quick View'),
                        'product-miniature' => $tab . $this->l('Product Miniature'),
                        'list' => $this->l('Listing'),
                        'listing-category' => $tab . $this->l('Category Page'),
                        'listing-manufacturer' => $tab . $this->l('Brand Page'),
                        'listing-supplier' => $tab . $this->l('Supplier Page'),
                        'listing-page' => $tab . $this->l('Listing Page'),
                        'listing-no-results' => $tab . $this->l('No Results'),
                        'cms' => $this->l('CMS'),
                        'cms-category' => $tab . $this->l('CMS Category'),
                    ],
                    'filter_key' => 'type',
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
            ];
        }

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Notifications.Info'),
                'icon' => 'icon-trash text-danger',
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Info'),
            ],
        ];

        $default_option = [
            '' => [
                'value' => '',
                'name' => $this->trans('Default', [], 'Admin.Global'),
            ],
        ];
        $listing_pages = CETheme::getOptions('listing-page', $id_lang, $id_shop);

        $this->fields_options['theme_settings'] = [
            'class' => version_compare(_PS_VERSION_, '1.7.8', '<') ? 'ce-theme-panel' : 'ce-theme-panel panel--new',
            'icon' => 'icon-cog',
            'title' => $this->l('Theme Settings'),
            'tabs' => [
                'site' => $this->l('Site'),
                'page' => $this->trans('Page', [], 'Admin.Global'),
                'prod' => $this->trans('Product', [], 'Admin.Global'),
                'list' => $this->l('Listing'),
                'cms' => $this->l('CMS'),
            ],
            'fields' => [
                'CE_HEADER' => [
                    'tab' => 'site',
                    'title' => $this->l('Header'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('header', $id_lang, $id_shop),
                ],
                'CE_FOOTER' => [
                    'tab' => 'site',
                    'title' => $this->l('Footer'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('footer', $id_lang, $id_shop),
                ],
                'elementor_active_kit' => [
                    'tab' => 'site',
                    'title' => $this->l('Theme Style'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => array_merge([
                        ['value' => '', 'name' => $this->trans('Add New', [], 'Admin.Actions')],
                    ], CETemplate::getKitOptions()),
                ],
                'CE_PAGE_INDEX' => [
                    'tab' => 'page',
                    'title' => $this->trans('Home', [], 'Admin.Navigation.Header'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-index', $id_lang, $id_shop),
                ],
                'CE_PAGE_CONTACT' => [
                    'tab' => 'page',
                    'title' => $this->trans('Contact', [], 'Admin.Navigation.Menu'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-contact', $id_lang, $id_shop),
                ],
                'CE_PAGE_NOT_FOUND' => [
                    'tab' => 'page',
                    'title' => $this->trans('404 error', [], 'Shop.Navigation'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-not-found', $id_lang, $id_shop),
                ],
                'CE_PAGE_AUTHENTICATION' => [
                    'tab' => 'page',
                    'title' => $this->trans('Login', [], 'Admin.Navigation.Menu'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-authentication', $id_lang, $id_shop),
                ],
                'CE_PAGE_PASSWORD' => [
                    'tab' => 'page',
                    'title' => $this->trans('Password', [], 'Admin.Global'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-password', $id_lang, $id_shop),
                ],
                'CE_PAGE_REGISTRATION' => [
                    'tab' => 'page',
                    'title' => $this->trans('Registration', [], 'Admin.Orderscustomers.Feature'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-registration', $id_lang, $id_shop),
                ],
                'CE_PAGE_MANUFACTURERS' => [
                    'tab' => 'page',
                    'title' => $this->trans('Brands', [], 'Admin.Global'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-manufacturers', $id_lang, $id_shop),
                ],
                'CE_PAGE_SUPPLIERS' => [
                    'tab' => 'page',
                    'title' => $this->trans('Suppliers', [], 'Admin.Global'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('page-suppliers', $id_lang, $id_shop),
                ],
                'CE_PRODUCT' => [
                    'tab' => 'prod',
                    'title' => $this->l('Product Page'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('product', $id_lang, $id_shop),
                ],
                'CE_PRODUCT_QUICK_VIEW' => [
                    'tab' => 'prod',
                    'title' => $this->l('Quick View'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('product-quick-view', $id_lang, $id_shop),
                ],
                'CE_PRODUCT_MINIATURE' => [
                    'tab' => 'prod',
                    'title' => $this->l('Miniature'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('product-miniature', $id_lang, $id_shop),
                ],
                'CE_LISTING_CATEGORY' => [
                    'tab' => 'list',
                    'title' => $this->l('Category Page'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + array_merge(CETheme::getOptions('listing-category', $id_lang, $id_shop), $listing_pages),
                ],
                'CE_LISTING_SEARCH' => [
                    'tab' => 'list',
                    'title' => $this->l('Search Page'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + $listing_pages,
                ],
                'CE_LISTING_NO_RESULTS' => [
                    'tab' => 'list',
                    'title' => $this->l('No Results'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('listing-no-results', $id_lang, $id_shop),
                ],
                'CE_LISTING_PRICES_DROP' => [
                    'tab' => 'list',
                    'title' => $this->trans('Prices drop', [], 'Shop.Navigation'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + $listing_pages,
                ],
                'CE_LISTING_NEW_PRODUCTS' => [
                    'tab' => 'list',
                    'title' => $this->trans('New products', [], 'Shop.Navigation'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + $listing_pages,
                ],
                'CE_LISTING_BEST_SALES' => [
                    'tab' => 'list',
                    'title' => $this->trans('Best sellers', [], 'Shop.Navigation'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + $listing_pages,
                ],
                'CE_LISTING_MANUFACTURER' => [
                    'tab' => 'list',
                    'title' => $this->l('Brand Page'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + array_merge(CETheme::getOptions('listing-manufacturer', $id_lang, $id_shop), $listing_pages),
                ],
                'CE_LISTING_SUPPLIER' => [
                    'tab' => 'list',
                    'title' => $this->l('Supplier Page'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + array_merge(CETheme::getOptions('listing-supplier', $id_lang, $id_shop), $listing_pages),
                ],
                'CE_CMS_CATEGORY' => [
                    'tab' => 'cms',
                    'title' => $this->l('CMS Category'),
                    'cast' => 'strval',
                    'type' => 'select',
                    'identifier' => 'value',
                    'list' => $default_option + CETheme::getOptions('cms-category', $id_lang, $id_shop),
                ],
                'CE_CMS_PAGE' => [
                    'tab' => 'cms',
                    'title' => $this->l('CMS Page'),
                    'type' => 'button',
                    'href' => $this->context->link->getAdminLink('AdminCmsContent', true, ['addcms' => 1]),
                    'icon' => 'plus-circle',
                    'label' => $this->trans('Add new page', [], 'Admin.Design.Help'),
                ],
                'CE_CMS_PAGES' => [
                    'tab' => 'cms',
                    'title' => '',
                    'type' => 'button',
                    'href' => $this->context->link->getAdminLink('AdminCmsContent'),
                    'icon' => 'list',
                    'label' => $this->l('Manage pages'),
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->trans('Save', [], 'Admin.Actions'),
                    'type' => 'submit',
                    'icon' => 'process-icon-save',
                    'class' => (int) _PS_VERSION_ < 8 ? 'btn btn-default pull-right' : 'btn btn-primary pull-right',
                ],
            ],
        ];
        $disabled_fields = [];

        if (!$this->hasManufacturers) {
            unset($this->fields_list['type']['list']['page-manufacturers'], $this->fields_list['type']['list']['listing-manufacturer']);
            $disabled_fields[] = 'CE_PAGE_MANUFACTURERS';
            $disabled_fields[] = 'CE_LISTING_MANUFACTURER';
        }
        if (!$this->hasSuppliers) {
            unset($this->fields_list['type']['list']['page-suppliers'], $this->fields_list['type']['list']['listing-supplier']);
            $disabled_fields[] = 'CE_PAGE_SUPPLIERS';
            $disabled_fields[] = 'CE_LISTING_SUPPLIER';
        }
        if (!Configuration::get('PS_DISPLAY_BEST_SELLERS') || Configuration::get('PS_CATALOG_MODE')) {
            $disabled_fields[] = 'CE_LISTING_BEST_SALES';
        }
        if ($disabled_fields) {
            foreach ($disabled_fields as $field_key) {
                $field = &$this->fields_options['theme_settings']['fields'][$field_key];
                $field['form_group_class'] = 'ce-disabled';
                $field['list'] = [];
                unset($field);
            }
            $this->tpl_option_vars['input'] = [
                'empty_message' => CESmarty::get(_CE_TEMPLATES_ . 'admin/admin.tpl', 'ce_disabled'),
            ];
        }
    }

    protected function processUpdateOptions()
    {
        parent::processUpdateOptions();

        if (!Configuration::get('elementor_active_kit')) {
            $id_kit = substr(CE\Plugin::instance()->kits_manager->getActiveId(), 0, -6);
            ${'_POST'}['elementor_active_kit'] = $id_kit;
            $this->fields_options['theme_settings']['fields']['elementor_active_kit']['list'][] = [
                'value' => $id_kit,
                'name' => "#$id_kit {$this->trans('Default', [], 'Admin.Global')}",
            ];
        }
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

        $id_lang = $this->context->language->id;
        $link = $this->context->link->getAdminLink('AdminCEThemes');
        $new = Tools::getIsset('addce_theme');
        $tabs = &$this->context->smarty->tpl_vars['tabs']->value;

        foreach ($tabs as &$tab0) {
            foreach ($tab0['sub_tabs'] as &$tab1) {
                if ('AdminParentCEContent' !== $tab1['class_name']) {
                    continue;
                }
                foreach ($tab1['sub_tabs'] as &$tab2) {
                    if ('AdminCEThemes' !== $tab2['class_name']) {
                        continue;
                    }
                    $sub_tabs = &$tab2['sub_tabs'];
                    $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminCEThemes'));

                    $tab['name'] = $this->trans('Template', [], 'Admin.Global');
                    $tab['current'] = $new || (!$this->type || 'template' === $this->type) && !$this->object;
                    $tab['href'] = "$link&type=template";
                    $sub_tabs[] = $tab;

                    $type = $this->object ? $this->object->type : $this->type;

                    $tab['name'] = $this->l('Header');
                    $tab['current'] = !$new && 'header' === $type;
                    $tab['href'] = "$link&type=header";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->l('Footer');
                    $tab['current'] = !$new && 'footer' === $type;
                    $tab['href'] = "$link&type=footer";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->trans('Page', [], 'Admin.Global');
                    $tab['current'] = !$new && strpos($type, 'page') === 0;
                    $tab['href'] = "$link&type=page";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->trans('Product', [], 'Admin.Global');
                    $tab['current'] = !$new && strpos($type, 'prod') === 0;
                    $tab['href'] = "$link&type=prod";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->l('Listing');
                    $tab['current'] = !$new && strpos($type, 'list') === 0;
                    $tab['href'] = "$link&type=list";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->l('CMS');
                    $tab['current'] = !$new && strpos($type, 'cms') === 0;
                    $tab['href'] = "$link&type=cms";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->l('Theme Style');
                    $tab['current'] = !$new && 'kit' === $type;
                    $tab['href'] = "$link&type=kit";
                    $sub_tabs[] = $tab;

                    return;
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
        if (empty($this->display) || 'options' === $this->display) {
            $this->page_header_toolbar_btn["add{$this->table}"] = [
                'href' => self::$currentIndex . "&add{$this->table}&token={$this->token}",
                'desc' => $this->trans('Add New', [], 'Admin.Actions'),
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
            $this->page_header_toolbar_title = 'kit' === $this->type
                ? sprintf($this->l('Edit %s'), $this->l('Theme Style'))
                : $this->l('Edit Template');
        } else {
            $this->page_header_toolbar_title = $this->l('Theme Builder');
        }

        $this->toolbar_title[] = 'kit' === $this->type
            ? $this->l('Styles')
            : $this->l('Templates', 'template_library');
    }

    public function initContent()
    {
        $this->context->smarty->assign('current_tab_level', 3);
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

    public function processFilter()
    {
        if (in_array($this->context->cookie->cethemesce_themeFilter_type, ['page-', 'prod', 'list', 'cms'])) {
            // Trick for type filtering, use LIKE instead of =
            $this->fields_list['type']['type'] = 'text';
        }
        parent::processFilter();

        $this->fields_list['type']['type'] = 'select';
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        // Translate template types
        if (!empty($this->_list)) {
            $type = &$this->fields_list['type']['list'];

            foreach ($this->_list as &$row) {
                empty($type[$row['type']]) || $row['type'] = str_replace('&emsp;', '', $type[$row['type']]);
            }
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->action_link && $this->addRowAction('export');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function displayExportLink($token, $id, $name = null)
    {
        $link = $this->context->link->getAdminLink('AdminCEEditor', true, [], [
            'ajax' => 1,
            'action' => 'elementor_library_direct_actions',
            'library_action' => 'export_template',
            'source' => 'local',
            'template_id' => $id . '010000',
        ]);

        return sprintf($this->action_link, Tools::safeOutput($link), '_self', 'mail-forward', $this->trans('Export', [], 'Admin.Actions'));
    }

    public function renderForm()
    {
        $kit = 'kit' === $this->type;
        $col = !$kit && count(Language::getLanguages(false, false, true)) > 1 ? 9 : 7;

        version_compare(_PS_VERSION_, '1.7.8', '<') || --$col;

        $this->fields_form = [
            'legend' => [
                'title' => $kit ? $this->l('Theme Style') : $this->trans('Template', [], 'Admin.Global'),
                'icon' => 'icon-edit',
            ],
            'input' => [
                'title' => [
                    'type' => 'text',
                    'label' => $this->trans('Title', [], 'Admin.Global'),
                    'name' => 'title',
                    'lang' => !$kit,
                    'col' => $col,
                ],
                'type' => [
                    'type' => 'select',
                    'label' => $this->trans('Type', [], 'Admin.Catalog.Feature'),
                    'name' => 'type',
                    'required' => true,
                    'disabled' => !empty($this->object->type),
                    'options' => $kit ? [
                        'default' => ['value' => 'kit', 'label' => $this->l('Theme Style')],
                        'query' => [],
                    ] : [
                        'default' => ['value' => '', 'label' => $this->l('Select...')],
                        'optiongroup' => [
                            'query' => [
                                'site' => [
                                    'label' => $this->l('Site'),
                                    'query' => [
                                        ['value' => 'header', 'label' => $this->l('Header')],
                                        ['value' => 'footer', 'label' => $this->l('Footer')],
                                    ],
                                ],
                                'page' => [
                                    'label' => $this->trans('Page', [], 'Admin.Global'),
                                    'query' => [
                                        'index' => [
                                            'value' => 'page-index',
                                            'label' => $this->l('Home Page'),
                                        ],
                                        'contact' => [
                                            'value' => 'page-contact',
                                            'label' => $this->l('Contact Page'),
                                        ],
                                        'authentication' => [
                                            'value' => 'page-authentication',
                                            'label' => $this->l('Login Page'),
                                        ],
                                        'password' => [
                                            'value' => 'page-password',
                                            'label' => $this->l('Password Page'),
                                        ],
                                        'registration' => [
                                            'value' => 'page-registration',
                                            'label' => $this->l('Registration Page'),
                                        ],
                                        'manufacturers' => [
                                            'value' => 'page-manufacturers',
                                            'label' => $this->l('Brands Page'),
                                        ],
                                        'suppliers' => [
                                            'value' => 'page-suppliers',
                                            'label' => $this->l('Suppliers Page'),
                                        ],
                                        'not-found' => [
                                            'value' => 'page-not-found',
                                            'label' => $this->l('404 Page'),
                                        ],
                                    ],
                                ],
                                'product' => [
                                    'label' => $this->trans('Product', [], 'Admin.Global'),
                                    'query' => [
                                        ['value' => 'product', 'label' => $this->l('Product Page')],
                                        ['value' => 'product-quick-view', 'label' => $this->l('Quick View')],
                                        ['value' => 'product-miniature', 'label' => $this->l('Miniature')],
                                    ],
                                ],
                                'list' => [
                                    'label' => $this->l('Listing'),
                                    'query' => [
                                        'category' => [
                                            'value' => 'listing-category',
                                            'label' => $this->l('Category Page'),
                                        ],
                                        'manufacturer' => [
                                            'value' => 'listing-manufacturer',
                                            'label' => $this->l('Brand Page'),
                                        ],
                                        'supplier' => [
                                            'value' => 'listing-supplier',
                                            'label' => $this->l('Supplier Page'),
                                        ],
                                        'page' => [
                                            'value' => 'listing-page',
                                            'label' => $this->l('Listing Page'),
                                        ],
                                        'no-results' => [
                                            'value' => 'listing-no-results',
                                            'label' => $this->l('No Results'),
                                        ],
                                    ],
                                ],
                                'cms' => [
                                    'label' => $this->l('CMS'),
                                    'query' => [
                                        'category' => [
                                            'value' => 'cms-category',
                                            'label' => $this->l('CMS Category'),
                                        ],
                                    ],
                                ],
                            ],
                            'label' => 'label',
                        ],
                        'options' => [
                            'query' => 'query',
                            'name' => 'label',
                            'id' => 'value',
                        ],
                    ],
                    'col' => 3,
                ],
                'content' => [
                    'type' => 'textarea',
                    'label' => $this->l($kit ? 'Style' : 'Content'),
                    'name' => 'content',
                    'lang' => !$kit,
                    'col' => $col,
                    'class' => 'hidden',
                ],
                'active' => [
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
        if (!$kit && !$this->hasManufacturers) {
            unset(
                $this->fields_form['input']['type']['options']['optiongroup']['query']['page']['query']['manufacturers'],
                $this->fields_form['input']['type']['options']['optiongroup']['query']['list']['query']['manufacturer']
            );
        }
        if (!$kit && !$this->hasSuppliers) {
            unset(
                $this->fields_form['input']['type']['options']['optiongroup']['query']['page']['query']['suppliers'],
                $this->fields_form['input']['type']['options']['optiongroup']['query']['list']['query']['supplier']
            );
        }
        if (!$kit && Shop::isFeatureActive()) {
            $this->fields_form['input']['shop'] = [
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
