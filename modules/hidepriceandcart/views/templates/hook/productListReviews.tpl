{*
* Hide Price and Cart
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   callforprice
* @author    FMM Modules
* @copyright Copyright 2021 © FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{if $flag == 1}
{if $is_price_enable == 1}
    <div id="fmm-hide-price" class="hidepriceandcart hidden">
    </div>
    
{/if}
{if $is_contact_enable == 1}
    <div  class="hidepriceandcart_msg" style="background-color:{$bg_color|escape:'htmlall':'UTF-8'};">
        <a class=""  style="background-color:{$bg_color|escape:'htmlall':'UTF-8'};" href='{$fmm_contact_us}'>{$message nofilter}</a>
    </div>
{/if}
    <div class="fmm_rule_applicable"></div>
<script>
    fmm_rule_applicable = true;
</script>
{else}
    <script>
    fmm_rule_applicable = false;
</script>
{/if}
