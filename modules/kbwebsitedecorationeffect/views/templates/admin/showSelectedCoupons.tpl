{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}

<div id="kb_excluded_coupon_holder">
    {if isset($selectedcoupons) && !empty($selectedcoupons)}
            <div class="form-control-static">
                <button type="button" onclick="deleteSelectedCoupon({$selectedcoupons['id_rule']|escape:'htmlall':'UTF-8'}, this);" class="delExcludedProduct btn btn-default" name="{$selectedcoupons['id_rule']|escape:'htmlall':'UTF-8'}"><i class="icon-remove text-danger"></i></button>
                &nbsp;{$selectedcoupons['name']}
            </div> 
        
    {/if}

</div>
