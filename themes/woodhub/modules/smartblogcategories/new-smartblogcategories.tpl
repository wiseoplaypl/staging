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
{if $isDropdown}
	<div class="block-categories hidden-md-down">
		<h4 class="h6 hidden-md-down"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Category' d='ModulesSmartblogcategoriesNew-smartblogcategories'}</a></h4>
		<div class="block_content" style="">
			<select onchange="document.location.href=this.options[this.selectedIndex].value;">
				<option value="">Select Category</option>
				{include file="./category-tree-branch.tpl" node=$blockCategTree last='true' select='false'}
			</select>
		</div>
	</div>
{else}
	{function name="blockCategTree" nodes=[] depth=0}
	  {strip}
	    {if $nodes|count}
	      <ul class="category-sub-menu">
	        {foreach from=$nodes item=node}
	        	{if $node.name != ''}
		          <li data-depth="{$depth}">
		            {if $depth===0}
		              <a href="{$node.link}">{$node.name}</a>
		              {if $node.children}
		              	
			              	{if $isDhtml}
				                <div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#exBlogCollapsingNavbar{$node.id}">
				                  <i class="material-icons add">&#xE145;</i>
				                  <i class="material-icons remove">&#xE15B;</i>
				                </div>
			                {/if}
			            
		                <div class="{if $isDhtml}collapse{/if}" id="exBlogCollapsingNavbar{$node.id}">
		                  {blockCategTree nodes=$node.children depth=$depth+1}
		                </div>
		              {/if}
		            {else}
		              <a class="category-sub-link" href="{$node.link}">{$node.name}</a>
		              {if $node.children}
		              	{if $isDhtml}
			                <span class="arrows" data-toggle="collapse" data-target="#exBlogCollapsingNavbar{$node.id}">
			                  <i class="material-icons arrow-right">&#xE315;</i>
			                  <i class="material-icons arrow-down">&#xE313;</i>
			                </span>
			            {/if}
		                <div class="{if $isDhtml}collapse{/if}" id="exBlogCollapsingNavbar{$node.id}">
		                  {blockCategTree nodes=$node.children depth=$depth+1}
		                </div>
		              {/if}
		            {/if}
		          </li>
		        {/if}
	        {/foreach}
	      </ul>
	    {/if}
	  {/strip}
	{/function}

	<div class="block-categories hidden-sm-down">
		<h4 class="h6 hidden-sm-down"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Category' d='ModulesSmartblogcategoriesNew-smartblogcategories'}</a></h4>
	  <ul class="category-top-menu">
	    <li><a class="text-uppercase h6" href="{$blockCategTree.link nofilter}">{$blockCategTree.name}</a></li>
	    <li>{blockCategTree nodes=$blockCategTree.children}</li>
	  </ul>
	</div>
{/if}