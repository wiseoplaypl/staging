<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_13_0($module)
{
    Shop::isFeatureActive() && Shop::setContext(Shop::CONTEXT_ALL);

    // Data moved to ConfigurationKPI
    Configuration::deleteByName('CE_LATEST_VERSION');

    // Remove useless files
    Tools::deleteDirectory(_CE_PATH_ . 'views/lib/e-gallery');
    array_map('unlink', array_filter([
        _CE_PATH_ . 'modules/catalog/widgets/listing/page-title.php',
    ], 'file_exists'));

    $module->registerHook('dashboardZoneOne', null, 0);

    return true;
}
