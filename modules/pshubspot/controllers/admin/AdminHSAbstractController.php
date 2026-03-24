<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */

use Tiralineas\PsHubspot\Connection;
use Tiralineas\PsHubspot\Navigator;

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'pshubspot/vendor/autoload.php';

class AdminHSAbstractController extends ModuleAdminController
{
    const PS_HUBSPOT_SETUP_STEPS = 5;
    protected $slug;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->bootstrap = true;
    }

    /**
     * Assign smarty variables for all default views, list and form, then call other init functions
     */
    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');

            return;
        }
        $this->getLanguages();
        $this->process();
        $this->initToolbar();
        // Peta aqui si lo descomentamos, no deja entrar al Dashboard
        // $this->initTabModuleList(); deprecated in 1.7.7
        $this->initPageHeaderToolbar();
        $this->addJS(__PS_BASE_URI__ . 'modules/pshubspot/views/js/back.js');
        $this->content .= $this->renderView();

        $this->context->smarty->assign([
            'maintenance_mode' => !(bool) Configuration::get('PS_SHOP_ENABLE'),
            'content' => $this->content,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ]);
    }

    protected function isValidClientIdsStoredOrRedirect()
    {
        if (!Connection::isValidClientIdsStored()) {
            Navigator::toDashboard();
        }
    }

    protected function getTplPath($file)
    {
        return _PS_MODULE_DIR_ .
            'pshubspot/views/templates/admin/' .
            $this->slug . '/' .
            $file . '.tpl';
    }
}
