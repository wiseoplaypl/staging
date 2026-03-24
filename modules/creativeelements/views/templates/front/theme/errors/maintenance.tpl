{**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 *}
{$id_kit = Configuration::get('elementor_active_kit')}
<!doctype html>
<html lang="{$language.iso_code}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
{if isset($ce_content)}
	<title>{$ce_content->title}</title>{$meta_description = $ce_content->content|strip_tags|trim|strip}
	<meta name="description" content="{$meta_description}">
{/if}
	<meta name="viewport" content="width=device-width, initial-scale=1">
{if !empty($favicon)}
	<link rel="icon" type="image/vnd.microsoft.icon" href="{$smarty.const._PS_IMG_}{$favicon}?{$favicon_update_time}">
	<link rel="shortcut icon" type="image/x-icon" href="{$smarty.const._PS_IMG_}{$favicon}?{$favicon_update_time}">
{/if}
	<style>
	html, body { margin: 0; padding: 0; }
	</style>
	<script>
	var baseDir = {json_encode($smarty.const.__PS_BASE_URI__)|cefilter};
	</script>
	<!--CE-JS-->
</head>
<body id="maintenance" class="{if $language.is_rtl}lang-rtl {/if}lang-{$language.iso_code} page-maintenance ce-kit-{$id_kit|intval}">
	<main>
		{$HOOK_MAINTENANCE|cefilter}
	</main>
	<!--CE-JS-->
</body>
</html>