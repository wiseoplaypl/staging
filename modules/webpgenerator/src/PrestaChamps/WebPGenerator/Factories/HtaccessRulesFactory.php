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

namespace PrestaChamps\WebPGenerator\Factories;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class HtaccessRulesFactory
 *
 * @package PrestaChamps\WebPGenerator\Services
 */
class HtaccessRulesFactory
{
    private $separator    = '# WebPGenerator Module - Do not edit';
    private $content      = array();
    private $domains      = array();
    private $mediaDomains = '';

    public function __construct()
    {
        foreach (\ShopUrl::getShopUrls() as $shop_url) {
            /** @var \ShopUrl $shop_url */
            if (!isset($this->domains[$shop_url->domain])) {
                $this->domains[$shop_url->domain] = array();
            }

            $this->domains[$shop_url->domain][] = array(
                'physical' => $shop_url->physical_uri,
                'virtual' => $shop_url->virtual_uri,
                'id_shop' => $shop_url->id_shop,
            );

            if ($shop_url->domain === $shop_url->domain_ssl) {
                continue;
            }

            if (!isset($this->domains[$shop_url->domain_ssl])) {
                $this->domains[$shop_url->domain_ssl] = array();
            }

            $this->domains[$shop_url->domain_ssl][] = array(
                'physical' => $shop_url->physical_uri,
                'virtual' => $shop_url->virtual_uri,
                'id_shop' => $shop_url->id_shop,
            );
        }
        $medias = array();
        if (\Configuration::getMultiShopValues('PS_MEDIA_SERVER_1')
            && \Configuration::getMultiShopValues('PS_MEDIA_SERVER_2')
            && \Configuration::getMultiShopValues('PS_MEDIA_SERVER_3')
        ) {
            $medias = array(
                \Configuration::getMultiShopValues('PS_MEDIA_SERVER_1'),
                \Configuration::getMultiShopValues('PS_MEDIA_SERVER_2'),
                \Configuration::getMultiShopValues('PS_MEDIA_SERVER_3'),
            );
        }
        foreach ($medias as $media) {
            foreach ($media as $media_url) {
                if ($media_url) {
                    $this->mediaDomains .= 'RewriteCond %{HTTP_HOST} ^' . $media_url . '$ [OR] ';
                }
            }
        }

        $this->addLine('');
        $this->addLine('# Apache 2.2');
        $this->addLine('<IfModule !mod_authz_core.c>');
        $this->addLine('    <Files ~ "(?i)^.*\.(webp)$">');
        $this->addLine('        Allow from all');
        $this->addLine('    </Files>');
        $this->addLine('</IfModule>');
        $this->addLine('# Apache 2.4');
        $this->addLine('<IfModule mod_authz_core.c>');
        $this->addLine('    <Files ~ "(?i)^.*\.(webp)$">');
        $this->addLine('        Require all granted');
        $this->addLine('        allow from all');
        $this->addLine('    </Files>');
        $this->addLine('</IfModule>');
        $this->addLine('');
        //Check browser compatibility from .htacces
        $this->addLine('');
        $this->addLine('<IfModule mod_setenvif.c>');
        $this->addLine('SetEnvIf Request_URI "\.(jpe?g|png)$" REQUEST_image');
        $this->addLine('</IfModule>');
        $this->addLine('');
        $this->addLine('<IfModule mod_headers.c>');
        $this->addLine('Header append Vary Accept env=REQUEST_image');
        $this->addLine('</IfModule>');
        $this->addLine('');
        $this->addLine('<IfModule mod_mime.c>');
        $this->addLine('AddType image/webp .webp');
        $this->addLine('</IfModule>');
    }

    public function generateProductImageEntries()
    {
        foreach ($this->domains as $domain => $list_uri) {
            foreach ($list_uri as $uri) {
                /** @noinspection DisconnectedForeachInstructionInspection */
                $this->addLine('');
                $this->addLine('<IfModule mod_rewrite.c>');
                $this->addLine('RewriteEngine On');
                $this->addLine('#Domain: ' . $domain);
                $this->addLine('RewriteRule . - [E=REWRITEBASE:'.$uri['physical'].']');

                $this->addLine('');

                $this->addLine('RewriteCond %{HTTP_ACCEPT} image/webp');
                $this->addLine('RewriteCond %{HTTP_HOST} ^' . $domain . '$');
                $this->addLine('RewriteCond %{DOCUMENT_ROOT}%{ENV:REWRITEBASE}$1.webp -f');
                $this->addLine('RewriteRule (.+)\.(jpe?g|png)$ %{ENV:REWRITEBASE}$1.webp [T=image/webp,L]');
                
                $this->addLine('');

                $rewrite_settings = (int)\Configuration::get('PS_REWRITING_SETTINGS', null, null, (int)$uri['id_shop']);
                if ($rewrite_settings) {
                    $domain_rewrite_cond = 'RewriteCond %{HTTP_HOST} ^' . $domain . '$';
                    // Rewrite product images < 100 millions
                    for ($i = 1; $i <= 8; $i++) {
                        $img_path = $img_name = '';
                        for ($j = 1; $j <= $i; $j++) {
                            $img_path .= '$' . $j . '/';
                            $img_name .= '$' . $j;
                        }
                        $img_name .= '$' . $j;
                        $this->addLine($this->mediaDomains);
                        $this->addLine($domain_rewrite_cond);
                        $this->addLine(
                            'RewriteRule ^' .
                            str_repeat('([0-9])', $i) .
                            '(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/' .
                            $img_path .
                            $img_name .
                            '$' .
                            ($j + 1) .
                            '.webp [L]'
                        );
                    }
                    $this->addLine($this->mediaDomains);
                    $this->addLine($domain_rewrite_cond);
                    $this->addLine(
                        'RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)' .
                        '(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2$3.webp [L]'
                    );
                    $this->addLine($this->mediaDomains);
                    $this->addLine($domain_rewrite_cond);
                    $this->addLine(
                        'RewriteRule ^c/([a-zA-Z_-]+)(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2.webp [L]'
                    );
                }

                $this->addLine('</IfModule>');
            }
        }
    }

    protected function addLine($content)
    {
        $this->content[] = $content;
    }

    public function __toString()
    {
        return implode(PHP_EOL, array($this->separator, implode(PHP_EOL, $this->content), $this->separator));
    }

    public function addToHtaccess()
    {
        $out = \Tools::file_get_contents(_PS_ROOT_DIR_ . '/.htaccess');
        $regex = '#' . preg_quote($this->separator, '#')
            . '(.*?)'
            . preg_quote($this->separator, '#')
            . '#'
            . 's';
        return file_put_contents(
            _PS_ROOT_DIR_ . '/.htaccess',
            $this . PHP_EOL . preg_replace($regex, '', $out)
        );
    }
}
