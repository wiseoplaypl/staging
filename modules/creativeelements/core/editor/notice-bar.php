<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2025 WebshopWorks.com & Elementor.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\CoreXBaseXBaseObject as BaseObject;
use CE\CoreXCommonXModulesXAjaxXModule as Ajax;

class CoreXEditorXNoticeBar extends BaseObject
{
    protected function getInitSettings()
    {
        $install_time = Plugin::$instance->getInstallTime();

        if ($install_time > strtotime('-30 days') || strtotime('+90 days', $install_time) < time()) {
            return [];
        }

        return [
            'muted_period' => 31,
            'option_key' => 'CE_EDITOR_RATE_NOTICE_DISMISSED',
            'review_url' => 'https://pagebuilder.webshopworks.com/rate-us',
            'feedback_url' => 'https://pagebuilder.webshopworks.com/feedback',
        ];
    }

    final public function getNotice()
    {
        if (!$GLOBALS['employee']->isSuperAdmin()) {
            return null;
        }

        $settings = $this->getSettings();

        if (empty($settings['option_key'])) {
            return null;
        }

        $dismissed_time = \ConfigurationKPI::getGlobalValue($settings['option_key']);

        if ($dismissed_time) {
            if ($dismissed_time > strtotime('-' . $settings['muted_period'] . ' days')) {
                return null;
            }

            // $this->setNoticeDismissed();
        }

        return $settings;
    }

    public function __construct()
    {
        add_action('elementor/ajax/register_actions', [$this, 'registerAjaxActions']);
    }

    public function setNoticeDismissed()
    {
        \ConfigurationKPI::updateGlobalValue($this->getSettings('option_key'), time());
    }

    public function setNoticeRated($request)
    {
        \ConfigurationKPI::updateGlobalValue($this->getSettings('option_key'), PHP_INT_MAX);

        Api::sendFeedback('rating', (int) $request['rating']);
    }

    public function registerAjaxActions(Ajax $ajax)
    {
        $ajax->registerAjaxAction('notice_bar_dismiss', [$this, 'setNoticeDismissed']);
        $ajax->registerAjaxAction('notice_bar_rate', [$this, 'setNoticeRated']);
    }
}
