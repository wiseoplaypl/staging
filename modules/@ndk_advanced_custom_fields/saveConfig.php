<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author	Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/
include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/../../init.php';

require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCfConfig.php';

if (1 == Tools::getValue('skip')) {
	return true;
} else {
	$id_user = (int) Context::getContext()->customer->id;
	$id_guest = (int) Context::getContext()->cookie->id_connections;
	if (ndkCfConfig::checkEnvironment() && 0 == $id_user) {
		$id_user = 0;
		$id_guest = 0;
	}

	if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/')) {
		mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/', 0777);
	}

	if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user)) {
		mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user, 0777);
	}

	if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct'))) {
		mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct'), 0777);
	}

	$context = Context::getContext();

	$id_lang = Context::getContext()->language->id;

	if ((int) Tools::getValue('idProduct') > 0 && '' != Tools::getValue('configName') && 'saveConfig' == Tools::getValue('action')) {
		$context = Context::getContext();
		$default_currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
		$user_currency = $context->currency;

		$search = Db::getInstance()->executeS(
			'SELECT fc.id_ndk_customization_field_configuration as id FROM '._DB_PREFIX_.'ndk_customization_field_configuration fc 
			LEFT JOIN `'._DB_PREFIX_.'ndk_customization_field_configuration_lang` fcl
				ON (fc.`id_ndk_customization_field_configuration` = fcl.`id_ndk_customization_field_configuration` AND fcl.`id_lang` = '.(int) $id_lang.')
			WHERE fc.id_product = '.(int) Tools::getValue('idProduct').' AND fc.id_customization = '.(int) Tools::getValue('idCustomization').' AND fc.id_user = '.$id_user.' AND fcl.name = "'.pSQL(Tools::getValue('configName')).'"'
		);
		if (sizeof($search) > 0) {
			//print(Tools::jsonEncode($search[0]['name']));
			$config = new ndkCfConfig((int) $search[0]['id'], (int) $id_lang);
		} else {
			$config = new ndkCfConfig();
		}
		if ($config->checkEnvironment() && 0 == $id_user) {
			$config->is_admin = 1;
			$id_user = 0;
			NdkCfConfig::clearAllCache();
		}
		if ('custom-' == Tools::substr(Tools::getValue('configName'), 0, 7)) {
			$config->is_admin = 0;
		}

		$config->id_user = $id_user;
		$config->id_guest = $id_guest;
		$config->id_product = (int) Tools::getValue('idProduct');
		$config->name = Tools::getValue('configName');
		$config->id_lang_default = (int) $id_lang;
		$config->tags = Tools::getValue('configTags');
		$config->id_customization = (int) Tools::getValue('idCustomization');
		$config->price = Tools::convertPriceFull(Tools::getValue('price'), $user_currency, $default_currency, 6);
		$json_result = '';
		if (null != Tools::getValue('json_values')) {
			foreach (Tools::getValue('json_values') as $key => $values) {
				if ('' != $values) {
					$json_result .= $key.'['.$values.']|';
				}
			}
		}

		$config->json_values = $json_result;
		$config->save();
		$leftFile = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct').'/'.$config->id.'-left.html';
		$rightFile = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct').'/'.$config->id.'-right.html';
		$layerFile = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct').'/'.$config->id.'-layer.html';

		if (1 == Configuration::get('NDK_SHOW_IMG_PREVIEW')) {
			$images = Tools::getValue('configImg');
			$im = 0;
			foreach ($images as $image) {
				if (file_exists(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct').'/'.$config->id.'-'.$im.'-img.jpg')) {
					unlink(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct').'/'.$config->id.'-'.$im.'-img.jpg');
				}

				$decoded = mb_convert_encoding(str_replace('data:image/png;base64,', '', $image), 'UTF-8', 'BASE64');

				$name = time();
				file_put_contents(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct').'/'.$config->id.'-'.$im.'-img.jpg', $decoded);

				++$im;
			}

			$file_path = Tools::getValue('base_url').'img/scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) Tools::getValue('idProduct').'/'.$config->id.'-0-img.jpg';
			$sql = 'UPDATE `'._DB_PREFIX_.'customized_data` cd SET value = \''.pSQL($file_path).'\'  WHERE cd.id_customization = '.(int) Tools::getValue('idCustomization').' AND cd.index = '.(int) Tools::getValue('preview_field');
			//var_dump($sql);
			Db::getInstance()->execute($sql);
		}

		$leftcontent = Tools::getValue('leftBlock');
		$rightcontent = Tools::getValue('rightBlock');
		$layercontent = Tools::getValue('layerBlock');

		file_put_contents($leftFile, utf8ize($leftcontent));
		file_put_contents($rightFile, utf8ize($rightcontent));
		file_put_contents($layerFile, utf8ize($layercontent));

		echo $config->id;
	} elseif ('getConfig' == Tools::getValue('action') && Tools::getValue('idConfig') > 0) {
		$return = [];
		$config = new ndkCfConfig((int) Tools::getValue('idConfig'));

		$leftFile = $config->leftFile;
		$rightFile = $config->rightFile;
		$layerFile = $config->layerFile;

		$leftContent = gzinflate(mb_convert_encoding(str_replace('data:image/png;base64,', '', Tools::file_get_contents($leftFile)), 'UTF-8', 'BASE64'));
		$rightContent = gzinflate(mb_convert_encoding(str_replace('data:image/png;base64,', '', Tools::file_get_contents($rightFile)), 'UTF-8', 'BASE64'));
		$layerContent = gzinflate(mb_convert_encoding(str_replace('data:image/png;base64,', '', Tools::file_get_contents($layerFile)), 'UTF-8', 'BASE64'));

		$leftContent = str_replace("\n", '', $leftContent);
		$rightContent = str_replace("\n", '', $rightContent);
		$layerContent = str_replace("\n", '', $layerContent);

		$return['leftBlock'] = str_replace("\r", '', $leftContent);
		$return['rightBlock'] = str_replace("\r", '', $rightContent);
		$return['layerBlock'] = str_replace("\r", '', $layerContent);
		$return['json_values'] = $config->json_values;
		//$returnHtml = '<div id="leftBlock">'.$return['leftBlock'].'</div>';
		//$returnHtml .= '<div id="rightBlock">'.$return['rightBlock'].'</div>';
		//$returnHtml .= '<div id="layer-block">'.$return['layerBlock'].'</div>';
		echo Tools::jsonEncode(utf8ize($return));
	}
}
	function utf8ize($d)
	{
		if (is_array($d)) {
			foreach ($d as $k => $v) {
				$d[$k] = utf8ize($v);
			}
		} elseif (is_string($d)) {
			return utf8_encode(str_replace('¬', '€', $d));
		}

		return $d;
	}
	//print( $content );
