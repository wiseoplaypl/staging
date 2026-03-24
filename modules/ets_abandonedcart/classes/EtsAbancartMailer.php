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


class EtsAbancartMailer extends MailCore
{
    private static $retriableErrorCodes = array(
        CURLE_COULDNT_RESOLVE_HOST,
        CURLE_COULDNT_CONNECT,
        CURLE_HTTP_NOT_FOUND,
        CURLE_READ_ERROR,
        CURLE_OPERATION_TIMEOUTED,
        CURLE_HTTP_POST_ERROR,
        CURLE_SSL_CONNECT_ERROR,
    );

    public static $mailAPIs = array(
        'hotmail',
        'sendgrid',
        'sendinblue',
        'mailjet'
    );

    /**
     * Send an e-mail
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
        return true;
    }

    const PS_SMTP_METHOD = 2;

    protected static function getTemplateBasePath($isoTemplate, $moduleName, $theme)
    {
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>')) {
            return parent::getTemplateBasePath($isoTemplate, $moduleName, $theme);
        }
        $basePathList = array(
            _PS_ROOT_DIR_ . '/themes/' . $theme . '/',
            _PS_ROOT_DIR_,
        );

        if ($moduleName !== false) {
            $templateRelativePath = '/modules/' . $moduleName . '/mails/';
        } else {
            $templateRelativePath = '/mails/';
        }

        /*---get iso_code---*/
        $isoCode = explode('/', $isoTemplate);
        if (isset($isoCode[0]))
            $isoCode = $isoCode[0];
        /*---end get iso_code---*/

        foreach ($basePathList as $base) {
            $templatePath = $base . $templateRelativePath;
            if (file_exists($templatePath . $isoTemplate . '.txt') || file_exists($templatePath . $isoTemplate . '.html')) {
                /*---include language---*/
                if (file_exists($templatePath . $isoCode . '/lang.php')) {
                    include_once($templatePath . $isoCode . '/lang.php');
                } elseif (file_exists(_PS_MAIL_DIR_ . $isoCode . '/lang.php')) {
                    include_once(_PS_MAIL_DIR_ . $isoCode . '/lang.php');
                } else {
                    Tools::dieOrLog(Tools::displayError('Error - The language file is missing for:') . ' ' . $isoCode, false);
                    return false;
                }
                /*---end include language---*/
                return $templatePath;
            }
        }

        return '';
    }

    public static function toPunycode($to)
    {
        if (method_exists('Mail', 'toPunycode'))
            return parent::toPunycode($to);
        return $to;
    }

    protected static function dieOrLog(
        $die,
        $message,
        $templates = [],
        $domain = 'Admin.Advparameters.Notification'
    ) {
        if (method_exists('Mail', 'dieOrLog')) {
            parent::dieOrLog($die, $message, $templates, $domain);
        } else {
            Tools::dieOrLog(sprintf(Tools::displayError($message), ...$templates), $die);
        }
    }

    public static function sendGrid($apiKeyId, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $message)
    {
        $url = 'https://api.sendgrid.com/v3/mail/send';
        $headers = array(
            "Authorization: Bearer " . $apiKeyId,
            "Content-Type: application/json",
        );
        $http_build_query = '{
            "personalizations": [{
                "to": [
                    {
                      "email": "' . $to . '",
                      "name": "' . $toName . '"
                    }
                ],
                ' . ($bcc ? '
                "bcc": [
                    {
                        "email": "' . $bcc . '"
                    }
                ],' : '') . '
                "subject": "' . $subject . '"
            }],
            "from": {
                "email": "' . $from . '",
                "name": "' . $fromName . '"
            },
            "reply_to": {
                "email": "' . $replyTo . '",
                "name": "' . $replyToName . '"
            },
            "content" : [{
                "type" : "text/html",
                "value" : ' . json_encode($message, JSON_HEX_QUOT | JSON_HEX_TAG) . '
            }]
        }';

        $response = self::cURL($headers, $url, $http_build_query);

        // Kiểm tra nếu cURL thất bại hoàn toàn
        if ($response === false) {
            return false;
        }

        // SendGrid trả về mã 202 khi thành công
        if ($response['http_code'] === 202) {
            return true;
        }

        // Phân tích phản hồi JSON để xử lý lỗi
        $responseData = json_decode($response['body'], true);

        return [
            'error' => isset($responseData['errors']) && !empty($responseData['errors'])
                ? implode('; ', array_column($responseData['errors'], 'message'))
                : 'SendGrid API error: HTTP Code ' . $response['http_code'],
            'details' => $responseData
        ];
    }

    public static function sendinBlue($apiKeyId, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $message)
    {
        $url = 'https://api.sendinblue.com/v3/smtp/email';
        $headers = array(
            "Accept: application/json",
            "Content-Type: application/json",
            "api-key: " . $apiKeyId,
        );
        $http_build_query = '{
            "sender": {
                "name": "' . $fromName . '",
                "email": "' . $from . '"
            },
            "to": [
                {
                    "email": "' . $to . '",
                    "name": "' . $toName . '"
                }
            ],
            ' . ($bcc ? '
            "bcc": [
                {
                    "email": "' . $bcc . '"
                }
            ],' : '') . '
            "htmlContent": ' . json_encode($message, JSON_HEX_QUOT | JSON_HEX_TAG) . ',
            "subject": "' . $subject . '",
            "replyTo": {
                "email": "' . $replyTo . '",
                "name": "' . ($replyToName ?: $fromName) . '"
            }
        }';

        $response = self::cURL($headers, $url, $http_build_query);

        // Kiểm tra nếu cURL thất bại hoàn toàn
        if ($response === false) {
            return false;
        }

        // Kiểm tra HTTP status code (201 là thành công cho Brevo/Sendinblue)
        if ($response['http_code'] === 201) {
            return true;
        }

        // Phân tích phản hồi JSON để xử lý lỗi
        $responseData = json_decode($response['body'], true);

        return [
            'error' => isset($responseData['message']) ? $responseData['message'] : 'Brevo API error: HTTP Code ' . $response['http_code'],
            'code' => isset($responseData['code']) ? $response['http_code']: '',
            'details' => $responseData
        ];
    }

    public static function mailJet($apiKeyId, $secretKey, $subject, $to, $toName, $from, $fromName, $bcc, $replyTo, $replyToName, $message)
    {
        $url = 'https://api.mailjet.com/v3.1/send';
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Basic " . call_user_func('base64_encode', $apiKeyId . ':' . $secretKey),
        );

        // Ưu tiên kiểm tra cấu hình email người gửi
        $senderEmail = Configuration::get('ETS_ABANCART_MAIL_SENDER_EMAIL_MAILJET');
        $senderName = Configuration::get('ETS_ABANCART_MAIL_SENDER_NAME_MAILJET') ?: $fromName;

        $sender = array();
        if ($senderEmail && Validate::isEmail($senderEmail)) {
            $sender['email'] = $senderEmail;
            $sender['name'] = $senderName;
        } else {
            // Chỉ gọi API Mailjet khi không có cấu hình email người gửi
            $sender = self::getSenderMailJet($headers);
            if (empty($sender)) {
                return [
                    'error' => 'Error - Sender is Inactive or Empty. Please active sender link: <a target="_blank" rel="noreferrer noopener" href="https://app.mailjet.com/account/sender">https://app.mailjet.com/account/sender</a>',
                ];
            } elseif (isset($sender['error'])) {
                return [
                    'error' => $sender['error']
                ];
            }
        }

        $http_build_query = '{
            "Messages": [
                {
                    "From": {
                        "Email": "' . $sender['email'] . '",
                        "Name": "' . $sender['name'] . '"
                    },
                    "To": [
                        {
                            "Email": "' . $to . '",
                            "Name": "' . $toName . '"
                        }
                    ],
                    ' . ($bcc ? '
                    "Bcc": [
                        {
                            "Email": "' . $bcc . '"
                        }
                    ],' : '') . '
                    "Reply-To": [
                        {
                            "Email": "' . $replyTo . '",
                            "Name": "' . $replyToName . '"
                        }
                    ],
                    "Subject": "' . $subject . '",
                    "HTMLPart": ' . json_encode($message, JSON_HEX_QUOT | JSON_HEX_TAG) . '
                }
            ]
        }';

        $response = self::cURL($headers, $url, $http_build_query);

        // Kiểm tra nếu cURL thất bại hoàn toàn
        if ($response === false) {
            return false;
        }

        // Mailjet trả về mã 200 hoặc 201 khi thành công
        if (in_array($response['http_code'], [200, 201])) {
            return true;
        }

        // Phân tích phản hồi JSON để xử lý lỗi
        $responseData = json_decode($response['body'], true);

        return [
            'error' => isset($responseData['ErrorMessage']) ? $responseData['ErrorMessage'] : (isset($responseData['Messages']) && !empty($responseData['Messages'][0]['Errors']) ?
                implode('; ', array_column($responseData['Messages'][0]['Errors'], 'ErrorMessage')) :
                'MailJet API error: HTTP Code ' . $response['http_code']),
            'details' => $responseData
        ];
    }

    public static function getSenderMailJet($headers)
    {
        $url = 'https://api.mailjet.com/v3/REST/sender';
        $response = self::cURL($headers, $url, [], 'GET');
        if (!$response) {
            return ['error' => 'Error: API Key error!'];
        }

        $body = isset($response['body']) ? $response['body'] : $response;
        $res = json_decode($body);
        $sender = [];
        if ($res && property_exists($res, 'Data') && is_array($res->Data) && count($res->Data) > 0) {
            foreach ($res->Data as $data) {
                if (property_exists($data, 'Status') && $data->Status === 'Active') {
                    $sender = [
                        'email' => $data->Email,
                        'name' => $data->Name,
                    ];
                    break;
                }
            }
        }
        return $sender;
    }

    public static function cURL($headers, $url, $http_build_query, $request = 'POST')
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ));
        if ($http_build_query) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $http_build_query);
        }

        $response = self::execute($curl, 5, false);

        if ($response === false) {
            curl_close($curl);
            return false;
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'body' => $response,
            'http_code' => $http_code
        ];
    }

    public static function execute($ch, $retries = 5, $closeAfterDone = true)
    {
        $result = false;
        while ($retries--) {
            if (($result = curl_exec($ch)) === false) {
                $curlErrno = curl_errno($ch);
                if (false === in_array($curlErrno, self::$retriableErrorCodes, true) || !$retries) {
                    if ($closeAfterDone) {
                        curl_close($ch);
                    }
                    return false;
                }
                continue;
            }
            if ($closeAfterDone) {
                curl_close($ch);
            }
            break;
        }
        return $result;
    }

    /*---end API mail service---*/

    /* ----- Outlook -----*/
    public static function getOutlookConfig($context)
    {
        return [
            'client_id' => Configuration::get('ETS_ABANCART_CLIENT_ID_HOTMAIL'),
            'client_secret' => Configuration::get('ETS_ABANCART_CLIENT_SECRET_HOTMAIL'),
            'redirect_uri' => $context->link->getModuleLink('ets_abandonedcart', 'callback', [], true)
        ];
    }

    public static function sendOutlookMail(
        $subject,
        $to,
        $toName,
        $message,
        $from = null,
        $fromName = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null,
        $context = null,
        $attachments = []
    ) {
        $from = $from ?: Configuration::get('ETS_ABANCART_MAIL_USER_HOTMAIL');
        $fromName = $fromName ?: Configuration::get('PS_SHOP_NAME');

        if (!$from || !$fromName) {
            return ['error' => 'Missing sender email or name in configuration.'];
        }

        $accessToken = Configuration::get('ETS_ABANCART_MAIL_ACCESS_TOKEN_HOTMAIL');
        if (!self::isAccessTokenValid()) {
            $accessToken = self::refreshOutlookAccessToken($context);
            if (!$accessToken) {
                return ['error' => 'Failed to refresh Outlook access token.'];
            }
        }

        // Cấu trúc dữ liệu email
        $toRecipients = [];
        if (is_array($to)) {
            foreach ($to as $k => $email) {
                $toRecipients[] = [
                    'emailAddress' => [
                        'address' => $email,
                        'name' => is_array($toName) && isset($toName[$k]) ? $toName[$k] : $toName,
                    ]
                ];
            }
        } else {
            $toRecipients[] = [
                'emailAddress' => [
                    'address' => $to,
                    'name' => !empty($toName) ? $toName : $to,
                ]
            ];
        }
        $emailData = [
            "message" => [
                "subject" => $subject,
                "body" => [
                    "contentType" => "HTML",
                    "content" => $message,
                ],
                "toRecipients" => $toRecipients,
                "from" => [
                    "emailAddress" => [
                        "address" => $from,
                        "name" => $fromName,
                    ],
                ],
            ],
        ];

        // Nếu có BCC, thêm vào JSON
        if ($bcc) {
            $emailData["message"]["ccRecipients"] = [[
                "emailAddress" => [
                    "address" => $bcc,
                ],
            ]];
        }

        // Nếu có Reply-To, thêm vào JSON
        if ($replyTo) {
            $emailData["message"]["replyTo"] = [[
                "emailAddress" => [
                    "address" => $replyTo,
                    "name" => $replyToName ?: $fromName,
                ],
            ]];
        }

        if (!empty($attachments)) {
            $emailData['message']['attachments'] = [];
            if (isset($attachments['content'])) {
                $attachments = [$attachments];
            }

            foreach ($attachments as $attachment) {
                if (
                    isset($attachment['content']) &&
                    isset($attachment['name']) &&
                    isset($attachment['mime'])
                ) {
                    $emailData['message']['attachments'][] = [
                        '@odata.type' => '#microsoft.graph.fileAttachment',
                        'name' => $attachment['name'],
                        'contentType' => $attachment['mime'],
                        'contentBytes' => base64_encode($attachment['content']),
                    ];
                }
            }
        }

        // Gửi email qua API Outlook
        $url = 'https://graph.microsoft.com/v1.0/me/sendMail';
        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json",
        ];

        $response = Tools::file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => json_encode($emailData),
            ],
        ]));

        $responseDecoded = json_decode($response, true);

        // Kiểm tra lỗi
        if (isset($responseDecoded['error'])) {
            return [
                'error' => isset($responseDecoded['error']['message']) ? $responseDecoded['error']['message'] : 'Unknown error',
                'details' => $responseDecoded['error'],
            ];
        }

        return ['success' => true];
    }


    public static function refreshOutlookAccessToken($context)
    {
        $config = self::getOutlookConfig($context);
        $data = [
            "client_id" => $config['client_id'],
            "client_secret" => $config['client_secret'],
            "refresh_token" => Configuration::get('ETS_MSV_MAIL_REFRESH_TOKEN_HOTMAIL'),
            "grant_type" => "refresh_token",
            "redirect_uri" => $config['redirect_uri'],
            "scope" => "https://graph.microsoft.com/Mail.Send offline_access"
        ];

        $ch = curl_init('https://login.microsoftonline.com/common/oauth2/v2.0/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);
        if (isset($response['access_token'])) {
            self::saveAccessToken($response);
            return $response['access_token'];
        }
        return false;
    }

    public static function saveAccessToken($response)
    {
        Configuration::updateValue('ETS_ABANCART_MAIL_ACCESS_TOKEN_HOTMAIL', $response['access_token']);
        Configuration::updateValue('ETS_ABANCART_MAIL_REFRESH_TOKEN_HOTMAIL', $response['refresh_token']);
        Configuration::updateValue('ETS_ABANCART_MAIL_TOKEN_EXPIRES_HOTMAIL', time() + $response['expires_in']);
    }

    public static function isAccessTokenValid()
    {
        return time() < Configuration::get('ETS_ABANCART_MAIL_TOKEN_EXPIRES_HOTMAIL');
    }

    /* ----- End Outlook ----- */
}
