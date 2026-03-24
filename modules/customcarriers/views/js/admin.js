/**
 * admin.js
 * File generated with Prestashop generator by SzpaQ
 * @author SzpaQ
 * @module customcarrier (Custom Carrier)
 * @version 0.0.1
 * @license All Rights Reserved
  * */

var switchChange = function() {
    $('[name=filterfeature], [name=filterproduct], [name=filtercategory], [name=price] ').each(function(){
        if ($(this).is(':checked')) {
            if ($(this).val() == 0) {
                $(this).closest('.form-group').next('.form-group').hide();
            } else {
                $(this).closest('.form-group').next('.form-group').show();
            }
        }
    });
    $('[name=pricebyfeature] ').each(function(){
        if ($(this).is(':checked')) {
            if ($(this).val() == 0) {
                $(this).closest('.form-group').next('.form-group').hide().next('.form-group').hide();
            } else {
                $(this).closest('.form-group').next('.form-group').show().next('.form-group').show();
            }
        }
    });
}
window.addEventListener('load', function(){
    switchChange();
    $('body').on('click','.prestashop-switch', function(){
        setTimeout(function(){
            switchChange();
        }, 100);
    })
})


