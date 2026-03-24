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

class AdminHiGoogleAnalyticsController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->adminForms = $this->module->adminForms;
    }

    public function init()
    {
        parent::init();

        if (!$this->ajax) {
            Tools::redirectAdmin($this->module->hiPrestaClass->getModuleUrl('&' . $this->module->name . '=events'));
        }
    }

    protected function ajaxRender($value = null, $controller = null, $method = null)
    {
        if (method_exists(get_parent_class($this), 'ajaxRender')) {
            return parent::ajaxRender($value, $controller, $method);
        }

        if ($controller === null) {
            $controller = get_class($this);
        }

        if ($method === null) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $method = $bt[1]['function'];
        }

        /* @deprecated deprecated since 1.6.1.1 */
        Hook::exec('actionAjaxDieBefore', ['controller' => $controller, 'method' => $method, 'value' => $value]);

        /*
         * @deprecated deprecated since 1.6.1.1
         * use 'actionAjaxDie'.$controller.$method.'Before' instead
         */
        Hook::exec('actionBeforeAjaxDie' . $controller . $method, ['value' => $value]);
        Hook::exec('actionAjaxDie' . $controller . $method . 'Before', ['value' => $value]);
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

        echo $value;
    }

    protected function ajaxDie($value = null, $controller = null, $method = null)
    {
        if (ob_get_contents()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        $this->ajaxRender($value, $controller, $method);
        exit;
    }

    public function displayAjaxHideMenuModule()
    {
        $this->context->cookie->hideMenuModule = 1;
        $this->context->cookie->write();

        $this->ajaxDie(json_encode([
            'error' => false,
        ]));
    }

    public function displayAjaxRenderCustomEventForm()
    {
        $this->ajaxDie(json_encode([
            'error' => false,
            'content' => $this->adminForms->renderEventForm((int) Tools::getValue('idItem')),
        ]));
    }

    public function displayAjaxRenderCustomEventList()
    {
        $this->ajaxDie(json_encode([
            'error' => false,
            'content' => $this->adminForms->renderEventsList(Tools::getValue('filters'), Tools::getValue('pageItems'), Tools::getValue('pageNumber')),
            'filters' => Tools::getValue('filters') ? true : false,
        ]));
    }

    public function displayAjaxSaveCustomEvent()
    {
        $this->module->saveCustomEvent((int) Tools::getValue('id_event'));

        $this->ajaxDie(json_encode([
            'error' => false,
            'message' => $this->module->l('Successfully saved'),
            'content' => $this->adminForms->renderEventsList(Tools::getValue('filters'), Tools::getValue('pageItems'), Tools::getValue('pageNumber')),
            'filters' => Tools::getValue('filters') ? true : false,
        ]));
    }

    public function displayAjaxUpdateCustomEventStatus()
    {
        $idEvent = (int) Tools::getValue('idElement');
        $event = new HiGoogleAnalyticsEvent($idEvent);
        $event->active = $event->active ? 0 : 1;

        if (!$event->update()) {
            $this->ajaxDie(json_encode([
                'error' => $this->l('We couldn\'t update the event status, please try again.'),
            ]));
        }

        exit(json_encode([
            'error' => false,
            'message' => $this->module->l('Status successfully changed'),
            'content' => $this->adminForms->renderEventsList(Tools::getValue('filters'), Tools::getValue('pageItems'), Tools::getValue('pageNumber')),
            'filters' => Tools::getValue('filters') ? true : false,
        ]));
    }

    public function displayAjaxDeleteCustomEvent()
    {
        $idEvent = (int) Tools::getValue('idElement');
        $event = new HiGoogleAnalyticsEvent($idEvent);

        if (!$event->delete()) {
            $this->ajaxDie(json_encode([
                'error' => $this->l('There was an error, please refresh the page and try again'),
            ]));
        }

        $this->ajaxDie(json_encode([
            'error' => false,
            'message' => $this->module->l('Event successfully deleted'),
            'content' => $this->adminForms->renderEventsList(Tools::getValue('filters'), Tools::getValue('pageItems'), Tools::getValue('pageNumber')),
            'filters' => Tools::getValue('filters') ? true : false,
        ]));
    }
}
