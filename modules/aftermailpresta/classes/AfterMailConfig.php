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

class AfterMailConfig extends ObjectModel
{

    /**
     *
     * @var int id
     */
    public $id_aftermail_conf;

    /**
     *
     * @var string Name
     */
    public $name;

    /**
     *
     * @var int delay
     */
    public $delay;

    /**
     *
     * @var boolean active
     */
    public $active;

    /**
     *
     * @var string subject of a mail
     */
    public $subject;

    public $e_mail_text_txt;

    public $e_mail_text_html;

    public $id_attachment;

    public $trigger_type = 0;

    public $id_orderstate;

    public $subscribe_ids = 0;

    public $min_cart_sum = 0;

    public $reminder_frequency;

    public $voucher;

    public $vouchertype;

    public $voucheramount;

    public $voucherdays;

    public $vouchername;

    public $restrictcustomer;
	
	 /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'aftermail_conf',
        'primary' => 'id_aftermail_conf',
        'multilang' => true,
        'fields' => array(                      
			'name' => array('type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isGenericName', 'required' => true, 'size' => 256),
			'delay' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),			
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'trigger_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_orderstate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'voucher' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'vouchertype' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'voucheramount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => false),
			'voucherdays' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'restrictcustomer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'min_cart_sum' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => false),
			'reminder_frequency' => array('type' => self::TYPE_STRING, 'lang' => true,'size' => 256),
			'subscribe_ids' => array('type' => self::TYPE_STRING, 'lang' => true, 'size' => 2048),
			
            /* Lang fields */            
			'vouchername' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => false, 'size' => 256),
            'subject' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
			'e_mail_text_html' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
			'e_mail_text_txt' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'),
			'id_attachment' => array('type' => self::TYPE_INT, 'lang' => true, 'validate' => 'isUnsignedInt'),			        
        ),
    );
      

    protected $fieldsRequired = array(
        'name'
    );

    protected $fieldsSize = array(
        'name' => 64
    );

    protected $fieldsValidate = array(
        'name' => 'isString'
    );

    protected $fieldsSizeLang = array(
        'subject' => 254,
        'e_mail_text_txt' => 15000,
        'e_mail_text_html' => 15000
    );

    protected $fieldsValidateLang = array(
        'subject' => 'isString',
        'e_mail_text_txt' => 'isString',
        'e_mail_text_html' => 'isString',
        'id_attachment' => 'isInt'
    );

    protected $table = 'aftermail_conf';

    protected $identifier = 'id_aftermail_conf';

    public function getFields()
    {
        parent::validateFields();
        $fields = array();
        if (isset($this->id)) {
            $fields['id_aftermail_conf'] = (int) $this->id;
        }
        $fields['name'] = pSQL($this->name);
        $fields['delay'] = (int) $this->delay;
        $fields['active'] = (int) $this->active;
        $fields['trigger_type'] = (int) $this->trigger_type;
        $fields['id_orderstate'] = (int) $this->id_orderstate;
        $fields['voucher'] = (int) $this->voucher;
        $fields['vouchertype'] = (int) $this->vouchertype;
        $fields['voucheramount'] = (float) $this->voucheramount;
        $fields['voucherdays'] = (int) $this->voucherdays;
        $fields['vouchername'] = pSQL($this->vouchername);
        $fields['restrictcustomer'] = (int) $this->restrictcustomer;
        $fields['min_cart_sum'] = (float) $this->min_cart_sum;
        $fields['reminder_frequency'] = pSQL($this->reminder_frequency);
        $fields['subscribe_ids'] = pSQL($this->subscribe_ids);
        
        return $fields;
    }

    public function getTranslationsFieldsChild()
    {
        $fields_array = array(
            'subject',
            'e_mail_text_html',
            'e_mail_text_txt',
            'id_attachment'
        );
        $fields = array();
        $languages = Language::getLanguages(false);
        $default_language = Configuration::get('PS_LANG_DEFAULT');
        foreach ($languages as $language) {
            $fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
            $fields[$language['id_lang']][$this->identifier] = (int) $this->id_aftermail_conf;
            
            foreach ($fields_array as $field) {
                if (! Validate::isTableOrIdentifier($field)) {
                    die(Tools::displayError());
                }
                
                /* Check fields validity */
                if (isset($this->{$field}[$language['id_lang']]) && ! empty($this->{$field}[$language['id_lang']])) {
                    $fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']], true);
                } elseif (in_array($field, $this->fieldsRequiredLang)) {
                    $fields[$language['id_lang']][$field] = pSQL($this->{$field}[$default_language]);
                } else {
                    $fields[$language['id_lang']][$field] = '';
                }
            }
            $fields[$language['id_lang']]['id_attachment'] = (isset($this->id_attachment[$language['id_lang']])) ? pSQL($this->id_attachment[$language['id_lang']], true) : 0;
            $fields[$language['id_lang']]['subject'] = (isset($this->subject[$language['id_lang']])) ? pSQL($this->subject[$language['id_lang']], true) : '';
            $fields[$language['id_lang']]['e_mail_text_html'] = (isset($this->e_mail_text_html[$language['id_lang']])) ? pSQL($this->e_mail_text_html[$language['id_lang']], true) : '';
            $fields[$language['id_lang']]['e_mail_text_txt'] = (isset($this->e_mail_text_txt[$language['id_lang']])) ? pSQL($this->e_mail_text_txt[$language['id_lang']], true) : '';
        }
        return $fields;
    }

    public function update($null_values = false)
    {
        $null_values = true;
        return parent::update(true);
    }

    public function delete()
    {
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE `id_aftermail_conf` = ' . (int) ($this->id);
        
        Db::getInstance()->Execute($sql);
        
        return parent::delete();
    }

    /*
     * Specify if a manufacturer already in base
     *
     * @param $id_manufacturer Manufacturer id
     * @return boolean
     */
    public static function afterMailConfigExists($id_aftermail_conf)
    {
        $row = Db::getInstance()->getRow('
		SELECT `id_aftermail_conf`
		FROM ' . _DB_PREFIX_ . 'aftermail_conf c
		WHERE m.`id_aftermail_conf` = ' . (int) ($id_aftermail_conf));
        
        return isset($row['id_aftermail_conf']);
    }

    /**
     * Delete several objects from database
     *
     * return boolean Deletion result
     */
    public function deleteSelection($selection)
    {
        if (! is_array($selection) || ! Validate::isTableOrIdentifier($this->identifier) || ! Validate::isTableOrIdentifier($this->table)) {
            die(Tools::displayError());
        }
        $result = true;
        foreach ($selection as $id) {
            $this->id = (int) ($id);
            $result = $result && $this->delete();
        }
        return $result;
    }

    /**
     * If a mail should be sent to any buyers (not specific to category or product),
     * a value will be returned here
     */
    public function getGeneralMail()
    {
        if (! isset($this->id_aftermail_conf)) {
            return array();
        }
        
        $sql = 'SELECT acf.id_aftermail_conf_filter AS id
		FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter acf
		WHERE acf.id_aftermail_conf=' . (int) $this->id_aftermail_conf . ' AND id_category=-1';
        
        $result = Db::getInstance()->ExecuteS($sql);
        $rows = array();
        
        foreach ($result as $row) {
            $my_row = array(
                'id' => $row['id'],
                'cat_name' => 'All',
                'cat_id' => array(
                    - 1
                ),
                'prod_name' => 'All',
                'prod_id' => array(
                    - 1
                ),
                'variant_name' => 'All',
                'variant_id' => array(
                    - 1
                )
            );
            array_push($rows, $my_row);
        }
        
        return $rows;
    }

    /**
     * If a mail should be sent to buyers of products in a certain category,
     * values should be returned here
     */
    public function getEmptyCategoriesForMail()
    {
        if (! isset($this->id_aftermail_conf)) {
            return array();
        }
        
        $sql = 'SELECT acf.id_aftermail_conf_filter AS id , c_lang.name AS name_cat, c.id_category AS id_cat
		FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter acf
		INNER JOIN ' . _DB_PREFIX_ . 'category c ON c.id_category = acf.id_category
		INNER JOIN ' . _DB_PREFIX_ . 'category_lang c_lang ON (c.id_category = c_lang.id_category AND c_lang.id_lang = ' . (int) (Context::getContext()->language->id) . ')
		WHERE acf.id_aftermail_conf=' . $this->id_aftermail_conf . ' AND acf.id_product=-1';
        
        $result = Db::getInstance()->ExecuteS($sql);
        
        $rows = array();
        $multiple_checker = array();
        foreach ($result as $row) {
            if (array_key_exists($row['id'], $multiple_checker)) {
                $multiple_checker[$row['id']]['count'] = 2;
                array_push($multiple_checker[$row['id']]['ids'], $row['id_cat']);
            } else {
                $multiple_checker[$row['id']]['count'] = 1;
                $multiple_checker[$row['id']]['ids'] = array(
                    $row['id_cat']
                );
            }
        }
        
        foreach ($result as $row) {
            $catname = 'Multi';
            
            // ignore this entry since already made
            if ($multiple_checker[$row['id']]['count'] == 3) {
                continue;
            }
            
            if ($multiple_checker[$row['id']]['count'] == 1) {
                $catname = $row['name_cat'];
            }
            
            if ($multiple_checker[$row['id']]['count'] == 2) {
                $catname = 'Multi';
                $multiple_checker[$row['id']]['count'] = 3;
            }
            
            $my_row = array(
                'id' => $row['id'],
                'cat_name' => $catname,
                'cat_id' => $multiple_checker[$row['id']]['ids'],
                'prod_name' => 'All',
                'prod_id' => array(
                    - 1
                ),
                'variant_name' => 'All',
                'variant_id' => array(
                    - 1
                )
            );
            array_push($rows, $my_row);
        }
        return $rows;
    }

    /**
     * If a mail should be sent to buyers of specific products in a certain category,
     * values should be returned here
     */
    public function getEmptyProductsForMail()
    {
        if (! isset($this->id_aftermail_conf)) {
            return array();
        }
        
        $sql = 'SELECT acf.id_aftermail_conf_filter AS id, c_lang.name AS name_cat, c.id_category AS id_cat, p_lang.name AS name_product,p.id_product AS id_product
		FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter acf

		INNER JOIN ' . _DB_PREFIX_ . 'category c ON c.id_category = acf.id_category
		INNER JOIN ' . _DB_PREFIX_ . 'category_lang c_lang ON (c.id_category = c_lang.id_category AND c_lang.id_lang = ' . (int) (Context::getContext()->language->id) . ')

		INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = acf.id_product
		INNER JOIN ' . _DB_PREFIX_ . 'product_lang p_lang ON (p.id_product = p_lang.id_product AND p_lang.id_lang = ' . (int) (Context::getContext()->language->id) . ')

		WHERE acf.id_aftermail_conf=' . (int) $this->id_aftermail_conf . ' AND acf.id_category!=-1 AND acf.id_product!=-1 AND acf.id_combination=-1';
        
        $result = Db::getInstance()->ExecuteS($sql);
        $rows = array();
        
        $multiple_checker = array();
        foreach ($result as $row) {
            if (array_key_exists($row['id'], $multiple_checker)) {
                $multiple_checker[$row['id']]['count'] = 2;
                array_push($multiple_checker[$row['id']]['ids'], $row['id_product']);
            } else {
                $multiple_checker[$row['id']]['count'] = 1;
                $multiple_checker[$row['id']]['ids'] = array(
                    $row['id_product']
                );
            }
        }
        foreach ($result as $row) {
            $prodname = 'Multi';
            // ignore this entry since already made
            if ($multiple_checker[$row['id']]['count'] == 3) {
                continue;
            }
            
            if ($multiple_checker[$row['id']]['count'] == 1) {
                $prodname = $row['name_product'];
            }
            
            if ($multiple_checker[$row['id']]['count'] == 2) {
                $prodname = 'Multi';
                $multiple_checker[$row['id']]['count'] = 3;
            }
            
            $my_row = array(
                'id' => $row['id'],
                'cat_name' => $row['name_cat'],
                'cat_id' => $row['id_cat'],
                'prod_name' => $prodname,
                'prod_id' => $multiple_checker[$row['id']]['ids'],
                'variant_name' => 'All',
                'variant_id' => array(
                    - 1
                )
            );
            array_push($rows, $my_row);
        }
        
        return $rows;
    }

    /**
     * If a mail should be sent to buyers of specific products in a certain category, values should be returned here
     */
    public function getVariantsForMail()
    {
        if (! isset($this->id_aftermail_conf)) {
            return array();
        }
        
        $sql = 'SELECT acf.id_aftermail_conf_filter AS id, c_lang.name AS name_cat, c.id_category AS id_cat, p_lang.name AS name_product,p.id_product AS id_product, acf.id_combination AS id_combination
		FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter acf

		INNER JOIN ' . _DB_PREFIX_ . 'category c ON c.id_category = acf.id_category
		INNER JOIN ' . _DB_PREFIX_ . 'category_lang c_lang ON (c.id_category = c_lang.id_category AND c_lang.id_lang = ' . (int) (Context::getContext()->language->id) . ')

		INNER JOIN ' . _DB_PREFIX_ . 'product p ON p.id_product = acf.id_product
		INNER JOIN ' . _DB_PREFIX_ . 'product_lang p_lang ON (p.id_product = p_lang.id_product AND p_lang.id_lang = ' . (int) (Context::getContext()->language->id) . ')

		WHERE acf.id_aftermail_conf=' . (int) $this->id_aftermail_conf . ' AND acf.id_category!=-1 AND acf.id_product!=-1 AND acf.id_combination!=-1';
        
        $result = Db::getInstance()->ExecuteS($sql);
        $rows = array();
        
        $multiple_checker = array();
        foreach ($result as $row) {
            if (array_key_exists($row['id'], $multiple_checker)) {
                $multiple_checker[$row['id']]['count'] = 2;
                array_push($multiple_checker[$row['id']]['ids'], $row['id_combination']);
            } else {
                $multiple_checker[$row['id']]['count'] = 1;
                $multiple_checker[$row['id']]['ids'] = array(
                    $row['id_combination']
                );
            }
        }
        foreach ($result as $row) {
            $comb_name = 'Multi';
            // ignore this entry since already made
            if ($multiple_checker[$row['id']]['count'] == 3) {
                continue;
            }
            
            if ($multiple_checker[$row['id']]['count'] == 1) {
                $comb_name = 'Single';
            } // $row['name_product'];
            
            if ($multiple_checker[$row['id']]['count'] == 2) {
                $comb_name = 'Multi';
                $multiple_checker[$row['id']]['count'] = 3;
            }
            
            $my_row = array(
                'id' => $row['id'],
                'cat_name' => $row['name_cat'],
                'cat_id' => $row['id_cat'],
                'prod_name' => $row['name_product'],
                'prod_id' => $row['id_product'],
                'variant_name' => $comb_name,
                'variant_id' => array(
                    - 1
                )
            );
            array_push($rows, $my_row);
        }
        
        return $rows;
    }

    public function deleteFilter($filterid)
    {
        Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE `id_aftermail_conf_filter` = ' . (int) $filterid);
    }

    public function saveFilter($filterid, $categories, $products, $variants)
    {		
		if (isset($filterid) && $filterid != - 1) {            
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter WHERE id_aftermail_conf_filter=' . (int) $filterid;	
            Db::getInstance()->Execute($sql);
        }
		
        if (! is_array($variants) || count($variants) == 0) {
            $variants[] = - 1;
        }
        
        if (! is_array($products) || count($products) == 0) {
            $products[0] = - 1;
        }
        
        if (! is_array($categories) || count($categories) == 0) {
            $categories[0] = - 1;
        }
        
        if (! isset($filterid) || $filterid == - 1) {
            $sql = 'SELECT MAX(id_aftermail_conf_filter) AS MAXID FROM ' . _DB_PREFIX_ . 'aftermail_conf_filter';
            $result = Db::getInstance()->ExecuteS($sql);
            $filterid = ((int) $result[0]['MAXID']) + 1;
        }
      
        foreach ($categories as $cat) {
            foreach ($products as $prod) {
                foreach ($variants as $comb) {
                    $row = array(
                        'id_aftermail_conf_filter' => (int) $filterid,
                        'id_aftermail_conf' => (int) ($this->id_aftermail_conf),
                        'id_category' => (int) $cat,
                        'id_product' => (int) $prod,
                        'id_combination' => (int) $comb
                    );
					Db::getInstance()->insert('aftermail_conf_filter', $row);					
                }
            }
        } 
    }
}
