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
<div class="container-fluid">
    <div class="content-lists mt-4">
        <div class="content-lists--child setup">
            <h3>
                <i class="fas fa-asterisk mr-1"></i>
                {l s='Set up groups & properties in HubSpot' mod='pshubspot'}
            </h3>

            <div class="content-lists--child__columns mt-3">
                <div class="content-lists--child__columns-item w-100 py-3">
                    <small>
                        {l s='In order to view your PrestaShop data correctly in HubSpot, you need to set up groups and properties in your HubSpot account.' mod='pshubspot'}</strong><br />
                        {l s='Once you set up groups and properties, you can easily see the following information about your contacts and customers:' mod='pshubspot'}
                    </small>

                    <br /><br />

                    <ul>
                        <li>{l s='Order information' mod='pshubspot'}</li>
                        <li>{l s='Previous purchases' mod='pshubspot'}</li>
                        <li>{l s='Abandoned cart details' mod='pshubspot'}</li>
                        <li>{l s='And more' mod='pshubspot'}</li>
                    </ul>

                    <br />

                    <form action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}"
                        method="post" enctype="multipart/form-data">

                        <input type="hidden" name="next_step" value="2" />

                        <button name="ps-hubspot_submit" id="ps-hubspot_sumbit_btn" class="button-custom px-1 py-1 mr-0"
                            type="submit">
                            <i class="fa fa-users mr-1"></i>
                            {l s='Create groups & properties' mod='pshubspot'}
                        </button>

                        <button name="ps-hubspot_submit_skip" id="ps-hubspot_sumbit_skip_btn" class="button-custom px-1 py-1"
                            type="submit">
                            <i class="fa fa-sign-out-alt mr-1"></i>
                            {l s='Skip this step' mod='pshubspot'}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>