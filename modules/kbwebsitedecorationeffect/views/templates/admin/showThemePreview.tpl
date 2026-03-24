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
{if $element_type == 'header'}
<div name="kbwd_preview_header_element" id="kbwd_preview_header_element" style="width: 30%">
    <img class="header-preview-image" style="max-width:570px;max-height:200px; border: 1px solid #C7D6DB" src=""> {*Variable contains URL, can not escape this*}
</div>
{elseif $element_type == 'footer'}
<div name="kbwd_preview_footer_element" id="kbwd_preview_footer_element" style="width: 30%">
    <img class="footer-preview-image" style="max-width:570px;max-height:200px; border: 1px solid #C7D6DB" src=""> {*Variable contains URL, can not escape this*}
</div>
{else}
<div name="kbwd_preview_random_element" id="kbwd_preview_random_element" style="width: 30%">
    <img class="random-preview-image" style="max-width:570px;max-height:200px; border: 1px solid #C7D6DB" src=""> {*Variable contains URL, can not escape this*}
</div>
{/if}
