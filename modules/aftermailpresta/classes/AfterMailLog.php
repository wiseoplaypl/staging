<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Shoprunners
 * Use, copy, modification or distribution of this source file without written
 * license agreement from Shoprunners is strictly forbidden.
 * In order to obtain a license, please contact us: info@shoprunners.de
 *
 * @author    Peter Schaeffer - Shoprunners
 * @copyright Copyright(c) 2012-2022 Shoprunners
 * @license   Commercial license
 * @package   aftermail
 */

class AfterMailLog extends ObjectModel
{

    public $id_aftermail_conf;

    public $id_order;

    public $id_customer;

    public $id_product;

    public $timestamp_tosend;

    public $send_state;

    public $reminder_delay;

    public $unsubscribe;

    public $unsubscribe_all;

    protected $table = 'aftermail_queue';

    protected $identifier = 'id_aftermail_queue';

    public function getFields()
    {
        parent::validateFields();
        $fields = array();
        if (isset($this->id)) {
            $fields['id_aftermail_queue'] = (int) $this->id;
        }
        $fields['id_aftermail_conf'] = pSQL($this->id_aftermail_conf);
        $fields['id_order'] = (int) $this->id_order;
        $fields['id_customer'] = (int) $this->id_customer;
        $fields['id_product'] = (int) $this->id_product;
        $fields['timestamp_tosend'] = $this->timestamp_tosend;
        $fields['send_state'] = (int) $this->send_state;

        $fields['reminder_delay'] = (int) $this->reminder_delay;
        $fields['unsubscribe'] = pSQL($this->unsubscribe);
        $fields['unsubscribe_all'] = pSQL($this->unsubscribe_all);

        return $fields;
    }

    public function update($null_values = false)
    {
        $null_values = true;
        return parent::update($null_values);
    }
}
