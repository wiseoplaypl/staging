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

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Console\Helper\ProgressBar;
use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebPConvert\WebPConvert;

/**
 * @package    pdev
 * @author     Zoltan Szanto <mrbig00@gmail.com>
 * @copyright  2021 Zoltán Szántó
 */
class WebpgeneratorCronModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if (Tools::getValue('key') !== ConfigurationCore::get(WebPGeneratorConfig::CRON_KEY)) {
            die();
        }

        $filter = function (SplFileInfo $file) {
            return !file_exists(str_replace(array('.jpg','.png','.JPG','.jpeg'), ".webp", $file->getRealPath()));
        };
        $finder = Finder::findFiles(array('*.jpg','*.png','*.JPG','*.jpeg'))
            ->in(array(_PS_IMG_DIR_ . '/p', _PS_IMG_DIR_ . '/m', _PS_IMG_DIR_ . '/cms'))
            ->filter($filter);

        $total = $finder->count();

        /*****************/
        $active_languages = Language::getLanguages(true);

        $patterns = array();
        $patterns[] = '(^\d.*\.jpg$)';
        
        foreach ($active_languages as $language) {
            $patterns[] = '(^' . $language['iso_code'] . '.*\.jpg$)';
        }

        // if (version_compare(phpversion(), '8.0.0', '<') === true) {
        //     $master_pattern = implode($patterns, '|');
        // }else{
        //     $master_pattern = implode('|', $patterns);
        // }
        $master_pattern = implode('|', $patterns);
        /*****************/

        $total_not_needed = 0;

        if ($total > 0) {
            foreach ($finder as $file) {
                /**
                 * @var SplFileInfo $file
                 */
                if (strpos($file->getRealPath(), '/m/') && preg_match('/' . $master_pattern . '/', $file->getBasename())) {
                    WebPConvert::convert(
                        $file->getRealPath(),
                        str_replace(array('.jpg','.png','.JPG','.jpeg'), ".webp", $file->getRealPath()),
                        WebPGeneratorConfig::getConverterSettings()
                    );
                } elseif (strpos($file->getRealPath(), '/m/')) {
                    $total_not_needed += 1;
                } else {
                    WebPConvert::convert(
                        $file->getRealPath(),
                        str_replace(array('.jpg','.png','.JPG','.jpeg'), ".webp", $file->getRealPath()),
                        WebPGeneratorConfig::getConverterSettings()
                    );
                }
            }

            $totalToGenerate = $total - $total_not_needed;

            if ($totalToGenerate > 0) {
                echo("Total files: {$totalToGenerate}");
                echo("<br>");
                echo("Finished converting missing files<br>");
            } else {
                echo("Nothing to convert");
            }
        } else {
            echo("Nothing to convert");
        }
        die();
    }
}
