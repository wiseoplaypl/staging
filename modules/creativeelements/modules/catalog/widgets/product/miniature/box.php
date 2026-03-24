<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\ModulesXCatalogXWidgetsXProductXBox as ProductBox;

class ModulesXCatalogXWidgetsXProductXMiniatureXBox extends ProductBox
{
    const REMOTE_RENDER = true;

    const SELECTOR = '{{WRAPPER}} .elementor-product-miniature';

    const SELECTOR_HOVER = '{{WRAPPER}} .elementor-product-miniature:hover';

    public function getName()
    {
        return 'product-miniature-box';
    }

    public function getTitle()
    {
        return __('Product Box');
    }

    public function getIcon()
    {
        return 'eicon-info-box';
    }

    public function getCategories()
    {
        return ['product-elements'];
    }

    public function getKeywords()
    {
        return ['shop', 'store', 'product', 'box'];
    }

    protected function getDefaultProductId()
    {
        return '';
    }

    public static function getSkinOptions()
    {
        return [];
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->updateControl(
            'skin',
            [
                'type' => ControlsManager::HIDDEN,
                'default' => 'custom',
            ]
        );

        $this->updateControl(
            'product_id',
            [
                'type' => ControlsManager::SELECT,
                'options' => [
                    '' => __('Current Product'),
                ],
                'separator' => '',
            ]
        );

        $this->removeControl('description_length');

        $this->updateControl('title_color', ['scheme' => '']);
        $this->updateControl('category_color', ['scheme' => '']);
        $this->updateControl('description_color', ['scheme' => '']);

        $this->updateControl('title_typography_font_family', ['scheme' => '']);
        $this->updateControl('title_typography_font_weight', ['scheme' => '']);

        $this->updateControl('category_typography_font_family', ['scheme' => '']);
        $this->updateControl('category_typography_font_weight', ['scheme' => '']);

        $this->updateControl('description_typography_font_family', ['scheme' => '']);
        $this->updateControl('description_typography_font_weight', ['scheme' => '']);
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-product-box';
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $product = $GLOBALS['smarty']->tpl_vars['product']->value;

        echo $this->fetchMiniature($settings, $product);
    }

    protected function renderSmarty()
    {
        $flags = self::FLAGS;
        $id = '{$product.id}-' . $this->getId();
        $settings = $this->getSettingsForDisplay();
        // BC fix
        $settings['qv_icon_align'] = $this->getSettings('qv_icon_align');
        $settings['atc_icon_align'] = $this->getSettings('atc_icon_align');

        $title_class = 'elementor-title' . ($settings['title_display'] ? ' ce-display-' . $settings['title_display'] : '');
        $size = $settings['image_size'] ?: $this->imageSize;
        $size_tablet = $settings['image_size_tablet']
            && $settings['image_size_tablet'] !== $size ? $settings['image_size_tablet'] : '';
        $size_mobile = $settings['image_size_mobile']
            && $settings['image_size_mobile'] !== $size_tablet ? $settings['image_size_mobile'] : '';

        $this->setRenderAttribute('cover', [
            'src' => "{(\$webp) ? \$cover.bySize.$size.sources.webp : \$cover.bySize.$size.url}",
            'alt' => '{$cover.legend|default:$product.name}',
            'width' => "{\$cover.bySize.$size.width}",
            'height' => "{\$cover.bySize.$size.height}",
        ]);
        $settings['show_second_image'] && $this->setRenderAttribute('image', [
            'src' => "{(\$webp) ? \$image.bySize.$size.sources.webp : \$image.bySize.$size.url}",
            'alt' => '{$image.legend}',
            'width' => "{\$image.bySize.$size.width}",
            'height' => "{\$image.bySize.$size.height}",
        ]); ?>
        {$cover = ($product.cover) ? $product.cover : $urls.no_picture_image}
        {$webp = isset($cover.bySize.<?php escape($size); ?>.sources.webp)}
        <div class="elementor-product-miniature">
            <a class="elementor-product-link" href="{$product.url}" aria-label="{$product.name}{if $product.show_price}: {$product.price}{/if}" aria-describedby="elementor-description-<?php escape($id); ?> elementor-category-<?php escape($id); ?>"></a>
            <div class="elementor-image">
                <picture class="elementor-cover-image">
                <?php if ($size_mobile) { ?>
                    <source media="(max-width: 767px)" srcset="<?php escape("{(\$webp) ? \$cover.bySize.$size_mobile.sources.webp : \$cover.bySize.$size_mobile.url}"); ?>">
                <?php } ?>
                <?php if ($size_tablet) { ?>
                    <source media="(max-width: 991px)" srcset="<?php escape("{(\$webp) ? \$cover.bySize.$size_tablet.sources.webp : \$cover.bySize.$size_tablet.url}"); ?>">
                <?php } ?>
                    <img <?php $this->printRenderAttributeString('cover'); ?>{if empty($fetchpriority)} loading="lazy"{else} fetchpriority="high"{/if}>
                </picture>
        <?php if ($settings['show_second_image']) { ?>
            {$index = (!empty($cover.position) && 1 < $cover.position) ? 0 : 1}
            {if !empty($product.images[$index])}
                {$image = $product.images[$index]}
                <picture class="elementor-second-image">
                <?php if ($size_mobile) { ?>
                    <source media="(max-width: 767px)" srcset="<?php escape("{(\$webp) ? \$image.bySize.$size_mobile.sources.webp : \$image.bySize.$size_mobile.url}"); ?>">
                <?php } ?>
                <?php if ($size_tablet) { ?>
                    <source media="(max-width: 991px)" srcset="<?php escape("{(\$webp) ? \$image.bySize.$size_tablet.sources.webp : \$image.bySize.$size_tablet.url}"); ?>">
                <?php } ?>
                    <img <?php $this->printRenderAttributeString('image'); ?> loading="lazy" fetchpriority="low">
                </picture>
            {/if}
        <?php } ?>
            <?php if ($settings['show_qv']) { ?>
                <a href="#ce-action=quickview" class="elementor-button elementor-quick-view" role="button">
                    <span class="elementor-button-content-wrapper">
                    <?php if ($qv_icon = IconsManager::getBcIcon($settings, 'qv_icon')) { ?>
                        <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['qv_icon_align']); ?>"><?php echo $qv_icon; ?></span>
                    <?php } ?>
                        <span class="elementor-button-text"><?php echo $settings['qv_text']; ?></span>
                    </span>
                </a>
            <?php } ?>
            </div>
        <?php foreach (['left', 'right'] as $position) { ?>
            <div class="elementor-badges-<?php escape($position); ?>">
            <?php foreach ($settings['show_badges'] as $badge) { ?>
                <?php if ($position === $settings['badge_' . $badge . '_position']) { ?>
                    {if !empty($product.flags[<?php var_export($flags[$badge]); ?>])}
                        <div class="elementor-badge elementor-badge-<?php escape($badge); ?>">
                            <?php echo $settings['badge_' . $badge . '_text'] ?: '{$product.flags[' . var_export($flags[$badge], true) . '].label}'; ?>
                        </div>
                    {/if}
                <?php } ?>
            <?php } ?>
            </div>
        <?php } ?>
            <div class="elementor-content">
            <?php if ($settings['show_category']) { ?>
                <div class="elementor-category" id="elementor-category-<?php escape($id); ?>" aria-label="{l s='Category: %category_name%' d='Shop.Theme.Catalog' sprintf=['%category_name%' => $product.category_name]}">{$product.category_name}</div>
            <?php } ?>
                <<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?> class="<?php escape($title_class); ?>">{$product.name}</<?php Utils::printValidatedHtmlTag($settings['title_tag']); ?>>
            <?php if ($settings['show_description']) { ?>
                <div class="elementor-description" id="elementor-description-<?php escape($id); ?>">{$product.description_short|strip_tags:0 nofilter}</div>
            <?php } ?>
            {if $product.show_price}
                <div class="elementor-price-wrapper">
            <?php if ($settings['show_regular_price']) { ?>
                {if $product.has_discount}
                    <del class="elementor-price-regular">{$product.regular_price}</del>
                {/if}
            <?php } ?>
                    <span class="elementor-price">{$product.price}</span>
                </div>
            {/if}
            </div>
        <?php if ($settings['show_atc']) { ?>
            {if !$configuration.is_catalog && $product.available_for_order}
                {if $product.add_to_cart_url}
                    {$atc_url = $product.add_to_cart_url}
                {else}
                    {$atc_url = {url entity='cart' ssl=true params=[
                        'add' => 1,
                        'id_product' => $product.id_product|intval,
                        'ipa' => $product.id_product_attribute|intval,
                        'token' => Tools::getToken(false)
                    ]}}
                {/if}
                <form class="elementor-atc" action="{$atc_url|escape}">
                    <input type="hidden" name="qty" value="{max(1, (!empty($product.product_attribute_minimal_quantity)) ? $product.product_attribute_minimal_quantity : $product.minimal_quantity)}">
                    <button type="submit" class="elementor-button elementor-size-<?php escape($settings['atc_size']); ?>"
                        data-button-action="add-to-cart" {if isset($product.flags.out_of_stock)}disabled{/if}>
                        <span class="elementor-button-content-wrapper">
                        <?php if ($atc_icon = IconsManager::getBcIcon($settings, 'atc_icon')) { ?>
                            <span class="elementor-button-icon elementor-align-icon-<?php escape($settings['atc_icon_align']); ?>" aria-hidden="true"><?php echo $atc_icon; ?></span>
                        <?php } ?>
                            <span class="elementor-button-text"><?php echo $settings['atc_text']; ?></span>
                        </span>
                    </button>
                </form>
            {/if}
        <?php } ?>
        </div>
        <?php
    }
}
