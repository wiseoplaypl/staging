<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */
include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/../../init.php';
require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCf.php';
require_once _PS_MODULE_DIR_.
    'ndk_advanced_custom_fields/models/ndkCfValues.php';
$context = Context::getContext();
$languages = Language::getLanguages();
$id_lang = Context::getContext()->language->id;
$product = new Product((int) Tools::getValue('id_product'), (int) $id_lang);

$allowed_extensions = [
    'jpg',
    'png',
    'jpeg',
    'png',
    'gif',
    'psd',
    'pdf',
    'ai',
    'csv',
    'xls',
    'txt',
    'zip',
    'rar',
    'eps',
    'tif',
];
if (Configuration::get('NDK_ACF_UPLOAD_EXT')) {
    $allowed_extensions = explode(
        ';',
        Configuration::get('NDK_ACF_UPLOAD_EXT')
    );
}
$ndkFields = NdkCf::getCustomFieldsForCreation(
    Tools::getValue('id_product'),
    $product->id_category_default
);
$uploaded_file =
    _PS_UPLOAD_DIR_.'up_ndkacf_'.$context->cart->id.'_'.time();
if (Tools::getValue('data')) {
    $image = Tools::getValue('data');
    $decoded = mb_convert_encoding(
        str_replace('data:image/png;base64,', '', $image),
        'UTF-8',
        'BASE64'
    );
    $name = time();
    file_put_contents($uploaded_file, $decoded);
} else {
    move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file);
}

// Try 4 different methods to determine the mime type

if (function_exists('finfo_open')) {
    $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
    $finfo = finfo_open($const);
    $mime_type = finfo_file($finfo, $uploaded_file);
    finfo_close($finfo);
} elseif (function_exists('mime_content_type')) {
    $mime_type = mime_content_type($uploaded_file);
} elseif (function_exists('exec')) {
    $mime_type = trim(
        exec('file -b --mime-type '.escapeshellarg($uploaded_file))
    );
    if (!$mime_type) {
        $mime_type = trim(
            exec('file --mime '.escapeshellarg($uploaded_file))
        );
    }
    if (!$mime_type) {
        $mime_type = trim(exec('file -bi '.escapeshellarg($uploaded_file)));
    }
}

if (
    empty($mime_type) ||
    'regular file' == $mime_type ||
    'text/plain' == $mime_type
) {
    $mime_type = $file_mime_type;
}

//var_dump($uploaded_file);

$extension = explode('/', $mime_type);

$mime_type_list_image = [
    'image/gif',
    'image/jpg',
    'image/jpeg',
    'image/pjpeg',
    'image/png',
    'image/x-png',
];

if (in_array($extension[1], $allowed_extensions)) {
    if (in_array($mime_type, $mime_type_list_image)) {
        //enregistrement image
        $errors = [];
        $file_name = md5(uniqid(rand(), true)).'.'.$extension[1];

        $product_picture_width = (int) Configuration::get(
            'PS_PRODUCT_PICTURE_WIDTH'
        );
        $product_picture_height = (int) Configuration::get(
            'PS_PRODUCT_PICTURE_HEIGHT'
        );
        $tmp_name = $uploaded_file;
        /* Original file */
        if (!Tools::copy($tmp_name, _PS_UPLOAD_DIR_.$file_name)) {
            //if (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name))
            $errors[] = Tools::displayError(
                'An error occurred during the image upload process 1.'
            );
        } /* A smaller one */
        /*elseif (!ImageManager::resize($tmp_name, _PS_UPLOAD_DIR_.$file_name.'_small', $product_picture_width, $product_picture_height))
         $errors[] = Tools::displayError('An error occurred during the image upload process 2.');*/ elseif (
            !chmod(_PS_UPLOAD_DIR_.$file_name, 0644)
        ) {
            $errors[] = Tools::displayError(
                'An error occurred during the image upload process 3.'
            );
        } else {
            //$context->cart->addPictureToProduct(Tools::getValue('id_product'), $image_field, 0,$file_name);
            if (
                ('bebaboubap' == Tools::getValue('special')) &&
                extension_loaded('imagick')
            ) {
                //on applique on filter
                NdkCf::sketchImage(_PS_UPLOAD_DIR_.$file_name);
            } elseif ('engraving' == Tools::getValue('special') &&
            extension_loaded('imagick')) {
                NdkCf::engravingImage(_PS_UPLOAD_DIR_.$file_name, false);
            }

            echo 'upload/'.$file_name;
        }
    } else {
        $file_name = md5(uniqid(rand(), true));
        Tools::copy(
            $uploaded_file,
            _PS_UPLOAD_DIR_.$file_name.'.'.$extension[1]
        );

        echo 'upload/'.$file_name.'.'.$extension[1];
    }
} else {
    @unlink($uploaded_file);
    echo 'Forbidden!';
}
