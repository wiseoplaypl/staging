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
use WebPConvert\Convert\Converters\ConverterTraits\ExecTrait;

class Cwebp extends AbstractConverter
{

    use EncodingAutoTrait;
    use ExecTrait;
    public function setProvidedOptions($providedOptions = [])
    {
        parent::setProvidedOptions($providedOptions);
        $this->options = array_merge($this->options,array(
            'command-line-options' => null,
            'rel-path-to-precompiled-binaries' => './Binaries',
            'try-common-system-paths' => 1,
            'try-supplied-binary-for-os' => 1,
        )
        );
        
    }

    private static $cwebpDefaultPaths = [
        'cwebp',
        '/usr/bin/cwebp',
        '/usr/local/bin/cwebp',
        '/usr/gnu/bin/cwebp',
        '/usr/syno/bin/cwebp'
    ];

    private static $suppliedBinariesInfo = [
        'WINNT' => [['cwebp.exe', '49e9cb98db30bfa27936933e6fd94d407e0386802cb192800d9fd824f6476873']],
        'Darwin' => [['cwebp-mac12', 'a06a3ee436e375c89dbc1b0b2e8bd7729a55139ae072ed3f7bd2e07de0ebb379']],
        'SunOS' => [['cwebp-sol', '1febaffbb18e52dc2c524cda9eefd00c6db95bc388732868999c0f48deb73b4f']],
        'FreeBSD' => [['cwebp-fbsd', 'e5cbea11c97fadffe221fdf57c093c19af2737e4bbd2cb3cd5e908de64286573']],
        'Linux' => [
            ['cwebp-linux-1.0.2-shared', 'd6142e9da2f1cab541de10a31527c597225fff5644e66e31d62bb391c41bfbf4'],

            ['cwebp-linux-1.0.2-static', 'a67092563d9de0fbced7dde61b521d60d10c0ad613327a42a81845aefa612b29'],

            ['cwebp-linux-0.6.1', '916623e5e9183237c851374d969aebdb96e0edc0692ab7937b95ea67dc3b2568'],
        ]
    ];

    public function checkOperationality()
    {
        if($this->checkOperationalityExecTrait())
        {
            $options = $this->options;
            if (!$options['try-supplied-binary-for-os'] && !$options['try-common-system-paths']) {
                return false;
            }
            return true;
        }
    }

    private function executeBinary($binary, $commandOptions, $useNice)
    {
        $command = ($useNice ? 'nice ' : '') . $binary . ' ' . $commandOptions;
        exec($command, $output, $returnCode);
        $this->logExecOutput($output);
        return intval($returnCode);
    }

    private static function escapeShellArgOnCommandLineOptions($commandLineOptions)
    {
        if (!ctype_print($commandLineOptions)) {
            throw new ConversionFailedException(
                'Non-printable characters are not allowed in the extra command line options'
            );
        }

        if (preg_match('#[^a-zA-Z0-9_\s\-]#', $commandLineOptions)) {
            throw new ConversionFailedException('The extra command line options contains inacceptable characters');
        }

        $cmdOptions = [];
        $arr = explode(' -', ' ' . $commandLineOptions);
        foreach ($arr as $cmdOption) {
            $pos = strpos($cmdOption, ' ');
            $cName = '';
            if (!$pos) {
                $cName = $cmdOption;
                if ($cName == '') {
                    continue;
                }
                $cmdOptions[] = '-' . $cName;
            } else {
                $cName = substr($cmdOption, 0, $pos);
                $cValues = substr($cmdOption, $pos + 1);
                $cValuesArr = explode(' ', $cValues);
                foreach ($cValuesArr as &$cArg) {
                    $cArg = escapeshellarg($cArg);
                }
                $cValues = implode(' ', $cValuesArr);
                $cmdOptions[] = '-' . $cName . ' ' . $cValues;
            }
        }
        return $cmdOptions;
    }

    private function createCommandLineOptions($version)
    {
        $version = preg_match('#^\d+\.\d+#', $version, $matches);
        $versionNum = 0;
        if (isset($matches[0])) {
            $versionNum = floatval($matches[0]);
        }
        $options = $this->options;

        $cmdOptions = [];

        if ($versionNum >= 0.3) {
            $cmdOptions[] = '-metadata ' . $options['metadata'];
        }

        if (!is_null($options['preset'])) {
            if ($options['preset'] != 'none') {
                $cmdOptions[] = '-preset ' . $options['preset'];
            }
        }

        // Size
        $addedSizeOption = false;
        if (!is_null($options['size-in-percentage'])) {
            $sizeSource = filesize($this->source);
            if ($sizeSource !== false) {
                $targetSize = floor($sizeSource * $options['size-in-percentage'] / 100);
                $cmdOptions[] = '-size ' . $targetSize;
                $addedSizeOption = true;
            }
        }

        // quality
        if (!$addedSizeOption) {
            $cmdOptions[] = '-q ' . $this->getCalculatedQuality();
        }

        // alpha-quality
        if ($this->options['alpha-quality'] !== 100) {
            $cmdOptions[] = '-alpha_q ' . escapeshellarg($this->options['alpha-quality']);
        }

        // Losless PNG conversion
        if ($options['encoding'] == 'lossless') {
            if (($options['near-lossless'] === 100) || ($versionNum < 0.5)) {
                $cmdOptions[] = '-lossless';
            }
        }

        // Near-lossles
        if ($options['near-lossless'] !== 100) {
            if ($versionNum < 0.5) {
            } else {
                if ($options['encoding'] == 'lossless') {
                    $cmdOptions[] ='-near_lossless ' . $options['near-lossless'];
                }
            }
        }

        if ($options['auto-filter'] === true) {
            $cmdOptions[] = '-af';
        }

        // Built-in method option
        $cmdOptions[] = '-m ' . strval($options['method']);

        // Built-in low memory option
        if ($options['low-memory']) {
            $cmdOptions[] = '-low_memory';
        }

        // command-line-options
        if ($options['command-line-options']) {
            array_push(
                $cmdOptions,
                ...self::escapeShellArgOnCommandLineOptions($options['command-line-options'])
            );
        }

        // Source file
        $cmdOptions[] = escapeshellarg($this->source);

        // Output
        $cmdOptions[] = '-o ' . escapeshellarg($this->destination);

        $cmdOptions[] = '2>&1';

        $commandOptions = implode(' ', $cmdOptions);
        return $commandOptions;
    }

    private function getSuppliedBinaryPathForOS()
    {
        $options = $this->options;
        if (!isset(self::$suppliedBinariesInfo[PHP_OS])) {
            return [];
        }
        $result = [];
        $files = self::$suppliedBinariesInfo[PHP_OS];
        foreach ($files as $i => list($file, $hash)) {
            $binaryFile = __DIR__ . '/' . $options['rel-path-to-precompiled-binaries'] . '/' . $file;
            $realPathResult = realpath($binaryFile);
            if ($realPathResult === false) {
                continue;
            }
            $binaryFile = $realPathResult;

            if (function_exists('hash_file')) {
                $binaryHash = hash_file('sha256', $binaryFile);

                if ($binaryHash != $hash) {
                    continue;
                }
            }
            $result[] = $binaryFile;
        }

        return $result;
    }

    private function discoverBinaries()
    {
        if (defined('WEBPCONVERT_CWEBP_PATH')) {
            return [constant('WEBPCONVERT_CWEBP_PATH')];
        }
        $binaries = [];
        if ($this->options['try-common-system-paths']) {                    
            foreach (self::$cwebpDefaultPaths as $binary) {
                if (@file_exists($binary)) {
                    $binaries[] = $binary;
                }
            }
        }
        if ($this->options['try-supplied-binary-for-os']) {
            $suppliedBinaries = $this->getSuppliedBinaryPathForOS();
            foreach ($suppliedBinaries as $suppliedBinary) {
                $binaries[] = $suppliedBinary;
            }
        }
        return $binaries;
    }

    private function detectVersion($binary)
    {
        $command = $binary . ' -version';
        exec($command, $output, $returnCode);

        if ($returnCode == 0) {
            if (isset($output[0])) {
                return $output[0];
            }
        } else {
            $this->logExecOutput($output);
            return $returnCode;
        }
    }

    private function detectVersions($binaries)
    {
        $binariesWithVersions = [];
        $binariesWithFailCodes = [];
        foreach ($binaries as $binary) {
            $versionStringOrFailCode = $this->detectVersion($binary);
            if (gettype($versionStringOrFailCode) == 'string') {
                $binariesWithVersions[$binary] = $versionStringOrFailCode;
            } else {
                $binariesWithFailCodes[$binary] = $versionStringOrFailCode;
            }
        }
        return ['detected' => $binariesWithVersions, 'failed' => $binariesWithFailCodes];
    }

    private function tryBinary($binary, $version, $useNice)
    {
        $commandOptions = $this->createCommandLineOptions($version);

        $returnCode = $this->executeBinary($binary, $commandOptions, $useNice);
        if ($returnCode == 0) {
            if (!file_exists($this->destination)) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    protected function doActualConvert()
    {
        $binaries = $this->discoverBinaries();

        if (count($binaries) == 0) {
            return false;
        }

        $versions = $this->detectVersions($binaries);
        if (count($versions['detected']) == 0) {
            return false;
        }

        $binaryVersions = $versions['detected'];
        arsort($binaryVersions);
        $useNice = (($this->options['use-nice']) && self::hasNiceSupport());

        $success = false;
        foreach ($binaryVersions as $binary => $version) {
            if ($this->tryBinary($binary, $version, $useNice)) {
                $success = true;
                break;
            }
        }

        if ($success) {
            $destinationParent = dirname($this->destination);
            $fileStatistics = stat($destinationParent);
            if ($fileStatistics !== false) {
                $permissions = $fileStatistics['mode'] & 0000666;
                chmod($this->destination, $permissions);
            }
            return true;
        } else {
            return false;
        }
    }
}
