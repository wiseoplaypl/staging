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

use Nette\Utils\Finder;
use PrestaChamps\WebPGenerator\Exceptions\FileErrorException;
use PrestaChamps\WebPGenerator\Exceptions\UnknownImageType;
use SplFileInfo;

/**
 * Class ImageDeleteService
 *
 * @package PrestaChamps\WebPGenerator\Services
 */
abstract class ImageDeleteService
{
    public static function clearWebPImages($path)
    {
        foreach (Finder::findFiles('*.webp')->from($path) as $file) {
            /**
             * @var $file SplFileInfo
             */
            if ($file->isFile() && $file->isReadable() && !$file->isDir()) {
                unlink($file->getRealPath());
            }
        }
    }
}
