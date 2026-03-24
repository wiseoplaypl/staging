{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<script>
var confirm_delete_data= '{l s='Do you want to clear this data?' mod='ets_imagecompressor' js='1'}';
var confirm_delete_all_data= '{l s='Do you want to clear all data?' mod='ets_imagecompressor' js='1'}';
</script>
<div id="module" class="panel">
    <div class="panel-heading">
        {l s='Database optimization' mod='ets_imagecompressor'}
    </div>
    <form>
        <div class="form-wrapper">
            <div class="alert alert-info">{l s='Clean unnecessary data in Prestashop database to improve your page loading time. The data below are only used for statistics and can be cleared if the statistics are not important for you.' mod='ets_imagecompressor'}</div>
            <table id="table-data" class="table data">
                <thead>
                    <tr>
                        <th><span class="title_box active">{l s='Data type' mod='ets_imagecompressor'}</span></th>
                        <th><span class="title_box active">{l s='Description' mod='ets_imagecompressor'}</span></th>
                        <th><span class="title_box active">{l s='Records' mod='ets_imagecompressor'}</span></th>
                        <th><span class="title_box active">{l s='Status' mod='ets_imagecompressor'}</span></th>
                        <th><span class="title_box active">{l s='Action' mod='ets_imagecompressor'}</span></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$datas item='data'}
                        <tr>
                            <td>{$data.name|escape:'html':'UTF-8'}</td>
                            <td>{$data.desc|escape:'html':'UTF-8'}</td>
                            <td><span class="total_data_row">{$data.total|intval}</span></td>
                            <td>
                                {if $data.total < 500}
                                    <span class="status-good">{l s='Good' mod='ets_imagecompressor'}</span>
                                {else if $data.total >=500  && $data.total<1000}
                                    <span class="status-medium">{l s='Medium' mod='ets_imagecompressor'}</span>
                                {else if}
                                    <span class="status-many">{l s='So many, clearance recommended' mod='ets_imagecompressor'}</span>
                                {/if}
                            </td>
                            <td>
                                {if $data.total}
                                    <a href="{$data.link_download|escape:'html':'UTF-8'}" target="_blank">
                                        <i class="fa fa-download"></i>{l s='Download' mod='ets_imagecompressor'}
                                    </a>
                                    <a class="delete_data_cache" href="{$data.link_delete|escape:'html':'UTF-8'}">
                                        <i class="fa fa-eraser"></i>{l s='Clean' mod='ets_imagecompressor'}
                                    </a>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <a class="btn btn-default pull-left delete_all_data_cache" href="{$link_delete_all|escape:'html':'UTF-8'}">
                <i class="icon-trash"></i> {l s='Clean all' mod='ets_imagecompressor'}
            </a>
        </div>
    </form>
</div>