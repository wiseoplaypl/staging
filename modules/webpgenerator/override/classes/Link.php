<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

/**
 * Class Link
 */
class Link extends LinkCore
{
    protected $webpSupported = false;

    public function __construct($protocolLink = null, $protocolContent = null)
    {
        parent::__construct($protocolLink, $protocolContent);
        $this->webpSupported = $this->isWebPSupported();
    }

    public function getImageLink($name, $ids, $type = null)
    {
        $parent = parent::getImageLink($name, $ids, $type);
        if ($this->webpSupported) {
            return str_replace('.jpg', '.webp', $parent);
        }

        return $parent;
    }

    public function getCatImageLink($name, $idCategory, $type = null)
    {
        $parent = parent::getCatImageLink($name, $idCategory, $type);
        if ($this->webpSupported) {
            return str_replace('.jpg', '.webp', $parent);
        }

        return $parent;
    }

    public function getSupplierImageLink($idSupplier, $type = null)
    {
        $parent = parent::getSupplierImageLink($idSupplier, $type);
        if ($this->webpSupported) {
            return str_replace('.jpg', '.webp', $parent);
        }

        return $parent;
    }

    public function getStoreImageLink($name, $idStore, $type = null)
    {
        $parent = parent::getStoreImageLink($name, $idStore, $type);
        if ($this->webpSupported) {
            return str_replace('.jpg', '.webp', $parent);
        }

        return $parent;
    }

    public function getManufacturerImageLink($idManufacturer, $type = null)
    {
        $parent = parent::getManufacturerImageLink($idManufacturer, $type);
        if ($this->webpSupported) {
            return str_replace('.jpg', '.webp', $parent);
        }

        return $parent;
    }

    public function isWebPSupported()
    {
        if (Configuration::get('module-webpconverter-demo-mode')) {
            return false;
        }

        return Module::isEnabled('webpgenerator') &&
            (isset($_SERVER['HTTP_ACCEPT']) === true) &&
            (false !== strpos($_SERVER['HTTP_ACCEPT'], 'image/webp'));
    }
}
