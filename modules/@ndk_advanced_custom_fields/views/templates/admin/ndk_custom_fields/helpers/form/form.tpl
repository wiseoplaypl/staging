{extends file="helpers/form/form.tpl"}

{block name="legend"}
	<div class="promo-banner-support">
		<h2><i class="material-icons">help</i>{l s="Do you need more ?" mod="ndk_advanced_custom_fields"}</h2>
		<p>{l s='You need information, more functionnalities, specific integration or want us to configure your products ?' mod="ndk_advanced_custom_fields"}</p>
		<p><a target="_blank" class="btn btn-outline-secondary contact-btn" href="https://addons.prestashop.com/contact-form.php?id_product=19536">{l s='Contact us to get help or qotation' mod="ndk_advanced_custom_fields"}</a></p>
	</div>
	
	<div class="panel-heading">
		{if isset($field.image) && isset($field.title)}<img src="{$field.image}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
			{if isset($field.icon)}<i class="{$field.icon}"></i>{/if}
			{$field.title}
	</div>
	<div class="admin-form-tabs">
		<a class="ndk-tab active" data-target="ndk-main">{l s="Main" mod="ndk_advanced_custom_fields"}</a>
		<a class="ndk-tab" data-target="ndk-design">{l s="Design" mod="ndk_advanced_custom_fields"}</a>
		<a class="ndk-tab visible-field hidden-10 hidden-5 hidden-11 hidden-23 hidden-8 hidden-99 " data-target="ndk-visual">{l s="Visual Effect" mod="ndk_advanced_custom_fields"}</a>
		<a class="ndk-tab hidden-field visible-0 visible-13 visible-14 visible-29 hidden-99 hidden-28 visible-29 visible-30" data-target="ndk-text  ">{l s="Text" mod="ndk_advanced_custom_fields"}</a>
		<a class="ndk-tab hidden-field visible-11 visible-17 visible-24 visible-23 visible-22 visible-26 visible-27 visible-14 visible-101  hidden-99 hidden-28" data-target="ndk-crossselling">{l s="Packs" mod="ndk_advanced_custom_fields"}</a>
	</div>
	
{/block}
{block name="field"}
    {if $input.type == 'rangeslider'}
    	{assign var=input_id value=$input.name|classname}
        <div class="col-lg-9 {$input.class}">
            <div class="slider-range slider-range-{$input_id}" target=".amount-{$input_id}" data-values="{if $fields_value[$input.name] == ''}0 - 0{else}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" data-step="{$input.step|intval}" data-max="{$input.max|intval}" data-min="{$input.min|intval}"></div>
             <input type="text"  data-slider=".slider-range-{$input_id}"  class="slider-range-input amount-{$input_id}"  name="{$input.name}" readonly style="border:0; color:#f6931f; font-weight:bold;">
              <p class="help-block">{$input.desc nofilter}</p>  
        </div>
        
        
     {else if $input.type == 'numberslider'}
    	{assign var=input_id value=$input.name|classname}
    	
        <div class="col-lg-9 {$input.class}">
            <div id="{$input_id}" class="slider-number slider-number-{$input_id}" data-value="{if $fields_value[$input.name] == ''}0{else}{$fields_value[$input.name]|escape:'html':'UTF-8'}{/if}" data-step="{$input.step|intval}" data-max="{$input.max|intval}" data-min="{$input.min|intval}" target=".amount-{$input_id}"></div>
                <input type="text"  data-slider=".slider-number-{$input_id}"  class="slider-number-input amount-{$input_id}"  name="{$input.name}" readonly style="border:0; color:#f6931f; font-weight:bold;"/>
				<p class="help-block">{$input.desc nofilter}</p>
        </div>
        
    {else if $input.type == 'product-autocomplete'}
    	<div class="col-lg-9">
	    	<div class="ajax_choose_product" class="row">
				<div class="col-lg-12">
					<p class="alert alert-info">{l s='Begin typing the first few letters of the product name, then select the product you are looking for from the drop-down list:'}</p>
					<div class="clear clearfix prodlist">
						{if ($input.products !='')}
							{foreach $input.products as $prod }
									<button data-id="{$prod['id_product']}" class="btn btn-default prodrow" type="button"><i class="icon-remove"></i>{$prod['name']} ( ref : {$prod['reference']} )</button>
							{/foreach}
						{/if}
					</div>
					<div class="input-group row-margin-bottom">
						<span class="input-group-addon">
							<i class="icon-search"></i>
						</span>
						<input type="text" value="" class="product_autocomplete_input"/>
						<input type="text" class="product-result" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" name="{$input.name}" />
					</div>
				</div>
			</div>
    	</div>
    {else}
        {$smarty.block.parent}
    {/if}
    
    
     <script type="text/javascript">
           
           
           $( function() {
	           $( ".slider-number" ).each(function(){
		             value =  parseFloat($(this).attr('data-value'));
                     min=  $(this).attr('data-min');
                     max=  $(this).attr('data-max');
                     step=  $(this).attr('data-step');
                      
		           $( this ).slider({
                     range: false,
                     value: value,
                     min: min,
                     max: max,
                     step: step,
                     slide: function( event, ui ) {
	                     target = $($(this).attr('target'));
                         target.val(  ui.value );
                     }
                	});
	           })
	           
	           $( ".slider-number-input" ).each(function(){
	                slider = $($(this).attr('data-slider'));
	                $(this).val(  slider.slider( "value") );
                })
                
                 $( ".slider-range" ).each(function(){
	                 values =  $(this).attr('data-values').split(' - ');
                     min=  $(this).attr('data-min');
                     max=  $(this).attr('data-max');
                     step=  $(this).attr('data-step');
	                 
	                 $( this ).slider({
	                     range: true,
	                     values: values,
	                     min: min,
	                     max: max,
	                     step: step,
	                     slide: function( event, ui ) {
		                     target = $($(this).attr('target'));
	                         target.val(  ui.values[ 0 ] + " - " + ui.values[ 1 ] );
	                     }
					 });
                 });
                    					
                $( ".slider-range-input" ).each(function(){
	                slider = $($(this).attr('data-slider'));
	                $(this).val(  slider.slider( "values", 0 ) +' - '+ slider.slider( "values", 1 ) );
                })
                
            });
            
            
        </script>
        
{/block}

