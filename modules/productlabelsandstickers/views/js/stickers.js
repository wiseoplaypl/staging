/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   productlabelsandstickers
* @author    FMM Modules
* @copyright FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
$(document).ready(function () {
    function moveLabelBanner() {
    	if (typeof sticker_type === 'undefined' || sticker_type !== 1) {
            return; // Exit if sticker_type is not 1
        }
        // Select all banners on the page
        $('.fmm_lable_banner').each(function () {
            const $banner = $(this);
            const $productContainer = $banner.closest('.product-container, .quickview');
            if ($productContainer.length) {
                const $productPrice = $productContainer.find('.product-price').first();
                if ($productPrice.length) {
                    $banner.insertAfter($productPrice);
                }
            }
        });
    }

    // Initial move on page load
    moveLabelBanner();

    // Listen for AJAX completion to handle quick view
    $(document).ajaxComplete(function (event, xhr, settings) {
        if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('quickview_html') && xhr.responseJSON.quickview_html) {
            // Delay to ensure the DOM is updated
            setTimeout(function () {
                moveLabelBanner();
            }, 100);
        }
    });
    prestashop.on('updatedProduct', function () {
        setTimeout(function () {
                moveLabelBanner();
            }, 100);
    });
    prestashop.on('updateCart', function () {
        setTimeout(function () {
                moveLabelBanner();
            }, 100);
    });
});