/**
 * 2007-2024 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2024 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function(e) {

	$('#product').attr('data-qty', $('input[name=qty]').val());
	//$('section.product-accessories').hide();

  //Uncomment below to show accessories under variants. Put display none on the main Div in tpl
  //$("div.product-variants").after($("div.multi-accessories"));
  //$("div.multi-accessories").show();

	$('input[name=qty]').change(function(e) {
        $('#product').attr('data-qty',this.value);
    });

	prestashop.on(
      'updateCart',
      function (event) {

		var static_token = $('input[name=token]').val();
		if(event.reason.linkAction=='add-to-cart'){

			$("input.accessories_checkbox").each(function(index, element) {
				if(this.checked){

				 var id_product_attribute = $(this).parents('table:first').find('#acc_product_'+this.value).val();
				 if (typeof id_product_attribute === "undefined")
				    var id_product_attribute = 0;
				 var accessory_qty = $('#product').attr('data-qty');

					$.ajax({
							type: 'POST',
							headers: { "cache-control": "no-cache" },
							url: prestashop.urls.pages.cart,
							async: false,
							cache: false,
							dataType : "json",
							data: 'action=add-to-cart&add=1&ajax=true&qty='+ accessory_qty + '&id_product=' + this.value + '&id_product_attribute=' + id_product_attribute + '&token=' + static_token,
							success: function(jsonData,textStatus,jqXHR)
							{
								//ajaxCart.updateCartInformation(jsonData, true);
							}
				    });
				}
		  });
		}
      });
});
