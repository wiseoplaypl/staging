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

namespace WebPConvert\Convert;
use WebPConvert\Convert\Converters\AbstractConverter;

class ConverterFactory
{
    public static function converterIdToClassname($converterId)
    {
        switch ($converterId) {
            case 'imagickbinary':
                $classNameShort = 'ImagickBinary';
                break;
            case 'imagemagick':
                $classNameShort = 'ImageMagick';
                break;
            case 'gmagickbinary':
                $classNameShort = 'GmagickBinary';
                break;
            case 'graphicsmagick':
                $classNameShort = 'GraphicsMagick';
                break;
            default:
                $classNameShort = ucfirst($converterId);
        }
        $className = 'WebPConvert\\Convert\\Converters\\' . $classNameShort;
        if (is_callable([$className, 'convert'])) {
            return $className;
        } else {
            throw new ConverterNotFoundException('There is no converter with id:' . $converterId);
        }
    }

    public static function makeConverterFromClassname(
        $converterClassName,
        $source,
        $destination,
        $options = [],
        $logger = null
    ) {
        if (!is_callable([$converterClassName, 'convert'])) {
            throw new ConverterNotFoundException(
                'There is no converter with class name:' . $converterClassName . ' (or it is not a converter)'
            );
        }

        return call_user_func(
            [$converterClassName, 'createInstance'],
            $source,
            $destination,
            $options,
            $logger
        );
    }

    public static function makeConverter($converterIdOrClassName, $source, $destination, $options = [], $logger = null)
    {
        if (strtolower($converterIdOrClassName) == $converterIdOrClassName) {
            $converterClassName = self::converterIdToClassname($converterIdOrClassName);
        } else {
            $converterClassName = $converterIdOrClassName;
        }

        return self::makeConverterFromClassname($converterClassName, $source, $destination, $options, $logger);
    }
}
