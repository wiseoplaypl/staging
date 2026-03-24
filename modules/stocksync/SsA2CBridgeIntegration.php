<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class SsA2CBridgeIntegration
{
    const CART_ID = 'Prestashop';
    const EVENT_CONNECT = 'connect';
    const EVENT_DISCONNECT = 'disconnect';
    const EVENT_UPDATE = 'update_store_key';

    public $rootPath = '';
    public $bridgePath = '';
    public $currentFolder = '';
    public $errorMessage = '';
    public $configFilePath = '';
    public $bridgeFilePath = '';
    public $connectorBridgePath = 'https://api.api2cart.com/v1.0/bridge.download.file?whitelabel=true&enable_encryption=false';

    private $_callbackUrl = '';
    private $_publicKey = '';
    private $_error;

    const VERIFY_UNINSTALL_LINK = '';
    // link to check for status 200 when uninstalling the module; skip verification if empty

    const ON_VERIFY_SUCCESS_UNINSTALL_MODULE = true;
    const ON_VERIFY_SUCCESS_DELETE_BRIDGE = true;
    // actions to perform if uninstall was verified

    const ON_VERIFY_ERROR_UNINSTALL_MODULE = true;
    const ON_VERIFY_ERROR_DELETE_BRIDGE = false;
    // actions to perform if error occurred on uninstall verification

    const INSTALL_OK = 0;
    const INSTALL_ERROR_CODE_CANT_DOWNLOAD = 101;
    const INSTALL_ERROR_CODE_CANT_UNZIP = 102;
    const INSTALL_ERROR_CODE_CANT_COPY = 103;


    public function __construct()
    {
        $this->rootPath = dirname(_PS_MODULE_DIR_) . '/';
        $this->currentFolder = dirname(__FILE__) . '/';
        $this->bridgePath = $this->rootPath . 'bridge2cart/';
        $this->configFilePath = $this->bridgePath . 'config.php';
        $this->bridgeFilePath = $this->bridgePath . 'bridge.php';
    }

    public function getStoreKey()
    {
        if (file_exists($this->configFilePath)) {
            require_once($this->configFilePath);
            return M1_TOKEN;
        }

        return false;
    }

    public function isBridgeExist()
    {
        if (
            is_dir($this->bridgePath)
            && file_exists($this->bridgeFilePath)
            && file_exists($this->configFilePath)
        ) {
            return true;
        }

        return false;
    }

    public function installBridge(string $storeKey)
    {
        if ($this->isBridgeExist()) {
            return true;
        }

        if (
            !file_put_contents($this->currentFolder
                . 'bridge.zip', self::fileGetContents($this->connectorBridgePath))
        ) {
            return self::INSTALL_ERROR_CODE_CANT_DOWNLOAD;
        }

        $zip = new ZipArchive;

        if (!$zip->open($this->currentFolder . 'bridge.zip')) {
            return self::INSTALL_ERROR_CODE_CANT_UNZIP;
        } else {
            $zip->extractTo($this->currentFolder);
            $zip->close();
        }

        if (!$this->xcopy($this->currentFolder . 'bridge2cart/', $this->rootPath . 'bridge2cart/')) {
            return self::INSTALL_ERROR_CODE_CANT_COPY;
        }

        $this->deleteDir($this->currentFolder . 'bridge2cart/');
        unlink($this->currentFolder . 'readme.txt');

        if ($this->_callbackUrl && $this->_publicKey) {
            if (!extension_loaded('openssl')) {
                $this->_error = 'Open SSL PHP extension isn\'t available.';
            }

            $this->_sendRequestToCallback($storeKey, self::EVENT_CONNECT);
        }

        return($this->_error) ? $this->_error : self::INSTALL_OK;
    }

    public function unInstallBridge()
    {
        if (!$this->isBridgeExist()) {
            return true;
        }

        if ($this->_callbackUrl && $this->_publicKey) {
            if (!extension_loaded('openssl')) {
                $this->_error = 'Open SSL PHP extension isn\'t available.';
            }

            $this->_sendRequestToCallback($this->getStoreKey(), self::EVENT_DISCONNECT);
        }

        return $this->deleteDir($this->bridgePath);
    }

    public function updateToken($token, $sendCallback = true)
    {
        $config = @fopen($this->configFilePath, 'w');

        if (!$config) {
            return false;
        }
        $writed = fwrite($config, "<?php define('M1_TOKEN', '" . $token . "');");

        if ($writed === false) {
            return false;
        }

        fclose($config);

        if ($this->_callbackUrl && $this->_publicKey && $sendCallback) {
            if (!extension_loaded('openssl')) {
                $this->_error = 'Open SSL PHP extension isn\'t available.';
            }

            $this->_sendRequestToCallback($token, self::EVENT_UPDATE);
        }

        return true;
    }

    private function deleteDir($dirPath)
    {
        if (is_dir($dirPath)) {
            $objects = scandir($dirPath);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                        $this->deleteDir($dirPath . DIRECTORY_SEPARATOR . $object);
                    } else {
                        if (!unlink($dirPath . DIRECTORY_SEPARATOR . $object)) {
                            return false;
                        }
                    }
                }
            }

            reset($objects);

            if (!rmdir($dirPath)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    private function xcopy($src, $dst)
    {
        $dir = opendir($src);

        if (!$dir || !mkdir($dst)) {
            return false;
        }

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->xcopy($src . '/' . $file, $dst . '/' . $file);
                } elseif (!copy($src . '/' . $file, $dst . '/' . $file)) {
                    $this->deleteDir($dst);
                    return false;
                }

                chmod($dst . $file, 0755);
                chmod($dst, 0755);
            }
        }

        closedir($dir);

        return true;
    }

    /**
     * @return string
     */
    public static function generateStoreKey()
    {
        $bytesLength = 256;

        if (function_exists('random_bytes')) { // available in PHP 7
            return md5(random_bytes($bytesLength));
        }

        if (function_exists('mcrypt_create_iv')) {
            $bytes = mcrypt_create_iv($bytesLength, MCRYPT_DEV_URANDOM);
            if ($bytes !== false && Tools::strlen($bytes) === $bytesLength) {
                return md5($bytes);
            }
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($bytesLength);
            if ($bytes !== false) {
                return md5($bytes);
            }
        }

        if (file_exists('/dev/urandom') && is_readable('/dev/urandom')) {
            $frandom = fopen('/dev/urandom', 'r');
            if ($frandom !== false) {
                return md5(fread($frandom, $bytesLength));
            }
        }

        $rand = '';
        for ($i = 0; $i < $bytesLength; $i++) {
            $rand .= chr(mt_rand(0, 255));
        }

        return md5($rand);
    }

    /**
     * @param $url
     * @param int $timeout
     * @return bool|mixed|string
     */
    public static function fileGetContents($url, $timeout = 5)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } else {
            return false;
        }
    }

    /**
     * @param $url
     * @param $post
     * @return bool
     */
    private function checkHttpStatusOk($url, $post)
    {
        if (empty($url)) {
            return true;
        }
        if (!function_exists('curl_init')) {
            return false;
        }
        $curl = curl_init();
        try {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            curl_exec($curl);
            $res = (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200);
            curl_close($curl);
            return $res;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function canUninstallModule()
    {
        $verified = $this->checkHttpStatusOk(
            self::VERIFY_UNINSTALL_LINK,
            json_encode(array('store_key' => $this->getStoreKey()))
        );
        if (
            ($verified && self::ON_VERIFY_SUCCESS_DELETE_BRIDGE)
            || (!$verified && self::ON_VERIFY_ERROR_DELETE_BRIDGE)
        ) {
            $this->unInstallBridge();
        }
        return($verified && self::ON_VERIFY_SUCCESS_UNINSTALL_MODULE)
            || (!$verified && self::ON_VERIFY_ERROR_UNINSTALL_MODULE);
    }

    /**
     * @param string $storeKey Store Key
     * @param string $event    Event
     *
     * @return bool|string
     */
    private function _sendRequestToCallback(string $storeKey, string $event)
    {
        if (!($pubKey = openssl_pkey_get_public($this->_publicKey))) {
            $this->_error = 'Public Key isn\'t valid. Please rebuild plugin with valid Public Key.';
        }

        $keyData = openssl_pkey_get_details($pubKey);
        $storeUrl = Tools::getHttpHost(true) . __PS_BASE_URI__;

        $params['store_url'] = $storeUrl;
        $params['store_key'] = $storeKey;
        $params['store_root'] = $this->rootPath;
        $params['bridge_url'] = rtrim($storeUrl, '/') . '/bridge2cart';

        ksort($params, SORT_STRING);
        $data = json_encode($params);

        $len = $keyData['bits'] / 8 - 11;
        $data = str_split($data, $len);
        $result = '';

        foreach ($data as $d) {
            if (openssl_public_encrypt($d, $encrypted, $this->_publicKey)) {
                $result .= $encrypted;
            } else {
                $this->_error = 'Open SSL encryption error';
            }
        }

        $headers = [
            'Accept-Language:*',
            'Content-Type: application/json',
            'x-webhook-cart-id:' . self::CART_ID,
            'x-webhook-event:' . $event,
            'x-webhook-timestamp:' . time()
        ];

        if (function_exists('curl_init')) {
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $this->_callbackUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['data' => base64_encode($result)]));

            $content = curl_exec($curl);

            if (!curl_errno($curl)) {
                switch (curl_getinfo($curl, CURLINFO_HTTP_CODE)) {
                    case 200:
                    case 201:
                    case 204:
                        curl_close($curl);

                        return $content;
                    default:
                        $this->_error = 'Callback Bad response.';
                }
            } else {
                $this->_error = 'Callback Bad response.';
            }

            curl_close($curl);
        } else {
            $this->_error = 'curl_init function not exist';
        }
    }
}