<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domainn
*/

class AdminStoreSettingsController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        Tools::redirectAdmin($this->module->getStoreLocatorLink());
    }
}
