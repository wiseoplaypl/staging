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

class ModulesXPremiumXWidgetsXTemplate extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/87-widgets/premium-widgets/446-template-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'template';
    }

    public function getTitle()
    {
        return __('Template');
    }

    public function getIcon()
    {
        return 'eicon-document-file';
    }

    public function getCategories()
    {
        return ['premium'];
    }

    public function getKeywords()
    {
        return ['template', 'library', 'block', 'page', 'content', 'anywhere'];
    }

    protected function getTemplateOptions()
    {
        $options = [];
        $rows = \Db::getInstance()->executeS('
            SELECT `id_ce_template` AS id, `title`, `type` FROM ' . _DB_PREFIX_ . 'ce_template
            WHERE `active` = 1 AND `type` IN ("section", "page")
        ');
        if ($rows) {
            $types = [
                'section' => __('Section'),
                'page' => __('Page'),
            ];
            foreach ($rows as &$row) {
                $type = $types[$row['type']];
                $options[$row['id']] = "#$row[id] $row[title] ($type)";
            }
        }

        return $options;
    }

    protected function getContentOptions()
    {
        $options = [];
        $id_lang = $GLOBALS['language']->id;
        $id_shop = $GLOBALS['context']->shop->id;
        $rows = \Db::getInstance()->executeS('
            SELECT c.`id_ce_content` AS id, cl.`title`, c.`hook` FROM ' . _DB_PREFIX_ . 'ce_content c
            INNER JOIN ' . _DB_PREFIX_ . 'ce_content_lang cl ON cl.`id_ce_content` = c.`id_ce_content` AND cl.`id_lang` = ' . (int) $id_lang . ' AND cl.`id_shop` = ' . (int) $id_shop . '
            WHERE c.`active` = 1 AND c.`id_product` = 0
        ');
        if ($rows) {
            foreach ($rows as &$row) {
                $options[$row['id']] = "#$row[id] $row[title] ($row[hook])";
            }
        }

        return $options;
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_template',
            [
                'label' => __('Template'),
            ]
        );

        $this->addControl(
            'type',
            [
                'label' => __('Type'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'cms' => __('CMS'),
                    'content' => __('Content Anywhere'),
                    'template' => __('Saved Templates'),
                ],
                'default' => 'template',
            ]
        );

        $this->addControl(
            'template_id',
            [
                'show_label' => false,
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'placeholder' => __('Select...'),
                ],
                'options' => _CE_ADMIN_ ? $this->getTemplateOptions() : [],
                'condition' => [
                    'type' => 'template',
                ],
            ]
        );

        $this->addControl(
            'content_id',
            [
                'show_label' => false,
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'placeholder' => __('Select...'),
                ],
                'options' => _CE_ADMIN_ ? $this->getContentOptions() : [],
                'condition' => [
                    'type' => 'content',
                ],
            ]
        );

        $this->addControl(
            'cms_id',
            [
                'show_label' => false,
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'placeholder' => __('Search & Select'),
                    'ajax' => [
                        'get' => 'Cms',
                    ],
                ],
                'condition' => [
                    'type' => 'cms',
                ],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $id_key = $settings['type'] . '_id';

        if ('template_id' === $id_key) {
            $uid = new UId($settings[$id_key], UId::TEMPLATE);
        } elseif (!empty($settings[$id_key])) {
            $uid = new UId($settings[$id_key], 'cms' === $settings['type'] ? UId::CMS : UId::CONTENT, $GLOBALS['language']->id, $GLOBALS['context']->shop->id);
        }

        empty($uid->id) || print Plugin::$instance->frontend->getBuilderContentForDisplay($uid);
    }

    public function renderPlainContent()
    {
    }
}
