<?php
/**
 * code.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module staffcode (Staff Code)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */


if (!defined('_PS_VERSION_')) {
    exit;
}

class StaffcodeCodeModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }
    public function initContent()
    {
        parent::initContent();
        /** File generated with Module generator made by SzpaQ */
        if (Tools::getIsset('addCode') && Tools::getIsset('staff_code')) {
            if ($code = StaffCodes::getByCode(Tools::getValue('staff_code'))) {
                Context::getContext()->cookie->staff_code = $code->id;
            } else {
                Context::getContext()->cookie->staff_code_error = $this->l('Code not found.');
            }
            Tools::redirect('order');
        }
        if (Tools::getIsset('delete')) {
            Context::getContext()->cookie->staff_code = false;
            Tools::redirect('order');
        }
    }
}
