<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2024 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use Prestashop\ModuleLibMboInstaller\DependencyBuilder;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

require_once(dirname(__FILE__) . '/SsA2CBridgeIntegration.php');

if (!defined('_PS_VERSION_')) {
  exit;
}

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
  require_once $autoloadPath;
}

class StockSync extends Module
{
  private $container;

  public function __construct()
  {
    $this->module_key = '25dcd1576d4e9dd27478100b57ce31f8';
    $this->name = 'stocksync';
    $this->tab = 'administration';
    $this->version = '1.3.0';
    $this->author = 'Syncx';
    $this->need_instance = 1;
    $this->ps_versions_compliancy = [
      'min' => '1.7.8',
      'max' => _PS_VERSION_,
    ];
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l('Stock Sync');
    $this->description = $this->l('Inventory Management');

    $this->confirmUninstall = $this->l('Are you sure you want to uninstall Stock Sync?');

    if ($this->container === null) {
      $this->container = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
        $this->name,
        $this->getLocalPath()
      );
    }
  }

  public function install()
  {
    if (Shop::isFeatureActive()) {
      Shop::setContext(Shop::CONTEXT_ALL);
    }

    /* CloudSync */
    $moduleManager = ModuleManagerBuilder::getInstance()->build();

    if (!$moduleManager->isInstalled("ps_eventbus")) {
      $moduleManager->install("ps_eventbus");
    } else if (!$moduleManager->isEnabled("ps_eventbus")) {
      $moduleManager->enable("ps_eventbus");
      $moduleManager->upgrade('ps_eventbus');
    } else {
      $moduleManager->upgrade('ps_eventbus');
    }

    return parent::install()
      && $this->getService('stocksync.ps_accounts_installer')->install();
  }

  public function uninstall()
  {
    $worker = new SsA2CBridgeIntegration();
    if (!$worker->canUninstallModule()) {
      return false;
    }
    return parent::uninstall()
    ;
  }

  public function getService($serviceName)
  {
    return $this->container->getService($serviceName);
  }

  public function getContent()
  {
    # Load dependencies manager
    $mboInstaller = new DependencyBuilder($this);

    if (!$mboInstaller->areDependenciesMet()) {
      $dependencies = $mboInstaller->handleDependencies();
      $this->smarty->assign('dependencies', $dependencies);
      return $this->display(__FILE__, 'views/templates/admin/dependency_builder.tpl');
    }

    $output = $this->getA2CContent() . $this->getPrestashopContent();
    return $output;
  }

  public function getPrestashopContent()
  {
    /*********************
     * PrestaShop Account *
     * *******************/

    $accountsService = null;

    try {
      $accountsFacade = $this->getService('stocksync.ps_accounts_facade');
      $accountsService = $accountsFacade->getPsAccountsService();
    } catch (\PrestaShop\PsAccountsInstaller\Installer\Exception\InstallerException $e) {
      $accountsInstaller = $this->getService('stocksync.ps_accounts_installer');
      $accountsInstaller->install();
      $accountsFacade = $this->getService('stocksync.ps_accounts_facade');
      $accountsService = $accountsFacade->getPsAccountsService();
    }

    try {
      Media::addJsDef([
        'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
          ->present($this->name),
      ]);

      // Retrieve the PrestaShop Account CDN
      $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());

    } catch (Exception $e) {
      $this->context->controller->errors[] = $e->getMessage();
      return '';
    }

    /**********************
     * PrestaShop Billing *
     * *******************/

    // Load the context for PrestaShop Billing
    $billingFacade = $this->getService('stocksync.ps_billings_facade');
    $partnerLogo = $this->getLocalPath() . 'views/img/partnerLogo.png';

    // PrestaShop Billing
    Media::addJsDef($billingFacade->present([
      'logo' => $partnerLogo,
      'tosLink' => 'https://www.stock-sync.com/terms',
      'privacyLink' => 'https://www.stock-sync.com/privacy-policy',
      'emailSupport' => 'support@stock-sync.com'
    ]));

    $this->context->smarty->assign('urlBilling', "https://unpkg.com/@prestashopcorp/billing-cdc/dist/bundle.js");

    /*********************
     * PrestaShop CloudSync *
     * *******************/
    $moduleManager = ModuleManagerBuilder::getInstance()->build();
    if ($moduleManager->isInstalled("ps_eventbus")) {
      $eventbusModule = \Module::getInstanceByName("ps_eventbus");
      if (version_compare($eventbusModule->version, '1.9.0', '>=')) {

        $eventbusPresenterService = $eventbusModule->getService('PrestaShop\Module\PsEventbus\Service\PresenterService');

        $this->context->smarty->assign('urlCloudsync', "https://assets.prestashop3.com/ext/cloudsync-merchant-sync-consent/latest/cloudsync-cdc.js");

        Media::addJsDef([
          'contextPsEventbus' => $eventbusPresenterService->expose($this, ['info', 'modules', 'themes'])
        ]);
      }
    }

    return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
  }

  public function getA2CContent()
  {
    if (Tools::getValue('action') == 'APIRequest') {
      $this->ajaxProcessA2CAPIRequest();
    }

    $worker = new SsA2CBridgeIntegration();
    $showButton = 'install';
    $storeKey = '';

    if ($worker->isBridgeExist()) {
      $storeKey = $worker->getStoreKey();
      $showButton = 'uninstall';
    }

    $prestashopVersion = (float) _PS_VERSION_;

    if ($prestashopVersion >= 1.5) {
      $isSslEnabled = Configuration::get('PS_SSL_ENABLED') == "1";

      $callbackProtocol = $isSslEnabled ? "https" : "http";
      $callbackDomain = Configuration::get('PS_SHOP_DOMAIN_SSL');

      if ($isSslEnabled) {
        $sqlPath = "SELECT physical_uri FROM ps_shop_url WHERE domain = '$callbackDomain'";
      } else {
        $sqlPath = "SELECT physical_uri FROM ps_shop_url WHERE domain_ssl = '$callbackDomain'";
      }

      $callbackPath = Db::getInstance()->getValue($sqlPath);
      $callbackUrl = $callbackProtocol . "://" . $callbackDomain . $callbackPath;
      $callbackUrl = rtrim($callbackUrl, '/');

      $callbackEmail = "";
      try {
        $psAccountsService = $this->getService('stocksync.ps_accounts_facade')->getPsAccountsService();
        $callbackEmail = $psAccountsService->getEmail();
      } catch (Exception $e) {
        // DO NOTHING
      }

      $this->context->smarty->assign(
        array(
          'showButton' => $showButton,
          'storeKey' => $storeKey,
          'cartName' => 'Prestashop',
          'ajaxUrl' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
          'callbackUrl' => $callbackUrl,
          'callbackEmail' => $callbackEmail,
        )
      );

      $this->context->controller->addCSS($this->_path . 'views/css/a2c_main.css', 'all');
      $this->context->controller->addJS($this->_path . 'views/js/a2c_scripts.js');

    }

    return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/a2c_header.tpl');
  }

  public function ajaxProcessA2CAPIRequest()
  {
    $worker = new SsA2CBridgeIntegration();
    $returnData = array(
      'install' => false,
      'storeKeyUpdate' => false,
      'remove' => false,
    );

    switch (Tools::getValue('method')) {
      case 'installBridge':
        $storeKey = SsA2CBridgeIntegration::generateStoreKey();
        $returnData['install'] = $worker->installBridge($storeKey);
        $returnData['storeKeyUpdate'] = $worker->updateToken($storeKey);
        $returnData['storeKey'] = $storeKey;
        break;

      case 'removeBridge':
        $returnData['remove'] = $worker->unInstallBridge();
        break;

      case 'updateToken':
        $storeKey = SsA2CBridgeIntegration::generateStoreKey();
        $returnData['storeKeyUpdate'] = $worker->updateToken($storeKey);
        $returnData['storeKey'] = $storeKey;
    }

    $json = null;

    if (method_exists('Tools', 'jsonEncode')) {
      $json = Tools::jsonEncode(array('result' => $returnData));
    } else {
      $json = json_encode(array('result' => $returnData));
    }

    $this->context->smarty->assign(
      array(
        'json' => $json
      )
    );

    die($this->context->smarty->fetch($this->local_path . 'views/templates/admin/a2c_ajax.tpl'));
  }
}