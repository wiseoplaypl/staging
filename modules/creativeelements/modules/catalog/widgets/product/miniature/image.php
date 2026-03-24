<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\ModulesXCatalogXWidgetsXProductXImage as ProductImage;

class ModulesXCatalogXWidgetsXProductXMiniatureXImage extends ProductImage
{
    public function getName()
    {
        return 'product-miniature-image';
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl('link_to', [
            'type' => ControlsManager::HIDDEN,
            'default' => 'product',
        ]);
    }

    protected function shouldPrintEmpty()
    {
        return false;
    }

    protected function renderSmarty()
    {
        $settings = $this->getSettingsForDisplay();
        $index = (int) $settings['image_index'] - 1;

        echo $index < 0
            ? '{if $product.cover}{$image = $product.cover}'
            : "{if isset(\$product.images[$index])}{\$image = \$product.images[$index]}";
        echo $settings['show_no_image']
            ? '{else}{$image = $urls.no_picture_image}{/if}' : ''; ?>
        {$caption = <?php echo $settings['show_caption'] ? '$image.legend' : '""'; ?>}
        {$imageBySize = $image.bySize[<?php var_export($settings['image_size']); ?>]}
        {$imageUrl = (isset($imageBySize.sources.webp)) ? $imageBySize.sources.webp : $imageBySize.url}
        {$ratio = round($imageBySize.width / $imageBySize.height, 3)}
        {$srcset = ["$imageUrl {$imageBySize.width}w"]}
        {foreach $image.bySize as $size => $img}
            {if <?php var_export($settings['image_size']); ?> !== $size && round($img.width / $img.height, 3) === $ratio}
                {$srcset[] = "{(isset($img.sources.webp)) ? $img.sources.webp : $img.url} {$img.width}w"}
            {/if}
        {/foreach}
        <div class="ce-product-image elementor-image">
        {if $caption}
            <figure class="ce-caption">
        {/if}
            <a href="{$product.url}">
                <img src="{$imageUrl}" srcset="{implode(', ', $srcset)}" alt="{$image.legend|default:$product.name}"{if empty($fetchpriority)} loading="lazy"{else} fetchpriority="high"{/if}
                <?php if ($settings['hover_animation']) { ?>
                    class="elementor-animation-<?php escape($settings['hover_animation']); ?>"
                <?php } ?>
                    width="{$imageBySize.width}" height="{$imageBySize.height}"
                    sizes="(max-width: {$imageBySize.width}px) 100vw, {$imageBySize.width}px">
            </a>
        {if $caption}
            <figcaption class="widget-image-caption ce-caption-text">{$caption|escape}</figcaption>
            </figure>
        {/if}
        </div>
        <?php
        $settings['show_no_image'] || print '{/if}';
    }
}
