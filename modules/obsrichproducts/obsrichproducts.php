<?php
/**
 * 2011-2025 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2025 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

$autoloadPath = dirname(__FILE__).'/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// NO `use` allowed to load namespaces. It is not compatible with Prestashop 1.6

class Obsrichproducts extends Module
{
    public function __construct()
    {
        $this->name = 'obsrichproducts';
        $this->tab = 'seo';
        $this->version = '1.21.0';
        $this->author = 'OBSolutions';
        $this->author_address = '0xF6A3888b1C6C2d5f20AdE2FdbE26C338A8F31011';
        $this->module_key = '8b8973497e65e604ef0b463d7653f2d2';
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => '9.0.0',
        );

        parent::__construct();

        $this->_errors = array();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('Rich Snippets for Products');
        $this->description = $this->l('Adds Rich Snippets to show rich Products on Google.');
    }

    public function install()
    {
        if (!parent::install() or !$this->registerHook('displayFooterProduct')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function hookProductOutOfStock($params)
    {
        return $this->hookDisplayFooterProduct($params);
    }

    public function hookDisplayProductExtraContent($params)
    {
        return $this->hookDisplayFooterProduct($params);
    }

    public function hookDisplayFooterProduct($params)
    {
        if (isset($params['product']) and $params['product']) {
            $commandHandler = new OBSolutions\RichSnippets\Product\Application\GetProductRichSnippetQueryHandler();
            $productRichSnippet = $commandHandler->handle(
                new OBSolutions\RichSnippets\Product\Application\GetProductRichSnippetQuery(
                    new OBSolutions\RichSnippets\Product\Infraestructure\Prestashop\ProductAdapter($params['product'])
                )
            );

            $productRichSnippetPresenter = new OBSolutions\RichSnippets\Product\Application\ProductRichSnippetJsonLdPresenter(
                $this,
                $productRichSnippet
            );

            return $productRichSnippetPresenter->present();
        }
    }
}
