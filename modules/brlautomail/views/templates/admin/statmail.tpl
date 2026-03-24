{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
* @copyright  2007-2023 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel kpi-container">
	<div class="row">
		<div class="col-sm-6 col-lg-3">
			<div id="box-conversion-rate" data-toggle="tooltip" class="box-stats label-tooltip color4">
				<div class="kpi-content">
						<i class="icon-envelope"></i>
							<span class="title">{l s='Mails sent' mod='brlautomail'} {$infosShop|escape:'quotes':'UTF-8'}</span>
					<span class="subtitle">{l s='Last 30 days' mod='brlautomail'}</span>
					<span class="value">{$mailEnvoye|escape:'quotes':'UTF-8'}</span>
				</div>
			</div>
		</div>
		<div class="col-sm-6 col-lg-3">
			<div id="box-conversion-rate" data-toggle="tooltip" class="box-stats label-tooltip color4">
				<div class="kpi-content">
						<i class="icon-envelope"></i>
							<span class="title">{l s='Mails waiting' mod='brlautomail'} {$infosShop|escape:'quotes':'UTF-8'}</span>
					<span class="subtitle">{l s='Deferred mode' mod='brlautomail'}</span>
					<span class="value">{$mailEnAttente|escape:'quotes':'UTF-8'}</span>
				</div>
			</div>
		</div>
	</div>
</div>
{if !empty($lstMailsEnAttente)}
<div class="panel kpi-container" style="max-height: 200px;overflow: scroll;">
	<div class="row">
		<div class="col-sm-12">
			<div id="box-conversion-rate" data-toggle="tooltip" class="box-stats label-tooltip color4">
				<div class="kpi-content">
					<i class="icon-envelope"></i>
					<span class="title">{l s='Mails waiting' mod='brlautomail'} {$infosShop|escape:'quotes':'UTF-8'}</span>
					<span class="subtitle">{l s='Deferred mode' mod='brlautomail'}</span>
				</div>
				<ul>
					{foreach $lstMailsEnAttente as $email}
						<li class="title">{$email['email']}</li>
					{/foreach}
				</ul>
			</div>
		</div>
	</div>
</div>
{/if}
