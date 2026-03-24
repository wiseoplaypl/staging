<?php
/**
 * aktualizujstanyproduktow.php
 * File generated with module files generator by SzpaQ <dev-bot>
 * This file is part od module dasdas (dsadsad)
 * @author asdasd
 * @copyright 2018 asdasd
 * @license asdasdas
 * */


if (!defined('_PS_VERSION_')) {
    exit;
}

class SynchronizestockAktualizujStanyProduktowModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }
    public function initContent()
    {
        if (Tools::getIsset('mezz')) {
            $this->processMezz();
        }
        if (Tools::getIsset('cron')) {
            $this->processCron();
            /**/
            header('Content-Type: application/json');
            echo json_encode('ok');
            exit;
            /**/
        }
        if (Tools::getIsset('reference') && Tools::getIsset('exists')) {
            /**/
            header('Content-Type:application/json');
            echo json_encode([
                'exists' => (bool) Db::getInstance()->getValue("
                    SELECT id_product FROM "._DB_PREFIX_."product WHERE reference LIKE '". pSQL(Tools::getValue("reference"))."'
                ")
            ]);
            exit;
            /**/
        }
        exit;
        $module = Module::getInstanceByName('synchronizestock');
        if (Tools::getIsset('missing')) {
            /**/
            header('Content-Type: application/json');
            echo json_encode('ok');

        }
        $products = Db::getInstance()->executeS("SELECT id_product id from ". _DB_PREFIX_ ."product");
        foreach ($products as $v) {
            $quantity = StockAvailable::getQuantityAvailableByProduct(
                $v['id']
            );
        }
        exit;
    }
    public function processCron()
    {
        set_time_limit(0);
        Context::getContext()->returnCronLogs = true;
        Context::getContext()->isCronTask = true;
        $time = time();
        $products = Db::getInstance()->executeS("SELECT id_product,reference FROM ". _DB_PREFIX_ ."product ORDER BY id_product DESC");
        $mezz = [];
        $logs = [];
        foreach ($products as $v) {
            $logs[] = [
                $v['reference'],
                StockAvailable::getQuantityAvailableByProduct($v['id_product']),
            ];
         //   echo $v['reference'] ."\n";
        }
        /**/

        header('Content-Type: application/json');
        echo json_encode(['CHECKED-PRODUCTS' => count($logs), 'execution-time'=> time() - $time]);
        exit;
        /**/
    }
    public function processMezz()
    {
        $page = Tools::getValue('page', 1);
    }
    public function insertLog($v, $log)
    {
        if (isset($log['disabled'])) {
            $message = $v['reference'] .': Product disabled ';
        } elseif (isset($log['enabled'])) {
            $message = $v['reference'] .': Product enabled ';
        }
        if (isset($log['reason'])) {
            $message .= '- '. $log['reason'];
        }
        $date = date('Y-m-d H:i:s');
        return Db::getInstance()->execute("
            INSERT INTO ". _DB_PREFIX_ ."log (
                `id_log`,
                `severity`,
                `error_code`,
                `message`,
                `object_type`,
                `object_id`,
                `id_employee`,
                `date_add`,
                `date_upd`
            ) VALUES (
                NULL,
                '1',
                '0',
                'CRONJOB!! ". pSQL($message) ."',
                'Product',
                '". $v['id_product']."',
                '30',
                '$date',
                '$date'
            )
        ");
    }
}
