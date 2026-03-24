{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2019 Hendrik Masson
 *  @license   Tous droits réservés
*}
		<div class="ajax_choose_product" class="row">
			<div class="col-lg-12">
				<p class="alert alert-info">{l s='Begin typing the first few letters of the product name, then select the product you are looking for from the drop-down list:'}</p>
				<div class="clear clearfix prodlist">
					{if ($objprods !='')}
						{foreach $objprods as $prod }
								<button data-id="{$prod['id_product']}" class="btn btn-default prodrow" type="button"><i class="icon-remove"></i>{$prod['name']} ( ref : {$prod['reference']} )</button>
						{/foreach}
					{/if}
				</div>
				<div class="input-group row-margin-bottom">
					<span class="input-group-addon">
						<i class="icon-search"></i>
					</span>
					<input type="text" value="" class="product_autocomplete_input"/>
				</div>
			</div>
		</div>
