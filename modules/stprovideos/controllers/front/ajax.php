<?php
class StProVideosAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $id_product = (int)Tools::getValue('id_product');
        $product = new Product($id_product, false, Configuration::get('PS_LANG_DEFAULT'), $this->context->shop->id);
        $video_data = $this->module->getVideosByProduct($product);
        ob_end_clean();
        header('Content-Type: application/json');
        die(json_encode($video_data));
    }
}