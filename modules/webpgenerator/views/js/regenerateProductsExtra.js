/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */
function regenerateWebpProductsExtra(productImages) {
    for (var i = 0; i < productImages.length; i++) {
        $.ajax({
            type: "POST",
            url: ajaxUrl,
            data: {
                action: 'regenerate',
                image: productImages[i],
                currentIndex: productImages[i],
                type: 'product',
                ajax: true
            }, success: function (response) {
                if (response.success) {
                    toastr.success('Product' + ' image regenerated');
                } else {
                    toastr.error(response.error);
                }
            },
            error: function (error) {
                toastr.error('Product' + ' image regeneration failed');
            }
        });
    }
}