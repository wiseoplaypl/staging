/**
 * 2007-2021 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */
 
$(document).ready(function() {
	
	
    if (typeof stripe_source != "undefined" && stripe_source != ""
        && typeof stripe_client_secret != "undefined" && stripe_client_secret != "") {
        if (StripePubKey) {
			var stripe = Stripe(StripePubKey);
        }
		pollForSourceStatus();
	}
	
	var source_chargeable = false;
	var MAX_POLL_COUNT = 60;
	var pollCount = 0;
	
	function pollForSourceStatus() {

	  stripe.retrieveSource({id: stripe_source, client_secret: stripe_client_secret}).then(function(result) {
		
		if (result.error){
		   alert(result.error.message);
		   location.replace(order_page);
		} else {
		    var source = result.source;
		    if (source.redirect.status === 'pending' && stripe_source_type != "multibanco")
			    location.replace(order_page);
			else if (source.status === 'chargeable') {
			  source_chargeable = true;
			  createCharge(source);
			} else if (source.status === 'pending' && pollCount < MAX_POLL_COUNT) {
			  // Try again in a second, if the Source is still `pending`:
			  pollCount += 1;
			  setTimeout(pollForSourceStatus, 1000);
			}  else if (source.status == "failed" || source.status == "canceled") {
				location.replace(order_page);
			} else if (source.status == "consumed" && !source_chargeable) {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: ajax_payment,
					data: {
						stripeToken: source.id,
						checkOrder: true,
						cart_id: source.metadata.cart_id,
						ajax: true,
					},
					success: function(data) {
						if (data.code == '1') {
							location.replace(data.url);
						}else{
							location.replace(order_page);
						}
					},
					error: function(err) {
						alert(err);
					}
				});
			 } else {
			  // Depending on the Source status, show your customer the relevant message.
			  if (source.status === 'pending')
			    alert(timed_out_err);
			  else 
			    alert(unknown_err);
			  location.replace(order_page);
			}
		}
	  });
	}

    function createCharge(result) {
		
        var res_data = {stripeToken: result.id,sourceType: result.type,ajax: true,};
       
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_payment,
            data: res_data,
            success: function(data) {
                if (data.code == '1') {
                    // Charge ok : redirect the customer to order confirmation page
                    location.replace(data.url);
                } else {
                    location.replace(order_page+'?stripe_error='+data.msg);
                }
            },
            error: function(err) {
                location.replace(order_page+'?stripe_error='+err);
            }
        });
    }
});