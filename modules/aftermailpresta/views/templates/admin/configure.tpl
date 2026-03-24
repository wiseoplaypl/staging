{*
* @author    Shoprunners <info@shoprunners.com>
* @copyright Shoprunners <www.shoprunners.de>
* @license   go to addons.prestashop.com (buy one module for one shop).
* @for PrestaShop version 1.6X anmd 1.7x
* @site www.shoprunners.de
* @email info@shoprunners.com
*}
<div class="bootstrap panel">
	<div class="panel-heading">
		<i class="icon-mail"></i> {l s='Automatic E-Mails' mod='aftermailpresta'}
	</div>

	<p>{l s='The most flexible way to send automatic e-mails to your customers' mod='aftermailpresta'}</p>
    <p>{l s='Use this URL for your crontab' mod='aftermailpresta'}</p>
    <p><strong>{$CRONURL|escape:'htmlall':'UTF-8'}</strong></p>
	<br>
	<p>{l s='Set your frequency of the cron job as often as you want your outgoing mail queue checked.' mod='aftermailpresta'}</p>
	<p>{l s='Configuration of mails can be found at Customers->Aftermail Tab'  mod='aftermailpresta'}. '</p>

	<hr style="width:100%;" />
	<p>{l s='Extra Documentation can be found here:' mod='aftermailpresta'}<br>
	<a href="http://addons.prestashop.com/en/advertising-marketing-newsletter-modules/8299-aftermail.html" target="_blank">Addon store</a>
	</p>
	<p>Readme as PDF(<a target="_blank" href="../modules/aftermailpresta/doc/readme_en.pdf">English</a>)(<a target="_blank" href="../modules/aftermailpresta/doc/readme_de.pdf">German</a>)</p>

	<p><span style="font-weight:bold;">Aftermail {$version|escape:'htmlall':'UTF-8'}</span></p>
	<p>{l s='Created by' mod='aftermailpresta'} Shoprunners</p>
</div>

