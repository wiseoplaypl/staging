{extends file="helpers/list/list_header.tpl"}
{block name="override_header"}
<script type="text/javascript">
	        var wpIframeUrl = "//{Configuration::get('NDKCF_BOT_DOMAIN')}.{Configuration::get('NDKCF_BOT_EXT')}/{if Context::getContext()->language->iso_code == 'fr'}fr{else}en{/if}/wpwbot-mobile-app"
</script>
{/block}