{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *}

<form class="form-horizontal">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-refresh"></i>&nbsp;&nbsp;{l s='Convert image to WebP' mod='webpgenerator'}
		</div>
		<div class="form-wrapper">
			<div class="form-group">
				<label class="control-label col-lg-3" for="regen-img-source-path">{l s='Source path' mod='webpgenerator'}</label>
				<div class="col-lg-9">
					<input id="regen-img-source-path" name="regen-img-source-path" type="text" placeholder="{l s='Source path' mod='webpgenerator'}"
						   class="form-control" required="">
					<p class="help-block">{l s='Add the image URL you want to generate in webp format' mod='webpgenerator'}<br> {l s='(Example: https://www.yoursitedomain.com/img/cms/cms-img.jpg)' mod='webpgenerator'}</p>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button id="custom-regenerate-submit-form" name="custom-regenerate-submit-form" class="btn btn-default pull-right">
				<i class="process-icon-refresh"></i>{l s='Regenerate' mod='webpgenerator'}
			</button>
		</div>
	</div>
</form>