<?php
/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class HiGoogleAnalyticsEvent extends ObjectModel
{
    public $id_event;
    public $active = true;
    public $action;
    public $selector;
    public $event_category;
    public $event_action;
    public $event_label;
    public $event_value;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'higacustomevent',
        'primary' => 'id_event',
        'multilang' => false,
        'fields' => [
            'active' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'action' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 50, 'required' => true],
            'selector' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255, 'required' => true],
            'event_category' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255, 'required' => true],
            'event_action' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255, 'required' => true],
            'event_label' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255],
            'event_value' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
        ],
    ];

    public function delete()
    {
        $res = parent::delete();
        $res &= Db::getInstance()->delete('higacustomevent_shop', '`id_event` = ' . (int) $this->id);

        return $res;
    }

    public function assignEventToShops()
    {
        $shop_ids = [];

        if (Shop::isFeatureActive()) {
            $shop_group = Tools::getValue('checkBoxShopGroupAsso_higacustomevent');
            if (is_array($shop_group) && $shop_group) {
                foreach ($shop_group as $shops) {
                    foreach (ShopGroup::getShopsFromGroup($shops) as $shop) {
                        $shop_ids[] = $shop['id_shop'];
                    }
                }
            }
            $shops = Tools::getValue('checkBoxShopAsso_higacustomevent');
            if (is_array($shops) && $shops) {
                foreach ($shops as $id) {
                    if (!in_array($id, $shop_ids)) {
                        $shop_ids[] = $id;
                    }
                }
            }

            // The event should be assigned at least to 1 shop.
            if (!$shop_ids) {
                $shop_ids[] = Context::getContext()->shop->id;
            }
        } else {
            $shop_ids[] = Context::getContext()->shop->id;
        }

        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'higacustomevent_shop` WHERE id_event = ' . (int) $this->id);
        if (is_array($shop_ids) && $shop_ids) {
            foreach ($shop_ids as $idShop) {
                Db::getInstance()->insert('higacustomevent_shop', [
                    'id_event' => (int) $this->id,
                    'id_shop' => (int) $idShop,
                ]);
            }
        }

        return true;
    }

    public static function getEvents($active = true, $idShop = null)
    {
        if (!$idShop) {
            $idShop = Context::getContext()->shop->id;
        }

        $query = new DbQuery();
        $query
            ->select('e.*')
            ->from('higacustomevent', 'e')
            ->leftJoin('higacustomevent_shop', 'es', 'es.`id_event` = e.`id_event`')
            ->where('es.`id_shop` = ' . (int) $idShop);

        if ($active) {
            $query->where('e.`active` = 1');
        }

        return Db::getInstance()->executeS($query);
    }

    public static function filter($filters = [], $pageItems = 50, $pageNumber = 1)
    {
        $idShop = Context::getContext()->shop->id;

        $searchEventCategory = false;
        $searchEventAction = false;
        $searchEventLabel = false;
        $searchStatus = false;
        $searchAction = false;
        $searchValue = false;
        if (isset($filters['higacustomeventFilter_event_category'])) {
            $searchEventCategory = $filters['higacustomeventFilter_event_category'];
        }
        if (isset($filters['higacustomeventFilter_event_action'])) {
            $searchEventAction = $filters['higacustomeventFilter_event_action'];
        }
        if (isset($filters['higacustomeventFilter_event_label'])) {
            $searchEventLabel = $filters['higacustomeventFilter_event_label'];
        }
        if (isset($filters['higacustomeventFilter_eventStatus'])) {
            $searchStatus = $filters['higacustomeventFilter_eventStatus'];
        }
        if (isset($filters['higacustomeventFilter_eventAction'])) {
            $searchAction = $filters['higacustomeventFilter_eventAction'];
        }
        if (isset($filters['higacustomeventFilter_event_value'])) {
            $searchValue = $filters['higacustomeventFilter_event_value'];
        }

        $query = new DbQuery();

        $query
            ->select('e.*')
            ->from('higacustomevent', 'e')
            ->leftJoin('higacustomevent_shop', 'es', 'es.`id_event` = e.`id_event`')
            ->where('es.`id_shop` = ' . (int) $idShop);

        if ($searchEventCategory) {
            $query->where('e.`event_category` like "%' . pSQL($searchEventCategory) . '%"');
        }

        if ($searchEventAction) {
            $query->where('e.`event_action` like "%' . pSQL($searchEventAction) . '%"');
        }

        if ($searchEventLabel) {
            $query->where('e.`event_label` like "%' . pSQL($searchEventLabel) . '%"');
        }

        if ($searchStatus !== false) {
            $query->where('e.`active` = ' . (int) $searchStatus);
        }

        if ($searchAction) {
            $query->where('e.`action` = \'' . pSQL($searchAction) . '\'');
        }

        if ($searchValue) {
            $query->where('e.`event_value` like "%' . pSQL($searchValue) . '%"');
        }

        $res = Db::getInstance()->executeS($query);
        $total = 0;
        if ($res) {
            $total = count($res);
        }

        $query->limit((int) $pageItems, (int) (($pageNumber - 1) * $pageItems));

        $events = Db::getInstance()->executeS($query);

        return [
            'total' => $total,
            'result' => $events,
        ];
    }
}
