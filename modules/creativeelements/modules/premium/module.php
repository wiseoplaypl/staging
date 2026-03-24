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

use CE\CoreXBaseXModule as BaseModule;
use CE\CoreXResponsiveXResponsive as Responsive;

class ModulesXPremiumXModule extends BaseModule
{
    public function getName()
    {
        return 'premium';
    }

    public static function addCaptchaPromoControls(WidgetBase $widget)
    {
        _CE_ADMIN_ && !\Tools::file_exists_cache(_PS_MODULE_DIR_ . 'invrecaptcha/invrecaptcha.php') && $widget->addControl(
            'captcha',
            [
                'type' => ControlsManager::RAW_HTML,
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'raw' => '
                    <i class="eicon-close" style="position:absolute; top:8px; right:8px; cursor:pointer" onclick="$(`.elementor-control-show_captcha input`).prop(`checked`, false).change()"></i>
                    Protect your site against spam and abuse, while letting your real customers pass through with ease.
                    <a href="https://addons.prestashop.com/website-security-access/32222-spam-protection-invisible-recaptcha.html" target="_blank">
                        <i class="eicon-link"></i>Invisible reCAPTCHA
                    </a> does this all in the background without any user interaction.
                    <style>.elementor-control-captcha:not(.elementor-hidden-control) ~ .elementor-control-show_captcha { display: none }</style>
                ',
                'condition' => [
                    'show_captcha!' => '',
                ],
            ]
        ) && $widget->addControl(
            'show_captcha',
            [
                'label' => 'Spam Protection - Invisible reCaptcha',
                'type' => ControlsManager::CHOOSE,
                'options' => [
                    'yes' => [
                        'title' => __('Learn More'),
                        'icon' => 'eicon-info-circle',
                    ],
                ],
                'default' => 'yes',
                'render_type' => 'none',
            ]
        );
    }

    public function registerDocuments($documents)
    {
        $documents->registerDocumentType('content', 'CE\ModulesXPremiumXDocumentsXContent');
    }

    public function registerWidgets($widgets_manager)
    {
        foreach ([
            'AnimatedHeadline',
            'Articles',
            'CallToAction',
            'LayerSlider',
            'FlipBox',
            'ImageHotspot',
            'ContactForm',
            'EmailSubscription',
            'TestimonialCarousel',
            'Countdown',
            'TrustedshopsReviews',
            'ImageSlider',
            'FacebookPage',
            'FacebookButton',
            'Template',
            'Module',
        ] as $class_suffix) {
            $widget_class = 'CE\ModulesXPremiumXWidgetsX' . $class_suffix;

            $widgets_manager->registerWidgetType(new $widget_class());
        }
    }

    public function registerStyles()
    {
        $has_custom_breakpoints = Responsive::hasCustomBreakpoints();
        $min = _PS_MODE_DEV_ ? '' : '.min';

        foreach ([
            'animated-headline',
            'countdown',
            'flip-box',
            'image-hotspot',
            'trustedshops-reviews',
        ] as $widget_name) {
            wp_register_style(
                "widget-$widget_name",
                _CE_ASSETS_URL_ . "css/widget-$widget_name$min.css",
                ['elementor-frontend'],
                _CE_VERSION_
            );
        }

        // Widgets with responsive styles
        foreach ([
            'articles',
            'call-to-action',
            'email-subscription',
            'form',
        ] as $widget_name) {
            wp_register_style(
                "widget-$widget_name",
                $this->getFrontendFileUrl("widget-$widget_name$min.css", $has_custom_breakpoints),
                ['elementor-frontend'],
                $has_custom_breakpoints ? null : _CE_VERSION_
            );
        }
    }

    public function __construct()
    {
        add_action('elementor/documents/register', [$this, 'registerDocuments']);
        add_action('elementor/widgets/widgets_registered', [$this, 'registerWidgets']);
        add_action('elementor/frontend/after_register_styles', [$this, 'registerStyles']);
    }
}
