<?php
/**
 * Cart.php
 * File generated with module generator by SzpaQ <dev-bot>
 * This file is part od module cartquantities (Allowed Quantities)
 * @author SzpaQ
 * @copyright 2019 SzpaQ
 * @license All Rights Reserved
 * */

class Cart extends CartCore
{
    public function updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', $id_address_delivery = 0, Shop $shop = null, $auto_add_cart_rule = true) {
        $result = parent::updateQty(
            $quantity,
            $id_product,
            $id_product_attribute,
            $id_customization,
            $operator,
            $id_address_delivery,
            $shop,
            $auto_add_cart_rule
        );
        if (Module::isEnabled('cartquantities')) {
            $module = Module::getInstanceByName('cartquantities');
            $where = "`id_product` = ".(int) $id_product .
                (
                    !empty($id_product_attribute)
                    ? " AND `id_product_attribute` = ".(int) $id_product_attribute
                    : ""
                ). "
                AND
                    `id_cart` = ". (int) $this->id .
                (
                    Configuration::get('PS_ALLOW_MULTISHIPPING') && $this->isMultiAddressDelivery()
                    ? " AND `id_address_delivery` = ". (int) $id_address_delivery
                    : ""
                ). "
            ";
            $sql = "
                SELECT
                    *
                FROM
                    ". _DB_PREFIX_ ."cart_product
                WHERE
                    $where
            ";
            $record = Db::getInstance()->getRow($sql);
            if ($record) {
                $minValue = $module->getQuantity($record['id_product']);
                $qty = ($operator == 'up')
                    ? $module->getQuantityUp($record['quantity'], $minValue)
                    : $module->getQuantityDown($record['quantity'], $minValue);
                if ($record['quantity'] != $qty) {
                    Db::getInstance()->execute("
                        UPDATE
                            ". _DB_PREFIX_ ."cart_product
                        SET
                            quantity = '". (int) $qty ."'
                        WHERE
                            $where
                    ");
                }
            }
        }
        return true;
    }
}
