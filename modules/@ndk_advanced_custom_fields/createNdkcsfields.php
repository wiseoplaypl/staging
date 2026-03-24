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
require_once _PS_MODULE_DIR_.
  'ndk_advanced_custom_fields/ndk_advanced_custom_fields.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCf.php';
require_once _PS_MODULE_DIR_.
  'ndk_advanced_custom_fields/models/ndkCfValues.php';
require_once _PS_MODULE_DIR_.
  'ndk_advanced_custom_fields/models/ndkCfRecipients.php';
require_once _PS_MODULE_DIR_.
  'ndk_advanced_custom_fields/models/ndkCfSpecificPrice.php';
require_once _PS_MODULE_DIR_.
  'ndk_advanced_custom_fields/models/ndkProdCreator.php';

if (sizeof(Tools::getValue('ndkcsfield')) < 1) {
  $return['id_product'] = (int) Tools::getValue('id_product');
  $return['id_cart'] = (int) $context->cart->id;
  $return['id_customization'] = 0;
  echo Tools::jsonEncode($return);
  exit();
}
$json_datas = [];
$json_datas['ndkcf'] = [];
$ndkPc = new ndkProdCreator();
$module = new ndk_advanced_custom_fields();
$return = [];
$context = Context::getContext();
$default_currency = new Currency(
  (int) Configuration::get('PS_CURRENCY_DEFAULT')
);
$user_currency = $context->currency;

$disabe_product_price = false;
$ndkcf_itself = false;

$languages = Language::getLanguages();
$id_lang = Context::getContext()->language->id;
$product = new Product((int) Tools::getValue('id_product'), (int) $id_lang);
$json_datas['id_product_original'] = $product->id;
$json_datas['reference_original'] = $product->reference;
$wholesale_price = $product->wholesale_price;
$real_pprice = $product->base_price;
$empty_form = true;
$is_recipient = false;
$newWeight = 0;
$packitemlist = [];
$custom_reference = [];
/*$cookieRealPrice = new Cookie('ndkRealPrice_'.(int)Tools::getValue('id_product'));
$cookieRealPrice->price = $real_pprice;
if (isset($cookieRealPrice)) {
  if (isset($cookieRealPrice->price)){
    $real_pprice = $cookieRealPrice->price;
  }
  else {
     $cookieRealPrice->price = $real_pprice;
  }
}*/

//$product->customizable = 1;
//$product->price = $real_pprice;
//$product->setFieldsToUpdate(array('customizable' => 1));
//$product->update();
Db::getInstance()->execute(
  'UPDATE `'.
    _DB_PREFIX_.
    'product` SET customizable = 1 WHERE id_product = '.
    (int) $product->id
);
Db::getInstance()->execute(
  'UPDATE `'.
    _DB_PREFIX_.
    'product_shop` SET customizable = 1 WHERE id_product = '.
    (int) $product->id
);
//Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET minimal_quantity = 0 WHERE id_product = '.(int)$product->id);
//Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_shop` SET minimal_quantity = 0 WHERE id_product = '.(int)$product->id);

if ((int) Tools::getValue('old_id_customization') > 0) {
  $customisation = new Customization(
    (int) Tools::getValue('old_id_customization')
  );
  $customProd = new Product((int) $customisation->id_product);
  //var_dump($customisation);

  if ($customProd->id != Tools::getValue('id_product')) {
    //$context->cart->updateQty((int)Tools::getValue('qty'), (int)$customProd->id, 0, (int)Tools::getValue('old_id_customization'), 'down');

    if ((int) Tools::getValue('ndkcf_id_combination') > 0) {
      $combNames = $product->getAttributesResume($id_lang);
      foreach ($combNames as $row) {
        if (
          $row['id_product_attribute'] ==
          (int) Tools::getValue('ndkcf_id_combination')
        ) {
          $combName = $row['attribute_designation'];
        }
      }
    } else {
      $combName = false;
    }
    $combName = false;

    foreach ($languages as $lang) {
      $customProd->name[$lang['id_lang']] = Tools::truncateString(
        $module->customized_text.
          ' '.
          $product->name[$id_lang].
          (isset($combName) && '' != $combName
            ? ' - '.$combName
            : ''),
        125
      );
      $customProd->link_rewrite[$lang['id_lang']] = Tools::str2url(
        Tools::truncateString(
          $product->name[$id_lang].
            (isset($combName) && '' != $combName
              ? ' - '.$combName
              : ''),
          100
        ).time()
      );
      $customProd->description_short[$lang['id_lang']] =
        $module->customized_text.
        ' :'.
        $product->name[$id_lang].
        (isset($combName) && '' != $combName ? ' - '.$combName : '');
    }
    $customProd->save();

    $newCustomProd = $customProd->id;
    //Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'image` WHERE id_product = '.(int)$newCustomProd);
    //Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'image_shop` WHERE id_product = '.(int)$newCustomProd);
    Db::getInstance()->execute(
      'DELETE FROM `'.
        _DB_PREFIX_.
        'pack` WHERE id_product_pack = '.
        (int) $newCustomProd
    );
    Db::getInstance()->execute(
      'DELETE FROM `'.
        _DB_PREFIX_.
        'ndk_customization_field_configuration` WHERE id_ndk_customization_field_configuration = '.
        (int) Tools::getValue('old_conf')
    );
    Db::getInstance()->execute(
      'DELETE FROM `'.
        _DB_PREFIX_.
        'ndk_customization_field_configuration_lang` WHERE id_ndk_customization_field_configuration = '.
        (int) Tools::getValue('old_conf')
    );
  } else {
    // $newCustomProd = NdkCf::createProductCustom(
    //     $product,
    //     (int) Tools::getValue('ndkcf_id_combination'),
    //     0,
    //     $module->customized_text
    // );
    $newCustomProd = $product->id;
  }
  $context->cart->updateQty(
    (int) $customisation->quantity,
    (int) $customisation->id_product,
    (int) $customisation->id_product_attribute,
    (int) $customisation->id,
    'down'
  );
  $customisation->delete();
} else {
  //$newCustomProd = NdkCf::createProductCustom($product, (int)Tools::getValue('ndkcf_id_combination'), 0, $module->customized_text);
  $newCustomProd = $product->id;
}

$id_address = (int) Context::getContext()->cart->id_address_invoice;
$address = Address::initialize($id_address, true);
if (Tools::getValue('force_taxe_rule_group')) {
  $tax_manager = TaxManagerFactory::getManager(
    $address,
    (int) Tools::getValue('force_taxe_rule_group')
  );
} else {
  $tax_manager = TaxManagerFactory::getManager(
    $address,
    Product::getIdTaxRulesGroupByIdProduct(
      (int) Tools::getValue('id_product'),
      Context::getContext()
    )
  );
}
$main_tax_manager = TaxManagerFactory::getManager(
  $address,
  Product::getIdTaxRulesGroupByIdProduct(
    (int) $product->id,
    Context::getContext()
  )
);
$main_product_tax_calculator = $main_tax_manager->getTaxCalculator();
$product_tax_calculator = $tax_manager->getTaxCalculator();
$usetax = PS_TAX_INC == Product::$_taxCalculationMethod;

if (
  Tools::getValue('id_product') &&
  sizeof(Tools::getValue('ndkcsfield')) > 0
) {
  // If cart has not been saved, we need to do it so that customization fields can have an id_cart
  // We check that the cookie exists first to avoid ghost carts

  if (!$context->cart->id) {
    $context->cart->add();
    $context->cookie->id_cart = (int) $context->cart->id;
  }
  $images = Tools::getValue('image-url');
  //$decoded = base64_decode(str_replace('data:image/png;base64,', '', $image));
  $im = 1;
  $cartImgs = [];
  $cover_id = Image::getCover((int) Tools::getValue('id_product'));
  $baseImage = new Image((int) $cover_id['id_image']);

  $cartImgs[0] =
    _PS_PROD_IMG_DIR_.
    $baseImage->getImgPath().
    '.'.
    $baseImage->image_format;
  if (is_array($images)) {
    foreach ($images as $image) {
      $decoded = mb_convert_encoding(
        str_replace('data:image/png;base64,', '', $image),
        'UTF-8',
        'BASE64'
      );
      $name = time();
      file_put_contents(
        _PS_UPLOAD_DIR_.
          'ndkacf_'.
          $context->cart->id.
          '-'.
          $im.
          '.png',
        $decoded
      );
      $cartImgs[$im] =
        _PS_UPLOAD_DIR_.
        'ndkacf_'.
        $context->cart->id.
        '-'.
        $im.
        '.png';
      ++$im;

      //print('<img src="'.$image.'"/>');
    }
  }
  $i = 0;
  $labels_detail = [];
  $labels_image = [];
  $labels_price = [];
  $labels_index = [];
  $labels_comb = [];
  $labels_base = [];
  $labels_preview = [];
  $labels_preview_img = [];
  $labels_custom_reference = [];

  $new_desc = [];
  foreach ($languages as $language) {
    $new_desc[$language['id_lang']] = '';
  }
  //$prices_text = $product->name[$id_lang].' : '.Tools::displayPrice(Product::getPriceStatic($product->id, $usetax)).'  ' ."\n" ;
  $prices_text = '';
  foreach ($languages as $language) {
    $labels_detail[$language['id_lang']][0]['name'] = NdkCf::l('Details');
    $labels_price[$language['id_lang']][0]['name'] = Tools::getValue(
      'cusTextTotal'
    );
    $labels_index[$language['id_lang']][0]['name'] = Tools::getValue(
      'cusTextRef'
    );
    $labels_comb[$language['id_lang']][0]['name'] = Tools::getValue(
      'cusTextComb'
    );
    $labels_base[$language['id_lang']][0]['name'] = NdkCf::l(
      'Base product'
    );
    $labels_preview[$language['id_lang']][0]['name'] = Tools::getValue(
      'previewText'
    );
    $labels_preview_img[$language['id_lang']][0]['name'] = NdkCf::l(
      'Preview (image)'
    );
    $labels_custom_reference[$language['id_lang']][0]['name'] = NdkCf::l(
      'reference'
    );
  }

  $customizationPrice = 0;
  $ndkcustomvalue = [];
  $ndkPrices = Tools::getValue('prices');
  $recipientDetails = '';
  $accessoryProdQuantity = [];
  $dimensions = [];
  $percent_price = [];
  $surfaceQuantity = [];
  $encountredSurface = [];
  $orientations = [];
  //$ndkFields = NdkCf::getCustomFieldsForCreation(Tools::getValue('id_product'), $product->id_category_default);

  foreach (Tools::getValue('ndkcsfield') as $field => $value) {
    if ('orientation' == $field) {
      foreach ($value as $k => $v) {
        $orientations[$k] = $v;
      }
    }
  }
  foreach (Tools::getValue('ndkcsfield') as $field => $value) {
    /*foreach($ndkFields as $ndkField){
     $field = $ndkField['id_ndk_customization_field'];
     $value = Tools::getValue('ndkcsfield')[$ndkField['id_ndk_customization_field']];*/

    /*
      if($field == 'orientation')
        foreach($value as $k=>$v)
          $orientations[$k] = $v;
    */

    $json_datas['ndkcf'][(int) $field] = [];
    $json_datas['ndkcf'][(int) $field]['values'] = [];

    if (!empty($value) && '' != $value) {
      $values = [];
      $empty_form = false;
      //1 on crée les champs
      $labels = [];
      $required = Db::getInstance()->executeS(
        'SELECT cf.`required`
     FROM `'.
          _DB_PREFIX_.
          'ndk_customization_field`cf 
     WHERE cf.`id_ndk_customization_field` = '.
          (int) $field
      );

      foreach ($languages as $language) {
        $labels[$language['id_lang']] = Db::getInstance()->executeS(
          'SELECT '.
            (1 == Configuration::get('NDK_USE_ADMIN_NAME')
              ? 'cfl.`admin_name`'
              : 'cfl.`name`').
            ' as name 
     FROM `'.
            _DB_PREFIX_.
            'ndk_customization_field_lang`cfl 
     WHERE cfl.`id_ndk_customization_field` = '.
            (int) $field.
            ' AND cfl.`id_lang` = '.
            (int) $language['id_lang']
        );
      }

      createLabel(
        $languages,
        1,
        (int) Tools::getValue('id_product'),
        $labels,
        $required ? $required[0]['required'] : 0
      );

      $json_datas['ndkcf'][(int) $field]['field'] = $labels[
        (int) Context::getContext()->language
          ->id
      ][0]['name'];
      //$product->customizable = 1;
      //$product->update(array('customizable' =>1));
      //Db::getInstance()->update('product', array('customizable' => 1), '`id_product` = '.(int)$product->id, 0, false);

      /* on gère les quantités */
      $accessoryQuantity = [];
      $custom_value = '';
      if (is_array($value)) {
        foreach ($value as $k => $v) {
          if ('quantity' == $k) {
            //var_dump($v);
            foreach ($v as $k2 => $v2) {
              $values[] = $k2;
              $accessoryQuantity[$k2] = $v2;
            }
          } elseif ('surface' == $k) {
            //var_dump($v);
            foreach ($v as $k2 => $v2) {
              $values[] = $k2;
              $surfaceQuantity[$k2] = $v2;
            }
          } elseif ('quantityProd' == $k) {
            foreach ($v as $k2 => $v2) {
              $values[] = $k2;
              $accessoryProdQuantity[$k2]['quantity'] = $v2;
            }
          } elseif ('accessory_customization' == $k) {
            foreach ($v as $k2 => $v2) {
              if (empty($v2)) {
                unset($v[$k2]);
              }
            }

            $custom_value = 'FORMAT|'.sizeof($v).'|';
            foreach ($v as $k2 => $v2) {
              if ('' != $v2) {
                $splited = explode('|', $k2);
                $attr_name = $splited[4];
                $number = $splited[3];

                $custom_value .=
                  'JUMPLINE|'.
                  $attr_name.
                  '|'.
                  $number.
                  '|'.
                  $v2.
                  '|'.
                  $attr_name.
                  '|';
              }
            }
            //$values[$field] = $custom_value;
            $values[] = $custom_value;
          } elseif ('checkbox' == $k) {
            //var_dump($v);
            foreach ($v as $k2 => $v2) {
              $values[] = $v2;
              $accessoryQuantity[$k2] = 1;
            }
          } elseif ('width' == $k) {
            $dimensions[$field] = $value;
            $values[] = $field;
          } elseif ('recipient' == $k) {
            $is_recipient = false;
            $imp = 1;
            $imploded = '';
            $recipientInfos = $v;
            $recipientField = new NdkCf((int) $field, $id_lang);
            $recipientInfos['availability'] =
              $recipientField->validity;
            $recipientInfos['title'] = $recipientField->notice;
            $recipientInfos[
              'id_ndk_customization_field'
            ] = (int) $field;
            $ndk = new ndk_advanced_custom_fields();
            foreach ($v as $k2 => $v2) {
              if ('send_mail' == $k2) {
                if (1 == $v2) {
                  $v2 = $ndk->l('yes');
                } else {
                  $v2 = $ndk->l('no');
                }
              }

              if ('email' == $k2) {
                if ('' != $v2) {
                  $v2 = '--';
                }
                $is_recipient = true;
              }

              $imploded .=
                '<strong>'.
                $ndk->l($k2).
                ' </strong>'.
                $v2.
                ($imp < sizeof($v) ? ' </br> ' : '');
              ++$imp;
            }
            if ($is_recipient) {
              $values[] = $imploded;
            }
          }
        }
      } else {
        //var_dump($value);
        $values[] = $value;
      }

      //var_dump($values);
      //on demarra la boucle
      /*if(!$is_recipient)
       $recipientDetails .= $labels[$language['id_lang']][0]['name'].' : ';*/
      $field_total_qtty = 0;
      foreach ($values as $value) {
        foreach ($accessoryProdQuantity as $key => $v) {
          $field_total_qtty += $v['quantity'];
        }
      }
      foreach ($values as $value) {
        if (
          count($accessoryProdQuantity) > 0 &&
          isset($accessoryProdQuantity[$value])
        ) {
          //on ajoute les accessoires produits
          $line = '';
          $incart = 0;
          //$newWeight = 0;
          $pack_available_quantity = 0;
          $last_quantity_encountred = 999999999999;
          //dump($accessoryProdQuantity);

          foreach ($accessoryProdQuantity as $key => $v) {
            if ($v['quantity'] > 0 && $key == $value) {
              $id_value = explode('|', $key)[0];
              $id_product = explode('|', $key)[1];
              $id_product_attribute = explode('|', $key)[2];
              if (
                (int) $id_product ==
                (int) Tools::getValue('id_product')
              ) {
                $disabe_product_price = true;
                $ndkcf_itself = true;
              }

              $maxP = $v['quantity'];
              $prodItem = NdkCf::getProductInfos(
                (int) $id_product,
                (int) $id_product_attribute
              );
              $prodItem = $prodItem[0];

              $sql_prices =
                'SELECT f.price as fieldPrice, f.show_price, v.price as valuePrice, v.reference FROM `'.
                _DB_PREFIX_.
                'ndk_customization_field_value` v 
                        LEFT JOIN `'.
                _DB_PREFIX_.
                'ndk_customization_field` f ON f.id_ndk_customization_field = v.id_ndk_customization_field 
                        WHERE v.id_ndk_customization_field_value = '.
                (int) $id_value;

              $itemPrices = Db::getInstance()->getRow(
                $sql_prices
              );

              $show_price = Db::getInstance()->getValue(
                'SELECT f.show_price FROM `'.
                  _DB_PREFIX_.
                  'ndk_customization_field_value` v 
                        LEFT JOIN `'.
                  _DB_PREFIX_.
                  'ndk_customization_field` f ON f.id_ndk_customization_field = v.id_ndk_customization_field 
                        WHERE v.id_ndk_customization_field_value = '.
                  (int) $id_value
              );

              if (!$context) {
                $context = Context::getContext();
              }

              //$context->cart->updateQty((int)$v['quantity'], (int)$id_product, (int)$id_product_attribute, null, 'up');
              if (0 == (int) $id_product_attribute) {
                $id_product_attribute = null;
              }

              $item_quantity = (int) $v['quantity'];
              if (
                (int) Tools::getValue(
                  'totalprodquantity-'.
                    (int) $id_value.
                    '-'.
                    (int) $id_product
                ) > 0
              ) {
                $item_quantity = (int) Tools::getValue(
                  'totalprodquantity-'.
                    (int) $id_value.
                    '-'.
                    (int) $id_product
                );
              }

              $item_price = Product::getPriceStatic(
                (int) $id_product,
                $usetax,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                (int) $item_quantity,
                false,
                (int) $context->customer->id,
                (int) $context->cart->id
              );

              //dump($item_price);
              if ($itemPrices) {
                if ('' != $itemPrices['reference']) {
                  $custom_reference[] = str_replace(
                    '[:id_product]',
                    (int) $newCustomProd,
                    $itemPrices['reference']
                  );
                }

                if (0 != $itemPrices['valuePrice']) {
                  $item_price = $itemPrices['valuePrice'];
                  $dontUseTax = false;
                } elseif (0 != $itemPrices['fieldPrice']) {
                  $item_price = $itemPrices['fieldPrice'];
                  $dontUseTax = false;
                } else {
                  //$item_price = $prodItem["orderprice"];
                  //$item_price = $prodItem['price'];
                  $dontUseTax = true;
                }

                $id_address = (int) Context::getContext()->cart
                  ->id_address_invoice;
                $address = Address::initialize(
                  $id_address,
                  true
                );
                // $tax_manager = TaxManagerFactory::getManager(
                //     $address,
                //     Product::getIdTaxRulesGroupByIdProduct(
                //         (int) $product->id,
                //         Context::getContext()
                //     )
                // );
                //$product_tax_calculator = $tax_manager->getTaxCalculator();
                $usetax =
                  PS_TAX_INC ==
                  Product::$_taxCalculationMethod;

                if ($usetax && !$dontUseTax) {
                  $item_price = $product_tax_calculator->addTaxes(
                    $item_price
                  );
                }
              }

              if ($id_product_attribute > 0) {
                $p = new Product((int) $id_product);
                $accessorycombNames = $p->getAttributesResume(
                  Context::getContext()->language->id
                );
                foreach ($accessorycombNames as $comb) {
                  //var_dump($comb);
                  if (
                    $comb['id_product_attribute'] ==
                    $id_product_attribute
                  ) {
                    $accessorycombName =
                      $comb['attribute_designation'];
                  }
                }
              }

              //ajout ndkspecificprice
              $item_price = NdkCfSpecificPrice::getPriceDiscounted(
                $item_price,
                (int) $field,
                (int) $id_value,
                (int) $item_quantity,
                Tools::getValue('id_product'),
                (int) $field_total_qtty
              );
              //fin ajout
              $item_price = Tools::convertPriceFull(
                $item_price,
                $user_currency,
                $default_currency,
                6
              );
              //dump($item_price);

              if (0 != $item_price && 0 != (int) $show_price) {
                $price_details =
                  ' = '.
                  Tools::displayPrice(
                    Tools::convertPriceFull(
                      (float) ($item_price * $maxP),
                      $default_currency,
                      $user_currency,
                      6
                    )
                  ).
                  ' ';
              } else {
                $price_details = '';
              }
              if (Tools::getValue('force_taxe_rule_group')) {
                $tax_name = new TaxRulesGroup(
                  (int) Tools::getValue('force_taxe_rule_group')
                );
              } else {
                $tax_name = new TaxRulesGroup(
                Product::getIdTaxRulesGroupByIdProduct(
                  (int) $id_product,
                  Context::getContext()
                )
              );
              }

              if (0 != (int) $show_price) {
                $line .=
                  $maxP.
                  ' x '.
                  Tools::displayPrice(
                    Tools::convertPriceFull(
                      (float) $item_price,
                      $default_currency,
                      $user_currency,
                      6
                    )
                  ).
                  ' - '.
                  $prodItem['name'].
                  ' '.
                  ($id_product_attribute > 0
                    ? ' - '.$accessorycombName
                    : '').
                  ' '.
                  $price_details.
                  ($usetax
                    ? ' ('.$tax_name->name.')'
                    : '').
                  '<br/>';
              } else {
                $line .=
                  $maxP.
                  ' x  - '.
                  $prodItem['name'].
                  ' '.
                  ($id_product_attribute > 0
                    ? ' - '.$accessorycombName
                    : '').
                  '<br/>';
              }
              //dump($item_price);
              $customizationPrice +=
                (float) ($item_price * $maxP);
              if (isset($prodItem['attrWeight'])) {
                if ((float) $prodItem['attrWeight'] > 0) {
                  $newWeight +=
                    $prodItem['attrWeight'] * $maxP;
                } else {
                  $newWeight += $prodItem['weight'] * $maxP;
                }
              } else {
                $newWeight += $prodItem['weight'] * $maxP;
              }

              //var_dump($newWeight);
              $packitemlist[] = [
                'id_product' => (int) $id_product,
                'quantity' => (int) $maxP,
                'id_product_attribute' => (int) $id_product_attribute,
              ];
              //Pack::addItem((int)$newCustomProd, (int)$id_product, (int)$maxP, (int)$id_product_attribute);
              $wholesale_price +=
                $prodItem['wholesale_price'] * $maxP;
              $incart += $maxP;
              $prod_available =
                StockAvailable::getQuantityAvailableByProduct(
                  $id_product,
                  $id_product_attribute
                ) / $maxP;

              $json_datas['ndkcf'][(int) $field]['values'][] = [
                'qtty' => (int) $maxP,
                'id_product' => (int) $id_product,
                'id_product_attribute' => (int) $id_product_attribute,
                'price' => (float) $item_price,
                'ndkcf_datas' => $itemPrices,
                'reference' => $prodItem['reference'],
              ];
              //$context->cart->updateQty((int)$v['quantity'], (int)$id_product, (int)$id_product_attribute, null, 'down');

              if ($prod_available < $last_quantity_encountred) {
                $pack_available_quantity = $prod_available;
                $last_quantity_encountred = $prod_available;
              }
              createLabel(
                $languages,
                1,
                (int) Tools::getValue('id_product'),
                $labels,
                $required[0]['required']
              );
              $ndkcustomvalue[] = [
                'index' => createLabel(
                  $languages,
                  1,
                  $newCustomProd,
                  $labels,
                  $required[0]['required']
                ),
                'value' => $line,
              ];
            }
          }
        } elseif (
          count($dimensions) > 0 &&
          isset($dimensions[$value])
        ) {
          if (
            isset($dimensions[$value]['width']) &&
            isset($dimensions[$value]['height']) &&
            '' != $dimensions[$value]['width'] &&
            ' ' != $dimensions[$value]['width'] &&
            '' != $dimensions[$value]['height'] &&
            ' ' != $dimensions[$value]['height']
          ) {
            $item_price = NdkCf::getDimensionPrice(
              (int) $field,
              $dimensions[$value]['width'],
              $dimensions[$value]['height']
            );
            $id_address = (int) Context::getContext()->cart
              ->id_address_invoice;
            $address = Address::initialize($id_address, true);
            // $tax_manager = TaxManagerFactory::getManager(
            //     $address,
            //     Product::getIdTaxRulesGroupByIdProduct(
            //         (int) $product->id,
            //         Context::getContext()
            //     )
            // );
            //$product_tax_calculator = $tax_manager->getTaxCalculator();
            $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;
            if ($usetax) {
              $item_price = $product_tax_calculator->addTaxes(
                $item_price
              );
            }

            $item_price = Tools::convertPriceFull(
              $item_price,
              $user_currency,
              $default_currency,
              6
            );

            $customizationPrice += $item_price;
            $price_detail =
              ' '.
              Tools::displayPrice(
                Tools::convertPriceFull(
                  $item_price,
                  $default_currency,
                  $user_currency,
                  6
                )
              ).
              ' ';

            //var_dump($item_price);
            $line =
              $dimensions[$value]['width'].
              'x'.
              $dimensions[$value]['height'].
              $price_detail;
            createLabel(
              $languages,
              1,
              (int) Tools::getValue('id_product'),
              $labels,
              $required[0]['required']
            );
            $ndkcustomvalue[] = [
              'index' => createLabel(
                $languages,
                1,
                $newCustomProd,
                $labels,
                $required[0]['required']
              ),
              'value' => $line,
            ];
          }
        } elseif (
          (count($surfaceQuantity) > 0 &&
            isset($surfaceQuantity[$value])) ||
          in_array($field, $encountredSurface)
        ) {
          //var_dump($field);

          if (!in_array($field, $encountredSurface)) {
            //var_dump($surfaceQuantity);
            $prices = NdkCf::getCustomizationPrice(
              $field,
              $value,
              Tools::getValue('id_product')
            );
            $item_price = $prices[0]['price'];

            $line = '';
            foreach ($surfaceQuantity as $key => $value) {
              $valObj = new NdkCfValues((int) $key, $id_lang);
              $item_price = $item_price * (float) $value;
              $line .= $valObj->value.'  '.$value.' ; ';
              unset($surfaceQuantity[$key]);
            }

            $item_price = Tools::convertPriceFull(
              $item_price,
              $user_currency,
              $default_currency,
              6
            );
            $customizationPrice += $item_price;
            $price_detail =
              ' '.
              Tools::displayPrice(
                Tools::convertPriceFull(
                  $item_price,
                  $default_currency,
                  $user_currency,
                  6
                )
              ).
              ' ';

            //var_dump($item_price);

            createLabel(
              $languages,
              1,
              (int) Tools::getValue('id_product'),
              $labels,
              $required[0]['required']
            );
            $ndkcustomvalue[] = [
              'index' => createLabel(
                $languages,
                1,
                $newCustomProd,
                $labels,
                $required[0]['required']
              ),
              'value' => $line.$price_detail,
            ];
            $encountredSurface[] = $field;
          }
        } else {
          //2 on renseigne les personnalisations
          //var_dump($value);

          $formated_value = false;
          $my_multiplicator = 1;
          $value = $value;
          //var_dump(substr($value, 0, 7));
          if ('FORMAT|' == Tools::substr($value, 0, 7)) {
            $valable_string = '';
            $lines = explode('JUMPLINE', $value);
            $formated_value = '';
            $value = '';
            $l = 0;
            foreach ($lines as $line) {
              if (0 == $l) {
                $my_multiplicator = (int) str_replace(
                  'FORMAT|',
                  '',
                  $line
                );
              } else {
                $vars = explode('|', $line);
                $valable_string .= explode(
                  '[',
                  str_replace(' ', '', $vars[3])
                )[0];
                $value = $vars[3];

                $formated_value .=
                  '<p class="cus_sub col-xs-6 col-md-3"><span class="cus_sub_container"><span class="cust_title">'.
                  $vars[1].
                  ' '.
                  $vars[2].
                  '</span>'.
                  ' <br/>'.
                  $vars[3].
                  '</span></p>';
              }

              ++$l;
            }
          }

          //var_dump($valable_string);
          //var_dump($my_multiplicator);

          $prices = NdkCf::getCustomizationPrice(
            $field,
            $value,
            Tools::getValue('id_product')
          );

          //dump($prices);
          if ($i + 1 < sizeof(Tools::getValue('ndkcsfield'))) {
            $suffix = ' - '."\n";
            $virgule = '<br />';
          } else {
            $suffix = ' ';
            $virgule = '';
          }

          $cprice = 0;
          $priced = false;

          //$price = $ndkPrices;
          //$cprice = $ndkPrices[$field];
          $percent = false;
          for ($j = 0; $j < sizeof($prices); ++$j) {
            $link_multiplicator = 1;

            if ((int) $prices[$j]['quantity_link'] > 0) {
              $quantity_link = (int) $prices[$j]['quantity_link'];
              if (Tools::getValue('ndkcsfield')[$quantity_link]) {
                $link_multiplicator = 0;
                foreach (
                  Tools::getValue('ndkcsfield')[
                    $quantity_link
                  ]['quantityProd']
                  as $key => $qtty
                ) {
                  $link_multiplicator += $qtty;
                }
              }
            }

            //var_dump($prices[$j]['type']);
            //var_dump($value);

            if (empty($value) || '' == $value) {
              if (!$priced) {
                $price = $prices[$j];
                $cprice = 0;
                $priced = true;
              }
            } elseif (
              $prices[$j]['valuePrice'] &&
              0 != $prices[$j]['valuePrice'] &&
              $prices[$j]['value'] &&
              $prices[$j]['value'] == $value
            ) {
              if (!$priced) {
                if (
                  array_key_exists($value, $accessoryQuantity)
                ) {
                  if (0 == (int) $accessoryQuantity[$value]) {
                    $accessoryQuantity[$value] = 1;
                  }
                } else {
                  $accessoryQuantity[$value] = 1;
                }
                $price = $prices[$j];
                if (0 != $prices[$j]['valuePrice']) {
                  $cprice = $prices[$j]['valuePrice'];
                  //on recupère les discount pour la valeur
                  $cprice = NdkCfSpecificPrice::getPriceDiscounted(
                    $cprice,
                    (int) $prices[$j][
                      'id_ndk_customization_field'
                    ],
                    $prices[$j][
                      'id_ndk_customization_field_value'
                    ],
                    (int) Tools::getValue('qty') *
                      $accessoryQuantity[$value] *
                      $link_multiplicator,
                    Tools::getValue('id_product'),
                    (int) $field_total_qtty
                  );

                  if ('' != $price['reference']) {
                    $custom_reference[] = str_replace(
                      '[:id_product]',
                      (int) $newCustomProd,
                      $price['reference']
                    );
                  }
                } elseif (0 != $prices[$j]['price']) {
                  $cprice = $prices[$j]['price'];
                  //on recupère les discount pour le champs
                  $cprice = NdkCfSpecificPrice::getPriceDiscounted(
                    $cprice,
                    (int) $prices[$j][
                      'id_ndk_customization_field'
                    ],
                    0,
                    0,
                    Tools::getValue('id_product'),
                    (int) $field_total_qtty
                  );
                } else {
                  $cprice = 0;
                  $priced = true;

                  if ('' != $price['reference']) {
                    $custom_reference[] = str_replace(
                      '[:id_product]',
                      (int) $newCustomProd,
                      $price['reference']
                    );
                  }
                }
              }
            } elseif (
              $prices[$j]['valuePrice'] <= 0 &&
              $prices[$j]['price_per_caracter'] <= 0
            ) {
              if (!$priced) {
                $price = $prices[$j];
                if (0 != $prices[$j]['price']) {
                  $cprice = $prices[$j]['price'];
                } else {
                  $cprice = 0;
                }
                $priced = true;

                if ('' != $price['reference']) {
                  $custom_reference[] = str_replace(
                    '[:id_product]',
                    (int) $newCustomProd,
                    $price['reference']
                  );
                }
              }
            } else {
              if (
                isset($prices[$j]['type']) &&
                (0 == $prices[$j]['type'] ||
                  13 == $prices[$j]['type'] ||
                  14 == $prices[$j]['type'] ||
                  6 == $prices[$j]['type'])
              ) {
                if (!isset($valable_string)) {
                  $value = str_replace('¶', '', $value);
                  $valable_string = explode(
                    '[',
                    str_replace(
                      ["\r\n", "\n", "\r", ' '],
                      '',
                      $value
                    )
                  );
                }
              }

              if (!$prices[$j]['valuePrice']) {
                if (isset($valable_string)) {
                  if (!$priced && '' != $valable_string[0]) {
                    $price = $prices[$j];
                    if (
                      $prices[$j]['price_per_caracter'] >
                      0
                    ) {
                      $my_multiplicator = 1;
                      $valable_string = explode(
                        '[',
                        str_replace(
                          ["\r\n", "\n", "\r", ' '],
                          '',
                          $value
                        )
                      );
                      $cprice =
                        $prices[$j][
                          'price_per_caracter'
                        ] *
                        mb_strlen(
                          trim($valable_string[0])
                        );
                    } else {
                      $cprice = $prices[$j]['price'];
                    }
                    $priced = true;
                  }
                }
              }
            }

            if ('percent' == $prices[$j]['price_type']) {
              $percent_price[
                $prices[$j]['id_ndk_customization_field']
              ] = $cprice;
              $percent = true;
            }
          }
          if (0 == count($accessoryQuantity)) {
            $accessoryQuantity[$value] = 0;
          //$value ='';
          } else {
            $cprice = $cprice * $accessoryQuantity[$value];
            if (0 == $accessoryQuantity[$value]) {
              $value = '';
            }
          }

          $price_detail = '';
          if (isset($prices[$j])) {
            if ('one_time' == $prices[$j]['price_type']) {
              $cprice = $cprice / (int) Tools::getValue('qty');
            }
          }

          $cprice = $cprice * $my_multiplicator * $link_multiplicator;

          $cpriceDisplayed = $cprice;
          if ($usetax) {
            $cpriceDisplayed_ht = $main_product_tax_calculator->removeTaxes($cprice);
            $cpriceDisplayed = $product_tax_calculator->addTaxes($cpriceDisplayed_ht);
          }
          if (0 != $cprice) {
            //dump($cprice);
            if ($percent) {
              $price_detail = ' +'.$cprice.'% ';
            } else {
              $price_detail =
                ' '.
                Tools::displayPrice(
                  Tools::convertPriceFull(
                    $cpriceDisplayed,
                    $default_currency,
                    $user_currency,
                    6
                  )
                ).
                ' ';
            }

            $show_price = Db::getInstance()->getValue(
              'SELECT show_price FROM '.
                _DB_PREFIX_.
                'ndk_customization_field 
                        WHERE id_ndk_customization_field = '.
                (int) $field
            );
            if (0 != (int) $show_price) {
              $prices_text .=
                $labels[$id_lang][0]['name'].
                ' : +'.
                Tools::displayPrice(
                  Tools::convertPriceFull(
                    $cprice,
                    $default_currency,
                    $user_currency,
                    6
                  )
                ).
                $suffix;
            } else {
              $price_detail = '';
            }
          }

          if (!$percent) {
            $customizationPrice += (float) $cprice;
          }
          //dump($customizationPrice);
          //$value_image = _PS_IMG_DIR_.'scenes/'.'ndkcf/'.$prices[$j]['id_ndk_customization_field_value'].'.jpg';
          $value_image_output = '';
          /*if(file_exists($value_image))
           $value_image_output = '<img src="/img/scenes/'.'ndkcf/'.$prices[$j]['id_ndk_customization_field_value'].'.jpg'.'"/>';*/

          if (!empty($value) && '' != $value) {
            $orientation = '';
            if (isset($orientations[$field])) {
              $orientation = ' ['.$orientations[$field].']';
            }

            $ndkcustomvalue[] = [
              'index' => createLabel(
                $languages,
                1,
                $newCustomProd,
                $labels,
                $required[0]['required']
              ),
              'value' => ($formated_value ? $formated_value : $value).
                ($accessoryQuantity[$value] > 1
                  ? ' x'.$accessoryQuantity[$value]
                  : '').
                $orientation.
                ' '.
                $price_detail.
                ' '.
                $value_image_output,
            ];

            $json_datas['ndkcf'][(int) $field]['values'][] = [
              'value' => $formated_value
                ? $formated_value
                : $value,
              'qtty' => (int) $accessoryQuantity[$value],
              'id_product' => null,
              'id_product_attribute' => null,
              'price' => (float) $cprice,
            ];
            //var_dump($value);
            if (!$is_recipient) {
              $recipientDetails .=
                ($accessoryQuantity[$value] > 1
                  ? $accessoryQuantity[$value].'x '
                  : '').
                ($formated_value ? $formated_value : $value).
                $orientation.
                ' '.
                $virgule;
            }

            createLabel(
              $languages,
              1,
              (int) Tools::getValue('id_product'),
              $labels,
              $required[0]['required']
            );
            //addTextFieldToProduct(Tools::getValue('id_product'), $index_field, 1, $value);

            foreach ($languages as $language) {
              if (!empty($value)) {
                $new_desc[$language['id_lang']] .=
                  $labels[$language['id_lang']][0]['name'].
                  ' : '.
                  (isset($formated_value)
                    ? $formated_value
                    : $value).
                  ($accessoryQuantity[$value] > 1
                    ? ' x'.$accessoryQuantity[$value]
                    : '').
                  '<br/>';
              }
            }
          }
        } //else
      }

      ++$i;
    }
  }

  $context = Context::getContext();
  $cur_cart = $context->cart;
  $id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');

  $id_country = (int) $context->country->id;
  $id_state = 0;
  $zipcode = 0;
  $id_address = 0;
  $id_customer = 0;
  $id_group = null;
  if (sizeof($percent_price) > 0) {
    $tempPrice = 0;
    $customizationPricePercent = 0;
    //get product price
    $myProductPrice = Product::getPriceStatic(
      (int) Tools::getValue('id_product'),
      false,
      (int) Tools::getValue('ndkcf_id_combination'),
      6,
      null,
      false,
      false,
      1,
      false,
      (int) $context->customer->id,
      (int) $context->cart->id
    );
    $myProductPrice -= (float) $product->ecotax;
    $myProductPrice = Tools::convertPriceFull(
      $myProductPrice,
      $user_currency,
      $default_currency,
      6
    );

    foreach ($percent_price as $key => $value) {
      if ($value > 0) {
        $valueHT = $value;
        $multiplicatorHT = $valueHT / 100;
        $totalPrice =
          $myProductPrice +
          $customizationPrice +
          $customizationPricePercent;
        $toAdd = $totalPrice * $multiplicatorHT;
        $customizationPricePercent += $toAdd;
      }
    }
    $customizationPrice += $customizationPricePercent;
  }
  /*
     if(sizeof($percent_price) >0)
     {
       $tempPrice = 0;
       //get product price
      $myProductPrice = Product::getPriceStatic((int)Tools::getValue('id_product'), false,(int)Tools::getValue('ndkcf_id_combination'), 6, null, false, false, 1, false, (int)$context->customer->id, (int)$context->cart->id);
      $myProductPrice -= (float)$product->ecotax;
      $tempPrice +=Tools::convertPriceFull($myProductPrice, $user_currency, $default_currency, 6);

      foreach($percent_price as $key=>$value){
        if($value > 0)
        {
          $valueHT = $product_tax_calculator->removeTaxes($value);
          $multiplicatorHT = $valueHT/100;
          $customizationPrice += $product_tax_calculator->addTaxes($tempPrice*$multiplicatorHT);
          $tempPrice += $tempPrice*$multiplicatorHT;

        }

      }
     }
  */

  if (0 != $customizationPrice || sizeof($accessoryProdQuantity) > 0) {
    $id_address = (int) Context::getContext()->cart->id_address_invoice;
    $address = Address::initialize($id_address, true);

    $usetax = PS_TAX_INC == Product::$_taxCalculationMethod;
    $newCustomProd = NdkCf::createProductCustom(
      $product,
      (int) Tools::getValue('ndkcf_id_combination'),
      0,
      $module->customized_text
    );

    $newCustomProdObj = new Product($newCustomProd);
    if (Tools::getValue('force_taxe_rule_group')) {
      $newCustomProdObj->id_tax_rules_group = (int) Tools::getValue('force_taxe_rule_group');
    }
    if (Tools::getValue('force_carrier')) {
      $newCustomProdObj->setCarriers([(int) Tools::getValue('force_carrier')]);
    }

    //forpack

    //forpack

    if (Pack::isPack((int) $product->id)) {
      $items = Db::getInstance()->executeS(
        'SELECT id_product_item, id_product_attribute_item, quantity FROM `'.
          _DB_PREFIX_.
          'pack` where id_product_pack = '.
          (int) $product->id
      );

      foreach ($items as $item) {
        Pack::addItem(
          (int) $newCustomProdObj->id,
          (int) $item['id_product_item'],
          (int) $item['quantity'],
          (int) $item['id_product_attribute_item']
        );
        $newCustomProdObj->cache_is_pack = 1;
      }
    } else {
      if (
        1 == Configuration::get('NDK_ADD_PRODUCT_PRICE') &&
        !$disabe_product_price
      ) {
        Pack::addItem(
          (int) $newCustomProdObj->id,
          (int) $product->id,
          (int) 1,
          (int) Tools::getValue('ndkcf_id_combination')
        );
        $newCustomProdObj->cache_is_pack = 1;
      }
    }
    if (1 == Configuration::get('NDK_SPLIT_PACK')) {
      $newCustomProdObj->pack_stock_type = 0;
    } else {
      $newCustomProdObj->pack_stock_type = 1;
    }
    $newCustomProdObj->save();

    if ($usetax) {
      $newprice = $main_product_tax_calculator->removeTaxes(
        $customizationPrice
      );
    } else {
      $newprice = $customizationPrice;
    }

    if (
      1 == Configuration::get('NDK_ADD_PRODUCT_PRICE') &&
      !$disabe_product_price
    ) {
      //$myProductPrice = Product::getPriceStatic((int)Tools::getValue('id_product'), false,(int)Tools::getValue('ndkcf_id_combination'), 6, null, false, false, 1, false, (int)$context->customer->id, (int)$context->cart->id);
      $myProductPrice = NdkCf::getBrutPrice(
        (int) Tools::getValue('id_product'),
        (int) Tools::getValue('ndkcf_id_combination')
      );
      //$myProductPrice -= (float)$product->ecotax;
      $newprice += Tools::convertPriceFull(
        $myProductPrice,
        $user_currency,
        $default_currency,
        6
      );
    }

    //$newCustomProdObj = new Product($newCustomProd);

    if (sizeof($packitemlist) > 0) {
      $packProdItems = [];
      foreach ($packitemlist as $item) {
        if (
          !isset(
            $packProdItems[
              $item['id_product'].
                '-'.
                $item['id_product_attribute']
            ]
          )
        ) {
          $packProdItems[
            $item['id_product'].
              '-'.
              $item['id_product_attribute']
          ] = [];
        }
        if (
          !isset(
            $packProdItems[
              $item['id_product'].
                '-'.
                $item['id_product_attribute']
            ]['quantity']
          )
        ) {
          $packProdItems[
            $item['id_product'].
              '-'.
              $item['id_product_attribute']
          ]['quantity'] = 0;
        }

        $packProdItems[
          $item['id_product'].'-'.$item['id_product_attribute']
        ]['quantity'] += $item['quantity'];
        $packProdItems[
          $item['id_product'].'-'.$item['id_product_attribute']
        ]['id_product'] = $item['id_product'];
        $packProdItems[
          $item['id_product'].'-'.$item['id_product_attribute']
        ]['id_product_attribute'] = $item['id_product_attribute'];
      }
      foreach ($packProdItems as $item) {
        Pack::addItem(
          (int) $newCustomProd,
          (int) $item['id_product'],
          (int) $item['quantity'],
          (int) $item['id_product_attribute']
        );
      }

      if ((int) Tools::getValue('ndkcf_id_combination') > 0) {
        $combNames = $product->getAttributesResume($id_lang);
        foreach ($combNames as $row) {
          if (
            $row['id_product_attribute'] ==
            (int) Tools::getValue('ndkcf_id_combination')
          ) {
            $combName = $row['attribute_designation'];
          }
        }
      } else {
        $combName = false;
      }

      foreach ($languages as $lang) {
        $newCustomProdObj->name[
          $lang['id_lang']
        ] = Tools::truncateString(
          $module->bundle_text.
            ' '.
            $product->name[$lang['id_lang']].
            (isset($combName) && '' != $combName
              ? ' - '.$combName
              : ''),
          125
        );
      }
    }

    // corrige le pb de reduction par groupe on ajoute si necessaire
    $reduction_from_category = GroupReduction::getValueForProduct(
      Tools::getValue('id_product'),
      (int) $context->customer->id_default_group
    );
    if (false !== $reduction_from_category) {
      $group_reduc = (float) $reduction_from_category;
    } else {
      // apply group reduction if there is no group reduction for this category
      $group_reduc = Group::getReductionByIdGroup(
        (int) $context->customer->id_default_group
      );
    }
    if (0 != (float) $group_reduc) {
      $coeff = 1 - (float) $group_reduc / 100;
      $newprice = $newprice / (float) $coeff;
    }

    $newCustomProdObj->price = number_format($newprice, 6, '.', '');
    $newCustomProdObj->wholesale_price = number_format(
      $wholesale_price,
      6,
      '.',
      ''
    );
    if ($newWeight > 0) {
      $newCustomProdObj->weight =
        (float) $product->weight + (float) $newWeight;
    }
    if (1 == Configuration::get('NDK_SPLIT_PACK')) {
      $newCustomProdObj->pack_stock_type = 0;
    } else {
      $newCustomProdObj->pack_stock_type = 1;
    }

    foreach ($languages as $language) {
      $newCustomProdObj->description[$language['id_lang']] =
        $new_desc[$language['id_lang']];
    }

    $qttytoset = (int) StockAvailable::getQuantityAvailableByProduct(
      (int) Tools::getValue('id_product'),
      (int) Tools::getValue('ndkcf_id_combination')
    );

    $qty_to_check = Tools::getValue('qty', 1);
    $cart_products = $context->cart->getProducts();

    if (is_array($cart_products)) {
      foreach ($cart_products as $cart_product) {
        if (Pack::isPack((int) $cart_product['id_product'])) {
          $packItems = Db::getInstance()->executeS(
            'SELECT id_product_item, id_product_attribute_item, quantity FROM `'.
              _DB_PREFIX_.
              'pack` where id_product_pack = '.
              (int) $cart_product['id_product']
          );
          foreach ($packItems as $item) {
            if (
              (!Tools::getValue('ndkcf_id_combination') ||
                $item['id_product_attribute_item'] ==
                  Tools::getValue('ndkcf_id_combination')) &&
              (Tools::getValue('id_product') &&
                $item['id_product_item'] ==
                  Tools::getValue('id_product'))
            ) {
              $qty_to_check +=
                $item['quantity'] *
                $cart_product['cart_quantity'];
              //$qty_to_check += Tools::getValue('qty');
            }
          }
        }

        if (
          (!Tools::getValue('ndkcf_id_combination') ||
            $cart_product['id_product_attribute'] ==
              Tools::getValue('ndkcf_id_combination')) &&
          (Tools::getValue('id_product') &&
            $cart_product['id_product'] ==
              Tools::getValue('id_product'))
        ) {
          $qty_to_check += $cart_product['cart_quantity'];
          //$qty_to_check += Tools::getValue('qty');
        }
      }
    }

    // Check product quantity availability
    if (Tools::getValue('ndkcf_id_combination') > 0) {
      if (
        !Product::isAvailableWhenOutOfStock($product->out_of_stock) &&
        !Attribute::checkAttributeQty(
          (int) Tools::getValue('ndkcf_id_combination'),
          $qty_to_check
        )
      ) {
        $qttytoset = 0;
      }
    } elseif ($product->hasAttributes()) {
      $minimumQuantity =
        2 == $product->out_of_stock
          ? !Configuration::get('PS_ORDER_OUT_OF_STOCK')
          : !$product->out_of_stock;

      if (
        !Product::isAvailableWhenOutOfStock($product->out_of_stock) &&
        !Attribute::checkAttributeQty(
          (int) Tools::getValue('ndkcf_id_combination'),
          $qty_to_check
        )
      ) {
        $qttytoset = 0;
      }
    } elseif (!$product->checkQty($qty_to_check)) {
      $qttytoset = 0;
    }

    $newCustomProdObj->quantity = $qttytoset;
    $newCustomProdObj->out_of_stock = $product->out_of_stock;
    $newCustomProdObj->update();
    $refProduct = $newCustomProdObj->id;
    Db::getInstance()->execute(
      'UPDATE `'.
        _DB_PREFIX_.
        'stock_available` SET `quantity` =  '.
        $qttytoset.
        ' WHERE id_product = '.
        (int) $newCustomProdObj->id
    );

    if (
      (int) Tools::getValue('ndkcf_id_combination') > 0 &&
      1 == Configuration::get('NDK_SHOW_COMBINATION')
    ) {
      $combName = '';
      $combNames = $product->getAttributesResume($id_lang);
      foreach ($combNames as $row) {
        if (
          $row['id_product_attribute'] ==
          (int) Tools::getValue('ndkcf_id_combination')
        ) {
          $combName = $row['attribute_designation'];
        }

        $combs = explode(',', $combName);
        foreach ($combs as $comb) {
          $rows = explode(' - ', $comb);
          $my_labels_comb = [];
          foreach ($languages as $language) {
            $my_labels_comb[$language['id_lang']][0]['name'] =
              $rows[0];
          }
          if ('' != $rows[0] && '' != $rows[1]) {
            $my_index_comb = createLabel(
              $languages,
              1,
              $refProduct,
              $my_labels_comb
            );
            addTextFieldToProduct(
              (int) $refProduct,
              $my_index_comb,
              1,
              $rows[1]
            );
          }
        }
      }
    }

    NdkCf::duplicateGroupReductionCache(
      (int) Tools::getValue('id_product'),
      $newCustomProdObj->id
    );
  } else {
    $refProduct = (int) Tools::getValue('id_product');
    //$newCustomProdObj = new Product($newCustomProd);
    //$newCustomProdObj->delete();
  }

  $details_field = createLabel($languages, 1, $refProduct, $labels_detail);
  $preview_field = createLabel($languages, 1, $refProduct, $labels_preview);
  $preview_field_img = createLabel(
    $languages,
    1,
    $refProduct,
    $labels_preview_img
  );
  if (0 != Tools::getValue('is_visual')) {
    if (1 == Configuration::get('NDK_SHOW_HD_PREVIEW')) {
      addTextFieldToProduct(
        $refProduct,
        $preview_field,
        1,
        NdkCf::l('No preview required')
      );
    }
    if (1 == Configuration::get('NDK_SHOW_IMG_PREVIEW')) {
      addTextFieldToProduct(
        $refProduct,
        $preview_field_img,
        1,
        NdkCf::l('No preview required')
      );
    }
  }

  $customization_price_field = createLabel(
    $languages,
    1,
    $refProduct,
    $labels_price
  );
  $link_index = createLabel($languages, 1, $refProduct, $labels_index);
  if (0 != $customizationPrice || sizeof($accessoryProdQuantity) > 0) {
    //print((int)$newCustomProdObj->id);

    //addTextFieldToProduct((int)Tools::getValue('id_product'), $link_index, 1, $newCustomProdObj->reference.' id:'.$newCustomProdObj->id);
    if (1 == Configuration::get('NDK_SHOW_TOTAL_COST')) {
      addTextFieldToProduct(
        $refProduct,
        $customization_price_field,
        1,
        Tools::displayPrice(
          Tools::convertPriceFull(
            $customizationPrice,
            $default_currency,
            $user_currency,
            6
          )
        )
      );
    }
    //$myIdCustomization = addTextFieldToProduct($refProduct, $details_field, 1, $prices_text);

    //compatibilité packs
    if (class_exists('NdkSpack')) {
      $steps = NdkSpack::getStepsForProduct(
        Tools::getValue('id_product')
      );
      if ($steps) {
        foreach ($steps as $id_step) {
          $step = new NdkSpackStep((int) $id_step);
          $curr_prods = $step->products;
          $step->products =
            '' != $curr_prods
              ? $curr_prods.','.$refProduct
              : $refProduct;
          //$step->products = $curr_prod.','.$refProduct;
          $step->save();
        }
      }
    }
  }

  //var_dump($ndkcustomvalue);
  $newNdkcustomvalue = [];
  $indexed = [];
  $indexedKey = [];

  $z = 0;
  foreach ($ndkcustomvalue as $value) {
    if (in_array($value['index'], $indexed)) {
      //$newNdkcustomvalue[ $indexed[$value['index']] ]['index']  = $value['index'];
      $newNdkcustomvalue[$value['index']]['value'] =
        $newNdkcustomvalue[$value['index']]['value'].
        '; '.
        $value['value'];
    } else {
      $newNdkcustomvalue[$value['index']] = $value;
      ++$z;
    }

    $indexed[] = $value['index'];
    //$indexedKey[$value['index']] = $z;
    //$z++;
  }

  //var_dump($newNdkcustomvalue);

  Db::getInstance()->execute(
    'UPDATE `'.
      _DB_PREFIX_.
      'customization_field` SET required = 0 WHERE id_product = '.
      (int) $refProduct
  );
  $newDesc = '';
  $myIdCustomization = 0;
  foreach ($newNdkcustomvalue as $val) {
    $myIdCustomization = addTextFieldToProduct(
      $refProduct,
      $val['index'],
      1,
      $val['value']
    );
    $fieldLabel = Db::getInstance()->getRow(
      '
     SELECT name FROM `'.
        _DB_PREFIX_.
        'customization_field_lang` WHERE `id_customization_field` = '.
        (int) $val['index'].
        ' AND `id_lang`= '.
        (int) Context::getContext()->language->id
    );

    //var_dump($fieldLabel);
    $newDesc .=
      '<p><b>'.$fieldLabel['name'].' : </b>'.$val['value'].'</p>';
  }

  //on retourne les valeurs
  if ((int) $context->customer->id > 0) {
    $return['id_customer'] = (int) $context->customer->id;
  } else {
    $return['id_customer'] = 0;
  }

  $return['id_product'] = (int) $refProduct;
  $return['id_cart'] = (int) $context->cart->id;
  $return['id_customization'] = (int) $myIdCustomization;
  $return['preview_field'] = $preview_field;
  $return['preview_field_img'] = $preview_field_img;

  echo Tools::jsonEncode($return);

  //on insere le recipient
  if (isset($recipientInfos)) {
    if (
      '' != $recipientInfos['firstname'] &&
      '' != $recipientInfos['lastname']
    ) {
      $recipient = new NdkCfRecipients();
      $recipient->id_product = (int) $refProduct;
      $recipient->id_combination = (int) Tools::getValue(
        'ndkcf_id_combination'
      );
      $recipient->id_cart = (int) $context->cart->id;
      $recipient->id_customization = (int) $myIdCustomization;
      $recipient->id_ndk_customization_field =
        $recipientInfos['id_ndk_customization_field'];
      $recipient->firstname = $recipientInfos['firstname'];
      $recipient->lastname = $recipientInfos['lastname'];
      $recipient->email = $recipientInfos['email'];
      $recipient->message = $recipientInfos['message'];
      $recipient->who_offers = $recipientInfos['who_offers'];
      $recipient->availability = $recipientInfos['availability'];
      $recipient->title = $recipientInfos['title'];
      $recipient->send_mail = $recipientInfos['send_mail'];
      $recipient->details = $recipientDetails;
      $recipient->code =
        'WEB'.Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
      $recipient->date = date('Y-m-d H:i:s');
      $recipient->save();
    }
  }

  if (0 != $customizationPrice || sizeof($accessoryProdQuantity) > 0) {
    if (1 == Configuration::get('NDK_KEEP_ORIGINAL_REFERENCE')) {
      if ((int) Tools::getValue('ndkcf_id_combination') > 0) {
        $combination = new Combination(
          (int) Tools::getValue('ndkcf_id_combination')
        );
        $newCustomProdObj->reference = $combination->reference;
      } else {
        $newCustomProdObj->reference = $product->reference;
      }
    } else {
      $newCustomProdObj->reference = Tools::str2url(
        'custom-'.
          $product->id.
          '-'.
          (int) Tools::getValue('ndkcf_id_combination').
          '-'.
          Context::getContext()->cart->id.
          '-'.
          $myIdCustomization
      );
    }

    $newCustomProdObj->description = $newDesc;
    $newCustomProdObj->active = 1;

    if ($ndkcf_itself) {
      foreach ($languages as $lang) {
        $newCustomProdObj->name[
          $lang['id_lang']
        ] = Tools::truncateString(
          $module->bundle_text.
            ' '.
            $product->name[$lang['id_lang']],
          125
        );
        $newCustomProdObj->link_rewrite[
          $lang['id_lang']
        ] = Tools::str2url(
          Tools::truncateString(
            $product->name[$lang['id_lang']],
            100
          ).time()
        );
        $newCustomProdObj->description_short[$lang['id_lang']] =
          $module->bundle_text.
          ' :'.
          $product->name[$lang['id_lang']];
      }
    }

    $newCustomProdObj->save();
    if (!$ndkcf_itself) {
      NdkCf::duplicateSpecificPrices(
        (int) $product->id,
        $newCustomProdObj->id
      );
      GroupReduction::duplicateReduction(
        (int) $product->id,
        $newCustomProdObj->id
      );
    }

    //get current price for group/customer
    $myNewPrice = Product::getPriceStatic(
      (int) $newCustomProdObj->id,
      $usetax,
      (int) 0,
      6,
      null,
      false,
      true,
      (int) Tools::getValue('qty'),
      false,
      (int) $context->customer->id,
      (int) $context->cart->id
    );
    if (sizeof($percent_price) > 0) {
      foreach ($percent_price as $key => $value) {
        if ($value > 0) {
          $multiplicatorHT = $value / 100;
          $myNewPrice += $myNewPrice * $multiplicatorHT;
        }
      }
    } else {
      $myNewPrice += $customizationPrice;
    }

    foreach (
      SpecificPrice::getIdsByProductId((int) $newCustomProdObj->id)
      as $data
    ) {
      $specific_price = new SpecificPrice(
        (int) $data['id_specific_price']
      );
      //$specific_price->price = -1;
      if ($specific_price->price > 0) {
        $specific_price->price = number_format($product_tax_calculator->removeTaxes($myNewPrice), 6, '.', '');
        $specific_price->reduction = 0;
      }

      if (1 == (int) Configuration::get('NDK_REDUC_ONLY_PRODUCT')) {
        if ('percentage' == $specific_price->reduction_type) {
          //on transforme en montant
          $price = Product::getPriceStatic(
            (int) $product->id,
            true,
            (int) 0,
            6,
            null,
            false,
            false,
            (int) Tools::getValue('qty'),
            false,
            (int) $context->customer->id,
            (int) $context->cart->id
          );
          //var_dump($price);
          $specific_price->reduction_type = 'amount';
          $reduc_percent = $specific_price->reduction;
          $new_amount = $price * $reduc_percent;
          $specific_price->reduction = $new_amount;
        }
      }

      $specific_price->update();
    }
  }

  //on ajoute le produit de base en tant que champs + le prix
  if (
    Product::getPriceStatic(
      $product->id,
      $usetax,
      (int) Tools::getValue('ndkcf_id_combination'),
      6
    ) > 0
  ) {
    //$myProductPrice = Product::getPriceStatic((int)Tools::getValue('id_product'), $usetax,(int)Tools::getValue('ndkcf_id_combination'), 6, null, false, true, (int)Tools::getValue('qty'), false, (int)$context->customer->id, (int)$context->cart->id);
    $myProductPrice = Product::getPriceStatic(
      (int) Tools::getValue('id_product'),
      false,
      (int) Tools::getValue('ndkcf_id_combination'),
      6,
      null,
      false,
      false,
      1,
      false,
      (int) $context->customer->id,
      (int) $context->cart->id
    );

    if ($usetax) {
      $myProductPrice = $product_tax_calculator->addTaxes(
        $myProductPrice
      );
    }

    if (Tools::getValue('force_taxe_rule_group')) {
      $tax_name = new TaxRulesGroup(
        (int) Tools::getValue('force_taxe_rule_group')
      );
    } else {
      $tax_name = new TaxRulesGroup(
        Product::getIdTaxRulesGroupByIdProduct(
          (int) Tools::getValue('id_product'),
          Context::getContext()
        )
      );
    }

    if ((int) Tools::getValue('ndkcf_id_combination') > 0) {
      $combNames = $product->getAttributesResume($id_lang);
      foreach ($combNames as $row) {
        if (
          $row['id_product_attribute'] ==
          (int) Tools::getValue('ndkcf_id_combination')
        ) {
          $combName = $row['attribute_designation'];
        }
      }
    } else {
      $combName = false;
    }

    $base_text =
      $product->name[$id_lang].
      (isset($combName) && '' != $combName
        ? '('.$combName.')'
        : ' - '.$product->reference).
      '  = '.
      Tools::displayPrice($myProductPrice).
      ($usetax ? ' ('.$tax_name->name.')' : '').
      "\n";

    //var_dump($custom_reference);
    if ('' != $custom_reference) {
      $custom_reference =
        $product->reference.''.implode('-', $custom_reference);
    }

    $link_index_reference = createLabel(
      $languages,
      1,
      $refProduct,
      $labels_custom_reference
    );
    if ('' != $custom_reference) {
      addTextFieldToProduct(
        (int) $refProduct,
        $link_index_reference,
        1,
        $custom_reference
      );
      if (
        1 == Configuration::get('NDK_ADD_REF_TO_NAME') &&
        (0 != $customizationPrice || sizeof($accessoryProdQuantity) > 0)
      ) {
        foreach ($languages as $lang) {
          $newCustomProdObj->name[
            $lang['id_lang']
          ] = Tools::truncateString(
            $custom_reference.
              ' - '.
              $product->name[$lang['id_lang']],
            125
          );
        }
        $newCustomProdObj->update();
      }
    }

    if (
      1 == Configuration::get('NDK_ADD_PRODUCT_PRICE') &&
      1 == Configuration::get('NDK_SHOW_BASE_PRODUCT') &&
      !$disabe_product_price
    ) {
      $link_index_base = createLabel(
        $languages,
        1,
        $refProduct,
        $labels_base
      );
      addTextFieldToProduct(
        (int) $refProduct,
        $link_index_base,
        1,
        $base_text
      );
    }
  }

  if (!$empty_form) {
    //enregistrement image
    $errors = [];

    $product_picture_width = (int) Configuration::get(
      'PS_PRODUCT_PICTURE_WIDTH'
    );
    $product_picture_height = (int) Configuration::get(
      'PS_PRODUCT_PICTURE_HEIGHT'
    );
    $suff = 1;
    if (1 != (int) Configuration::get('NDK_SHOW_IMG_PREVIEW')) {
      $cartImgs = [];
    }
    foreach ($cartImgs as $key => $value) {
      foreach ($languages as $language) {
        $labels_image[$language['id_lang']][0]['name'] =
          'Image '.$suff;
      }
      $image_field = createLabel(
        $languages,
        0,
        (int) Tools::getValue('id_product'),
        $labels_image
      );
      $file_name = md5(uniqid(rand(), true));
      $tmp_name = $value;
      /* Original file */
      if (
        !ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name)
      ) {
        $errors[] = '';
      } //Tools::displayError('An error occurred during the image upload process.');
      /* A smaller one */ elseif (
        !ImageManager::resize(
          $tmp_name,
          _PS_UPLOAD_DIR_.$file_name.'_small',
          $product_picture_width,
          $product_picture_height
        )
      ) {
        $errors[] = '';
      }
      //Tools::displayError('An error occurred during the image upload process.');
      elseif (
        !chmod(_PS_UPLOAD_DIR_.$file_name, 0777) ||
        !chmod(_PS_UPLOAD_DIR_.$file_name.'_small', 0777)
      ) {
        $errors[] = '';
      } //Tools::displayError('An error occurred during the image upload process.');
      /*else
       $context->cart->addPictureToProduct((int)$refProduct, $image_field, 0,$file_name);*/

      /*if($customizationPrice > 0) {

         //add image to product
         $image = new Image();
         $image->id_product = $newCustomProd;
         $image->position = Image::getHighestPosition($newCustomProd) + 1;
         $image->cover = ($suff == 1 ? true : false); // or false;
         if (($image->validateFields(false, true)) === true &&
         ($image->validateFieldsLang(false, true)) === true && $image->add())
         {
           $shops = Shop::getContextListShopID();
           $image->associateTo($shops);

           if (!NdkCf::copyImg($newCustomProd, $image->id, $tmp_name, 'products', true))
           {
             $image->delete();
           }
         }
         //eof
      }*/
      ++$suff;
    }

    /*if($customizationPrice > 0) {

       //add image to product
       $product_images = Image::getImages((int)$id_lang, (int)Tools::getValue('id_product'), (int)Tools::getValue('ndkcf_id_combination'));
       if(sizeof($product_images) > 0)
       {
         $image = new Image( (int)$product_images[0]['id_image'] );
         $image->id_product = $newCustomProd;
         $image->position = Image::getHighestPosition($newCustomProd) + 1;
         $image->cover = true; // or false;
         if (($image->validateFields(false, true)) === true &&
         ($image->validateFieldsLang(false, true)) === true && $image->add())
         {
           $shops = Shop::getContextListShopID();
           $image->associateTo($shops);

           if (!NdkCf::copyImg($newCustomProd, $image->id, $tmp_name, 'products', true))
           {
             $image->delete();
           }
         }
         //eof
      }
    }*/

    $customization_product = Db::getInstance()->executeS(
      'SELECT * FROM `'.
        _DB_PREFIX_.
        'customization`
     WHERE `id_cart` = '.
        (int) $context->cart->id.
        ' AND `id_product` = '.
        (int) Tools::getValue('id_product')
    );

    //print($customization_product[0]['id_customization']);
  }
}

$json_datas['result'] = $return;
saveJsonDatas($json_datas);

function saveJsonDatas($json_datas)
{
  $id_product = $json_datas['result']['id_product'];
  $id_customer = Context::getContext()->customer->id;
  $id_customization = $json_datas['result']['id_customization'];
  if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/')) {
    mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/', 0777);
  }
  if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) $id_customer)) {
    mkdir(
      _PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) $id_customer,
      0777
    );
  }

  if (
    !is_dir(
      _PS_IMG_DIR_.
        'scenes/'.
        'ndkcf/pdf/'.
        (int) $id_customer.
        '/'.
        (int) $id_product
    )
  ) {
    mkdir(
      _PS_IMG_DIR_.
        'scenes/'.
        'ndkcf/pdf/'.
        (int) $id_customer.
        '/'.
        (int) $id_product,
      0777
    );
  }

  if (
    !is_dir(
      _PS_IMG_DIR_.
        'scenes/'.
        'ndkcf/pdf/'.
        (int) $id_customer.
        '/'.
        (int) $id_product.
        '/'.
        (int) $id_customization
    )
  ) {
    mkdir(
      _PS_IMG_DIR_.
        'scenes/'.
        'ndkcf/pdf/'.
        (int) $id_customer.
        '/'.
        (int) $id_product.
        '/'.
        (int) $id_customization,
      0777
    );
  }

  file_put_contents(
    _PS_IMG_DIR_.
      'scenes/'.
      'ndkcf/pdf/'.
      (int) $id_customer.
      '/'.
      (int) $id_product.
      '/'.
      (int) $id_customization.
      '/config.json',
    Tools::jsonEncode($json_datas)
  );
}

function createLabel($languages, $type, $id_product, $labels, $required = 0)
{
  $result = false;
  $count = 0;
  $id_customization_field = 0;
  $required = 0;
  if ($labels[(int) Context::getContext()->language->id]) {
    if (
      '' != $labels[(int) Context::getContext()->language->id][0]['name']
    ) {
      //on recherche un champs existant
      $result = Db::getInstance()->executeS(
        '
         SELECT cf.`id_product`, cfl.id_customization_field
         FROM `'.
          _DB_PREFIX_.
          'customization_field` cf
         NATURAL JOIN `'.
          _DB_PREFIX_.
          'customization_field_lang` cfl
         WHERE cf.`id_product` = '.
          (int) $id_product.
          ' AND cfl.`id_lang` = '.
          (int) Context::getContext()->language->id.
          ' AND cfl.name = \''.
          pSQL(
            $labels[(int) Context::getContext()->language->id][0][
              'name'
            ]
          ).
          '\'
         ORDER BY cf.`id_customization_field`'
      );
      $count += sizeof($result);
    }
  }

  if (0 == $count && $labels[(int) Context::getContext()->language->id]) {
    // Label insertion
    if (
      !Db::getInstance()->execute(
        '
      INSERT INTO `'.
          _DB_PREFIX_.
          'customization_field` (`id_product`, `type`, `required`)
      VALUES ('.
          (int) $id_product.
          ', '.
          (int) $type.
          ', '.
          (int) $required.
          ')'
      ) ||
      !($id_customization_field = (int) Db::getInstance()->Insert_ID())
    ) {
      return false;
    }

    // Multilingual label name creation
    $values = '';

    foreach (Shop::getContextListShopID() as $id_shop) {
      foreach ($languages as $language) {
        $values .=
          '('.
          (int) $id_customization_field.
          ', '.
          (int) $language['id_lang'].
          ', '.
          (int) $id_shop.
          ', \''.
          pSQL(
            $labels[(int) Context::getContext()->language->id][0][
              'name'
            ]
          ).
          '\'), ';
      }
    }

    $values = rtrim($values, ', ');
    if (
      !Db::getInstance()->execute(
        '
          INSERT INTO `'.
          _DB_PREFIX_.
          'customization_field_lang` (`id_customization_field` ,`id_lang`, `id_shop`, `name`)
          VALUES '.
          $values
      )
    ) {
      return false;
    }

    // Set cache of feature detachable to true
    Configuration::updateGlobalValue(
      'PS_CUSTOMIZATION_FEATURE_ACTIVE',
      '1'
    );
  } else {
    if ($result) {
      $id_customization_field = $result[0]['id_customization_field'];
    }
    Db::getInstance()->execute(
      '
      UPDATE `'.
        _DB_PREFIX_.
        'customization_field` SET `required` = '.
        (int) $required.
        ' WHERE id_customization_field = '.
        (int) $id_customization_field
    );
  }

  return (int) $id_customization_field;
}

function addTextFieldToProduct($id_product, $index, $type, $text_value)
{
  return _addCustomization($id_product, 0, $index, $type, $text_value, 0);
}

/**
 * Add customer's pictures.
 *
 * @return bool Always true
 */
function addPictureToProduct($id_product, $index, $type, $file)
{
  return _addCustomization($id_product, 0, $index, $type, $file, 0);
}

function _addCustomization(
  $id_product,
  $id_product_attribute,
  $index,
  $type,
  $field,
  $quantity
) {
  $context = Context::getContext();

  $exising_customization = Db::getInstance()->executeS(
    '
      SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'.
      _DB_PREFIX_.
      'customization` cu
      LEFT JOIN `'.
      _DB_PREFIX_.
      'customized_data` cd
      ON cu.`id_customization` = cd.`id_customization`
      WHERE cu.id_cart = '.
      (int) $context->cart->id.
      '
      AND cu.id_product = '.
      (int) $id_product.
      '
      AND in_cart = 0'
  );

  if ($exising_customization) {
    // If the customization field is alreay filled, delete it
    foreach ($exising_customization as $customization) {
      if (
        $customization['type'] == $type &&
        $customization['index'] == $index
      ) {
        Db::getInstance()->execute(
          '
           DELETE FROM `'.
            _DB_PREFIX_.
            'customized_data`
           WHERE id_customization = '.
            (int) $customization['id_customization'].
            '
           AND type = '.
            (int) $customization['type'].
            '
           AND `index` = '.
            (int) $customization['index']
        );
        if (Product::CUSTOMIZE_FILE == $type) {
          @unlink(_PS_UPLOAD_DIR_.$customization['value']);
          @unlink(
            _PS_UPLOAD_DIR_.$customization['value'].'_small'
          );
        }
        break;
      }
    }
    $id_customization = $exising_customization[0]['id_customization'];
  } else {
    Db::getInstance()->execute(
      'INSERT INTO `'.
        _DB_PREFIX_.
        'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`)
         VALUES ('.
        (int) $context->cart->id.
        ', '.
        (int) $id_product.
        ', '.
        (int) $id_product_attribute.
        ', '.
        (int) $quantity.
        ')'
    );
    $id_customization = Db::getInstance()->Insert_ID();
  }

  if ((float) _PS_VERSION_ > 5.6) {
    $query =
      'INSERT INTO `'.
      _DB_PREFIX_.
      'customized_data` (`id_customization`, `type`, `index`, `value`, `id_module`)
      VALUES ('.
      (int) $id_customization.
      ', '.
      (int) $type.
      ', '.
      (int) $index.
      ','.
      '\''.
      addslashes(nl2br($field)).
      '\''.
      ', '.
      (int) Module::getModuleIdByName('ndk_advanced_custom_fields').
      ')';
  //var_dump($query);
  } else {
    $query =
      'INSERT INTO `'.
      _DB_PREFIX_.
      'customized_data` (`id_customization`, `type`, `index`, `value`)
      VALUES ('.
      (int) $id_customization.
      ', '.
      (int) $type.
      ', '.
      (int) $index.
      ', \''.
      addslashes(nl2br($field)).
      '\')';
  }

  if (!Db::getInstance()->execute($query)) {
    return false;
  }

  return $id_customization;
}
