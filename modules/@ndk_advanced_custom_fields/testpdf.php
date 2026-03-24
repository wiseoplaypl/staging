<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */

include dirname(__FILE__) . "/../../config/config.inc.php";
include dirname(__FILE__) . "/../../init.php";
require_once _PS_MODULE_DIR_ . "ndk_advanced_custom_fields/models/ndkCf.php";
require_once _PS_MODULE_DIR_ .
    "ndk_advanced_custom_fields/models/ndkCfValues.php";
require_once _PS_MODULE_DIR_ .
    "ndk_advanced_custom_fields/models/ndkCfRecipients.php";
@ini_set("display_errors", "on");
@error_reporting(E_ALL | E_STRICT);

$mod = Module::getInstanceByName("ndk_advanced_custom_fields");
$context = Context::getContext();
$languages = Language::getLanguages();
$id_lang = Context::getContext()->language->id;

//$order = new Order(Tools::getValue('id_order'));
$mod->generatePdfRecipient(Tools::getValue("id_order"));
?>
