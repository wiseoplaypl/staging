/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/

(function($) {
	$(document).ready(function() {
		$('body').append('<div id="autoResizeTextareaCopy" style="box-sizing: border-box; -moz-box-sizing: border-box;  -ms-box-sizing: border-box; -webkit-box-sizing: border-box; visibility: hidden;"></div>');
		var $copy = $('#autoResizeTextareaCopy');
		
		function autoSize($textarea, options) { 
			// The copy must have the same padding, the same dimentions and the same police than the original.
			$copy.css({
				fontFamily:     $textarea.css('fontFamily'),
				fontSize:       $textarea.css('fontSize'),
				padding:        $textarea.css('padding'),
				paddingLeft:    $textarea.css('paddingLeft'),
				paddingRight:   $textarea.css('paddingRight'),
				paddingTop:     $textarea.css('paddingTop'), 
				paddingBottom:  $textarea.css('paddingBottom'), 
				width:          $textarea.css('width')
			});
			$textarea.css('overflow', 'hidden');
			
			// Copy textarea contents; browser will calculate correct height of copy.
			var text = $textarea.val().replace(/\n/g, '<br/>');
			$copy.html(text + '<br />');
			
			// Then, we get the height of the copy and we apply it to the textarea.
			var newHeight = $copy.css('height');
			$copy.html(''); // We do this because otherwise, a large void appears in the page if the textarea has a high height.
			if(parseInt(newHeight) != 0) {
				if((options.maxHeight != null && parseInt(newHeight) < parseInt(options.maxHeight)) || options.maxHeight == null) {
					if(options.animate.enabled) {
						$textarea.animate({ 
							height: newHeight 
						}, {
							duration: options.animate.duration,
							complete: options.animate.complete,
							step:     options.animate.step,
							queue:    false
						});
					}
					else {
						$textarea.css('height', newHeight);
					}
					
					$textarea.css('overflow-y', 'hidden');
				}
				else {
					$textarea.css('overflow-y', 'scroll');
				}
			}
		}
		
		
	});
})(jQuery);