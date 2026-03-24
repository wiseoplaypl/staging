<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_1_7($object)
{
    $result = true;
    
    if(!Tools::version_compare(_PS_VERSION_, '1.7'))
    	$result &= $object->registerHook('displayProductAdditionalInfo');
    
	return $result;
}
