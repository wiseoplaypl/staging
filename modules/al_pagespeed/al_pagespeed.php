<?php
/**
 * al_pagespeed.php
 * This file was generated with automatic module generator created by SzpaQ <dev-bot>
 * File is part of module al_pagespeed
 * @author SzpaQ
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Al_pagespeed extends Module
{
    /** @var array */
    private $errors = [];

    public function __construct()
    {
        $this->name = 'al_pagespeed';
        $this->tab = 'front_office_features';
        $this->version = '0.0.1';
        $this->author = 'SzpaQ';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Artlis - Pagespeed Insight optimalization');
        $this->description = $this->l('PageSpeed Optimization');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        $this->registerHook('displayHome');
        $this->registerHook('displayPreloads');
    }

    public function install()
    {
        return !parent::install()
        || !$this->registerHook('displayPreloads')
        || !$this->registerHook('displayHome')
            ? false
            : true;
    }

    public function hookDisplayPreloads($params = []) {
        if (isset($params['page']) && $params['page'] == 'index') {
            return $this->display(__FILE__, 'views/templates/hook/header.tpl');
        }
    }
    public function hookDisplayHome($params = [])
    {
        $this->context->smarty->assign('mprefix', Context::getContext()->isMobile() ? 'm-' : '');
        $this->context->smarty->assign(
            'sizes',
            Context::getContext()->isMobile()
                ? 'width="130" height="133"'
                : 'width="596" height="608"'
        );
        return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
    }
}
