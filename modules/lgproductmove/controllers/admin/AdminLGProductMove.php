<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

class AdminLGProductMoveController extends ModuleAdminController
{
    const OPERATION_MOVE = 1;
    const OPERATION_COPY = 2;
    const NUMBER_OF_PRODUCTS = 10;

    protected $categoria_origen         = 0;
    protected $categoria_destino        = 0;
    protected $operacion                = 0;
    protected $selected_products        = array();
    protected $selected_pagination      = 2;
    protected $last_selected_pagination = 1;
    protected $last_offset              = 1;
    protected $limit                    = 100;
    protected $offset                   = 1;
    protected $all_selected             = false;

    protected $categorias          = array();
    protected $marcas              = array();
    protected $productos           = array();
    protected $prodscat            = array();
    protected $pagination          = array();
    protected $filters             = array();
    protected $total_pages         = 0;
    protected $total_products      = 0;
    protected $response_status     = 'ok';

    private $security_token        = 'algo';

    protected $debug               = false;
    protected $get_times           = false; // Enable to get times consumed on ajax products copy/move on response

    public function __construct()
    {
        $this->module = 'lgproductmove';
        $this->bootstrap  = true;

        parent::__construct();

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->id_lang = $this->context->cookie->id_lang;

        // Inicializamos lo necesario
        $this->categoria_origen      = (int)Tools::getValue('categoria_origen', 0);
        $this->categoria_destino     = (int)Tools::getValue('categoria_destino', 0);
        $this->operacion             = (int)Tools::getValue('accion', 0);
        $this->all_selected          = (int)Tools::getValue('select_all', 0);
        if ($this->all_selected) {
            if ($this->categoria_origen > 0) {
                $cat = new Category($this->categoria_origen);
                $aux = array();
                $aux = $cat->getProductsWs();
                foreach ($aux as $product) {
                    $this->selected_products[] = $product['id'];
                }
            }
        } else {
            $this->selected_products = Tools::getValue('selected_products', array());
        }
        $this->selected_pagination   = Tools::getValue('lgproductmove_pagination', self::NUMBER_OF_PRODUCTS);
        $this->limit                 = $this->selected_pagination;
        $this->offset                = Tools::getValue('p', 1);
        $this->productos             = array();
        $this->prodscat              = array();
        $this->pagination            = array(2, 5,10,100,500,1000);
        $this->total_pages           = 0;
        $this->total_products        = 0;
        $this->categorias            = $this->catTreeNew();
        $this->filters               = Tools::getValue('filters', array());
        $this->security_token        = md5('algo');

        // Para la versión 1.5, las variables se han de meter en plantilla
//        if (version_compare(_PS_VERSION_, '1.6', '<')) {
        $this->context->smarty->assign(
            array(
                // Variables para javascript
                'lgproductmove_token'                    => Tools::getAdminTokenLite('AdminLGProductMove'),
                'lgproductmove_satoken'                  => $this->security_token,
                'lgproductmove_msg_unkknownerror'        => $this->l('Unknown error'),
                'lgproductmove_msg_origincatnotselected' => $this->l('Origin category not selected'),
                'lgproductmove_msg_targetcatnotselected' => $this->l('Target category not selected'),
                'lgproductmove_msg_actionnotselected'    => $this->l('Operation not selected'),
                'lgproductmove_recharge'                 => (Tools::getValue('moveproducts', false) !== false),
                'lgproductmove_msg_samecat'              => $this->l(
                    'The origin and target categories are the same'
                ),
                'lgproductmove_msg_emptyproducts'        => $this->l(
                    'You must select at least one product in the column SELECTION'
                ),
            )
        );
//        }
    }

    protected function initLGMoveProducts()
    {
        $categoria = new Category($this->categoria_origen);
        $this->categoria_origen_name = $categoria->name[$this->context->language->id];

        $this->getMarcas();
        $cookie    = Context::getContext()->cookie;

        // Aviso de criterio de ordenación (creo que esto ya no influye), también obtenemos el lenguage de la cookie,
        // mas adelante es utilizado (aunque creo que se podría cambiar y obtener del contexto)
        $ordertype = Configuration::get('PS_PRODUCTS_ORDER_BY');
        if ($ordertype != 4) {
            $this->displayWarning(
                $this->l('For the module to be effective, the following option \"Default order by:').
                '&nbsp;'.$this->l('Position inside category\" must be enabled. You can configure it')
                .' <a href="index.php?tab=AdminPPreferences&token='
                .Tools::getAdminTokenLite('AdminPPreferences')
                .'"target="_blank" style="color:#FF0000;">'.$this->l('here').'</a>'
            );
        }

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            if (Module::isInstalled('ps_facetedsearch') && Module::isEnabled('ps_facetedsearch')) {
                if (Configuration::get('PS_LAYERED_FULL_TREE') == true) {
                    $this->displayWarning(
                        $this->l(
                            'You need deactivate the option "show product of subcategories" of the module '
                        ).
                        $this->l(
                            '"facete search"'
                        )
                        .' <a href="index.php?controller=AdminModules&configure=ps_facetedsearch&token='
                        .Tools::getAdminTokenLite('AdminModules')
                        .'"target="_blank" style="color:#FF0000;">'.$this->l('here').'</a>'
                    );
                }
            }
        } else {
            if (Module::isInstalled('blocklayered') && Module::isEnabled('blocklayered')) {
                if (Configuration::get('PS_LAYERED_FULL_TREE') == true) {
                    $this->displayWarning(
                        $this->l(
                            'You need deactivate the option "show product of subcategories" of the module '
                        ).
                        $this->l(
                            '"facete search"'
                        )
                        .' <a href="index.php?controller=AdminModules&configure=ps_facetedsearch&token='
                        .Tools::getAdminTokenLite('AdminPPreferences')
                        .'"target="_blank" style="color:#FF0000;">'.$this->l('here').'</a>'
                    );
                }
            }
        }

        // Solo calculamos si seleccionamos alguna categoría
        if (Tools::getValue('categoria_origen')) {
            // Calculamosel número total de productos
            $this->total_products = $this->getTotalCategoryProducts($this->categoria_origen);

            // Carlos: Si el número de productos es menor que el número de productos por página seleccionado,
            // tenemos que setearla a 1.
            if ($this->total_products < $this->selected_pagination) {
                $this->offset = 1;
            }
//            echo(print_r((int)$this->offset,true));

            // Carlos: Si cambiamos la paginación (lo sabemos porque la metemos en la cookie), hay que chequear en que
            // página estábamos visualizando para dejarlo en al misma, tomando como referencia el primer producto de
            // la página anterior: numero_de_pagina x numero_de_productos_por_pagina
            if ($this->context->cookie->__isset('lgproductmove_user_pagination_'.$this->context->shop->id)) {
                $this->last_selected_pagination = $this->context->cookie->__get(
                    'lgproductmove_user_pagination_'.$this->context->shop->id
                );

                if ($this->context->cookie->__isset('lgproductmove_last_offset_'.$this->context->shop->id)) {
                    $this->last_offset = $this->context->cookie->__get(
                        'lgproductmove_last_offset_'.$this->context->shop->id
                    );
                }

                if ($this->last_offset - 1 <= 0) {
                    $this->last_offset = 1;
                }

                $last_products_reference_index = (($this->last_offset-1) * $this->last_selected_pagination);

                if ($this->last_selected_pagination != $this->selected_pagination) {
                    $final_page = (int)($last_products_reference_index / $this->selected_pagination);
                    if ($last_products_reference_index % $this->selected_pagination > 0) {
                        $final_page++;
                    }
                    $this->offset = $final_page;
                    if ($this->offset <= 0) {
                        $this->offset = 1;
                    }
                    $this->context->cookie->__set(
                        'lgproductmove_user_pagination_'.$this->context->shop->id,
                        $this->selected_pagination
                    );
                }
            } else {
                $this->context->cookie->__set(
                    'lgproductmove_user_pagination_'.$this->context->shop->id,
                    $this->selected_pagination
                );
            }
//            die(print_r((int)$this->offset,true));

            // Actualizamos el indice de la pagina anterior
            $this->context->cookie->__set(
                'lgproductmove_last_offset_'.$this->context->shop->id,
                $this->offset
            );

            // Obtenemos los productos
            $this->prodscat = $this->prodCat($this->categoria_origen, $this->offset, $this->limit, false);
            if (isset($this->prodscat['productos']) && !empty($this->prodscat['productos'])) {
                $this->total_products = $this->prodscat['total'];
            }

            if ($this->total_products > 0) {
                $this->total_pages = (int)($this->total_products / $this->selected_pagination);
                if ($this->total_products % $this->selected_pagination > 0) {
                    $this->total_pages++;
                }
            }

            $this->context->cookie->write();

            // Reordenamos la consulta para la plantilla, esto es necesario debido a la antigua consulta,
            // para la nueva, que trae los resultados directos hay que cambiar esto por nada y reescribir al plantilla
            // con respecto al resultado directo que es más óptimo, porque ya no son necesarias las miniconsultas, que
            // se hacen a continuación, que multiplicado por el número de productos no es nada óptimo
            if ((isset($this->prodscat['productos']) && !empty($this->prodscat['productos']))
                && Tools::getValue('categoria_origen')
            ) {
                foreach ($this->prodscat['productos'] as $prodcat) {
                    $producto = $this->prodInfo($prodcat['id_product'], $this->id_lang);
                    $this->productos[$prodcat['id_product']]['info'] = $producto;
                    $images = Image::getImages((int)($cookie->id_lang), $producto['id_product']);
                    $this->productos[$prodcat['id_product']]['images'] = $images;

                    $imagen = '';
                    if (version_compare(_PS_VERSION_, '1.5', '<')) {
                        $imageObj = new Image($images[0]['id_image']);
                        $imagen = _THEME_PROD_DIR_.$imageObj->getExistingImgPath().'-small.jpg" />';
                    }
                    if (version_compare(_PS_VERSION_, '1.5', '>=')) {
                        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                            $id_imagen = 0;
                            if (isset($images[0])) {
                                $id_imagen = $images[0]['id_image'];
                            }
                            foreach ($images as $image) {
                                if ($image['cover']) {
                                    $id_imagen = $image['id_image'];
                                }
                            }
                        } else {
                            $id_imagen = 0;
                            if (isset($images[0])) {
                                $id_imagen = $images[0]['id_image'];
                            }
                            foreach ($images as $image) {
                                if ($image['cover']) {
                                    $id_imagen = $image['id_image'];
                                }
                            }
                            $id_imagen = $producto['id_product'];
                        }

                        if (file_exists(_PS_TMP_IMG_DIR_.'product_'.$id_imagen.'.jpg')) {
                            $imagen = __PS_BASE_URI__.'img/tmp/product_'.$id_imagen.'.jpg?time='.time();
                        } elseif (file_exists(
                            _PS_TMP_IMG_DIR_.'product_mini_'.$id_imagen.'_'.$this->context->shop->id.'.jpg'
                        )) {
                            $imagen  = __PS_BASE_URI__.'img/tmp/product_mini_'
                                .$id_imagen.'_'.$this->context->shop->id.'.jpg?time='.time();
                        } elseif (file_exists(_PS_TMP_IMG_DIR_.'product_mini_'.$id_imagen.'.jpg')) {
                            $imagen = __PS_BASE_URI__.'img/tmp/product_mini_'
                                .$id_imagen.'.jpg?time='.time();
                        } else {
                            $imagen = $this->getProductThumbnail($producto['id_product'], 0);
                        }

                        $producto = Db::getInstance()->getRow(
                            'SELECT * '.
                            'FROM '._DB_PREFIX_.'product '.
                            'WHERE id_product = '.(int)$producto['id_product']
                        );
                        $producto_nombre = Db::getInstance()->getValue(
                            'SELECT name '.
                            'FROM '._DB_PREFIX_.'product_lang '.
                            'WHERE id_product = '.(int)$producto['id_product'].
                            ' AND id_lang = '.(int)$this->id_lang
                        );
                        $producto_quantity = Db::getInstance()->getValue(
                            'SELECT quantity '.
                            'FROM '._DB_PREFIX_.'stock_available '.
                            'WHERE id_product = '.(int)$producto['id_product'].
                            '  AND id_product_attribute = 0'
                        );
                        $this->productos[$prodcat['id_product']]['imagen'] = $imagen;
                        $this->productos[$prodcat['id_product']]['producto'] = $producto;
                        if (isset($producto['id_manufacturer']) && !is_null($producto['id_manufacturer'])) {
                            $this->productos[$prodcat['id_product']]['producto']['manufacturer'] = $this->getManuf(
                                $producto['id_manufacturer']
                            );
                        }
                        if (isset($this->productos[$prodcat['id_product']]['producto']['date_upd'])
                            && !is_null($this->productos[$prodcat['id_product']]['producto']['date_upd'])
                        ) {
                            $this->productos[$prodcat['id_product']]['producto']['fecha'] = $this->fecha(
                                $this->productos[$prodcat['id_product']]['producto']['date_upd']
                            );
                        }
                        if (isset($this->productos[$prodcat['id_product']]['producto']['price'])
                            && !is_null($this->productos[$prodcat['id_product']]['producto']['price'])
                        ) {
                            $this->productos[$prodcat['id_product']]['producto']['precio'] = number_format(
                                $this->productos[$prodcat['id_product']]['producto']['price'],
                                2,
                                ',',
                                '.'
                            );
                        }
                        $this->productos[$prodcat['id_product']]['nombre'] = $producto_nombre;
                        $this->productos[$prodcat['id_product']]['quantity'] = $producto_quantity;
                        $this->productos[$prodcat['id_product']]['prodActive'] = $this->prodActive(
                            $prodcat['id_product']
                        );
                        $this->productos[$prodcat['id_product']]['imgProdActive'] = $this->imgProdActive(
                            $this->prodActive($prodcat['id_product'])
                        );
                        $this->productos[$prodcat['id_product']]['position'] = $prodcat['position'];
                    }
                }
            }
        }
    }

    protected function getMarcas()
    {
        $marcas = array();
        $marcas = Db::getInstance()->ExecuteS(
            'SELECT id_manufacturer, name '.
            'FROM '._DB_PREFIX_.'manufacturer '.
            'ORDER BY name ASC'
        );

        foreach ($marcas as $marca) {
            $this->marcas[$marca['id_manufacturer']] = $marca;
        }
        unset($marcas);
    }

    private function catTree(
        $parent_id = '0',
        $spacing = '',
        $exclude = '',
        $cat_tree_array = '',
        $include_itself = false
    ) {
        if (!is_array($cat_tree_array)) {
            $cat_tree_array = array();
        }
        if ($parent_id == 0) {
            $parent_id = (int)Configuration::get('PS_ROOT_CATEGORY');
        }
        if ($include_itself) {
            $category = Db::getInstance()->getRow(
                'SELECT cl.name '.
                'FROM '._DB_PREFIX_.'category_lang cl '.
                'WHERE cl.id_lang = '.(int)$this->id_lang.
                '  AND cl.id_category = '.(int)$parent_id
            );
            $cat_tree_array[] = array('id_category' => (int)$parent_id, 'name' => (int)$category['name']);
        }
        if (substr_count(_PS_VERSION_, '1.4') > 0) {
            $precategories = Db::getInstance()->ExecuteS(
                'SELECT c.id_category, cl.name, c.id_parent '.
                'FROM '._DB_PREFIX_.'category c, '._DB_PREFIX_.'category_lang cl '.
                'WHERE c.id_category = cl.id_category '.
                '  AND cl.id_lang = '.(int)$this->id_lang.
                '  AND c.id_parent = '.(int)$parent_id.'; '
            );
        } else {
            $precategories = Db::getInstance()->ExecuteS(
                'SELECT * '.
                'FROM '._DB_PREFIX_.'category c '.
                'LEFT JOIN '._DB_PREFIX_.'category_lang cl ON c.id_category = cl.id_category '.
                'LEFT JOIN '._DB_PREFIX_.'category_shop cs ON c.id_category = cs.id_category '.
                'WHERE c.id_category = cl.id_category '.
                '  AND cl.id_lang = '.(int)$this->id_lang.
                '  AND c.id_parent = '.(int)$parent_id.
                '  AND cl.id_shop = '.(int)$this->context->shop->id.
                '  AND cs.id_shop = '.(int)$this->context->shop->id
            );
        }
        foreach ($precategories as $categories) {
            if ($exclude != $categories['id_category']) {
                $cat_tree_array[] = array(
                    'id_category' => (int)$categories['id_category'],
                    'name' => $spacing.$categories['name']
                );
            }
            $cat_tree_array = $this->catTree(
                (int)$categories['id_category'],
                $spacing.'&nbsp;&nbsp;&nbsp;',
                $exclude,
                $cat_tree_array
            );
        }
        return $cat_tree_array;
    }

    // CARLOS función auxiliar para obtener el recorrido en arbol de las categorias
    private function getChilds($cat, $index)
    {
        $order   = array();
        $order[] = $index; //intval($index);
        if (isset($cat[$index]) && !empty($cat[$index])) {
            //echo $index;
            foreach (array_keys($cat[$index]) as $index_child) {
                $order = array_merge($order, $this->getChilds($cat, (int)$index_child));
            }
        }
        return $order;
    }

    private function catTreeNew($uncat = false)
    {
        $categories_finales = $uncat
            ? array(
                0 => array(
                    'id_category' => '999999999',
                    'name' => $this->l('Uncategorized products'),
                    'level_depth' => '0',
                    'active' => '1'
                )
            )
            : array();
        $childs             = array();
        $raices             = array();
        $orden_final        = array();

        // CARLOS: Cambio en la forma en la que obtenemos las categorias usando el core ya que para catalogos grandes
        // el antiguo sistema se quedaba colgado. Además esta consulta está cacheada y va rapidísima usando el core
        $lang = (int)Context::getContext()->language->id;
        $categories = Category::getCategories($lang, false);

        // CARLOS: Para que nuestro selector funciona a prueba de bombas, vamos a obtener todos los arboles disjuntos
        //         que serán todos los indices del array devuelto. Para ello calculamos todas las raices
        foreach ($categories as $index => $cat) {
            $raices[$index] = $index;
        }

        foreach ($categories as $category) {
            foreach ($category as $child) {
                $childs[$child['infos']['id_category']] = $child;
            }
        }

        foreach ($categories as $index => $cat) {
            foreach ($cat as $index_child => $child) {
                if (in_array($index_child, $raices)) {
                    unset($raices[$index_child]);
                }
            }
        }

        // CARLOS: el orden que recibimos de la función es un recorrido en niveles, necesitamos el recorrido en arbol
        foreach ($raices as $index => $cat) {
            $orden_final = array_merge($orden_final, $this->getChilds($categories, $cat));
        }

        // CARLOS: cambiamos el array a devolver con la nueva forma de obtener el orden
        foreach ($orden_final as $category) {
            if (isset($childs[$category]) && $category > 0) {
                $name = '';
                $categories_finales[] = array(
                    'id_category' => $childs[$category]['infos']['id_category'],
                    'name'        => $name.Tools::ucfirst($childs[$category]['infos']['name']),
                    'level_depth' => $childs[$category]['infos']['level_depth'],
                    'active' => $childs[$category]['infos']['active'],
                );
            }
        }

        return $categories_finales;
    }

    private function prodCat($id_cat, $offset = 1, $limit = null, $cache = true)
    {
        if (is_null($limit)) {
            $limit = self::NUMBER_OF_PRODUCTS;
        }

        $final_offset = 0;
        if ($offset-1 < 0) {
            $final_offset = 0;
        } else {
            $final_offset = $offset - 1;
        }

        $ands = '';
        if (!empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                switch ($filter) {
                    case 'id':
                        $ands .= '  AND p.`id_product` LIKE "%'.$value.'%" ';
                        break;
                    case 'name':
                        $ands .= '  AND pl.`name` LIKE "%'.$value.'%" ';
                        break;
                    case 'reference':
                        $ands .= '  AND p.`reference` LIKE "%'.$value.'%" ';
                        break;
                    case 'price':
                        $ands .= '  AND ps.`price` LIKE "%'.$value.'%" ';
                        break;
                    case 'stock':
                        $ands .= '  AND sa.`quantity` LIKE "%'.$value.'%" ';
                        break;
                    case 'manufacturer':
                        if ($value > 0) {
                            $ands .= '  AND p.`id_manufacturer` = ' . $value . ' ';
                        }
                        break;
                    case 'status':
                        if ($value >= 0 && $value < 2) {
                            $ands .= '  AND ps.`active` LIKE "%'.$value.'%" ';
                        }
                        break;
                    case 'date':
                        $ands .= '  AND DATE_FORMAT(ps.`date_upd`, "%Y-%m-%d") = "'.$value.'" ';
                        break;
                }
            }
        }
        $cache_id = 'AdminLGProductMove::prodCat_'.(int)$id_cat.'_'.$this->context->shop->id;
        if (!Cache::isStored($cache_id) || !$cache) {
            $prodcat = array(
                'products' => array(),
                'total' => 0,
            );

            $sql_2 = 'SELECT DISTINCT'.
                '   p.`id_product`, '.
                '   pl.`name`, '.
                '   p.`reference`, '.
                '   cl.`name` as category_name, '.
                '   ps.`price`, '.
                '   ps.`date_upd`, '.
                '   ps.`active`, '.
                '   cp.`position` '.
                'FROM `'._DB_PREFIX_.'product` p '.
                'LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ('.
                '   ps.`id_product` = p.`id_product` '.
                '   AND ps.`id_shop` = '.$this->context->shop->id.
                ') '.
                'LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON ('.
                '   pl.`id_product` = p.`id_product` '.
                '   AND pl.`id_lang` = '.$this->context->language->id.
                ') '.
                'LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`) '.
                'LEFT JOIN `'._DB_PREFIX_.'category_shop` cs ON ('.
                '   cs.`id_category` = cp.`id_category` '.
                '   AND cs.`id_shop` = pl.`id_shop`'.
                ') '.
                'LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.
                '   cl.`id_category` = cp.`id_category` '.
                '   AND cl.`id_shop` = cs.`id_shop` '.
                '   AND pl.`id_lang` = cl.`id_lang` '.
                ') '.
                'LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON ('.
                '   p.`id_product` = sa.`id_product` '.
                '   AND sa.`id_product_attribute` = 0 '.
                ') '.
                'WHERE cp.`id_category` '.((int)$id_cat==999999999?'IS NULL':'= '.(int)$id_cat).' '.
                $ands.
                'ORDER BY cp.`position` ASC '.
                'LIMIT '.($final_offset*$limit).', '.$limit.';';

            $rows = Db::getInstance()->ExecuteS($sql_2);

            if ($rows) {
                $prodcat['productos'] = $rows;
                $prodcat['total'] = $this->getTotalCategoryProducts($id_cat, $cache);
            }

            $prodcat['sql'] = $sql_2;

            if ($cache) {
                Cache::store($cache_id, $prodcat);
            }
            return $prodcat;
        }
        return Cache::retrieve($cache_id);
    }

    private function prodInfo($id_product, $id_lang)
    {
        $productos = Db::getInstance()->getRow(
            'SELECT * '.
            'FROM '._DB_PREFIX_.'product p '.
            'LEFT JOIN '._DB_PREFIX_.'product_lang pl ON p.id_product = pl.id_product '.
            'WHERE p.id_product = '.(int)$id_product.
            '  AND pl.id_lang = '.(int)$id_lang
        );
        return $productos;
    }

    private function prodActive($id_product)
    {
        if (substr_count(_PS_VERSION_, '1.4') > 0) {
            $active = Db::getInstance()->getValue(
                'SELECT active '.
                'FROM '._DB_PREFIX_.'product '.
                'WHERE id_product = '.(int)$id_product
            );
        } else {
            $active = Db::getInstance()->getValue(
                'SELECT active '.
                'FROM '._DB_PREFIX_.'product_shop '.
                'WHERE id_product = '.(int)$id_product.
                '  AND id_shop = '.(int)$this->context->shop->id
            );
        }
        return $active;
    }

    private function imgProdActive($active)
    {
        if ($active == 1) {
            return '../modules/lgproductmove/views/img/enabled.gif';
        }
        if ($active == 0) {
            return '../modules/lgproductmove/views/img/disabled.gif';
        }
    }

    private function getTotalCategoryProducts($id_cat, $cache = true)
    {
        $ands = '';
        if (!empty($this->filters)) {
            foreach ($this->filters as $filter => $value) {
                switch ($filter) {
                    case 'id':
                        $ands .= '  AND p.`id_product` LIKE "%'.$value.'%" ';
                        break;
                    case 'name':
                        $ands .= '  AND pl.`name` LIKE "%'.$value.'%" ';
                        break;
                    case 'reference':
                        $ands .= '  AND p.`reference` LIKE "%'.$value.'%" ';
                        break;
                    case 'price':
                        $ands .= '  AND ps.`price` LIKE "%'.$value.'%" ';
                        break;
                    case 'manufacturer':
                        if ($value > 0) {
                            $ands .= '  AND p.`id_manufacturer` = ' . $value . ' ';
                        }
                        break;
                    case 'status':
                        if ($value >= 0 && $value < 2) {
                            $ands .= '  AND ps.`active` LIKE "%'.$value.'%" ';
                        }
                        break;
                    case 'date':
                        $ands .= '  AND DATE_FORMAT(ps.`date_upd`, "%Y-%m-%d") = "'.$value.'" ';
                        break;
                }
            }
        }
        $cache_id = 'AdminLGProductMove::prodCat_'.(int)$id_cat.'_count_'.$this->context->shop->id;
        if (!Cache::isStored($cache_id) || !$cache) {
//            $prodcat = array(
//                'products' => array(),
//                'total' => 0,
//            );
            $sql_1 = 'SELECT COUNT(*) ' .
                'FROM `' . _DB_PREFIX_ . 'product` p ' .
                'LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ('.
                '   ps.`id_product` = p.`id_product` '.
                '   AND ps.`id_shop` = '.$this->context->shop->id.
                ') '.
                'LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON ('.
                '   pl.`id_product` = p.`id_product` '.
                '   AND pl.`id_lang` = '.$this->context->language->id.
                ') '.
                'LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`) '.
                'LEFT JOIN `'._DB_PREFIX_.'category_shop` cs ON ('.
                '   cs.`id_category` = cp.`id_category` '.
                '   AND cs.`id_shop` = pl.`id_shop`'.
                ') '.
                'LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON ('.
                '   cl.`id_category` = cp.`id_category` '.
                '   AND cl.`id_shop` = cs.`id_shop` '.
                '   AND pl.`id_lang` = cl.`id_lang` '.
                ') '.
                'WHERE cp.`id_category` '.((int)$id_cat==999999999?'IS NULL':'= '.(int)$id_cat).' '.
                $ands . ';';



            $num_rows = Db::getInstance()->getValue($sql_1);
            if ($cache) {
                Cache::store($cache_id, $num_rows);
            }

            return $num_rows;
        }
        return Cache::retrieve($cache_id);
    }

    private function fecha($date)
    {
//        list($date1, $date2) = array_pad(explode(' ', $date), 2, null);
//        list($year, $month, $day) = array_pad(explode('-', $date1), 3, null);
        list($year, $month, $day) = array_pad(explode('-', $date), 3, null);
//        list($hour, $minute, $second) = array_pad(explode(':', $date2), 3, null);
        //return $day.'/'.$month.'/'.$year.' '.$hour.':'.$minute.':'.$second;
        return $year.'-'.$month.'-'.$day; //.' '.$hour.':'.$minute.':'.$second;
    }

    private function getManuf($id_manufacturer)
    {
        $manufacturer = Db::getInstance()->getValue(
            'SELECT name '.
            'FROM '._DB_PREFIX_.'manufacturer '.
            'WHERE id_manufacturer = '.(int)$id_manufacturer
        );
        return $manufacturer;
    }

    private function getP()
    {
        $default_lang = $this->context->language->id;
        $lang         = Language::getIsoById($default_lang);
        $pl           = array('es', 'fr', 'it');
        if (!in_array($lang, $pl)) {
            $lang = 'en';
        }
        $base   = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ?
            'https://'.$this->context->shop->domain_ssl :
            'http://'.$this->context->shop->domain);
        $uri    = $base.$this->context->shop->getBaseURI();
        $page = $this->createTemplate('publi.tpl');
        $this->context->smarty->assign(
            array(
                'uri'            => $uri,
                'publi_iso_lang' => $lang,
            )
        );
        return $page->fetch();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if ($this->display == 'edit' || $this->display == 'add') {
            $this->toolbar_btn['save'] = array(
                'short' => 'Save',
                'href' => '#',
                'desc' => $this->l('Save'),
            );

            $this->toolbar_btn['save-and-stay'] = array(
                'short' => 'SaveAndStay',
                'href' => '#',
                'desc' => $this->l('Save and stay'),
            );

            // adding button for adding a new combination in Combination tab
            $this->toolbar_btn['newCombination'] = array(
                'short' => 'New combination',
                'desc' => $this->l('New combination'),
                'class' => 'toolbar-new'
            );
        } else {
            $this->toolbar_btn['import'] = array(
                'href' => $this->context->link->getAdminLink('AdminImport', true) . '&import_type=products',
                'desc' => $this->l('Import')
            );
        }

        $this->context->smarty->assign('toolbar_scroll', 1);
        $this->context->smarty->assign('show_toolbar', 1);
        $this->context->smarty->assign('toolbar_btn', $this->toolbar_btn);
    }

    public function createTemplate($tpl_name)
    {
        if (file_exists($this->getTemplatePath().$tpl_name) && $this->viewAccess()) {
            return $this->context->smarty->createTemplate($this->getTemplatePath().$tpl_name, $this->context->smarty);
        }

        return parent::createTemplate($tpl_name);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $bo_theme = ((Validate::isLoadedObject($this->context->employee)
            && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');

        $this->addCSS(_MODULE_DIR_.'/lgproductmove/views/css/publi/style.css');
        $this->addJquery();
        if (!file_exists(_PS_BO_ALL_THEMES_DIR_ . $bo_theme . DIRECTORY_SEPARATOR . 'template')) {
            $bo_theme = 'default';
        }
        if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
            $this->addJqueryUI('ui.datepicker');
            $this->addJS(__PS_BASE_URI__.'modules/lgproductmove/views/js/lgproductmove.js');
            $this->addJS(__PS_BASE_URI__ . 'modules/lgproductmove/views/js/loadingoverlay.min.js');
        } else {
            $this->addJqueryUI('datepicker');
            $this->addJs(_PS_MODULE_DIR_ . 'lgproductmove/views/js/lgproductmove.js');
            $this->addJS(_PS_MODULE_DIR_ . 'lgproductmove/views/js/loadingoverlay.min.js');
        }
    }

    public function initContent()
    {
        $this->initLGMoveProducts();
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $page = $this->createTemplate('AdminLGProductMove.tpl');
        } else {
            $page = $this->createTemplate('AdminLGProductMove_15.tpl');
        }
        $this->context->smarty->assign(
            array(
                'categoria_origen'  => $this->categoria_origen,
                'categoria_destino' => $this->categoria_destino,
                'categorias'        => $this->catTreeNew(true),
                'categorias_to'     => $this->categorias,
                'marcas'            => $this->marcas,
                'productos'         => $this->productos,

                // Estos son para la paginación
                'simple_header'       => false,
                'list_total'          => $this->total_products,
                'total_pages'         => $this->total_pages,
                'selected_pagination' => $this->selected_pagination,
                'pagination'          => $this->pagination,
                'page'                => $this->offset,
                'list_id'             => 'lgproductmove',
            )
        );
        $this->content = $this->getP().$page->fetch();
        parent::initContent();
    }

    public function ajaxProcessGetProducts()
    {
        $this->initLGMoveProducts();
        $page = $this->createTemplate('product_rows.tpl');
        $this->context->smarty->assign(
            array(
                'marcas'            => $this->marcas,
                'productos'         => $this->productos,

                // Estos son para la paginación
                'simple_header'       => false,
                'list_total'          => $this->total_products,
                'total_pages'         => $this->total_pages,
                'selected_pagination' => $this->selected_pagination,
                'pagination'          => $this->pagination,
                'page'                => $this->offset,
                'list_id'             => 'lgproductmove',
                'filters'             => $this->filters,
            )
        );
        $rows      = $page->fetch();
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $page = $this->createTemplate('pagination.tpl');
        } else {
            $page = $this->createTemplate('pagination_15.tpl');
        }

        $this->context->smarty->assign(
            array(
                'marcas'            => $this->marcas,
                'productos'         => $this->productos,

                // Estos son para la paginación
                'simple_header'       => false,
                'list_total'          => $this->total_products,
                'total_pages'         => $this->total_pages,
                'selected_pagination' => $this->selected_pagination,
                'pagination'          => $this->pagination,
                'page'                => $this->offset,
                'list_id'             => 'lgproductmove',
                'filters'             => $this->filters,
            )
        );
        $pagination = $page->fetch();
        $response = array(
            'rows'   => $rows,
            'pagination' => $pagination,
            'total_products' => $this->total_products,
            'status' => 'ok',
        );
        if (isset($this->prodscat['sql'])) {
            $response['sql'] = $this->prodscat['sql'];
        }
        if ($this->debug) {
            $response['debug']['total_products'] = $this->total_products;
            $response['debug']['total_pages'] = $this->total_pages;
            $response['debug']['page'] = $this->offset;
            $response['debug']['last_page'] = $this->last_offset;
            $response['debug']['selected_pagination'] = $this->selected_pagination;
            $response['debug']['cookie_selected_pagination'] = $this->last_selected_pagination;
        }
        die(Tools::jsonEncode($response, true));
    }

    public function ajaxProcessCopyMoveProducts()
    {
        if ($this->get_times) {
            $begin    = microtime();
        }
        $response = array();

        if (Tools::getValue('satoken') == $this->security_token) {
            $process_complete  = false;

            if (empty($this->selected_products) && !$this->all_selected) {
                $response['status'] = 'ko';
                $response['errors'][] = $this->l('You must select at least one product in the column SELECTION');
            }

            if ($this->categoria_origen == 0) {
                $response['status'] = 'ko';
                $response['errors'][] = $this->l('Origin category not selected');
            }

            if ($this->categoria_destino == 0) {
                $response['status'] = 'ko';
                $response['errors'][] = $this->l('Target category not selected');
            }

            if ($this->operacion == 0) {
                $response['status'] = 'ko';
                $response['errors'][] = $this->l('Operation not selected');
            }

            if ($this->categoria_origen     > 0
                && $this->categoria_destino > 0
                && (!empty($this->selected_products) || $this->all_selected)
            ) {
                foreach ($this->selected_products as $product_id) {
                    $product_id = (int)$product_id;
                    if ($product_id) { // Comprobamos que la id obtenida sea correcta
                        $product            = new Product($product_id);
                        if ($product->id) { // Comprobamos que el producto exista
                            $product_categories = $product->getCategories();

                            // if moving or copying always add target category
                            if (!in_array($this->categoria_destino, $product_categories)) {
                                $product_categories[] = $this->categoria_destino;
                            }

                            // If moving we have to delete product from origin category
                            if ($this->operacion == self::OPERATION_MOVE) {
                                if (($key = array_search($this->categoria_origen, $product_categories)) !== false) {
                                    unset($product_categories[$key]);
                                    if ($product->id_category_default == $this->categoria_origen) {
                                        $product->id_category_default = $this->categoria_destino;
                                    }
                                }
                            }
                            if ((int)Tools::getValue('copycatlikedefault', 0) === 1) {
                                $product->id_category_default = $this->categoria_destino;
                                if ($this->get_times) {
                                    $begin_p = microtime();
                                }
                                Db::getInstance()->update(
                                    'product',
                                    array('id_category_default' => $product->id_category_default),
                                    'id_product = '.$product_id
                                );
                                if ($this->get_times) {
                                    $end_p = microtime();
                                    $response['Times'][$product_id]['Update Table Product'] =
                                        $this->getDataTime($begin_p, $end_p);
                                    $begin_s = microtime();
                                }
                                Db::getInstance()->update(
                                    'product_shop',
                                    array('id_category_default' => $product->id_category_default),
                                    'id_product = '.$product_id.' AND id_shop = '.$this->context->shop->id
                                );
                                if ($this->get_times) {
                                    $end_s = microtime();
                                    $response['Times'][$product_id]['Update Table Product Shop'] =
                                        $this->getDataTime($begin_s, $end_s);
                                }
                            }

                            $begin_up = microtime();
                            if ($this->updateCategories($product->id, $product_categories)) {
                                $process_complete = true;
                            }
                            $end_up = microtime();
                            $response['Times'][$product_id]['Update Categories (Core function)'] =
                                $this->getDataTime($begin_up, $end_up);
                            //$product->save();
                        } else {
                            if ($this->debug) {
                                $response['debug']['not found'] = $product_id;
                            }
                        }
                    } else {
                        if ($this->debug) {
                            $response['debug']['bad IDs'] = $product_id;
                        }
                    }
                }
            }

            if (!$process_complete) {
                if ($this->selected_products and $this->operacion == self::OPERATION_MOVE) {
                    $response['status'] = 'ko';
                    $response['errors'][] =
                        $this->l('The products have not been moved (the resources needed to move them').
                        '&nbsp;'.
                        $this->l('are superior to the resources currently provided by your server)');
                }
                if ($this->selected_products and $this->operacion == self::OPERATION_COPY) {
                    $response['status'] = 'ko';
                    $response['errors'][] =
                        $this->l('The products have not been copied (the resources needed to copy them').
                        '&nbsp;'.
                        $this->l('are superior to the resources currently provided by your server)');
                }
                $maxvars = ini_get('max_input_vars');
                $vars = count($_POST) + 1;
                if ($vars >= $maxvars) {
                    $response['status'] = 'ko';
                    $response['errors'][] =
                        $this->l('The value of your max_input_vars variable').
                        '&nbsp;('.$maxvars.')&nbsp;'.
                        $this->l('must be superior to the number of variables you are trying to send').
                        '&nbsp;('.$vars.')';
                }
                $maxsize = ini_get('post_max_size');
                $size = (int)$_SERVER['CONTENT_LENGTH']/1048576;
                if ($size >= $maxsize) {
                    $response['status'] = 'ko';
                    $response['errors'][] =
                        $this->l('The value of your post_max_size variable').
                        '&nbsp;('.$maxsize.')&nbsp;'.
                        $this->l('must be superior to the size of the variables you are trying to send').
                        '&nbsp;('.$size.')';
                }
            } else {
                if ($this->operacion == self::OPERATION_MOVE) {
                    $response['status'] = 'ok';
                    $response['message'][] = $this->l('The selected products have been successfully moved');
                }
                if ($this->operacion == self::OPERATION_COPY) {
                    $response['status'] = 'ok';
                    $response['message'][] = $this->l('The selected products have been successfully copied');
                }
            }
        } else {
            $this->httpResponseCode(401);
            $response['status'] = 'ko';
            $response['errors'][] = $this->l('Access denied');
            die(Tools::jsonEncode($response));
        }

        if (!empty($response['errors'])) {
            $this->httpResponseCode(400);
        } else {
            $this->httpResponseCode(200);
        }
        header('Content-Type: application/json');

        if ($this->get_times) {
            $end = microtime();
            $response['Times']['Overall Time'] = $this->getDataTime($begin, $end);
        }
        die(Tools::jsonEncode($response));
    }

    protected function getDataTime($before, $after)
    {
        $before_time = explode(' ', $before);
        $after_time  = explode(' ', $after);

        $before_timestamp    = (int)$before_time[1];
        $before_microseconds = $before_time[0];
        $after_timestamp     = (int)$after_time[1];
        $after_microseconds  = $after_time[0];

        $difference_timestamp = $after_timestamp - $before_timestamp;
        //$fecha_aux = new DateTime('now', new DateTimeZone('Europe/Madrid'));
        //$fecha_aux->setTimestamp($difference_timestamp);

        $difference_microseconds = $after_microseconds - $before_microseconds;
        if ($difference_microseconds < 0.0) {
            $difference_timestamp -= 1;
            $difference_microseconds = 1.0 + $difference_microseconds;
        }

        return array(
            'ELAPSED' => $difference_timestamp . ' ' . $difference_microseconds,
            'BEFORE'  => $before_timestamp . ' ' . $before_microseconds,
            'AFTER'   => $after_timestamp . ' ' . $after_microseconds,
        );
    }

    // For 4.3.0 <= PHP <= 5.4.0
    protected function httpResponseCode($newcode = null)
    {
        if (!function_exists('http_response_code')) {
            static $code = 200;
            if ($newcode !== null) {
                header('X-PHP-Response-Code: '.$newcode, true, $newcode);
                if (!headers_sent()) {
                    $code = $newcode;
                }
            }
            return $code;
        } else {
            http_response_code($newcode);
        }
    }

    /**
     * Gets the correct temporal thumbnail for a specific product combination.
     *
     * @param $id_product
     * @param $id_product_attribute
     * @return bool|string
     */
    protected function getProductThumbnail($id_product, $id_product_attribute = null)
    {
        $name = 'product_mini_' . (int)$id_product .
            (($id_product_attribute) ? '_' . (int)$id_product_attribute : '') . '.jpg';

        if (file_exists(_PS_TMP_IMG_DIR_.$name)) {
            return '../img/tmp/' . $name;
        } else {
            $id_lang = $this->context->language->id;
            $product_image = $this->getBestProductImage($id_lang, $id_product);

            if (isset($product_image['id_image'])) {
                $image = new Image($product_image['id_image']);

                // generate temporal thumbnail
                ImageManager::thumbnail(
                    _PS_IMG_DIR_ . 'p/' . $image->getExistingImgPath() . '.jpg',
                    $name,
                    45,
                    'jpg'
                );

                if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                    return '../img/tmp/' . $name;
                }
            }
        }
        return false;
    }

    protected function getBestProductImage($id_lang, $id_product)
    {
        $product_image = array();
        $product_images = Image::getImages(
            $id_lang,
            $id_product
        );

        if ($product_images) {
            $product_image = $product_images[0];
        }
        return $product_image;
    }

    public function updateCategories($id_product = null, $categories = array())
    {
        if (empty($categories)) {
            return false;
        }

        $result = Db::getInstance()->executeS(
            'SELECT c.`id_category`
            FROM `' . _DB_PREFIX_ . 'category_product` cp
            LEFT JOIN `' . _DB_PREFIX_ . 'category` c ON (c.`id_category` = cp.`id_category`)
            ' . Shop::addSqlAssociation('category', 'c', true, null, true) . '
            WHERE cp.`id_category` NOT IN (' . implode(',', array_map('intval', $categories)) . ')
            AND cp.id_product = ' . (int) $id_product
        );

        // if none are found, it's an error
        if (!is_array($result)) {
            return false;
        }

        foreach ($result as $categ_to_delete) {
            $this->deleteCategory($id_product, $categ_to_delete['id_category']);
        }

        if (!$this->addToCategories($id_product, $categories)) {
            return false;
        }

        SpecificPriceRule::applyAllRules(array((int) $id_product));

        Cache::clean('Product::getProductCategories_' . (int) $id_product);

        return true;
    }

    public function deleteCategory($id_product, $id_category, $clean_positions = true)
    {
        $result = Db::getInstance()->executeS(
            'SELECT `id_category`, `position`
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_product` = ' . (int) $id_product . '
            AND id_category = ' . (int) $id_category . ''
        );

        $return = Db::getInstance()->delete(
            'category_product',
            'id_product = ' . (int)$id_product . ' AND id_category = ' . (int)$id_category
        );

        if ($clean_positions === true) {
            foreach ($result as $row) {
                $this->cleanPositions((int) $id_product, (int) $row['id_category'], (int) $row['position']);
            }
        }
        return $return;
    }

    public function cleanPositions($id_category, $position = 0)
    {
        $return = true;

        if (!(int) $position) {
            $result = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_category` = ' . (int) $id_category . '
                ORDER BY `position`
            ');
            $total = count($result);

            for ($i = 0; $i < $total; ++$i) {
                $return &= Db::getInstance()->update(
                    'category_product',
                    array('position' => $i),
                    '`id_category` = ' . (int) $id_category . ' AND `id_product` = ' . (int) $result[$i]['id_product']
                );
                $return &= Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'product` p' . Shop::addSqlAssociation('product', 'p') . '
                    SET p.`date_upd` = "' . date('Y-m-d H:i:s') .
                    '", product_shop.`date_upd` = "' . date('Y-m-d H:i:s') . '"
                    WHERE p.`id_product` = ' . (int) $result[$i]['id_product']
                );
            }
        } else {
            $result = Db::getInstance()->executeS('
                SELECT `id_product`
                FROM `' . _DB_PREFIX_ . 'category_product`
                WHERE `id_category` = ' . (int) $id_category . ' AND `position` > ' . (int) $position . '
                ORDER BY `position`
            ');
            $total = count($result);
            $return &= Db::getInstance()->update(
                'category_product',
                array('position' => array('type' => 'sql', 'value' => '`position`-1')),
                '`id_category` = ' . (int) $id_category . ' AND `position` > ' . (int) $position
            );

            for ($i = 0; $i < $total; ++$i) {
                $return &= Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'product` p' . Shop::addSqlAssociation('product', 'p') . '
                    SET p.`date_upd` = "' . date('Y-m-d H:i:s') .
                    '", product_shop.`date_upd` = "' . date('Y-m-d H:i:s') . '"
                    WHERE p.`id_product` = ' . (int) $result[$i]['id_product']
                );
            }
        }

        return $return;
    }

    public function addToCategories($id_product, $categories = array())
    {
        if (empty($categories)) {
            return false;
        }

        if (!is_array($categories)) {
            $categories = array($categories);
        }

        if (!count($categories)) {
            return false;
        }

        $categories = array_map('intval', $categories);

        $current_categories = Product::getProductCategories($id_product);
        $current_categories = array_map('intval', $current_categories);

        // for new categ, put product at last position
        $res_categ_new_pos = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT id_category, MAX(position)+1 newPos
            FROM `' . _DB_PREFIX_ . 'category_product`
            WHERE `id_category` IN(' . implode(',', $categories) . ')
            GROUP BY id_category');

        $new_categories = array();

        foreach ($res_categ_new_pos as $array) {
            $new_categories[(int) $array['id_category']] = (int) $array['newPos'];
        }

        $new_categ_pos = array();
        // The first position must be 1 instead of 0
        foreach ($categories as $id_category) {
            $new_categ_pos[$id_category] = isset($new_categories[$id_category]) ? $new_categories[$id_category] : 1;
        }

        $product_cats = array();

        foreach ($categories as $new_id_categ) {
            if (!in_array($new_id_categ, $current_categories)) {
                $product_cats[] = array(
                    'id_category' => (int) $new_id_categ,
                    'id_product' => (int) $id_product,
                    'position' => (int) $new_categ_pos[$new_id_categ],
                );
            }
        }

        Db::getInstance()->insert('category_product', $product_cats);

        return true;
    }
}
