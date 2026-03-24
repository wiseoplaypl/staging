<?php
/**
 * AdminStaffCodeConfigurationController.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module staffcode (Staff Code)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminStaffCodeConfigurationController extends ModuleAdminController
{
    public $obj = false;
    public $confirmations = [];
    public function __construct()
    {
        parent::__construct();
        $this->module = Module::getInstanceByName('staffcode');
        $this->bootstrap = true;
        $this->name = $this->l("StaffCodeConfiguration");
        $this->displayName = $this->l("StaffCodeConfiguration");
        $this->toolbar_title = $this->l("StaffCodeConfiguration");
    }
    public function initContent()
    {
        parent::initContent();
        $this->processAddStaffCode();
        $this->context->smarty->assign(array(
            'content' =>
            $this->renderListStaffCodeOrders() .
            $this->renderListStaffCodes() .
            $this->renderFormAddStaffCode() .
            $this->renderFormConfig()
        ));
    }
    public function renderListStaffCodes()
    {
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->no_link = false;
        $helper->identifier = 'id_staff_code';
        $helper->show_toolbar = false;
        $helper->module = $this->module;
        $helper->table ='staff_codes';
        $helper->title = $this->l('Staff Codes');
        $helper->token = Tools::getAdminTokenLite('AdminStaffCodeConfiguration');
        $page = (int) Tools::getValue('submitFilter'. $helper->table) ?: 1;
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int) Tools::getValue($helper->table .'_pagination') ?: 500;
        $limit = (($page * $perPage) - $perPage) .','. $perPage;
        $query = "
            SELECT
                id_staff_code,
                email_address,
                code
            FROM
                ". _DB_PREFIX_ ."staff_codes
        ";
        $fields = array(
            'id_staff_code' => array(
                'title' => $this->l('#'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
            'email_address' => array(
                'title' => $this->l('Email'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
            'code' => array(
                'title' => $this->l('Staff Code'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
        );
        $where = array();
        $order = '';
        foreach ($fields as $k => $v) {
            if ($v['search'] && Tools::getValue($helper->table.'Filter_'. $k)) {
                $where[] = $k . ' LIKE \'%'. pSQL(Tools::getValue($helper->table.'Filter_'. $k)) .'%\'';
            }
            if (Tools::getValue($helper->table.'Orderby')
                && Tools::getValue($helper->table.'Orderway')
            ) {
                $order = ' ORDER BY '.Tools::getValue($helper->table.'Orderby') .' '
                    . (Tools::getValue($helper->table.'Orderway') == 'desc' ? 'DESC' : 'ASC');
            }
        }
        if (count($where)) {
            $query .= ' WHERE '. implode(' AND ', $where);
        }
        $helper->listTotal = Db::getInstance()->getValue(
            preg_replace('/SELECT ([^FROM]*)/', "SELECT COUNT(*) ", $query)
        );
        $query .= $order . ' LIMIT '. $limit;
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminStaffCodeConfiguration', false);
        $helper->actions = array('delete');
        $helper->toolbar_btn['new'] = array(
            'href' => $helper->currentIndex. '&add'.
            '&token='.Tools::getAdminTokenLite('AdminStaffCodeConfiguration'),
            'desc' => $this->l('Add new...')
        );
        return $helper->generateList(Db::getInstance()->executeS($query), $fields);
    }
    public function renderListStaffCodeOrders()
    {
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->no_link = false;
        $helper->identifier = 'id_staff_code';
        $helper->show_toolbar = true;
        $helper->module = $this->module;
        $helper->table ='staff_codes';
        $helper->title = $this->l('Orders List');
        $helper->token = Tools::getAdminTokenLite('AdminStaffCodeConfiguration');
        $page = (int) Tools::getValue('submitFilter'. $helper->table) ?: 1;
        if ($page < 1) {
            $page = 1;
        }
        $perPage = (int) Tools::getValue($helper->table .'_pagination') ?: 50;
        $limit = (($page * $perPage) - $perPage) .','. $perPage;
        $query = "
            SELECT
                sco.id_staff_code_order,
                sco.id_staff_code,
                sco.id_order,
                sc.code,
                sc.email_address,
                o.reference,
                o.total_paid
            FROM
                ". _DB_PREFIX_ ."staff_code_order sco
            LEFT JOIN
                ". _DB_PREFIX_ ."staff_codes sc
            ON
                sc.id_staff_code = sco.id_staff_code
            LEFT JOIN
                ". _DB_PREFIX_ ."orders o
            ON
                sco.id_order = o.id_order
            ORDER BY
                sco.id_order DESC

        ";
        $fields = array(
            'id_staff_code' => array(
                'title' => $this->l('#'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
            'id_order' => array(
                'title' => $this->l('ID ORDER'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
            'email_address' => array(
                'title' => $this->l('Email'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
            'code' => array(
                'title' => $this->l('Staff Code'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
            'total_paid' => array(
                'title' => $this->l('Total'),
                'type' => 'string',
                'orderby' => true,
                'search' => true,
            ),
        );
        $where = array();
        $order = '';
        foreach ($fields as $k => $v) {
            if ($v['search'] && Tools::getValue($helper->table.'Filter_'. $k)) {
                $where[] = $k . ' LIKE \'%'. pSQL(Tools::getValue($helper->table.'Filter_'. $k)) .'%\'';
            }
            if (Tools::getValue($helper->table.'Orderby')
                && Tools::getValue($helper->table.'Orderway')
            ) {
                $order = ' ORDER BY '.Tools::getValue($helper->table.'Orderby') .' '
                    . (Tools::getValue($helper->table.'Orderway') == 'desc' ? 'DESC' : 'ASC');
            }
        }
        if (count($where)) {
            $query .= ' WHERE '. implode(' AND ', $where);
        }
        $helper->listTotal = Db::getInstance()->getValue(
            preg_replace('/SELECT ([^FROM]*)/', "SELECT COUNT(*) ", $query)
        );
        $query .= $order . ' LIMIT '. $limit;
        $helper->currentIndex = Context::getContext()->link->getAdminLink('AdminStaffCodeConfiguration', false);
        $helper->actions = array();
        $helper->toolbar_btn['new'] = array(
            'href' => $helper->currentIndex. '&add'.
            '&token='.Tools::getAdminTokenLite('AdminStaffCodeConfiguration'),
            'desc' => $this->l('Add new...')
        );
        return $helper->generateList(Db::getInstance()->executeS($query), $fields);
    }
    public function renderFormAddStaffCode()
    {
        $this->helper = new HelperForm();
        $this->helper->module = $this;
        $this->helper->token = Tools::getAdminTokenLite('StaffCodeConfiguration');
        $this->helper->title = $this->l('Add Staff Code');
        $form = array();
        $form['legend'] = array('title' => $this->l('Add Staff Code'));
        $form['input'] = array(
            array(
                'type' => 'text',
                'label' => $this->l('Staff Code'),
                'required' => true,
                'name' => 'code',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Email address'),
                'required' => true,
                'name' => 'emailaddress',
            ),
        );
        $form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'name' => 'AddStaffCode_Submit',
        );
        $this->helper->fields_value = array(
            'code' => Tools::getValue('code'),
            'emailaddress' => Tools::getValue('emailaddress'),
        );
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function processFormConfig()
    {
        if (Tools::getIsset('AddStaffCode_Config')) {
            Configuration::updateValue(
                'staffCodeGroup',
                json_encode(Tools::getValue('staffCodeGroup_selected')) ?: []
            );
        }
    }
    public function renderFormConfig()
    {
        $this->processFormConfig();
        $this->helper = new HelperForm();
        $this->helper->module = $this;
        $this->helper->token = Tools::getAdminTokenLite('StaffCodeConfiguration');
        $this->helper->title = $this->displayName;
        $form = array();
        $form['legend'] = array('title' => $this->l('AddStaffCode'));
        $form['input'] = array(
            array(
                'type' => 'swap',
                'label' => $this->l('Customer group'),
                'required' => false,
                'name' => 'staffCodeGroup',
                'options' => array(
                    'query' => $this->getgroupOptions(),
                    'id' => 'id',
                    'name' => 'name',
                ),
                'desc' => $this->l('Choose group of customer which will see the input in cart.')
            ),
        );
        $form['submit'] = array(
            'title' => $this->l('Save'),
            'class' => 'btn btn-default pull-right',
            'name' => 'AddStaffCode_Config',
        );
        $this->helper->fields_value = array(
            'staffCodeGroup' => json_decode(Configuration::get('staffCodeGroup'), true) ?: [],
            'staffCodeGroup_selected' => json_decode(Configuration::get('staffCodeGroup'), true) ?: []

        );
        return $this->helper->generateForm(array(array('form' => $form)));
    }
    public function processAddStaffCode()
    {
        if (Tools::getIsset('AddStaffCode_Submit')) {
            if (!filter_var(Tools::getValue('emailaddress'), FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = $this->l('Email is not valid.');
                return;
            }
            if (StaffCodes::emailExists(Tools::getValue('emailaddress'))) {
                // $this->errors[] = 'Email already on the list.';
              //  return;
            }
            if (StaffCodes::codeExists(Tools::getValue('code'))) {
                $this->errors[] = 'Code already on the list.';
                return;
            }
            if (StaffCodes::addCode(Tools::getValue('code'), Tools::getValue('emailaddress'))) {
                $this->confirmations[] = $this->l('Code successfully added.');
                return;
            } else {
                $this->errors[] = $this->l('Something went wrong during adding code.');
                return;
            }
        }
        if (Tools::getIsset('deletestaff_codes')) {
            $code = new StaffCodes((int) Tools::getValue('id_staff_code'));
            if ($code->delete()) {
                $this->confirmations[] = $this->l('Code successfully deleted.');
            } else {
                $this->errors[] = $this->l('Something went wrong during deleting code.');
            }
        }
    }
    private function getgroupOptions() : array
    {
        /** return must be an array of rows. Row must have id and name column */
        return Db::getInstance()->executeS("
            SELECT id_group as id, name
            FROM ". _DB_PREFIX_ ."group_lang
            WHERE id_lang = 1
        ");
    }

}
