<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class LGProductMove extends Module
{
    public $bootstrap;

    public function __construct()
    {
        $this->name          = 'lgproductmove';
        $this->tab           = 'quick_bulk_update';
        $this->version       = '1.4.7';
        $this->author        = 'Línea Gráfica';
        $this->need_instance = 0;
        $this->module_key    = '3ee980ae847a1db080d6f0a56d348e19';
        if (substr_count(_PS_VERSION_, '1.6') > 0) {
            $this->bootstrap = true;
        } else {
            $this->bootstrap = false;
        }
        parent::__construct();
        $this->displayName = $this->l('Moving and Assigning Products between Categories');
        $this->description =
        $this->l('Move and assign products between categories from the menu Catalog - Moving / Assigning products.');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }
        return $this->installModuleTab('AdminLGProductMove', $this->l('Moving / Assigning products'), 'AdminCatalog');
    }

    public function uninstall()
    {
        $result = $this->uninstallTab('AdminLGProductMove');
        return $result && parent::uninstall();
    }

    public function uninstallTab($class)
    {
        $id_tab = (int)Tab::getIdFromClassName($class);
        $tab = new Tab($id_tab);
        return $tab->delete();
    }

    private function installModuleTab($class, $name, $class_parent)
    {
        if (!is_array($name)) {
            $name = self::getMultilangField($name);
        }
        $tab             = new Tab();
        $tab->name       = $name;
        $tab->class_name = $class;
        $tab->module     = $this->name;
        $tab->id_parent  = $tab->id_parent = (int)Tab::getIdFromClassName($class_parent);
        return $tab->save();
    }

    private function getMultilangField($field)
    {
        $languages = Language::getLanguages();
        $res = array();
        foreach ($languages as $lang) {
            $res[$lang['id_lang']] = $field;
        }
        return $res;
    }
}
