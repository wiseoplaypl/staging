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

use Tiralineas\PsHubspot\DealPipeline;
use Tiralineas\PsHubspot\Navigator;

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once _PS_MODULE_DIR_ . 'pshubspot/controllers/admin/AdminHSAbstractController.php';
require_once _PS_MODULE_DIR_ . 'pshubspot/model/HsPipeline.php';

class AdminHSDealsController extends AdminHSAbstractController
{
    protected $slug = 'hs_deals';

    public function __construct()
    {
        parent::__construct();
        $this->meta_title = [$this->l('Deals')];
        $this->context = Context::getContext();
        $this->bootstrap = true;
    }

    public function postProcess()
    {
        if (((bool) Tools::isSubmit('ps-hubspot_save_pipeline_mapping')) == true) {
            foreach (Tools::getValue('mappings') as $key => $value) {
                $stage = new HsPipeline($key);
                $stage->sync_as = $value;
                $stage->save();
            }
        }
        if (((bool) Tools::isSubmit('ps-hubspot_save_deal_filter')) == true) {
            $keys = [
                'PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED',
                'PS_HUBSPOT_DEAL_DATE_FILTER',
                'PS_HUBSPOT_DEALSABANDONED_SENIORITY',
            ];
            foreach ($keys as $key) {
                if (
                    $key == 'PS_HUBSPOT_DEALSABANDONED_SENIORITY'
                    && Tools::getValue($key) <= 0
                ) {
                    continue;
                }
                \Configuration::updateValue(
                    $key,
                    Tools::getValue($key),
                    false,
                    '',
                    ''
                );
            }
            Configuration::updateValue('PS_HUBSPOT_MIGRATION_DATE_FROM', date('Y-m-d H:m', strtotime(Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER', '', '', ''))));
        }
    }

    public function process()
    {
        if (\Tiralineas\PsHubspot\Connection::fetchAccessTokenFromCode()) {
            Navigator::toSetup();
        }
    }

    public function renderView()
    {
        $stages = DealPipeline::getPipelineById(\Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', ''), true);

        Context::getContext()->smarty->assign(
            [
                'hub_id' => \Configuration::get('PS_HUBSPOT_TOKEN_HUB_ID', '', '', ''),
                'mappings' => HsPipeline::getCurrentMapping(),
                // 'stages' => json_decode(Tools::file_get_contents(_PS_MODULE_DIR_ . 'ps_hubspot/commerce_pipeline.json'))->stages,
                'stages' => $stages,
                'PS_HUBSPOT_DEAL_PIPELINE_LABEL' => \Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_LABEL', '', '', '') ?: \Configuration::get('PS_HUBSPOT_DEAL_PIPELINE_ID', '', '', ''),
                'PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED' => \Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED', '', '', ''),
                'PS_HUBSPOT_DEAL_DATE_FILTER' => \Configuration::get('PS_HUBSPOT_DEAL_DATE_FILTER', '', '', ''),
                'PS_HUBSPOT_DEALSABANDONED_SENIORITY' => \Configuration::get('PS_HUBSPOT_DEALSABANDONED_SENIORITY', '', '', ''),
            ]
        );

        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . 'pshubspot/views/templates/admin/hs_deals/view.tpl');
    }
}
