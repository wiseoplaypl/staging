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

use WebPConvert\Convert\Converters\AbstractConverter;
use WebPConvert\Convert\Converters\ConverterTraits\EncodingAutoTrait;

class Gmagick extends AbstractConverter
{
    use EncodingAutoTrait;

    public function checkOperationality()
    {
        if (!extension_loaded('Gmagick')) {
            return false;
        }

        if (!class_exists('Gmagick')) {
            return false;
        }

        $im = new \Gmagick($this->source);

        if (!in_array('WEBP', $im->queryformats())) {
            return false;
        }
        return true;
    }

    public function checkConvertability()
    {
        $im = new \Gmagick();
        $mimeType = $this->getMimeTypeOfSource();
        switch ($mimeType) {
            case 'image/png':
                if (!in_array('PNG', $im->queryFormats())) {
                    return false;
                }
                break;
            case 'image/jpeg':
                if (!in_array('JPEG', $im->queryFormats())) {
                    return false;
                }
                break;
        }
        return true;
    }
    protected function doActualConvert()
    {
        $options = $this->options;
        try {
            $im = new \Gmagick($this->source);
        } catch (\Exception $e) {
            return false;
        }
        $im->setimageformat('WEBP');

        if (method_exists($im, 'setimageoption')) {
            $im->setimageoption('webp', 'method', $options['method']);
            $im->setimageoption('webp', 'lossless', $options['encoding'] == 'lossless' ? 'true' : 'false');
            $im->setimageoption('webp', 'alpha-quality', $options['alpha-quality']);

            if ($options['auto-filter'] === true) {
                $im->setimageoption('webp', 'auto-filter', 'true');
            }
        }
        if ($options['metadata'] == 'none') {
            $im->stripImage();
        }
        $im->setcompressionquality($this->getCalculatedQuality());

        $imageBlob = $im->getImageBlob();

        $success = @file_put_contents($this->destination, $imageBlob);

        if (!$success) {
            return false;
        }
        else
            return true;
    }
}
