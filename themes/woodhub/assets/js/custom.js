/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/*
 * Custom code goes here.
 * A template should always ship with an empty custom.js
 */
window.addEventListener('load', function(){
    $('body').on('click', '.promo-banner button', function(){
        var $banner = $(this).closest('.promo-banner');
        var $link = $banner.find('a');

        if ($link.length) {
            var clickUrl = $link.attr('href');
            var domain = window.location.hostname; // Pobiera domenę aktualnej strony

            // Jeśli clickUrl nie zawiera domeny, doklej domenę
            if (!clickUrl.includes(domain)) {
                clickUrl = 'https://' + domain + clickUrl;
            }

            console.log(clickUrl);
            window.location.href = clickUrl;
        } else {
            console.log('Link nie został znaleziony.');
        }
    });
    prestashop.emit('updateProduct', {eventType:'ok'})
})


$('body').on('click', '.product-cover', function(){
    $(this).find('.zoom-in').trigger('click');
})
