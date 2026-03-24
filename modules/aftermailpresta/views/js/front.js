/**
* AfterMail version 1.9.0
*
* @author    Shopmonauten <prestashop@shopmonauten.com>
* @copyright Shopmonauten <www.shopmonauten.com>
* @license   go to addons.prestashop.com (buy one module for one shop).
* @for PrestaShop version 1.6X
* @site www.shopmonauten.de
* @email prestashop@shopmonauten.com
*/

$(document).ready(function () {

	$( "#subscribebutton" ).click(function() {
		var frequency = $("#subscribe_product").val();
		var id_conf = $("#id_conf").val();
		// save the expand statut in the user cookie
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseDir + 'modules/aftermailpresta/aftermailajax.php' + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'action=subscribe&id_product='+id_product+'&frequency='+frequency+'&id_conf='+id_conf+'&id_attribute=',
			success: function(jsonData,textStatus,jqXHR)
			{
				console.log(jsonData,textStatus,jqXHR);
				location.reload();
			},
			error: function(params)
			{
				console.log("could not subscribe: "+error);
			}
		});
	});
	$( "#unsubscribebutton" ).click(function() {
		var token = $("#unsub_token").val();
		var id_conf = $("#id_conf").val();
		// save the expand statut in the user cookie
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseDir + 'modules/aftermailpresta/aftermailajax.php' + '?rand=' + new Date().getTime(),
			async: true,
			cache: false,
			data: 'action=unsubscribe&id_product='+id_product+'&token='+token+'&id_conf='+id_conf+'&id_attribute=',
			success: function(jsonData,textStatus,jqXHR)
			{
				console.log(jsonData,textStatus,jqXHR);
				location.reload();
			},
			error: function(params)
			{
				console.log("could not unsubscribe: "+error);
			}
		});
	});
});
