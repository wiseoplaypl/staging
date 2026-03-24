<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Shoprunners
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: info@shoprunners.de
 *
 * @author    Peter Schaeffer - Shoprunners
 * @copyright Copyright(c) 2015-2022 Shoprunners
 * @license   Commercial license
 * @package   aftermail
 */

include dirname(__FILE__) . '/../../config/config.inc.php';
include dirname(__FILE__) . '/classes/AfterMailLog.php';
include dirname(__FILE__) . '/aftermailpresta.php';

if (Tools::getValue('secure_key') && Module::isEnabled('aftermailpresta')) {
    $secureKey = Configuration::get('PS_AFTERMAIL_SECURE_KEY');
    if (!empty($secureKey) && $secureKey === Tools::getValue('secure_key')) {
        $aftermail = new AfterMailPresta();
        $aftermail->cronTask();
    }
}
