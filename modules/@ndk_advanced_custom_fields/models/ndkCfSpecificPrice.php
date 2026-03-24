<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2017 Hendrik Masson
 *  @license   Tous droits réservés
 */
class NdkCfSpecificPrice extends ObjectModel
{
    public $id_ndk_customization_field_specific_price;
    public $id_ndk_customization_field;
    public $id_ndk_customization_field_value;
    public $reduction;
    public $reduction_type;
    public $from_quantity;

    public static $definition = [
        'table' => 'ndk_customization_field_specific_price',
        'primary' => 'id_ndk_customization_field_specific_price',
        'fields' => [
            'id_ndk_customization_field' => [
                'type' => self::TYPE_INT,
                'required' => false,
            ],
            'id_ndk_customization_field_value' => [
                'type' => self::TYPE_INT,
                'required' => false,
            ],
            'reduction' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'required' => false,
            ],
            'reduction_type' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
            ],
            'from_quantity' => ['type' => self::TYPE_INT, 'required' => false],
        ],
    ];

    public static function getSpecificPrices(
        $id_field,
        $id_value = 0,
        $quantity = 0,
        $with_taxes = 0,
        $id_product = 0,
        $all = false
    ) {
        $where_qtty = ' ORDER BY sp.from_quantity ';
        if ((int) $quantity > 0) {
            $where_qtty =
                ' AND sp.`from_quantity` <= '.
                (int) $quantity.
                ' ORDER BY sp.from_quantity desc';
        }

        $sql =
            '
				SELECT *
				FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field_specific_price` sp
				WHERE sp.`id_ndk_customization_field` = '.
            (int) $id_field.
            (!$all ? ' AND sp.`id_ndk_customization_field_value` = '.(int) $id_value : '')
             .$where_qtty;
        $result = Db::getInstance()->executeS($sql);

        if (sizeof($result) > 0) {
            if ($with_taxes) {
                $context = Context::getContext();
                $customer_group = $context->customer->getGroups();
                $customer_group[] = 0;
                $id_address = (int) Context::getContext()->cart
                    ->id_address_invoice;
                $address = Address::initialize($id_address, true);
                $tax_manager = TaxManagerFactory::getManager(
                    $address,
                    Product::getIdTaxRulesGroupByIdProduct(
                        (int) $id_product,
                        Context::getContext()
                    )
                );
                $product_tax_calculator = $tax_manager->getTaxCalculator();
                $usetax = Group::getPriceDisplayMethod(
                    Group::getPriceDisplayMethod(
                        Context::getContext()->customer->id_default_group
                    )
                );
                $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;
                $i = 0;
                foreach ($result as $row) {
                    if ('amount' == $row['reduction_type'] && $usetax) {
                        $result[$i][
                            'reduction'
                        ] = $product_tax_calculator->addTaxes(
                            $row['reduction']
                        );
                    }
                    ++$i;
                }
            }

            return $result;
        } else {
            return false;
        }
    }

    public static function getSpecificPricesNamed(
        $id_field,
        $id_value = 0,
        $quantity = 0,
        $id_product = 0
    ) {
        $where_qtty = ' ORDER BY sp.from_quantity ';
        if ((int) $quantity > 0) {
            $where_qtty =
                ' AND sp.`from_quantity` <= '.
                (int) $quantity.
                ' ORDER BY sp.from_quantity desc';
        }

        $sql =
            '
				SELECT *, vl.value 
				FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field_specific_price` sp 
										LEFT JOIN '.
            _DB_PREFIX_.
            'ndk_customization_field_value_lang vl ON (vl.id_ndk_customization_field_value = sp.id_ndk_customization_field_value AND vl.id_lang = '.
            (int) Context::getContext()->language->id.
            ') 
				WHERE sp.`id_ndk_customization_field` = '.
            (int) $id_field.
            ($id_value > 0
                ? ' AND sp.`id_ndk_customization_field_value` = '.
                    (int) $id_value
                : '').
            $where_qtty;
        $result = Db::getInstance()->executeS($sql);

        if (sizeof($result) > 0) {
            $context = Context::getContext();
            $customer_group = $context->customer->getGroups();
            $customer_group[] = 0;
            $id_address = (int) Context::getContext()->cart->id_address_invoice;
            $address = Address::initialize($id_address, true);
            $tax_manager = TaxManagerFactory::getManager(
                $address,
                Product::getIdTaxRulesGroupByIdProduct(
                    (int) $id_product,
                    Context::getContext()
                )
            );
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $usetax = Group::getPriceDisplayMethod(
                Group::getPriceDisplayMethod(
                    Context::getContext()->customer->id_default_group
                )
            );
            $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;
            $i = 0;
            foreach ($result as $row) {
                if ('amount' == $row['reduction_type'] && $usetax) {
                    $result[$i][
                        'reduction'
                    ] = $product_tax_calculator->addTaxes($row['reduction']);
                }
                ++$i;
            }

            return $result;
        } else {
            return false;
        }
    }

    public static function getIdByPrimary(
        $id_ndk_customization_field_value = 0,
        $from_quantity = 0
    ) {
        if (
            0 == (int) $id_ndk_customization_field_value ||
            0 == $from_quantity
        ) {
            return 0;
        }

        $id_lang = Context::getContext()->language->id;
        $fields = Db::getInstance()->getValue(
            '
				SELECT `id_ndk_customization_field_specific_price` as id
				FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field_specific_price`
				WHERE id_ndk_customization_field_value = '.
                (int) $id_ndk_customization_field_value.
                ' AND from_quantity = '.
                (int) $from_quantity
        );

        return $fields;
    }

    public static function getPriceDiscounted($old_price = 0, $group = 0, $value = 0, $quantity = 0, $id_product = 0, $qtty_total = 0)
    {
        $specificPrices = NdkCfSpecificPrice::getSpecificPrices(
            (int) $group,
            (int) $value,
            (int) $quantity,
            true,
            (int) $id_product
        );
        //dump($specificPrices);
        if (!$specificPrices && $qtty_total > 0) {
            $specificPrices = NdkCfSpecificPrice::getSpecificPrices(
                (int) $group,
                0,
                (int) $qtty_total,
                true,
                0
            );
        }
        if (
            $specificPrices &&
            sizeof($specificPrices) > 0
        ) {
            if (
                (float) $specificPrices[0][
                    'reduction'
                ] > 0
            ) {
                if (
                    'amount' == $specificPrices[0][
                        'reduction_type'
                    ]
                ) {
                    $old_price =
                        $old_price -
                        (float) $specificPrices[0][
                            'reduction'
                        ];
                } else {
                    $old_price =
                        $old_price -
                        $old_price *
                            ((float) $specificPrices[0][
                                'reduction'
                            ] /
                                100);
                }
            }
        }

        return $old_price;
    }
}
