<?php
/*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class StProVideoYuyanClass
{
    public static function deleteByVideo($id_st_pro_video)
    {
    	if(!$id_st_pro_video)
    		return false;
        return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'st_pro_video_yuyan WHERE `id_st_pro_video`='.(int)$id_st_pro_video);
    }
    public static function getByVideo($id_st_pro_video)
    {
    	if(!$id_st_pro_video)
    		return false;
        return Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'st_pro_video_yuyan WHERE `id_st_pro_video`='.(int)$id_st_pro_video);
    }

    public static function changeVideoYuyan($id_st_pro_video, $yuyans)
    {
        if(!$id_st_pro_video)
            return false;
        $res = true;
        foreach ($yuyans as $id_lang)
            if($id_lang)
                $res &= Db::getInstance()->insert('st_pro_video_yuyan', array(
                    'id_st_pro_video' => (int)$id_st_pro_video,
                    'id_lang' => $id_lang
                ));
        return $res;
    }
}