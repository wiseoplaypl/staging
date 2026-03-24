/**
 * 2007-2025 PrestaShop
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2025 PrestaShop SA
 * @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
 * International Registered Trademark & Property of PrestaShop SA
 */

var stripe;
var elements;
var cardElement;
var ibanElement;
var fpxBank;
var idealBank;
var fpxBank_name;
var idealBank_name;
var var_address;
var res_status = null;
var style = {};

/*
const myInterval = setInterval(checkstripeformloaded, 1000);
function checkstripeformloaded() {
       if($("#payment-element-form").length){
          initiateStripePayments(event);
          clearInterval(myInterval);
        }
    }
    */
$(document).ready(function(e) {

  initiateStripePayment(e);
  $(document).on('updatePayMentMethod',function(){
      initiateStripePayment(e);
  });

  //////////////Paymentcard controller//////////////////////////////////
	$("#module-stripejs-paymentcard input[name='payment-option']").click(function(e) {
	  var option_id = $(this).attr('id');
	  $("#module-stripejs-paymentcard .js-additional-information,.js-payment-option-form").each(function(index, element) {
		  if($(this).attr('id')=="pay-with-"+option_id+"-form" || $(this).attr('id')==option_id+"-additional-information")
		  $(this).fadeIn();
		  else
		  $(this).hide();
	  });
    });
	$("#module-stripejs-paymentcard input[name='payment-option']:first").click();
  $("#module-stripejs-paymentcard #payment-confirmation button").click(function(e) {
	  var selected_method = $("#module-stripejs-paymentcard input[name='payment-option']:checked").attr('id');
    var selected_method_id = "pay-with-"+selected_method+"-form";
    $("#"+selected_method_id+" form").submit();
    });

  ///////////////////////// SAVED CARD Payment ///////////////////////////
  $('#selected_pm').val(1);
  $('input[name=stripe_pm]:first').click();
  $('input[name=stripe_pm]').click(function (event) {
  	$('#selected_pm').val(this.value);
  		$('input[name=stripe_pm]').each(function (event) {
  			if($(this).is(':checked')) {
  			  $(this).parents('label.card_line').addClass('active');
  			} else {
  				$(this).parents('label.card_line').removeClass('active');
  				}
  			});
  	   if(this.value==1) {
  		   $("#stripe-payment-form .form-row").show();
  	   } else {
  		   $("#stripe-payment-form .form-row, #card-errors,#error-message").hide();
  	   }
  });

  if (typeof stripe_error !=='undefined' && stripe_error!="") {
        if(stripe_error=='1')
          alert(stripe_error_msg);
        else if(stripe_error!='')
          alert(stripe_error);
    }
});

async function initiateStripePayment(e) {

  if (typeof StripePubKey =='undefined')
     return;
  else {
       stripe = Stripe(StripePubKey,{locale: lang_iso_code});
       elements = stripe.elements();
  }
  const paymentvars = await fetch(ajax_payment+"?getPaymentVars=1", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    //body: JSON.stringify(options),
  }).then(data => {
    return data.json();
  }).catch(e => {alert(e.message);});

  if(typeof paymentvars.cu_email =='undefined'){
    return;
  }
  ps_cart_id = paymentvars.ps_cart_id;
  cu_email = paymentvars.cu_email;
  cu_fname = paymentvars.cu_fname;
  cu_lname = paymentvars.cu_lname;
  billing_address = paymentvars.billing_address;
  ship_address = paymentvars.ship_address;
  country_iso_code = paymentvars.country_iso_code;
  amount_ttl = parseInt(paymentvars.amount_ttl);
  var_address = {line1: billing_address.line1,
         line2: billing_address.line2,
         city: billing_address.city,
         postal_code: billing_address.zip_code,
         country: country_iso_code,
         state: billing_address.state,};

  initiateStripePayments(e);
  if($("div.stripe-payment-17").length){
    $("div.stripe-payment-17").show();
  }
}

function initiateStripePayments(e) {

    ////////////////////////Stripe Payment element///////////////////
    if(stripe_allow_cards==3 && $("#payment-element-form").length) {
      initializePaymentElement(e);
    }
   //////////////////////////////////Stripe CARD Element ////////////////////
   if(stripe_allow_cards==1 && $("#stripe-payment-form").length) {
     initializeCardElement(e);
   }

   //////////////////////////////////Stripe CARD Element ////////////////////
   if(stripe_allow_sepa==1 && $("#stripe-sepa-form").length) {

   		ibanElement = elements.create('iban', {style: style, supportedCountries: ['SEPA'], placeholderCountry: country_iso_code,});
   		ibanElement.mount('#iban-element');
   		var errorMessage = document.getElementById('sepa-errors');
   		var bankName = document.getElementById('bank-name');
   		ibanElement.on('change', function(event) {
   		  errorMessage.textContent = ((event.error) ? event.error.message : '');
   		  bankName.textContent = ((event.bankName) ? event.bankName : '');
   		});
    }

   //////////////////////////// FPX ///////////////////////////
   if(stripe_allow_fpx==true && $("#fpx-bank-element").length){
   	// Create an instance of the fpxBank Element.
   	fpxBank = elements.create('fpxBank',{style: style,accountHolderType: 'individual',});
   	fpxBank.mount('#fpx-bank-element');
   	fpxBank.on('change', function(event) {
   	if (event.empty==false)
   	  fpxBank_name = event.value;
       });
   }

   //////////////////////////// iDeal ///////////////////////////
   if(stripe_allow_ideal==true && $("#ideal-bank-element").length){
   	// Create an instance of the fpxBank Element.
   	idealBank = elements.create('idealBank',{style: style});
   	idealBank.mount('#ideal-bank-element');
   	idealBank.on('change', function(event) {
   	if (event.empty==false)
   	  idealBank_name = event.value;
       });
   }

   ///////////////// Payment Request Button //////////////////
   if(stripe_allow_prbutton==true && $("#payment-request-button").length){

        const options = {
        mode: 'payment',
        amount: amount_ttl,
        currency: currency_lower,
      };

      const elements = stripe.elements(options);
      //const express_options = {applePay: 'always',googlePay: 'always',};

      const expressCheckoutElement = elements.create('expressCheckout');
      expressCheckoutElement.mount('#payment-request-button');

      const expressCheckoutDiv = document.getElementById('payment-request-button');
      expressCheckoutDiv.style.visibility = 'hidden';

      $("input[name='conditions_to_approve[terms-and-conditions]']").change(function(e) {
            if($(this).is(":checked")) {
           $("#payment-request-button").show();
           $(".stripe-pr-tos-error").hide();
          } else {
          $("#payment-request-button").hide();
          $(".stripe-pr-tos-error").show();
         }
      });

     expressCheckoutElement.on('ready', ({availablePaymentMethods}) => {
         if (!availablePaymentMethods) {
           $('.prbutton-alert').show();
           //document.getElementById('payment-request-button').style.display = 'none';
         } else {
           expressCheckoutDiv.style.visibility = 'initial';
           $('.prbutton-alert').hide();
           if(!$("#module-stripejs-paymentcard").length && !$("input[name='conditions_to_approve[terms-and-conditions]']").is(":checked")) {
             $("#payment-request-button, .stripe-pr-tos-error").toggle();
            }
         }
      });

      const handleError = (error) => {
        const messageContainer = document.querySelector('.stripe-payment-errors-prbutton');
        messageContainer.textContent = error.message;
      }

      expressCheckoutElement.on('confirm', async (event) => {

        $('#stripe-ajax-loader-prbutton,#payment-request-button').toggle();
     		$('#payment-confirmation button[type=submit]').prop("disabled", true);
     		var toggle_selector = '#stripe-ajax-loader-prbutton,#payment-request-button';
     	  var error_selector = '.stripe-payment-errors-prbutton';

        const {error: submitError} = await elements.submit();
        if (submitError) {
          showStripePayError(toggle_selector, 0, submitError, error_selector);
          return;
        }

        const {pi_cs: clientSecret} = await fetch(ajax_payment+"?stripeElement=getPICS", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          //body: JSON.stringify(options),
        }).then(data => {
          return data.json();
        }).catch(e => {alert(e.message);});

        stripe.confirmPayment({elements, clientSecret,confirmParams: {return_url: validation_url,},redirect: "if_required",}).then(function(result) {
          if (result.error) {
          showStripePayError(toggle_selector, result.error.code, result.error.message, error_selector);
          } else {
           $('#pr-payment-success').show();
           processPaymentIntent(result.paymentIntent.id,0,0,'prbutton',toggle_selector,error_selector);
          }
        });

      });
   }
}

// Fetches a payment intent and captures the client secret
async function initializePaymentElement(e) {
  $('#stripe-api-loader').show();
  const clientSecret = await fetch(ajax_payment+"?stripeElement=getPICS", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    //body: JSON.stringify(options),
  }).then(data => {
    return data.json();
  }).catch(e => {alert(e.message);});

  pi_cs = clientSecret.pi_cs;
  paymentArr = {paymentMethodOrder:['card']};
  elements = stripe.elements({clientSecret: clientSecret.pi_cs, appearance: {theme: 'stripe'}, });
  //if(!!clientSecret.pi_pm)
    //paymentArr = {paymentMethodOrder:[clientSecret.pi_pm]};

  const paymentElement = elements.create("payment",{layout: {type: StripePETheme,radios: StripePEThemeRadio,spacedAccordionItems:false},defaultValues:{billingDetails:{email: cu_email}},fields:{billingDetails:{name: 'never',email: 'never',address:'never',}}});
  paymentElement.mount("#payment-element");
  paymentElement.on('change', function(event) {
  if (event.value.type=='card') {
  //  $('div.reuse_authorize').fadeIn();
  }else {
  //  $('div.reuse_authorize').fadeOut();
  }
});
paymentElement.on('ready', function(event) {
    $('#stripe-api-loader').hide();
});
}

async function initializeCardElement(e) {
    e.preventDefault();
    if ($("#card-element").length) {
        // Create an instance of the card Element.
        var card = elements.create('card', {style: style,postalCode:billing_address.zip_code, country:country_iso_code,hidePostalCode:stripe_allow_zip,defaultValues:{billingDetails:{name: cu_fname+' '+cu_lname,email: cu_email,address:var_address}}});
        card.mount('#card-element');
        card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        displayError.textContent = ((event.error)?event.error.message:'');
        });
        cardElement = card;
    }
}

///////////////////////Handle Submit Calls/////////////////
function submitStripePayment(e,method) {

  var methods_redirects = ["konbini","alipay", "ideal", "giropay", "bancontact", "sofort", "p24","fpx","grabpay","oxxo", "eps", "multibanco", "klarna",'affirm','afterpay_clearpay'];

  if(stripe_allow_cards==3 && method=='OPC' && $("#payment-element-form").length){
    if($("#payment-element").is(":empty")){
      //if(stripe_payment_methods_opc==2) {
      //  $("div.stripe-payment-17").attr('id','modal_stripe');
      //  $("div.stripe-payment-17").addClass('modal');
      //  $("#stripe-submit-button,#stripe_payment_close").show();
      //  $("div.stripe-payment-17").modalStripe({cloning: false, closeOnOverlayClick: false, closeOnEsc: false}).open();
      //}
      initiateStripePayment(e);
    } else {
      handleSubmitPaymentElement(e);
    }
  }

  if(stripe_allow_cards==3 && method=='card' && $("#payment-element-form").length) {
    handleSubmitPaymentElement(e);
  } else if(stripe_allow_cards>0 && stripe_allow_cards<3 && method=='card' && $("#stripe-payment-form").length) {
    handleSubmitCardElement(e);
  } else if(method=='prbutton' && stripe_allow_prbutton==true && $("#payment-request-button").length) {
    if ($('.prbutton-alert').is(":visible"))
      alert($('.prbutton-alert').text());
    else
      alert(prbutton_alert);
  } else if(method=='sepa' && stripe_allow_sepa==true && $("#iban-element").length) {
    handleSubmitSepa(e);
  } else if(method=='wechat_pay') {
    handleSubmitWechat(e);
  } else if(method=='paynow') {
    handleSubmitPaynow(e);
  } else if(methods_redirects.indexOf(method) != -1) {
    handleSubmitRedirectPay(method);
  }
}

////////////////////////Stripe Payment element///////////////////
async function handleSubmitPaymentElement(e) {

  $('input[name=payment-option]:checked').focus();
  $('#payment-element-form,#error-message').hide();
  $('#stripe-ajax-loader').show();
  $('#payment-confirmation button[type=submit]').prop("disabled", true);
  var toggle_selector = '#stripe-ajax-loader,#payment-element-form';
  var error_selector = '#error-message';
  var callArray = { elements, redirect:'if_required',
                    confirmParams: {
                      return_url: validation_url,
                      payment_method_data:{billing_details:{name: cu_fname+' '+cu_lname,email: cu_email,address:var_address,}},
                    },
                  };
  handlePI('paymentElement', function(res_status){
     if(res_status.code==1) {
        stripe.confirmPayment(callArray).then(function(result) {
         if (result.error) {
           showStripePayError(toggle_selector, result.error.code, result.error.message, error_selector);
         } else {

           var pi_statuses = ["succeeded","processing", "requires_capture"];
           if(pi_statuses.indexOf(result.paymentIntent.status) != -1 || (result.paymentIntent.next_action.type=='boleto_display_details' && result.paymentIntent.status=='requires_action')) {
             $('#payment-success').show();
             processPaymentIntent(result.paymentIntent.id,0,0,'paymentElement',toggle_selector,error_selector);
           } else {
             showStripePayError(toggle_selector, 0, stripe_error_msg, error_selector);
           }
         }
        });
      }
    });

  return false;
}

/////////////////////Stripe Checkout & Card Element//////////////////
async function handleSubmitCardElement(e) {

  var stripe_pm = document.getElementById('selected_pm').value;
  var quickPay = ((stripe_pm!=1) ? 1 : 0);
  var cardholderName = document.getElementById('cardholder-name');
  $('input[name=payment-option]:checked').focus();
  $('#stripe-ajax-loader,#stripe-payment-form').toggle();
  $('#payment-confirmation button[type=submit]').prop("disabled", true);
  var toggle_selector = '#stripe-ajax-loader,#stripe-payment-form';
  var error_selector = '#card-errors';

  if(stripe_allow_cards==2 && stripe_pm==1){

    handlePI('checkout', function(res_status){
      if(res_status.code==1) {
        $('#checkout-success').show();
        stripe.redirectToCheckout({
          sessionId: res_status.sess_id
          }).then(function (result) {
            $('#checkout-success').hide();
            var err_msg = showStripePayError(toggle_selector, 0, result.error.message, error_selector);
            alert(result.error.message);
          });
      }
     });

   } else {

    var use_payment_method = ((stripe_pm!=1) ? stripe_pm : {card: cardElement,billing_details: {name: cardholderName.value,email: billing_address.email,address: var_address,},});
    var future_use = (($('input[name=reuse_authorize]:checked').length)?'on_session':null);

    handlePI('card', function(res_status){
     if(res_status==false) {
       var err_msg = showStripePayError(toggle_selector, 0, res_status.msg, error_selector);
     } else if(res_status.code==1) {
       confirm_beforeunload=true;
       stripe.confirmCardPayment(res_status.pi_cs, {payment_method:use_payment_method,setup_future_usage:future_use,return_url:validation_url,}).then(function(result) {
        if (result.error) {
          var err_msg = showStripePayError(toggle_selector, result.error.code, result.error.message, error_selector);
        } else {
          $('#payment-success').show();
          processPaymentIntent(result.paymentIntent.id,quickPay,$('input[name=reuse_authorize]:checked').length,'card',toggle_selector,error_selector);
        }
        });
    }
    });
   }
}

////////////////////////////////// SEPA Direct Debit Payments ////////////////////
async function handleSubmitSepa(e) {

		  $('input[name=payment-option]:checked').focus();
		  $('#stripe-sepa-form,#stripe-ajax-loader-sepa').toggle();
		  $('#payment-confirmation button[type=submit]').prop("disabled", true);
		  var toggle_selector = '#stripe-sepa-form,#stripe-ajax-loader-sepa';
	    var error_selector = '#sepa-errors';

		  handlePI('sepa_debit', function(res_status) {
			 if(res_status==false) {
				 showStripePayError(toggle_selector, 0, res_status.msg, error_selector);
			 } else if(res_status.code==1) {
				confirm_beforeunload=true;
				stripe.confirmSepaDebitPayment(res_status.pi_cs,{payment_method: {sepa_debit: ibanElement,
					  billing_details: {name: document.querySelector('input[name="sepa_name"]').value,email: cu_email,address: var_address, },},}).then(function(result) {
					if (result.error) {
						showStripePayError(toggle_selector, result.error.code, result.error.message, error_selector);
					 } else {
						$('#sepa-payment-success').show();
						processPaymentIntent(result.paymentIntent.id,0,0,'sepa_debit',toggle_selector,error_selector);
					 }
				  });
			 }
		});
}

$(document).on('click', '#sofort_available_countries .close', function(e){
	$('#sofort_available_countries').modalStripe().close();
});

/////////////////////////////////////WeChat Pay/////////////////////////////////
async function handleSubmitWechat(e) {

   $('.stripe-payment-errors-wechat').hide();
   $('#stripe-ajax-loader-wepay').show();
   $('#payment-confirmation button[type=submit]').prop("disabled", true);
   var toggle_selector = '#stripe-ajax-loader-wepay';
   var error_selector = '.stripe-payment-errors-wechat';
   handlePI('wechat_pay', function(res_status){

    if(res_status.code==1) {
      stripe.confirmWechatPayPayment( res_status.pi_cs, {
             payment_method_options: {wechat_pay: {client: 'web',},},
           },
        ).then(function(result) {
          $('#stripe-ajax-loader-wepay').hide();
          if (result.error) {
            showStripePayError('.qr_code', 'pay_declined', result.error.message, error_selector);
          } else if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
            $('#stripe-ajax-loader-wechat').show();
            processPaymentIntent(result.paymentIntent.id,0,0,'wechat_pay',toggle_selector,error_selector);
          } else {
            showStripePayError('.qr_code', 'wechat_declined', stripe_error_msg, error_selector);
          }
        });
      }
  });
}

/////////////////////////////////////PayNow/////////////////////////////////
async function handleSubmitPaynow(e) {

     $('.stripe-payment-errors-paynow').hide();
     $('#stripe-ajax-loader-nowpay').show();
     $('#payment-confirmation button[type=submit]').prop("disabled", true);
     var toggle_selector = '#stripe-ajax-loader-nowpay';
     var error_selector = '.stripe-payment-errors-paynow';
     handlePI('paynow', function(res_status){

       if(res_status.code==1) {
         stripe.confirmPayNowPayment( res_status.pi_cs,{
          payment_method: {billing_details: {name: cu_fname+' '+cu_lname,email: cu_email},},
        }).then(function(result) {
          $('#stripe-ajax-loader-nowpay').hide();
          if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
            $('#stripe-ajax-loader-paynow').show();
            processPaymentIntent(result.paymentIntent.id,0,0,'paynow',toggle_selector,error_selector);
          }else if (result.error) {
           showStripePayError('', '', result.error.message, error_selector);
          } else {
            showStripePayError('', '', stripe_error_msg, error_selector);
          }
        });
      }
     });
}

//////////////////Redirect payment methods////////////////////
async function handleSubmitRedirectPay(method_stripejs) {

		if(method_stripejs == 'ideal' && idealBank_name==''){
		  alert(bank_empty_error);$('input[name=payment-option]:checked').focus();
		  return false;
		}else if(method_stripejs == 'fpx' && fpxBank_name==''){
		  alert(bank_empty_error);$('input[name=payment-option]:checked').focus();
		  return false;
		}

		$('#payment-confirmation button[type=submit]').prop("disabled", true);
		$('#stripe-ajax-loader-redirect').insertAfter('#payment-confirmation button[type=submit]').show();
		var res_status = false;

			//"alipay", "bancontact", "ideal", "giropay", "sofort", "p24","fpx","grabpay", "eps", "oxxo", "multibanco"
			var res_order = false
			handlePI(method_stripejs, function(res_status){

			  if(res_status.code==1) {
          if(method_stripejs=='klarna') {
					stripe.confirmKlarnaPayment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,address:var_address,}},
							   return_url: validation_url,},
                 //{handleActions:false,}
					  ).then(function(result) { if (result.error) { alert_error(result.error.message);
					   } });
				}else if(method_stripejs=='affirm') {
        stripe.confirmAffirmPayment( res_status.pi_cs, {
               payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,address:var_address,}},
               return_url: validation_url,},
               //{handleActions:false,}
          ).then(function(result) { if (result.error) { alert_error(result.error.message);
           } });
      }else if(method_stripejs=='afterpay_clearpay') {
        stripe.confirmAfterpayClearpayPayment( res_status.pi_cs, {
               payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,address:var_address,}},
               return_url: validation_url,},
          ).then(function(result) { if (result.error) { alert_error(result.error.message);
           } });
      }else if(method_stripejs=='alipay') {
        stripe.confirmAlipayPayment( res_status.pi_cs, {
               payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},
               return_url: validation_url,},
          ).then(function(result) { if (result.error) { alert_error(result.error.message);
           } });
      }else if(method_stripejs=='bancontact') {
					stripe.confirmBancontactPayment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
					     } });
				}else if(method_stripejs=='ideal') {
					stripe.confirmIdealPayment( res_status.pi_cs, {
							   payment_method: { ideal: idealBank, billing_details: {name: document.getElementById('accountholder-name').value,email: cu_email,}},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {document.getElementById('ideal-errors').textContent = result.error.message;
					       alert_error(result.error.message);
					     } });
				}else if(method_stripejs=='sofort') {
					stripe.confirmSofortPayment( res_status.pi_cs, {
							   payment_method: {sofort: {country: $('select#sofort_country option').filter(":selected").val()},
							                    billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
					     } });
				}else if(method_stripejs=='p24') {
					stripe.confirmP24Payment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
					     }  });
				}else if(method_stripejs=='eps') {
					stripe.confirmEpsPayment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
					     } });
				}else if(method_stripejs=='giropay') {
					stripe.confirmGiropayPayment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
            } });
				}else if(method_stripejs=='fpx') {
					stripe.confirmFpxPayment( res_status.pi_cs, {
							   payment_method: { fpx: fpxBank},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {document.getElementById('fpx-errors').textContent = result.error.message;
					       alert_error(result.error.message);
					     } });
				}else if(method_stripejs=='grabpay') {
					stripe.confirmGrabPayPayment( res_status.pi_cs, {
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
					     } });
				}else if(method_stripejs=='oxxo') {
					stripe.confirmOxxoPayment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},
							   return_url: validation_url,},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
					     } });
				}else if(method_stripejs=='multibanco') {
					stripe.confirmMultibancoPayment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},},
                 {handleActions:false,}
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
            } else { placeOrderInfo(result.paymentIntent.id, method_stripejs, function(res_order){
								      if(res_order.code==1){
                       window.location.replace(res_order.url);
                     }
								    });
					   } });
				}/*else if(method_stripejs=='konbini') {
					stripe.confirmKonbiniPayment( res_status.pi_cs, {
							   payment_method: { billing_details: {name: cu_fname+' '+cu_lname,email: cu_email,}},},
					  ).then(function(result) { if (result.error) {alert_error(result.error.message);
            } else { //placeOrderInfo(res_status.pi_id, method_stripejs, function(res_order){
								  // if(res_order==true){
                    // window.location.replace(result.paymentIntent.next_action.redirect_to_url.url);
                   //}
								 //});
					   } });
				}*/
			  }
			});
}

function alert_error(message){
	alert(message);
	$('#payment-confirmation button[type=submit]').prop("disabled", false);
	$('#stripe-ajax-loader-redirect').hide();
}

function handlePI(pm,callback){

	  var future_use = (($('input[name=reuse_authorize]:checked').length)?1:0);
	  confirm_beforeunload=true;
	  $.ajax({
			  type: 'POST',
			  url: ajax_payment,
			  cache:false,
			  data: {
				  sourceType: pm,
				  handlePIntent: true,
				  card_reuse: future_use,
				  ajax: true,
			  },
			  success: function(data) {
				  res = data.replace(/[^{]*/i,'');var data = JSON.parse(res);
				  confirm_beforeunload=false;
				  if(typeof data.code !=='undefined' && data.code == 0) {
					  $('#payment-confirmation button[type=submit]').prop("disabled", false);
					  $('#stripe-ajax-loader-redirect,#stripe-ajax-loader').hide();
					  $('#stripe-payment-form,#payment-element-form').show();
					  alert(data.msg);
					  callback(false);
				  } else if(typeof data.code !=='undefined' && data.code == 1) {
					  callback(data);
				  }else {
					  $('#payment-confirmation button[type=submit]').prop("disabled", false);
					  $('#stripe-ajax-loader-redirect,#stripe-ajax-loader').hide();
					  $('#stripe-payment-form').show();
					  }
			  },
			  error: function(jqXHR, exception) {
				  alert(showStripePayError('', jqXHR, 'ajax', true,exception));
				  return false;
			  }
		  });
}

function placeOrderInfo(pi_id,pm,callback){

	confirm_beforeunload=true;
  var ajax_data = {stripeToken: pi_id, sourceType: pm, cart_id: ps_cart_id, ajax: true,};

	  $.ajax({
			  type: 'POST',
			  url: ajax_payment,
			  data: ajax_data,
			  cache:false,
			  success: function(data) {
				  res = data.replace(/[^{]*/i,'');var data = JSON.parse(res);
				  confirm_beforeunload=false;
				  if(typeof data.code !=='undefined' && data.code == 0) {
					  $('#payment-confirmation button[type=submit]').prop("disabled", false);
					  $('#stripe-ajax-loader-redirect').hide();
					  alert(data.msg);
					  callback(false);
				  } else if(typeof data.code !=='undefined' && data.code == 1) {
					  callback(data);
				  }
			  },
			  error: function(jqXHR, exception) {
				  alert(showStripePayError('', jqXHR, 'ajax', true,exception));
				  return false;
			  }
		  });
}

function processPaymentIntent(result_id,quickpay,card_reuse,type,toggle_selector,error_selector){
	quickpay = typeof quickpay !== 'undefined' ? quickpay : 0;
	card_reuse = typeof card_reuse !== 'undefined' ? card_reuse : 0;
  if(type!='paymentElement'){
	  toggle_selector = '#stripe-ajax-loader,#stripe-payment-form';
	  error_selector = '#card-errors';
  }
	confirm_beforeunload=true;
	if(type != 'card') {
		var ajax_data = {stripeToken: result_id, sourceType: type, ajax: true,};
	} else {
	   var ajax_data = {stripeToken: result_id, card_reuse: card_reuse, quick_pay: quickpay, sourceType: type, ajax: true,};
	}
	$.ajax({
			type: 'POST',
			url: ajax_payment,
			data: ajax_data,
			cache:false,
			success: function(data) {
			  result = data.replace(/[^{]*/i,'');var data = JSON.parse(result);
			  confirm_beforeunload=false;
			  if (data.code == '1') {
					  location.replace(data.url);
				  } else {
					  showStripePayError(toggle_selector, data.code, data.msg, error_selector);
				  }
			},
			error: function(jqXHR, exception) {
				showStripePayError(toggle_selector, jqXHR, 'ajax', error_selector,exception);
			}
		});
}

function showStripePayError(toggleID, code, msg, only_msg, exception){
	exception = typeof exception !== 'undefined' ? exception : null;
	confirm_beforeunload=false;
	$(toggleID).toggle();
	var err_msg = '';
  if(typeof prestashop.selectors !== 'undefined'){
    const formSelectors = prestashop.selectors.checkout.form;
    $(formSelectors).data('disabled', false);
  }
	$('#payment-confirmation button[type=submit]').prop("disabled", false);
	$('#payment-confirmation button[type=submit]').removeAttr('disabled');
  $('#payment-confirmation button[type=submit]').removeClass('disabled');
  $('#payment-success').hide();

	if(msg=='ajax') {
		var msg = '';
        if (code.status === 0) {
            err_msg = 'No connection avaliable. Please Verify your Network.';
        } else if (code.status == 404) {
            err_msg = 'Requested page not found. [404]';
        } else if (code.status == 500) {
            err_msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
            err_msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            err_msg = 'Request got time out.';
        } else if (exception === 'abort') {
            err_msg = 'Ajax request aborted.';
        } else {
            err_msg = 'Uncaught Error.\n' + code.responseText;
        }

	} else {
		err_msg = $('#stripe-'+code).text();
		if (!err_msg || err_msg == "undefined" || err_msg == '')
			err_msg = msg;
	}
	if(only_msg==true)
	  return err_msg;

	$(only_msg).text(err_msg).show();
}

;(function($, window, document, undefined) {
    "use strict";
    /*jshint smarttabs:true*/

    // :focusable expression, needed for tabindexes in modal
    $.extend($.expr[':'],{
        focusable: function(element){
            var map, mapName, img,
                nodeName = element.nodeName.toLowerCase(),
                isTabIndexNotNaN = !isNaN($.attr(element,'tabindex'));
            if ('area' === nodeName) {
                map = element.parentNode;
                mapName = map.name;
                if (!element.href || !mapName || map.nodeName.toLowerCase() !== 'map') {
                    return false;
                }
                img = $('img[usemap=#' + mapName + ']')[0];
                return !!img && visible(img);
            }

            var result = isTabIndexNotNaN;
            if (/input|select|textarea|button|object/.test(nodeName)) {
                result = !element.disabled;
            } else if ('a' === nodeName) {
                result = element.href || isTabIndexNotNaN;
            }

            return result && visible(element);

            function visible(element) {
                return $.expr.filters.visible(element) &&
                    !$(element).parents().addBack().filter(function() {
                        return $.css(this,'visibility') === 'hidden';
                    }).length;
            }
        }
    });

    var pluginNamespace = 'the-modal',
        // global defaults
        defaults = {
            lockClass: 'themodal-lock',
            overlayClass: 'themodal-overlay',

            closeOnEsc: true,
            closeOnOverlayClick: true,

            onBeforeClose: null,
            onClose: null,
            onOpen: null,

            cloning: true
        };
    var oMargin = {};
    var ieBodyTopMargin = 0;

    function isIE() {
        return ((navigator.appName == 'Microsoft Internet Explorer') ||
        (navigator.userAgent.match(/MSIE\s+\d+\.\d+/)) ||
        (navigator.userAgent.match(/Trident\/\d+\.\d+/)));
    }

    function lockContainer(options, overlay) {
        var body = $('body');
        var oWidth = body.outerWidth(true);
        body.addClass(options.lockClass);
        var sbWidth = body.outerWidth(true) - oWidth;
        if (isIE()) {
            ieBodyTopMargin = body.css('margin-top');
            body.css('margin-top', 0);
        }

        if (sbWidth != 0) {
            var tags = $('html, body');
            tags.each(function () {
                var $this = $(this);
                oMargin[$this.prop('tagName').toLowerCase()] = parseInt($this.css('margin-right'));
            });
            $('html').css('margin-right', oMargin['html'] + sbWidth);
            overlay.css('left', 0 - sbWidth);
        }
    }

    function unlockContainer(options) {
        if (isIE()) {
            $('body').css('margin-top', ieBodyTopMargin);
        }

        var body = $('body');
        var oWidth = body.outerWidth(true);
        body.removeClass(options.lockClass);
        var sbWidth = body.outerWidth(true) - oWidth;

        if (sbWidth != 0) {
            $('html, body').each(function () {
                var $this = $(this);
                $this.css('margin-right', oMargin[$this.prop('tagName').toLowerCase()])
            });
        }
    }

    function init(els, options) {
        var modalOptions = options;

        if(els.length) {
            els.each(function(){
                $(this).data(pluginNamespace+'.options', modalOptions);
            });
        } else {
            $.extend(defaults, modalOptions);
        }

        // on Ctrl+A click fire `onSelectAll` event
        $(window).bind('keydown',function(e){
            if (!(e.ctrlKey && e.keyCode == 65)) {
                return true;
            }

            if ( $('input:focus, textarea:focus').length > 0 ) {
                return true;
            }

            var selectAllEvent = new $.Event('onSelectAll');
            selectAllEvent.parentEvent = e;
            $(window).trigger(selectAllEvent);
            return true;
        });

        els.bind('keydown',function(e){
            var modalFocusableElements = $(':focusable',$(this));
            if(modalFocusableElements.filter(':last').is(':focus') && (e.which || e.keyCode) == 9){
                e.preventDefault();
                modalFocusableElements.filter(':first').focus();
            }
        });

        return {
            open: function(options) {
                var el = els.get(0),
                    localOptions = $.extend({}, defaults, $(el).data(pluginNamespace+'.options'), options);

                // close modal if opened
                if($('.'+localOptions.overlayClass).length) {
                    $.modalStripe().close();
                }

                var overlay = $('<div/>').addClass(localOptions.overlayClass).prependTo('body');
                overlay.data(pluginNamespace+'.options', localOptions);

                lockContainer(localOptions, overlay);

                if(el) {
                    var openedModalElement = null;
                    if (!localOptions.cloning) {
                        overlay.data(pluginNamespace+'.el', el);
                        $(el).data(pluginNamespace+'.parent', $(el).parent());
                        openedModalElement = $(el).appendTo(overlay).show();
                    } else {
                        openedModalElement = $(el).clone(true).appendTo(overlay).show();
                    }
                }

                if(localOptions.closeOnEsc) {
                    $(document).bind('keyup.'+pluginNamespace, function(e){
                        if(e.keyCode === 27) {
                            $.modalStripe().close(localOptions);
                        }
                    });
                }

                if(localOptions.closeOnOverlayClick) {
                    $('.' + localOptions.overlayClass).on('click.' + pluginNamespace, function(e){
                        if (e.target.className == localOptions.overlayClass){
                            $.modalStripe().close(localOptions);
                        }
                    });
                }

                $(document).bind('touchmove.'+pluginNamespace,function(e){
                    if(!$(e).parents('.' + localOptions.overlayClass)) {
                        e.preventDefault();
                    }
                });

                if(el) {
                    $(window).bind('onSelectAll',function(e){
                        e.parentEvent.preventDefault();

                        var range = null,
                            selection = null,
                            selectionElement = openedModalElement.get(0);
                        if (document.body.createTextRange) { //ms
                            range = document.body.createTextRange();
                            range.moveToElementText(selectionElement);
                            range.select();
                        } else if (window.getSelection) { //all others
                            selection = window.getSelection();
                            range = document.createRange();
                            range.selectNodeContents(selectionElement);
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }
                    });
                }

                if(localOptions.onOpen) {
                    localOptions.onOpen(overlay, localOptions);
                }
            },
            close: function(options) {
                var el = els.get(0),
                    localOptions = $.extend({}, defaults, $(el).data(pluginNamespace+'.options'), options);
                var overlay = $('.' + localOptions.overlayClass);

                if ($.isFunction(localOptions.onBeforeClose)) {
                    if (localOptions.onBeforeClose(overlay, localOptions) === false) {
                        return;
                    }
                }

                if (!localOptions.cloning) {
                    if (!el) {
                        el = overlay.data(pluginNamespace+'.el');
                    }
                    $(el).hide().appendTo($(el).data(pluginNamespace+'.parent'));
                }

                overlay.remove();
                unlockContainer(localOptions);

                if(localOptions.closeOnEsc) {
                    $(document).unbind('keyup.'+pluginNamespace);
                }

                $(window).unbind('onSelectAll');

                if(localOptions.onClose) {
                    localOptions.onClose(overlay, localOptions);
                }
            }
        };
    }

    $.modalStripe = function(options){
        return init($(), options);
    };

    $.fn.modalStripe = function(options) {
        return init(this, options);
    };

})(jQuery, window, document);

var confirm_beforeunload=false;
$(window).on('beforeunload', function(e){

    if(confirm_beforeunload==true){
		e.returnValue = confirm_unload_msg;
        return confirm_unload_msg;
    }

});
