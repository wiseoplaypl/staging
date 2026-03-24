<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domainn
*/

class AdminStoreSlipsController extends AdminPdfController
{
    public function processGenerateDeliverySlipsPDF()
    {
        $order_invoice_collection = OrderStoreInvoice::getByDeliveryDateInterval(
            Tools::getValue('date_from'),
            Tools::getValue('date_to'),
            (int) Tools::getValue('id_store')
        );

        if (!count($order_invoice_collection)) {
            die($this->l('No invoice was found'));
        }

        $this->generatePDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP);
    }
}
