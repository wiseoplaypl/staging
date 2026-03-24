{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
* @copyright  2007-2023 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<form id="module_form" class="defaultForm form-horizontal" action="{$action|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
    <div class="panel" id="fieldset_0">
        <div class="panel-heading"><i class="icon-cog"></i>{l s='Test mail' mod='brlautomail'}</div>

        <div class="form-wrapper">
                            
            <div class="form-group">
                <label class="control-label col-lg-3">{l s='Test Email' mod='brlautomail'}</label>
                <div class="col-lg-9">
                    <input type="text" name="email_test" id="email_test" value="" class="fixed-width-lr">
                    <p class="help-block"></p>
                </div>
            </div>
            
        </div>
        
        <div class="panel-footer">
            <button type="submit" value="1" id="brl_save" name="brl_send_test" class="btn btn-default pull-right">
                <i class="process-icon-envelope"></i> {l s='Send test email' mod='brlautomail'}
            </button>
        </div>
    </div>
</form>
