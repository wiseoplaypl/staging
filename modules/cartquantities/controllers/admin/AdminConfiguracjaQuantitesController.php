<?php
/**
 * AdminConfiguracjaQuantitesController.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module cartquantities (Allowed Quantities)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminConfiguracjaQuantitesController extends ModuleAdminController
{
    public $obj = false;
    public function __construct()
    {
        parent::__construct();
        $this->module = Module::getInstanceByName('cartquantities');
        $this->bootstrap = true;
        $this->name = $this->l("ConfiguracjaQuantites");
        $this->displayName = $this->l("ConfiguracjaQuantites");
        $this->toolbar_title = $this->l("ConfiguracjaQuantites");
    }
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array(
            'content' => $this->renderFormConfiguration()
        ));
    }
    public function renderFormConfiguration()
    {
        $this->helper = new HelperForm();
        $this->helper->module = $this;
        $this->helper->token = Tools::getAdminTokenLite('ConfiguracjaQuantites');
        $this->helper->title = $this->displayName;
        $form = array();
        $form['legend'] = array('title' => $this->l('Configuration'));
        $form['input'] = array(
            array(
                'type' => 'select',
                'label' => $this->l('Feature'),
                'required' => true,
                'name' => 'idfeature',
                'desc' => 'Select feature of product which will point a number of allowed kits',
                'options' => array(
                    'query' => $this->getidfeatureOptions(),
                    'id' => 'id',
                    'name' => 'name',
                ),
            ),
        );
        $form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'name' => 'Configuration_Submit',
        );
        $this->helper->fields_value = array(
            'idfeature' => Configuration::Get($this->module->name. 'idfeature')
        );
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function postProcess()
    {
        $this->processConfiguration();
    }
    public function processConfiguration()
    {
        if (Tools::getIsset('Configuration_Submit')) {
            Configuration::updateValue(
                $this->module->name. 'idfeature',
                Tools::getValue('idfeature')
            );
        }
    }
    private function getidfeatureOptions()
    {
        return Db::getInstance()->executeS("
            SELECT
                id_feature id,
                name
            FROM
                ". _DB_PREFIX_ ."feature_lang
            WHERE
                id_lang = ". Context::getContext()->language->id ."
            GROUP BY id_feature
        ");
        /** return must be an array of rows. Row must have id and name column */
        return array();
    }
}
