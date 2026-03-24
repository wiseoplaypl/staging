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
<div class="tab-pane panel " id="cron"  >
    <div class="panel-heading"><i class="icon-calendar"></i> {l s='Cron Jobs' mod='ultimateimagetool'}</div>
		<div class="alert alert-info"><span class="alert_close"></span>
			<h2>{l s='Cron job urls' mod='ultimateimagetool'}</h2>
			<br/> 
			{l s='Cron Products Url:' mod='ultimateimagetool'} <a target="_blank" href="{$uit_module_path|escape:'htmlall':'UTF-8'}/cron/job.php?token={$uit_token|escape:'htmlall':'UTF-8'}">{$uit_module_path|escape:'htmlall':'UTF-8'}/cron/job.php?token={$uit_token|escape:'htmlall':'UTF-8'}</a>
			<br/>
			<br/>
			<br/>
			<p style="color:black !important;">{l s='By executing the cron job you will automatically compress:' mod='ultimateimagetool'} </p>	
			<p style="color:black !important;">1. {l s='your newly added product, category, supplier, manufacturer images.' mod='ultimateimagetool'} </p>	
			<p style="color:black !important;">2. {l s='pending images that have not been compressed.' mod='ultimateimagetool'} </p>	
			<p style="color:black !important; font-weight: bold">{l s='For large catalogs we recommend setting the cron job to be executed every 1-2 minutes. If no items are on the que to be processed, the memory consumed is minimal.' mod='ultimateimagetool'} </p>	
			<p><strong>{l s='IMPORTANT !!!! Do not compress the ORIGINAL size, until you have optimized all the other images sizes. The compression process cannot be reverted, the only way to remake the picture in case of quality loss is to rebuild the image sizes.' mod='ultimateimagetool'}</strong></p>
			<p>{l s='Test image quality by going to the `Test image quality` tab.' mod='ultimateimagetool'}</p>


		</div>
    <div class="clear"></div>
    {if $uit_cron_last_execution}<strong>{l s='Last cron execution time: ' mod='ultimateimagetool'} {$uit_cron_last_execution|escape:'htmlall':'UTF-8'}</strong>{/if}

	<br/><br/>
	<form method="post" action="">
    <table class="table">
	    <thead class="">
	    	<tr class="first">
				<th  class="">{l s='Type' mod='ultimateimagetool'}</th>
				<th class="">{l s='Counter' mod='ultimateimagetool'}</th>
				<th style="width:50%" class="">{l s='Status' mod='ultimateimagetool'}</th>
			</tr> 
	    </thead>
	    <tbody id="samdha_warper">
	    	<tr>
	    		<td>{l s='Cron Image Quality' mod='ultimateimagetool'}</td>
	    		<td>
	    		<select id="cron_image_quality" name="cron_image_quality">
	    		
	    			<option value="60" {if $uit_cron_quality == 60} selected="selected" {/if}>{l s='60' mod='ultimateimagetool'}</option>
	    			<option value="70" {if $uit_cron_quality == 70} selected="selected" {/if}>{l s='70' mod='ultimateimagetool'}</option>
	    			<option value="80" {if $uit_cron_quality == 80} selected="selected" {/if}>{l s='80' mod='ultimateimagetool'}</option>
	    			<option value="90" {if $uit_cron_quality == 90} selected="selected" {/if}>{l s='90' mod='ultimateimagetool'}</option>
	    			<option value="100" {if $uit_cron_quality == 100} selected="selected" {/if}>{l s='100' mod='ultimateimagetool'}</option>
	    		</select>

	    	
	    		</td>
	    		<td>{l s='Image quality, must be between 1-100, We recommend testing the image quality before starting a general optimization.' mod='ultimateimagetool'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Items compressed per execution' mod='ultimateimagetool'}</td>
	    		<td>
	    		<select id="product_per_excution" name="product_per_excution">
	    			<option value="1" {if $uit_product == 1} selected="selected" {/if}>1</option>
	    			<option value="2" {if $uit_product == 2} selected="selected" {/if}>2</option>
	    			<option value="3" {if $uit_product == 3} selected="selected" {/if}>3</option>
	    			<option value="4" {if $uit_product == 4} selected="selected" {/if}>4</option>
	    			<option value="5" {if $uit_product == 5} selected="selected" {/if}>5</option>
	    		</select>
	    		</td>
	    		<td>{l s='Number or products which images will be oprimized on a cron job execution.' mod='ultimateimagetool'}</td>
	    	</tr>
	    	<tr>
	    		<td colspan="3" id="cron_html">
	    			
	    		</td>
	    	</tr>
	    </tbody>
	   </table>
	   <br/>

	   </form>


</div>
<div class="clear"></div>