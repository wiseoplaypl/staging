<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
 *
 * @author    NTS <nexustotalsolutions@gmail.com>
 * @copyright Copyright (c) NTS
 * @license   Commercial license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Accessorycheckbox extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'accessorycheckbox';
        $this->tab = 'others';
        $this->version = '2.3.1';
        $this->author = 'NTS';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = array('min' => '1.4', 'max' => _PS_VERSION_);
        $this->module_key = '71ce03a627b35d2db28a10caa95cf926';

        parent::__construct();

        $this->displayName = $this->l('Multi Accessories with Checkboxes');
        $this->description = $this->l('Show accessory products with checkbox on product page in front shop.');
    }

    public function install()
    {
        $hook_name = (_PS_VERSION_>=1.7?'displayProductButtons':'displayRightColumnProduct');
        Configuration::updateValue('AccessoriesToken', md5(Tools::passwdGen()));
        $ret = parent::install()
        && $this->registerHook('header')
        && $this->registerHook($hook_name)
        && $this->installDb();

        return $ret;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

     public function installDb()
    {
        return Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'accessories` (`id_accessory` int(11) NOT NULL AUTO_INCREMENT,
            `id_category` int(10),
            `id_product` int(10),
             PRIMARY KEY (`id_accessory`))
             ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');
    }

    public function hookHeader()
    {
        if (Tools::getValue('controller') == 'product') {
            $this->context->controller->addCSS($this->_path.'/views/css/accessorycheckbox.css');
            if (_PS_VERSION_>=1.7) {
            $this->context->controller->registerJavascript('modules-accessorycheckbox', 'modules/'.$this->name.'/views/js/accessorycheckbox.js');
            }
        }
    }

    public function hookDisplayRightColumnProduct()
    {
        return $this->display_accessories();
    }

    public function hookDisplayProductButtons()
    {
        return $this->display_accessories();
    }

    public function hookProductActions()
    {
        return $this->display_accessories();
    }

    public function display_accessories()
    {
        $product = new Product(Tools::getValue('id_product'));
        if(Configuration::get('category_products_acc')) {
            $p_categories = Product::getProductCategories(Tools::getValue('id_product'));
            $p_categories = implode(',',$p_categories);
            $cat_accessories = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'accessories` where id_category IN ('.$p_categories.')');
        } else {
            $cat_accessories = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'accessories` where id_category='.$product->id_category_default);
        }

        $accessories = (!Configuration::get('related_accessories')?$product->getAccessories($this->context->language->id):array());
        //print_r($cat_accessories);die;
        foreach ($accessories as $key => $acc) {

            foreach($cat_accessories as $keys=>$cat_acc) {
                if($cat_acc['id_product']==$acc['id_product'])
                  unset($cat_accessories[$keys]);
                }

              $p = new Product($acc['id_product']);
              $combinations = $p->getAttributesResume($this->context->language->id);
              $accessories[$key]['combinations'] = (!is_array($combinations)?array():$combinations);
         }

        $count_acc = count($accessories);

        foreach($cat_accessories as $cat_acc) {
           $new_acc_counter = $count_acc++;

           $new_accessory = $this->getAccessories($cat_acc['id_product'], $this->context->language->id, $this->context->shop->id);

           $p = new Product($cat_acc['id_product']);
           $combinations = $p->getAttributesResume($this->context->language->id);
           $new_accessory[0]['combinations'] = (!is_array($combinations)?array():$combinations);

           $accessories[$new_acc_counter] = $new_accessory[0];
        }
        //print_r($accessories);die;
        if (isset($accessories) && count($accessories)>0) {
            $this->context->controller->addJQueryPlugin('fancybox');
            $this->context->smarty->assign(array('accessories'=> $accessories,'_PS_VERSION_'=>_PS_VERSION_));
            return $this->display(__FILE__, 'accessorycheckbox.tpl');
        }
    }

    public function getContent()
    {

        if(Tools::isSubmit('SubmitAccessories')) {
            Configuration::updateValue('related_accessories', Tools::getValue('related_accessories'));
            Configuration::updateValue('category_products_acc', Tools::getValue('category_products_acc'));
            $post_acc = Tools::getValue('accessories');
            Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'accessories` ');
            foreach($post_acc as $key=>$acces) {
                if ($key==0)
                   continue;
                $id_cat = $key;
                foreach($acces as $acc) {
                    $id_pro = $acc;
                    $data = array('id_category'=>$id_cat,'id_product'=>$id_pro);
                    $check_exists = Db::getInstance()->getValue('SELECT id_accessory FROM `'._DB_PREFIX_.'accessories` where id_category='.$id_cat.' && id_product='.$id_pro);
                    if(empty($check_exists)) {
                        Db::getInstance()->insert('accessories',$data);
                    }
                      // Db::getInstance()->update('accessories',$data,'id_accessory='.$check_exists);
                }
          }
        }
        $shopDomainSsl = Tools::getShopDomainSsl(true, true);
        $stripeBOCssUrl = $shopDomainSsl.__PS_BASE_URI__.'modules/'.$this->name.'/views/css/prestashop-admin.css';
        $categories = Category::getSimpleCategories($this->context->language->id);
        $accessories = Db::getInstance()->executeS('SELECT a.*,b.name
            FROM `'._DB_PREFIX_.'accessories` a
            LEFT JOIN `'._DB_PREFIX_.'product_lang` b ON a.id_product=b.id_product && b.id_lang='.(int)$this->context->cookie->id_lang.' where b.id_shop='.(int)$this->context->shop->id);

        $accessories_arr = array();
        foreach($accessories as $acc){
            $accessories_arr[$acc['id_category']][] = array('id_product'=>$acc['id_product'],'name'=>$acc['name']);
        }

         $tplVars = array(
            'id_lang' => (int)$this->context->cookie->id_lang,
            'id_shop' => (int)$this->context->shop->id,
            'categories' => $categories,
            'accessories' => $accessories_arr,
            'AccessoriesToken' => Configuration::get('AccessoriesToken'),
            'ps_version' => _PS_VERSION_,
            'this_path' => $this->_path,
            'stripeBOCssUrl' => $stripeBOCssUrl,
			'mod_ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax', array('ajax'=>true), true),
        );

        $tplVars['success'] = (Tools::isSubmit('SubmitAccessories')?true:false);

        $this->context->smarty->assign($tplVars);
        return $this->display(__FILE__, 'views/templates/admin/settings.tpl');
    }

    public function getAccessories($id_product, $id_lang, $id_shop, $active = true){

        $product = new Product($id_product);

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`,
                    pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
                    image_shop.`id_image`, il.`legend`, m.`name` as manufacturer_name, cl.`name` AS category_default, IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
                    DATEDIFF(
                        p.`date_add`,
                        DATE_SUB(
                            "'.date('Y-m-d').' 00:00:00",
                            INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
                        )
                    ) > 0 AS new
                FROM `'._DB_PREFIX_.'product` p
                '.Shop::addSqlAssociation('product', 'p').'
                LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
                    ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
                    p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
                )
                LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (
                    product_shop.`id_category_default` = cl.`id_category`
                    AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
                )
                LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (p.`id_manufacturer`= m.`id_manufacturer`)
                '.Product::sqlStock('p', 0).'
                WHERE p.`id_product` = '.(int)$id_product.
                ($active ? ' AND product_shop.`active` = 1 AND product_shop.`visibility` != \'none\'' : '').'
                GROUP BY product_shop.id_product';

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return false;
        }

        foreach ($result as $k => &$row) {
            if (!Product::checkAccessStatic((int)$row['id_product'], false)) {
                unset($result[$k]);
                continue;
            } else {
                $row['id_product_attribute'] = Product::getDefaultAttribute((int)$row['id_product']);
            }
        }

        return $product->getProductsProperties($id_lang, $result);
        }
}
