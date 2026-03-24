{extends file='helpers/form/form.tpl'}

{block name='defaultForm'}

    <script>
        var deactivation_date_error = "{l s='Expire date can not be less than active date.' mod='kbwebsitedecorationeffect'}";
        var ajaxaction = '{$action|escape:'quotes':'UTF-8'}';
        
        
        var multiple_select_message = "{l s='This field can not be empty.' mod='kbwebsitedecorationeffect'}";
        var no_coupon_added = "{l s='Kindly select a coupon to be shown on the discount strip.' mod='kbwebsitedecorationeffect'}";
        var default_tab='{$default_tab|escape:'htmlall':'UTF-8'}';
        var is_default_header_image ='{$is_default_header_image|escape:'htmlall':'UTF-8'}';
        var is_default_footer_image='{$is_default_footer_image|escape:'htmlall':'UTF-8'}';
        var is_default_random_image ='{$is_default_random_image|escape:'htmlall':'UTF-8'}';
        var invalid_file_format_txt = '{l s='Invalid File Format' mod='kbwebsitedecorationeffect' }';
        var file_size_error_txt = '{l s='File size must be less than 2MB' mod='kbwebsitedecorationeffect' }';
        var invalid_file_txt = '{l s='Invalid File.' mod='kbwebsitedecorationeffect' }';
        var upload_file_error = '{l s='This field can not be empty.' mod='kbwebsitedecorationeffect' }';
        var header_image_path = {$header_image_path nofilter}; {*Variable contains html content, escape not required*}
        var footer_image_path = {$footer_image_path nofilter}; {*Variable contains html content, escape not required*}
        var random_image_path = {$random_image_path nofilter}; {*Variable contains html content, escape not required*}
        velovalidation.setErrorLanguage({
            empty_field: "{l s='Field cannot be empty.' mod='kbwebsitedecorationeffect'}",
            positive_amount: "{l s='Field should be positive.' mod='kbwebsitedecorationeffect'}",
            number_field: "{l s='You can enter only numbers.' mod='kbwebsitedecorationeffect'}",
            positive_number: "{l s='Number should be greater than 0.' mod='kbwebsitedecorationeffect'}",
            maxchar_field: "{l s='Field cannot be greater than {#} characters.' mod='kbwebsitedecorationeffect'}",
            minchar_field: "{l s='Field cannot be less than {#} character(s).' mod='kbwebsitedecorationeffect'}",
            validate_range: "{l s='Number is not in the valid range. It should be betwen #d1 and #d2' mod='kbwebsitedecorationeffect'}",
            valid_amount: "{l s='Field should be numeric.' mod='kbwebsitedecorationeffect'}",
            valid_decimal: "{l s='Field can have only upto two decimal values.' mod='kbwebsitedecorationeffect'}",
            specialchar_zip: "{l s='Zip should not have special characters.' mod='kbwebsitedecorationeffect'}",
            valid_percentage: "{l s='Percentage should be in number.' mod='kbwebsitedecorationeffect'}",
            between_percentage: "{l s='Percentage should be between 0 and 100.' mod='kbwebsitedecorationeffect'}",
            maxchar_size: "{l s='Size cannot be greater than {#} characters.' mod='kbwebsitedecorationeffect'}",
            maxchar_color: "{l s='Color could not be greater than {#} characters.' mod='kbwebsitedecorationeffect'}",
            invalid_color: "{l s='Color is not valid.' mod='kbwebsitedecorationeffect'}",
            specialchar: "{l s='Special characters are not allowed.' mod='kbwebsitedecorationeffect'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='kbwebsitedecorationeffect'}",
        });
    </script>
    <div class='row'>
            <div class="productTabs col-lg-2 col-md-3">
                <div class="list-group">
                    {$i=1}
                    {foreach $available_tabs key=numStep item=tab}
                            <a class="list-group-item {if $tab.selected|escape:'htmlall':'UTF-8'}{if $version eq 1.5}selected{else}active{/if}{/if}" id="link-{$tab.id|escape:'htmlall':'UTF-8'}" onclick="change_tab(this,{$i|escape:'htmlall':'UTF-8'});">
                                {$tab.name|escape:'htmlall':'UTF-8'}
                                <i class="icon-exclamation-circle" style="display:none;"></i>
                            </a>
                            {$i=$i+1}
                    {/foreach}
                </div>
            </div>
            
                {$form} {*Variable contains html content, escape not required*}
                {$form1} {*Variable contains html content, escape not required*}
                {$form2} {*Variable contains html content, escape not required*}
                {$form3} {*Variable contains html content, escape not required*}
                {$form4} {*Variable contains html content, escape not required*}
                <div id="notification_header_error" style="color:red;"></div>
                <div id="notification_footer_error" style="color:red;"></div>
                <div id="notification_random_error" style="color:red;"></div>
                
                
                
	</div>
{/block}

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