{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2019 Presta.Site
* @license   LICENSE.txt
*}

<div id="psl-quick-guide">
    {if $psv == 1.5}
        <br/><fieldset><legend>{l s='Quick guide' mod='pstproductfilter'}</legend>
    {else}
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-cogs"></i> {l s='Quick guide' mod='pstproductfilter'}
            </div>
    {/if}

            <h4>{l s='How to work with this module:' mod='pstproductfilter'}</h4>
            <ol>
                <li>{l s='Navigate to the products page:' mod='pstproductfilter'} <a target="_blank" href="{$pstpf_products_link|escape:'quotes':'UTF-8'}">{l s='link' mod='pstproductfilter'}</a></li>
                <li>{l s='You will see new block "Product filter". It already displays a few basic filters.' mod='pstproductfilter'}</li>
                <li>{l s='Try to use any filter input. The module will load results by ajax and update the product list.' mod='pstproductfilter'}</li>
                <li>{l s='You can also change the filter list. Click the button "Edit filters" at the top right corner of the filter block.' mod='pstproductfilter'}</li>
                <li>{l s='You will see a list of inactive filters. Drag and drop any filter to the active list to use it.' mod='pstproductfilter'}</li>
                <li>{l s='Also you can see there a list of columns. By clicking at the corresponding checkbox you can show/hide any column in the product list.' mod='pstproductfilter'}</li>
            </ol>
            <hr>
            <p>{l s='Please rate this module if you like it. You can do it in your profile at Addons Marketplace' mod='pstproductfilter'}
                (<a target="_blank" href="https://addons.prestashop.com/ratings.php" class="pbc-addons-link">{l s='link' mod='pstproductfilter'}</a>). {l s='Thanks!' mod='pstproductfilter'}</p>

    {if $psv == 1.5}
        </fieldset><br/>
    {else}
        </div>
    {/if}
</div>
