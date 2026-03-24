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

let prevWidth = null;
(function ($) {
	"use strict";
	var $window = $(window);
	var $document = $(document);
	var $mobile = 992;
	/*----------------------------------------------*/
	/* Loader  */
	/*----------------------------------------------*/
	$(window).on("load", function () {
		$('.page-loader').fadeOut('slow', function () {
			$(this).remove();
		});
	});
	/*----------------------------------------------*/
	/* main category drop dowm  */
	/*----------------------------------------------*/
	$(function () {
		// add Class for simple menu
		$('.menu  > ul > li > div.popover > ul:not(:has(ul))').addClass('simple-menu');
		// add class
		$('.menu  > ul > li ').each(function (indx) {
			if ($(this).find('ul').hasClass('simple-menu')) {
				$(this).addClass('normal-dropdown');
			}
		});
	});
	/*----------------------------------------------*/
	/* menu mobile  */
	/*----------------------------------------------*/
	$('#menu-icon i').on("click", function () {
		$(this).toggleClass('open-menu');
		var openmenu = $(this).hasClass('open-menu');
		if (openmenu) {
			if ($(window).width() < 992) {
				$(this).parents('body').css('overflow', 'hidden');
			}
			$(this).parents('body').find('#mobile_top_menu_wrapper').addClass('box-menu');
		} else {
			$(this).parents('body').css('overflow', 'visible');
			$(this).parents('body').find('#mobile_top_menu_wrapper').removeClass('box-menu');
		}
	});
	$('.menu-close').on("click", function () {
		$(this).parents('body').css('overflow', 'visible');
		$(this).parents('body').find('#mobile_top_menu_wrapper').removeClass('box-menu');
		$(this).parents('body').find('#menu-icon i').removeClass('open-menu');
	});
	$('.filter-close').on("click", function () {
		$(this).parents('body').find('#search_filters_wrapper').removeClass('filter-open');
		$(this).parents('body').find('#search_filters_wrapper').addClass('hidden-md-down');
		$(this).parents('body').css('overflow', 'visible');
	});
	$(function () {
		changeHTML();
	});
	$window.on('resize', function () {
		changeHTML();
	});

	function changeHTML() {
		if ($window.width() < parseInt($mobile, 10)) {
			if ($("#left-column").next().is('#content-wrapper')) {
				var elm = $("#content-wrapper");
				elm.insertBefore(elm.prev());
			}
		} else {
			if ($("#content-wrapper").next().is('#left-column')) {
				var elm = $("#left-column");
				elm.insertBefore(elm.prev());
			}
		}
	}
	/*----------------------------------------------*/
	// Update column+content in responsive 
	/*----------------------------------------------*/
	var responsiveflag = !1;
	$(document).ready(function () {
		responsiveResize();
		$(window).resize(responsiveResize);
	});

	function responsiveResize() {
		if ($(window).width() <= 991 && responsiveflag == !1) {
			updateColumnsAndContent('enable');
			responsiveflag = !0;
		} else if ($(window).width() >= 992) {
			updateColumnsAndContent('disable');
			responsiveflag = !1;
		}
	}

	function updateColumnsAndContent(status) {
		if (status == 'enable') {
			$("#left-column .products-section-title, #right-column .products-section-title, .block-blog h4").on('click', function (e) {
				$(this).toggleClass('active').next().stop().slideToggle('medium');
				e.preventDefault();
			});
			$("#left-column .products-section-title, #right-column .products-section-title, .block-blog h4").next().slideUp('fast');
		} else {
			$('#left-column .products-section-title, #right-column .products-section-title, .block-blog h4').removeClass('active').off().next().removeAttr('style').slideDown('fast');
		}
	}
	/*----------------------------------------------*/
	/* drop dowm  */
	/*----------------------------------------------*/
	$(function () {
			$(".header-top .search-box").on('click', function (e) {
				$('.mt-search-widget').addClass('search-box-open');
				event.stopPropagation();
				$('#mt_search_widget').insertAfter('.header-top .container');
				$(".cart_block, #_desktop_user_info .dropdown-menu, .language-selector .dropdown-menu, .currency-selector .dropdown-menu").slideUp("slow");
			});
			$(".search-close").on('click', function () {
				$('.mt-search-widget').removeClass('search-box-open');
			});
			$(".header-cart").on('click', function () {
				$(".cart_block").slideToggle("slow");
				$('.mt-search-widget').removeClass('search-box-open');
			});
			$(".currency-selector").on('click', function () {
				$(".cart_block").slideUp("slow");
				$('.mt-search-widget').removeClass('search-box-open');
			});
			$(".language-selector").on('click', function () {
				$(".cart_block").slideUp("slow");
				$('.mt-search-widget').removeClass('search-box-open');
			});
			$("#_desktop_user_info").on('click', function () {
				$(".cart_block").slideUp("slow");
				$('.mt-search-widget').removeClass('search-box-open');
			});
		});
	$(document).on('click', function () {
		$(".cart_block").slideUp('slow');
	});
	/*----------------------------------------------*/
	/* Change Display Type category  */
	/*----------------------------------------------*/
	$(document).on('click', '.pro_list_gird .grid-view', function (e) {
		e.preventDefault();
		$('#categories-product .product-miniature').removeClass('product-list col-xl-12 col-lg-12 col-md-12 col-sm-12').addClass('product-grid col-xl-4 col-lg-4 col-md-4 col-sm-6');
		$('.pro_list_gird').find('.grid-view').addClass('active');
		$('.pro_list_gird').find('.list-view').removeClass('active');
		$.cookie('nov_grid_list', 'grid', {
			expires: 1,
			path: '/'
		});
	});
	$(document).on('click', '.pro_list_gird .list-view', function (e) {
		e.preventDefault();
		$('#categories-product .product-miniature').removeClass('product-grid col-xl-4 col-lg-4 col-md-4 col-sm-6').addClass('product-list col-xl-12 col-lg-12 col-md-12 col-sm-12');
		$('.pro_list_gird').find('.grid-view').removeClass('active');
		$('.pro_list_gird').find('.list-view').addClass('active');
		$.cookie('nov_grid_list', 'list', {
			expires: 1,
			path: '/'
		});
	});
	/*----------------------------------------------*/
	/* Owl Slider  */
	/*----------------------------------------------*/
	$(function () {
		//Intro slider
		$('.mthomeslider').owlCarousel({
			autoplay: true,
			autoplayTimeout: $('.mthomeslider').data('interval'),
			autoplayHoverPause: $('.mthomeslider').data('pause'),
			singleItem: true,
			nav: true,
			dots: false,
			items: 1,
			loop: true,
			autoHeight: true,
			animateIn: 'fadeIn',
			animateOut: 'fadeOut',
			navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
			responsive: {
				0: {
					nav: false,
					dots: true
				},
				992: {
					nav: false,
					dots: true
				}
			},
			rtl: jQuery("body").hasClass("lang-rtl") ? true : false
		});
		$('.product-carousel').each(function () {
			if ($(this).closest('#left-column').length == 0 && $(this).closest('#right-column').length == 0) {
				$(this).addClass('owl-carousel owl-theme');
				const items = $(this).data('items') || 4;
				const sliderOptions = {
					loop: false,
					autoplay: false,
					autoplayHoverPause: true,
					singleItem: true,
					dots: false,
					nav: true,
					navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
					items: items,
					responsive: {
						0: {
							items: 1
						},
						320: {
							items: items - 2 > 1 ? items - 2 : 1
						},
						768: {
							items: items - 1 > 1 ? items - 1 : 1
						},
						1200: {
							items: items
						}
					}
				};
				if (jQuery("body").hasClass("lang-rtl")) sliderOptions['rtl'] = true;
				$(this).owlCarousel(sliderOptions);
			}
		});
		// testimonial
		$('#testimonial').owlCarousel({
			items: 1,
			loop: false,
			autoplay: false,
			autoplayHoverPause: true,
			singleItem: true,
			dots: true,
			nav: false,
			navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
			responsive: {
				0: {
					items: 1
				},
				320: {
					items: 1
				},
				576: {
					items: 1
				},
				1200: {
					items: 1
				}
			},
			rtl: jQuery("body").hasClass("lang-rtl") ? true : false
		});
		//blog Item 3
		$('.blog-item-3').owlCarousel({
			items: 3,
			loop: false,
			autoplay: false,
			autoplayHoverPause: true,
			singleItem: true,
			dots: false,
			nav: true,
			navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
			responsive: {
				0: {
					items: 1
				},
				320: {
					items: 1
				},
				768: {
					items: 2
				},
				1200: {
					items: 3
				}
			},
			rtl: jQuery("body").hasClass("lang-rtl") ? true : false
		});
		//brand
		$('.brand-items').owlCarousel({
			items: 6,
			loop: false,
			margin: 20,
			autoplay: true,
			autoplayHoverPause: true,
			singleItem: true,
			dots: false,
			nav: false,
			navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
			responsive: {
				0: {
					items: 1
				},
				320: {
					items: 2
				},
				480: {
					items: 3
				},
				768: {
					items: 4
				},
				992: {
					items: 5
				},
				1200: {
					items: 6
				}
			},
			rtl: jQuery("body").hasClass("lang-rtl") ? true : false
		});
		productPageInit();
	});

	function productPageInit() {
		//product zoom
		$('#product .product-zoom').zoom();
			// additional silder
			setTimeout(function () {
				$('.product-detail .mask.scroll .product-images.slick-carousel').slick({
					infinite: false,
					vertical: true,
					verticalSwiping: true,
					slidesToShow: 4,
					slidesToScroll: 1,
					prevArrow: '<button class="slick-prev" aria-label="Previous" type="button"><i class="fa fa-long-arrow-up"></i></button>',
					nextArrow: '<button class="slick-next" aria-label="Next" type="button"><i class="fa fa-long-arrow-down"></i></button>',
					responsive: [{
						breakpoint: 1200,
						settings: {
							slidesToShow: 3
						}
					}, {
						breakpoint: 992,
						settings: {
							slidesToShow: 2
						}
					}, {
						breakpoint: 768,
						settings: {
							slidesToShow: 3
						}
					}, {
						breakpoint: 426,
						settings: {
							slidesToShow: 2
							// You can unslick at a given breakpoint now by adding:
							// settings: "unslick"
							// instead of a settings object
						} }]
				});
		}, 0);
	}
	prestashop.on('updatedProduct', function () {
		productPageInit();
	});

	/*----------------------------------------------*/
	/* quantity seter*/
	/*----------------------------------------------*/
	$(document).on('click', '.plus, .minus', function (e) {
		e.preventDefault();
		var parent = $(this).parents('.product-btn-quantity');
		var quantity = parent.find('[name="quantity"]');
		var val = quantity.val();
		if ($(this).hasClass('plus')) {
			val = parseInt(val) + 1;
		} else {
			if (val == 1) {
				val = 1;
			} else {
				val = val >= 1 ? parseInt(val) - 1 : 0;
			}
		}
		quantity.val(val);
		quantity.trigger("change");
		return false;
	});
	/*----------------------------------------------*/
	/* ajax search */
	/*----------------------------------------------*/
	var timer;
	$("#input_search").keyup(function (e) {
		e.stopPropagation();
		clearTimeout(timer);
		timer = setTimeout(function () {
			var search_key = $("#input_search").val();
			$.ajax({
				type: 'GET',
				url: prestashop['urls']['base_url'] + 'modules/mt_search/mt_search-ajax.php',
				headers: {
					"cache-control": "no-cache"
				},
				async: true,
				data: 'search_key=' + search_key,
				success: function success(data) {
					$('#search_popup').innerHTML = data;
				}
			}).done(function (msg) {
				$("#search_popup").html(msg);
			});
		}, 1000);
	});
	/*----------------------------------------------*/
	/* Newsletter */
	/*----------------------------------------------*/
	$(function () {
		if ($('.newsletter-popup-wrapper').length > 0) {
			//get popup cookie        
			if ($.cookie("newsletterpopup") != 'true') {
				$.magnificPopup.open({
					items: {
						src: '.newsletter-popup-wrapper',
						type: 'inline'
					},
					removalDelay: 500, //delay removal by X to allow out-animation
					callbacks: {
						beforeOpen: function () {},
						open: function () {}
					}
				});
			}
		}
	});
	$(function () {
		// Back to top
		backToTop();
		setPageTitle();
		hoverzoom();
		productzoom();
		headerfix();
	});
	$window.on('resize', function () {
		hoverzoom();
		productzoom();
		headerfix();
	});
	$(window).scroll(function (){
		headerfix();
	});
	/*----------------------------------------------*/
	//  parallax /
	/*----------------------------------------------*/
	$(function () {
		if ($(window).width() > 767) {
			var parallax = document.querySelectorAll(".parallax"),
			    speed = 0.5;
			window.onscroll = function () {
				[].slice.call(parallax).forEach(function (el, i) {
					var windowYOffset = window.pageYOffset / 1.5,
					    elBackgrounPos = "50% " + windowYOffset * speed + "px";
					el.style.backgroundPosition = elBackgrounPos;
				});
			};
		}
	});
	/*----------------------------------------------*/
	/* More category */
	/*----------------------------------------------*/
	$(document).ready(function () {
		let moreText = $('#_desktop_top_menu').data('more')
		if (!moreText) {
			'More'
		}
		if ($window.width() >= parseInt($mobile, 10)) {
			var max_elem = 4;
			if ($(window).width() < 1200) {
				max_elem = 2;
			}
			var items = $('#top-menu > li:not(.link)');
			var surplus = items.slice(max_elem, items.length);
			surplus.wrapAll('<li class="more_menu"><div class="popover sub-menu js-sub-menu">');
			$('.more_menu').prepend('<a href="#" id="more_asse" class="dropdown-item" data-depth="0">' + moreText + '</a>');
		} else {
			$("#top-menu > li:not(.link)").removeClass(".more_menu");
		}
	});
	/*----------------------------------------------*/
	/* header fixed  */
	/*----------------------------------------------*/
	function headerfix(){
		if ($("body").is('.m-header-fixed')) {
			if ($window.width() >= parseInt(992,10)) {
				if ($('body#index').length) {
					if ($window.scrollTop() >= 150) {
						$(".header-top").addClass("header-fixed");
					} else {
						$(".header-top").removeClass("header-fixed");
					}
					prevWidth = $(window).width();
				}
			}
			else {
				$(".header-top").removeClass("header-fixed");
			}
		} else {
			$(".header-top").removeClass("header-fixed");
		}
	}
	/*----------------------------------------------*/
	/* responsive zoom*/
	/*----------------------------------------------*/
	function productzoom() {
		if ($(window).width() >= 992) {
			$('#product .product-zoom').zoom();
		} else {
			$('#product .product-zoom').trigger('zoom.destroy');
		}
	}
	/*----------------------------------------------*/
	/* product comments */
	/*----------------------------------------------*/
	$(document).ready(function () {
		$("#product_comments_block_extra ul.comments_advices a").on('click', function () {
			$('*[class^="tab-pane"]').removeClass('active');
			$('*[class^="tab-pane"]').removeClass('in');
			$('div#tab-review').addClass('active');
			$('div#tab-review').addClass('in');
			$('ul.nav-tabs a[href^="#"]').removeClass('active');
			$('a[href="#tab-review"]').addClass('active');
		});
	});
	$(window).on("load", function () {
		/* Page Scroll to id fn call */
		$("#product_comments_block_extra ul.comments_advices a.reviews ").mPageScroll2id({
			highlightSelector: "#product_comments_block_extra ul.comments_advices a.reviews ",
			offset: 100
		});
	});
	/*----------------------------------------------*/
	/* PageTitle */
	/*----------------------------------------------*/
	function setPageTitle() {
		const heading = document.title;
		$('.breadcrumb h6, #content h1.page_title').html(heading);
	}
	/*----------------------------------------------*/
	/* Hover zoom button */
	/*----------------------------------------------*/
	function hoverzoom() {
		setTimeout(function () {
			if ($(window).width() > 767) {
				$('.hover-zoom').magnificPopup({
					type: 'image',
					gallery: {
						enabled: true,
						arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"><i class="fa fa-angle-%dir% fa-4x"></i></button>'
					}
				});
			}
		}, 1000);
	}
	/*----------------------------------------------*/
	/* Back to top function */
	/*----------------------------------------------*/
	function backToTop() {
		//Check to see if the window is top if not then display button
		$(window).scroll(function () {
			if ($(this).scrollTop() > 250) {
				$('.scrollToTop').fadeIn();
			} else {
				$('.scrollToTop').fadeOut();
			}
		});
		//Click event to scroll to top
		$('.scrollToTop').on('click', function () {
			$('html, body').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	}
})(jQuery);