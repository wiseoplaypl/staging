<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Shoprunners
 * Use, copy, modification or distribution of this source file without written
 * license agreement from Shoprunners is strictly forbidden.
 * In order to obtain a license, please contact us: info@shoprunners.de
 *
 * @author    Peter Schaeffer - Shoprunners
 * @copyright Copyright(c) 2012-2022 Shoprunners
 * @license   Commercial license
 * @package   aftermail
 */

include dirname(__FILE__) . '/../../classes/AfterMailLog.php';

class aftermailprestacronModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {
        parent::initContent();
		if (Tools::getValue('secure_key') && Module::isEnabled('aftermailpresta')) {
			$secureKey = Configuration::get('PS_AFTERMAIL_SECURE_KEY');
			if (! empty($secureKey) && $secureKey === Tools::getValue('secure_key')) {
				global $kernel;
				if(!$kernel){
					require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
					$kernel = new \AppKernel('prod', false);
					$kernel->boot();
					$context = Context::getContext();
					if (!isset($context->employee) || $context->employee->id == 0) {
						$context->employee = new Employee(1);					
					}
				}
				$aftermail = new AfterMailPresta();
				$aftermail->cronTask();
			}
		}
	}
	
	public function display() {
		return null;
	}
}