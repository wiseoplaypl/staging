/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

var ajax_action_path = window.location.href.split('#')[0]+'&ajax=1',
	bulk_click = [],
	blockAjax = false;

$(document).ready(function() {
	at.init();
});

var at = {
	blockAjax: false,
	bulkClick: [],
	defineBasicVars: function() {
		at.$ct = $('select[name="at_ct"]');
		at.$dynamicList = $('.dynamic-list');
		at.$langFrom = $('select.from-lang');
	},
	init: function() {
		at.defineBasicVars();
		at.bindAPIevents();
		at.bindBasicEvents();
		$(window).on('resize', function(){
			at.setWidthForResourceFields();
		});
	},
	bindBasicEvents: function() {
		$(document).on('click', 'a[href="#"]', function(e){
			e.preventDefault();
		}).on('click', '.go-to-page', function(e){
			at.updateList($(this).data('page'));
		}).on('change', 'select.update-list, input.update-list', function(e){
			at.updateList(1);
		});
		at.$ct.on('change', function(){
			var ct = $(this).val();
			$('.special-param, .special-option').addClass('hidden').filter('.'+ct).removeClass('hidden');
			$(this).addClass('update-fields');
			$('.order-by').val('id').change();
			at.multiselect.init($('select.order-by, select.special-param.'+ct+''));
		});
		if (at.validateSelectedProvider(true)) {
			at.$ct.change();
		}
		at.$langFrom.on('change', function() {
			var from = $(this).val(),
				$toSelect = $('select.to-lang'),
				$optionsOrig = $toSelect.find('option')
				all = $optionsOrig.filter(':selected').length == $optionsOrig.not('.hidden').length;
			$optionsOrig.removeClass('hidden').filter('[value="'+from+'"]').addClass('hidden').prop('selected', false);
			var $optionsNew = $toSelect.find('option').not('.hidden');
			if (all) {
				$optionsNew.prop('selected', true);
			} else if (!$optionsNew.filter('option:selected').length) {
				$optionsNew.first().prop('selected', true);
			}
			$(this).addClass('update-lang-from');
			at.multiselect.init($toSelect);
		});

		$('.dont_overwrite_existing').on('change', function() {
			var overwrite = $(this).prop('checked') ? 0 : 1,
				params = 'ajax_action=saveOverwriteOption&overwrite='+overwrite;
			$.ajax({url: ajax_action_path+'&'+params});
		});

		$('.order-way-label').on('click', function(){
			$(this).removeClass('active').siblings('.order-way-label').addClass('active');
			$('input.order-way').val($('.order-way-label.active').data('way')).change();
		});

		at.$dynamicList.on('change', '.item-checkbox', function() {
			if ($(this).hasClass('blocked')) {
				at.toggleCheckedProp($(this));
			} else {
				at.updateCheckedNum();
			}
		}).on('click', '.translateCurrent', function() {
			if (at.blockAjax) {
				return;
			}
			$(this).toggleClass('loading');
			if ($(this).hasClass('loading')) {
				var content_type = at.$ct.val(),
					identifier = $(this).closest('tr').data('identifier'),
					from = $('select[name="at_from"]').val(),
					to = $('select[name="at_to[]"]').val(),
					fields = $('select[name="fields[]"]').val();
				at.translate(content_type, identifier, from, to, fields);
			}
		}).on('change', ' .checkAllItems', function(e) {
			if ($(this).hasClass('blocked')) {
				at.toggleCheckedProp($(this));
			} else {
				at.getItemCheckboxes().prop('checked', $(this).prop('checked'));
				at.updateCheckedNum();
			}
		}).on('click', '.translateSelected', function() {
			$(this).toggleClass('loading');
			at.bulkClick = [];
			$(this).find('.processed-num').html('0');
			if ($(this).hasClass('loading')) {
				$('.checkAllItems, .item-checkbox').addClass('blocked');
				at.getItemCheckboxes(':checked').each(function() {
					at.bulkClick.push($(this).closest('tr').find('.translateCurrent'));
				});
				if (at.bulkClick.length) {
					at.bulkClick[0].click();
				} else {
					$(this).click();
				}
			} else {
				$('.checkAllItems, .item-checkbox').removeClass('blocked');
			}
		});
		at.multiselect.init();
	},
	toggleCheckedProp: function ($el) {
		$el.prop('checked', !$el.prop('checked'));
	},
	updateCheckedNum: function() {
		var num = at.getItemCheckboxes(':checked').length, ready = num > 0;
		$('.translateSelected').toggleClass('disabled', !ready).find('.checked-num').html(num);
	},
	getItemCheckboxes: function(filterItems) {
		var $items = at.$dynamicList.find('.item-checkbox');
		if (filterItems) {
			$items = $items.filter(filterItems);
		}
		return $items;
	},
	bindAPIevents: function() {
		$('.toggleAPISettings, .closeModal').on('click', function() {
			$('body').toggleClass('show-api-settings');
		});
		$('select[name="provider"]').on('change', function() {
			var provider = $(this).val();
			$('.api-credentials-form, .api-info').addClass('hidden')
			.filter('[data-provider="'+provider+'"]').removeClass('hidden');
		}).change();
		$('.how-to-label').on('click', function() {
			$(this).parent().toggleClass('show-how-to');
		})
		$('.saveAPI').on('click', function() {
			var $btn = $(this),
				provider = at.getCurrentProvider(),
				$providerForm = $('.api-credentials-form[data-provider="'+provider+'"]'),
				credentials = $providerForm.serialize(),
				data = {ajax_action: 'saveAPI', provider: provider, credentials: credentials},
				response = function(r) {
					if ('saved' in r && r.saved) {
						$('body').removeClass('show-api-settings');
						at.updateStats(r);
						$('.selected-provider-name').html($('select[name="provider"]').find('option:selected').text());
						$btn.removeClass('loading');
						at.$ct.addClass('update-silently').change();
						setTimeout(function() {
							$('.selected-provider, .provider-stats').addClass('just-saved');
							setTimeout(function() {
								$('.selected-provider, .provider-stats').removeClass('just-saved');
							}, 1000);
						}, 10);
						if ($('.at-container').hasClass('no-provider')) {
							$('.at-container').removeClass('no-provider');
						}
					}
				};
			$btn.addClass('loading');
			$('.api-settings-modal').find('.thrown-error').remove();
			at.ajaxRequest(data, response, $('.api-settings-modal'));
		});
	},
	validateSelectedProvider: function(callPopup) {
		if ($('.at-container').hasClass('no-provider')) {
			if (callPopup) {
				$('body').addClass('show-api-settings');
			}
			return false;
		}
		return true;
	},
	updateStats: function(r) {
		if ('api_stats_data' in r) {
			$('.selected-stats-d').html(r.api_stats_data.day);
			$('.selected-stats-m').html(r.api_stats_data.month);
		}
	},
	getCurrentProvider: function() {
		return $('.api-provider').find('select').val();
	},
	updateList: function(page) {
		if (!at.validateSelectedProvider(false)) {
			return;
		}
		var data = 'ajax_action=callResourseList&'+$('.resource-settings-form').serialize()+
			'&'+$('.list-params').serialize()+'&'+$('.list-pagination').serialize()+'&p='+page,
			response = function(r) {
				if ('list_html' in r) {
					at.$dynamicList.html(utf8_decode(r.list_html));
					at.multiselect.init();
				}
				if ('translatable_fields' in r) {
					at.updateResourceFields(r);
				}
			};
		if (at.$ct.hasClass('update-fields')) {
			data += '&updateFields=1';
			at.$ct.removeClass('update-fields');
		}
		if (at.$langFrom.hasClass('update-lang-from')) {
			data += '&updateLangFrom='+at.$langFrom.val();
			at.$langFrom.removeClass('update-lang-from');
		}
		if (at.$ct.hasClass('update-silently')) {
			at.$ct.removeClass('update-silently');
		} else {
			at.$dynamicList.find('table').addClass('loading');
		}
		at.ajaxRequest(data, response, at.$dynamicList);
	},
	multiselect: {
		init: function($els) {
			$els = $els ? $els : $('.at-panel').find('select').not('.wrapped');
			$els.each(function() {
				var $select = $(this);
				if ($select.hasClass('wrapped')) {
					at.multiselect.unwrap($select);
				}
				var parentClass = $.trim('multiselect'+($select.attr('multiple') ? ' multiple' : '')
					+($select.attr('class') ? ' '+$select.attr('class') : '')),
					minWidth = $select.outerWidth() + 10,
					ids = {show: $select.hasClass('show-ids'), max: []},
					html = '<div class="current-option">--</div><div class="available-options">',
					$options = $select.find('option').not('.hidden');
				if ($select.data('qs') && $options.length > $select.data('qs')) {
					html += '<div class="qs-wrapper">';
					html += '<input type="text" class="qs" placeholder="'+at.txt.quick_search+'">';
					html += '</div>';
				}
				html += '<div class="available-options-list">';
				$options.each(function(i) {
					var val = $(this).val();
					if (!i && $select.hasClass('first-empty-option')) {
						$select.data('empty', $(this).text());
					} else {
						html += '<label class="option-label'+($(this).is(':selected') ? ' selected' : '')+'"';
						html += ' data-val="'+val+'">';
						if (ids.show) {
							html += '<span class="option-id" data-id="'+val+'">'+val+'</span> ';
							ids.max.push(val);
						}
						html += '<span class="option-name">'+$(this).text()+'</span></label>';
					}
				});
				html += '</div>';
				if ($select.data('all')) {
					html += '<a href="#" class="select-all">'+at.txt.select_all+'</a>';
					minWidth = minWidth < 90 ? 90 : minWidth;
				}
				html += '</div></div>';
				minWidth = minWidth > 300 ? 300 : minWidth;
				$select.wrap('<div class="'+parentClass+'" style="min-width:'+minWidth+'px"></div>')
				.addClass('wrapped').before(html);
				if (ids.show) {
					var $parent = $select.parent(),
						$ids = $parent.find('.option-id');
					$parent.addClass('show-available-options'); // open
					var w = $ids.filter('[data-id="'+Math.max.apply(Math, ids.max)+'"]').width();
					$ids.css({'width': w+'px'});
					$parent.css({'min-width': (minWidth+w)+'px'}).removeClass('show-available-options'); // close
				}
			});
			$('.multiselect').not('.ready').each(function(){
				var $el = $(this),
					$options = $el.find('.option-label');
				$el.on('click', '.current-option', function() {
					if (!$el.hasClass('show-available-options')) {
						$el.addClass('show-available-options');
						setTimeout(function() {
							at.multiselect.onClickOutSide(
								$el.find('.available-options'),
								function() {
									$el.removeClass('show-available-options');
								}
							);
						}, 10);
						$el.off('mouseenter mouseleave').on('mouseenter', function() {
							clearTimeout(at.multiselect.mouseEventsTimer);
						}).on('mouseleave', function() {
							at.multiselect.mouseEventsTimer = setTimeout(function() {
								if ($el.hasClass('show-available-options')) {
									$el.find('.current-option').click(); // autohide available options
								}
								$el.off('mouseenter mouseleave');
							}, 1000);
						});
					}
				}).on('click', '.option-label', function() {
					var $label = $(this);
					if ($el.hasClass('multiple')) {
						$label.toggleClass('selected');
						if (!$el.find('.option-label.selected').length &&
							!$el.hasClass('first-empty-option')) {
							$label.addClass('selected');
						}
					} else {
						$label.addClass('selected').siblings().removeClass('selected');
					}
					at.multiselect.updateSelectedValue($el, true);
				}).on('click', '.select-all', function() {
					$options.addClass('selected');
					at.multiselect.updateSelectedValue($el, true);
				}).on('keyup', '.qs', function() {
					var value = $(this).val();
					clearTimeout(at.multiselect.quickSearchTimer);
					at.multiselect.quickSearchTimer = setTimeout(function() {
						$options.removeClass('qs-hidden');
						// search for IDs starting from 1 characters, other strings - starting from 3
						if (!isNaN(value) || value.length > 2) {
							$options.each(function() {
								if ($(this).text().toLowerCase().indexOf(value.toLowerCase()) === -1) {
									$(this).addClass('qs-hidden');
								}
							});
						}
					}, 300);
				});
				at.multiselect.updateSelectedValue($el, false);
				$el.addClass('ready');
			});
		},
		unwrap: function($select) {
			var $parent = $select.parent();
			$select.insertAfter($parent);
			$parent.remove();
		},
		updateSelectedValue: function($el, triggerSelectChange) {
			var $options = $el.find('.option-label'),
				$selectedOptions = $options.filter('.selected'),
				$select = $el.find('select'),
				trimDashes = $select.attr('name') == 'id_category[]',
				selectedText = $select.data('empty'),
				selectedValue = $el.hasClass('multiple') ? [] : 0;
			if ($selectedOptions.length) {
				var values = {};
				$selectedOptions.each(function() {
					var text = $.trim($(this).find('.option-name').text());
					if (trimDashes) {
						text = text.replace(/^\-+/g, '');
					}
					values[$(this).data('val')] = text;
				});
				selectedValue = Object.keys(values);
				selectedText = $options.length == $selectedOptions.length && $select.data('all') ?
				$select.data('all') : Object.values(values).join(', ');
				if (!$el.hasClass('multiple')) {
					selectedValue = selectedValue[0];
				}
			}
			$el.toggleClass('has-selection', !!$selectedOptions.length).find('.current-option').html(selectedText);
			if (triggerSelectChange) {
				var timeBeforeChange = $el.hasClass('multiple') ? 500 : 10;
				clearTimeout(at.multiselect.selectChangeTimer);
				at.multiselect.selectChangeTimer = setTimeout(function(){
					$select.val(selectedValue).change();
					$el.find('.current-option').click(); // hide available options
				}, timeBeforeChange);
			}
		},
		onClickOutSide: function($el, action) {
			var identifier = at.multiselect.cosCounter++;
			$(document).off('click.'+identifier).on('click.'+identifier, function(e) {
				if (!$el.is(e.target) && $el.has(e.target).length === 0) {
					action();
					$(document).off('click.'+identifier);
				}
			});
		},
		mouseEventsTimer: null,
		selectChangeTimer: null,
		quickSearchTimer: null,
		cosCounter: 1,
	},
	updateResourceFields: function(r) {
		var html = '';
		if (r.translatable_fields.length) {
			html = '<select name="fields[]" data-all="'+utf8_decode(r.all_fields_label)+'" multiple>';
			r.translatable_fields.forEach(function(i) {
				html += '<option value="'+i+'" selected>'+i+'</option>';
			});
			html += '</select>';
		}
		$('.resource-fields').html(html);
		at.multiselect.init();
		at.setWidthForResourceFields();
	},
	setWidthForResourceFields: function() {
		var $f = $('.resource-settings-form'),
			w = $f.outerWidth() - $f.find('label.inline-block').outerWidth()
			- $f.find('.resource-options').outerWidth() - 15;
		$('.resource-fields').css({'width': w+'px'}).find('.multiselect').css({'min-width': 0});
	},
	translate: function(content_type, identifier, from, to, fields) {
		if (!at.validateSelectedProvider(true)) {
			return;
		}
		var $tr = $('tr[data-identifier="'+identifier+'"]'),
			$bulkBtn = $('.translateSelected');
			data = {
				content_type: content_type,
				identifier: identifier,
				from: from,
				to: to,
				fields: fields,
				overwrite_existing: $('.dont_overwrite_existing').prop('checked') ? 0 : 1,
				ajax_action: 'autoTranslate',
			},
			response = function(r) {
				// $tr.find('.ajax-response').addClass('ok').html('<i class="icon-check"></i> '+utf8_decode(r.response_msg));
				$tr.find('.loading').removeClass('loading');
				if (r.hasError) {
					at.prependErrors(at.$dynamicList, r.errors);
				} else if (r.translation_stats_html) {
					$tr.find('.translation-stats').html(utf8_decode(r.translation_stats_html))
					.find('span').not('.stats-not-supported').addClass('flashing');
					at.updateStats(r);
				}
				at.bulkClick.shift();
				if (at.bulkClick.length) {
					at.bulkClick[0].click();
					var processedNum = at.getItemCheckboxes(':checked').length - at.bulkClick.length;
					$bulkBtn.find('.processed-num').html(processedNum);
				} else if ($bulkBtn.hasClass('loading')) {
					$bulkBtn.click();
				}
			};
		at.ajaxRequest(data, response);
	},
	ajaxRequest: function(data, response, $autoPrependErrorsTo) {
		if (at.blockAjax) {
			alert('Please wait');
			return;
		}
		at.blockAjax = true;
		$.ajax({
			type: 'POST',
			url: ajax_action_path,
			data: data,
			dataType : 'json',
			success: function(r) {
				at.blockAjax = false;
				if (r.hasError && $autoPrependErrorsTo) {
					at.prependErrors($autoPrependErrorsTo, r.errors);
					$('.loading').removeClass('loading');
				} else {
					if ('saved' in r && r.saved) {
						$.growl.notice({ title: '', message: at.txt.saved});
					}
					response(r);
				}
			},
			error: function(r) {
				at.blockAjax = false;
				$('.loading').removeClass('loading');
				console.warn($(r.responseText).text() || r.responseText);
			}
		});
	},
	prependErrors: function($el, err) {
		var $err = $('<div class="thrown-error">'+utf8_decode(err)+'</div>'),
			errTxt = $err.text(),
			repeated = false;
		$el.find('.thrown-error').each(function(){
			var $repContainer = $(this).find('.repeat'),
				repTxt = $repContainer.text();
			if ($.trim($(this).text()) == $.trim(errTxt+' '+repTxt)) {
				if (!$repContainer.length) {
					$(this).find('.alert').append(' <span class="repeat">2</span>');
				} else {
					$repContainer.html(parseInt(repTxt) + 1);
				}
				repeated = true;
				return false;
			}
		});
		if (!repeated) {
			$el.prepend($err);
		}
		$('html, body').animate({scrollTop: $el.offset().top - 150}, 300);
	}
};

function utf8_decode (utfstr) {
	var res = '';
	for (var i = 0; i < utfstr.length;) {
		var c = utfstr.charCodeAt(i);
		if (c < 128){
			res += String.fromCharCode(c);
			i++;
		} else if((c > 191) && (c < 224)) {
			var c1 = utfstr.charCodeAt(i+1);
			res += String.fromCharCode(((c & 31) << 6) | (c1 & 63));
			i += 2;
		} else {
			var c1 = utfstr.charCodeAt(i+1);
			var c2 = utfstr.charCodeAt(i+2);
			res += String.fromCharCode(((c & 15) << 12) | ((c1 & 63) << 6) | (c2 & 63));
			i += 3;
		}
	}
	return res;
}
/* since 3.0.0 */
