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

$body_classes = [
    'elementor-editor-active',
    'ps-version-' . str_replace('.', '-', _PS_VERSION_),
];

if (is_rtl()) {
    $body_classes[] = 'rtl';
}

// if (!Plugin::$instance->role_manager->userCan('design')) {
//     $body_classes[] = 'elementor-editor-content-only';
// }

$favicon = _PS_IMG_ . \Configuration::get('PS_FAVICON') . '?' . \Configuration::get('PS_IMG_UPDATE_TIME');
$notice = Plugin::$instance->editor->notice_bar->getNotice();

ob_start();
?><!DOCTYPE html>
<html lang="<?php echo get_locale(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php if (\Tools::usingSecureMode()) { ?>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
<?php } ?>
    <title><?php _e('Creative Elements - live Theme & Page Builder'); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php escape($favicon); ?>">
    <?php do_action('wp_head'); ?>
    <script>
    var _PS_VERSION_ = <?php echo json_encode(_PS_VERSION_); ?>;
    var ajaxurl = <?php echo json_encode(Helper::getAjaxLink()); ?>;
    </script>
</head>
<body class="<?php echo implode(' ', $body_classes); ?>">
    <div id="elementor-editor-wrapper">
        <div id="elementor-panel" class="elementor-panel"></div>
        <div id="elementor-preview">
            <div id="elementor-loading">
                <div class="elementor-loader-wrapper">
                    <div class="elementor-loader">
                        <div class="elementor-loader-boxes">
                            <div class="elementor-loader-box"></div>
                            <div class="elementor-loader-box"></div>
                            <div class="elementor-loader-box"></div>
                            <div class="elementor-loader-box"></div>
                        </div>
                    </div>
                    <div class="elementor-loading-title"><?php _e('Loading'); ?></div>
                </div>
            </div>
            <div id="elementor-preview-responsive-wrapper" class="elementor-device-desktop elementor-device-rotate-portrait">
                <div id="elementor-preview-loading">
                    <i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
                </div>
            <?php if ($notice) { ?>
                <div id="elementor-notice-bar">
                    <img src="<?php escape(_CE_URL_ . 'logo.png'); ?>" alt="logo" width="26" height="26" style="border-radius: 6px;">
                    <div id="elementor-notice-bar__message">
                        <div id="elementor-notice-bar__rate">
                            <span class="ce-valign-fix"><?php _e('Love using Creative Elements?'); ?></span>
                            <span id="elementor-notice-bar__stars">
                                <a title="1/5"><i class="eicon-star-o"></i><i class="eicon-star"></i></a>
                                <a title="2/5"><i class="eicon-star-o"></i><i class="eicon-star"></i></a>
                                <a title="3/5"><i class="eicon-star-o"></i><i class="eicon-star"></i></a>
                                <a title="4/5"><i class="eicon-star-o"></i><i class="eicon-star"></i></a>
                                <a title="5/5"><i class="eicon-star-o"></i><i class="eicon-star"></i></a>
                                <span id="elementor-notice-bar__emoji"></span>
                            </span>
                        </div>
                        <div id="elementor-notice-bar__review" style="opacity: 0; transform: translateY(200%);">
                            <span class="ce-valign-fix"><?php _e('Awesome! Would you mind taking a moment to share your experience by leaving a review on PrestaShop Addons?'); ?></span>
                            <a href="<?php escape($notice['review_url']); ?>" target="_blank" class="ce-valign-fix">
                                <i class="eicon-editor-external-link"></i><?php _e('Yes, take me there!'); ?>
                            </a>
                        </div>
                        <div id="elementor-notice-bar__feedback" style="opacity: 0; transform: translateY(200%);">
                            <span class="ce-valign-fix"><?php _e('We’re sorry to hear you’re not completely satisfied. Could you tell us what we can do to improve Creative Elements?'); ?></span>
                            <a href="<?php escape($notice['feedback_url']); ?>" target="_blank" class="ce-valign-fix">
                                <i class="eicon-editor-external-link"></i><?php _e('Send Feedback'); ?>
                            </a>
                        </div>
                    </div>
                    <a id="elementor-notice-bar__close">
                        <i class="eicon-close"></i> <span class="ce-valign-fix"><?php _e('Not Now'); ?></span>
                    </a>
                </div>
            <?php } ?>
            </div>
        </div>
        <div id="elementor-navigator"></div>
    </div>
    <?php do_action('wp_footer'); ?>
    <?php do_action('admin_print_footer_scripts'); ?>
</body>
</html>
<?php
ob_flush();
