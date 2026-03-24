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
class Ets_imagecompressor_optimize
{

    protected static $instance;
    protected $isblog;
    protected $isSlide;
    protected $isBanner;
    protected $_errors=array();
    protected $_resmush=0;
    protected $is17=false;
    protected $is16=false;
    protected $_google =0;
    public $number_optimize=1;
    public function __construct()
    {
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
        if(version_compare(_PS_VERSION_, '1.7', '<'))
            $this->is16 = true;
        if(Module::isInstalled('ybc_blog') && Module::isEnabled('ybc_blog'))
            $this->isblog = true;
        else
            $this->isblog = false;
        if((Module::isInstalled('ps_imageslider') && Module::isEnabled('ps_imageslider')) ||  (Module::isInstalled('homeslider') && Module::isEnabled('homeslider')))
            $this->isSlide = true;
        else
            $this->isSlide = false;
        if((Module::isInstalled('blockbanner') && Module::isEnabled('blockbanner')) ||  (Module::isInstalled('ps_banner') && Module::isEnabled('ps_banner')))
            $this->isBanner = true;
        else
            $this->isBanner = false;
        if(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT')=='google' || Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT')=='php')
            $this->number_optimize=5;
    }
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Ets_imagecompressor_optimize();
        }
        return self::$instance;
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ets_imagecompressor', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    public function getBaseLink()
    {
        $link = (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').Context::getContext()->shop->domain.Context::getContext()->shop->getBaseURI();
        return trim($link,'/');
    }
    public function compress($path, $type, $quality,$url_image=null,$quality_old=0,$is_product = false,$continue=false,$continue_webp=false) {
        $module  = Module::getInstanceByName('ets_imagecompressor');
        if(Tools::isSubmit('btnSubmitImageOptimize') || Tools::isSubmit('btnSubmitImageAllOptimize')|| Tools::isSubmit('btnSubmitPageCacheDashboard'))
        {
            $script_optimize = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
            $image = 'old';
        }
        elseif(Tools::isSubmit('submitUploadImageCompress'))
        {
            $script_optimize = Configuration::get('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_UPLOAD');
            $image='upload';
        }
        elseif(Tools::isSubmit('submitBrowseImageOptimize'))
        {
            $script_optimize = Configuration::get('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT_BROWSE');
            $image='browse';
        }
        else
        {
            $script_optimize = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
            $image ='new';
        }
        if(!is_array($type))
        {
            $name = $type;
            $source = $path.$name;
            $destination = $path.$name;
            $temp = $path.'temp-'.$name;
        }
        else
        {
            $name = Tools::stripslashes($type['name']);
            $source = $path.'-'.$name.'.jpg';
            $destination = $path.'-'.$name.'.jpg';
            $temp = $path.'-'.$name.'-temp.jpg';
        }
        $file_size_old= Tools::ps_round(@filesize($source)/1024,2);
        if($quality >=100)
        {
            if(Configuration::get('ETS_SPEEP_RESUMSH')!=2)
                Configuration::updateValue('ETS_SPEEP_RESUMSH',2);
            if($is_product)
            {
                $destination_webp = str_replace('.jpg','.webp',$destination);
                if(file_exists($destination_webp))
                    @unlink($destination_webp);
            }
            if(file_exists($temp))
                unlink($temp);
            return array(
                'file_size' => $file_size_old,
                'optimize_type' => $script_optimize ? $script_optimize : 'google',
            );
        }

        if($script_optimize=='resmush' && $this->checkOptimizeImageResmush() && $url_image && $quality<100 && $this->_resmush < 10 && !$continue)
        {
            $this->_errors=array();
            if($file_size = $this->compressByReSmush($url_image,$quality,$temp,$destination,$file_size_old,$is_product))
            {
                if(file_exists($temp))
                    @unlink($temp);
                if(file_exists($path.'fileType'))
                    @unlink($path.'fileType');
                return $file_size;
            }
            else
            {
                $this->_resmush++;
                if(file_exists($temp))
                    @unlink($temp);
                return false;
            }
        }
        if($script_optimize=='tynypng' && !$continue)
        {
            $tynypng_api_keys = explode(';',Configuration::get('ETS_IMGCOMPRESSOR_API_TYNY_KEY'));
            if(Configuration::get('ETS_IMGCOMPRESSOR_ERRORS_TINYPNG'))
                $errors_api =  json_decode(Configuration::get('ETS_IMGCOMPRESSOR_ERRORS_TINYPNG'),true);
            else
                $errors_api = array();
            if($tynypng_api_keys)
            {
                foreach($tynypng_api_keys as $api_key)
                {
                    if(!isset($errors_api[$api_key]) || (isset($errors_api[$api_key]) && $errors_api[$api_key]<=5 ))
                    {
                        if(file_exists($temp))
                            @unlink($temp);
                        $this->_errors = array();
                        if($file_size = $this->compressByTyNyPNG($source,$api_key,$is_product))
                        {
                            if(isset($errors_api[$api_key]) && $errors_api[$api_key]!=1)
                            {
                                $errors_api[$api_key]=1;
                                Configuration::updateValue('ETS_IMGCOMPRESSOR_ERRORS_TINYPNG', json_encode($errors_api));
                            }
                            return $file_size;
                        }
                        else
                        {
                            if(isset($errors_api[$api_key]))
                                $errors_api[$api_key]++;
                            else
                                $errors_api[$api_key]=1;
                            Configuration::updateValue('ETS_IMGCOMPRESSOR_ERRORS_TINYPNG', json_encode($errors_api));
                            return false;
                        }

                    }
                }
            }
        }
        if(($script_optimize=='google' || $continue_webp) && $this->_google <=5 && !$continue)
        {
            $this->_errors=array();
            $mime_type= Tools::strtolower(mime_content_type($source));
            if($mime_type=='image/jpeg' || $mime_type=='image/png' )
                $optimized = $this->compressByGoogleScript($source,$destination,$temp,$quality,$is_product);
            else{
                if(file_exists($temp))
                    @unlink($temp);
                return array(
                    'file_size' => $file_size_old,
                    'optimize_type' => 'google',
                );
            }
            if($optimized)
            {
                $this->_google = 0;
                if(file_exists($temp))
                    @unlink($temp);
                return $optimized;
            }
            else
            {
                $this->_google++;
                if(file_exists($temp))
                    @unlink($temp);
                return false;
            }
        }
        if($script_optimize!='php' && (!$continue || $continue_webp) && ($image=='old' || $image=='upload' || $image=='browse'))
        {
            $mime_type= Tools::strtolower(mime_content_type($source));
            if($image=='upload')
            {
                if(file_exists($source))
                    @unlink($source);
            }
            if(($script_optimize=='google' || $continue_webp) && ($mime_type=='image/jpeg' || $mime_type=='image/png'))
            {
                if(file_exists($temp))
                    @unlink($temp);
                die(
                    json_encode(
                        array(
                            'error' => $module->displayGoogleError($this->_errors,true),
                            'script_continue'=> 'php',
                        )
                    )
                );
            }
            if(file_exists($temp))
                @unlink($temp);
            die(
                json_encode(
                    array(
                        'error' => $this->_errors ? $module->displayError($this->_errors,true):$module->displayError($this->l('errors'),true),
                        'script_continue' => 'php',
                    )
                )
            );
        }
        return $this->compressByPhp($path,$name,$source,$destination,$temp,$quality,$type,$file_size_old,$quality_old,$is_product);
    }
    public function checkOptimizeImageResmush()
    {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );
        if(!in_array(Tools::getRemoteAddr(), $whitelist)){
            return true;
        }
        return false;
    }
    public function compressByReSmush($url_image,$quality,$temp,$destination,$file_size_old,$is_product=false)
    {
        $optimized_jpg_arr = json_decode(Tools::file_get_contents('http://api.resmush.it/ws.php?img='. $url_image.($quality <80 ? '&qlty='.(int)$quality: '')),true);
        if(isset($optimized_jpg_arr['dest']))
        {
            $optimized_jpg_url= $optimized_jpg_arr['dest'];
            if(Configuration::get('ETS_SPEEP_RESUMSH')!=1)
                Configuration::updateValue('ETS_SPEEP_RESUMSH',1);
            file_put_contents($temp,Tools::file_get_contents($optimized_jpg_url));
            if(@file_exists($temp))
            {
                $file_size= Tools::ps_round(@filesize($temp)/1024,2);
                if($file_size>0)
                {
                    Tools::copy($temp,$destination);
                    @unlink($temp);
                    if($is_product)
                    {
                        $destination_webp = str_replace('.jpg','.webp',$destination);
                        if(file_exists($destination_webp))
                            @unlink($destination_webp);
                    }
                    if($file_size < $file_size_old)
                        return array(
                            'file_size' => $file_size,
                            'optimize_type' => 'resmush',
                        );
                    else
                        return array(
                            'file_size' => $file_size_old,
                            'optimize_type' => 'resmush',
                        );
                }
                else
                {
                    @unlink($temp);
                    $this->_errors[] = $this->l('Resmush failed to create image');
                    return false;
                }
            }
        }
        $this->_errors[] = $this->l('Resmush failed to create image');
        return false;
    }
    public function compressByTyNyPNG($source,$api_key,$is_product = false)
    {
        $curl = curl_init();
        $curlOptions = array(
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.tinypng.com/shrink',
            CURLOPT_USERAGENT => 'TinyPNG PHP v1',
            CURLOPT_USERPWD => 'api:'.$api_key,
        );
        curl_setopt_array($curl, $curlOptions);
        curl_setopt($curl, CURLOPT_POSTFIELDS, Tools::file_get_contents($source));
        $response = curl_exec($curl);
        $content = json_decode(Tools::substr($response, curl_getinfo($curl, CURLINFO_HEADER_SIZE)),true);
        if(isset($content['output']['url']) && $content['output']['url'])
        {
            if($content['output']['size']>0)
            {
                if(file_put_contents($source, Tools::file_get_contents($content['output']['url'])) !== false)
                {
                    if($is_product)
                    {
                        $destination_webp = str_replace('.jpg','.webp',$source);
                        if(file_exists($destination_webp))
                            @unlink($destination_webp);
                    }
                    return array(
                        'file_size' => Tools::ps_round($content['output']['size']/1024,2),
                        'optimize_type' => 'tynypng',
                    );
                }
            }
        }
        $this->_errors[] = $this->l('TinyPNG is not working. Your API key(s) is invalid or you may have reached API limit.');
        return false;
    }
    public function compressByGoogleScript($source,$destination,$temp,$quality,$is_product = false)
    {
        require dirname(__FILE__) . '/classes/rosell-dk/vendor/autoload.php';
        $options = array(
            'converters' => array('cwebp', 'vips', 'imagick', 'gmagick', 'imagemagick', 'graphicsmagick', 'gd'),
            'alpha-quality' => (int)$quality
        );
        $optimize = WebPConvert\WebPConvert::convert($source, $temp, $options, null);
        if (@file_exists($temp))
        {
            if($optimize && $file_size= Tools::ps_round(@filesize($temp)/1024,2))
            {
                $is_webp = (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_ENABLE_WEBP_FORMAT');
                $destination_webp = str_replace('.jpg','.webp',$destination);
                if($is_webp && $is_product)
                {
                    Tools::copy($temp,$destination_webp);
                }
                else
                {
                    Tools::copy($temp,$destination);
                    if($is_product && file_exists($destination_webp))
                        @unlink($destination_webp);
                }
                @unlink($temp);
                return array(
                    'file_size' => $file_size,
                    'optimize_type' => Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') : 'php',
                );
            }
            else
                return false;
        }
        else
            return false;
    }
    public function compressByPhp($path,$name,$source,$destination,$temp,$quality,$type,$file_size_old,$quality_old,$is_product = false)
    {
        if($this->png_has_transparency($source))
        {
            if(file_exists($temp))
                @unlink($temp);
            return array(
                'file_size' => $file_size_old,
                'optimize_type' => Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') : 'php',
            );
        }

        ini_set('gd.jpeg_ignore_warning', 1);
        $temp2 = $path.'temp2-'.$name;
        Tools::copy($source,$temp2);
        $image= @getimagesize($source);
        $default = false;
        if($quality >=100 || ($quality<=80 && is_array($type) && isset($type['width']) && $type['width']<=260) || ($name==Configuration::get('PS_LOGO') && $quality<=80))
        {
            if(file_exists($temp2))
                @unlink($temp2);
            if($is_product)
            {
                $destination_webp = str_replace('.jpg','.webp',$destination);
                if(file_exists($destination_webp))
                    @unlink($destination_webp);
            }
            if($quality_old <= 80){
                if(file_exists($temp))
                    @unlink($temp);
                return array(
                    'file_size' => $file_size_old,
                    'optimize_type' => Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') : 'php',
                );
            }
            $default = true;
        }
        if($image)
        {
            ini_set('gd.jpeg_ignore_warning', 1);
            $widthImage = $image[0];
            $heightImage = $image[1];
            $imageCanves = imagecreatetruecolor($widthImage, $heightImage);
            switch(Tools::strtolower($image['mime']))
            {
                case 'image/jpeg':
                    $NewImage = imagecreatefromjpeg($source);
                    break;
                case 'image/JPEG':
                    $NewImage = imagecreatefromjpeg($source);
                    break;
                case 'image/png':
                    $NewImage = imagecreatefrompng($source);
                    break;
                case 'image/PNG':
                    $NewImage = imagecreatefrompng($source);
                    break;
                case 'image/gif':
                    $NewImage = imagecreatefromgif($source);
                    break;
                default:
                    return array(
                        'file_size' => $file_size_old,
                        'optimize_type' => Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') : 'php',
                    );
            }
            $white = imagecolorallocate($imageCanves, 255, 255, 255);
            imagefill($imageCanves,0,0,$white);
            // Resize Image
            if(imagecopyresampled($imageCanves, $NewImage,0, 0, 0, 0, $widthImage, $heightImage, $widthImage, $heightImage))
            {
                // copy file

                if(imagejpeg($imageCanves,$destination,$default ? 80 : $quality))
                {
                    imagedestroy($imageCanves);
                    if(Tools::copy($destination,$temp))
                    {
                        $file_size= Tools::ps_round(@filesize($temp)/1024,2);
                        if($file_size > $file_size_old)
                        {
                            Tools::copy($temp2,$destination);
                            $file_size = $file_size_old;
                        }
                        if(file_exists($temp))
                            @unlink($temp);
                        if(file_exists($temp2))
                            @unlink($temp2);
                        if(file_exists($path.'fileType'))
                            @unlink($path.'fileType');
                        if($is_product)
                        {
                            $destination_webp = str_replace('.jpg','.webp',$destination);
                            if(file_exists($destination_webp))
                                @unlink($destination_webp);
                        }
                        return array(
                            'file_size' => $file_size,
                            'optimize_type' => Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') : 'php',
                        );
                    }
                }
            }
        }
        if(file_exists($temp2))
            @unlink($temp2);
        if(file_exists($temp))
            @unlink($temp);
        if(file_exists($path.'fileType'))
            @unlink($path.'fileType');
        return array(
            'file_size' => $file_size_old,
            'optimize_type' => Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') : 'php',
        );
    }
    public function png_has_transparency( $filename ) {
        if ( Tools::strlen( $filename ) == 0 || !file_exists( $filename ) )
            return false;
        if ( ord( call_user_func('file_get_contents',$filename, false, null, 25, 1)) & 4 )
        {
            return true;
        }
        $contents = Tools::file_get_contents( $filename );
        if (stripos( $contents, 'PLTE' ) !== false && stripos( $contents, 'tRNS' ) !== false )
            return true;

        return false;
    }
    public static function getImagesUnUsed($folder='c',$table='category',$primakey='id_category',$image_type='categories',$delete=false)
    {
        $images = glob(_PS_IMG_DIR_.$folder.'/[1-9]*.jpg');
        if($images)
        {
            foreach($images as $key=> $image)
            {
                if(strpos($image,'_bk.jpg')!==false)
                    unset($images[$key]);
                else
                {
                    $image_name = explode('/',$image);
                    $image_name = $image_name[Count($image_name)-1];
                    $image_name2 = explode('-',$image_name);
                    $id_object = str_replace('.jpg','',$image_name2[0]);
                    if(Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.pSQL($table).' WHERE '.pSQL($primakey).'="'.(int)$id_object.'"'))
                    {
                        $type = str_replace(array($id_object.'-','.jpg'),'',$image_name);
                        if($type==$id_object || Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE name ="'.pSQL($type).'" AND '.pSQL($image_type).'=1'))
                        {
                            unset($images[$key]);
                        }
                    }

                }
            }
        }
        $total_size =0;
        if($images)
        {
            foreach($images as $image)
            {
                if($delete)
                {
                    if(file_exists($image))
                        @unlink($image);
                }
                else
                    $total_size += filesize($image);
            }
        }
        $total_size = Tools::ps_round($total_size/1024,2);
        return array(
            'total_image' => Count($images),
            'total_size' => $total_size <1024 ? $total_size.'KB': (($total_size = Tools::ps_round($total_size/1024,2)) < 1024 ? $total_size.'MB' : Tools::ps_round($total_size/1024,2).'GB'),
        );
    }
    public static function getImagesProductUnUsed($delete=false)
    {
        $images = Db::getInstance()->executeS('SELECT i.id_image FROM `'._DB_PREFIX_.'image` i
            INNER JOIN `'._DB_PREFIX_.'image_shop` ims ON (i.id_image= ims.id_image)
            LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ims.id_product = ps.id_product)
            WHERE ps.id_product is null AND ims.id_shop="'.(int)Context::getContext()->shop->id.'"
        ');
        $total_image =0;
        $total_size = 0;
        if($images)
        {
            foreach($images as $image)
            {
                $image_obj = new Image($image['id_image']);
                if($delete)
                    $image_obj->delete();
                else
                {
                    $path = _PS_PROD_IMG_DIR_.$image_obj->getImgFolder();
                    $product_images = glob($path.'*.jpg');
                    if($product_images)
                    {
                        $total_image += count($product_images);
                        foreach($product_images as $product_image)
                        {
                            $total_size+= filesize($product_image);
                        }
                    }
                }

            }
        }
        $total_size = Tools::ps_round($total_size/1024,2);
        return array(
            'total_image' => $total_image,
            'total_size' => $total_size <1024 ? $total_size.'KB': (($total_size = Tools::ps_round($total_size/1024,2)) < 1024 ? $total_size.'MB' : Tools::ps_round($total_size/1024,2).'GB'),
        );
    }
    public static function createBlogImage($path,$name,$restore = true)
    {
        $type_image = Tools::strtolower(Tools::substr(strrchr($name, '.'), 1));
        if(in_array($type_image,array('jpg', 'gif', 'jpeg', 'png', 'webp')))
        {
            $name_bk = str_replace('.'.$type_image,'',$name).'_bk.'.$type_image;
            if(file_exists($path.$name_bk) && $restore)
            {
                if(file_exists($path.$name))
                    unlink($path.$name);
                Tools::copy($path.$name_bk,$path.$name);
                if(file_exists($path.'fileType'))
                    @unlink($path.'fileType');
                return Tools::ps_round(filesize($path.$name_bk)/1024,2);
            }
            elseif(file_exists($path.$name))
            {
                if(!file_exists($path.$name_bk))
                    Tools::copy($path.$name,$path.$name_bk);
                if(file_exists($path.'fileType'))
                    @unlink($path.'fileType');
                return Tools::ps_round(filesize($path.$name)/1024,2);
            }
        }
        return 0;

    }
    public function getLinkTable($table,$type='')
    {
        if($table=='category')
            return $this->getBaseLink().'/img/c/';
        elseif($table=='manufacturer')
            return $this->getBaseLink().'/img/m/';
        elseif($table=='blog_post')
            return $this->getBaseLink().'/modules/ybc_blog/views/img/post/'.($type=='thumb' ? 'thumb/':'');
        elseif($table=='blog_category')
            return $this->getBaseLink().'/modules/ybc_blog/views/img/category/'.($type=='thumb' ? 'thumb/':'');
        elseif($table=='blog_gallery')
            return $this->getBaseLink().'/modules/ybc_blog/views/img/gallery/'.($type=='thumb' ? 'thumb/':'');
        else
            return $this->getBaseLink().'/img/su/';
    }
    public static function createImage($path,$type,$optimizied=false)
    {
        $tgt_width = $tgt_height = 0;
        $src_width = $src_height = 0;
        $error = 0;
        if(file_exists($path.'.jpg'))
        {
            if(@file_exists($path.'-'.Tools::stripslashes($type['name']).'.jpg') && $optimizied)
            {
                @unlink($path.'-'.Tools::stripslashes($type['name']).'.jpg');
            }
            if(!@file_exists($path.'-'.Tools::stripslashes($type['name']).'.jpg'))
            {
                ImageManager::resize(
                    $path.'.jpg',
                    $path.'-'.Tools::stripslashes($type['name']).'.jpg',
                    $type['width'],
                    $type['height'],
                    'jpg',
                    false,
                    $error,
                    $tgt_width,
                    $tgt_height,
                    5,
                    $src_width,
                    $src_height
                );
            }
        }
        if(file_exists($path.'-'.Tools::stripslashes($type['name']).'.jpg'))
            return Tools::ps_round(filesize($path.'-'.Tools::stripslashes($type['name']).'.jpg')/1024,2);
        else
            return false;
    }
    public function optimizeNewImageProduct($params)
    {
        $id_image = isset($params['id_image']) ? (int)$params['id_image']:false;
        if($id_image && ($type_product=Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_PRODCUT_TYPE')) )
        {
            $quality= ($quality = (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE')) > 0 ? $quality : 90;
            $new_image= new Image($id_image);
            $path = $new_image->getPathForCreation();
            $types= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE products=1 AND  name IN ("'.implode('","',array_map('pSQL',explode(',',$type_product))).'")');
            if($types)
            {
                foreach($types as $type)
                {
                    $ETS_IMGCOMPRESSOR_UPDATE_QUALITY = (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_UPDATE_QUALITY');
                    $ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
                    if($ETS_IMGCOMPRESSOR_UPDATE_QUALITY)
                        $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image = '.(int)$id_image.' AND type_image="'.pSQL($type['name']).'" AND quality!=100';
                    else
                        $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image ="'.(int)$id_image.'" AND type_image ="'.pSQL($type['name']).'"'.($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT !='tynypng' || $quality==100 || !$ETS_IMGCOMPRESSOR_UPDATE_QUALITY ? ' AND quality="'.(int)$quality.'"':' AND quality!=100').' AND optimize_type = "'.pSQL($ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT).'"';
                    if(!Db::getInstance()->getRow($sql))
                    {
                        $optimizied= (int)Db::getInstance()->getValue('SELECT id_image FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image ="'.(int)$id_image.'" AND type_image ="'.pSQL($type['name']).'"');
                        if($size_old= self::createImage($path,$type,$optimizied))
                        {
                            if($this->checkOptimizeImageResmush())
                            {
                                $product_class = new Product($new_image->id_product,false, Context::getContext()->language->id);
                                $url_image= Context::getContext()->link->getImageLink($product_class->link_rewrite,$new_image->id,$type['name']);
                            }
                            else
                                $url_image=null;
                            $quality_old = Db::getInstance()->getValue('SELECT quality FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image ="'.(int)$id_image.'" AND type_image ="'.pSQL($type['name']).'"');
                            $compress = $this->compress($path,$type,$quality,$url_image,$quality_old,true);
                            while($compress===false)
                            {
                                $compress = $this->compress($path,$type,$quality,$url_image,$quality_old,true);
                            }
                            if(!$optimizied)
                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_product_image` (id_image,type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$id_image.'","'.pSQL($type['name']).'","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQL($compress['optimize_type']).'")');
                            else
                                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_product_image` SET quality ="'.(int)$quality.'",size_new="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",size_old="'.(float)$size_old.'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE id_image ="'.(int)$id_image.'" AND type_image ="'.pSQL($type['name']).'"');
                        }
                    }
                }
            }
        }
    }
    public function optimizeProductImage($all_type=false,$update_quality=0)
    {
        $quality=Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') :50;
        $optmize_script= Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        $product_types = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_PRODCUT_TYPE');
        if($all_type)
            $types= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE products=1');
        else
            $types= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE products=1 AND  name IN ("'.implode('","',array_map('pSQL',explode(',',$product_types))).'")');
        $ok=false;
        if($types)
        {
            foreach($types as $type)
            {
                if($update_quality && $quality!=100)
                    $and_quality = ' AND pi.quality!=100';
                else
                    $and_quality = ($optmize_script !='tynypng'  || $quality==100 || !$update_quality ? ' AND pi.quality="'.(int)$quality.'"':' AND pi.quality!=100').($quality!=100 ? ' AND pi.optimize_type = "'.pSQL($optmize_script).'"':'');
                $images = Db::getInstance()->executeS('
                SELECT i.id_image FROM `'._DB_PREFIX_.'image` i
                LEFT JOIN `'._DB_PREFIX_.'ets_imagecompressor_product_image` pi ON i.id_image = pi.id_image AND pi.type_image="'.pSQL($type['name']).'"'.(string)$and_quality.'
                WHERE pi.id_image is NULL LIMIT 0 ,'.(int)$this->number_optimize);
                if($images)
                {

                    $ok=true;
                    foreach($images as $image)
                    {
                        $image_obj = new Image($image['id_image']);
                        $path = $image_obj->getPathForCreation();
                        foreach($types as $type)
                        {
                            if($update_quality && $quality!=100)
                                $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image = '.(int)$image['id_image'].' AND type_image="'.pSQL($type['name']).'" AND quality!=100';
                            else
                                $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image = '.(int)$image['id_image'].' AND type_image="'.pSQL($type['name']).'"'.($optmize_script !='tynypng' || $quality==100 ? ' AND quality="'.(int)$quality.'"':' AND quality!=100').($quality!=100 ? ' AND optimize_type = "'.pSQL($optmize_script).'"':'');
                            if(!Db::getInstance()->getRow($sql))
                            {
                                $optimizied = (int)Db::getInstance()->getRow('SELECT id_image FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image = '.(int)$image['id_image'].' AND type_image="'.pSQL($type['name']).'"');
                                if($size_old = self::createImage($path,$type,$optimizied))
                                {
                                    if($this->checkOptimizeImageResmush() )
                                    {
                                        $product_class= new Product($image_obj->id_product,false,Context::getContext()->language->id);
                                        $url_image= Context::getContext()->link->getImageLink($product_class->link_rewrite,$image_obj->id,$type['name']);
                                    }
                                    else
                                        $url_image=null;
                                    $quality_old = Db::getInstance()->getValue('SELECT quality FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE id_image = '.(int)$image['id_image'].' AND type_image="'.pSQL($type['name']).'"');
                                    $compress = $this->compress($path,$type,$quality,$url_image,$quality_old,true);
                                    while($compress===false)
                                    {
                                        $compress = $this->compress($path,$type,$quality,$url_image,$quality_old,true);
                                    }
                                    if(!$optimizied)
                                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_product_image` (id_image,type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$image['id_image'].'","'.pSQL($type['name']).'","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQL($compress['optimize_type']).'")');
                                    else
                                    {
                                        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_product_image` SET quality ="'.(int)$quality.'",size_old="'.(float)$size_old.'",size_new ="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE id_image ="'.(int)$image['id_image'].'" AND type_image ="'.pSQL($type['name']).'"');
                                    }
                                }
                                else
                                {
                                    if(!$optimizied)
                                        Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_product_image` (id_image,type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$image['id_image'].'","'.pSQL($type['name']).'","'.(int)$quality.'","0","0","'.($optmize_script ? pSQL($optmize_script):'php').'")');
                                    else
                                        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_product_image` SET quality ="'.(int)$quality.'",size_old="0",size_new ="0",optimize_type="'.($optmize_script ? pSQL($optmize_script):'php').'" WHERE id_image ="'.(int)$image['id_image'].'" AND type_image ="'.pSQL($type['name']).'"');
                                }
                                $this->_saveTotalImageOpimized($path.'-'.Tools::stripslashes($type['name']).'.jpg');
                            }
                            elseif(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') =='tynypng' && !Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE quality="'.(int)$quality.'" AND id_image ="'.(int)$image['id_image'].'" AND type_image ="'.pSQL($type['name']).'"' ))
                            {
                                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_product_image` SET quality ="'.(int)$quality.'" WHERE id_image ="'.(int)$image['id_image'].'" AND type_image ="'.pSQL($type['name']).'"');
                                $this->_saveTotalImageOpimized($path.'-'.Tools::stripslashes($type['name']).'.jpg');
                            }

                        }
                    }
                }
                if (Module::isInstalled('ets_multilangimages') && Module::isEnabled('ets_multilangimages')) {
                    $ets_MultiLangImage = Module::getInstanceByName('ets_multilangimages');
                    $images = Db::getInstance()->executeS('
                    SELECT i.id_image_lang FROM `' . _DB_PREFIX_ . 'ets_image_lang` i
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` pi ON i.id_image_lang = pi.id_image_lang AND pi.type_image="' . pSQL($type['name']) . '"' . (string)$and_quality . '
                    WHERE pi.id_image_lang is NULL LIMIT 0 ,' . (int)$this->number_optimize);
                    if ($images) {
                        $ok = true;
                        foreach ($images as $image) {
                            $path = $ets_MultiLangImage->getPathForCreation($image['id_image_lang']);
                            foreach ($types as $type) {
                                if ($update_quality && $quality != 100)
                                    $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` WHERE id_image_lang = ' . (int)$image['id_image_lang'] . ' AND type_image="' . pSQL($type['name']) . '" AND quality!=100';
                                else
                                    $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` WHERE id_image_lang = ' . (int)$image['id_image_lang'] . ' AND type_image="' . pSQL($type['name']) . '"' . ($optmize_script != 'tynypng' || $quality == 100 ? ' AND quality="' . (int)$quality . '"' : ' AND quality!=100') . ($quality != 100 ? ' AND optimize_type = "' . pSQL($optmize_script) . '"' : '');
                                if (!Db::getInstance()->getRow($sql)) {
                                    $optimizied = (int)Db::getInstance()->getValue('SELECT id_image_lang FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` WHERE id_image_lang = "' . (int)$image['id_image_lang'] . '" AND type_image like "' . pSQL($type['name']) . '"', false);
                                    if ($size_old = Ets_imagecompressor_optimize::createImage($path, $type, $optimizied)) {
                                        if ($this->checkOptimizeImageResmush()) {
                                            $url_image = $ets_MultiLangImage->getLangImageLink($image['id_image_lang'], $type['name']);
                                        } else
                                            $url_image = null;
                                        $quality_old = Db::getInstance()->getValue('SELECT quality FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` WHERE id_image_lang = ' . (int)$image['id_image_lang'] . ' AND type_image="' . pSQL($type['name']) . '"');
                                        $compress = $this->compress($path, $type, $quality, $url_image, $quality_old);
                                        while ($compress === false) {
                                            $compress = $this->compress($path, $type, $quality, $url_image, $quality_old);
                                        }
                                        if (!$optimizied) {

                                            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` (id_image_lang,type_image,quality,size_old,size_new,optimize_type) VALUES("' . (int)$image['id_image_lang'] . '","' . pSQL($type['name']) . '","' . (int)$quality . '","' . (float)$size_old . '","' . ($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old) . '","' . pSQL($compress['optimize_type']) . '")');
                                        } else
                                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` SET quality ="' . (int)$quality . '",size_old="' . (float)$size_old . '",size_new ="' . ($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old) . '",optimize_type="' . pSQL($compress['optimize_type']) . '" WHERE id_image_lang ="' . (int)$image['id_image_lang'] . '" AND type_image ="' . pSQL($type['name']) . '"');
                                    } else {
                                        if (!$optimizied) {
                                            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` (id_image_lang,type_image,quality,size_old,size_new,optimize_type) VALUES("' . (int)$image['id_image_lang'] . '","' . pSQL($type['name']) . '","' . (int)$quality . '","0","0","' . ($optmize_script ? pSQL($optmize_script) : 'php') . '")');
                                        } else
                                            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` SET quality ="' . (int)$quality . '",size_old="0",size_new ="0",optimize_type="' . ($optmize_script ? pSQL($optmize_script) : 'php') . '" WHERE id_image_lang ="' . (int)$image['id_image_lang'] . '" AND type_image ="' . pSQL($type['name']) . '"');
                                    }
                                    $this->_saveTotalImageOpimized($path . '-' . Tools::stripslashes($type['name']) . '.jpg');
                                } elseif (Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') == 'tynypng' && !Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` WHERE quality="' . (int)$quality . '" AND id_image_lang ="' . (int)$image['id_image_lang'] . '" AND type_image ="' . pSQL($type['name']) . '"')) {
                                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_imagecompressor_product_image_lang` SET quality ="' . (int)$quality . '" WHERE id_image_lang ="' . (int)$image['id_image_lang'] . '" AND type_image ="' . pSQL($type['name']) . '"');
                                    $this->_saveTotalImageOpimized($path . '-' . Tools::stripslashes($type['name']) . '.jpg');
                                }

                            }
                        }
                    }
                }
            }

        }
        if($ok)
        {
            die(
                json_encode(
                    array(
                        'resume' => true,
                        'optimize_type' => 'products',
                        'limit_optimized' => 0,
                    )
                )
            );
        }
        else
        {
            return true;
        }
    }
    public function _saveTotalImageOpimized($image)
    {
        $total_image_optimized = (int)Configuration::get('ETS_IMGCOMPRESSOR_TOTAL_IMAGE_OPTIMIZED')+1;
        Configuration::updateValue('ETS_IMGCOMPRESSOR_TOTAL_IMAGE_OPTIMIZED',$total_image_optimized);
        if($images = Configuration::get('ETS_IMGCOMPRESSOR_LIST_IMAGE_OPTIMIZED'))
        {
            $images = explode(',',$images);
            if(count($images)<5)
                $images[] = $image;
            else
                $images[4] = $image;
            Configuration::updateValue('ETS_IMGCOMPRESSOR_LIST_IMAGE_OPTIMIZED',implode(',',$images));
        }
        else
            Configuration::updateValue('ETS_IMGCOMPRESSOR_LIST_IMAGE_OPTIMIZED',$image);
        if($total_image_optimized &&  $total_image_optimized % $this->number_optimize==0)
            die('continue'.$total_image_optimized);

    }
    public function optimiziObjImage($table,$type_obj,$path,$all_type=false,$next='',$update_quality=0)
    {
        $optmize_script= Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        $quality=Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') :90;
        if($all_type)
            $types = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE '.pSQL($type_obj).'=1');
        else
            $types= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE '.pSQL($type_obj).'=1 AND  name IN ("'.implode('","',array_map('pSQL',explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_'.Tools::strtoupper($table).'_TYPE')))).'")');
        $ok=false;
        if($types)
        {
            if($types)
            {
                foreach($types as $type)
                {
                    if($update_quality && $quality!=100)
                        $and_quality = ' AND pi.quality!=100';
                    else
                        $and_quality = ($optmize_script !='tynypng'  || $quality==100 || !$update_quality ? ' AND pi.quality="'.(int)$quality.'"':' AND pi.quality!=100').($quality!=100 ? ' AND pi.optimize_type = "'.pSQL($optmize_script).'"':'');
                    $objects = Db::getInstance()->executeS('
                    SELECT o.id_'.pSQL($table).' FROM '._DB_PREFIX_.pSQL($table).' o
                    LEFT JOIN '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image pi ON o.id_'.pSQL($table).' = pi.id_'.pSQL($table).' AND pi.type_image="'.pSQL($type['name']).'"'.(string)$and_quality.'
                    WHERE pi.id_'.pSQL($table).' is NULL LIMIT 0 ,'.(int)$this->number_optimize);
                    if($objects)
                    {
                        $ok=true;
                        foreach($objects as $object)
                        {
                            $path_image =$path.$object['id_'.$table];
                            if($update_quality && $quality!=100)
                                $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'" AND quality!=100';
                            else
                                $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'"'.($optmize_script !='tynypng' || $quality==100 ? ' AND quality="'.(int)$quality.'"':' AND quality!=100').($quality!=100  ? ' AND optimize_type = "'.pSQL($optmize_script).'"':'');
                            if(!Db::getInstance()->getRow($sql))
                            {
                                $optimizied = Db::getInstance()->getValue('SELECT id_'.pSQL($table).' FROM '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'"');
                                if($size_old = Ets_imagecompressor_optimize::createImage($path_image,$type,$optimizied))
                                {
                                    if($this->checkOptimizeImageResmush())
                                        $url_image= $this->getLinkTable($table).$object['id_'.$table].'-'.$type['name'].'.jpg';
                                    else
                                        $url_image=null;
                                    $quality_old = Db::getInstance()->getValue('SELECT quality FROM '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'" AND optimize_type = "'.pSQL(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT')).'"');
                                    $compress= $this->compress($path_image,$type,$quality,$url_image,$quality_old);
                                    while($compress===false)
                                        $compress= $this->compress($path_image,$type,$quality,$url_image,$quality_old);
                                    if(!$optimizied)
                                    {
                                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image (id_'.pSQL($table).',type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$object['id_'.$table].'","'.pSQL($type['name']).'","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQl($compress['optimize_type']).'")');
                                    }
                                    else
                                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image SET quality="'.(int)$quality.'",size_old="'.(float)$size_old.'",size_new="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'"');
                                    $this->_saveTotalImageOpimized($path.'-'.Tools::stripslashes($type['name']).'.jpg');
                                }
                                else
                                {
                                    if(!$optimizied)
                                    {
                                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image (id_'.pSQL($table).',type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$object['id_'.$table].'","'.pSQL($type['name']).'","'.(int)$quality.'","0","0","'.pSQl($optmize_script).'")');
                                    }
                                    else
                                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image SET quality="'.(int)$quality.'",size_old="0",size_new="0",optimize_type="'.pSQL($optmize_script).'" WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'"');
                                }
                            }elseif(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') =='tynypng' && !Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image WHERE quality="'.(int)$quality.'" AND id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'"'))
                            {
                                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_'.pSQL($table).'_image SET quality="'.(int)$quality.'" WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type['name']).'"');
                                $this->_saveTotalImageOpimized($path.'-'.Tools::stripslashes($type['name']).'.jpg');
                            }
                        }
                    }
                }
            }
        }
        unset($next);
        if($ok)
            die(
                json_encode(
                    array(
                        'resume' => true,
                        'optimize_type' => $type_obj,
                        'limit_optimized' => 0,
                    )
                )
            );
        else
        {
            return true;
        }
    }
    public function optimiziBlogImage($table,$path,$all_type=false,$next='',$update_quality=0)
    {
        $ybc_blog = Module::getInstanceByName('ybc_blog');
        if (version_compare($ybc_blog->version, '3.2.0', '>'))
            return $this->optimiziBlogImage_3_2_0($table, $path, $all_type, $next,$update_quality);
        $quality=Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') :90;
        $optmize_script = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        if($all_type)
            if($table=='slide')
                $types=array('image');
            else
                $types = array('image','thumb');
        else
            $types = explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_'.Tools::strtoupper($table).'_TYPE'));
        $ok= false;
        if($types)
        {
            foreach($types as $type)
            {
                if($type)
                {

                    if($type=='thumb')
                        $path .='thumb/';
                    if($update_quality&& $quality!=100)
                        $end_quality = ' AND quality!=100';
                    else
                        $end_quality = ($optmize_script !='tynypng' || $quality==100 || !$update_quality ? ' AND quality="'.(int)$quality.'"':' AND quality!=100').($quality!=100 ? ' AND optimize_type = "'.pSQL($optmize_script).'"':'');
                    $objects = Db::getInstance()->executeS('SELECT bl.* FROM '._DB_PREFIX_.'ybc_blog_'.pSQL($table).' bl
                    LEFT JOIN '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image bli ON type_image="'.pSQL($type).'" AND bli.id_'.pSQL($table).'=bl.id_'.pSQL($table).(string)$end_quality.'
                    WHERE bli.id_'.pSQL($table).' is NULL LIMIT 0,'.(int)$this->number_optimize);
                    if($objects)
                    {
                        $ok=true;
                        foreach($objects as $object)
                        {
                            if($update_quality && $quality!=100)
                                $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type).'" AND quality!=100';
                            else
                                $sql = 'SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type).'"'.($optmize_script !='tynypng' || $quality==100 ? ' AND quality="'.(int)$quality.'"':' AND quality!=100').($quality!=100 ? ' AND optimize_type = "'.pSQL($optmize_script).'"':'');
                            if(!Db::getInstance()->getRow($sql))
                            {

                                $optimizied = Db::getInstance()->getValue('SELECT id_'.pSQL($table).' FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type).'"');
                                if($size_old = self::createBlogImage($path,$object[$type]))
                                {
                                    if($this->checkOptimizeImageResmush())
                                        $url_image= $this->getLinkTable('blog_'.$table,$type).$object[$type];
                                    else
                                        $url_image=null;
                                    $compress= $this->compress($path,$object[$type],$quality,$url_image,false);
                                    while($compress===false)
                                        $compress= $this->compress($path,$object[$type],$quality,$url_image,false);
                                    if(!$optimizied)
                                    {
                                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image (id_'.pSQL($table).',type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$object['id_'.$table].'","'.pSQL($type).'","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQl($compress['optimize_type']).'")');
                                    }
                                    else
                                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image SET quality="'.(int)$quality.'",size_old="'.(float)$size_old.'",size_new="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type).'"');
                                    $this->_saveTotalImageOpimized($path.$object[$type]);
                                }
                                else
                                {
                                    if(!$optimizied)
                                    {
                                        Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image (id_'.pSQL($table).',type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$object['id_'.$table].'","'.pSQL($type).'","'.(int)$quality.'","0","0","'.pSQl($optmize_script).'")');
                                    }
                                    else
                                        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image SET quality="'.(int)$quality.'",size_old="0",size_new="0",optimize_type="'.pSQL($optmize_script).'" WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type).'"');
                                }
                            }elseif(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') =='tynypng' && !Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE quality="'.(int)$quality.'" AND id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type).'"'))
                            {
                                Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image SET quality="'.(int)$quality.'" WHERE id_'.pSQL($table).' = '.(int)$object['id_'.$table].' AND type_image="'.pSQL($type).'"');
                                $this->_saveTotalImageOpimized($path.$object[$type]);
                            }
                        }
                    }
                }
            }
        }
        unset($next);
        if($ok)
            die(
                json_encode(
                    array(
                        'resume' => true,
                        'optimize_type' => $table,
                        'limit_optimized' => 0,
                    )
                )
            );
        else
            return true;
    }
    public function optimiziBlogImage_3_2_0($table,$path,$all_type=false,$next='',$update_quality=0)
    {
        $quality = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') : 90;
        $optmize_script = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT');
        if ($all_type)
            if ($table == 'slide')
                $types = array('image');
            else
                $types = array('image', 'thumb');
        else
            $types = explode(',', Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_BLOG_' . Tools::strtoupper($table) . '_TYPE'));
        $ok = false;
        if ($types) {
            foreach ($types as $type) {
                if ($type) {
                    if ($type == 'thumb')
                        $path .= 'thumb/';
                    if ($update_quality && $quality != 100)
                        $end_quality = ' AND quality!=100';
                    else
                        $end_quality = ($optmize_script != 'tynypng' || $quality == 100 || !$update_quality ? ' AND quality="' . (int)$quality . '"' : ' AND quality!=100') . ($quality != 100 ? ' AND optimize_type = "' . pSQL($optmize_script) . '"' : '');
                    $objects = Db::getInstance()->executeS('SELECT bl.* FROM `' . _DB_PREFIX_ . 'ybc_blog_' . pSQL($table) . '_lang` bl
                    LEFT JOIN `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` bli ON bl.' . pSQL($type) . ' = bli.' . pSQL($type) . ' AND type_image="' . pSQL($type) . '" AND bli.' . pSQL($type) . '!="" AND bli.id_' . pSQL($table) . '=bl.id_' . pSQL($table) . (string)$end_quality . '
                    WHERE bli.id_' . pSQL($table) . ' is NULL AND bl.' . pSQL($type) . '!="" LIMIT 0,' . (int)$this->number_optimize);
                    if ($objects) {
                        $ok = true;
                        foreach ($objects as $object) {
                            if ($update_quality && $quality != 100)
                                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` WHERE id_' . pSQL($table) . ' = ' . (int)$object['id_' . $table] . ' AND type_image="' . pSQL($type) . '" AND quality!=100 AND ' . pSQL($type) . ' = "' . pSQL($object[$type]) . '"';
                            else
                                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` WHERE id_' . pSQL($table) . ' = ' . (int)$object['id_' . $table] . ' AND type_image="' . pSQL($type) . '" AND ' . pSQL($type) . ' = "' . pSQL($object[$type]) . '"' . ($optmize_script != 'tynypng' || $quality == 100 ? ' AND quality="' . (int)$quality . '"' : ' AND quality!=100') . ($quality != 100 ? ' AND optimize_type = "' . pSQL($optmize_script) . '"' : '');
                            if (!Db::getInstance()->getRow($sql)) {
                                $optimizied = Db::getInstance()->getValue('SELECT id_' . pSQL($table) . ' FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` WHERE id_' . pSQL($table) . ' = ' . (int)$object['id_' . $table] . ' AND type_image="' . pSQL($type) . '" AND ' . pSQL($type) . ' = "' . pSQL($object[$type]) . '"');
                                if ($size_old = self::createBlogImage($path, $object[$type])) {

                                    if ($this->checkOptimizeImageResmush())
                                        $url_image = $this->getLinkTable('blog_' . $table, $type) . $object[$type];
                                    else
                                        $url_image = null;
                                    $compress = $this->compress($path, $object[$type], $quality, $url_image, false);
                                    while ($compress === false)
                                        $compress = $this->compress($path, $object[$type], $quality, $url_image, false);

                                    if (!$optimizied) {
                                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` (id_' . pSQL($table) . ',type_image,quality,size_old,size_new,optimize_type,`' . pSQL($type) . '`) VALUES("' . (int)$object['id_' . $table] . '","' . pSQL($type) . '","' . (int)$quality . '","' . (float)$size_old . '","' . ($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old) . '","' . pSQl($compress['optimize_type']) . '","' . pSQL($object[$type]) . '")');
                                    } else
                                        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` SET quality="' . (int)$quality . '",size_old="' . (float)$size_old . '",size_new="' . ($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old) . '",optimize_type="' . pSQL($compress['optimize_type']) . '" WHERE id_' . pSQL($table) . ' = ' . (int)$object['id_' . $table] . ' AND type_image="' . pSQL($type) . '" AND `' . pSQL($type) . '` = "' . pSQL($object[$type]) . '"');
                                    $this->_saveTotalImageOpimized($path . $object[$type]);
                                } else {
                                    if (!$optimizied) {
                                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` (id_' . pSQL($table) . ',type_image,quality,size_old,size_new,optimize_type,`' . (pSQL($type)) . '`) VALUES("' . (int)$object['id_' . $table] . '","' . pSQL($type) . '","' . (int)$quality . '","0","0","' . pSQl($optmize_script) . '","' . pSQL($object[$type]) . '")');
                                    } else
                                        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` SET quality="' . (int)$quality . '",size_old="0",size_new="0",optimize_type="' . pSQL($optmize_script) . '" WHERE id_' . pSQL($table) . ' = ' . (int)$object['id_' . $table] . ' AND type_image="' . pSQL($type) . '" AND ' . pSQL($type) . ' = "' . pSQL($object[$type]) . '" ');
                                }
                            } elseif (Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') == 'tynypng' && !Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` WHERE quality="' . (int)$quality . '" AND id_' . pSQL($table) . ' = ' . (int)$object['id_' . $table] . ' AND type_image="' . pSQL($type) . '" AND ' . pSQL($type) . ' = "' . pSQL($object[$type]) . '" ')) {
                                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_imagecompressor_blog_' . pSQL($table) . '_image` SET quality="' . (int)$quality . '" WHERE id_' . pSQL($table) . ' = ' . (int)$object['id_' . $table] . ' AND type_image="' . pSQL($type) . '" AND ' . pSQL($type) . ' = "' . pSQL($object[$type]) . '"');
                                $this->_saveTotalImageOpimized($path . $object[$type]);
                            }
                        }
                    }
                }
            }
        }
        unset($next);
        if ($ok)
            die(
                json_encode(
                    array(
                        'resume' => true,
                        'optimize_type' => $table,
                        'limit_optimized' => 0,
                    )
                )
            );
        else
            return true;
    }
    public function optimiziSlideImage($all_type=false,$update_quality=0,$limit=0)
    {
        if($all_type || Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_HOME_SLIDE_TYPE'))
        {

            $homeSlides = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'homeslider_slides_lang` LIMIT '.(int)$limit.','.(int)$this->number_optimize);
            $quality=Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') :90;
            $total_images = Ets_imagecompressor_defines::getTotalImage('home_slide',true,false,false,$all_type) - Ets_imagecompressor_defines::getTotalImage('home_slide',true,true,false,$all_type);
            if($homeSlides && $total_images >0 )
            {
                $path = _PS_MODULE_DIR_.($this->is17 ? 'ps_imageslider':'homeslider').'/images/';
                foreach($homeSlides as $homeSlide)
                {
                    if( $update_quality && $quality!=100)
                        $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` WHERE id_homeslider_slides = "'.(int)$homeSlide['id_homeslider_slides'].'" AND image="'.pSQL($homeSlide['image']).'" AND quality!=100';
                    else
                        $sql ='SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` WHERE id_homeslider_slides ="'.(int)$homeSlide['id_homeslider_slides'].'" AND image = "'.pSQL($homeSlide['image']).'"'.(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') !='tynypng' || $quality==100 ? ' AND quality="'.(int)$quality.'"':' AND quality!=100').($quality!=100 ? ' AND optimize_type = "'.pSQL(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT')).'"':'');
                    if(!Db::getInstance()->getRow($sql))
                    {
                        if($size_old= self::createBlogImage($path,$homeSlide['image']))
                        {
                            if($this->checkOptimizeImageResmush())
                                $url_image= $this->getBaseLink().'/modules/'.($this->is17 ? 'ps_imageslider':'homeslider').'/images/'.$homeSlide['image'];
                            else
                                $url_image=null;
                            $compress= $this->compress($path,$homeSlide['image'],$quality,$url_image,false);
                            while($compress===false)
                                $compress= $this->compress($path,$homeSlide['image'],$quality,$url_image,false);
                            if(!Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` WHERE id_homeslider_slides="'.(int)$homeSlide['id_homeslider_slides'].'" AND image="'.pSQL($homeSlide['image']).'"'))
                            {
                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` (id_homeslider_slides,image,type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$homeSlide['id_homeslider_slides'].'","'.pSQL($homeSlide['image']).'", "image","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQl($compress['optimize_type']).'")');
                            }
                            else
                                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` SET quality="'.(int)$quality.'",size_old="'.(float)$size_old.'",size_new="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE id_homeslider_slides="'.(int)$homeSlide['id_homeslider_slides'].'" AND image="'.pSQL($homeSlide['image']).'"');
                            $this->_saveTotalImageOpimized($path.$homeSlide['image']);
                        }
                    }
                    elseif(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') =='tynypng' && !Db::getInstance()->getRow('SELECT *FROM `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` WHERE quality="'.(int)$quality.'" AND id_homeslider_slides="'.(int)$homeSlide['id_homeslider_slides'].'" AND image="'.pSQL($homeSlide['image']).'"'))
                    {
                        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` SET quality="'.(int)$quality.'" WHERE id_homeslider_slides="'.(int)$homeSlide['id_homeslider_slides'].'"  AND image="'.pSQL($homeSlide['image']).'"');
                        $this->_saveTotalImageOpimized($path.$homeSlide['image']);
                    }
                }
                die(
                    json_encode(
                        array(
                            'resume' => true,
                            'optimize_type' => 'home_slide',
                            'limit_optimized' => $limit+$this->number_optimize,
                        )
                    )
                );
            }
            if($total_images>0)
            {
                json_encode(
                    array(
                        'resume' => true,
                        'optimize_type' => 'other_image',
                        'limit_optimized' => 0,
                    )
                );
            }
            else
            {
                $_POST['limit_optimized'] =0;
                return true;
            }
        }
        else{
            json_encode(
                array(
                    'resume' => true,
                    'optimize_type' => 'other_image',
                    'limit_optimized' => 0,
                )
            );
        }
        return false;
    }
    public function optimiziOthersImage($all_type,$update_quality=0)
    {
        $quality=Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') ? Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE') :90;
        if($all_type)
            $types = array('logo','banner');
        else
            $types = explode(',',Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_OTHERS_TYPE'));
        if($types && Ets_imagecompressor_defines::getTotalImage('others',true,false,false,$all_type) - Ets_imagecompressor_defines::getTotalImage('others',true,true,false,$all_type)>0)
        {
            foreach($types as $type)
            {
                $images = array();
                if($type=='logo')
                {
                    if(Configuration::get('PS_LOGO'))
                        $images[] = Configuration::get('PS_LOGO');
                    $path = _PS_IMG_DIR_;
                }elseif($type=='banner')
                {
                    $languages = Language::getLanguages(false);
                    if($this->is17)
                    {
                        $path = _PS_MODULE_DIR_.'ps_banner/img/';
                        if(module::isInstalled('ps_banner') && Module::isEnabled('ps_banner'))
                        {
                            foreach($languages as $language)
                            {
                                if(($image = Configuration::get('BANNER_IMG',$language['id_lang'])) && !in_array($image,$images))
                                    $images[] = $image;
                            }
                        }
                    }
                    else
                    {
                        $path = _PS_MODULE_DIR_.'blockbanner/img/';
                        if(module::isInstalled('blockbanner') && Module::isEnabled('blockbanner'))
                        {
                            foreach($languages as $language)
                            {
                                if(($image = Configuration::get('BLOCKBANNER_IMG',$language['id_lang'])) && !in_array($image,$images))
                                    $images[] = $image;
                            }
                        }
                    }
                }
                elseif($type=='themeconfig')
                {
                    $path = _PS_MODULE_DIR_.'themeconfigurator/img/';
                    if(Module::isInstalled('themeconfigurator') && Module::isEnabled('themeconfigurator'))
                    {
                        $themeconfigurators = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'themeconfigurator` WHERE image!="" GROUP BY image');
                        if($themeconfigurators)
                        {
                            foreach($themeconfigurators as $themeconfigurator)
                                $images[]= $themeconfigurator['image'];
                        }
                    }
                }
                if($images)
                {
                    foreach($images as $image)
                    {

                        if($update_quality && $quality!=100)
                            $sql = 'SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_others_image` WHERE image = "'.pSQL($image).'" AND type_image="'.pSQL($type).'" AND quality!=100';
                        else
                            $sql ='SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_others_image` WHERE image="'.pSQL($image).'" AND type_image="'.pSQL($type).'"'.(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') !='tynypng' || $quality==100  ? ' AND quality="'.(int)$quality.'"':' AND quality!=100').($quality!=100 ? ' AND optimize_type = "'.pSQL(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT')).'"':'');
                        if(!Db::getInstance()->getRow($sql))
                        {
                            if($size_old = self::createBlogImage($path,$image))
                            {
                                if($this->checkOptimizeImageResmush())
                                {
                                    if($type=='logo')
                                        $url_image = $this->getBaseLink().'/'.$image;

                                    elseif($type=='banner')
                                    {
                                        $url_image = $this->getBaseLink().'/modules/'.($this->is17 ? 'ps_banner' : 'blockbanner').'/img/'.$image;
                                    }
                                    elseif($type=='themeconfig')
                                    {
                                        $url_image = $this->getBaseLink().'/modules/themeconfigurator/img/'.$image;
                                    }
                                    else
                                        $url_image = null;
                                }
                                else
                                    $url_image=null;
                                $optimizied = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'ets_imagecompressor_others_image` WHERE image="'.pSQL($image).'" AND type_image="'.pSQL($type).'"');
                                $compress= $this->compress($path,$image,$quality,$url_image,false);
                                while($compress===false)
                                    $compress= $this->compress($path,$image,$quality,$url_image,false);
                                if(!$optimizied)
                                {
                                    Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_others_image` (image,type_image,quality,size_old,size_new,optimize_type) VALUES("'.pSQL($image).'","'.pSQL($type).'","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQl($compress['optimize_type']).'")');
                                }
                                else
                                    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_others_image` SET quality="'.(int)$quality.'",size_old="'.(float)$size_old.'",size_new="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE image="'.pSQL($image).'" AND type_image="'.pSQL($type).'"');
                                $this->_saveTotalImageOpimized($path.$image);
                            }
                        }elseif(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT') =='tynypng' && !Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'ets_imagecompressor_others_image` WHERE quality="'.(int)$quality.'" AND image="'.pSQL($image).'" AND type_image="'.pSQL($type).'"'))
                        {
                            Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ets_imagecompressor_others_image` SET quality="'.(int)$quality.'" WHERE image="'.pSQL($image).'" AND type_image="'.pSQL($type).'"');
                            $this->_saveTotalImageOpimized($path.$image);
                        }

                    }
                }
            }
        }
        return true;
    }
    public function getTotalSizeSave($quality,$check_quality)
    {
        $sql = 'SELECT sum(total_old) as old,sum(total_new) as new
            FROM (SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_product_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
            ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_category_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
            ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_supplier_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
            ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_manufacturer_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
            ($this->isblog ? ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_post_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
                ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_category_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
                ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_slide_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
                ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_home_slide_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
                ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_others_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100').
                ' UNION ALL
            SELECT sum(size_old) as total_old,sum(size_new) as total_new FROM `'._DB_PREFIX_.'ets_imagecompressor_blog_gallery_image` WHERE size_new < size_old'.($check_quality ? ' AND quality="'.(int)$quality.'"' :' AND quality!=100') :'').') t';
        $total = Db::getInstance()->getRow($sql);
        $total_save= $total['old'] - $total['new'];
        $total_old = $total['old'];
        if($total_save)
            $percent_save = ($total_save/$total_old) * 100;
        if($total_save <1024)
            $total_text ='KB';
        else
        {
            $total_save = $total_save/1024;
            if($total_save<1024)
                $total_text='Mb';
            else
            {
                $total_save= $total_save/1024;
                $total_text='Gb';
            }
        }
        return $total_save >0 ? $this->l('save').' '.Tools::ps_round($total_save,2).$total_text.' ('.Tools::ps_round($percent_save,2).'%)' :'';
    }
    public function actionUpdateBlogImage($params)
    {
        $ybc_blog = Module::getInstanceByName('ybc_blog');
        if(isset($params['id_post']))
        {
            $table = 'blog';
            $path = version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'post/':   _PS_MODULE_DIR_.'ybc_blog/views/img/post/';
            $id_obj = $params['id_post'];
        }
        if(isset($params['id_category']))
        {
            $table = 'category';
            $path = version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'category/': _PS_MODULE_DIR_.'ybc_blog/views/img/category/';
            $id_obj = $params['id_category'];
        }
        if(isset($params['id_gallery']))
        {
            $table = 'gallery';
            $path = version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'gallery/': _PS_MODULE_DIR_.'ybc_blog/views/img/gallery/';
            $id_obj = $params['id_gallery'];
        }
        if(isset($params['id_slide']))
        {
            $table = 'slide';
            $path = version_compare($ybc_blog->version, '3.2.0', '>') ? _PS_YBC_BLOG_IMG_DIR_.'slide/': _PS_MODULE_DIR_.'ybc_blog/views/img/slide/';
            $id_obj = $params['id_slide'];
        }
        $quality = ($quality= (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE')) >0 ? $quality : 90;
        if(isset($params['image']) && $params['image'])
        {
            $type = 'image';
            if($size_old= self::createBlogImage($path,$params['image']))
            {
                if($this->checkOptimizeImageResmush())
                    $url_image= $this->getLinkTable('blog_'.$table,'image').$params['image'];
                else
                    $url_image=null;
                $quality_old = Db::getInstance()->getValue('SELECT quality FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$id_obj.' AND type_image="'.pSQL($type).'" AND optimize_type = "'.pSQL(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT')).'"');
                $compress = $this->compress($path,$params['image'],$quality,$url_image,$quality_old);
                while($compress===false)
                {
                    $compress = $this->compress($path,$params['image'],$quality,$url_image,$quality_old);
                }
                if(!Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = "'.(int)$id_obj.'" AND type_image="'.pSQL($type).'"'))
                {
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image (id_'.pSQL($table).',type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$id_obj.'","'.pSQL($type).'","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQl($compress['optimize_type']).'")');
                }
                else
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image SET quality="'.(int)$quality.'",size_old="'.(float)$size_old.'",size_new="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE id_'.pSQL($table).' = '.(int)$id_obj.' AND type_image="'.pSQL($type).'"');
            }
        }
        if(isset($params['thumb']) && $params['thumb'])
        {
            $type = 'thumb';
            $path .= 'thumb/';
            if($size_old = self::createBlogImage($path,$params['thumb']))
            {
                if($this->checkOptimizeImageResmush())
                    $url_image= $this->getLinkTable('blog_'.$table,'thumb').$params['thumb'];
                else
                    $url_image=null;
                $quality_old = Db::getInstance()->getValue('SELECT quality FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = '.(int)$id_obj.' AND type_image="'.pSQL($type).'" AND optimize_type = "'.pSQL(Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_SCRIPT')).'"');
                $compress= $this->compress($path,$params['thumb'],$quality,$url_image,$quality_old);
                while($compress===false)
                {
                    $compress = $this->compress($path,$params['thumb'],$quality,$url_image,$quality_old);
                }
                if(!Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image WHERE id_'.pSQL($table).' = "'.(int)$id_obj.'" AND type_image="'.pSQL($type).'"'))
                {
                    Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image (id_'.pSQL($table).',type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$id_obj.'","'.pSQL($type).'","'.(int)$quality.'","'.(float)$size_old.'","'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'","'.pSQl($compress['optimize_type']).'")');
                }
                else
                    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'ets_imagecompressor_blog_'.pSQL($table).'_image SET quality="'.(int)$quality.'",size_old="'.(float)$size_old.'",size_new="'.($compress['file_size'] < $size_old ? (float)$compress['file_size'] : (float)$size_old).'",optimize_type="'.pSQL($compress['optimize_type']).'" WHERE id_'.pSQL($table).' = '.(int)$id_obj.' AND type_image="'.pSQL($type).'"');
            }
        }
    }
    public static function optimizeImageSupplier($id_supplier)
    {
        $path = _PS_SUPP_IMG_DIR_.$id_supplier;
        $quality= ($quality = (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE')) >0 ? $quality:90;
        if(isset($_FILES) && count($_FILES) && ($supplier_types = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_SUPPLIER_TYPE')) && file_exists($path.'.jpg'))
        {
            $types= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE suppliers=1 AND  name IN ("'.implode('","',array_map('pSQL',explode(',',$supplier_types))).'")');
            if($types)
            {
                foreach($types as $type)
                {
                    if($size_old = self::createImage($path,$type))
                    {
                        if(self::getInstance()->checkOptimizeImageResmush())
                            $url_image= self::getInstance()->getLinkTable('supplier').$id_supplier.'-'.$type['name'].'.jpg';
                        else
                            $url_image=null;
                        $compress = Module::getInstanceByName('ets_imagecompressor')->compress($path,$type,$quality,$url_image,0);
                        if($compress)
                        {
                            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_imagecompressor_supplier_image` WHERE id_supplier="'.(int)$id_supplier.'" AND type_image="'.pSQL($type['name']).'"');
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_supplier_image` (id_supplier,type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$id_supplier.'","'.pSQL($type['name']).'","'.(int)$quality.'","'.(float)$size_old.'","'.(float)$compress['file_size'].'","'.pSQL($compress['optimize_type']).'")');
                        }
                    }
                }
            }
        }
        return true;
    }
    public static function optimizeCategoryImage($id_category)
    {
        $path = _PS_CAT_IMG_DIR_.$id_category;
        $quality = ($quality = (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE')) >0 ? $quality :90;
        if(isset($_FILES) && count($_FILES) && ($category_types = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_CATEGORY_TYPE')) && file_exists($path.'.jpg'))
        {
            $types= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE categories=1 AND  name IN ("'.implode('","',array_map('pSQL',explode(',',$category_types))).'")');
            if($types)
            {
                foreach($types as $type)
                {
                    if($size_old = self::createImage($path,$type)) {
                        if (self::getInstance()->checkOptimizeImageResmush())
                            $url_image = self::getInstance()->getLinkTable('category') . $id_category . '-' . $type['name'] . '.jpg';
                        else
                            $url_image = null;
                        $compress = Module::getInstanceByName('ets_imagecompressor')->compress($path, $type, $quality, $url_image, 0);
                        if ($compress)
                        {
                            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_imagecompressor_category_image` WHERE id_category="'.(int)$id_category.'" AND type_image="'.pSQL($type['name']).'"');
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_category_image` (id_category,type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$id_category.'","'.pSQL($type['name']).'","'.(int)$quality.'","'.(float)$size_old.'","'.(float)$compress['file_size'].'","'.pSQL($compress['optimize_type']).'")');
                        }
                    }
                }
            }
        }
        return true;
    }
    public static function optimizeManufacturerImage($id_manufacturer)
    {
        $path = _PS_MANU_IMG_DIR_.$id_manufacturer;
        $quality= ($quality = (int)Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_QUALITY_OPTIMIZE')) >0 ? $quality:90;
        if(isset($_FILES) && count($_FILES) && ($manu_types = Configuration::getGlobalValue('ETS_IMGCOMPRESSOR_OPTIMIZE_IMAGE_MANUFACTURER_TYPE')) && file_exists($path.'.jpg'))
        {
            $types= Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'image_type` WHERE manufacturers=1 AND  name IN ("'.implode('","',array_map('pSQL',explode(',',$manu_types))).'")');
            if($types)
            {
                foreach($types as $type)
                {
                    if($size_old = self::createImage($path,$type))
                    {
                        if(self::getInstance()->checkOptimizeImageResmush())
                            $url_image = self::getInstance()->getLinkTable('manufacturer').$id_manufacturer.'-'.$type['name'].'.jpg';
                        else
                            $url_image=null;
                        $compress = Module::getInstanceByName('ets_imagecompressor')->compress($path,$type,$quality,$url_image,0);
                        if($compress)
                        {
                            Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ets_imagecompressor_manufacturer_image` WHERE id_manufacturer="'.(int)$id_manufacturer.'" AND type_image="'.pSQL($type['name']).'"');
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ets_imagecompressor_manufacturer_image` (id_manufacturer,type_image,quality,size_old,size_new,optimize_type) VALUES("'.(int)$id_manufacturer.'","'.pSQL($type['name']).'","'.(int)$quality.'","'.(float)$size_old.'","'.(float)$compress['file_size'].'","'.pSQL($compress['optimize_type']).'")');
                        }
                    }
                }
            }
        }
        return true;
    }
}