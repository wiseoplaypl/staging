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

namespace WebPConvert\Convert\Converters;

use WebPConvert\Helpers\InputValidator;
use WebPConvert\Helpers\MimeType;
use WebPConvert\Convert\Converters\BaseTraits\AutoQualityTrait;
use WebPConvert\Convert\Converters\BaseTraits\DestinationPreparationTrait;
use WebPConvert\Convert\Converters\BaseTraits\OptionsTrait;


abstract class AbstractConverter
{
    use AutoQualityTrait;
    use OptionsTrait;
    use DestinationPreparationTrait;

    abstract protected function doActualConvert();

    public function supportsLossless()
    {
        return false;
    }

    protected $source;

    protected $destination;

    public function checkOperationality()
    {
    }

    public function checkConvertability()
    {
        return true;
    }

    public function __construct($source, $destination, $options = [], $logger = null)
    {
        InputValidator::checkSourceAndDestination($source, $destination);

        $this->source = $source;
        $this->destination = $destination;
        $this->setProvidedOptions($options);
    }
    public function getSource()
    {
        return $this->source;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
    protected static function getConverterDisplayName()
    {
        return substr(strrchr('\\' . static::class, '\\'), 1);
    }
    protected static function getConverterId()
    {
        return strtolower(self::getConverterDisplayName());
    }
    public static function createInstance($source, $destination, $options = [], $logger = null)
    {
        return new static($source, $destination, $options, $logger);
    }

    protected function logReduction($source, $destination)
    {
    }

    private function doConvertImplementation()
    {
        if($this->checkOperationality() && $this->checkConvertability())
        {
            $this->runActualConvert();
    
            $source = $this->source;
            $destination = $this->destination;
    
            if (!@file_exists($destination)) {
                return false;
            } elseif (@filesize($destination) === 0) {
                unlink($destination);
                return false;
            }
            return true;
        }
        
    }

    public function doConvert()
    {
        return $this->doConvertImplementation();
    }

    protected function runActualConvert()
    {
        return $this->doActualConvert();
    }

    public static function convert($source, $destination, $options = [], $logger = null)
    {
        $c = self::createInstance($source, $destination, $options, $logger);
        return $c->doConvert();
    }

    public function getMimeTypeOfSource()
    {
        return MimeType::getMimeTypeDetectionResult($this->source);
    }
}
