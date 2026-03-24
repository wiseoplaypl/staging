<?php
/**
* Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Bongooglereviews extends Module
{
    public function __construct()
    {
        $this->name = 'bongooglereviews';
        $this->tab = 'front_office_features';
        $this->version = '6.0.1';
        $this->bootstrap = true;
        $this->author = 'Bonpresta';
        $this->module_key = '8c55b62c17b515befe2a74c00960cfa4';
        parent::__construct();
        $this->displayName = $this->l('Google Customer Reviews');
        $this->description = $this->l('Integration Google Customer Reviews.');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    protected function getModuleSettings()
    {
        $res = [
            'BONGOOGLEREVIEWS_DISPLAY' => true,
            'BONGOOGLEREVIEWS_DISPLAY_BADGE' => true,
            'BONGOOGLEREVIEWS_ID' => 119624581,
            'BONGOOGLEREVIEWS_POSITION' => 'BOTTOM_LEFT',
        ];

        return $res;
    }

    public function install()
    {
        $settings = $this->getModuleSettings();

        foreach ($settings as $name => $value) {
            Configuration::updateValue($name, $value);
        }

        return parent::install() &&
        $this->registerHook('displayFooter') &&
        $this->registerHook('displayOrderConfirmation') &&
        $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        $settings = $this->getModuleSettings();

        foreach (array_keys($settings) as $name) {
            Configuration::deleteByName($name);
        }

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if ((bool) Tools::isSubmit('submitSettingsReviews')) {
            if (!$errors = $this->checkItemFields()) {
                $this->postProcess();
                $output .= $this->displayConfirmation($this->l('Save all settings.'));
            } else {
                $output .= $errors;
            }
        }

        return $output . $this->renderForm();
    }

    protected function checkItemFields()
    {
        $errors = [];

        if (Tools::isEmpty(Tools::getValue('BONGOOGLEREVIEWS_ID'))) {
            $errors[] = $this->l('Id is required.');
        }

        if ($errors) {
            return $this->displayError(implode('<br />', $errors));
        } else {
            return false;
        }
    }

    protected function getConfigGoogleReviews()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable google reviews:'),
                        'name' => 'BONGOOGLEREVIEWS_DISPLAY',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'enable',
                                'value' => 1,
                                'label' => $this->l('Yes')],
                            [
                                'id' => 'disable',
                                'value' => 0,
                                'label' => $this->l('No')],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Google Merchant ID:'),
                        'name' => 'BONGOOGLEREVIEWS_ID',
                        'col' => 2,
                        'required' => true,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Position:'),
                        'name' => 'BONGOOGLEREVIEWS_POSITION',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'BOTTOM_LEFT',
                                    'name' => $this->l('Bottom Left')],
                                [
                                    'id' => 'BOTTOM_RIGHT',
                                    'name' => $this->l('Bottom Right')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Badge:'),
                        'name' => 'BONGOOGLEREVIEWS_DISPLAY_BADGE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'enable',
                                'value' => 1,
                                'label' => $this->l('Yes')],
                            [
                                'id' => 'disable',
                                'value' => 0,
                                'label' => $this->l('No')],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSettingsReviews';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigGoogleReviews()]);
    }

    protected function getConfigFieldsValues()
    {
        $filled_settings = [];
        $settings = $this->getModuleSettings();

        foreach (array_keys($settings) as $name) {
            $filled_settings[$name] = Configuration::get($name);
        }

        return $filled_settings;
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFieldsValues();
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    protected function langCode($code)
    {
        $expcode = explode('-', $code);
        if (count($expcode) > 1) {
            $code_upper = Tools::strtoupper($expcode[1]);
            $code = $expcode[0] . '_' . $code_upper;
        } else {
            $code = Tools::strtoupper($code);
        }

        return $code;
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get('BONGOOGLEREVIEWS_DISPLAY')) {
            Media::addJsDefL('l_code', $this->langCode($this->context->language->language_code));
        }
    }

    public function hookOrderConfirmation()
    {
        if (Configuration::get('BONGOOGLEREVIEWS_DISPLAY')) {
            $order = new Order((int) Tools::getValue('id_order'));
            $address_delivery = new Address((int) $order->id_address_delivery);
            $delivery_country = new Country($address_delivery->id_country);

            $this->context->smarty->assign([
                'google_reviews_id' => Configuration::get('BONGOOGLEREVIEWS_ID'),
                'google_reviews_id_order' => Tools::getValue('id_order'),
                'google_reviews_date' => date('Y-m-d'),
                'google_reviews_email' => $this->context->customer->email,
                'google_reviews_delivery_country' => $delivery_country->iso_code,
            ]);

            return $this->fetch('module:bongooglereviews/views/templates/hook/google-reviews.tpl');
        }
    }

    public function hookDisplayFooter()
    {
        if (Configuration::get('BONGOOGLEREVIEWS_DISPLAY')) {
            $this->context->smarty->assign([
                'google_reviews_id' => Configuration::get('BONGOOGLEREVIEWS_ID'),
                'google_reviews_badge' => Configuration::get('BONGOOGLEREVIEWS_DISPLAY_BADGE'),
                'google_reviews_position' => Configuration::get('BONGOOGLEREVIEWS_POSITION'),
                'module_page_name' => $this->context->controller->php_self,
            ]);

            return $this->fetch('module:bongooglereviews/views/templates/hook/footer-google-reviews.tpl');
        }
    }
}
