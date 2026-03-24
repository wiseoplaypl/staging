<div class="offer-countdown" style="background: {$bg_color};color:{$text_color}">
    <div class="offer-countdown-content">
        {l s='Use coupon code ' mod='kbwebsitedecorationeffect'}<span class="coupon-name">{$coupon_code}</span> {l s='to get' mod='kbwebsitedecorationeffect'} {if $is_amount_type == 1}{$reduction_amount} {l s='discount' mod='kbwebsitedecorationeffect'}{else if $is_percentage_type == 1} {$reduction_percentage} % {l s='discount' mod='kbwebsitedecorationeffect'}{else}{l s='Free Shipping' mod='kbwebsitedecorationeffect'}{/if} {l s='on your next order.' mod='kbwebsitedecorationeffect'}<span id="demo"></span>
    </div>
    <p class="offer-countdown-close" style="background: {$cross_bg_color};">x</p>
</div>
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