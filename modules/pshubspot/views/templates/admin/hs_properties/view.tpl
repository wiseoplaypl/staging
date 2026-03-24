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
<div id="pshubspot_container" class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child extras">
            <h3>
                <i class="fas fa-network-wired mr-1"></i>
                 {l s='Properties' mod='pshubspot'}
            </h3>

            <div class="table table-responsive p-3">
                <table class="table-striped w-100 text-center">
                    <thead>
                    <tr>
                        <th class="px-1 text-center">{l s='Property' mod='pshubspot'}</th>
                        <th class="px-1 text-center">{l s='Group' mod='pshubspot'}</th>
                        <th class="px-1 text-center">{l s='Property name' mod='pshubspot'}</th>
                        <th class="px-1 text-center">{l s='#' mod='pshubspot'}</th>
                    </tr>
                    </thead>

                    <tbody>
                     {foreach $properties as $property}
                        <tr>
                            <td>{$property.object_type|escape:'htmlall':'UTF-8'}</td>
                            <td>{$property.groupName|escape:'htmlall':'UTF-8'}</td>
                            <td>{$property.name|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                 {if $property.sync_at}
                                     {l s='Created' mod='pshubspot'}
                                 {else}
                                    <form method="POST" id="form_{$property.id|escape:'html':'UTF-8'}">
                                        <input type="hidden" name="id" value="{$property.id|escape:'htmlall':'UTF-8'}"/>
                                        <button name="ps-hubspot_sync_property" id="ps-hubspot_sumbit_btn"
                                                class="button-custom px-1 py-1 ml-0 mr-0 mb-0" type="submit" onclick="hs_sync_property({$property.id|escape:'htmlall':'UTF-8'})">
                                            <i class="fas fa-plus mr-1 color-white"></i>
                                             {l s='Create' mod='pshubspot'}
                                        </button>
                                    </form>
                                 {/if}
                            </td>
                        </tr>
                     {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>