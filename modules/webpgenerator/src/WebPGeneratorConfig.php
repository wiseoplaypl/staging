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

/**
 * Class WebPGeneratorConfig
 */
class WebPGeneratorConfig
{
    /** Required for PHP < 5.6 compatibility */
    public static $className = 'WebPGeneratorConfig';

    public static $multiLang = array();

    const LOAD_POLYFILL     = 'load-polyfill';

    const CONVERTER_CWEBP   = 'cwebp';
    const CONVERTER_IMAGICK = 'imagick';
    const CONVERTER_GMAGICK = 'gmagick';
    const CONVERTER_GD      = 'gd';
    const CONVERTER_EWWW    = 'ewww';

    const CONVERTER_CWEBP_USE_NICE                = 'webp-gen-cwebp-use-nice';
    const CONVERTER_CWEBP_TRY_COMMON_SYSTEM_PATHS = 'webp-gen-cwebp-try-common-system-paths';
    const CONVERTER_CWEBP_TRY_SUPPLIED_BINARY     = 'webp-gen-cwebp-try-supplied-binary-for-os';
    const CONVERTER_CWEBP_AUTO_FILTER             = 'webp-gen-cwebp-autofilter';
    const CONVERTER_CWEBP_CMD_OPTIONS             = 'webp-gen-cwebp-command-line-options';

    const CONVERTER_EWWW_API_KEY = 'webp-gen-ewww-api-key';

    const CONFIG_COMMON_QUALITY    = 'webp-gen-config-common-quality';
    const CONFIG_COMMON_META_DATA  = 'webp-gen-config-common-metadata';
    const CONFIG_COMMON_METHOD     = 'webp-gen-config-common-method';
    const CONFIG_COMMON_LOW_MEMORY = 'webp-gen-config-common-low-memory';
    const CONFIG_COMMON_LOSSLESS   = 'webp-gen-config-common-lossless';


    const CONVERTER_TO_USE = 'module-webpconverter-converter-to-use';

    const DEMO_MODE = 'module-webpconverter-demo-mode';

    const CRON_KEY = 'module-webpconverter-cron-key';

    const SHARP_YUV = 'module-webpconverter-sharp-yuv';
    /**
     * Save a config value
     *
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public static function saveValue($key, $value)
    {
        return Configuration::updateValue($key, $value);
    }

    /**
     * Get configuration keys and values
     *
     * @return array
     */
    public static function getConfigurationValues()
    {
        try {
            $class = new ReflectionClass(static::$className);
            $values = array();
            foreach ($class->getConstants() as $constant) {
                if (is_string($constant)) {
                    if (in_array($constant, static::$multiLang, true)) {
                        static::getMultilangConfigValues($constant, $values);
                    } else {
                        $values[$constant] = Configuration::get($constant);
                    }
                }
            }
            return $values;
        } catch (Exception $exception) {
            return array();
        }
    }

    /**
     * Get a multilang config key (mainly used with the HelperForm class)
     *
     * @param $key
     * @param $values
     */
    private static function getMultilangConfigValues($key, &$values)
    {
        $languages = Language::getLanguages(false, false, false);
        $values[$key] = array();
        foreach ($languages as $language) {
            $values[$key][$language['id_lang']] = Configuration::get($key, $language['id_lang']);
        }
    }

    /**
     * Decide if a config key exists in the DB or not, doesn't really care about multilang
     *
     * @param null $configKey
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function configExists($configKey = null)
    {
        $query = new \DbQuery();
        $query->select('count(*)');
        $query->from('configuration');
        $query->where("name = '" . pSQL($configKey) . "'");

        return (int)Db::getInstance()->executeS($query) > 0;
    }

    /**
     * @return array
     * @throws PrestaShopException
     */
    public static function getConverterSettings()
    {
        $config = Configuration::getMultiple(array(
            static::CONFIG_COMMON_QUALITY,
            static::CONFIG_COMMON_META_DATA,
            static::CONFIG_COMMON_METHOD,
            static::CONFIG_COMMON_LOW_MEMORY,
            static::CONFIG_COMMON_LOSSLESS,
        ));

        return array(
            'quality' => (int)$config[static::CONFIG_COMMON_QUALITY],
            'metadata' => (string)$config[static::CONFIG_COMMON_META_DATA],
            'method' => (int)$config[static::CONFIG_COMMON_METHOD],
            'low-memory' => (bool)$config[static::CONFIG_COMMON_LOW_MEMORY],
            'lossless' => (bool)$config[static::CONFIG_COMMON_LOSSLESS],
            'converters' => static::getConverterStack(),
            'converter-options' => array(
                'ewww' => static::getEwwwSettings(),
                'cwebp' => static::getCWebpSettings(),
                'gd' => array('skip-pngs' => false),
            ),
        );
    }

    public static function getConverterStack()
    {
        return array(Configuration::get(static::CONVERTER_TO_USE));
    }

    public static function getEwwwSettings()
    {
        return array(
            'api-key' => Configuration::get(static::CONVERTER_EWWW_API_KEY),
        );
    }

    public static function getCWebpSettings()
    {
        return array(
            'use-nice' => (bool)Configuration::get(static::CONVERTER_CWEBP_USE_NICE),
            'try-common-system-paths' => (bool)Configuration::get(static::CONVERTER_CWEBP_TRY_COMMON_SYSTEM_PATHS),
            'try-supplied-binary-for-os' => (bool)Configuration::get(static::CONVERTER_CWEBP_TRY_SUPPLIED_BINARY),
            'autofilter' => (bool)Configuration::get(static::CONVERTER_CWEBP_AUTO_FILTER),
            'command-line-options' => Configuration::get(static::CONVERTER_CWEBP_CMD_OPTIONS),
            'sharp_yuv' => Configuration::get(static::SHARP_YUV)
        );
    }

    public static function updateRegenerationProgress($entityType, $index)
    {
        Configuration::updateValue("PC_WEBP_REGENERATE_$entityType", (int)$index);
    }

    public static function getRegenerationProgress($entityType)
    {
        return (int)Configuration::get("PC_WEBP_REGENERATE_$entityType", null, null, null, 0);
    }
}
