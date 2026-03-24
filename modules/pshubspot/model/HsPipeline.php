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

use Tiralineas\PsHubspot\AccessToken;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/model/AbstractObjectModel.php';

class HsPipeline extends AbstractObjectModel
{
    const DO_NOT_SYNC = -1;
    // defining one variable for db column, name should be column name
    public $id = null;
    public $sync_as;
    public $sync_at = null;

    public static function getCurrentMapping()
    {
        $fixedCartState = [
            'id_order_state' => 999,
            'name' => 'Abandon Cart',
            'value' => (new self(999))->sync_as,
        ];

        return array_map(
            function ($state) {
                $stage = new self($state['id_order_state']);

                return [
                    'id' => $state['id_order_state'],
                    'name' => $state['name'],
                    'value' => $stage->sync_as,
                ];
            },
            array_merge([$fixedCartState], OrderState::getOrderStates((int) Context::getContext()->language->id))
        );
    }

    public static function getClientPipelines()
    {
        AccessToken::refresh();
        self::init();
        $pipelines = (string) self::$client->sendGetDealsPipelinesRequest()->getBody();

        if (!$pipelines) {
            return '';
        }

        return json_decode($pipelines)->results;
    }

    public static $definition = [
        'table' => 'tiralineas_hs_pipeline',
        'primary' => 'id',
        'multilang' => false,
        'fields' => [
            'id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ],
            'sync_as' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
            ],
            'sync_at' => [
                'type' => self::TYPE_DATE,
                // 'validate' => 'isDateOrNull'
            ],
        ],
    ];
}
