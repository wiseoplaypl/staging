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

use CE\ModulesXCatalogXWidgetsXCategoryXList as CategoryList;
use CE\ModulesXCmsXControlsXSelectCategory as SelectCMSCategory;

class ModulesXCmsXWidgetsXCategoryList extends CategoryList
{
    const HELP_URL = '';

    public function getName()
    {
        return 'cms-category-list';
    }

    public function getTitle()
    {
        return __('Subcategories');
    }

    public function getIcon()
    {
        return 'eicon-sitemap';
    }

    public function getCategories()
    {
        return ['cms-elements'];
    }

    public function getKeywords()
    {
        return ['cms', 'category', 'listing', 'buttons', 'subcategories'];
    }

    protected function registerControls()
    {
        parent::registerControls();

        $this->addControl(
            'id_cms_category',
            [
                'label' => __('Category Root'),
                'label_block' => true,
                'type' => SelectCMSCategory::CONTROL_TYPE,
                'select2options' => [
                    'allowClear' => false,
                ],
                'options' => [
                    '0' => __('Current Category') . ' / ' . __('Default'),
                ],
                'default' => 0,
                'separator' => 'before',
            ],
            [
                'position' => [
                    'of' => 'id_category',
                ],
            ]
        );
        $this->removeControl('id_category');
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-heading';
    }

    protected function render()
    {
        $context = $GLOBALS['context'];
        $settings = $this->getSettingsForDisplay();

        if (!$settings['id_cms_category'] && isset($context->smarty->tpl_vars['sub_categories'])) {
            $categories = &$context->smarty->tpl_vars['sub_categories']->value;
        } else {
            $root = new \CMSCategory();
            $root->id = $settings['id_cms_category'] ?: 1;
            $categories = $root->getSubCategories($context->language->id);

            foreach ($categories as &$category) {
                $category['link'] = Helper::$link->getCMSCategoryLink($category['id_cms_category'], $category['link_rewrite'], $context->language->id, $context->shop->id);
            }
        }

        if (!$categories) {
            return;
        }
        $this->addRenderAttribute('_container', 'role', 'navigation');

        if ('' !== $settings['title_text']) {
            $title_tag = Utils::validateHtmlTag($settings['title_tag']);
            $this->addRenderAttribute('title_text', [
                'class' => 'elementor-heading-title',
                'id' => $title_id = 'ce-category-list__title-' . $this->getId(),
            ]);
            $this->addInlineEditingAttributes('title_text');
            $settings['title_display'] && $this->addRenderAttribute('title_text', 'class', 'ce-display-' . $settings['title_display']);

            echo "<$title_tag {$this->getRenderAttributeString('title_text')}>$settings[title_text]</$title_tag>";

            $this->addRenderAttribute('_container', 'aria-labelledby', $title_id);
        } else {
            $this->addRenderAttribute('_container', 'aria-label', __('Categories', 'Shop.Theme.Catalog'));
        }

        if ('icon-list' === $settings['skin']) {
            $this->addRenderAttribute('_container', [
                'role' => 'navigation',
                'aria-label' => __('Categories', 'Shop.Theme.Catalog'),
            ]);
            $this->addRenderAttribute('icon_list', 'class', 'elementor-icon-list-items');
            $this->addRenderAttribute('list_item', 'class', 'elementor-icon-list-item');

            if ('inline' === $settings['view']) {
                $this->addRenderAttribute('icon_list', 'class', 'elementor-inline-items');
                $this->addRenderAttribute('list_item', 'class', 'elementor-inline-item');
            }
            $icon = !empty($settings['list_icon']['value'])
                && ob_start()
                && IconsManager::renderIcon($settings['list_icon'], ['class' => 'elementor-icon-list-icon', 'aria-hidden' => 'true'])
                ? ob_get_clean() : '';
            echo "<ul {$this->getRenderAttributeString('icon_list')}>";
            foreach ($categories as &$category) { ?>
                <li class="elementor-icon-list-item">
                    <a href="<?php escape($category['link']); ?>">
                        <?php echo $icon; ?>
                        <span class="elementor-icon-list-text"><?php echo $category['name']; ?></span>
                    </a>
                </li><?php
            }
            echo '</ul>';
        } elseif ('button' === $settings['skin']) {
            echo '<div class="ce-category-list ce-scrollbar-x--auto" role="list">';
            foreach ($categories as &$category) { ?>
                <div class="elementor-button-wrapper" role="listitem">
                    <a href="<?php escape($category['link']); ?>" class="elementor-button elementor-size-<?php escape($settings['button_size']); ?>">
                        <span class="elementor-button-content-wrapper">
                            <span class="elementor-button-text"><?php echo $category['name']; ?></span>
                        </span>
                    </a>
                </div><?php
            }
            echo '</div>';
        }
    }
}
