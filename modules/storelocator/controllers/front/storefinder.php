<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domainn
*/

class StorelocatorStorefinderModuleFrontController extends ModuleFrontController
{
    protected $tpl = 'stores.tpl';

    public function init()
    {
        parent::init();
        if (!extension_loaded('Dom')) {
            $this->errors[] = Tools::displayError('PHP "Dom" extension has not been loaded.');
            $this->context->smarty->assign('errors', $this->errors);
        }
        $this->context = Context::getContext();

        if (true === (bool) Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->tpl = 'module:' . $this->module->name . '/views/templates/front/stores_17.tpl';
        }
    }

    protected function displayAjax()
    {
        $days = array();
        $product = Tools::getValue('product');

        $stores = $this->module->getStores();
        $dom = new DOMDocument('1.0');
        $node = $dom->createElement('markers');
        $parnode = $dom->appendChild($node);

        $days[1] = $this->module->translations['monday'];
        $days[2] = $this->module->translations['tuesday'];
        $days[3] = $this->module->translations['wednesday'];
        $days[4] = $this->module->translations['thursday'];
        $days[5] = $this->module->translations['friday'];
        $days[6] = $this->module->translations['saturday'];
        $days[7] = $this->module->translations['sunday'];

        foreach ($stores as $store) {
            $related_products = $store['related_products'];
            $related_products = explode(',', $related_products);
            if (!empty($product) && in_array($product, $related_products)) {
                $other = '';
                $node = $dom->createElement('marker');
                $newnode = $parnode->appendChild($node);
                $newnode->setAttribute('name', $store['name']);
                $address = $this->module->processStoreAddress($store);

                $other .= $this->module->renderStoreWorkingHours($store);
                $newnode->setAttribute('addressNoHtml', strip_tags(str_replace('<br />', ' ', $address)));
                $newnode->setAttribute('address', $address);
                $newnode->setAttribute('other', $other);
                $newnode->setAttribute('phone', $store['phone']);
                $newnode->setAttribute('fax', $store['fax']);
                $newnode->setAttribute('email', $store['email']);
                $newnode->setAttribute('note', $store['note']);
                $newnode->setAttribute('id_store', (int) ($store['id_store']));
                $newnode->setAttribute('has_store_picture', file_exists(_PS_STORE_IMG_DIR_ . (int) ($store['id_store']) . '.jpg'));
                $newnode->setAttribute('lat', (float) ($store['latitude']));
                $newnode->setAttribute('lng', (float) ($store['longitude']));
                $newnode->setAttribute('link', $store['link_rewrite']);
                if (isset($store['distance'])) {
                    $newnode->setAttribute('distance', (int) ($store['distance']));
                }
            } elseif (empty($product)) {
                $other = '';
                $node = $dom->createElement('marker');
                $newnode = $parnode->appendChild($node);
                $newnode->setAttribute('name', $store['name']);
                $address = $this->module->processStoreAddress($store);

                $other .= $this->module->renderStoreWorkingHours($store);
                $newnode->setAttribute('addressNoHtml', strip_tags(str_replace('<br />', ' ', $address)));
                $newnode->setAttribute('address', $address);
                $newnode->setAttribute('other', $other);
                $newnode->setAttribute('phone', $store['phone']);
                $newnode->setAttribute('fax', $store['fax']);
                $newnode->setAttribute('email', $store['email']);
                $newnode->setAttribute('note', $store['note']);
                $newnode->setAttribute('id_store', (int) ($store['id_store']));
                $newnode->setAttribute('has_store_picture', file_exists(_PS_STORE_IMG_DIR_ . (int) ($store['id_store']) . '.jpg'));
                $newnode->setAttribute('lat', (float) ($store['latitude']));
                $newnode->setAttribute('lng', (float) ($store['longitude']));
                $newnode->setAttribute('link', $store['link_rewrite']);
                if (isset($store['distance'])) {
                    $newnode->setAttribute('distance', (int) ($store['distance']));
                }
            }
        }

        header('Content-type: text/xml');
        die($dom->saveXML());
    }

    public function initContent()
    {
        parent::initContent();
        if (Configuration::get('PS_STORES_SIMPLIFIED')) {
            $this->module->assignStoresSimplified();
        } else {
            $this->module->assignStores();
        }
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $medium_size = Image::getSize(ImageType::getFormattedName('medium'));
        } else {
            $medium_size = Image::getSize(ImageType::getFormatedName('medium'));
        }

        $def_zoom = (int) Configuration::get('FMESL_ZOOM_VALUE');
        $def_zoom = ($def_zoom <= 0) ? 10 : $def_zoom;
        $lang_id = (int) $this->context->language->id;
        $id_product = Tools::getValue('id_product', 0);
        $this->context->smarty->assign(array(
            'mediumSize' => $medium_size,
            'defaultLat' => (float) Configuration::get('PS_STORES_CENTER_LAT'),
            'defaultLong' => (float) Configuration::get('PS_STORES_CENTER_LONG'),
            'searchUrl' => $this->context->link->getModuleLink($this->module->name, 'storefinder', array(), true),
            'logo_store' => Configuration::get('PS_STORES_ICON'),
            'stores' => $this->module->getAllStores($id_product),
            'FMESL_STORE_EMAIL' => (int) Configuration::get('FMESL_STORE_EMAIL'),
            'FMESL_STORE_FAX' => (int) Configuration::get('FMESL_STORE_FAX'),
            'FMESL_STORE_NOTE' => (int) Configuration::get('FMESL_STORE_NOTE'),
            'FMESL_USER' => (int) Configuration::get('FMESL_USER'),
            'FMESL_RESET' => (int) Configuration::get('FMESL_RESET'),
            'FMESL_SBP' => (int) Configuration::get('FMESL_SBP'),
            'fmm_sl_zoom' => (int) $def_zoom,
            'fmm_sl_pageheading' => Configuration::get('FMESL_PAGE_TITLE', $lang_id),
            'map_theme' => $this->module->getMapStyles(Configuration::get('FMESL_MAIN_MAP_THEME')),
        ));

        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
            $api_key = Configuration::get('FMESL_KEY');
            $_http = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ? 'https' : 'http';
            $default_country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
            $stores = $this->module->loadAllStores();
            $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $this->context->smarty->assign(array(
                'img_store_dir' => _THEME_STORE_DIR_,
                'img_ps_dir' => $protocol_link . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_,
                'cookie' => $this->context->cookie,
                'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
                'api_key' => $api_key,
                'http' => $_http,
                'region' => Tools::substr($default_country->iso_code, 0, 2),
                '_stores' => $stores,
                'sl_url' => $this->context->link->getPageLink('search'),
                'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
                'base_dir_ssl' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
                'force_ssl' => $force_ssl,
            ));
        }
        $this->setTemplate($this->tpl);
    }

    /**
     * get available stores for google map
     * @return json
     */
    public function displayAjaxGetMapStores()
    {
        if (Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            die($this->ajaxRender(json_encode(array(
                'success' => true,
                'html' => $this->module->getHookMap('carrier', true),
            ))));
        }
        else {
            die(Tools::jsonEncode(array(
                'success' => true,
                'html' => $this->module->getHookMap('carrier', true),
            )));
        }
    }

    /**
     * save selected pickup store
     * @return json
     */
    public function displayAjaxSelectStore()
    {
        $response = array('success' => false, 'hasError' => false, 'msg' => '');
        if (isset($this->context->cart) && $this->context->cart) {
            $storeCarrier = (int) Configuration::get(
                'FMESL_DEFAULT_CARRIER',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );

            $id_carrier = ($idc = (int) Tools::getValue('id_carrier'))? $idc : $this->context->cart->id_carrier;

            if ($storeCarrier != $id_carrier) {
                Locator::popStore($this->context->cart->id);
            } else {
                // set default store as pickup if no selection has been made by user
                $id_store = (int) Tools::getValue('id_store');
                $pickup_date = Tools::getValue('pickup_date', '');
                $isPickupDate = (int) Configuration::get(
                    'FMESL_PICKUP_DATE',
                    null,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                );
                $isPickupTime = (int) Configuration::get(
                    'FMESL_PICKUP_TIME',
                    null,
                    Context::getContext()->shop->id_shop_group,
                    Context::getContext()->shop->id
                );
                
                if ((!$isPickupDate && !$isPickupTime) || empty($pickup_date)) {
                    $pickup_date = date('0000-00-00 00:00:00');
                }

                // if (!$id_store) {
                //     $id_store = (int) Configuration::get(
                //         'FMESL_DEFAULT_STORE',
                //         false,
                //         $this->context->shop->id_shop_group,
                //         $this->context->shop->id
                //     );
                // }

                if ($id_store && Validate::isLoadedObject($store = new Store($id_store, $this->context->language->id))) {
                    $id_address = (int) Locator::getStoreAddressId($id_store);

                    if (!$id_address || !Validate::isLoadedObject($address = new Address((int) $id_address))) {
                        $address = new Address();
                        $address->id_customer = null;
                        $address->id_supplier = null;
                        $address->id_warehouse = null;
                        $address->id_manufacturer = null;
                        $address->alias = sprintf('Store_%s', $id_store);
                        $address->firstname = 'Pickup';
                        $address->lastname = 'From Store';
                        $address->id_country = $store->id_country;
                        $address->id_state = $store->id_state;
                        $address->company = $store->name;
                        $address->address1 = $store->address1;
                        $address->address2 = $store->address2;
                        $address->postcode = $store->postcode;
                        $address->city = $store->city;
                        $address->phone = $store->phone;
                        $address->other = $store->note;

                        if ($address->save() &&
                            Locator::addStoreAddress(array('id_store' => (int) $store->id, 'id_address' => (int) $address->id))) {
                            $id_address = (int) Locator::getStoreAddressId($id_store);
                        }
                    }

                    $data = array(
                        'id_store' => (int) $id_store,
                        'pickup_date' => pSQL($pickup_date),
                        'id_cart' => (int) $this->context->cart->id,
                        'id_carrier' => (int) $id_carrier,
                    );

                    if ($id_address && !empty($pickup_date) && $pickup_date != '0000-00-00 00:00:00') {
                        Locator::pushStore($data);
                        $response['success'] = true;
                        $response['msg'] = $this->module->translations['store_selection_success'];
                    }
                }
            }
        }
        if (Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            die($this->ajaxRender(json_encode($response)));
        }
        else {
            die(Tools::jsonEncode($response));
        }
    }

    public function displayAjaxGetStoreDates()
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_store = (int) Tools::getValue('id_store', $this->module->getDefaultStore());
        if (!$id_store) {
            $id_store = (int) Configuration::get(
                'FMESL_DEFAULT_STORE',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
        }

        // current time zone
        $currentZone = Configuration::get('PS_TIMEZONE');
        $zoneTime = new DateTime(date('Y-m-d'), new DateTimeZone($currentZone));

        $year = $zoneTime->format('Y');
        // current month
        $month = $zoneTime->format('m');

        // next month
        $month += 1;

        // get last day of current month
        $lastday = (int)(date('%d', mktime(0, 0, 0, ($month >= 12 ? 1 : $month + 1), 0, ($month >= 12 ? $year + 1 : $year))));
        // generate date for last day of next month
        $lastDate = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($lastday, 2, '0', STR_PAD_LEFT);

        $wk = array();
        $dates = array('success' => true, 'disabled' => null);
        // gettings weekdays
        $weekdays = array_keys($this->module->weekdays);


        if ($id_store && Validate::isLoadedObject($store = new Store($id_store, $id_lang))) {
            $additionalData = Locator::getStoreByCart($this->context->cart->id);
            // set selected store
            Locator::updateStoreByCart($this->context->cart->id, array('id_store' => (int)$id_store, 'pickup_date' => $additionalData['pickup_date']));
            if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') && true === Tools::version_compare(_PS_VERSION_, '8.0.0', '<')) {
                $storeHours = Tools::jsonDecode($store->hours);
            }
            elseif (true === Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
                $storeHours = json_decode($store->hours);
            }
            else {
                $storeHours = unserialize($store->hours);
            }

            if (isset($storeHours) && $storeHours) {
                foreach ($storeHours as $key => $hour) {
                    if (isset($hour) && $hour) {
                        // separating opening and closong hours
                        $h = (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>='))? explode('-', trim($hour[0])) : explode('-', trim($hour));

                        // disable days of weeks, if no valid opening/closing time is set
                        if ((!isset($h[0]) || false === strtotime($h[0])) && (!isset($h[1]) || false === strtotime($h[1]))) {
                            $endDate = strtotime($lastDate);
                            $startDate = $zoneTime->format('Y-m-d');

                            $start = strtotime(Tools::ucfirst($weekdays[$key]), strtotime($startDate));
                            for (; $start <= $endDate; $start = strtotime('+1 week', $start)) {
                                $dates['disabled'][] = date('Y-m-d', $start);
                            }
                        }

                        $index = $key + 1;
                        // setting sunday as starting weekday for js calander (sunday = 0, monday = 1 and so on)
                        $wk[($index > 6) ? 0 : $index] = array(
                            'minTime' => (!isset($h[0]) || false === strtotime($h[0])) ? false : date("H:i", strtotime($h[0])),
                            'maxTime' => (!isset($h[1]) || false === strtotime($h[1])) ? false : date("H:i", strtotime($h[1])),
                            'defaultHour' => (int) (!isset($h[0]) || false === strtotime($h[0])) ? false : date("H", strtotime($h[0])),
                            'defaultMinute' => (int) (!isset($h[0]) || false === strtotime($h[0])) ? false : date("i", strtotime($h[0])),
                        );
                    }
                }
            }

            ksort($wk);
            $dates['timeslot'] = $wk;
            if (isset($dates['disabled']) && $dates['disabled']) {
                $dates['disabled'] = implode(',', $dates['disabled']);
            }
        }
        if (Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            die($this->ajaxRender(json_encode($dates)));
        }
        else {
            die(Tools::jsonEncode($dates));
        }
    }

    /**
     * save pickup data
     * @return json
     */
    public function displayAjaxSavePickup()
    {
        $id_store = (int) Tools::getValue('id_store');
        $isPickupDate = (int) Configuration::get(
            'FMESL_PICKUP_DATE',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        );
        $isPickupTime = (int) Configuration::get(
            'FMESL_PICKUP_TIME',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        );

        $pickupTime = ($isPickupTime)? Tools::safeOutput(Tools::getValue('pickupTime')): '';
        $pickupDate = ($isPickupDate)? Tools::safeOutput(Tools::getValue('pickupDate')) : '';
        $pickupDateTime = date('Y-m-d H:i:s', strtotime($pickupDate . (!empty($pickupTime) ? ' ' . $pickupTime : '')));
        
        if (!$isPickupDate && !$isPickupTime) {
            $pickupDateTime = date('0000-00-00 00:00:00');
        }

        $response = array('hasError' => true, 'msg' => $this->module->translations['invalid_request']);
        if ($id_store && Validate::isLoadedObject($store = new Store($id_store, $this->context->cart->id_lang))) {
            if (!$store->active) {
                $response['msg'] = $this->module->translations['store_inactive'];
            } elseif ($isPickupDate && $isPickupTime && (empty($pickupDateTime) || !Validate::isDate($pickupDateTime))) {
                $response['msg'] = $this->module->translations['invalid_pickup_date'];
            } else {
                $data = array(
                    'id_store' => (int) $id_store,
                    'pickup_date' => pSQL($pickupDateTime),
                );
                $findActiveStoreByCartId = Locator::getStoreByCart((int)$this->context->cart->id);//displayAjaxSelectStore
                if (empty($findActiveStoreByCartId)) {//make sure the entry already exists in DB
                    $id_carrier = ($idc = (int) Tools::getValue('id_carrier'))? $idc : $this->context->cart->id_carrier;
                    $data = array(
                        'id_store' => (int) $id_store,
                        'pickup_date' => $pickupDateTime,
                        'id_cart' => (int) $this->context->cart->id,
                        'id_carrier' => (int) $id_carrier,
                    );
                    
                    Locator::pushStore($data);
                    if (false === Locator::updateStoreByCart($this->context->cart->id, $data)) {
                        $response['msg'] = $this->module->translations['saved_pickup_date_error'];
                    } else {
                        $response['hasError'] = false;
                        $response['msg'] = $this->module->translations['saved_pickup_date'];
                    }
                }
                else {
                    if (false === Locator::updateStoreByCart($this->context->cart->id, $data)) {
                        $response['msg'] = $this->module->translations['saved_pickup_date_error'];
                    } else {
                        $response['hasError'] = false;
                        $response['msg'] = $this->module->translations['saved_pickup_date'];
                    }
                }
            }
        }
        if (Tools::version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            die($this->ajaxRender(json_encode($response)));
        }
        else {
            die(Tools::jsonEncode($response));
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef(array('map_theme' => sprintf('JSON.parse(\'%s\')', $this->module->getMapStyles(Configuration::get('FMESL_MAIN_MAP_THEME')))));
        $this->module->setMediaFiles();
    }
}
