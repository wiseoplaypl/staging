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
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql[] = 'INSERT IGNORE INTO `' . _DB_PREFIX_ . "tiralineas_available_lists`
    (`name`,`dynamic`,`filters`) VALUES    
    ('Customers',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";s:8:\"customer\";s:8:\"property\";s:14:\"lifecyclestage\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Leads',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";s:4:\"lead\";s:8:\"property\";s:14:\"lifecyclestage\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Abandoned Cart',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";s:3:\"yes\";s:8:\"property\";s:22:\"current_abandoned_cart\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Best Customers',1,'a:1:{i:0;a:3:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}i:1;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:22:\"order_frequency_rating\";s:4:\"type\";s:11:\"enumeration\";}i:2;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:20:\"order_recency_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Big Spenders',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Loyal Customers',1,'a:1:{i:0;a:2:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:22:\"order_frequency_rating\";s:4:\"type\";s:11:\"enumeration\";}i:1;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:20:\"order_recency_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Churning Customers',1,'a:1:{i:0;a:3:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}i:1;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:5;s:8:\"property\";s:22:\"order_frequency_rating\";s:4:\"type\";s:11:\"enumeration\";}i:2;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:20:\"order_recency_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Low Value Lost Customers',1,'a:1:{i:0;a:3:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}i:1;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:22:\"order_frequency_rating\";s:4:\"type\";s:11:\"enumeration\";}i:2;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:20:\"order_recency_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('New Customers',1,'a:1:{i:0;a:2:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:22:\"order_frequency_rating\";s:4:\"type\";s:11:\"enumeration\";}i:1;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:20:\"order_recency_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Customers needing attention',1,'a:1:{i:0;a:3:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:3;s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}i:1;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:3;s:8:\"property\";s:22:\"order_frequency_rating\";s:4:\"type\";s:11:\"enumeration\";}i:2;a:4:{s:8:\"operator\";s:7:\"SET_ANY\";s:5:\"value\";s:3:\"1;2\";s:8:\"property\";s:20:\"order_recency_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('About to Sleep',1,'a:1:{i:0;a:3:{i:0;a:4:{s:8:\"operator\";s:7:\"SET_ANY\";s:5:\"value\";s:3:\"1;2\";s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}i:1;a:4:{s:8:\"operator\";s:7:\"SET_ANY\";s:5:\"value\";s:3:\"1;2\";s:8:\"property\";s:22:\"order_frequency_rating\";s:4:\"type\";s:11:\"enumeration\";}i:2;a:4:{s:8:\"operator\";s:7:\"SET_ANY\";s:5:\"value\";s:3:\"1;2\";s:8:\"property\";s:20:\"order_recency_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Mid Spenders',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:3;s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Low Spenders',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:15:\"monetary_rating\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Newsletter Subscriber',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";s:3:\"yes\";s:8:\"property\";s:23:\"newsletter_subscription\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('One time purchase customers',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:1;s:8:\"property\";s:22:\"total_number_of_orders\";s:4:\"type\";s:6:\"number\";}}}'),
    ('Two time purchase customers',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:2;s:8:\"property\";s:22:\"total_number_of_orders\";s:4:\"type\";s:6:\"number\";}}}'),
    ('Three time purchase customers',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:3;s:8:\"property\";s:22:\"total_number_of_orders\";s:4:\"type\";s:6:\"number\";}}}'),
    ('Bought four or more times',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";i:4;s:8:\"property\";s:22:\"total_number_of_orders\";s:4:\"type\";s:6:\"number\";}}}'),
    ('Marketing Qualified Leads',1,'a:1:{i:0;a:1:{i:0;a:4:{s:8:\"operator\";s:2:\"EQ\";s:5:\"value\";s:22:\"marketingqualifiedlead\";s:8:\"property\";s:14:\"lifecyclestage\";s:4:\"type\";s:11:\"enumeration\";}}}'),
    ('Engaged Customers',1,'a:1:{i:0;a:1:{i:0;a:7:{s:8:\"operator\";s:11:\"WITHIN_TIME\";s:14:\"withinLastTime\";i:60;s:18:\"withinLastTimeUnit\";s:4:\"DAYS\";s:14:\"withinLastDays\";i:60;s:14:\"withinTimeMode\";s:4:\"PAST\";s:8:\"property\";s:15:\"last_order_date\";s:4:\"type\";s:4:\"date\";}}}'),
    ('DisEngaged Customers',1,'a:1:{i:0;a:2:{i:0;a:8:{s:14:\"withinLastTime\";i:60;s:18:\"withinLastTimeUnit\";s:4:\"DAYS\";s:23:\"reverseWithinTimeWindow\";b:1;s:14:\"withinLastDays\";i:60;s:14:\"withinTimeMode\";s:4:\"PAST\";s:4:\"type\";s:4:\"date\";s:8:\"operator\";s:11:\"WITHIN_TIME\";s:8:\"property\";s:15:\"last_order_date\";}i:1;a:7:{s:14:\"withinLastTime\";i:180;s:18:\"withinLastTimeUnit\";s:4:\"DAYS\";s:14:\"withinLastDays\";i:180;s:14:\"withinTimeMode\";s:4:\"PAST\";s:4:\"type\";s:4:\"date\";s:8:\"operator\";s:11:\"WITHIN_TIME\";s:8:\"property\";s:15:\"last_order_date\";}}}'),
    ('Repeat Buyers',1,'a:1:{i:0;a:2:{i:0;a:4:{s:4:\"type\";s:6:\"number\";s:8:\"operator\";s:3:\"GTE\";s:8:\"property\";s:22:\"total_number_of_orders\";s:5:\"value\";i:5;}i:1;a:4:{s:4:\"type\";s:6:\"number\";s:8:\"operator\";s:3:\"LTE\";s:8:\"property\";s:27:\"average_days_between_orders\";s:5:\"value\";i:30;}}}')
;";
