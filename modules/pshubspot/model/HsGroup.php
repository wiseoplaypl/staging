<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/model/AvailableProperties.php';

class HsGroup extends AvailableProperties
{
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $id = $this->getGroupsPropertyId(); // Force to use groups id always.
        parent::__construct($id, $id_lang, $id_shop);
        if (is_null($this->id)) {
            $this->id = $id;
            $this->is_new = true;
        }
        static::init();
    }

    private function getGroupsPropertyId()
    {
        $sql = 'SELECT ' . self::$definition['primary'] . ' FROM ' . _DB_PREFIX_ . self::$definition['table'] .
        ' WHERE name = "customer_groups"';

        return (int) DB::getInstance()->getValue($sql);
    }
}
