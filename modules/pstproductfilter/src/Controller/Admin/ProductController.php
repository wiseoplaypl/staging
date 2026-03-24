<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2020 Presta.Site
 * @license   LICENSE.txt
 */

namespace PrestaShop\Module\PstProductFilter\Controller\Admin;

use Category;
use PrestaShop\PrestaShop\Adapter\Product\ListParametersUpdater;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\Component\CsvResponse;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Product\ProductCategories;
use PrestaShopBundle\Form\Admin\Sell\Product\Category\CategoryFilterType;
use PrestaShopBundle\Security\Voter\PageVoter;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface as ProductInterfaceProvider;
use Symfony\Component\HttpFoundation\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductController extends FrameworkBundleAdminController
{
    const PRODUCT_OBJECT = 'ADMINPRODUCTS_';

    public function listAction(Request $request)
    {
        if (!$this->isGranted([PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE], self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $module = \Module::getInstanceByName('pstproductfilter');
        if ($request->request->has('submitResetproduct')) {
            $module->resetFilters();
        }

        if (!$request->request->get('ajax') && !$request->attributes->get('no_redirect')) {
            return $this->redirect($this->generateUrl('admin_product_catalog', $_GET), '302');
        }

        $vars = $this->getListVars($request);

        return $this->render(
            '@Modules/pstproductfilter/views/templates/admin/form_products.html.twig',
            $vars
        );
    }

    public function listV2Action(Request $request, ProductFilters $filters)
    {
        if (!$this->isGranted([PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE], self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $module = \Module::getInstanceByName('pstproductfilter');
        if ($request->request->has('submitResetproduct')) {
            $module->resetFilters();
        }

        if (!$request->request->get('ajax') && !$request->attributes->get('no_redirect')) {
            return $this->redirect($this->generateUrl('admin_product_catalog', $_GET), '302');
        }

        $productGrid = $this->get('prestashop.core.grid.factory.product')->getGrid($filters);

        $filteredCategoryId = null;
        if (isset($filters->getFilters()['id_category'])) {
            $filteredCategoryId = (int) $filters->getFilters()['id_category'];
        }

        $categoriesForm = $this->createForm(CategoryFilterType::class, $filteredCategoryId, [
            'action' => $this->generateUrl('admin_products_grid_category_filter'),
        ]);

        return $this->render(
            '@Modules/pstproductfilter/views/templates/admin/list.html.twig',
            [
                'categoryFilterForm' => $categoriesForm->createView(),
                'productGrid' => $this->presentGrid($productGrid),
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $this->getProductToolbarButtons($request->get('_legacy_controller')),
                'help_link' => $this->generateSidebarLink('AdminProducts'),
            ]
        );
    }

    public function catalogEmptyAction(Request $request)
    {
        if (!$this->isGranted([PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE], self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $module = \Module::getInstanceByName('pstproductfilter');
        if ($request->request->has('submitResetproduct')) {
            $module->resetFilters();
        }

        $vars = $this->getListVars($request);

        return $this->render(
            '@Modules/pstproductfilter/views/templates/admin/catalog_empty_content.html.twig',
            $vars
        );
    }

    public function itemsAction(Request $request)
    {
        if (!$this->isGranted([PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE], self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $vars = $this->getListVars($request);

        return $this->render(
            '@Modules/pstproductfilter/views/PrestaShop/Admin/Product/CatalogPage/Lists/list.html.twig',
            $vars
        );
    }

    public function theadAction(Request $request)
    {
        if (!$this->isGranted([PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE], self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $module = \Module::getInstanceByName('pstproductfilter');

        $vars = [
            'pstpf_columns' => $module->getColumns(false, true),
        ];
        $vars = array_merge($request->attributes->all(), $vars);

        return $this->render(
            '@Modules/pstproductfilter/views/PrestaShop/Admin/Product/CatalogPage/Lists/thead.html.twig',
            $vars
        );
    }

    public function filtersAction(Request $request)
    {
        if (!$this->isGranted([PageVoter::READ, PageVoter::UPDATE, PageVoter::CREATE], self::PRODUCT_OBJECT)) {
            return $this->redirect('admin_dashboard');
        }

        $module = \Module::getInstanceByName('pstproductfilter');

        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        $combinedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());

        $pstpf_filters = $module->getFilters();
        $pstpf_values = [];
        foreach ($pstpf_filters as $filter_name => $pstpf_filter) {
            if (!empty($pstpf_filter['value'])) {
                $pstpf_values['pstpf_' . $filter_name] = $pstpf_filter['value'];
            }
        }

        $vars = [
            'pstpf_columns' => $module->getColumns(false, true),
            'pstpf_values' => $pstpf_values,
        ];
        $vars = array_merge($combinedFilterParameters, $request->attributes->all(), $vars);

        return $this->render(
            '@Modules/pstproductfilter/views/PrestaShop/Admin/Product/CatalogPage/Lists/filters.html.twig',
            $vars
        );
    }

    protected function getListVars($request, $module = null)
    {
        if (!$module) {
            $module = \Module::getInstanceByName('pstproductfilter');
        }
        if ($request->attributes->get('is_list_override')) {
            $limit = $request->attributes->get('limit');
            $offset = $request->attributes->get('offset');
            $orderBy = $request->attributes->get('orderBy');
            $sortOrder = $request->attributes->get('sortOrder');
        } else {
            $limit = $request->request->get('limit', $request->request->get('paginator_select_page_limit', 'last'));
            if ($request->request->has('paginator_jump_page') && is_numeric($limit)) {
                $p = $request->request->get('paginator_jump_page');
                $offset = ((int) $p - 1) * (int) $limit;
            } else {
                $offset = $request->request->get('offset', 0);
            }
            $orderBy = $request->request->get('orderBy', 'last');
            $sortOrder = $request->request->get('sortOrder', 'last');
        }

        $language = $this->getContext()->language;
        $request->getSession()->set('_locale', $language->locale);
        $request = $this->get('prestashop.adapter.product.filter_categories_request_purifier')->purify($request);

        /** @var $productProvider ProductInterfaceProvider */
        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');

        // Set values from persistence and replace in the request
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        /** @var ListParametersUpdater $listParametersUpdater */
        $listParametersUpdater = $this->get('prestashop.adapter.product.list_parameters_updater');
        $listParameters = $listParametersUpdater->buildListParameters(
            $request->query->all(),
            $persistedFilterParameters,
            compact('offset', 'limit', 'orderBy', 'sortOrder')
        );
        $offset = $listParameters['offset'];
        $limit = $listParameters['limit'];
        $orderBy = $listParameters['orderBy'];
        $sortOrder = $listParameters['sortOrder'];

        // The product provider performs the same merge internally, so we do the same so that the displayed filters are
        // consistent with the request ones
        $combinedFilterParameters = array_replace($persistedFilterParameters, $request->request->all());

        $toolbarButtons = $this->getToolbarButtons();

        // Fetch product list (and cache it into view subcall to listAction)
        $products = $productProvider->getCatalogProductList(
            $offset,
            $limit,
            $orderBy,
            $sortOrder,
            $request->request->all()
        );
        $lastSql = $productProvider->getLastCompiledSql();

        $hasCategoryFilter = $productProvider->isCategoryFiltered();
        $hasColumnFilter = $productProvider->isColumnFiltered();
        $totalFilteredProductCount = (count($products) > 0) ? $products[0]['total'] : 0;
        $totalProductCount = $productProvider->countAllProducts();

        // Pagination
        $paginationParameters = $request->attributes->all();
        $paginationParameters['_route'] = 'admin_product_catalog';
        $categoriesForm = $this->createForm(ProductCategories::class);
        if (!empty($persistedFilterParameters['filter_category'])) {
            $categoriesForm->setData(
                [
                    'categories' => [
                        'tree' => [0 => $combinedFilterParameters['filter_category']],
                    ],
                ]
            );
        }

        $cleanFilterParameters = $listParametersUpdater->cleanFiltersForPositionOrdering(
            $combinedFilterParameters,
            $orderBy,
            $hasCategoryFilter
        );

        $permissionError = null;
        if ($this->get('session')->getFlashBag()->has('permission_error')) {
            $permissionError = $this->get('session')->getFlashBag()->get('permission_error')[0];
        }

        $categoriesFormView = $categoriesForm->createView();
        $selectedCategory = !empty($combinedFilterParameters['filter_category'])
            ? new \Category($combinedFilterParameters['filter_category']) : null;

        // Drag and drop is ONLY activated when EXPLICITLY requested by the user
        // Meaning a category is selected and the user clicks on REORDER button
        $activateDragAndDrop = 'position_ordering' === $orderBy && $hasCategoryFilter;

        $adminProductWrapper = $this->get('prestashop.adapter.admin.wrapper.product');
        // Adds controller info (URLs, etc...) to product list
        foreach ($products as &$product) {
            $product['url'] = $this->generateUrl(
                'admin_product_form',
                ['id' => $product['id_product']]
            );
            $product['unit_action_url'] = $this->generateUrl(
                'admin_product_unit_action',
                [
                    'action' => 'duplicate',
                    'id' => $product['id_product'],
                ]
            );
            $product['preview_url'] = $adminProductWrapper->getPreviewUrlFromId($product['id_product']);
        }

        // for compatibility with third-party modules and their smarty variables
        $legacyContext = $this->get('prestashop.adapter.legacy.context')->getContext();
        $legacyContext->smarty->assign([
            'filters_disabled' => $activateDragAndDrop,
        ]);

        // Template vars injection
        return array_merge(
            $cleanFilterParameters,
            [
                'limit' => $limit,
                'offset' => $offset,
                'orderBy' => $orderBy,
                'sortOrder' => $sortOrder,
                'has_filter' => $hasCategoryFilter || $hasColumnFilter,
                'has_category_filter' => $hasCategoryFilter,
                'selected_category' => $selectedCategory,
                'has_column_filter' => $hasColumnFilter,
                'products' => $products,
                'last_sql' => $lastSql,
                'product_count_filtered' => $totalFilteredProductCount,
                'product_count' => $totalProductCount,
                'activate_drag_and_drop' => $activateDragAndDrop,
                'pagination_parameters' => $paginationParameters,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'categories' => $categoriesFormView,
                'pagination_limit_choices' => $productProvider->getPaginationLimitChoices(),
                'import_link' => $this->generateUrl('admin_import', ['import_type' => 'products']),
                'sql_manager_add_link' => $this->getSqlRequestsURL(),
                'enableSidebar' => true,
                'help_link' => $this->generateSidebarLink('AdminProducts'),
                'is_shop_context' => $this->get('prestashop.adapter.shop.context')->isShopContext(),
                'permission_error' => $permissionError,
                'layoutTitle' => $this->trans('Products', 'Admin.Global'),
                'last_sql_query' => $lastSql,
                'pstpf_columns' => $module->getColumns(false, true),
                'pstpf_with_cols' => version_compare(_PS_VERSION_, '8.0.0', '<'),
            ]
        );
    }

    protected function getSqlRequestsURL()
    {
        // PS 1.7.5 compatibility
        if (version_compare(_PS_VERSION_, '1.7.5.0', '>=') && version_compare(_PS_VERSION_, '1.7.6.0', '<')) {
            return $this->get('prestashop.adapter.legacy.context')->getAdminLink(
                'AdminRequestSql',
                true,
                ['addrequest_sql' => 1]
            );
        }

        return $this->generateUrl('admin_sql_requests_create');
    }

    private function getToolbarButtons()
    {
        $toolbarButtons = [];
        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_product_new'),
            'desc' => $this->trans('New product', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'help' => $this->trans('Create a new product: CTRL+P', 'Admin.Catalog.Help'),
        ];

        return $toolbarButtons;
    }

    public function exportAction()
    {
        $module = \Module::getInstanceByName('pstproductfilter');

        $productProvider = $this->get('prestashop.core.admin.data_provider.product_interface');
        $persistedFilterParameters = $productProvider->getPersistedFilterParameters();
        $orderBy = $persistedFilterParameters['last_orderBy'];
        $sortOrder = $persistedFilterParameters['last_sortOrder'];

        $columns = $module->getColumns(false, true);
        $headers = [];
        foreach ($columns as $key => $column) {
            $headers[$key] = ($column['name'] == 'ID' ? 'id' : $column['name']); // fix for Excel
        }

        // prepare callback to fetch data from DB
        $dataCallback = function ($offset, $limit) use ($productProvider, $orderBy, $sortOrder, $headers) {
            $products =
                $productProvider->getCatalogProductList($offset, $limit, $orderBy, $sortOrder, [], true, false);

            $data = [];
            foreach ($products as $product) {
                $row = [];
                foreach ($headers as $h_key => $column_name) {
                    if ($h_key == 'image' && isset($product['image_link'])) {
                        $product[$h_key] = $product['image_link'];
                    }
                    if (isset($product[$h_key]) && is_array($product[$h_key])) {
                        $glue = ', ';
                        // change the glue if the array is multidimensional
                        if (count($product[$h_key]) != count($product[$h_key], COUNT_RECURSIVE)) {
                            $glue = ' | ';
                        }
                        $row[$h_key] = $this->implodeRecursive($glue, $product[$h_key]);
                    } elseif (isset($product[$h_key]) && is_string($product[$h_key])) {
                        $row[$h_key] = ((isset($product[$h_key]) && $product[$h_key] != '--') ? $product[$h_key] : '');
                    } elseif (isset($product[$h_key]) && is_integer($product[$h_key])) {
                        $row[$h_key] = (string) $product[$h_key];
                    } else {
                        $row[$h_key] = '';
                    }
                }
                if ($row) {
                    $data[] = $row;
                }
            }

            return $data;
        };

        return (new CsvResponse())
            ->setData($dataCallback)
            ->setHeadersData($headers)
            ->setModeType(CsvResponse::MODE_OFFSET)
            ->setFileName('products_' . date('Y-m-d_His') . '.csv');
    }

    public function exportV2Action(ProductFilters $filters)
    {
        $grid = $this->get('prestashop.core.grid.factory.product')->getGrid($filters);
        $module = \Module::getInstanceByName('pstproductfilter');
        $columns = $module->getColumns(false, true);
        $headers = [];
        foreach ($columns as $key => $column) {
            $headers[$key] = ($column['name'] == 'ID' ? 'id' : $column['name']); // fix for Excel
        }

        $data = [];

        foreach ($grid->getData()->getRecords()->all() as $record) {
            $row = [];
            foreach ($headers as $h_key => $column_name) {
                if (isset($record[$h_key]) && is_array($record[$h_key])) {
                    $glue = ', ';
                    // change the glue if the array is multidimensional
                    if (count($record[$h_key]) != count($record[$h_key], COUNT_RECURSIVE)) {
                        $glue = ' | ';
                    }
                    $row[$h_key] = $this->implodeRecursive($glue, $record[$h_key]);
                } elseif (isset($record[$h_key]) && is_string($record[$h_key])) {
                    $row[$h_key] = ((isset($record[$h_key]) && $record[$h_key] != '--') ? $record[$h_key] : '');
                } elseif (isset($record[$h_key]) && is_integer($record[$h_key])) {
                    $row[$h_key] = (string) $record[$h_key];
                } else {
                    $row[$h_key] = '';
                }
            }
            if ($row) {
                $data[] = $row;
            }
        }

        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headers)
            ->setFileName('products_' . date('Y-m-d_His') . '.csv');
    }

    public function implodeRecursive($glue, $array)
    {
        $ret = '';

        foreach ($array as $item) {
            if (is_array($item)) {
                $ret .= $this->implodeRecursive(', ', $item) . $glue;
            } else {
                $ret .= $item . $glue;
            }
        }

        $ret = \Tools::substr($ret, 0, 0 - \Tools::strlen($glue));

        return $ret;
    }

    /**
     * @param string $securitySubject
     *
     * @return array<string, array<string, mixed>>
     */
    private function getProductToolbarButtons(string $securitySubject): array
    {
        $toolbarButtons = [];

        // do not show create button if user has no permissions for it
        if (!$this->isGranted(PageVoter::CREATE, $securitySubject)) {
            return $toolbarButtons;
        }

        $toolbarButtons['add'] = [
            'href' => $this->generateUrl('admin_products_create', ['shopId' => $this->getShopIdFromShopContext()]),
            'desc' => $this->trans('New product', 'Admin.Actions'),
            'icon' => 'add_circle_outline',
            'class' => 'btn-primary new-product-button',
            'floating_class' => 'new-product-button',
            'data_attributes' => [
                'modal-title' => $this->trans('Add new product', 'Admin.Catalog.Feature'),
            ],
        ];

        return $toolbarButtons;
    }

    /**
     * @return int|null
     */
    private function getShopIdFromShopContext()
    {
        /** @var Context $shopContext */
        $shopContext = $this->get('prestashop.adapter.shop.context');
        $shopId = $shopContext->getContextShopID();

        return !empty($shopId) ? (int) $shopId : null;
    }
}
