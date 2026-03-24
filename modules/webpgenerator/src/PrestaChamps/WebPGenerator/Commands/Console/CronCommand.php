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

namespace PrestaChamps\WebPGenerator\Commands\Console;

if (!defined('_PS_VERSION_')) {
    exit;
}

use SplFileInfo;
use Symfony\Component\Console\Helper\ProgressBar;
use Nette\Utils\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebPConvert\WebPConvert;
use WebPGeneratorConfig;

/**
 * Class CronCommand
 *
 * @package PrestaChamps\WebPGenerator\Commands\Console
 */
class CronCommand extends Command
{
    protected function configure()
    {
        $this->setName('webp-generator:cron');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = function (SplFileInfo $file) {
            return !file_exists(str_replace(array('.jpg','.png','.JPG','.jpeg'), ".webp", $file->getRealPath()));
        };
        $finder = Finder::findFiles(array('*.jpg','*.png','*.JPG','*.jpeg'))
            ->in(array(_PS_IMG_DIR_ . '/p', _PS_IMG_DIR_ . '/m', _PS_IMG_DIR_ . '/cms'))
            ->filter($filter);

        $total = $finder->count();

        /*****************/
        $active_languages = \Language::getLanguages(true);

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
            $progressBar = new ProgressBar($output, $total);
            $progressBar->start();
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
                $progressBar->advance();
            }
            $progressBar->finish();

            $totalToGenerate = $total - $total_not_needed;

            if ($totalToGenerate > 0) {
                $output->writeln("Total files: {$totalToGenerate}");
                $output->writeln("");
                $output->writeln("Finished converting missing files");
            } else {
                $output->writeln("Nothing to convert");
            }
        } else {
            $output->writeln("Nothing to convert");
        }
    }
}
