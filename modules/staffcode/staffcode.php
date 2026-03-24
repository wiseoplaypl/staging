<?php
/**
 * staffcode.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module staffcode (Staff Code)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) .'/classes/StaffCodes.php';
require_once dirname(__FILE__) .'/classes/StaffCodeOrder.php';

class Staffcode extends Module
{
    private $errors = array();
    public function __construct()
    {
        $this->name = 'staffcode';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'SzpaQ';
        parent::__construct();
        $this->displayName = $this->l('Staff Code');
        $this->description = $this->l('Add staff codes and send email to associated customer');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        return !$this->installModels()
        || !parent::install()
        || !$this->registerHook('displayBenethVoucher')
        || !$this->registerHook('actionValidateOrder')
        || !$this->installTabs() ? false : true;
    }
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminStaffCodeConfiguration'));
    }
    public function hookActionValidateOrder($params = array())
    {
        return true;
    }
    public function returnHookStaffCode()
    {
        $code = new StaffCodes(Context::getContext()->cookie->staff_code);
        if ($code->id) {
            $this->context->smarty->assign('staff_code', $code);
        } else {
            $this->context->smarty->assign('staff_code', false);
        }
        $this->context->smarty->assign(
            'staff_code_error',
            Context::getContext()->cookie->staff_code_error
        );
        Context::getContext()->cookie->staff_code_error = false;
        return $this->display(__FILE__, "views/templates/hook/displayBenethVoucher.tpl");
    }
    public function hookDisplayBenethVoucher($params = array())
    {
        $selectedGroups = json_decode(Configuration::get('staffCodeGroup'), true) ?: [];
        if (
            (
                !$this->context->customer ||
                !$this->context->customer->id
            ) &&
            in_array(Configuration::get('PS_UNIDENTIFIED_GROUP'), $selectedGroups)
        ) {
            return $this->returnHookStaffCode();
        }
        if (
            $this->context->customer &&
            $this->context->customer->id
        ){
            if (in_array($this->context->customer->id_default_group, $selectedGroups)) {
                return $this->returnHookStaffCode();
            }
            if (
                Db::getInstance()->getValue("
                    SELECT id_customer
                    FROM ". _DB_PREFIX_ ."customer_group
                    WHERE id_group IN ('". implode(',', $selectedGroups) ."')
                    AND id_customer = '". $this->context->customer->id ."'
                ")
            ) {
                return $this->returnHookStaffCode();
            }
        }
    }
    private function installModels()
    {
        $models = array(
            'StaffCodes',
            'StaffCodeOrder',
        );
        foreach ($models as $v) {
            if (!$v::installTable()) {
                return false;
            }
        }
        return true;
    }
    public function installTabs()
    {
        $tabs = array(
            'AdminStaffCodeConfiguration',
        );
        Db::getInstance()->execute("
            DELETE FROM ". _DB_PREFIX_ ."tab WHERE module = '". $this->name ."'
        ");
        $languages = Language::getLanguages();
        foreach ($tabs as $class_name) {
            $tab = new Tab();
            $tab -> class_name = $class_name;
            $tab -> id_parent = -1;
            $tab -> active = 1;
            $tab -> module = $this->name;
            $tab -> name = array();
            foreach ($languages as $v) {
                $tab->name[$v['id_lang']] = $class_name;
            }
            if (!$tab->save()) {
                Db::getInstance()->execute("
                    DELETE FROM ". _DB_PREFIX_ ."tab WHERE module = '". $this->name ."'
                ");
                return false;
            }
            return true;
        }
    }

}
