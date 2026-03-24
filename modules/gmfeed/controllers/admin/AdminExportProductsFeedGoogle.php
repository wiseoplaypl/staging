<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2023 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
include_once('../modules/gmfeed/model/google.php');
include_once('../modules/gmfeed/model/gms.php');
include_once('../modules/gmfeed/gmfeed.php');

class AdminExportProductsFeedGoogleController extends ModuleAdminController
{
    public $available_fields;
    public $id_country_default;
    public $default_carrier;
    public $all_carriers;

    public function __construct()
    {

        $this->all_carriers = Carrier::getCarriers(Tools::getValue('export_language', Configuration::get('PS_LANG_DEFAULT')), true, false, false, null, Carrier::ALL_CARRIERS);
        $this->default_carrier = new Carrier((int)Configuration::get('PS_CARRIER_DEFAULT'));
        $this->id_country_default = Configuration::get('PS_COUNTRY_DEFAULT');
        $this->taxonomyFiles = array(
            'Argentina' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.es-ES.txt',
            'Australia' => 'http://www.google.com/basepages/producttype/taxonomy-with-ids.en-AU.txt',
            'Austria' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.de-DE.txt',
            'Belgium French' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.fr-FR.txt',
            'Belgium Dutch' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.nl-NL.txt',
            'Brazil' => 'http://www.google.com/basepages/producttype/taxonomy-with-ids.pt-BR.txt',
            'Canada English' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt',
            'Canada French' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.fr-FR.txt',
            'Chile' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.es-ES.txt',
            'Colombia' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.es-ES.txt',
            'Czechia' => 'http://www.google.com/basepages/producttype/taxonomy-with-ids.cs-CZ.txt',
            'Denmark' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.da-DK.txt',
            'France' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.fr-FR.txt',
            'Germany' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.de-DE.txt',
            'Ireland' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-GB.txt',
            'Italy' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.it-IT.txt',
            'Japan' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.ja-JP.txt',
            'Malaysia' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt',
            'Mexico' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.es-ES.txt',
            'Netherlands' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.nl-NL.txt',
            'New Zealand' => 'http://www.google.com/basepages/producttype/taxonomy-with-ids.en-AU.txt',
            'Philippines' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt',
            'Poland' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.pl-PL.txt',
            'Portugal' => 'http://www.google.com/basepages/producttype/taxonomy-with-ids.pt-BR.txt',
            'Russia' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.ru-RU.txt',
            'Singapore' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt',
            'South Africa' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt',
            'Spain' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.es-ES.txt',
            'Sweden' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.sv-SE.txt',
            'Switzerland French' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.fr-CH.txt',
            'Switzerland German' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.de-CH.txt',
            'Switzerland Italian' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.it-CH.txt',
            'Turkey' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.tr-TR.txt',
            'United Kingdom' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-GB.txt',
            'United States' => 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt'
        );

        $this->bootstrap = true;
        parent::__construct();
        $this->meta_title = $this->l('Export Products');

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
        $this->available_fields['combinations'] = array(
            'id' => array('label' => 'ID'),
            'gtin' => array('label' => 'GTIN'),
            'identifier_exists' => array('label' => 'Identifier_exists'),
            'name' => array('label' => 'Title'),
            'quantity' => array('label' => 'availability'),
            'availability_date' => array('label' => 'availability_date'),
            'inventory' => array('label' => 'Inventory'),
            'brand' => array('label' => 'Brand'),
            'include_url' => array('label' => 'Link'),
            'image_url' => array('label' => 'Image link'),
            'additional_image_link' => array('label' => 'Additional_image_link'),
            'item_subtitle' => array('label' => 'Item subtitle'),
            'description_short' => array('label' => 'Description'),
            'item_category' => array('label' => 'product_type'),
            'condition' => array('label' => 'Condition'),
            'price_tex' => array('label' => 'Price'),
            'price_tin' => array('label' => 'Price'),
            'sale_price_tin' => array('label' => 'Sale Price'),
            'sale_price_tex' => array('label' => 'Sale Price'),
            //'vat' => array('label' => 'tax rate'),
            'contextual_keywords' => array('label' => 'Contextual keywords'),
            'google_product_category' => array('label' => 'Google product category'),
            'item_group_id' => array('label' => 'item_group_id'),
            'gender' => array('label' => 'gender'),
            'age_group' => array('label' => 'age group'),
            'size' => array('label' => 'size'),
            'color' => array('label' => 'color'),
            'store_code' => array('label' => 'Store code'),
        );
        $this->available_fields['products'] = array(
            'id' => array('label' => 'ID'),
            'gtin' => array('label' => 'GTIN'),
            'identifier_exists' => array('label' => 'Identifier_exists'),
            'name' => array('label' => 'Title'),
            'quantity' => array('label' => 'availability'),
            'availability_date' => array('label' => 'availability_date'),
            'inventory' => array('label' => 'Inventory'),
            'brand' => array('label' => 'Brand name'),
            'include_url' => array('label' => 'Link'),
            'image_url' => array('label' => 'Image URL'),
            'additional_image_link' => array('label' => 'Additional_image_link'),
            'item_subtitle' => array('label' => 'Item subtitle'),
            'description_short' => array('label' => 'Description'),
            'item_category' => array('label' => 'product_type'),
            'condition' => array('label' => 'Condition'),
            'price_tin' => array('label' => 'Price'),
            'price_tex' => array('label' => 'Price'),
            'unit_price' => array('label' => 'Unit price'),
            'sale_price_tin' => array('label' => 'Sale Price'),
            'sale_price_tex' => array('label' => 'Sale Price'),
            //'vat' => array('label' => 'vat'),
            'contextual_keywords' => array('label' => 'Contextual keywords'),
            'google_product_category' => array('label' => 'Google_product_category'),
            'item_group_id' => array('label' => 'item_group_id'),
            'gender' => array('label' => 'gender'),
            'age_group' => array('label' => 'age group'),
            'size' => array('label' => 'size'),
            'color' => array('label' => 'color'),
            'store_code' => array('label' => 'Store code'),
        );
    }

    public static function getAllCategoriesName($root_category = null, $id_lang = false, $active = true, $groups = null,
                                                $use_shop_restriction = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
    {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        if (isset($groups) && Group::isFeatureActive() && !is_array($groups)) {
            $groups = (array)$groups;
        }

        $cache_id = 'Category::getAllCategoriesName_' . md5((int)$root_category . (int)$id_lang . (int)$active . (int)$use_shop_restriction
                . (isset($groups) && Group::isFeatureActive() ? implode('', $groups) : ''));

        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('
				SELECT c.id_category, cl.name
				FROM `' . _DB_PREFIX_ . 'category` c
				' . ($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
				' . (isset($groups) && Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg ON c.`id_category` = cg.`id_category`' : '') . '
				' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int)$root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
				WHERE 1 ' . $sql_filter . ' ' . ($id_lang ? 'AND `id_lang` = ' . (int)$id_lang : '') . '
				' . ($active ? ' AND c.`active` = 1' : '') . '
				' . (isset($groups) && Group::isFeatureActive() ? ' AND cg.`id_group` IN (' . implode(',', $groups) . ')' : '') . '
				' . (!$id_lang || (isset($groups) && Group::isFeatureActive()) ? ' GROUP BY c.`id_category`' : '') . '
				' . ($sql_sort != '' ? $sql_sort : ' ORDER BY c.`level_depth` ASC') . '
				' . ($sql_sort == '' && $use_shop_restriction ? ', category_shop.`position` ASC' : '') . '
				' . ($sql_limit != '' ? $sql_limit : '')
            );

            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        return $result;
    }

    public function ajaxProcess()
    {
        if (Tools::getValue('action') == 'downloadCategories' && Tools::getValue('language_code', 'false') != 'false') {
            $language = explode("-", Tools::getValue('language_code'));
            if (count($language) > 1) {
                $language_code_first = $language[0];
                $language_code_second = $language[1];
            } else {
                $language_code_first = $language[0];
                $language_code_second = $language[0];
            }

            $download = Tools::file_get_contents('https://www.google.com/basepages/producttype/taxonomy-with-ids.' . $language_code_first . '-' . strtoupper($language_code_second) . '.txt');
            if ((bool)$download == 0) {
                $download = Tools::file_get_contents('https://www.google.com/basepages/producttype/taxonomy-with-ids.' . $language_code_second . '-' . strtoupper($language_code_second) . '.txt');
                if ((bool)$download == 0) {
                    $download = Tools::file_get_contents('https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt');
                }
            }

            if (file_exists('../modules/gmfeed/google/' . Tools::getValue('language_code') . '.txt') && (bool)$download != 0) {
                @unlink('../modules/gmfeed/google/' . Tools::getValue('language_code') . '.txt');
            }

            if ((bool)$download != 0 && @file_put_contents('../modules/gmfeed/google/' . Tools::getValue('language_code') . '.txt', $download)) {
                echo 1;
            } else {
                echo 0;
            }
            die();
        }

        if (Tools::getValue('action') == 'downloadCategoriesCustom' && Tools::getValue('language_code', 'false') != 'false') {

            $download = Tools::file_get_contents($this->taxonomyFiles[Tools::getValue('selected_option')]);

            if (file_exists('../modules/gmfeed/google/' . Tools::getValue('language_code') . '.txt') && (bool)$download != 0) {
                @unlink('../modules/gmfeed/google/' . Tools::getValue('language_code') . '.txt');
            }

            if ((bool)$download != 0 && @file_put_contents('../modules/gmfeed/google/' . Tools::getValue('language_code') . '.txt', $download)) {
                echo 1;
            } else {
                echo 0;
            }
            die();
        } elseif (Tools::getValue('action') == 'googleCategories') {
            $categories = array();
            $id_language = $this->context->language->id;
            foreach (Language::getLanguages(true) AS $language) {
                if (strtolower($language['language_code']) == strtolower(Tools::getValue('language_code'))) {
                    $id_language = $language['id_lang'];
                }
            }

            $google_categories = pixelgoogle::getAllByLanguage($id_language);

            foreach ($this::getAllCategoriesName(null, $id_language, false) AS $category) {
                $categories[$category['id_category']]['name'] = $category['name'];
                $categories[$category['id_category']]['id'] = $category['id_category'];
                if (Configuration::get('pf_tree')) {
                    $cat = new Category($category['id_category']);
                    $categories[$category['id_category']]['parents'] = array_reverse($cat->getParentsCategories());
                }
            }

            $this->context->smarty->assign('gmfeed_categories', $categories);
            $this->context->smarty->assign('gmfeed_google_categories', $google_categories);

            echo $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'gmfeed/views/script-fancybox.tpl') . $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'gmfeed/views/categories-fancybox.tpl');
            die();
        } elseif (Tools::getValue('action') == 'searchCategory') {
            $contents = Tools::file_get_contents('../modules/gmfeed/google/' . Tools::getValue('language_code') . '.txt');
            $pattern = preg_quote(Tools::getValue('q'), '/');
            $pattern = "/^.*$pattern.*\$/mi";
            if (preg_match_all($pattern, $contents, $matches)) {
                echo implode("\n", $matches[0]);
            } else {
                echo $this->l('No matches found');
            }
            die();
        } elseif (Tools::getValue('action') == 'saveCategories') {
            foreach (Language::getLanguages(true) AS $language) {
                if (strtolower($language['language_code']) == strtolower(Tools::getValue('language_code'))) {
                    $id_language = $language['id_lang'];
                }
            }
            $id_category = Tools::getValue('id_category');
            $id_google = preg_replace("/[^0-9][0-9A-Z]/", '', Tools::getValue('value'));

            $pg = new pixelgoogle(((int)Tools::getValue('id_association') > 0 ? Tools::getValue('id_association') : pixelgoogle::getByDetails(Tools::getValue('id_category'), $id_google, $id_language)));

            $pg->id_category = $id_category;
            $pg->id_google = $id_google;
            $pg->id_lang = $id_language;
            $pg->value = Tools::getValue('value');
            $pg->save();
        } elseif (Tools::getValue('action') == 'deleteCategories') {
            foreach (Language::getLanguages(true) AS $language) {
                if (strtolower($language['language_code']) == strtolower(Tools::getValue('language_code'))) {
                    $id_language = $language['id_lang'];
                }
            }

            $pg = new pixelgoogle(((int)Tools::getValue('id_association') > 0 ? Tools::getValue('id_association') : pixelgoogle::getByDetails(Tools::getValue('id_category'), null, $id_language)));
            $pg->delete();
        }
    }

    public function renderView()
    {
        return $this->renderConfigurationForm();
    }

    public function renderConfigurationForm()
    {
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $langs = Language::getLanguages();
        $id_shop = (int)$this->context->shop->id;
        $options_images = ImageType::getImagesTypes('products');

        foreach ($langs as $key => $language) {
            $options[] = array(
                'id_option' => $language['id_lang'],
                'name' => $language['name']
            );
        }

        $cats = $this->getCategories($lang->id, true, $id_shop);

        $pricetax = array(
            array(
                'id_option' => 'price_tin',
                'name' => 'Price Tax Included'
            ),
            array(
                'id_option' => 'price_tex',
                'name' => 'Price Tax Excluded'
            )
        );

        $yesno = array(
            array(
                'id_option' => '0',
                'name' => $this->l('No'),
            ),
            array(
                'id_option' => '1',
                'name' => $this->l('Yes'),
            )
        );

        $categories[] = array(
            'id_option' => 99999,
            'name' => 'All'
        );

        foreach ($cats as $key => $cat) {
            $categories[] = array(
                'id_option' => $cat['id_category'],
                'name' => $cat['name']
            );
        }

        $manufacturers[] = array(
            'id_option' => 99999,
            'name' => 'All'
        );
        foreach (Manufacturer::getManufacturers(false, $this->context->language->id, false) as $key => $man) {
            $manufacturers[] = array(
                'id_option' => $man['id_manufacturer'],
                'name' => $man['name']
            );
        }

        $suppliers[] = array(
            'id_option' => 99999,
            'name' => 'All'
        );
        foreach (Supplier::getSuppliers(false, $this->context->language->id, false) as $key => $man) {
            $suppliers[] = array(
                'id_option' => $man['id_supplier'],
                'name' => $man['name']
            );
        }

        $export_shipping_info = array(
            array(
                'id_option' => '0',
                'name' => $this->l('Do not include')
            ),
            array(
                'id_option' => '1',
                'name' => $this->l('Include (shipping price calculated for each item separately)')
            ),
            array(
                'id_option' => '2',
                'name' => $this->l('Set shipping price manually')
            ),
        );

        $export_agegroup_values = array(
            array(
                'id_option' => 'newborn',
                'name' => $this->l('newborn (Up to 3 months old)')
            ),
            array(
                'id_option' => 'infant',
                'name' => $this->l('infant (3–12 months old)')
            ),
            array(
                'id_option' => 'toddler',
                'name' => $this->l('toddler (1–5 years old)')
            ),
            array(
                'id_option' => 'kids',
                'name' => $this->l('kids (5–13 years old)')
            ),
            array(
                'id_option' => 'adult',
                'name' => $this->l('adult (Typically teens or older)')
            ),
        );

        $export_gender_values = array(
            array(
                'id_option' => 'male',
                'name' => $this->l('male')
            ),
            array(
                'id_option' => 'female',
                'name' => $this->l('female')
            ),
            array(
                'id_option' => 'unisex',
                'name' => $this->l('unisex')
            ),
        );

        $export_id_product = array(
            array(
                'id_option' => 'id_product',
                'name' => $this->l('id_product')
            ),
            array(
                'id_option' => 'id_combination',
                'name' => $this->l('id_combination')
            ),
            array(
                'id_option' => 'id_product_id_combination',
                'name' => $this->l('id_product-id_attribute')
            ),
        );

        $what_to_export = array(
            array(
                'id_option' => 'products',
                'name' => $this->l('Products')
            ),
            array(
                'id_option' => 'combinations',
                'name' => $this->l('All products variants')
            ),
        );
        $export_file_format = array(
            array(
                'id_option' => 'csv',
                'name' => $this->l('CSV')
            ),
            array(
                'id_option' => 'xml',
                'name' => $this->l('XML')
            ),
        );

        $inputs = array(
            array(
                'type' => 'select',
                'label' => $this->l('Remember settings'),
                'desc' => $this->l('Remember settings of form'),
                'name' => 'remember_settings',
                'class' => 't',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),

            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('What you want to export?') . '</h2><hr/>',
            ),

            array(
                'type' => 'select',
                'label' => $this->l('Type of file module will generate'),
                'desc' => $this->l('Module allows to generate CSV or XML file. Here you can decide what format of file module will create. Suggested: lightweight CSV'),
                'name' => 'export_file_format',
                'class' => 't export_format',
                'value' => 'csv',
                'options' => array(
                    'query' => $export_file_format,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('What you want to export?'),
                'name' => 'export_type',
                'class' => 't export_type',
                'value' => 'products',
                'options' => array(
                    'query' => $what_to_export,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Save to file'),
                'desc' => $this->l('Decide if you want to save feed to file or download it immediately once feed is generated') .
                    '<br/>' .
                    $this->l('Option when active will save feed to file located in module directory:') . '<span class="savedFeedUrl" class="font-weight:bold">' . Context::getContext()->shop->getBaseURL(true, true) . 'modules/gmfeed/</span><span class="SavedFeedUrlFile"></span><br/>' .
                    '<div class="alert alert-warning">' . $this->l('This option is applicable to feed generated by url.') . '</div>',
                'name' => 'export_save_file',
                'class' => 't',
                'value' => 'csv',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('How to identify product?'),
                'name' => 'export_identification',
                'options' => array(
                    'query' => $export_id_product,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'desc' => $this->l('It is an unique identification number of product in shop\'s catalog. It will be introduced to feed as an unique ID of product')
            ),


            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Gender, color, age_group, size') . '</h2><hr/>',
            ),
            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h4>' . $this->l('Size') . '</h4>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export "size"'),
                'desc' => $this->l('Select this option if you want to include "size" field to feed'),
                'name' => 'export_size',
                'class' => 't export_size',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('"Size" attribute'),
                'desc' => $this->l('Select product\'s attribute that stores information about "size"') . '. <a target="_blank" href="' . Context::getContext()->link->getAdminLink('AdminAttributesGroups', true) . '">' . $this->l('Manage your shop\'s attributes here') . '</a>' . ' ' . $this->l('You can select multiple values press ctrl and click on attribute to select many values'),
                'name' => 'export_size_attribute',
                'class' => 't',
                'multiple' => true,
                'options' => array(
                    'query' => array_merge(array(array('id_attribute_group' => 0, 'name' => $this->l(' -- none --'))), AttributeGroup::getAttributesGroups(Context::getContext()->language->id)),
                    'id' => 'id_attribute_group',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Default "size" value'),
                'desc' => $this->l('If your product will not have a "size" attribute module will use value defined here'),
                'name' => 'export_size_default',
                'class' => 't',
            ),

            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<strong><h4>' . $this->l('Gender') . '</h4></strong><hr />',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export "gender"'),
                'desc' => $this->l('Select this option if you want to include "gender" field to feed'),
                'name' => 'export_gender',
                'class' => 't export_gender',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export "gender" value from'),
                'desc' => $this->l('Select attribute or feature to export "Gender" information'),
                'name' => 'export_gender_type',
                'class' => 't export_gender_type',
                'options' => array(
                    'query' => array(array('name' => $this->l('Attribute'), 'id_option' => 'attribute'), array('name' => $this->l('Feature'), 'id_option' => 'feature')),
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('"Gender" feature'),
                'desc' => $this->l('Select product\'s feature that stores information about "Gender"') . '. <a target="_blank" href="' . Context::getContext()->link->getAdminLink('AdminFeatures', true) . '">' . $this->l('Manage your shop\'s features here') . '</a>',
                'name' => 'export_gender_feature',
                'class' => 't',
                'options' => array(
                    'query' => Feature::getFeatures(Context::getContext()->language->id, true),
                    'id' => 'id_feature',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('"Gender" attribute'),
                'desc' => $this->l('Select product\'s attribute that stores information about "Gender"') . '. <a target="_blank" href="' . Context::getContext()->link->getAdminLink('AdminAttributesGroups', true) . '">' . $this->l('Manage your shop\'s attributes here') . '</a>',
                'name' => 'export_gender_attribute',
                'class' => 't',
                'options' => array(
                    'query' => AttributeGroup::getAttributesGroups(Context::getContext()->language->id),
                    'id' => 'id_attribute_group',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Default "gender" value'),
                'desc' => $this->l('If your product will not have a "gender" feature, module will use value defined here') . '',
                'name' => 'export_gender_default',
                'class' => 't',
                'options' => array(
                    'query' => array(
                        array('name' => $this->l('Male'), 'id_option' => 'Male'),
                        array('name' => $this->l('Female'), 'id_option' => 'Female'),
                        array('name' => $this->l('Unisex'), 'id_option' => 'Unisex')),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<strong><h4>' . $this->l('Color') . '</h4></strong><hr/>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export "color"'),
                'desc' => $this->l('Select this option if you want to include "color" field to feed'),
                'name' => 'export_color',
                'class' => 't export_color',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export "color" value from'),
                'desc' => $this->l('Select attribute or feature to export color information'),
                'name' => 'export_color_type',
                'class' => 't export_color_type',
                'options' => array(
                    'query' => array(array('name' => $this->l('Attribute'), 'id_option' => 'attribute'), array('name' => $this->l('Feature'), 'id_option' => 'feature')),
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('"Color" attribute'),
                'desc' => $this->l('Select product\'s attribute that stores information about "color"') . '. <a target="_blank" href="' . Context::getContext()->link->getAdminLink('AdminAttributesGroups', true) . '">' . $this->l('Manage your shop\'s attributes here') . '</a>',
                'name' => 'export_color_attribute',
                'class' => 't',
                'options' => array(
                    'query' => AttributeGroup::getAttributesGroups(Context::getContext()->language->id),
                    'id' => 'id_attribute_group',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('"Color" feature'),
                'desc' => $this->l('Select product\'s feature that stores information about "color"') . '. <a target="_blank" href="' . Context::getContext()->link->getAdminLink('AdminFeatures', true) . '">' . $this->l('Manage your shop\'s features here') . '</a>',
                'name' => 'export_color_feature',
                'class' => 't',
                'options' => array(
                    'query' => Feature::getFeatures(Context::getContext()->language->id, true),
                    'id' => 'id_feature',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Default "color" value'),
                'desc' => $this->l('If your product will not have a "color" feature or "color" attribute module will use value defined here')
                    . ' <span class="label label-info">' . $this->l('Please note that Google accepts only english names of colors') . '</span>'
                    . '',
                'name' => 'export_color_default',
                'class' => 't',
            ),
            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<strong><h4>' . $this->l('Age group') . '</h4></strong><hr/>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export age_group'),
                'desc' => $this->l('Select this option if you want to include "age_group" field to feed'),
                'name' => 'export_age_group',
                'class' => 't export_age_group',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export "age group" value from'),
                'desc' => $this->l('Select attribute or feature to export "age group" information'),
                'name' => 'export_age_group_type',
                'class' => 't export_color_type',
                'options' => array(
                    'query' => array(array('name' => $this->l('Attribute'), 'id_option' => 'attribute'), array('name' => $this->l('Feature'), 'id_option' => 'feature')),
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('"age group" feature'),
                'desc' => $this->l('Select product\'s feature that stores information about "age group"') . '. <a target="_blank" href="' . Context::getContext()->link->getAdminLink('AdminFeatures', true) . '">' . $this->l('Manage your shop\'s features here') . '</a>',
                'name' => 'export_age_group_feature',
                'class' => 't',
                'options' => array(
                    'query' => Feature::getFeatures(Context::getContext()->language->id, true),
                    'id' => 'id_feature',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('"age group" attribute'),
                'desc' => $this->l('Select product\'s attribute that stores information about "age group"') . '. <a target="_blank" href="' . Context::getContext()->link->getAdminLink('AdminAttributesGroups', true) . '">' . $this->l('Manage your shop\'s attributes here') . '</a>',
                'name' => 'export_age_group_attribute',
                'class' => 't',
                'options' => array(
                    'query' => AttributeGroup::getAttributesGroups(Context::getContext()->language->id),
                    'id' => 'id_attribute_group',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Default "age group" value'),
                'desc' => $this->l('If your product will not have a "age group" feature, module will use value defined here') . '',
                'name' => 'export_age_group_default',
                'class' => 't',
                'options' => array(
                    'query' => array(
                        array('name' => $this->l('adult'), 'id_option' => 'adult'),
                        array('name' => $this->l('kids'), 'id_option' => 'kids'),
                        array('name' => $this->l('toddler'), 'id_option' => 'toddler'),
                        array('name' => $this->l('infant'), 'id_option' => 'infant'),
                        array('name' => $this->l('newbord'), 'id_option' => 'newbord'),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),


            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Feed settings') . '</h2><hr/>',
            ),


            array(
                'type' => 'select',
                'label' => $this->l('Include item_group_id'),
                'name' => 'export_igid',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'desc' => $this->l('Use the item_group_id attribute to group product variants in your product data. Variants are a group of similar items that only differ from one another by product details like size, color, material, pattern, age_group, and gender. When you use an item_group_id to group your products, you can ensure that your product and its variants are shown to users as a group, instead of separately.')
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Unique product identifier'),
                'desc' => $this->l('Selected order of fields: ') . '<span id="gtin_details" style="font-weight:bold;"></span>. ' . $this->l('If first field will not exist, module will try to use second one. If second will not exist too - module will include information that product does not have unique product identifier') . '<br/><br/> ' . $this->l('Unique product identifiers (UPI) define the product you\'re selling in the global marketplace. They uniquely distinguish products you are selling and help match search queries with your offers. Unique product identifiers are assigned to each product by the manufacturer, so if you sell the same product as another retailer, the UPIs will be identical') . ' '
                    . $this->l('Common unique product identifiers include Global Trade Item Numbers (GTINs), Manufacturer Part Numbers (MPNs), and brand names. Not all products have unique product identifiers. However, if your product does have one, especially a GTIN, providing it can help make your ads richer and easier for users to find. If your product doesn\'t have a UPI, module will inform about it in feed'),
                'name' => 'export_gtin',
                'values' => array(
                    array(
                        'id' => 'upc',
                        'value' => 'upc',
                        'label' => $this->l('Product UPC barcode')
                    ),
                    array(
                        'id' => 'ean13',
                        'value' => 'ean13',
                        'label' => $this->l('Product ean13 / JAN barcode')
                    ),
                    array(
                        'id' => 'reference',
                        'value' => 'reference',
                        'label' => $this->l('Product reference number')
                    ),
                    array(
                        'id' => 'nothing',
                        'value' => 'nothing',
                        'label' => $this->l('Make this field empty, identifier_exists field will have value "false" then')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Include identifier_exists field'),
                'name' => 'export_ie',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'desc' => $this->l('Option when active will include identifier_exists parameter to feed'),
            ),
            /**
             * array(
             * 'type' => 'select',
             * 'label' => $this->l('Include gender field'),
             * 'desc' => $this->l('Specify the gender of products in this feed. When you provide this information, potential customers can accurately filter products by gender to help narrow their search. Keep in mind that google also use the gender information together with the values you provide for size and age_group to standardize the sizes that are shown to users. Required for enhanced listings on surfaces across Google for all Apparel & Accessories (166) products. Required for Shopping ads for all Apparel & Accessories (166) products when targeting these countries: Brazil, France, Germany, Japan, The United Kingdom, The United States'),
             * 'name' => 'export_gender',
             * 'class' => 't',
             * 'options' => array(
             * 'query' => $yesno,
             * 'id' => 'id_option',
             * 'name' => 'name'
             * )
             * ),
             * array(
             * 'type' => 'select',
             * 'label' => $this->l('Set value of gender field of this feed'),
             * 'name' => 'export_gender_values',
             * 'class' => 't',
             * 'options' => array(
             * 'query' => $export_gender_values,
             * 'id' => 'id_option',
             * 'name' => 'name'
             * )
             * ),
             * array(
             * 'type' => 'select',
             * 'label' => $this->l('Include age_group field'),
             * 'desc' => $this->l('When you use this attribute, your product can appear in results that are filtered by age. For example, if results are filtered by Women instead of Girls. The age_group attribute can also work together with the gender attribute to help ensure that users see the correct size information.'),
             * 'name' => 'export_agegroup',
             * 'class' => 't',
             * 'options' => array(
             * 'query' => $yesno,
             * 'id' => 'id_option',
             * 'name' => 'name'
             * )
             * ),
             * array(
             * 'type' => 'select',
             * 'label' => $this->l('Set value of age_group field'),
             * 'name' => 'export_agegroup_values',
             * 'class' => 't',
             * 'options' => array(
             * 'query' => $export_agegroup_values,
             * 'id' => 'id_option',
             * 'name' => 'name'
             * )
             * ),
             **/

            array(
                'type' => 'select',
                'label' => $this->l('Include shipping price'),
                'desc' => $this->l('Select if you want to include shipping price to feed and how you want to define the price of shipping'),
                'name' => 'export_shipping_info',
                'class' => 't',
                'options' => array(
                    'query' => $export_shipping_info,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),

            array(
                'type' => 'select',
                'label' => $this->l('Include product\'s additional shipping cost'),
                'desc' => $this->l('Each product can have own unique value of additional shipping fee. If you will activate this option module will include it to delivery price'),
                'name' => 'export_additional_sc',
                'class' => 't',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Manual shipping value'),
                'desc' => $this->l('Set the value of shipping cost that will be included to each product'),
                'name' => 'export_shipping_info_price',
                'class' => 't',
                'prefix' => '<div id="shipping_currency"></div>'
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Automatic shipping price zone select'),
                'desc' => $this->l('If you selected option to include the shipping price automatically, select the zone. Module will calculate shipping price for selected zone. Please note that automatic price calculation requires more hosting resources than flat value of delivery for each product.'),
                'name' => 'export_shipping_id_zone',
                'class' => 't',
                'options' => array(
                    'query' => Zone::getZones(false, false),
                    'id' => 'id_zone',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Include weight'),
                'desc' => $this->l('Include weight of product to feed'),
                'name' => 'export_product_weight',
                'class' => 't',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product type'),
                'desc' => $this->l('Use the product_type attribute to include your own product categorization system in your product data'),
                'name' => 'export_product_type',
                'class' => 't',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product type - include category ID'),
                'desc' => $this->l('When your will activate own categorization system - you can add ID of category to its name to make each category unique'),
                'name' => 'export_product_type_id',
                'class' => 't',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Short description'),
                'desc' => $this->l('Select what description will included to "short description" field in generated catalog file / feed'),
                'name' => 'export_short_description_what',
                'values' => array(
                    array(
                        'id' => 'short_short',
                        'value' => 'short',
                        'label' => $this->l('Short description')
                    ),
                    array(
                        'id' => 'short_desc',
                        'value' => 'desc',
                        'label' => $this->l('Long description')
                    ),
                ),
                'is_bool' => true,
            ),
            /**
             * array(
             * 'type' => 'radio',
             * 'label' => $this->l('Description'),
             * 'desc' => $this->l('Select what description will included to "description" field in generated catalog file / feed'),
             * 'name' => 'export_description_what',
             * 'values' => array(
             * array(
             * 'id' => 'desc_desc',
             * 'value' => 'desc',
             * 'label' => $this->l('Long description')
             * ),
             * array(
             * 'id' => 'desc_short',
             * 'value' => 'short',
             * 'label' => $this->l('Short description')
             * ),
             * ),
             * 'is_bool' => true,
             * ),
             * **/
            array(
                'type' => 'radio',
                'label' => $this->l('Remove html from descriptions'),
                'desc' => $this->l('Module can export formatted descriptions and also pure description text (without html tags)'),
                'name' => 'export_removehtml',
                'values' => array(
                    array(
                        'id' => 'removehtml_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    ),
                    array(
                        'id' => 'removehtml_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Product name'),
                'desc' => $this->l('Select what product name will be included to "name" field in generated catalog file / feed'),
                'name' => 'export_product_name',
                'values' => array(
                    array(
                        'id' => 'gm_product_name',
                        'value' => 'name',
                        'label' => $this->l('Product name')
                    ),
                    array(
                        'id' => 'gn_meta_title',
                        'value' => 'meta_title',
                        'label' => $this->l('Meta title')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Format name'),
                'desc' => $this->l('If you want to change format of product name - select possible options.') . $this->l('This option is here because Google does not allow to use CAPITALIZED TEXT for product names. So you can reformat the name of products with this option. Do it if your default names of items do not follow Google shopping requirements.'),
                'name' => 'export_product_name_format',
                'values' => array(
                    array(
                        'id' => '0',
                        'value' => '0',
                        'label' => $this->l('- do not format name -')
                    ),
                    array(
                        'id' => '1',
                        'value' => '1',
                        'label' => $this->l('Capitalize first letter, example: "Product name"')
                    ),
                    array(
                        'id' => '2',
                        'value' => '2',
                        'label' => $this->l('Capitalize words, example: "Product Name"')
                    ),
                    array(
                        'id' => '3',
                        'value' => '3',
                        'label' => $this->l('Lowercase all letters, example: "product name"')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Language'),
                'desc' => $this->l('Choose a language you wish to export'),
                'name' => 'export_language',
                'class' => 't',
                'options' => array(
                    'query' => $options,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Delimiter'),
                'name' => 'export_delimiter',
                'value' => ';',
                'desc' => $this->l('The character to separate the fields in CSV file') . '. ' . $this->l('Usually pipe "|" - Google merchant center allows to use it as a column delimiter')
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Information about stock'),
                'name' => 'export_instock_info',
                'values' => array(
                    array(
                        'id' => 'active0',
                        'value' => 0,
                        'label' => $this->l('include real stock information')
                    ),
                    array(
                        'id' => 'active1',
                        'value' => 1,
                        'label' => $this->l('insert "in-stock" info for all products even if some of them are out of stock')
                    ),
                    array(
                        'id' => 'active2',
                        'value' => 2,
                        'label' => $this->l('insert "out-of-stock" info for all products even if some of them are in stock')
                    ),
                    array(
                        'id' => 'active3',
                        'value' => 3,
                        'label' => $this->l('insert "backorder" info for all products even if some of them are out of stock / in stock')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'date',
                'label' => $this->l('Availability date'),
                'name' => 'export_availability_date',
                'value' => ';',
                'desc' => $this->l('Google for products that are available to "backorder" requires availability_date. If your products available to "backorder" will not have own unique availability date you can set it here - module will use this date in feed')
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Include quantity info'),
                'desc' => $this->l('Option when enabled will include "inventory" column to feed with quantity of item/combination'),
                'name' => 'export_inventory',
                'values' => array(
                    array(
                        'id' => 'inventory_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    ),
                    array(
                        'id' => 'inventory_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                ),
                'is_bool' => true,
            ),

            array(
                'type' => 'text',
                'label' => $this->l('Default manufacturer value'),
                'desc' => $this->l('If product will not be associated with any manufacturer - this will be the value of manufacturer field for this product in exported catalog file / feed'),
                'name' => 'export_manufacturers_default',
            ),

            array(
                'type' => 'select',
                'label' => $this->l('Image size for product pictures'),
                'name' => 'export_img',
                'options' => array(
                    'query' => $options_images,
                    'id' => 'name',
                    'name' => 'name'
                )
            ),

            array(
                'type' => 'select',
                'label' => $this->l('Use default product picture if combination will not have associated photo'),
                'name' => 'export_mainpicture',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('How many pictures you want to export?'),
                'name' => 'export_what_pictures',
                'values' => array(
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Export cover only')
                    ),
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Export all pictures')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Currency'),
                'desc' => $this->l('Choose a currency you wish to export'),
                'name' => 'export_currency',
                'class' => 't',
                'options' => array(
                    'query' => Currency::getCurrencies(false, false),
                    'id' => 'id_currency',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Price tax included or excluded'),
                'desc' => $this->l('Choose if you want to export the price with or without tax.'),
                'name' => 'export_tax',
                'class' => 't export_tax',
                'options' => array(
                    'query' => $pricetax,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Include specific prices'),
                'desc' => $this->l('Select this option if you use "specific prices" to setup the different prices of item for various currencies'),
                'name' => 'export_specific',
                'class' => 't export_specific',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),

            array(
                'type' => 'radio',
                'label' => $this->l('Export also unit price'),
                'name' => 'export_unit_price',
                'values' => array(
                    array(
                        'id' => 'active_off',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'active_on',
                        'value' => 0,
                        'label' => $this->l('No')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Export prices for selected country'),
                'desc' => $this->l('In PrestaShop prices are country-related, for example - some countries may show prices tax included or tax excluded, some countries may have discounts, some not. Decide for what country calculate prices'),
                'name' => 'export_country_prices',
                'class' => 't export_country_prices',
                'options' => array(
                    'query' => Country::getCountries($this->context->language->id),
                    'id' => 'id_country',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Export specific products') . '</h2><hr/>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product Manufacturer'),
                'desc' => $this->l('Choose a manufacturer you wish to export'),
                'name' => 'export_manufacturers',
                'class' => 't',
                'options' => array(
                    'query' => $manufacturers,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product Supplier'),
                'desc' => $this->l('Choose a supplier you wish to export'),
                'name' => 'export_suppliers',
                'class' => 't',
                'options' => array(
                    'query' => $suppliers,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Product Category'),
                'desc' => $this->l('Choose a product category you wish to export'),
                'name' => 'export_category',
                'class' => 't',
                'options' => array(
                    'query' => $categories,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),

            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Exclude some products') . '</h2><hr/>',
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Exclude products without pictures'),
                'name' => 'export_nophoto_exclude',
                'values' => array(
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    ),
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Products\'s availability'),
                'name' => 'export_active',
                'values' => array(
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Export all products.')
                    ),
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Export only active products')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Products\'s stock'),
                'name' => 'export_instock',
                'values' => array(
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Export all products.')
                    ),
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Export only in-stock products')
                    ),
                ),
                'is_bool' => true,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Exclude products with specific reference'),
                'desc' => $this->l('This option when active will give you possibility to exclude some products from feed based on reference number.'),
                'name' => 'export_exreference',
                'class' => 't export_exreference',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Exclude product where reference starts with:'),
                'desc' => $this->l('If you activated option to exclude some products based on reference - this option allows to exclude items with reference that starts with defined string. Type it here. Warning! This option does exclude also combinations that starts with this reference'),
                'name' => 'export_exreference_value',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Exclude products worth less than defined value'),
                'desc' => $this->l('This option when active will give you possibility to exclude some products that are worth less than defined value in default currency'),
                'name' => 'export_exworthless',
                'class' => 't export_exworthless',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Value of products (worth less) to exclude'),
                'desc' => $this->l(''),
                'prefix' => $this->context->currency->iso_code,
                'name' => 'export_exworthless_value',
            ),
            array(
                'type' => 'html',
                'label' => $this->l('Exclude products'),
                'name' => 'export_exclude',
                'html_content' => $this->renderExcludeForm(),
            ),
            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Limit number of products in feed') . '</h2><hr/>',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Limit number of products in feed'),
                'prefix' => $this->l('Number'),
                'desc' => $this->l('Set the number of products in the feed. Option is useful for hosting accounts with limited resources (to generate feed with large number of products you need enough resources, if you do not have good hosting account - you can split one large feed into several small parts). If you do not want to create such limit - leave this field empty or fill it with 0') .
                    '. ' . $this->l('Number of products in shop:') . ' <span class="badge badge-alert getAllProductsNb">' . $this->getAllProductsNb() . '</span>' . '<br/>' . '<div class="getAllProductsNbInfo alert alert-info"></div>',
                'name' => 'limit_limit',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Part of feed'),
                'prefix' => $this->l('Number'),
                'desc' => $this->l('Set what part of feed you want to generate. If you do not want to split the feed into small parts, fill this field with 0.') . ' '
                    . '<br/>'
                    . $this->l('Example of usage: If your shop has 3000 products and if you want to generate feeds with max 1000 products, you need to generate 3 feeds:')
                    . '<br/>' . $this->l('limit: ') . '1000' . ' - ' . $this->l('Part:') . '1'
                    . '<br/>' . $this->l('limit: ') . '1000' . ' - ' . $this->l('Part:') . '2'
                    . '<br/>' . $this->l('limit: ') . '1000' . ' - ' . $this->l('Part:') . '3'
                    . '<br/>',
                'name' => 'limit_page',
            ),
            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Local product inventory feed specification') . '</h2>' . $this->l('By participating in local surfaces across Google, your in-store products can appear in unpaid product listings across Google surfaces, including Google Search, Google Images, Google Shopping, Google Maps, and Google Lens. ') . '<hr/>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Include store code to feed'),
                'desc' => $this->l('A unique alphanumeric identifier for each local store. Use the same store codes that you provided in your Google My Business account.'),
                'name' => 'export_storecode',
                'class' => 't export_storecode',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'name' => 'export_sc',
                'type' => 'text',
                'label' => $this->l('Store code'),
                'desc' => $this->l('The store code attribute is case-sensitive and must match the store codes submitted in your Google My Business account.'),
            ),

            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Google categorization of products') . '</h2><hr/>',
            ),
            array(
                'type' => 'html',
                'label' => $this->l('Google Categories'),
                'name' => 'export_google_categories',
                'html_content' => $this->renderGoogleCategories(),
            ),
            array(
                'type' => 'html',
                'name' => '',
                'desc' => '<h2>' . $this->l('Create short link') . '</h2><hr/>',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Create short link'),
                'desc' => $this->l('You can save feed as a shortlink, everyone who will access to this short link will get actual feed of products'),
                'name' => 'export_shortlink',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Name'),
                'desc' => $this->l('Set name of this feed (you will be able to easily distinct this feed from other saved feeds)'),
                'name' => 'export_shortlinkName',
            ),
            array(
                'type' => 'hidden',
                'label' => $this->l('Name'),
                'name' => 'export_id_gms',
            ),
        );


        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Export Options'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save') . ' & ' . $this->l('Export'),
                    'icon' => 'process-icon-download-alt'
                ),
                'buttons' => array(
                    array('type' => 'submit', 'class' => 'pull-right', 'name' => 'saveonly', 'title' => $this->l('Save'), 'icon' => 'process-icon-save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;

        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitExport';
        $helper->currentIndex = self::$currentIndex;
        $helper->token = Tools::getAdminTokenLite('AdminExportProductsFeedGoogle');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $this->heading() . $this->initList() . $this->returnEditInfo() . $helper->generateForm(array($fields_form));
    }

    public function loadScript()
    {
        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('feed_updated', $this->l('URL of product\'s feed updated'));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'gmfeed/views/script.tpl');
    }

    public function heading()
    {
        $shop = new ShopUrl($this->context->shop->id);
        return "
        <div class='alert alert-info'>
        " . $this->l('This form generates a .csv or .xml file that is ready to import for Google Merchant Center "products" purposes.') . "<br/>
        " . $this->l('Define what kind of products\' feed you want to create and press Export button') . "<br/>
        " . $this->l('Module will generate a .csv / .xml file and your browser will download it immediately') . "
        </div>
        
        <div class='alert alert-info'>
            " . $this->l('Optionally you can use an URL - feed of products (feed uses filters defined below in export options form)') . "<br/><br/>
            <button class=\"btn btn-default show-links clearfix\" style=\"margin-bottom:20px;\"><i class=\"process-icon-ok\"></i>" . $this->l('Show links') . "</button>        
            <div class='hide panel clearfix'>
                <h3>" . $this->l('Secured URL') . "</h3>
                <div><span> " . $shop->getUrl(true) . "modules/gmfeed/feed.php?</span><span class='feedurl' style='word-wrap: break-word;'></span></div><br/><hr/><br/>
                <h3>" . $this->l('Non-secured URL') . "</h3>
                <div><span> " . $shop->getUrl(false) . "modules/gmfeed/feed.php?</span><span class='feedurl' style='word-wrap: break-word;'></span></div>    
            </div>
        </div>
        <div class='panel'>
            <h3>" . $this->l('Configure form from URL') . "</h3>
            <div class='alert alert-info'>
            " . $this->l('This form allows to automatically configure previously selected options based on url of feed. Just paste the feed here and module will automatically select all fields in configuration form') . "
            </div>
            <input type='text' class='deserializeUrl' value='" . (Tools::isSubmit('updategms') && !Tools::isSubmit('newfeed') ? (Tools::getValue('id_gms', 'false') != 'false' ? '?' . gms::getConfig(Tools::getValue('id_gms')) : '') : '') . "'/>
        </div>
        " . $this->loadScript();
    }

    public function renderExcludeForm()
    {
        $this->context->smarty->assign(array(
            'msgTitle' => $this->l('Product added to the list'),
            'msgContents' => $this->l('Product added to the list of products'),
            'version' => _PS_VERSION_,
            'gmfeed_products' => (Configuration::get('gm_gmfeed_products', 'false') != 'false' && Configuration::get('gm_gmfeed_products', 'false') != '' && Configuration::get('gm_gmfeed_products', 'false') != NULL ? explode(",", Configuration::get('gm_gmfeed_products', 'false')) : array()),
            'this' => $this
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'gmfeed/views/exclude-products.tpl');
    }

    public function returnProductName($id)
    {
        $product = new Product($id, false, $this->context->language->id);
        return $product->name . ' (' . 'ref: ' . $product->reference . ')';
    }

    public function renderGoogleCategories()
    {
        $taxonomy_files = array();
        foreach (Language::getLanguages(true) AS $language) {
            if (file_exists('../modules/gmfeed/google/' . $language['language_code'] . '.txt')) {
                $taxonomy_files[$language['language_code']] = 1;
            } else {
                $taxonomy_files[$language['language_code']] = 0;
            }
        }

        $this->context->smarty->assign('taxonomy_files', $taxonomy_files);
        $this->context->smarty->assign('taxonomy_files_custom', $this->taxonomyFiles);
        $this->context->smarty->assign('link', $this->context->link);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'gmfeed/views/google_lang_links.tpl');
    }

    public function getConfigFieldsValues()
    {
        return array(
            'gmfeed_products' => (Configuration::get('gm_gmfeed_products') != false ? explode(",", Configuration::get('gm_gmfeed_products')) : array()),
            'export_igid' => (Configuration::get('gm_export_igid') != false ? Configuration::get('gm_export_igid') : 0),
            'export_product_name' => (Configuration::get('gm_export_product_name') != false ? Configuration::get('gm_export_product_name') : 'name'),
            'remember_settings' => (Configuration::get('gm_remember_settings') != false ? Configuration::get('gm_remember_settings') : true),
            'export_additional_sc' => (Configuration::get('gm_export_additional_sc') != false ? Configuration::get('gm_export_additional_sc') : false),
            'export_active' => (Configuration::get('gm_export_active') != false ? Configuration::get('gm_export_active') : false),
            'export_instock' => (Configuration::get('gm_export_instock') != false ? Configuration::get('gm_export_instock') : false),
            'export_file_format' => (Configuration::get('gm_export_file_format') != false ? Configuration::get('gm_export_file_format') : 'csv'),
            'export_gtin' => (Configuration::get('gm_export_gtin') != false ? Configuration::get('gm_export_gtin') : 'upc'),
            'export_identification' => (Configuration::get('gm_export_identification') != false ? Configuration::get('gm_export_identification') : 'id_product'),
            'export_category' => (Configuration::get('gm_export_category') != false ? Configuration::get('gm_export_category') : 'all'),
            'export_manufacturers_default' => (Configuration::get('gm_export_manufacturers_default') != false ? Configuration::get('gm_export_manufacturers_default') : 'Default'),
            'export_type' => (Configuration::get('gm_export_type') != false ? Configuration::get('gm_export_type') : 'product'),
            'export_img' => (Configuration::get('gm_export_img') != false ? Configuration::get('gm_export_img') : 1),
            'export_manufacturers' => (Configuration::get('gm_export_manufacturers') != false ? Configuration::get('gm_export_manufacturers') : 'all'),
            //'export_description_what' => (Configuration::get('gm_export_description_what') != false ? Configuration::get('gm_export_description_what') : 'desc'),
            'export_short_description_what' => (Configuration::get('gm_export_short_description_what') != false ? Configuration::get('gm_export_short_description_what') : 'short'),
            'export_suppliers' => (Configuration::get('gm_export_suppliers') != false ? Configuration::get('gm_export_suppliers') : 'all'),
            'export_delimiter' => (Configuration::get('gm_export_delimiter') != false ? Configuration::get('gm_export_delimiter') : '|'),
            'delete_images' => (Configuration::get('gm_delete_images') != false ? Configuration::get('gm_delete_images') : 0),
            'export_currency' => (Configuration::get('gm_export_currency') != false ? Configuration::get('gm_export_currency') : Configuration::get('PS_CURRENCY_DEFAULT')),
            'export_instock_info' => (Configuration::get('gm_export_instock_info') != false ? Configuration::get('gm_export_instock_info') : 0),
            'export_what_pictures' => (Configuration::get('gm_export_what_pictures') != false ? Configuration::get('gm_export_what_pictures') : 0),
            'export_removehtml' => (Configuration::get('gm_export_removehtml') != false ? Configuration::get('gm_export_removehtml') : 0),
            'export_language' => (Configuration::get('gm_export_language') != false ? Configuration::get('gm_export_language') : Configuration::get('PS_LANG_DEFAULT')),
            'export_tax' => (Configuration::get('gm_export_tax') != false ? Configuration::get('gm_export_tax') : 'price_tin'),
            'export_product_type' => (Configuration::get('gm_export_product_type') != false ? Configuration::get('gm_export_product_type') : 0),
            'export_product_type_id' => (Configuration::get('gm_export_product_type_id') != false ? Configuration::get('gm_export_product_type_id') : 0),
            'export_exclude' => (Configuration::get('gm_export_exclude') != false ? Configuration::get('gm_export_exclude') : ''),
            'export_product_weight' => (Configuration::get('gm_export_product_weight') != false ? Configuration::get('gm_export_product_weight') : 0),
            'export_shipping_info' => (Configuration::get('gm_export_shipping_info') != false ? Configuration::get('gm_export_shipping_info') : 0),
            'export_shipping_info_price' => (Configuration::get('gm_export_shipping_info_price') != false ? Configuration::get('gm_export_shipping_info_price') : 0),
            'export_shipping_id_zone' => (Configuration::get('gm_export_shipping_id_zone') != false ? Configuration::get('gm_export_shipping_id_zone') : 1),
            'export_unit_price' => (Configuration::get('gm_export_unit_price') != false ? Configuration::get('gm_export_unit_price') : 0),
            'limit_limit' => (Configuration::get('gm_limit_limit') != false ? Configuration::get('gm_limit_limit') : 0),
            'limit_page' => (Configuration::get('gm_limit_page') != false ? Configuration::get('gm_limit_page') : 0),
            'export_specific' => (Configuration::get('gm_export_specific') != false ? Configuration::get('gm_export_specific') : 1),
            'export_product_name_format' => (Configuration::get('gm_export_product_name_format') != false ? Configuration::get('gm_export_product_name_format') : 0),
            'export_save_file' => (Configuration::get('gm_export_save_file') != false ? Configuration::get('gm_export_save_file') : 0),
            'export_nophoto_exclude' => (Configuration::get('gm_export_nophoto_exclude') != false ? Configuration::get('gm_export_nophoto_exclude') : 0),


            'export_color' => (Configuration::get('gm_export_color') != false ? Configuration::get('gm_export_color') : 0),
            'export_color_type' => (Configuration::get('gm_export_color_type') != false ? Configuration::get('gm_export_color_type') : ''),
            'export_color_attribute' => (Configuration::get('gm_export_color_attribute') != false ? Configuration::get('gm_export_color_attribute') : 0),
            'export_color_feature' => (Configuration::get('gm_export_color_feature') != false ? Configuration::get('gm_export_color_feature') : 0),
            'export_color_default' => (Configuration::get('gm_export_color_default') != false ? Configuration::get('gm_export_color_default') : ''),
            'export_gender' => (Configuration::get('gm_export_gender') != false ? Configuration::get('gm_export_gender') : 0),
            'export_gender_type' => (Configuration::get('gm_export_gender_type') != false ? Configuration::get('gm_export_gender_type') : 0),
            'export_gender_feature' => (Configuration::get('gm_export_gender_feature') != false ? Configuration::get('gm_export_gender_feature') : 0),
            'export_gender_attribute' => (Configuration::get('gm_export_gender_attribute') != false ? Configuration::get('gm_export_gender_attribute') : 0),
            'export_gender_default' => (Configuration::get('gm_export_gender_default') != false ? Configuration::get('gm_export_gender_default') : ''),
            'export_age_group' => (Configuration::get('gm_export_age_group') != false ? Configuration::get('gm_export_age_group') : 0),
            'export_age_group_type' => (Configuration::get('gm_export_age_group_type') != false ? Configuration::get('gm_export_age_group_type') : 0),
            'export_age_group_feature' => (Configuration::get('gm_export_age_group_feature') != false ? Configuration::get('gm_export_age_group_feature') : 0),
            'export_age_group_attribute' => (Configuration::get('gm_export_age_group_attribute') != false ? Configuration::get('gm_export_age_group_attribute') : 0),
            'export_age_group_default' => (Configuration::get('gm_export_age_group_default') != false ? Configuration::get('gm_export_age_group_default') : 0),
            'export_size' => (Configuration::get('gm_export_size') != false ? Configuration::get('gm_export_size') : 0),
            'export_size_attribute[]' => (Configuration::get('gm_export_size_attribute') != false ? explode(",", Configuration::get('gm_export_size_attribute')) : 0),
            'export_size_default' => (Configuration::get('gm_export_size_default') != false ? Configuration::get('gm_export_size_default') : ''),
            'export_availability_date' => (Configuration::get('gm_export_availability_date') != false ? Configuration::get('gm_export_availability_date') : ''),


            'export_exreference' => (Configuration::get('gm_export_exreference') != false ? Configuration::get('gm_export_exreference') : ''),
            'export_exreference_value' => (Configuration::get('gm_export_exreference_value') != false ? Configuration::get('gm_export_exreference_value') : ''),
            'export_country_prices' => (Configuration::get('gm_export_country_prices') != false ? Configuration::get('gm_export_country_prices') : Configuration::get('PS_COUNTRY_DEFAULT')),
            'export_storecode' => (Configuration::get('gm_export_storecode') != false ? Configuration::get('gm_export_storecode') : 0),
            'export_sc' => (Configuration::get('gm_export_sc') != false ? Configuration::get('gm_export_sc') : ''),
            'export_mainpicture' => (Configuration::get('gm_export_mainpicture') != false ? Configuration::get('gm_export_mainpicture') : 0),
            'export_inventory' => (Configuration::get('gm_export_inventory') != false ? Configuration::get('gm_export_inventory') : 0),
            'export_ie' => (Configuration::get('gm_export_ie') != false ? Configuration::get('gm_export_ie') : 0),

            'export_shortlink' => (Configuration::get('gm_export_shortlink') != false ? Configuration::get('gm_export_shortlink') : 0),
            'export_shortlinkName' => (Configuration::get('gm_export_shortlinkName') != false ? Configuration::get('gm_export_shortlinkName') : ''),
            'export_id_gms' => (Configuration::get('gm_export_id_gms') != false ? Configuration::get('gm_export_id_gms') : false),

            'export_exworthless' => (Configuration::get('gm_export_exworthless') != false ? Configuration::get('gm_export_exworthless') : false),
            'export_exworthless_value' => (Configuration::get('gm_export_exworthless_value') != false ? Configuration::get('gm_export_exworthless_value') : ''),
        );
    }

    public function getAllProductsNb()
    {
        $nb = Db::getInstance()->getRow('SELECT COUNT(*) AS nb FROM `' . _DB_PREFIX_ . 'product`');
        return (isset($nb['nb']) ? (int)$nb['nb'] : 0);
    }

    public function hasCombinations($id_product)
    {
        if (null === $id_product || 0 >= $id_product) {
            return false;
        }
        $attributes = Product::getAttributesInformationsByProduct($id_product);

        return !empty($attributes);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitExport')) {
            if (Tools::getValue('export_shortlink') == 1) {

                if ((int)Tools::getValue('export_id_gms')) {
                    $gms = new gms(Tools::getValue('export_id_gms'));
                    $gms->name = Tools::getValue('export_shortlinkName', $this->l('Unnamed Feed'));
                    $conf = '';
                    $post = array();
                    foreach ($_POST AS $posted => $value) {
                        if (is_array($value)) {
                            foreach ($value AS $vv) {
                                $post[] = $posted . '[]=' . $vv;
                            }
                        } else {
                            $post[] = $posted . '=' . $value;
                        }
                    }
                    $conf = implode('&', $post);
                    $gms->conf = $conf;
                    $gms->save();
                } else {
                    $gms = new gms();
                    $gms->name = Tools::getValue('export_shortlinkName', $this->l('Unnamed Feed'));
                    $conf = '';
                    $post = array();
                    foreach ($_POST AS $posted => $value) {
                        if (is_array($value)) {
                            foreach ($value AS $vv) {
                                $post[] = $posted . '[]=' . $vv;
                            }
                        } else {
                            $post[] = $posted . '=' . $value;
                        }
                    }
                    $conf = implode('&', $post);
                    $gms->conf = $conf;
                    $gms->save();

                }
            }

            if (Tools::getValue('remember_settings') == 1) {
                foreach ($this->getConfigFieldsValues() AS $gmkey => $gmvalue) {
                    if ($gmkey == 'gmfeed_products') {
                        if (Tools::getValue('gmfeed_products', 'false') != 'false') {
                            Configuration::updateValue('gm_gmfeed_products', implode(',', Tools::getValue('gmfeed_products')));
                        }
                    } elseif ($gmkey == 'export_size_attribute[]') {
                        if (Tools::getValue('export_size_attribute', 'false') != 'false') {
                            Configuration::updateValue('gm_export_size_attribute', implode(',', Tools::getValue('export_size_attribute')));
                        }
                    } else {
                        Configuration::updateValue('gm_' . $gmkey, Tools::getValue($gmkey));
                    }
                }
            }

            if (Tools::isSubmit('saveonly')) {
                $this->context->controller->confirmations[] = $this->l("Settings saved");
                return;
            }

            $export_type = Tools::getValue('export_type');
            $delimiter = Tools::getValue('export_delimiter');
            $id_lang = Tools::getValue('export_language');
            $id_shop = (int)$this->context->shop->id;
            $weight_unit = Configuration::get('PS_WEIGHT_UNIT');


            if (Tools::getValue('export_product_type') == 1) {
                $this->available_fields[$export_type]['product_type'] = array('label' => 'product_type');
            }

            if (Tools::getValue('export_product_weight') == 1) {
                $this->available_fields[$export_type]['weight'] = array('label' => 'shipping_weight');
            }

            if (Tools::getValue('export_shipping_info', 'false') != 'false') {
                if (Tools::getValue('export_shipping_info', 'false') != 0) {
                    $this->available_fields[$export_type]['shipping'] = array('label' => 'shipping');
                }
            }

            set_time_limit(0);


            if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                $fileName = $export_type . '_' . date("Y_m_d_H_i_s") . '.csv';
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header('Content-Description: File Transfer');
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename={$fileName}");
                header("Expires: 0");
                header("Pragma: public");
            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                $fileName = $export_type . '_' . date("Y_m_d_H_i_s") . '.xml';
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header('Content-Description: File Transfer');
                header('Content-Type: application/xml; charset=utf-8');
                header("Content-Disposition: attachment; filename={$fileName}");
                header("Expires: 0");
                header("Pragma: public");
            }

            if (Tools::getValue('export_country_prices', 'false') != 'false') {
                $this->context->country = new Country(Tools::getValue('export_country_prices'));
            } else {
                $this->context->country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
            }

            echo "\xEF\xBB\xBF";
            $f = fopen('php://output', 'w');


            if (Tools::getValue('export_what_pictures') == 0) {
                unset($this->available_fields[$export_type]['additional_image_link']);
            }


            if (Tools::getValue('export_storecode') == 0) {
                unset($this->available_fields[$export_type]['store_code']);
            }

            $export_tax = Tools::getValue('export_tax');

            if ($export_tax == 'price_tin') {
                unset($this->available_fields[$export_type]['price_tex']);
                unset($this->available_fields[$export_type]['sale_price_tex']);
            } elseif ($export_tax == 'price_tex') {
                unset($this->available_fields[$export_type]['price_tin']);
                unset($this->available_fields[$export_type]['sale_price_tin']);
            }


            if (Tools::getValue('export_unit_price') == 0) {
                unset($this->available_fields[$export_type]['unit_price']);
            }

            if (Tools::getValue('export_igid') == 0) {
                unset($this->available_fields[$export_type]['item_group_id']);
            }


            if (Tools::getValue('export_gender') == 0) {
                unset($this->available_fields[$export_type]['gender']);
            }

            if (Tools::getValue('export_age_group') == 0) {
                unset($this->available_fields[$export_type]['age_group']);
            }

            if (Tools::getValue('export_size') == 0) {
                unset($this->available_fields[$export_type]['size']);
            }

            if (Tools::getValue('export_color') == 0) {
                unset($this->available_fields[$export_type]['color']);
            }


            if (Tools::getValue('export_inventory') != 1) {
                unset($this->available_fields[$export_type]['inventory']);
            }

            if (Tools::getValue('export_ie') != 1) {
                unset($this->available_fields[$export_type]['identifier_exists']);
            }

            foreach ($this->available_fields[$export_type] as $field => $array) {
                $titles[] = $array['label'];
            }

            if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                fputcsv($f, $titles, $delimiter, '"');
            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                $xml_array = array();
            }

            $export_active = (Tools::getValue('export_active') == 0 ? false : true);
            $export_instock = (Tools::getValue('export_instock') == 0 ? false : true);
            $export_category = (Tools::getValue('export_category') == 99999 ? false : Tools::getValue('export_category'));
            $pixel_google_categories = pixelgoogle::getAllByLanguage($id_lang);
            $category_names = array();
            $export_product_type_id = Tools::getValue('export_product_type_id', 0);

            $limit_page = Tools::getValue('limit_page', 0);
            if ($limit_page > 0) {
                $limit_page--;
            }
            $limit_limit = tools::getValue('limit_limit', 0);
            $limit_page = $limit_page * $limit_limit;

            $default_availability_date = Configuration::get('gm_export_availability_date');

            switch ($export_type) {
                case 'products':
                    $currency = new Currency(Tools::getValue('export_currency'));
                    $this->context->currency = $currency;
                    $products = Product::getProducts($id_lang, $limit_page, $limit_limit, 'id_product', 'ASC', $export_category, $export_active);
                    foreach ($products as $product) {
                        if (Tools::getValue('gmfeed_products', 'false') != 'false') {
                            if (in_array($product['id_product'], Tools::getValue('gmfeed_products'))) {
                                continue;
                            }
                        }

                        $p = new Product($product['id_product'], true, $id_lang, $id_shop);
                        if (isset($product_features)) {
                            unset($product_features);
                        }
                        $product_features = Product::getFrontFeaturesStatic($id_lang, $p->id);

                        if (Tools::getValue('export_exreference') == 1 && $p->reference != "") {
                            if (strncmp($p->reference, Tools::getValue('export_exreference_value'), strlen(Tools::getValue('export_exreference_value'))) === 0) {
                                continue;
                            }
                        }
                        $p->loadStockData();
                        if ($export_instock == true && $p->quantity <= 0) {
                            continue;
                        }


                        if (Tools::getValue('export_exworthless') == 1) {
                            if (Tools::getValue('export_exworthless_value')) {
                                $currency_default = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'), $this->context->language->id);
                                if ($this->context->currency->id != $currency_default->id) {
                                    $product_price = Tools::convertPriceFull($p->getPrice(true, null, 6), $this->context->currency, $currency_default);
                                } else {
                                    $product_price = $p->getPrice(false, null, 6);
                                }
                                if ($product_price < Tools::getValue('export_exworthless_value')) {
                                    continue;
                                }
                            }
                        }

                        $line = array();

                        $category_default = new Category($p->id_category_default, $id_lang);
                        foreach ($this->available_fields['products'] as $field => $array) {
                            switch ($field) {
                                case 'gender':
                                    $product_feature_value = Tools::getValue('export_gender_default');
                                    $line[$field] = '-';
                                    if (Tools::getValue('export_gender_type') == 'feature') {
                                        foreach ($product_features AS $pfk => $pf) {
                                            if ($pf['id_feature'] == Tools::getValue('export_gender_feature')) {
                                                $line[$field] = $pf['value'];
                                            }
                                        }
                                    } elseif (Tools::getValue('export_gender_type') == 'attribute') {
                                        if ($this->hasCombinations($p->id)) {
                                            foreach ($p->getAttributeCombinationsById($p->cache_default_attribute, $id_lang) AS $attrk => $attr) {
                                                if ($attr['id_attribute_group'] == Tools::getValue('export_gender_attribute')) {
                                                    $line[$field] = $attr['attribute_name'];
                                                }
                                            }
                                        }
                                    }
                                    if ($line[$field] == '-') {
                                        $line[$field] = $product_feature_value;
                                    }
                                    unset($product_feature_value);
                                    break;
                                case 'age_group':
                                    $product_feature_value = Tools::getValue('export_age_group_default');
                                    $line[$field] = '-';
                                    if (Tools::getValue('export_age_group_type') == 'feature') {
                                        foreach ($product_features AS $pfk => $pf) {
                                            if ($pf['id_feature'] == Tools::getValue('export_age_group_feature')) {
                                                $line[$field] = $pf['value'];
                                            }
                                        }
                                        if ($line[$field] == '-') {
                                            $line[$field] = $product_feature_value;
                                        }
                                    } elseif (Tools::getValue('export_age_group_type') == 'attribute') {
                                        if ($this->hasCombinations($p->id)) {
                                            foreach ($p->getAttributeCombinationsById($p->cache_default_attribute, $id_lang) AS $attrk => $attr) {
                                                if ($attr['id_attribute_group'] == Tools::getValue('export_age_group_attribute')) {
                                                    $line[$field] = $attr['attribute_name'];
                                                }
                                            }
                                        }
                                    }
                                    if ($line[$field] == '-') {
                                        $line[$field] = $product_feature_value;
                                    }
                                    unset($product_feature_value);
                                    break;
                                case 'color':
                                    $product_feature_value = Tools::getValue('export_color_default');
                                    $line[$field] = '-';
                                    if (Tools::getValue('export_color_type') == 'feature') {
                                        foreach ($product_features AS $pfk => $pf) {
                                            if ($pf['id_feature'] == Tools::getValue('export_color_feature')) {
                                                $line[$field] = $pf['value'];
                                            }
                                        }
                                    } elseif (Tools::getValue('export_color_type') == 'attribute') {
                                        if ($this->hasCombinations($p->id)) {
                                            foreach ($p->getAttributeCombinationsById($p->cache_default_attribute, $id_lang) AS $attrk => $attr) {
                                                if ($attr['id_attribute_group'] == Tools::getValue('export_color_attribute')) {
                                                    $line[$field] = $attr['attribute_name'];
                                                }
                                            }
                                        }
                                    }

                                    if ($line[$field] == '-') {
                                        $line[$field] = $product_feature_value;
                                    }
                                    unset($product_feature_value);
                                    break;
                                case 'size':
                                    $product_feature_value = Tools::getValue('export_size_default');
                                    $line[$field] = '-';

                                    if ($this->hasCombinations($p->id)) {
                                        foreach ($p->getAttributeCombinationsById($p->cache_default_attribute, $id_lang) AS $attrk => $attr) {
                                            if (in_array($attr['id_attribute_group'], Tools::getValue('export_size_attribute'))) {
                                                $line[$field] = $attr['attribute_name'];
                                            }
                                        }
                                    }

                                    if ($line[$field] == '-') {
                                        $line[$field] = $product_feature_value;
                                    }
                                    unset($product_feature_value);
                                    break;

                                case 'shipping':
                                    if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                                        if (Tools::getValue('export_shipping_info') == 1) {
                                            $line[$field] = $this->context->country->iso_code . ":::" . Tools::ps_round((Tools::getValue('export_additional_sc') == 1 ? Tools::convertPrice($p->additional_shipping_cost, $currency, true) : 0) + Tools::convertPrice($this->getShippingCost($p->price, $p->weight), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        } elseif (Tools::getValue('export_shipping_info') == 2) {
                                            $line[$field] = $this->context->country->iso_code . ":::" . Tools::ps_round((Tools::getValue('export_additional_sc') == 1 ? Tools::convertPrice($p->additional_shipping_cost, $currency, true) : 0) + Tools::convertPrice(Tools::getValue('export_shipping_info_price'), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        }
                                    } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                                        if (Tools::getValue('export_shipping_info') == 1) {
                                            $line[$field]['country'] = $this->context->country->iso_code;
                                            $line[$field]['price'] = Tools::ps_round((Tools::getValue('export_additional_sc') == 1 ? Tools::convertPrice($p->additional_shipping_cost, $currency, true) : 0) + Tools::convertPrice($this->getShippingCost($p->price, $p->weight), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        } elseif (Tools::getValue('export_shipping_info') == 2) {
                                            $line[$field]['country'] = $this->context->country->iso_code;
                                            $line[$field]['price'] = Tools::ps_round((Tools::getValue('export_additional_sc') == 1 ? Tools::convertPrice($p->additional_shipping_cost, $currency, true) : 0) + Tools::convertPrice(Tools::getValue('export_shipping_info_price'), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        }
                                    }
                                    break;
                                case 'item_group_id':
                                    $line[$field] = 'GID' . $p->id;
                                    break;
                                case 'id':
                                    $line[$field] = $p->id;
                                    break;
                                case 'gtin':
                                    if (Tools::getValue('export_gtin') == 'nothing') {
                                        $line[$field] = '';
                                    } elseif (Tools::getValue('export_gtin') == 'upc') {
                                        if (Validate::isUpc($p->upc)) {
                                            $line[$field] = $p->upc;
                                        } elseif (Validate::isEan13($p->ean13)) {
                                            $line[$field] = $p->ean13;
                                        } else {
                                            $line[$field] = '';
                                        }
                                    } elseif (Tools::getValue('export_gtin') == 'ean13') {
                                        if (Validate::isEan13($p->ean13)) {
                                            $line[$field] = $p->ean13;
                                        } elseif (Validate::isUpc($p->upc)) {
                                            $line[$field] = $p->upc;
                                        } else {
                                            $line[$field] = '';
                                        }
                                    } elseif (Tools::getValue('export_gtin') == 'reference') {
                                        if (isset($p->reference)) {
                                            $line[$field] = $p->reference;
                                        } elseif (Validate::isUpc($p->upc)) {
                                            $line[$field] = $p->upc;
                                        } elseif (Validate::isEan13($p->ean13)) {
                                            $line[$field] = $p->ean13;
                                        } else {
                                            $line[$field] = '';
                                        }
                                    } else {
                                        $line[$field] = '';
                                    }
                                    break;
                                case 'identifier_exists':
                                    if (strlen($line['gtin']) > 1) {
                                        $line[$field] = 'true';
                                    } else {
                                        $line[$field] = 'false';
                                    }
                                    break;
                                case 'name':
                                    $line[$field] = (Tools::getValue('export_product_name') == 'meta_title' ? $p->meta_title : $p->name);
                                    if (Tools::getValue('export_product_name_format') == 1) {
                                        $line[$field] = ucfirst(strtolower($line[$field]));
                                    } else if (Tools::getValue('export_product_name_format') == 2) {
                                        $line[$field] = mb_convert_case(ucwords(strtolower($line[$field])), MB_CASE_TITLE, "UTF-8");
                                    } else if (Tools::getValue('export_product_name_format') == 3) {
                                        $line[$field] = strtolower($line[$field]);
                                    }

                                    break;
                                case 'quantity':
                                    $availability_date = '';
                                    $stock = '';
                                    $allow_oosp = $p->isAvailableWhenOutOfStock((int)$p->out_of_stock);

                                    if ($p->quantity > 0) {
                                        $stock = 'in_stock';
                                    }
                                    if (($p->available_for_order == 1)) {
                                        if (($allow_oosp == 1 || $allow_oosp == 0) && $p->quantity > 0) {
                                            $stock = 'in_stock';
                                        } elseif ($allow_oosp == 1 && $p->quantity <= 0) {
                                            $stock = 'backorder';
                                            if (Validate::isDate($p->available_date)) {
                                                $availability_date = $p->available_date;
                                            } else {
                                                $availability_date = $default_availability_date;
                                            }
                                        } elseif ($allow_oosp == 0 && $p->quantity <= 0) {
                                            $stock = 'out_of_stock';
                                        } elseif ($p->quantity < 0) {
                                            $stock = 'in_stock';
                                        }
                                    } else {
                                        $stock = 'out_of_stock';
                                    }

                                    if (Tools::getValue('export_instock_info') == 0) {

                                    } elseif (Tools::getValue('export_instock_info') == 1) {
                                        $stock = 'in_stock';
                                    } elseif (Tools::getValue('export_instock_info') == 2) {
                                        $stock = 'out_of_stock';
                                    } elseif (Tools::getValue('export_instock_info') == 3) {
                                        $stock = 'backorder';
                                        if (Validate::isDate($p->available_date)) {
                                            $availability_date = $p->available_date;
                                        } else {
                                            $availability_date = $default_availability_date;
                                        }
                                    }

                                    $line[$field] = $stock;
                                    $line['availability_date'] = $availability_date;
                                    break;
                                case 'inventory':
                                    $line[$field] = $p->quantity;
                                    break;
                                case 'condition':
                                    $line[$field] = $p->condition;
                                    break;
                                case 'include_url':
                                    $line['include_url'] = Context::getContext()->link->getProductLink($p->id, null, null, null, $id_lang, $this->context->shop->id);
                                    break;
                                case 'image_url':
                                    $line['image_url'] = '';
                                    $imagelinks = array();
                                    $images = $p->getImages($id_lang);
                                    foreach ($images as $image) {
                                        $imagelinks[] = $this->context->link->getImageLink($p->link_rewrite, $p->id . '-' . $image['id_image'], Tools::getValue('export_img'));
                                    }
                                    if (isset($imagelinks[1])) {
                                        $line['image_url'] = $imagelinks[1];
                                    } else if (isset($imagelinks[0])) {
                                        $line['image_url'] = $imagelinks[0];
                                    } else {
                                        $line['image_url'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                    }
                                    if ($line['image_url'] == '') {
                                        $line['image_url'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                    }
                                    break;
                                case 'additional_image_link':
                                    $line['additional_image_link'] = '';
                                    $imagelinks = array();
                                    $images = $p->getImages($id_lang);
                                    foreach ($images as $image) {
                                        $imagelinks[] = $this->context->link->getImageLink($p->link_rewrite, $p->id . '-' . $image['id_image'], Tools::getValue('export_img'));
                                    }
                                    if (isset($imagelinks[0]) && Tools::getValue('export_what_pictures') == 1) {
                                        array_shift($imagelinks);
                                        $line['additional_image_link'] = $imagelinks[0];
                                    } else {
                                        $line['additional_image_link'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                    }

                                    if ($line['additional_image_link'] == '') {
                                        $line['additional_image_link'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                    }
                                    break;
                                case 'item_subtitle':
                                    $line[$field] = ucfirst($p->name);
                                    $meta = Meta::getProductMetas($p->id, $id_lang, '');
                                    $line['item_subtitle'] = $meta['meta_title'];
                                    break;
                                case 'description_short':
                                    $description_short = '-';
                                    if (Tools::getValue('export_short_description_what', 'short') == 'short') {
                                        if (Tools::getValue('export_removehtml', 0) != 0) {
                                            $description_short = strip_tags($p->description_short);
                                        } else {
                                            $description_short = $p->description_short;
                                        }
                                    } elseif (Tools::getValue('export_short_description_what', 'short') == 'desc') {
                                        if (Tools::getValue('export_removehtml', 0) != 0) {
                                            $description_short = strip_tags($p->description);
                                        } else {
                                            $description_short = $p->description;
                                        }
                                    }
                                    $line[$field] = (strlen(trim($description_short)) > 0 ? trim($description_short) : '-');
                                    break;
                                case 'item_category':
                                    $line[$field] = $category_default->name;
                                    break;
                                case 'price_tin':
                                    if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                        $line[$field] = Tools::ps_round($p->getPrice(true, null, 6) + $p->getPrice(true, null, 6, null, true), (version_compare(_PS_VERSION_, '') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        if ($p->getPrice(true, null, 6, null, true) === 0.0) {
                                            $line['sale_price'] = '';
                                        } else {
                                            $line['sale_price'] = Tools::ps_round($p->getPrice(true, null, 6), (version_compare(_PS_VERSION_, '1.6.1.0') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        }
                                    } else {
                                        $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(true, null, 6) + $p->getPrice(true, null, 6, null, true), $currency, true), ((version_compare(_PS_VERSION_, '1.6.1.0') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_)) . ' ' . $currency->iso_code;
                                        if ($p->getPrice(true, null, 6, null, true) === 0.0) {
                                            $line['sale_price'] = '';
                                        } else {
                                            $line['sale_price'] = Tools::ps_round(Tools::convertPrice($p->getPrice(true, null, 6), $currency, true), ((version_compare(_PS_VERSION_, '1.6.1.0') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_)) . ' ' . $currency->iso_code;
                                        }
                                    }
                                    break;
                                case 'price_tex':
                                    if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                        $line[$field] = Tools::ps_round($p->getPrice(false, null, 6) + $p->getPrice(false, null, 6, null, true), (version_compare(_PS_VERSION_, '1.6.1.0') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        if ($p->getPrice(false, null, 6, null, true) === 0.0) {
                                            $line['sale_price'] = '';
                                        } else {
                                            $line['sale_price'] = Tools::ps_round($p->getPrice(false, null, 6), (version_compare(_PS_VERSION_, '1.6.1.0') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                        }
                                    } else {
                                        $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(false, null, 6) + $p->getPrice(false, null, 6, null, true), $currency, true), ((version_compare(_PS_VERSION_, '1.6.1.0') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_)) . ' ' . $currency->iso_code;
                                        if ($p->getPrice(false, null, 6, null, true) === 0.0) {
                                            $line['sale_price'] = '';
                                        } else {
                                            $line['sale_price'] = Tools::ps_round(Tools::convertPrice($p->getPrice(false, null, 6), $currency, true), ((version_compare(_PS_VERSION_, '1.6.1.0') == true) ? '2' : _PS_PRICE_COMPUTE_PRECISION_)) . ' ' . $currency->iso_code;
                                        }
                                    }
                                    break;
                                case 'unit_price':
                                    $line[$field] = Tools::ps_round(Tools::convertPrice($p->unit_price), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                    break;
                                /**
                                 * case 'sale_price_tin':
                                 * if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                 * $line[$field] = Tools::ps_round($p->getPrice(true, null, 6), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                 * } else {
                                 * $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(true, null, 6), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                 * }
                                 * break;
                                 * case 'sale_price_tex':
                                 * if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                 * $line[$field] = Tools::ps_round($p->getPrice(false, null, 6), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                 * } else {
                                 * $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(false, null, 6), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                 * }
                                 * break;
                                 * **/
                                case 'brand':
                                    $line[$field] = ($p->manufacturer_name != "" ? $p->manufacturer_name : Tools::getValue('export_manufacturers_default', 'Default'));
                                    break;
                                case 'contextual_keywords':
                                    $name = explode(" ", $p->name);
                                    $line[$field] = implode(';', $name);
                                    break;
                                case 'google_product_category':
                                    if (isset($pixel_google_categories[$p->id_category_default]['id_google'])) {
                                        $line['google_product_category'] = $pixel_google_categories[$p->id_category_default]['id_google'];
                                    } else {
                                        $line['google_product_category'] = 0;
                                    }
                                    break;
                                case 'product_type':
                                    $category_names_array = array();
                                    foreach (Product::getProductCategories($p->id) AS $pcatid) {
                                        if (!isset($category_names[$pcatid])) {
                                            $category_names[$pcatid] = new Category($pcatid, Tools::getValue('export_language'));
                                            $category_names_array[] = ($export_product_type_id == 1 ? $pcatid . '-' : '') . $category_names[$pcatid]->name;
                                        } else {
                                            $category_names_array[] = ($export_product_type_id == 1 ? $pcatid . '-' : '') . $category_names[$pcatid]->name;
                                        }
                                    }
                                    $line['product_type'] = implode(" > ", $category_names_array);
                                    break;
                                case 'weight':
                                    $line['weight'] = number_format($p->weight, 2, '.', '') . ' ' . $weight_unit;
                                    break;
                                case 'store_code':
                                    $line['store_code'] = Tools::getValue('export_sc');
                                    break;
                            }
                        }

                        $include = 1;

                        if (Tools::getValue('export_manufacturers') != 99999) {
                            if ($p->id_manufacturer != Tools::getValue('export_manufacturers')) {
                                $include = 0;
                            }
                        }
                        if (Tools::getValue('export_suppliers') != 99999) {
                            if (Supplier::getProductInformationsBySupplier(Tools::getValue('export_suppliers'), $p->id) == null) {
                                $include = 0;
                            }
                        }
                        if (Tools::getValue('export_nophoto_exclude') == 1) {
                            if (is_array($images)) {
                                if (count($images) <= 0) {
                                    $include = 0;
                                }
                            } else {
                                $include = 0;
                            }
                        }

                        if ($include == 1) {
                            if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                                foreach ($line as $lkey => $litem) {
                                    $lkey = $this->changeKeyToGoogleFeed($lkey);
                                    $new_line[$lkey] = $litem;
                                }

                                fputcsv($f, $line, $delimiter, '"');
                            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                                $new_line = array();
                                foreach ($line as $lkey => $litem) {
                                    $lkey = $this->changeKeyToGoogleFeed($lkey);
                                    $new_line[$lkey] = $litem;
                                }
                                $xml_array[] = $new_line;
                            }
                        }
                    }

                    break;
                case 'combinations':
                    $currency = new Currency(Tools::getValue('export_currency', (int)Configuration::get('PS_CURRENCY_DEFAULT')));
                    $this->context->currency = $currency;

                    if (!Combination::isFeatureActive()) {
                        return false;
                    }

                    $limit_page = Tools::getValue('limit_page', 0);
                    if ($limit_page > 0) {
                        $limit_page--;
                    }
                    $limit_limit = tools::getValue('limit_limit', 0);
                    $limit_page = $limit_page * $limit_limit;

                    $products = Product::getProducts($id_lang, $limit_page, $limit_limit, 'id_product', 'ASC', $export_category, $export_active);

                    foreach ($products as $product) {
                        if (Tools::getValue('gmfeed_products', 'false') != 'false') {
                            if (in_array($product['id_product'], Tools::getValue('gmfeed_products'))) {
                                continue;
                            }
                        }
                        $line = array();
                        $p = new Product($product['id_product'], true, $id_lang, $id_shop);
                        $p->loadStockData();

                        if (isset($product_features)) {
                            unset($product_features);
                        }
                        $product_features = Product::getFrontFeaturesStatic($id_lang, $p->id);

                        $category_default = new Category($p->id_category_default, $id_lang);
                        $sql = 'SELECT
                            pa.`supplier_reference` AS supplier_reference,
                            ag.`id_attribute_group`,
                            ag.`is_color_group`,
                            agl.`name` AS group_name,
                            agl.`public_name` AS public_group_name,
                            a.`id_attribute`,
                            a.`position` AS attribute_position,
                            ag.`position` AS group_position,
                            al.`name` AS attribute_name,
                            a.`color` AS attribute_color,
                            product_attribute_shop.`id_product_attribute` AS id_product_attribute,
                            IFNULL(stock.quantity, 0) as quantity,
                            pa.`price`,
                            product_attribute_shop.`ecotax`,
                            product_attribute_shop.`weight`,
                            pa.`ean13`,
                            product_attribute_shop.`wholesale_price`,
                            pa.`upc`,
                            pa.`default_on`,
                            pa.`reference` AS reference,
                            product_attribute_shop.`unit_price_impact`,
                            product_attribute_shop.`ecotax`,
                            product_attribute_shop.`minimal_quantity`,
                            product_attribute_shop.`available_date`,
                            product_attribute_shop.`id_shop` AS id_shop,
                            ag.`group_type`
                            FROM `' . _DB_PREFIX_ . 'product_attribute` pa
                            ' . Shop::addSqlAssociation('product_attribute', 'pa') . '
                            ' . Product::sqlStock('pa', 'pa') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`)
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`)
                            LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`)
                            ' . Shop::addSqlAssociation('attribute', 'a') . '

                            WHERE pa.`id_product` = ' . (int)$p->id . '
                            GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
                            ORDER BY pa.`id_product_attribute`';

                        $attributes = Db::getInstance()->executeS($sql);
                        if (count($attributes) <= 0) {
                            continue;
                        }

                        if ($attributes) {
                            $attributes_ready = array();
                            $attributes_details = array();

                            foreach ($attributes as $vvalue) {
                                $attributes_details[$vvalue['id_product_attribute']]['new_group'][] = $vvalue['public_group_name'] . ':' . $vvalue['group_type'] . ':' . $vvalue['group_position'];
                                $attributes_details[$vvalue['id_product_attribute']]['new_attribute'][] = $vvalue['attribute_name'] . ':' . $vvalue['attribute_position'];
                                $attributes_ready[$vvalue['id_product_attribute']] = $vvalue;
                            }
                        }

                        if ($attributes_ready) {
                            foreach ($attributes_ready as $value) {
                                if ($export_instock == true && $value['quantity'] <= 0) {
                                    continue;
                                }
                                if (Tools::getValue('export_exreference') == 1 && $value['reference'] != "") {
                                    if (strncmp($value['reference'], Tools::getValue('export_exreference_value'), strlen(Tools::getValue('export_exreference_value'))) === 0) {
                                        continue;
                                    }
                                }
                                foreach ($this->available_fields['combinations'] as $field => $array) {
                                    switch ($field) {
                                        //case 'vat':
                                        //$line[$field] = $p->tax_rate;
                                        //break;
                                        case 'gender':
                                            $product_feature_value = Tools::getValue('export_gender_default');
                                            $line[$field] = '-';
                                            if (Tools::getValue('export_gender_type') == 'feature') {
                                                foreach ($product_features AS $pfk => $pf) {
                                                    if ($pf['id_feature'] == Tools::getValue('export_gender_feature')) {
                                                        $line[$field] = $pf['value'];
                                                    }
                                                }
                                            } elseif (Tools::getValue('export_gender_type') == 'attribute') {
                                                if ($this->hasCombinations($p->id)) {
                                                    foreach ($p->getAttributeCombinationsById($p->cache_default_attribute, $id_lang) AS $attrk => $attr) {
                                                        if ($attr['id_attribute_group'] == Tools::getValue('export_gender_attribute')) {
                                                            $line[$field] = $attr['attribute_name'];
                                                        }
                                                    }
                                                }
                                            }
                                            if ($line[$field] == '-') {
                                                $line[$field] = $product_feature_value;
                                            }
                                            unset($product_feature_value);
                                            break;

                                        case 'age_group':
                                            $product_feature_value = Tools::getValue('export_age_group_default');
                                            $line[$field] = '-';
                                            if (Tools::getValue('export_age_group_type') == 'feature') {
                                                foreach ($product_features AS $pfk => $pf) {
                                                    if ($pf['id_feature'] == Tools::getValue('export_age_group_feature')) {
                                                        $line[$field] = $pf['value'];
                                                    }
                                                }
                                                if ($line[$field] == '-') {
                                                    $line[$field] = $product_feature_value;
                                                }
                                            } elseif (Tools::getValue('export_age_group_type') == 'attribute') {
                                                if ($this->hasCombinations($p->id)) {
                                                    foreach ($p->getAttributeCombinationsById($p->cache_default_attribute, $id_lang) AS $attrk => $attr) {
                                                        if ($attr['id_attribute_group'] == Tools::getValue('export_age_group_attribute')) {
                                                            $line[$field] = $attr['attribute_name'];
                                                        }
                                                    }
                                                }
                                            }
                                            if ($line[$field] == '-') {
                                                $line[$field] = $product_feature_value;
                                            }
                                            unset($product_feature_value);
                                            break;
                                        case 'color':
                                            $product_feature_value = Tools::getValue('export_color_default');
                                            $line[$field] = '-';
                                            if (Tools::getValue('export_color_type') == 'feature') {
                                                foreach ($product_features AS $pfk => $pf) {
                                                    if ($pf['id_feature'] == Tools::getValue('export_color_feature')) {
                                                        $line[$field] = $pf['value'];
                                                    }
                                                }
                                            } elseif (Tools::getValue('export_color_type') == 'attribute') {
                                                if ($this->hasCombinations($p->id)) {
                                                    foreach ($p->getAttributeCombinationsById($value['id_product_attribute'], $id_lang) AS $attrk => $attr) {
                                                        if ($attr['id_attribute_group'] == Tools::getValue('export_color_attribute')) {
                                                            $line[$field] = $attr['attribute_name'];
                                                        }
                                                    }
                                                }
                                            }

                                            if ($line[$field] == '-') {
                                                $line[$field] = $product_feature_value;
                                            }
                                            unset($product_feature_value);
                                            break;
                                        case 'size':
                                            $product_feature_value = Tools::getValue('export_size_default');
                                            $line[$field] = '-';

                                            if ($this->hasCombinations($p->id)) {
                                                foreach ($p->getAttributeCombinationsById($value['id_product_attribute'], $id_lang) AS $attrk => $attr) {
                                                    if (in_array($attr['id_attribute_group'], Tools::getValue('export_size_attribute'))) {
                                                        $line[$field] = $attr['attribute_name'];
                                                    }
                                                }
                                            }

                                            if ($line[$field] == '-') {
                                                $line[$field] = $product_feature_value;
                                            }
                                            unset($product_feature_value);
                                            break;

                                        case 'shipping':
                                            if (Tools::getValue('export_shipping_info') == 1) {
                                                $line[$field] = ":::" . Tools::ps_round((Tools::getValue('export_additional_sc') == 1 ? Tools::convertPrice($p->additional_shipping_cost, $currency, true) : 0) + Tools::convertPrice($this->getShippingCost($p->price, $p->weight), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            } elseif (Tools::getValue('export_shipping_info') == 2) {
                                                $line[$field] = ":::" . Tools::ps_round((Tools::getValue('export_additional_sc') == 1 ? Tools::convertPrice($p->additional_shipping_cost, $currency, true) : 0) + Tools::convertPrice(Tools::getValue('export_shipping_info_price'), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            }
                                            break;
                                        case 'item_group_id':
                                            $line[$field] = 'GID' . $p->id;
                                            break;
                                        case 'id':
                                            if (Tools::getValue('export_identification') == 'id_product') {
                                                $id = $p->id;
                                            } elseif (Tools::getValue('export_identification') == 'id_combination') {
                                                $id = $value['id_product_attribute'];
                                            } elseif (Tools::getValue('export_identification') == 'id_product_id_combination') {
                                                $id = $p->id . '-' . $value['id_product_attribute'];
                                            } else {
                                                $id = $p->id;
                                            }
                                            $line[$field] = $id;
                                            break;
                                        case 'gtin':
                                            $line[$field] = '';
                                            if (Tools::getValue('export_gtin') == 'nothing') {
                                                $line[$field] = '';
                                            } elseif (Tools::getValue('export_gtin') == 'upc') {
                                                if (Validate::isUpc($value['upc'])) {
                                                    $line[$field] = $value['upc'];
                                                } elseif (Validate::isEan13($value['ean13'])) {
                                                    $line[$field] = $value['ean13'];
                                                } else {
                                                    $line[$field] = '';
                                                }
                                            } elseif (Tools::getValue('export_gtin') == 'ean13') {
                                                if (Validate::isEan13($value['ean13'])) {
                                                    $line[$field] = $value['ean13'];
                                                } elseif (Validate::isUpc($value['upc'])) {
                                                    $line[$field] = $value['upc'];
                                                } else {
                                                    $line[$field] = '';
                                                }
                                            } elseif (Tools::getValue('export_gtin') == 'reference') {
                                                if (isset($p->reference)) {
                                                    $line[$field] = $p->reference;
                                                } elseif (Validate::isUpc($value['upc'])) {
                                                    $line[$field] = $value['upc'];
                                                } elseif (Validate::isEan13($value['ean13'])) {
                                                    $line[$field] = $value['ean13'];
                                                } else {
                                                    $line[$field] = '';
                                                }
                                            } else {
                                                $line[$field] = '';
                                            }

                                            break;
                                        case 'identifier_exists':
                                            if (strlen($line['gtin']) > 1) {
                                                $line[$field] = 'true';
                                            } else {
                                                $line[$field] = 'false';
                                            }
                                            break;
                                        case 'name':
                                            $line[$field] = (Tools::getValue('export_product_name') == 'meta_title' ? $p->meta_title : $p->name);
                                            if (Tools::getValue('export_product_name_format') == 1) {
                                                $line[$field] = ucfirst(strtolower($line[$field]));
                                            } else if (Tools::getValue('export_product_name_format') == 2) {
                                                $line[$field] = ucwords(strtolower($line[$field]));
                                            } else if (Tools::getValue('export_product_name_format') == 3) {
                                                $line[$field] = strtolower($line[$field]);
                                            }
                                            break;
                                        case 'quantity':
                                            $availability_date = '';
                                            $stock = '';
                                            $allow_oosp = $p->isAvailableWhenOutOfStock((int)$p->out_of_stock);
                                            if ($value['quantity'] > 0) {
                                                $stock = 'in_stock';
                                            }

                                            if (($p->available_for_order == 1)) {
                                                if (($allow_oosp == 1 || $allow_oosp == 0) && $value['quantity'] > 0) {
                                                    $stock = 'in_stock';
                                                } elseif ($allow_oosp == 1 && $value['quantity'] <= 0) {
                                                    $stock = 'backorder';
                                                    if (Validate::isDate($value['available_date'])) {
                                                        $availability_date = $value['available_date'];
                                                    } else {
                                                        $availability_date = $default_availability_date;
                                                    }
                                                } elseif ($allow_oosp == 0 && $value['quantity'] <= 0) {
                                                    $stock = 'out_of_stock';
                                                } elseif ($value['quantity'] < 0) {
                                                    $stock = 'in_stock';
                                                }
                                            } else {
                                                $stock = 'out_of_stock';
                                            }

                                            if (Tools::getValue('export_instock_info') == 0) {

                                            } elseif (Tools::getValue('export_instock_info') == 1) {
                                                $stock = 'in_stock';
                                            } elseif (Tools::getValue('export_instock_info') == 2) {
                                                $stock = 'out_of_stock';
                                            } elseif (Tools::getValue('export_instock_info') == 3) {
                                                $stock = 'backorder';
                                                if (Validate::isDate($value['available_date'])) {
                                                    $availability_date = $value['available_date'];
                                                } else {
                                                    $availability_date = $default_availability_date;
                                                }
                                            }

                                            $line[$field] = $stock;
                                            $line['availability_date'] = $availability_date;
                                            break;
                                        case 'inventory':
                                            $line[$field] = $value['quantity'];
                                            break;
                                        case 'brand':
                                            $line[$field] = ($p->manufacturer_name != "" ? $p->manufacturer_name : Tools::getValue('export_manufacturers_default', 'Default'));
                                            break;
                                        case 'include_url':
                                            $line['include_url'] = Context::getContext()->link->getProductLink($p->id, null, null, null, $id_lang, $this->context->shop->id, $value['id_product_attribute']);
                                            break;
                                        case 'additional_image_link':
                                            $line['additional_image_link'] = '';
                                            if (Tools::getValue('export_what_pictures') == 1) {
                                                $images = $p->getImages($id_lang);
                                                foreach ($images as $image) {
                                                    $imagelinks[] = $this->context->link->getImageLink($p->link_rewrite, $p->id . '-' . $image['id_image'], Tools::getValue('export_img'));
                                                }
                                                if (isset($imagelinks[0])) {
                                                    array_shift($imagelinks);
                                                    $line['additional_image_link'] = $imagelinks[0];
                                                } else {
                                                    $line['additional_image_link'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                                }
                                                unset($images);
                                            }
                                            if ($line['additional_image_link'] == '') {
                                                $line['image_url'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                            }
                                            break;
                                        case 'image_url':
                                            $imagelinks = array();
                                            $line['image_url'] = '';

                                            $sql = 'SELECT id_image FROM `' . _DB_PREFIX_ . 'product_attribute_image` WHERE `id_product_attribute`=' . $value['id_product_attribute'];
                                            $images = Db::getInstance()->executeS($sql);

                                            $image_urls = array();
                                            foreach ($images as $image) {
                                                if (isset($image['id_image'])) {
                                                    if ((int)$image['id_image'] > 0) {
                                                        $image_urls[] = $this->context->link->getImageLink($p->link_rewrite, $p->id . '-' . $image['id_image'], Tools::getValue('export_img'));
                                                    }
                                                }
                                                if (isset($image_urls)) {
                                                    $line['image_url'] = (isset($image_urls[1]) ? $image_urls[1] : $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg');
                                                }
                                            }

                                            if ($line['image_url'] == '' && Tools::getValue('export_mainpicture') == 1) {
                                                $imagelinks = array();
                                                $images = $p->getImages($id_lang);
                                                foreach ($images as $image) {
                                                    $imagelinks[] = $this->context->link->getImageLink($p->link_rewrite, $p->id . '-' . $image['id_image'], Tools::getValue('export_img'));
                                                }
                                                if (isset($imagelinks[0])) {
                                                    $line['image_url'] = $imagelinks[0];
                                                } else {
                                                    $line['image_url'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                                }
                                                if ($line['image_url'] == '') {
                                                    $line['image_url'] = $this->context->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                                }
                                            }

                                            if ($line['image_url'] == '') {
                                                $line['image_url'] = Context::getContext()->shop->getBaseUrl(true, true) . 'img/p/' . $this->context->language->iso_code . '-default-' . Tools::getValue('export_img') . '.jpg';
                                            }

                                            unset($imagelinks);
                                            break;
                                        case 'item_subtitle':
                                            $line[$field] = ucfirst($p->name);
                                            $meta = Meta::getProductMetas($p->id, $id_lang, '');
                                            $line['item_subtitle'] = $meta['meta_title'];
                                            break;
                                        case 'description_short':
                                            $description_short = '-';
                                            if (Tools::getValue('export_short_description_what', 'short') == 'short') {
                                                if (Tools::getValue('export_removehtml', 0) != 0) {
                                                    $description_short = strip_tags($p->description_short);
                                                } else {
                                                    $description_short = $p->description_short;
                                                }
                                            } elseif (Tools::getValue('export_short_description_what', 'short') == 'desc') {
                                                if (Tools::getValue('export_removehtml', 0) != 0) {
                                                    $description_short = strip_tags($p->description);
                                                } else {
                                                    $description_short = $p->description;
                                                }
                                            }
                                            $line[$field] = (strlen(trim($description_short)) > 0 ? trim($description_short) : '-');
                                            break;
                                        case 'item_category':
                                            $line[$field] = $category_default->name;
                                            break;
                                        case 'condition':
                                            $line[$field] = $p->condition;
                                            break;
                                        case 'price_tin':
                                            if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                                $line[$field] = Tools::ps_round($p->getPrice(true, $value['id_product_attribute'], 2, null, false, false), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            } else {
                                                $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(true, $value['id_product_attribute'], 2, null, false, false), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            }
                                            break;
                                        case 'price_tex':
                                            if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                                $line[$field] = Tools::ps_round($p->getPrice(false, $value['id_product_attribute'], 2, null, false, false), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            } else {
                                                $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(false, $value['id_product_attribute'], 2, null, false, false), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            }
                                            break;
                                        case 'sale_price_tin':
                                            if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                                $line[$field] = Tools::ps_round($p->getPrice(true, $value['id_product_attribute'], 2, null, false, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            } else {
                                                $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(true, $value['id_product_attribute'], 2, null, false, true), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            }
                                            break;
                                        case 'sale_price_tex':
                                            if (Configuration::get('PS_CURRENCY_DEFAULT') != $this->context->currency->id && Tools::getValue('export_specific')) {
                                                $line[$field] = Tools::ps_round($p->getPrice(false, $value['id_product_attribute'], 2, null, false, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            } else {
                                                $line[$field] = Tools::ps_round(Tools::convertPrice($p->getPrice(false, $value['id_product_attribute'], 2, null, false, true), $currency, true), _PS_PRICE_COMPUTE_PRECISION_) . ' ' . $currency->iso_code;
                                            }
                                            break;
                                        case 'google_product_category':
                                            if (isset($pixel_google_categories[$p->id_category_default]['id_google'])) {
                                                $line['google_product_category'] = $pixel_google_categories[$p->id_category_default]['id_google'];
                                            } else {
                                                $line['google_product_category'] = 0;
                                            }
                                            break;
                                        case 'contextual_keywords':
                                            $name = explode(" ", $p->name);
                                            $line[$field] = implode(';', $name);
                                            break;
                                        case 'product_type':
                                            $category_names_array = array();
                                            foreach (Product::getProductCategories($p->id) AS $pcatid) {
                                                if (!isset($category_names[$pcatid])) {
                                                    $category_names[$pcatid] = new Category($pcatid, Tools::getValue('export_language'));
                                                    $category_names_array[] = ($export_product_type_id == 1 ? $pcatid . '-' : '') . $category_names[$pcatid]->name;
                                                } else {
                                                    $category_names_array[] = ($export_product_type_id == 1 ? $pcatid . '-' : '') . $category_names[$pcatid]->name;
                                                }
                                            }
                                            $line['product_type'] = implode(" > ", $category_names_array);
                                            break;
                                        case 'weight':
                                            $line['weight'] = number_format($value['weight'], 2, '.', '') . ' ' . $weight_unit;
                                            break;
                                        case 'store_code':
                                            $line['store_code'] = Tools::getValue('export_sc');
                                            break;
                                    }

                                    if (!array_key_exists($field, $line)) {
                                        $line[$field] = '';
                                    }
                                }


                                $include = 1;

                                if (Tools::getValue('export_manufacturers') != 99999) {
                                    if ($p->id_manufacturer != Tools::getValue('export_manufacturers')) {
                                        $include = 0;
                                    }
                                }
                                if (Tools::getValue('export_suppliers') != 99999) {
                                    if (Supplier::getProductInformationsBySupplier(Tools::getValue('export_suppliers'), $p->id) == null) {
                                        $include = 0;
                                    }
                                }

                                if (Tools::getValue('export_nophoto_exclude') == 1) {
                                    if (is_array($images)) {
                                        if (count($images) <= 0) {
                                            $include = 0;
                                        }
                                    } else {
                                        $include = 0;
                                    }
                                }

                                if ($include == 1) {
                                    if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                                        $new_line = array();
                                        foreach ($line as $lkey => $litem) {
                                            $lkey = $this->changeKeyToGoogleFeed($lkey);
                                            $new_line[$lkey] = $litem;
                                        }
                                        fputcsv($f, $line, $delimiter, '"');
                                    } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                                        $new_line = array();
                                        foreach ($line as $lkey => $litem) {
                                            $lkey = $this->changeKeyToGoogleFeed($lkey);
                                            $new_line[$lkey] = $litem;
                                        }
                                        $xml_array[] = $new_line;
                                    }
                                }
                            }
                        }
                    }
                    break;
            }

            if (Tools::getValue('export_file_format', 'csv') == 'csv') {

            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                $export_type = Tools::getValue('export_type', 'object');
                $xml = new SimpleXMLElement('<?xml version="1.0"?><feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0"><title>' . $export_type . ' - ' . Context::getContext()->shop->name . '</title><link rel="self" href="' . Context::getContext()->shop->getBaseUrl(true, true) . '"/><updated>' . date('c') . '</updated></feed>');
                $this->array_to_xml($xml_array, $xml);
                echo $xml->asXML();
            }

            fclose($f);
            die();
        }
    }

    public function initContent()
    {
        if (Tools::getValue('ajax', 'false') == 'false') {
            $this->content = $this->renderView() . $this->renderScript();
            parent::initContent();
        } else {
            $this->ajaxProcess();
        }
    }

    public function getWarehouses($id_warehouses)
    {
        return $id_warehouses['id_warehouse'];
    }

    public function renderScript()
    {

    }

    private function initList()
    {
        if (Tools::getValue('deletegms', 'false') != 'false') {
            $this->context->controller->confirmations[] = $this->l('Feed removed');
            $gms = new gms(Tools::getValue('id_gms'));
            $gms->delete();
        }
        $this->helper_fields_list = array(
            'id_gms' => array(
                'title' => $this->l('Id'),
                'width' => 140,
                'type' => 'text',
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 140,
                'type' => 'text',
            ),
            'url' => array(
                'title' => $this->l('Url to feed'),
                'width' => 140,
                'type' => 'text',
            ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->no_link = true;
        $helper->simple_header = true;
        // Actions to be displayed in the "Actions" column
        $helper->actions = array('edit', 'delete');
        $helper->identifier = 'id_gms';
        $helper->show_toolbar = true;
        $helper->title = $this->l('Saved feed settings');
        $helper->table = 'gms';
        $helper->token = Tools::getAdminTokenLite('AdminExportProductsFeedGoogle');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->context->controller->module->name;
        return $helper->generateList(gms::getAll(), $this->helper_fields_list);
    }

    public function returnEditInfo()
    {
        if (Tools::getValue('updategms', 'false') != 'false') {
            return $this->returnAlert($this->l('You are in feed edit mode. To create new feed click button below.'));
        }
    }

    public function returnAlert($msg)
    {
        $this->context->smarty->assign('returnAlert', $msg);
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'gmfeed/views/returnAlert.tpl');
    }

    public function getCategories($id_lang, $active, $id_shop)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'category` c
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`
			WHERE ' . ($id_shop ? 'cl.`id_shop` = ' . (int)$id_shop : '') . ' ' . ($id_lang ? 'AND `id_lang` = ' . (int)$id_lang : '') . '
			' . ($active ? 'AND `active` = 1' : '') . '
			' . (!$id_lang ? 'GROUP BY c.id_category' : '') . '
			ORDER BY c.`level_depth` ASC, c.`position` ASC');

        return $result;
    }

    /**
     * @param $array
     * @param $xml
     */
    public function array_to_xml($array, &$xml)
    {
        $export_type = Tools::getValue('export_type', 'object');
        foreach ($array as $key => $value) {
            $key = str_replace('sale_price_tin', 'sale_price', $key);
            $key = str_replace('sale_price_tex', 'sale_price', $key);
            $key = str_replace('price_tex', 'price', $key);
            $key = str_replace('price_tin', 'price', $key);
            $key = str_replace('name', 'title', $key);
            $key = str_replace('quantity', 'availability', $key);
            $key = str_replace('include_url', 'link', $key);
            $key = str_replace('description_short', 'description', $key);
            $key = str_replace('image_url', 'image_link', $key);

            if (is_array($value)) {
                if ($key == "shipping") {
                    $subnode = $xml->addChild("shipping", '', 'http://base.google.com/ns/1.0');
                    $this->array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("entry");
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml->addChild($key, htmlspecialchars("$value"), 'http://base.google.com/ns/1.0');
            }
        }
    }

    public function changeKeyToGoogleFeed($key)
    {
        $key = str_replace('sale_price_tin', 'sale_price', $key);
        $key = str_replace('sale_price_tex', 'sale_price', $key);
        $key = str_replace('price_tex', 'price', $key);
        $key = str_replace('price_tin', 'price', $key);
        $key = str_replace('name', 'title', $key);
        $key = str_replace('quantity', 'availability', $key);
        $key = str_replace('include_url', 'link', $key);
        $key = str_replace('description_short', 'description', $key);
        $key = str_replace('image_url', 'image_link', $key);
        $key = str_replace('item_category', 'product_type', $key);
        return $key;
    }

    function getShippingCost($price, $weight, $id_zone = 1)
    {
        $carrier_by_weight = $this->default_carrier->getDeliveryPriceByWeight($weight, $id_zone);
        if ($carrier_by_weight != false) {
            return $carrier_by_weight;
        }

        $carrier_by_price = $this->default_carrier->getDeliveryPriceByPrice($price, $id_zone, Tools::getValue('export_currency'));
        if ($carrier_by_price != false) {
            return $carrier_by_price;
        }

        foreach ($this->all_carriers AS $carrier) {
            $carrier = new Carrier($carrier['id_carrier']);
            if ($carrier->shipping_method == 2) {
                $carrier_by_price = $carrier->getDeliveryPriceByPrice($price, $id_zone, Tools::getValue('export_currency'));
                if ($carrier_by_price != false) {
                    return $carrier_by_price;
                }
            } elseif ($carrier->shipping_method == 1) {
                $carrier_by_weight = $carrier->getDeliveryPriceByWeight($weight, $id_zone);
                if ($carrier_by_weight != false) {
                    return $carrier_by_weight;
                }
            }
        }

    }
}
