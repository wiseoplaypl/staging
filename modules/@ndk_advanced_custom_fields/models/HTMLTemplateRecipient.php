<?php
/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

class HTMLTemplateRecipient extends HTMLTemplate
{
    public $recipient;
    public $smarty;

    public function __construct(NdkCfRecipients $recipient, $smarty)
    {
        $this->recipient = $recipient;
        $this->smarty = $smarty;
        $this->context = Context::getContext();

        // header informations
        $this->date = Tools::displayDate($recipient->date);
        $this->title = HTMLTemplateRecipient::l('Your code');

        $this->shop = new Shop((int)$this->context->shop->id);
    }


    /**
     * Returns the template's HTML header
     *
     * @return string HTML header
     */
    /*public function getHeader()
    {
        $this->assignCommonHeaderData();
        $this->smarty->assign(array(
            'header' => $this->l('Code cadeau'),
        ));
        return $this->smarty->fetch($this->getTemplate('header_recipient'));
    }*/

    public function getHeader()
    {
        return '';
    }

    public function getContent()
    {
        $order = new Order((int)$this->recipient->id_order);
        $customer = new Customer((int)$order->id_customer);
        $product = new Product((int)$this->recipient->id_product, (int)$order->id_lang);
        $customization = new Customization((int)$this->recipient->id_customization);
        $base_dir = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        $availability = date('d-m-Y', strtotime($order->invoice_date. ' + '.$this->recipient->availability.' days'));

        if (file_exists(_PS_IMG_DIR_.'scenes/'.'ndkcf/mask/'.(int)$this->recipient->id_ndk_customization_field.'.jpg')) {
            $image_link = $base_dir.'img/scenes/ndkcf/mask/'.(int)$this->recipient->id_ndk_customization_field.'.jpg';
        } else {
            $image_link = '';
        }

        $this->smarty->assign(array(
            'customer' => $customer,
            'recipient' => $this->recipient,
            'product' => $product,
            'order' => $order,
            'image_link' => $image_link,
            'availability' => $availability,
            'customizations' => $customization->getWsCustomizedDataTextFields(),
       ));
        $template = _PS_MODULE_DIR_.'ndk_advanced_custom_fields/views/templates/front/recipient.tpl';
        if (file_exists(_PS_THEME_DIR_.'modules/ndk_advanced_custom_fields/views/templates/front/recipient.tpl')) {
            $template = _PS_THEME_DIR_.'modules/ndk_advanced_custom_fields/views/templates/front/recipient.tpl';
        }

        //$fetched = $this->smarty->fetch($template);
        //die($fetched);

        return $this->smarty->fetch($template);
    }


    /**
     * Returns the template filename when using bulk rendering
     *
     * @return string filename
     */
    public function getBulkFilename()
    {
        return 'recipient.pdf';
    }

    /**
     * Returns the template filename
     *
     * @return string filename
     */
    public function getFilename()
    {
        return 'recipient.pdf';
    }

    public function getPagination()
    {
        return $this->smarty->fetch($this->getTemplate('pagination'));
    }
}
