/*!
 * Creative Elements - live Theme & Page Builder
 * Copyright 2019-2025 WebshopWorks.com
 */

window.ceAdmin && document.addEventListener('DOMContentLoaded', () => {
	// Cancel button fix
	$('.btn[id$=_form_cancel_btn]')
		.removeAttr('onclick')
		.attr('href', location.href.replace(/&id\w*=\d+|&(add|update)\w+(=[^&]*)?/g, ''));
	// Priority button fix
	$('.btn-primary.btn-default').removeClass('btn-default');

	// Fix for shortcode
	$('.ce-shortcode input').on('click.ce', function(e) {
		this.select();
		document.execCommand('copy');
		showSuccessMessage('<i class="icon-paste"></i> <code>' + this.value + '</code>');
	}).parent()
		.removeAttr('onclick')
		.removeClass('pointer')
	;

	// Fix for after ajax save new ybc_blog post update links
	history.pushState = (function(parent) {
		return function(data, title, url) {
			var id = url.match(/&id_post=(\d+)/)?.pop();

			id && $('.btn-edit-with-ce').each(function() {
				this.href = this.href.replace('&id_page=0', '&id_page=' + id);
			});
			return parent.apply(this, arguments);
		};
	})(history.pushState);

	// HiBlog compatibility
	/configure[=\/]hiblog/.test(location.href) && $(document).on('ajaxSuccess', function onAjaxSuccess(e, xhr, args) {
		var idPost = $('#id_post').val();

		idPost && (args.data.get && args.data.get('action') === 'savePost' || ~args.data.indexOf('action=displayPostForm')) && $.post(
			$('a[href*=AdminCEContent]').prop('href'),
			{
				action: 'hideEditor',
				ajax: true,
				id: idPost,
				idType: 16,
			},
			function onSuccessHideEditor(data) {
				ceAdmin.hideEditor = data;
				ceAdmin.uid = idPost + ceAdmin.uid.slice(-6);
				ceAdmin.$contents = ceAdmin.getContents('textarea[name^=blog_description_]');

				$('.wrapper-edit-with-ce').prevAll('.mce-tinymce').hide(0);
			},
			'json'
		);
	});

	// Quick Edit
	function addQuickEdit(id, idType, idLang, idShop, insertMethod, $elem) {
		if ($.fn.pstooltip) {
			$('<div class="btn-group ce-edit">').html(idLang
				? '<a class="btn tooltip-link dropdown-item" href="' + ceAdmin.editorUrl + id + idType + idLang + idShop + '">' +
						'<i class="material-icons mi-ce"></i>' +
					'</a>'
				: '<a class="btn tooltip-link dropdown-item dropdown-toggle" data-toggle="dropdown">' +
						'<i class="material-icons mi-ce"></i>' +
					'</a>' +
					'<div class="dropdown-menu dropdown-menu-right">' +
						ceAdmin.languages.map(
							lang => '<a href="' + ceAdmin.editorUrl + id + idType + ('0' + lang.id_lang).substr(-2) + idShop + '" class="btn dropdown-item">' + lang.name + '</a>'
						).join('') +
					'</div>'
			)[insertMethod]($elem).find('a:first').pstooltip({title: ceAdmin.i18n.editCE, placement: 'left'});
		} else {
			$('<div class="btn-group ce-edit" title="' + ceAdmin.i18n.editCE + '">').html(idLang
				? '<a class="btn btn-default" href="' + ceAdmin.editorUrl + id + idType + idLang + idShop + '">' +
						'<i class="icon-AdminParentCEContent"></i>' +
					'</a>'
				: '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">' +
						'<i class="icon-AdminParentCEContent"></i>' +
					'</button>' +
					'<ul class="dropdown-menu">' +
						ceAdmin.languages.map(
							lang => '<li><a href="' + ceAdmin.editorUrl + id + idType + ('0' + lang.id_lang).substr(-2) + idShop + '">' + lang.name + '</a></li>'
						).join('') +
					'</ul>'
			)[insertMethod]($elem);
		}
	}
	var selectors = {
		AdminCEContent: 'table.ce_content td.identifier-type',
		AdminCETemplates: 'table.ce_template td.identifier-type',
		AdminCEThemes: 'table:is(.ce_theme, .ce_template) td.identifier-type',
		AdminProducts: 'table.product td:nth-of-type(2), #product_grid_table .bulk_action-type + td',
		AdminCategories: 'table.category td.fixed-width-xs:nth-of-type(-n+2), #category_grid_table td.identifier-type',
		AdminManufacturers: 'table.manufacturer td.fixed-width-xs:nth-of-type(-n+2), #manufacturer_grid_table .bulk_action-type + td',
		AdminSuppliers: 'table.supplier td.fixed-width-xs:nth-of-type(-n+2), #supplier_grid_table .bulk_action-type + td',
		AdminCmsContent: 'table.cms td.fixed-width-xs:nth-of-type(-n+2), #cms_page_grid_table .bulk_action-type + td',
	};
	ceAdmin.editManufacturers || delete selectors.AdminManufacturers;
	ceAdmin.editSuppliers || delete selectors.AdminSuppliers;

	selectors[help_class_name] && $(selectors[help_class_name]).get().forEach(td => {
		var template = $('#form-ce_template').length,
			id = parseInt( $(td).text() ),
			idType = template ? '01' : ceAdmin.uid.substr(-6, 2),
			idLang = template ? '00' : ('0' + ceAdmin.languages[0].id_lang).substr(-2),
			idShop = template ? '00' : ceAdmin.uid.substr(-2),
			$btnGroup = $(td.parentNode).find('.btn-group-action');

		addQuickEdit(id, idType, ceAdmin.languages.length > 1 && !template ? null : idLang, idShop, 'prependTo', $btnGroup);
	});

	// Minor CSS fix
	$('.btn-group-action a.product-edit.tooltip-link').addClass('dropdown-item');

	// Import Template
	var $import = $('.ce-import-panel').removeClass('hide')
		.parent().slideUp(0).insertBefore('#form-ce_template')
	;
	$('.ce-import-panel #file').attr({
		accept: '.json,.zip',
		required: true,
	});

	if ('AdminCEThemes' === help_class_name) {
		// Auto open Theme Settings tab
		$(() => {
			var index = $('#head_tabs .current').parent().index() - 2;
			if (index > 0) {
				$('.ce-theme-panel .nav-tabs a').eq(index).click();
			}
		});
		// Quick Edit for Theme Settings
		$('.ce-theme-panel select').on('change', function onChangeSelect() {
			$(this).next()[this.dataset.value === this.value ? 'show' : 'hide']();
		}).get().forEach(select => {
			if (!(select.dataset.value = select.value)) {
				return;
			}
			var kit = 'elementor_active_kit' === select.id,
				id = select.value,
				idType = kit ? '01' : ceAdmin.uid.substr(-6, 2),
				idLang = kit ? '00' : ('0' + ceAdmin.languages[0].id_lang).substr(-2),
				idShop = kit ? '00' : ceAdmin.uid.substr(-2);

			addQuickEdit(id, idType, ceAdmin.languages.length > 1 && !kit ? null : idLang, idShop, 'insertAfter', select);
		});
	} else if ('AdminCmsContent' === help_class_name) {
		// Quick Edit for CMS Category
		var id = location.search.match(/id_cms_category=(\d+)/)?.pop() || 1,
			idType = '08',
			idLang = ('0' + ceAdmin.languages[0].id_lang).substr(-2),
			idShop = ceAdmin.uid.substr(-2),
			$link = $('<a class="ce-edit">').html('<i class="icon-AdminParentCEContent"></i> ' + ceAdmin.i18n.edit),
			liLast = _PS_VERSION_.localeCompare('1.7.6', undefined, {numeric: true}) < 0 ? '.cat_bar li:last' : '.card .breadcrumb li:last';

		if (ceAdmin.languages.length > 1) {
			$link.attr('data-toggle', 'dropdown').add($.fn.pstooltip
				? '<div class="dropdown-menu">' +
						ceAdmin.languages.map(
							lang => '<a href="' + ceAdmin.editorUrl + id + idType + ('0' + lang.id_lang).substr(-2) + idShop + '" class="btn dropdown-item">' + lang.name + '</a>'
						).join('') +
					'</div>'
				: '<ul class="dropdown-menu">' +
						ceAdmin.languages.map(
							lang => '<li><a href="' + ceAdmin.editorUrl + id + idType + ('0' + lang.id_lang).substr(-2) + idShop + '">' + lang.name + '</a></li>'
						).join('') +
					'</ul>'
			).appendTo(liLast)
				.parent().css('position', 'relative');
			$link[0].href = 'javascript:;'
		} else {
			$link.appendTo(liLast);
			$link[0].href = ceAdmin.editorUrl + id + idType + idLang + idShop;
		}
		$link.prev().contents().unwrap();

		$.fn.pstooltip
			? $link.eq(0).pstooltip({title: ceAdmin.i18n.editCE})
			: $link[0].title = ceAdmin.i18n.editCE;

		// Quick Edit for CMS Categories
		$('table.cms_category td.fixed-width-xs:nth-of-type(-n+2), #cms_page_category_grid_panel .bulk_action-type + td').get().forEach(td => {
			var id = parseInt( $(td).text() ),
				$btnGroup = $(td.parentNode).find('.btn-group-action');

			addQuickEdit(id, idType, ceAdmin.languages.length > 1 ? null : idLang, idShop, 'prependTo', $btnGroup);
		});
	}

	// Handler functions
	ceAdmin.onClickImport = function() {
		$import.hasClass('visible')
			? $import.removeClass('visible').slideUp(300)
			: $import.addClass('visible').slideDown(300)
		;
	};
	ceAdmin.onClickBtnBack = function(e) {
		ceAdmin.checkChanges = true;
	};
	ceAdmin.onClickBtnWrapper = function(e) {
		this.children[0].click();
	};
	ceAdmin.onClickBtnEdit = function(e) {
		e.stopPropagation();
		ceAdmin.checkChanges = true;

		if ('0' === ceAdmin.uid[0]) {
			if (document.body.classList.contains('adminmaintenance')) {
				return this.href += '&action=addMaintenance';
			}
			ceAdmin.checkChanges = e.preventDefault();
			return alert(ceAdmin.i18n.save);
		}
	};

	// Button templates
	var tmplBtnBack = $('#tmpl-btn-back-to-ps').html(),
		tmplBtnEdit = $('#tmpl-btn-edit-with-ce').html();

	if (ceAdmin.footerProduct) {
		const tag = $('.product-page-v2').length ? 'h3' : 'h2';
		var $tf = $('<div class="translationsFields tab-content">').wrap('<div class="translations tabbable">');
		$tf.parent()
			.insertAfter('#related-product, :has(>#product_description_related_products)')
			.wrap('<div class="form-group">')
			.before(`<${tag} class="ce-product-hook">displayFooterProduct</${tag}>`)
		;

		$('textarea[id*=description_short_]').each((i, el) => {
			var idLang = el.id.split('_').pop(),
				langClass = el.parentNode.className.match(/translation-label-\w+/)?.pop(),
				$btn = $(tmplBtnEdit).click(ceAdmin.onClickBtnEdit);

			if ('0' === ceAdmin.footerProduct[0]) {
				$btn[0].href += '&action=addFooterProduct&uid=' + (1*ceAdmin.uid + 100*idLang);
			} else {
				$btn[0].href += '&uid=' + (1*ceAdmin.footerProduct + 100*idLang) + '&footerProduct=' + ceAdmin.uid.slice(0, -6);
			}
			$('<div class="translation-field tab-pane">')
				.addClass(langClass)
				.addClass(el.parentNode.classList.contains('active') ? 'active' : '')
				.addClass(el.parentNode.classList.contains('visible') ? 'visible' : '')
				.append($btn)
				.appendTo($tf)
			;
		});
	} else if (ceAdmin.uidAdditional) {
		ceAdmin.$additional = $('textarea[name*="[additional_description]"]').each((i, el) => {
			var idLang = parseInt(el[el.id ? 'id' : 'name'].split('_').pop()) || 0,
				$btn = $(tmplBtnEdit).insertBefore(el).click(ceAdmin.onClickBtnEdit);

			$btn[0].href += '&uid=' + (1*ceAdmin.uidAdditional + 100*idLang);

			if (~ceAdmin.hideAdditionalEditor.indexOf(idLang)) {
				$(tmplBtnBack).insertBefore($btn).click(ceAdmin.onClickBtnBack)[0].href += '&uid=' + (1*ceAdmin.uidAdditional + 100*idLang);
				$btn.wrap('<div class="wrapper-edit-with-ce">').parent().click(ceAdmin.onClickBtnWrapper);
				$(el).hide().next('.maxLength').hide();
			} else {
				$btn.after('<br>');
			}
		});
	}

	ceAdmin.getContents = function(selector) {
		if (!ceAdmin.editSuppliers && $('form[name=supplier]').length ||
			!ceAdmin.editManufacturers && $('form[name=manufacturer]').length
		) {
			return;
		}
		return $(selector).each((i, el) => {
			var idLang = parseInt(el[el.id ? 'id' : 'name'].split('_').pop()) || 0,
				$btn = $(tmplBtnEdit).insertBefore(el).click(ceAdmin.onClickBtnEdit);

			$btn[0].href += '&uid=' + (1*ceAdmin.uid + 100*idLang);

			if (~ceAdmin.hideEditor.indexOf(idLang)) {
				$(tmplBtnBack).insertBefore($btn).click(ceAdmin.onClickBtnBack)[0].href += '&uid=' + (1*ceAdmin.uid + 100*idLang);
				$btn.wrap('<div class="wrapper-edit-with-ce">').parent().click(ceAdmin.onClickBtnWrapper);
				$(el).hide().next('.maxLength').hide();
			} else {
				$btn.after('<br>');
			}
		});
	};

	ceAdmin.$contents = ceAdmin.getContents([
		'body:not(.adminproducts) textarea[name^=content_]:not([name*=short])',
		'body:not(.adminproducts) textarea[name*="[content]"]',
		'body:not(.adminpsblogblogs) textarea[name^=description_]:not([name*=short])',
		'textarea[name*="[description]"]:not([name*=short])',
		'textarea[name^=post_content_]',
		'textarea[name=content]',
		'.adminmaintenance textarea'
	].join());

	ceAdmin.form = ceAdmin.$contents[0] && ceAdmin.$contents[0].form;
	ceAdmin.formChanged = false;

	$(function() {
		// run after jQuery's document ready
		$(ceAdmin.form).one('change', ':input', function() {
			ceAdmin.formChanged = true;
		});
	});
	$(window).on('beforeunload', function() {
		if (ceAdmin.checkChanges && ceAdmin.formChanged) {
			delete ceAdmin.checkChanges;
			return "Changes you made may not be saved!";
		}
	});
});

if (/[&\?]perm=/.test(location.search)) {
	history.replaceState({}, document.title, location.href.replace(/[&\?]perm=\d+/, ''));
}
