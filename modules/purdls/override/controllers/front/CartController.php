<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA Miłosz Myszczuk VATEU: PL9730945634
 * @copyright 2010-2021 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class CartController extends CartControllerCore
{
    public function displayAjaxProductRefresh()
    {
        if ($this->id_product)
        {
            $url = $this->context->link->getProductLink($this->id_product, null, null, null, $this->context->language->id, null, (int)Product::getIdProductAttributesByIdAttributes($this->id_product, Tools::getValue('group'), true), false, false, true, ['quantity_wanted' => (int)$this->qty]);
            if (Configuration::get('purls_product_hash') == 0 && (Configuration::get('purls_products') == 1 || Configuration::get('purls_products') == 2)) {
                $parts = explode("#", $url);
                $parts[0] .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'idpa=' . (int)Product::getIdProductAttributesByIdAttributes($this->id_product, Tools::getValue('group'), true);
                $url = implode("#", $parts);
            } else {
                $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'idpa=' . (int)Product::getIdProductAttributesByIdAttributes($this->id_product, Tools::getValue('group'), true);
            }
        }
        else
        {
            $url = false;
        }
        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxDie(Tools::jsonEncode([
            'success' => true,
            'productUrl' => $url
        ]));
    }
}

?>