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

use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\IdentificationHeader;

class EtsAbancartSymfonyMailer extends EtsAbancartMailer
{
    /**
     * Send an e-mail using Symfony Mailer (PrestaShop 9.0.0 compatible)
     *
     * @param int $idLang Language ID
     * @param string $template Template name
     * @param string $subject Subject of the e-mail
     * @param array $templateVars Template variables
     * @param mixed $to Recipient e-mail address or array of addresses
     * @param string|array<string>|null $toName Recipient name (optional)
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
        if (!$idShop) {
            $idShop = $context->shop->id;
        }

        // Hook before email
        $hookBeforeEmailResult = Hook::exec(
            'actionEmailSendBefore',
            array(
                'idLang' => &$idLang,
                'template' => &$template,
                'subject' => &$subject,
                'templateVars' => &$templateVars,
                'to' => &$to,
                'toName' => &$toName,
                'from' => &$from,
                'fromName' => &$fromName,
                'fileAttachment' => &$fileAttachment,
                'mode_smtp' => &$mode_smtp,
                'templatePath' => &$templatePath,
                'die' => &$die,
                'idShop' => &$idShop,
                'bcc' => &$bcc,
                'replyTo' => &$replyTo,
                'replyToName' => &$replyToName,
            ),
            null,
            true
        );

        if ($hookBeforeEmailResult === null) {
            $keepGoing = false;
        } else {
            $keepGoing = array_reduce(
                $hookBeforeEmailResult,
                function ($carry, $item) {
                    return ($item === false) ? false : $carry;
                },
                true
            );
        }

        if (!$keepGoing) {
            return true;
        }

        $shop = new Shop((int)$idShop);
        $mailApiType = Configuration::get('ETS_ABANCART_MAIL_SERVICE') ?: 'default';
        $isMailApi = in_array($mailApiType, self::$mailAPIs);
        
        $configs = array(
            'PS_MAIL_SERVER',
            'PS_MAIL_USER',
            'PS_MAIL_PASSWD',
            'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_SMTP_PORT',
        );
        
        $configs_extra = array(
            'PS_MAIL_METHOD',
            'PS_SHOP_EMAIL',
            'PS_SHOP_NAME',
            'PS_MAIL_TYPE',
            'PS_MAIL_DKIM_ENABLE',
            'PS_MAIL_DKIM_DOMAIN',
            'PS_MAIL_DKIM_SELECTOR',
            'PS_MAIL_DKIM_KEY',
        );
        
        $configuration = Configuration::getMultiple($configs_extra, null, null, $idShop);
        
        if ($mailApiType != 'default' && !$isMailApi) {
            $prefix = Tools::strtoupper($mailApiType);
            foreach ($configs as $config) {
                $configuration[$config] = Configuration::get(preg_replace('/^PS_(.+?)$/', 'ETS_ABANCART_$1_' . $prefix, $config), null, null, $idShop);
            }
            $configuration['PS_MAIL_METHOD'] = self::METHOD_SMTP;
            $configuration['PS_SHOP_EMAIL'] = $configuration['PS_MAIL_USER'];
        } elseif (trim($mailApiType) == 'default') {
            foreach ($configs as $config) {
                $configuration[$config] = Configuration::get($config, null, null, $idShop);
            }
        }

        if ($configuration['PS_MAIL_METHOD'] == self::METHOD_DISABLE) {
            return true;
        }

        Hook::exec('sendMailAlterTemplateVars',
            array(
                'template' => $template,
                'template_vars' => &$templateVars,
            )
        );

        if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION']) || Tools::strtolower($configuration['PS_MAIL_SMTP_ENCRYPTION']) === 'off') {
            $isTls = false;
        } else {
            $isTls = true;
        }

        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }

        /*
         * Sending an e-mail can be of vital importance for the merchant, when his password
         * is lost for example, so we must not die but do our best to send the e-mail.
         */
        if (!isset($from) || !Validate::isEmail($from)) {
            $from = $configuration['PS_SHOP_EMAIL'];
        }

        if (!Validate::isEmail($from)) {
            $from = null;
        }

        if (!isset($fromName) || !Validate::isMailName($fromName)) {
            $fromName = $configuration['PS_SHOP_NAME'];
        }

        if (!Validate::isMailName($fromName)) {
            $fromName = null;
        }

        /*
         * It would be difficult to send an e-mail if the e-mail is not valid,
         * so this time we can die if there is a problem.
         */
        if (!is_array($to) && !Validate::isEmail($to)) {
            self::dieOrLog($die, 'Error: parameter "to" is corrupted');
            return false;
        }

        if (null !== $bcc && !is_array($bcc) && !Validate::isEmail($bcc)) {
            self::dieOrLog($die, 'Error: parameter "bcc" is corrupted');
            $bcc = null;
        }

        if (!is_array($templateVars)) {
            $templateVars = [];
        }

        if (is_string($toName) && !empty($toName) && !Validate::isMailName($toName)) {
            $toName = null;
        }

        if (!Validate::isTplName($template)) {
            self::dieOrLog($die, 'Error: invalid e-mail template');
            return false;
        }

        if (!Validate::isMailSubject($subject)) {
            self::dieOrLog($die, 'Error: invalid e-mail subject');
            return false;
        }

        // Create Symfony Email object
        $email = new Email();

        /* Construct multiple recipients list if needed */
        if (is_array($to) && $to) {
            foreach ($to as $key => $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    continue;
                }

                if (isset($toName[$key])) {
                    $addrName = $toName[$key];
                } else {
                    $addrName = $toName;
                }

                $addrName = ($addrName == null || $addrName == $addr || !Validate::isGenericName($addrName)) ? '' : self::mimeEncode($addrName);
                $email->addTo(new Address(self::toPunycode($addr), $addrName));
            }
            $toPlugin = $to[0];
        } else {
            /* Simple recipient, one address */
            $toPlugin = $to;
            $toName = (($toName == null || $toName == $to) ? '' : self::mimeEncode($toName));
            $email->addTo(new Address(self::toPunycode($to), $toName));
        }

        if (isset($bcc) && is_array($bcc) && $bcc) {
            foreach ($bcc as $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    continue;
                }
                $email->addBcc(new Address(self::toPunycode($addr)));
            }
        } elseif (isset($bcc) && $bcc) {
            $email->addBcc(new Address(self::toPunycode($bcc)));
        }

        try {
            /* Connect with the appropriate configuration */
            if (!$isMailApi) {
                if ($configuration['PS_MAIL_METHOD'] == self::METHOD_SMTP) {
                    if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                        self::dieOrLog($die, 'Error: invalid SMTP server or SMTP port');
                        return false;
                    }
                    
                    $transport = (new EsmtpTransport(
                        $configuration['PS_MAIL_SERVER'],
                        (int) $configuration['PS_MAIL_SMTP_PORT'],
                        $isTls
                    ))
                        ->setUsername($configuration['PS_MAIL_USER'])
                        ->setPassword($configuration['PS_MAIL_PASSWD']);
                } else {
                    $transport = new SendmailTransport();
                }
                
                $mailer = new Mailer($transport);
            }

            /* Get templates content */
            $iso = Language::getIsoById((int)$idLang);
            $isoDefault = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));
            $isoArray = [];
            if ($iso) {
                $isoArray[] = $iso;
            }

            if ($isoDefault && $iso !== $isoDefault) {
                $isoArray[] = $isoDefault;
            }

            if (!in_array('en', $isoArray)) {
                $isoArray[] = 'en';
            }

            $moduleName = false;

            if (preg_match('#modules/#', str_replace(DIRECTORY_SEPARATOR, '/', $templatePath)) &&
                preg_match('#modules/([a-z0-9_-]+)/#ui', str_replace(DIRECTORY_SEPARATOR, '/', $templatePath), $res)
            ) {
                $moduleName = $res[1];
            }
            
            $templatePathExists = false;
            $isoTemplate = '';
            
            foreach ($isoArray as $isoCode) {
                $isoTemplate = $isoCode . '/' . $template;
                $templatePath = self::getTemplateBasePath($isoTemplate, $moduleName, $shop->theme);

                if (!file_exists($templatePath . $isoTemplate . '.txt') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)) {
                    continue;
                } elseif (!file_exists($templatePath . $isoTemplate . '.html') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML)) {
                    continue;
                } else {
                    $templatePathExists = true;
                    break;
                }
            }
            
            if (!$templatePathExists) {
                self::dieOrLog($die, 'Error - The following e-mail template is missing: %s', [$template]);
                return false;
            }
            
            $templateHtml = '';
            $templateTxt = '';
            
            Hook::exec(
                'actionEmailAddBeforeContent',
                array(
                    'id_lang' => (int)$idLang,
                ),
                null,
                true
            );
            
            if (file_exists($templatePath . $isoTemplate . '.html')) {
                $templateHtml .= EtsAbancartHelper::file_get_contents($templatePath . $isoTemplate . '.html');
            }
            
            if (file_exists($templatePath . $isoTemplate . '.txt')) {
                $templateTxt .= EtsAbancartHelper::file_get_contents($templatePath . $isoTemplate . '.txt');
            } else {
                $templateTxt .= strip_tags(
                    html_entity_decode(
                        $templateHtml,
                        ENT_COMPAT,
                        'utf-8'
                    )
                );
            }
            
            Hook::exec(
                'actionEmailAddAfterContent',
                array(
                    'id_lang' => (int)$idLang,
                ),
                null,
                true
            );

            /* Create mail and attach differents parts */
            if (Configuration::get('PS_MAIL_SUBJECT_PREFIX')) {
                $subject = '[' . strip_tags($configuration['PS_SHOP_NAME']) . '] ' . $subject;
            }
            $email->subject($subject);

            /* Set Message-ID - getmypid() is blocked on some hosting */
            $email
                ->getHeaders()
                ->add(new IdentificationHeader('Message-ID', Mail::generateId()));

            if (!($replyTo && Validate::isEmail($replyTo))) {
                $replyTo = $from;
            }

            if (!empty($replyTo) && $replyTo != $toPlugin) {
                $email->replyTo(new Address($replyTo, (string) $replyToName));
            }

            if (false !== Configuration::get('PS_LOGO_MAIL') && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $idShop))) {
                $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $idShop);
            } else {
                if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $idShop))) {
                    $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $idShop);
                } else {
                    $logo = null;
                }
            }
            
            ShopUrl::cacheMainDomainForShop((int)$idShop);
            
            /* don't attach the logo as */
            if (isset($logo)) {
                if ($isMailApi) {
                    $templateVars['{shop_logo}'] = Tools::getShopDomainSsl(true) . _PS_IMG_ . Configuration::get('PS_LOGO', null, null, $idShop);
                } else {
                    $templateVars['{shop_logo}'] = 'cid:shop_logo';
                    $email->embedFromPath($logo, 'shop_logo');
                }
            }
            
            if (!($context->link instanceof Link)) {
                $context->link = new Link();
            }
            
            $url_params = isset($templateVars['{url_params}']) && $templateVars['{url_params}'] ? $templateVars['{url_params}'] : [];
            $templateVars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $idShop));
            $templateVars['{shop_url}'] = EtsAbancartTools::getInstance($context)->getCanonicalUrl($context->link->getPageLink('index', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{my_account_url}'] = EtsAbancartTools::getInstance($context)->getCanonicalUrl($context->link->getPageLink('my-account', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{login_url}'] = EtsAbancartTools::getInstance($context)->getCanonicalUrl($context->link->getPageLink('authentication', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{register_url}'] = EtsAbancartTools::getInstance($context)->getCanonicalUrl($context->link->getPageLink('registration', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{guest_tracking_url}'] = EtsAbancartTools::getInstance($context)->getCanonicalUrl($context->link->getPageLink('guest-tracking', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{history_url}'] = EtsAbancartTools::getInstance($context)->getCanonicalUrl($context->link->getPageLink('history', true, $idLang, null, false, $idShop), $url_params);
            $templateVars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $idShop));
            
            if (!empty($templateVars['{content}']) && (int)Configuration::get('ETS_ABANCART_EMAIL_GENERATE_URL') > 0) {
                preg_match_all('/<a\s+([^>]*\s+)?href=["\']([^"\']*)["\']([^>]*)>/i', $templateVars['{content}'], $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    // Process URL generation logic here if needed
                }
            }
            
            if (isset($templateVars['{content}']))
                $templateVars['{context}'] = $templateVars['{content}'];
                
            $extraTemplateVars = [];
            Hook::exec(
                'actionGetExtraMailTemplateVars',
                array(
                    'id_lang' => (int)$idLang,
                    'extra_template_vars' => &$extraTemplateVars,
                ),
                null,
                true
            );
            $templateVars = array_merge($templateVars, $extraTemplateVars);
            
            $send = false;
            
            if ($isMailApi) {
                $mailMsg = strtr($templateTxt, $templateVars);
                
                switch ($mailApiType) {
                    case 'sendgrid':
                        $apiKeyId = Configuration::get('ETS_ABANCART_API_KEY_SENDGRID');
                        $send = self::sendGrid($apiKeyId, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $mailMsg);
                        break;
                    case 'sendinblue':
                        $apiKeyId = Configuration::get('ETS_ABANCART_API_KEY_SENDINBLUE');
                        $send = self::sendinBlue($apiKeyId, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $mailMsg);
                        break;
                    case 'mailjet':
                        $apiKeyId = Configuration::get('ETS_ABANCART_API_KEY_MAILJET');
                        $secretKey = Configuration::get('ETS_ABANCART_SECRET_KEY_MAILJET');
                        $send = self::mailJet($apiKeyId, $secretKey, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $mailMsg);
                        break;
                    case 'hotmail':
                        $send = self::sendOutlookMail($subject, $to, $toName, $mailMsg, $from, $fromName, $bcc, $replyTo, $replyToName, $context);
                        break;
                }
            } else {
                // Use Symfony Mailer
                if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML) {
                    $templateHtml = strtr($templateHtml, $templateVars);
                    $email->html($templateHtml);
                    if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH) {
                        $templateTxt = strtr($templateTxt, $templateVars);
                        $email->text($templateTxt);
                    }
                } else {
                    $templateTxt = strtr($templateTxt, $templateVars);
                    $email->text($templateTxt);
                }

                if (!empty($fileAttachment)) {
                    // Multiple attachments?
                    if (!is_array(current($fileAttachment))) {
                        $fileAttachment = [$fileAttachment];
                    }

                    foreach ($fileAttachment as $attachment) {
                        if (isset($attachment['content'], $attachment['name'])) {
                            $email->attach($attachment['content'], $attachment['name'], $attachment['mime'] ?? null);
                        }
                    }
                }
                
                /* Send mail */
                $email->from(new Address($from, (string) $fromName));

                // Hook to alter Symfony Mailer before sending mail
                Hook::exec('actionMailAlterMessageBeforeSend', [
                    'message' => &$email,
                ]);

                /* Create new message and DKIM sign it, if enabled and all data for signature are provided */
                if ((bool) $configuration['PS_MAIL_DKIM_ENABLE'] === true
                    && !empty($configuration['PS_MAIL_DKIM_DOMAIN'])
                    && !empty($configuration['PS_MAIL_DKIM_SELECTOR'])
                    && !empty($configuration['PS_MAIL_DKIM_KEY'])
                ) {
                    $signer = new DkimSigner(
                        $configuration['PS_MAIL_DKIM_KEY'],
                        $configuration['PS_MAIL_DKIM_DOMAIN'],
                        $configuration['PS_MAIL_DKIM_SELECTOR']
                    );

                    $signedEmail = $signer->sign($email);
                }

                $mailer->send($signedEmail ?? $email);
                $send = true;
            }

            ShopUrl::resetMainDomainCache();

            if ($send && Configuration::get('PS_LOG_EMAILS')) {
                $mail = new Mail();
                $mail->template = Tools::substr($template, 0, 62);
                $mail->subject = Tools::substr($email->getSubject(), 0, 255);
                $mail->id_lang = (int) $idLang;

                $recipientsTo = self::convertAddressesToArray($email->getTo());
                $recipientsCc = self::convertAddressesToArray($email->getCc());
                $recipientsBcc = self::convertAddressesToArray($email->getBcc());

                if (!is_array($recipientsTo)) {
                    $recipientsTo = [];
                }
                if (!is_array($recipientsCc)) {
                    $recipientsCc = [];
                }
                if (!is_array($recipientsBcc)) {
                    $recipientsBcc = [];
                }
                
                foreach (array_merge($recipientsTo, $recipientsCc, $recipientsBcc) as $emailAlias => $recipient_name) {
                    $mail->recipient = Tools::substr($emailAlias, 0, 255);
                    $mail->add();
                }
            }

            return $send;
        } catch (ExceptionInterface $e) {
            PrestaShopLogger::addLog(
                'Mail Error: ' . $e->getMessage(),
                3,
                null,
                'Mail',
                (int) $idShop,
                true
            );
            $errors[] = $e->getMessage();
            return false;
        }
    }
    
    /**
     * @param Address[] $addresses
     *
     * @return array<string, string|null>
     */
    private static function convertAddressesToArray(array $addresses): array
    {
        $recipients = [];

        foreach ($addresses as $address) {
            $recipients[$address->getAddress()] = $address->getName();
        }

        return $recipients;
    }
}
