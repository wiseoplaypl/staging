<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\CoreXBaseXModule as BaseModule;

class ModulesXCustomerXModule extends BaseModule
{
    public function getName()
    {
        return 'customer';
    }

    public function registerDocuments($documents)
    {
        $documents->registerDocumentType('page-authentication', 'CE\ModulesXCustomerXDocumentsXPageAuthentication');
        $documents->registerDocumentType('page-password', 'CE\ModulesXCustomerXDocumentsXPagePassword');
        $documents->registerDocumentType('page-registration', 'CE\ModulesXCustomerXDocumentsXPageRegistration');
    }

    public function __construct()
    {
        add_action('elementor/documents/register', [$this, 'registerDocuments']);
    }
}
