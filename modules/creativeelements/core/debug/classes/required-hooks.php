<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\CoreXDebugXClassesXInspectionBase as InspectionBase;

class CoreXDebugXClassesXRequiredHooks extends InspectionBase
{
    public function run()
    {
        $hooks = [
            'actionfrontcontroller' . (version_compare(_PS_VERSION_, '1.7.7', '<') ? 'afterinit' : 'initafter') => 0,
            'displayfooterproduct' => 0,
            'displayheader' => 0,
            'displayoverridetemplate' => 0,
            'overridelayouttemplate' => 0,
        ];
        foreach ($hooks as $hook_name => &$id_hook) {
            if (!$id_hook = \Hook::getIdByName($hook_name)) {
                \PrestaShopLogger::addLog('Creative Elements: Required hook is missing: ' . $hook_name, 3, 0, 'SwiftMessage', 0, true);

                return false;
            }
        }
        $context = $GLOBALS['context'];
        $id_shop = $context->shop->id;
        $id_module = $context->controller->module->id;
        $rows = \Db::getInstance()->executeS(
            'SELECT `id_hook` FROM ' . _DB_PREFIX_ . 'hook_module' .
            ' WHERE `id_module` = ' . (int) $id_module . ' AND `id_shop` = ' . (int) $id_shop . ' AND `id_hook` IN (' . implode(',', array_map('intval', $hooks)) . ')'
        ) ?: [];

        if ($hook_diff = array_diff($hooks, array_column($rows, 'id_hook'))) {
            \PrestaShopLogger::addLog('Creative Elements: Required hook is not registered: ' . key($hook_diff), 3, 0, 'SwiftMessage', 0, true);

            return false;
        }

        return true;
    }

    public function getName()
    {
        return 'required-hooks';
    }

    public function getMessage()
    {
        return __('Important Hooks are missing, please reset the module!');
    }

    public function getHelpDocText()
    {
        return __('Module manager', 'Admin.Navigation.Menu');
    }

    public function getHelpDocUrl()
    {
        return _PS_VERSION_ < 9
            ? Helper::$link->getAdminLink('AdminModules', true)
            : \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('router')->generate('admin_module_manage');
    }
}
