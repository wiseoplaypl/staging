/*!
 * Creative Elements - live Theme & Page Builder
 * Copyright 2019-2025 WebshopWorks.com
 */

jQuery($ => {
	if ('#license' === location.hash) {
		history.replaceState({}, document.title, location.pathname + location.search);

		$('#modal_license').modal();
	}

	const $regenerate = $('#page-header-desc-configuration-regenerate-css'),
		$replace = $(document.replace_url);

	$regenerate
		.attr('title', '<p style="width:182px;">' + $regenerate.attr('onclick').substr(2) + '</p>')
		.tooltip({
			html: true,
			placement: 'bottom',
		})
		.on('click.ce', function onClickRegenerateCss() {
			this.disabled = true;
			$regenerate.find('i').attr('class', 'process-icon-reload icon-rotate-right icon-spin');

			$.post(location.href, {
				ajax: true,
				action: 'regenerate_css',
			}, function onSuccessRegenerateCss(resp) {
				$regenerate.removeAttr('disabled').find('i').attr('class', 'process-icon-ok');
			}, 'json');
		})
		.removeAttr('onclick')
	;
	$replace.on('submit.ce', function onSubmitReplaceUrl(event) {
		event.preventDefault();

		if ($replace.find('.icon-spin').length) {
			return;
		}
		$replace.find('i').attr('class', 'icon-circle-o-notch icon-spin');

		$.post(
			location.href,
			$(this).serialize(),
			function onSuccessReplaceUrl(resp) {
				if (resp.success) {
					document.replace_url.reset();
				}
				$replace.find('i').attr('class', 'icon-refresh');

				$replace.find('.alert').attr({
					'class': 'alert alert-' + (resp.success ? 'success' : 'danger')
				}).html(resp.data);
			},
			'json'
		);
	});

	const form = document.forms.configuration_form;

	form.subTab.value = $('#head_tabs .current').attr('id').replace('subtab-', '');

	$('#head_tabs').on('click', 'a', function onClickTab(event) {
		event.preventDefault();

		var tab = this.id.replace('subtab-', ''),
			url = location.pathname + location.search.replace(/&subTab=\w+/, '');

		$('#head_tabs a.current').removeClass('current');
		$(this).addClass('current');
		$('#configuration_fieldset_' + tab).removeClass('hidden').siblings().addClass('hidden');

		form.subTab.value = tab;
		history.replaceState({}, document.title, url + '&subTab=' + tab);
	});

	const limit = form.elementor_max_revisions.value;

	$(form.elementor_max_revisions).on('change', function onChangeMaxRevisions() {
		$reduceRevisions[0].disabled = this.value !== limit;
	});

	const $reduceRevisions = $('#elementor_clear_revisions').on('click', function onClickClearRevisions() {
		if (!confirm($reduceRevisions.attr('onclick').substr(2))) {
			return;
		}
		this.disabled = true;
		$reduceRevisions.find('i').attr('class', 'icon-circle-o-notch icon-spin');

		$.post(location.href, {
			ajax: true,
			action: 'clear_revisions',
		}, function onSuccessClearRevisions(resp) {
			$reduceRevisions.removeAttr('disabled').find('i').attr('class', 'icon-trash');

			if (resp.success) {
				showSuccessMessage(resp.data);
			} else {
				showErrorMessage(resp.data || 'Unknown Error');
			}
		}, 'json');
	});

	$(form.elementor_stretched_section_container).attr({
		placeholder: 'body',
	});
	$(form.elementor_viewport_lg).attr({
		type: 'number',
		min: 769,
		max: 1439,
	});
	$(form.elementor_viewport_md).attr({
		type: 'number',
		min: 481,
		max: 1024,
	});
	$(form.elementor_assets_priority).attr({
		type: 'number',
		min: 50,
		max: 999,
	});
});
