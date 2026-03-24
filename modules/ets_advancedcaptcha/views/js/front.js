/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
var PA_GOOGLE_V3_CAPTCHA_SITE_KEY = PA_GOOGLE_V3_CAPTCHA_SITE_KEY || false;
var PA_GOOGLE_V3_POSITION = PA_GOOGLE_V3_POSITION || 'bottomright';
var PA_GOOGLE_CAPTCHA_THEME = PA_GOOGLE_CAPTCHA_THEME || 'light';

var v3_render_clientID = {};
var func_pa = {
    addNotificationCustom: function (productId, productAttributeId) {
        // to keep backward compatibility
        if (typeof productId === 'undefined') {
            var ids = $('div.js-mailalert > input[type=hidden]');
            productId = ids.eq(0).val();
            productIdAttribute = ids.eq(1).val();
        }
        var captchaParams = '';
        if ($('div.js-mailalert textarea[name=g-recaptcha-response]').length > 0) {
            captchaParams += '&g-recaptcha-response=' + $('div.js-mailalert textarea[name=g-recaptcha-response]').val();
        } else if ($('div.js-mailalert input[name=pa_captcha]').length > 0) {
            captchaParams += '&pa_captcha=' + $('div.js-mailalert input[name=pa_captcha]').val();
        }
        if ($('div.js-mailalert input[name=posTo]').length > 0) {
            captchaParams += '&posTo=' + $('div.js-mailalert input[name=posTo]').val();
        }
        $.ajax({
            type: 'POST',
            url: $('div.js-mailalert').data('url'),
            data: 'id_product=' + productId + '&id_product_attribute=' + productAttributeId + '&customer_email=' + $('div.js-mailalert > input[type=email]').val() + (captchaParams ? captchaParams : ''),
            success: function (resp) {
                resp = JSON.parse(resp);

                $('.js-mailalert-alerts').html('<article class="mt-1 alert alert-' + (resp.error ? 'danger' : 'success') + '" role="alert" data-alert="' + (resp.error ? 'error' : 'success') + '">' + resp.message + '</article>').show();
                if (!resp.error) {
                    $('div.js-mailalert > .js-mailalert-add, div.js-mailalert > input[type=email], div.js-mailalert .gdpr_consent_wrapper, div.js-mailalert .captcha_out_of_stock').hide();
                }
            }
        });
        if ($('div.js-mailalert img.pa_captcha_img_data').length > 0) {
            func_pa.refreshCaptcha($('div.js-mailalert img.pa_captcha_img_data').removeClass('loaded'));
        } else {
            func_pa.resetReCaptcha();
        }
        return false;
    },
    addNotification: function () {
        var ids = $('div.js-mailalert > input[type=hidden]');
        var captchaParams = '';
        if ($('div.js-mailalert textarea[name=g-recaptcha-response]').length > 0) {
            captchaParams += '&g-recaptcha-response=' + $('div.js-mailalert textarea[name=g-recaptcha-response]').val();
        } else if ($('div.js-mailalert input[name=pa_captcha]').length > 0) {
            captchaParams += '&pa_captcha=' + $('div.js-mailalert input[name=pa_captcha]').val();
        }
        if ($('div.js-mailalert input[name=posTo]').length > 0) {
            captchaParams += '&posTo=' + $('div.js-mailalert input[name=posTo]').val();
        }
        $.ajax({
            type: 'POST',
            url: $('div.js-mailalert').data('url'),
            data: 'id_product=' + ids[0].value + '&id_product_attribute=' + ids[1].value + '&customer_email=' + $('div.js-mailalert > input[type=email]').val() + (captchaParams ? captchaParams : ''),
            success: function (resp) {
                resp = JSON.parse(resp);
                $('div.js-mailalert > span').html('<article class="alert alert-' + (resp.error ? 'danger' : 'success') + '" role="alert" data-alert="' + (resp.error ? 'error' : 'success') + '">' + resp.message + '</article>').show();
                if (!resp.error) {
                    $('div.js-mailalert > button').hide();
                    $('div.js-mailalert > input[type=email]').hide();
                    $('div.js-mailalert > #gdpr_consent').hide();
                    $('div.js-mailalert > .captcha_out_of_stock').hide();
                }
                if ($('div.js-mailalert input[name=pa_captcha]').length > 0) {
                    $('div.js-mailalert input[name=pa_captcha]').val('');
                }
            }
        });
        if ($('div.js-mailalert img.pa_captcha_img_data').length > 0) {
            func_pa.refreshCaptcha($('div.js-mailalert img.pa_captcha_img_data').removeClass('loaded'));
        } else {
            func_pa.resetReCaptcha();
        }
        return false;
    },
    addNotification16: function () {
        if ($('#oosHook .gdpr_hook.receive').length > 0 && !$('.pa_notify input[name=gdpr_accept]').is(':checked')) {
            $('#pa_mailalert_link').prop('disabled', true);
            return false;
        }
        if ($('#pa_oos_customer_email').val() == mailalerts_placeholder || (typeof mailalerts_url_add == 'undefined')) {
            return false;
        }
        var captchaParams = '';
        if ($('.pa_notify textarea[name=g-recaptcha-response]').length > 0) {
            captchaParams += '&g-recaptcha-response=' + $('.pa_notify textarea[name=g-recaptcha-response]').val();
        } else if ($('.pa_notify input[name=pa_captcha]').length > 0) {
            captchaParams += '&pa_captcha=' + $('.pa_notify input[name=pa_captcha]').val();
        }
        if ($('.pa_notify input[name=posTo]').length > 0) {
            captchaParams += '&posTo=' + $('.pa_notify input[name=posTo]').val();
        }
        $.ajax({
            type: 'POST',
            url: mailalerts_url_add,
            data: 'id_product=' + id_product + '&id_product_attribute=' + $('#idCombination').val() + '&customer_email=' + $('#pa_oos_customer_email').val() + (captchaParams ? captchaParams : ''),
            success: function (json) {
                if (json == '-1') {
                    $('#pa_oos_customer_email_result').html(mailalerts_captcha_error);
                    $('#pa_oos_customer_email_result').css('color', 'red').show();
                } else if (json == '1') {
                    $('#pa_mailalert_link, #pa_oos_customer_email, .pa_notify .page_product').hide();
                    $('#oosHook').find('#gdpr_consent').hide();
                    $('#pa_oos_customer_email_result').html(mailalerts_registered);
                    $('#pa_oos_customer_email_result').css('color', 'green').show();
                } else if (json == '2') {
                    $('#pa_oos_customer_email_result').html(mailalerts_already);
                    $('#pa_oos_customer_email_result').css('color', 'red').show();
                } else {
                    $('#pa_oos_customer_email_result').html(mailalerts_invalid);
                    $('#pa_oos_customer_email_result').css('color', 'red').show();
                }
                if ($('.pa_notify input[name=pa_captcha]').length > 0) {
                    $('.pa_notify input[name=pa_captcha]').val('');
                }
            }
        });
        if ($('.pa_notify img.pa_captcha_img_data').length > 0) {
            func_pa.refreshCaptcha($('.pa_notify img.pa_captcha_img_data').removeClass('loaded'));
        } else {
            func_pa.resetReCaptcha();
        }
        return false;
    },
    refreshCaptcha: function (img) {
        if (img.length && !img.hasClass('loaded')) {
            var orgLink = img.attr('src');
            var orgCode = img.attr('data-rand');
            var rand = Math.random();
            img.attr('src', orgLink.replace(orgCode, rand));
            img.attr('data-rand', rand);
            if (!img.hasClass('loaded')) {
                img.addClass('loaded');
            }
        }
    },
    resetReCaptcha: function () {
        if (typeof grecaptcha === "undefined") {
            return;
        }
        var hasWidgets = (typeof recaptchaWidgets !== "undefined" && recaptchaWidgets.length);
        if (grecaptcha.enterprise && typeof grecaptcha.enterprise.reset === 'function') {
            if (hasWidgets) {
                for (var j = 0; j < recaptchaWidgets.length; j++) {
                    grecaptcha.enterprise.reset(recaptchaWidgets[j]);
                }
            }
            return;
        }
        if (typeof grecaptcha.reset === 'function') {
            if (hasWidgets) {
                for (var i = 0; i < recaptchaWidgets.length; i++) {
                    grecaptcha.reset(recaptchaWidgets[i]);
                }
            } else {
                grecaptcha.reset();
            }
        }
    },
    loadCaptcha: function () {
        var img = $('.pa_captcha_img_data:not(.loaded)').first();
        if (img.length > 0) {
            img.load(function () {
                if (!img.hasClass('loaded')) {
                    func_pa.refreshCaptcha(img);
                }
                if (img[0].complete && img.hasClass('loaded')) {
                    func_pa.loadCaptcha();
                }
            }).filter(function () {
                return this.complete;
            }).load();
        }
    },
    loadReCaptchaV3: function (form) {
        if (!PA_GOOGLE_V3_CAPTCHA_SITE_KEY || !form || form.hasClass('g-loaded') || form.hasClass('g-loading')) {
            return false;
        }

        var g_captcha = form.find('[id*=g-recaptcha-response-]');
        if (!g_captcha.length) {
            form.removeClass('g-loading g-loaded');
            return false;
        }

        var captchaId = g_captcha[0].id;
        var action = ($('body').attr('id') || 'submit').replace(/(?=[^A-Za-z_])([^A-Za-z_])/g, '_');
        var renderAndExecute = function (clientId) {
            grecaptcha.ready(function () {
                grecaptcha.execute(clientId, { action: action }).then(function (token) {
                    if (token) {
                        var existingInput = g_captcha.find('input[name="g-recaptcha-response"]');
                        if (existingInput.length) {
                            existingInput.val(token);
                        } else {
                            g_captcha.append('<input type="hidden" name="g-recaptcha-response" value="' + token + '"/>');
                        }
                        g_captcha.find('textarea#g-recaptcha-response').remove();
                        form.removeClass('g-loading').addClass('g-loaded');
                    }
                }).catch(function () {
                    form.removeClass('g-loading');
                    setTimeout(function () {
                        func_pa.loadReCaptchaV3(form);
                    }, 800);
                });
            });
        };

        form.addClass('g-loading');

        if (v3_render_clientID[captchaId]) {
            renderAndExecute(v3_render_clientID[captchaId]);
            return true;
        }

        var ensureScript = function () {
            if (typeof window.grecaptcha !== 'undefined' && typeof window.grecaptcha.ready === 'function') {
                var renderClientId = grecaptcha.render(captchaId, {
                    sitekey: PA_GOOGLE_V3_CAPTCHA_SITE_KEY,
                    badge: PA_GOOGLE_V3_POSITION,
                    size: 'invisible',
                    theme: PA_GOOGLE_CAPTCHA_THEME
                });
                v3_render_clientID[captchaId] = renderClientId;
                renderAndExecute(renderClientId);
                return;
            }
            setTimeout(ensureScript, 150);
        };

        if (!document.getElementById('pa-recaptcha-v3')) {
            var s = document.createElement('script');
            s.id = 'pa-recaptcha-v3';
            s.async = true;
            s.defer = true;
            s.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(PA_GOOGLE_V3_CAPTCHA_SITE_KEY);
            s.onerror = function () {
                form.removeClass('g-loading');
            };
            document.head.appendChild(s);
        }

        ensureScript();
        return true;
    },
};

var FORCE_RECAPTCHA_FORMS = [];

$(document).ready(function () {
    if (PA_GOOGLE_V3_CAPTCHA_SITE_KEY) {
        var delayIndex = 0;
        $('form:not(.g-loaded):not(.g-loading)').each(function () {
            var $form = $(this);
            var hasPlaceholder = $form.find('[id*=g-recaptcha-response-]').length > 0;
            var isForced = FORCE_RECAPTCHA_FORMS.some(function (sel) { return $form.is(sel); });
            if (!hasPlaceholder && !isForced) {
                return;
            }
            if (!hasPlaceholder && isForced) {
                $form.append('<div id="g-recaptcha-response-' + Math.random().toString(36).slice(2) + '"></div>');
            }
            setTimeout(function () {
                func_pa.loadReCaptchaV3($form);
            }, delayIndex * 300);
            delayIndex++;
        });
    }

    $('.pa_captcha_img_data.loaded').removeClass('loaded');

    $(document).on('click', '.js-mailalert-add-custom', function (e) {
        e.preventDefault();
        func_pa.addNotificationCustom($(this).data('product'), $(this).data('product-attribute'));
    });

    $(document).on('click', '#pa_captcha_refesh', function (e) {
        e.preventDefault();
        func_pa.refreshCaptcha($(this).parents('.pa_captcha_img').find('.pa_captcha_img_data').removeClass('loaded'));
        $(this).parents('.pa-captcha-inf').find('input[name=pa_captcha]').val('');
    });

    $(document).on('keypress', '#pa_oos_customer_email', function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            func_pa.addNotification16();
        }
    });

    $('email-subscription .form[action*=blockEmailSubscription]').on('submit', function (event) {
        event.preventDefault();
        var psemailsubscriptionForm = $(this);
        if (typeof psemailsubscription_subscription === 'undefined') {
            return true;
        }
        $('.block_newsletter_alert').remove();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: psemailsubscription_subscription,
            cache: false,
            data: $(this).serialize(),
            success: function (data) {
                if (data.nw_error) {
                    psemailsubscriptionForm.prepend('<p class="alert alert-danger block_newsletter_alert">' + data.msg + '</p>');
                } else {
                    psemailsubscriptionForm.prepend('<p class="alert alert-success block_newsletter_alert">' + data.msg + '</p>');
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
        return false;
    });
});
document.addEventListener("DOMContentLoaded", function (event) {
    if ($('.forgot-password').length > 0 && $('.login.page_order').length > 0 && $('#login-form .login.page_order').length <= 0) {
        $('.forgot-password').before($('.login.page_order').clone(true).addClass('copy'));
        $('.login.page_order:not(.copy)').detach();
    } else if ($('.cart-grid-right .login.page_order').length > 0) {
        $('.cart-grid-right .login.page_order').detach();
    } else {
        $('.login.page_order:not(#login-form .login.page_order)').detach();
    }
    if ($('.pa_captcha_img_data').length > 1) {
        func_pa.loadCaptcha();
    }
});
$(document).ajaxComplete(function (event, xhr, options) {
    if ($('.quickview').length && typeof ets_captcha_load !== "undefined") {
        ets_captcha_load($('.quickview'));
        return;
    }

    var data = options && options.data;
    var dataStr = Array.isArray(data) ? data.join('&') : (typeof data === 'string' ? data : '');
    if (dataStr && $('body#authentication').length && dataStr.indexOf('SubmitCreate') !== -1) {
        var elapsed = 0, step = 150;
        var intervalSet = setInterval(function () {
            elapsed += step;
            if (!$('#account-creation_form').hasClass('g-loaded')) {
                if (PA_GOOGLE_V3_CAPTCHA_SITE_KEY) {
                    func_pa.loadReCaptchaV3($('#account-creation_form'));
                } else if (typeof ets_captcha_load !== "undefined") {
                    ets_captcha_load($('#account-creation_form').addClass('g-loaded'));
                }
            }
            if (elapsed > step * 10 || $('#account-creation_form').hasClass('g-loaded')) {
                clearInterval(intervalSet);
            }
        }, step);
        return;
    }

    if (dataStr && ($('body#order-opc').length || $('body#orderopc').length) && $('[name=g-recaptcha-response]').length && dataStr.indexOf('submitAccount') !== -1) {
        if (PA_GOOGLE_V3_CAPTCHA_SITE_KEY) {
            var $orderForm = $('form#new_account_form');
            if ($orderForm.length) {
                func_pa.loadReCaptchaV3($orderForm.removeClass('g-loaded'));
            }
        } else if (typeof grecaptcha !== "undefined") {
            func_pa.resetReCaptcha();
        }
        return;
    }

    if (dataStr && dataStr.indexOf('g-recaptcha-response') !== -1 && options.url && options.url.indexOf('subscription') !== -1) {
        var $form = $("form[action*=EmailSubscription]");
        if ($form.length && PA_GOOGLE_V3_CAPTCHA_SITE_KEY) {
            var g_captcha = $form.find('[id*=g-recaptcha-response-]');
            if (g_captcha.length && v3_render_clientID[g_captcha[0].id]) {
                var bodyId = ($('body').attr('id') || 'submit').replace(/(?=[^A-Za-z_])([^A-Za-z_])/g, '_');
                grecaptcha.reset(v3_render_clientID[g_captcha[0].id]);
                grecaptcha.execute(v3_render_clientID[g_captcha[0].id], { action: bodyId }).then(function (token) {
                    if (token) {
                        var existing = g_captcha.find('input[name=g-recaptcha-response]');
                        if (existing.length) {
                            existing.val(token);
                        } else {
                            g_captcha.append('<input type="hidden" name="g-recaptcha-response" value="' + token + '"/>');
                        }
                        $form.addClass('g-loaded');
                    }
                });
            }
        } else if (typeof grecaptcha !== "undefined") {
            func_pa.resetReCaptcha();
        }
        return;
    }

    if (xhr && xhr.responseJSON && xhr.responseJSON.product_additional_info) {
        var $img = $('div.js-mailalert img.pa_captcha_img_data');
        if ($img.length) {
            func_pa.refreshCaptcha($img.removeClass('loaded'));
        } else if (PA_GOOGLE_V3_CAPTCHA_SITE_KEY) {
            func_pa.loadReCaptchaV3($('form:not(.g-loaded)'));
        } else if (typeof ets_captcha_load !== "undefined") {
            ets_captcha_load(document.getElementsByTagName('form'));
        }
    }
});
$(document).ready(function () {
    if ($('input[name="captcha"]').closest('.form-group').length && $('.captcha_register').length) {
        var div_clone = $('input[name="captcha"]').closest('.form-group').clone();
        $('input[name="captcha"]').closest('.form-group').hide();
        $('.captcha_register.register').after(div_clone);
    }
});
