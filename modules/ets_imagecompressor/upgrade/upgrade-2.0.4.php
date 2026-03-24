<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_'))
	exit;
function upgrade_module_2_0_4($object)
{
    $sqls = array();
    if(!$object->checkCreatedColumn('ets_imagecompressor_blog_slide_image','image'))
    {
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_slide_image` ADD `image` VARCHAR(222) NOT NULL AFTER `type_image`';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_slide_image` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_slide`, `type_image`, `image`) USING BTREE';
    }
    if(!$object->checkCreatedColumn('ets_imagecompressor_blog_gallery_image','image'))
    {
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_gallery_image` ADD `image` VARCHAR(222) NOT NULL AFTER `type_image`, ADD `thumb` VARCHAR(222) NOT NULL AFTER `image`';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_gallery_image` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_gallery`, `type_image`, `image`,`thumb`) USING BTREE';
    }
    if(!$object->checkCreatedColumn('ets_imagecompressor_blog_category_image','image'))
    {
        $sqls[]='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_category_image` ADD `image` VARCHAR(222) NOT NULL AFTER `type_image`, ADD `thumb` VARCHAR(222) NOT NULL AFTER `image`';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_category_image` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_category`, `type_image`, `image`,`thumb`) USING BTREE';
    }    
    if(!$object->checkCreatedColumn('ets_imagecompressor_blog_post_image','image'))
    {
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_post_image` ADD `image` VARCHAR(222) NOT NULL AFTER `type_image`, ADD `thumb` VARCHAR(222) NOT NULL AFTER `image`';
        $sqls[] ='ALTER TABLE `'._DB_PREFIX_.'ets_imagecompressor_blog_post_image` DROP PRIMARY KEY, ADD PRIMARY KEY (`id_post`, `type_image`, `image`,`thumb`) USING BTREE';
    }
    $sqls[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ets_imagecompressor_product_image_lang` (
      `id_image_lang` int(11) NOT NULL,
      `id_lang` int(11) NULL,
      `type_image` varchar(64) NOT NULL,
      `quality` int(11) NOT NULL,
      `size_old` float(10,2),
      `size_new` float(10,2),
      `optimize_type` VARCHAR(8),
      PRIMARY KEY( `id_image_lang`, `type_image`)
    )  ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8';    
    if($sqls)
    {
        foreach($sqls as $sql)
            Db::getInstance()->execute($sql);
    }
    return true;
}