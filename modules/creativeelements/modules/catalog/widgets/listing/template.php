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

class ModulesXCatalogXWidgetsXListingXTemplate extends WidgetBase
{
    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/107-widgets/listing-widgets/433-listing-template-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'listing-template';
    }

    public function getTitle()
    {
        return __('Listing Template');
    }

    public function getIcon()
    {
        return 'eicon-document-file';
    }

    public function getCategories()
    {
        return ['listing-elements'];
    }

    public function getKeywords()
    {
        return ['listing', 'template', 'theme'];
    }

    protected function getListingOptions()
    {
        $options = [];
        $id_lang = $GLOBALS['language']->id;
        $id_shop = $GLOBALS['context']->shop->id;
        $rows = \Db::getInstance()->executeS('
            SELECT t.`id_ce_theme` AS id, tl.`title` FROM ' . _DB_PREFIX_ . 'ce_theme t
            INNER JOIN ' . _DB_PREFIX_ . 'ce_theme_lang tl
                ON tl.`id_ce_theme` = t.`id_ce_theme` AND tl.`id_lang` = ' . (int) $id_lang . ' AND tl.`id_shop` = ' . (int) $id_shop . '
            WHERE t.`active` = 1 AND t.`type` = "listing-page"
        ');
        if ($rows) {
            $type = __('Listing Page');

            foreach ($rows as &$row) {
                $options[$row['id']] = "#$row[id] $row[title] ($type)";
            }
        }

        return $options;
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_template',
            [
                'label' => __('Listing Template'),
            ]
        );

        $this->addControl(
            'theme_id',
            [
                'show_label' => false,
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'select2options' => [
                    'placeholder' => __('Select...'),
                ],
                'options' => $GLOBALS['context']->controller instanceof \AdminCEEditorController ? $this->getListingOptions() : [],
            ]
        );

        $this->endControlsSection();
    }

    protected function render()
    {
        if ($id = $this->getSettings('theme_id')) {
            $uid = new UId($id, UId::THEME, $GLOBALS['language']->id, $GLOBALS['context']->shop->id);

            echo Plugin::$instance->frontend->getBuilderContentForDisplay($uid);
        }
    }

    public function renderPlainContent()
    {
    }
}
