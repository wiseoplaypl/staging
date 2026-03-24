<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

$filename = 'test.pdf';
$display = 'I';

$content = '<html><head><meta charset="utf-8">
	<style>
	 '.Tools::file_get_contents(_PS_MODULE_DIR_.'ndk_advanced_custom_fields/views/css/ndkacf.css').'
	 '.Tools::file_get_contents(Configuration::get('NDK_ACF_FONTS').'&effect=shadow-multiple|3d-float|fire').'
	</style>
		
	</head>';
$content.= '<body style="position:relative;">';
$content .= Tools::getValue('htmlNodes');
$content.= '</body></html>';


print( $content );

?>