<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class CreativeElementsPreviewModuleFrontController extends ModuleFrontController
{
    protected $uid;

    protected $title;

    public function init()
    {
        parent::init();

        $this->uid = CreativeElements::getPreviewUId(false);

        if (!$this->uid) {
            Tools::redirect('index.php?controller=404');
        }
    }

    public function initContent()
    {
        $model = $this->uid->getModel();

        if ('CEContent' === $model) {
            $this->warning[] = CESmarty::get(_CE_TEMPLATES_ . 'admin/admin.tpl', 'ce_undefined_position');
        }
        $post = CE\get_post($this->uid);

        $this->title = $post->post_title;
        $this->context->smarty->assign($model::${'definition'}['table'], [
            'id' => $post->_obj->id,
            'content' => '',
        ]);

        if ($id_product = (int) Tools::getValue('id_product')) {
            $presenter_class = version_compare(_PS_VERSION_, '1.7.5', '<')
                ? '\PrestaShop\PrestaShop\Core\Product\ProductPresenter'
                : '\PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductPresenter';
            $presenter = new $presenter_class(
                new ImageRetriever($this->context->link),
                $this->context->link,
                new PriceFormatter(),
                new ProductColorsRetriever(),
                $this->context->getTranslator()
            );
            $this->context->smarty->assign('product', $presenter->present(
                (new ProductPresenterFactory($this->context))->getPresentationSettings(),
                (new ProductAssembler($this->context))->assembleProduct(['id_product' => $id_product]),
                $this->context->language
            ));
        }

        parent::initContent();

        $this->title = $post->post_title;
        $this->context->smarty->addTemplateDir(_CE_TEMPLATES_);
        $this->context->smarty->assign([
            'HOOK_LEFT_COLUMN' => '',
            'HOOK_RIGHT_COLUMN' => '',
            'breadcrumb' => $this->getBreadcrumb(),
        ]);
        $this->template = 'front/preview.tpl';
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [
            'links' => [
                ['url' => 'javascript:;', 'title' => 'Creative Elements'],
                ['url' => 'javascript:;', 'title' => CE\__('Preview')],
            ],
        ];
        if (!empty($this->title)) {
            $breadcrumb['links'][] = ['url' => 'javascript:;', 'title' => $this->title];
        }

        return $breadcrumb;
    }

    public function getBreadcrumbPath()
    {
        $breadcrumb = $this->getBreadcrumbLinks();

        return CESmarty::capture(_CE_TEMPLATES_ . 'admin/admin.tpl', 'ce_preview_breadcrumb', [
            'links' => $breadcrumb['links'],
        ]);
    }
}
