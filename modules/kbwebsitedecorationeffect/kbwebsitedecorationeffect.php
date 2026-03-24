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
 * @copyright 2015 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class KbWebsiteDecorationEffect extends Module
{

    public function __construct()
    {
        $this->name = 'kbwebsitedecorationeffect';
        $this->tab = 'front_office_features';
        $this->version = '1.0.5';
        $this->author = 'Knowband';
        $this->need_instance = 0;
        $this->module_key = '10000391881799e4fda7b6143d8419c9';
        $this->author_address = '0x2C366b113bd378672D4Ee91B75dC727E857A54A6';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        parent::__construct(); /* Calling the parent constuctor method */
        $this->displayName = $this->l('Website Decoration Effect');
        $this->description = $this->l('Website Decoration Effects For Special Occasion');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        if (!Configuration::get('KB_SNOW_EFFECT')) {
            $this->warning = $this->l('No name provided');
        }
        if (!class_exists('Mobile_Detect')) {
            require_once(_PS_TOOL_DIR_ . 'mobile_Detect/Mobile_Detect.php');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
         /**
         * As the hook header was not used anywhere so removed the same on installation of the module
         * PMoct2023 hookheader-remove
         * @date 16-10-2023
         * @modifier Pragya Maurya
         */
        if (!parent::install() ||
            !$this->registerHook('displayHeader')
        ) {
            return false;
        }
        /* Update default value in db */
        $res = $this->getDefaultSettings();
        Configuration::updateValue('KB_SNOW_EFFECT', json_encode($res));
        $this->clearCache();
        return true;
    }

    /* Uninstallation */

    public function uninstall()
    {
         /**
         * As we have updated the install function to not register hook header so 
         * updating the uninstall function to removing the line responsible for unregister of the hook header
         * PMoct2023 hookheader-remove
         * @date 16-10-2023
         * @modifier Pragya Maurya
         */
        if (!parent::uninstall() ||
            !$this->unregisterHook('displayHeader') ||
            !Configuration::deleteByName('KB_SNOW_EFFECT')
        ) {
            return false;
        }
        $this->clearCache();
        return true;
    }

    public function enable($force_all = false)
    {
        $this->unistallDefaultSettings();
        $this->clearCache();
        return parent::enable($force_all);
    }

    private function unistallDefaultSettings()
    {
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $settings = json_decode(Configuration::get('KB_SNOW_EFFECT'), true);
        $settings['enable'] = 0;
        Configuration::updateValue('KB_SNOW_EFFECT', json_encode($settings));
    }

    /**
     * disable module
     * @param boolean $force_all
     * @return boolean
     */
    public function disable($force_all = false)
    {
        $this->unistallDefaultSettings();
        $this->clearCache();
        return parent::disable($force_all);
    }

    protected function clearCache()
    {
        $context = ContextCore::getContext();
        $context->smarty->clearAllCache();
    }

    /* Hook for showing navigation bar in front */

    public function hookDisplayHeader()
    {
        $content = '';
       
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $module_settings = json_decode(Configuration::get('KB_SNOW_EFFECT'), true);
        if ($module_settings['enable'] == 1) {
            //Detect device
            $this->context->controller->addJS($this->_path . 'views/js/front/kb_common.js', 'all');
            if ($module_settings['enable_snoweffect'] == 1) {
//                $this->context->controller->addJS($this->_path . 'views/js/snowstorm.js', 'all');
                if ($module_settings['mobile'] != 1) {
                    $this->mobile_detect = new Mobile_Detect();
                    if ($this->mobile_detect->isMobile()) {
                        return '';
                    }
                }

                //Set per visit frequency
                if (isset($this->context->cookie->kbeffect_last_visit_cookie)) {
                    if (isset($module_settings['max_display_freq'])) {
                        $nextvisittime = 0;
                        $kbeffect_last_visit_cookie = (int) $this->context->cookie->kbeffect_last_visit_cookie;
                        if ($module_settings['max_display_freq'] == 2) {  //hour
                            $nextvisittime = $kbeffect_last_visit_cookie + 3600;
                        } elseif ($module_settings['max_display_freq'] == 3) {  //day
                            $nextvisittime = $kbeffect_last_visit_cookie + 86400;
                        } elseif ($module_settings['max_display_freq'] == 4) {  //week
                            $nextvisittime = $kbeffect_last_visit_cookie + 604800;
                        } elseif ($module_settings['max_display_freq'] == 5) {  //month
                            $nextvisittime = $kbeffect_last_visit_cookie + 2592000;
                        } else {
                            $this->context->cookie->__set('kbeffect_last_visit_cookie', time());
                        }
                        if ($module_settings['max_display_freq'] != 1) {
                            if (time() >= $nextvisittime) {
                                $this->context->cookie->__set('kbeffect_last_visit_cookie', time());
                            } else {
                                return '';
                            }
                        }
                    }
                } else {
                    $this->context->cookie->__set('kbeffect_last_visit_cookie', time());
                }
                //Check pages
                if ($module_settings['where_to_display'] == 2) {
                    $show_page = array();
                    $show_page_fetched = array();
                    $show_page = $module_settings['show_page'];
                    $current_page = $this->context->controller->php_self;
                    foreach ($show_page as $key => $value) {
                        $show_page_fetched[] = $value;
                    }
                    if (!in_array($current_page, $show_page_fetched)) {
                        return '';
                    } else {
                        $show_on_page = false;
                    }
                } elseif ($module_settings['where_to_display'] == 3) {
                    $not_show_page = array();
                    $not_show_page_fetched = array();
                    $not_show_page = $module_settings['not_show_page'];
                    $current_page = $this->context->controller->php_self;
                    foreach ($not_show_page as $key => $value) {
                        $not_show_page_fetched[] = $value;
                    }
                    if (in_array($current_page, $not_show_page_fetched)) {
                        return '';
                    }
                }

                $current_client_time = strtotime(date('Y-m-d H:i:s'));
                if (($module_settings["fix_time"] == 1)) {
                    if (($current_client_time >= strtotime($module_settings["active_date"])) && ($current_client_time <= strtotime($module_settings["expire_date"]))) {
                    } else {
                        return '';
                    }
                }
                // changes by rishabh jain

                if ($module_settings['enable_snoweffect'] == 1) {
                    if ($module_settings['effect_type'] == 'flake') {
                        $this->context->controller->addCSS($this->_path . 'views/css/snowfall.css', 'all');
                        $this->context->controller->addJS($this->_path . 'views/js/snow-flurry.min.js', 'all');
                    } else if ($module_settings['effect_type'] == 'flurry') {
                        $this->context->controller->addJS($this->_path . 'views/js/jquery.flurry.min.js', 'all');
                    } else if ($module_settings['effect_type'] == 'letitsnow') {
                        $this->context->controller->addJS($this->_path . 'views/js/letItSnow.js', 'all');
                    } else if ($module_settings['effect_type'] == 'christmas') {
                        $this->context->controller->addJS($this->_path . 'views/js/jquery.snow.js', 'all');
                    }
                    $this->context->controller->addJS($this->_path . 'views/js/snow-custom.js', 'all');
                }
                if ($this->checkSecureUrl()) {
                    $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
                } else {
                    $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
                }
                $this->context->smarty->assign('image_path', $module_dir . 'kbwebsitedecorationeffect/views/img');
//                $module_settings['enable_extra_effect'] = 0;
                $this->context->smarty->assign(
                    array(
                        'kb_effect_type' => $module_settings['effect_type'],
                        'kb_snowfall_flake_color' => $module_settings['flake_color'],
                        'kb_snowfall_flake_size' => $module_settings['flake_size'],
                        'kb_snowfall_number_flakes' => $module_settings['flake_numbers'],
                        'kb_snowfall_min_speed' => $module_settings['flake_min_speed'],
                        'kb_snowfall_max_speed' => $module_settings['flake_max_speed'],
                        'kb_snowfall_disable_time' => $module_settings['flake_disable_time'],
                        'kb_flurry_rotation_variance' => $module_settings['flurry_rotation_variance'],
                        'kb_flurry_rotation' => $module_settings['flurry_rotation'],
                        'kb_flurry_wind_variance' => $module_settings['flurry_wind_variance'],
                        'kb_flurry_wind_drift' => $module_settings['flurry_wind_drift'],
                        'kb_flurry_max_size' => $module_settings['flurry_max_size'],
                        'kb_flurry_min_size' => $module_settings['flurry_min_size'],
                        'kb_flurry_speed' => $module_settings['flurry_speed'],
                        'kb_flurry_frequency' => $module_settings['flurry_frequency'],
                        'kb_flurry_height' => $module_settings['flurry_height'],
                        'kb_flurry_character' => $module_settings['flurry_character'],
                        'kb_letitsnow_falltime' => $module_settings['letitsnow_falltime'],
                        'kb_letitsnow_speed' => $module_settings['letitsnow_speed'],
                        'kb_letitsnow_max_count' => $module_settings['letitsnow_max_count'],
                        'kb_letitsnow_max_size' => $module_settings['letitsnow_max_size'],
                        'kb_letitsnow_min_size' => $module_settings['letitsnow_min_size'],
                        'kb_christmas_min_size' => $module_settings['christmas_min_size'],
                        'kb_christmas_max_size' => $module_settings['christmas_max_size'],
                        'kb_christmas_fallTimeMultiplier' => $module_settings['christmas_fallTimeMultiplier'],
                        'kb_christmas_fallTimeDifference' => $module_settings['christmas_fallTimeDifference'],
                        'kb_christmas_spawnInterval' => $module_settings['christmas_spawnInterval'],
                        'is_enabled_extra_effect' => 0,
                        'extra_effect_type' => 1,
                    )
                );
                $content = $this->display(__FILE__, 'kbwebsitedecorationeffect.tpl');
            }
            $content .= $this->getDiscountStripContent();
            $content .= $this->getFooterElementContent();
            $content .= $this->getHeaderElementContent();
            $content .= $this->getRandomElementContent();
            
            return $content;
        }
    }

    public function setKbMedia()
    {
        $this->context->controller->addJs($this->getModuleDirUrl() . 'kbwebsitedecorationeffect/views/js/velovalidation.js');
        $this->context->controller->addJs($this->getModuleDirUrl() . 'kbwebsitedecorationeffect/views/js/admin/kbwebsitedecorationeffect.js');
        $this->context->controller->addCSS($this->getModuleDirUrl() . 'kbwebsitedecorationeffect/views/css/admin/kbwebsitedecorationeffect.css');
        $this->context->controller->addJs($this->getModuleDirUrl() . 'kbwebsitedecorationeffect/views/js/admin/jquery.autocomplete.js');
//        $this->context->controller->addJs($this->_path . 'views/js/admin/jquery.autocomplete.js');
    }

    public function getHeaderElementContent()
    {
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $module_settings = json_decode(Configuration::get('KB_SNOW_EFFECT'), true);
        $header_settings = json_decode(Configuration::get('KB_WD_HEADER_SETTING'), true);
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $is_show_header_block = false;
        if ($module_settings['enable'] == 1 && $header_settings['enable'] == 1) {
            if ($header_settings['where_to_display'] == 2) {
                $show_page = array();
                $show_page_fetched = array();
                $show_page = $header_settings['show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($show_page as $key => $value) {
                    $show_page_fetched[] = $value;
                }
                if (!in_array($current_page, $show_page_fetched)) {
                    return '';
                } else {
                    $show_on_page = false;
                }
            } elseif ($header_settings['where_to_display'] == 3) {
                $not_show_page = array();
                $not_show_page_fetched = array();
                $not_show_page = $header_settings['not_show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($not_show_page as $key => $value) {
                    $not_show_page_fetched[] = $value;
                }
                if (in_array($current_page, $not_show_page_fetched)) {
                    return '';
                }
            }
            $current_client_time = strtotime(date('Y-m-d H:i:s'));
            if (($header_settings["fix_time"] == 1)) {
                if (($current_client_time >= strtotime($header_settings["active_date"])) && ($current_client_time <= strtotime($header_settings["expire_date"]))) {
                    $is_show_header_block = true;
                } else {
                    return '';
                }
            }
            if ($header_settings["fix_time"] == 0) {
                $is_show_header_block = true;
            }
        }
        if ($is_show_header_block) {
            if ((int) $header_settings['element_type'] == 1) {
                $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/header/'.$header_settings['element'].'.*';
                $match1 = glob($exist_file);
                if (count($match1) > 0) {
                    $ban = explode('/', $match1[0]);
                    $ban = end($ban);
                    $ban = trim($ban);
                    if (file_exists($match1[0])) {
                        $header_element_demo = $module_dir . $this->name . '/views/img/header/' . $ban.'?time='.time();
                        $is_default_header_image = 0;
                        $this->context->smarty->assign('header_image_path', $header_element_demo);
                        return $this->display(__FILE__, 'header_bar.tpl');
                    } else {
                        return '';
                    }
                }
            } else {
                $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/header.*';
                $match1 = glob($exist_file);
                if (count($match1) > 0) {
                    $ban = explode('/', $match1[0]);
                    $ban = end($ban);
                    $ban = trim($ban);
                    if (file_exists($match1[0])) {
                        $header_element_demo = $module_dir . $this->name . '/views/img/custom/' . $ban.'?time='.time();
                        $is_default_header_image = 0;
                        $this->context->smarty->assign('header_image_path', $header_element_demo);
                        return $this->display(__FILE__, 'header_bar.tpl');
                    } else {
                        return '';
                    }
                }
            }
        }
        return '';
    }
    public function getFooterElementContent()
    {
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $module_settings = json_decode(Configuration::get('KB_SNOW_EFFECT'), true);
        $footer_settings = json_decode(Configuration::get('KB_WD_FOOTER_SETTING'), true);
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $is_show_footer_block = false;
        if ($module_settings['enable'] == 1 && $footer_settings['enable'] == 1) {
            if ($footer_settings['where_to_display'] == 2) {
                $show_page = array();
                $show_page_fetched = array();
                $show_page = $footer_settings['show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($show_page as $key => $value) {
                    $show_page_fetched[] = $value;
                }
                if (!in_array($current_page, $show_page_fetched)) {
                    return '';
                } else {
                    $show_on_page = false;
                }
            } elseif ($footer_settings['where_to_display'] == 3) {
                $not_show_page = array();
                $not_show_page_fetched = array();
                $not_show_page = $footer_settings['not_show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($not_show_page as $key => $value) {
                    $not_show_page_fetched[] = $value;
                }
                if (in_array($current_page, $not_show_page_fetched)) {
                    return '';
                }
            }
            $current_client_time = strtotime(date('Y-m-d H:i:s'));
            if (($footer_settings["fix_time"] == 1)) {
                if (($current_client_time >= strtotime($footer_settings["active_date"])) && ($current_client_time <= strtotime($footer_settings["expire_date"]))) {
                    $is_show_footer_block = true;
                } else {
                    return '';
                }
            }
            if ($footer_settings["fix_time"] == 0) {
                $is_show_footer_block = true;
            }
        }
        if ($is_show_footer_block) {
            if ((int) $footer_settings['element_type'] == 1) {
                $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/footer/'.$footer_settings['element'].'.*';
                $match1 = glob($exist_file);
                if (count($match1) > 0) {
                    $ban = explode('/', $match1[0]);
                    $ban = end($ban);
                    $ban = trim($ban);
                    if (file_exists($match1[0])) {
                        $footer_element_demo = $module_dir . $this->name . '/views/img/footer/' . $ban.'?time='.time();
                        $is_default_header_image = 0;
                        $this->context->smarty->assign('footer_image_path', $footer_element_demo);
                        return $this->display(__FILE__, 'footer_bar.tpl');
                    } else {
                        return '';
                    }
                }
            } else {
                $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/footer.*';
                $match1 = glob($exist_file);
                if (count($match1) > 0) {
                    $ban = explode('/', $match1[0]);
                    $ban = end($ban);
                    $ban = trim($ban);
                    if (file_exists($match1[0])) {
                        $footer_element_demo = $module_dir . $this->name . '/views/img/custom/' . $ban.'?time='.time();
                        $is_default_header_image = 0;
                        $this->context->smarty->assign('footer_image_path', $footer_element_demo);
                        return $this->display(__FILE__, 'footer_bar.tpl');
                    } else {
                        return '';
                    }
                }
            }
        }
        return '';
    }
    public function getRandomElementContent()
    {
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $module_settings = json_decode(Configuration::get('KB_SNOW_EFFECT'), true);
        $random_settings = json_decode(Configuration::get('KB_WD_RANDOM_SETTING'), true);
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $is_show_random_block = false;
        if ($module_settings['enable'] == 1 && $random_settings['enable'] == 1) {
            if ($random_settings['where_to_display'] == 2) {
                $show_page = array();
                $show_page_fetched = array();
                $show_page = $random_settings['show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($show_page as $key => $value) {
                    $show_page_fetched[] = $value;
                }
                if (!in_array($current_page, $show_page_fetched)) {
                    return '';
                } else {
                    $show_on_page = false;
                }
            } elseif ($random_settings['where_to_display'] == 3) {
                $not_show_page = array();
                $not_show_page_fetched = array();
                $not_show_page = $random_settings['not_show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($not_show_page as $key => $value) {
                    $not_show_page_fetched[] = $value;
                }
                if (in_array($current_page, $not_show_page_fetched)) {
                    return '';
                }
            }
            $current_client_time = strtotime(date('Y-m-d H:i:s'));
            if (($random_settings["fix_time"] == 1)) {
                if (($current_client_time >= strtotime($random_settings["active_date"])) && ($current_client_time <= strtotime($random_settings["expire_date"]))) {
                    $is_show_random_block = true;
                } else {
                    return '';
                }
            }
            if ($random_settings["fix_time"] == 0) {
                $is_show_random_block = true;
            }
        }
        if ($is_show_random_block) {
            $design_type = '';
            $this->context->controller->addCSS($this->_path . 'views/css/front/random_element.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/front/random_element.js', 'all');
            if ((int) $random_settings['element_movement'] == 1) {
                $this->context->smarty->assign('is_image_moving', 0);
                $this->context->smarty->assign('movement_type', $random_settings['static_position']);
                $class_name = '';
                if ($random_settings['static_position'] == 1) {
                    $class_name = 'posFixedItemstopLeft';
                } elseif ($random_settings['static_position'] == 2) {
                    $class_name = 'posFixedItemstopRight';
                } elseif ($random_settings['static_position'] == 3) {
                    $class_name = 'posFixedItemsbottomLeft';
                } elseif ($random_settings['static_position'] == 4) {
                    $class_name = 'posFixedItemsbottomRight';
                } elseif ($random_settings['static_position'] == 5) {
                    $class_name = 'posFixedItemsLeftVerticalCenter';
                } elseif ($random_settings['static_position'] == 6) {
                    $class_name = 'posFixedItemsRightVerticalCenter';
                } elseif ($random_settings['static_position'] == 7) {
                    $class_name = 'posFixedItemsTopCenter';
                } elseif ($random_settings['static_position'] == 8) {
                    $class_name = 'posFixedItemsBottomCenter';
                }
                $this->context->smarty->assign('css_class_name', $class_name);
            } else {
                $this->context->smarty->assign('is_image_moving', 1);
                $this->context->smarty->assign('movement_type', $random_settings['moving_position']);
                $class_name = '';
                if ($random_settings['moving_position'] == 1) {
                    $class_name = 'posFixedItemstopLeft'.' '. 'GoRight';
                } elseif ($random_settings['moving_position'] == 2) {
                    $class_name = 'posFixedItemsTopRight'.' '. 'GoLeft';
                } elseif ($random_settings['moving_position'] == 3) {
                    $class_name = 'posFixedItemsbottomLeft'.' '. 'GoRight';
                } elseif ($random_settings['moving_position'] == 4) {
                    $class_name = 'posFixedItemsbottomRight'.' '. 'GoLeft';
                } elseif ($random_settings['moving_position'] == 5) {
                    $class_name = 'posFixedItemsLeftVerticalCenter'.' '. 'GoRight';
                } elseif ($random_settings['moving_position'] == 6) {
                    $class_name = 'posFixedItemsRightVerticalCenter'.' '. 'GoLeft';
                } elseif ($random_settings['moving_position'] == 7) {
                    $class_name = 'posFixedItemstopLeft'.' '. 'GoBottom';
                } elseif ($random_settings['moving_position'] == 8) {
                    $class_name = 'posFixedItemsTopRight'.' '. 'GoBottom';
                } elseif ($random_settings['moving_position'] == 9) {
                    $class_name = 'posFixedItemsbottomLeft'.' '. 'GoTop';
                } elseif ($random_settings['moving_position'] == 10) {
                    $class_name = 'posFixedItemsbottomRight'.' '. 'GoTop';
                } elseif ($random_settings['moving_position'] == 11) {
                    $class_name = 'posFixedItemsRight'.' '. 'GoTop';
                    $design_type = 'random';
                } elseif ($random_settings['moving_position'] == 12) {
                    $class_name = 'posFixedItemsRight'.' '. 'GoTop';
                    $design_type = 'flip';
                }
                if ($random_settings['moving_position'] == 11 || $random_settings['moving_position'] == 12) {
                    $is_special_effect_image = '1';
                    $this->context->smarty->assign('is_special_effect_image', $is_special_effect_image);
                    $this->context->smarty->assign('design_type', $design_type);
                }
                $this->context->smarty->assign('css_class_name', $class_name);
            }
            if ((int) $random_settings['element_type'] == 1) {
                $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/random/'.$random_settings['element'].'.*';
                $match1 = glob($exist_file);
                if (count($match1) > 0) {
                    $ban = explode('/', $match1[0]);
                    $ban = end($ban);
                    $ban = trim($ban);
                    if (file_exists($match1[0])) {
                        $random_element_demo = $module_dir . $this->name . '/views/img/random/' . $ban.'?time='.time();
                        $this->context->smarty->assign('random_image_path', $random_element_demo);
                        
                        return $this->display(__FILE__, 'random_element.tpl');
                    } else {
                        return '';
                    }
                }
            } else {
                $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/random.*';
                $match1 = glob($exist_file);
                if (count($match1) > 0) {
                    $ban = explode('/', $match1[0]);
                    $ban = end($ban);
                    $ban = trim($ban);
                    if (file_exists($match1[0])) {
                        $random_element_demo = $module_dir . $this->name . '/views/img/custom/' . $ban.'?time='.time();
                        $this->context->smarty->assign('random_image_path', $random_element_demo);
                        return $this->display(__FILE__, 'random_element.tpl');
                    } else {
                        return '';
                    }
                }
            }
        }
        return '';
    }
    public function getDiscountStripContent()
    {
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $module_settings = json_decode(Configuration::get('KB_SNOW_EFFECT'), true);
        $discount_settings = json_decode(Configuration::get('KB_WD_DISCOUNT_SETTING'), true);
        $is_show_bottom_block = false;
        
        if ($module_settings['enable'] == 1 && $discount_settings['enable'] == 1) {
            if ($discount_settings['where_to_display'] == 2) {
                $show_page = array();
                $show_page_fetched = array();
                $show_page = $discount_settings['show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($show_page as $key => $value) {
                    $show_page_fetched[] = $value;
                }
                if (!in_array($current_page, $show_page_fetched)) {
                    return '';
                } else {
                    $show_on_page = false;
                }
            } elseif ($discount_settings['where_to_display'] == 3) {
                $not_show_page = array();
                $not_show_page_fetched = array();
                $not_show_page = $discount_settings['not_show_page'];
                $current_page = $this->context->controller->php_self;
                foreach ($not_show_page as $key => $value) {
                    $not_show_page_fetched[] = $value;
                }
                if (in_array($current_page, $not_show_page_fetched)) {
                    return '';
                }
            }
            $current_client_time = strtotime(date('Y-m-d H:i:s'));
            if (($discount_settings["fix_time"] == 1)) {
                if (($current_client_time >= strtotime($discount_settings["active_date"])) && ($current_client_time <= strtotime($discount_settings["expire_date"]))) {
                } else {
                    return '';
                }
            }
            $cart_rule_obj = new CartRule($discount_settings["redirect_coupon_id"]);
            if ($discount_settings["fix_time"] == 0) {
                $is_show_bottom_block = true;
            } else {
                if (($current_client_time >= strtotime($cart_rule_obj->date_from)) && ($current_client_time <= strtotime($cart_rule_obj->date_to))) {
                    $is_show_bottom_block = true;
                } else {
                    return '';
                }
            }
        }
//        print_r($cart_rule_obj);
//        die;
        if ($is_show_bottom_block) {
            if ($cart_rule_obj->quantity > 0) {
                $coupon_code = $cart_rule_obj->code;
                $reduction_amount = 0;
                $is_amount_type = 0;
                $is_percentage_type = 0;
                $reduction_percentage  = 0;
                $is_free_shipping = 0;
                if ($cart_rule_obj->reduction_amount > 0) {
                    $is_amount_type = 1;
                    $reduction_amount = Tools::displayPrice($cart_rule_obj->reduction_amount);
                } else if ($cart_rule_obj->reduction_percent > 0) {
                    $is_percentage_type = 1;
                    $reduction_percentage = $cart_rule_obj->reduction_percent;
                } else if ((int) $cart_rule_obj->free_shipping == 1) {
                    $is_free_shipping = 1;
                } else {
                    return '';
                }
                $this->context->smarty->assign(
                    array(
                        'bg_color' => $discount_settings['strip_close_bg_color'],
                        'text_color' => $discount_settings['strip_text_color'],
                        'cross_bg_color' => $discount_settings['strip_close_bg_color'],
                        'coupon_code' => $coupon_code,
                        'reduction_amount' => $reduction_amount,
                        'is_amount_type' => $is_amount_type,
                        'is_percentage_type' => $is_percentage_type,
                        'reduction_percentage' => $reduction_percentage,
                        'is_free_shipping' => $is_free_shipping,
                    )
                );
                $this->context->controller->addCSS($this->_path . 'views/css/front/bottom_bar.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/front/bottom_bar.js', 'all');
                return $this->display(__FILE__, 'bottom_bar.tpl');
            } else {
                return '';
            }
        }
        return '';
    }
    public function getContent()
    {
//        print_r($_FILES);
//        print_r(Tools::getAllvalues());
//        die;
        $languages = Language::getLanguages(false);
        $output = null;
        $this->setKbMedia();
        // changes by rishbah jain
        $this->tab_display = 'GeneralSettings';
        // changes over
        $error_count = 0;
        if (!empty($_FILES)) {
            if ($_FILES['uploadedHeaderfile']['error'] == 0 && $_FILES['uploadedHeaderfile']['name'] != '' && $_FILES['uploadedHeaderfile']['size'] > 0) {
                $file_extension = pathinfo($_FILES['uploadedHeaderfile']['name'], PATHINFO_EXTENSION);
                $path = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/header.' . $file_extension;
                $exist_image = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/header.*';
                if (file_exists($exist_image)) {
                    unlink($exist_image);
                }
                move_uploaded_file(
                    $_FILES['uploadedHeaderfile']['tmp_name'],
                    $path
                );
                chmod(_PS_MODULE_DIR_ . $this->name . '/views/img/custom/header.' . $file_extension, 0777);
            }
            if ($_FILES['uploadedFooterfile']['error'] == 0 && $_FILES['uploadedFooterfile']['name'] != '' && $_FILES['uploadedFooterfile']['size'] > 0) {
                $file_extension = pathinfo($_FILES['uploadedFooterfile']['name'], PATHINFO_EXTENSION);
                $path = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/footer.' . $file_extension;
                $exist_image = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/footer.*';
                if (file_exists($exist_image)) {
                    unlink($exist_image);
                }
                move_uploaded_file(
                    $_FILES['uploadedFooterfile']['tmp_name'],
                    $path
                );
                chmod(_PS_MODULE_DIR_ . $this->name . '/views/img/custom/footer.' . $file_extension, 0777);
            }
            if ($_FILES['uploadedRandomfile']['error'] == 0 && $_FILES['uploadedRandomfile']['name'] != '' && $_FILES['uploadedRandomfile']['size'] > 0) {
                $file_extension = pathinfo($_FILES['uploadedRandomfile']['name'], PATHINFO_EXTENSION);
                $path = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/random.' . $file_extension;
                $exist_image = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/random.*';
                if (file_exists($exist_image)) {
                    unlink($exist_image);
                }
                move_uploaded_file(
                    $_FILES['uploadedRandomfile']['tmp_name'],
                    $path
                );
                chmod(_PS_MODULE_DIR_ . $this->name . '/views/img/custom/random.' . $file_extension, 0777);
            }
        }
        if (Tools::getIsset('kbeffect')) {
            $module_config = Tools::getValue('kbeffect');
            if ($module_config['fix_time'] == 1) {
                if (empty($module_config['active_date']) || empty($module_config['active_date'])) {
                    $output .= $this->displayError($this->l('Please provide active & expire date.'));
                    $error_count++;
                }
            }
            /* Updating form values in configuration table */
            if ($error_count == 0) {
                Configuration::updateValue('KB_SNOW_EFFECT', json_encode($module_config));
                $this->clearCache();
            }
        }
        $module_header_config = array();
        if (Tools::getIsset('HeaderSetting')) {
            $module_header_config = Tools::getValue('HeaderSetting');
            if ($module_header_config['fix_time'] == 1) {
                if (empty($module_header_config['active_date']) || empty($module_header_config['active_date'])) {
                    $output .= $this->displayError($this->l('Please provide active & expire date for header element.'));
                    $error_count++;
                }
            }
            /* Updating form values in configuration table */
            if ($error_count == 0) {
                Configuration::updateValue('KB_WD_HEADER_SETTING', json_encode($module_header_config));
            }
        }
        $module_footer_config = array();
        if (Tools::getIsset('FooterSetting')) {
            $module_footer_config = Tools::getValue('FooterSetting');
            if ($module_footer_config['fix_time'] == 1) {
                if (empty($module_footer_config['active_date']) || empty($module_footer_config['active_date'])) {
                    $output .= $this->displayError($this->l('Please provide active & expire date for header element.'));
                    $error_count++;
                }
            }
            /* Updating form values in configuration table */
            if ($error_count == 0) {
                Configuration::updateValue('KB_WD_FOOTER_SETTING', json_encode($module_footer_config));
            }
        }
        
        $module_random_config = array();
        if (Tools::getIsset('RandomSetting')) {
            $module_random_config = Tools::getValue('RandomSetting');
            if ($module_random_config['fix_time'] == 1) {
                if (empty($module_random_config['active_date']) || empty($module_random_config['active_date'])) {
                    $output .= $this->displayError($this->l('Please provide active & expire date for header element.'));
                    $error_count++;
                }
            }
            /* Updating form values in configuration table */
            if ($error_count == 0) {
                Configuration::updateValue('KB_WD_RANDOM_SETTING', json_encode($module_random_config));
            }
        }
        $module_discount_config = array();
        if (Tools::getIsset('DiscountSetting')) {
            $module_discount_config = Tools::getValue('DiscountSetting');
            $module_discount_config['redirect_coupon_id'] = Tools::getValue('redirect_coupon_id', 0);
            if ($module_discount_config['fix_time'] == 1) {
                if (empty($module_discount_config['active_date']) || empty($module_discount_config['active_date'])) {
                    $output .= $this->displayError($this->l('Please provide active & expire date for header element.'));
                    $error_count++;
                }
            }
            /* Updating form values in configuration table */
            if ($error_count == 0) {
                Configuration::updateValue('KB_WD_DISCOUNT_SETTING', json_encode($module_discount_config));
                $output .= $this->displayConfirmation($this->l('Configuration has been saved successfully.'));
            } else {
                $output .= $this->displayError($this->l('Something went wrong.Please try again.'));
            }
        }
        $this->context->smarty->assign('reset', '');
        $this->context->smarty->assign('firstCall', false);
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $this->context->smarty->assign('image_path_1', $module_dir . 'kbwebsitedecorationeffect/views/img/effect_1.png');
        $this->context->smarty->assign('image_path_2', $module_dir . 'kbwebsitedecorationeffect/views/img/effect_1.png');
        $this->context->smarty->assign('image_path_3', $module_dir . 'kbwebsitedecorationeffect/views/img/effect_1.png');
        $this->context->smarty->assign('image_path_4', $module_dir . 'kbwebsitedecorationeffect/views/img/effect_1.png');
        $this->context->smarty->assign('image_path_5', $module_dir . 'kbwebsitedecorationeffect/views/img/effect_1.png');
        $this->context->smarty->assign('image_path_6', $module_dir . 'kbwebsitedecorationeffect/views/img/effect_1.png');
        $this->context->smarty->assign('image_path_7', $module_dir . 'kbwebsitedecorationeffect/views/img/effect_1.png');
        return $output . $this->renderAdminConfigurationHtml();
    }

    private function renderAdminConfigurationHtml()
    {

        $output = null;
        $this->available_tabs_lang = array(
            'GeneralSettings' => $this->l('General Settings'),
            'HeaderElementSetting' => $this->l('Header Element Settings'),
            'FooterElementSetting' => $this->l('Footer Element Setting'),
            'RandomElementSetting' => $this->l('Random Element Settings'),
            'DiscountCouponSetting' => $this->l('Discount Coupon Settings'),
        );
        if (Tools::getvalue('ajaxcouponaction')) {
            echo $this->ajaxcouponlist();
            die;
        }
        $this->available_tabs = array(
            'GeneralSettings',
            'HeaderElementSetting',
            'FooterElementSetting',
            'RandomElementSetting',
            'DiscountCouponSetting',
        );
        
        $effect_options = array(
            array(
                'id_option' => 'flake',
                'name' => $this->l('Snow Effect 1')
            ),
            array(
                'id_option' => 'flurry',
                'name' => $this->l('Snow Effect 2')
            ),
            array(
                'id_option' => 'letitsnow',
                'name' => $this->l('Snow Effect 3')
            ),
            array(
                'id_option' => 'christmas',
                'name' => $this->l('Snow Effect 4')
            ),
        );
        $where_to_display = array(
            array(
                'id_wheredisplay' => '1',
                'name' => $this->l('Show on all pages'),
            ),
            array(
                'id_wheredisplay' => '2',
                'name' => $this->l('Shown on selected pages'),
            ),
            array(
                'id_wheredisplay' => '3',
                'name' => $this->l('Do not show on selected pages'),
            ),
        );
        $element_type = array(
            array(
                'id_element_type' => '1',
                'name' => $this->l('Choose almog existing element'),
            ),
            array(
                'id_element_type' => '2',
                'name' => $this->l('Upload Customized element'),
            ),
        );
        $display_freq = array(
            array(
                'id_freq' => '1',
                'name' => $this->l('Every Visit'),
            ),
            array(
                'id_freq' => '2',
                'name' => $this->l('One Visit per hour'),
            ),
            array(
                'id_freq' => '3',
                'name' => $this->l('One visit per day'),
            ),
            array(
                'id_freq' => '4',
                'name' => $this->l('One visit per week'),
            ),
            array(
                'id_freq' => '5',
                'name' => $this->l('One visit per month'),
            ),
        );
        $random_element_position_type = array(
            array(
                'id_position' => '1',
                'name' => $this->l('Static'),
            ),
            array(
                'id_position' => '2',
                'name' => $this->l('Moving'),
            ),
        );
        $random_element_static_positions = array(
            array(
                'id_position' => '1',
                'name' => $this->l('Top Left'),
            ),
            array(
                'id_position' => '2',
                'name' => $this->l('Top Right'),
            ),
            array(
                'id_position' => '3',
                'name' => $this->l('Bottom Left'),
            ),
            array(
                'id_position' => '4',
                'name' => $this->l('Bottom Right'),
            ),
            array(
                'id_position' => '5',
                'name' => $this->l('Left Centered'),
            ),
            array(
                'id_position' => '6',
                'name' => $this->l('Right Centered'),
            ),
            array(
                'id_position' => '7',
                'name' => $this->l('Top Centered'),
            ),
            array(
                'id_position' => '8',
                'name' => $this->l('Bottom Centered'),
            ),
        );
        $random_element_moving_positions = array(
            array(
                'id_position' => '1',
                'name' => $this->l('Top Left to right'),
            ),
            array(
                'id_position' => '2',
                'name' => $this->l('Top Right to Left'),
            ),
            array(
                'id_position' => '3',
                'name' => $this->l('Bottom Left to right'),
            ),
            array(
                'id_position' => '4',
                'name' => $this->l('Bottom Right To Left'),
            ),
            array(
                'id_position' => '5',
                'name' => $this->l('Center Left to right'),
            ),
            array(
                'id_position' => '6',
                'name' => $this->l('Center Right to Left'),
            ),
            array(
                'id_position' => '7',
                'name' => $this->l('Left Top to bottom'),
            ),
            array(
                'id_position' => '8',
                'name' => $this->l('Right Top to bottom'),
            ),
            array(
                'id_position' => '9',
                'name' => $this->l('Left Bottom to Top'),
            ),
            array(
                'id_position' => '10',
                'name' => $this->l('Right Bottom to Top'),
            ),
//            array(
//                'id_position' => '11',
//                'name' => $this->l('Random'),
//            ),
//            array(
//                'id_position' => '12',
//                'name' => $this->l('Diagonally right to left'),
//            ),
        );
        
        $extra_effects = array(
            array(
                'id_effect' => '0',
                'name' => $this->l('Select Any Option'),
            ),
            array(
                'id_effect' => '1',
                'name' => $this->l('New year effect 1'),
            ),
            array(
                'id_effect' => '2',
                'name' => $this->l('New year effect 2'),
            ),
            array(
                'id_effect' => '3',
                'name' => $this->l('New year effect 3'),
            ),
            array(
                'id_effect' => '4',
                'name' => $this->l('New year effect 4'),
            ),
            array(
                'id_effect' => '5',
                'name' => $this->l('New year effect 5'),
            ),
            array(
                'id_effect' => '6',
                'name' => $this->l('New year effect 6'),
            ),
            array(
                'id_effect' => '7',
                'name' => $this->l('New year effect 7'),
            ),
        );
        $front_pages = array();
        $controllers_name = $this->getControllers();
        $controllers_original = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
        $controllers = array_unique(array_merge($controllers_original, $controllers_name));
        if (empty($controllers)) {
            $front_pages[] = array(
                'id_page_type' => ' ',
                'name' => '',
            );
        } else {
            foreach ($controllers as $key => $value) {
                $front_pages[] = array(
                    'id_page_type' => $key,
                    'name' => $value,
                );
            }
        }
        $this->form_fields = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Module Configuration Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Enable/Disable the module'),
                        'type' => 'switch',
                        'name' => 'kbeffect[enable]',
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'kbeffect[enable]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'kbeffect[enable]_off',
                            ),
                        ),
                        'hint' => $this->l('Enable or Disable the plugin functionality'),
                    ),
                    // changes by rishabh jain
//                    array(
//                        'label' => $this->l('Enable/Disable the extra effect'),
//                        'type' => 'switch',
//                        'name' => 'kbeffect[enable_extra_effect]',
//                        'values' => array(
//                            array(
//                                'value' => 1,
//                            ),
//                            array(
//                                'value' => 0,
//                            ),
//                        ),
//                        'hint' => $this->l('Enable or Disable the extra Effect'),
//                    ),
//                    array(
//                        'label' => $this->l('Select Extra Effect Type'),
//                        'type' => 'select',
//                        'name' => 'kbeffect[extra_effect_type]',
//                        'hint' => $this->l('Select extra effect type which you want to apply on frontend'),
//                        'is_bool' => true,
//                        'onchange' => 'showHideExtraEffectSetting(this)',
//                        'options' => array(
//                            'query' => $extra_effects,
//                            'id' => 'id_effect',
//                            'name' => 'name',
//                        ),
//                    ),
//                    array(
//                            'type' => 'file',
//                            'label' => $this->l('Image for logo:'),
//                            'class' => '',
//                            'name' => 'kbeffect_preview',
//                            'id' => 'kbeffect_preview',
//                            'display_image' => true,
//                            'required' => false,
//                            'image' => $this->display(__FILE__, 'views/templates/admin/effect_preview.tpl'),
//                        ),
                    array(
                        'label' => $this->l('Enable/Disable the Snow effect'),
                        'type' => 'switch',
                        'name' => 'kbeffect[enable_snoweffect]',
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'kbeffect[enable_snoweffect]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'kbeffect[enable_snoweffect]_off',
                            ),
                        ),
                        'hint' => $this->l('Enable or Disable the Snow Effect'),
                    ),
                    array(
                        'label' => $this->l('Display on Mobile device'),
                        'type' => 'switch',
                        'name' => 'kbeffect[mobile]',
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'kbeffect[mobile]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'kbeffect[mobile]_off',
                            ),
                        ),
                        'hint' => $this->l('Enable or Disable the plugin functionality on mobile device'),
                    ),
                    array(
                        'label' => $this->l('Maximum Display Frequency'),
                        'type' => 'select',
                        'name' => 'kbeffect[max_display_freq]',
                        'hint' => $this->l('Select maximum display frequency.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $display_freq,
                            'id' => 'id_freq',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Where to Display'),
                        'type' => 'select',
                        'name' => 'kbeffect[where_to_display]',
                        'hint' => $this->l('Select where to display snow'),
                        'onchange' => 'showHideDisplayPageSetting(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $where_to_display,
                            'id' => 'id_wheredisplay',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'kbeffect[show_page][]',
                        'hint' => $this->l('Select the Pages to display snow.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'kbeffect[not_show_page][]',
                        'hint' => $this->l('Select the Pages  not to display snow'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('Set Fix Time'),
                        'type' => 'switch',
                        'hint' => $this->l('Allow effects to display for defined time.'),
                        'class' => 'optn_allow_date',
                        'name' => 'kbeffect[fix_time]',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'kbeffect[fix_time]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'kbeffect[fix_time]_off',
                            ),
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Active Date/Time'),
                        'name' => 'kbeffect[active_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Active time of display effects'),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Expire Date/Time'),
                        'name' => 'kbeffect[expire_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Expire time of display effects'),
                    ),
                    array(
                        'label' => $this->l('Select Snow Effect Type'),
                        'type' => 'select',
                        'name' => 'kbeffect[effect_type]',
                        'hint' => $this->l('Select snow effect type which you want to apply on frontend'),
                        'desc' => $this->l('Snow Effect 1 will not work with mobile device'),
                        'is_bool' => true,
                        'onchange' => 'showHideEffectSetting(this)',
                        'options' => array(
                            'query' => $effect_options,
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Snow Color'),
                        'name' => 'kbeffect[flake_color]',
                        'maxlength' => 10,
                        'required' => true,
                        'hint' => $this->l('Snow Flake Color'),
                    ),
                    // <editor-fold defaultstate="collapsed" desc="Snow Flake Fields">
                    array(
                        'type' => 'text',
                        'label' => $this->l('Max Snow Flake Size (px)'),
                        'class' => 'show_effect_option_flake',
                        'name' => 'kbeffect[flake_size]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Max Snow Flake Size (px) i.e. 10')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of Flakes'),
                        'class' => 'show_effect_option_flake',
                        'name' => 'kbeffect[flake_numbers]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Recommended Maximum of 25')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flake Minimum Speed Seconds'),
                        'class' => 'show_effect_option_flake',
                        'name' => 'kbeffect[flake_min_speed]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Top to Bottom Speed: Must be less than Snow Flake Maximum Speed')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flake Maximum Speed Seconds'),
                        'class' => 'show_effect_option_flake',
                        'name' => 'kbeffect[flake_max_speed]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Top to Bottom Speed: Must be less than Snow Flake Minimum Speed')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Disable Snow Flakes After (Seconds)'),
                        'class' => 'show_effect_option_flake',
                        'name' => 'kbeffect[flake_disable_time]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => false,
                        'hint' => $this->l('Disable Snow Flakes After (Seconds)'),
                    ),
                    // </editor-fold>
                    // <editor-fold defaultstate="collapsed" desc="Snow Flurry Fields">
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Character'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_character]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Determines the character to be replicated as a snowflake. Default is "❄". If you set this to a string of several characters Flurry will randomize which flakes use each character (e.g. "❄❅❆")')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Height'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_height]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Controls how far down the page the flakes will fall in pixels.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Frequency'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_frequency]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Controls how frequently new flakes are generated in milliseconds; lower creates more flakes at a time')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Speed'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_speed]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Controls how long it takes each flake to fall in milliseconds; lower is faster')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum Snow Flurry Flake Size'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_min_size]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Determines the font size of the smallest flakes in pixels')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum Snow Flurry Flake Size'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_max_size]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Determines the font size of the largest flakes in pixels')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Flake Wind Drift'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_wind_drift]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Controls how far to the left each flake will drift in pixels.Use a negative number to make flakes drift to the right.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Flake Wind Variance'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_wind_variance]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Controls how much each flake will drift in pixels using the wind drift value as a base; lower creates less random drift.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Flake Rotation'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_rotation]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Controls how much each flake will rotate in degrees while it falls; lower is less rotation.')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Snow Flurry Flake Rotation Variance'),
                        'class' => 'show_effect_option_flurry',
                        'name' => 'kbeffect[flurry_rotation_variance]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Controls how much each flake rotation will be randomized by in degrees; lower creates less random rotation.')
                    ),
                    // </editor-fold>
                    // <editor-fold defaultstate="collapsed" desc="LetItSnow Fields">
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum Snow Flake Size'),
                        'class' => 'show_effect_option_letitsnow',
                        'name' => 'kbeffect[letitsnow_min_size]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Minimum Snow Flake Size i.e. 5')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum Snow Flake Size'),
                        'class' => 'show_effect_option_letitsnow',
                        'name' => 'kbeffect[letitsnow_max_size]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Maximum Snow Flake Size i.e. 10')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum Count Of Snow Flake'),
                        'class' => 'show_effect_option_letitsnow',
                        'name' => 'kbeffect[letitsnow_max_count]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Maximum Count Of Snow Flake i.e. 100')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Wind Speed'),
                        'class' => 'show_effect_option_letitsnow',
                        'name' => 'kbeffect[letitsnow_speed]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Wind Speed for Snow Flake i.e. 250')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Fall time'),
                        'class' => 'show_effect_option_letitsnow',
                        'name' => 'kbeffect[letitsnow_falltime]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Fall time for Snow Flake i.e. 10000')
                    ),
//                    array(
//                        'label' => $this->l('Move Snow in Cursor Direction'),
//                        'type' => 'switch',
//                        'hint' => $this->l('If enabled then snow direction will change as per the cursor movement.'),
//                        'class' => 'optn_allow_date',
//                        'name' => 'kbeffect[snow_cursor_direction]',
//                        'is_bool' => true,
//                        'values' => array(
//                            array(
//                                'value' => 1,
//                            ),
//                            array(
//                                'value' => 0,
//                            ),
//                        ),
//                    ),
                    // </editor-fold>
                    // <editor-fold defaultstate="collapsed" desc="Christmas Fields">
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum Snow Flake Size'),
                        'class' => 'show_effect_option_christmas',
                        'name' => 'kbeffect[christmas_min_size]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Minimum Snow Flake Size i.e. 20')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Maximum Snow Flake Size'),
                        'class' => 'show_effect_option_christmas',
                        'name' => 'kbeffect[christmas_max_size]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Maximum Snow Flake Size i.e. 50')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Flake Fall Time Multiplier'),
                        'class' => 'show_effect_option_christmas',
                        'name' => 'kbeffect[christmas_fallTimeMultiplier]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Flake Fall Time Multiplier i.e. 20')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Flake fall time difference'),
                        'class' => 'show_effect_option_christmas',
                        'name' => 'kbeffect[christmas_fallTimeDifference]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('Flake fall time difference i.e. 10000')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Interval between new element spawns'),
                        'class' => 'show_effect_option_christmas',
                        'name' => 'kbeffect[christmas_spawnInterval]',
                        'maxlength' => 10,
                        'col' => 2,
                        'required' => true,
                        'hint' => $this->l('interval (miliseconds) between new element spawns i.e 500')
                    ),
                // </editor-fold>
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right velovalidation_kbeffect'
                ),
            ),
        );
        
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $module_settings = json_decode(Configuration::get('KB_SNOW_EFFECT'), true);
        $field_value = array(
            // changes by rishabh jain
            'kbeffect[enable_snoweffect]' => isset($module_settings['enable_snoweffect']) ? $module_settings['enable_snoweffect'] : 0,
//            'kbeffect[extra_effect_type]' => isset($module_settings['extra_effect_type']) ? $module_settings['extra_effect_type'] : 1,
//            'kbeffect[enable_extra_effect]' => isset($module_settings['enable_extra_effect']) ? $module_settings['enable_extra_effect'] : 0,
            // changes over
            'kbeffect[enable]' => isset($module_settings['enable']) ? $module_settings['enable'] : 0,
            'kbeffect[mobile]' => isset($module_settings['mobile']) ? $module_settings['mobile'] : 0,
            'kbeffect[fix_time]' => isset($module_settings['fix_time']) ? $module_settings['fix_time'] : 0,
//            'kbeffect[snow_cursor_direction]' => isset($module_settings['snow_cursor_direction']) ? $module_settings['snow_cursor_direction'] : 0,
            'kbeffect[active_date]' => isset($module_settings['active_date']) ? $module_settings['active_date'] : '',
            'kbeffect[expire_date]' => isset($module_settings['expire_date']) ? $module_settings['expire_date'] : '',
            'kbeffect[max_display_freq]' => isset($module_settings['max_display_freq']) ? $module_settings['max_display_freq'] : 1,
            'kbeffect[where_to_display]' => isset($module_settings['where_to_display']) ? $module_settings['where_to_display'] : 1,
            'kbeffect[effect_type]' => isset($module_settings['effect_type']) ? $module_settings['effect_type'] : '',
            'kbeffect[flake_color]' => isset($module_settings['flake_color']) ? $module_settings['flake_color'] : '#ef3500',
            'kbeffect[flake_size]' => isset($module_settings['flake_size']) ? $module_settings['flake_size'] : 15,
            'kbeffect[flake_numbers]' => isset($module_settings['flake_numbers']) ? $module_settings['flake_numbers'] : 50,
            'kbeffect[flake_min_speed]' => isset($module_settings['flake_min_speed']) ? $module_settings['flake_min_speed'] : 15,
            'kbeffect[flake_max_speed]' => isset($module_settings['flake_max_speed']) ? $module_settings['flake_max_speed'] : 30,
            'kbeffect[flake_disable_time]' => isset($module_settings['flake_disable_time']) ? $module_settings['flake_disable_time'] : 0,
            'kbeffect[flurry_character]' => isset($module_settings['flurry_character']) ? $module_settings['flurry_character'] : '❄❅❆',
            'kbeffect[flurry_height]' => isset($module_settings['flurry_height']) ? $module_settings['flurry_height'] : 500,
            'kbeffect[flurry_frequency]' => isset($module_settings['flurry_frequency']) ? $module_settings['flurry_frequency'] : 100,
            'kbeffect[flurry_speed]' => isset($module_settings['flurry_speed']) ? $module_settings['flurry_speed'] : 3000,
            'kbeffect[flurry_min_size]' => isset($module_settings['flurry_min_size']) ? $module_settings['flurry_min_size'] : 8,
            'kbeffect[flurry_max_size]' => isset($module_settings['flurry_max_size']) ? $module_settings['flurry_max_size'] : 28,
            'kbeffect[flurry_wind_drift]' => isset($module_settings['flurry_wind_drift']) ? $module_settings['flurry_wind_drift'] : 40,
            'kbeffect[flurry_wind_variance]' => isset($module_settings['flurry_wind_variance']) ? $module_settings['flurry_wind_variance'] : 20,
            'kbeffect[flurry_rotation]' => isset($module_settings['flurry_rotation']) ? $module_settings['flurry_rotation'] : 90,
            'kbeffect[flurry_rotation_variance]' => isset($module_settings['flurry_rotation_variance']) ? $module_settings['flurry_rotation_variance'] : 180,
            'kbeffect[letitsnow_min_size]' => isset($module_settings['letitsnow_min_size']) ? $module_settings['letitsnow_min_size'] : 1,
            'kbeffect[letitsnow_max_size]' => isset($module_settings['letitsnow_max_size']) ? $module_settings['letitsnow_max_size'] : 5,
            'kbeffect[letitsnow_max_count]' => isset($module_settings['letitsnow_max_count']) ? $module_settings['letitsnow_max_count'] : 100,
            'kbeffect[letitsnow_speed]' => isset($module_settings['letitsnow_speed']) ? $module_settings['letitsnow_speed'] : 250,
            'kbeffect[letitsnow_falltime]' => isset($module_settings['letitsnow_falltime']) ? $module_settings['letitsnow_falltime'] : 10000,
            'kbeffect[christmas_min_size]' => isset($module_settings['christmas_min_size']) ? $module_settings['christmas_min_size'] : 20,
            'kbeffect[christmas_max_size]' => isset($module_settings['christmas_max_size']) ? $module_settings['christmas_max_size'] : 50,
            'kbeffect[christmas_fallTimeMultiplier]' => isset($module_settings['christmas_fallTimeMultiplier']) ? $module_settings['christmas_fallTimeMultiplier'] : 20,
            'kbeffect[christmas_fallTimeDifference]' => isset($module_settings['christmas_fallTimeDifference']) ? $module_settings['christmas_fallTimeDifference'] : 10000,
            'kbeffect[christmas_spawnInterval]' => isset($module_settings['christmas_spawnInterval']) ? $module_settings['christmas_spawnInterval'] : 500,
        );
        if (!isset($module_settings['show_page'])) {
            $field_value['kbeffect[show_page][]'] = array();
        } else {
            $field_value['kbeffect[show_page][]'] = $module_settings['show_page'];
        }
        if (!isset($module_settings['not_show_page'])) {
            $field_value['kbeffect[not_show_page][]'] = array();
        } else {
            $field_value['kbeffect[not_show_page][]'] = $module_settings['not_show_page'];
        }
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->smarty->assign('show_toolbar', false);
        }
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $type_element =  'header';
        
        
//        $header_element_demo = $module_dir . 'kbwebsitedecorationeffect/views/img/bottom.png';
        $header_element_demo = $this->getImgDirUrl() . _THEME_PROD_DIR_ . Language::getIsoById((int) $this->context->language->id) . '.jpg';
        $is_default_header_image = 1;
        $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/header.*';
        $match1 = glob($exist_file);
        if (count($match1) > 0) {
            $ban = explode('/', $match1[0]);
            $ban = end($ban);
            $ban = trim($ban);
            if (file_exists($match1[0])) {
                $header_element_demo = $module_dir . $this->name . '/views/img/custom/' . $ban.'?time='.time();
                $is_default_header_image = 0;
            }
        }
        $this->context->smarty->assign('is_default_header_image', $is_default_header_image);
        $header_elements = array(
            array(
                'id_element' => '1',
                'name' => $this->l('Header Element 1'),
            ),
            array(
                'id_element' => '2',
                'name' => $this->l('Header Element 2'),
            ),
            array(
                'id_element' => '3',
                'name' => $this->l('Header Element 3'),
            ),
            array(
                'id_element' => '4',
                'name' => $this->l('Header Element 4'),
            ),
        );
        $footer_elements = array(
            array(
                'id_element' => '1',
                'name' => $this->l('Footer Element 1'),
            ),
            array(
                'id_element' => '2',
                'name' => $this->l('Footer Element 2'),
            ),
            array(
                'id_element' => '3',
                'name' => $this->l('Footer Element 3'),
            ),
            array(
                'id_element' => '4',
                'name' => $this->l('Footer Element 4'),
            ),
            array(
                'id_element' => '5',
                'name' => $this->l('Footer Element 5'),
            ),
            array(
                'id_element' => '6',
                'name' => $this->l('Footer Element 6'),
            ),
            array(
                'id_element' => '7',
                'name' => $this->l('Footer Element 7'),
            ),
            array(
                'id_element' => '8',
                'name' => $this->l('Footer Element 8'),
            ),
        );
        $random_elements = array(
            array(
                'id_element' => '1',
                'name' => $this->l('Random Element 1'),
            ),
            array(
                'id_element' => '2',
                'name' => $this->l('Random Element 2'),
            ),
            array(
                'id_element' => '3',
                'name' => $this->l('Random Element 3'),
            ),
            array(
                'id_element' => '4',
                'name' => $this->l('Random Element 4'),
            ),
            array(
                'id_element' => '5',
                'name' => $this->l('Random Element 5'),
            ),
            array(
                'id_element' => '6',
                'name' => $this->l('Random Element 6'),
            ),
            array(
                'id_element' => '7',
                'name' => $this->l('Random Element 7'),
            ),
            array(
                'id_element' => '8',
                'name' => $this->l('Random Element 8'),
            ),
            array(
                'id_element' => '9',
                'name' => $this->l('Random Element 9'),
            ),
            array(
                'id_element' => '10',
                'name' => $this->l('Random Element 10'),
            ),
            array(
                'id_element' => '11',
                'name' => $this->l('Random Element 11'),
            ),
            array(
                'id_element' => '12',
                'name' => $this->l('Random Element 12'),
            ),
            array(
                'id_element' => '13',
                'name' => $this->l('Random Element 13'),
            ),
        );
        $this->context->smarty->assign('element_type', $type_element);
        $this->fields_form1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Header Element Setting'),
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Enable/Disable the Header Element'),
                        'type' => 'switch',
                        'name' => 'HeaderSetting[enable]',
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'HeaderSetting[enable]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'HeaderSetting[enable]_off',
                            ),
                        ),
                        'hint' => $this->l('Enable or Disable the header Element'),
                    ),
                    array(
                        'label' => $this->l('Where to Display'),
                        'type' => 'select',
                        'name' => 'HeaderSetting[where_to_display]',
                        'hint' => $this->l('Select where to display snow'),
                        'onchange' => 'showHideDisplayPageHeaderSetting(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $where_to_display,
                            'id' => 'id_wheredisplay',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'HeaderSetting[show_page][]',
                        'hint' => $this->l('Select the Pages to display snow.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'HeaderSetting[not_show_page][]',
                        'hint' => $this->l('Select the Pages  not to display snow'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('Set Fix Time'),
                        'type' => 'switch',
                        'hint' => $this->l('Allow effects to display for defined time.'),
                        'class' => 'optn_allow_date',
                        'name' => 'HeaderSetting[fix_time]',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'HeaderSetting[fix_time]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'HeaderSetting[fix_time]_off',
                            ),
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Active Date/Time'),
                        'name' => 'HeaderSetting[active_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Active time of display effects'),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Expire Date/Time'),
                        'name' => 'HeaderSetting[expire_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Expire time of display effects'),
                    ),
                    array(
                        'label' => $this->l('Select element type'),
                        'type' => 'select',
                        'name' => 'HeaderSetting[element_type]',
                        'hint' => $this->l('Select whether you want to use an existing element or wanna upload a customized one'),
                        'onchange' => 'showHideHeaderElementType(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $element_type,
                            'id' => 'id_element_type',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Select Element'),
                        'type' => 'select',
                        'name' => 'HeaderSetting[element]',
                        'hint' => $this->l('Select extra effect type which you want to apply on frontend'),
                        'is_bool' => true,
                        'onchange' => 'showHideHeaderElementSetting(this)',
                        'options' => array(
                            'query' => $header_elements,
                            'id' => 'id_element',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Element Preview'),
                        'type' => 'html',
                        'name' => 'kbwd_preview_header_element',
                        'id' => 'kbwd_preview_header_element',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name. '/views/templates/admin/showThemePreview.tpl'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Header Element:'),
                        'class' => '',
                        'name' => 'uploadedHeaderfile',
                        'id' => 'uploadedHeaderfile',
                        'display_image' => true,
                        'image' => "<img id='notificatonHeaderimage' src='".$header_element_demo."' width='100%;' height='100px;'>",
                        'required' => false,
//                        'desc' => $this->l('Upload your Image')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right kb_header_setting_btn'
                ),
            )
        );
        // header element setting
        $HeaderElementFieldValues = array();
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $header_settings = json_decode(Configuration::get('KB_WD_HEADER_SETTING'), true);
        $HeaderElementFieldValues = array(
            // changes by rishabh jain
            'HeaderSetting[enable]' => isset($header_settings['enable']) ? $header_settings['enable'] : 0,
            'HeaderSetting[element]' => isset($header_settings['element']) ? $header_settings['element'] : 0,
            'HeaderSetting[where_to_display]' => isset($header_settings['where_to_display']) ? $header_settings['where_to_display'] : 0,
            'HeaderSetting[element_type]' => isset($header_settings['element_type']) ? $header_settings['element_type'] : 0,
            'HeaderSetting[fix_time]' => isset($header_settings['fix_time']) ? $header_settings['fix_time'] : 0,
            'HeaderSetting[active_date]' => isset($header_settings['active_date']) ? $header_settings['active_date'] : '',
            'HeaderSetting[expire_date]' => isset($header_settings['expire_date']) ? $header_settings['expire_date'] : '',
            'HeaderSetting[where_to_display]' => isset($header_settings['where_to_display']) ? $header_settings['where_to_display'] : 1,
            // changes over
        );
        if (!isset($header_settings['show_page'])) {
            $HeaderElementFieldValues['HeaderSetting[show_page][]'] = array();
        } else {
            $HeaderElementFieldValues['HeaderSetting[show_page][]'] = $header_settings['show_page'];
        }
        if (!isset($header_settings['not_show_page'])) {
            $HeaderElementFieldValues['HeaderSetting[not_show_page][]'] = array();
        } else {
            $HeaderElementFieldValues['HeaderSetting[not_show_page][]'] = $header_settings['not_show_page'];
        }
        
        // footer element
        $type_element =  'footer';
        
//        $footer_element_demo = $module_dir . 'kbwebsitedecorationeffect/views/img/bottom.png';
        $footer_element_demo = $this->getImgDirUrl() . _THEME_PROD_DIR_ . Language::getIsoById((int) $this->context->language->id) . '.jpg';
        $this->context->smarty->assign('element_type', $type_element);
        $is_default_footer_image = 1;
        $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/footer.*';
        $match1 = glob($exist_file);
        if (count($match1) > 0) {
            $ban = explode('/', $match1[0]);
            $ban = end($ban);
            $ban = trim($ban);
            if (file_exists($match1[0])) {
                $footer_element_demo = $module_dir . $this->name . '/views/img/custom/' . $ban.'?time='.time();
                $is_default_footer_image = 0;
            }
        }
        $this->context->smarty->assign('is_default_footer_image', $is_default_footer_image);
        $this->fields_form2 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Footer Element Setting'),
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Enable/Disable the Header Element'),
                        'type' => 'switch',
                        'name' => 'FooterSetting[enable]',
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'FooterSetting[enable]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'FooterSetting[enable]_off',
                            ),
                        ),
                        'hint' => $this->l('Enable or Disable the header Element'),
                    ),
                    array(
                        'label' => $this->l('Where to Display'),
                        'type' => 'select',
                        'name' => 'FooterSetting[where_to_display]',
                        'hint' => $this->l('Select where to display snow'),
                        'onchange' => 'showHideDisplayPageFooterSetting(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $where_to_display,
                            'id' => 'id_wheredisplay',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'FooterSetting[show_page][]',
                        'hint' => $this->l('Select the Pages to display snow.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'FooterSetting[not_show_page][]',
                        'hint' => $this->l('Select the Pages  not to display snow'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('Set Fix Time'),
                        'type' => 'switch',
                        'hint' => $this->l('Allow effects to display for defined time.'),
                        'class' => 'optn_allow_date',
                        'name' => 'FooterSetting[fix_time]',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'FooterSetting[fix_time]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'FooterSetting[fix_time]_off',
                            ),
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Active Date/Time'),
                        'name' => 'FooterSetting[active_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Active time of display effects'),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Expire Date/Time'),
                        'name' => 'FooterSetting[expire_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Expire time of display effects'),
                    ),
                    array(
                        'label' => $this->l('Select element type'),
                        'type' => 'select',
                        'name' => 'FooterSetting[element_type]',
                        'hint' => $this->l('Select whether you want to use an existing element or wanna upload a customized one'),
                        'onchange' => 'showHideFooterElementType(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $element_type,
                            'id' => 'id_element_type',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Select Element'),
                        'type' => 'select',
                        'name' => 'FooterSetting[element]',
                        'hint' => $this->l('Select extra effect type which you want to apply on frontend'),
                        'is_bool' => true,
                        'onchange' => 'showHideFooterElementSetting(this)',
                        'options' => array(
                            'query' => $footer_elements,
                            'id' => 'id_element',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Element Preview'),
                        'type' => 'html',
                        'name' => 'kbwd_preview_footer_element',
                        'id' => 'kbwd_preview_footer_element',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name. '/views/templates/admin/showThemePreview.tpl'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Header Element:'),
                        'class' => '',
                        'name' => 'uploadedFooterfile',
                        'id' => 'uploadedFooterfile',
                        'display_image' => true,
                        'image' => "<img id='notificatonFooterimage' src='".$footer_element_demo."' width='100%;' height='100px;'>",
                        'required' => false,
//                        'desc' => $this->l('Upload your Image')
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right kb_footer_setting_btn'
                ),
            )
        );
        // header element setting
        $FooterElementFieldValues = array();
//        $HeaderElementFieldValues = json_decode(Configuration::get('KB_WD_HEADER_SETTING'), true);
       
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $footer_settings = json_decode(Configuration::get('KB_WD_FOOTER_SETTING'), true);
        $FooterElementFieldValues = array(
            // changes by rishabh jain
            'FooterSetting[enable]' => isset($footer_settings['enable']) ? $footer_settings['enable'] : 0,
            'FooterSetting[element]' => isset($footer_settings['element']) ? $footer_settings['element'] : 0,
            'FooterSetting[where_to_display]' => isset($footer_settings['where_to_display']) ? $footer_settings['where_to_display'] : 0,
            'FooterSetting[element_type]' => isset($footer_settings['element_type']) ? $footer_settings['element_type'] : 0,
            'FooterSetting[fix_time]' => isset($footer_settings['fix_time']) ? $footer_settings['fix_time'] : 0,
            'FooterSetting[active_date]' => isset($footer_settings['active_date']) ? $footer_settings['active_date'] : '',
            'FooterSetting[expire_date]' => isset($footer_settings['expire_date']) ? $footer_settings['expire_date'] : '',
            'FooterSetting[where_to_display]' => isset($footer_settings['where_to_display']) ? $footer_settings['where_to_display'] : 1,
            // changes over
        );
        if (!isset($footer_settings['show_page'])) {
            $FooterElementFieldValues['FooterSetting[show_page][]'] = array();
        } else {
            $FooterElementFieldValues['FooterSetting[show_page][]'] = $footer_settings['show_page'];
        }
        if (!isset($footer_settings['not_show_page'])) {
            $FooterElementFieldValues['FooterSetting[not_show_page][]'] = array();
        } else {
            $FooterElementFieldValues['FooterSetting[not_show_page][]'] = $footer_settings['not_show_page'];
        }
        
        // random element
        
        
        $type_element =  'random';
        
        $random_element_demo = $module_dir . 'kbwebsitedecorationeffect/views/img/bottom.png';
        $random_element_demo = $this->getImgDirUrl() . _THEME_PROD_DIR_ . Language::getIsoById((int) $this->context->language->id) . '.jpg';
        $this->context->smarty->assign('element_type', $type_element);
        $is_default_random_image = 1;
        $exist_file = _PS_MODULE_DIR_ . $this->name . '/views/img/custom/random.*';
        $match1 = glob($exist_file);
        if (count($match1) > 0) {
            $ban = explode('/', $match1[0]);
            $ban = end($ban);
            $ban = trim($ban);
            if (file_exists($match1[0])) {
                $random_element_demo = $module_dir . $this->name . '/views/img/custom/' . $ban.'?time='.time();
                $is_default_random_image = 0;
            }
        }
        $this->context->smarty->assign('is_default_random_image', $is_default_random_image);
        $this->fields_form3 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Random Element Setting'),
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Enable/Disable the Header Element'),
                        'type' => 'switch',
                        'name' => 'RandomSetting[enable]',
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'RandomSetting[enable]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'RandomSetting[enable]_off',
                            ),
                        ),
                        'hint' => $this->l('Enable or Disable the header Element'),
                    ),
                    array(
                        'label' => $this->l('Where to Display'),
                        'type' => 'select',
                        'name' => 'RandomSetting[where_to_display]',
                        'hint' => $this->l('Select where to display snow'),
                        'onchange' => 'showHideDisplayPageRandomSetting(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $where_to_display,
                            'id' => 'id_wheredisplay',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'RandomSetting[show_page][]',
                        'hint' => $this->l('Select the Pages to display snow.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'RandomSetting[not_show_page][]',
                        'hint' => $this->l('Select the Pages  not to display snow'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('Set Fix Time'),
                        'type' => 'switch',
                        'hint' => $this->l('Allow effects to display for defined time.'),
                        'class' => 'optn_allow_date',
                        'name' => 'RandomSetting[fix_time]',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'RandomSetting[fix_time]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'RandomSetting[fix_time]_off',
                            ),
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Active Date/Time'),
                        'name' => 'RandomSetting[active_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Active time of display effects'),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Expire Date/Time'),
                        'name' => 'RandomSetting[expire_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Expire time of display effects'),
                    ),
                    array(
                        'label' => $this->l('Select element type'),
                        'type' => 'select',
                        'name' => 'RandomSetting[element_type]',
                        'hint' => $this->l('Select whether you want to use an existing element or wanna upload a customized one'),
                        'onchange' => 'showHideRandomElementType(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $element_type,
                            'id' => 'id_element_type',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Select Element'),
                        'type' => 'select',
                        'name' => 'RandomSetting[element]',
                        'hint' => $this->l('Select extra effect type which you want to apply on frontend'),
                        'is_bool' => true,
                        'onchange' => 'showHideRandomElementSetting(this)',
                        'options' => array(
                            'query' => $random_elements,
                            'id' => 'id_element',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Element Preview'),
                        'type' => 'html',
                        'name' => 'kbwd_preview_random_element',
                        'id' => 'kbwd_preview_random_element',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name. '/views/templates/admin/showThemePreview.tpl'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Random Element:'),
                        'class' => '',
                        'name' => 'uploadedRandomfile',
                        'id' => 'uploadedRandomfile',
                        'display_image' => true,
                        'image' => "<img id='notificatonRandomimage' src='".$random_element_demo."' width='100%;' height='100px;'>",
                        'required' => false,
//                        'desc' => $this->l('Upload your Image')
                    ),
                    array(
                        'label' => $this->l('Select Element Movement Type'),
                        'type' => 'select',
                        'name' => 'RandomSetting[element_movement]',
                        'hint' => $this->l('Select whether the element would be static or moving'),
                        'is_bool' => true,
                        'onchange' => 'showHideRandomElementPositions(this)',
                        'options' => array(
                            'query' => $random_element_position_type,
                            'id' => 'id_position',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Select Static Random Element Position'),
                        'type' => 'select',
                        'name' => 'RandomSetting[static_position]',
                        'hint' => $this->l('Select the position of moving random element'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $random_element_static_positions,
                            'id' => 'id_position',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Select Moving Random Element Direction'),
                        'type' => 'select',
                        'name' => 'RandomSetting[moving_position]',
                        'hint' => $this->l('Select the direction of movement of the random element'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $random_element_moving_positions,
                            'id' => 'id_position',
                            'name' => 'name',
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right kb_random_setting_btn'
                ),
            )
        );
        // header element setting
        $RandomElementFieldValues = array();
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $random_settings = json_decode(Configuration::get('KB_WD_RANDOM_SETTING'), true);
//        print_r($random_settings);
//        die;
        $RandomElementFieldValues = array(
            // changes by rishabh jain
            'RandomSetting[enable]' => isset($random_settings['enable']) ? $random_settings['enable'] : 0,
            'RandomSetting[element]' => isset($random_settings['element']) ? $random_settings['element'] : 0,
            'RandomSetting[where_to_display]' => isset($random_settings['where_to_display']) ? $random_settings['where_to_display'] : 0,
            'RandomSetting[element_type]' => isset($random_settings['element_type']) ? $random_settings['element_type'] : 0,
            'RandomSetting[fix_time]' => isset($random_settings['fix_time']) ? $random_settings['fix_time'] : 0,
            'RandomSetting[active_date]' => isset($random_settings['active_date']) ? $random_settings['active_date'] : '',
            'RandomSetting[expire_date]' => isset($random_settings['expire_date']) ? $random_settings['expire_date'] : '',
            'RandomSetting[where_to_display]' => isset($random_settings['where_to_display']) ? $random_settings['where_to_display'] : 1,
            'RandomSetting[element_movement]' => isset($random_settings['element_movement']) ? $random_settings['element_movement'] : 1,
            'RandomSetting[static_position]' => isset($random_settings['static_position']) ? $random_settings['static_position'] : 1,
            'RandomSetting[moving_position]' => isset($random_settings['moving_position']) ? $random_settings['moving_position'] : 1,
            
            // changes over
        );
        if (!isset($random_settings['show_page'])) {
            $RandomElementFieldValues['RandomSetting[show_page][]'] = array();
        } else {
            $RandomElementFieldValues['RandomSetting[show_page][]'] = $random_settings['show_page'];
        }
        if (!isset($random_settings['not_show_page'])) {
            $RandomElementFieldValues['RandomSetting[not_show_page][]'] = array();
        } else {
            $RandomElementFieldValues['RandomSetting[not_show_page][]'] = $random_settings['not_show_page'];
        }
        /**
         * As Tools::jsonDecode is not defined in PS1.8 so used json_decode  
         * PMoct2023 compatibility1.8
         * @date 12-10-2023
         * @modifier Pragya Maurya
         */
        $discount_settings = json_decode(Configuration::get('KB_WD_DISCOUNT_SETTING'), true);
        if (isset($discount_settings['redirect_coupon_id']) && $discount_settings['redirect_coupon_id'] != 0) {
            $cart_rule_obj = new CartRule($discount_settings['redirect_coupon_id']);
            $cart_rule_data  = array();
            $cart_rule_data['id_rule'] = $discount_settings['redirect_coupon_id'];
            $cart_rule_data['name'] = $cart_rule_obj->name[$this->context->language->id].'(desc: '. $cart_rule_obj->description .')';
            $this->context->smarty->assign('selectedcoupons', $cart_rule_data);
        }
        // discount element
        $this->fields_form4 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Discount Coupon Strip Setting'),
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Enable/Disable the discount Coupon Strip'),
                        'type' => 'switch',
                        'name' => 'DiscountSetting[enable]',
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'DiscountSetting[enable]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'DiscountSetting[enable]_off',
                            ),
                        ),
                        'hint' => $this->l('Enable or Disable the header Element'),
                    ),
                    array(
                        'label' => $this->l('Where to Display'),
                        'type' => 'select',
                        'name' => 'DiscountSetting[where_to_display]',
                        'hint' => $this->l('Select where to display snow'),
                        'onchange' => 'showHideDisplayPageDiscountSetting(this)',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $where_to_display,
                            'id' => 'id_wheredisplay',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'DiscountSetting[show_page][]',
                        'hint' => $this->l('Select the Pages to display snow.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'multiple' => true,
                        'label' => $this->l('Select the Page'),
                        'name' => 'DiscountSetting[not_show_page][]',
                        'hint' => $this->l('Select the Pages  not to display snow'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $front_pages,
                            'id' => 'id_page_type',
                            'name' => 'name',
                        ),
//                        'required' => true,
                    ),
                    array(
                        'label' => $this->l('Set Fix Time'),
                        'type' => 'switch',
                        'hint' => $this->l('Allow effects to display for defined time.'),
                        'class' => 'optn_allow_date',
                        'name' => 'DiscountSetting[fix_time]',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'value' => 1,
                                'id' => 'DiscountSetting[fix_time]_on',
                            ),
                            array(
                                'value' => 0,
                                'id' => 'DiscountSetting[fix_time]_off',
                            ),
                        ),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Active Date/Time'),
                        'name' => 'DiscountSetting[active_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Active time of display effects'),
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Expire Date/Time'),
                        'name' => 'DiscountSetting[expire_date]',
                        'col' => 6,
                        'required' => true,
                        'hint' => $this->l('Expire time of display effects'),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Discount Strip Background color'),
                        'name' => 'DiscountSetting[strip_background_color]',
                        'maxlength' => 10,
                        'required' => true,
                        'hint' => $this->l('Discount Strip Background color'),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Discount Strip text color'),
                        'name' => 'DiscountSetting[strip_text_color]',
                        'maxlength' => 10,
                        'required' => true,
                        'hint' => $this->l('Discount Strip text color'),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Discount Strip close buttom background color'),
                        'name' => 'DiscountSetting[strip_close_bg_color]',
                        'maxlength' => 10,
                        'required' => true,
                        'hint' => $this->l('Discount Strip close buttom background color'),
                    ),
                    array(
                        'label' => $this->l('Enter the Coupon Name'),
                        'type' => 'text',
                        'hint' => $this->l('Start typing the coupon name'),
                        'class' => 'ac_input',
                        'required' => true,
                        'name' => 'redirect_coupon_name',
                        'autocomplete' => false,
                    ),
                    array(
                        'type' => 'html',
                        'name' => '',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name. '/views/templates/admin/showSelectedCoupons.tpl'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'redirect_coupon_id',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right kb_discount_setting_btn'
                ),
            )
        );
        // discount element setting
        $DiscountElementFieldValues = array();
        
        $DiscountElementFieldValues = array(
            // changes by rishabh jain
            'DiscountSetting[enable]' => isset($discount_settings['enable']) ? $discount_settings['enable'] : 0,
//            'RandomSetting[element]' => isset($discount_settings['element']) ? $discount_settings['element'] : 0,
            'DiscountSetting[where_to_display]' => isset($discount_settings['where_to_display']) ? $discount_settings['where_to_display'] : 0,
            'DiscountSetting[fix_time]' => isset($discount_settings['fix_time']) ? $discount_settings['fix_time'] : 0,
            'DiscountSetting[active_date]' => isset($discount_settings['active_date']) ? $discount_settings['active_date'] : '',
            'DiscountSetting[expire_date]' => isset($discount_settings['expire_date']) ? $discount_settings['expire_date'] : '',
            'DiscountSetting[where_to_display]' => isset($discount_settings['where_to_display']) ? $discount_settings['where_to_display'] : 1,
            'redirect_coupon_name' => isset($discount_settings['redirect_coupon_name']) ? $discount_settings['redirect_coupon_name'] : '',
            'redirect_coupon_id' => isset($discount_settings['redirect_coupon_id']) ? $discount_settings['redirect_coupon_id'] : 0,
            'DiscountSetting[strip_close_bg_color]' => isset($discount_settings['strip_close_bg_color']) ? $discount_settings['strip_close_bg_color'] : '#225094',
            'DiscountSetting[strip_background_color]' => isset($discount_settings['strip_background_color']) ? $discount_settings['strip_background_color'] : '#356cbf',
            'DiscountSetting[strip_text_color]' => isset($discount_settings['strip_text_color']) ? $discount_settings['strip_text_color'] : '#ffffff',
            // changes over
        );
        if (!isset($discount_settings['show_page'])) {
            $DiscountElementFieldValues['DiscountSetting[show_page][]'] = array();
        } else {
            $DiscountElementFieldValues['DiscountSetting[show_page][]'] = $discount_settings['show_page'];
        }
        if (!isset($discount_settings['not_show_page'])) {
            $DiscountElementFieldValues['DiscountSetting[not_show_page][]'] = array();
        } else {
            $DiscountElementFieldValues['DiscountSetting[not_show_page][]'] = $discount_settings['not_show_page'];
        }
        // changes over discount element
        $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }
        $form = $this->getform($this->form_fields, $languages, $field_value, 'general', $action);
        $form1 = $this->getform($this->fields_form1, $languages, $HeaderElementFieldValues, 'header', $action);
        $form2 = $this->getform($this->fields_form2, $languages, $FooterElementFieldValues, 'footer', $action);
        $form3 = $this->getform($this->fields_form3, $languages, $RandomElementFieldValues, 'randomelement', $action);
        $form4 = $this->getform($this->fields_form4, $languages, $DiscountElementFieldValues, 'discount', $action);
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        
        $header_image_path = array(
            array(
                'id_image' => 1,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/header/1.png',
            ),
            array(
                'id_image' => 2,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/header/2.gif',
            ),
            array(
                'id_image' => 3,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/header/3.png',
            ),
            array(
                'id_image' => 4,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/header/4.png',
            ),
        );
        $footer_image_path = array(
            array(
                'id_image' => 1,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/1.gif',
            ),
            array(
                'id_image' => 2,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/2.gif',
            ),
            array(
                'id_image' => 3,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/3.gif',
            ),
            array(
                'id_image' => 4,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/4.png',
            ),
            array(
                'id_image' => 5,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/5.png',
            ),
            array(
                'id_image' => 6,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/6.gif',
            ),
            array(
                'id_image' => 7,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/7.png',
            ),
            array(
                'id_image' => 8,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/footer/8.png',
            ),
            
        );
        $random_image_path = array(
            array(
                'id_image' => 1,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/1.gif',
            ),
            array(
                'id_image' => 2,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/2.gif',
            ),
            array(
                'id_image' => 3,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/3.gif',
            ),
            array(
                'id_image' => 4,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/4.gif',
            ),
            array(
                'id_image' => 5,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/5.gif',
            ),
            array(
                'id_image' => 6,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/6.gif',
            ),
            array(
                'id_image' => 7,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/7.png',
            ),
            array(
                'id_image' => 8,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/8.gif',
            ),
            array(
                'id_image' => 9,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/9.png',
            ),
            array(
                'id_image' => 10,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/10.png',
            ),
            array(
                'id_image' => 11,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/11.gif',
            ),
            array(
                'id_image' => 12,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/12.gif',
            ),
            array(
                'id_image' => 13,
                'path' => $module_dir . 'kbwebsitedecorationeffect/views/img/random/13.gif',
            ),
        );
        $this->context->smarty->assign('footer_image_path', json_encode($footer_image_path));
        $this->context->smarty->assign('header_image_path', json_encode($header_image_path));
        $this->context->smarty->assign('random_image_path', json_encode($random_image_path));
        
        
        $tabs_data = array();
        foreach ($this->available_tabs as $tab) {
            $tabs_data[$tab] = array(
                'id' => $tab,
                'selected' => (Tools::strtolower($tab) == Tools::strtolower($this->tab_display) || (isset($this->tab_display_module) && 'module' . $this->tab_display_module == Tools::strtolower($tab))),
                'name' => $this->available_tabs_lang[$tab],
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            );
        }
        if (!version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
//            $this->context->controller->addCSS($this->_path . 'views/css/admin/pageviewer_16.css');
            $version = 1.6;
        } else {
//            $this->context->controller->addCSS($this->_path . 'views/css/admin/pageviewer_15.css');
            $version = 1.5;
        }

//        $this->context->smarty->assign('table', $list);
        $this->context->smarty->assign('default_tab', $this->tab_display);
//        $this->context->smarty->assign('notification_list', $notificationlist);
//        $this->context->smarty->assign('slider_list', $sliderlist);
//        $this->context->smarty->assign('banner_list', $bannerslist);
        $this->context->smarty->assign('available_tabs', $tabs_data);
        $this->context->smarty->assign('default_language_id', $this->context->language->id);
        $this->context->smarty->assign('default_language_code', $this->context->language->iso_code);
        $this->context->smarty->assign('form', $form);
        $this->context->smarty->assign('form1', $form1);
        $this->context->smarty->assign('form2', $form2);
        $this->context->smarty->assign('form3', $form3);
        $this->context->smarty->assign('form4', $form4);
//        $this->context->smarty->assign('slider_form', $sliderform);
//        $this->context->smarty->assign('form_add_new', $form_add_new);
        $this->context->smarty->assign('firstCall', false);
        $this->context->smarty->assign('general_settings', $this->l('General Settings'));
        $this->context->smarty->assign('mod_dir', _MODULE_DIR_);
        $this->context->smarty->assign('version', $version);
//        $this->context->smarty->assign('view', $view);
//        $this->context->smarty->assign('notification_button', $notificationview);
//        $this->context->smarty->assign('cancel_button', $cancelview);
        $this->context->smarty->assign('action', $action);
        $this->context->smarty->assign('action_page', $action . '&configure='.$this->name);

        $tpl = 'Form_custom.tpl';
        $helper = new Helper();
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                : 0;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'form/';
        $helper->setTpl($tpl);
        $tpl = $helper->generate();


        
//        print_r(json_encode($footer_image_path));
//        die;
//        $this->context->smarty->assign('header_image_path', $module_dir . 'kbwebsitedecorationeffect/views/img/header');
        $output = $output . $tpl;
        return $output;
    }
    
    public function ajaxcouponlist()
    {
        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list, 
         * they are no return values just because string:"(ref : #ref_pattern#)" 
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (desc:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $sql = 'SELECT cr.`id_cart_rule`, cl.name,cr.description
		FROM `' . _DB_PREFIX_ . 'cart_rule` cr
		LEFT JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` cl ON (cl.id_cart_rule = '
                . 'cr.id_cart_rule AND cl.id_lang = '
                . '' . (int) Context::getContext()->language->id. ')
		WHERE (cl.name LIKE \'%' . pSQL($query) . '%\')';

        $items = Db::getInstance()->executeS($sql);
        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['description']) ?
                        ' (desc: ' . $item['description'] . ')' : '') .
                '|' . (int) ($item['id_cart_rule']) . "\n";
            }
        }
    }
    
    public function getform($field_form, $languages, $field_value, $id, $action)
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $field_value;
        $helper->name_controller = $this->name;
        $helper->languages = $languages;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                : 0;
        $helper->default_form_language = $this->context->language->id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        if ($id == 'general') {
            $helper->show_toolbar = true;
        } else {
            $helper->show_toolbar = false;
        }
        $helper->table = $id;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = $action;
        return $helper->generateForm(array('form' => $field_form));
    }
    
    /* Default settings for module */

    public function getDefaultSettings()
    {
        $field_value = array(
            'enable' => 0,
            'mobile' => 0,
            'max_display_freq' => 1,
            'where_to_display' => 1,
            'fix_time' => 0,
            'active_date' => '',
            'expire_date' => '',
            'show_page' => array(),
            'not_show_page' => array(),
            'flake_color' => '#ef3500',
            'effect_type' => 'flake',
            'flake_size' => 15,
            'flake_numbers' => 50,
            'flake_min_speed' => 15,
            'flake_max_speed' => 30,
            'flake_disable_time' => 0,
            'flurry_character' => '❄❅❆',
            'flurry_height' => 500,
            'flurry_frequency' => 100,
            'flurry_speed' => 3000,
            'flurry_min_size' => 8,
            'flurry_max_size' => 28,
            'flurry_wind_drift' => 40,
            'flurry_wind_variance' => 20,
            'flurry_rotation' => 90,
            'flurry_rotation_variance' => 180,
            'letitsnow_min_size' => 3,
            'letitsnow_max_size' => 7,
            'letitsnow_max_count' => 100,
            'letitsnow_speed' => 10,
            'letitsnow_falltime' => 5000,
            'christmas_min_size' => 20,
            'christmas_max_size' => 50,
            'christmas_fallTimeMultiplier' => 20,
            'christmas_fallTimeDifference' => 10000,
            'christmas_spawnInterval' => 500,
        );

        return $field_value;
    }

    private function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
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
    private function getControllers()
    {
        $controllers_name = array(
            'address' => $this->l('New Address Page'),
            'addresses' => $this->l('Addresses Page'),
            'attachment' => $this->l('Attachment Page'),
            'authentication' => $this->l('Login Page'),
            'bestsales' => $this->l('Best Sales Page'),
            'cart' => $this->l('Cart Page'),
            'category' => $this->l('Category Page'),
            'changecurrency' => $this->l('Change Currency Page'),
            'cms' => $this->l('Cms Page'),
            'compare' => $this->l('Compare Page'),
            'contact' => $this->l('Contact Page'),
            'discount' => $this->l('Discount Page'),
            'getfile' => $this->l('Get File Page'),
            'guesttracking' => $this->l('Guest Tracking Page'),
            'history' => $this->l('History Page'),
            'identity' => $this->l('Identity Page'),
            'index' => $this->l('Home Page'),
            'manufacturer' => $this->l('Manufacturer Page'),
            'myaccount' => $this->l('MyAccount Page'),
            'newproducts' => $this->l('New Products Page'),
            'orderconfirmation' => $this->l('Order Confirmation Page'),
            'order' => $this->l('Order Page'),
            'orderdetail' => $this->l('Order Detail Page'),
            'orderfollow' => $this->l('Order Follow Page'),
            'orderopc' => $this->l('Order Opc Page'),
            'orderreturn' => $this->l('Order Return Page'),
            'orderslip' => $this->l('Order Slip Page'),
            'pagenotfound' => $this->l('Page Not Found Page'),
            'parentorder' => $this->l('Parent Order Page'),
            'password' => $this->l('Password Page'),
            'pdfinvoice' => $this->l('Pdf Invoice Page'),
            'pdforderreturn' => $this->l('Pdf Order Return Page'),
            'pdforderslip' => $this->l('Pdf Order Slip Page'),
            'pricesdrop' => $this->l('Prices Drop Page'),
            'product' => $this->l('Product Page'),
            'search' => $this->l('Search Page'),
            'sitemap' => $this->l('Sitemap Page'),
            'statistics' => $this->l('Statistics Page'),
            'stores' => $this->l('Stores Page'),
            'supplier' => $this->l('Supplier Page'),
        );
        return $controllers_name;
    }
}
