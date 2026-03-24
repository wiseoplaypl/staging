<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

class Ets_advancedcaptchaCaptchaModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
        $this->imageCaptcha();
    }

    /**
     * @throws \Random\RandomException
     */
    public function imageCaptcha()
    {
        if (headers_sent())
            return;
        ob_start();
        $random_bytes = openssl_random_pseudo_bytes(4); // 4 bytes = 32 bits
        $security_code = Tools::substr(hash('sha256', $random_bytes), 17, 6);
        if (($posTo = Tools::getValue('pos', false)) && Validate::isCleanHtml($posTo)) {
            $captcha = Ets_advancedcaptcha::PREFIX_CODE . $posTo;
            $this->context->cookie->{$captcha} = $security_code;
            $this->context->cookie->write();
        } else
            die('404 not found!');

        require_once(dirname(__FILE__) . '/../../classes/ets_pa_image');
        $class= 'Ets_pa_image';
        $method = 'createImage';
        call_user_func_array(array($class, $method),array($security_code));
        exit();
    }

}