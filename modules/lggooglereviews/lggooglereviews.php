<?php
/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require realpath(dirname(__FILE__)) . '/config/config.inc.php';

class Lggooglereviews extends Module
{
    protected $config_form = false;
    public $platform = 'ps';

    public function __construct()
    {
        $this->name = 'lggooglereviews';
        $this->tab = 'front_office_features';
        $this->version = '1.0.3';
        $this->author = 'Línea Gráfica';
        $this->need_instance = 0;
        $this->path = $this->_path;
        $this->imageType = 'png';
        $this->id_product = '85924';
        $this->module_key = '2fd7480ebc175c2496bafc6f070d2908';
        $this->platform = 'ps';
        $this->is_17 = Tools::substr(_PS_VERSION_, 0, 3) === '1.7';

        // Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('LG Google Reviews');
        $this->description = $this->l('Include Google reviews in your store');
        $this->default_pagination = 25;
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        require_once dirname(__FILE__) . '/sql/install.php';

        // $iso_code = Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'));

        Configuration::updateValue('LGGOOGLEREVIEWS_LOGGING', 0);
        Configuration::updateValue('LGGOOGLEREVIEWS_APIKEY', '');

        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayFooter')
            && $this->registerHook('displayFooterAfter')
            && $this->registerHook('displayFooterBefore')
            && $this->registerHook('displayHome')
            && $this->registerHook('displayHomeBottom')
            && $this->registerHook('displayLeftColumn')
            && $this->registerHook('displayRightColumn')
            && $this->registerHook('displayBackOfficeHeader')
            && $this->registerHook('customLGGoogleReviews'); // Custom hook to display the module where customer wants
    }

    public function uninstall()
    {
        require_once dirname(__FILE__) . '/sql/uninstall.php';
        return parent::uninstall();
    }

    public function getContent()
    {
        $this->token = Tools::getAdminTokenLite('AdminModules');
        $this->url = $this->context->link->getAdminLink('AdminModules', false) . '&' .
            'configure=' . $this->name . '&' .
            'token=' . Tools::getAdminTokenLite('AdminModules') . '&' .
            'tab_lg=';

        $lg_help_url = is_dir(dirname(__FILE__) . '/views/img/help/' . $this->context->language->iso_code)
            ? _MODULE_DIR_ . $this->name . '/views/img/help/' . $this->context->language->iso_code
            : _MODULE_DIR_ . $this->name . '/views/img/help/en';

        $publi_class_name = str_replace('Override', '', get_class($this));
        $publi_class_name .= 'Publi' . Tools::strtoupper($this->platform);

        $publi_class_name::setModule($this);
        $publi_class_name::setModules($publi_class_name::$modules);

        $params = [
            'lg_id_product' => $this->id_product,
            'lg_module_dir' => $this->_path,
            'lg_module_name' => $this->name,
            'lg_base_url' => _MODULE_DIR_ . $this->name . '/',
            'lg_help_url' => $lg_help_url . '/',
            'lg_iso_code' => $this->context->language->iso_code,
        ];

        $this->context->smarty->assign($params);

        switch (Tools::getValue('tab_lg')) {
            case 'places':
                $id_place = (int) Tools::getValue('id_lggooglereviews_place');
                if (Tools::getValue('addnew') || Tools::isSubmit('updatelggooglereviews_place')) {
                    $body = $this->renderPlaceForm($id_place);
                } elseif (Tools::issubmit('deletelggooglereviews_place')) {
                    $place = new LggooglereviewsPlace($id_place);
                    $place->delete();
                    $body = $this->renderPlacesList();
                } else {
                    if ($this->postProcessPlace()) {
                        $body = $this->renderPlacesList();
                    } else {
                        $body = $this->renderPlaceForm($id_place);
                    }
                }
                break;
            case 'help':
                $lggooglereviews_apikey = Configuration::get('LGGOOGLEREVIEWS_APIKEY');
                $lggooglereviews_places = LggooglereviewsPlace::getList();

                $this->context->smarty->assign([
                    'lggooglereviews_apikey' => !empty($lggooglereviews_apikey) ? $lggooglereviews_apikey : '',
                    'lggooglereviews_places' => !empty($lggooglereviews_places) ? $lggooglereviews_places : [],
                ]);

                $body = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/help.tpl');

                break;
            case 'debug':
                $list_debug = $this->renderDebugList();
                $body = $list_debug;
                break;
            default:
                $this->postProcess();
                $form = $this->renderForm();
                $body = $form;
                break;
        }

        $params = [
            'lg_menu' => $this->getMenu(),
        ];

        $this->context->smarty->assign($params);

        $header = $publi_class_name::renderHeader();
        $footer = $publi_class_name::renderFooter();

        $this->context->smarty->assign($params);

        return $header . $body . $footer;
    }

    public function renderPlaceForm($id_place = 0)
    {
        $lg_tab = 'places';

        $shops = Shop::getShops();

        $number_reviews = [];
        $number_reviews[] = ['num' => 1];
        $number_reviews[] = ['num' => 2];
        $number_reviews[] = ['num' => 3];
        $number_reviews[] = ['num' => 4];
        $number_reviews[] = ['num' => 5];

        $order_reviews = [];
        $order_reviews[] = ['ord' => 'Most relevant'];
        $order_reviews[] = ['ord' => 'Newest'];

        $place = new LggooglereviewsPlace($id_place);

        // Get default language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $image = _PS_TMP_IMG_DIR_ . $this->name . '_' . $place->id . '.' . $this->imageType;
        $image_url = ImageManager::thumbnail(
            $image,
            $this->name . '_80_' . (int) $place->id . '.' . $this->imageType,
            80,
            $this->imageType,
            true,
            true
        );
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

        // Init Fields form array
        $fields_form = [];
        $fields_form['form'] = [
            'tabs' => [
                'basic' => $this->l('Basic configuration'),
                'display' => $this->l('Presentation'),
                'statistic' => $this->l('Statistic'),
            ],
            'input' => [
                [
                    'tab' => 'basic',
                    'type' => 'hidden',
                    'name' => LggooglereviewsPlace::$definition['primary'],
                ],
                [
                    'tab' => 'basic',
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'size' => 256,
                    'required' => true,
                ],
                [
                    'tab' => 'basic',
                    'type' => 'select',
                    'label' => $this->l('Shop'),
                    'name' => 'id_shop',
                    'options' => [
                        'query' => $shops,
                        'id' => 'id_shop',
                        'name' => 'name',
                    ],
                    'required' => true,
                ],
                [
                    'tab' => 'basic',
                    'type' => 'file',
                    'label' => $this->l('Place image'),
                    'name' => 'logo',
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'display_image' => true,
                    'col' => 6,
                    'desc' => $this->l('Upload a place image for your reviews block.'),
                ],
                [
                    'tab' => 'basic',
                    'type' => 'text',
                    'label' => $this->l('Google Places ID'),
                    'name' => 'google_place_id',
                    'size' => 32,
                    'required' => true,
                ],
                [
                    'tab' => 'display',
                    'type' => 'select',
                    'label' => $this->l('Reviews to display'),
                    'desc' => $this->l('Select how many reviews you want to show per row in desktop version'),
                    'name' => 'num_reviews',
                    'options' => [
                        'query' => $number_reviews,
                        'id' => 'num',
                        'name' => 'num',
                    ],
                    'required' => true,
                ],
                [
                    'tab' => 'display',
                    'type' => 'select',
                    'label' => $this->l('Order of reviews'),
                    'desc' => $this->l('Select how you want to order the reviews'),
                    'name' => 'order_reviews',
                    'options' => [
                        'query' => $order_reviews,
                        'id' => 'ord',
                        'name' => 'ord',
                    ],
                    'required' => true,
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Display Rich snippets'),
                    'name' => 'display_snippets',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want active rich snippets'),
                    'values' => [
                        [
                            'id' => 'display_snippets_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'display_snippets_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show header'),
                    'name' => 'show_header',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want show header'),
                    'values' => [
                        [
                            'id' => 'show_header_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_header_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show image header'),
                    'name' => 'show_image_header',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want show image in header'),
                    'values' => [
                        [
                            'id' => 'show_image_header_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_image_header_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show link to Google business page'),
                    'name' => 'show_link_business',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want show your Google Business page'),
                    'values' => [
                        [
                            'id' => 'show_link_business_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_link_business_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show link to "View all reviews"'),
                    'name' => 'show_link_all_reviews',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want show link to "View all reviews"'),
                    'values' => [
                        [
                            'id' => 'show_link_all_reviews_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_link_all_reviews_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show link to "Add your review"'),
                    'name' => 'show_link_add_review',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want show link to "Add your reviews" to your customers'),
                    'values' => [
                        [
                            'id' => 'show_link_add_review_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_link_add_review_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'text',
                    'label' => $this->l('Url add review'),
                    'name' => 'url_add_review',
                    'size' => 256,
                    'required' => false,
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show message "Powered by Google"'),
                    'name' => 'show_powered_by_google',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want show message "Powered by Google"'),
                    'values' => [
                        [
                            'id' => 'show_powered_by_google_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_powered_by_google_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show your rating and count reviews'),
                    'name' => 'show_total_ratings',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want show your rating and your total reviews'),
                    'values' => [
                        [
                            'id' => 'show_total_ratings_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_total_ratings_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show translated reviews'),
                    'name' => 'translated_reviews',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want to show translated reviews. Select "No" if you want to display reviews in the original language of the review.'),
                    'values' => [
                        [
                            'id' => 'translated_reviews_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'translated_reviews_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'display',
                    'type' => 'switch',
                    'label' => $this->l('Show link to users profile'),
                    'name' => 'show_user_links',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want add link to user profiles'),
                    'values' => [
                        [
                            'id' => 'show_user_links_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'show_user_links_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'statistic',
                    'type' => 'switch',
                    'label' => $this->l('Valid Place ID'),
                    'name' => 'validated',
                    'readonly' => 'true',
                    'disabled' => 'true',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'validated_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'validated_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'tab' => 'statistic',
                    'type' => 'text',
                    'readonly' => 'true',
                    'label' => $this->l('Rating'),
                    'name' => 'rating',
                    'size' => 32,
                ],
                [
                    'tab' => 'statistic',
                    'type' => 'text',
                    'readonly' => 'true',
                    'label' => $this->l('Review count'),
                    'name' => 'review_count',
                    'size' => 32,
                ],
                [
                    'tab' => 'statistic',
                    'type' => 'text',
                    'readonly' => 'true',
                    'label' => $this->l('Url profile'),
                    'name' => 'url',
                    'size' => 32,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->url . $lg_tab;
        $helper->fieldImageSettings = [
            'name' => 'logo',
            'dir' => 'tmp',
        ];

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true; // false -> remove toolbar
        $helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name . '_place';

        $helper->tpl_vars = [
            'fields_value' => $this->loadPlace(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    public function loadPlace()
    {
        // $langs = LanguageCore::getLanguages();
        // $id_lang = (int) Context::getContext()->language->id;

        $id_place = (int) Tools::getValue('id_lggooglereviews_place', 0);
        $place = new LggooglereviewsPlace($id_place);

        $values = [];

        $values['id_lggooglereviews_place'] = $id_place;

        $values['name'] = pSQL(Tools::getValue('name')) != ''
            ? pSQL(Tools::getValue('name'))
            : $place->name;

        // $description_default = '';

        $values['id_shop'] = pSQL(Tools::getValue('id_shop')) != ''
            ? pSQL(Tools::getValue('id_shop'))
            : $place->id_shop;

        $values['google_place_id'] = pSQL(Tools::getValue('google_place_id')) != ''
            ? pSQL(Tools::getValue('google_place_id'))
            : $place->google_place_id;

        $values['url_add_review'] = pSQL(Tools::getValue('url_add_review')) != ''
            ? pSQL(Tools::getValue('url_add_review'))
            : $place->url_add_review;

        $values['num_reviews'] = pSQL(Tools::getValue('num_reviews')) != ''
            ? pSQL(Tools::getValue('num_reviews'))
            : $place->num_reviews;

        $values['order_reviews'] = pSQL(Tools::getValue('order_reviews')) != ''
            ? pSQL(Tools::getValue('order_reviews'))
            : $place->order_reviews;

        $values['display_snippets'] = pSQL(Tools::getValue('display_snippets')) != ''
            ? pSQL(Tools::getValue('display_snippets'))
            : $place->display_snippets;

        $values['show_header'] = pSQL(Tools::getValue('show_header')) != ''
            ? pSQL(Tools::getValue('show_header'))
            : $place->show_header;

        $values['show_image_header'] = pSQL(Tools::getValue('show_image_header')) != ''
            ? pSQL(Tools::getValue('show_image_header'))
            : $place->show_image_header;

        $values['show_link_business'] = pSQL(Tools::getValue('show_link_business')) != ''
            ? pSQL(Tools::getValue('show_link_business'))
            : $place->show_link_business;

        $values['show_link_all_reviews'] = pSQL(Tools::getValue('show_link_all_reviews')) != ''
            ? pSQL(Tools::getValue('show_link_all_reviews'))
            : $place->show_link_all_reviews;

        $values['show_link_add_review'] = pSQL(Tools::getValue('show_link_add_review')) != ''
            ? pSQL(Tools::getValue('show_link_add_review'))
            : $place->show_link_add_review;

        $values['show_powered_by_google'] = pSQL(Tools::getValue('show_powered_by_google')) != ''
            ? pSQL(Tools::getValue('show_powered_by_google'))
            : $place->show_powered_by_google;

        $values['show_total_ratings'] = pSQL(Tools::getValue('show_total_ratings')) != ''
            ? pSQL(Tools::getValue('show_total_ratings'))
            : $place->show_total_ratings;

        $values['translated_reviews'] = pSQL(Tools::getValue('translated_reviews')) != ''
            ? pSQL(Tools::getValue('translated_reviews'))
            : $place->translated_reviews;

        $values['show_user_links'] = pSQL(Tools::getValue('show_user_links')) != ''
            ? pSQL(Tools::getValue('show_user_links'))
            : $place->show_user_links;

        $values['rating'] = pSQL(Tools::getValue('rating')) != ''
            ? pSQL(Tools::getValue('rating'))
            : $place->rating;

        $values['review_count'] = pSQL(Tools::getValue('review_count')) != ''
            ? pSQL(Tools::getValue('review_count'))
            : $place->review_count;

        $values['url'] = pSQL(Tools::getValue('url')) != ''
            ? pSQL(Tools::getValue('url'))
            : $place->url;

        $values['validated'] = pSQL(Tools::getValue('validated')) != ''
            ? pSQL(Tools::getValue('validated'))
            : $place->validated;

        return $values;
    }

    /**
     * Save form data.
     */
    protected function postProcessPlace()
    {
        if (Tools::isSubmit('submitlggooglereviews_place')) {
            $id_place = (int) Tools::getValue('id_lggooglereviews_place');

            $place = new LggooglereviewsPlace($id_place);
            $errors = [];

            // Validations
            $name = psql(Tools::getValue('name'));

            if ($name != '') {
                $place->name = $name;
            } else {
                $errors[] = sprintf($this->l('The %s field is mandatory'), $this->l('name'));
            }

            $id_shop = (int) Tools::getValue('id_shop');
            $place->id_shop = $id_shop;

            $num_reviews = (int) Tools::getValue('num_reviews');
            $place->num_reviews = $num_reviews;

            $order_reviews = psql(Tools::getValue('order_reviews'));
            $place->order_reviews = $order_reviews;

            $display_snippets = (int) Tools::getValue('display_snippets');
            $place->display_snippets = $display_snippets;

            $show_header = (int) Tools::getValue('show_header');
            $place->show_header = $show_header;

            $show_image_header = (int) Tools::getValue('show_image_header');
            $place->show_image_header = $show_image_header;

            $show_image_header = (int) Tools::getValue('show_image_header');
            $place->show_image_header = $show_image_header;

            $show_link_business = (int) Tools::getValue('show_link_business');
            $place->show_link_business = $show_link_business;

            $show_link_all_reviews = (int) Tools::getValue('show_link_all_reviews');
            $place->show_link_all_reviews = $show_link_all_reviews;

            $show_link_add_review = (int) Tools::getValue('show_link_add_review');
            $place->show_link_add_review = $show_link_add_review;

            $show_powered_by_google = (int) Tools::getValue('show_powered_by_google');
            $place->show_powered_by_google = $show_powered_by_google;

            $show_total_ratings = (int) Tools::getValue('show_total_ratings');
            $place->show_total_ratings = $show_total_ratings;

            $translated_reviews = (int) Tools::getValue('translated_reviews');
            $place->translated_reviews = $translated_reviews;

            $show_user_links = (int) Tools::getValue('show_user_links');
            $place->show_user_links = $show_user_links;

            $google_place_id = psql(Tools::getValue('google_place_id'));

            if (!empty($google_place_id)) {
                $place->google_place_id = $google_place_id;
            } else {
                $errors[] = sprintf($this->l('The %s field is mandatory'), $this->l('google_place_id'));
            }

            $google_place_id_exist = LggooglereviewsPlace::googlePlaceIDExists($google_place_id);

            if ($google_place_id_exist
                && $google_place_id_exist != (int) $id_place
            ) {
                $errors[] = $this->l('Another place with the same ID already exists.');
            }

            $url_add_review = psql(Tools::getValue('url_add_review'));
            $place->url_add_review = $url_add_review;

            // Show Messages
            if (count($errors) > 0 || !$place->save()) {
                $this->context->smarty->assign('show_errors', $errors);

                return false;
            } else {
                if (isset($_FILES['logo']) && isset($_FILES['logo']['tmp_name']) && $_FILES['logo']['tmp_name'] != '') {
                    $target_file = _PS_TMP_IMG_DIR_ . $this->name . '_' . $id_place . '.' . $this->imageType;
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    // Check if image file is a actual image or fake image
                    $check = getimagesize($_FILES['logo']['tmp_name']);
                    if ($check === false) {
                        $errors[] = $this->l('File is not image.');
                    }

                    // Check if file already exists
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                    // Allow certain file formats
                    if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg'
                        && $imageFileType != 'gif') {
                        $errors[] = $this->l('File format is not correct.');
                    }
                    if (!count($errors)) {
                        if (!ImageManager::thumbnail(
                            $_FILES['logo']['tmp_name'],
                            $this->name . '_' . (int) $place->id . '.' . $this->imageType,
                            150,
                            $this->imageType,
                            true,
                            true
                        )) {
                            $errors[] = $this->l('Sorry, there was an error uploading your file.');
                        }
                    }
                }

                LggooglereviewsTools::getReviews($place);

                if (count($errors) > 0 || !$place->save()) {
                    $this->context->smarty->assign('show_errors', $errors);

                    return false;
                }

                $this->context->smarty->assign('show_message', 1);

                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Create the list of places.
     */
    protected function renderPlacesList()
    {
        $list_places = LggooglereviewsPlace::getList(1);
        $lg_tab = 'places';

        $fields_list = [
            'name' => [
                'title' => $this->l('Name'),
                'type' => 'text',
            ],
            'google_place_id' => [
                'title' => $this->l('Google Places Id'),
                'type' => 'text',
            ],
            'validated' => [
                'title' => $this->l('Validated'),
                'type' => 'bool',
                'status' => true,
                'align' => 'center',
            ],
            'rating' => [
                'title' => $this->l('Rating'),
                'type' => 'text',
                'align' => 'center',
            ],
            'review_count' => [
                'title' => $this->l('Reviews'),
                'type' => 'text',
                'align' => 'center',
            ],
        ];

        $helper_list = new HelperList();
        $helper_list->name = 'places';
        $helper_list->module = $this;
        $this->table = LggooglereviewsPlace::$definition['table'];
        $this->className = 'LggooglereviewsPlace';
        $helper_list->title = $this->l('Places list');
        $helper_list->shopLinkType = '';
        $helper_list->no_link = true;
        $helper_list->default_form_language = 1;
        $helper_list->show_toolbar = true;
        $helper_list->toolbar_btn['new'] = [
            'href' => $this->url . $lg_tab . '&addnew=true',
            'desc' => $this->l('Add new'),
        ];
        $helper_list->simple_header = false;
        $helper_list->identifier = LggooglereviewsPlace::$definition['primary'];

        if (version_compare(_PS_VERSION_, '1.7.6.1', '>=')) {
            $helper_list->table = 'places';
        } else {
            $helper_list->table = LggooglereviewsPlace::$definition['table'];
        }
        $helper_list->table = LggooglereviewsPlace::$definition['table'];

        // Actions to be displayed in the 'Actions' column
        $helper_list->actions = ['edit', 'delete'];
        $helper_list->list_id = $helper_list->table;
        $helper_list->currentIndex = $this->url . $lg_tab;
        $helper_list->token = $this->token;

        // This is needed for displayEnableLink to avoid code duplication
        $this->_helperlist = $helper_list;
        $helper_list->listTotal = count($list_places);
        $helper_list->tpl_vars['icon'] = 'icon-AdminParentOrders';
        if (version_compare(_PS_VERSION_, '1.6.0.14', '>')) {
            $helper_list->_default_pagination = $this->default_pagination;
            $helper_list->_pagination = [10];
        }

        /* Paginate the result */
        $page = ($page = Tools::getValue('submitFilter' . $helper_list->table)) ? $page : 1;
        $pagination = ($pagination = Tools::getValue($helper_list->table . '_pagination')) ?
            $pagination : $this->default_pagination;
        $list_places = $this->paginate($list_places, $page, $pagination);
        $generated_list = $helper_list->generateList($list_places, $fields_list);

        return $generated_list;
    }

    public function paginate($array_elements, $page = 1, $pagination = 5)
    {
        if (count($array_elements) > $pagination) {
            $array_elements = array_slice($array_elements, $pagination * ($page - 1), $pagination);
        }

        return $array_elements;
    }

    /**
     * Create the list of log events.
     */
    protected function renderDebugList()
    {
        $helper = new HelperList();

        $list_logs = LggooglereviewsLog::getList(LggooglereviewsLog::LIMIT_LOG);

        $fields_events = [
            'date_add' => [
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'align' => 'text-center',
            ],
            'reason' => [
                'title' => $this->l('Reason'),
                'type' => 'text',
            ],
        ];

        $helper->show_toolbar = false;
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->actions = [];
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        // $helper->listTotal = count($list_events);
        // $helper->_default_pagination = 20;

        $helper->tpl_vars = [
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        $helper->identifier = LggoogleanalyticsEvent::$definition['primary'];
        $helper->title = $this->l('Last Submit events');
        $helper->table = LggoogleanalyticsEvent::$definition['table'];
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name .
            '&module_name=' . $this->name . '&tab_name=' . $this->tab . '&tab_lg=debug';
        $helper->orderBy = 'date_add';
        $helper->orderBy = 'desc';

        $list = $helper->generateList($list_logs, $fields_events);

        return $list;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLggooglereviews';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), // Add values for your inputs
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $availableHooks = $this->getAvailableHooks();

        $list_hooks = [];

        $item = [];
        $item['id'] = '';
        $item['name'] = '-';

        $list_hooks[] = $item;

        foreach ($availableHooks as $key => $hook) {
            unset($hook);

            $item = [];
            $item['id'] = $key;
            $item['name'] = $key;
            $list_hooks[] = $item;
        }

        return [
            'form' => [
                'tabs' => [
                    'basic' => $this->l('Basic configuration'),
                    'debug_mode' => $this->l('Debug mode'),
                ],
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'tab' => 'basic',
                        'col' => 6,
                        'type' => 'text',
                        'label' => $this->l('Google Maps Api Key'),
                        'name' => 'LGGOOGLEREVIEWS_APIKEY',
                        'desc' => $this->l('Add your Google Maps Api Key to allow request data to Google Places'),
                    ],
                    [
                        'tab' => 'basic',
                        'col' => 6,
                        'type' => 'select',
                        'label' => $this->l('Display in hook'),
                        'name' => 'LGGOOGLEREVIEWS_HOOK',
                        'options' => [
                            'query' => $list_hooks,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                        'desc' => $this->l('Select position to display'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getMenu()
    {
        $tab = Tools::getValue('tab_lg');
        $tab_link = $this->context->link->getAdminLink('AdminModules', true)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&tab_lg=';

        $menu = [];
        $menu[] = [
            'label' => $this->l('Google Reviews'),
            'link' => $tab_link . 'settings',
            'active' => ($tab == 'settings' || empty($tab) ? 1 : 0),
        ];
        $menu[] = [
            'label' => $this->l('Places'),
            'link' => $tab_link . 'places',
            'active' => ($tab == 'places' ? 1 : 0),
        ];
        $menu[] = [
            'label' => $this->l('Help'),
            'link' => $tab_link . 'help',
            'active' => ($tab == 'help' ? 1 : 0),
        ];

        if (Configuration::get('LGGOOGLEREVIEWS_LOGGING')) {
            $menu[] = [
                'label' => $this->l('Debug'),
                'link' => $tab_link . 'debug',
                'active' => ($tab == 'debug' ? 1 : 0),
            ];
        }

        return $menu;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'LGGOOGLEREVIEWS_APIKEY' => Configuration::get('LGGOOGLEREVIEWS_APIKEY'),
            'LGGOOGLEREVIEWS_LOGGING' => Configuration::get('LGGOOGLEREVIEWS_LOGGING'),
            'LGGOOGLEREVIEWS_HOOK' => Configuration::get('LGGOOGLEREVIEWS_HOOK'),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (Tools::isSubmit('submitLggooglereviews')) {
            $form_values = $this->getConfigFormValues();

            foreach (array_keys($form_values) as $key) {
                Configuration::updateValue($key, Tools::getValue($key));
                $this->context->smarty->assign('show_message', 1);
            }

            if ((int) Tools::getValue('LGGOOGLEREVIEWS_LOGGING') == 0) {
                LggooglereviewsLog::cleanDebug(true);
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            $this->context->controller->addCSS($this->_path . 'views/css/publi/lgpubli.css');

            LggooglereviewsLog::cleanDebug();
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookDisplayHeader()
    {
        // php_self: "category"
        if (!$this->context->controller instanceof CategoryController) {
            if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
                $this->context->controller->addJQuery();
            }

            $this->getMedia();
        }

        // if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
        //     return ($this->display(__FILE__, '/views/templates/front/header.tpl'));
        // }
    }

    public function getMedia()
    {
        $path = LggooglereviewsTools::getMediaBasePath($this);
        LggooglereviewsTools::addJS($path . 'views/js/store_widget.js', 'lgggoglereviews_store_widget_js');
        LggooglereviewsTools::addCSS(
            $path . 'views/css/store_widget.css',
            'lgggoglereviews_store_widget_Css',
            $this->context
        );

        $this->context->controller->addCSS([$this->_path . 'views/css/front.css', 'all']);

        LggooglereviewsTools::addJS($path . 'views/js/front.js');
        LggooglereviewsTools::addCSS($path . 'views/css/owl.carousel.min.css', 'owl.carousel');
        LggooglereviewsTools::addCSS($path . 'views/css/owl.theme.default.css', 'owl.theme.default');
        LggooglereviewsTools::addJS($path . 'views/js/owl.carousel.min.js', 'owl.carousel');
        LggooglereviewsTools::addCSS($path . 'views/css/jquery.lgslider.css', 'jquery.lgslider');
    }

    public function getAvailableHooks()
    {
        $methods = get_class_methods(__CLASS__);
        $methods_to_exclude = ['hookDisplayBackOfficeHeader' => 0, 'hookDisplayHeader' => 0];

        if ($this->is_17) {
            // Some hooks are not available in 1.7
            $methods_to_exclude['hookDisplayMyAccountBlockFooter'] = 0;
            $methods_to_exclude['hookDisplayNav'] = 0;
            $methods_to_exclude['hookDisplayPayment'] = 0;
            $methods_to_exclude['hookDisplayProductComparison'] = 0;
            $methods_to_exclude['hookDisplayProductTab'] = 0;
            $methods_to_exclude['hookDisplayProductTabContent'] = 0;
            $methods_to_exclude['hookDisplayTopColumn'] = 0;
        } else {
            $methods_to_exclude['displayWrapperTop'] = 0;
            $methods_to_exclude['hookDisplayNav1'] = 0;
            $methods_to_exclude['hookDisplayNav2'] = 0;
            $methods_to_exclude['hookDisplayNavFullWidth'] = 0;
            $methods_to_exclude['hookDisplayFooterBefore'] = 0;
            $methods_to_exclude['hookDisplayFooterAfter'] = 0;
        }

        $available_hooks = [];
        foreach ($methods as $m) {
            if (Tools::substr($m, 0, 11) === 'hookDisplay' && !isset($methods_to_exclude[$m])) {
                $available_hooks[str_replace('hookDisplay', 'display', $m)] = 0;
                $available_hooks['customLGGoogleReviews'] = 0;
            }
        }
        ksort($available_hooks);
        return $available_hooks;
    }

    public function displayNativeHook($hook_name)
    {
        $display_hook = Configuration::get('LGGOOGLEREVIEWS_HOOK');

        $context = Context::getContext();

        if ($hook_name == $display_hook) {
            $lang = Context::getContext()->language->iso_code;

            $list = LggooglereviewsPlace::getlist();

            $reviews = [];

            foreach ($list as $place) {
                $placeObj = new LggooglereviewsPlace((int) $place['id_lggooglereviews_place']);
                $reviews_place = LggooglereviewsTools::getReviews($placeObj, $lang, 1);

                if ($reviews_place['status'] == 'success') {
                    $reviews[$place['google_place_id']] = $place;
                    $reviews[$place['google_place_id']]['api'] = $reviews_place['result'];

                    $image = _PS_TMP_IMG_DIR_ . $this->name . '_' . $place['id_lggooglereviews_place'] . '.' .
                        $this->imageType;
                    $image_size = file_exists($image) ? filesize($image) / 1000 : false;
                    if ($image_size) {
                        $this_path_ssl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') .
                            htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') .
                            __PS_BASE_URI__;

                        $image_url = $this_path_ssl . 'img/tmp/' . $this->name . '_80_' .
                            (int) $place['id_lggooglereviews_place'] . '.' . $this->imageType;
                        $reviews[$place['google_place_id']]['logo'] = $image_url;
                    } else {
                        $reviews[$place['google_place_id']]['logo'] = '';
                    }
                }
            }

            $this->context->smarty->assign([
                'url_img' => $this->_path . 'views' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR,
                'reviews' => $reviews,
                'hook_name' => $hook_name,
                'dateformat' => LggooglereviewsTools::getDateFormat(),
                'module_name' => $this->name,
                'is_17' => $this->is_17,
            ]);

            // return $this->display(__FILE__, '/views/templates/hook/reviews.tpl');
            return $context->smarty->fetch($this->getTemplatePath('views/templates/hook/reviews.tpl'));
        }
    }

    public function hookDisplayFooter()
    {
        return $this->displayNativeHook('displayFooter');
    }

    public function hookDisplayHome()
    {
        return $this->displayNativeHook('displayHome');
    }

    public function hookDisplayHomeBottom()
    {
        return $this->displayNativeHook('displayHomeBottom');
    }

    public function hookDisplayLeftColumn()
    {
        return $this->displayNativeHook('displayLeftColumn');
    }

    public function hookDisplayRightColumn()
    {
        return $this->displayNativeHook('displayRightColumn');
    }

    public function hookDisplayFooterAfter()
    {
        return $this->displayNativeHook('displayFooterAfter');
    }

    public function hookDisplayFooterBefore()
    {
        return $this->displayNativeHook('displayFooterBefore');
    }

    public function customLGGoogleReviews()
    {
        return $this->displayNativeHook('customLGGoogleReviews');
    }
}
