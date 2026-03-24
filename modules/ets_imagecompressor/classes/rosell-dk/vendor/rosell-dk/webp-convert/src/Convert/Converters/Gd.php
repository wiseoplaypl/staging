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

class Gd extends AbstractConverter
{
    public function supportsLossless()
    {
        return false;
    }

    private $errorMessageWhileCreating = '';
    private $errorNumberWhileCreating;

    public function checkOperationality()
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        if (!function_exists('imagewebp')) {
            return false;
        }
        return true;
    }

    public function checkConvertability()
    {
        $mimeType = $this->getMimeTypeOfSource();
        switch ($mimeType) {
            case 'image/png':
                if (!function_exists('imagecreatefrompng')) {
                    return false;
                }
                break;

            case 'image/jpeg':
                if (!function_exists('imagecreatefromjpeg')) {
                    return false;
                }
        }
        return true;
    }

    private static function functionsExist($functionNamesArr)
    {
        foreach ($functionNamesArr as $functionName) {
            if (!function_exists($functionName)) {
                return false;
            }
        }
        return true;
    }

    private function makeTrueColorUsingWorkaround(&$image)
    {
        if (self::functionsExist(['imagecreatetruecolor', 'imagealphablending', 'imagecolorallocatealpha',
                'imagefilledrectangle', 'imagecopy', 'imagedestroy', 'imagesx', 'imagesy'])) {
            $dst = imagecreatetruecolor(imagesx($image), imagesy($image));

            if ($dst === false) {
                return false;
            }

            if (imagealphablending($dst, false) === false) {
                return false;
            }

            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);

            if ($transparent === false) {
                return false;
            }

            if (imagefilledrectangle($dst, 0, 0, imagesx($image), imagesy($image), $transparent) === false) {
                return false;
            }

            if (imagealphablending($dst, true) === false) {
                return false;
            };

            if (imagecopy($dst, $image, 0, 0, 0, 0, imagesx($image), imagesy($image)) === false) {
                return false;
            }
            imagedestroy($image);

            $image = $dst;
            return true;
        } else {
            return false;
        }
    }

    private function makeTrueColor(&$image)
    {
        if (function_exists('imagepalettetotruecolor')) {
            return imagepalettetotruecolor($image);
        } else {
            return $this->makeTrueColorUsingWorkaround($image);
        }
    }

    private function createImageResource()
    {
        $mimeType = $this->getMimeTypeOfSource();

        if ($mimeType == 'image/png') {
            $image = imagecreatefrompng($this->source);
            return $image;
        }

        if ($mimeType == 'image/jpeg') {
            $image = imagecreatefromjpeg($this->source);
            return $image;
        }
        if($mimeType=='image/gif')
        {
            $image = imagecreatefromgif($this->source);
            return $image;
        }
    }

    protected function tryToMakeTrueColorIfNot(&$image)
    {
        $mustMakeTrueColor = false;
        if (function_exists('imageistruecolor')) {
            if (imageistruecolor($image)) {
            } else {
                $mustMakeTrueColor = true;
            }
        } else {
            $mustMakeTrueColor = true;
        }

        if ($mustMakeTrueColor) {
            $this->makeTrueColor($image);
        }
    }

    protected function trySettingAlphaBlending($image)
    {
        if (function_exists('imagealphablending')) {
            if (!imagealphablending($image, true)) {
                return false;
            }
        } else {
            return false;
        }

        if (function_exists('imagesavealpha')) {
            if (!imagesavealpha($image, true)) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    protected function errorHandlerWhileCreatingWebP($errno, $errstr, $errfile, $errline)
    {
        $this->errorNumberWhileCreating = $errno;
        $this->errorMessageWhileCreating = $errstr . ' in ' . $errfile . ', line ' . $errline .
            ', PHP ' . PHP_VERSION . ' (' . PHP_OS . ')';
    }

    protected function destroyAndRemove($image)
    {
        imagedestroy($image);
        if (file_exists($this->destination)) {
            unlink($this->destination);
        }
    }

    protected function tryConverting($image)
    {
        $addedZeroPadding = false;
        set_error_handler(array($this, "errorHandlerWhileCreatingWebP"));

        $q = $this->getCalculatedQuality();

        ob_start();

        $success = imagewebp($image, null, $q);

        if (!$success) {
            $this->destroyAndRemove($image);
            ob_end_clean();
            restore_error_handler();
        }

        if (ob_get_length() % 2 == 1) {
            $addedZeroPadding = true;
        }
        $output = ob_get_clean();
        restore_error_handler();

        if ($output == '') {
            $this->destroyAndRemove($image);
        }
        if ($this->errorMessageWhileCreating != '') {
            switch ($this->errorNumberWhileCreating) {
                case E_WARNING:
                    break;
                case E_NOTICE:
                    break;
                default:
                    $this->destroyAndRemove($image);
                    break;
            }
        }
        $success = file_put_contents($this->destination, $output);
        if (!$success) {
            $this->destroyAndRemove($image);
        }
    }

    protected function doActualConvert()
    {
        $image = $this->createImageResource();
        $this->tryToMakeTrueColorIfNot($image);

        if ($this->getMimeTypeOfSource() == 'image/png') {
            $this->trySettingAlphaBlending($image);
        }
        $this->tryConverting($image);

        imagedestroy($image);
    }
}
