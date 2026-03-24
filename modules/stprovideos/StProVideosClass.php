<?php
/*
* 2007-2014 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class StProVideosClass extends ObjectModel
{
	public $id;
    /** @var integer*/
    public $id_product;
    /** @var integer*/
    public $id_shop;
    /**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table'     => 'st_pro_videos',
		'primary'   => 'id_st_pro_videos',
		'multilang' => false,
		'fields'    => array(
            'id_product'        =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'id_shop'           =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
        ),
	);
	public function delete()
    {             
        return parent::delete() && StProVideoClass::deleteByVideos($this->id);
    }
    public static function deleteById($id_st_pro_videos)
    {
        $id_st_pro_videos = (int)$id_st_pro_videos;
        if(!$id_st_pro_videos)
            return false;
        StProVideoClass::deleteByVideos($id_st_pro_videos);
        return Db::getInstance()->delete('st_pro_videos', 'id_st_pro_videos = '.$id_st_pro_videos);
    }
    public static function deleteByProductId($id_product)
    {
        $id_product = (int)$id_product;
        if(!$id_product)
            return false;
        $id_st_pro_videos = self::getVideosIdByProductId($id_product);
        if(!$id_st_pro_videos)
            return false;
        return self::deleteById($id_st_pro_videos);
    }
	public function checkExists($id_product)
	{
		$sql = 'SELECT COUNT(0)
				FROM `'._DB_PREFIX_.'st_pro_videos`
				WHERE `id_product` = '.(int)$id_product.'
				AND id_shop='.(int)Context::getContext()->shop->id.'
				'.($this->id ? 'AND id_st_pro_videos !='.$this->id : '');

		return Db::getInstance()->getValue($sql);
	}
    public static function getByProductId($id_product = 0)
    {
        return Db::getInstance()->getRow('
            SELECT *
            FROM `'._DB_PREFIX_.'st_pro_videos`
            WHERE `id_product`='.(int)$id_product.'
			AND id_shop='.(int)Context::getContext()->shop->id
            );
    }
    public static function getVideosIdByProductId($id_product = 0)
    {
        return Db::getInstance()->getValue('
            SELECT id_st_pro_videos
            FROM `'._DB_PREFIX_.'st_pro_videos`
            WHERE `id_product`='.(int)$id_product.'
            AND id_shop='.(int)Context::getContext()->shop->id
            );
    }
    public static function getProductIdByVideosId($id_st_pro_videos = 0)
    {
        return Db::getInstance()->getValue('
            SELECT id_product
            FROM `'._DB_PREFIX_.'st_pro_videos`
            WHERE `id_st_pro_videos`='.(int)$id_st_pro_videos.'
            AND id_shop='.(int)Context::getContext()->shop->id
            );
    }
    public static function getProVideos($id_product = 0, $id_lang=0)
    {
        return Db::getInstance()->executeS('
            SELECT v.*
            FROM `'._DB_PREFIX_.'st_pro_videos` vs
            LEFT JOIN `'._DB_PREFIX_.'st_pro_video` v ON vs.id_st_pro_videos = v.id_st_pro_videos
            WHERE vs.id_product='.(int)$id_product.'
            AND (NOT EXISTS (SELECT id_st_pro_video FROM `'._DB_PREFIX_.'st_pro_video_yuyan` y WHERE v.id_st_pro_video = y.id_st_pro_video) || EXISTS (SELECT id_st_pro_video FROM `'._DB_PREFIX_.'st_pro_video_yuyan` y WHERE v.id_st_pro_video = y.id_st_pro_video AND y.id_lang='.$id_lang.'))
			AND v.active=1
			AND vs.id_shop='.(int)Context::getContext()->shop->id.'
            ORDER BY v.position'
            );
    }
	public static function getAll()
	{
		$id_lang = (int)Context::getContext()->language->id;
		$id_shop = (int)Context::getContext()->shop->id;
		return Db::getInstance()->executeS('
			SELECT pl.name, pv.* FROM `'._DB_PREFIX_.'st_pro_videos` pv
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON pv.id_product = pl.id_product AND pl.id_lang='.$id_lang.' AND pl.id_shop='.$id_shop.'
			WHERE pv.id_shop = '.$id_shop.' 
			');
	}
	public function copyFromPost()
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) && $key != 'id_'.$this->table && !isset($_FILES[$key]))
				$this->{$key} = $value;

        /* Multilingual fields */
        if (sizeof($this->fieldsValidateLang))
        {
            $languages = Language::getLanguages(false);
            foreach ($languages AS $language)
                foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]) && !isset($_FILES[$field.'_'.(int)($language['id_lang'])]))
                        $this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
        }
	}
}

?>
