<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Shoprunners
 * Use, copy, modification or distribution of this source file without written
 * license agreement from Shoprunners is strictly forbidden.
 * In order to obtain a license, please contact us: info@shoprunners.de
 *
 * @author    Peter Schaeffer - Shoprunners
 * @copyright Copyright(c) 2012-2022 Shoprunners
 * @license   Commercial license
 * @package   aftermail
 */

include dirname(__FILE__) . '/../../config/config.inc.php';
include dirname(__FILE__) . '/classes/AfterMailLog.php';
include dirname(__FILE__) . '/aftermailpresta.php';

if (Tools::getValue('action')) {
    $id_lang = Context::getContext()->language->id;
    
    if (Tools::getValue('action') == 'loadProducts') {
        fillProductBox(Tools::getValue('category'), Tools::getValue('id_filter'), $id_lang);
    } elseif (Tools::getValue('action') == 'loadAttributes') {
        fillAttributeBox(Tools::getValue('product'), Tools::getValue('id_filter'), $id_lang);
    } elseif (Tools::getValue('action') == 'loadCategories') {
        fillCategoryBox(Tools::getValue('id_filter'), $id_lang);
    } elseif (Tools::getValue('action') == 'subscribe') {
        if (Context::getContext()->customer->isLogged()) {
            subscribe();
        }
    } elseif (Tools::getValue('action') == 'unsubscribe') {
        if (Context::getContext()->customer->isLogged() || Tools::getValue('customer_id') !== false) {
            unsubscribe();
        }
    } elseif (Tools::getValue('action') == 'unsubscribe_all') {
        if (Tools::getValue('customer_id') !== false) {
            unsubscribeAll();
        }
    }
}

function subscribe()
{
    $id_product = Tools::getValue('id_product');
    $frequency = Tools::getValue('frequency');
    $id_conf = Tools::getValue('id_conf');
    $id_customer = Context::getContext()->customer->id;
    
    $result = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_queue` ' . 'WHERE id_product = ' . (int) $id_product . ' AND id_customer = ' . (int) $id_customer);
    $taken = false;
    $saved = false;
    if (empty($result)) {
        $newqueue = new AfterMailLog();
        
        $newqueue->id_aftermail_conf = $id_conf;
        $newqueue->id_customer = $id_customer;
        $newqueue->id_product = $id_product;
        $newqueue->reminder_delay = ((int) $frequency) * 24 * 60; // days into minutes
        $time2Send = date('Y-m-d H:i:s', time() + 60 * $newqueue->reminder_delay); // minutes into seconds
        $newqueue->timestamp_tosend = $time2Send;
        $newqueue->unsubscribe = Tools::strtoupper(Tools::passwdGen(16));
        $newqueue->unsubscribe_all = Tools::strtoupper(Tools::passwdGen(16));
        $saved = $newqueue->save();
    } else {
        $taken = true;
    }
    echo '{"result": "' . ($saved ? 'ok' : 'err') . '", cause: "' . ($saved ? '' : ($taken ? 'taken' : 'error')) . '"}';
}

function unsubscribe()
{
    $id_product = Tools::getValue('id_product');
    $id_conf = Tools::getValue('id_conf');
    $id_customer = Tools::getValue('customer_id') !== false ? Tools::getValue('customer_id') : Context::getContext()->customer->id;
    
    $token = Tools::getValue('token');
    
    $success = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'aftermail_queue` WHERE id_product = ' . (int) $id_product . ' AND id_customer = ' . (int) $id_customer . ' AND id_aftermail_conf = ' . (int) $id_conf . ' AND unsubscribe = "' . pSQL($token) . '"');
    $rows = Db::getInstance()->Affected_Rows();
    
    $mod = new AfterMailPresta();
    
    if (Tools::getValue('customer_id') !== false) {
        echo '<style>* {font-family: Arial;}</style><br><br><br><center><h2>' . $mod->l($rows > 0 ? 'You\'ve successfully unsubscribed your product reminder' : "An error occured while unsubscribing") . '</h2><a href="' . _PS_BASE_URL_ . '">' . $mod->l('Back to the shop') . '</a></center>';
    } else {
        echo '{"result": "' . ($rows > 0 ? 'ok' : 'err') . '"}';
    }
}

function unsubscribeAll()
{
    $id_customer = Tools::getValue('customer_id');
    $token = Tools::getValue('token');
    
    $success = Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'aftermail_queue` WHERE id_customer = ' . (int) $id_customer . ' AND unsubscribe_all = "' . pSQL($token) . '"');
    $rows = Db::getInstance()->Affected_Rows();
    
    $mod = new AfterMailPresta();
    
    if (Tools::getValue('customer_id') !== false) {
        echo '<style>* {font-family: Arial;}</style><br><br><br><center><h2>' . $mod->l($rows > 0 ? 'You\'ve successfully unsubscribed from all reminders' : "An error occured while unsubscribing") . '</h2><a href="' . _PS_BASE_URL_ . '">' . $mod->l('Back to the shop') . '</a></center>';
    } else {
        echo '{"result": "' . ($rows > 0 ? 'ok' : 'err') . '"}';
    }
}

function fillCategoryBox($filterid, $id_lang)
{
    $preselected = array();
    
    if ($filterid && $filterid != - 1) {
        $sql = 'SELECT id_category FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter WHERE id_aftermail_conf_filter = ' . (int) $filterid;
        
        $preselectedTmp = Db::getInstance()->ExecuteS($sql);
        foreach ($preselectedTmp as $myEntry) {
            array_push($preselected, $myEntry['id_category']);
        }
    }
    
    $my_root_category = Category::getRootCategory();
    
    $my_categories = recurseLiteCategTree($my_root_category, 10, 0, $id_lang);
    
    if (empty($preselected) || in_array(- 1, $preselected)) {
        $html = '<option value="-1" selected="selected">All</option>';
    } else {
        $html = '<option value="-1">All</option>';
    }
    
    if (in_array($my_categories['id'], $preselected)) {
        $html .= '<option value="' . $my_categories['id'] . '" selected="selected">' . $my_categories['name'][$id_lang] . '</option>';
    } else {
        $html .= '<option value="' . $my_categories['id'] . '">' . $my_categories['name'][$id_lang] . '</option>';
    }
    
    $html .= getCategoryChildren($my_categories['children'], 2, 0, $preselected, $id_lang);
    
    echo $html;
}

function getCategoryChildren($category_array, $counter, $level, $preselected, $id_lang)
{
    $html = '';
    $level ++;
    $catarraycount = count($category_array);
    for ($i = 0; $i < $catarraycount; $i ++) {
        $border = '';
        for ($l = 0; $l < $level; $l ++) {
            $border .= '-';
        }
        
        if ($level > 0) {
            $border .= '>';
        }
        
        if (in_array($category_array[$i]['id'], $preselected)) {
            $html .= '<option value="' . $category_array[$i]['id'] . '" selected="selected">' . $border . $category_array[$i]['name'] . '</option>';
        } else {
            $html .= '<option value="' . $category_array[$i]['id'] . '">' . $border . $category_array[$i]['name'] . '</option>';
        }
        
        $counter ++;
        $html .= getCategoryChildren($category_array[$i]['children'], $counter, $level, $preselected, $id_lang);
    }
    return $html;
}

function fillProductBox($categoryID, $filterid, $id_lang)
{
    $preselected = array();
    
    if ($filterid && $filterid != - 1) {
        $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter WHERE id_aftermail_conf_filter = ' . (int) $filterid;
        // echo $sql;
        $preselectedTmp = Db::getInstance()->ExecuteS($sql);
        foreach ($preselectedTmp as $myEntry) {
            array_push($preselected, $myEntry['id_product']);
        }
    }
    
    $products = Product::getProducts((int) $id_lang, 0, 0, 'name', 'ASC', (int) $categoryID, false);
    
    if ((int) $filterid == - 1 || count($preselected) == 0) {
        echo '<option value="-1" selected>All</option>';
    } else {
        echo '<option value="-1">All</option>';
    }
    
    foreach ($products as $product) {
        if (in_array((int) $product['id_product'], $preselected)) {
            echo '<option value="' . $product['id_product'] . ' " selected>' . $product['name'] . '</option>';
        } else {
            echo '<option value="' . $product['id_product'] . ' ">' . $product['name'] . '</option>';
        }
    }
}

function fillAttributeBox($productID, $filterid, $id_lang)
{
    $product = new Product((int) $productID);
    $combinations = $product->getAttributeCombinaisons((int) ($id_lang));
    
    $preselected = array();
    $allSeleceted = false;
    if ($filterid && (int)$filterid != - 1) {
        $sql = 'SELECT id_combination FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter WHERE id_aftermail_conf_filter = ' . (int) $filterid;
        
        $preselectedTmp = Db::getInstance()->ExecuteS($sql);
        foreach ($preselectedTmp as $myEntry) {
            array_push($preselected, $myEntry['id_combination']);
            if ((int) $myEntry['id_combination'] == - 1) {
                $allSeleceted = true;
                $preselected = array(
                    - 1);
                break;
            }
        }
    } else {
        $allSeleceted = true;
    }
    
    $combArray = array();
    if (is_array($combinations)) {
        foreach ($combinations as $k => $combination) {
            $combArray[$combination['id_product_attribute']]['reference'] = $combination['reference'];
            $combArray[$combination['id_product_attribute']]['attributes'][] = array(
                $combination['group_name'],
                $combination['attribute_name'],
                $combination['id_attribute']);
        }
    }
    if ($allSeleceted) {
        echo '<option value="-1" selected>All</option>';
    } else {
        echo '<option value="-1">All</option>';
    }
    
    // $irow = 0;
    if ($combArray) {
        foreach ($combArray as $id_product_attribute => $product_attribute) {
            $list = '';
            $jsList = '';
            /* In order to keep the same attributes order */
            asort($product_attribute['attributes']);
            
            foreach ($product_attribute['attributes'] as $attribute) {
                $list .= addslashes(htmlspecialchars($attribute[0])) . ' - ' . addslashes(htmlspecialchars($attribute[1])) . ', ';
                $jsList .= '\'' . addslashes(htmlspecialchars($attribute[0])) . ' : ' . addslashes(htmlspecialchars($attribute[1])) . '\', \'' . $attribute[2] . '\', ';
            }
            $list = rtrim($list, ', ');
            $jsList = rtrim($jsList, ', ');
            
            if (in_array((int) $id_product_attribute, $preselected)) {
                echo '<option value="' . $id_product_attribute . '" selected>' . Tools::stripslashes($list) . '</option>';
            } else {
                echo '<option value="' . $id_product_attribute . '">' . Tools::stripslashes($list) . '</option>';
            }
        }
    }
}

/**
 * Recursive scan of subcategories
 *
 * @param integer $maxDepth
 *            Maximum depth of the tree (i.e. 2 => 3 levels depth)
 * @param integer $currentDepth
 *            specify the current depth in the tree (don't use it, only for rucursivity!)
 * @param array $excludedIdsArray
 *            specify a list of ids to exclude of results
 * @param integer $idLang
 *            Specify the id of the language used
 *
 * @return array Subcategories lite tree
 */
function recurseLiteCategTree($category, $maxDepth = 3, $currentDepth = 0, $id_lang = null, $excludedIdsArray = null)
{
    if (! (int) $id_lang) {
        $id_lang = _USER_ID_LANG_;
    }
    
    $children = array();
    $subcats = array();
    
    if ($maxDepth == 0 || $currentDepth < $maxDepth) {
        $subcats = $category->getSubCategories((int) $id_lang, true);
        if (count($subcats) > 0) {
            foreach ($subcats as &$subcat) {
                if (! $subcat['id_category']) {
                    break;
                } elseif (! is_array($excludedIdsArray) || ! in_array($subcat['id_category'], $excludedIdsArray)) {
                    $categ = new Category((int) $subcat['id_category'], (int) $id_lang);
                    $children[] = recurseLiteCategTree($categ, $maxDepth, $currentDepth + 1, (int) $id_lang, $excludedIdsArray);
                }
            }
        }
    }
    
    return array(
        'id' => (int) $category->id_category,
        'name' => $category->name,
        'children' => $children);
}
