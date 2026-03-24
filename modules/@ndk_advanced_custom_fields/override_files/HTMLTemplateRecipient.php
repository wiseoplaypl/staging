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
	public function getHeader()
	{
		$this->assignCommonHeaderData();
		$this->smarty->assign(array(
			'header' => $this->l('Code cadeau'),
		));
		return $this->smarty->fetch($this->getTemplate('header'));
	}

	
		public function getContent()
	{
        $order = new Order((int)$this->recipient->id_order);
        $customer = new Customer((int)$order->id_customer);
        $product = new Product((int)$this->recipient->id_product, (int)$order->id_lang);
        $customization = new Customization((int)$this->recipient->id_customization);
        $availability = date('d-m-Y', strtotime($order->invoice_date. ' + '.$this->recipient->availability.' days'));
        $this->smarty->assign(array(
        	'customer' => $customer,
        	'recipient' => $this->recipient,
        	'product' => $this->address_supplier,
        	'order' => $order,
        	'availability' => $availability,
        	'customizations' => $customization->getWsCustomizedDataTextFields(),
       ));
        
        
		return $this->smarty->fetch($this->getTemplate('recipient'));
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
}
