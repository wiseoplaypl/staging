<?php
/**
* 2015-2017 NTS
*
* DISCLAIMER
*
* You are NOT allowed to modify the software.
* It is also not legal to do any changes to the software and distribute it in your own name / brand.
*
* @author    NTS
* @copyright 2015-2017 NTS
* @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
* International Registered Trademark & Property of NTS
*/

if (!defined('_PS_VERSION_')) { exit; }

class AccessorycheckboxAjaxModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->ssl = true;
		$this->ajax = true;
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {

		if ($this->module->active && Tools::getIsset('AccessoriesToken') && Tools::getValue('AccessoriesToken')!='') {

		  if(Tools::getValue('action')=='search'){

			 //$res = Search::find((int)Tools::getValue('id_lang'), pSQL(Tools::getValue('q')),1,10,'position','desc',true);
			 $res = Db::getInstance()->executeS('SELECT `id_product`,`name` FROM `'._DB_PREFIX_.'product_lang` WHERE NAME LIKE "%'.pSQL(Tools::getValue('q')).'%" && `id_lang`='.(int)Tools::getValue('id_lang').' && `id_shop`='.(int)Tools::getValue('id_shop').' limit 10');
			 $list='';
			 foreach($res as $p){
				   if(Tools::getValue('hide_current')=='true'){
					   $products = explode('-',Tools::getValue('products'));
					 if(!in_array((int)$p['id_product'], $products))
					 $list.="<li id='".(int)$p['id_product']."'>(".(int)$p['id_product'].") - ".$p['name']."</li>";
				   }else
					 $list.="<li id='".(int)$p['id_product']."'>(".(int)$p['id_product'].") - ".$p['name']."</li>";
				 }
				 if($list=='')
				 $list.="<li>No Result found!</li>";
			 die(json_encode(array('status' => 'ok','result' => $list)));

		   }

		} else {
			die(json_encode(array('status' => 'ko','result' => 'Unknown error, please use another card or contact us.')));
		}

	}
}
