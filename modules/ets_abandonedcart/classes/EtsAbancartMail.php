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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class EtsAbandonedMail - Proxy class for version-specific mail implementations
 *
 * This class serves as an intermediary that automatically detects the PrestaShop version
 * and loads the appropriate mail class implementation:
 * - For PrestaShop 9.0.0+: Uses EtsAbandonedSymfonyMail (Symfony Mailer)
 * - For PrestaShop < 9.0.0: Uses EtsAbancartMail (Swift Mailer)
 */
class EtsAbancartMail
{
    const METHOD_SMTP = 2;
    const METHOD_DISABLE = 3;
    const SEND_MAIL_DELIVERED = 1;
    const SEND_MAIL_FAILED = 0;
    const SEND_MAIL_TIMEOUT = 2;
    /**
     * @var string The actual mail class to use based on PrestaShop version
     */
    private static $mailClass = null;

    /**
     * Initialize and determine which mail class to use based on PrestaShop version
     */
    private static function init()
    {
        if (self::$mailClass !== null) {
            return;
        }
        // Load base mail class first
        if (!class_exists('EtsAbancartMailer')) {
            require_once(dirname(__FILE__) . '/EtsAbancartMailer.php');
        }
        // Check PrestaShop version to determine which mail class to use
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            // PrestaShop 9.0.0+ uses Symfony Mailer
            if (!class_exists('EtsAbancartSymfonyMailer')) {
                require_once dirname(__FILE__) . '/EtsAbancartSymfonyMailer.php';
            }
            self::$mailClass = 'EtsAbancartSymfonyMailer';
        } else {
            // PrestaShop < 9.0.0 uses Swift Mailer
            if (!class_exists('EtsAbancartSwiftMailer')) {
                require_once dirname(__FILE__) . '/EtsAbancartSwiftMailer';
            }
            self::$mailClass = 'EtsAbancartSwiftMailer';
        }
    }

    /**
     * Send an e-mail using the appropriate mail class based on PrestaShop version
     *
     * @param int $idLang Language ID
     * @param string $template Template name
     * @param string $subject Subject of the e-mail
     * @param array $templateVars Template variables
     * @param mixed $to Recipient e-mail address or array of addresses
     * @param string|null $toName Recipient name (optional)
     * @param string|null $from Sender e-mail address (optional)
     * @param string|null $fromName Sender name (optional)
     * @param mixed|null $fileAttachment Attachment file(s) (optional)
     * @param int|null $mode_smtp SMTP mode (optional)
     * @param string $templatePath Path to the template directory
     * @param bool $die Whether to die on error or log it
     * @param int|null $idShop Shop ID (optional)
     * @param mixed|null $bcc BCC recipient(s) (optional)
     * @param string|null $replyTo Reply-to e-mail address (optional)
     * @param string|null $replyToName Reply-to name (optional)
     * @param array &$errors Reference to an array for collecting errors
     * @param Context|null $context Context object (optional)
     *
     * @return bool True on success, false on failure
     */
    public static function send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null,
        &$errors = [],
        $context = null
    ) {
        self::init();

        // Call the appropriate mail class based on PrestaShop version
        return call_user_func_array(
            [self::$mailClass, 'send'],
            [
                $idLang,
                $template,
                $subject,
                $templateVars,
                $to,
                $toName,
                $from,
                $fromName,
                $fileAttachment,
                $mode_smtp,
                $templatePath,
                $die,
                $idShop,
                $bcc,
                $replyTo,
                $replyToName,
                &$errors,
                $context
            ]
        );
    }
}
