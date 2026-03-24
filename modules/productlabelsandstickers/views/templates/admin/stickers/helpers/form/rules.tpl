{*
* 2007-2018 PrestaShop
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
*  @author    FMM Modules
*  @copyright 2018 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<form class="form-horizontal" enctype="multipart/form-data" method="post" id="configuration_form"
	action="{$action|escape:'htmlall':'UTF-8'}">
	{if $id > 0}<input type="hidden" value="{$id|escape:'htmlall':'UTF-8'}" name="fmm_stickers_rules_id" />{/if}
	<div class="panel{if $ps_17 <= 0} ps_16_specific{/if}">
		<div class="panel-heading"><i class="icon-cogs"></i> {l s='Create/Edit Rule' mod='productlabelsandstickers'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<div>
					<label class="control-label col-lg-3 required">{l s='Status' mod='productlabelsandstickers'}</label>
					<div class="col-lg-9">
						<p class="radio">
							<label for="PQ_STAT_1"><input type="radio" value="1" {if $data.status > 0} checked="checked"
									{/if} id="PQ_STAT_1"
									name="status">{l s='Enable' mod='productlabelsandstickers'}</label>
						</p>
						<p class="radio">
							<label for="PQ_STAT_2"><input type="radio" value="0" {if $data.status <= 0}
									checked="checked" {/if} id="PQ_STAT_2"
									name="status">{l s='Disable' mod='productlabelsandstickers'}</label>
						</p>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3">{l s='Title' mod='productlabelsandstickers'}</label>
				<div class="col-lg-6">
					<input type="text" name="title" {if !empty($data.title)}
						value="{$data.title|escape:'htmlall':'UTF-8'}" {/if} />
					<div class="help-block">{l s='Only used in backoffice.' mod='productlabelsandstickers'}</div>
				</div>
			</div>
			<div class="form-group">
				<label
					class="control-label col-lg-3">{l s='Start Date(Optional)' mod='productlabelsandstickers'}:</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" class="startdatepicker" value="{$data.start_date|escape:'htmlall':'UTF-8'}"
							name="start_date">
						<span class="input-group-addon"><img
								src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productlabelsandstickers/views/img/date.png"></span>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="form-group">
				<label
					class="control-label col-lg-3">{l s='Expiry Date(Optional)' mod='productlabelsandstickers'}:</label>
				<div class="col-lg-3">
					<div class="input-group">
						<input type="text" class="expirydatepicker" value="{$data.expiry_date|escape:'htmlall':'UTF-8'}"
							name="expiry_date">
						<span class="input-group-addon"><img
								src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/productlabelsandstickers/views/img/date.png"></span>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-group">
				<label
					class="control-label col-lg-3 required">{l s='Select Shop' mod='productlabelsandstickers'}</label>
				<div class="col-lg-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="fixed-width-xs"> </th>
								<th class="fixed-width-xs"><span class="title_box">ID</span></th>
								<th>
									<span class="title_box">
										{l s='Store name' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input type="checkbox" value="0" id="groupBox_1" onclick="selectAllShops(this);"
										class="groupBox" name="shops[]">
								</td>
								<td>0</td>
								<td>
									<label for="groupBox_1">{l s='All' mod='productlabelsandstickers'}</label>
								</td>
							</tr>
							{foreach from=$shops item=_item}
								<tr>
									<td>
										<input type="checkbox" value="{$_item.id_shop|escape:'htmlall':'UTF-8'}"
											{if in_array($_item.id_shop, $shop_data)} checked="checked" {/if}
											id="groupBox_2" class="groupBox sub_sp" name="shops[]">
									</td>
									<td>{$_item.id_shop|escape:'htmlall':'UTF-8'}</td>
									<td>
										<label for="groupBox_2">{$_item.name|escape:'htmlall':'UTF-8'}</label>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<div class="form-group">
				<label
					class="control-label col-lg-3 required">{l s='Select Sticker' mod='productlabelsandstickers'}</label>
				<div class="col-lg-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th class="fixed-width-xs"> </th>
								<th class="fixed-width-xs"><span class="title_box">ID</span></th>
								<th>
									<span class="title_box">
										{l s='Sticker' mod='productlabelsandstickers'}
									</span>
								</th>
								<th>
									<span class="title_box">
										{l s='Sticker Label' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input {if $data.sticker_id !== '' && $data.sticker_id == 0} checked="checked" {/if} type="radio" name="sticker_id" value="0" />
								</td>
								<td>
									{l s='-' mod='productlabelsandstickers'}
								</td>
								<td>
									{l s='-' mod='productlabelsandstickers'}
								</td>
								<td>
								<b>{l s='Disable' mod='productlabelsandstickers'}</b>
								</td>
							</tr>
							{foreach from=$stickers item=_sticker}
								<tr>
									<td>
										<input type="radio" name="sticker_id"
											value="{$_sticker.sticker_id|escape:'htmlall':'UTF-8'}"
											{if $data.sticker_id == $_sticker.sticker_id} checked="checked" {/if} />
									</td>
									<td>
										{$_sticker.sticker_id|escape:'htmlall':'UTF-8'}
									</td>
									<td>
										{if $_sticker.text_status > 0}
											{$_sticker.title|escape:'htmlall':'UTF-8'}
										{else}
											<img src="{$base_image|escape:'htmlall':'UTF-8'}{$_sticker.sticker_image|escape:'htmlall':'UTF-8'}"
												width="50" />
										{/if}
									</td>
									<td>
										{$_sticker.sticker_name|escape:'htmlall':'UTF-8'}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<div class="form-group">
				<label
					class="control-label col-lg-3 required">{l s='Select Banners' mod='productlabelsandstickers'}</label>
				<div class="col-lg-6">
					<table class="table table-bordered">
						<thead style="width:100%;">
							<tr>
								<th width="15%" style="padding-left:3px;">
									<h1
										style="color: #585563; font-size: 15px !important; margin-top: 7px !important; margin-bottom: 7px !important; margin-left:7px !important">
										{l s='Select' mod='productlabelsandstickers'}</h1>
								</th>
								<th width="85%" style="padding-left:3px">
									<h1
										style="color: #585563; font-size: 15px !important; margin-top: 7px !important; margin-bottom: 7px !important;margin-left:7px !important">
										{l s='Banner' mod='productlabelsandstickers'}</h1>
								</th>
							</tr>
						</thead>
						<tbody style="width:100%;">
							<tr>
								<td style="padding-left:6px"><input id="stickerbanner_0" type="radio" {if isset($data.stickerbanner_id)}{if $data.stickerbanner_id == 0} checked="checked" {/if}{/if}
										name="stickerbanner" value="0" /></td>
								<td><label for="stickerbanner_0">{l s='Disable' mod='productlabelsandstickers'}</label>
								</td>
							</tr>
							{foreach from=$fmm_banners key=i item=banner}
								<tr>
									<td style="padding-left:6px"><input type="radio" {if isset($data.stickerbanner_id)}{if $data.stickerbanner_id == $banner.stickersbanners_id} checked="checked" {/if}{/if}
											id="stickerbanner_{$banner.stickersbanners_id|escape:'htmlall':'UTF-8'}"
											name="stickerbanner"
											value="{$banner.stickersbanners_id|escape:'htmlall':'UTF-8'}" /></td>
									<td><label for="stickerbanner_{$banner.stickersbanners_id|escape:'htmlall':'UTF-8'}">
											<div
												style="padding: 10px; text-align: center; color: {$banner.color|escape:'htmlall':'UTF-8'}; background: {$banner.bg_color|escape:'htmlall':'UTF-8'}; border: 1px solid {$banner.border_color|escape:'htmlall':'UTF-8'};font-family: {$banner.font|escape:'htmlall':'UTF-8'}; font-size: {$banner.font_size|escape:'htmlall':'UTF-8'}px;font-weight: {$banner.font_weight|escape:'htmlall':'UTF-8'};">
												{$banner.title|escape:'htmlall':'UTF-8'}</div>
										</label></td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			<div class="form-group">
				<label
					class="control-label col-lg-3 required">{l s='Select Rule' mod='productlabelsandstickers'}</label>
				<div class="col-lg-6">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th> </th>
								<th>
									<span class="title_box">
										{l s='Rule Type' mod='productlabelsandstickers'}
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input type="radio" name="rule" value="product" {if $data.rule_type == 'product'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Product' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="onsale" {if $data.rule_type == 'onsale'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='On Sale - Has Specific Prices' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="outofstock"
										{if $data.rule_type == 'outofstock'} checked="checked" {/if}
										onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Out of stock - Not combination base' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="new" {if $data.rule_type == 'new'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='New Product' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="bestseller"
										{if $data.rule_type == 'bestseller'} checked="checked" {/if}
										onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Bestsellers' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="price_less"
										{if $data.rule_type == 'price_less'} checked="checked" {/if}
										onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Price Less Than' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="price_greater"
										{if $data.rule_type == 'price_greater'} checked="checked" {/if}
										onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Price Greater Than' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="reference"
										{if $data.rule_type == 'reference'} checked="checked" {/if}
										onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Reference' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="tag" {if $data.rule_type == 'tag'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Tag' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="category" {if $data.rule_type == 'category'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Category' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="brand" {if $data.rule_type == 'brand'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Brand/Manufacturer' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="supplier" {if $data.rule_type == 'supplier'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Supplier' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="customer" {if $data.rule_type == 'customer'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Customer Group' mod='productlabelsandstickers'}
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="stock_g" {if $data.rule_type == 'stock_g'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Stock/Quantity Greater Than' mod='productlabelsandstickers'}
									({l s='Not combination relative' mod='productlabelsandstickers'})
								</td>
							</tr>
							<tr>
								<td>
									<input type="radio" name="rule" value="stock_l" {if $data.rule_type == 'stock_l'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Stock/Quantity Less Than' mod='productlabelsandstickers'}
									({l s='Not combination relative' mod='productlabelsandstickers'})
								</td>
							</tr>

							<tr>
								<td>
									<input type="radio" name="rule" value="condition"
										{if $data.rule_type == 'condition'} checked="checked" {/if}
										onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Product Condition' mod='productlabelsandstickers'}
								</td>
							</tr>

							<tr>
								<td>
									<input type="radio" name="rule" value="p_type" {if $data.rule_type == 'p_type'}
										checked="checked" {/if} onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Product Type' mod='productlabelsandstickers'}
								</td>
							</tr>

							<tr>
								<td>
									<input type="radio" name="rule" value="p_feature"
										{if $data.rule_type == 'p_feature'} checked="checked" {/if}
										onclick="checkMate(this);" />
								</td>
								<td>
									{l s='Has Features' mod='productlabelsandstickers'}
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="form-group" id="rule_brands_list" {if $data.rule_type == 'brand'} style="display: block" 
			{else}
				style="display: none" {/if}>
				<label
					class="control-label col-lg-3">{l s='Brands/Manufacturers' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th> </th>
									<th>
										<span class="title_box">
											{l s='ID' mod='productlabelsandstickers'}
										</span>
									</th>
									<th>
										<span class="title_box">
											{l s='Name' mod='productlabelsandstickers'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								{if !isset($brands) || empty($brands)}
									<tr>
										<td>{l s='No brands found.' mod='productlabelsandstickers'}</td>
									</tr>
								{else}
									{foreach from=$brands item=brand}
										<tr>
											<td>
												<input type="checkbox" name="brands[]" value="{$brand.id}"
													{if isset($data.value_array) && in_array($brand.id, $data.value_array)}
													checked="checked" {/if} />
											</td>
											<td>
												{$brand.id}
											</td>
											<td>
												{$brand.name}
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="form-group" id="rule_supplier_list" {if $data.rule_type == 'supplier'} style="display: block"
				{else} style="display: none" 
				{/if}>
				<label class="control-label col-lg-3">{l s='Suppliers' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th> </th>
									<th>
										<span class="title_box">
											{l s='ID' mod='productlabelsandstickers'}
										</span>
									</th>
									<th>
										<span class="title_box">
											{l s='Name' mod='productlabelsandstickers'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								{if !isset($suppliers) || empty($suppliers)}
									<tr>
										<td>{l s='No suppliers found.' mod='productlabelsandstickers'}</td>
									</tr>
								{else}
									{foreach from=$suppliers item=brand}
										<tr>
											<td>
												<input type="checkbox" name="suppliers[]" value="{$brand.id}"
													{if isset($data.value_array) && in_array($brand.id, $data.value_array)}
													checked="checked" {/if} />
											</td>
											<td>
												{$brand.id}
											</td>
											<td>
												{$brand.name}
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
						<div class="col-lg-9">
							<div class="help-block">
								{l s='*Rule is only for ' mod='productlabelsandstickers'} <i
									style="color: red">{l s='default supplier' mod='productlabelsandstickers'}</i>

							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="form-group" id="rule_condition_list" {if $data.rule_type == 'condition'} style="display: block"
				{else} style="display: none" 
				{/if}>
				<label class="control-label col-lg-3">{l s='Product Condition' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th> </th>
									<th>
										<span class="title_box">
											{l s='ID' mod='productlabelsandstickers'}
										</span>
									</th>
									<th>
										<span class="title_box">
											{l s='Name' mod='productlabelsandstickers'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<input type="checkbox" name="conditions[]" value="1"
											{if isset($data.value_array) && in_array(1, $data.value_array)}
											checked="checked" {/if}>
									</td>
									<td>
										1
									</td>
									<td>
										{l s='NEW' mod='productlabelsandstickers'}
									</td>
								</tr>
								<tr>
									<td>
										<input type="checkbox" name="conditions[]" value="2"
											{if isset($data.value_array) && in_array(2, $data.value_array)}
											checked="checked" {/if}>
									</td>
									<td>
										2
									</td>
									<td>
										{l s='Used' mod='productlabelsandstickers'}
									</td>
								</tr>

								<tr>
									<td>
										<input type="checkbox" name="conditions[]" value="3"
											{if isset($data.value_array) && in_array(3, $data.value_array)}
											checked="checked" {/if}>
									</td>
									<td>
										3
									</td>
									<td>
										{l s='Refurbished' mod='productlabelsandstickers'}
									</td>
								</tr>
							</tbody>
						</table>
						<div class="col-lg-9">

						</div>
					</div>
				</div>
			</div>


			<div class="form-group" id="rule_p_type_list" {if $data.rule_type == 'p_type'} style="display: block" 
			{else}
				style="display: none" {/if}>
				<label class="control-label col-lg-3">{l s='Product Type' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th> </th>
									<th>
										<span class="title_box">
											{l s='ID' mod='productlabelsandstickers'}
										</span>
									</th>
									<th>
										<span class="title_box">
											{l s='Name' mod='productlabelsandstickers'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>
										<input type="checkbox" name="p_types[]" value="0"
											{if isset($data.value_array) && in_array(0, $data.value_array)}
											checked="checked" {/if}>
									</td>
									<td>
										0
									</td>
									<td>
										{l s='Standard product' mod='productlabelsandstickers'}
									</td>
								</tr>
								<tr>
									<td>
										<input type="checkbox" name="p_types[]" value="1"
											{if isset($data.value_array) && in_array(1, $data.value_array)}
											checked="checked" {/if}>
									</td>
									<td>
										1
									</td>
									<td>
										{l s='Pack of products' mod='productlabelsandstickers'}
									</td>
								</tr>

								<tr>
									<td>
										<input type="checkbox" name="p_types[]" value="2"
											{if isset($data.value_array) && in_array(2, $data.value_array)}
											checked="checked" {/if}>
									</td>
									<td>
										2
									</td>
									<td>
										{l s='Virtual product' mod='productlabelsandstickers'}
									</td>
								</tr>
							</tbody>
						</table>
						<div class="col-lg-9">

						</div>
					</div>
				</div>
			</div>

			<div class="form-group" id="rule_feature_list" {if $data.rule_type == 'p_feature'} style="display: block;"
				{else} style="display: none" 
				{/if}>
				<label class="control-label col-lg-3">{l s='Features' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th> </th>
									<th>
										<span class="title_box">
											{l s='ID' mod='productlabelsandstickers'}
										</span>
									</th>
									<th>
										<span class="title_box">
											{l s='Name' mod='productlabelsandstickers'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								{if !isset($allfeatures) || empty($allfeatures)}
									<tr>
										<td>{l s='No Feature found.' mod='productlabelsandstickers'}</td>
									</tr>
								{else}
									{foreach from=$allfeatures item=feature}
										<tr>
											<td>
												<input type="checkbox" name="feature[]" value="{$feature.id_feature_value}"
													{if isset($data.value_array) && in_array($feature.id_feature_value, $data.value_array)}
													checked="checked" {/if} />
											</td>
											<td>
												{$feature.id_feature_value}
											</td>
											<td>
												{$feature.name}-> {$feature.value}
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="form-group" id="rule_category_list" {if $data.rule_type == 'category'} style="display: block;"
				{else} style="display: none" 
				{/if}>
				<label class="control-label col-lg-3">{l s='Categories' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th> </th>
									<th>
										<span class="title_box">
											{l s='ID' mod='productlabelsandstickers'}
										</span>
									</th>
									<th>
										<span class="title_box">
											{l s='Name' mod='productlabelsandstickers'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								{if !isset($categories) || empty($categories)}
									<tr>
										<td>{l s='No brands found.' mod='productlabelsandstickers'}</td>
									</tr>
								{else}
									{foreach from=$categories item=category}
										<tr>
											<td>
												<input type="checkbox" name="category[]" value="{$category.id_category}"
													{if isset($data.value_array) && in_array($category.id_category, $data.value_array)}
													checked="checked" {/if} />
											</td>
											<td>
												{$category.id_category}
											</td>
											<td>
												{$category.name}
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="form-group" id="rule_product_list" {if $data.rule_type == 'product'} style="display: block;"
				{else} style="display: none" 
				{/if}>
				<br />
				<label class="control-label col-lg-3">{l s='Find Product' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8 placeholder_holder">
						<input type="text" placeholder="Example: Blue XL shirt" onkeyup="getRelProducts(this);" />
						<div id="rel_holder"></div>
						<div id="rel_holder_temp">
							<ul>
								{if (!empty($products)) && $products[0] != null}
									{foreach from=$products item=product}
										<li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media">
											<div class="media-left"><img
													src="{$link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}"
													class="media-object image"></div>
											<div class="media-body media-middle"><span
													class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i
													onclick="relDropThis(this);" class="material-icons delete">clear</i></div>
											<input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}"
												name="related_products[]">
										</li>
									{/foreach}
								{/if}
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group" id="rule_product_list_exclude">
				<br />
				<label class="control-label col-lg-3">{l s='Excluded Products' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8 placeholder_holder">
						<input type="text" placeholder="Example: Blue XL shirt" onkeyup="getRelProductsEx(this);" />
						<div id="rel_holder_ex"></div>
						<div id="ex_rel_holder_temp">
							<ul>
								{if (!empty($ex_products)) && $ex_products[0] != null}
									{foreach from=$ex_products item=product}
										<li id="row_{$product->id|escape:'htmlall':'UTF-8'}" class="media">
											<div class="media-left"><img
													src="{$link->getImageLink($product->link_rewrite, $product->id_image, 'home_default')|escape:'htmlall':'UTF-8'}"
													class="media-object image"></div>
											<div class="media-body media-middle"><span
													class="label">{$product->name|escape:'htmlall':'UTF-8'}&nbsp;(ID:{$product->id|escape:'htmlall':'UTF-8'})</span><i
													onclick="relDropThisEx(this);" class="material-icons delete">clear</i></div>
											<input type="hidden" value="{$product->id|escape:'htmlall':'UTF-8'}"
												name="excluded_products[]">
										</li>
									{/foreach}
								{/if}
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group" id="rule_value"
				{if $data.rule_type == 'new' || $data.rule_type == 'onsale' || $data.rule_type == 'outofstock' || $data.rule_type == 'bestseller' || $data.rule_type == 'brand' || $data.rule_type == 'supplier' || $data.rule_type == 'category' || $data.rule_type == 'p_feature' || $data.rule_type == 'product' || $data.rule_type == 'customer' || !isset($data.rule_type)}
				style="display: none" {else} style="display: block" 
				{/if}>
				<label class="control-label col-lg-3 required">{l s='Rule Value' mod='productlabelsandstickers'}</label>
				<div class="col-lg-6">
					<input type="text" name="rule_value" value="{$data.value|escape:'htmlall':'UTF-8'}" />
				</div>
				<div class="col-lg-9 col-lg-offset-3">
					<div class="help-block">
						{l s='In case of' mod='productlabelsandstickers'} <i
							style="color: red">{l s='reference OR tags' mod='productlabelsandstickers'}</i>
						{l s='DO NOT add space after comma.' mod='productlabelsandstickers'}<br />
					</div>
				</div>
			</div>

			<div class="form-group" id="rule_customer_list" {if $data.rule_type == 'customer'} style="display: block"
				{else} style="display: none" 
				{/if}>
				<label class="control-label col-lg-3">{l s='Customer Groups' mod='productlabelsandstickers'}</label>
				<div class="col-lg-9">
					<div class="col-lg-8">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th> </th>
									<th>
										<span class="title_box">
											{l s='ID' mod='productlabelsandstickers'}
										</span>
									</th>
									<th>
										<span class="title_box">
											{l s='Name' mod='productlabelsandstickers'}
										</span>
									</th>
								</tr>
							</thead>
							<tbody>
								{if !isset($customers) || empty($customers)}
									<tr>
										<td>{l s='No Customer groups found.' mod='productlabelsandstickers'}</td>
									</tr>
								{else}
									{foreach from=$customers item=customer}
										<tr>
											<td>
												<input type="checkbox" name="customers[]" value="{$customer.id_group}"
													{if isset($data.value_array) && in_array($customer.id_group, $data.value_array)}
													checked="checked" {/if} />
											</td>
											<td>
												{$customer.id_group}
											</td>
											<td>
												{$customer.name}
											</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
		<div class="panel-footer">
			<button name="submitRules" class="btn btn-default pull-right" type="submit"><i
					class="process-icon-save"></i> {l s='Save' mod='productlabelsandstickers'}</button>
		</div>
	</div>

</form>
<script>
	{literal}
		var mod_url = "{/literal}{$action_url|escape:'htmlall':'UTF-8'}{literal}";
		mod_url = mod_url.replace(/&amp;/g, "&");

		function selectAllShops(g) {
			if (jQuery(g).is(":checked")) {
				jQuery('.sub_sp').attr('disabled', 'disabled');
			} else {
				jQuery('.sub_sp').removeAttr('disabled');
			}
		}

		function checkMate(_e) {
			var e_val = jQuery(_e).val();
			if (e_val === 'new' || e_val === 'onsale' || e_val === 'bestseller' || e_val === 'outofstock') {
				jQuery('#rule_value').hide();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_p_type_list').hide();
				jQuery('#rule_condition_list').hide();
				jQuery('#rule_product_list_exclude').show();
			} else if (e_val === 'brand') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_brands_list').show();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_condition_list').hide();
				jQuery('#rule_product_list_exclude').show();
				jQuery('#rule_p_type_list').hide();
			} else if (e_val === 'supplier') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_supplier_list').show();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_product_list_exclude').show();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_condition_list').hide();
				jQuery('#rule_p_type_list').hide();
			} else if (e_val === 'category') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_category_list').show();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_product_list_exclude').show();
				jQuery('#rule_condition_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_p_type_list').hide();
			} else if (e_val === 'p_feature') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').show();
				jQuery('#rule_product_list_exclude').show();
				jQuery('#rule_condition_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_p_type_list').hide();
			} else if (e_val === 'product') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_product_list').show();
				jQuery('#rule_condition_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_p_type_list').hide();
				jQuery('#rule_product_list_exclude').hide();
			} else if (e_val === 'customer') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_product_list_exclude').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_customer_list').show();
				jQuery('#rule_p_type_list').hide();
				jQuery('#rule_condition_list').hide();
			} else if (e_val === 'condition') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_product_list_exclude').show();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_p_type_list').hide();
				jQuery('#rule_condition_list').show();
			} else if (e_val === 'p_type') {
				jQuery('#rule_value').hide();
				jQuery('#rule_value input').attr('disabled', 'disabled');
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_product_list_exclude').show();
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_p_type_list').show();
				jQuery('#rule_condition_list').hide();
			} else {
				jQuery('#rule_value').show();
				jQuery('#rule_value input').removeAttr('disabled');
				jQuery('#rule_brands_list').hide();
				jQuery('#rule_supplier_list').hide();
				jQuery('#rule_category_list').hide();
				jQuery('#rule_feature_list').hide();
				jQuery('#rule_product_list').hide();
				jQuery('#rule_product_list_exclude').show();
				jQuery('#rule_condition_list').hide();
				jQuery('#rule_customer_list').hide();
				jQuery('#rule_p_type_list').hide();
			}
			if (e_val === 'reference' || e_val === 'tag' || e_val === 'stock_g' || e_val === 'stock_l') {
				jQuery('#rule_product_list_exclude').hide();
			}
			console.log(e_val);
		}
		$(document).ready(function() {
			$('.expirydatepicker, .startdatepicker').datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd',
				// Define a custom regional settings in order to use PrestaShop translation tools
				currentText : {/literal}'{l s='Now' mod='productlabelsandstickers' js=1}'{literal},
				closeText 	: {/literal}'{l s='Done' mod='productlabelsandstickers' js=1}'{literal},
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: {/literal}'{l s='Choose Time' mod='productlabelsandstickers' js=1}'{literal},
				timeText 	: {/literal}'{l s='Time' mod='productlabelsandstickers' js=1}'{literal},
				hourText 	: {/literal}'{l s='Hour' mod='productlabelsandstickers' js=1}'{literal},
				minuteText 	: {/literal}'{l s='Minute' mod='productlabelsandstickers' js=1}'{literal},
			});
		});

		function getRelProducts(e) {
			var search_q_val = $(e).val();
			//controller_url = controller_url+'&q='+search_q_val;
			if (typeof search_q_val !== 'undefined' && search_q_val) {
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: mod_url + '&q=' + search_q_val,
					success: function(data) {
						var quicklink_list =
							'<li class="rel_breaker" onclick="relClearData();"><i class="material-icons">&#xE14C;</i></li>';
						$.each(data, function(index, value) {
							if (typeof data[index]['id'] !== 'undefined')
								quicklink_list += '<li onclick="relSelectThis(' + data[index]['id'] + ',' +
								data[index]['id_product_attribute'] + ',\'' + data[index]['name'] +
								'\',\'' + data[index]['image'] + '\');"><img src="' + data[index][
									'image'
								] + '" width="60"> ' + data[index]['name'] + '</li>';
						});
						if (data.length == 0) {
							quicklink_list = '';
						}
						$('#rel_holder').html('<ul>' + quicklink_list + '</ul>');
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log(textStatus);
					}
				});
			} else {
				$('#rel_holder').html('');
			}
		}

		function getRelProductsEx(e) {
			var search_q_val = $(e).val();
			//controller_url = controller_url+'&q='+search_q_val;
			if (typeof search_q_val !== 'undefined' && search_q_val) {
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: mod_url + '&q=' + search_q_val,
					success: function(data) {
						var quicklink_list =
							'<li class="rel_breaker" onclick="relClearDataEx();"><i class="material-icons">&#xE14C;</i></li>';
						$.each(data, function(index, value) {
							if (typeof data[index]['id'] !== 'undefined')
								quicklink_list += '<li onclick="relSelectThisEx(' + data[index]['id'] +
								',' + data[index]['id_product_attribute'] + ',\'' + data[index]['name'] +
								'\',\'' + data[index]['image'] + '\');"><img src="' + data[index][
									'image'
								] + '" width="60"> ' + data[index]['name'] + '</li>';
						});
						if (data.length == 0) {
							quicklink_list = '';
						}
						$('#rel_holder_ex').html('<ul>' + quicklink_list + '</ul>');
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log(textStatus);
					}
				});
			} else {
				$('#rel_holder_Ex').html('');
			}
		}

		function relSelectThis(id, ipa, name, img) {
			if ($('#row_' + id + '_' + ipa).length > 0) {
				showErrorMessage(error_msg);
			} else {
				var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="' + img +
					'" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name +
					'&nbsp;(ID:' + id +
					')</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="' +
					id + '" name="related_products[]"></li>'
				$('#rel_holder_temp ul').append(draw_html);
			}
		}

		function relSelectThisEx(id, ipa, name, img) {
			if ($('#row_' + id + '_' + ipa).length > 0) {
				showErrorMessage(error_msg);
			} else {
				var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="' + img +
					'" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name +
					'&nbsp;(ID:' + id +
					')</span><i onclick="relDropThisEx(this);" class="material-icons delete">clear</i></div><input type="hidden" value="' +
					id + '" name="excluded_products[]"></li>'
				$('#ex_rel_holder_temp ul').append(draw_html);
			}
		}

		function relClearData() {
			$('#rel_holder').html('');
		}

		function relClearDataEx() {
			$('#rel_holder_ex').html('');
		}

		function relDropThis(e) {
			$(e).parent().parent().remove();
		}

		function relDropThisEx(e) {
			$(e).parent().parent().remove();
		}
	{/literal}
</script>{literal}
	<style type="text/css">
		#rule_category_list {
			max-height: 600px;
			overflow-y: scroll
		}

		#rel_holder ul {
			position: absolute;
			left: 12px;
			border-radius: 4px;
			top: 40px;
			margin: 0px 0 20%;
			padding: 0;
			background: #fff;
			border: 1px solid #BBCDD2;
			z-index: 999
		}

		#rel_holder ul li {
			list-style: none;
			padding: 5px 10px;
			display: block;
			margin: 0px
		}

		#rel_holder ul li:hover {
			cursor: pointer;
			background: #25B9D7
		}

		#rel_holder ul li.rel_breaker {
			padding: 0px;
			margin: -1px -22px 0 0;
			background: #fff;
			float: right;
			border: 1px solid #BBCDD2;
			border-left: 0px;
			height: 24px;
		}

		#rel_holder ul li.rel_breaker:hover {
			background: #fff;
		}

		.rel_breaker i {font-size: 22px; color: #E50B70; cursor: pointer}
		#rel_holder_temp {
			clear: both;
			padding: 10px 0
		}

		#rel_holder_temp ul {
			padding: 0;
			margin: 0
		}

		#rel_holder_temp ul li {
			list-style: none;
			padding: 3px 5px;
			border-radius: 5px;
			margin: 6px 0;
			border: 1px solid #E5E5E5;
			display: block
		}

		#rel_holder_temp ul li div {
			display: inline-block;
			vertical-align: middle
		}

		#rel_holder_temp ul li .media-left {
			width: 8%
		}

		#rel_holder_temp ul li .media-left img {
			max-width: 100%
		}

		#rel_holder_temp ul li .media-body {
			width: 86%;
			margin-left: 5%
		}

		#rel_holder_temp ul li .media-body span {
			float: left;
			font-size: 13px;
			color: #6c868e;
			font-weight: normal;
			white-space: normal !important;
			text-align: left;
			width: 92%
		}

		#rel_holder_temp ul li .media-body i {
			float: right;
			cursor: pointer
		}

		.placeholder_holder {
			position: relative
		}

		.ps_16_specific .material-icons {font-size: 1px;color: #fff;}
		.ps_16_specific .material-icons::before {content: "\f00d"; font-family: "FontAwesome"; font-size: 25px;text-align: center;
		color: red;
		font-style: normal;
		text-indent: -9999px;
		font-weight: normal;
		line-height: 20px;
		}


		#rel_holder_ex ul {
			position: absolute;
			left: 12px;
			border-radius: 4px;
			top: 40px;
			margin: 0px 0 20%;
			padding: 0;
			background: #fff;
			border: 1px solid #BBCDD2;
			z-index: 999
		}

		#rel_holder_ex ul li {
			list-style: none;
			padding: 5px 10px;
			display: block;
			margin: 0px
		}

		#rel_holder_ex ul li:hover {
			cursor: pointer;
			background: #25B9D7
		}

		#rel_holder_ex ul li.rel_breaker {
			padding: 0px;
			margin: -1px -22px 0 0;
			background: #fff;
			float: right;
			border: 1px solid #BBCDD2;
			border-left: 0px;
			height: 24px;
		}

		#rel_holder_ex ul li.rel_breaker:hover {
			background: #fff;
		}

		.rel_breaker i {font-size: 22px; color: #E50B70; cursor: pointer}
		#ex_rel_holder_temp {
			clear: both;
			padding: 10px 0
		}

		#ex_rel_holder_temp ul {
			padding: 0;
			margin: 0
		}

		#ex_rel_holder_temp ul li {
			list-style: none;
			padding: 3px 5px;
			border-radius: 5px;
			margin: 6px 0;
			border: 1px solid #E5E5E5;
			display: block
		}

		#ex_rel_holder_temp ul li div {
			display: inline-block;
			vertical-align: middle
		}

		#ex_rel_holder_temp ul li .media-left {
			width: 8%
		}

		#ex_rel_holder_temp ul li .media-left img {
			max-width: 100%
		}

		#ex_rel_holder_temp ul li .media-body {
			width: 86%;
			margin-left: 5%
		}

		#ex_rel_holder_temp ul li .media-body span {
			float: left;
			font-size: 13px;
			color: #6c868e;
			font-weight: normal;
			white-space: normal !important;
			text-align: left;
			width: 92%
		}

		#ex_rel_holder_temp ul li .media-body i {
			float: right;
			cursor: pointer
		}
	</style>
{/literal}