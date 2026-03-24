<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */
require_once _PS_MODULE_DIR_ .
    "ndk_advanced_custom_fields/models/HTMLTemplateRecipient.php";
class NdkCfRecipients extends ObjectModel
{
    public $id_ndk_customization_field_recipient;
    public $id_ndk_customization_field;
    public $id_cart;
    public $id_product;
    public $id_combination;
    public $id_customization;
    public $id_order;
    public $firstname;
    public $lastname;
    public $email;
    public $value;
    public $message;
    public $who_offers;
    public $details;
    public $code;
    public $availability;
    public $date;
    public $title;
    public $send_mail;

    public static $definition = [
        "table" => "ndk_customization_field_recipient",
        "primary" => "id_ndk_customization_field_recipient",
        "multilang" => false,
        "fields" => [
            "id_ndk_customization_field_recipient" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
            "id_ndk_customization_field" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
            "id_cart" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
            "id_product" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
            "id_combination" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
            "id_customization" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
            "id_order" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
            "firstname" => [
                "type" => ObjectModel::TYPE_STRING,
                "required" => false,
            ],
            "lastname" => [
                "type" => ObjectModel::TYPE_STRING,
                "required" => false,
            ],
            "email" => [
                "type" => ObjectModel::TYPE_HTML,
                "required" => false,
            ],
            "message" => [
                "type" => ObjectModel::TYPE_STRING,
                "required" => false,
            ],
            "who_offers" => [
                "type" => ObjectModel::TYPE_STRING,
                "required" => false,
            ],
            "details" => [
                "type" => ObjectModel::TYPE_HTML,
                "required" => false,
            ],
            "code" => [
                "type" => ObjectModel::TYPE_STRING,
                "required" => false,
            ],
            "availability" => [
                "type" => ObjectModel::TYPE_STRING,
                "required" => false,
            ],
            "title" => [
                "type" => ObjectModel::TYPE_STRING,
                "required" => false,
            ],

            "date" => [
                "type" => ObjectModel::TYPE_DATE,
                "required" => false,
            ],
            "send_mail" => [
                "type" => ObjectModel::TYPE_INT,
                "lang" => false,
            ],
        ],
    ];

    public function __construct(
        $id = null,
        $id_lang = null,
        $lite_result = false,
        $hide_lookbook_position = false
    ) {
        parent::__construct($id, $id_lang);
    }

    public static function getRecipientForOrder(
        $id_cart,
        $id_product,
        $id_product_attribute = 0,
        $id_customization
    ) {
        if ($id_product_attribute < 1) {
            $id_product_attribute = 0;
        }

        $sql =
            "SELECT id_ndk_customization_field_recipient FROM  `" .
            _DB_PREFIX_ .
            'ndk_customization_field_recipient`
	WHERE id_product = ' .
            (int) $id_product .
            " AND id_cart = " .
            (int) $id_cart .
            //. ' AND id_combination = ' .(int)$id_product_attribute
            " AND id_customization = " .
            (int) $id_customization;
        $fields = Db::getInstance()->getRow($sql);
        return $fields["id_ndk_customization_field_recipient"];
    }

    public static function generatePDF($object, $template, $render = false)
    {
        $pdf = new PDF($object, $template, Context::getContext()->smarty);
        return $pdf->render($render);
    }

    public static function sendGiftMail($order, $recipient)
    {
        $link = new Link();
        $customer = new Customer((int) $order->id_customer);
        $id_lang = $customer->id_lang;
        $id_shop = (int) $customer->id_shop;

        $template_vars = [
            "{customerFirstname}" => $customer->firstname,
            "{customerLastname}" => $customer->lastname,
            "{recipientFirstname}" => $recipient->firstname,
            "{recipientLastname}" => $recipient->lastname,
        ];

        // Join PDF gift
        $file_attachement = [];
        //$pdf = new PDF($recipient, 'Recipient', Context::getContext()->smarty);
        //$pdf->pdf_renderer->setPageOrientation('L');
        $file_attachement["content"] = self::generatePDF(
            $recipient,
            "Recipient"
        );
        $file_attachement["name"] = "WEB" . $order->reference . ".pdf";
        $file_attachement["mime"] = "application/pdf";

        if (
            !is_dir(
                _PS_IMG_DIR_ .
                    "scenes/" .
                    "ndkcf/pdf/" .
                    (int) $order->id_customer
            )
        ) {
            mkdir(
                _PS_IMG_DIR_ .
                    "scenes/" .
                    "ndkcf/pdf/" .
                    (int) $order->id_customer,
                0777
            );
        }

        $recipientFile =
            _PS_IMG_DIR_ .
            "scenes/" .
            "ndkcf/pdf/" .
            (int) $order->id_customer .
            "/" .
            $recipient->id_product .
            "_" .
            $recipient->id_customization .
            "_" .
            $file_attachement["name"];
        file_put_contents($recipientFile, $file_attachement["content"]);

        $to = [];
        array_push(
            $to,
            (string) Configuration::get("PS_SHOP_EMAIL", null, null, $id_shop)
        );
        if ($recipient->send_mail == 1) {
            array_push($to, (string) $customer->email);
            array_push($to, (string) $recipient->email);
        } else {
            array_push($to, (string) $customer->email);
        }

        //$to = $customer->email;

        $iso = Language::getIsoById($id_lang);

        if (
            file_exists(
                _PS_MODULE_DIR_ .
                    "ndk_advanced_custom_fields/mails/" .
                    $iso .
                    "/send_gift.txt"
            ) &&
            file_exists(
                _PS_MODULE_DIR_ .
                    "ndk_advanced_custom_fields/mails/" .
                    $iso .
                    "/send_gift.html"
            )
        ) {
            Mail::Send(
                $id_lang,
                "send_gift",
                Mail::l("A gift for you", $id_lang),
                $template_vars,
                $to,
                null,
                (string) Configuration::get(
                    "PS_SHOP_EMAIL",
                    null,
                    null,
                    $id_shop
                ),
                (string) Configuration::get(
                    "PS_SHOP_NAME",
                    null,
                    null,
                    $id_shop
                ),
                $file_attachement,
                null,
                _PS_MODULE_DIR_ . "ndk_advanced_custom_fields/mails/",
                false,
                $id_shop
            );
        }
    }
}
