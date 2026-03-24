<?php
/**
 * 2011-2025 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2025 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

namespace OBSolutions\RichSnippets\Product\Infraestructure\Adapters\TrustedShopsIntegration;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class TrustedShopsIntegrationProductRetriever
{
    public static $trustedShopId;

    public function __construct()
    {
        if (!self::$trustedShopId) {
            $wrapper = new TrustedShopsIntegrationWrapper();
            self::$trustedShopId = $wrapper->getTrustedShopId();
        }
    }

    /**
     * @return bool
     */
    public static function enabled()
    {
        if (class_exists('TrustedShopsIntegration')) {
            $wrapper = new TrustedShopsIntegrationWrapper();
            self::$trustedShopId = $wrapper->getTrustedShopId();

            return self::$trustedShopId ? true : false;
        }

        return false;
    }

    protected function encodeSku($sku)
    {
        $encodedSku = '';
        for ($i = 0; $i < $this->JS_StringLength($sku); ++$i) {
            $charCodeAt = $this->JS_charCodeAt($sku, $i);
            $encodedSku .= base_convert($charCodeAt, 10, 16);
        }

        return $encodedSku;
    }

    protected function getUTF16CodeUnits($string)
    {
        $string = substr(json_encode($string), 1, -1);
        preg_match_all('/\\\\u[0-9a-fA-F]{4}|./mi', $string, $matches);

        return $matches[0];
    }

    protected function JS_StringLength($string)
    {
        return count($this->getUTF16CodeUnits($string));
    }

    protected function JS_charCodeAt($string, $index)
    {
        $utf16CodeUnits = $this->getUTF16CodeUnits($string);
        $unit = $utf16CodeUnits[$index];

        if (strlen($unit) > 1) {
            $hex = substr($unit, 2);

            return hexdec($hex);
        }

        return ord($unit);
    }
}
