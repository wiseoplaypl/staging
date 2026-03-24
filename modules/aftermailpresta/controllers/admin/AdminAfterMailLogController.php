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

class AdminAfterMailLogController extends AdminController
{

    public function __construct()
    {
        $this->table = 'aftermail_queue';
        $this->className = 'AfterMailLog';
        // $this->delete = true;
        // $this->edit = false;
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Would you like to delete the selected items?'),
            ),
        );

        $this->_select = 'c.`lastname` as lastname, am.name';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'aftermail_conf` am ON (am.`id_aftermail_conf` = a.`id_aftermail_conf`)';

        // subtab for mail logs
        $this->fields_list = array(
            'id_aftermail_queue' => array(
                'title' => $this->l('Log ID'),
                'align' => 'center',
                'width' => 10,
            ),
            'id_aftermail_conf' => array(
                'title' => $this->l('Aftermail ID'),
                'align' => 'center',
                'width' => 10,
            ),
            'name' => array(
                'title' => $this->l('Mail Name'),
                'align' => 'center',
                'width' => 50,
            ),
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'align' => 'center',
                'width' => 25,
            ),
            'lastname' => array(
                'title' => $this->l('Customer'),
                'align' => 'center',
                'width' => 25,
            ),
            'timestamp_tosend' => array(
                'title' => $this->l('Schedule'),
                'type' => 'datetime',
                'align' => 'center',
                'width' => 100,
            ),
            'send_state' => array(
                'title' => $this->l('Sent'),
                'icon' => array(
                    0 => 'disabled.gif',
                    1 => 'enabled.gif',
                    'default' => 'disabled.gif',
                ),
                'type' => 'bool',
                'orderby' => false,
            ),
        );

        parent::__construct();
    }
}
