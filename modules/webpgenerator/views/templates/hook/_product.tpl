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
    <div class="panel-body">
        <div class="btn-group" role="group">
            <a href="#" class="btn btn-primary img-start" onclick='regenerateWebpProductsExtra({$productImages}); return false;'>
                <i class="icon icon-retweet" aria-hidden="true"></i>
                {l s='Regenerate Webp images' mod='webpgenerator'}
            </a>
        </div>
        <div class="btn-group pull-right float-right" role="group">
            <a href="{$regenerateConfigLink}" class="btn">
                <i class="process-icon-configure" aria-hidden="true"></i>
                {l s='Webp module config' mod='webpgenerator'}
            </a>
        </div>
    </div>
</div>