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

class StProVideoClass extends ObjectModel
{
    public $id;
    /** @var integer*/
    public $id_st_pro_videos;
    /** @var integer*/
    public $position;
    /** @var integer */
    public $active;
    /** @var string*/
    public $url;
    /** @var string*/
    public $thumbnail;
    /** @var integer*/
    public $online_thumbnail;
    /** @var integer*/
    public $loop;
    /** @var integer*/
    public $muted;
    /** @var integer*/
    public $autoplay;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table'     => 'st_pro_video',
        'primary'   => 'id_st_pro_video',
        'multilang' => false,
        'fields'    => array(
            'id_st_pro_videos'        =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'position'           =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'active'              => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'url'                 => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 255),
            'thumbnail'    => array('type' => self::TYPE_STRING, 'validate' => 'isAnything', 'size' => 255),
            'online_thumbnail'           =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'loop'           =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'muted'           =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'autoplay'           =>array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
        ),
    );

    public static function deleteByVideos($id_st_pro_videos)
    {
        $videos = self::getAll($id_st_pro_videos);
        $res = Db::getInstance()->delete('st_pro_video', 'id_st_pro_videos = '.(int)$id_st_pro_videos);
        foreach ($videos as $video) {
            $res &= StProVideoYuyanClass::deleteByVideo($video['id_st_pro_video']);
        }
        return $res;
    }
    public function delete()
    {             
        $res = parent::delete();
        if($res && !self::hasVideos($this->id_st_pro_videos))
            $res &= StProVideosClass::deleteById($this->id_st_pro_videos);
        return $res;
    }
    public static function hasVideos($id_st_pro_videos)
    {
        if(!$id_st_pro_videos)
            return false;

        return Db::getInstance()->getValue('
            SELECT count(0) 
            FROM `'._DB_PREFIX_.'id_st_pro_video`
            WHERE id_st_pro_videos='.$id_st_pro_videos
        );
    }
    public static function getAll($id_st_pro_videos, $id_lang=0, $active=0)
    {
        $result = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'st_pro_video`
            WHERE `id_st_pro_videos`='.(int)$id_st_pro_videos.($active ? ' AND `active`=1 ' : '').'
            ORDER BY `position`
            ');
        if(is_array($result) && count($result))
            foreach($result AS &$rs)
                self::fetchMediaServer($rs);
        return $result;
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
    

    public static function fetchMediaServer(&$banner)
    {
        $fields = array('thumbnail');
        if (is_string($banner) && $banner)
        {
            if (strpos($banner, '/upload/') === false && strpos($banner, '/modules/') === false)
                $banner = _THEME_PROD_PIC_DIR_.$banner;
            $banner = context::getContext()->link->protocol_content.Tools::getMediaServer($banner).$banner;
            return $banner;
        }
        foreach($fields AS $field)
        {
            if (is_array($banner) && isset($banner[$field]) && $banner[$field])
            {
                if (strpos($banner[$field], '/upload/') === false && strpos($banner[$field], '/modules/') === false )
                    $banner[$field] = _THEME_PROD_PIC_DIR_.$banner[$field];
                $banner[$field] = context::getContext()->link->protocol_content.Tools::getMediaServer($banner[$field]).$banner[$field];
            }
        }
    }
    
}
?>
