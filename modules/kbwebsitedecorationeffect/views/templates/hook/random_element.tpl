{if isset($is_special_effect_image) && is_special_effect_image == 1}
    <script>
        var random_image = '{$random_image_path}';
        var design_type = '{$design_type}';
    </script>
    <div id="kb_christmas_thatha_image" style="bottom:0; right:0;">
        <img src="{$random_image_path}">
    </div>
{else}
    <img src="{$random_image_path}" id="random_element" class="{$css_class_name}" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}
{/if}
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