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

<script>
    {literal}
    function set_def_cat() {
        if ($('.change_def_cat option:selected').val() == "1") {
            $('#masspc_def_categories').show();
        } else {
            $('#masspc_def_categories').hide();
        }
    }
    {/literal}
</script>

<div class="col-lg-12 form-group margin-form">
    <label class="form-group control-label col-lg-3">{l s='What you want to do?' mod='masspc'}</label>
    <div class="form-group margin-form ">
        <div class="col-lg-6">
            <select name="wtd">
                <option value="0">{l s='Nothing, i just want to change default category' mod='masspc'}</option>
                <option value="1">{l s='Assign selected products to categories' mod='masspc'}</option>
                <option value="2">{l s='Unassign selected products from categories' mod='masspc'}</option>
                <option value="3">{l s='Remove all associations from selected products and assign them to selected categories' mod='masspc'}</option>
                <option value="4">{l s='Assign products to selected categories and all parent categories' mod='masspc'}</option>
            </select>
        </div>
    </div>
</div>
<div class="col-lg-12 form-group margin-form">
    <label class="form-group control-label col-lg-3">{l s='Do you want to change default category for selected \'target products\' ?' mod='masspc'}</label>
    <div class="form-group margin-form ">
        <div class="col-lg-6">
            <select name="change_def_cat" class="change_def_cat" onchange="set_def_cat();">
                <option value="0">{l s='No' mod='masspc'}</option>
                <option value="1">{l s='Yes' mod='masspc'}</option>
            </select>
        </div>
    </div>
    <div class="clearfix"></div>
    <div id="masspc_def_categories" class="col-lg-12 form-group margin-form" style="display:none;">
        <label class="form-group control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" title="{l s='Select new default category' mod='masspc'}">{l s='Categories' mod='masspc'}</span>
        </label>
        <div class="form-group margin-form ">
            <div class="col-lg-9">
                {$categories_def}
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="panel-footer">
    <a href="javascript:displayCartRuleTab('tproducts');" class="btn btn-default pull-left">
        <i class="process-icon-back"></i>
        {l s='back' mod='masspc'}
    </a>
    <a href="javascript:displayCartRuleTab('cselection');" class="btn btn-default pull-right">
        <i class="process-icon-next"></i>
        {l s='next' mod='masspc'}
    </a>
</div>