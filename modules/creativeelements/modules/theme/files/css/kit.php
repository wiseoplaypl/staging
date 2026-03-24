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

use CE\CoreXFilesXCSSXBase as Base;
use CE\CoreXFilesXCSSXPost as Post;
use CE\CoreXSettingsXManager as SettingsManager;

class ModulesXThemeXFilesXCSSXKit extends Post
{
    private $kit_id;

    public function __construct($uid)
    {
        $this->post_id = $uid;
        $this->kit_id = substr($uid, 0, -6);

        Base::__construct(static::FILE_PREFIX . "kit-{$this->kit_id}.css");
    }

    public function getName()
    {
        return 'kit';
    }

    protected function getFileHandleId()
    {
        return 'ce-kit-' . $this->kit_id;
    }

    protected function renderCss()
    {
        $model = SettingsManager::getSettingsManagers('page')->getModel($this->post_id);

        $this->addControlsStackStyleRules(
            $model,
            $model->getStyleControls(),
            $model->getSettings(),
            ['{{WRAPPER}}'],
            [$model->getCssWrapperSelector()]
        );
    }

    public function enqueue()
    {
        Base::enqueue();
    }
}
