<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */

class Cart extends CartCore
{
    //Correct a native function from prestashop with error to find packitem cart quantity
    public function getProductQuantity(
        $idProduct,
        $idProductAttribute = 0,
        $idCustomization = 0,
        $idAddressDelivery = 0
    ) {
        $productIsPack = Pack::isPack($idProduct);
        $defaultPackStockType = Configuration::get("PS_PACK_STOCK_TYPE");
        $packStockTypesAllowed = [
            Pack::STOCK_TYPE_PRODUCTS_ONLY,
            Pack::STOCK_TYPE_PACK_BOTH,
        ];
        $packStockTypesDefaultSupported = (int) in_array(
            $defaultPackStockType,
            $packStockTypesAllowed
        );
        $firstUnionSql =
            'SELECT cp.`quantity` as first_level_quantity, 0 as pack_quantity
          FROM `' .
            _DB_PREFIX_ .
            "cart_product` cp";
        $secondUnionSql =
            'SELECT 0 as first_level_quantity, cp.`quantity` * p.`quantity` as pack_quantity
          FROM `' .
            _DB_PREFIX_ .
            "cart_product` cp" .
            " JOIN `" .
            _DB_PREFIX_ .
            "pack` p ON cp.`id_product` = p.`id_product_pack`" .
            " JOIN `" .
            _DB_PREFIX_ .
            "product` pr ON p.`id_product_pack` = pr.`id_product`";

        if ($idCustomization) {
            $customizationJoin =
                '
                LEFT JOIN `' .
                _DB_PREFIX_ .
                'customization` c ON (
                    c.`id_product` = cp.`id_product`
                    AND c.`id_product_attribute` = cp.`id_product_attribute`
                )';
            $firstUnionSql .= $customizationJoin;
            $secondUnionSql .= $customizationJoin;
        }
        $commonWhere =
            '
            WHERE cp.`id_product_attribute` = ' .
            (int) $idProductAttribute .
            '
            AND cp.`id_customization` = ' .
            (int) $idCustomization .
            '
            AND cp.`id_cart` = ' .
            (int) $this->id;

        if (
            Configuration::get("PS_ALLOW_MULTISHIPPING") &&
            $this->isMultiAddressDelivery()
        ) {
            $commonWhere .=
                " AND cp.`id_address_delivery` = " . (int) $idAddressDelivery;
        }

        if ($idCustomization) {
            $commonWhere .=
                " AND c.`id_customization` = " . (int) $idCustomization;
        }
        $firstUnionSql .= $commonWhere;
        $firstUnionSql .= " AND cp.`id_product` = " . (int) $idProduct;
        if (!$productIsPack) {
            $secondUnionSql .= $commonWhere;
        }
        $secondUnionSql .= " AND cp.`id_cart` = " . (int) $this->id;

        $secondUnionSql .= " AND p.`id_product_item` = " . (int) $idProduct;
        $secondUnionSql .=
            " AND (pr.`pack_stock_type` IN (" .
            implode(",", $packStockTypesAllowed) .
            ') OR (
            pr.`pack_stock_type` = ' .
            Pack::STOCK_TYPE_DEFAULT .
            '
            AND ' .
            $packStockTypesDefaultSupported .
            ' = 1
        ))';
        $parentSql =
            'SELECT
            COALESCE(SUM(first_level_quantity) + SUM(pack_quantity), 0) as deep_quantity,
            COALESCE(SUM(first_level_quantity), 0) as quantity
          FROM (' .
            $firstUnionSql .
            " UNION " .
            $secondUnionSql .
            ") as q";

        return Db::getInstance()->getRow($parentSql);
    }
}
