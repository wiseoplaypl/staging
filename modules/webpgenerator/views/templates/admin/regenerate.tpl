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

<div class="panel">
	<div class="panel-heading">
        <i class="icon-refresh"></i>&nbsp;&nbsp;{l s='Regenarate WebP images' mod='webpgenerator'}
    </div>
	<div class="row">
		<div class="alert alert-info col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<p>
				<b>WebP</b> is a modern image format that provides superior <b>lossless and lossy</b> compression for images on
				the web. Using WebP, webmasters and web developers can create smaller, richer images that make the web faster.
			</p>
			<p>
				WebP lossless images are <a
					href="https://developers.google.com/speed/webp/docs/webp_lossless_alpha_study#results">26% smaller</a> in
				size compared to PNGs. WebP lossy images are <a href="https://developers.google.com/speed/webp/docs/webp_study">25-34%
					smaller</a> than comparable JPEG images at equivalent SSIM quality index.
			</p>
			<p>
				Lossless WebP <b>supports transparency</b> (also known as alpha channel) at a cost of just 22% additional bytes.
				For cases when lossy RGB compression is acceptable, <b>lossy WebP also supports transparency</b>, typically
				providing 3× smaller file sizes compared to PNG.
			</p>
		</div>
		
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<p>{l s='You can regenerate all your images safely.' mod='webpgenerator'}</p>
		
			{include file='./_image-section.tpl' imagesToRegenerate=$imagesToRegenerate}
		</div>

	</div>
</div>

<dialog id="custom-image-dialog" style="min-width: 50%; top: 25%;">
    <form class="form-horizontal" method="dialog">
        <fieldset>
            <legend>{l s='Convert image to WebP' mod='webpgenerator'}</legend>
            <div class="form-group">
                <label class="col-md-3 control-label" for="img-source-path">{l s='Source path' mod='webpgenerator'}</label>
                <div class="col-md-9">
                    <input id="img-source-path" name="img-source-path" type="text" placeholder="{l s='Source path' mod='webpgenerator'}"
                           class="form-control input-md" required="">
                    <span class="help-block">{l s='Add the image URL you want to generate in webp format' mod='webpgenerator'}<br> {l s='(Example: https://www.yoursitedomain.com/img/cms/cms-img.jpg)' mod='webpgenerator'}</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label" for="custom-regenerate-submit"></label>
                <div class="col-md-9">
                    <button id="custom-regenerate-submit" name="custom-regenerate-submit" class="btn btn-success">
                        {l s='Regenerate' mod='webpgenerator'}
                    </button>
                    <button id="custom-regenerate-cancel" name="custom-regenerate-cancel" class="btn btn-danger" type="button">
                        {l s='Cancel' mod='webpgenerator'}
                    </button>
                </div>
            </div>
        </fieldset>
    </form>
</dialog>