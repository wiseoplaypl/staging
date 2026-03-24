{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2018 Hendrik Masson
 *  @license   Tous droits réservés
*}
{if $ps_version > 1.6}
	{assign var="base_dir_ssl" value=$urls.base_url}
	{assign var="base_dir" value=$urls.base_url}
{/if}

{if $is_admin_logged}
	<script>
	var ndk_admin_loggued = true;
	</script>
	<span class="activate-zone-edit btn">[ADMIN] {l s='edit zones' mod='ndk_advanced_custom_fields'}</span>
{else}
	<script>
		var ndk_admin_loggued = false;
	</script>
{/if}
<div class="block ndkcsfields-block config_boxes {if $is_admin_logged}is_admin_logged{/if}">
	{if ($user_configs && $user_configs|@count > 0) || ($admin_configs && $admin_configs|@count > 0 ) || ($allow_admin_config && $is_admin_logged) || $allow_user_config}
	<h3 class="ndkcfTitle userPanelTitle btn btn-default">{l s='Choose / edit configuration' mod='ndk_advanced_custom_fields'}</h3>
	{/if}
		{* {if $logged} *}
			{if $user_configs && $user_configs|@count > 0}
				<div class="form-group clearfix userPanel user-configs" data-view="0">
					<label class="toggler dontquick">{l s='Load your customization' mod='ndk_advanced_custom_fields'}</label>
					<div class="fieldPane clearfix">
						<div id="configItem_0" class="configItem col-xs-4">
							<div class="configImg">
								<img class="img-responsive ndkLoadConfigImg" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/modules/ndk_advanced_custom_fields/views/img/new_file.png"/>
							</div>
							<span class="ndkLoadConfig btn btn-default" data-id="0">{l s='Default' mod='ndk_advanced_custom_fields'}</span>
						</div>
						{foreach from=$user_configs item=config}
						{assign var=tpltags value=','|explode:$config.tags}
						<div id="configItem_{$config.id_ndk_customization_field_configuration}" class="configItem col-xs-4 filterTag {if $config.default_config == 1}default_config{/if} {if $config.tags && $config.tags !=''} tagged {foreach from=$tpltags item=tag}{$tag|replace:' ':'-'} {/foreach}{/if}" data-tags="{foreach from=$tpltags item=tag}{$tag}|{/foreach}" >
							{if $config.default_config == 1}
								{assign var=default_config value=$config.id_ndk_customization_field_configuration}
							{/if}
							
							<div class="configImg">
								{if isset($config.img) && $config.img !=''}
								<img class="img-responsive ndkLoadConfigImg" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/img/scenes/{$config.img}"/>
								{else}
								<img class="img-responsive ndkLoadConfigImg" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/modules/ndk_advanced_custom_fields/views/img/customization.png"/>
								{/if}
							</div>
							<span class="ndkLoadConfig btn btn-default" data-id="{$config.id_ndk_customization_field_configuration}">{$config.name}</span>
						</div>
						{/foreach}
					</div>
				</div>
			{/if}
		{* {/if} *}
		
		{if $admin_configs && $admin_configs|@count > 0 && $allow_admin_config}
			<div class="form-group clearfix userPanel admin-configs" data-view="0">
				<label class="toggler dontquick">{l s='Choose a predefined template' mod='ndk_advanced_custom_fields'}</label>
				<div class="fieldPane clearfix row" id="main-admintemplates">
					<div class="clear col-xs-12 clearfix visu-tools"></div>
					<div id="configItem_0" class="configItem col-xs-4">
						<div class="configImg">
							<img class="img-responsive ndkLoadConfigImg" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/modules/ndk_advanced_custom_fields/views/img/new_file.png"/>
						</div>
						<span class="ndkLoadConfig btn btn-default" data-id="0">{l s='Default' mod='ndk_advanced_custom_fields'}</span>
					</div>
					{foreach from=$admin_configs item=config}
					{assign var=tpltags value=','|explode:$config.tags}
						
					
					<div id="configItem_{$config.id_ndk_customization_field_configuration}" class="{if $config.default_config == 1}default_config{/if} configItem col-xs-4 filterTag {if $config.tags && $config.tags !=''} tagged {foreach from=$tpltags item=tag}{$tag|replace:' ':'-'} {/foreach}{/if}" data-tags="{foreach from=$tpltags item=tag}{$tag}|{/foreach}" data-root="admintemplates">
						{if $config.default_config == 1}
							{assign var=default_config value=$config.id_ndk_customization_field_configuration}
						{/if}
						<div class="configImg">
							{if isset($config.img) && $config.img !=''}
							<img class="img-responsive ndkLoadConfigImg" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/img/scenes/{$config.img}"/>
							{else}
							<img class="img-responsive ndkLoadConfigImg" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}/modules/ndk_advanced_custom_fields/views/img/customization.png"/>
							{/if}
						</div>
						<span class="ndkLoadConfig btn btn-default" data-id="{$config.id_ndk_customization_field_configuration}" data-tags="{$config.tags}">{$config.name|escape:'htmlall'}</span>
					</div>
					{/foreach}
				</div>
			</div>
		{else}
			{assign var=default_config value=0}
		{/if}
		
		{if $allow_user_config || $is_admin_logged && $allow_admin_config }
			<div class="form-group clearfix userPanel" data-view="0">
				<label class="toggler dontquick">{if $is_admin_logged}{l s='[ADMIN]' mod='ndk_advanced_custom_fields'} - {/if}{l s='Save your customization' mod='ndk_advanced_custom_fields'}</label>
					<div class="fieldPane clearfix">
					{if $is_admin_logged}<i>{l s='You see this block because you are logged in administration panel. Customers will not see it' mod='ndk_advanced_custom_fields'} </i> {/if}
					{if ($allow_user_config) || ($is_admin_logged && $allow_admin_config) }
						<p class="label-warning error text-warning" id="name_already_exists" style="display: none;">{l s='This configuration name already exists for this product, please choose another' mod='ndk_advanced_custom_fields'}</p>
						
						<input type="hidden" name="" value="{Context::getContext()->customer->id|intval}" id="ndkcf_id_customer"/>
						<p class="clear clearfix">
							<label>{l s='Name : ' mod='ndk_advanced_custom_fields'}</label>
							<input  data-message="{l s='required field' mod='ndk_advanced_custom_fields'}" class="form-control pull-left" type="text" name="ndkcf_config_name" value="{if isset($conf) && $conf}{$conf->name[Context::getContext()->language->id|intval]}{/if}" id="ndkcf_config_name"/>
						</p>
						<p class="clear clearfix configTags hidden">
							<label>{l s='Tags : ' mod='ndk_advanced_custom_fields'}</label>
							<input type="text" class=" tagify " name="ndkcf_config_tags" value="" id="ndkcf_config_tags"/>
						</p>
						<a id="ndkSaveCustomization" class="btn btn-default pull-right" href="#">{l s='Save customization' mod='ndk_advanced_custom_fields'}</a>
					{else}
						<p class="label-warning error text-warning">{l s='If you want to save you customization, you have to be a registrered user' mod='ndk_advanced_custom_fields'}</p>
						{capture name='authLink'}authentication&back={$link->getProductLink($product_id)}{/capture}
						<a href="{$link->getPageLink($smarty.capture.authLink)}" class="btn btn-default pull-left" href="#">{l s='Login' mod='ndk_advanced_custom_fields'}</a>
					{/if}
					</div>
			</div>
		{/if}
		
		{if $edit_config > 0}
			{addJsDefL name=oldRef}{$old_ref}{/addJsDefL}
		{else}
		{/if}
	<script type="text/javascript">editConfig={if $edit_config > 0}{$edit_config}{elseif $default_config > 0}{$default_config}{else}0{/if};</script>
	{addJsDefL name=refProd}{$ref_prod}{/addJsDefL}
</div>