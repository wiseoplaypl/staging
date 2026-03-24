{**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 *}
<!-- modules/pshubspot/views/templates/admin/hs_setup/step0.tpl -->
<div class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-asterisk mr-1"></i>
                 {l s='Define Store' mod='pshubspot'}
            </h3>

            <div class="content-lists--child__columns mt-3">
                <div class="content-lists--child__columns-item w-100 py-3">
                    <small>
                         {l s='Users will be able to segment data based on the store it came from, and will be able to see separate overviews and import tools for each store.' mod='pshubspot'}
                    </small>

                    <div class="separator-line mb-2"></div>

                    <form action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}"
                          method="post" enctype="multipart/form-data">

                        <input type="hidden" name="next_step" value="1"/>

                         {foreach from=$shops item=shop}
                            <div class="w-50">
                                <div class="w-100 mb-1">
                                    <label class="width-label-inline w-50">
                                        <small>
                                             {l s='Store label for shop id %s (%s)' mod='pshubspot' sprintf=[$shop->id, $shop->name]}
                                        </small>
                                    </label>

                                    <input class="w-50" type="text" name="shop[{$shop->id|escape:'htmlall':'UTF-8'}][label]" value="{$shop->name|escape:'htmlall':'UTF-8'}"/>

                                </div>
                                 {if $sourceStores}
                                    <div class="w-100 mb-1">
                                        <label class="width-label-inline w-50">
                                            <small>
                                                 {l s='Source stores loaded from your HubSpot' mod='pshubspot'}
                                            </small>
                                        </label>
                                        <select name="shop[{$shop->id|escape:'htmlall':'UTF-8'}][sourcestore]" data-id="{$shop->id|escape:'htmlall':'UTF-8'}" class="w-50 sourceStoreSelector">
                                            <option selected disabled>{l s='Choose one' mod='pshubspot'}</option>
                                            <OPTGROUP label="{l s='Use existing one' mod='pshubspot'}">
                                                 {foreach $sourceStores as $store}
                                                    <option value="{$store['value']|escape:'htmlall':'UTF-8'}">
                                                         {$store['label']|escape:'htmlall':'UTF-8'}
                                                    </option>
                                                 {/foreach}
                                            </OPTGROUP>
                                            <OPTGROUP label="{l s='Create new' mod='pshubspot'}">
                                                <option value="-1">{l s='I want to create a new reference for this shop' mod='pshubspot'}</option>
                                            </OPTGROUP>
                                        </select>
                                    </div>
                                 {/if}
                                <div class="reference w-100 {if $sourceStores} hidden {/if}" data-ref="{$shop->id|escape:'htmlall':'UTF-8'}">
                                    <label class="width-label- w-50">
                                        <small>
                                             {l s='Unique external reference' mod='pshubspot' sprintf=[$shop->id]}
                                        </small>
                                    </label>

                                    <input class="w-50" type="text" name="shop[{$shop->id|escape:'htmlall':'UTF-8'}][ref]" value="{$shop->ref|escape:'htmlall':'UTF-8'}"/>
                                </div>
                            </div>
                         {/foreach}

                        <br/><br/>

                        <button name="ps-hubspot_submit" id="ps-hubspot_connect-account_btn"
                                class="button-custom px-1 mx-1 mr-0" type="submit">
                            <i class="fas fa-store mr-1"></i>
                             {l s='Define shop' mod='pshubspot'}
                        </button>

                        <button name="ps-hubspot_submi_skip hidden" id="ps-hubspot_connect-account_btn"
                                class="button-custom px-1 mx-1" type="submit">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                             {l s='Skip this step' mod='pshubspot'}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.sourceStoreSelector').on('change', function () {
            var id = $(this).data('id');
            $('.reference[data-ref=' + id + ']').addClass("hidden");
            if ($(this).val() == "-1") $('.reference[data-ref=' + id + ']').removeClass("hidden");
        });
    })

</script>
