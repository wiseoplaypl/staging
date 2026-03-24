<?php
if (file_exists(dirname(__FILE__) .'/timelog')) {
    if(filesize(dirname(__FILE__) .'/timelog') > 10000) {
        file_put_contents(dirname(__FILE__) .'/timelog', '');
    }
}
$starttime = microtime(true);
file_put_contents(dirname(__FILE__) .'/timelog', date('Y-m-d H:i:s') .' Start Script'.PHP_EOL, FILE_APPEND);
require(dirname(__FILE__).'/../../config/config.inc.php');
file_put_contents(dirname(__FILE__) .'/timelog', round(microtime(true)-$starttime, 3).'s Get products from presta system start'.PHP_EOL, FILE_APPEND);
$products = Db::getInstance()->executeS("SELECT id_product, reference, active FROM ". _DB_PREFIX_ ."product");
file_put_contents(dirname(__FILE__) .'/timelog', round(microtime(true)-$starttime, 3).'s Get products from presta system end'.PHP_EOL, FILE_APPEND);
IF (!count($products)) {
    exit('NIe ma produktów');
}
$module = Module::getInstanceByName('synchronizestock');
$stocked = array();
file_put_contents(dirname(__FILE__) .'/timelog', round(microtime(true)-$starttime, 3).'s Get products from Stock system start'.PHP_EOL, FILE_APPEND);
$stockProducts = $module->getProductsFromStockSystem();
file_put_contents(dirname(__FILE__) .'/timelog', round(microtime(true)-$starttime, 3).'s Get products from Stock system end'.PHP_EOL , FILE_APPEND);

$noStock = Configuration::get('DEVBOT_STOCK_DEACTIVATE_NO_STOCK');
$zeroStock = Configuration::get("DEVBOT_STOCK_DEACTIVATE_ZERO_STOCK");
file_put_contents(dirname(__FILE__) .'/timelog', round(microtime(true)-$starttime, 3).'s loop Products START'.PHP_EOL , FILE_APPEND);
foreach ($products as $k => $v) {
    echo $v['reference'] .': ';
    try
    {
        if (isset($stockProducts[$v['reference']])) {
            if ($stockProducts[$v['reference']] == 0) {
                if ($v['active'] == 1) {
                    $stock = StockAvailable::getQuantityAvailableByProduct($v['id_product']);
                }
            } else {
                if ($v['active'] == 0) {
                    $stock = StockAvailable::getQuantityAvailableByProduct($v['id_product']);
                }
            }
        } else {
            echo 'NOT IN STOCK: ';
            if ($noStock && $v['active'] == 1)
            {
                echo 'DEACTIVATED.';
                $module->disableProduct($v['id_product']);
            }
        }
        echo PHP_EOL;
    }
    catch (Exception $e)
    {

    }
}
file_put_contents(dirname(__FILE__) .'/timelog', round(microtime(true)-$starttime, 3).'s loop Products END'.PHP_EOL.PHP_EOL.PHP_EOL , FILE_APPEND);
exit;

