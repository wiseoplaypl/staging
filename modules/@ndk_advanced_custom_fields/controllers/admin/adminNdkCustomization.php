<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

class AdminNdkCustomizationController extends ModuleAdminController
{
	
	
	public $bootstrap = true;
	
	public function __construct()
	{
			$this->bootstrap = true;
			$this->table = 'ndk_customization_field_configuration';
			$this->className = 'NdkCfConfig';
			$this->lang = true;
			$this->explicitSelect = true;
			$this->allow_export = true;
			$this->_defaultOrderBy = 'id_ndk_customization_field_configuration';
			
			$this->identifier = 'id_ndk_customization_field_configuration';
			
			$this->fieldImageSettings = array(
				array('name' => 'image', 'dir' => 'scenes/ndkcf/'),
				array('name' => 'thumb', 'dir' => 'scenes/ndkcf/thumbs')
			);
			parent::__construct();
			$this->bulk_actions = array(
				'delete' => array(
					'text' => $this->l('Delete selected'),
					'icon' => 'icon-trash',
					'confirm' => $this->l('Delete selected items?')
				)
			);
			
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			         SELECT DISTINCT a.id_ndk_customization_field, a.products, pl.`name` 
			         FROM `'._DB_PREFIX_.'ndk_customization_field` a
			         LEFT JOIN `'._DB_PREFIX_.'product` p ON (FIND_IN_SET( p.`id_product`, a.`products`)) 
			         LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product`= p.`id_product` AND pl.`id_lang` = '.(int)Context::getContext()->language->id.') 
			         WHERE TRIM(IFNULL(pl.name,\'\')) <> \'\' ORDER BY pl.name ASC ');
	
			$products_array = array();
			foreach ($result as $row)
			   $products_array[$row['products']] = $row['name'];
			   
			$products_array = array_unique($products_array);
			asort($products_array);
			
			
			$this->fields_list = array(
				'id_ndk_customization_field_configuration' => array(
					'title' => $this->l('ID'),
					'align' => 'center',
					'width' => 25
				),
				'name' => array(
					'title' => $this->l('Name'),
					'filter_key' => 'b!name'
				),
				'id_user' => array(
				   'title' => $this->l('Customer'),
				   /*'list' => $t_array,*/
				   'filter_key' => 'a!id_user',
				   /*'callback' => 'getCustomerName',*/
				),
				
				'tags' => array(
					'title' => $this->l('Tags'),
					'filter_key' => 'b!tags'
				),
				'id_product' => array(
					'title' => $this->l('Product'),
					'filter_key' => 'a!id_product',
					'type' => 'select',
					'list' => $products_array,
				),
				
				'id_customization' => array(
					'title' => $this->l('ID customization'),
					'filter_key' => 'a!id_customization'
				),
			
				'is_admin' => array(
					'title' => $this->l('Admin customization'),
					'active' => 'is_admin',
					'type' => 'bool',
					'class' => 'fixed-width-xs',
					'align' => 'center',
					'orderby' => false
				),
				'id_lang_default' => array(
				   'title' => $this->l('ID default lang'),
				   /*'list' => $t_array,*/
				   'filter_key' => 'a!id_lang_default',
				   /*'callback' => 'getCustomerName',*/
				),
				'default_config' => array(
				   'title' => $this->l('Default'),
				   'active' => 'default_config',
				   'type' => 'bool',
				   'class' => 'fixed-width-xs',
				   'align' => 'center',
				   'orderby' => false
				),
				'price' => array(
					'title' => $this->l('Price HT'),
					'filter_key' => 'a!price'
				),
				
			);
			
			parent::__construct();
		}
		
		public function renderList()
		{
				if (Tools::getIsset($this->_filter) && trim($this->_filter) == '')
					$this->_filter = $this->original_filter;
				
				$this->addRowAction('edit');
				$this->addRowAction('delete');
		
				return parent::renderList();
		}
		
		public function init()
		   {
		      if (Tools::getIsset('default_configndk_customization_field_configuration'))
		      {
		         NdkCfConfig::setDefaultConfig((int)Tools::getValue('id_ndk_customization_field_configuration'));
		      }
		      
		      parent::init();
		   }
		
		
		
		public function renderForm()
			{
				//$this->initToolbar();
				$obj = $this->loadObject(true);
				$id_shop = Context::getContext()->shop->id;
				$this->initFieldsForm();
				if (!($obj = $this->loadObject(true)))
					return;
		
				return parent::renderForm();
			}
			
		public function initFieldsForm()
		{
			$obj = $this->loadObject(true);
			$config = new NdkCfConfig($obj->id, null);
			$this->addJqueryPlugin(array('autocomplete', 'tagify'));
			$this->addJs(_MODULE_DIR_.'ndk_advanced_custom_fields/views/js/admin.js' );
			
			$products_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
						SELECT p.id_product, CONCAT ( \'#\', p.id_product, \' - \',  pl.name, \' (ref:\', p.reference, \')\') AS pname 
						FROM `'._DB_PREFIX_.'product` p 
						INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->context->language->id.')
						ORDER BY pl.name ASC');
						
			$empty_refp = array('id_product' => 0, 'pname' => '--');
			array_push($products_array, $empty_refp);	
			
			$additionnals = '';
			if(file_exists(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$obj->id_user.'/'.$obj->id_product.'/'.$obj->id.'-0-img.jpg'))
			{
			   $additionnals='<img class="img-responsive configCover" src="../img/scenes/ndkcf/configs/'.$obj->id_user.'/'.$obj->id_product.'/'.$obj->id.'-0-img.jpg"/>';
			   $additionnals .='<span class="AjaxremoveFile" data-file="/img/scenes/ndkcf/configs/'.$obj->id_user.'/'.$obj->id_product.'/'.$obj->id.'-0-img.jpg"><i class="icon icon-trash"></i>'.$this->l('remove').'</span>';
			 }
			 
			$link = new Link();
			if((int)$obj->id_product > 0)
				$button_link = $link->getProductLink((int)$obj->id_product).'?id_ndk_customization_field_configuration='.$obj->id;
			else
				$button_link = '#';
			
			$edit_frame = '<iframe frameborder="0" style="width:100%; min-height:600px" class="clear clearfix" src="'.$button_link.'&content_only=1"></iframe>';
			
			$button = '<div class="clear clearfix"><a target="_blank" class="btn btn-default" href="'.$button_link.'">'.$this->l('Edit configuration').'</a></div>';				
			$fields_form = array(
				'legend' => array(
					'title' => $this->l('Customization'),
					),
				'submit' => array(
					'title' => $this->l('Save'),
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Name :'),
						'name' => 'name',
						'lang' => true,
						'size' => 48
					),
					array(
						'type' => 'tags',
						'label' => $this->l('Tags:'),
						'name' => 'tags',
						'lang' => true,
						'desc' => $this->l('Tag your template to set filters (press enter after typing your tag)'),
					),
					array(
						'type' => 'select',
						'label' => $this->l('Product'),
						'name' => 'id_product',
						'class' => 'chosen',
						'options' => array(
							'query' => $products_array,
							'id' => 'id_product',
							'name' => 'pname'
							),
					),
					array(
					   'type' => 'file',
					   'label' => $this->l('Image:'),
					   'name' => 'image',
					   'display_image' => true,
					   'desc' => $additionnals,
					   'hint' => $this->l('Cover image for this configuration')
					),
					array(
						'type' => 'html',
						'name' => ' ',
						'label' => $this->l('preview :'),
						'desc' => Tools::file_get_contents($config->pdffile).$button,
					),
				)
					
			);
			$this->fields_form = $fields_form;
		}
		
		
		public function processSave()
			{
				if ($this->display == 'add' || $this->display == 'edit')
					$this->identifier = 'ndk_customization_field_configuration';
		
				if (!$this->id_object)
					return $this->processAdd();
				else
					return $this->processUpdate();
			}
		
		
		
		public function getCustomerName($id_user)
		{
			$sql = 'SELECT CONCAT( firstname, " ", lastname ) as name FROM `'._DB_PREFIX_.'customer`
							WHERE `id_customer` = '.(int)$id_user;
			$return = Db::getInstance()->getRow($sql);
			        return $return['name'];
			
		}
		
		protected function postImage($id)
		{
		   $obj = $this->loadObject(true);
		   if ($obj->id && (isset($_FILES['image'])))
		   {
		      $name = $_FILES["image"]["name"];
		      if($name != '')
		      {
		         
		             $base_img_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$obj->id_user.'/'.$obj->id_product.'/'.$obj->id.'-0-img.jpg';
		             $tmp_name = $_FILES["image"]["tmp_name"];
		             move_uploaded_file($tmp_name, $base_img_path);
		        }
		   }
		   return parent::postImage($obj->id);
		}
		
		
}

?>