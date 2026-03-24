{*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   productlabelsandstickers
* @author    FMM Modules
* @copyright FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div id="stickers_{$id|escape:'htmlall':'UTF-8'}">
{foreach $stickers as $stick}
{literal}
<script>
{/literal}{if {$stick.x_align} == 'left' && {$stick.y_align} == 'top'}{literal}
$('#stickers_{/literal}{$id|escape:'htmlall':'UTF-8'}{literal}').parent().parent().find('.product_img_link')
.prepend("<span style='display: inline-block; position: absolute; left: 6px; top: 6px;'><img src='{/literal}{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}{literal}' style='width:{/literal}{$stick.sticker_size_list|escape:'htmlall':'UTF-8'}px{literal};' /></span>");
{/literal}{elseif {$stick.x_align} == 'right' && {$stick.y_align} == 'top'}{literal}
$('#stickers_{/literal}{$id|escape:'htmlall':'UTF-8'}{literal}').parent().parent().find('.product_img_link')
.prepend("<span style='display: inline-block; position: absolute; right: 6px; top: 6px;'><img src='{/literal}{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}{literal}' style='width:{/literal}{$stick.sticker_size_list|escape:'htmlall':'UTF-8'}px{literal};' /></span>");
{/literal}{elseif {$stick.x_align} == 'left' && {$stick.y_align} == 'bottom'}{literal}
$('#stickers_{/literal}{$id|escape:'htmlall':'UTF-8'}{literal}').parent().parent().find('.product_img_link')
.append("<span style='display: inline-block; position: absolute; left: 6px; bottom: 6px;'><img src='{/literal}{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}{literal}' style='width:{/literal}{$stick.sticker_size_list|escape:'htmlall':'UTF-8'}px{literal};' /></span>");
{/literal}{else}{literal}
$('#stickers_{/literal}{$id|escape:'htmlall':'UTF-8'}{literal}').parent().parent().find('.product_img_link')
.append("<span style='display: inline-block; position: absolute; right: 6px; bottom: 6px;'><img src='{/literal}{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}{literal}' style='width:{/literal}{$stick.sticker_size_list|escape:'htmlall':'UTF-8'}px{literal};' /></span>");
{/literal}{/if}{literal}
//alert(par);
</script>
{/literal}
{/foreach}
</div>






