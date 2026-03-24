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

if (!defined('_PS_VERSION_')) {
    exit;
}


class EtsAbancartCache extends ObjectModel
{
    public function clearCacheAllSmarty($template, $cache_id = null)
    {
        $module = Module::getInstanceByName(_ETS_ABANCART_NAME_);
        if (!$module instanceof Ets_abandonedcart) {
            return null;
        }
        return $module->clearCache($template, _ETS_ABANCART_NAME_ . ($cache_id !== null ? '|' . trim($cache_id, '|') : ''));
    }

    public function clearCacheBoSmarty($template, $name = null, $before = null, $after = null)
    {
        $module = Module::getInstanceByName(_ETS_ABANCART_NAME_);
        if (!$module instanceof Ets_abandonedcart) {
            return null;
        }
        return $module->clearCache($template, $module->getCachedId($name, $before, $after));
    }
}
