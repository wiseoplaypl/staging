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

if (!defined('_PS_VERSION_')) {
    exit();
}

class AftermailPresta extends Module
{

    public function __construct()
    {
        $this->name = 'aftermailpresta';
        $this->tab = 'emailing';
        $this->version = '2.2.3';
        $this->author = 'Shoprunners';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = array(
            'min' => '1.6.0.3',
            'max' => _PS_VERSION_);
        $this->bootstrap = true;

        $this->module_key = '0a0ffe0a42cf70092412fc066aa6a587';

        parent::__construct();

        $this->displayName = $this->l('AfterMail');
        $this->description = $this->l('Sends specific e-mail notifications to customers after buying a product');
        $this->confirmUninstall = $this->l('Are you sure you want to delete all aftermail configurations and logged mails?');
    }

    public function install()
    {
        if (_PS_VERSION_ >= 1.7) {
            $tab = Tab::getIdFromClassName('AdminParentCustomer');
        } else {
            $tab = Tab::getIdFromClassName('AdminCustomers');
        }

        if (!parent::install()
            || !$this->registerHook('newOrder')
            || !$this->registerHook('postUpdateOrderStatus')
            || !$this->registerHook('productbuttons')
            || !$this->registerHook('displayRightColumnProduct')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('actionAuthentication')
            || !Configuration::updateValue('AFTERMAIL_VERSION', $this->version)
            || !$this->installAdminAfterMailTab('AdminAfterMail', 'AfterMail', $tab)) {
            return false;
        }

        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aftermail_conf` (
				`id_aftermail_conf` INT NOT NULL AUTO_INCREMENT ,
				`name` VARCHAR( 150 ) NOT NULL ,
				`delay` INT( 10 ) NOT NULL ,
				`active` TINYINT( 1 ),
				`trigger_type` TINYINT( 1 ),
				`id_orderstate` INT( 5 ),
				`voucher` tinyint(4) NOT NULL,
				`vouchertype` tinyint(4) NOT NULL,
				`voucheramount` float NOT NULL,
				`voucherdays` int(11) NOT NULL,
				`vouchername` varchar(60) NOT NULL,
				`restrictcustomer` TINYINT( 1 ),
				`min_cart_sum` FLOAT NOT NULL,
                `reminder_frequency` VARCHAR( 255 ),
                `subscribe_ids` VARCHAR( 255 ),
				PRIMARY KEY  (`id_aftermail_conf`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aftermail_conf_lang` (
				`id_aftermail_conf` INT NOT NULL ,
				`id_lang` SMALLINT NOT NULL ,
				`e_mail_text_html` TEXT NOT NULL ,
				`e_mail_text_txt` TEXT NOT NULL ,
				`subject` VARCHAR( 150 ) NOT NULL ,
				`id_attachment` INT NOT NULL ,
				PRIMARY KEY  (`id_aftermail_conf`,`id_lang`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aftermail_queue` (
				`id_aftermail_queue` INT NOT NULL AUTO_INCREMENT,
				`id_aftermail_conf` INT NOT NULL ,
				`id_order` INT NOT NULL ,
				`id_customer` INT NOT NULL ,
				`id_product` INT NOT NULL ,
				`timestamp_tosend` TIMESTAMP NOT NULL ,
                `reminder_delay` INT( 10 ) NOT NULL ,
				`send_state` TINYINT( 1 ),
                `unsubscribe` VARCHAR( 30 ),
                `unsubscribe_all` VARCHAR( 30 ),

				PRIMARY KEY  (`id_aftermail_queue`)
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci')) {
            return false;
        }

        if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aftermail_conf_filter` (
				`id_aftermail_conf_filter` INT NOT NULL ,
				`id_aftermail_conf` INT NOT NULL ,
				`id_category` INT NOT NULL ,
				`id_product` INT NOT NULL ,
				`id_combination` INT NOT NULL
			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci')) {
            return false;
        }

        /*
         * if (! Db::getInstance()->Execute('
         * CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'aftermail_prod_remind` (
         * `id_aftermail_prod_remind` INT NOT NULL AUTO_INCREMENT,
         * `id_aftermail_conf` INT NOT NULL,
         * `id_product` INT NOT NULL,
         * `duration` INT NOT NULL
         * ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci')) {
         * return false;
         * }
         */

        Configuration::updateValue('PS_AFTERMAIL_SECURE_KEY', Tools::strtoupper(Tools::passwdGen(16)));

        // we need to copy a tpl file
        /* Logger::addLog('Copy new template'); */
        $this->deleteDir(_PS_ADMIN_DIR_ . '/themes/default/template/controllers/after_mail/');
        return $this->smartCopy(_PS_MODULE_DIR_ . '/aftermailpresta/views/templates/admin/_configure/', _PS_ADMIN_DIR_ . '/themes/default/template/controllers/');
        // everything went right
        // return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallAdminAfterMailTab('AdminAfterMail') || !Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'aftermail_conf') || !Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'aftermail_conf_lang') || !Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'aftermail_queue') || !Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'aftermail_conf_filter') || !Configuration::deleteByName('PS_AFTERMAIL_SECURE_KEY') || !Configuration::deleteByName('AFTERMAIL_VERSION')) {
            return false;
        }

        // delete the template file
        $this->deleteDir(_PS_ADMIN_DIR_ . '/themes/default/template/controllers/after_mail/');
        return true;
    }

    public function hookActionAuthentication($params)
    {
        $customer = $params['customer'];
        if ($customer && !Validate::isLoadedObject($customer)) {
            die(Tools::displayError('Incorrect object Customer.'));
        }
        $possibleMatches = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf` WHERE active=1 AND trigger_type = 4');

        if (empty($possibleMatches)) {
            return;
        }

        $aftermails2Queue = array();
        foreach ($possibleMatches as $possibleMatch) {
            array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
        }

        if (!empty($aftermails2Queue)) {
            // there was a match and we have to add the mails to the queue
            foreach ($aftermails2Queue as $match) {
                $timeConf = Db::getInstance()->ExecuteS('SELECT delay FROM `' . _DB_PREFIX_ . 'aftermail_conf` WHERE id_aftermail_conf=' . (int) $match);
                $time2Send = date('Y-m-d H:i:s', time() + (60 * (int) ($timeConf[0]['delay'])));
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'aftermail_queue` ( `id_aftermail_conf` , `id_order` , `id_customer` , `timestamp_tosend` , `send_state`)
					VALUES (' . (int) $match . ',' . 0 . ',' . (int) $customer->id . ',\'' . $time2Send . '\',0)');
            }
        }
    }

    public function hookNewOrder($params)
    {
        $order = $params['order'];
        if ($order && !Validate::isLoadedObject($order)) {
            die(Tools::displayError('Incorrect object Order.'));
        }

        $possibleMatches = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf` WHERE active=1 AND trigger_type = 0');

        if (empty($possibleMatches)) {
            return;
        }

        $aftermails2Queue = array();
        foreach ($possibleMatches as $possibleMatch) {
            $match = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf']);

            // this means, no special filter for this mail was set -> Add to queue if cart sum is big enough!
            if (empty($match)) {
                if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                    array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                }

                // go to the next possible match
                continue;
            }
            // now we need to get the products of the order
            $products = Db::getInstance()->ExecuteS('SELECT od.id_order, od.product_id, od.product_attribute_id, p.id_category_default FROM `' . _DB_PREFIX_ . 'order_detail` AS od, `' . _DB_PREFIX_ . 'product` AS p WHERE p.id_product=od.product_id and od.id_order=' . (int) $order->id);
            foreach ($products as $product) {
                $categories = implode(', ', Product::getProductCategories((int) $product['product_id']));

                // 1. find category match
                $exist = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf'] . ' AND (id_category=-1 OR id_category IN (' . $categories . ')) AND id_product=-1 AND id_combination = -1');
                if (!empty($exist)) {
                    if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                        array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                    }
                    // go to the next possible match
                    break;
                }
                // 2. find product match
                $exist = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf'] . ' AND id_category IN (' . $categories . ')' . ' AND id_product=' . (int) $product['product_id'] . ' AND id_combination = -1');
                if (!empty($exist)) {
                    if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                        array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                    }
                    // go to the next possible match
                    break;
                }
                // 3. find attribute match
                $exist = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf'] . ' AND id_category IN (' . $categories . ')' . ' AND id_product=' . (int) $product['product_id'] . ' AND id_combination =' . (int) $product['product_attribute_id']);
                if (!empty($exist)) {
                    if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                        array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                    }
                    // go to the next possible match
                    break;
                }
            }
        }

        if (!empty($aftermails2Queue)) {
            // there was a match and we have to add the mails to the queue
            foreach ($aftermails2Queue as $match) {
                $timeConf = Db::getInstance()->ExecuteS('SELECT delay FROM `' . _DB_PREFIX_ . 'aftermail_conf` WHERE id_aftermail_conf=' . (int) $match);
                $time2Send = date('Y-m-d H:i:s', time() + (60 * (int) ($timeConf[0]['delay'])));
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'aftermail_queue` ( `id_aftermail_conf` , `id_order` , `id_customer` , `timestamp_tosend` , `send_state`)
					VALUES (' . (int) $match . ',' . (int) $order->id . ',' . (int) $order->id_customer . ',\'' . $time2Send . '\',0)');
            }
        }
    }

    private function checkOrderValue($order, $mincartsum)
    {
        $cart = new Cart($order->id_cart);
        $orderValue = $cart->getOrderTotal(true);
        if ($mincartsum == 0 || $orderValue > (float) $mincartsum) {
            return true;
        }
        return false;
    }

    /* Hook called when an order changed its status */
    public function hookPostUpdateOrderStatus($params)
    {
        if (!Validate::isLoadedObject($params['newOrderStatus'])) {
            die(Tools::displayError('Some parameters are missing.'));
        }
        $newOrder = $params['newOrderStatus'];
        $order = new Order((int) $params['id_order']);
        if ($order && !Validate::isLoadedObject($order)) {
            die(Tools::displayError('Incorrect object Order.'));
        }

        $possibleMatches = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf` WHERE active=1 AND trigger_type = 1 AND id_orderstate=' . (int) $newOrder->id);
        if (empty($possibleMatches)) {
            return;
        }

        $aftermails2Queue = array();
        foreach ($possibleMatches as $possibleMatch) {
            $match = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf']);

            // this means, no special filter for this mail was set -> Add to queue!
            if (empty($match)) {
                if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                    array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                }
                // go to the next possible match
                continue;
            }

            // now we need to get the products of the order
            $products = Db::getInstance()->ExecuteS('SELECT od.id_order, od.product_id, od.product_attribute_id, p.id_category_default FROM `' . _DB_PREFIX_ . 'order_detail` AS od,  `' . _DB_PREFIX_ . 'product` AS p WHERE p.id_product=od.product_id and od.id_order=' . (int) $order->id);
            foreach ($products as $product) {
                // 1. find category match
                $exist = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf'] . ' AND (id_category=-1 OR id_category=' . (int) $product['id_category_default'] . ') AND id_product=-1 AND id_combination = -1');
                if (!empty($exist)) {
                    if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                        array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                    }
                    // go to the next possible match
                    break;
                }
                // 2. find product match
                $exist = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf'] . ' AND id_category=' . (int) $product['id_category_default'] . ' AND id_product=' . (int) $product['product_id'] . ' AND id_combination = -1');
                if (!empty($exist)) {
                    if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                        array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                    }
                    // go to the next possible match
                    break;
                }
                // 3. find attribute match
                $exist = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf_filter` WHERE id_aftermail_conf=' . (int) $possibleMatch['id_aftermail_conf'] . ' AND id_category=' . (int) $product['id_category_default'] . ' AND id_product=' . (int) $product['product_id'] . ' AND id_combination =' . (int) $product['product_attribute_id']);
                if (!empty($exist)) {
                    if ($this->checkOrderValue($order, $possibleMatch['min_cart_sum'])) {
                        array_push($aftermails2Queue, $possibleMatch['id_aftermail_conf']);
                    }
                    // go to the next possible match
                    break;
                }
            }
        }

        if (!empty($aftermails2Queue)) {
            // there was a match and we have to add the mails to the queue
            foreach ($aftermails2Queue as $match) {
                // make sure, we add no duplicate mails!
                $alreadyThere = Db::getInstance()->ExecuteS('SELECT * FROM  `' . _DB_PREFIX_ . 'aftermail_queue` WHERE id_order = ' . (int) $params['id_order'] . ' AND id_aftermail_conf=' . (int) $match);
                if (!empty($alreadyThere)) {
                    continue;
                }

                $timeConf = Db::getInstance()->ExecuteS('SELECT delay FROM `' . _DB_PREFIX_ . 'aftermail_conf` WHERE id_aftermail_conf=' . (int) $match);
                $time2Send = date('Y-m-d H:i:s', time() + (60 * (int) ($timeConf[0]['delay'])));
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'aftermail_queue` ( `id_aftermail_conf` , `id_order` , `id_customer` , `timestamp_tosend` , `send_state`)
					VALUES (' . (int) $match . ',' . (int) $params['id_order'] . ',' . (int) $order->id_customer . ',\'' . $time2Send . '\',0)');
            }
        }
    }

    public function getContent()
    {
        $cronurl = Tools::getShopDomain(true, true) . __PS_BASE_URI__ . 'index.php?fc=module&module=aftermailpresta&controller=cron&secure_key=' . Configuration::get('PS_AFTERMAIL_SECURE_KEY');
        $this->context->smarty->assign('version', $this->version);
        $this->context->smarty->assign('CRONURL', $cronurl);
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        return $output . $this->renderForm();
    }

    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        return $helper->generateForm(array());
    }

    /**
     * @function installAdminKeyManagerTab
     */
    private function installAdminAfterMailTab($tabClass, $tabName, $idTabParent)
    {
        @copy(_PS_MODULE_DIR_ . $this->name . '/logo.gif', _PS_IMG_DIR_ . 't/' . $tabClass . '.gif');
        $tab = new Tab();
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTabParent;
        if (!$tab->save()) {
            return false;
        }
        return true;
    }

    /**
     * @function uninstallAdminKeyManagerTab
     */
    private function uninstallAdminAfterMailTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }

    public function cronTask()
    {
        $possibleMatches = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_conf` WHERE active=1 AND trigger_type = 3');
        if (!empty($possibleMatches)) {
            $toSend = Db::getInstance()->ExecuteS('
                SELECT c.id_customer
                FROM  `' . _DB_PREFIX_ . 'customer` c
                LEFT JOIN `' . _DB_PREFIX_ . 'aftermail_queue` q ON q.id_customer = c.id_customer AND DATE(q.timestamp_tosend) = CURDATE()
                WHERE MONTH(c.birthday) = MONTH(CURDATE()) AND DAY(c.birthday) = DAY(CURDATE()) AND q.id_aftermail_queue IS NULL');

            if (!empty($toSend)) {
                foreach ($possibleMatches as $match) {
                    foreach ($toSend as $cust) {
                        $time2Send = date('Y-m-d H:i:s', time() + (60 * (int) ($match['delay'])));
                        Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'aftermail_queue` (`id_aftermail_conf` , `id_customer` , `timestamp_tosend` , `send_state`)
					VALUES (' . (int) $match['id_aftermail_conf'] . ',' . (int) $cust['id_customer'] . ',\'' . $time2Send . '\',0)');
                    }
                }
            }
        }

        $queue = Db::getInstance()->ExecuteS('SELECT q.id_aftermail_queue,q.id_order,q.id_customer,q.id_product,q.timestamp_tosend,q.reminder_delay,q.unsubscribe,q.unsubscribe_all,c.id_aftermail_conf,c.trigger_type FROM  `' . _DB_PREFIX_ . 'aftermail_queue` q LEFT JOIN `' . _DB_PREFIX_ . 'aftermail_conf` c ON q.id_aftermail_conf = c.id_aftermail_conf WHERE send_state=0 AND timestamp_tosend<NOW()');
        // something to send?
        if (empty($queue)) {
            return;
        }

        foreach ($queue as $entry) {
            // gather some tempate vars
            if ($entry['trigger_type'] == 2 || $entry['trigger_type'] == 3 || $entry['trigger_type'] == 4) {
                $sql = 'SELECT * FROM  `' . _DB_PREFIX_ . 'customer` c WHERE id_customer = ' . (int) $entry['id_customer'];
                $customer = Db::getInstance()->ExecuteS($sql);
            } else {
                $sql = 'SELECT o.id_order, o.id_lang,c.id_customer,c.id_gender,c.email,c.lastname,c.firstname,c.birthday FROM  `' . _DB_PREFIX_ . 'orders` o LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON o.id_customer = c.id_customer WHERE o.id_order=' . (int) $entry['id_order'];
                $customer = Db::getInstance()->ExecuteS($sql);
            }

            foreach ($customer as $c) {
                if ($entry['trigger_type'] == 2 || $entry['trigger_type'] == 3 || $entry['trigger_type'] == 4) {
                    $c['id_order'] = 0;
                }
                $customizedMail = Db::getInstance()->ExecuteS('SELECT * FROM  `' . _DB_PREFIX_ . 'aftermail_conf_lang` WHERE id_lang=' . (int) $c['id_lang'] . ' AND id_aftermail_conf=' . (int) $entry['id_aftermail_conf']);
                foreach ($customizedMail as $myMail) {
                    $templateVars = array(
                        '{email}' => $c['email'],
                        '{lastname}' => $c['lastname'],
                        '{firstname}' => $c['firstname'],
                        '{id_order}' => $c['id_order'],
                        '{id_customer}' => $c['id_customer'],
                        '{birthday}' => $c['birthday']);
                    if ($entry['trigger_type'] != 2 && $entry['trigger_type'] != 4) {
                        $templateVars = array_merge($templateVars, $this->fillTemplateVars($c['id_order']));
                    }
                    if ($entry['trigger_type'] != 4) {
                        $templateVars = array_merge($templateVars, $this->getTriggerType2Urls($entry, $c['id_lang']));
                    }

                    // check for voucher
                    $vouch = $this->check4Vouchers($entry['id_aftermail_conf'], $c['id_customer']);
                    if ($vouch != false) {
                        $templateVars = array_merge($templateVars, $vouch);
                    }

                    $attachmentAr = null;
                    if ($myMail['id_attachment'] != 0) {
                        $attachment = new Attachment((int) $myMail['id_attachment']);
                        $filename = _PS_DOWNLOAD_DIR_ . $attachment->file;
                        $handle = fopen($filename, 'r');
                        $content = fread($handle, filesize($filename));
                        fclose($handle);
                        $attachmentAr = array(
                            'content' => $content,
                            'name' => $attachment->file_name,
                            'mime' => $attachment->mime);
                    }

                    Mail::Send((int) ($c['id_lang']), $entry['id_aftermail_conf'], $myMail['subject'], $templateVars, $c['email'], $c['firstname'] . ' ' . $c['lastname'], null, null, $attachmentAr, null, dirname(__FILE__) . '/mails/');

                    if ($entry['trigger_type'] == 2) {
                        $newEntry = new AfterMailLog((int) $entry['id_aftermail_queue']);
                        $time2Send = date('Y-m-d H:i:s', strtotime($newEntry->timestamp_tosend) + 60 * $entry['reminder_delay']); // minutes into seconds
                        $newEntry->timestamp_tosend = $time2Send;
                        $newEntry->id = 0;
                        $newEntry->save();
                    }

                    $updateSQL = 'UPDATE  `' . _DB_PREFIX_ . 'aftermail_queue` SET send_state=1 WHERE id_aftermail_queue=' . (int) $entry['id_aftermail_queue'];
                    Db::getInstance()->Execute($updateSQL);
                }
            }
        }
    }

    /**
     *
     * @param
     *            Object Address $the_address that needs to be txt formated
     * @return String the txt formated address block
     */
    protected function _getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array(
            'avoid' => array()), $line_sep, ' ', $fields_style);
    }

    private function check4Vouchers($aftermailConfigID, $id_customer)
    {
        $id_customa = $id_customer;

        $vouchers = Db::getInstance()->ExecuteS('SELECT voucher, vouchertype, voucheramount, voucherdays, vouchername, restrictcustomer
			FROM  `' . _DB_PREFIX_ . 'aftermail_conf`
			WHERE id_aftermail_conf=' . (int) $aftermailConfigID);

        foreach ($vouchers as $voucher) {
            if (((int) $voucher['voucher'] == 1)) {
                if ($voucher['restrictcustomer']) {
                    $id_customa = 0;
                }

                $myvoucher = $this->createVoucher($voucher['voucheramount'], $voucher['voucherdays'], $id_customa, $voucher['vouchertype'], $voucher['vouchername']);
                // log error
                if (!$myvoucher) {
                    continue;
                } else {
                    return $myvoucher;
                }
            }
        }
    }

    private function getTriggerType2Urls($row, $lang)
    {
        $product = new Product($row['id_product']);
        $data = array(
            '{product_name}' => $product->name,
            '{product_id}' => $product->id,
            '{product_url}' => Context::getContext()->link->getProductLink($product, null, null, null, $lang, null, 0, true),
            '{unsubscribe_url}' => _PS_BASE_URL_ . '/modules/aftermailpresta/aftermailajax.php?action=unsubscribe&id_product=' . $row['id_product'] . '&id_conf=' . $row['id_aftermail_conf'] . '&customer_id=' . $row['id_customer'] . '&token=' . $row['unsubscribe'],
            '{unsubscribe_all_url}' => _PS_BASE_URL_ . '/modules/aftermailpresta/aftermailajax.php?action=unsubscribe_all&customer_id=' . $row['id_customer'] . '&token=' . $row['unsubscribe_all']);
        return $data;
    }

    private function fillTemplateVars($id_order)
    {
        if (!isset(Context::getContext()->employee)) {
            $employees = Employee::getEmployees(true);
            foreach ($employees as $employee) {
                $tmpemployee = new Employee((int) $employee['id_employee']);
                if (isset($tmpemployee) && $tmpemployee->isSuperAdmin()) {
                    Context::getContext()->employee = $tmpemployee;
                    break;
                }
            }
        }

        $order = new Order($id_order);
        $invoice = new Address($order->id_address_invoice);
        $currency = new Currency($order->id_currency);
        $delivery = new Address($order->id_address_delivery);
        $delivery_state = $delivery->id_state ? new State($delivery->id_state) : false;
        $invoice_state = $invoice->id_state ? new State($invoice->id_state) : false;
        $customer = new Customer((int) $order->id_customer);

        // Construct order detail table for the email
        $products_list = '';
        $virtual_product = true;

        $product_ul_list = '<ul>';

        $cart = new Cart($order->id_cart);
        $cart_rules = $cart->getCartRules();
        $products = $cart->getProducts();

        foreach ($products as $key => $product) {
            $price = Product::getPriceStatic((int) $product['id_product'], false, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 6, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            $price_wt = Product::getPriceStatic((int) $product['id_product'], true, ($product['id_product_attribute'] ? (int) $product['id_product_attribute'] : null), 2, null, false, true, $product['cart_quantity'], false, (int) $order->id_customer, (int) $order->id_cart, (int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

            $customization_quantity = 0;
            $customized_datas = Product::getAllCustomizedDatas((int) $order->id_cart);
            if (isset($customized_datas[$product['id_product']][$product['id_product_attribute']])) {
                $customization_text = '';
                foreach ($customized_datas[$product['id_product']][$product['id_product_attribute']][$order->id_address_delivery] as $customization) {
                    if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                        foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                            $customization_text .= $text['name'] . ': ' . $text['value'] . '<br />';
                        }
                    }

                    if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                        $customization_text .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])) . '<br />';
                    }
                    $customization_text .= '---<br />';
                }
                $customization_text = rtrim($customization_text, '---<br />');

                $customization_quantity = (int) $product['customization_quantity'];
                $products_list .= '<tr style="background-color: ' . ($key % 2 ? '#DDE2E6' : '#EBECEE') . ';">
					<td style="padding: 0.6em 0.4em;width: 15%;">' . $product['reference'] . '</td>
					<td style="padding: 0.6em 0.4em;width: 30%;"><strong>' . $product['name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : '') . ' - ' . Tools::displayError('Customized') . (!empty($customization_text) ? ' - ' . $customization_text : '') . '</strong></td>
					<td style="padding: 0.6em 0.4em; width: 20%;">' . Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt, $currency, false) . '</td>
					<td style="padding: 0.6em 0.4em; width: 15%;">' . $customization_quantity . '</td>
					<td style="padding: 0.6em 0.4em; width: 20%;">' . Tools::displayPrice($customization_quantity * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt), $currency, false) . '</td>
				</tr>';
            }

            if (!$customization_quantity || (int) $product['cart_quantity'] > $customization_quantity) {
                $products_list .= '<tr style="background-color: ' . ($key % 2 ? '#DDE2E6' : '#EBECEE') . ';">
					<td style="padding: 0.6em 0.4em;width: 15%;">' . $product['reference'] . '</td>
					<td style="padding: 0.6em 0.4em;width: 30%;"><strong>' . $product['name'] . (isset($product['attributes']) ? ' - ' . $product['attributes'] : '') . '</strong></td>
					<td style="padding: 0.6em 0.4em; width: 20%;">' . Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt, $currency, false) . '</td>
					<td style="padding: 0.6em 0.4em; width: 15%;">' . ((int) $product['cart_quantity'] - $customization_quantity) . '</td>
					<td style="padding: 0.6em 0.4em; width: 20%;">' . Tools::displayPrice(((int) $product['cart_quantity'] - $customization_quantity) * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($price, 2) : $price_wt), $currency, false) . '</td>
				</tr>';
            }

            // Check if is not a virutal product for the displaying of shipping
            if (!$product['is_virtual']) {
                $virtual_product &= false;
            }

            $link = new Link();
            $url2product = $link->getProductLink($product);
            if ($url2product) {
                $product_ul_list .= '<li><a href="' . $link->getProductLink($product) . '">' . $product['name'] . '</a></li>';
            } else {
                $product_ul_list .= '<li>' . $product['name'] . '</li>';
            }
        } // end foreach ($products)
        $product_ul_list .= '</ul>';
        // calculating discounts

        $cart_rules_list = '';
        if (sizeof($cart->getProducts()) > 0) {
            foreach ($cart_rules as $cart_rule) {
                $package = array(
                    'id_carrier' => $order->id_carrier,
                    'id_address' => $order->id_address_delivery,
                    'products' => $cart->getProducts());
                $values = array(
                    'tax_incl' => $cart_rule['obj']->getContextualValue(true, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package),
                    'tax_excl' => $cart_rule['obj']->getContextualValue(false, $this->context, CartRule::FILTER_ACTION_ALL_NOCAP, $package));

                // If the reduction is not applicable to this order, then continue with the next one
                if (!$values['tax_excl']) {
                    continue;
                }

                $cart_rules_list .= '
			<tr>
				<td colspan="4" style="padding:0.6em 0.4em;text-align:right">' . Tools::displayError('Voucher name:') . ' ' . $cart_rule['obj']->name . '</td>
				<td style="padding:0.6em 0.4em;text-align:right">' . ($values['tax_incl'] != 0.00 ? '-' : '') . Tools::displayPrice($values['tax_incl'], $currency, false) . '</td>
			</tr>';
            }
        }
        $carrier = new Carrier((int) $order->id_carrier);

        $data = array(
            '{delivery_block_txt}' => $this->_getFormatedAddress($delivery, "\n"),
            '{invoice_block_txt}' => $this->_getFormatedAddress($invoice, "\n"),
            '{delivery_block_html}' => $this->_getFormatedAddress($delivery, '<br />', array(
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname' => '<span style="font-weight:bold;">%s</span>')),
            '{invoice_block_html}' => $this->_getFormatedAddress($invoice, '<br />', array(
                'firstname' => '<span style="font-weight:bold;">%s</span>',
                'lastname' => '<span style="font-weight:bold;">%s</span>')),
            '{delivery_company}' => $delivery->company,
            '{delivery_firstname}' => $delivery->firstname,
            '{delivery_lastname}' => $delivery->lastname,
            '{delivery_address1}' => $delivery->address1,
            '{delivery_address2}' => $delivery->address2,
            '{delivery_city}' => $delivery->city,
            '{delivery_postal_code}' => $delivery->postcode,
            '{delivery_country}' => $delivery->country,
            '{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
            '{delivery_phone}' => ($delivery->phone) ? $delivery->phone : $delivery->phone_mobile,
            '{delivery_other}' => $delivery->other,
            '{invoice_company}' => $invoice->company,
            '{invoice_vat_number}' => $invoice->vat_number,
            '{invoice_firstname}' => $invoice->firstname,
            '{invoice_lastname}' => $invoice->lastname,
            '{invoice_address2}' => $invoice->address2,
            '{invoice_address1}' => $invoice->address1,
            '{invoice_city}' => $invoice->city,
            '{invoice_postal_code}' => $invoice->postcode,
            '{invoice_country}' => $invoice->country,
            '{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
            '{invoice_phone}' => ($invoice->phone) ? $invoice->phone : $invoice->phone_mobile,
            '{invoice_other}' => $invoice->other,
            '{order_name}' => $order->getUniqReference(),
            '{order_date}' => Tools::displayDate($order->date_add),

            // '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), (int)$order->id_lang, 1),
            '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, false),
            '{carrier}' => $virtual_product ? Tools::displayError('No carrier') : $carrier->name,
            '{payment}' => Tools::substr($order->payment, 0, 32),
            '{products}' => $this->formatProductAndVoucherForEmail($products_list),
            '{discounts}' => $this->formatProductAndVoucherForEmail($cart_rules_list),
            '{total_paid}' => Tools::displayPrice($order->total_paid, $currency, false),
            '{total_products}' => Tools::displayPrice($order->total_paid - $order->total_shipping - $order->total_wrapping + $order->total_discounts, $currency, false),
            '{total_discounts}' => Tools::displayPrice($order->total_discounts, $currency, false),
            '{total_shipping}' => Tools::displayPrice($order->total_shipping, $currency, false),
            '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $currency, false),
            '{products_list}' => $product_ul_list);

        return $data;
    }

    public function formatProductAndVoucherForEmail($content)
    {
        return $content;
    }

    public function smartCopy($source, $dest, $options = array('folderPermission' => 0755, 'filePermission' => 0755))
    {
        $result = false;

        if (is_file($source)) {
            if ($dest[Tools::strlen($dest) - 1] == '/') {
                /*
                 * if (! file_exists($dest)) {
                 * cmfcDirectory::makeAll($dest, $options['folderPermission'], true);
                 * }
                 */

                $__dest = $dest . '/' . basename($source);
            } else {
                $__dest = $dest;
            }

            $result = copy($source, $__dest);
            chmod($__dest, $options['filePermission']);
        } elseif (is_dir($source)) {
            if ($dest[Tools::strlen($dest) - 1] == '/') {
                if ($source[Tools::strlen($source) - 1] != '/') {
                    // Change parent itself and its contents
                    $dest = $dest . basename($source);
                    @mkdir($dest);
                    chmod($dest, $options['filePermission']);
                }
            } else {
                if ($source[Tools::strlen($source) - 1] == '/') {
                    // Copy parent directory with new name and all its content
                    @mkdir($dest, $options['folderPermission']);
                    chmod($dest, $options['filePermission']);
                } else {
                    // Copy parent directory with new name and all its content
                    @mkdir($dest, $options['folderPermission']);
                    chmod($dest, $options['filePermission']);
                }
            }

            $dirHandle = opendir($source);
            while ($file = readdir($dirHandle)) {
                if ($file != '.' && $file != '..') {
                    if (!is_dir($source . '/' . $file)) {
                        $__dest = $dest . '/' . $file;
                    } else {
                        $__dest = $dest . '/' . $file;
                    }

                    $result = $this->smartCopy($source . '/' . $file, $__dest, $options);
                }
            }
            closedir($dirHandle);
        } else {
            $result = false;
        }

        return $result;
    }

    public function deleteDir($dirPath)
    {
        // throw new InvalidArgumentException("$dirPath must be a directory");
        if (!is_dir($dirPath)) {
            return;
        }

        if (Tools::substr($dirPath, Tools::strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }

        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    private function createVoucher($amount, $days, $customersid, $type, $discountName)
    {
        $validuntil1 = strftime('%Y-%m-%d', strtotime('+' . (int) ($days) . ' day'));
        $validuntil2 = strftime('%d.%m.%Y', strtotime('+' . (int) ($days) . ' day'));

        $voucher = $this->createDiscount((float) ($amount), $customersid, $validuntil1, $discountName, $type);
        if ($voucher !== false) {
            $vouchervars = array(
                '{voucher_amount}' => $amount,
                '{voucher_days}' => $days,
                '{voucher_validtil}' => $validuntil2,
                '{voucher_num}' => $voucher->code);
            return $vouchervars;
        } else {
            return false;
        }
    }

    private function createDiscount($amount, $id_customer, $dateValidity, $description, $discounttype)
    {
        $cartRule = new CartRule();
        if ($discounttype == 0) {
            $cartRule->reduction_percent = (float) $amount;
        } else {
            $cartRule->reduction_amount = (float) $amount;
        }

        // $cartRule->reduction_percent = (float)$amount;
        $cartRule->id_customer = (int) $id_customer;
        $cartRule->date_to = $dateValidity;
        $cartRule->date_from = date('Y-m-d H:i:s');
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $cartRule->cart_rule_restriction = 1;
        $cartRule->minimum_amount = 0;

        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $cartRule->name[(int) $language['id_lang']] = $description;
        }

        $code = 'AM-' . (int) (20) . '-' . Tools::strtoupper(Tools::passwdGen(10));
        $cartRule->code = $code;
        $cartRule->active = 1;
        if (!$cartRule->add()) {
            return false;
        }
        return $cartRule;
    }

    public function makeDays(&$item, $key)
    {
        if ($item % 30 == 0) {
            $item = ($item / 30) . ' ' . $this->l('Month' . ($item > 30 ? 's' : ''));
        } elseif ($item % 365 == 0) {
            $item = ($item / 365) . ' ' . $this->l('Year' . ($item > 365 ? 's' : ''));
        } elseif ($item == 1) {
            $item = $this->l($key == -1 ? 'Day' : 'Every day');
        } else {
            $item .= ' ' . $this->l('Days');
        }
    }

    public function hookDisplayRightColumnProduct($params)
    {
        if ($this->context->customer->isLogged()) {
            // 1. get available configs
            $result = Db::getInstance()->ExecuteS('SELECT id_aftermail_conf, reminder_frequency, subscribe_ids FROM `' . _DB_PREFIX_ . 'aftermail_conf` ' . 'WHERE active = 1 AND trigger_type = 2 AND reminder_frequency IS NOT NULL AND subscribe_ids IS NOT NULL');

            foreach ($result as $row) {
                $ids = explode(',', $row['subscribe_ids']);
                foreach ($ids as $id) {
                    if ($row['subscribe_ids'] == '0' || trim($id) === Tools::getValue("id_product")) {
                        $result2 = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'aftermail_queue` ' . 'WHERE id_product = ' . (int) Tools::getValue("id_product") . ' AND id_customer = ' . (int) $this->context->customer->id);
                        if (empty($result2)) {
                            // 2. get frequencies
                            $frequencies = explode(',', $row['reminder_frequency']);
                            $frequenciesFormatted = explode(',', $row['reminder_frequency']);

                            array_walk($frequenciesFormatted, array(
                                $this,
                                'makeDays'));

                            $this->context->smarty->assign('frequencies', $frequencies);
                            $this->context->smarty->assign('frequenciesFormatted', $frequenciesFormatted);
                        } else {
                            $days = $result2[0]['reminder_delay'] / 24 / 60;
                            $this->makeDays($days, -1);
                            $this->context->smarty->assign('unsub_token', $result2[0]['unsubscribe']);
                            $this->context->smarty->assign('formattedOption', Tools::strtolower($days));
                        }

                        $this->context->smarty->assign('id_conf', $row['id_aftermail_conf']);
                        $this->context->smarty->assign('subscribed', !empty($result2));

                        return $this->display(__FILE__, 'productreminder.tpl');
                    }
                }
            }
        }
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJS(($this->_path) . 'views/js/front.js');
    }

    public function hookDisplayProductButtons($params)
    {
        return $this->hookDisplayRightColumnProduct($params);
    }

    public function getUnsubscribeLink()
    {
        //
    }

    public function unsubscribe($params)
    {

        /*
     * Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'aftermail_queue` ( `id_aftermail_conf` , `id_order` , `id_customer` , `timestamp_tosend` , `send_state`)
     * VALUES (' . (int)$match . ',' . (int) $order->id . ',' . (int) $order->id_customer . ',\'' . $time2Send . '\',0)');
     */
    }

    public function unsubscribeAll($params)
    {
        //
    }
}
