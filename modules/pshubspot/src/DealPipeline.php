<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */

namespace Tiralineas\PsHubspot;

use Tiralineas\HubspotApi\Client;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Helper Class to deal with DelaPipelines
 */
class DealPipeline
{
    public static function storePipelineId($name = 'Ecommerce Pipeline')
    {
        $pipeline = self::getPipeline($name);
        \Configuration::updateValue('PS_HUBSPOT_DEAL_PIPELINE_ID', $pipeline->pipelineId, false, '', '');
    }

    public static function getPipeline($name = 'Ecommerce Pipeline', $onlyStages = false)
    {
        $client = new Client(
            [
                'headers' => ['Authorization' => 'Bearer ' . AccessToken::get()],
            ]
        );
        $allDealPipelines = [];
        $GetPipelinesResult = $client->sendGetDealsPipelinesRequest();
        if (!empty($GetPipelinesResult)) {
            $allDealPipelines = json_decode(
                (string) $client->sendGetDealsPipelinesRequest()->getBody()
            )->results;
        }
        $fetchedPipeline = '';
        if ($allDealPipelines) {
            array_walk(
                $allDealPipelines,
                function ($singlePipeline) use ($name, &$fetchedPipeline, $onlyStages) {
                    if ($singlePipeline->label == $name) {
                        $fetchedPipeline = $onlyStages ? $singlePipeline->stages : $singlePipeline;
                    }
                }
            );
        }

        return $fetchedPipeline;
    }

    public static function getPipelineById($pipelineId = '', $onlyStages = false)
    {
        $client = new Client(
            [
                'headers' => ['Authorization' => 'Bearer ' . AccessToken::get()],
            ]
        );
        $loadedPipelines = $client->sendGetDealsPipelinesRequest();
        if ($loadedPipelines) {
            $allDealPipelines = json_decode(
                (string) $client->sendGetDealsPipelinesRequest()->getBody()
            )->results;
        }
        $fetchedPipeline = '';
        if (isset($allDealPipelines) && $allDealPipelines) {
            array_walk(
                $allDealPipelines,
                function ($singlePipeline) use ($pipelineId, &$fetchedPipeline, $onlyStages) {
                    if ($singlePipeline->pipelineId == $pipelineId) {
                        $fetchedPipeline = $onlyStages ? $singlePipeline->stages : $singlePipeline;
                    }
                }
            );
        }

        return $fetchedPipeline;
    }
}
