{*
* 2007-2017 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="tab-pane panel " id="lazy_load"  >
    <div class="panel-heading"><i class="icon-magic"></i> {l s='Lazy load' mod='ultimateimagetool'}</div>
		<div class="alert alert-info">
			{l s='To offer a better experience to your customers you can activate the lazy load option.' mod='ultimateimagetool'}
			<br/> 
			{l s='Activating the lazy load option, will increase the page loading time, and give you a better SEO ranking ' mod='ultimateimagetool'}
			<br/> 
			{l s='Most lazy load scripts make the images, unindexable, this is not the case there, as this script stops the network from loading the image, without editing the `src` of the image in the code. ' mod='ultimateimagetool'}
			<br/> 
			<strong>{l s='Make sure you test this feature after you activate it, so it doesn`t cause problems. ' mod='ultimateimagetool'}</strong>

		</div>
		<div class="alert alert-warning">
			{l s='Lazy load can be incompatible with custom zoom/slider modules, if this is the case manual changes must be done to the module for lazy load to be compatible with your theme/modules.' mod='ultimateimagetool'}
			<br/> 
			{l s='If this is the case, we do manual changes to the module to make it compatible with your theme/modules if you have zen-option purchased ' mod='ultimateimagetool'}

		</div>
    <div class="clear"></div>

		 <table class="table">
		    <tbody id="samdha_warper">

		    	<tr>
		    		<td>{l s='Lazy Load' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="lazy_load_active" name="lazy_load_active"  >
			    			<option value="disabled"{if $uit_lazy_load == 'disabled'} selected{/if}>{l s='Disabled' mod='ultimateimagetool'}</option>
			    			<option value="enabled" {if $uit_lazy_load == 'enabled'} selected{/if}>{l s='Enabled' mod='ultimateimagetool'}</option>
			    		</select>

		    		</td>
		    	</tr>
	    	<tr >
	    		<td colspan="4" class="lazy_html "></td>
	    	</tr>
		    </tbody>
	    </table> 
</div>
<div class="clear"></div>