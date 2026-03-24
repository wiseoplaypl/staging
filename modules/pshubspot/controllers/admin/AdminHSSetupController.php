<?php
/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */

use Tiralineas\PsHubspot\Connection;
use Tiralineas\PsHubspot\DealPipeline;
use Tiralineas\PsHubspot\Navigator;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'pshubspot/controllers/admin/AdminHSAbstractController.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsStore.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsPipeline.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/AvailableProperties.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/AvailableGroups.php';

// @todo detect if multishop and force sync of each shop not all shops
class AdminHSSetupController extends AdminHSAbstractController
{
    protected $slug = 'hs_setup';
    public $lastcompleted_step = 0;

    public function __construct()
    {
        parent::__construct();
        $this->meta_title = [$this->l('Initial SetUp')];
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->setup_wizard = [
            $this->l('Create Store Id'),
            $this->l('Create Properties'),
            $this->l('Map order statuses to deal stages'),
            $this->l('Define orders to sync'),
        ];
        $this->lastcompleted_step = Configuration::get('PS_HUBSPOT_SETUP_STEP', '', '', '') ?: 0;
        $this->next_step = Tools::getValue('next_step', $this->lastcompleted_step);
        if (!Connection::isValidClientIdsStored() || $this->lastcompleted_step < 0) {
            Configuration::deleteByName('PS_HUBSPOT_CLIENT_IDS_STORED'); // @Todo smarter way to reset account
            Configuration::deleteByName('PS_HUBSPOT_SETUP_STEP');
            Navigator::toDashboard();
        }
    }

    public function postProcess()
    {
        if (((bool) Tools::isSubmit('ps-hubspot_submit')) == true || ($this->lastcompleted_step == 0)) {
            if ($this->{'runStep' . $this->next_step}()) {
                $this->lastcompleted_step = max($this->lastcompleted_step, $this->next_step);
                Configuration::updateValue(
                    'PS_HUBSPOT_SETUP_STEP',
                    $this->lastcompleted_step,
                    false,
                    '',
                    ''
                );
            } else {
                $this->next_step = $this->lastcompleted_step;
            }
        }
        if (((bool) Tools::isSubmit('ps-hubspot_submit_skip')) == true) {
            $this->lastcompleted_step = max($this->lastcompleted_step, $this->next_step);
            Configuration::updateValue(
                'PS_HUBSPOT_SETUP_STEP',
                $this->lastcompleted_step,
                false,
                '',
                ''
            );
        }
        if ($this->lastcompleted_step >= count($this->setup_wizard)) {
            $this->setupFinished();
        }
        if (empty($this->next_step) && $this->lastcompleted_step) {
            $this->next_step = $this->lastcompleted_step;
        }
    }

    public function process()
    {
        if ($this->lastcompleted_step > 0) {
            $this->display = $this->setup_wizard[$this->lastcompleted_step];
        }
    }

    public function renderView()
    {
        $shops = $sourceStores = $pipelines = '';
        if (Configuration::get('PS_HUBSPOT_SETUP_STEP', '', '', '') == 0 || $this->next_step === 0) {
            $shops = self::getShops();
            $sourceStores = HsStore::getStores();
            try {
                // Para luego poder acceder a las tiendas como un array
                $sourceStores = json_decode(json_encode($sourceStores), true);
            } catch (\Exception $e) {
                print_r($e);
            }
        }
        if (Configuration::get('PS_HUBSPOT_SETUP_STEP', '', '', '') == 2 || $this->next_step == 2) {
            $pipelines = HsPipeline::getClientPipelines();
        }
        $id_lang = Context::getContext()->language->id;
        $getCustomerGroups = \Group::getGroups($id_lang);
        $getCustomerGroupsUnSync = explode(',', \Configuration::get('PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC')) ?: '';
        $customerGroupAvailable = [];
        $customerGroupUnSync = [];
        foreach ($getCustomerGroups as $key => $customerGroup) {
            if (!in_array($customerGroup['id_group'], $getCustomerGroupsUnSync)) {
                array_push($customerGroupAvailable, $customerGroup);
            } else {
                array_push($customerGroupUnSync, $customerGroup);
            }
        }
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'pshubspot/views/css/customer_groups.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'pshubspot/views/js/jquery-ui.min.js');

        $this->context->smarty->assign(
            [
                'setup_wizard' => $this->setup_wizard,
                'lastcompleted_step' => $this->lastcompleted_step,
                // Step1
                'shops' => self::getShops(),
                'sourceStores' => (array) $sourceStores,
                // Step2
                'mappings' => HsPipeline::getCurrentMapping(),
                'PIPELINE_ID' => \Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', ''),
                'stages' => DealPipeline::getPipelineById(\Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', ''), true),
                'pipelineAndStages' => $pipelines,
                // Step3
                'PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED' => \Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED', '', '', ''),
                'PS_HUBSPOT_DEAL_DATE_FILTER' => \Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER', '', '', ''),
                'PS_HUBSPOT_DEALSABANDONED_SENIORITY' => \Configuration::get('PS_HUBSPOT_DEALSABANDONED_SENIORITY', '', '', '') ?: '240',
                'PS_CUSTOMER_GROUPS_AVAILABLE' => $customerGroupAvailable,
                'PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC' => $customerGroupUnSync,
            ]
        );

        return $this->context->smarty->fetch($this->getTplPath('step' . $this->next_step));
    }

    private function getShops()
    {
        $ret = [];
        foreach (Shop::getShopsCollection(true) as $shop) {
            $shop->ref = HsStore::generateExternalIdfromId($shop->id);
            $ret[] = $shop;
        }

        return $ret;
    }

    private function runStep0()
    {
        return true;
    }

    /**
     * Create maping and load properties
     *
     * @return bool
     */
    private function runStep1()
    {
        $shops = Tools::getValue('shop', []);
        $errors = [];
        array_walk(
            $shops, // @todo build proper default
            function ($shop, $shop_id) use (&$errors) {
                if (!(new HsStore($shop_id))->sync($shop)) {
                    $errors[] = 'Unable to create store for shop_id ' . $shop_id . ' in HubSpot with label ' . $shop['label'];
                }
            }
        );
        $this->errors = array_merge($this->errors, $errors);

        return count($this->errors) == 0;
    }

    /**
     * Load properties
     *
     * @return bool
     */
    private function runStep2()
    {
        // TODO: Si el número de opciones en una propiedad tipo Select es muy grande, puede dar problemas (ej: customer_groups)
        $object_types = ['contact', 'deal'];
        $errors = [];
        array_walk(
            $object_types,
            function ($object_type) use (&$errors) {
                $groups = AvailableGroups::getUnsync($object_type);
                array_walk(
                    $groups,
                    function ($group, $i) use ($object_type, &$errors) {
                        usleep(100000);
                        if (!(new AvailableGroups($group['id']))->sync($object_type, $group)) {
                            $errors[] = 'Unable to create ' . $object_type . ' group ' . $group['name'] . ' in HubSpot with label ';
                        }
                    }
                );
                $properties = AvailableProperties::getUnsync($object_type);
                array_walk(
                    $properties,
                    function ($property, $i) use (&$errors) {
                        usleep(100000);
                        $available_prop = new AvailableProperties($property['id']);
                        if (!$available_prop->sync()) {
                            $errors[] = 'Unable to create ' . $property['object_type'] . ' property ' . $property['name'] . ' in HubSpot with label ' . $property['label'];
                        }
                    }
                );
            }
        );
        $this->errors = array_merge($this->errors, $errors);
        $_SESSION['getApiPropertyNames'] = [];

        return count($this->errors) == 0;
    }

    /**
     * Create maping
     *
     * @return bool
     */
    private function runStep3()
    {
        // DealPipeline::storePipelineId(Tools::getValue('pipeline_label'));
        \Configuration::updateValue('PS_HUBSPOT_DEAL_PIPELINE_LABEL', Tools::getValue('pipeline_label'), false, '', '');
        \Configuration::updateValue('PS_HUBSPOT_DEAL_PIPELINE_ID', Tools::getValue('pipeline_id'), false, '', '');
        foreach (Tools::getValue('mappings') as $key => $value) {
            $stage = new HsPipeline($key);
            $stage->sync_as = $value;
            $stage->save();
        }

        return count($this->errors) == 0;
    }

    private function runStep4()
    {
        $keys = [
            'PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED',
            'PS_HUBSPOT_DEAL_DATE_FILTER',
            'PS_HUBSPOT_DEALSABANDONED_SENIORITY',
            'PS_HUBSPOT_CUSTOMER_GROUPS_UNSYNC',
        ];
        foreach ($keys as $key) {
            \Configuration::updateValue(
                $key,
                Tools::getValue($key),
                false,
                '',
                ''
            );
        }

        return true;
    }

    private function setupFinished()
    {
        $tabs_to_toggle = [
            ['AdminHSSetup', 0],
            ['AdminHSDashboard', 1],
            ['AdminHSLists', 0],
            ['AdminHSProperties', 0],
            ['AdminHSWorkflows', 0],
            ['AdminHSContacts', 0],
            ['AdminHSDeals', 1],
            ['AdminHSSettings', 1],
        ];
        array_walk(
            $tabs_to_toggle,
            function ($item) {
                $idtab = \Tab::getIdFromClassName($item[0]);
                $tab = new \Tab((int) $idtab);
                $tab->active = $item[1];
                $tab->update();
            }
        );
        Navigator::toDashboard();
    }
}
