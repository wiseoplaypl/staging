{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2020 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
<script type="text/javascript">
    function checkAllSubcats(parentTree){
        $('#' + parentTree + ' input[type=checkbox]:checked').each(function(e){
            $(this).parent().parent().find('input[type=checkbox]').attr('checked', true);
        });
    }
    function uncheckAllsubcats(parentTree){
        $('#' + parentTree + ' input[type=checkbox]:checked').each(function(e){
            $(this).parent().parent().find('input[type=checkbox]').attr('checked', false);
        });
    }

    $(document).ready(function () {
        displayCartRuleTab('tproducts');
    });
    function displayCartRuleTab(tab) {
        $('.masspc_tab').hide();
        $('.masspc_tab_page').removeClass('selected');
        $('#masspc_' + tab).show();
        $('#masspc_link_' + tab).addClass('selected');
        $('#currentFormTab').val(tab);
    }
</script>
{if $errors != false}
    <div class="bootstrap">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {foreach from=$errors item=value name=foo}
                {math equation="a+b" a=$smarty.foreach.foo.index b=1}. {$value}
                <br/>
            {/foreach}
        </div>
    </div>
{/if}
<div class="masspc_container">
    <div class="col-lg-2 " id="mass-pc">
        <div class="productTabs">
            <ul class="tab">
                <li class="tab-row">
                    <a class="masspc_tab_page selected" id="masspc_link_tproducts" href="javascript:displayCartRuleTab('tproducts');">1. {l s='Target products' mod='masspc'}</a>
                </li>
                <li class="tab-row">
                    <a class="masspc_tab_page" id="masspc_link_wtd" href="javascript:displayCartRuleTab('wtd');">2. {l s='What to do' mod='masspc'}</a>
                </li>
                <li class="tab-row">
                    <a class="masspc_tab_page" id="masspc_link_cselection" href="javascript:displayCartRuleTab('cselection');">3. {l s='Categories selection' mod='masspc'}</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Tab Content -->
    <form action="{$URL|escape:'htmlall':'UTF-8'}&amp;saveConfiguration" name="masspc_form" id="masspc_form" method="post" enctype="multipart/form-data" class="col-lg-10 panel" {if $version < 1.6}style="margin-left: 145px;"{/if}>
        <input type="hidden" id="currentFormTab" name="currentFormTab" value="general"/>
        <div id="masspc_tproducts" class="masspc_tab tab-pane">
            <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/masspc/views/img/config.png"/> {l s='Target products' mod='masspc'}</h3>
            <div class="separation"></div>
            {include file="../admin/target_products.tpl"}
        </div>
        <div id="masspc_wtd" class="masspc_tab tab-pane" style="display:none;">
            <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/masspc/views/img/config.png"/> {l s='What to do' mod='masspc'}</h3>
            <div class="separation"></div>
            {include file="../admin/wtd.tpl"}
        </div>
        <div id="masspc_cselection" class="masspc_tab tab-pane" style="display:none;">
            <h3 class="tab"><img src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/masspc/views/img/config.png"/> {l s='Categories selection' mod='masspc'}</h3>
            <div class="separation"></div>
            {include file="../admin/cselection.tpl"}
        </div>
        <div class="separation"></div>
    </form>
    <div class="clearfix"></div>
</div>
<br></br>
<div class="clearfix"></div>
{literal}
    <style type="text/css">
        /*== PS 1.6 ==*/
        #mass-pc ul.tab {
            list-style: none;
            padding: 0;
            margin: 0
        }

        #mass-pc ul.tab li a {
            background-color: white;
            border: 1px solid #DDDDDD;
            display: block;
            margin-bottom: -1px;
            padding: 10px 15px;
        }

        #mass-pc ul.tab li a {
            display: block;
            color: #555555;
            text-decoration: none
        }

        #mass-pc ul.tab li a.selected {
            color: #fff;
            background: #00AFF0
        }

        #masspc_form .language_flags {
            display: none
        }

        form#masspc_form {
            background-color: #ebedf4;
            border: 1px solid #ccced7;
            /*min-height: 404px;*/
            padding: 5px 10px 10px;
        }
    </style>
{/literal}