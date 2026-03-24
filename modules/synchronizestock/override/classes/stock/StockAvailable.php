<?php
/**
 * StockAvailable.php
 * File generated with module files generator by SzpaQ <dev-bot>
 * This file is part od module synchronizestock (Synchronizacja z magazynem)
 * @author SzpaQ
 * @copyright 2018 SzpaQ
 * @license All Rights Reserved
 * */

class StockAvailable extends StockAvailableCore
{
       public static function getQuantityAvailableByProduct(
        $id_product = null,
        $id_product_attribute = null,
        $id_shop = null
    ) {
        $log = false;
        $result = parent::getQuantityAvailableByProduct(
            $id_product,
            $id_product_attribute,
            $id_shop
        );
        if ($id_product === null) {
            return $result;
        }
            if (Module::isEnabled('synchronizestock')) {
                $module = Module::getInstanceByName('synchronizestock');
                if ($module->isMattress($id_product)) {
                    return $result;
                }
                $product = new Product($id_product);
                if (!$product->reference) {
                    return $result;
                }
                if ($module -> isMezz($product)) {
                    /* * if is mezz product * */
                    return (int) $module->getQuantityMezz($product, $result);
                } elseif ($module -> isWsb($product->id)) {
                    /** if product belongs to wsb supplier */
                    if (!$module -> isWsbReplacable($product)) {
                        return (int) $module -> getQuantityWsb($product);
                    } else {
                        return 0;
                    }

                    if ($ref !== false) {
                        return $ref;
                        /* *
                         * check if there is mezz product
                         * that would be enabled
                         * if so disable wsb product and enable mezz
                         * */
                    }
                    return $result;
                } else {
                    return (int) $module->getQuantityOthers($product);
                }
            } else {

                return $result;
            }





        $module = Module::getInstanceByName('synchronizestock');

        /** Exclude mattresses from logic */
        if ($module -> isMattress($id_product)) {
            return $result;
        }

        $logs = [];
        $product = new Product($id_product);
        if ($module -> isMezz($product)) {
            if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {
                $result = (int) $module->getQuantityMezz($product);
                return $result;
            }
        } elseif ($module -> isWsb($product->id)) {
            if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {
                $saveProduct = false;
                if ($ref = $module -> isWsbReplacable($product)) {

                    /**/
                    header('Content-Type:application/json');
                    echo json_encode($result);
                    exit;
                    /**/
                    if ($product -> active == 1) {
                        $product -> active = 0;
                        $saveProduct = true;
                        $logs['disabled'] = true;
                        $logs['reason'] = 'Found mezz product:'. $ref;
                    }
                } else {
                    $result = $module -> getQuantityWsb($product);
                }
                return $result;
            }
        } else {

        }
        $url = 'http://stock.liquidationfurniture.ie/apii.php?get_stock_warehouse='. $product->reference;
        $stock = file_get_contents($url);
        $stock = json_decode($stock);
        if ($stock && $stock->outside_supplier_reference_no) {
            $logs = [];
            $stock->quantity = $stock -> quantity > 0 ? 1 : 0;
            if ($result != $stock->quantity) {
                $logs['quantity_old'] = $result;
                $logs['quantity_new'] = $stock->quantity;
                self::setQuantity($id_product, $id_product_attribute, $stock->quantity);
            }
            if (trim($stock->outside_supplier_reference_no)) {
                $WSB = Db::getInstance()->getValue("
                    SELECT id_product
                    FROM ". _DB_PREFIX_ ."product
                    WHERE reference = '". $stock->outside_supplier_reference_no ."'
                ");
                if ($WSB) {
                    $wsb = new Product($WSB);
                    if ($wsb->active == 0 && $stock -> quantity == 0) {
                        $logs['enabled_wsb'] = $wsb->reference;
                        $wsb -> active = 1;
                        $wsb->save();
                    } elseif ($wsb->active == 1 && $stock -> quantity > 0) {
                        $wsb -> active = 0;
                        $wsb->save();
                        $logs['disabled_wsb'] = $wsb->reference;
                    }
                }

            }
            if ($stock -> quantity > 0) {

            }
            $saveProduct = false;
            if ($stock -> quantity < 1) {
                if ($product -> active == 1) {
                    $logs['disabled'] = true;
                    $saveProduct = true;
                    $product -> active = 0;
                }
            } elseif ($product -> active == 0) {
                $saveProduct = true;
                $product -> active = 1;
                $logs['enabled'] = true;
            }
            if ($stock -> reference != $product -> reference) {
                $logs['reference_old'] = $product->reference;
                $logs['reference_new'] = $stock->reference;
                $saveProduct = true;
                $product->reference = $stock->reference;
            }
            if ($saveProduct === true) {
                $product->save();
            }
            if ($log === true) {
                if (!empty($logs)) {
                    Context::getContext()->cronController->addLog($product->reference, $logs);
                }
            }
            return (int) $stock->quantity;
        } else {
            if (preg_match('/szpak/', $_SERVER['HTTP_USER_AGENT'])) {
                return $module->getQuantityOthers($product);
            }
            if (Module::isEnabled('synchronizestock') && !$id_product_attribute) {
                $module = Module::getInstanceByName('synchronizestock');
                $stock = $module->getStockAvailable($product, $id_product_attribute, $id_shop);
                if ($stock === false) {
                    return $result;
                }
                if ($stock === 0) {
                    if ($product->active == 1) {
                        $product->active = 0;
                        $product->save();
                    }
                    return 0;
                } elseif ($stock > 1) {
                    if ($product->active == 0) {
                        $product->active = 1;
                        $product->save();
                    }
                    if ($stock !== $result) {
              //          StockAvailable::setQuantity($id_product, $id_product_attribute, (int) $stock);
                    }
                }
                return $stock;
            }
        }
        return $result;
    }
}
