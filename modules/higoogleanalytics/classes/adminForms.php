<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class HiGoogleAnalyticsAdminForms
{
    public function __construct($module)
    {
        $this->module = $module;
        $this->name = $module->name;
        $this->context = Context::getContext();
    }

    public function l($string)
    {
        return $this->module->l($string);
    }

    public function renderSettingsForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Debug Mode'),
                        'name' => 'debugMode',
                        'is_bool' => true,
                        'desc' => $this->l('When enabled, the module will display popups for each event'),
                        'doc' => 'debugMode',
                        'values' => [
                            [
                                'id' => 'debugMode_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'debugMode_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Clean Database when module uninstalled'),
                        'name' => 'cleanDb',
                        'is_bool' => true,
                        'desc' => $this->l('Not recommended, use this only when you’re not going to use the module'),
                        'doc' => 'cleanDb',
                        'values' => [
                            [
                                'id' => 'cleanDb_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'cleanDb_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitSettingsForm',
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $languages = Language::getLanguages(false);
        foreach ($languages as $key => $language) {
            $languages[$key]['is_default'] = (int) ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }
        $helper->languages = $languages;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitBlockSettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ) . '&configure=' . $this->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->name . '&' . $this->name . '=generelSettings';
        $helper->module = $this->module;
        $helper->tpl_vars = [
            'fields_value' => [
                'debugMode' => $this->module->debugMode,
                'cleanDb' => $this->module->cleanDb,
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function renderGa4Form()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Google Analytics 4 Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Google Analytics 4 tracking'),
                        'name' => 'enableGa4Tracking',
                        'is_bool' => true,
                        'doc' => 'enableGa4Tracking',
                        'values' => [
                            [
                                'id' => 'enableGa4Tracking_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'enableGa4Tracking_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google Analytics 4 measurement ID'),
                        'name' => 'ga4MeasurementId',
                        'placeholder' => 'G-XXXXX',
                        'class' => 'ga4-measurement-id',
                        'doc' => 'ga4MeasurementId',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitGa4Form',
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $languages = Language::getLanguages(false);
        foreach ($languages as $key => $language) {
            $languages[$key]['is_default'] = (int) ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }
        $helper->languages = $languages;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitBlockSettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ) . '&configure=' . $this->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->name . '&' . $this->name . '=googleAnalytics4';
        $helper->module = $this->module;
        $helper->tpl_vars = [
            'fields_value' => [
                'enableGa4Tracking' => $this->module->enableGa4Tracking,
                'ga4MeasurementId' => $this->module->ga4MeasurementId,
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function renderOrderSettings()
    {
        $orderStates = OrderState::getOrderStates($this->context->language->id);

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Order Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Include taxes in conversion value'),
                        'name' => 'includeTaxes',
                        'is_bool' => true,
                        'doc' => 'includeTaxes',
                        'values' => [
                            [
                                'id' => 'includeTaxes_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'includeTaxes_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Include shipping cost in conversion value'),
                        'name' => 'includeShipping',
                        'is_bool' => true,
                        'doc' => 'includeShipping',
                        'values' => [
                            [
                                'id' => 'includeShipping_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'includeShipping_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Include gift wrapping cost in conversion value'),
                        'name' => 'includeWrapping',
                        'is_bool' => true,
                        'doc' => 'includeWrapping',
                        'values' => [
                            [
                                'id' => 'includeWrapping_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'includeWrapping_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Include taxes for product prices'),
                        'name' => 'includeProductTaxes',
                        'is_bool' => true,
                        'doc' => 'includeProductTaxes',
                        'values' => [
                            [
                                'id' => 'includeProductTaxes_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'includeProductTaxes_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Deduct discount amount from conversation value'),
                        'name' => 'deductDiscount',
                        'is_bool' => true,
                        'doc' => 'deductDiscount',
                        'values' => [
                            [
                                'id' => 'deductDiscount_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'deductDiscount_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Deduct Cost price from conversation value'),
                        'name' => 'deductWholesalePrice',
                        'is_bool' => true,
                        'doc' => 'deductWholesalePrice',
                        'values' => [
                            [
                                'id' => 'deductWholesalePrice_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'deductWholesalePrice_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Refunded order status(es)'),
                        'multiple' => true,
                        'name' => 'refundedOrderStates',
                        'id' => 'refundedOrderStates',
                        'doc' => 'refundedOrderStates',
                        'options' => [
                            'query' => $orderStates,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Partially refunded order status(es)'),
                        'multiple' => true,
                        'name' => 'partialRefundedOrderStates',
                        'id' => 'partialRefundedOrderStates',
                        'doc' => 'partialRefundedOrderStates',
                        'options' => [
                            'query' => $orderStates,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitOrderSettingsForm',
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $languages = Language::getLanguages(false);
        foreach ($languages as $key => $language) {
            $languages[$key]['is_default'] = (int) ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }
        $helper->languages = $languages;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->submit_action = 'submitBlockSettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        ) . '&configure=' . $this->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->name . '&' . $this->name . '=orderSettings';
        $helper->module = $this->module;
        $helper->tpl_vars = [
            'fields_value' => [
                'refundedOrderStates[]' => $this->module->refundedOrderStates,
                'partialRefundedOrderStates[]' => $this->module->partialRefundedOrderStates,
                'includeTaxes' => $this->module->includeTaxes,
                'includeShipping' => $this->module->includeShipping,
                'includeWrapping' => $this->module->includeWrapping,
                'includeProductTaxes' => $this->module->includeProductTaxes,
                'deductDiscount' => $this->module->deductDiscount,
                'deductWholesalePrice' => $this->module->deductWholesalePrice,
            ],
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function renderEventsList($filters = [], $pageItems = 50, $pageNumber = 1)
    {
        if (!(int) $pageItems) {
            $pageItems = 50;
        }
        if (!(int) $pageNumber) {
            $pageNumber = 1;
        }

        $fields_list = [
            'id_event' => [
                'title' => $this->l('ID'),
                'width' => 60,
                'type' => 'text',
                'search' => false,
            ],
            'action' => [
                'title' => $this->l('Action Type'),
                'type' => 'select',
                'search' => true,
                'filter_key' => 'eventAction',
                'list' => [
                    'click' => $this->l('Click'),
                    'scroll' => $this->l('Scroll'),
                ],
            ],
            'selector' => [
                'title' => $this->l('Selector'),
                'type' => 'text',
                'search' => false,
            ],
            'event_category' => [
                'title' => $this->l('Event Category'),
                'type' => 'text',
                'search' => true,
            ],
            'event_action' => [
                'title' => $this->l('Event Action'),
                'type' => 'text',
                'search' => true,
            ],
            'event_label' => [
                'title' => $this->l('Event Label'),
                'type' => 'text',
                'search' => true,
            ],
            'event_value' => [
                'title' => $this->l('Event Value'),
                'type' => 'text',
                'search' => true,
            ],
            'status' => [
                'title' => $this->l('Status'),
                'width' => 140,
                'type' => 'select',
                'search' => true,
                'filter_key' => 'eventStatus',
                'list' => [
                    1 => $this->l('Active'),
                    0 => $this->l('Inactive'),
                ],
            ],
        ];
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->no_link = true;
        $helper->actions = ['edit', 'delete'];
        $helper->identifier = 'id_event';
        $helper->show_toolbar = false;
        $helper->title = $this->l('Custom Events');
        $helper->table = 'higacustomevent';
        $helper->module = $this->module;
        $helper->toolbar_btn['new'] = [
            'href' => '#',
            'desc' => $this->l('Add New Event'),
        ];
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&' . $this->name . '=events';
        $events = HiGoogleAnalyticsEvent::filter($filters, $pageItems, $pageNumber);
        $helper->listTotal = $events['total'];

        return $helper->generateList($events['result'], $fields_list);
    }

    public function renderEventForm($idEvent = 0)
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $idEvent ? $this->l('Update Event') : $this->l('Add Event'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_event',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                        'doc' => 'eventActive',
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Action Type'),
                        'name' => 'eventAction',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'click',
                                    'name' => $this->l('Click'),
                                ],
                                [
                                    'id' => 'scroll',
                                    'name' => $this->l('Scroll'),
                                ],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'doc' => 'eventActionType',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Selector'),
                        'name' => 'selector',
                        'required' => true,
                        'doc' => 'eventSelector',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Event Category'),
                        'name' => 'event_category',
                        'required' => true,
                        'doc' => 'eventCategory',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Event Action'),
                        'name' => 'event_action',
                        'required' => true,
                        'doc' => 'eventAction',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Event Label'),
                        'name' => 'event_label',
                        'placeholder' => $this->l('Optional'),
                        'doc' => 'eventLabel',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Event Value'),
                        'name' => 'event_value',
                        'placeholder' => $this->l('Optional'),
                        'doc' => 'eventValue',
                    ],
                    [
                        'type' => 'shop',
                        'label' => $this->l('Select Shops'),
                        'name' => 'checkBoxShopAsso',
                        'doc' => 'eventShop',
                    ],
                ],
                'submit' => [
                    'title' => $idEvent ? $this->l('Update') : $this->l('Add'),
                    'name' => 'submitSaveCustomEvent',
                    'class' => 'btn btn-default pull-right hipv-submit-video-save',
                ],
                'buttons' => [
                    [
                        'title' => $this->module->l('Cancel'),
                        'name' => 'closeCustomEventForm',
                        'type' => 'submit',
                        'icon' => 'process-icon-cancel',
                        'class' => 'btn btn-default pull-left',
                    ],
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this->module;
        $helper->id = $idEvent;
        $helper->table = 'higacustomevent';
        $helper->identifier = 'id_event';
        $languages = Language::getLanguages(false);
        foreach ($languages as $key => $language) {
            $languages[$key]['is_default'] = (int) ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }
        $helper->languages = $languages;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->show_toolbar = false;
        $helper->submit_action = 'submitCustomEvent';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->module->tab . '&module_name=' . $this->name . '&' . $this->name . '=customEvents';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->module = $this;
        $helper->tpl_vars = [
            'fields_value' => $this->getEventFieldsValues($idEvent),
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getEventFieldsValues($idEvent = 0)
    {
        $event = new HiGoogleAnalyticsEvent($idEvent);

        return [
            'id_event' => $idEvent,
            'active' => $event->active,
            'eventAction' => $event->action,
            'selector' => $event->selector,
            'event_category' => $event->event_category,
            'event_action' => $event->event_action,
            'event_label' => $event->event_label,
            'event_value' => $event->event_value,
        ];
    }
}
