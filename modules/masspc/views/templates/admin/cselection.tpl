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

<div id="masspc_categories_to" lass="col-lg-12 form-group margin-form">
    <label class="form-group control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip" title="{l s='Selected products from step 1 (Target products) will be assigned / unassigned from defined categories' mod='masspc'}">{l s='Categories' mod='masspc'}</span>
    </label>
    <div class="form-group margin-form ">
        <div class="col-lg-9">
            {$categories_to}
            <span onclick="checkAllSubcats('associated-categories-tree-to'); return false;" id="check-all-associated-subcategories-tree-bulk" class="btn btn-default">
                <i class="icon-check-sign"></i>	{l s='Check all subcategories of checked categories' mod='masspc'}
            </span>
            <span onclick="uncheckAllsubcats('associated-categories-tree-to'); return false;" id="uncheck-all-associated-subcategories-tree-bulk" class="btn btn-default">
                <i class="icon-check-empty"></i> {l s='Uncheck all subcategories of checked categories' mod='masspc'}
            </span>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<br><br/>

{if $version >= 1.6}
    <div class="panel-footer">
        <a href="javascript:displayCartRuleTab('wtd');" class="btn btn-default pull-left">
            <i class="process-icon-back"></i>
            {l s='back' mod='masspc'}
        </a>
        <button class="btn btn-default pull-right" name="saveConfiguration" type="submit">
            <i class="process-icon-save"></i>
            {l s='Mass update!' mod='masspc'}
        </button>
    </div>
{else}
    <div style="text-align:center">
        <input type="submit" value="{l s='Mass update!' mod='masspc'}" class="button" name="saveConfiguration"/>
    </div>
{/if}