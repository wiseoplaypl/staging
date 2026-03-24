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

use CE\CoreXDynamicTagsXDataTag as DataTag;
use CE\ModulesXDynamicTagsXModule as Module;

class ModulesXDynamicTagsXTagsXScroll extends DataTag
{
    public function getName()
    {
        return 'scroll';
    }

    public function getTitle()
    {
        return __('Scroll');
    }

    public function getGroup()
    {
        return Module::ACTION_GROUP;
    }

    public function getCategories()
    {
        return [Module::URL_CATEGORY];
    }

    public function getPanelTemplateSettingKey()
    {
        return 'target';
    }

    protected function registerControls()
    {
        $this->addControl(
            'target',
            [
                'show_label' => false,
                'label_block' => true,
                'type' => ControlsManager::SELECT,
                'options' => [
                    'page' => __('Page to Top'),
                    'container' => __('Scrollable Element'),
                ],
                'default' => 'page',
            ]
        );

        $this->addControl(
            'action',
            [
                'label' => __('Action'),
                'type' => ControlsManager::SELECT,
                'options' => [
                    'prev' => __('Previous', 'Shop.Theme.Global'),
                    'next' => __('Next', 'Shop.Theme.Global'),
                ],
                'default' => 'next',
                'condition' => [
                    'target' => 'container',
                ],
            ]
        );
    }

    protected function registerAdvancedSection()
    {
        $this->startControlsSection(
            'advanced',
            [
                'label' => __('Advanced'),
            ]
        );

        $this->addControl(
            'selector',
            [
                'label' => __('Selector'),
                'type' => ControlsManager::TEXT,
                'label_block' => true,
                'placeholder' => __('e.g. #primary / .wrapper / main etc'),
            ]
        );

        $this->endControlsSection();
    }

    public function getValue(array $options = [])
    {
        return Plugin::$instance->frontend->createActionHash('scroll', $this->getSettings());
    }
}
