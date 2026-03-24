/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

var qazy = {};

qazy.qazy_image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTM4IDc5LjE1OTgyNCwgMjAxNi8wOS8xNC0wMTowOTowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTcgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkM2MDI0QTYyNjdCRDExRTc4NjM5Q0JDNDlDRTQ1Mzk3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkM2MDI0QTYzNjdCRDExRTc4NjM5Q0JDNDlDRTQ1Mzk3Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QzYwMjRBNjA2N0JEMTFFNzg2MzlDQkM0OUNFNDUzOTciIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QzYwMjRBNjE2N0JEMTFFNzg2MzlDQkM0OUNFNDUzOTciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7LAwgcAAAAEElEQVR42mL6//8/A0CAAQAJBgMA+A+HZAAAAABJRU5ErkJggg==";
qazy.view_elements = [];

$.fn.isInViewport = function() {
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();

    var viewportTop = $(window).scrollTop();
    var viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
};
																	  
qazy.reveal = function(){
	for(var count = 0; count < qazy.view_elements.length; count++)
	{
		var offsetParentTop = 0;
		var temp = qazy.view_elements[count];
		do
		{
			if(!isNaN(temp.offsetTop))
			{
				offsetParentTop += temp.offsetTop;
			}
		}while(temp = temp.offsetParent)
		
		var pageYOffset = window.pageYOffset;
		var viewportHeight = window.innerHeight;
		
		var offsetParentLeft = 0;
		var temp = qazy.view_elements[count];
		do
		{
			if(!isNaN(temp.offsetLeft))
			{
				offsetParentLeft += temp.offsetLeft;
			}
		}while(temp = temp.offsetParent);
		
		var pageXOffset = window.pageXOffset;
		var viewportWidth = window.innerWidth;
		
		if(offsetParentTop > pageYOffset && offsetParentTop < pageYOffset + viewportHeight && offsetParentLeft > pageXOffset && offsetParentLeft < pageXOffset + viewportWidth)
		{
			qazy.view_elements[count].src = qazy.view_elements[count].getAttribute("data-qazy-src");
			qazy.view_elements.splice(count, 1);
			count--;
		}
		else
		{

		}
	}
};
            
window.addEventListener("resize", qazy.reveal, false);
window.addEventListener("scroll", qazy.reveal, false);
            

function hasSomeParentTheClass(element, classname) {
    if (element.className.split(' ').indexOf(classname)>=0) return true;
    return element.parentNode && hasSomeParentTheClass(element.parentNode, classname);
}

//responsible for stopping img loading the image from server and also for displaying lazy loading image.
qazy.qazy_list_maker = function(){

	jQuery('.pb-left-column img, .MagicToolboxContainer img, #homeslider img, .bxslider img, .ls-wp-container img, .slides img, #splitslider-container img, .carousels-pack img, #carousel img, .carousel img, .slide img, .gallery img, .ls-slide img').attr('data-qazy','false');
	var elements = document.querySelectorAll("img:not([data-qazy='false'])");

	for(var count = 0; count < elements.length; count++)
	{
		var class_el = elements[count].className;
		var parent = jQuery(elements[count]).parent();
		var self_el = jQuery(elements[count]);

		if ( !class_el.includes('lazy') && (parent.attr('data-link') == null)  && (parent.attr('data-transition') == null)  && (!parent.hasClass('mz-lens')) && (!parent.hasClass('mz-figure')) && (!parent.hasClass('magictoolbox-selector')) && (!parent.hasClass('mz-thumb')) &&  (!parent.hasClass('slides'))  &&  (!parent.hasClass('slide')) &&  (self_el.attr('data-thumb') == null) )
		{
			qazy.view_elements.push(elements[count]);
			elements[count].setAttribute("data-qazy", "false");
			var source_url = elements[count].src;
			elements[count].setAttribute("data-qazy-src", source_url);
			elements[count].src = elements[count].getAttribute("data-qazy-placeholder") || qazy.qazy_image; 			
		}
	}
};
            
qazy.intervalObject = setInterval(function(){
	qazy.qazy_list_maker();
}, 50);

window.addEventListener("load", function() {
	clearInterval(qazy.intervalObject);
	qazy.qazy_list_maker();
	qazy.reveal();
}, false);


function check_for_bad_images()
{
	jQuery('img[data-qazy=false]').each(function(){
		var el = jQuery(this);
		if ($(this).isInViewport()) 
		{
			var el_src = jQuery(this).attr('src');
			if( el_src.indexOf("data:") >= 0)
			{
				el.attr('src', el.attr('data-qazy-src'));
			}
		}
	});
}
   
   
jQuery(document).ready(function(){
	setInterval(function(){ check_for_bad_images() }, 250); 
});

setInterval(function(){ qazy.reveal();  }, 200); 
jQuery(window).scroll();
