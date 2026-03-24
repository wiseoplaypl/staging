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
{$otherTypes = ['others','cms','theme', 'modules']}
{if isset($imagesToRegenerate)}
	<table class="table regeneration-table">
		<thead>
			<tr>
				<th class="image-type">{l s='Image type' mod='webpgenerator'}</th>
				<th class="image-actions">{l s='Actions' mod='webpgenerator'}</th>
				<th class="regenerate-progress">{l s='Progress' mod='webpgenerator'}</th>
				<th class="regenerate-progress-percentage">{l s='%' mod='webpgenerator'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$imagesToRegenerate key=key item=imgToRegenerate}
				{assign var="imageType" value=$imgToRegenerate.imageType|escape:'htmlall':'UTF-8'}
				<tr id="{$imageType}-imgs">
					<td class="image-type">{$imageType|escape:'htmlall':'UTF-8'}</td>
					<td class="image-actions">
						<div class="btn-group" role="group">
							<a href="#" class="btn btn-default img-start"
									{if in_array($imageType, $otherTypes)}
										onclick="loadOtherImages(false, '{$imageType}', function(){literal}{{/literal}return regenerate('{$imageType}'){literal}}{/literal})" title="{l s='Regenerate' mod='webpgenerator'}"
									{else}
										onclick="regenerate('{$imageType}')" title="{l s='Regenerate' mod='webpgenerator'}"
									{/if}
									{if $imgToRegenerate.imageCount == 0 && !in_array($imageType, $otherTypes)}disabled{/if}>
								<i class="icon icon-retweet" aria-hidden="true"></i>
							</a>
							<a href="#" class="btn btn-default img-pause"
							   {if $imgToRegenerate.imageCount == 0 && !in_array($imageType, $otherTypes)}disabled{/if}
							   onclick="resume('{$imageType}')" title="{l s='Resume regeneration' mod='webpgenerator'}">
								<i class="icon icon-play" aria-hidden="true"></i>
							</a>
							<a href="#" class="btn btn-default img-stop" onclick="stop('{$imageType}')" disabled=""
							   title="{l s='Stop regeneration' mod='webpgenerator'}">
								<i class="icon icon-pause"></i>
							</a>
							{if in_array($imageType, $otherTypes)}
								<a href="#" class="btn btn-default img-load-other-imgs" onclick="loadOtherImages(true,'{$imageType}')"
								   title="{l s='Load images' mod='webpgenerator'}">
									<i class="icon icon-download"></i> {l s='Load images' mod='webpgenerator'}
								</a>
							{/if}
						</div>
						{if in_array($imageType, $otherTypes)}
							<div id="{$imageType}-images-load" class="alert alert-warning alert-loading" style="display: none">
								{l s='Loading images, please wait...' mod='webpgenerator'}
							</div>
						{/if}
						{if $imageType == 'product'}
                            <a href="#" class="btn btn-default"
                               onclick="setRange()"
                               title="{l s='Specify a range of product images' mod='webpgenerator'}">
                                <i class="icon icon-arrows-h"></i>
                            </a>
						{/if}
					</td>
					<td class="regenerate-progress">
						<span class="img-completed">0</span>/<span class="img-total">{$imgToRegenerate.imageCount}</span>
					</td>
					<td class="regenerate-progress-percentage">
						<div class="progress">
							<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0">
								<span class="sr-percent">0</span>%
							</div>
						</div>
					</td>
				</tr>
			{/foreach}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" class="text-right">
					<button id="delete-all-webp-images" class="btn btn-danger" title="{l s='Delete all WebP images' mod='webpgenerator'}">
                        <i class="icon-trash"></i>
						{l s='Delete all WebP images' mod='webpgenerator'}
                    </button>
				</td>
			</tr>
		</tfoot>
	</table>
{/if}