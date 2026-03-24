<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
*/

class AdminKbBackSubscriberListController extends ModuleAdminControllerCore
{

    public function __construct()
    {
        parent::__construct();
        $this->custom_smarty = new Smarty();
        $this->custom_smarty->setTemplateDir(_PS_MODULE_DIR_ . 'backinstock/views/templates/admin/list');
        $this->custom_smarty->caching = false;
        $this->bootstrap = true;
        $this->display = 'list';
        $this->identifier = 'id';
        $this->module = Module::getInstanceByName('backinstock');
        $this->module->lang = false;
        $this->shop = false;
        $this->table = 'product_update_product_detail';
//        $this->className = 'kbproductupdateproductdetail';


        $this->toolbar_title = $this->module->l('Subscriber Listing', 'adminkbbacksubscriberlistcontroller');

        $this->fields_list = array(
            'id' => array(
                'title' => $this->module->l('Id', 'adminkbbacksubscriberlistcontroller'),
                'align' => 'text-center',
                'order_key' => 'a.id',
                'filter_key' => 'a!id',
            ),
            'email' => array(
                'title' => $this->module->l('Email', 'adminkbbacksubscriberlistcontroller'),
//                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!email',
                'callback' => 'getCustomerEmail',
            ),
//            'customer_id' => array(
//                'title' => $this->module->l('Customer Id', 'adminkbbacksubscriberlistcontroller'),
//                'align' => 'text-center',
//                'havingFilter' => true,
//                'filter_key' => 'a!customer_id',
//            ),
//            'product_id' => array(
//                'title' => $this->module->l('Product Id', 'adminkbbacksubscriberlistcontroller'),
//                'align' => 'text-center',
//                'havingFilter' => true,
//                'filter_key' => 'a!product_id',
//            ),
            'id_image' => array(
                'title' => $this->module->l('Image', 'adminkbbacksubscriberlistcontroller'),
                'orderby' => false,
                'filter' => false,
                'search' => false,
                'callback' => 'showCoverImage'
            ),
            'product_name' => array(
                'title' => $this->module->l('Product Name', 'adminkbbacksubscriberlistcontroller'),
                'type' => 'text',
                'search' => true,
                'havingFilter' => true,
                'orderby' => false,
                'callback' => 'getProductName',
            ),
//            'product_attribute_id' => array(
//                'title' => $this->module->l('Combination Id', 'adminkbbacksubscriberlistcontroller'),
//                'align' => 'text-center',
//                'havingFilter' => true,
//                'filter_key' => 'a!product_attribute_id',
//            ),
            'send' => array(
                'title' => $this->module->l('Back in stock Mail Sent', 'adminkbbacksubscriberlistcontroller'),
                'list' => array(
                    0 => $this->module->l('Pending'),
                    1 => $this->module->l('Sent'),
                ),
                'type' => 'select',
                'align' => 'text-center',
                'havingFilter' => true,
                'filter_key' => 'a!send',
                'callback' => 'showMailSentStatus',
            ),
            'order' => array(
                'type' => 'select',
                'title' => $this->module->l('Order Placed', 'adminkbbacksubscriberlistcontroller'),
                'align' => 'text-center',
                'list' => array(
                    0 => $this->module->l('No'),
                ),
                'havingFilter' => true,
                'filter_key' => 'a!order',
                'callback' => 'showOrderLink',
            ),
            'low_stock_mail' => array(
                'title' => $this->module->l('Low Stock Alert Mail Sent', 'adminkbbacksubscriberlistcontroller'),
                'list' => array(
                    0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
                    1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
                ),
                'type' => 'select',
                'havingFilter' => true,
                'align' => 'text-center',
                'filter_key' => 'a!low_stock_mail',
                'callback' => 'showLowStockMailSentStatus',
            ),
            'date_added' => array(
                'title' => $this->module->l('Date Added', 'adminkbbacksubscriberlistcontroller'),
                'type' => 'datetime',
                'havingFilter' => true,
                'filter_key' => 'a!date_add',
            ),
        );

        $this->_select .= 'a.product_id as id_image,a.*';
        $this->_group_by =  'a.id';
        $this->_orderBy =  'a.id';
        $this->_orderWay =  'desc';
        $this->addRowAction('delete');
        $this->list_no_link = true;
    }
    
    public function showMailSentStatus($id_row, $tr)
    {
        unset($id_row);
        $mail_status = array(
            0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
            1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
        );
        return $mail_status[$tr['send']];
    }
    
    public function getProductName($id_row, $tr)
    {
        $product_name = '';
        $id_product = 0;
        if ($id_row != '') {
            $product_name = $id_row;
            $id_product = $tr['product_id'];
            $product_obj = new Product($tr['product_id']);
            if ((int) $tr['product_attribute_id'] > 0) {
                $attributes = $product_obj->getAttributesResume($this->context->language->id);
                if (is_array($attributes) && !empty($attributes)) {
                    foreach ($attributes as $attr_key => $attribute_data) {
                        if ($attribute_data['id_product_attribute'] == $tr['product_attribute_id']) {
                            $product_name .= ': '.$attribute_data['attribute_designation'];
                            break;
                        }
                    }
                }
            }
        }
        $admin_product_url = $this->context->link->getAdminLink(
            'AdminProducts',
            true,
            array('id_product' => $id_product)
        );
        return '<a href="'.$admin_product_url.'" target="_blank">'.$product_name.'</a>';
    }
    
    public function getCustomerEmail($id_row, $tr)
    {
        $customer_email = $id_row;
        $id_customer = 0;
        if ($id_row != '') {
            $id_customer = $tr['customer_id'];
        }
        if ($id_customer) {
            $admin_customer_url = $this->context->link->getAdminlink('AdminCustomers') . '&id_customer=' . $id_customer. '&updatecustomer';
            return '<a href="'.$admin_customer_url.'" target="_blank">'.$customer_email.'</a>';
        } else {
            return $customer_email;
        }
    }
    
    public function showLowStockMailSentStatus($id_row, $tr)
    {
        unset($id_row);
        $mail_status = array(
            0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
            1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
        );
        return $mail_status[$tr['low_stock_mail']];
    }
    public function showOrderLink($id_row, $tr)
    {
        if ($id_row) {
            $order_obj = new Order($id_row);
            return '<a href="'.$this->context->link->getAdminLink('AdminOrders') . '&id_order=' . $id_row. '&vieworder">'.$order_obj->getUniqReference().'<a>';
        } else {
            return $this->module->l('No Order Placed', 'adminkbbacksubscriberlistcontroller');
        }
    }
    
    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        if (isset($this->context->cookie->kb_redirect_warning)) {
            $this->warnings[] = $this->context->cookie->kb_redirect_warning;
            unset($this->context->cookie->kb_redirect_warning);
        }
        parent::initContent();
    }
    
    /**
     * Get edit link
     */
    private function getImgDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_;
        } else {
            $module_dir = _PS_BASE_URL_;
        }
        return $module_dir;
    }
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;

        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function processDelete()
    {
        if (Tools::getIsset('deleteproduct_update_product_detail') && Tools::getIsset('id')) {
            $id = Tools::getValue('id');
            $sql = 'DELETE FROM `'._DB_PREFIX_.'product_update_product_detail` WHERE' .
                ' id = "'.pSQL($id).'" ';
            if (Db::getInstance()->execute($sql)) {
                $this->confirmations[] = $this->module->l('Subscriber deleted successfully.', 'adminkbbacksubscriberlistcontroller');
            }
        }
    }
    
    public function showCoverImage($id_row, $row_data)
    {
        $path_to_image = false;
        $id_product = 0;
        if (!empty($row_data['product_id'])) {
            $id_product = $row_data['product_id'];
            $product_obj = new Product($row_data['product_id']);
            $image = Image::getCover($row_data['product_id']);
            $link = new Link;
            if ($this->checkSecureUrl()) {
                $image_link = $image ? 'https://'.$link->getImageLink($product_obj->link_rewrite[$this->context->language->id], $image['id_image'], ImageType::getFormattedName('home')) : false;
            } else {
                $image_link = $image ? 'http://'.$link->getImageLink($product_obj->link_rewrite[$this->context->language->id], $image['id_image'], ImageType::getFormattedName('home')) : false;
            }
            if ($image_link == '') {
                $image_link = $this->getImgDirUrl() . _THEME_PROD_DIR_ . Language::getIsoById((int) $this->context->language->id) . '.jpg';
            }

            if ((int) $row_data['product_attribute_id'] > 0) {
                $attributes = $product_obj->getAttributesResume($this->context->language->id);
                if (is_array($attributes) && !empty($attributes)) {
                    foreach ($attributes as $attr_key => $attribute_data) {
                        if ($attribute_data['id_product_attribute'] == $row_data['product_attribute_id']) {
                            $combination_images = $product_obj->_getAttributeImageAssociations($attribute_data['id_product_attribute']);
                            $product_attr_data = array();
                            foreach ($combination_images as $image_id) {
                                $image = new Image($image_id);
                                if (isset($image_id) && $image_id != '') {
                                    $img_path = $this->getImgDirUrl() . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
                                } else {
                                    $img_path = $this->getImgDirUrl() . _THEME_PROD_DIR_ . Language::getIsoById((int) $this->context->language->id) . '.jpg';
                                }
                                $product_attr_data['image_ids'][] = $image_id;
                                $product_attr_data['comb_images'][$image_id]['caption'] = $image->legend[$this->context->language->id];
                                $product_attr_data['comb_images'][$image_id]['path'] = $img_path;
                            }
                            if (isset($combination_images) && !empty($combination_images)) {
                                $product_attr_data['default_comb_img'] = $product_attr_data['comb_images'][$combination_images[0]]['path'];
                                $image_link = $product_attr_data['comb_images'][$combination_images[0]]['path'];
                            }
                        }
                    }
                }
            }
            $admin_product_url = $this->context->link->getAdminLink(
                'AdminProducts',
                true,
                array('id_product' => $id_product)
            );
            return '<a href="'.$admin_product_url.'" target="_blank"><img src="'.$image_link.'" height="75px" width="75px"></a>';
        }
    }
    
    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_btn['csv_export'] = array(
            'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
            'desc' => $this->module->l('Export Subscribers', 'adminkbbacksubscriberlistcontroller'),
            'icon' => 'process-icon-export'
        );
        $this->page_header_toolbar_btn['manual_trigger'] = array(
            'href' => self::$currentIndex . '&manualStockTrigger' . $this->table . '&token=' . $this->token,
            'desc' => $this->module->l('Manual Stock Trigger', 'adminkbbacksubscriberlistcontroller'),
            'icon' => 'process-icon-refresh'
        );
        parent::initPageHeaderToolbar();
    }
    
    public function postProcess()
    {
        if (Tools::isSubmit('exportproduct_update_product_detail')) {
            return $this->processExportKbCSV();
        }
        if (Tools::isSubmit('manualStockTriggerproduct_update_product_detail')) {
            return $this->processManualStockTrigger();
        }
        parent::postProcess();
    }
    
    public function processManualStockTrigger()
    {
        $send_mails_count = 0;
        $get_data = 'select * from ' . _DB_PREFIX_ . 'product_update_product_detail a 
        where active=1 and send="0"';
        $user_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($get_data);
        foreach ($user_data as $user) {
            $quantity_query = 'select quantity from ' . _DB_PREFIX_
                . 'stock_available where id_product_attribute='
                . (int) $user['product_attribute_id'] . ' and id_product=' . (int) $user['product_id'];
            $quantity_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($quantity_query);
            if ($quantity_data[0]['quantity'] > 0) {
                $id_image = Product::getCover($user['product_id']);
                $current = Product::getPriceStatic($user['product_id'], true, null, 6);
                if (count($id_image) > 0) {
                    $image = new Image($id_image['id_image']);
                    $img_path = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . '.jpg';
                }

                $link = $this->context->link->getModuleLink('backinstock', 'delete');

                $url = $this->context->link->getProductLink($user['product_id']);

                $dot_found = 0;
                $needle = '.php';
                $dot_found = strpos($link, $needle);
                if ($dot_found !== false) {
                    $ch = '&';
                } else {
                    $ch = '?';
                }

                $shop_id = Context::getContext()->shop->id;
                $lang_id = $this->context->cookie->id_lang;
                $cid = $user['product_attribute_id'];
                $id = $user['product_id'];
                $cemail = urlencode($user['email']);
                $delete_url = $link . $ch . 'email=' . $cemail . '&id=' . $id .
                    '&attribute_id=' . $cid . '&shop_id=' . $shop_id;
                $product_obj = new Product($user['product_id'], false, $lang_id, $shop_id);
                $attributes = $product_obj->getAttributeCombinationsById($user['product_attribute_id'], $this->context->cookie->id_lang);


                $product_name = $product_obj->name;
                $product_description = $product_obj->description_short;
                if (count($attributes) > 0) {
                    $attr = '';
                    foreach ($attributes as $attribute) {
                        $attr .= $attribute['group_name'] . ': ' . $attribute['attribute_name'] . ', ';
                    }
                    $attr = Tools::substr($attr, 0, -2);
                } else {
                    $attr = '';
                }

                if ((bool) Configuration::get('PS_SSL_ENABLED')) {
                    $ps_base_url = _PS_BASE_URL_SSL_;
                } else {
                    $ps_base_url = _PS_BASE_URL_;
                }
                $img = '<img src="' . $img_path . '" alt="' . $product_name . '" height="100px" width="100px">';
                $getsubject = 'select subject,body from ' . _DB_PREFIX_ . 'product_update_email_templates where id_lang='
                    . (int) $this->context->language->id . ' and template_no="2"';
                $data_subject = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($getsubject);
                $template_vars = array(
                    '{template}' => $data_subject['body'],
                    '{minimal_image}' => $this->context->link->getMediaLink(
                        __PS_BASE_URI__ . 'modules/backinstock/views/img/minimal6.png'
                    ),
                    '{product_description}' => $product_description,
                    '{product_link}' => $this->context->link->getProductLink($product_obj),
                    '{product_image}' => $img_path,
                    '{product_name}' => $product_name,
                    '{current_price}' => Tools::displayPrice($current),
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_url}' => _PS_BASE_URL_ . __PS_BASE_URI__,
                    'ps_root_path' => $ps_base_url
                    . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', ''),
                    '{url}' => $url
                );
                unset($product_obj);
                $subject = $data_subject['subject'];
                $email = $user['email'];
                $lang_iso = $user['lang_iso'];
                if (empty($lang_iso)) {
                    $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
                } else {
                    $id_lang = Language::getIdByIso($lang_iso);
                }
                if (Mail::Send($id_lang, 'quantity_drop', $subject, $template_vars, $email, null, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), null, null, dirname(__FILE__) . '/mails/', false, $this->context->shop->id)) {
                    $update_time = 'update ' . _DB_PREFIX_ . 'product_update_product_detail'
                        . ' set mail_send_date=now(),send="1" where id=' . (int) $user['id'];
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_time);
                    $send_mails_count++;
                }
            }
        }
        if ((int)$send_mails_count > 0) {
            $this->context->cookie->__set(
                'kb_redirect_success',
                $send_mails_count. $this->module->l(' Back In stock Mails send to Subscribers.', 'adminkbbacksubscriberlistcontroller')
            );
        } else {
            $this->context->cookie->__set(
                'kb_redirect_warning',
                $this->module->l('No manual update in product quantity of any of the subscribed product.', 'adminkbbacksubscriberlistcontroller')
            );
        }
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminKbBackSubscriberList', true)
        );
    }
    
    public function processExportKbCSV()
    {
        $data_type = Tools::getValue('data_type', '');
        $sql_connection_query = 'Select * from ' . _DB_PREFIX_ . 'product_update_product_detail';
        $download_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_connection_query);
        if (count($download_data) > 0) {
            $header_array = array(
                $this->module->l('id', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Email', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Product Id', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Combination Id', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Date', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Back In Stock Email Status', 'adminkbbacksubscriberlistcontroller'),
                $this->module->l('Low Stock Alert Email Status', 'adminkbbacksubscriberlistcontroller'),
            );
            $export_data = array();
            $mail_status = array(
                0 => $this->module->l('Pending', 'adminkbbacksubscriberlistcontroller'),
                1 => $this->module->l('Sent', 'adminkbbacksubscriberlistcontroller'),
            );
            foreach ($download_data as $data_key => $data) {
                $detail = array();
                $detail = array(
                    $data['id'],
                    $data['email'],
                    $data['product_id'],
                    $data['product_attribute_id'],
                    $data['date_added'],
                    $mail_status[$data['send']],
                    $mail_status[$data['low_stock_mail']]
                );
                $export_data[] = $detail;
            }
            $this->kbCsvExport($header_array, $export_data);
            return true;
        }
    }
    
    public function kbCsvExport($header_array, $download_data)
    {
        $filename = "kb_subscribers.csv";
        $file = fopen('php://output', 'w');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        fputcsv($file, $header_array, ',');
        if (count($download_data)) {
            foreach ($download_data as $p_data) {
                fputcsv($file, $p_data, ',');
            }
        }
        fclose($file);
        die();
    }
    
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}
