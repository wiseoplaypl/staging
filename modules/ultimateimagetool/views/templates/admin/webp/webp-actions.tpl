   <div class="panel">
	   <div class="panel-heading"><i class="icon-book"></i> {l s='Settings' mod='ultimateimagetool'}</div>
			<div class="alert alert-info"><span class="alert_close"></span>
				<p>{l s='You can disable it at any time with no risk to your original JPG images.' mod='ultimateimagetool'}</p>
				<p>{l s='If you have updated from a module version that does not support WebP conversion, after you update the module, you should reset the module to reinstall overrides.' mod='ultimateimagetool'}</p>
				<p>{l s='You must manually optimize your existing images, when you first activate the option. It will automatically convert all your future images to webp while this option is active' mod='ultimateimagetool'}</p>
				<p>{l s='WebP images for cms/theme/module images are only displayed with .jpg and if the browser support .webp is automatically served and mime type changed but you will still see .jpg in the image src="" section' mod='ultimateimagetool'}</p>
				<p>{l s='Test if webp is active by following these steps:' mod='ultimateimagetool'} <a style="text-decoration: underline;" target="_blank" href="{$uit_module_path|escape:'htmlall':'UTF-8'}/check_webp.pdf">{l s='Check if webp works guide ' mod='ultimateimagetool'}</a></p>
			</div>
	   		<div class="clear"></div>
			<table class="table">
		    	<tr>
		    		<td>{l s='Enable WebP images' mod='ultimateimagetool'}</td>
		    		<td>
		    		<select id="uit_webp" name="uit_webp">
		    			<option value="0" >{l s='Disabled' mod='ultimateimagetool'}</option>
		    			<option value="1" {if $uit_use_webp == 1}selected{/if}>{l s='Enabled - replace .jpg with .webp, example image will be: http://yoursite.com/img/demoimage.webp (NOT RECOMMENDED)' mod='ultimateimagetool'}</option>
		    			<option value="2" {if $uit_use_webp == 2}selected{/if}>{l s='Enabled - keep .jpg termination, serve .webp and change mime type to image/webp, example image will be: http://yoursite.com/img/demoimage.jpg but the format will be webp (RECOMMENDED)' mod='ultimateimagetool'}</option>
		    		</select>
		    		</td>
		    		<td>{l s='WebP Enabled/Disabled in the frontend, you can disable it at any time with no risk to your original JPG images.' mod='ultimateimagetool'} <div><strong>{l s='We strongly recommend using the last option in the left selector, images will still have .jpg termination in the page source but the images will be served in .webp format' mod='ultimateimagetool'}</strong></div></td>
		    	</tr>
					<tr>
		    		<td>{l s='Auto convert Images to webp' mod='ultimateimagetool'}</td>
		    		<td>
		    		<select id="uit_auto_webp" name="uit_auto_webp">
		    			<option value="0" >{l s='No' mod='ultimateimagetool'}</option>
		    			<option value="1" {if $uit_auto_webp == 1}selected{/if}>{l s='Yes' mod='ultimateimagetool'}</option>
		    		</select>
		    		</td>
		    		<td>{l s='If you mass import products, your script will timeout, you can disable this option to save memory and manually convert them after' mod='ultimateimagetool'}</td>
		    	</tr>
		    </table>



		    <div id="uit_action_status" style="display:none" class="alert alert-success">{l s='WebP image status updated' mod='ultimateimagetool'}</div>
		    <br/>
   </div>
			{if !$webp_exists && !$imagick_exists}
				{if !$webp_exists}
					<div class="alert alert-danger"><span class="alert_close"></span>
						<p>{l s='php GD extension is not installed or does not support webp conversion' mod='ultimateimagetool'}</p>
					</div>
				{/if}
				{if !$imagick_exists}
				<div class="alert alert-danger"><span class="alert_close"></span>
					{l s='php Imagick extension is not installed or does not support webp conversion' mod='ultimateimagetool'}
					<br/> 
					
				</div>
				{/if}
				<div class="alert alert-danger"><span class="alert_close"></span>
					{l s='Contact your hosting provider to install any of the above php extensions with webp support (It`s not enough to have the extensions installed, they must have webp support), to use the webp conversion tool' mod='ultimateimagetool'}
					<br/>
				</div>
				<div class="alert alert-info"><span class="alert_close"></span>
					<p>{l s='As a fallback, you can continue to convert the images, you will use our inhouse conversion software via API (it is FREE), but it`s not optimal as it will add delay to the conversion process, and it will depend on the our server usage and it`s not always available.' mod='ultimateimagetool'}</p>
					<p>{l s='After you finish converting with our inhouse conversion, contact your hosting and enable one of the above php extensions.' mod='ultimateimagetool'}</p>
					<br/>
				</div>
			{/if}
			
