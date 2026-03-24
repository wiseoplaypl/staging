<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/
include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/../../init.php';

require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCf.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfValues.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfRecipients.php';

if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/')) {
   mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/', 0777);
}

if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer'))) {
   mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer'), 0777);
}

if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer').'/'.(int) Tools::getValue('idProduct'))) {
   mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer').'/'.(int) Tools::getValue('idProduct'), 0777);
}

if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer').'/'.(int) Tools::getValue('idProduct').'/'.(int) Tools::getValue('idCustomization'))) {
   mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer').'/'.(int) Tools::getValue('idProduct').'/'.(int) Tools::getValue('idCustomization'), 0777);
}

$protocol = (1 == Configuration::get('PS_SSL_ENABLED') && 1 == Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https' : 'http');

//@ini_set('display_errors', 'on');
//@error_reporting(E_ALL | E_STRICT);

$context = Context::getContext();
$languages = Language::getLanguages();
$id_lang = Context::getContext()->language->id;

$filename = 'test.pdf';
$display = 'I';

//<link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto|Lato|Oswald|Slabo+27px|Roboto+Condensed|Lora|Source+Sans+Pro|Montserrat|PT+Sans|Open+Sans+Condensed:300|Raleway" rel="stylesheet" type="text/css">

if (Tools::getValue('fonts')) {
   $font_link = '<link href="'.$protocol.'://fonts.googleapis.com/css?family=';
   foreach (Tools::getValue('fonts') as $font) {
      if (is_array($font)) {
         foreach ($font as $f) {
            $myFont = str_replace(', cursive', '', $f);
            $font_link .= str_replace(' ', '+', $myFont).'|';
         }
      } else {
         $myFont = str_replace(', cursive', '', $font);
         $font_link .= str_replace(' ', '+', $myFont).'|';
      }
   }
   $font_link .= '" rel="stylesheet" type="text/css">';
} else {
   $font_link = '';
}

$customcss = '';
if (file_exists(_PS_THEME_DIR_.'modules/ndk_advanced_custom_fields/views/css/ndkacf.css')) {
   $customcss .= Tools::file_get_contents(_PS_THEME_DIR_.'modules/ndk_advanced_custom_fields/views/css/ndkacf.css');
}
if (file_exists(_PS_THEME_DIR_.'modules/ndk_advanced_custom_fields/views/css/custom.css')) {
   $customcss .= Tools::file_get_contents(_PS_THEME_DIR_.'modules/ndk_advanced_custom_fields/views/css/custom.css');
}

$content = '<html><head><meta charset="utf-8">
   <style>
   '.Tools::file_get_contents(_PS_MODULE_DIR_.'ndk_advanced_custom_fields/views/css/ndkacf.css').Tools::file_get_contents(_PS_MODULE_DIR_.'ndk_advanced_custom_fields/views/css/fields/type_29.css').$customcss.'
   
   </style>
   '.$font_link.'  
   </head>';
$content .= '<body style="position:relative;" class="ndk-render-html product-id-'.(int) Tools::getValue('idProduct').'">';

foreach (Tools::getValue('htmlNodes') as $block) {
   $content .= $block;
}

$content .= '</body></html>';
$regex = '~data:image/[a-zA-Z]*;base64,[a-zA-Z0-9+/\\=]*=~';
/*$content = preg_replace_callback(
     $regex,
     function ($matches) {
       return base64_decode($matches[0]);
     },
     $content
   );
 */

file_put_contents(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer').'/'.(int) Tools::getValue('idProduct').'/'.(int) Tools::getValue('idCustomization').'/render.html', $content);
$file_path = Tools::getValue('base_url').'img/scenes/'.'ndkcf/pdf/'.(int) Tools::getValue('idCustomer').'/'.(int) Tools::getValue('idProduct').'/'.(int) Tools::getValue('idCustomization').'/render.html';

$sql = 'UPDATE `'._DB_PREFIX_.'customized_data` cd SET value = \''.pSQL($file_path).'\'  WHERE cd.id_customization = '.(int) Tools::getValue('idCustomization').' AND cd.index = '.(int) Tools::getValue('preview_field');
//var_dump($sql);
Db::getInstance()->execute($sql);

//print( $content );

/*$images = Tools::getValue('images');
$im = 0;
foreach($images as $image)
{
   $decoded = mb_convert_encoding( str_replace('data:image/png;base64,', '', $image), "UTF-8", "BASE64" );
   $name = time();
   file_put_contents(_PS_IMG_DIR_.'scenes/'.'ndkcf/pdf/'.(int)Tools::getValue('idCustomer').'/'.(int)Tools::getValue('idProduct').'/'.(int)Tools::getValue('idCustomization').'/render-'.$im.'.png', $decoded);
   $im++;
}*/
