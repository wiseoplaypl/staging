<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2020 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */


class AdminExportProductsController extends ModuleAdminController
{
    public $available_fields;

    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->meta_title = $this->l('Export Products');

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
        if (Tools::getValue('export_format') == 'ps177') {
            $this->available_fields['combinations'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'product_reference' => array('label' => 'Product reference'),
                'group' => array('label' => 'Attribute (Name:Type:Position)'),
                'attribute' => array('label' => 'Value (Value:Position)'),
                'supplier_reference' => array('label' => 'Supplier reference'),
                'reference' => array('label' => 'Reference'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'mpn' => array('label' => 'MPN'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'impact_price' => array('label' => 'Impact on price'),
                'ecotax' => array('label' => 'Ecotax'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'low_stock_threshold' => array('label' => 'Low stock threshold'),
                'low_stock_alert' => array('label' => 'Send me an email when the quantity is under this level'),
                'weight' => array('label' => 'Impact on weight'),
                'default_on' => array('label' => 'Default (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Combination availability date'),
                'image_position' => array('label' => 'Image position'),
                'image_url' => array('label' => 'Image URL'),
                'image_alt_texts' => array('label' => 'Image alt texts'),
                'shop' => array(
                    'label' => 'ID / Name of shop',
                    'help' => 'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                ),
                'include_url' => array('label' => 'Link to product page'),
            );
            $this->available_fields['products'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'active' => array('label' => 'Active (0/1)'),
                'name' => array('label' => 'Name'),
                'categories' => array('label' => 'Categories (x,y,z...)'),
                'price_tex' => array('label' => 'Price tax excluded'),
                'price_tin' => array('label' => 'Price tax included'),
                'id_tax_rules_group' => array('label' => 'Tax rules ID'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'on_sale' => array('label' => 'On sale (0/1)'),
                'reduction_price' => array('label' => 'Discount amount'),
                'reduction_percent' => array('label' => 'Discount percent'),
                'reduction_from' => array('label' => 'Discount from (yyyy-mm-dd)'),
                'reduction_to' => array('label' => 'Discount to (yyyy-mm-dd)'),
                'reference' => array('label' => 'Reference #'),
                'supplier_reference' => array('label' => 'Supplier reference #'),
                'supplier_name' => array('label' => 'Supplier'),
                'manufacturer_name' => array('label' => 'Manufacturer'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'mpn' => array('label' => 'MPN'),
                'ecotax' => array('label' => 'Ecotax'),
                'width' => array('label' => 'Width'),
                'height' => array('label' => 'Height'),
                'depth' => array('label' => 'Depth'),
                'weight' => array('label' => 'Weight'),
                'delivery_in_stock' => array('label' => 'Delivery time of in-stock products'),
                'delivery_out_stock' => array('label' => 'Delivery time of out-of-stock products'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'low_stock_threshold' => array('label' => 'Low stock threshold'),
                'low_stock_alert' => array('label' => 'Send me an email when the quantity is under this level'),
                'visibility' => array('label' => 'Visibility'),
                'additional_shipping_cost' => array('label' => 'Additional shipping cost'),
                'unity' => array('label' => 'Unit for the unit price'),
                'unit_price' => array('label' => 'Unit price'),
                'description_short' => array('label' => 'Short description (Summary)'),
                'description' => array('label' => 'Description'),
                'tags' => array('label' => 'Tags (x,y,z...)'),
                'meta_title' => array('label' => 'Meta title'),
                'meta_keywords' => array('label' => 'Meta keywords'),
                'meta_description' => array('label' => 'Meta description'),
                'link_rewrite' => array('label' => 'URL rewritten'),
                'available_now' => array('label' => 'Text when in stock'),
                'available_later' => array('label' => 'Text when backorder allowed'),
                'available_for_order' => array('label' => 'Available for order (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Product available date'),
                'date_add' => array('label' => 'Product creation date'),
                'show_price' => array('label' => 'Show price (0 = No, 1 = Yes)'),
                'image' => array('label' => 'Image URLs (x,y,z...)'),
                'image_alt_texts' => array('label' => 'Image alt texts'),
                'delete_existing_images' => array('label' => 'Delete existing images (0 = No, 1 = Yes)'),
                'features' => array('label' => 'Feature (Name:Value:Position:Customized)'),
                'online_only' => array('label' => 'Available online only (0 = No, 1 = Yes)'),
                'condition' => array('label' => 'Condition'),
                'customizable' => array('label' => 'Customizable (0 = No, 1 = Yes)'),
                'uploadable_files' => array('label' => 'Uploadable files (0 = No, 1 = Yes)'),
                'text_fields' => array('label' => 'Text fields (0 = No, 1 = Yes)'),
                'out_of_stock' => array('label' => 'Action when out of stock'),
                'is_virtual' => array('label' => 'Virtual product (0 = No, 1 = Yes)'),
                'file_url' => array('label' => 'File URL'),
                'nb_downloadable' => array('label' => 'Number of allowed downloads'),
                'date_expiration' => array('label' => 'Expiration date (yyyy-mm-dd)'),
                'nb_days_accessible' => array('label' => 'Number of days'),
                'shop' => array('label' => 'ID / Name of shop'),
                'advanced_stock_management' => array('label' => 'Advanced Stock Management'),
                'depends_on_stock' => array('label' => 'Depends on stock'),
                'warehouse' => array('label' => 'Warehouse'),
                'accessories' => array('label' => 'Accessories (x,y,z...)'),
                'include_url' => array('label' => 'link to product'),
            );
        } elseif (Tools::getValue('export_format') == 'ps173') {
            $this->available_fields['combinations'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'product_reference' => array('label' => 'Product reference'),
                'group' => array('label' => 'Attribute (Name:Type:Position)'),
                'attribute' => array('label' => 'Value (Value:Position)'),
                'supplier_reference' => array('label' => 'Supplier reference'),
                'reference' => array('label' => 'Reference'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'impact_price' => array('label' => 'Impact on price'),
                'ecotax' => array('label' => 'Ecotax'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'low_stock_threshold' => array('label' => 'Low stock threshold'),
                'low_stock_alert' => array('label' => 'Send me an email when the quantity is under this level'),
                'weight' => array('label' => 'Impact on weight'),
                'default_on' => array('label' => 'Default (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Combination availability date'),
                'image_position' => array('label' => 'Image position'),
                'image_url' => array('label' => 'Image URL'),
                'image_alt_texts' => array('label' => 'Image alt texts'),
                'shop' => array(
                    'label' => 'ID / Name of shop',
                    'help' => 'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                ),
                'include_url' => array('label' => 'Link to product page'),
            );
            $this->available_fields['products'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'active' => array('label' => 'Active (0/1)'),
                'name' => array('label' => 'Name'),
                'categories' => array('label' => 'Categories (x,y,z...)'),
                'price_tex' => array('label' => 'Price tax excluded'),
                'price_tin' => array('label' => 'Price tax included'),
                'id_tax_rules_group' => array('label' => 'Tax rules ID'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'on_sale' => array('label' => 'On sale (0/1)'),
                'reduction_price' => array('label' => 'Discount amount'),
                'reduction_percent' => array('label' => 'Discount percent'),
                'reduction_from' => array('label' => 'Discount from (yyyy-mm-dd)'),
                'reduction_to' => array('label' => 'Discount to (yyyy-mm-dd)'),
                'reference' => array('label' => 'Reference #'),
                'supplier_reference' => array('label' => 'Supplier reference #'),
                'supplier_name' => array('label' => 'Supplier'),
                'manufacturer_name' => array('label' => 'Manufacturer'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'ecotax' => array('label' => 'Ecotax'),
                'width' => array('label' => 'Width'),
                'height' => array('label' => 'Height'),
                'depth' => array('label' => 'Depth'),
                'weight' => array('label' => 'Weight'),
                'delivery_in_stock' => array('label' => 'Delivery time of in-stock products'),
                'delivery_out_stock' => array('label' => 'Delivery time of out-of-stock products'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'low_stock_threshold' => array('label' => 'Low stock threshold'),
                'low_stock_alert' => array('label' => 'Send me an email when the quantity is under this level'),
                'visibility' => array('label' => 'Visibility'),
                'additional_shipping_cost' => array('label' => 'Additional shipping cost'),
                'unity' => array('label' => 'Unit for the unit price'),
                'unit_price' => array('label' => 'Unit price'),
                'description_short' => array('label' => 'Short description (Summary)'),
                'description' => array('label' => 'Description'),
                'tags' => array('label' => 'Tags (x,y,z...)'),
                'meta_title' => array('label' => 'Meta title'),
                'meta_keywords' => array('label' => 'Meta keywords'),
                'meta_description' => array('label' => 'Meta description'),
                'link_rewrite' => array('label' => 'URL rewritten'),
                'available_now' => array('label' => 'Text when in stock'),
                'available_later' => array('label' => 'Text when backorder allowed'),
                'available_for_order' => array('label' => 'Available for order (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Product available date'),
                'date_add' => array('label' => 'Product creation date'),
                'show_price' => array('label' => 'Show price (0 = No, 1 = Yes)'),
                'image' => array('label' => 'Image URLs (x,y,z...)'),
                'image_alt_texts' => array('label' => 'Image alt texts'),
                'delete_existing_images' => array('label' => 'Delete existing images (0 = No, 1 = Yes)'),
                'features' => array('label' => 'Feature (Name:Value:Position:Customized)'),
                'online_only' => array('label' => 'Available online only (0 = No, 1 = Yes)'),
                'condition' => array('label' => 'Condition'),
                'customizable' => array('label' => 'Customizable (0 = No, 1 = Yes)'),
                'uploadable_files' => array('label' => 'Uploadable files (0 = No, 1 = Yes)'),
                'text_fields' => array('label' => 'Text fields (0 = No, 1 = Yes)'),
                'out_of_stock' => array('label' => 'Action when out of stock'),
                'is_virtual' => array('label' => 'Virtual product (0 = No, 1 = Yes)'),
                'file_url' => array('label' => 'File URL'),
                'nb_downloadable' => array('label' => 'Number of allowed downloads'),
                'date_expiration' => array('label' => 'Expiration date (yyyy-mm-dd)'),
                'nb_days_accessible' => array('label' => 'Number of days'),
                'shop' => array('label' => 'ID / Name of shop'),
                'advanced_stock_management' => array('label' => 'Advanced Stock Management'),
                'depends_on_stock' => array('label' => 'Depends on stock'),
                'warehouse' => array('label' => 'Warehouse'),
                'accessories' => array('label' => 'Accessories (x,y,z...)'),
                'include_url' => array('label' => 'link to product'),
            );
        } elseif (Tools::getValue('export_format') == 'ps170-ps172') {
            $this->available_fields['combinations'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'product_reference' => array('label' => 'Product reference'),
                'group' => array('label' => 'Attribute (Name:Type:Position)'),
                'attribute' => array('label' => 'Value (Value:Position)'),
                'supplier_reference' => array('label' => 'Supplier reference'),
                'reference' => array('label' => 'Reference'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'impact_price' => array('label' => 'Impact on price'),
                'ecotax' => array('label' => 'Ecotax'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'weight' => array('label' => 'Impact on weight'),
                'default_on' => array('label' => 'Default (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Combination availability date'),
                'image_position' => array('label' => 'Image position'),
                'image_url' => array('label' => 'Image URL'),
                'image_alt_texts' => array('label' => 'Image alt texts'),
                'shop' => array(
                    'label' => 'ID / Name of shop',
                    'help' => 'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                ),
                'include_url' => array('label' => 'Link to product page'),
            );
            $this->available_fields['products'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'active' => array('label' => 'Active (0/1)'),
                'name' => array('label' => 'Name'),
                'categories' => array('label' => 'Categories (x,y,z...)'),
                'price_tex' => array('label' => 'Price tax excluded'),
                'price_tin' => array('label' => 'Price tax included'),
                'id_tax_rules_group' => array('label' => 'Tax rules ID'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'on_sale' => array('label' => 'On sale (0/1)'),
                'reduction_price' => array('label' => 'Discount amount'),
                'reduction_percent' => array('label' => 'Discount percent'),
                'reduction_from' => array('label' => 'Discount from (yyyy-mm-dd)'),
                'reduction_to' => array('label' => 'Discount to (yyyy-mm-dd)'),
                'reference' => array('label' => 'Reference #'),
                'supplier_reference' => array('label' => 'Supplier reference #'),
                'supplier_name' => array('label' => 'Supplier'),
                'manufacturer_name' => array('label' => 'Manufacturer'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'ecotax' => array('label' => 'Ecotax'),
                'width' => array('label' => 'Width'),
                'height' => array('label' => 'Height'),
                'depth' => array('label' => 'Depth'),
                'weight' => array('label' => 'Weight'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'visibility' => array('label' => 'Visibility'),
                'additional_shipping_cost' => array('label' => 'Additional shipping cost'),
                'unity' => array('label' => 'Unit for the unit price'),
                'unit_price' => array('label' => 'Unit price'),
                'description_short' => array('label' => 'Short description (Summary)'),
                'description' => array('label' => 'Description'),
                'tags' => array('label' => 'Tags (x,y,z...)'),
                'meta_title' => array('label' => 'Meta title'),
                'meta_keywords' => array('label' => 'Meta keywords'),
                'meta_description' => array('label' => 'Meta description'),
                'link_rewrite' => array('label' => 'URL rewritten'),
                'available_now' => array('label' => 'Text when in stock'),
                'available_later' => array('label' => 'Text when backorder allowed'),
                'available_for_order' => array('label' => 'Available for order (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Product available date'),
                'date_add' => array('label' => 'Product creation date'),
                'show_price' => array('label' => 'Show price (0 = No, 1 = Yes)'),
                'image' => array('label' => 'Image URLs (x,y,z...)'),
                'image_alt_texts' => array('label' => 'Image alt texts'),
                'delete_existing_images' => array('label' => 'Delete existing images (0 = No, 1 = Yes)'),
                'features' => array('label' => 'Feature (Name:Value:Position:Customized)'),
                'online_only' => array('label' => 'Available online only (0 = No, 1 = Yes)'),
                'condition' => array('label' => 'Condition'),
                'customizable' => array('label' => 'Customizable (0 = No, 1 = Yes)'),
                'uploadable_files' => array('label' => 'Uploadable files (0 = No, 1 = Yes)'),
                'text_fields' => array('label' => 'Text fields (0 = No, 1 = Yes)'),
                'out_of_stock' => array('label' => 'Action when out of stock'),
                'is_virtual' => array('label' => 'Virtual product (0 = No, 1 = Yes)'),
                'file_url' => array('label' => 'File URL'),
                'nb_downloadable' => array('label' => 'Number of allowed downloads'),
                'date_expiration' => array('label' => 'Expiration date (yyyy-mm-dd)'),
                'nb_days_accessible' => array('label' => 'Number of days'),
                'shop' => array('label' => 'ID / Name of shop'),
                'advanced_stock_management' => array('label' => 'Advanced Stock Management'),
                'depends_on_stock' => array('label' => 'Depends on stock'),
                'warehouse' => array('label' => 'Warehouse'),
                'include_url' => array('label' => 'link to product'),
            );
        } else {
            $this->available_fields['combinations'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'product_reference' => array('label' => 'Product reference'),
                'group' => array('label' => 'Attribute (Name:Type:Position)'),
                'attribute' => array('label' => 'Value (Value:Position)'),
                'supplier_reference' => array('label' => 'Supplier reference'),
                'reference' => array('label' => 'Reference'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'impact_price' => array('label' => 'Impact on price'),
                'ecotax' => array('label' => 'Ecotax'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'weight' => array('label' => 'Impact on weight'),
                'default_on' => array('label' => 'Default (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Combination availability date'),
                'image_position' => array('label' => 'Image position'),
                'image_url' => array('label' => 'Image URL'),
                'delete_existing_images' => array('label' => 'Delete existing images (0 = No, 1 = Yes)'),
                'shop' => array(
                    'label' => 'ID / Name of shop',
                    'help' => 'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                ),
                'include_url' => array('label' => 'Link to product page'),
            );
            $this->available_fields['products'] = array(
                'id' => array('label' => 'Product ID (ID)'),
                'active' => array('label' => 'Active (0/1)'),
                'name' => array('label' => 'Name'),
                'categories' => array('label' => 'Categories (x,y,z...)'),
                'price_tex' => array('label' => 'Price tax excluded'),
                'price_tin' => array('label' => 'Price tax included'),
                'id_tax_rules_group' => array('label' => 'Tax rules ID'),
                'wholesale_price' => array('label' => 'Wholesale price'),
                'on_sale' => array('label' => 'On sale (0/1)'),
                'reduction_price' => array('label' => 'Discount amount'),
                'reduction_percent' => array('label' => 'Discount percent'),
                'reduction_from' => array('label' => 'Discount from (yyyy-mm-dd)'),
                'reduction_to' => array('label' => 'Discount to (yyyy-mm-dd)'),
                'reference' => array('label' => 'Reference #'),
                'supplier_reference' => array('label' => 'Supplier reference #'),
                'supplier_name' => array('label' => 'Supplier'),
                'manufacturer_name' => array('label' => 'Manufacturer'),
                'ean13' => array('label' => 'EAN13'),
                'upc' => array('label' => 'UPC'),
                'ecotax' => array('label' => 'Ecotax'),
                'width' => array('label' => 'Width'),
                'height' => array('label' => 'Height'),
                'depth' => array('label' => 'Depth'),
                'weight' => array('label' => 'Weight'),
                'quantity' => array('label' => 'Quantity'),
                'minimal_quantity' => array('label' => 'Minimal quantity'),
                'visibility' => array('label' => 'Visibility'),
                'additional_shipping_cost' => array('label' => 'Additional shipping cost'),
                'unity' => array('label' => 'Unit for the unit price'),
                'unit_price' => array('label' => 'Unit price'),
                'description_short' => array('label' => 'Short description (Summary)'),
                'description' => array('label' => 'Description'),
                'tags' => array('label' => 'Tags (x,y,z...)'),
                'meta_title' => array('label' => 'Meta title'),
                'meta_keywords' => array('label' => 'Meta keywords'),
                'meta_description' => array('label' => 'Meta description'),
                'link_rewrite' => array('label' => 'URL rewritten'),
                'available_now' => array('label' => 'Text when in stock'),
                'available_later' => array('label' => 'Text when backorder allowed'),
                'available_for_order' => array('label' => 'Available for order (0 = No, 1 = Yes)'),
                'available_date' => array('label' => 'Product available date'),
                'date_add' => array('label' => 'Product creation date'),
                'show_price' => array('label' => 'Show price (0 = No, 1 = Yes)'),
                'image' => array('label' => 'Image URLs (x,y,z...)'),
                'delete_existing_images' => array('label' => 'Delete existing images (0 = No, 1 = Yes)'),
                'features' => array('label' => 'Feature (Name:Value:Position:Customized)'),
                'online_only' => array('label' => 'Available online only (0 = No, 1 = Yes)'),
                'condition' => array('label' => 'Condition'),
                'customizable' => array('label' => 'Customizable (0 = No, 1 = Yes)'),
                'uploadable_files' => array('label' => 'Uploadable files (0 = No, 1 = Yes)'),
                'text_fields' => array('label' => 'Text fields (0 = No, 1 = Yes)'),
                'out_of_stock' => array('label' => 'Action when out of stock'),
                'shop' => array(
                    'label' => 'ID / Name of shop',
                    'help' => 'Ignore this field if you don\'t use the Multistore tool. If you leave this field empty, the default shop will be used.',
                ),
                'advanced_stock_management' => array(
                    'label' => 'Advanced Stock Management',
                    'help' => 'Enable Advanced Stock Management on product (0 = No, 1 = Yes).',
                ),
                'depends_on_stock' => array(
                    'label' => 'Depends on stock',
                    'help' => '0 = Use quantity set in product, 1 = Use quantity from warehouse.',
                ),
                'warehouse' => array(
                    'label' => 'Warehouse',
                    'help' => 'ID of the warehouse to set as storage.'
                ),
                'include_url' => array('label' => 'link to product'),
            );
        }

        $this->available_fields['categories'] = array(
            'id' => array('label' => 'Category ID'),
            'active' => array('label' => 'Active (0/1)'),
            'name' => array('label' => 'Name'),
            'id_parent' => array('label' => 'Parent category'),
            'is_root_category' => array('label' => 'Root category (0/1)'),
            'description' => array('label' => 'Description'),
            'meta_title' => array('label' => 'Meta title'),
            'meta_keywords' => array('label' => 'Meta keywords'),
            'meta_description' => array('label' => 'Meta description'),
            'link_rewrite' => array('label' => 'URL rewritten'),
            'image' => array('label' => 'Image URL'),
            'id_shop_default' => array('label' => 'ID / Name of shop'),
        );


    }

    public function addProfitMargin($price)
    {
        if (Tools::getValue('export_increase_price') == 1 && (int)Tools::getValue('export_profit_margin') > 0) {
            return number_format($price + ($price * Tools::getValue('export_profit_margin') / 100), 2, '.', '');
        }
        return $price;
    }


    public function getBaseLinkOld($id_shop = null, $ssl = null)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null)
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null)
            $shop = new Shop($id_shop);
        else
            $shop = Context::getContext()->shop;

        $base = (($ssl && Configuration::get('PS_SSL_ENABLED')) ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain);

        return $base . $shop->getBaseURI();
    }

    public function psversion($part = 1)
    {

        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    public function renderView()
    {
        return $this->renderConfigurationForm();
    }

    public function loadScript()
    {
        $this->context->smarty->assign('feed_updated', $this->l('URL of product\'s feed updated'));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'exportproducts/script.tpl');
    }

    public function heading()
    {
        $shop = new ShopUrl($this->context->shop->id);
        return "
        <div class='alert alert-info'>
        " . $this->l('This form generates a .csv file that is ready to import with prestashop\'s CSV IMPORT tool.') . "<br/>
        " . $this->l('Define what kind of products\' feed you want to create and press Export button') . "<br/>
        " . $this->l('Module will generate a .csv file and your browser will download it immediately') . "
        </div>
        <div class='alert alert-info'>
            " . $this->l('Optionally you can use an URL than generates feed of products (feed uses filters defined below in export options form)') . "<br/><br/>
            <button class=\"btn btn-default show-links\"><i class=\"process-icon-ok\"></i>" . $this->l('Show links') . "</button>
            <br/><br/>
            <div class='hide'>
            <div style='-ms-word-break: break-all; word-break: break-all; word-break: break-word;-webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto;'><strong>secured url:</strong><br/><span> " . $shop->getUrl(true) . "modules/exportproducts/feed.php?</span><span class='feedurl'></span></div><br/><hr/><br/>
            <div style='-ms-word-break: break-all; word-break: break-all; word-break: break-word;-webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto;'><strong>non-secured url:</strong><br/><span> " . $shop->getUrl(false) . "modules/exportproducts/feed.php?</span><span class='feedurl'></span></div>
            </div>
        </div>
        " . $this->loadScript();
    }

    public function renderConfigurationForm()
    {
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $langs = Language::getLanguages();
        $id_shop = (int)$this->context->shop->id;

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
            'id_option' => 9999999999,
            'name' => 'All'
        );

        foreach ($cats as $key => $cat) {
            $categories[] = array(
                'id_option' => $cat['id_category'],
                'name' => $cat['name']
            );
        }

        $manufacturers[] = array(
            'id_option' => 9999999999,
            'name' => 'All'
        );
        foreach (Manufacturer::getManufacturers(false, $this->context->language->id, false) as $key => $man) {
            $manufacturers[] = array(
                'id_option' => $man['id_manufacturer'],
                'name' => $man['name']
            );
        }

        $suppliers[] = array(
            'id_option' => 9999999999,
            'name' => 'All'
        );
        foreach (Supplier::getSuppliers(false, $this->context->language->id, false) as $key => $man) {
            $suppliers[] = array(
                'id_option' => $man['id_supplier'],
                'name' => $man['name']
            );
        }


        $what_to_export = array(
            array(
                'id_option' => 'products',
                'name' => $this->l('Products')
            ),
            array(
                'id_option' => 'combinations',
                'name' => $this->l('Combinations')
            ),
            array(
                'id_option' => 'categories',
                'name' => $this->l('Categories')
            ),
        );

        $export_type = array(
            array(
                'id_option' => 'ps16',
                'name' => $this->l('1.6.x')
            ),
            array(
                'id_option' => 'ps170-ps172',
                'name' => $this->l('1.7.0.0 to 1.7.2.x')
            ),
            array(
                'id_option' => 'ps173',
                'name' => $this->l('1.7.3.0 to 1.7.6.x')
            ),
            array(
                'id_option' => 'ps177',
                'name' => $this->l('1.7.7+')
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
                'type' => 'select',
                'label' => $this->l('Type of file module will generate'),
                'desc' => $this->l('Module allows to generate CSV or XML file. Here you can decide what format of file module will create'),
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
                'label' => $this->l('CSV/XML file structure'),
                'name' => 'export_format',
                'class' => 't export_format',
                'value' => 'ps16',
                'options' => array(
                    'query' => $export_type,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'desc' => $this->l('Select version of PrestaShop where you want to import the CSV file - module will generate CSV file with format for selected version of PrestaShop') . '<div class="alert alert-info">' . $this->l('Default "Import csv" PrestaShop\'s 1.6 tool accepts different file structure than "import csv" tool in PretaShop 1.7.3+') . "<br/>" . $this->l('Because of this you can select format of CSV file that module will generate') . '</div>',
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
                'label' => $this->l('Filter products by date'),
                'name' => 'export_activate_date',
                'class' => 't',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
                'desc' => $this->l('You can export products added during specific period. You can create such filter when you will activate this option')
            ),
            array(
                'type' => 'datetime',
                'label' => $this->l('Date from'),
                'name' => 'export_dateFrom',
                'class' => 'datepicker exportDateFrom',
                'desc' => $this->l('Module will not export products added before this date')
            ),
            array(
                'type' => 'datetime',
                'label' => $this->l('Date to'),
                'name' => 'export_dateTo',
                'class' => 'datepicker exportDateto',
                'desc' => $this->l('Module will not export products added before after date')
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
                'desc' => $this->l('The character to separate the fields in CSV file') . '. ' . $this->l('Usually semicolon ";" - it is a default character in PrestaShop Import CSV tool. But you can change it to any other character.')
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Multiple value separator'),
                'name' => 'export_separator',
                'desc' => $this->l('The character to separate the various values') . '. ' . $this->l('Usually comma "," - it is a default character in PrestaShop Import CSV tool. But you can change it to any other character.')
            ),
            array(
                'type' => 'radio',
                'label' => $this->l('Products\'s availability'),
                'name' => 'export_active',
                'values' => array(
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => 'Export all products.'
                    ),
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => 'Export only active products'
                    ),
                ),
                'is_bool' => true,
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
            /**
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
             * **/
            array(
                'type' => 'categories',
                'tree' => array('id' => 'export_categories', 'use_search' => true, 'use_checkbox' => true, 'selected_categories' => explode("|", Configuration::get('ep_export_categories'))),
                'label' => $this->l('Product Categories'),
                'desc' => $this->l('Choose a product category you wish to export').'. '.$this->l('If you will not select categories - module will export whole catalog'),
                'name' => 'export_categories',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Category field format: ID'),
                'desc' => $this->l('Field will contain ID of category instead its name (as it is by default). Please note that prestashop IMPORT CSV tool does not accept ID of category. PrestaShop Import tool uses category name.'),
                'name' => 'export_category_format',
                'class' => 't',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Delete existing images'),
                'desc' => $this->l('Choose if you want to remove existing images of products / combinations'),
                'name' => 'delete_images',
                'class' => 't export_tax',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Include link to product'),
                'desc' => $this->l('Great option if you want to export products with current link to product page'),
                'name' => 'include_url',
                'class' => 't export_tax',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
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
                'type' => 'select',
                'label' => $this->l('Increase price'),
                'desc' => $this->l('Great option if you want to add some profit margin to products\' prices. This increase price of product base price.'),
                'name' => 'export_increase_price',
                'class' => 't export_tax',
                'options' => array(
                    'query' => $yesno,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Profit margin'),
                'prefix' => '%',
                'name' => 'export_profit_margin',
                'desc' => $this->l('Type the value of profit margin in percentage. Module will increase prices in feed by defined percentage value'),
            ),
        );

        $pricetintex = array(
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
            )
        );


        $inputs = array_merge($inputs, $pricetintex);


        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Export Options'),
                    'icon' => 'icon-cogs'
                ),
                'input' => $inputs,
                'submit' => array(
                    'title' => $this->l('Save & Export'),
                    'icon' => 'process-icon-download-alt'
                ),
                'buttons' => array(
                    array('type' => 'submit', 'class' => 'pull-right', 'name' => 'saveonly', 'title' => $this->l('Save'), 'icon' => 'process-icon-save'),
                ),
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
        $helper->token = Tools::getAdminTokenLite('AdminExportProducts');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $this->heading() . $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'remember_settings' => (Configuration::get('ep_remember_settings') != false ? Configuration::get('ep_remember_settings') : 1),
            'export_format' => (Configuration::get('ep_export_format') != false ? Configuration::get('ep_export_format') : 'ps173'),
            'export_active' => (Configuration::get('ep_export_active') != false ? Configuration::get('ep_export_active') : false),
            'export_instock' => (Configuration::get('ep_export_instock') != false ? Configuration::get('ep_export_instock') : false),
            'export_identification' => (Configuration::get('ep_export_identification') != false ? Configuration::get('ep_export_identification') : 'id_product'),
            //'export_category' => (Configuration::get('ep_export_category') != false ? Configuration::get('ep_export_category') : 'all'),
            'export_manufacturers_default' => (Configuration::get('ep_export_manufacturers_default') != false ? Configuration::get('ep_export_manufacturers_default') : 'Default'),
            'export_type' => (Configuration::get('ep_export_type') != false ? Configuration::get('ep_export_type') : 'product'),
            'export_img' => (Configuration::get('ep_export_img') != false ? Configuration::get('ep_export_img') : 1),
            'export_manufacturers' => (Configuration::get('ep_export_manufacturers') != false ? Configuration::get('ep_export_manufacturers') : 9999999999),
            'export_description_what' => (Configuration::get('ep_description_what') != false ? Configuration::get('ep_description_what') : 'desc'),
            'export_short_description_what' => (Configuration::get('ep_short_description_what') != false ? Configuration::get('ep_short_description_what') : 'short'),
            'export_suppliers' => (Configuration::get('ep_export_suppliers') != false ? Configuration::get('ep_export_suppliers') : 9999999999),
            'export_delimiter' => (Configuration::get('ep_export_delimiter') != false ? Configuration::get('ep_export_delimiter') : '|'),
            'export_separator' => (Configuration::get('ep_export_separator') != false ? Configuration::get('ep_export_separator') : ','),
            'delete_images' => (Configuration::get('ep_delete_images') != false ? Configuration::get('ep_delete_images') : 0),
            'export_currency' => (Configuration::get('ep_export_currency') != false ? Configuration::get('ep_export_currency') : (int)Configuration::get('PS_CURRENCY_DEFAULT')),
            'export_instock_info' => (Configuration::get('ep_export_instock_info') != false ? Configuration::get('ep_export_instock_info') : 0),
            'export_what_pictures' => (Configuration::get('ep_export_what_pictures') != false ? Configuration::get('ep_export_what_pictures') : 0),
            'export_removehtml' => (Configuration::get('ep_export_removehtml') != false ? Configuration::get('ep_export_removehtml') : 1),
            'export_language' => (Configuration::get('ep_export_language') != false ? Configuration::get('ep_export_language') : (int)Configuration::get('PS_LANG_DEFAULT')),
            'limit_limit' => (Configuration::get('ep_limit_limit') != false ? Configuration::get('ep_limit_limit') : 0),
            'limit_page' => (Configuration::get('ep_limit_page') != false ? Configuration::get('ep_limit_page') : 0),
            'include_url' => (Configuration::get('ep_include_url') != false ? Configuration::get('ep_include_url') : 0),
            'export_tax' => (Configuration::get('ep_export_tax') != false ? Configuration::get('ep_export_tax') : 'price_tin'),
            'export_file_format' => (Configuration::get('ep_export_file_format') != false ? Configuration::get('ep_export_file_format') : 'csv'),
            'export_product_type' => (Configuration::get('ep_export_product_type') != false ? Configuration::get('ep_export_product_type') : 0),
            'export_inventory' => (Configuration::get('ep_export_inventory') != false ? Configuration::get('ep_export_inventory') : 0),
            'export_profit_margin' => (Configuration::get('ep_export_inventory') != false ? Configuration::get('ep_export_inventory') : 0),
            'export_increase_price' => (Configuration::get('ep_export_increase_price') != false ? Configuration::get('ep_export_increase_price') : 0),
            'export_category_format' => (Configuration::get('ep_export_category_format') != false ? Configuration::get('ep_export_category_format') : 0),
            'export_dateFrom' => (Configuration::get('ep_export_dateFrom') != false ? Configuration::get('ep_export_dateFrom') : ''),
            'export_dateTo' => (Configuration::get('ep_export_dateTo') != false ? Configuration::get('ep_export_dateTo') : ''),
            'export_activate_date' => (Configuration::get('ep_export_activate_date') != false ? Configuration::get('ep_export_activate_date') : 0),
            'export_categories' => (Configuration::get('ep_export_categories') != false ? explode("|", Configuration::get('ep_export_categories')) : array()),
        );

        /**
         * return array(
         * 'export_active' => false,
         * 'export_file_format' => 'csv',
         * 'export_type' => 'ps16',
         * 'export_category' => 'all',
         * 'export_format' => 'ps16',
         * 'export_type' => 'product',
         * 'export_manufacturers' => 'all',
         * 'export_suppliers' => 'all',
         * 'export_delimiter' => ';',
         * 'export_separator' => ',',
         * 'delete_images' => '0',
         * 'include_url' => '0',
         * 'limit_limit' => 0,
         * 'limit_page' => 0,
         * 'export_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
         * 'export_tax' => 'price_tin',
         * 'export_profit_margin' => '',
         * 'export_increase_price' => 0
         * );
         * **/
    }

    public function multi_rename_key(&$array, $old_keys, $new_keys)
    {
        if (!is_array($array)) {
            ($array == "") ? $array = array() : false;
            return $array;
        }
        foreach ($array as &$arr) {
            if (is_array($old_keys)) {
                foreach ($new_keys as $k => $new_key) {
                    (isset($old_keys[$k])) ? true : $old_keys[$k] = NULL;
                    $arr[$new_key] = (isset($arr[$old_keys[$k]]) ? $arr[$old_keys[$k]] : null);
                    unset($arr[$old_keys[$k]]);
                }
            } else {
                $arr[$new_keys] = (isset($arr[$old_keys]) ? $arr[$old_keys] : null);
                unset($arr[$old_keys]);
            }
        }
        return $array;
    }

    public function getProductsPro($id_lang, $start, $limit, $order_by, $order_way, $id_category = false, $id_manufacturer = false, $id_supplier = false, $only_active = false, Context $context = null, $dateFrom = false, $dateTo = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if ($id_manufacturer == 9999999999) {
            $id_manufacturer = false;
        }

        if ($id_supplier == 9999999999) {
            $id_supplier = false;
        }


        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $sql = 'SELECT p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)' . '
                LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)' . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` ps ON (ps.`id_product` = p.`id_product`)' .
            ($id_category ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` c ON (c.`id_product` = p.`id_product`)' : '') . '
                WHERE pl.`id_lang` = ' . (int)$id_lang .
            ($id_category ? ' AND c.`id_category` IN (' . implode(",", $id_category) . ')' : '') .
            ($id_supplier ? ' AND ps.`id_supplier` = ' . (int)$id_supplier : '') .
            ($id_manufacturer ? ' AND p.`id_manufacturer` = ' . (int)$id_manufacturer : '') .
            ($dateFrom ? Validate::isDate($dateFrom) ? ' AND p.date_add >= "' . $dateFrom . '"' : '' : '') .
            ($dateTo ? Validate::isDate($dateTo) ? ' AND p.date_add <= "' . $dateTo . '"' : '' : '') .
            ($only_active ? ' AND product_shop.`active` = 1' : '') . '  GROUP BY p.`id_product`
                ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) .
            ($limit > 0 ? ' LIMIT ' . (int)$start . ',' . (int)$limit : '');


        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);



        if ($order_by == 'price') {
            Tools::orderbyPrice($rq, $order_way);
        }

        foreach ($rq as &$row) {
            $row = Product::getTaxesInformations($row);
        }

        return $rq;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitExport')) {
            if (Tools::getValue('remember_settings') == 1) {
                foreach ($this->getConfigFieldsValues() AS $epkey => $epvalue) {
                    if ($epkey == 'export_categories') {
                        $epvalue = implode("|", Tools::getValue($epkey));
                        Configuration::updateValue('ep_' . $epkey, $epvalue);
                    } else {
                        Configuration::updateValue('ep_' . $epkey, Tools::getValue($epkey));
                    }
                }
            }

            if (Tools::isSubmit('saveonly')) {
                $this->context->controller->confirmations[] = $this->l("Settings saved");
                return;
            }

            $export_type = Tools::getValue('export_type');
            $delimiter = Tools::getValue('export_delimiter');
            $separator = Tools::getValue('export_separator');
            $id_lang = Tools::getValue('export_language');
            $id_shop = (int)$this->context->shop->id;

            set_time_limit(0);
            if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                $fileName = $export_type . '_' . date("Y_m_d_H_i_s") . '.csv';
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header('Content-Description: File Transfer');
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename={$fileName}");
                header("Expires: 0");
                header("Pragma: public");
                echo "\xEF\xBB\xBF";
            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                $fileName = $export_type . '_' . date("Y_m_d_H_i_s") . '.xml';
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header('Content-Description: File Transfer');
                header('Content-Type: application/xml; charset=utf-8');
                header("Content-Disposition: attachment; filename={$fileName}");
                header("Expires: 0");
                header("Pragma: public");
            }

            $f = fopen('php://output', 'w');


            $export_tax = Tools::getValue('export_tax');
            if ($export_tax == 'price_tin') {
                unset($this->available_fields[$export_type]['price_tex']);
            } elseif ($export_tax == 'price_tex') {
                unset($this->available_fields[$export_type]['price_tin']);
            }

            if (Tools::getValue('include_url') != 1) {
                unset($this->available_fields[$export_type]['include_url']);
            }

            foreach ($this->available_fields[$export_type] as $field => $array) {
                if ($field == 'include_url') {
                    if (Tools::getValue('include_url') == 1) {
                        $titles[] = $array['label'];
                    }
                } else {
                    $titles[] = $array['label'];
                }
            }

            if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                fputcsv($f, $titles, $delimiter, '"');
            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                $xml_array = array();
            }

            $export_active = (Tools::getValue('export_active') == 0 ? false : true);
            $export_category = (Tools::getValue('export_categories', 'false') == 'false' ? false : Tools::getValue('export_categories'));

            $limit_page = Tools::getValue('limit_page', 0);
            if ($limit_page > 0) {
                $limit_page--;
            }
            $limit_limit = tools::getValue('limit_limit', 0);
            $limit_page = $limit_page * $limit_limit;

            switch ($export_type) {
                case 'products':

                    $products = $this->getProductsPro($id_lang, $limit_page, $limit_limit, 'id_product', 'ASC', $export_category, Tools::getValue('export_manufacturers'), Tools::getValue('export_suppliers'), $export_active, null, Tools::getValue('export_dateFrom'), Tools::getValue('export_dateTo'));


                    foreach ($products as $product) {
                        $line = array();
                        $p = new Product($product['id_product'], true, $id_lang, $id_shop);
                        $p->loadStockData();

                        if (Tools::getValue('export_manufacturers') != 9999999999) {
                            if ($p->id_manufacturer != Tools::getValue('export_manufacturers')) {
                                $include = 0;
                                continue;
                            }
                        }

                        /**
                         * FILTER NOT REQUIRED - getProductsPro function gets products by supplier
                         * if (Tools::getValue('export_suppliers') != 9999999999)
                         * {
                         * if (Supplier::getProductInformationsBySupplier(Tools::getValue('export_suppliers'), $p->id) == null)
                         * {
                         * $include = 0;
                         * continue;
                         * }
                         * }
                         **/


                        foreach ($this->available_fields['products'] as $field => $array) {
                            if (isset($p->$field) && !is_array($p->$field)) {
                                if ($field == 'active' && $p->$field == 0) {
                                    $line[$field] = 0;
                                } elseif ($field == 'minimal_quantity') {
                                    $line[$field] = ($p->minimal_quantity > 0 ? $p->minimal_quantity : 1);
                                } elseif ($field == "is_virtual") {
                                    $line[$field] = $p->$field ? $p->$field : '';
                                    if ((int)$p->$field == 1 && in_array(Tools::getValue('export_format'), array('ps170-ps172', 'ps173'))) {
                                        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'product_download` WHERE `id_product` = "' . $p->id . '" AND active = 1';
                                        $query = Db::getInstance()->executeS($sql);
                                        if (isset($query[0])) {
                                            $q = $query[0];
                                            $productDownload = new ProductDownload($q['id_product_download']);
                                            $line['file_url'] = ($this->psversion() == 5 ? $this->getBaseLinkOld(Context::getContext()->shop->id, true) : Context::getContext()->link->getBaseLink(Context::getContext()->shop->id)) . 'download/' . $q['filename'];
                                            $line['nb_downloadable'] = $q['nb_downloadable'];
                                            $line['date_expiration'] = $q['date_expiration'];
                                            $line['nb_days_accessible'] = $q['nb_days_accessible'];
                                        }
                                    }
                                } else {
                                    $line[$field] = $p->$field ? $p->$field : '';
                                }
                            } else {
                                switch ($field) {
                                    case 'include_url':
                                        if (Tools::getValue('include_url') == 1) {
                                            $line['include_url'] = Context::getContext()->link->getProductLink($p->id, null, null, null, $id_lang);
                                        }
                                        break;
                                    case 'categories':
                                        // get categories
                                        $cats = $p->getProductCategoriesFull($p->id, $id_lang);

                                        // remove main product category
                                        foreach ($cats AS $keycat => $cat) {
                                            if ($cat['id_category'] == $p->id_category_default) {
                                                $default_category = $cat;
                                                $default_category_id = $cat['id_category'];
                                                unset($cats[$keycat]);
                                            }
                                        }

                                        if (!isset($default_category_id)) {
                                            $default_category_id = 2;
                                        }

                                        // merge two arrays (main product category must be first)
                                        $myArray = array('' . (string)$default_category_id => $default_category) + $cats;

                                        // implode categories to string
                                        $cat_array = array();
                                        foreach ($myArray as $cat) {
                                            if (Tools::getValue('export_category_format') == 0) {
                                                $cat_array[] = $cat['name'];
                                            } else {
                                                $cat_array[] = $cat['id_category'];
                                            }
                                        }

                                        $line['categories'] = implode($separator, $cat_array);
                                        break;
                                    case 'price_tex':
                                        $line['price_tex'] = $this->addProfitMargin($p->getPrice(false, null, 2, null, false, false, 1));
                                        break;
                                    case 'price_tin':
                                        $line['price_tin'] = $this->addProfitMargin($p->getPrice(true, null, 2, null, false, false, 1));
                                        break;
                                    case 'upc':
                                        $line['upc'] = $p->upc ? $p->upc : ' ';
                                        break;
                                    case 'features':
                                        $line['features'] = '';
                                        $features = self::getFrontFeaturesStatic($id_lang, $p->id);
                                        $position = 1;
                                        $features_array = array();
                                        foreach ($features as $feature) {
                                            array_push($features_array, $feature['name'] . ':' . $feature['value'] . ':' . $feature['position'] . ':' . $feature['custom']);
                                            $position++;
                                        }
                                        $line['features'] = implode($separator, $features_array);

                                        break;
                                    case 'reduction_price':
                                        $specificPrice = SpecificPrice::getSpecificPrice($p->id, $id_shop, 0, 0, 0, 0);

                                        $line['reduction_price'] = '';
                                        $line['reduction_percent'] = '';
                                        $line['reduction_from'] = '';
                                        $line['reduction_to'] = '';

                                        if (isset($specificPrice['reduction_type'])) {
                                            if ($specificPrice['reduction_type'] == 'amount') {
                                                $line['reduction_price'] = $specificPrice['reduction'];
                                            } elseif ($specificPrice['reduction_type'] == 'percentage') {
                                                $line['reduction_percent'] = $specificPrice['reduction'] * 100;
                                            }
                                        }

                                        if ($line['reduction_price'] != '' || $line['reduction_percent'] != '') {
                                            if ($specificPrice['from'] != '0000-00-00 00:00:00') {
                                                $line['reduction_from'] = date_format(date_create($specificPrice['from']), 'Y-m-d');
                                            }

                                            if ($specificPrice['to'] != '0000-00-00 00:00:00') {
                                                $line['reduction_to'] = date_format(date_create($specificPrice['to']), 'Y-m-d');
                                            }
                                        }

                                        break;
                                    case 'tags':
                                        $tags = $p->getTags($id_lang);
                                        $line['tags'] = $tags;
                                        break;
                                    case 'image':
                                        $link = new Link();
                                        $imagelinks = array();
                                        $images = $p->getImages($id_lang);
                                        foreach ($images as $image) {
                                            $imagelinks[] = Tools::getShopProtocol() . $link->getImageLink($p->link_rewrite, $p->id . '-' . $image['id_image']);
                                        }
                                        $line['image'] = implode($separator, $imagelinks);
                                        break;
                                    case 'image_alt_texts':
                                        $imageLegend = array();
                                        $images = $p->getImages($id_lang);
                                        foreach ($images as $image) {
                                            $imageLegend[] = $image['legend'];
                                        }
                                        $line['image_alt_texts'] = implode($separator, $imageLegend);
                                        break;
                                    case 'delete_existing_images':
                                        $line['delete_existing_images'] = Tools::getValue('delete_images', 0);
                                        break;
                                    case 'shop':
                                        $line['shop'] = $id_shop;
                                        break;
                                    case 'warehouse':
                                        $warehouses = Warehouse::getWarehousesByProductId($p->id);
                                        $line['warehouse'] = '';
                                        if (!empty($warehouses)) {
                                            $line['warehouse'] = implode($separator, array_map(array(
                                                $this,
                                                'getWarehouses'
                                            ), $warehouses));
                                        }

                                        break;
                                    case 'date_added':
                                        $date = new DateTime($p->date_add);
                                        $line['date_add'] = $date->format("Y-m-d");
                                        break;
                                    case 'accessories':
                                        $line['accessories'] = implode($separator, array_map(function ($entry) {
                                            return $entry['id_product'];
                                        }, Product::getAccessoriesLight($id_lang, $p->id)));
                                }


                                if (!array_key_exists($field, $line)) {
                                    $line[$field] = '';
                                }
                            }
                        }

                        $include = 1;

                        if ($include == 1) {
                            if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                                fputcsv($f, $line, $delimiter, '"');
                            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                                $xml_array[] = $line;
                            }
                        }
                    }
                    break;
                case 'combinations':
                    if (!Combination::isFeatureActive()) {
                        return false;
                    }


                    $products = $this->getProductsPro($id_lang, $limit_page, $limit_limit, 'id_product', 'ASC', $export_category, Tools::getValue('export_manufacturers'), Tools::getValue('export_suppliers'), $export_active, null, Tools::getValue('export_dateFrom'), Tools::getValue('export_dateTo'));


                    foreach ($products as $product) {
                        $line = array();
                        $p = new Product($product['id_product'], true, $id_lang, $id_shop);
                        $p->loadStockData();


                        if (Tools::getValue('export_manufacturers') != 9999999999) {
                            if ($p->id_manufacturer != Tools::getValue('export_manufacturers')) {
                                $include = 0;
                                continue;
                            }
                        }

                        /**
                         * FILTER NOT REQUIRED - getProductsPro function gets products by supplier
                         * if (Tools::getValue('export_suppliers') != 9999999999)
                         * {
                         * if (Supplier::getProductInformationsBySupplier(Tools::getValue('export_suppliers'), $p->id) == null)
                         * {
                         * $include = 0;
                         * continue;
                         * }
                         * }
                         **/

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

                            WHERE pa.`id_product` = ' . (int)$p->id . ' AND al.`id_lang` = ' . (int)$id_lang . ' AND agl.`id_lang` = ' . (int)$id_lang . '
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
                                foreach ($this->available_fields['combinations'] as $field => $array) {
                                    if (in_array($field, array('id'))) {
                                        if (isset($p->$field) && !is_array($p->$field)) {
                                            $line[$field] = $p->$field;
                                        }
                                    } else {
                                        switch ($field) {

                                            case 'include_url':
                                                if (Tools::getValue('include_url') == 1) {
                                                    $line['include_url'] = Context::getContext()->link->getProductLink($p->id, null, null, null, $id_lang, null, $value['id_product_attribute']);
                                                }
                                                break;
                                            case 'product_reference':
                                                $line[$field] = $p->reference;
                                                break;
                                            case 'group':
                                                $line[$field] = implode($separator, $attributes_details[$value['id_product_attribute']]['new_group']);
                                                break;
                                            case 'attribute':
                                                $line[$field] = implode($separator, $attributes_details[$value['id_product_attribute']]['new_attribute']);
                                                break;
                                            case 'supplier_reference':
                                                $line[$field] = $value['supplier_reference'];
                                                break;
                                            case 'reference':
                                                $line[$field] = $value['reference'];
                                                break;
                                            case 'ean13':
                                                $line[$field] = $value['ean13'];
                                                break;
                                            case 'upc':
                                                $line[$field] = $value['upc'];
                                                break;
                                            case 'wholesale_price':
                                                $line[$field] = $value['wholesale_price'];
                                                break;
                                            case 'unit_price_impact':
                                                $line[$field] = $value['unit_price_impact'];
                                                break;
                                            case 'ecotax':
                                                $line[$field] = $value['ecotax'];
                                                break;
                                            case 'minimal_quantity':
                                                $line[$field] = ($value['minimal_quantity'] > 0 ? $value['minimal_quantity'] : 1);
                                                break;
                                            case 'quantity':
                                                $line[$field] = $value['quantity'];
                                                break;
                                            case 'impact_price':
                                                $line[$field] = $value['price'];
                                                break;
                                            case 'weight':
                                                $line[$field] = $value['weight'];
                                                break;
                                            case 'default_on':
                                                $line[$field] = $value['default_on'] == 1 ? 1 : 0;
                                                break;
                                            case 'available_date':
                                                $line[$field] = $value['available_date'];
                                                break;
                                            case 'delete_existing_images':
                                                $line['delete_existing_images'] = Tools::getValue('delete_images', 0);
                                                break;
                                            case 'image_alt_texts':
                                                $imageLegend = array();
                                                $sql = 'SELECT il.legend FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai  INNER JOIN  `' . _DB_PREFIX_ . 'image_lang` il ON pai.`id_image` = il.`id_image` WHERE il.`id_lang` = ' . $id_lang . ' AND pai.`id_product_attribute`=' . $value['id_product_attribute'];
                                                $images = Db::getInstance()->executeS($sql);

                                                foreach ($images as $image) {
                                                    $imageLegend[] = $image['legend'];
                                                }

                                                $line['image_alt_texts'] = implode($separator, $imageLegend);
                                                break;

                                            case 'image_position':
                                                $link = new Link();

                                                $sql = 'SELECT id_image FROM `' . _DB_PREFIX_ . 'product_attribute_image` WHERE `id_product_attribute`=' . $value['id_product_attribute'];
                                                $images = Db::getInstance()->executeS($sql);

                                                $image_urls = array();
                                                $image_positions = array();
                                                foreach ($images as $image) {
                                                    if (isset($image['id_image'])) {
                                                        if ((int)$image['id_image'] > 0) {
                                                            $sql = 'SELECT position FROM `' . _DB_PREFIX_ . 'image` WHERE `id_image`=' . $image['id_image'];
                                                            $q = Db::getInstance()->executeS($sql);
                                                            if (isset($q[0]['position'])) {
                                                                $image_positions[] = $q[0]['position'];
                                                            }
                                                        } else {
                                                            $line['image_position'] = '';
                                                        }
                                                    } else {
                                                        $line['image_position'] = '';
                                                    }

                                                    if (isset($image['id_image'])) {
                                                        if ((int)$image['id_image'] > 0) {
                                                            $image_urls[] = Tools::getShopProtocol() . $link->getImageLink($p->link_rewrite, $p->id . '-' . $image['id_image']);
                                                        } else {
                                                            $line['image_url'] = '';
                                                        }
                                                    } else {
                                                        $line['image_url'] = '';
                                                    }
                                                }

                                                if (isset($image_positions)) {
                                                    $line['image_position'] = implode($separator, $image_positions);
                                                }
                                                if (isset($image_urls)) {
                                                    $line['image_url'] = implode($separator, $image_urls);
                                                }


                                                break;

                                            case 'shop':
                                                $line[$field] = $value['id_shop'];
                                                break;
                                            case 'warehouse':
                                                $warehouses = Warehouse::getWarehousesByProductId($p->id);
                                                $line['warehouse'] = '';
                                                if (!empty($warehouses)) {
                                                    $line['warehouse'] = implode($separator, array_map("$this->getWarehouses", $warehouses));
                                                }
                                                break;
                                        }

                                        if (!array_key_exists($field, $line)) {
                                            $line[$field] = '';
                                        }

                                    }
                                }

                                $include = 1;
                                if ($include == 1) {
                                    if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                                        fputcsv($f, $line, $delimiter, '"');
                                    } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                                        $xml_array[] = $line;
                                    }
                                }
                            }

                        }
                    }
                    break;
                case 'categories':
                    $link = new Link();
                    $categories = $this->getCategories($id_lang, $export_active, $id_shop);

                    foreach ($categories as $cat) {
                        $line = array();
                        $c = new Category($cat['id_category'], $id_lang, $id_shop);

                        foreach ($this->available_fields['categories'] as $field => $array) {
                            if (isset($c->$field) && !is_array($c->$field)) {
                                $line[$field] = $c->$field;
                            } else {
                                switch ($field) {
                                    case 'image':
                                        $line['image'] = Tools::getShopProtocol() . $link->getCatImageLink($c->name, $c->id_category);
                                        break;
                                }
                            }

                            if (!array_key_exists($field, $line)) {
                                $line[$field] = '';
                            }
                        }

                        if (Tools::getValue('export_file_format', 'csv') == 'csv') {
                            fputcsv($f, $line, $delimiter, '"');
                        } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                            $xml_array[] = $line;
                        }
                    }
                    break;
            }
            if (Tools::getValue('export_file_format', 'csv') == 'csv') {

            } elseif (Tools::getValue('export_file_format', 'csv') == 'xml') {
                $export_type = Tools::getValue('export_type', 'object');
                $xml = new SimpleXMLElement('<?xml version="1.0"?><' . $export_type . '></' . $export_type . '>');
                $this->array_to_xml($xml_array, $xml);
                echo $xml->asXML();
            }

            fclose($f);
            die();
        }
    }

    public static function getFrontFeaturesStatic($id_lang, $id_product)
    {
        $_frontFeaturesCache[$id_product . '-' . $id_lang] = array();
        if (!Feature::isFeatureActive()) {
            return array();
        }
        $_frontFeaturesCache[$id_product . '-' . $id_lang] = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT name, value, pf.id_feature, custom, position
            FROM ' . _DB_PREFIX_ . 'feature_product pf
            LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = ' . (int)$id_lang . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int)$id_lang . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'feature_value fv ON (fv.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int)$id_lang . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = ' . (int)$id_lang . ')
            ' . Shop::addSqlAssociation('feature', 'f') . '
            WHERE pf.id_product = ' . (int)$id_product . '
            ORDER BY f.position ASC'
        );

        return $_frontFeaturesCache[$id_product . '-' . $id_lang];
    }

    /**
     * @param $array
     * @param $xml
     */
    public function array_to_xml($array, &$xml)
    {
        $export_type = Tools::getValue('export_type', 'object');
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("$export_type$key");
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    public function initContent()
    {
        $this->content = $this->renderView() . $this->renderScript();
        parent::initContent();
    }

    public function getWarehouses($id_warehouses)
    {
        return $id_warehouses['id_warehouse'];
    }

    public function renderScript()
    {

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

    public function getAllProductsNb()
    {
        $nb = Db::getInstance()->getRow('SELECT COUNT(*) AS nb FROM `' . _DB_PREFIX_ . 'product`');
        return (isset($nb['nb']) ? (int)$nb['nb'] : 0);
    }
}
