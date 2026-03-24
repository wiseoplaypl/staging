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

class AdminCETemplatesController extends ModuleAdminController
{
    public $bootstrap = true;

    public $table = 'ce_template';

    public $identifier = 'id_ce_template';

    public $className = 'CETemplate';

    public $multishop_context = Shop::CONTEXT_ALL;

    protected $action_link;

    protected $_defaultOrderBy = 'title';

    protected $_where = 'AND a.type != "kit"';

    public function __construct()
    {
        parent::__construct();

        if ($type = Tools::getValue('type')) {
            if ('all' === $type) {
                unset($this->context->cookie->submitFilterce_template);
                unset($this->context->cookie->cetemplatesce_templateFilter_type);
            } else {
                $this->context->cookie->submitFilterce_template = 1;
                $this->context->cookie->cetemplatesce_templateFilter_type = $type;
            }
        }

        $this->fields_options['import-template'] = [
            'class' => 'ce-import-panel hide',
            'icon' => 'icon-upload',
            'title' => $this->l('Import Template'),
            'description' => $this->l('Choose a JSON template file or a .zip archive of templates, and add them to the list of templates available in your library.'),
            'fields' => [
                'action' => [
                    'type' => 'hidden',
                    'value' => 'import_template',
                    'no_multishop_checkbox' => true,
                ],
                'file' => [
                    'type' => 'file',
                    'title' => $this->l('Template file'),
                    'name' => 'file',
                    'no_multishop_checkbox' => true,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->l('Import Now'),
                    'type' => 'submit',
                    'icon' => 'process-icon-import',
                    'class' => (int) _PS_VERSION_ < 8 ? 'btn btn-default pull-right' : 'btn btn-primary pull-right',
                ],
            ],
        ];

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
                    'page' => $this->trans('Page', [], 'Admin.Global'),
                    'section' => $this->l('Section'),
                    'header' => $this->l('Header'),
                    'footer' => $this->l('Footer'),
                    'page-index' => $this->l('Home Page'),
                    'page-contact' => $this->l('Contact Page'),
                    'page-authentication' => $this->l('Login Page'),
                    'page-password' => $this->l('Password Page'),
                    'page-registration' => $this->l('Registration Page'),
                    'page-manufacturers' => $this->l('Brands Page'),
                    'page-suppliers' => $this->l('Suppliers Page'),
                    'page-not-found' => $this->l('404 Page'),
                    'product' => $this->l('Product Page'),
                    'product-quick-view' => $this->l('Quick View'),
                    'product-miniature' => $this->l('Product Miniature'),
                    'listing-category' => $this->l('Category Page'),
                    'listing-manufacturer' => $this->l('Brand Page'),
                    'listing-supplier' => $this->l('Supplier Page'),
                    'listing-page' => $this->l('Listing Page'),
                    'listing-no-results' => $this->l('No Results'),
                    'cms-category' => $this->l('CMS Category'),
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
            'shortcode' => [
                'title' => $this->l('Shortcode'),
                'class' => 'ce-shortcode',
                'type' => 'editable',
                'orderby' => false,
                'search' => false,
            ],
        ];

        $excludes = [];

        if (!$display_suppliers = Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            $excludes[] = 'page-suppliers';
            $excludes[] = 'listing-supplier';
        }
        if (version_compare(_PS_VERSION_, '1.7.7', '<') ? !$display_suppliers : !Configuration::get('PS_DISPLAY_MANUFACTURERS')) {
            $excludes[] = 'page-manufacturers';
            $excludes[] = 'listing-manufacturer';
        }
        if ($excludes) {
            $this->_where = 'AND a.type NOT IN ("kit", ' . implode(', ', array_map('json_encode', $excludes)) . ')';

            foreach ($excludes as $exclude) {
                unset($this->fields_list['type']['list'][$exclude]);
            }
        }

        $this->bulk_actions = [
            'export' => [
                'text' => $this->trans('Export', [], 'Admin.Actions'),
                'icon' => 'icon-mail-forward',
            ],
            'delete_divider' => [
                'text' => 'divider',
            ],
            'delete' => [
                'text' => $this->trans('Delete', [], 'Admin.Actions'),
                'icon' => 'icon-trash text-danger',
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Info'),
            ],
        ];

        $this->action_link = CESmarty::get(_CE_TEMPLATES_ . 'admin/admin.tpl', 'ce_action_link');
    }

    public function processBulkExport()
    {
        $uids = [];

        foreach ($this->boxes as $id) {
            $uids[] = new CE\UId($id, CE\UId::TEMPLATE);
        }

        CE\Plugin::instance()->templates_manager->getSource('local')->exportMultipleTemplates($uids);
    }

    protected function processUpdateOptions()
    {
        // Process import template
        CE\UId::$_ID = new CE\UId(0, CE\UId::TEMPLATE);

        $res = CE\Plugin::instance()->templates_manager->directImportTemplate();

        if ($res instanceof CE\WPError) {
            $this->errors[] = $res->getMessage();
        } elseif (isset($res[1]['template_id'])) {
            // More templates
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCETemplates', true, [], ['conf' => 18]));
        } elseif (isset($res[0]['template_id'])) {
            // Simple template
            $id = substr($res[0]['template_id'], 0, -6);

            Tools::redirectAdmin($this->context->link->getAdminLink('kit' === $res[0]['type'] ? 'AdminCEThemes' : 'AdminCETemplates', true, [], [
                'id_ce_template' => $id,
                'updatece_template' => 1,
                'conf' => 18,
            ]));
        } else {
            $this->errors[] = $this->l('Unknown error during import!');
        }
    }

    public function ajaxProcessMigrate()
    {
        if ($ids = Tools::getValue('ids')) {
            require_once _CE_PATH_ . 'classes/CEMigrate.php';

            $done = [];

            foreach ($ids as $id) {
                CEMigrate::moveTemplate($id) && $done[] = (int) $id;
            }
            $res = CEMigrate::removeIds('template', $done);

            exit(json_encode($res));
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
        $link = $this->context->link->getAdminLink('AdminCETemplates');
        $type = $this->context->cookie->cetemplatesce_templateFilter_type;
        $new = Tools::getIsset('addce_template');
        $tabs = &$this->context->smarty->tpl_vars['tabs']->value;
        $sections = [
            'section',
            'header',
            'footer',
            'product-quick-view',
            'product-miniature',
        ];
        foreach ($tabs as &$tab0) {
            foreach ($tab0['sub_tabs'] as &$tab1) {
                if ('AdminParentCEContent' !== $tab1['class_name']) {
                    continue;
                }
                foreach ($tab1['sub_tabs'] as &$tab2) {
                    if ('AdminCETemplates' !== $tab2['class_name']) {
                        continue;
                    }
                    $sub_tabs = &$tab2['sub_tabs'];
                    $tab = Tab::getTab($id_lang, Tab::getIdFromClassName('AdminCETemplates'));

                    $tab['name'] = $this->trans('Template', [], 'Admin.Global');
                    $tab['current'] = $new || (!in_array($type, ['section', 'page']) || !$type) && !$this->object;
                    $tab['href'] = "$link&type=all";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->trans('Page', [], 'Admin.Global');
                    $tab['current'] = !$new && ($this->object ? !in_array($this->object->type, $sections) : 'page' === $type);
                    $tab['href'] = "$link&type=page";
                    $sub_tabs[] = $tab;

                    $tab['name'] = $this->l('Section');
                    $tab['current'] = !$new && ($this->object ? in_array($this->object->type, $sections) : 'section' === $type);
                    $tab['href'] = "$link&type=section";
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
        if ('add' !== $this->display && 'edit' !== $this->display) {
            $this->page_header_toolbar_btn['addce_template'] = [
                'icon' => 'process-icon-new',
                'desc' => $this->trans('Add New', [], 'Admin.Actions'),
                'href' => self::$currentIndex . '&addce_template&token=' . $this->token,
            ];
            $this->page_header_toolbar_btn['importce_template'] = [
                'icon' => 'process-icon-import',
                'desc' => $this->trans('Import', [], 'Admin.Actions'),
                'href' => 'javascript:ceAdmin.onClickImport()',
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
            $this->page_header_toolbar_title = $this->l('Edit Template');
        } else {
            $this->page_header_toolbar_title = $this->l('My Templates');
        }

        $this->toolbar_title[] = $this->l('Templates', 'template_library');
    }

    public function initContent()
    {
        $this->context->smarty->assign('current_tab_level', 3);
        // Fix: Override directory missing on PS 9
        $this->context->smarty->getTemplateDir(1)
            || $this->context->smarty->addTemplateDir(_PS_OVERRIDE_DIR_ . 'controllers/admin/templates');

        parent::initContent();

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

        // Add Shortcodes & Translate template types
        if (!empty($this->_list)) {
            $type = &$this->fields_list['type']['list'];

            foreach ($this->_list as &$row) {
                $row['id'] = $row['id_ce_template'];
                $row['shortcode'] = "{hook h='CETemplate' id=$row[id]}";
                empty($type[$row['type']]) || $row['type'] = $type[$row['type']];
            }
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('preview');
        $this->addRowAction('export');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function displayPreviewLink($token, $id, $name = null)
    {
        $link = $this->context->link->getModuleLink('creativeelements', 'preview', [
            'id_employee' => $this->context->employee->id,
            'cetoken' => Tools::getAdminTokenLite('AdminCETemplates'),
            'preview_id' => $id . '010000',
        ], null, null, null, true);

        return sprintf($this->action_link, Tools::safeOutput($link), '_blank', 'eye', $this->trans('Preview', [], 'Admin.Actions'));
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
        $col = version_compare(_PS_VERSION_, '1.7.8', '<') ? 7 : 6;

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Template', [], 'Admin.Global'),
                'icon' => 'icon-edit',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Title', [], 'Admin.Global'),
                    'name' => 'title',
                    'col' => $col,
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Type', [], 'Admin.Catalog.Feature'),
                    'name' => 'type',
                    'disabled' => $disabled = !empty($this->object->type),
                    'options' => [
                        'default' => ['value' => '', 'label' => $this->l('Select...')],
                        'query' => $disabled ? [
                            ['value' => $this->object->type, 'label' => $this->fields_list['type']['list'][$this->object->type]],
                        ] : [
                            ['value' => 'page', 'label' => $this->trans('Page', [], 'Admin.Global')],
                            ['value' => 'section', 'label' => $this->l('Section')],
                        ],
                        'id' => 'value',
                        'name' => 'label',
                    ],
                    'col' => 3,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content',
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
                    'default_value' => 1,
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

        return parent::renderForm();
    }

    protected function l($string, $ctx = '', $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation($this->module, $string, $ctx, null, $addslashes, _CE_LOCALE_, false, $htmlentities);
    }
}
