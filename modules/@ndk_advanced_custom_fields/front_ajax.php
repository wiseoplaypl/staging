<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */
include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/../../init.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCf.php';
require_once _PS_MODULE_DIR_.
    'ndk_advanced_custom_fields/models/ndkCfValues.php';
require_once _PS_MODULE_DIR_.
    'ndk_advanced_custom_fields/models/ndkCfSpecificPrice.php';
require_once _PS_MODULE_DIR_.
    'ndk_advanced_custom_fields/models/ndkCfConfig.php';

$context = Context::getContext();
$force_tax = false;
if (Tools::getValue('force_taxe_rule_group')) {
    $id_tax_rule_group = (int) Tools::getValue('force_taxe_rule_group');
    $force_tax = true;
    $old_tax_rule_group = Product::getIdTaxRulesGroupByIdProduct(
        (int) Tools::getValue('id_product'),
        Context::getContext()
    );
} elseif (Tools::getValue('id_product')) {
    $id_tax_rule_group = Product::getIdTaxRulesGroupByIdProduct(
        (int) Tools::getValue('id_product'),
        Context::getContext()
    );
}

$link = new Link();

function setNewTaxRuleGroup($price, $old_id, $new_id)
{
    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);
    $old_tax_manager = TaxManagerFactory::getManager(
        $address,
        $old_id
    );
    $new_tax_manager = TaxManagerFactory::getManager(
        $address,
        $new_id
    );
    $old_ratio = (1 + $old_tax_manager->getTaxCalculator()->getTotalRate() / 100);
    $new_ratio = (1 + $new_tax_manager->getTaxCalculator()->getTotalRate() / 100);
    $converted_ratio = $new_ratio / $old_ratio;

    return $price * $converted_ratio;
}

function checkEnvironment()
{
    $cookie = new Cookie(
        'psAdmin',
        '',
        (int) Configuration::get('PS_COOKIE_LIFETIME_BO')
    );

    return isset($cookie->id_employee) &&
        isset($cookie->passwd) &&
        Employee::checkPassword($cookie->id_employee, $cookie->passwd);
}

if ((float) _PS_VERSION_ > 1.6) {
    if (
        Tools::getValue('action') &&
        'formatPrice' == Tools::getValue('action')
    ) {
        $myPrice = Tools::getValue('price');
        if ($force_tax) {
            $myPrice = setNewTaxRuleGroup($myPrice, $old_tax_rule_group, $id_tax_rule_group);
        }
        $price = formatNdk($myPrice);
        echo $price;
    }

    if (
        Tools::getValue('action') &&
        'getCombination' == Tools::getValue('action')
    ) {
        if (Tools::getValue('group')) {
            $context = Context::getContext();

            $id_address = (int) Context::getContext()->cart->id_address_invoice;
            $address = Address::initialize($id_address, true);
            $tax_manager = TaxManagerFactory::getManager(
                $address,
                $id_tax_rule_group
            );
            $product_tax_calculator = $tax_manager->getTaxCalculator();
            $usetax = Group::getPriceDisplayMethod(
                Group::getPriceDisplayMethod(
                    Context::getContext()->customer->id_default_group
                )
            );
            $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

            $data = [];
            $data[
                'id_product_attribute'
            ] = (int) Product::getIdProductAttributesByIdAttributes(
                (int) Tools::getValue('id_product'),
                Tools::getValue('group')
            );

            if (0 == (int) $data['id_product_attribute']) {
                $id_product_attribute = null;
            } else {
                $id_product_attribute = $data['id_product_attribute'];
            }

            $data['price'] = Product::getPriceStatic(
                (int) Tools::getValue('id_product'),
                $usetax,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                (int) Tools::getValue('quantity'),
                false,
                (int) $context->customer->id,
                (int) $context->cart->id
            );
            $product = new Product((int) Tools::getValue('id_product'));
            $images = Ndkcf::getAttributeImagesAssociations(
                $id_product_attribute,
                (int) Tools::getValue('id_product')
            );
            $data['images'] = [];
            if ($images) {
                foreach ($images as $image) {
                    $data['images'][] =
                        (1 == Configuration::get('PS_SSL_ENABLED') &&
                        1 == Configuration::get('PS_SSL_ENABLED_EVERYWHERE')
                            ? 'https://'
                            : 'http://').
                        $link->getImageLink(
                            $product->link_rewrite[
                                Context::getContext()->language->id
                            ],
                            $image,
                            Configuration::get('NDK_IMAGE_LARGE_SIZE')
                        );
                }
            }
            //$data['stock'] = (int)StockAvailable::getQuantityAvailableByProduct((int)Tools::getValue('id_product'), (int)$id_product_attribute);
            $data['stock'] = (int) Product::getQuantity(
                (int) Tools::getValue('id_product'),
                (int) $id_product_attribute,
                null,
                $context->cart
            );

            $combName =
                $product->name[(int) Context::getContext()->language->id];

            $combNames = $product->getAttributesResume(
                Context::getContext()->language->id
            );
            foreach ($combNames as $row) {
                if (
                    $row['id_product_attribute'] == (int) $id_product_attribute
                ) {
                    $combName .= ' '.$row['attribute_designation'];
                }
            }
            $data['product_name'] = $combName;
            //echo (int)Product::getIdProductAttributesByIdAttributes((int)Tools::getValue('id_product'), Tools::getValue('group'));
            echo json_encode($data);
        }
    }
}
if (
    Tools::getValue('action') &&
    'removePriceTaxes' == Tools::getValue('action')
) {
    $context = Context::getContext();

    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);
    $tax_manager = TaxManagerFactory::getManager(
        $address,
        $id_tax_rule_group
    );
    $product_tax_calculator = $tax_manager->getTaxCalculator();
    $price_without_taxes = $product_tax_calculator->removeTaxes(
        Tools::getValue('price')
    );
    echo $price_without_taxes;
}

if (
    Tools::getValue('action') &&
    'getAttributePrice' == Tools::getValue('action')
) {
    $context = Context::getContext();

    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);
    $tax_manager = TaxManagerFactory::getManager(
        $address,
        $id_tax_rule_group
    );
    $product_tax_calculator = $tax_manager->getTaxCalculator();
    $usetax = Group::getPriceDisplayMethod(
        Group::getPriceDisplayMethod(
            Context::getContext()->customer->id_default_group
        )
    );
    $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

    if (0 == (int) Tools::getValue('id_product_attribute')) {
        $id_product_attribute = null;
    } else {
        $id_product_attribute = (int) Tools::getValue('id_product_attribute');
    }

    //echo Product::getPriceStatic((int)Tools::getValue('id_product'), true,(int)Tools::getValue('id_product_attribute'), 2);

    $result['old_price'] = Product::getPriceStatic(
        (int) Tools::getValue('id_product'),
        $usetax,
        $id_product_attribute,
        6,
        null,
        false,
        false,
        (int) Tools::getValue('quantity'),
        false,
        (int) $context->customer->id,
        (int) $context->cart->id
    );

    $result_price = Product::getPriceStatic(
        (int) Tools::getValue('id_product'),
        $usetax,
        $id_product_attribute,
        6,
        null,
        false,
        true,
        (int) Tools::getValue('quantity'),
        false,
        (int) $context->customer->id,
        (int) $context->cart->id
    );

    $result_price = NdkCfSpecificPrice::getPriceDiscounted(
        ((float) Tools::getValue('override_price') > 0 ? (float) Tools::getValue('override_price') : $result_price),
        (int) Tools::getValue('group'),
        (int) Tools::getValue('id_value'),
        (int) Tools::getValue('quantity'),
        (int) Tools::getValue('id_product'),
        (int) Tools::getValue('qtty_total')
    );

    $result['price'] = $result_price;

    $result['weight'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
        '
        SELECT product_attribute_shop.`weight`
        FROM `'.
            _DB_PREFIX_.
            'product_attribute` pa
        '.
            Shop::addSqlAssociation('product_attribute', 'pa').
            '
        WHERE pa.`id_product_attribute` = '.
            (int) $id_product_attribute
    );

    $p_oos = StockAvailable::outOfStock((int) Tools::getValue('id_product'));
    $result['oos'] = Product::isAvailableWhenOutOfStock($p_oos);
    $result['stock'] = StockAvailable::getQuantityAvailableByProduct(
        (int) Tools::getValue('id_product'),
        (int) $id_product_attribute
    );

    echo json_encode($result);
}

if (
    Tools::getValue('action') &&
    'getAttributeImg' == Tools::getValue('action')
) {
    if (0 == (int) Tools::getValue('id_product_attribute')) {
        $id_product_attribute = null;
    } else {
        $id_product_attribute = (int) Tools::getValue('id_product_attribute');
    }

    $id_image = Ndkcf::getAttributeImageAssociations(
        $id_product_attribute,
        (int) Tools::getValue('id_product')
    );
    echo(1 == Configuration::get('PS_SSL_ENABLED') &&
    1 == Configuration::get('PS_SSL_ENABLED_EVERYWHERE')
        ? 'https://'
        : 'http://').
        $link->getImageLink(
            Tools::getValue('link_rewrite'),
            $id_image,
            Configuration::get('NDK_IMAGE_LARGE_SIZE')
        );
    //var_dump($id_image);
}

if (
    Tools::getValue('action') &&
    'getSpecificPrice' == Tools::getValue('action')
) {
    $context = Context::getContext();
    $customer_group = $context->customer->getGroups();
    $customer_group[] = 0;
    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);
    $tax_manager = TaxManagerFactory::getManager(
        $address,
        $id_tax_rule_group
    );
    $product_tax_calculator = $tax_manager->getTaxCalculator();
    $usetax = Group::getPriceDisplayMethod(
        Group::getPriceDisplayMethod(
            Context::getContext()->customer->id_default_group
        )
    );
    $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

    if (0 == (int) Tools::getValue('id_product_attribute')) {
        $id_product_attribute = false;
    } else {
        $id_product_attribute = (int) Tools::getValue('id_product_attribute');
    }

    $reductions = false;

    if (
        sizeof(
            SpecificPrice::getByProductId(
                (int) Tools::getValue('id_product'),
                false,
                false
            )
        ) > 0
    ) {
        $reductions = SpecificPrice::getByProductId(
            (int) Tools::getValue('id_product'),
            false,
            false
        );
        //echo json_encode($reductions);
    }

    //$old_price = Product::getPriceStatic((int)Tools::getValue('id_product'), $usetax,$id_product_attribute, 6, null, false, false, (int)1, false, (int)$context->customer->id, (int)$context->cart->id);
    //$old_price = Product::getPriceStatic((int)Tools::getValue('id_product'), $usetax,$id_product_attribute, 6, null, false, false, (int)1, false);
    $old_price = NdkCf::getBrutPrice(
        (int) Tools::getValue('id_product'),
        (int) Tools::getValue('ndkcf_id_combination'),
        $usetax,
        false
    );
    $last_qtty = -1;
    $now = date('Y-m-d H:i:00');
    $last_reduc = 0;
    if ($reductions) {
        foreach ($reductions as $key => $value) {
            $from = $value['from'];
            if ('0000-00-00 00:00:00' == $value['to']) {
                $to = '2100-00-00 00:00:00';
            } else {
                $to = $value['to'];
            }

            //var_dump(in_array((int)$value['id_group'], $customer_group));

            if (
                (int) $value['from_quantity'] <=
                    (int) Tools::getValue('quantity') &&
                (int) $value['from_quantity'] > $last_qtty &&
                ($now >= $from && $now <= $to) &&
                in_array((int) $value['id_group'], $customer_group) &&
                ($value['reduction'] > $last_reduc ||
                    ($value['price'] > 0 &&
                        $value['price'] < $old_price - $last_reduc)) &&
                ($value['id_product_attribute'] == $id_product_attribute ||
                    0 == $value['id_product_attribute']) &&
                ($value['id_shop'] == $context->shop->id ||
                    0 == $value['id_shop']) &&
                ($value['id_currency'] == $context->currency->id ||
                    0 == $value['id_currency'])
            ) {
                //var_dump($value['price']);
                $last_qtty = $value['from_quantity'];
                if (
                    (int) $value['from_quantity'] <=
                    (int) Tools::getValue('quantity')
                ) {
                    $reduction = $value;
                }
                if ($value['price'] > 0) {
                    if ($usetax) {
                        $value['price'] = $product_tax_calculator->addTaxes(
                            $value['price']
                        );
                    }

                    $reduc = $old_price - $value['price'];

                    $reduction['reduction'] = $reduc;
                    $last_reduc = $reduc;
                } else {
                    $last_reduc = $value['reduction'];
                }
            }
        }
    }
    if (isset($reduction)) {
        if (
            'amount' == $reduction['reduction_type'] &&
            0 == $reduction['reduction_tax'] &&
            $usetax
        ) {
            $reduction['reduction'] = $product_tax_calculator->addTaxes(
                $reduction['reduction']
            );
        }
    }
    //$reduction['public_price'] = Product::getPriceStatic((int)Tools::getValue('id_product'), $usetax,$id_product_attribute, 6, null, false, true, (int)1, false, (int)$context->customer->id, (int)$context->cart->id);
    $reduction['public_price'] = Product::getPriceStatic(
        (int) Tools::getValue('id_product'),
        $usetax,
        $id_product_attribute,
        6,
        null,
        false,
        true,
        (int) Tools::getValue('quantity'),
        false,
        (int) $context->customer->id,
        (int) $context->cart->id
    );
    $reduction['old_price'] = $old_price;

    echo json_encode($reduction);
}

if (
    Tools::getValue('id_value') &&
    Tools::getValue('id_value') > 0 &&
    Tools::getValue('action') &&
    'getRestrictions' == Tools::getValue('action')
) {
    $val = new ndkCfValues(
        (int) Tools::getValue('id_value'),
        Context::getContext()->language->id
    );

    $result = [];
    if ('' != $val->influences_restrictions) {
        $values = explode(',', $val->influences_restrictions);

        $result['restrictions'] = [];
        foreach ($values as $value) {
            if ($value[0].$value[1].$value[2] == 'all') {
                $result['restrictions'][] =
                    explode('-', $value)[1].'|all|all';
            } else {
                $v = new ndkCfValues(
                    (int) $value,
                    Context::getContext()->language->id
                );
                $result['restrictions'][] =
                    $v->id_ndk_customization_field.
                    '|'.
                    $value.
                    '|'.
                    $v->value;
            }
        }
    }

    if ('' != $val->influences_obligations) {
        $values = explode(',', $val->influences_obligations);
        $result['obligations'] = [];
        foreach ($values as $value) {
            if ($value[0].$value[1].$value[2] == 'all') {
                $result['obligations'][] = explode('-', $value)[1].'|all|all';
            } else {
                $v = new ndkCfValues(
                    (int) $value,
                    Context::getContext()->language->id
                );
                $result['obligations'][] =
                    $v->id_ndk_customization_field.
                    '|'.
                    $value.
                    '|'.
                    $v->value;
            }
        }
    }

    echo Tools::jsonEncode($result);
}

if (Tools::getValue('action') && 'getRangePrice' == Tools::getValue('action')) {
    $item_price = NdkCf::getDimensionPrice(
        (int) Tools::getValue('group'),
        Tools::getValue('width'),
        Tools::getValue('height')
    );
    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);
    $tax_manager = TaxManagerFactory::getManager(
        $address,
        $id_tax_rule_group
    );
    $product_tax_calculator = $tax_manager->getTaxCalculator();
    $usetax = Group::getPriceDisplayMethod(
        Group::getPriceDisplayMethod(
            Context::getContext()->customer->id_default_group
        )
    );
    $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

    if (0 == Product::$_taxCalculationMethod) {
        $usetax = true;
    } else {
        $usetax = false;
    }

    if ($usetax) {
        $item_price = $product_tax_calculator->addTaxes($item_price);
    }

    echo $item_price;
}

if (Tools::getValue('action') && 'getMinHeight' == Tools::getValue('action')) {
    $sql =
        'SELECT MIN(height + 0.0) as min FROM '.
        _DB_PREFIX_.
        'ndk_customization_field_csv WHERE height !="" AND id_ndk_customization_field = '.
        (int) Tools::getValue('group').
        ' AND width ='.
        (float) Tools::getValue('width');
    $min = Db::getInstance()->getValue($sql);
    //var_dump($sql);
    echo $min;
}

if (
    Tools::getValue('action') &&
    'getPricesDiscount' == Tools::getValue('action')
) {
    $context = Context::getContext();

    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);
    $tax_manager = TaxManagerFactory::getManager(
        $address,
        $id_tax_rule_group
    );
    $product_tax_calculator = $tax_manager->getTaxCalculator();
    $usetax = Group::getPriceDisplayMethod(
        Group::getPriceDisplayMethod(
            Context::getContext()->customer->id_default_group
        )
    );
    $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

    $prices = NdkCf::getCustomizationPrice(
        Tools::getValue('group'),
        Tools::getValue('value'),
        Tools::getValue('id_product')
    );
    $cprice = 0;
    $priced = false;
    $value = Tools::getValue('value');

    for ($j = 0; $j < sizeof($prices); ++$j) {
        if (empty($value) || '' == $value) {
            if (!$priced) {
                $price = $prices[$j];
                $cprice = 0;
                $priced = true;
            }
        } elseif (
            $prices[$j]['valuePrice'] &&
            $prices[$j]['valuePrice'] > 0 &&
            $prices[$j]['value'] &&
            $prices[$j]['value'] == $value
        ) {
            if (!$priced) {
                $price = $prices[$j];

                if ($prices[$j]['valuePrice'] > 0) {
                    $cprice = $prices[$j]['valuePrice'];
                    //on recupère les discount pour la valeur
                    $cprice = NdkCfSpecificPrice::getPriceDiscounted(
                        $cprice,
                        (int) Tools::getValue('group'),
                        $prices[$j]['id_ndk_customization_field_value'],
                        (int) Tools::getValue('quantity'),
                        (int) Tools::getValue('id_product')
                    );
                } elseif ($prices[$j]['price'] > 0) {
                    $cprice = $prices[$j]['price'];
                    //on recupère les discount pour le champs
                    $cprice = NdkCfSpecificPrice::getPriceDiscounted(
                        $cprice,
                        (int) Tools::getValue('group'),
                        0,
                        0
                    );
                } else {
                    $cprice = 0;
                }
                $priced = true;
            }
        } elseif (
            $prices[$j]['valuePrice'] <= 0 &&
            $prices[$j]['price_per_caracter'] <= 0
        ) {
            if (!$priced) {
                $price = $prices[$j];
                if ($prices[$j]['price'] > 0) {
                    $cprice = $prices[$j]['price'];
                } else {
                    $cprice = 0;
                }
                $priced = true;
            }
        } else {
            if (
                isset($prices[$j]['type']) &&
                (0 == $prices[$j]['type'] ||
                    13 == $prices[$j]['type'] ||
                    14 == $prices[$j]['type'] ||
                    6 == $prices[$j]['type'])
            ) {
                $value = str_replace('¶', '', $value);
                $valable_string = explode('[', str_replace(' ', '', $value));
            }

            if (!$prices[$j]['valuePrice']) {
                if (isset($valable_string)) {
                    if (!$priced && '' != $valable_string[0]) {
                        $price = $prices[$j];
                        if ($prices[$j]['price_per_caracter'] > 0) {
                            $valable_string = explode(
                                '[',
                                str_replace(' ', '', $value)
                            );

                            $cprice =
                                $prices[$j]['price_per_caracter'] *
                                Tools::strlen($valable_string[0]);
                        } else {
                            $cprice = $prices[$j]['price'];
                        }
                        $priced = true;
                    }
                }
            }
        }

        if ('percent' == $prices[$j]['price_type']) {
            $product_tax_calculator->removeTaxes($cprice);
        }
        //$percent_price[$prices[$j]['id_ndk_customization_field']] = $cprice;
    }

    echo json_encode($cprice);
}

if (
    Tools::getValue('action') &&
    'getAllPricesDiscount' == Tools::getValue('action')
) {
    $context = Context::getContext();

    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);
    $tax_manager = TaxManagerFactory::getManager(
        $address,
        $id_tax_rule_group
    );
    $product_tax_calculator = $tax_manager->getTaxCalculator();
    $usetax = Group::getPriceDisplayMethod(
        Group::getPriceDisplayMethod(
            Context::getContext()->customer->id_default_group
        )
    );
    $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;
    $return = [];
    $i = 0;
    foreach (Tools::getValue('group') as $key => $value) {
        $group = $key;
        if ((int) $group > 0 && '' != $value) {
            $prices = NdkCf::getCustomizationPrice(
                $group,
                $value,
                Tools::getValue('id_product')
            );
            $cprice = 0;
            $priced = false;
            //$value = Tools::getValue('value');

            for ($j = 0; $j < sizeof($prices); ++$j) {
                if (empty($value) || '' == $value) {
                    if (!$priced) {
                        $price = $prices[$j];
                        $cprice = 0;
                        $priced = true;
                    }
                } elseif (
                    $prices[$j]['valuePrice'] &&
                    $prices[$j]['valuePrice'] > 0 &&
                    $prices[$j]['value'] &&
                    $prices[$j]['value'] == $value
                ) {
                    if (!$priced) {
                        $price = $prices[$j];

                        if ($prices[$j]['valuePrice'] > 0) {
                            $cprice = $prices[$j]['valuePrice'];
                            //on recupère les discount pour la valeur
                            $cprice = NdkCfSpecificPrice::getPriceDiscounted(
                                $cprice,
                                (int) $group,
                                $prices[$j]['id_ndk_customization_field_value'],
                                (int) Tools::getValue('quantity')
                            );
                        } elseif ($prices[$j]['price'] > 0) {
                            $cprice = $prices[$j]['price'];
                            //on recupère les discount pour le champs
                            $cprice = NdkCfSpecificPrice::getPriceDiscounted(
                                $cprice,
                                (int) $group,
                                0,
                                0
                            );
                        } else {
                            $cprice = 0;
                        }
                        $priced = true;
                    }
                } elseif (
                    $prices[$j]['valuePrice'] <= 0 &&
                    $prices[$j]['price_per_caracter'] <= 0
                ) {
                    if (!$priced) {
                        $price = $prices[$j];
                        if ($prices[$j]['price'] > 0) {
                            $cprice = $prices[$j]['price'];
                        } else {
                            $cprice = 0;
                        }
                        $priced = true;
                    }
                } else {
                    if (
                        isset($prices[$j]['type']) &&
                        (0 == $prices[$j]['type'] ||
                            13 == $prices[$j]['type'] ||
                            14 == $prices[$j]['type'] ||
                            6 == $prices[$j]['type'])
                    ) {
                        $value = str_replace('¶', '', $value);
                        $valable_string = explode(
                            '[',
                            str_replace(' ', '', $value)
                        );
                    }

                    if (!$prices[$j]['valuePrice']) {
                        if (isset($valable_string)) {
                            if (!$priced && '' != $valable_string[0]) {
                                $price = $prices[$j];
                                if ($prices[$j]['price_per_caracter'] > 0) {
                                    $valable_string = explode(
                                        '[',
                                        str_replace(' ', '', $value)
                                    );

                                    $cprice =
                                        $prices[$j]['price_per_caracter'] *
                                        Tools::strlen($valable_string[0]);
                                } else {
                                    $cprice = $prices[$j]['price'];
                                }
                                $priced = true;
                            }
                        }
                    }
                }

                if ('percent' == $prices[$j]['price_type']) {
                    $product_tax_calculator->removeTaxes($cprice);
                }

                $return[$group] = $cprice;
            }
        }
        ++$i;
    }

    echo json_encode($return);
}

if ('getConfImage' == Tools::getValue('action')) {
    $conf = new NdkCfConfig((int) Tools::getValue('id_conf'));
    echo $conf->cover;
}

if ('getSubValues' == Tools::getValue('action')) {
    $ndkAcf = Module::getInstanceByName('ndk_advanced_custom_fields');
    echo $ndkAcf->ajaxCall();
}
if ('setZoneAjax' == Tools::getValue('action')) {
    if (checkEnvironment()) {
        $field = new NdkCf((int) Tools::getValue('group'));
        $field->x_axis = (float) Tools::getValue('left');
        $field->y_axis = (float) Tools::getValue('top');
        $field->zone_width = (float) Tools::getValue('width');
        $field->zone_height = (float) Tools::getValue('height');
        $field->save();
    }
}

function convertAmountNdk($price)
{
    return (float) Tools::convertPrice($price);
}

function formatNdk($price)
{
    return Tools::displayPrice($price);
}
