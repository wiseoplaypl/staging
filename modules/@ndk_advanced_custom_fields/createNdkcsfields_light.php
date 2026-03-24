<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/ndk_advanced_custom_fields.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCf.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfValues.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfRecipients.php';
$return = array();
$context = Context::getContext();
$languages = Language::getLanguages();
$id_lang = Context::getContext()->language->id;
$product = new Product((int)Tools::getValue('id_product'), (int)$id_lang);

$real_pprice = $product->base_price;
$empty_form = true;
$is_recipient = false;
Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET customizable = 1 WHERE id_product = '.(int)$product->id);
Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product_shop` SET customizable = 1 WHERE id_product = '.(int)$product->id);

if (!Tools::getValue('ndkcsfield')) {
    $return = array();
    $return['id_product'] = (int)Tools::getValue('id_product');
    $return['id_cart'] = (int)$context->cart->id;
    print(Tools::jsonEncode($return));
    return true;
}


if (Product::$_taxCalculationMethod == 0) {
    $usetax = true;
} else {
    $usetax = false;
}
$ndkFields = NdkCf::getCustomFieldsForCreation(Tools::getValue('id_product'), $product->id_category_default);

if (Tools::getValue('id_product') && Tools::getValue('ndkcsfield') > 0) {
    // If cart has not been saved, we need to do it so that customization fields can have an id_cart
    // We check that the cookie exists first to avoid ghost carts
    
    
    if (!$context->cart->id) {
        $context->cart->add();
        $context->cookie->id_cart = (int)$context->cart->id;
    }
    
    if ((int)Tools::getValue('old_id_customization') > 0) {
        $customisation = new Customization((int)Tools::getValue('old_id_customization'));
        $customProd = new Product((int)$customisation->id_product);
        $context->cart->updateQty((int)Tools::getValue('qty'), (int)$customProd->id, 0, (int)Tools::getValue('old_id_customization'), 'down');
        $customisation->delete();
    }
        
    $images = Tools::getValue('image-url');
    //$decoded = base64_decode(str_replace('data:image/png;base64,', '', $image));
    $im = 0;
    $cartImgs = array();
    foreach ($images as $image) {
        $decoded = mb_convert_encoding(str_replace('data:image/png;base64,', '', $image), "UTF-8", "BASE64");
        $name = time();
        file_put_contents(_PS_UPLOAD_DIR_.'ndkacf_'. $context->cart->id . '-'.$im.'.png', $decoded);
        $cartImgs[$im] = _PS_UPLOAD_DIR_.'ndkacf_'. $context->cart->id . '-'.$im.'.png';
        $im++;
        
        //print('<img src="'.$image.'"/>');
    }
    
    $i = 0;
    $labels_detail = array();
    $labels_image = array();
    $labels_price = array();
    $labels_index = array();
    $labels_preview = array();
    $labels_preview_img = array();
    $labels_custom_reference = array();
    
    $new_desc = array();
    foreach ($languages as $language) {
        $new_desc[$language['id_lang']] = '';
    }
    //$prices_text = $product->name[$id_lang].' : '.Tools::displayPrice(Product::getPriceStatic($product->id, $usetax)).'  ' ."\n" ;
    $prices_text ='';
    foreach ($languages as $language) {
        $labels_detail[$language['id_lang']][0]['name'] = 'Details';
        $labels_price[$language['id_lang']][0]['name'] = Tools::getValue('cusTextTotal');
        $labels_index[$language['id_lang']][0]['name'] = Tools::getValue('cusTextRef');
        $labels_preview[$language['id_lang']][0]['name'] = Tools::getValue('previewText');
        $labels_preview_img[$language['id_lang']][0]['name'] = NdkCf::l('Preview (image)');
        $labels_custom_reference[$language['id_lang']][0]['name'] = NdkCf::l('reference');
    }
    
    $customizationPrice = 0;
    $ndkcustomvalue = array();

    $recipientDetails = '';
    
    $ndkFields = NdkCf::getCustomFieldsForCreation(Tools::getValue('id_product'), $product->id_category_default);
    $custom_reference = '';
    $orientations = array();
    
    
    
    foreach (Tools::getValue('ndkcsfield') as $field => $value) {
        if ($field == 'orientation') {
            foreach ($value as $k=>$v) {
                $orientations[$k] = $v;
            }
        }
    }
    
    /*foreach(Tools::getValue('ndkcsfield') as $field => $value){*/
    foreach ($ndkFields as $ndkField) {
        $field = $ndkField['id_ndk_customization_field'];
        if (isset(Tools::getValue('ndkcsfield')[$ndkField['id_ndk_customization_field']])) {
            $value = Tools::getValue('ndkcsfield')[$ndkField['id_ndk_customization_field']];
        } else {
            $value = '';
        }
        if (!empty($value) && $value !='') {
            $values = array();
            $empty_form = false;
            //1 on crée les champs
            $labels = array();
            $required = Db::getInstance()->executeS(
                'SELECT cf.`required`
            FROM `'._DB_PREFIX_.'ndk_customization_field`cf 
            WHERE cf.`id_ndk_customization_field` = '.(int)$field
            );
            
            foreach ($languages as $language) {
                $labels[$language['id_lang']] = Db::getInstance()->executeS(
                    'SELECT '.(Configuration::get('NDK_USE_ADMIN_NAME') == 1 ? 'cfl.`admin_name`' : 'cfl.`name`').' as name 
            FROM `'._DB_PREFIX_.'ndk_customization_field_lang`cfl 
            WHERE cfl.`id_ndk_customization_field` = '.(int)$field.' AND cfl.`id_lang` = '.(int)$language['id_lang']
                );
            }
            
            
            //$product->customizable = 1;
            //$product->update(array('customizable' =>1));
            //Db::getInstance()->update('product', array('customizable' => 1), '`id_product` = '.(int)$product->id, 0, false);
            
            /* on gère les quantités */
            $accessoryQuantity = array();
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($k == 'quantity') {
                        //var_dump($v);
                        foreach ($v as $k2 => $v2) {
                            $values[] = $k2;
                            $accessoryQuantity[$k2] = $v2;
                        }
                    } elseif ($k == 'checkbox') {
                        //var_dump($v);
                        foreach ($v as $k2 => $v2) {
                            $values[] = $k2;
                            $accessoryQuantity[$k2] = 1;
                        }
                    } elseif ($k == 'recipient') {
                        $is_recipient = false;
                        $imp = 1;
                        $imploded = '';
                        $recipientInfos = $v;
                        $recipientField = new NdkCf((int)$field, $id_lang);
                        $recipientInfos['availability'] = $recipientField->validity;
                        $recipientInfos['title'] = $recipientField->notice;
                        $recipientInfos['id_ndk_customization_field'] = (int)$field;
                        $ndk = new ndk_advanced_custom_fields();
                        foreach ($v as $k2 => $v2) {
                            if ($k2 == 'send_mail') {
                                if ($v2 == 1) {
                                    $v2 = $ndk->l('yes');
                                } else {
                                    $v2 = $ndk->l('no');
                                }
                            }
                            
                            if ($k2 == 'email' && $v2 !='') {
                                $is_recipient = true;
                            }
                            
                                
                            $imploded .= '<strong>'.$ndk->l($k2).' </strong>'.$v2.($imp < sizeof($v) ? ' <br/> ': '');
                            $imp++;
                        }
                        if ($is_recipient) {
                            $values[] = $imploded;
                        }
                    }
                }
            } else {
                $values[]= $value;
            }
            
            if (!$is_recipient) {
                $recipientDetails .= $labels[$language['id_lang']][0]['name'].' : ';
            }
            //on demarra la boucle
            foreach ($values as $value) {
                    
                    //2 on renseigne les personnalisations
                    
                if ($i+1 < sizeof(Tools::getValue('ndkcsfield'))) {
                    $suffix = ' - ' ."\n";
                    $virgule = '<br/>';
                } else {
                    $suffix = ' ';
                    $virgule = '';
                }
                    
                        
                                        
                if (count($accessoryQuantity) == 0) {
                    $accessoryQuantity[$value] = 0;
                //$value ='';
                } else {
                    if ($accessoryQuantity[$value] == 0) {
                        $value ='';
                    }
                }
                     
                    
                        
                        
                if (!empty($value) && $value !='') {
                    $orientation = '';
                    if (isset($orientations[$field])) {
                        $orientation = ' ['.$orientations[$field].']';
                    }
                                
                    $reference = NdkCf::getValueRef($field, $value, Tools::getValue('id_product'));
                    if ($reference != '') {
                        $custom_reference.='-'.str_replace('[:id_product]', (int)Tools::getValue('id_product'), $reference);
                    }
                                

                    $ndkcustomvalue[]= array('index' => createLabel($languages, 1, (int)Tools::getValue('id_product'), $labels, $required[0]['required']), 'value' => $value. ($accessoryQuantity[$value] > 0 ? ' X'.$accessoryQuantity[$value] : '').$orientation);
                            
                    if (!$is_recipient) {
                        $recipientDetails .= ' '.($accessoryQuantity[$value] > 0 ? $accessoryQuantity[$value] : '').' '.$value.$virgule;
                    }
                            
                    createLabel($languages, 1, (int)Tools::getValue('id_product'), $labels, $required[0]['required']);
                    //addTextFieldToProduct(Tools::getValue('id_product'), (int)Tools::getValue('ndkcf_id_combination'), $index_field, 1, $value);
                            
                    foreach ($languages as $language) {
                        if (!empty($value)) {
                            $new_desc[$language['id_lang']] .= $labels[$language['id_lang']][0]['name'] .' : '.$value. ($accessoryQuantity[$value] > 0 ? ' X'.$accessoryQuantity[$value] : '').'<br/>';
                        }
                    }
                }
            }
            $i++;
        }
    }
    
    
    $refProduct = (int)Tools::getValue('id_product');
    
    
    $details_field = createLabel($languages, 1, $refProduct, $labels_detail);
    $customization_price_field = createLabel($languages, 1, $refProduct, $labels_price);
    
    $link_index = createLabel($languages, 1, $refProduct, $labels_index);
    
    
    $preview_field = createLabel($languages, 1, $refProduct, $labels_preview);
    $preview_field_img = createLabel($languages, 1, $refProduct, $labels_preview_img);
    
    
   
    if (Tools::getValue('is_visual')!= 0) {
        if (Configuration::get('NDK_SHOW_HD_PREVIEW') == 1) {
            addTextFieldToProduct($refProduct, (int)Tools::getValue('ndkcf_id_combination'), $preview_field, 1, NdkCf::l('No preview required'));
        }
        if (Configuration::get('NDK_SHOW_IMG_PREVIEW') == 1) {
            addTextFieldToProduct($refProduct, (int)Tools::getValue('ndkcf_id_combination'), $preview_field_img, 1, NdkCf::l('No preview required'));
        }
    }
    
    
    $newNdkcustomvalue = array();
    $indexed = array();
    $indexedKey = array();
    
    $z = 0;
    foreach ($ndkcustomvalue as $value) {
        if (in_array($value['index'], $indexed)) {
            //$newNdkcustomvalue[ $indexed[$value['index']] ]['index']  = $value['index'];
            $newNdkcustomvalue[ $value['index'] ]['value']  = $newNdkcustomvalue[ $value['index'] ]['value'].'; '.$value['value'];
        } else {
            $newNdkcustomvalue[$value['index']] = $value;
            $z++;
        }
       
        $indexed[] = $value['index'];
        //$indexedKey[$value['index']] = $z;
       //$z++;
    }
    
    //var_dump($newNdkcustomvalue);
    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization_field` SET required = 0 WHERE id_product = '.(int)$refProduct);
    
    foreach ($newNdkcustomvalue as $val) {
        $myIdCustomization = addTextFieldToProduct($refProduct, (int)Tools::getValue('ndkcf_id_combination'), $val['index'], 1, $val['value']);
    }
    
    $link_index_reference = createLabel($languages, 1, $refProduct, $labels_custom_reference);
    if ($custom_reference !='') {
        addTextFieldToProduct((int)$refProduct, (int)Tools::getValue('ndkcf_id_combination'), $link_index_reference, 1, $custom_reference);
    }
    
    //on insere le recipient
    if (isset($recipientInfos)) {
        if ($recipientInfos['firstname'] !='' && $recipientInfos['lastname'] !='') {
            $recipient = new NdkCfRecipients();
            $recipient->id_product = (int)$refProduct;
            $recipient->id_combination = (int)Tools::getValue('ndkcf_id_combination');
            $recipient->id_cart = (int)$context->cart->id;
            $recipient->id_customization = (int)$myIdCustomization;
            $recipient->id_ndk_customization_field = $recipientInfos['id_ndk_customization_field'];
            $recipient->firstname = $recipientInfos['firstname'];
            $recipient->lastname = $recipientInfos['lastname'];
            $recipient->email = $recipientInfos['email'];
            $recipient->message = $recipientInfos['message'];
            $recipient->availability = $recipientInfos['availability'];
            $recipient->title = $recipientInfos['title'];
            $recipient->send_mail = $recipientInfos['send_mail'];
            $recipient->details = $recipientDetails;
            $recipient->code = 'WEB'.Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
            $recipient->date = date('Y-m-d H:i:s');
            $recipient->save();
        }
    }
    
    
        
    
    
    if (!$empty_form) {
    
            //enregistrement image
        $errors = array();
            
        $product_picture_width = (int)Configuration::get('PS_PRODUCT_PICTURE_WIDTH');
        $product_picture_height = (int)Configuration::get('PS_PRODUCT_PICTURE_HEIGHT');
        $suff = 1;
            
        /*foreach($cartImgs as $key => $value)
        {
            foreach ($languages as $language) {
                    $labels_image[$language['id_lang']][0]['name'] = 'Image '.$suff;
            }
            $image_field = createLabel($languages, 0, (int)Tools::getValue('id_product'), $labels_image);
            $file_name = md5(uniqid(rand(), true));
            $tmp_name = $value;

            if (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name))
                $errors[] = Tools::displayError('An error occurred during the image upload process.');

            elseif (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name.'_small', $product_picture_width, $product_picture_height))
                $errors[] = Tools::displayError('An error occurred during the image upload process.');
            elseif (!chmod(_PS_UPLOAD_DIR_.$file_name, 0777) || !chmod(_PS_UPLOAD_DIR_.$file_name.'_small', 0777))
                $errors[] = Tools::displayError('An error occurred during the image upload process.');
            else
            $context->cart->addPictureToProduct((int)$refProduct, $image_field, 0,$file_name);


            $suff++;
        }*/
            
        $customization_product = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'customization`
            WHERE `id_cart` = '.(int)$context->cart->id.' AND `id_product` = '.(int)Tools::getValue('id_product'));
            
            
        //print($customization_product[0]['id_customization']);
    }
}
//on retourne les valeurs
if ((int)$context->customer->id > 0) {
    $return['id_customer'] = (int)$context->customer->id;
} else {
    $return['id_customer'] = 0;
}
    
$return['id_product'] = (int)$refProduct;
$return['id_cart'] = (int)$context->cart->id;
if (isset($myIdCustomization)) {
    $return['id_customization'] = (int)$myIdCustomization;
}

$return['preview_field'] = $preview_field;
$return['preview_field_img'] = $preview_field_img;

print(Tools::jsonEncode($return));

    function createLabel($languages, $type, $id_product, $labels, $required = 0)
    {
        $count = 0;
        $id_customization_field = 0;
        if ($labels[(int) Context::getContext()->language->id][0]['name'] !='') {
            //on recherche un champs existant
            $result = Db::getInstance()->executeS('
                   SELECT cf.`id_product`, cfl.id_customization_field 
                   FROM `'._DB_PREFIX_.'customization_field` cf
                   NATURAL JOIN `'._DB_PREFIX_.'customization_field_lang` cfl
                   WHERE cf.`id_product` = '.(int)$id_product. ' AND cfl.`id_lang` = '.(int) Context::getContext()->language->id.' AND cfl.name = \''.pSQL($labels[(int) Context::getContext()->language->id][0]['name']).'\' 
                   ORDER BY cf.`id_customization_field`');
            $count += sizeof($result);
        }
          
          
        if ($count == 0) {
            // Label insertion
            if (!Db::getInstance()->execute('
                INSERT INTO `'._DB_PREFIX_.'customization_field` (`id_product`, `type`, `required`)
                VALUES ('.(int)$id_product.', '.(int)$type.', '.(int)$required.')') ||
                !$id_customization_field = (int)Db::getInstance()->Insert_ID()) {
                return false;
            }
       
            // Multilingual label name creation
            $values = '';
       
            foreach (Shop::getContextListShopID() as $id_shop) {
                foreach ($languages as $language) {
                    $values .= '('.(int)$id_customization_field.', '.(int) $language['id_lang'].', '.(int)$id_shop.', \''.pSQL($labels[(int) Context::getContext()->language->id][0]['name']).'\'), ';
                }
            }
                
            $values = rtrim($values, ', ');
            if (!Db::getInstance()->execute('
                         INSERT INTO `'._DB_PREFIX_.'customization_field_lang` (`id_customization_field` ,`id_lang`, `id_shop`, `name`)
                         VALUES '.$values)) {
                return false;
            }
       
            // Set cache of feature detachable to true
            Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', '1');
        } else {
            if ($result) {
                $id_customization_field = $result[0]['id_customization_field'];
            }
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'customization_field` SET `required` = '.(int)$required.' WHERE id_customization_field = '.(int)$id_customization_field);
        }
    
        return (int)$id_customization_field;
    }
    
    
    function addTextFieldToProduct($id_product, $id_attribute, $index, $type, $text_value)
    {
        return _addCustomization($id_product, $id_attribute, $index, $type, $text_value, 0);
    }
    
        /**
         * Add customer's pictures
         *
         * @return bool Always true
         */
        function addPictureToProduct($id_product, $id_attribute, $index, $type, $file)
        {
            return _addCustomization($id_product, $id_attribute, $index, $type, $file, 0);
        }
    
    
    
    function _addCustomization($id_product, $id_product_attribute, $index, $type, $field, $quantity)
    {
        $context = Context::getContext();
          
        $exising_customization = Db::getInstance()->executeS(
            '
             SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `'._DB_PREFIX_.'customization` cu
             LEFT JOIN `'._DB_PREFIX_.'customized_data` cd
             ON cu.`id_customization` = cd.`id_customization`
             WHERE cu.id_cart = '.(int)$context->cart->id.'
             AND cu.id_product = '.(int)$id_product.'
             AND in_cart = 0'
        );
    
        if ($exising_customization) {
            // If the customization field is alreay filled, delete it
            foreach ($exising_customization as $customization) {
                if ($customization['type'] == $type && $customization['index'] == $index) {
                    Db::getInstance()->execute('
                      DELETE FROM `'._DB_PREFIX_.'customized_data`
                      WHERE id_customization = '.(int)$customization['id_customization'].'
                      AND type = '.(int)$customization['type'].'
                      AND `index` = '.(int)$customization['index']);
                    if ($type == Product::CUSTOMIZE_FILE) {
                        @unlink(_PS_UPLOAD_DIR_.$customization['value']);
                        @unlink(_PS_UPLOAD_DIR_.$customization['value'].'_small');
                    }
                    break;
                }
            }
            $id_customization = $exising_customization[0]['id_customization'];
        } else {
            Db::getInstance()->execute(
                'INSERT INTO `'._DB_PREFIX_.'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`)
                VALUES ('.(int)$context->cart->id.', '.(int)$id_product.', '.(int)$id_product_attribute.', '.(int)$quantity.')'
            );
            $id_customization = Db::getInstance()->Insert_ID();
        }
    
        /*$query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`)
           VALUES ('.(int)$id_customization.', '.(int)$type.', '.(int)$index.', \''.pSQL($field).'\')';*/
          
        $query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`)
             VALUES ('.(int)$id_customization.', '.(int)$type.', '.(int)$index.', \''.addslashes(nl2br($field)).'\')';
    
        if (!Db::getInstance()->execute($query)) {
            return false;
        }
        return $id_customization;
    }
