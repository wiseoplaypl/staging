<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author	Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
*/
class Product extends ProductCore
{
	public function hasAllRequiredCustomizableFields(Context $context = null)
	{
		require_once _PS_MODULE_DIR_.'ndk_advanced_custom_fields/models/ndkCf.php';
		$is_required = NdkCf::isRequiredCustomization(
				$this->id,
				(int) $this->id_category_default
			);
		// if(Context::getContext()->controller->php_self == 'cart' )
		// return true;

		if (!Customization::isFeatureActive() && !$is_required) {
			return true;
		}
		if (!$context) {
			$context = Context::getContext();
		}

		$fields = $context->cart->getProductCustomization($this->id, null, true);
		if (($required_fields = $this->getRequiredCustomizableFields()) === false) {
			return false;
		}

		$fields_present = [];
		foreach ($fields as $field) {
			$fields_present[] = ['id_customization_field' => $field['index'], 'type' => $field['type']];
		}

		if (is_array($required_fields) && count($required_fields)) {
			foreach ($required_fields as $required_field) {
				if (!in_array($required_field, $fields_present)) {
					return false;
				}
			}
		}

		//dump($is_required);
		if (count($fields) < 1 && $is_required) {
			return false;
		}

		return true;
	}
}
