<?php
/**
* 2007-2025 PrestaShop
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
 * @author    NTS <nexustotalsolutions@gmail.com>
 * @copyright Copyright (c) NTS
 * @license   Commercial license
 */

if (!defined('_PS_VERSION_')) { exit; }

class StripejsWebhook extends StripeJs
{
    public static function create()
    {
        try {
            $context = Context::getContext();

            $webhookEndpoint = \Stripe\WebhookEndpoint::create(array(
                'url' => $context->link->getModuleLink('stripejs', 'webhook', array('ajax'=>true), true),
                'enabled_events' => StripeJs::$webhook_events,
                'api_version' => '2020-08-27',
            ));

            Configuration::updateValue('STRIPEJS_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES'), $webhookEndpoint->secret);
        } catch (Exception $e) {
			      Logger::addLog('Create webhook endpoint - '.(string)$e->getMessage(), 4, null, 'stripejs', null, true);
            return (string)$e->getMessage();
        }
		return true;
    }

    public static function getWebhookList()
    {
        try {
            return \Stripe\WebhookEndpoint::all(array('limit' => 16));
        } catch (Exception $e) {
            return false;
        }
    }

    public static function countWebhooksList()
    {
        $list = self::getWebhookList();
        $count_webhook = ((isset($list->data) && is_array($list->data))?count($list->data):0);
        return $count_webhook;
    }

    public static function webhookExists()
    {
        $context = Context::getContext();
        $webhooksList = self::getWebhookList();
        $webhookUrl = $context->link->getModuleLink('stripejs', 'webhook', array('ajax'=>true), true);
        $webhookExists = false;

        foreach ($webhooksList->data as $webhook) {
            if ($webhook->url == $webhookUrl) {
                Configuration::updateValue('STRIPEJS_WEBHOOK_SIG_'.(int)Configuration::get('STRIPE_MODES'), $webhook->id);
                $webhookExists = true;
                break;
            }
        }
        return $webhookExists;
    }
}
