/*
* AfterMail version 1.8.6
*
* @author    Shopmonauten <prestashop@shopmonauten.com>
* @copyright Shopmonauten <www.shopmonauten.com>
* @license   go to addons.prestashop.com (buy one module for one shop).
* @for PrestaShop version 1.6X
* @site www.shopmonauten.de
* @email prestashop@shopmonauten.com
*/
$(document).ready(function () {
	var voucherElement = getE('voucher');
	var strSeletcted = voucherElement.options[voucherElement.selectedIndex].value;

	if(strSeletcted==0) {
		$('#vouchertype').hide();
		$('#voucheramount').hide();
		$('#voucherdays').hide();
		$('#vouchername').hide();
		$('#vouchertype').parent().prev().hide();
		$('#voucheramount').parent().prev().hide();
		$('#voucherdays').parent().prev().hide();
		$('#vouchername').parent().prev().hide();

	} else {
		$('#vouchertype').show();
		$('#voucheramount').show();
		$('#voucherdays').show();
		$('#vouchername').show();
		$('#vouchertype').parent().prev().show();
		$('#voucheramount').parent().prev().show();
		$('#voucherdays').parent().prev().show();
		$('#vouchername').parent().prev().show();
	}
	showTriggerStates();
});