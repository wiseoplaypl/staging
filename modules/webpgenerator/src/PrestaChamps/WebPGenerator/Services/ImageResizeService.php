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

namespace PrestaChamps\WebPGenerator\Services;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Hook;
use Image;
use PrestaChamps\WebPGenerator\Exceptions\FileErrorException;
use PrestaChamps\WebPGenerator\Exceptions\UnknownImageType;
use PrestaShopException;
use Tools;
use WebPConvert\Exceptions\WebPConvertException;
use WebPConvert\WebPConvert;
use WebPGeneratorConfig;

/**
 * Class ImageResizeService
 *
 * @package PrestaChamps\WebPGenerator\Services
 */
abstract class ImageResizeService
{
    /**
     * @param       $image
     * @param       $type
     * @param array $types
     *
     * @return bool
     * @throws FileErrorException
     * @throws UnknownImageType
     * @throws PrestaShopException
     */
    public static function resizeOtherImage($image, $type, $types = array())
    {
        $process = array(
            'category'     => _PS_CAT_IMG_DIR_,
            'manufacturer' => _PS_MANU_IMG_DIR_,
            'supplier'     => _PS_SUPP_IMG_DIR_,
            'product'      => _PS_PROD_IMG_DIR_,
            'store'        => _PS_STORE_IMG_DIR_,
        );
        $dir = $process[$type];
        $result = false;
        if (true) {
            $success = true;
            foreach ($types as $imageType) {
                // Customizable writing dir
                $newDir = $dir;
                if ($imageType['name'] === 'thumb_scene') {
                    $newDir .= 'thumbs/';
                }
                $newFile = $newDir . Tools::substr($image, 0, -4) . '-' . Tools::stripslashes($imageType['name']);
                $result = self::resize($newFile);
                $success = $success && $result;
            }

            // convert the base image also
            WebPConvert::convert(
                $dir . $image,
                str_replace(array('.jpg','.png','.JPG','.jpeg'), ".webp", $dir . $image),
                WebPGeneratorConfig::getConverterSettings()
            );
        } else {
            throw new UnknownImageType('Unknown image type');
        }

        return $result;
    }

    /**
     * @param       $imageId
     * @param array $types
     *
     * @return bool
     * @throws FileErrorException
     * @throws PrestaShopException
     * @throws WebPConvertException
     */
    public static function resizeProductImage($imageId, $types = array())
    {
        $imageObj = new Image($imageId);
        $existing_img = _PS_PROD_IMG_DIR_ . $imageObj->getExistingImgPath() . '.jpg';
        $success = false;
        if (file_exists($existing_img) && filesize($existing_img)) {
            $success = true;
            foreach ($types as $imageType) {
                $newFile = _PS_PROD_IMG_DIR_ .
                    $imageObj->getExistingImgPath() .
                    '-' .
                    Tools::stripslashes($imageType['name']);
                $result = self::resize($newFile);
                $success = $success && $result;
            }
            /* this part converted the original product image */
            // WebPConvert::convert(
            //     $existing_img,
            //     str_replace(['.jpg','.png','.JPG','.jpeg'], ".webp", $existing_img),
            //     WebPGeneratorConfig::getConverterSettings()
            // );
        } else {
            throw new FileErrorException("Source file doesn't exists: $existing_img");
        }

        return true;
    }

    public static function convert($source)
    {
        WebPConvert::convert(
            $source,
            str_replace(array('.jpg','.png','.JPG','.jpeg'), '.webp', $source),
            WebPGeneratorConfig::getConverterSettings()
        );

        return true;
    }

    /**
     * @param      $destination
     * @return bool
     * @throws FileErrorException
     * @throws PrestaShopException
     */
    protected static function resize($destination)
    {
        if (file_exists($destination) && !unlink($destination)) {
            throw new FileErrorException();
        }

        Hook::exec('actionOnImageResizeAfter', array('dst_file' => "$destination.jpg"));

        return true;
    }
}
