{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
* @copyright 2010-2025 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{foreach $myhreflang AS $khl => $mhl}
    {if isset($mhl.url)}
        {if $khl == 'canonical'}
            <link rel="canonical" href="{$mhl.url}"/>
        {elseif $khl == 'xdefault'}
            <link rel="alternate" href="{$mhl.url}" hreflang="x-default"/>
        {elseif $khl != 'xdefault' && $khl != 'canonical'}
            <link rel="alternate" href="{$mhl.url}"
                  hreflang="{if $mhl.hreflang|strlen<=2}{$mhl.hreflang}-{$mhl.hreflang}{else}{$mhl.hreflang}{/if}"/>
        {/if}
    {/if}
{/foreach}