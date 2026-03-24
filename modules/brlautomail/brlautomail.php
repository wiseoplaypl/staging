<?php
/**
 * Prestashop brl_mail_new_client
 *
 * @author    BRL technologies <contact@brl_technologies.com>
 * @copyright 2007-2023 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Brlautomail extends Module
{
    protected $modeDebug = false;
    protected $errorMessages = '';
    protected $confirmMessages = '';
    protected $nomLogFile = '';

    public function __construct()
    {
        $this->name = 'brlautomail';
        $this->tab = 'emailing';
        $this->nomLogFile = 'BRL_LOG_' . $this->name . '.log';
        $this->version = '1.0.11';
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->author = 'Autour Du Digital';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Automatic Mail');
        $this->description = $this->l('Example: Send an email 4 days after the shipment to ask the customer to put a product review.');
        $this->module_key = '31ccbd9ad837ba853a30138e84889e3c';
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        Configuration::updateValue('BRL_MO_MAILNC_OPTION_DIFF', 0);
        Configuration::updateValue('BRL_MO_MAILNC_OPTION_SEND', 'new');
        Configuration::updateValue('BRL_MO_MAILNC_OPTION_UNIQUE', 0);
        Configuration::updateValue('BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER', 1);
        Configuration::updateValue($this->name . '_id_product', '');
        Configuration::updateValue($this->name . '_mode_debug', 0);
        Configuration::updateValue($this->name . '_delete_logs', 0);
        Configuration::updateValue('BRL_MO_MAILNC_EXPEDITEUR', '');
        $languages = Language::getLanguages(false);
        $text_vide = [];
        foreach ($languages as $lang) {
            $text_vide[$lang['id_lang']] = '';
        }
        Configuration::updateValue('BRL_MO_MAILNC_OBJECT', $text_vide);
        Configuration::updateValue('BRL_MO_MAILNC_MESSAGE', $text_vide);

        if (!Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'brlautomail` (
                `id` int(6) NOT null AUTO_INCREMENT,
                `id_shop` int(10) UNSIGNED NOT null,
                `id_customer` int(10) UNSIGNED NOT null,
                `email` varchar(255) NOT null,
                `date_commande` datetime DEFAULT null,
                `date_send` datetime DEFAULT null,
                `is_send` tinyint(4) NOT null,
                `id_event` tinyint(4) NOT null,
                PRIMARY KEY(`id`),
                KEY `is_send` (`is_send`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8')) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            // PS 1.4
            return parent::install() &&
            $this->registerHook('newOrder') &&
            $this->registerHook('postUpdateOrderStatus');
        } elseif (version_compare(_PS_VERSION_, '1.7', '<')) {
            // PS 1.5 et 1.6
            return parent::install() &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('actionOrderStatusPostUpdate');
        } else {
            // > PS 1.7
            return parent::install() &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('actionOrderStatusPostUpdate');
        }
    }

    public function uninstall()
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'brlautomail');
        $this->unregisterHook('NewOrder');
        $this->unregisterHook('actionValidateOrder');
        $this->unregisterHook('postUpdateOrderStatus');
        $this->unregisterHook('actionOrderStatusPostUpdate');
        Configuration::deleteByName($this->name . '_id_product');
        Configuration::deleteByName($this->name . '_mode_debug');
        Configuration::deleteByName($this->name . '_delete_logs');

        return parent::uninstall();
    }

    public function getContent()
    {
        if ($this->modeDebug) {
            // print_r($this);
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }

        $this->context->controller->addJS($this->_path . 'views/js/brlautomail.js');
        $this->context->controller->addCSS($this->_path . '/views/css/back.css');

        // PARTIE FOOTER
        $url_module = $this->context->link->getAdminLink('AdminModules', false)
         . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
         . '&token=' . Tools::getAdminTokenLite('AdminModules');

        if (file_exists($this->local_path . $this->nomLogFile)) {
            $sizeOfNomLogFile = filesize($this->local_path . $this->nomLogFile);
        } else {
            $sizeOfNomLogFile = '';
        }

        $valueDebug = Tools::getValue('brl_mode_debug');
        // selon la valeur du mode debug, fait tel ou tel update en bdd
        if ($valueDebug == 1) {
            Configuration::updateValue($this->name . '_mode_debug', 1);
        } else {
            Configuration::updateValue($this->name . '_mode_debug', 0);
        }

        $valueBrlDeleteLog = Tools::getValue('brl_delete_logs');
        // si le fichier existe et qu'il est à 1, alors supprime le fichier et update la valeur en BDD pour le repasser à 0
        if (file_exists($this->local_path . $this->nomLogFile) && ($valueBrlDeleteLog == '1')) {
            unlink($this->local_path . $this->nomLogFile);
            Configuration::updateValue($this->name . '_delete_logs', 0);
            // rediriger la page pour retirer le paramètre
            Tools::redirectAdmin($url_module);
        }

        // pour récupérer la valeur dans smarty, assigner la config à une variable que l'on appel dans le tpl
        $this->context->smarty->assign([
            'brl_mode_debug' => Configuration::get($this->name . '_mode_debug'),
            'brl_delete_logs' => Configuration::get($this->name . '_delete_logs'),
            'brl_module_version' => $this->version,
            'brl_php_version' => phpversion(),
            'brl_ps_version' => _PS_VERSION_,
            'brl_sizeFichierLog' => $sizeOfNomLogFile,
            'brl_nomFichierLog' => $this->local_path . $this->nomLogFile,
            'brl_nomModule' => $this->name,
        ]);

        if (Tools::isSubmit('BRL_MO_MAILNC_OPTION_SAVE')) {
            // SAVE PARAM
            Configuration::updateValue(
                'BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER',
                (int) Tools::getValue('BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER', '')
            );
            Configuration::updateValue(
                'BRL_MO_MAILNC_OPTION_UNIQUE',
                (int) Tools::getValue('BRL_MO_MAILNC_OPTION_UNIQUE', '')
            );
            Configuration::updateValue(
                'BRL_MO_MAILNC_OPTION_SEND',
                Tools::getValue('BRL_MO_MAILNC_OPTION_SEND', '')
            );
            Configuration::updateValue(
                'BRL_MO_MAILNC_OPTION_ETAT_SEND',
                (int) Tools::getValue('BRL_MO_MAILNC_OPTION_ETAT_SEND', '')
            );
            Configuration::updateValue(
                'BRL_MO_MAILNC_OPTION_DIFF',
                (int) Tools::getValue('BRL_MO_MAILNC_OPTION_DIFF', '')
            );

            if (!Validate::isInt(Tools::getValue('BRL_MO_MAILNC_OPTION_DIFF', ''))) {
                $this->errorMessages .= $this->displayError(
                    $this->l('Field entry error: ') . '\'' . $this->l('Deferring the shipment of') . '\''
                );
            } else {
                $this->confirmMessages .= $this->displayConfirmation($this->l('Saved options'));
            }
        } elseif (Tools::isSubmit('BRL_MO_MAILNC_SAVE')) {
            // SAVE MAIL
            $expediteur = Tools::getValue('BRL_MO_MAILNC_EXPEDITEUR', '');
            Configuration::updateValue('BRL_MO_MAILNC_EXPEDITEUR', $expediteur);

            $languages = Language::getLanguages(true);
            if (!$languages) {
                return false;
            }

            $object = [];
            $message = [];
            foreach ($languages as $lang) {
                $object[$lang['id_lang']] = Tools::getValue('BRL_MO_MAILNC_OBJECT_' . $lang['id_lang'], '');
                $message[$lang['id_lang']] = htmlentities(Tools::getValue('BRL_MO_MAILNC_MESSAGE_' . $lang['id_lang'], ''));
            }
            // $message = htmlentities($message);
            Configuration::updateValue('BRL_MO_MAILNC_OBJECT', $object);
            Configuration::updateValue('BRL_MO_MAILNC_MESSAGE', $message);

            $this->confirmMessages .= $this->displayConfirmation($this->l('Email registered'));
        } elseif (Tools::isSubmit('brl_send_test')) {
            $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            $expediteur = Configuration::get('BRL_MO_MAILNC_EXPEDITEUR');
            $object = Configuration::get('BRL_MO_MAILNC_OBJECT', $lang->id);
            $message = Configuration::get('BRL_MO_MAILNC_MESSAGE', $lang->id);

            $email_test = trim(Tools::getValue('email_test', ''));
            if (!Validate::isEmail($email_test)) {
                $this->errorMessages .= $this->displayError($this->l('Invalid test email'));
            } else {
                if ($this->sendMail($email_test, $expediteur, $object, $message)) {
                    $this->confirmMessages .= $this->displayConfirmation($this->l('Test email sent successfully'));
                } else {
                    $this->errorMessages .= $this->displayError($this->l('Error sending test email'));
                }
            }
        }

        $retour = $this->confirmMessages . $this->errorMessages;

        $this->context->smarty->assign(
            'action', $this->context->link->getAdminLink(
                'AdminModules',
                false
            ) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
        );
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            if ((int) Shop::getContextShopID() != 0) {
                $retour .= $this->displayStat();
                // Parametres d'envoi
                $retour .= $this->renderFormParam();
                $retour .= $this->renderForm();
                // Mail de test
                $retour .= $this->display(__FILE__, 'views/templates/admin/formmailtest.tpl');
            } else {
                $retour .= $this->displayError($this->l('Please select a store to start configure the module.'));
            }
        } else {
            $retour .= $this->displayStat();
            // Parametres d'envoi
            $retour .= $this->renderFormParam();
            $retour .= $this->renderForm();
            // Mail de test
            $retour .= $this->display(__FILE__, 'views/templates/admin/formmailtest.tpl');
        }

        // PARTIES HEADER & FOOTER
        $output = '';
        $output2 = '';

        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/brlheader.tpl');
        $this->context->smarty->assign('url_module', $url_module);
        $output2 = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/brlfooter.tpl');

        return $output . $retour . $output2;
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Email'),
                    'icon' => 'icon-envelope',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Sender'),
                        'name' => 'BRL_MO_MAILNC_EXPEDITEUR',
                        'lang' => false,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Object'),
                        'name' => 'BRL_MO_MAILNC_OBJECT',
                        'lang' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Message (html)'),
                        'name' => 'BRL_MO_MAILNC_MESSAGE',
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 10,
                        'class' => 'rte',
                        'desc' => $this->l('According to the law RGPD, depending on the nature of your mail, remember to add in your mail the legal notices that is appropriate'),
                        'autoload_rte' => true,
                        'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
        Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'BRL_MO_MAILNC_SAVE';
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        )
         . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages(true);
        if (!$languages) {
            return false;
        }

        $data = [
            'BRL_MO_MAILNC_EXPEDITEUR' => Configuration::get('BRL_MO_MAILNC_EXPEDITEUR', null, null, (int) Shop::getContextShopID()),
        ];

        foreach ($languages as $lang) {
            $data['BRL_MO_MAILNC_OBJECT'][$lang['id_lang']] = Configuration::get('BRL_MO_MAILNC_OBJECT', $lang['id_lang'], null, (int) Shop::getContextShopID());
            $data['BRL_MO_MAILNC_MESSAGE'][$lang['id_lang']] = html_entity_decode(Configuration::get('BRL_MO_MAILNC_MESSAGE', $lang['id_lang'], null, (int) Shop::getContextShopID()));
        }

        return $data;
    }

    public function renderFormParam()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Shipping option'),
                    'icon' => 'icon-cog',
                ],
                'input' => [
                    [
                        'label' => $this->l('Only for the customers registered to the newsletter'),
                        'name' => 'BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER',
                        'type' => 'switch',
                        'is_bool' => true,
                        'class' => '',
                        'values' => [
                            ['label' => $this->l('Yes'), 'value' => 1, 'id' => 'BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER_1'],
                            ['label' => $this->l('No'), 'value' => 0, 'id' => 'BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER_0'],
                        ],
                    ],
                    [
                        'label' => $this->l('Only once per customer'),
                        'name' => 'BRL_MO_MAILNC_OPTION_UNIQUE',
                        'type' => 'switch',
                        'is_bool' => true,
                        'class' => '',
                        'values' => [
                            ['label' => $this->l('Yes'), 'value' => 1, 'id' => 'BRL_MO_MAILNC_OPTION_UNIQUE_1'],
                            ['label' => $this->l('No'), 'value' => 0, 'id' => 'BRL_MO_MAILNC_OPTION_UNIQUE_0'],
                        ],
                    ],
                    [
                        'label' => $this->l('Send the mail'),
                        'name' => 'BRL_MO_MAILNC_OPTION_SEND',
                        'type' => 'radio',
                        'is_bool' => true,
                        'class' => '',
                        'values' => [
                            ['label' => $this->l('Has the creation of an order'), 'value' => 'new', 'id' => 'BRL_MO_MAILNC_OPTION_SEND_NEW'],
                            ['label' => $this->l('On a change of state of an order'), 'value' => 'etat', 'id' => 'BRL_MO_MAILNC_OPTION_SEND_ETAT'],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('When the command passes to the'),
                        'name' => 'BRL_MO_MAILNC_OPTION_ETAT_SEND',
                        'required' => false,
                        'class' => 'hide_etat_new_commande fixed-width-xxl',
                        'options' => [
                            'query' => OrderState::getOrderStates(Context::getContext()->cookie->id_lang),
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Deferring the shipment of'),
                        'desc' => $this->l(
                            'With deferring, any consignments to be made are made at each change of state of an order. So be careful if you do not have a lot of order.'
                        ),
                        'name' => 'BRL_MO_MAILNC_OPTION_DIFF',
                        'class' => 'fixed-width-xxl',
                        'suffix' => $this->l('Days'),
                        'size' => 4,
                        'required' => false,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
        Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'BRL_MO_MAILNC_OPTION_SAVE';
        $helper->currentIndex = $this->context->link->getAdminLink(
            'AdminModules',
            false
        )
         . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValuesParam(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValuesParam()
    {
        $data = ['BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER' => (int) Configuration::get('BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER', null, null, (int) Shop::getContextShopID()),
            'BRL_MO_MAILNC_OPTION_UNIQUE' => (int) Configuration::get('BRL_MO_MAILNC_OPTION_UNIQUE', null, null, (int) Shop::getContextShopID()),
            'BRL_MO_MAILNC_OPTION_SEND' => Configuration::get('BRL_MO_MAILNC_OPTION_SEND', null, null, (int) Shop::getContextShopID()),
            'BRL_MO_MAILNC_OPTION_ETAT_SEND' => (int) Configuration::get('BRL_MO_MAILNC_OPTION_ETAT_SEND', null, null, (int) Shop::getContextShopID()),
            'BRL_MO_MAILNC_OPTION_DIFF' => (int) Configuration::get('BRL_MO_MAILNC_OPTION_DIFF', null, null, (int) Shop::getContextShopID()),
        ];
        return $data;
    }

    public function hookNewOrder($params)
    {
        return $this->hookActionValidateOrder($params);
    }
    public function hookActionValidateOrder($params)
    {
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        // $lang->id
        $id_shop_cart = $params['cart']->id_shop;
        $id_lang_cart = $params['cart']->id_lang;

        $expediteur = Configuration::get('BRL_MO_MAILNC_EXPEDITEUR', null, null, $id_shop_cart);
        $object = Configuration::get('BRL_MO_MAILNC_OBJECT', $id_lang_cart, null, $id_shop_cart);
        $message = Configuration::get('BRL_MO_MAILNC_MESSAGE', $id_lang_cart, null, $id_shop_cart);
        $id_customer = $params['cart']->id_customer;
        $email_customer = $params['customer']->email;
        $registered_newsletter_customer = $this->getRegisteredNewsletterCustomer($id_customer);

        $brl_mo_mailnc_option_register_newsletter = (int) Configuration::get('BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER', null, null, $id_shop_cart);
        $brl_mo_mailnc_option_unique = (int) Configuration::get('BRL_MO_MAILNC_OPTION_UNIQUE', null, null, $id_shop_cart);
        $brl_mo_mailnc_option_send = Configuration::get('BRL_MO_MAILNC_OPTION_SEND', null, null, $id_shop_cart);
        $brl_mo_mailnc_option_diff = (int) Configuration::get('BRL_MO_MAILNC_OPTION_DIFF', null, null, $id_shop_cart);

        if ($brl_mo_mailnc_option_register_newsletter == 1 && $registered_newsletter_customer == 0) {
            $this->BRLlog(
                'hookActionValidateOrder : Uniquement si option : Seulement pour les clients inscrits à la newsletter'
            );
            return;
        }

        // Envoi des éventuels différés
        if (!empty($brl_mo_mailnc_option_diff)) {
            $this->sendMailDiffere($expediteur, $object, $message, $brl_mo_mailnc_option_diff, $brl_mo_mailnc_option_send, $id_shop_cart);
        }

        // Uniquement si option : A la création de la commande
        if ($brl_mo_mailnc_option_send != 'new') {
            $this->BRLlog('hookActionValidateOrder : Uniquement si option : A la création de la commande');
            return;
        }

        // Check option une fois par client
        if ($brl_mo_mailnc_option_unique == '1') {
            if (!$this->isCustomerFirstMail($id_customer)) {
                $this->BRLlog('hookActionValidateOrder : Check option une fois par client');
                return;
            }
        }

        $this->BRLlog('hookActionValidateOrder : sendMail(' . $email_customer . ',' . $expediteur . ',' . $object . ',' . $message . ')');

        // Check etat d'envoi
        if (empty($brl_mo_mailnc_option_diff)) {
            // envoi imédiat
            if ($this->sendMail($email_customer, $expediteur, $object, $message)) {
                $this->saveSendMail($id_customer, $email_customer, $brl_mo_mailnc_option_send, 1, $id_shop_cart);
                $this->BRLlog('hookActionValidateOrder : sendMail(' . $email_customer . ',' . $expediteur . ',' . $object . ',' . $message . ')');
            } else {
                $this->BRLlog('hookActionValidateOrder : sendMail ERROR (' . $email_customer . ',' . $expediteur . ',' . $object . ',' . $message . ')');
            }
        } else {
            // Enregistrement de l'envoi différé du mail
            $this->saveSendMail($id_customer, $email_customer, $brl_mo_mailnc_option_send, 0, $id_shop_cart);
            $this->BRLlog('hookActionOrderStatusPostUpdate : saveSendMail');
        }
        /*if ($this->sendMail($email_customer, $expediteur, $object, $message)) {
            $this->saveSendMail($id_customer, $email_customer);
        } else {
            $this->BRLlog("hookActionValidateOrder : sendMail ERROR ($email_customer, $expediteur, $object, $message)");
        }*/
    }

    public function hookPostUpdateOrderStatus($params)
    {
        return $this->hookActionOrderStatusPostUpdate($params);
    }
    public function hookActionOrderStatusPostUpdate($params)
    {
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        // $lang->id
        $id_shop_order = 0;
        $newOrderStatus = $params['newOrderStatus']->id; // after status changed
        // $orderStatus = $params['orderStatus']; after order is placed
        $id_customer = '';
        $id_lang_cart = 0;

        if (isset($params['id_order']) && !empty($params['id_order'])) {
            $order = new Order($params['id_order']);
            $id_customer = $order->id_customer;
            $id_shop_order = $order->id_shop;
            $id_lang_cart = $order->id_lang;
        }

        if (empty($id_customer) && isset($params['cart']) && isset($params['cart']->id_customer) && !empty($params['cart']->id_customer)) {
            $id_customer = $params['cart']->id_customer;
            $id_shop_order = $params['cart']->id_shop;
            $id_lang_cart = $params['cart']->id_lang;
        }

        $expediteur = Configuration::get('BRL_MO_MAILNC_EXPEDITEUR', null, null, $id_shop_order);
        $object = Configuration::get('BRL_MO_MAILNC_OBJECT', $id_lang_cart, null, $id_shop_order);
        $message = Configuration::get('BRL_MO_MAILNC_MESSAGE', $id_lang_cart, null, $id_shop_order);
        $email_customer = $this->getEmailCustomer($id_customer);
        $registered_newsletter_customer = $this->getRegisteredNewsletterCustomer($id_customer);

        $brl_mo_mailnc_option_register_newsletter = (int) Configuration::get('BRL_MO_MAILNC_OPTION_REGISTER_NEWSLETTER', null, null, $id_shop_order);
        $brl_mo_mailnc_option_unique = (int) Configuration::get('BRL_MO_MAILNC_OPTION_UNIQUE', null, null, $id_shop_order);
        $brl_mo_mailnc_option_send = Configuration::get('BRL_MO_MAILNC_OPTION_SEND', null, null, $id_shop_order);
        $brl_mo_mailnc_option_etat_send = (int) Configuration::get('BRL_MO_MAILNC_OPTION_ETAT_SEND', null, null, $id_shop_order);
        $brl_mo_mailnc_option_diff = (int) Configuration::get('BRL_MO_MAILNC_OPTION_DIFF', null, null, $id_shop_order);

        if ($brl_mo_mailnc_option_register_newsletter == 1 && $registered_newsletter_customer == 0) {
            $this->BRLlog('hookActionValidateOrder : Uniquement si option : Seulement pour les clients inscrits à la newsletter');
            return;
        }

        // Envoi des éventuels différés
        if (!empty($brl_mo_mailnc_option_diff)) {
            $this->sendMailDiffere($expediteur, $object, $message, $brl_mo_mailnc_option_diff, $brl_mo_mailnc_option_send, $id_shop_order);
        }

        // Uniquement si option : Sur changement d'etat
        if ($brl_mo_mailnc_option_send != 'etat') {
            $this->BRLlog('hookActionOrderStatusPostUpdate : Uniquement si option : Sur changement d\'etat');
            return;
        }

        // Check option une fois par client
        if ($brl_mo_mailnc_option_unique == '1') {
            if (!$this->isCustomerFirstMail($id_customer)) {
                $this->BRLlog('hookActionOrderStatusPostUpdate : Check option une fois par client');
                return;
            }
        }

        // Check etat d'envoi
        if ($brl_mo_mailnc_option_etat_send == $newOrderStatus) {
            if (empty($brl_mo_mailnc_option_diff)) {
                // envoi imédiat
                if ($this->sendMail($email_customer, $expediteur, $object, $message)) {
                    $this->saveSendMail($id_customer, $email_customer, $brl_mo_mailnc_option_send, 1, $id_shop_order);
                    $this->BRLlog(
                        'hookActionOrderStatusPostUpdate :
                        sendMail (' . $email_customer . ',' . $expediteur . ',' . $object . ',' . $message . ')'
                    );
                } else {
                    $this->BRLlog(
                        'hookActionOrderStatusPostUpdate :
                        sendMail ERROR (' . $email_customer . ',' . $expediteur . ',' . $object . ',' . $message . ')'
                    );
                }
            } else {
                // Enregistrement de l'envoi différé du mail
                $this->saveSendMail($id_customer, $email_customer, $brl_mo_mailnc_option_send, 0, $id_shop_order);
                $this->BRLlog('hookActionOrderStatusPostUpdate : saveSendMail');
            }
        } else {
            $this->BRLlog(
                'hookActionOrderStatusPostUpdate : Check etat d\'envoi : ' . $brl_mo_mailnc_option_etat_send . '!=' . $newOrderStatus
            );
        }
    }

    public function sendMail($destinataire, $expediteur, $sujet, $message)
    {
        if (empty($destinataire) || empty($expediteur) || empty($sujet) || empty($message)) {
            return false;
        }
        // pour les boutiques qui n'arrivent pas à interpréter le html
        $message = html_entity_decode($message);
        /*$message = html_entity_decode($message);
        $headers = "From: \"$expediteur\"<$expediteur>\n";
        $headers .= "Reply-To: $expediteur\n";
        $headers .= "Content-Type: text/html; charset=\"iso-8859-1\"";
        return mail($destinataire, $sujet, $message, $headers);
        */

        // TODO : CREER LE DOSSIER DE LANGUE DANS /mail S'IL N'EXISTE PAS !!!!!!

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $donnees = ['{title_mail}' => $sujet, '{body_mail}' => $message];
        return Mail::Send(
            $lang->id,
            'template',
            $sujet,
            $donnees,
            $destinataire,
            null,
            $expediteur,
            null,
            null,
            null,
            dirname(__FILE__) . '/mails/'
        );
    }

    public function saveSendMail($id_customer, $email, $event, $is_send, $id_shop)
    {
        if (empty($id_customer)) {
            return;
        }

        $id_event = 1;
        if ($event == 'etat') {
            $id_event = 2;
        }

        $date_commande = date('Y-m-d H:i:s');
        $date_send = date('Y-m-d H:i:s');
        if (empty($is_send)) {
            $date_send = '0000-00-00 00:00:00';
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'brlautomail` (id_shop, id_customer, email, date_commande, date_send, is_send, id_event)
             VALUES
            (' . (int) $id_shop . ',
            ' . (int) $id_customer . ',
            \'' . pSQL($email) . '\',
            \'' . pSQL($date_commande) . '\',
            \'' . pSQL($date_send) . '\',
            ' . (int) $is_send . ',
            ' . (int) $id_event . ')'
        );
        $result_bool = empty($result);

        return $result_bool;
    }

    public function sendMailDiffere($expediteur, $object, $message, $nbrJourDiffere, $event, $id_shop)
    {
        $id_event = 1;
        if ($event == 'etat') {
            $id_event = 2;
        }
        $filtreShop = ' AND id_shop = ' . (int) $id_shop . ' ';
        $date_send = date('Y-m-d H:i:s');
        $lstEnvoi = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * 
            FROM `' . _DB_PREFIX_ . 'brlautomail` 
            WHERE is_send = 0 
            AND id_event = ' . (int) $id_event . '
			' . $filtreShop . '
            AND DATEDIFF(NOW(), `date_commande` ) >= ' . (int) $nbrJourDiffere
        );

        foreach ($lstEnvoi as $infoEnvoi) {
            $idEnvoi = $infoEnvoi['id'];
            $email_customer = $infoEnvoi['email'];

            if ($this->sendMail($email_customer, $expediteur, $object, $message)) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'brlautomail` 
                    SET `is_send` = \'1\', date_send = \'' . pSQL($date_send) . '\' 
                    WHERE id = ' . (int) $idEnvoi
                );
                $this->BRLlog('sendMailDiffere : ' . $infoEnvoi['id']);
            } else {
                $this->BRLlog(
                    'sendMailDiffere : '
                    . $infoEnvoi['id'] .
                    ' sendMail ERROR ($email_customer, $expediteur, $object, $message)'
                );
            }
        }
        $this->BRLlog('hookActionOrderStatusPostUpdate : sendMailDiffere');
    }

    public function getEmailCustomer($id_customer)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT email FROM `' . _DB_PREFIX_ . 'customer` WHERE id_customer = ' . (int) $id_customer . '');
    }

    public function getRegisteredNewsletterCustomer($id_customer)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT newsletter FROM `' . _DB_PREFIX_ . 'customer` WHERE id_customer = ' . (int) $id_customer . '');
    }

    public function isCustomerFirstMail($id_customer)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT DISTINCT id_customer FROM `' . _DB_PREFIX_ . 'brlautomail` WHERE id_customer = \'' . (int) $id_customer . '\'');
        $result_bool = empty($result);
        return $result_bool;
    }

    public function displayStat()
    {
        $id_shop = (int) Shop::getContextShopID();
        $filtreShop = '';
        $infosShop = ' (ID Shop : $id_shop)';
        if (!empty($id_shop)) {
            $filtreShop = ' AND id_shop = ' . (int) $id_shop . ' ';
        }

        $mailEnvoye = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'brlautomail` WHERE date_send > DATE_SUB(NOW(), INTERVAL 30 DAY) ' . $filtreShop);

        $mailEnAttente = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'brlautomail` WHERE is_send = 0 ' . $filtreShop);

        $lstMailsEnAttente = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT email FROM `' . _DB_PREFIX_ . 'brlautomail` WHERE is_send = 0 ' . $filtreShop);

        if (empty($mailEnvoye)) {
            $mailEnvoye = 0;
        }
        if (empty($mailEnAttente)) {
            $mailEnAttente = 0;
        }

        $this->context->smarty->assign('infosShop', $infosShop);
        $this->context->smarty->assign('mailEnvoye', $mailEnvoye);
        $this->context->smarty->assign('mailEnAttente', $mailEnAttente);
        $this->context->smarty->assign('lstMailsEnAttente', $lstMailsEnAttente);

        return $this->display(__FILE__, 'views/templates/admin/statmail.tpl');
    }

    public function BRLlog($text)
    {
        // récupérer l'id shop
        $idShop = (int) Shop::getContextShopID();
        // récup la config
        if (Configuration::get($this->name . '_mode_debug', 0) == 1) {
            $date = date('d-m-Y H:i:s');
            $fp = fopen($this->local_path . $this->nomLogFile, 'a');
            fwrite($fp, print_r($date . ' / id_shop : ' . $idShop . ' ' . $text, true) . '\n');
            fclose($fp);
        }
    }
}
