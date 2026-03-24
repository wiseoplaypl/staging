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

if (!defined('_PS_VERSION_'))
    exit;

include_once(dirname(__FILE__).'/StProVideosClass.php');
include_once(dirname(__FILE__).'/StProVideoClass.php');
include_once(dirname(__FILE__).'/StProVideoYuyanClass.php');

class StProVideos extends Module
{
    private $_html = '';
    public $fields_form;
    public $fields_value;
    public $_prefix_st = 'ST_PVIDEO_';
    private $_st_themes_17 = false;
    private $_st_themes_16 = false;
    public $theme_name = '';
    public $validation_errors = array();
    private static $video_position = array();
    protected static $access_rights = 0775;
    protected static $selectors = array(
        'video_container' => array(
            'classic' => '.images-container .product-cover',
            'panda' => '.pro_gallery_top_inner',
            'transformer' => '.pro_gallery_top_inner',
            'warehouse' => '.images-container .product-cover',
            'alysum' => '.images-container .product-cover',
            'panda1' => '.pb-left-column #image-block',
            'default-bootstrap' => '.pb-left-column #image-block',
            'transformer3' => '.pb-left-column #image-block',
        ),
        'gallery_container' => array(
            'classic' => '.images-container .product-cover',
            'panda' => '.pro_gallery_top',
            'transformer' => '.pro_gallery_top',
            'warehouse' => '#product-images-large',
            'alysum' => '.images-container .product-cover',
            'panda1' => '#bigpic_list_frame',
            'default-bootstrap' => '.pb-left-column #image-block',
            'transformer3' => '.pb-left-column #image-block',
        ),
        'thumbnail_container' => array(
            'classic' => '.images-container .product-images',
            'panda' => '.pro_gallery_thumbs',
            'transformer' => '.pro_gallery_thumbs',
            'warehouse' => '#product-images-thumbs',
            'alysum' => '.images-container .product-images',
            'panda1' => '#thumbs_list_frame',
            'default-bootstrap' => '#thumbs_list_frame',
            'transformer3' => '#thumbs_list_frame',
        ),
        'video_selector' => array(
            'classic' => '.images-container .product-cover',
            'panda' => '.pro_gallery_top_container .swiper-slide',
            'transformer' => '.pro_gallery_top_container .swiper-slide',
            'warehouse' => '#product-images-large .slick-slide',
            'alysum' => '.images-container .product-cover',
            'panda1' => '#image-block .item',
            'default-bootstrap' => '.pb-left-column #image-block',
            'transformer3' => '.pb-left-column #image-block',
        ),
        'thumbnail_selector' => array(
            'classic' => '.images-container .thumb-container',
            'panda' => '.pro_gallery_thumbs_container .swiper-slide',
            'transformer' => '.pro_gallery_thumbs_container .swiper-slide',
            'warehouse' => '#product-images-thumbs .slick-slide',
            'alysum' => '.images-container .thumb-container',
            'panda1' => '#thumbs_list .item',
            'default-bootstrap' => '#thumbs_list_frame li',
            'transformer3' => '#thumbs_list_frame li',
        ),
        'thumbnail_html' => array(
            'classic' => '<li class="thumb-container st_pro_video_relative st_pro_video_thumbnail" data-video-id="***"><img class="thumb $$$" src="###" width="100"><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button></li>',
            'panda' => '<div class="swiper-slide" data-video-id="***"><img src="@@@"/><div class="st_pro_video_layer st_pro_video_flex"><div class="pro_gallery_thumb_box general_border"><img class="pro_gallery_thumb $$$" src="###"/></div><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button></div></div>',
            'transformer' => '<div class="swiper-slide" data-video-id="***"><img src="@@@"/><div class="st_pro_video_layer st_pro_video_flex"><div class="pro_gallery_thumb_box general_border"><img class="pro_gallery_thumb $$$" src="###"/></div><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button></div></div>',
            'warehouse' => '<div class="swiper-slide" data-video-id="***"><div><div class="thumb-container"><div class="st_pro_video_relative"><img src="@@@" class="img-fluid"/><div class="st_pro_video_layer st_pro_video_flex"><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button><img class="thumb img-fluid $$$" src="###"></div></div></div></div></div>',
            'alysum' => '<li class="thumb-container st_pro_video_relative st_pro_video_thumbnail" data-video-id="***"><img class="thumb $$$" src="###"><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button></li>',
            'panda1' => '<div class="item" data-video-id="***"><img src="@@@" class="img-responsive"/><div class="st_pro_video_layer st_pro_video_flex"><div class="pro_gallery_thumb_box general_border"><img class="$$$" src="###"/></div><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button></div></div>',
            'default-bootstrap' => '<li class="st_pro_video_relative" data-video-id="***"><img src="@@@" class="img-responsive"/><div class="st_pro_video_layer st_pro_video_flex"><div class="pro_gallery_thumb_box general_border"><img class="$$$" src="###"/></div><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button></div></li>',
            'transformer3' => '<li class="st_pro_video_relative" data-video-id="***"><img src="@@@" class="img-responsive"/><div class="st_pro_video_layer st_pro_video_flex"><div class="pro_gallery_thumb_box general_border"><img class="$$$" src="###"/></div><button class="st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button></div></li>',
        ),
        'gallery_image_type' => array(
            'classic' => 'large_default',
            'panda' => 'medium_default',
            'transformer' => 'medium_default',
            'warehouse' => 'large_default',
            'alysum' => 'large_default',
            'panda1' => 'large_default',
            'default-bootstrap' => 'large_default',
            'transformer3' => 'large_default',
        ),
        'thumbnail_image_type' => array(
            'classic' => 'home_default',
            'panda' => 'cart_default',
            'transformer' => 'cart_default',
            'warehouse' => 'medium_default',
            'alysum' => 'home_default',
            'panda1' => 'small_default',
            'default-bootstrap' => 'cart_default',
            'transformer3' => 'medium_default',
        ),
        'slider' => array(
            'classic' => 0,
            'panda' => 1,
            'transformer' => 1,
            'warehouse' => 3,
            'alysum' => 0,
            'panda1' => 4,
            'default-bootstrap' => 0,
            'transformer3' => 0,
        ),
        'thumb_slider' => array(
            'classic' => 0,
            'panda' => 1,
            'transformer' => 1,
            'warehouse' => 3,
            'alysum' => 0,
            'panda1' => 4,
            'default-bootstrap' => 5,
            'transformer3' => 5,
        ),
        'z_index' => array(
            'classic' => 9,
            'panda' => 9,
            'transformer' => 9,
            'warehouse' => 23,
            'alysum' => 9,
            'panda1' => 9,
            'default-bootstrap' => 9,
            'transformer3' => 9,
        ),
        'desc_container' => array(
            'classic' => '#description .product-description',
            'panda' => '#description .product-description',
            'transformer' => '#description .product-description',
            'warehouse' => '#description .product-description',
            'alysum' => '#description .product-description',
            'panda1' => '#idTab1 .pa_content',
            'default-bootstrap' => '#page-product-box .rte',
            'transformer3' => '#idTab1 .pa_content',
        ),
    );
    private $_st_is_16;
    function __construct()
    {
        $this->name           = 'stprovideos';
        $this->tab            = 'front_office_features';
        $this->version        = '1.2.0';
        $this->author         = 'SUNNYTOO.COM';
        $this->need_instance  = 0;
        $this->bootstrap      = true;
        parent::__construct();

        $this->displayName = $this->l('Product videos');
        $this->description = $this->l('Using a fancy way to display self-hosted videos or youtube videos for products.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->_st_is_16      = Tools::version_compare(_PS_VERSION_, '1.7');


        self::$video_position = array(
            1 => array(
                'id' => 1,
                'name' => $this->l('Top left')
            ),
            2 => array(
                'id' => 2,
                'name' => $this->l('Top center')
            ),
            3 => array(
                'id' => 3,
                'name' => $this->l('Top right')
            ),
            4 => array(
                'id' => 4,
                'name' => $this->l('Center left')
            ),
            /*5 => array(
                'id' => 5,
                'name' => $this->l('Center center')
            ),*/
            6 => array(
                'id' => 6,
                'name' => $this->l('Center right')
            ),
            7 => array(
                'id' => 7,
                'name' => $this->l('Bottom left')
            ),
            8 => array(
                'id' => 8,
                'name' => $this->l('Bottom center')
            ),
            9 => array(
                'id' => 9,
                'name' => $this->l('Bottom right')
            ),
        );
        $_theme_name = Configuration::get($this->_prefix_st.'THEME_NAME');
        if($_theme_name)
            $this->theme_name = $_theme_name;
        else{
            if($this->_st_is_16){
                $this->theme_name = Tools::strtolower(_THEME_NAME_);
                if($this->theme_name=='panda')
                    $this->theme_name = 'panda1';
                if($this->theme_name=='transformer')
                    $this->theme_name = 'transformer3';
                $parent_theme_name = '';
            }else{
                $this->theme_name = Tools::strtolower(Context::getContext()->shop->theme->getName());
                $parent_theme_name = Tools::strtolower(Context::getContext()->shop->theme->get("parent",""));
            }
            if($parent_theme_name=='panda')
                $this->theme_name = 'panda';
            if($parent_theme_name=='transformer')
                $this->theme_name = 'transformer';
            if($parent_theme_name=='warehouse')
                $this->theme_name = 'warehouse';
            if($parent_theme_name=='alysum')
                $this->theme_name = 'alysum';
        }
        $this->_st_themes_17 = ($this->theme_name=='panda' || $this->theme_name=='transformer');
        $this->_st_themes_16 = ($this->theme_name=='panda1');
    }

    function install()
    {
        if (!parent::install()
            || !$this->installDB()
            || !$this->registerHook('actionProductDelete')
            || !$this->registerHook('displayHeader')
            || !Configuration::updateValue($this->_prefix_st.'AUTOPLAY', 1)
            || !Configuration::updateValue($this->_prefix_st.'CONTROLS', 1)
            || !Configuration::updateValue($this->_prefix_st.'CONTROLS_YOUTUBE', 0)
            || !Configuration::updateValue($this->_prefix_st.'WIDTH', '')
            || !Configuration::updateValue($this->_prefix_st.'HEIGHT', '')
            || !Configuration::updateValue($this->_prefix_st.'LOOP', 1)
            || !Configuration::updateValue($this->_prefix_st.'MUTED', 1)
            || !Configuration::updateValue($this->_prefix_st.'FLUID', 1)
            || !Configuration::updateValue($this->_prefix_st.'ASPECTRATIO', false)
            || !Configuration::updateValue($this->_prefix_st.'PLAYBACKRATES', true)
            || !Configuration::updateValue($this->_prefix_st.'HOW_TO_DISPLAY', 0)
            || !Configuration::updateValue($this->_prefix_st.'SKIN', 0)
            || !Configuration::updateValue($this->_prefix_st.'QUICK_VIEW', 0)

            || !Configuration::updateValue($this->_prefix_st.'BTN_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_BG_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_BG_OPACITY', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_HOVER_BG_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_HOVER_BG_OPACITY', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_ICON_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_HEIGHT', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_RADIUS', '')

            || !Configuration::updateValue($this->_prefix_st.'BG', '')
            || !Configuration::updateValue($this->_prefix_st.'BG_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'BG_OPACITY', '')
            || !Configuration::updateValue($this->_prefix_st.'CONTROL_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'CONTROL_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'PROGRESS_BAR_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'PROGRESS_BAR_OPACITY', '')
            || !Configuration::updateValue($this->_prefix_st.'PROGRESS_BAR_HIGHLIGHT_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'LOAD_PROGRESS_BAR_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'LOAD_PROGRESS_BAR_OPACITY', '')

            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_BG', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_OPACITY', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_ICON_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_POSITION', 8)
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_OFFSET_X', 0)
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_OFFSET_Y', 20)

            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_BG', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_OPACITY', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_ICON_SIZE', '')

            || !Configuration::updateValue($this->_prefix_st.'VIDEO_CONTAINER', '')
            || !Configuration::updateValue($this->_prefix_st.'DESC_CONTAINER', '')
            || !Configuration::updateValue($this->_prefix_st.'GALLERY_CONTAINER', '')
            || !Configuration::updateValue($this->_prefix_st.'THUMBNAIL_CONTAINER', '')
            || !Configuration::updateValue($this->_prefix_st.'VIDEO_SELECTOR', '')
            || !Configuration::updateValue($this->_prefix_st.'THUMBNAIL_SELECTOR', '')
            || !Configuration::updateValue($this->_prefix_st.'THUMBNAIL_HTML', '')
            || !Configuration::updateValue($this->_prefix_st.'GALLERY_IMAGE_TYPE', '')
            || !Configuration::updateValue($this->_prefix_st.'THUMBNAIL_IMAGE_TYPE', '')
            || !Configuration::updateValue($this->_prefix_st.'SLIDER', 0)
            || !Configuration::updateValue($this->_prefix_st.'THUMB_SLIDER', 0)
            || !Configuration::updateValue($this->_prefix_st.'Z_INDEX', 0)

            || !Configuration::updateValue($this->_prefix_st.'THUMBNAIL_WIDTH', 0)
            || !Configuration::updateValue($this->_prefix_st.'DESC_PLAYER_WIDTH', 0)

            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_BORDER_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_BORDER_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_BTN_BORDER_RADIUS', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_BORDER_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'BTN_BORDER_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_BORDER_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_BORDER_SIZE', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_ICON_BORDER_RADIUS', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_VIDEO_TEXT_COLOR', '')
            || !Configuration::updateValue($this->_prefix_st.'PLAY_VIDEO_TEXT_BG', '')

            || !Configuration::updateValue($this->_prefix_st.'THUMBNAIL_EVENT', 0)
            || !Configuration::updateValue($this->_prefix_st.'AUTO_CLOSE_AT_END', 1)

            || !Configuration::updateValue($this->_prefix_st.'CUSTOM_CSS', '')
            || !Configuration::updateValue($this->_prefix_st.'THEME_NAME', '')
            || !Configuration::updateValue($this->_prefix_st.'YOUTUBE_API', 0)


        )
            return false;

        $languages = Language::getLanguages();
        $play_video_text = array();
        foreach ($languages as $language){
            $play_video_text[$language['id_lang']] = '';
        }
        Configuration::updateValue($this->_prefix_st.'PLAY_VIDEO_TEXT', $play_video_text, true);
        // $this->_clearCache('stprovideos.tpl');
        if (!$this->_st_is_16){
            $this->registerHook('displayAdminProductsExtra');
            $this->registerHook('displayProductAdditionalInfo');
        }
        return true;
    }
    private function installDB()
    {
        $return = (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'st_pro_videos` (
              `id_st_pro_videos` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `id_product` int(10) unsigned NOT NULL DEFAULT 0,
              `id_shop` int(11) unsigned NOT NULL,
              PRIMARY KEY (`id_st_pro_videos`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

        $return &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'st_pro_video` (
              `id_st_pro_video` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `id_st_pro_videos` int(11) unsigned NOT NULL,
                `url` varchar(255) DEFAULT NULL,
                `thumbnail` varchar(255) DEFAULT NULL,
              `position` int(10) unsigned NOT NULL DEFAULT 0,
                `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
                `online_thumbnail`tinyint(1) unsigned NOT NULL DEFAULT 1,
                `loop`tinyint(1) unsigned NOT NULL DEFAULT 4,
                `muted`tinyint(1) unsigned NOT NULL DEFAULT 4,
                `autoplay`tinyint(1) unsigned NOT NULL DEFAULT 4,
              PRIMARY KEY (`id_st_pro_video`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

        $return &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'st_pro_video_yuyan` (
                `id_st_pro_video` INT UNSIGNED NOT NULL,
                `id_lang` int(10) unsigned NOT NULL ,
                PRIMARY KEY (`id_st_pro_video`, `id_lang`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

        return $return;
    }
    private function uninstallDB()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'st_pro_videos`,`'._DB_PREFIX_.'st_pro_video`,`'._DB_PREFIX_.'st_pro_video_yuyan`');
    }
    public function uninstall()
    {
        // $this->_clearCache('stprovideos.tpl');
        if (!parent::uninstall() ||
            !$this->uninstallDB())
            return false;
        return true;
    }

    public function getContent()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.6.0', '>=')) {
            $st_product_url = 'index.php?controller=AdminProducts&token='.Tools::getAdminTokenLite('AdminProducts').'&ajax=1&action=productsList&disableCombination=1';
        } else {
            $st_product_url = 'ajax_products_list.php?disableCombination=true';
        }
        Media::addJsDef(['st_product_url' => $st_product_url]);
        $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        $this->context->controller->addJS(($this->_path).'views/js/admin.js');
        $check_result = $this->_checkImageDir();
        /*if (Tools::getValue('ajax') && Tools::getValue('act') == 'saveProVideo' ) {
            $result = array(
                'r' => false,
                'm' => '',
            );
            $id_product = Tools::getValue('id_product');
            $url = Tools::getValue('url');
            $thumb = Tools::getValue('thumb');
           if ($id_product && StProVideosClass::saveProVideo($id_product, $url, $thumb)) {
                $result = array(
                    'r' => true,
                    'm' => '',
                );
           }
           die(json_encode($result));
        }*/

        $id_st_pro_videos = (int)Tools::getValue('id_st_pro_videos');
        $id_product = (int)Tools::getValue('id_product');
        $id_st_pro_video = (int)Tools::getValue('id_st_pro_video');
        if(Tools::getValue('act')=='delete_image' && $id_st_pro_videos)
        {
            $result = array(
                'r' => false,
                'm' => '',
                'd' => ''
            );
            $video = new StProVideosClass($id_st_pro_videos);
            $video->thumbnail = '';
            $result['r'] = $video->save();
            die(json_encode($result));
        }
        if (isset($_POST['save'.$this->name]) || isset($_POST['save'.$this->name.'AndStay']))
        {
            $error = array();
            $validate_id_product = $validate_id_st_pro_videos = 0;
            if($id_st_pro_videos && ($videos = new StProVideosClass($id_st_pro_videos)) && Validate::isLoadedObject($videos)){
                $validate_id_product = $videos->id_product;
                $validate_id_st_pro_videos = $id_st_pro_videos;
            }elseif($id_product){
                if($videos_data = StProVideosClass::getByProductId($id_product)){
                    $validate_id_st_pro_videos = $videos_data['id_st_pro_videos'];
                    $validate_id_product = $id_product;
                }else{
                    $videos = new StProVideosClass();
                    $videos->id_product = $id_product;
                    $videos->id_shop = (int)$this->context->shop->id;
                    if($videos->save()){
                        $validate_id_st_pro_videos = $videos->id;
                        $validate_id_product = $id_product;
                    }
                }
            }
            if(!$validate_id_st_pro_videos || !$validate_id_product)
                $error[] = $this->displayError($this->l('No validate prodcut id or pro video id.'));

            if ($id_st_pro_video)
                $video = new StProVideoClass($id_st_pro_video);
            else
                $video = new StProVideoClass();
            $video->copyFromPost();
            $video->id_st_pro_videos = $validate_id_st_pro_videos;

            $res = $this->stUploadImage('thumbnail');
            if (count($res['error'])) {
                $error = array_merge($error, $res['error']);
            } elseif($res['image']) {
                $video->thumbnail = $res['image'];
            }

            if (!$video->id_st_pro_videos) {
                $error[] = $this->displayError($this->l('Pro video id is required'));
            }
            if (!$video->url) {
                $error[] = $this->displayError($this->l('Video url is required'));
            }
            if (!count($error) && $video->validateFields(false) && $video->validateFieldsLang(false))
            {
                if($video->save())
                {
                    StProVideoYuyanClass::deleteByVideo($video->id);
                    $lang_arr = array();
                    $all_languages = Language::getLanguages(false);
                    foreach ($all_languages as $lang){
                        if(Tools::getValue('id_lang_'.$lang['id_lang']))
                            $lang_arr[] = $lang['id_lang'];
                    }
                    if(count($lang_arr) && count($lang_arr)<count($all_languages))
                        $res_yuyan = StProVideoYuyanClass::changeVideoYuyan($video->id,$lang_arr);
                    $this->_clearCache('*');
                    if(isset($_POST['save'.$this->name.'AndStay']))
                        Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&id_st_pro_video='.$video->id.'&conf='.($id_st_pro_videos?4:3).'&update'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
                    else
                        Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&id_st_pro_videos='.$video->id_st_pro_videos.'&view'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
                }
                else {
                    $this->_html .= $this->displayError($this->l('An error occurred during while').' '.($id_st_pro_video ? $this->l('updating') : $this->l('creation')));
                }
            } else {
                $this->_html .= count($error) ? implode('',$error) : $this->displayError($this->l('invalid value for field(s).'));
            }
        }
        if (isset($_POST['savesetting'.$this->name]))
        {
            if (isset($_POST['custom_css']) && $_POST['custom_css']) {
                $_POST['custom_css'] = str_replace('\\', '¤', $_POST['custom_css']);
            }
            $this->initSettingFormFields();
            foreach($this->fields_form as $form){
                foreach($form['form']['input'] as $field){
                    if(isset($field['validation']))
                    {
                        $ishtml = ($field['validation']=='isAnything') ? true : false;
                        $errors = array();
                        $value = Tools::getValue($field['name']);
                        if (isset($field['required']) && $field['required'] && $value==false && (string)$value != '0')
                                $errors[] = sprintf(Tools::displayError('Field "%s" is required.'), $field['label']);
                        elseif($value)
                        {
                            $field_validation = $field['validation'];
                            if (!Validate::$field_validation($value))
                                $errors[] = sprintf(Tools::displayError('Field "%s" is invalid.'), $field['label']);
                        }
                        // Set default value
                        if ($value === false && isset($field['default_value']))
                            $value = $field['default_value'];

                        if(count($errors))
                        {
                            $this->validation_errors = array_merge($this->validation_errors, $errors);
                        }
                        elseif($value==false)
                        {
                            switch($field['validation'])
                            {
                                case 'isUnsignedId':
                                case 'isUnsignedInt':
                                case 'isInt':
                                case 'isBool':
                                    $value = 0;
                                break;
                                default:
                                    $value = '';
                                break;
                            }
                            Configuration::updateValue($this->_prefix_st.strtoupper($field['name']), $value);
                        }
                        else
                            Configuration::updateValue($this->_prefix_st.strtoupper($field['name']), $value, $ishtml);
                    }
                }
            }
            $this->updatePlayVideoText();

            if(count($this->validation_errors))
                $this->_html .= $this->displayError(implode('<br/>',$this->validation_errors));
            else
            {
                $this->_clearCache('*');
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&conf=4&setting'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
            }
        }
        if (Tools::isSubmit('update'.$this->name) && $id_st_pro_video)
        {
            $helper = $this->initForm();
            return $this->_html.$helper->generateForm($this->fields_form);
        } elseif (Tools::isSubmit('delete'.$this->name) && ($id_st_pro_videos || $id_st_pro_video)) {
            if($id_st_pro_videos){
                $videos = new StProVideosClass($id_st_pro_videos);
                if(Validate::isLoadedObject($videos) && $videos->delete())
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
            }
            if($id_st_pro_video){
                $video = new StProVideoClass($id_st_pro_video);
                $current_product_id = StProVideosClass::getProductIdByVideosId($video->id_st_pro_videos);
                if(Validate::isLoadedObject($video) && $video->delete())
                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&id_product='.$current_product_id.'&view'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
            }
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        } elseif (Tools::isSubmit('product'.$this->name) && ($id_product = Tools::getValue('id_product'))) {
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&id_product='.$id_product.'&view'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        } elseif (Tools::isSubmit('view'.$this->name) && (($id_product = Tools::getValue('id_product')) || $id_st_pro_videos)) {
            $list_data = array();
            if($id_st_pro_videos && ($videos = new StProVideosClass($id_st_pro_videos)) && Validate::isLoadedObject($videos)){
                $id_product = $videos->id_product;
                $list_data = StProVideoClass::getAll($id_st_pro_videos);
            }

            $product = new Product((int)$id_product, false, $this->context->language->id);
            if(Validate::isLoadedObject($product)){
                $this->_html .= '<div class="st_pro_video_current_product panel">'.$this->l('Edit/add videos for').' <a href="'.$product->getLink().'">'.$product->id.'-'.$product->name.'['.$product->reference.']'.'</a></div>';
                if((!$list_data || !count($list_data)) && ($temp_id_st_pro_videos = StProVideosClass::getVideosIdByProductId($id_product)))
                    $list_data = StProVideoClass::getAll($temp_id_st_pro_videos);
            }else{
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
            }

            $helper = $this->initVideoList();
            $helper_video_form = $this->initForm();
            return $this->_html.$helper->generateList($list_data, $this->fields_list).$helper_video_form->generateForm($this->fields_form);
        } elseif (Tools::isSubmit('setting'.$this->name)) {
            $this->initSettingFormFields();
            $helper = $this->initSettingForm();
            return $this->_html.$helper->generateForm($this->fields_form);
        } else {
            $helper_start_form = $this->initStartForm();
            $helper = $this->initList();
            return $this->_html.$helper_start_form->generateForm($this->fields_form).$helper->generateList(StProVideosClass::getAll((int)$this->context->language->id), $this->fields_list);
        }
    }
    public function updatePlayVideoText() {
        $languages = Language::getLanguages();
        $play_video_text = array();
        $defaultLanguage = new Language((int)(Configuration::get('PS_LANG_DEFAULT')));
        foreach ($languages as $language){
            $play_video_text[$language['id_lang']] = Tools::getValue('play_video_text_' . $language['id_lang']) ?: Tools::getValue('play_video_text_'.$defaultLanguage->id);
        }
        Configuration::updateValue($this->_prefix_st.'PLAY_VIDEO_TEXT', $play_video_text, true);
        return true;
    }
    protected function stUploadImage($item)
    {
        $result = array(
            'error' => array(),
            'image' => '',
            'thumb' => '',
        );
        if (isset($_FILES[$item]) && isset($_FILES[$item]['tmp_name']) && !empty($_FILES[$item]['tmp_name']))
        {
            $type = strtolower(substr(strrchr($_FILES[$item]['name'], '.'), 1));
            $name = str_replace(strrchr($_FILES[$item]['name'], '.'), '', $_FILES[$item]['name']);
            $imagesize = array();
            $imagesize = @getimagesize($_FILES[$item]['tmp_name']);
            if (!empty($imagesize) &&
                in_array(strtolower(substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) &&
                in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
            {
                $c_name = $name ? Tools::str2url($name) : sha1(microtime());
                if ($upload_error = ImageManager::validateUpload($_FILES[$item]))
                    $result['error'][] = $upload_error;
                elseif (!move_uploaded_file($_FILES[$item]['tmp_name'], _PS_UPLOAD_DIR_.$this->name.'/'.$c_name.'.'.$type))
                    $result['error'][] = $this->displayError($this->l('An error occurred during the image upload.'));

                if(!count($result['error']))
                {
                    $result['image'] = $this->name.'/'.$c_name.'.'.$type;
                    $result['width'] = $imagesize[0];
                    $result['height'] = $imagesize[1];
                }
                return $result;
            }
        }
        else
            return $result;
    }
    public static function displayLanguages($id_st_pro_video, $tr)
    {
        $result = '';
        $lang_arr = StProVideoYuyanClass::getByVideo($tr['id_st_pro_video']);
        if(!$lang_arr){
            $module = new StProVideos();
            $result = $module->l('All');
        }
        else{
            $lang_ids = array();
            foreach ($lang_arr as $value) {
                $lang_ids[] = $value['id_lang'];
            }
            $all_languages = Language::getLanguages(false);
            foreach ($all_languages as $lang) {
                $result .= in_array($lang['id_lang'],$lang_ids) ? $lang['name'].'<br/>' : '';
            }
        }
        return $result;
    }
    public static function displayThumbnail($thumbnail, $tr)
    {
        return '<img src="'.$thumbnail.'" width="70">';
    }
    public static function displayAutoplay($autoplay, $tr)
    {
        $module = new StProVideos();
        $use_default_text = '';
        if($autoplay==4){
            $use_default_text .= $module->l('Use default value').' - ';
            $autoplay = Configuration::get($module->_prefix_st.'AUTOPLAY');
        }

        return $use_default_text.($autoplay ? $module->l('YES') : $module->l('NO'));
    }
    protected function initVideoList()
    {
        $this->fields_list = array(
            'id_st_pro_video' => array(
                'title' => $this->l('Id'),
                'width' => 120,
                'type' => 'text',
                'search' => false,
                'orderby' => false
            ),
            'url' => array(
                'title' => $this->l('Video url'),
                'type' => 'text',
                'width' => 200,
                'search' => false,
                'orderby' => false
            ),
            'thumbnail' => array(
                'title' => $this->l('Thumbnail'),
                'width' => 100,
                'type' => 'text',
                'callback' => 'displayThumbnail',
                'callback_object' => 'StProVideos',
                'search' => false,
                'orderby' => false
            ),
            'autoplay' => array(
                'title' => $this->l('Autoplay'),
                'width' => 100,
                'type' => 'text',
                'callback' => 'displayAutoplay',
                'callback_object' => 'StProVideos',
                'search' => false,
                'orderby' => false
            ),
            'online_thumbnail' => array(
                'title' => $this->l('Languages'),
                'width' => 200,
                'type' => 'text',
                'callback' => 'displayLanguages',
                'callback_object' => 'StProVideos',
                'search' => false,
                'orderby' => false
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'align' => 'center',
                'class'=>'fixed-width-xl',
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ),
            'active' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
                'type' => 'bool',
                'width' => 25,
                'search' => false,
                'orderby' => false
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_st_pro_video';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->imageType = 'jpg';
        $helper->toolbar_btn['back'] =  array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to product list')
        );

        $helper->title = $this->l('Videos');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper;
    }
    protected function initList()
    {
        $this->fields_list = array(
            'id_st_pro_videos' => array(
                'title' => $this->l('Id'),
                'width' => 120,
                'type' => 'text',
                'search' => false,
                'orderby' => false
            ),
            'name' => array(
                'title' => $this->l('Product name'),
                'width' => 300,
                'type' => 'text',
                'search' => false,
                'orderby' => false
            ),
            'id_product' => array(
                'title' => $this->l('Product ID'),
                'width' => 120,
                'type' => 'text',
                'search' => false,
                'orderby' => false
            ),
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_st_pro_videos';
        $helper->actions = array('view', 'delete');
        $helper->show_toolbar = true;
        $helper->imageType = 'jpg';
        $helper->toolbar_btn['edit'] =  array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&setting'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Global settings'),
        );
        /*$helper->toolbar_btn['new'] =  array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add a video'),
        );*/

        $helper->title = $this->l('Products');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper;
    }
    protected function initStartForm()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Select a product to add new videos or edit existed videos'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                'products' => array(
                    'type' => 'text',
                    'label' => $this->l('Enter product name:'),
                    'name' => 'products',
                    'autocomplete' => false,
                    'class' => 'fixed-width-xxl',
                    'desc' => '<ul id="curr_products"></ul>',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_product',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Continue'),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $helper->module = $this;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'product'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array('products'=>'','id_product'=>''),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper;
    }

    protected function initForm()
    {
        $lang_arr = array();
        $all_languages = Language::getLanguages(false);
        foreach ($all_languages as $lang){
            $lang_arr[] = array(
                'id' => $lang['id_lang'],
                'name' => $lang['name'],
                'val' => $lang['id_lang'],
            );
        }
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Add new video'),
                'icon' => 'icon-cogs'
            ),
            'description' => '',
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_product',
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'id_st_pro_videos',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Video url:'),
                    'name' => 'url',
                    'default_value' => '',
                    'class' => 'fixed-width-xxl',
                    'desc' => array(
                        $this->l('Url of a .mp4 file or a youtube url.'),
                        sprintf($this->l('For more info, please refer to the documenation section on %sthis page%s.'), '<a href="https://www.sunnytoo.com/product/prestashop-product-videos-module-showing-videos-on-the-main-image-gallery" target="_blank">','</a>'),
                        '<div id="youtube_thumbnail"></div>'
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use youtube thumbnail if it is a youtube video.'),
                    'name' => 'online_thumbnail',
                    'is_bool' => true,
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'online_thumbnail_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'online_thumbnail_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                'thumbnail' => array(
                    'type' => 'file',
                    'label' => $this->l('Custom thumbnail:'),
                    'name' => 'thumbnail',
                    'desc' => '',
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Language'),
                    'name' => 'id_lang',
                    'lang' => true,
                    'values' => array(
                        'query' => $lang_arr,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Status:'),
                    'name' => 'active',
                    'is_bool' => true,
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Position:'),
                    'name' => 'position',
                    'default_value' => 0,
                    'class' => 'fixed-width-sm'
                ),
                'autoplay' => array(
                    'type' => 'radio',
                    'label' => $this->l('Autoplay:'),
                    'name' => 'autoplay',
                    'is_bool' => false,
                    'default_value' => 4,
                    'values' => array(
                        array(
                            'id' => 'autoplay_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'autoplay_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                        array(
                            'id' => 'autoplay_default',
                            'value' => 4,
                            'label' => $this->l('Use default'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                    'desc' => array(
                            $this->l('This option is NOT a guarantee that your video will autoplay.'),
                            $this->l('This option would not take effect if videos are put to the end of thumbnail gallery.'),
                            $this->l('Check the description of this module on sunnytoo.com for more info.'),
                        ),
                ),
                'loop' => array(
                    'type' => 'radio',
                    'label' => $this->l('Loop:'),
                    'name' => 'loop',
                    'default_value' => 4,
                    'values' => array(
                        array(
                            'id' => 'loop_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'loop_off',
                            'value' => 2,
                            'label' => $this->l('No'),
                        ),
                        /*array(
                            'id' => 'loop_no',
                            'value' => 3,
                            'label' => $this->l('No, close player after a video ends'),
                        ),*/
                        array(
                            'id' => 'loop_default',
                            'value' => 4,
                            'label' => $this->l('Use default'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Muted:'),
                    'name' => 'muted',
                    'is_bool' => true,
                    'default_value' => 4,
                    'values' => array(
                        array(
                            'id' => 'muted_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'muted_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                        array(
                            'id' => 'muted_default',
                            'value' => 4,
                            'label' => $this->l('Use default'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
            ),
            'buttons' => array(
                array(
                    'type' => 'submit',
                    'title'=> $this->l('Save all'),
                    'icon' => 'process-icon-save',
                    'class'=> 'pull-right'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save and stay'),
                'stay' => true
            ),
        );
        $id_st_pro_videos = (int)Tools::getValue('id_st_pro_videos');
        $id_product = (int)Tools::getValue('id_product');
        $id_st_pro_video = (int)Tools::getValue('id_st_pro_video');


        $video = new StProVideoClass($id_st_pro_video);
        if(Validate::isLoadedObject($video)){
            $id_st_pro_videos = $video->id_st_pro_videos;
            if ($video->thumbnail) {
                StProVideoClass::fetchMediaServer($video->thumbnail);
                $this->fields_form[0]['form']['input']['thumbnail']['image'] = '<img class="st_thumb_nail" src="'.$video->thumbnail.'" /><p>
                <a class="btn btn-default st_delete_image" data-id='.$video->id.' href="javascript:;">
                <i class="icon-trash"></i> '.$this->l('Delete').'</a></p>
                ';
            }
            $this->fields_form[0]['form']['input'][] = array(
                'type' => 'html',
                'id' => 'a_cancel',
                'label' => '',
                'name' => '<a class="btn btn-default btn-block fixed-width-md" href="'.AdminController::$currentIndex.'&configure='.$this->name.'&id_st_pro_videos='.$id_st_pro_videos.'&view'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><i class="icon-arrow-left"></i> '.$this->l('Back to video list').'</a>',
            );
            $this->fields_form[0]['form']['legend']['title'] = $this->l('Edit video');
        }else{
            unset($this->fields_form[0]['form']['submit']);
        }

        $pro_videos = new StProVideosClass($id_st_pro_videos);
        if(Validate::isLoadedObject($pro_videos)){
            $id_product = $pro_videos->id_product;
        }
        $product = new Product((int)$id_product, false, Context::getContext()->language->id);
        if(Validate::isLoadedObject($product)){
            $this->fields_form[0]['form']['description'] = $product->id.'-'.$product->name.'['.$product->reference.']';
        }else{
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        }

        if($this->_st_themes_17){
            $images_size = Image::getSize(Configuration::get('STSN_THUMB_IMAGE_TYPE') ? Configuration::get('STSN_THUMB_IMAGE_TYPE') : 'cart_default');
        }elseif ($this->_st_themes_16) {
            $images_size = Image::getSize('small_default');
        }elseif ($this->_st_is_16) {
            $images_size = Image::getSize('cart_default');
        }else{
            $images_size = Image::getSize('home_default');
        }
        if($images_size)
            $this->fields_form[0]['form']['input']['thumbnail']['desc'] = $this->l('Recommend size is').' '.$images_size['width'].'x'.$images_size['height'];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->id = (int)$video->id;
        $helper->module = $this;
        $helper->table =  'st_pro_video';
        $helper->identifier = 'id_st_pro_video';
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->submit_action = 'save'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getFieldsValueSt($video),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $lang_all = true;
        $lang_selected = array();
        if(Validate::isLoadedObject($video)){
            $lang_arr = StProVideoYuyanClass::getByVideo($video->id);
            foreach ($lang_arr as $lang) {
               $lang_selected[] = $lang['id_lang'];
            }
            $lang_all = count($lang_selected)==0 || count($lang_selected)==count($all_languages);
        }
        foreach ($all_languages as $lang){
             $helper->tpl_vars['fields_value']['id_lang_'.$lang['id_lang']] = ($lang_all || in_array($lang['id_lang'], $lang_selected)) ? 1 : 0;
        }
        $helper->tpl_vars['fields_value']['id_st_pro_videos'] = $id_st_pro_videos;
        $helper->tpl_vars['fields_value']['id_product'] = $id_product;

        return $helper;
    }

    public function initSettingFormFields()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Global settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->l('How to display play buttons:'),
                    'name' => 'how_to_display',
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'how_to_display_0',
                            'value' =>0,
                            'label' => $this->l('Display a play button on the main image section, a player will show out when the user clicks on the button. This module will only show one video per product when in this option. This module will be albe to be compatible with all themes with this option.')),
                        array(
                            'id' => 'how_to_display_2',
                            'value' => 2,
                            'label' => $this->l('Display a play button on the first image and its thumbnail. This module will only show one video per product when in this option. This module will be albe to be compatible with all themes with this option.')),
                        array(
                            'id' => 'how_to_display_7',
                            'value' => 7,
                            'label' => $this->l('Before the product description.')),
                        array(
                            'id' => 'how_to_display_8',
                            'value' => 8,
                            'label' => $this->l('After the product description.')),
                        array(
                            'id' => 'how_to_display_1',
                            'value' => 1,
                            'label' => $this->l('Add video thumbnails to the beginning of product thumbnail gallery. If a vidoe does not have thumbnail, then product cover image will be used.')),
                        array(
                            'id' => 'how_to_display_3',
                            'value' => 3,
                            'label' => $this->l('Add video thumbnails to the end of product thumbnail gallery. If a vidoe does not have thumbnail, then product cover image will be used.')),
                        array(
                            'id' => 'how_to_display_4',
                            'value' => 4,
                            'label' => $this->l('Add play icons to the beginning of product thumbnail gallery. Custom video thumbnails will be shown out.')),
                        array(
                            'id' => 'how_to_display_6',
                            'value' => 6,
                            'label' => $this->l('Add play icons to the end of product thumbnail gallery. Custom video thumbnails will be shown out.')),
                    ),
                    'validation' => 'isUnsignedInt',
                    'desc' => $this->l('The last four options are not working for all themes.'),
                ),
                'autoplay' => array(
                    'type' => 'switch',
                    'label' => $this->l('Autoplay:'),
                    'name' => 'autoplay',
                    'is_bool' => false,
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'autoplay_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'autoplay_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        )
                    ),
                    'validation' => 'isUnsignedInt',
                    'desc' => array(
                            $this->l('This option is NOT a guarantee that your video will autoplay.'),
                            $this->l('This option would not take effect if videos are put to the end of thumbnail gallery.'),
                            $this->l('Check the description of this module on sunnytoo.com for more info.'),
                        ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show controls:'),
                    'name' => 'controls',
                    'is_bool' => true,
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'controls_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'controls_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        )
                    ),
                    'validation' => 'isUnsignedInt',
                    'desc' => $this->l('It is highly recommended to keep this option ON.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use Youtube native controls for Youtube videos:'),
                    'name' => 'controls_youtube',
                    'is_bool' => true,
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'controls_youtube_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'controls_youtube_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        )
                    ),
                    'validation' => 'isUnsignedInt',
                    'desc' => $this->l('Setting for controls will not work for Youtube native controls.'),
                ),
                /*array(
                    'type' => 'text',
                    'label' => $this->l('Player width:'),
                    'name' => 'width',
                    'class' => 'fixed-width-xxl',
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Player height:'),
                    'name' => 'height',
                    'class' => 'fixed-width-xxl',
                    'validation' => 'isUnsignedInt',
                ),*/
                'loop' => array(
                    'type' => 'radio',
                    'label' => $this->l('Loop:'),
                    'name' => 'loop',
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'loop_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'loop_off',
                            'value' => 2,
                            'label' => $this->l('No'),
                        ),
                        /*array(
                            'id' => 'loop_off',
                            'value' => 3,
                            'label' => $this->l('No, close player after a video ends'),
                        ),*/
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Auto-close at the end of the video.:'),
                    'name' => 'auto_close_at_end',
                    'is_bool' => true,
                    'default_value' => 1,
                    'values' => array(
                        array(
                            'id' => 'auto_close_at_end_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'auto_close_at_end_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        )
                    ),
                    'validation' => 'isUnsignedInt',
                    'desc' => array(
                        $this->l('This setting is for the first two ways of displaying videos, it doesn\'t work for displaying vidoes in slider.'),
                        $this->l('This setting doesn\'t work for looped videos.'),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Muted:'),
                    'name' => 'muted',
                    'is_bool' => true,
                    'default_value' => 0,
                    'values' => array(
                        array(
                            'id' => 'muted_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'muted_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        )
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Skin:'),
                    'name' => 'skin',
                    'options' => array(
                        'query' => array(
                            array('id' => 1, 'name' => $this->l('Sublime')),
                            array('id' => 2, 'name' => $this->l('OGZ')),
                            array('id' => 3, 'name' => $this->l('Youtube')),
                            array('id' => 4, 'name' => $this->l('Facebook')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->l('Default'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display vidoes on the Quick view:'),
                    'name' => 'quick_view',
                    'is_bool' => true,
                    'default_value' => 0,
                    'values' => array(
                        array(
                            'id' => 'quick_view_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'quick_view_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        )
                    ),
                    'validation' => 'isUnsignedInt',
                    /*'desc' => array(
                        $this->l('This works for Transformer v4 and Panda v2 only.'),
                        $this->l('Vidoes will show out slightly later, because they are loaded via ajax in the quick view window.'),
                    ),*/
                ),
            ),
            'submit' => array(
                'title' => $this->l('   Save all  ')
            ),
        );
        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Play button settings'),
                'icon' => 'icon-cogs'
            ),
            'description' => $this->l('This button is used to show players out.'),
            'input' => array(
                array(
                    'type' => 'color',
                    'label' => $this->l('Color:'),
                    'name' => 'play_btn_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Background:'),
                    'name' => 'play_btn_bg',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Background opacity:'),
                    'name' => 'play_btn_opacity',
                    'validation' => 'isFloat',
                    'class' => 'fixed-width-lg',
                    'desc' => $this->l('From 0.0 (fully transparent) to 1.0 (fully opaque). Opacity will not takes effect if no background color.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Size:'),
                    'name' => 'play_btn_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Play icon size:'),
                    'name' => 'play_btn_icon_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Play button position when it is on the main image gallery:'),
                    'name' => 'play_btn_position',
                    'options' => array(
                        'query' => self::$video_position,
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->l('Middle center'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Play button offset X:'),
                    'name' => 'play_btn_offset_x',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Play button offset Y:'),
                    'name' => 'play_btn_offset_y',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Border color:'),
                    'name' => 'play_btn_border_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Border size:'),
                    'name' => 'play_btn_border_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Border radius:'),
                    'name' => 'play_btn_border_radius',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('"Play video" text:'),
                    'name' => 'play_video_text',
                    'size' => 64,
                    'lang' => true,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('"Play video" color:'),
                    'name' => 'play_video_text_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('"Play video" background:'),
                    'name' => 'play_video_text_bg',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
            ),
            'submit' => array(
                'title' => $this->l('   Save all  ')
            ),
        );
        $this->fields_form[2]['form'] = array(
            'legend' => array(
                'title' => $this->l('Player\'s big play button settings'),
                'icon' => 'icon-cogs'
            ),
            'description' => $this->l('This button is used to play videos, this button will not show out if autoplay is enbled.'),
            'input' => array(
                array(
                    'type' => 'color',
                    'label' => $this->l('Color:'),
                    'name' => 'btn_color',
                    'size' => 33,
                    'validation' => 'isColor',
                    'desc' => $this->l('Big play button will not show out when autoplay is enabled.'),
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Background color:'),
                    'name' => 'btn_bg_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Background opacity:'),
                    'name' => 'btn_bg_opacity',
                    'validation' => 'isFloat',
                    'class' => 'fixed-width-lg',
                    'desc' => $this->l('From 0.0 (fully transparent) to 1.0 (fully opaque). Opacity will not takes effect if no background color.'),
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Hover background color:'),
                    'name' => 'btn_hover_bg_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Hover background opacity:'),
                    'name' => 'btn_hover_bg_opacity',
                    'validation' => 'isFloat',
                    'class' => 'fixed-width-lg',
                    'desc' => $this->l('From 0.0 (fully transparent) to 1.0 (fully opaque). Opacity will not takes effect if no background color.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Size:'),
                    'name' => 'btn_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Icon size:'),
                    'name' => 'btn_icon_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Height:'),
                    'name' => 'btn_height',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Big play button radius:'),
                    'name' => 'btn_radius',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Border color:'),
                    'name' => 'btn_border_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Border size:'),
                    'name' => 'btn_border_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
            ),
            'submit' => array(
                'title' => $this->l('   Save all  ')
            ),
        );
        $this->fields_form[5]['form'] = array(
            'legend' => array(
                'title' => $this->l('Play icon on video thumbnails'),
                'icon' => 'icon-cogs'
            ),
            'description' => $this->l('Settings here are for the play icon on video thumbnails.'),
            'input' => array(
                array(
                    'type' => 'color',
                    'label' => $this->l('Color:'),
                    'name' => 'play_icon_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Background:'),
                    'name' => 'play_icon_bg',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Background opacity:'),
                    'name' => 'play_icon_opacity',
                    'validation' => 'isFloat',
                    'class' => 'fixed-width-lg',
                    'desc' => $this->l('From 0.0 (fully transparent) to 1.0 (fully opaque). Opacity will not takes effect if no background color.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Size:'),
                    'name' => 'play_icon_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Icon size:'),
                    'name' => 'play_icon_icon_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Border color:'),
                    'name' => 'play_icon_border_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Border size:'),
                    'name' => 'play_icon_border_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Border radius:'),
                    'name' => 'play_icon_border_radius',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
            ),
            'submit' => array(
                'title' => $this->l('   Save all  ')
            ),
        );
        $this->fields_form[3]['form'] = array(
            'legend' => array(
                'title' => $this->l('Player settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'color',
                    'label' => $this->l('Background color:'),
                    'name' => 'bg',
                    'size' => 33,
                    'validation' => 'isColor',
                    'desc' => $this->l('This setting dose not work for YouTube vidoes.'),
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Control background color:'),
                    'name' => 'bg_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Control background opacity:'),
                    'name' => 'bg_opacity',
                    'validation' => 'isFloat',
                    'class' => 'fixed-width-lg',
                    'desc' => $this->l('From 0.0 (fully transparent) to 1.0 (fully opaque). Opacity will not takes effect if no background color.'),
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Control buttons color:'),
                    'name' => 'control_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Control buttons size:'),
                    'name' => 'control_size',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Progress bar color:'),
                    'name' => 'progress_bar_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Progress bar opacity:'),
                    'name' => 'progress_bar_opacity',
                    'validation' => 'isFloat',
                    'class' => 'fixed-width-lg',
                    'desc' => $this->l('From 0.0 (fully transparent) to 1.0 (fully opaque). Opacity will not takes effect if no background color.'),
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Play progress bar color:'),
                    'name' => 'progress_bar_highlight_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Load progress bar color:'),
                    'name' => 'load_progress_bar_color',
                    'size' => 33,
                    'validation' => 'isColor',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Load progress bar opacity:'),
                    'name' => 'load_progress_bar_opacity',
                    'validation' => 'isFloat',
                    'class' => 'fixed-width-lg',
                    'desc' => $this->l('From 0.0 (fully transparent) to 1.0 (fully opaque). Opacity will not takes effect if no background color.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Z-index:'),
                    'name' => 'z_index',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Custom CSS Code:'),
                    'name' => 'custom_css',
                    'cols' => 80,
                    'rows' => 20,
                    'validation' => 'isAnything',
                ),
            ),
            'submit' => array(
                'title' => $this->l('   Save all  ')
            ),
        );
        $this->fields_form[4]['form'] = array(
            'legend' => array(
                'title' => $this->l('Advanced settings'),
                'icon' => 'icon-cogs'
            ),
            'description' => $this->l('Do not change settings in this section unless you know what you are doing. If this module does not work on your site, then contact ST-themes, we will guide you to do changes in this section to make the module work.'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Main image container:'),
                    'name' => 'video_container',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Gallery container:'),
                    'name' => 'gallery_container',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Gallery item selector:'),
                    'name' => 'video_selector',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Thumbnail container:'),
                    'name' => 'thumbnail_container',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Thumbnail item selector:'),
                    'name' => 'thumbnail_selector',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Thumbnail template:'),
                    'name' => 'thumbnail_html',
                    'cols' => 80,
                    'rows' => 10,
                    'validation' => 'isAnything',
                    'desc' => $this->l(' Turn off the "Use HTMLPurifier Library" setting on the Preferences > General page first.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Thumbnail width:'),
                    'name' => 'thumbnail_width',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Slider type:'),
                    'name' => 'slider',
                    'options' => array(
                        'query' => array(
                            array('id' => 1, 'name' => $this->l('Swiper')),
                            array('id' => 2, 'name' => $this->l('Owl carrousel')),
                            array('id' => 3, 'name' => $this->l('Slick slider')),
                            array('id' => 4, 'name' => $this->l('Owl carrousel 1')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->l('Default - None'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Thumbnail slider type:'),
                    'name' => 'thumb_slider',
                    'options' => array(
                        'query' => array(
                            array('id' => 1, 'name' => $this->l('Swiper')),
                            array('id' => 2, 'name' => $this->l('Owl carrousel 2')),
                            array('id' => 3, 'name' => $this->l('Slick slider')),
                            array('id' => 4, 'name' => $this->l('Owl carrousel 1')),
                            array('id' => 5, 'name' => $this->l('Serial Scroll')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->l('Default - Scrollbox'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                'gallery_image_type'=>array(
                    'type' => 'select',
                    'label' => $this->l('Gallery image type:'),
                    'name' => 'gallery_image_type',
                    'default_value' => 'medium_default',
                    'options' => array(
                        'query' => array(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'validation' => 'isGenericName',
                ),
                'thumbnail_image_type'=>array(
                    'type' => 'select',
                    'label' => $this->l('Thumbnail image type:'),
                    'name' => 'thumbnail_image_type',
                    'default_value' => 'cart_default',
                    'options' => array(
                        'query' => array(),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'validation' => 'isGenericName',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Event for thumbnails:'),
                    'name' => 'thumbnail_event',
                    'options' => array(
                        'query' => array(
                            array('id' => 1, 'name' => $this->l('Click')),
                            array('id' => 2, 'name' => $this->l('Hover')),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->l('Use default'),
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product description container:'),
                    'name' => 'desc_container',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('The width of video player when in the product description:'),
                    'name' => 'desc_player_width',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Theme name:'),
                    'name' => 'theme_name',
                    'default_value' => 0,
                    'values' => array(
                        array(
                            'id' => 'theme_name_on',
                            'value' => '',
                            'label' => $this->l('Auto detect'),
                        ),
                        array(
                            'id' => 'theme_name_panda',
                            'value' => 'panda',
                            'label' => $this->l('Panda theme v2'),
                        ),
                        array(
                            'id' => 'theme_name_transformer',
                            'value' => 'transformer',
                            'label' => $this->l('Transformer theme v2'),
                        ),
                        array(
                            'id' => 'theme_name_panda1',
                            'value' => 'panda1',
                            'label' => $this->l('Panda theme v1 for PS1.6 and panda theme v1 for TB'),
                        ),
                        array(
                            'id' => 'theme_name_transformer3',
                            'value' => 'transformer3',
                            'label' => $this->l('Transformer theme v3'),
                        ),
                        array(
                            'id' => 'theme_name_classic',
                            'value' => 'classic',
                            'label' => $this->l('PrestaShop 1.7 classic'),
                        ),
                        array(
                            'id' => 'theme_name_bootstrap',
                            'value' => 'default-bootstrap',
                            'label' => $this->l('PrestaShop 1.6 bootstrap'),
                        ),
                        array(
                            'id' => 'theme_name_warehouse',
                            'value' => 'warehouse',
                            'label' => $this->l('Warehouse'),
                        ),
                        array(
                            'id' => 'theme_name_alysum',
                            'value' => 'alysum',
                            'label' => $this->l('Alysum'),
                        ),
                    ),
                    'validation' => 'isAnything',
                    'desc' => $this->l('If you are using Panda theme or Transformer theme and you changed theme name without using child theme feature, then you need to set this setting.'),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Stop loading YouTube api:'),
                    'name' => 'youtube_api',
                    'default_value' => 0,
                    'values' => array(
                        array(
                            'id' => 'disable_1',
                            'value' => 1,
                            'label' => $this->l('YES')),
                        array(
                            'id' => 'disable_0',
                            'value' => 0,
                            'label' => $this->l('NO')),
                    ),
                    'validation' => 'isUnsignedInt',
                    'desc' => $this->l('If you don\'t use any youtube videos, then you can enable this option to stop loading YouTube API.'),
                ),
            ),
            'submit' => array(
                'title' => $this->l('   Save all  ')
            ),
        );

        $this->fields_form[0]['form']['input'][] = array(
            'type' => 'html',
            'id' => 'a_cancel',
            'label' => '',
            'name' => '<a class="btn btn-default btn-block fixed-width-md" href="'.AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><i class="icon-arrow-left"></i> '.$this->l('Back to list').'</a>',
        );

        $image_types_arr = array(
            array('id' => '', 'name' => $this->l('--')),
        );
        $imagesTypes = ImageType::getImagesTypes('products');
        foreach ($imagesTypes as $k=>$imageType) {
            if(Tools::substr($imageType['name'],-3)=='_2x')
                continue;
            $image_types_arr[] = array('id' => $imageType['name'], 'name' => $imageType['name'].'('.$imageType['width'].'x'.$imageType['height'].')');
        }
        $this->fields_form[4]['form']['input']['gallery_image_type']['options']['query'] = $image_types_arr;
        $this->fields_form[4]['form']['input']['thumbnail_image_type']['options']['query'] = $image_types_arr;
    }
    protected function initSettingForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'savesetting'.$this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper;
    }
    public function hookDisplayAdminProductsExtra($params)
    {
        /*$id_product = $params['id_product'];
        if($id_product) {
            $st_pro_video = StProVideosClass::getProVideo($id_product);
        }
        $this->smarty->assign(array(
            'st_pro_video_url' => $st_pro_video ? $st_pro_video['url'] : '',
            'st_pro_video_thumbnail' => $st_pro_video ? $st_pro_video['thumbnail'] : '',
            'id_product' => $id_product,
            'current_url' => 'index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
        ));*/
        $id_product = $params['id_product'];
        $this->smarty->assign(array(
            'tiaozhuan_url' => $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&id_product='.$id_product.'&view'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
        ));
        return $this->display(__FILE__, 'views/templates/admin/stprovideos.tpl');
    }

    public static function getYoutubeId($url) {
        preg_match("/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/", $url,$match);
        return ($match && Tools::strlen($match[1]) == 11) ? $match[1] : false;
    }
    public function getSelector($key){
        return array_key_exists($this->theme_name, self::$selectors[$key]) ? self::$selectors[$key][$this->theme_name] : self::$selectors[$key]['classic'];
    }

    public function getVideosByProduct($product)
    {
        if(Validate::isLoadedObject($product))
        {
            $thumbnail_html = Configuration::get($this->_prefix_st.'THUMBNAIL_HTML');
            $thumbnail_html = $thumbnail_html?:$this->getSelector('thumbnail_html');
            $gallery_image_type = Configuration::get($this->_prefix_st.'GALLERY_IMAGE_TYPE');
            $thumbnail_image_type = Configuration::get($this->_prefix_st.'THUMBNAIL_IMAGE_TYPE');
            $gallery_image_type = $gallery_image_type?:$this->getSelector('gallery_image_type');
            $thumbnail_image_type = $thumbnail_image_type?:$this->getSelector('thumbnail_image_type');
            $how_to_display = (int)Configuration::get($this->_prefix_st.'HOW_TO_DISPLAY');

            $cover = $product->getCover($product->id);
            if($cover){
                $gallery_image_url = Context::getContext()->link->getImageLink($product->link_rewrite, $cover['id_image'], $gallery_image_type);
                $thumbnail_image_url = Context::getContext()->link->getImageLink($product->link_rewrite, $cover['id_image'], $thumbnail_image_type);
            }else{
                $gallery_image_url = Tools::getCurrentUrlProtocolPrefix().Tools::getMediaServer(_THEME_PROD_DIR_)._THEME_PROD_DIR_.$this->context->language->iso_code.'-default-'.$gallery_image_type.'.jpg';
                $thumbnail_image_url = Tools::getCurrentUrlProtocolPrefix().Tools::getMediaServer(_THEME_PROD_DIR_)._THEME_PROD_DIR_.$this->context->language->iso_code.'-default-'.$thumbnail_image_type.'.jpg';
            }
            if ($videos = StProVideosClass::getProVideos($product->id, $this->context->language->id))
            {
                $has_autoplay = false;
                foreach ($videos as &$video) {
                    if($how_to_display==1 || $how_to_display==3 || $how_to_display==4 || $how_to_display==6){
                        StProVideoClass::fetchMediaServer($video['thumbnail']);
                        if(preg_match("/youtu(\.be|be\.com)/", $video['url'])){
                            $video['url'] =  preg_replace("(^https?)", "", $video['url']);
                            if(!$video['thumbnail'] && $video['online_thumbnail'] && ($youtube_id=self::getYoutubeId($video['url']))){
                                $video['thumbnail'] =  "//img.youtube.com/vi/".$youtube_id."/default.jpg";
                            }
                        }
                        if($thumbnail_html){
                            $video['thumbnail_html'] = str_replace('###', ($video['thumbnail'] ?: $thumbnail_image_url), $thumbnail_html);
                            $video['thumbnail_html'] = str_replace('@@@', $thumbnail_image_url, $video['thumbnail_html']);
                            $video['thumbnail_html'] = str_replace('***', $video['id_st_pro_video'], $video['thumbnail_html']);
                            $video['thumbnail_html'] = str_replace('$$$', ($video['thumbnail']?'st_pro_video_custom_thumb':($cover?'st_pro_video_native_thumb':'st_pro_video_no_image_thumb')), $video['thumbnail_html']);
                        }
                    }

                    if($has_autoplay===false && $video['autoplay']==1){
                        $has_autoplay = true;
                    }elseif($video['autoplay']==1){
                        $video['autoplay']==0;
                    }
                }
                if($has_autoplay===false && $videos[0]['autoplay']==4 && Configuration::get($this->_prefix_st.'AUTOPLAY'))
                    $videos[0]['autoplay']==1;
                return array(
                    'videos' => $videos,
                    'gallery_image_url' => $gallery_image_url,
                    'thumbnail_image_url' => $thumbnail_image_url,
                );
            }
        }
        return false;
    }
    public function hookDisplayHeader($params)
    {
        $thumbnail_html = Configuration::get($this->_prefix_st.'THUMBNAIL_HTML');
        $thumbnail_html = $thumbnail_html?:$this->getSelector('thumbnail_html');
        $how_to_display = (int)Configuration::get($this->_prefix_st.'HOW_TO_DISPLAY');
        $video_container = Configuration::get($this->_prefix_st.'VIDEO_CONTAINER');
        $desc_container = Configuration::get($this->_prefix_st.'DESC_CONTAINER');
        $gallery_container = Configuration::get($this->_prefix_st.'GALLERY_CONTAINER');
        $thumbnail_container = Configuration::get($this->_prefix_st.'THUMBNAIL_CONTAINER');
        $video_selector = Configuration::get($this->_prefix_st.'VIDEO_SELECTOR');
        $thumbnail_selector = Configuration::get($this->_prefix_st.'THUMBNAIL_SELECTOR');
        $gallery_image_type = Configuration::get($this->_prefix_st.'GALLERY_IMAGE_TYPE');
        $thumbnail_image_type = Configuration::get($this->_prefix_st.'THUMBNAIL_IMAGE_TYPE');
        $gallery_image_type = $gallery_image_type?:$this->getSelector('gallery_image_type');
        $thumbnail_image_type = $thumbnail_image_type?:$this->getSelector('thumbnail_image_type');

        $slider = (int)Configuration::get($this->_prefix_st.'SLIDER');
        $thumb_slider = (int)Configuration::get($this->_prefix_st.'THUMB_SLIDER');

        $st_pro_video = array(
            'how_to_display' => $how_to_display,
            '_st_themes_17' => (bool)$this->_st_themes_17,
            '_st_themes_16' => (bool)$this->_st_themes_16,
            'theme_name' => $this->theme_name,
            'video_container' => $video_container?:$this->getSelector('video_container'),
            'desc_container' => $desc_container?:$this->getSelector('desc_container'),
            'gallery_container' => $gallery_container?:$this->getSelector('gallery_container'),
            'thumbnail_container' => $thumbnail_container?:$this->getSelector('thumbnail_container'),
            'video_selector' => $video_selector?:$this->getSelector('video_selector'),
            'thumbnail_selector' => $thumbnail_selector?:$this->getSelector('thumbnail_selector'),
            'thumbnail_html' => $thumbnail_html,
            'slider' => $slider?:$this->getSelector('slider'),
            'thumb_slider' => $thumb_slider?:$this->getSelector('thumb_slider'),
            'gallery_image_size' => Image::getSize($gallery_image_type),
            'thumbnail_image_size' => Image::getSize($thumbnail_image_type),
        );
        if (method_exists($this->context->controller, 'getProduct'))
        {
            $product = $this->context->controller->getProduct();
            if($video_data = $this->getVideosByProduct($product))
                $st_pro_video = array_merge($st_pro_video, $video_data);
        }

        Media::addJsDef(array('stprovideos' => array_merge(array(
            'autoplay' => (bool)Configuration::get($this->_prefix_st.'AUTOPLAY'),
            'controls' => (bool)Configuration::get($this->_prefix_st.'CONTROLS'),
            'controls_youtube' => (bool)Configuration::get($this->_prefix_st.'CONTROLS_YOUTUBE'),
            'loop' => (int)Configuration::get($this->_prefix_st.'LOOP'),
            'muted' => (bool)Configuration::get($this->_prefix_st.'MUTED'),
            'quick_view' => (int)Configuration::get($this->_prefix_st.'QUICK_VIEW'),
            'thumbnail_event' => (int)Configuration::get($this->_prefix_st.'THUMBNAIL_EVENT'),
            'auto_close_at_end' => (int)Configuration::get($this->_prefix_st.'AUTO_CLOSE_AT_END'),
            'play_video_text'=> html_entity_decode(Configuration::get($this->_prefix_st.'PLAY_VIDEO_TEXT', $this->context->language->id)),
            'st_is_16' => $this->_st_is_16,
            'get_videos_url' => $this->context->link->getModuleLink('stprovideos', 'ajax'),
        ), $st_pro_video)));

        $this->context->controller->addJS($this->_path.'views/js/front.js');
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
        $this->context->controller->addJS($this->_path.'views/js/video.min.js');
        $this->context->controller->addCSS($this->_path.'views/css/video-js.css');
        if(!Configuration::get($this->_prefix_st.'YOUTUBE_API'))
            $this->context->controller->addJS($this->_path.'views/js/youtube.min.js');
        if($skin = Configuration::get($this->_prefix_st.'SKIN'))
            $this->context->controller->addCSS($this->_path.'views/css/skin-'.$skin.'.css');

        $template_file = $this->_st_is_16 ? 'header.tpl' : 'module:stprovideos/views/templates/hook/header.tpl';
        if (!$this->isCached($template_file, $this->getCacheId()) || 1 == 1) {
            $css = '';

            if($btn_color = Configuration::get($this->_prefix_st.'BTN_COLOR'))
                $css .= '.video-js .vjs-big-play-button, .video-js:hover .vjs-big-play-button{color:'.$btn_color.';border-color:'.$btn_color.';}';
            if(($btn_bg_color = Configuration::get($this->_prefix_st.'BTN_BG_COLOR')) && Validate::isColor($btn_bg_color)){
                $btn_bg_opacity = Configuration::get($this->_prefix_st.'BTN_BG_OPACITY');
                if($btn_bg_opacity==='' || $btn_bg_opacity==1)
                    $css .= '.video-js .vjs-big-play-button{background-color: '.$btn_bg_color.'; }';
                else {
                    $btn_bg_color_arr = self::hex2rgb($btn_bg_color);
                    if(is_array($btn_bg_color_arr)) {
                        if($btn_bg_opacity<0 || $btn_bg_opacity>1)
                            $btn_bg_opacity = 0.5;
                        $css .= '.video-js .vjs-big-play-button{background-color: rgba('.$btn_bg_color_arr[0].','.$btn_bg_color_arr[1].','.$btn_bg_color_arr[2].','.$btn_bg_opacity.'); }';
                    }
                }
            }
            if(($btn_hover_bg_color = Configuration::get($this->_prefix_st.'BTN_HOVER_BG_COLOR')) && Validate::isColor($btn_hover_bg_color)){
                $btn_hover_bg_opacity = Configuration::get($this->_prefix_st.'BTN_HOVER_BG_OPACITY');
                if($btn_hover_bg_opacity==='' || $btn_hover_bg_opacity==1)
                    $css .= '.video-js:hover .vjs-big-play-button, .video-js .vjs-big-play-button:focus{background-color: '.$btn_hover_bg_color.'; }';
                else {
                    $btn_hover_bg_color_arr = self::hex2rgb($btn_hover_bg_color);
                    if(is_array($btn_hover_bg_color_arr)) {
                        if($btn_hover_bg_opacity<0 || $btn_hover_bg_opacity>1)
                            $btn_hover_bg_opacity = 0.5;
                        $css .= '.video-js:hover .vjs-big-play-button, .video-js .vjs-big-play-button:focus{background-color: rgba('.$btn_hover_bg_color_arr[0].','.$btn_hover_bg_color_arr[1].','.$btn_hover_bg_color_arr[2].','.$btn_hover_bg_opacity.'); }';
                    }
                }
            }
            $btn_border_size = Configuration::get($this->_prefix_st.'BTN_BORDER_SIZE');
            if($btn_border_size || $btn_border_size===0 || $btn_border_size==='0')
                $css .= '.video-js .vjs-big-play-button{border-width: '.$btn_border_size.'px; }';
            if($btn_size = Configuration::get($this->_prefix_st.'BTN_SIZE'))
                $css .= '.video-js .vjs-big-play-button{width: '.$btn_size.'px; height: '.$btn_size.'px; line-height: '.($btn_size-2*(int)$btn_border_size).'px; margin-left:-'.round($btn_size/2).'px;margin-top:-'.round($btn_size/2).'px;}';
            if($btn_height = Configuration::get($this->_prefix_st.'BTN_HEIGHT'))
                $css .= '.video-js .vjs-big-play-button{height: '.$btn_height.'px; line-height: '.($btn_height-2*(int)$btn_border_size).'px; margin-top:-'.round($btn_height/2).'px;}';
            if($btn_icon_size = Configuration::get($this->_prefix_st.'BTN_ICON_SIZE'))
                $css .= '.video-js .vjs-big-play-button{font-size: '.$btn_icon_size.'px; }';;
            if($btn_border_color = Configuration::get($this->_prefix_st.'BTN_BORDER_COLOR'))
                $css .= '.video-js .vjs-big-play-button{border-color: '.$btn_border_color.'; }';;
            $btn_radius = Configuration::get($this->_prefix_st.'BTN_RADIUS');
            if($btn_radius || $btn_radius===0 || $btn_radius==='0')
                $css .= '.video-js .vjs-big-play-button{border-radius: '.$btn_radius.'px; }';

            if($bg_color = Configuration::get($this->_prefix_st.'BG')){
                $css .= '.st_pro_videos_box, .st_pro_videos{background: '.$bg_color.';}';
            }
            if(($bg_color = Configuration::get($this->_prefix_st.'BG_COLOR')) && Validate::isColor($bg_color)){
                $bg_opacity = Configuration::get($this->_prefix_st.'BG_OPACITY');
                if($bg_opacity==='' || $bg_opacity==1)
                    $css .= '.video-js .vjs-control-bar,.video-js .vjs-menu-button .vjs-menu-content{background-color: '.$bg_color.'; }';
                else {
                    $bg_color_arr = self::hex2rgb($bg_color);
                    if(is_array($bg_color_arr)) {
                        if($bg_opacity<0 || $bg_opacity>1)
                            $bg_opacity = 0.5;
                        $css .= '.video-js .vjs-control-bar,.video-js .vjs-menu-button .vjs-menu-content{background-color: rgba('.$bg_color_arr[0].','.$bg_color_arr[1].','.$bg_color_arr[2].','.$bg_opacity.'); }';
                    }
                }
            }
            if($control_color = Configuration::get($this->_prefix_st.'CONTROL_COLOR'))
                $css .= '.video-js{color: '.$control_color.'; }';
            if($control_size = Configuration::get($this->_prefix_st.'CONTROL_SIZE'))
                $css .= '.video-js .vjs-control-bar{font-size: '.$control_size.'px; }';

            if(($progress_bar_color = Configuration::get($this->_prefix_st.'PROGRESS_BAR_COLOR')) && Validate::isColor($progress_bar_color)){
                $progress_bar_opacity = Configuration::get($this->_prefix_st.'PROGRESS_BAR_OPACITY');
                if($progress_bar_opacity==='' || $progress_bar_opacity==1)
                    $css .= '.video-js .vjs-slider, .video-js .vjs-load-progress{background-color: '.$progress_bar_color.'; }';
                else {
                    $progress_bar_color_arr = self::hex2rgb($progress_bar_color);
                    if(is_array($progress_bar_color_arr)) {
                        if($progress_bar_opacity<0 || $progress_bar_opacity>1)
                            $progress_bar_opacity = 0.5;
                        $css .= '.video-js .vjs-slider, .video-js .vjs-load-progress{background-color: rgba('.$progress_bar_color_arr[0].','.$progress_bar_color_arr[1].','.$progress_bar_color_arr[2].','.$progress_bar_opacity.'); }';
                    }
                }
            }
            if(($load_progress_bar_color = Configuration::get($this->_prefix_st.'LOAD_PROGRESS_BAR_COLOR')) && Validate::isColor($load_progress_bar_color)){
                $load_progress_bar_opacity = Configuration::get($this->_prefix_st.'LOAD_PROGRESS_BAR_OPACITY');
                if($load_progress_bar_opacity==='' || $load_progress_bar_opacity==1)
                    $css .= '.video-js .vjs-load-progress div{background-color: '.$load_progress_bar_color.'; }';
                else {
                    $load_progress_bar_color_arr = self::hex2rgb($load_progress_bar_color);
                    if(is_array($load_progress_bar_color_arr)) {
                        if($load_progress_bar_opacity<0 || $load_progress_bar_opacity>1)
                            $load_progress_bar_opacity = 0.5;
                        $css .= '.video-js .vjs-load-progress div{background-color: rgba('.$load_progress_bar_color_arr[0].','.$load_progress_bar_color_arr[1].','.$load_progress_bar_color_arr[2].','.$load_progress_bar_opacity.'); }';
                    }
                }
            }
            if($progress_bar_highlight_color = Configuration::get($this->_prefix_st.'PROGRESS_BAR_HIGHLIGHT_COLOR'))
                $css .= '.video-js .vjs-volume-level,.video-js .vjs-play-progress,.video-js .vjs-slider-bar{background: '.$progress_bar_highlight_color.'; }';

            if($play_btn_color = Configuration::get($this->_prefix_st.'PLAY_BTN_COLOR'))
                $css .= '.st_pro_video_play .vjs-icon-placeholder{color: '.$play_btn_color.'; }';
            if(($play_btn_bg = Configuration::get($this->_prefix_st.'PLAY_BTN_BG')) && Validate::isColor($play_btn_bg)){
                $play_btn_opacity = Configuration::get($this->_prefix_st.'PLAY_BTN_OPACITY');
                if($play_btn_opacity==='' || $play_btn_opacity==1)
                    $css .= '.st_pro_video_play .vjs-icon-placeholder{background-color: '.$play_btn_bg.'; }';
                else {
                    $play_btn_bg_arr = self::hex2rgb($play_btn_bg);
                    if(is_array($play_btn_bg_arr)) {
                        if($play_btn_opacity<0 || $play_btn_opacity>1)
                            $play_btn_opacity = 0.5;
                        $css .= '.st_pro_video_play .vjs-icon-placeholder{background-color: rgba('.$play_btn_bg_arr[0].','.$play_btn_bg_arr[1].','.$play_btn_bg_arr[2].','.$play_btn_opacity.'); }';
                    }
                }
            }
            $play_btn_border_size = Configuration::get($this->_prefix_st.'PLAY_BTN_BORDER_SIZE');
            if($play_btn_border_size || $play_btn_border_size===0 || $play_btn_border_size==='0')
                $css .= '.st_pro_video_play .vjs-icon-placeholder{border-width: '.$play_btn_border_size.'px; }';
            $play_btn_size = Configuration::get($this->_prefix_st.'PLAY_BTN_SIZE');
            if($play_btn_size){
                $css .= '.st_pro_video_play .vjs-icon-placeholder{width: '.$play_btn_size.'px;height: '.$play_btn_size.'px;line-height: '.($play_btn_size-2*(int)$play_btn_border_size).'px; }';
                $css .= '.st_pro_video_play{margin-left: -'.floor($play_btn_size/2).'px;margin-top: -'.floor($play_btn_size/2).'px;}';
            }else{
                $play_btn_size = 36;
            }
            if($play_btn_icon_size = Configuration::get($this->_prefix_st.'PLAY_BTN_ICON_SIZE'))
                $css .= '.st_pro_video_play .vjs-icon-placeholder{font-size: '.$play_btn_icon_size.'px; }';

            if($play_btn_border_color = Configuration::get($this->_prefix_st.'PLAY_BTN_BORDER_COLOR'))
                $css .= '.st_pro_video_play .vjs-icon-placeholder{border-color: '.$play_btn_border_color.'; }';;
            $play_btn_border_radius = Configuration::get($this->_prefix_st.'PLAY_BTN_BORDER_RADIUS');
            if($play_btn_border_radius || $play_btn_border_radius===0 || $play_btn_border_radius==='0')
                $css .= '.st_pro_video_play .vjs-icon-placeholder{border-radius: '.$play_btn_border_radius.'px; }';
            if($play_video_text_color = Configuration::get($this->_prefix_st.'PLAY_VIDEO_TEXT_COLOR'))
                $css .= '.st_play_video_text{color: '.$play_video_text_color.'; }';
            if($play_video_text_bg = Configuration::get($this->_prefix_st.'PLAY_VIDEO_TEXT_BG'))
                $css .= '.st_play_video_text{background-color: '.$play_video_text_bg.';padding: 0 3px 1px;}';

            $play_btn_position = Configuration::get($this->_prefix_st.'PLAY_BTN_POSITION');
                $play_btn_offset_x = (int)Configuration::get($this->_prefix_st.'PLAY_BTN_OFFSET_X');
                $play_btn_offset_y = (int)Configuration::get($this->_prefix_st.'PLAY_BTN_OFFSET_Y');
                switch ($play_btn_position) {
                    case 1:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{left:'.$play_btn_offset_x .'px;right:auto;margin-left:0;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{top:'.$play_btn_offset_y.'px;bottom:auto;}';
                        break;
                    case 2:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{left:50%;margin-left:'.($play_btn_offset_x-$play_btn_size/2).'px;right:auto;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{top:'.$play_btn_offset_y.'px;bottom:auto;}';
                        break;
                    case 3:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{right:'.$play_btn_offset_x.'px;left:auto;margin-left:0;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{top:'.$play_btn_offset_y.'px;bottom:auto;}';
                        break;
                    case 4:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{left:'.$play_btn_offset_x.'px;right:auto;margin-left:0;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{top:50%;margin-top:'.($play_btn_offset_y-$play_btn_size/2).'px;bottom:auto;}';
                        break;
                    case 0:
                    case 5:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{left:50%;margin-left:'.($play_btn_offset_x-$play_btn_size/2).'px;right:auto;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{top:50%;margin-top:'.($play_btn_offset_y-$play_btn_size/2).'px;bottom:auto;}';
                        break;
                    case 6:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{right:'.$play_btn_offset_x.'px;left:auto;margin-left:0;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{top:50%;margin-top:'.($play_btn_offset_y-$play_btn_size/2).'px;bottom:auto;}';
                        break;
                    case 7:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{left:'.$play_btn_offset_x.'px;right:auto;margin-left:0;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{bottom:'.$play_btn_offset_y.'px;top:auto;}';
                        break;
                    case 8:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{left:50%;margin-left:'.($play_btn_offset_x-$play_btn_size/2).'px;right:auto;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{bottom:'.$play_btn_offset_y.'px;top:auto;}';
                        break;
                    case 9:
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{right:'.$play_btn_offset_x.'px;left:auto;margin-left:0;}';
                        $css .= '.st_pro_video_play_on_first_gallery, .st_pro_video_play_static{bottom:'.$play_btn_offset_y.'px;top:auto;}';
                        break;
                }

            if($play_icon_color = Configuration::get($this->_prefix_st.'PLAY_ICON_COLOR'))
                $css .= '.st_pro_video_play_icon{color: '.$play_icon_color.'; }';
            if(($play_icon_bg = Configuration::get($this->_prefix_st.'PLAY_ICON_BG')) && Validate::isColor($play_icon_bg)){
                $play_icon_opacity = Configuration::get($this->_prefix_st.'PLAY_ICON_OPACITY');
                if($play_icon_opacity==='' || $play_icon_opacity==1)
                    $css .= '.st_pro_video_play_icon{background-color: '.$play_icon_bg.'; }';
                else {
                    $play_icon_bg_arr = self::hex2rgb($play_icon_bg);
                    if(is_array($play_icon_bg_arr)) {
                        if($play_icon_opacity<0 || $play_icon_opacity>1)
                            $play_icon_opacity = 0.5;
                        $css .= '.st_pro_video_play_icon{background-color: rgba('.$play_icon_bg_arr[0].','.$play_icon_bg_arr[1].','.$play_icon_bg_arr[2].','.$play_icon_opacity.'); }';
                    }
                }
            }
            $play_icon_border_size = Configuration::get($this->_prefix_st.'PLAY_ICON_BORDER_SIZE');
            if($play_icon_border_size || $play_icon_border_size===0 || $play_icon_border_size==='0')
                $css .= '.st_pro_video_play_icon{border-width: '.$play_icon_border_size.'px; }';
            if($play_icon_size = Configuration::get($this->_prefix_st.'PLAY_ICON_SIZE')){
                $css .= '.st_pro_video_play_icon{width: '.$play_icon_size.'px;height: '.$play_icon_size.'px;line-height: '.($play_icon_size-2*(int)$play_icon_border_size).'px; margin-top:-'.round($play_icon_size/2).'px;margin-left:-'.round($play_icon_size/2).'px;}';
            }
            if($play_icon_icon_size = Configuration::get($this->_prefix_st.'PLAY_ICON_ICON_SIZE'))
                $css .= '.st_pro_video_play_icon{font-size: '.$play_icon_icon_size.'px; }';
            if($play_icon_border_color = Configuration::get($this->_prefix_st.'PLAY_ICON_BORDER_COLOR'))
                $css .= '.st_pro_video_play_icon{border-color: '.$play_icon_border_color.'; }';
            $play_icon_border_radius = Configuration::get($this->_prefix_st.'PLAY_ICON_BORDER_RADIUS');
            if($play_icon_border_radius || $play_icon_border_radius===0 || $play_icon_border_radius==='0')
                $css .= '.st_pro_video_play_icon{border-radius: '.$play_icon_border_radius.'px; }';

            if($thumbnail_width = Configuration::get($this->_prefix_st.'THUMBNAIL_WIDTH'))
                $css .= '.st_pro_video_thumbnail img{width: '.$thumbnail_width.'px;}';
            if($desc_player_width = Configuration::get($this->_prefix_st.'DESC_PLAYER_WIDTH'))
                $css .= '.st_pro_video_miaoshu .st_pro_videos_box{max-width: '.$desc_player_width.'px;}';
            if($how_to_display==4 || $how_to_display==6)
                $css .= '.st_pro_video_native_thumb, .hightlight_curr_thumbs .clicked_thumb .st_pro_video_native_thumb, .hightlight_curr_thumbs .curr_combination_thumb .st_pro_video_native_thumb,.hightlight_curr_thumbs .st_pro_video_native_thumb,#product-images-thumbs .st_pro_video_native_thumb, #content .product-images>li.thumb-container>.thumb.selected.st_pro_video_native_thumb, #content .product-images>li.thumb-container>.thumb.st_pro_video_native_thumb:hover{opacity: 0;}';

            $z_index = Configuration::get($this->_prefix_st.'Z_INDEX');
            $z_index = $z_index?:$this->getSelector('z_index');
            if($z_index){
                $css .= '.st_pro_videos_box{z-index: '.$z_index.';}';
                $css .= '.st_pro_video_btn{z-index: '.($z_index+1).';}';
            }

            if ($custom_css = Configuration::get($this->_prefix_st.'CUSTOM_CSS'))
                $css .= html_entity_decode(str_replace('¤', '\\', $custom_css));

            $st_pro_video['custom_css'] = $css;
        }
        $this->context->smarty->assign('st_pro_video',$st_pro_video);
        $this->context->controller->addJS($this->_path.'views/js/front.js');
        if($this->_st_is_16)
            return $this->display(__FILE__, $template_file, $this->getCacheId());
        else
            return $this->fetch($template_file, $this->getCacheId());
    }
    public function hookDisplayProductAdditionalInfo($params){
        if(!isset($params['product']) || Tools::getValue('action')!='quickview')
            return false;
        $product = $this->context->controller->getProduct();
        if($video_data = $this->getVideosByProduct($product)){
            $this->context->smarty->assign('video_data',$video_data);
            return $this->fetch('module:stprovideos/views/templates/hook/forquickview.tpl');
        }
        return;
    }

    public static function hex2rgb($hex) {
       $hex = str_replace("#", "", $hex);

       if(strlen($hex) == 3) {
          $r = hexdec(substr($hex,0,1).substr($hex,0,1));
          $g = hexdec(substr($hex,1,1).substr($hex,1,1));
          $b = hexdec(substr($hex,2,1).substr($hex,2,1));
       } else {
          $r = hexdec(substr($hex,0,2));
          $g = hexdec(substr($hex,2,2));
          $b = hexdec(substr($hex,4,2));
       }
       $rgb = array($r, $g, $b);
       return $rgb;
    }
    public function hookActionProductDelete($params)
    {
        if (isset($params['id_product']) && $params['id_product'])
            StProVideosClass::deleteByProductId($params['id_product']);
        // $this->_clearCache('stprovideos.tpl');
        return;
    }
    public function hookFilterProductContent()
    {

    }
    private function getConfigFieldsValues()
    {
        $custom_css = Configuration::get($this->_prefix_st.'CUSTOM_CSS');
        $fields_values = array(
            'autoplay' => Configuration::get($this->_prefix_st.'AUTOPLAY'),
            'controls' => Configuration::get($this->_prefix_st.'CONTROLS'),
            'controls_youtube' => Configuration::get($this->_prefix_st.'CONTROLS_YOUTUBE'),
            'width' => Configuration::get($this->_prefix_st.'WIDTH'),
            'height' => Configuration::get($this->_prefix_st.'HEIGHT'),
            'loop' => Configuration::get($this->_prefix_st.'LOOP'),
            'muted' => Configuration::get($this->_prefix_st.'MUTED'),
            'fluid' => Configuration::get($this->_prefix_st.'FLUID'),
            'aspectRatio' => Configuration::get($this->_prefix_st.'ASPECTRATIO'),
            'playbackRates' => Configuration::get($this->_prefix_st.'PLAYBACKRATES'),
            'how_to_display' => Configuration::get($this->_prefix_st.'HOW_TO_DISPLAY'),
            'skin' => Configuration::get($this->_prefix_st.'SKIN'),
            'quick_view' => Configuration::get($this->_prefix_st.'QUICK_VIEW'),


            'btn_color' => Configuration::get($this->_prefix_st.'BTN_COLOR'),
            'btn_bg_color' => Configuration::get($this->_prefix_st.'BTN_BG_COLOR'),
            'btn_bg_opacity' => Configuration::get($this->_prefix_st.'BTN_BG_OPACITY'),
            'btn_hover_bg_color' => Configuration::get($this->_prefix_st.'BTN_HOVER_BG_COLOR'),
            'btn_hover_bg_opacity' => Configuration::get($this->_prefix_st.'BTN_HOVER_BG_OPACITY'),
            'btn_size' => Configuration::get($this->_prefix_st.'BTN_SIZE'),
            'btn_icon_size' => Configuration::get($this->_prefix_st.'BTN_ICON_SIZE'),
            'btn_height' => Configuration::get($this->_prefix_st.'BTN_HEIGHT'),
            'btn_radius' => Configuration::get($this->_prefix_st.'BTN_RADIUS'),

            'bg' => Configuration::get($this->_prefix_st.'BG'),
            'bg_color' => Configuration::get($this->_prefix_st.'BG_COLOR'),
            'bg_opacity' => Configuration::get($this->_prefix_st.'BG_OPACITY'),
            'control_color' => Configuration::get($this->_prefix_st.'CONTROL_COLOR'),
            'control_size' => Configuration::get($this->_prefix_st.'CONTROL_SIZE'),
            'progress_bar_color' => Configuration::get($this->_prefix_st.'PROGRESS_BAR_COLOR'),
            'progress_bar_opacity' => Configuration::get($this->_prefix_st.'PROGRESS_BAR_OPACITY'),
            'progress_bar_highlight_color' => Configuration::get($this->_prefix_st.'PROGRESS_BAR_HIGHLIGHT_COLOR'),
            'load_progress_bar_opacity' => Configuration::get($this->_prefix_st.'LOAD_PROGRESS_BAR_OPACITY'),
            'load_progress_bar_color' => Configuration::get($this->_prefix_st.'LOAD_PROGRESS_BAR_COLOR'),

            'play_btn_color' => Configuration::get($this->_prefix_st.'PLAY_BTN_COLOR'),
            'play_btn_bg' => Configuration::get($this->_prefix_st.'PLAY_BTN_BG'),
            'play_btn_opacity' => Configuration::get($this->_prefix_st.'PLAY_BTN_OPACITY'),
            'play_btn_size' => Configuration::get($this->_prefix_st.'PLAY_BTN_SIZE'),
            'play_btn_icon_size' => Configuration::get($this->_prefix_st.'PLAY_BTN_ICON_SIZE'),
            'play_btn_position' => Configuration::get($this->_prefix_st.'PLAY_BTN_POSITION'),
            'play_btn_offset_x' => Configuration::get($this->_prefix_st.'PLAY_BTN_OFFSET_X'),
            'play_btn_offset_y' => Configuration::get($this->_prefix_st.'PLAY_BTN_OFFSET_Y'),

            'play_icon_color' => Configuration::get($this->_prefix_st.'PLAY_ICON_COLOR'),
            'play_icon_bg' => Configuration::get($this->_prefix_st.'PLAY_ICON_BG'),
            'play_icon_opacity' => Configuration::get($this->_prefix_st.'PLAY_ICON_OPACITY'),
            'play_icon_size' => Configuration::get($this->_prefix_st.'PLAY_ICON_SIZE'),
            'play_icon_icon_size' => Configuration::get($this->_prefix_st.'PLAY_ICON_ICON_SIZE'),

            'play_btn_border_color' => Configuration::get($this->_prefix_st.'PLAY_BTN_BORDER_COLOR'),
            'play_btn_border_size' => Configuration::get($this->_prefix_st.'PLAY_BTN_BORDER_SIZE'),
            'play_btn_border_radius' => Configuration::get($this->_prefix_st.'PLAY_BTN_BORDER_RADIUS'),
            'btn_border_color' => Configuration::get($this->_prefix_st.'BTN_BORDER_COLOR'),
            'btn_border_size' => Configuration::get($this->_prefix_st.'BTN_BORDER_SIZE'),
            'play_icon_border_color' => Configuration::get($this->_prefix_st.'PLAY_ICON_BORDER_COLOR'),
            'play_icon_border_size' => Configuration::get($this->_prefix_st.'PLAY_ICON_BORDER_SIZE'),
            'play_icon_border_radius' => Configuration::get($this->_prefix_st.'PLAY_ICON_BORDER_RADIUS'),

            'video_container' => Configuration::get($this->_prefix_st.'VIDEO_CONTAINER'),
            'desc_container' => Configuration::get($this->_prefix_st.'DESC_CONTAINER'),
            'gallery_container' => Configuration::get($this->_prefix_st.'GALLERY_CONTAINER'),
            'thumbnail_container' => Configuration::get($this->_prefix_st.'THUMBNAIL_CONTAINER'),
            'video_selector' => Configuration::get($this->_prefix_st.'VIDEO_SELECTOR'),
            'thumbnail_selector' => Configuration::get($this->_prefix_st.'THUMBNAIL_SELECTOR'),
            'thumbnail_html' => Configuration::get($this->_prefix_st.'THUMBNAIL_HTML'),
            'gallery_image_type' => Configuration::get($this->_prefix_st.'GALLERY_IMAGE_TYPE'),
            'thumbnail_image_type' => Configuration::get($this->_prefix_st.'THUMBNAIL_IMAGE_TYPE'),
            'slider' => Configuration::get($this->_prefix_st.'SLIDER'),
            'thumb_slider' => Configuration::get($this->_prefix_st.'THUMB_SLIDER'),
            'z_index' => Configuration::get($this->_prefix_st.'Z_INDEX'),
            'play_video_text_color' => Configuration::get($this->_prefix_st.'PLAY_VIDEO_TEXT_COLOR'),
            'play_video_text_bg' => Configuration::get($this->_prefix_st.'PLAY_VIDEO_TEXT_BG'),

            'thumbnail_width' => Configuration::get($this->_prefix_st.'THUMBNAIL_WIDTH'),
            'thumbnail_event' => Configuration::get($this->_prefix_st.'THUMBNAIL_EVENT'),
            'auto_close_at_end' => Configuration::get($this->_prefix_st.'AUTO_CLOSE_AT_END'),
            'desc_player_width' => Configuration::get($this->_prefix_st.'DESC_PLAYER_WIDTH'),
            'theme_name' => Configuration::get($this->_prefix_st.'THEME_NAME'),
            'youtube_api' => Configuration::get($this->_prefix_st.'YOUTUBE_API'),

            'custom_css' => $custom_css ? str_replace('¤', '\\', $custom_css) : '',
        );

        $languages = Language::getLanguages(false);
        $fields_values['play_video_text'] = array();
        foreach ($languages as $language)
        {
            $fields_values['play_video_text'][$language['id_lang']] = Configuration::get($this->_prefix_st.'PLAY_VIDEO_TEXT', $language['id_lang']);
        }

        return $fields_values;
    }

    /**
     * Return the list of fields value
     *
     * @param object $obj Object
     * @return array
     */
    public function getFieldsValueSt($obj,$fields_form="fields_form")
    {
        foreach ($this->$fields_form as $fieldset)
            if (isset($fieldset['form']['input']))
                foreach ($fieldset['form']['input'] as $input)
                    if (!isset($this->fields_value[$input['name']]))
                        if (isset($input['type']) && $input['type'] == 'shop')
                        {
                            if ($obj->id)
                            {
                                $result = Shop::getShopById((int)$obj->id, $this->identifier, $this->table);
                                foreach ($result as $row)
                                    $this->fields_value['shop'][$row['id_'.$input['type']]][] = $row['id_shop'];
                            }
                        }
                        elseif (isset($input['lang']) && $input['lang'])
                            foreach (Language::getLanguages(false) as $language)
                            {
                                $fieldValue = $this->getFieldValueSt($obj, $input['name'], $language['id_lang']);
                                if (empty($fieldValue))
                                {
                                    if (isset($input['default_value']) && is_array($input['default_value']) && isset($input['default_value'][$language['id_lang']]))
                                        $fieldValue = $input['default_value'][$language['id_lang']];
                                    elseif (isset($input['default_value']))
                                        $fieldValue = $input['default_value'];
                                }
                                $this->fields_value[$input['name']][$language['id_lang']] = $fieldValue;
                            }
                        else
                        {
                            $fieldValue = $this->getFieldValueSt($obj, $input['name']);
                            if ($fieldValue===false && isset($input['default_value']))
                                $fieldValue = $input['default_value'];
                            $this->fields_value[$input['name']] = $fieldValue;
                        }

        return $this->fields_value;
    }

    /**
     * Return field value if possible (both classical and multilingual fields)
     *
     * Case 1 : Return value if present in $_POST / $_GET
     * Case 2 : Return object value
     *
     * @param object $obj Object
     * @param string $key Field name
     * @param integer $id_lang Language id (optional)
     * @return string
     */
    public function getFieldValueSt($obj, $key, $id_lang = null)
    {
        if ($id_lang)
            $default_value = ($obj->id && isset($obj->{$key}[$id_lang])) ? $obj->{$key}[$id_lang] : false;
        else
            $default_value = isset($obj->{$key}) ? $obj->{$key} : false;

        return Tools::getValue($key.($id_lang ? '_'.$id_lang : ''), $default_value);
    }
    protected function _checkImageDir()
    {
        $result = true;
        if (!file_exists(_PS_UPLOAD_DIR_.$this->name))
        {
            $success = @mkdir(_PS_UPLOAD_DIR_.$this->name, self::$access_rights, true)
                        || @chmod(_PS_UPLOAD_DIR_.$this->name, self::$access_rights);
            if(!$success) {
                $result = false;
                $this->_html .= $this->displayError('"'._PS_UPLOAD_DIR_.$this->name.'" '.$this->l('An error occurred during new folder creation'));
            }
        }

        if (!is_writable(_PS_UPLOAD_DIR_)) {
            $result = false;
            $this->_html .= $this->displayError('"'._PS_UPLOAD_DIR_.$this->name.'" '.$this->l('directory isn\'t writable.'));
        }
        return $result;
    }
}
