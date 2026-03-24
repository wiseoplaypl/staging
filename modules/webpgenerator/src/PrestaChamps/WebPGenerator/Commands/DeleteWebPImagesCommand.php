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

namespace PrestaChamps\WebPGenerator\Commands;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Nette\Utils\Finder;

/**
 * Class WebPDeleteCommand
 * @package PrestaChamps\WebPGenerator\Commands
 */
class DeleteWebPImagesCommand
{
    protected $imgFolderPath;

    public function __construct($imgFolderPath)
    {
        $this->imgFolderPath = $imgFolderPath;
    }

    public function execute()
    {
        $result = true;
        foreach (Finder::findFiles('*.webp')->from($this->imgFolderPath)->getIterator() as $file) {
            /**
             * @var $file \SplFileInfo
             */
            $result = $result && unlink($file);
        }

        return $result;
    }
}
