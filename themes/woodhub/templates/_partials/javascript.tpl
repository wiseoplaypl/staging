{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{assign var="nodef" value=["modules-cookie", "corejs", "theme-main" ]}
{assign var="ignorejs" value=["dbaf683f82df4d0716e5f749995c3bfebc1b845d", "klarnapayment-onsite-messaging", "6a604eca9aac0074a41b59d2e37016652f35ae3e", "f3cebe5b7fb6809474ad8a0d6de5491cf297a48e", "64cd1f7f9d4d0cd8892800467e32772606b5f3c8", "5d97d57aa98e3ae90da963fc29ffd403458135b4", "881dd08aafb161591d82caca182f345c6274290e", "3f8074901a7f44e8f73ef61f1eefbb32f298c7c8", "be12b83da8d75b5e10c3b6bd591b167879e60d6e", "8f17e43acf24650dfdea97359a13805f0119f918", "modules-phsimpleblog", "89cd75b700e33658da70c202d9c2254519974453", "f65c90416d996e8ab3a07915c6a14e33f100e64c" ]}

{foreach $javascript.external as $js}
    {if in_array($js.id, $nodef)}
        <script name="{$js.id}" type="text/javascript" src="{$js.uri}" {$js.attribute}></script>
    {else}
        {if $page.page_name == 'index' && in_array($js.id, $ignorejs)}

        {else}
                <script name="{$js.id}" type="text/javascript" src="{$js.uri}" {$js.attribute} defer></script>
        {/if}
    {/if}
{/foreach}

{foreach $javascript.inline as $js}
  <script type="text/javascript" name="{$js.id}">
    {$js.content nofilter}
  </script>
{/foreach}

{if isset($vars) && $vars|@count}
  <script type="text/javascript">
    {foreach from=$vars key=var_name item=var_value}
    var {$var_name} = {$var_value|json_encode nofilter};
    {/foreach}
  </script>
{/if}
