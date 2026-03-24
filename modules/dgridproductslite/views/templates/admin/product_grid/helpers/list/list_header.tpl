{*
* 2007-2016 PrestaShop
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
* @author    SeoSA <885588@bk.ru>
* @copyright 2012-2020 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}
    <script>
		if (typeof tabs_manager == 'undefined')
			var tabs_manager = {};
        {assign var='priceDisplayPrecision' value=$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval}
        var currencySign = "{$currencySign|escape:'quotes':'UTF-8'}";
        var currencyRate = "{$currencyRate|escape:'quotes':'UTF-8'}";
        var currencyFormat = "{$currencyFormat|escape:'quotes':'UTF-8'}";
        var currencyBlank = "{$currencyBlank|escape:'quotes':'UTF-8'}";
        var priceDisplayPrecision = "{$priceDisplayPrecision|escape:'quotes':'UTF-8'}";
        var id_default_lang = {$default_lang->id|intval};
        var ad = '{$ad|addslashes|escape:'quotes':'UTF-8'}';
        var ps_v = "{$ps_v|escape:'quotes':'UTF-8'}";
    </script>
    <script>
        l_grid = {};
        l_grid['ean13'] = "{l s='Ean13 wrong' mod='dgridproductslite'}";
        l_grid['upc'] = "{l s='Upc wrong' mod='dgridproductslite'}";
        l_grid['type_error'] = "{l s='Write wrong' mod='dgridproductslite'}";
    </script>
{/block}

{block name=leadin}
    <!-- list_header.tpl -->
    <div class="tab-content">
    <div class="panel mode_search tab-pane active" id="grid_prod_search">
        <div class="row">
            <div class="form-group">
                <div class="col-lg-6 tree_custom">
                    <div class="form-group">
                        <div class="row">
                            <label class="control-label col-lg-12">
                                {l s='Select category by search' mod='dgridproductslite'}
                            </label>
                            {include file="./tree.tpl"
                            categories=$categories
                            id_category=Configuration::get('PS_ROOT_CATEGORY')
                            root=true
                            view_header=true
                            multiple=true
                            selected_categories=$selected_categories
                            name='categories'
                            }
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 search-products">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="search_product_name float-right">
                                    {include file="./btn_radio.tpl" input=$input_product_name_type_search}
                                </div>

                                <div class="float-left">
                                    <div class="float-left">
                                        <label class="form-group control-label search-prod margin-right">{l s='Search product' mod='dgridproductslite'}</label>
                                    </div>
                                    <span>
                                        <input name="search_query" class="form-control fixed-width-lg float-left margin-right" type="text" value="{$search_query|escape:'quotes':'UTF-8'}"/>
                                        <select class="form-control float-left fixed-width-lg custom-select" name="type_search">
                                            <option value="0" {if $type_search == 0}selected{/if}>{l s='Name' mod='dgridproductslite'}</option>
                                            <option value="1" {if $type_search == 1}selected{/if}>{l s='Id product' mod='dgridproductslite'}</option>
                                            <option value="2" {if $type_search == 2}selected{/if}>{l s='Reference' mod='dgridproductslite'}</option>
                                            <option value="3" {if $type_search == 3}selected{/if}>{l s='EAN-13' mod='dgridproductslite'}</option>
                                            <option value="4" {if $type_search == 4}selected{/if}>{l s='UPC' mod='dgridproductslite'}</option>
                                            <option value="5" {if $type_search == 5}selected{/if}>{l s='Description' mod='dgridproductslite'}</option>
                                            <option value="6" {if $type_search == 6}selected{/if}>{l s='Description short' mod='dgridproductslite'}</option>
                                        </select>
                                    </span>
                                </div>

                            </div>
                        </div>
                        <hr>
                        <div class="row form-group">
                            <label class="control-label col-lg-12 label-margin">
                                {l s='Search by manufacturer' mod='dgridproductslite'}
                            </label>
                            <div class="col-lg-12">
                                <select id="manufacturer" class="" style="width: 100%;" multiple name="manufacturer[]">
                                    <option value="0">-</option>
                                    {foreach from=$manufacturers item=manufacturer}
                                        <option value="{$manufacturer.id_manufacturer|intval}" {if in_array($manufacturer.id_manufacturer, $search_manufacturers)}selected{/if}>{$manufacturer.name|escape:'quotes':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                                <script>
                                    $('[name="manufacturer[]"]').select2();
                                </script>
                            </div>
                        </div>
                        <hr>
                        <div class="row form-group">
                            <label class="control-label col-xs-12">
                                {l s='Search by supplier' mod='dgridproductslite'}
                            </label>
                            <div class="col-xs-12">
                                <select id="supplier" class="form-control" style="width: 100%;" multiple name="supplier[]">
                                    <option value="0">-</option>
                                    {foreach from=$suppliers item=supplier}
                                        <option value="{$supplier.id_supplier|intval}" {if in_array($supplier.id_supplier, $search_suppliers)}selected{/if}>{$supplier.name|escape:'quotes':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                                <script>
                                    $('[name="supplier[]"]').select2();
                                </script>
                            </div>
                        </div>
                        <hr>
                        <div class="row form-group">
                            <label class="control-label col-xs-12">
                                {l s='Search by carrier' mod='dgridproductslite'}
                            </label>
                            <div class="col-xs-12">
                                <select id="carrier" class="form-control" style="width: 100%;" multiple name="carrier[]">
                                    <option value="0">-</option>
                                    {foreach from=$carriers item=carrier}
                                        <option value="{$carrier.id_reference|intval}" {if in_array($carrier.id_reference, $search_carriers)}selected{/if}>{$carrier.name|escape:'quotes':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                                <script>
                                    $('[name="carrier[]"]').select2();
                                </script>
                            </div>
                        </div>
                        <hr>
                        <div class="row form-group">
                            <div class="col-xs-12 col-sm-6">
                                <label class="control-label float-left margin-right">
                                    {l s='Only active products' mod='dgridproductslite'}
                                </label>
                                {if $smarty.const._PS_VERSION_ < 1.6}
                                    <div class="float-left">
                                        <label class="t"><img src="../img/admin/enabled.gif"></label>
                                        <input name="active" value="1" type="radio"/>
                                        <label class="t"><img src="../img/admin/disabled.gif"></label>
                                        <input checked name="active" value="0" type="radio"/>
                                    </div>
                                {else}
                                    <span class="switch prestashop-switch fixed-width-lg float-left">
                                        {foreach [1,0] as $value}
                                            <input
                                                    type="radio"
                                                    name="active"
                                                    {if $value == 1}
                                                        id="active_on"
                                                    {else}
                                                        id="active_off"
                                                    {/if}
                                                    value="{$value|escape:'quotes':'UTF-8'}"
                                                    {if $active == $value}checked="checked"{/if}
                                            />
                                            <label
                                                    {if $value == 1}
                                                        for="active_on"
                                                    {else}
                                                        for="active_off"
                                                    {/if}
                                            >
                                                {if $value == 1}
                                                    {l s='Yes' mod='dgridproductslite'}
                                                {else}
                                                    {l s='No' mod='dgridproductslite'}
                                                {/if}
                                            </label>
                                        {/foreach}
                                        <a class="slide-button btn"></a>
                                    </span>
                                {/if}
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="control-label float-left margin-right">
                                    {l s='Only disabled products' mod='dgridproductslite'}
                                </label>
                                {if $smarty.const._PS_VERSION_ < 1.6}
                                    <div class="float-left">
                                        <label class="t"><img src="../img/admin/enabled.gif"></label>
                                        <input name="disable" value="1" type="radio"/>
                                        <label class="t"><img src="../img/admin/disabled.gif"></label>
                                        <input checked name="disable" value="0" type="radio"/>
                                    </div>
                                {else}
                                    <span class="switch prestashop-switch fixed-width-lg float-left">
                                        {foreach [1,0] as $value}
                                            <input
                                                    type="radio"
                                                    name="disable"
                                                    {if $value == 1}
                                                        id="disable_on"
                                                    {else}
                                                        id="disable_off"
                                                    {/if}
                                                    value="{$value|escape:'quotes':'UTF-8'}"
                                                    {if $disable == $value}checked="checked"{/if}
                                            />
                                            <label
                                                    {if $value == 1}
                                                        for="disable_on"
                                                    {else}
                                                        for="disable_off"
                                                    {/if}
                                            >
                                                {if $value == 1}
                                                    {l s='Yes' mod='dgridproductslite'}
                                                {else}
                                                    {l s='No' mod='dgridproductslite'}
                                                {/if}
                                            </label>
                                        {/foreach}
                                        <a class="slide-button btn"></a>
                                    </span>
                                {/if}
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-xs-12">
                                <label class="control-label margin-right-lg float-left">
                                    {l s='Search by quantity?' mod='dgridproductslite'}
                                </label>
                                <div class="clearfix float-left">

                                    <div class="search-quantity float-left margin-right">
                                        <label class="control-label float-left margin-right">
                                            {l s='From' mod='dgridproductslite'}
                                        </label>
                                        <input class="fixed-width-sm float-left" type="text" name="qty_from" {if $qty_from}value="{$qty_from|intval}"{/if}>
                                    </div>

                                    <div class="search-quantity float-left margin-right">
                                        <label class="control-label float-left margin-right">
                                            {l s='To' mod='dgridproductslite'}
                                        </label>
                                        <input class="fixed-width-sm float-left" type="text" name="qty_to" {if $qty_to}value="{$qty_to|intval}"{/if}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group row">
                            <div class="col-xs-12">
                                <label class="control-label margin-right-lg float-left">
                                    {l s='Search by price?' mod='dgridproductslite'}
                                </label>
                                <div class="clearfix float-left">
                                    <div class="search-quantity float-left margin-right">
                                        <label class="control-label float-left margin-right">
                                            {l s='From' mod='dgridproductslite'}
                                        </label>
                                        <input class="fixed-width-sm float-left" type="text" name="price_from" {if $price_from}value="{$price_from|intval}"{/if}>
                                    </div>

                                    <div class="search-quantity float-left margin-right">
                                        <label class="control-label float-left margin-right">
                                            {l s='To' mod='dgridproductslite'}
                                        </label>
                                        <input class="fixed-width-sm float-left" type="text" name="price_to" {if $price_to}value="{$price_to|intval}"{/if}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {$datetimepicker}
                        <div class="form-group clearfix">
                            <label class="control-label float-left margin-right">
                                {l s='How many to show products?' mod='dgridproductslite'}
                            </label>
                            <div class="float-left">
                                <select class="form-control custom-select" name="how_many_show">
                                    <option selected value="20" {if $how_many_show == 20}selected{/if}>20</option>
                                    <option value="50" {if $how_many_show == 50}selected{/if}>50</option>
                                    <option value="100" {if $how_many_show == 100}selected{/if}>100</option>
                                    <option value="300" {if $how_many_show == 300}selected{/if}>300</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        {if count($attributes)}
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="clearfix form-group">

                                        <label class="control-label margin-right">{l s='Select attributes' mod='dgridproductslite'}</label>

                                        <select name="attribute_group" class="fixed-width-lg custom-select" id="select_attribute">
                                            {foreach from=$attribute_groups item=attribute_group}
                                                <option
                                                        class="option_feature"
                                                        value="{$attribute_group.id_attribute_group|intval}">
                                                    {$attribute_group.name|escape:'quotes':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>

                                        <button id="btn_list" class="btn btn-sm {if $search_attributes[0]}open btn-danger{else}btn-success{/if}">
                                            <i class="icon-caret-down"></i>
                                            <i class="icon-caret-up"></i>
                                            <span class="view-block">{l s='View' mod='dgridproductslite'}</span>
                                            <span class="hidden-block">{l s='Hidden' mod='dgridproductslite'}</span>
                                        </button>

                                    </div>

                                    {foreach from=$attribute_groups item=attribute_group}
                                        <div class="row data-attribute-values"
                                             style="display: none;"
                                             data-attribute-values="{$attribute_group.id_attribute_group|intval}">
                                            {foreach from=$attributes item=attribute}
                                                {if $attribute_group.name == $attribute.group}
                                                    <div class="col-xs-12 col-sm-3" class="form-group clearfix">
                                                        <div class="attribute_checkbox">
                                                            <label class="control-label">
                                                                <input type="checkbox" name="attributes[]" value="{$attribute.id_attribute|escape:'quotes':'UTF-8'}"{if in_array($attribute.id_attribute, $search_attributes)} checked="checked"{/if}>
                                                                {$attribute.name|escape:'quotes':'UTF-8'}
                                                            </label>
                                                        </div>
                                                    </div>
                                                {/if}
                                            {/foreach}
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        {/if}
                        <div class="row form-group col-xs-12 col-sm-12">
                            <label class="control-label float-left margin-right">
                                {l s='Input custom feature' mod='dgridproductslite'}
                            </label>
                            <input type="text" class="col-xs-3 col-sm-3" style="margin-bottom: 10px;" name="custom_feature">

                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <label class="control-label float-left margin-right">
                                {l s='Low stock threshold' mod='dgridproductslite'}
                            </label>
                            {if $smarty.const._PS_VERSION_ < 1.6}
                                <div class="float-left">
                                    <label class="t"><img src="../img/admin/enabled.gif"></label>
                                    <input name="low" value="1" type="radio"/>
                                    <label class="t"><img src="../img/admin/disabled.gif"></label>
                                    <input checked name="low" value="0" type="radio"/>
                                </div>
                            {else}
                                <span class="switch prestashop-switch fixed-width-lg float-left">
                                        {foreach [1,0] as $value}
                                            <input
                                                    type="radio"
                                                    name="low"
                                                    {if $value == 1}
                                                        id="low_on"
                                                    {else}
                                                        id="low_off"
                                                    {/if}
                                                    value="{$value|escape:'quotes':'UTF-8'}"
                                                    {if $low == $value}checked="checked"{/if}
                                            />
                                            <label
                                                    {if $value == 1}
                                                        for="low_on"
                                                    {else}
                                                        for="low_off"
                                                    {/if}
                                            >
                                                {if $value == 1}
                                                    {l s='Yes' mod='dgridproductslite'}
                                                {else}
                                                    {l s='No' mod='dgridproductslite'}
                                                {/if}
                                            </label>
                                        {/foreach}
                                        <a class="slide-button btn"></a>
                                    </span>
                            {/if}
                        </div>

                    </div>
                </div>
                <div class="col-lg-12 control_btn">
                    <button id="beginSearch" class="btn btn-success">
                        {l s='Search product' mod='dgridproductslite'}
                    </button>
                    <button id="resetSearch" class="btn btn-danger">
                        {l s='Reset filter' mod='dgridproductslite'}
                    </button>
                </div>
            </div>
        </div>

        <button class="hidden_filter btn btn-primary">
            {l s='Search product' mod='dgridproductslite'} <i class="icon-chevron-down"></i>
        </button>

    </div>
    </div>
{/block}