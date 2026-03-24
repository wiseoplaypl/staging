/**
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2020 Hendrik Masson
 *  @license   Tous droits réservés
*/
var caracter_colors = [];
//caracter_colors['name'] = ['#19395a','#305761','#85be82','#282a29','#6c8380','#a20100','#45052c','#83af8c','#295c1d','#ffffff','#955671', '#1d4c59'];
//caracter_colors['name'] = ['#19395a','#305761','#85be82','#282a29','#6c8380','#a20100','#45052c','#83af8c','#295c1d','#ffffff','#955671', '#1d4c59'];
caracter_colors['body'] = ['#efdccf', '#f2d5c7', '#edcfb8', '#e0c0a7', '#cfac8e', '#c09b7c', '#ac8a6c', '#9c795a', '#826245'];
caracter_colors['hair'] = ['#ccb27f', '#ba9845', '#c7874b', '#cc792d', '#68523a', '#5c3629', '#302319', '#c4c4c4', '#939494'];
caracter_colors['baby-body'] = ['#efdccf', '#f2d5c7', '#edcfb8', '#e0c0a7', '#cfac8e', '#c09b7c', '#ac8a6c', '#9c795a', '#826245'];
caracter_colors['baby-hair'] = ['#ccb27f', '#ba9845', '#c7874b', '#cc792d', '#68523a', '#5c3629', '#302319', '#c4c4c4', '#939494'];
caracter_colors['beard'] = ['#ceb865','#b99805','#d4741c','#df5b00','#594422','#4f201c','#1e1610','#c5d1d1','#8aa09e'];


$(document).on('click', '.addCaracter', function(e) {
  e.preventDefault();
  var group = $(this).attr('data-group');
  zindex = $(this).attr('data-zindex');
  target = $(this).attr('data-id');
  view = $(this).attr('data-view');
  dragdrop = $(this).attr('data-dragdrop');
  resizeable = $(this).attr('data-resizeable');
  rotateable = $(this).attr('data-rotateable');
  price = $(this).attr('data-price');
  blend = $(this).attr('data-blend');
  maxlenght = $(this).attr('data-max');
  minItems = parseInt($(this).attr('data-min-item'));
  maxItems = parseInt($(this).attr('data-max-item'));
  $(this).parent().find('.max-limit').hide();

  others = $(this).parent().find('.designer-item');
  number = others.length + 1;

	  clonedUpload = $(this).parent().find('.ndkhiddenuploadfile').clone();
	  clonedUpload.removeClass('ndkhiddenuploadfile').addClass('imgItem');
	  clonedUpload.find('.img-value').attr('data-group', group + '-' + number);

	  clonedLibrary = $(this).parent().find('.ndkhiddenimglibrary').clone();
	  clonedLibrary.removeClass('ndkhiddenimglibrary').addClass('imgItem').attr('id', 'main-' + group + '-' + number);
	  clonedLibrary.find('.img-value').attr('data-group', group + '-' + number);
	  clonedLibrary.find('.img-item-row').attr('data-group', group + '-' + number);
	  clonedLibrary.find('.ndktextarea').attr('data-group',  group).attr('id', 'text-item-'+ group + '-' + parseInt(number)).attr('data-number', parseInt(number));

	  $(this).parent().find('.designer-item').slideUp();
	  del_btn = '<a href="#" class="remove-item-block"  data-group="' + group + '" data-group-target="' + group + '-' + parseInt(number) + '"><span><i class="material-icons">delete</i></span></a>';

	  imgItem = '<div id="designer-item-container-'+group+'-'+parseInt(number)+'" class="designer-item-container"><h4 id="toggler-' + group + '-' + parseInt(number) + '"  data-target="#item-' + group + '-' + parseInt(number) + '" class="itemToggler"><span class="item-name">' + underwearText + '<span> </span>' + parseInt(number) + '</span>'+del_btn+'</h4><div id="item-' + group + '-' + parseInt(number) + '" class="designer-item clearfix clear" data-number="' + parseInt(number) + '">' +  clonedLibrary.html();

	  if ($(this).parent().find('.orientation_selection').length > 0) {
		orientable_block = $(this).parent().find('.orientation_selection').html();
		imgItem += '<div data-group-target="' + group + '-' + parseInt(number) + '" class="clear clearfix orientation_selection">' + orientable_block + '</div>';
	  }
	  imgItem += '</div></div>';
	 // imgItem += '<div class="ndk_selector"><ul class="colorize_svg" data-group="'+group+'" data-type="body"></ul></div>';
	  imgItem = $(this).parent().find('.itemsBlock').append(imgItem);
	  scrollToNdk(imgItem, 800);


	  //$('#ndkcsfield_' + group).val(designerValue).trigger('keyup');

	  $('.ndk_selector').each(function() {
		$(this).setNdkSelector();
	  });

	  //window['fieldColors_'+group] = caracter_text_colors;
	  initText($('#text-item-'+group+'-'+number));
	  $('#text-item-'+group+'-'+number).find('.noborder').val(underwearText + ' ' +number).trigger('keyup');
	  //makeSortable();
	  setTypesGroups('#item-'+ group + '-' + parseInt(number));
	  setTimeout(function(){
		  $('#item-'+ group + '-' + parseInt(number)+' .group-type:visible:eq(0) .group-title:eq(0):not(.active)').trigger('click');
	  }, 500)
	  imgItem.find('.itemToggler').trigger('click');
	checkMultiFieldLimits(group)


})

$(document).on('click', '.type_colorize_svg li', function(){
		  root = $(this).parent().parent().parent();
		  color = $(this).find('span').text();
		  $(this).parent().find('li').removeClass('selected');
		  $(this).addClass('selected');
		  $(this).parent().parent().find('.index-value').html($(this).html());

			root.find('span.colorSelector').html(color);
		  root.find('.texteditor, .noborder').attr('data-texture', '');
		  root.find('span.colorSelector').css('background', color).html(color);
		  if(color.indexOf('url') >= 0){
			   root.find('.texteditor, .noborder').attr('data-texture', color);
			   root.find('.texteditor').css('color', settings['initialColor']);
		  }
		  else
			   root.find('.texteditor').css('color', color);

		   selectedColor = color;
		   if(typeof(root.find('.texteditor').css('color')) != 'undefined')
				root.find('.textarea').css('background', getOpositeColor(root.find('.texteditor').css('color')) ).trigger('keyup');



		  root.find('.replaced-svg').each(function(){
			$(this).find("path:not([fill='#332d29']):not([fill='#47312d']):not([fill='#2d1a17']):not([fill='#cdd2d3'])").attr('fill', '').css('fill', '');
			$(this).attr('fill', color);
			var target = $(this).parent();
			if ($(this).parent().hasClass('selected-svg')) {
			  $.when(
				target.parent().parent().trigger('click')
			  ).done(function() {
				  target.trigger('click');
			  });
			}
		});


});

function composeCaracter(visu, group, view, zindex, dragdrop, resizeable, rotateable, width, height, type, coloreffect, background, category)
{
		rootGroup = group.split('-')[0];

		if (typeof(composeCaracter_Override) == 'function') {
			return composeCaracter_Override(visu, group, view, zindex, dragdrop, resizeable, rotateable, width, height, type, coloreffect);
		}
		background = '';
		target = '#caracter-container-'+group;

		if(typeof(visu) =='object')
		{
			visu = visu.prop('outerHTML');
		}
		else
			visu = visu;
		
		$('#caracter-container-'+group).find('.composition_element-'+category).remove();
		
		if($(target).length == 0)
		{
			others = $('.zone_limit[data-group='+rootGroup+']').find('.main-caracter-container').length;
			my_visu = '<div class="caracter-container" id="caracter-container-'+group+'">'+visu+'</div>';
			designCompo(my_visu, group, view, zindex, dragdrop,resizeable, rotateable, 0, 0, type, coloreffect, background, false, 'main-caracter-container', category);
		}
		else{
			designCompo(visu, group, view, zindex, dragdrop,resizeable, rotateable, 0, 0, type, coloreffect, background, target, category);
		}
		console.log(group)
		all = $('.zone_limit[data-group='+rootGroup+']').find('.main-caracter-container').length;
		$i = 0;
		$('.zone_limit[data-group='+rootGroup+']').find('.main-caracter-container').each(function(){
			$(this).css('left', (100/all)*$i+'%').css('max-width', 100/all+'%');
			$i++;
		})
		
}






function setColorSelector(type, target)
{
	target = target || 'body';
	if(typeof(caracter_colors[type]) !='undefined')
	{
		if(caracter_colors[type].length > 0)
		{
			$(target).find('.group-type[data-group-type='+type+']').find('.type_colorize_svg').remove();
	  myColors = caracter_colors[type];
			colorSelector = $('<ul class="type_colorize_svg clear clearfix" data-group="'+group+'" data-type="'+type+'"></ul>').appendTo($(target).find('.group-type[data-group-type='+type+'] .groupList'));


			for (var i = 0; i < myColors.length; i++) {
				  var item = $('<li '+(i == 0 ? 'class="initial_color"' : '')+'><span data-color="' + myColors[i] + '" style="background:' + myColors[i] + ';">' + myColors[i] + '</span></li>').appendTo(colorSelector);

			}
		}
	}
}

$(document).on('click', '.img-item-row .text-select-node', function(){
	$(this).parent().find('.img-value').trigger('click')
	$('.filterTag span').removeClass('select-item-kln')
	$(this).addClass('select-item-kln');
})


$(document).on('click', '.group-title', function(){
  target = $(this).parent().find('.groupList');
  groupBlock = $(this).parent().parent().parent();
  $('.group-title').removeClass('active');
  groupBlock.find('.groupList').not(target).slideUp();
  $(this).toggleClass('active');
  target.slideToggle();
  target.trigger('ndk_opened')
})

$(document).on('click', "[data-type='model']", function(){
  $('#visual_'+$(this).attr('data-group')).remove();
  setTimeout(function(){
	  $('.group-type[data-group-type="size"]:visible .group-title:eq(0):not(.active)').trigger('click');
  }, 500)
});

$(document).on('typeValueSet', '.img-value-model', function(e){
	var el = $(this),
	settedValue = '',
	group = el.attr('data-group'),
	type = el.parent().attr('data-type');
	settedValue = el.attr('title').toLowerCase();
	
	if(typeof(settedValue) != 'undefined')
	{
		if(settedValue.indexOf('blanc') > -1)
			setTextColor(group, '#DE0B15');
		else
			setTextColor(group, '#FFFFFF');
	}
});

function setTextColor(group, color)
{
	var openColor = $('#textZone'+group+' .colorSelector').trigger('click');
	$.when(openColor).done(function(){
		  $('#textZone'+group+' .fontColorSelectUl li:eq(0)').text(color).css('background', color).trigger('click');
		  console.log(color);
		  $('#textZone'+group+' .texteditor').css('color', color)
	});
}

$(document).on('click', "[data-type='size']", function(){
  setTimeout(function(){
	  $('.group-type[data-group-type="zone"]:visible .group-title:eq(0):not(.active)').trigger('click');
  }, 500)
});

$(document).on('click', "[data-type='zone']", function(){
  setTimeout(function(){
	  $('.group-type[data-group-type="name"]:visible .group-title:eq(0):not(.active)').trigger('click');
  }, 500)
});



$(document).on('typesSet', '.designer-item', function(){
	me = $(this);
	me.find('[data-group-type="name"]').detach().appendTo($(this));
	me.find('.group-type').each(function(){
		group = $(this).parent().attr('id').replace('item-', '').split('-')[0];
		if($(this).find('.group_value_input').length < 1)
		  $(this).append('<input data-message="'+fillText+'" type="hidden" class="required_field group_value_input" data-group="'+group+'" name=""/>');
	  })
});


$(document).on('keyup', "[data-group-type='name'] .noborder", function(){
   group = parseInt($(this).parent().parent().parent().parent().find('.ndktextarea:eq(0)').attr('data-group'));
   number = $(this).parent().parent().parent().parent().find('.ndktextarea').attr('data-number');
   my_text = $(this).val();
   $('#item-' + group + '-' + number + ' .group-type[data-group-type="name"] .group-value').html(' : ' +my_text); 
   $('#item-' + group + '-' + number + ' .group-type[data-group-type="name"] .group_value_input').val(my_text);   
  //getMultiFieldDetails(group);
});

$(document).on('click', '.delTypeButton', function(e){
  e.preventDefault();
  me = $(this);
  group = me.attr('data-group');
  type = me.attr('data-type');
  removeTypeElement(group, type);
  if(type == 'with-baby')
  {
	  removeTypeElement(group, 'baby-body');
	  removeTypeElement(group, 'baby-hair');
  }
})


function removeTypeElement(group, type)
{
  $('[data-type='+type+'][data-group='+group+'] .selected-svg').removeClass('selected-svg');
  $('#caracter-container-'+group).find('.composition_element-'+type).remove();
}