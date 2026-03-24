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

include_once _PS_MODULE_DIR_ . '/aftermailpresta/classes/AfterMailConfig.php';
include_once _PS_MODULE_DIR_ . '/aftermailpresta/classes/AfterMailLog.php';

class AdminAfterMailController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'aftermail_conf';
        $this->className = 'AfterMailConfig';
        $this->lang = false;
        $this->identifier = 'id_aftermail_conf';
        $this->deleted = false;
        $this->context = Context::getContext();
        $this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        $this->fields_list = array(
            'id_aftermail_conf' => array(
                'title' => $this->l('ID'),
                'align' => 'center'),
            'name' => array(
                'title' => $this->l('Name')),
            'delay' => array(
                'title' => $this->l('Delay')),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false));
    }

    public function setMedia($isnewTheme = false)
    {
        parent::setMedia($isnewTheme);

        $this->addJS(_PS_MODULE_DIR_ . '/aftermailpresta/views/js/configBack.js');
    }

    public function initListAfterMails()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->toolbar_title = $this->l('List of AfterMail entries');
    }

    public function initListAfterMailQueue()
    {
        $this->toolbar_title = $this->l('AfterMail Queue');

        // reset actions and query vars
        $this->actions = array();
        unset($this->fields_list, $this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);

        $this->table = 'aftermail_queue';
        $this->identifier = 'id_aftermail_queue';
        $this->list_id = 'aftermail_queue';
        $this->deleted = false;
        $this->_orderBy = null;

        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')));

        // test if a filter is applied for this list
        if (Tools::isSubmit('submitFilter' . $this->table) || $this->context->cookie->{'submitFilter' . $this->table} !== false) {
            $this->filter = true;
        }

        // test if a filter reset request is required for this list
        if (Tools::getValue('submitReset' . $this->table)) {
            $this->action = 'reset_filters';
        } else {
            $this->action = '';
        }

        // Sub tab queue

        // subtab for mail logs
        $this->fields_list = array(
            'id_aftermail_queue' => array(
                'title' => $this->l('Log ID'),
                'align' => 'center'),
            'aftermail_conf' => array(
                'title' => $this->l('Aftermail ID'),
                'align' => 'center'),
            'name' => array(
                'title' => $this->l('Mail Name'),
                'align' => 'center'),
            'id_order' => array(
                'title' => $this->l('Order ID'),
                'align' => 'center'),
            'lastname' => array(
                'title' => $this->l('Customer'),
                'align' => 'center'),
            'timestamp_tosend' => array(
                'title' => $this->l('Schedule'),
                'type' => 'datetime',
                'align' => 'center'),
            'send_state' => array(
                'title' => $this->l('Sent'),

                // 'icon' => array(0 => 'disabled.gif', 1 => 'enabled.gif', 'default' => 'disabled.gif'),
                'type' => 'bool',
                'active' => 'status',
                'orderby' => false));

        $this->_select = 'a.`id_aftermail_conf` as aftermail_conf, c.`lastname` as lastname, am.name';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'aftermail_conf` am ON (am.`id_aftermail_conf` = a.`id_aftermail_conf`)';

        $this->toolbar_title = $this->l('AdminAfterMail Queue:');
        // call postProcess() for take care about actions and filters
        $this->postProcess();
        $this->initToolbar();
        $this->toolbar_btn = null;
    }

    public function renderList()
    {
        $this->initListAfterMails();
        $lists = parent::renderList();

        $this->_filter = false;
        $this->initListAfterMailQueue();

        // call postProcess() to take care of actions and filters
        $this->postProcess();

        parent::initToolbar();
        $lists .= parent::renderList();

        return $lists;
    }

    /**
     * AdminController::init() override
     *
     * @see AdminController::init()
     */
    public function init()
    {
        parent::init();
        if (Tools::isSubmit('deleteaftermail_queue')) {
            $this->action = 'delete';
        }
    }

    public function initProcess()
    {
        if (Tools::isSubmit('deleteaftermail_queue') || Tools::isSubmit('submitBulkdeleteaftermail_queue') || Tools::isSubmit('submitBulkdelete')) {
            $this->table = 'aftermail_queue';
            $this->identifier = 'id_aftermail_queue';
            $this->className = 'AfterMailLog';
            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'confirm' => $this->l('Delete selected items?')));
        }
        parent::initProcess();
    }

    public function renderForm()
    {
        $attachments = Attachment::getAttachments((int) ($this->context->language->id), -1, false);
        array_unshift($attachments, '-');

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('AfterMail :')), // ,
            // 'image' => '../img/admin/manufacturers.gif'
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name:'),
                    'name' => 'name',
                    'size' => 40,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}'),
                array(
                    'type' => 'text',
                    'label' => $this->l('Delay (minutes)'),
                    'name' => 'delay',
                    'size' => 7,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}'),
                array(
                    'type' => 'select',
                    'label' => $this->l('Trigger'),
                    'name' => 'trigger_type',
                    'required' => false,
                    'onchange' => 'showTriggerStates();',
                    'default_value' => 0,
                    'options' => array(
                        'query' => $this->getTriggerState(),
                        'id' => 'value',
                        'name' => 'name')),
                array(
                    'type' => 'select',
                    'label' => '<br>',
                    'name' => 'id_orderstate',
                    'required' => false,
                    'options' => array(
                        'query' => OrderState::getOrderStates((int) ($this->context->language->id)),
                        'id' => 'id_order_state',
                        'name' => 'name')),
                array(
                    'type' => 'text',
                    'label' => $this->l('days of frequencies'),
                    'name' => 'reminder_frequency',
                    'class' => 'subscription',
                    'required' => false,
                    'size' => 20,
                    'class' => 'subscription',
                    'hint' => $this->l('comma separeted list of frequencies in days')),
                array(
                    'type' => 'text',
                    'label' => $this->l('IDs to show for.'),
                    'name' => 'subscribe_ids',
                    'required' => false,
                    'size' => 20,
                    'class' => 'subscription',
                    'hint' => $this->l('For these comma separated product ids a subscription button will be shown, enter 0 for all')),
                array(
                    'type' => 'select_attachment',
                    'label' => $this->l('Attachment'),
                    'name' => 'id_attachment',
                    'lang' => true,
                    'required' => false,
                    'options' => array(
                        'query' => $attachments,
                        'id' => 'id_attachment',
                        'name' => 'name')),
                array(
                    'type' => 'text',
                    'label' => $this->l('Subject:'),
                    'name' => 'subject',
                    'size' => 70,
                    'required' => true,
                    'lang' => true,
                    'hint' => $this->l('The subject of your e-mail')),
                array(
                    'type' => 'text',
                    'label' => $this->l('Min cart value'),
                    'name' => 'min_cart_sum',
                    'size' => 10,
                    'required' => false,
                    'lang' => false,
                    'hint' => $this->l('The cart value must be at least of this value')),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('HTML E-Mail:'),
                    'name' => 'e_mail_text_html',
                    'cols' => 48,
                    'rows' => 7,
                    'autoload_rte' => true,
                    'required' => false,
                    'lang' => true),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('TXT E-Mail:'),
                    'name' => 'e_mail_text_txt',
                    'cols' => 48,
                    'rows' => 7,
                    'autoload_rte' => false,
                    'required' => false,
                    'lang' => true),
                array(
                    'type' => 'select',
                    'label' => $this->l('Create a voucher'),
                    'name' => 'voucher',
                    'default_value' => 0,
                    'required' => false,
                    'onchange' => 'showVoucherState();',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 0,
                                'name' => $this->l('No')),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Yes'))),
                        'id' => 'id_option',
                        'name' => 'name')),
                array(
                    'type' => 'select',
                    'label' => $this->l('Type of voucher'),
                    'name' => 'vouchertype',
                    'required' => false,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 0,
                                'name' => $this->l('Percent')),
                            array(
                                'id_option' => 1,
                                'name' => $this->l('Amount'))),
                        'id' => 'id_option',
                        'name' => 'name')),
                array(
                    'type' => 'text',
                    'label' => $this->l('Amount'),
                    'name' => 'voucheramount',
                    'size' => 4,
                    'required' => false,
                    'hint' => $this->l('Only Numbers allowed')),
                array(
                    'type' => 'text',
                    'label' => $this->l('How many days is the voucher valid'),
                    'name' => 'voucherdays',
                    'size' => 4,
                    'required' => false,
                    'hint' => $this->l('Only Numbers allowed')),
                array(
                    'type' => 'text',
                    'label' => $this->l('Name of the voucher'),
                    'name' => 'vouchername',
                    'size' => 10,
                    'required' => false,
                    'hint' => $this->l('i.e. Thank you discount')),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Restrict voucher to buyer'),
                    'name' => 'restrictcustomer',
                    'required' => false,
                    'class' => 't',
                    'values' => array(
                        array(
                            'id' => 'restrict_on',
                            'value' => 1,
                            'label' => $this->l('Restrict')),
                        array(
                            'id' => 'restrict_off',
                            'value' => 0,
                            'label' => $this->l('Do not restrict')))),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Enable:'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'))))),
            'submit' => array(
                'title' => $this->l('Save')));

        if (!($aftermailObj = $this->loadObject(true))) {
            return;
        }

        if (isset($aftermailObj->id) && $aftermailObj->id != -1 && $aftermailObj->trigger_type != 4) {
            $this->context->smarty->assign('filterConfiguration', $this->showCombiConf($aftermailObj));
        }

        return parent::renderForm();
    }

    /**
     * AdminController::initToolbar() override
     *
     * @see AdminController::initToolbar()
     *
     */
    public function initToolbar()
    {
        parent::initToolbar();
    }

    private function getTriggerState()
    {
        $result = array(
            array(
                'id' => 'trigger_type',
                'value' => '0',
                'name' => $this->l('1. Moment of order creation')),
            array(
                'id' => 'trigger_type',
                'value' => '1',
                'name' => $this->l('2. When order switches to state:')),
            array(
                'id' => 'trigger_type',
                'value' => '2',
                'name' => $this->l('3. Product Subscription')),
            array(
                'id' => 'trigger_type',
                'value' => '3',
                'name' => $this->l('4. User Birthday')),
            array(
                'id' => 'trigger_type',
                'value' => '4',
                'name' => $this->l('5. User logs in')));
        return $result;
    }

    private function showCombiConf($aftermailObj)
    {
        $ajaxfile = _MODULE_DIR_ . 'aftermailpresta/aftermailajax.php';

        $tablecontent = $this->createConfigTableEntries($aftermailObj);
        $onlyBaseCat = false;
        $html = '<br><br>	<div class="leadin"></div>
		<fieldset id="fieldset_1">

		<legend>' . $this->l('Filter Configuration') . '</legend>
			<table border="0" cellpadding="0" cellspacing="0" class="table">
				<tr>
					<th>' . $this->l('ID') . '</th>
					<th>' . $this->l('Category') . '</th>
					<th>' . $this->l('Product') . '</th>
					<th>' . $this->l('Variant') . '</th>
					<th class="center">' . $this->l('Actions') . '</th>
				</tr>';
        foreach ($tablecontent as $row) {
            $html .= '
			<tr>
				<td>' . $row['id'] . '</td>
				<td>' . $row['cat_name'] . '</td>
				<td>' . $row['prod_name'] . '</td>
				<td>' . $row['variant_name'] . '</td>';
            $catIds = '';
            $prodIds = '';
            $variantIds = '';

            if (is_array($row['cat_id'])) {
                $catIds = implode(',', $row['cat_id']);
            } else {
                $catIds = $row['cat_id'];
            }

            if (is_array($row['prod_id'])) {
                $prodIds = implode(',', $row['prod_id']);
            } else {
                $prodIds = $row['prod_id'];
            }

            if (is_array($row['variant_id'])) {
                $variantIds = implode(',', $row['variant_id']);
            } else {
                $variantIds = $row['variant_id'];
            }

            $html .= '<td class="center">
					<a style="cursor: pointer;">
						<img src="../img/admin/edit.gif" alt="' . $this->l('Modify this Filter') . '" onclick="javascript: currentFilterID=' . $row['id'] . '; loadCats(' . $row['id'] . ',[' . $catIds . '],[' . $prodIds . '],[' . $variantIds . ']);  showFilterSaveButton(); " />
					</a>
					<a href="' . AdminController::$currentIndex . '&deleteFilter&filterID=' . $row['id'] . '&id_aftermail_conf=' . $aftermailObj->id . '&token=' . Tools::getAdminToken('AdminAfterMail' . (int) (Tab::getIdFromClassName('AdminAfterMail')) . (int) ($this->context->employee->id)) . '" onclick="return confirm(\'' . $this->l('Are you sure?', __CLASS__, true, false) . '\');">
						<img src="../img/admin/delete.gif" alt="' . $this->l('Delete this filter') . '" />
					</a>
					</td>
				</tr>';

            if ($row['cat_id'] == -1) {
                $onlyBaseCat = true;
                break;
            }
        }
        $html .= '</table>';
        // if no special category defined, we can't add more filter rules.
        if (!$onlyBaseCat) {
            $html .= '<br><input value="' . $this->l('    Add new filter    ') . '" class="button" onclick="loadCats(-1,-1);showFilterSaveButton();" type="button">';
        }

        $html .= '
			<script type="text/javascript">
				function loadCats(filterid, selectedCategories, selectedProducts,selectedVariants) {
					var myAction="loadCategories";
					$("#categories").load("' . $ajaxfile . '",{action:myAction,id_filter: currentFilterID},
						function() {
							fillFilter(filterid,selectedCategories,selectedProducts,selectedVariants,"' . $ajaxfile . '");
						}
					);
				}
				var currentFilterID = -1;
	  			$(document).ready(function() {

	      			$("#categories").change(function(){

	        			var id_category=$(this).children(\'option:selected\').val();
	        			var multi= isMultiSelected("categories");
	        			if (id_category=="-1" || multi) {
	        				setList2All("products");
	        				setList2All("attributes");
	        				$("#product_config").hide();
	        				$("#variant_config").hide();
	        				return;
	        			}
	        			var myAction="loadProducts";
	        			$("#products").load("' . $ajaxfile . '",{action:myAction,category: id_category},function() {selectEntry("products","-1");});
	      			});

	      			$("#products").change(function(){

	        			var id_product = $(this).children(\'option:selected\').val();
	        			var id_filter = currentFilterID;
	        			if (typeof id_product == \'undefined\') {
	        				id_product = -1;
	        			}
	        			if (typeof id_filter == \'undefined\') {
	        				id_filter = -1;
	        			}
	        			var multi= isMultiSelected("products");
	        			if (id_product=="-1" || multi) {
	        				setList2All("attributes");
	        				$("#variant_config").hide();
	        				return;
	        			}else {
	        				$("#variant_config").show();
	        				var myAction="loadAttributes";
	        				$("#attributes").load("' . $ajaxfile . '",{action:myAction,product: id_product,filter:id_filter},function() {selectEntry("attributes","-1");});
						}
	      			});
	  			});
			</script>';

        $html .= '<div id="category_config" style="display:none;">
		 		<form id="aftermail_filter_new" enctype="multipart/form-data" name="aftermail_filter_new" onsubmit="return setCurrentFilter(this)" action="' . AdminController::$currentIndex . '&saveFilterConf&' . $this->table . '=1&token=' . $this->token . '" method="post" class="form">
					<input type="hidden" name="filterid" id="filterid" />
					<input type="hidden" name="id_aftermail_conf" id="id_aftermail_conf" value="' . $aftermailObj->id . '" />
				<br><label>' . $this->l('Category Configuration') . '</label>
				<select name="categories[]" id="categories" multiple style="width: 480px;" size="10" onchange="showOrHideProductAttrs();"></select>
			</div><br>';

        $html .= '<div id="product_config" style="display:none;">
				<label>' . $this->l('Product Configuration') . '</label>';
        $html .= '<select name="products[]" id="products" multiple style="width: 480px;" size="10">
			  	</select></div><br>';
        $html .= '<div id="variant_config" style="display:none;">
			<label>' . $this->l('Variant Configuration') . '</label>
					<select name="attributes[]" id="attributes" multiple style="width: 480px;" size="10">
			  </select></div>
			<div id="saveFilterButton" class="margin-form" style="display:none;">
			<input name="saveFilterConf" type="submit" style="width: 150px;" value="' . $this->l('Save Filter') . '" class="button" type="button"></div>
			</form>
			</fieldset>
		</div>';
        return $html;
    }

    /**
     * returns the array with filter configuration
     * array has id, cat_name,cat_id,prod_name,prod_id,variant_name, variant_id as keys
     */
    private function createConfigTableEntries($aftermailObj)
    {
        $entries = array();
        $generalFilter = $aftermailObj->getGeneralMail();
        $catFilter = $aftermailObj->getEmptyCategoriesForMail();
        $prodFilter = $aftermailObj->getEmptyProductsForMail();
        $variantFilter = $aftermailObj->getVariantsForMail();

        foreach ($generalFilter as $row) {
            array_push($entries, $row);
        }

        foreach ($catFilter as $row) {
            array_push($entries, $row);
        }

        foreach ($prodFilter as $row) {
            array_push($entries, $row);
        }

        foreach ($variantFilter as $row) {
            array_push($entries, $row);
        }

        return $entries;
    }

    public function postProcess($token = null)
    {
        if (Tools::isSubmit('deleteFilter')) {
            if (!($aftermailObj = $this->loadObject(true))) {
                return;
            }

            $aftermailObj->deleteFilter(Tools::getValue('filterID'));

            Tools::redirectAdmin(AdminController::$currentIndex . '&id_aftermail_conf=' . $aftermailObj->id_aftermail_conf . '&updateaftermail_conf&token=' . ($token ? $token : $this->token));
        } elseif (Tools::isSubmit('saveFilterConf')) {
            if (!($aftermailObj = $this->loadObject(true))) {
                return;
            }

            $aftermailObj->saveFilter(Tools::getValue('filterid'), Tools::getValue('categories'), Tools::getValue('products'), Tools::getValue('attributes'));

            Tools::redirectAdmin(AdminController::$currentIndex . '&id_aftermail_conf=' . $aftermailObj->id_aftermail_conf . '&updateaftermail_conf&token=' . ($token ? $token : $this->token));
        } elseif (Tools::isSubmit('submitAddaftermail_conf')) {
            parent::postProcess($token);
            if (!($aftermailObj = $this->loadObject(true))) {
                return;
            }

            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $aftermailObj->subject[$language['id_lang']] = Tools::getValue('subject_' . $language['id_lang']);
                $aftermailObj->e_mail_text_txt[$language['id_lang']] = Tools::getValue('e_mail_text_txt_' . $language['id_lang']);
                $aftermailObj->e_mail_text_html[$language['id_lang']] = Tools::getValue('e_mail_text_html_' . $language['id_lang']);
                $aftermailObj->id_attachment[$language['id_lang']] = Tools::getValue('id_attachment_' . $language['id_lang']);
            }
            $aftermailObj->trigger_type = Tools::getValue('trigger_type');
            $aftermailObj->id_orderstate = Tools::getValue('id_orderstate');
            $aftermailObj->delay = Tools::getValue('delay');
            $aftermailObj->name = Tools::getValue('name');
            $aftermailObj->active = Tools::getValue('active');
            $aftermailObj->min_cart_sum = Tools::getValue('min_cart_sum');

            if ($aftermailObj->trigger_type === '2') {
                $aftermailObj->reminder_frequency = Tools::getValue('reminder_frequency');
                $aftermailObj->subscribe_ids = Tools::getValue('subscribe_ids');
            } else {
                $aftermailObj->reminder_frequency = '';
                $aftermailObj->subscribe_ids = '';
            }

            $aftermailObj->voucher = Tools::getValue('voucher');

            if ($aftermailObj->voucher == '1') {
                $aftermailObj->vouchertype = Tools::getValue('vouchertype');
                $aftermailObj->voucheramount = Tools::getValue('voucheramount');
                $aftermailObj->voucherdays = Tools::getValue('voucherdays');
                $aftermailObj->vouchername = Tools::getValue('vouchername');
                if (Tools::getValue('restrictcustomer')) {
                    $aftermailObj->restrictcustomer = 1;
                }
            } else {
                $aftermailObj->vouchertype = '';
                $aftermailObj->voucheramount = '';
                $aftermailObj->voucherdays = '';
                $aftermailObj->vouchername = '';
                $aftermailObj->restrictcustomer = 0;
            }

            $aftermailObj->save();
        } else {
            parent::postProcess($token);
        }
    }

    protected function afterAdd($object)
    {
        if (!($aftermailObj = $this->loadObject(true))) {
            return;
        }

        $aftermailObj->id_aftermail_conf = $object->id;

        $this->saveFile('' . $aftermailObj->id_aftermail_conf, $aftermailObj);

        return parent::afterAdd($aftermailObj);
    }

    protected function afterUpdate($obj)
    {
        if (!($aftermailObj = $this->loadObject(true))) {
            return;
        }

        $this->saveFile('' . $aftermailObj->id_aftermail_conf, $aftermailObj);

        return parent::afterUpdate($aftermailObj);
    }

    private function saveFile($filename, $data)
    {
        $tmpLanguages = Language::getLanguages(false);
        foreach ($tmpLanguages as $lang) {
            if (!file_exists(_PS_MODULE_DIR_ . 'aftermailpresta/mails/' . $lang['iso_code'] . '/')) {
                if (!mkdir(_PS_MODULE_DIR_ . 'aftermailpresta/mails/' . $lang['iso_code'] . '/', 0755)) {
                    die('Please create a "' . $lang['iso_code'] . '" directory in ' . _PS_MODULE_DIR_ . 'aftermailpresta/mails/');
                }
            }

            $dir = 'aftermailpresta/mails/' . $lang['iso_code'] . '/';

            $my_file_html = _PS_MODULE_DIR_ . $dir . $filename . '.html';
            $my_file_txt = _PS_MODULE_DIR_ . $dir . $filename . '.txt';

            $handle = fopen($my_file_html, 'w+');

            $dirname = _PS_THEME_DIR_ . 'modules/' . $dir;

            if (!file_exists($dirname)) {
                mkdir(_PS_THEME_DIR_ . 'modules/' . $dir, 0755, true);
            }

            $handleTheme = fopen($dirname . $filename . '.html', 'w+');

            if (!$handle) {
                Logger::addLog('Error occurred while saving html mail: ' . $my_file_html);
                die('Cannot open file:  ' . Tools::displayError('Error occurred while saving module html mail: ' . $my_file_html));
            }

            if (!$handleTheme) {
                Logger::addLog('Error occurred while saving html mail: ' . _PS_THEME_DIR_ . 'modules/' . $dir . $filename . '.html');
                die('Cannot open file:  ' . Tools::displayError('Error occurred while saving theme html mail: ' . _PS_THEME_DIR_ . 'modules/' . $dir . $filename . '.html'));
            }

            fwrite($handle, $this->getFieldValue($data, 'e_mail_text_html', $lang['id_lang']));
            fclose($handle);
            fwrite($handleTheme, $this->getFieldValue($data, 'e_mail_text_html', $lang['id_lang']));
            fclose($handleTheme);

            $handle = fopen($my_file_txt, 'w+');
            $handleTheme = fopen(_PS_THEME_DIR_ . 'modules/' . $dir . $filename . '.txt', 'w+');
            if (!$handle || !$handleTheme) {
                Logger::addLog('Error occurred while saving txt mail');
                die('Cannot open file:  ' . Tools::displayError('Error occurred while saving txt mail'));
            }

            if (!$handle) {
                Logger::addLog('Error occurred while saving txt mail: ' . $my_file_txt);
                die('Cannot open file:  ' . Tools::displayError('Error occurred while saving module txt mail: ' . $my_file_txt));
            }

            if (!$handleTheme) {
                Logger::addLog('Error occurred while saving txt mail: ' . _PS_THEME_DIR_ . 'modules/' . $dir . $filename . '.txt');
                die('Cannot open file:  ' . Tools::displayError('Error occurred while saving theme txt mail: ' . _PS_THEME_DIR_ . 'modules/' . $dir . $filename . '.txt'));
            }

            fwrite($handle, trim(strip_tags($this->getFieldValue($data, 'e_mail_text_txt', $lang['id_lang']))));
            fclose($handle);
            fwrite($handleTheme, trim(strip_tags($this->getFieldValue($data, 'e_mail_text_txt', $lang['id_lang']))));
            fclose($handleTheme);
        }
    }

    /**
     * Overrides parent to delete items from sublist
     *
     * @return mixed
     */
    public function processBulkDelete()
    {
        $this->table = 'aftermail_queue';
        $this->identifier = 'id_aftermail_queue';
        $this->className = 'AfterMailLog';
        $this->boxes = Tools::getValue($this->table . 'Box');

        return parent::processBulkDelete();
    }
}
