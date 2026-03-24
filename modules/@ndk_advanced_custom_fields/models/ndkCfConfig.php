<?php
/**
 *  Tous droits réservés NDKDESIGN.
 *
 *  @author    Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2022 Hendrik Masson
 *  @license   Tous droits réservés
 */
class NdkCfConfig extends ObjectModel
{
  public $id_user;
  public $id_product;
  public $id_customization;
  public $is_admin;
  public $name;
  public $tags;
  public $default_config;
  public $price;
  public $id_lang_default;
  public $json_values;
  public $id_guest;

  public static $definition = [
  'table' => 'ndk_customization_field_configuration',
  'primary' => 'id_ndk_customization_field_configuration',
  'multilang' => true,
  'fields' => [
  'id_user' => [
  'type' => self::TYPE_INT,
  'validate' => 'isunsignedInt',
  'required' => false,
  ],
  'id_guest' => [
  'type' => self::TYPE_INT,
  'validate' => 'isunsignedInt',
  'required' => false,
  ],
  'id_lang_default' => [
  'type' => self::TYPE_INT,
  'validate' => 'isunsignedInt',
  'required' => false,
  ],
  'id_product' => [
  'type' => self::TYPE_INT,
  'validate' => 'isunsignedInt',
  'required' => false,
  ],
  'id_customization' => [
  'type' => self::TYPE_INT,
  'validate' => 'isunsignedInt',
  'required' => false,
  ],
  'is_admin' => [
  'type' => self::TYPE_BOOL,
  'validate' => 'isBool',
  'required' => false,
  ],
  'default_config' => [
  'type' => self::TYPE_BOOL,
  'validate' => 'isBool',
  'required' => false,
  ],
  'price' => ['type' => ObjectModel::TYPE_FLOAT, 'required' => false],
  'json_values' => [
  'type' => self::TYPE_HTML,
  'lang' => false,
  'required' => false,
  ],
  'name' => [
  'type' => self::TYPE_STRING,
  'lang' => true,
  'validate' => 'isGenericName',
  'required' => false,
  ],
  'tags' => [
  'type' => self::TYPE_STRING,
  'lang' => true,
  'validate' => 'isGenericName',
  'required' => false,
  ],
  ],
  ];

  public $leftFile = 'default';
  public $rightFile = 'default';
  public $layerFile = 'default';
  public $pdffile = 'default';
  public $cover = false;
  public $pdfDir = false;
  public $jsonFile = false;

  public function __construct($id = null, $id_lang = null)
  {
    parent::__construct($id, $id_lang);

    $user_file = $this->id_user;

    $rightFile =
  _PS_IMG_DIR_.
  'scenes/'.
  'ndkcf/configs/'.
  $this->id_user.
  '/'.
  $this->id_product.
  '/'.
  $this->id.
  '-right.html';

    if (!file_exists($rightFile)) {
      $user_file = 0;
      $rightFile = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$user_file.'/'.$this->id_product.'/'.$this->id.'-right.html';
    }
    $leftFile = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$user_file.'/'.$this->id_product.'/'.$this->id.'-left.html';

    $layerFile = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$user_file.'/'.$this->id_product.'/'.$this->id.'-layer.html';

    if (isset($this->id_customization)) {
      $id_customization = (int) $this->id_customization;
      $sqlc =
  'SELECT c.id_product FROM '.
  _DB_PREFIX_.
  'customization c 
  WHERE c.id_customization = '.
  (int) $id_customization;
      $pdffile =
  _PS_IMG_DIR_.
  'scenes/ndkcf/pdf/'.
  $this->id_user.
  '/'.
  (int) Db::getInstance()->getRow($sqlc)['id_product'].
  '/'.
  $id_customization.
  '/render.html';
      $this->pdffile = $this->id && file_exists($pdffile) ? $pdffile : false;

      $jsonFile =
  _PS_IMG_DIR_.
  'scenes/ndkcf/pdf/'.
  $this->id_user.
  '/'.
  (int) Db::getInstance()->getRow($sqlc)['id_product'].
  '/'.
  $id_customization.
  '/config.json';
      $this->jsonFile = $this->id && file_exists($jsonFile) ? $jsonFile : false;

      $this->pdfDir =
  _PS_IMG_DIR_.
  'scenes/ndkcf/pdf/'.
  $this->id_user.
  '/'.
  (int) Db::getInstance()->getRow($sqlc)['id_product'].
  '/'.
  $id_customization;
    }
    $this->leftFile = $this->id && file_exists($leftFile) ? $leftFile : false;
    $this->rightFile =
  $this->id && file_exists($rightFile) ? $rightFile : false;
    $this->layerFile =
  $this->id && file_exists($layerFile) ? $layerFile : false;

    if (
  file_exists(
  _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$this->id_user.'/'.$this->id_product.'/'.$this->id.'-0-img.jpg'
  )
  ) {
      if (
  getimagesize(
  _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$this->id_user.'/'.$this->id_product.'/'.$this->id.
  '-0-img.jpg'
  ) > 0
  ) {
        $this->cover =
  'ndkcf/configs/'.
  $this->id_user.
  '/'.
  $this->id_product.
  '/'.
  $this->id.
  '-0-img.jpg';
      } elseif (
  file_exists(
  _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$this->id_user.'/'.$this->id_product.'/'.$this->id.
  '-1-img.jpg'
  )
  ) {
        if (
  getimagesize(
  _PS_IMG_DIR_.
  'scenes/'.
  'ndkcf/configs/'.
  $this->id_user.
  '/'.
  $this->id_user.
  '/'.
  $this->id_product.
  '/'.
  $this->id.
  '-1-img.jpg'
  ) > 0
  ) {
          $this->cover =
  'ndkcf/configs/'.
  $this->id_user.
  '/'.
  $this->id_product.
  '/'.
  $this->id.
  '-1-img.jpg';
        }
      }
    }
  }

  public function delete()
  {
    if ($this->leftFile) {
      unlink($this->leftFile);
    }
    if ($this->rightFile) {
      unlink($this->rightFile);
    }
    if ($this->layerFile) {
      unlink($this->layerFile);
    }
    if ($this->pdffile) {
      unlink($this->pdffile);
    }
    if ($this->pdffile) {
      unlink($this->pdffile);
    }
    if ($this->cover) {
      unlink(_PS_IMG_DIR_.'scenes/'.$this->cover);
    }
    if ($this->jsonFile) {
      $this->rrmdir($this->jsonFile);
    }

    for ($i = 0; $i < 3; ++$i) {
      if (
  file_exists(
  _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$this->id_user.'/'.$this->id_product.'/'.$this->id.
  '-'.
  $i.
  '-img.jpg'
  )
  ) {
        unlink(
  _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$this->id_user.'/'.$this->id_product.'/'.$this->id.
  '-'.
  $i.
  '-img.jpg'
  );
      }
    }

    return parent::delete();
  }

  public static function checkEnvironment()
  {
    $cookie = new Cookie(
  'psAdmin',
  '',
  (int) Configuration::get('PS_COOKIE_LIFETIME_BO')
  );

    return isset($cookie->id_employee) &&
  isset($cookie->passwd) &&
  Employee::checkPassword($cookie->id_employee, $cookie->passwd);
  }

  public static function updateGuestConfigs()
  {
    if (Context::getContext()->customer->id > 0) {
      // $sql =
      //   "update " .
      //   _DB_PREFIX_ .
      //   "ndk_customization_field_configuration set id_user = " .
      //   (int) Context::getContext()->customer->id .
      //   " where is_admin = 0 and id_user = 0 and id_guest =" .
      //   (int) Context::getContext()->cookie->id_connections;
      // //dump($sql);
      // Db::getInstance()->execute($sql);

      $configs = Db::getInstance()->executeS(
  'select id_ndk_customization_field_configuration as id from '.
  _DB_PREFIX_.
  'ndk_customization_field_configuration where is_admin = 0 and id_user = 0 and id_guest ='.
  (int) Context::getContext()->cookie->id_connections
  );

      foreach ($configs as $config) {
        $conf = new NdkCfConfig($config['id']);
        $conf->moveConFiles((int) Context::getContext()->customer->id);
        $conf->id_user = (int) Context::getContext()->customer->id;
        $conf->update();
      }
    }
  }

  public static function ensureFolder($id_user, $id_product)
  {
    if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/')) {
      mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/', 0777);
    }

    if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user)) {
      mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user, 0777);
    }

    if (!is_dir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) $id_product)) {
      mkdir(_PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.$id_user.'/'.(int) $id_product, 0777);
    }
  }

  public function moveConFiles($new_id)
  {
    self::ensureFolder($new_id, $this->id_product);
    $old_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.(int) $this->id_user.
  '/'.
  (int) $this->id_product.
  '/'.
  (int) $this->id;
    $new_path = _PS_IMG_DIR_.'scenes/'.'ndkcf/configs/'.(int) $new_id.
  '/'.
  (int) $this->id_product.
  '/'.
  (int) $this->id;

    $files = [
  '-left.html',
  '-right.html',
  '-layer.html',
  'img-0.jpg',
  'img-1.jpg',
  ];
    foreach ($files as $file) {
      if (file_exists($old_path.$file)) {
        rename($old_path.$file, $new_path.$file);
      }
    }

    // if($id_customization = (int) $this->id_customization > 0){
  //    $sqlc =
  //    "SELECT c.id_product FROM " .
  //    _DB_PREFIX_ .
  //    'customization c
  //        WHERE c.id_customization = ' .
  //    (int) $id_customization;
  //
  //     $id_product = (int) Db::getInstance()->getRow($sqlc)["id_product"];
  //
  //     $old_pdf_path =_PS_IMG_DIR_ .
  //     "scenes/ndkcf/pdf/" .
  //     (int)$this->id_user .
  //     "/" .
  //     (int)$id_product .
  //     "/" .
  //     (int)$id_customization .
  //     "/";
  //
  //     $new_pdf_path =_PS_IMG_DIR_ .
  //     "scenes/ndkcf/pdf/" .
  //     (int)$new_id .
  //     "/" .
  //     (int)$id_product .
  //     "/" .
  //     (int)$id_customization .
  //     "/";
  //     $pdf_files = [
  //           "render.html",
  //           "config.json",
  //         ];
  //         foreach ($pdf_files as $file) {
  //               if (file_exists($old_pdf_path . $file)) {
  //                 rename($old_pdf_path . $file, $new_pdf_path . $file);
  //               }
  //             }
  // }
  }

  public static function getConfigs(
  $id_user,
  $id_product = false,
  $id_lang,
  $id_guest = 0
  ) {
    $sql =
  'SELECT fc.*, fcl.* FROM '.
  _DB_PREFIX_.
  'ndk_customization_field_configuration fc
  LEFT JOIN `'.
  _DB_PREFIX_.
  'ndk_customization_field_configuration_lang` fcl
  ON (fc.`id_ndk_customization_field_configuration` = fcl.`id_ndk_customization_field_configuration` AND fcl.`id_lang` = '.
  (int) $id_lang.
  ')
  WHERE 1 AND fc.id_lang_default = '.
  (int) $id_lang.
  '  
  AND id_user = '.
  (int) $id_user.
  ' 
  '.
  ($id_product > 0 ? 'AND id_product = '.(int) $id_product.' ' : '').
  ' 
  '.
  ($id_guest > 0 ? 'AND id_guest = '.(int) $id_guest.' ' : '');

    //dump($sql);
    $search = Db::getInstance()->executeS($sql);
    $i = 0;
    foreach ($search as $item) {
      $search[$i]['img'] = false;
      if (
  file_exists(
  _PS_IMG_DIR_.
  'scenes/'.
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-0-img.jpg'
  )
  ) {
        if (
  getimagesize(
  _PS_IMG_DIR_.
  'scenes/'.
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-0-img.jpg'
  ) > 0
  ) {
          $search[$i]['img'] =
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-0-img.jpg';
        } elseif (
  file_exists(
  _PS_IMG_DIR_.
  'scenes/'.
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-1-img.jpg'
  )
  ) {
          if (
  getimagesize(
  _PS_IMG_DIR_.
  'scenes/'.
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-1-img.jpg'
  ) > 0
  ) {
            $search[$i]['img'] =
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-1-img.jpg';
          }
        }
      }

      ++$i;
    }
    if (sizeof($search) > 0) {
      return $search;
    }
  }

  public static function getAdminConfigs($id_product = false, $id_lang)
  {
    $search = Db::getInstance()->executeS(
  'SELECT fc.*, fcl.* FROM '.
  _DB_PREFIX_.
  'ndk_customization_field_configuration fc
  LEFT JOIN `'.
  _DB_PREFIX_.
  'ndk_customization_field_configuration_lang` fcl
  ON (fc.`id_ndk_customization_field_configuration` = fcl.`id_ndk_customization_field_configuration` AND fcl.`id_lang` = '.
  (int) $id_lang.
  ') 
  WHERE 1 
  AND is_admin = 1 AND fc.id_lang_default = '.
  (int) $id_lang.
  ' 
  '.
  ($id_product > 0 ? 'AND id_product = '.(int) $id_product.' ' : '')
  );

    $i = 0;
    foreach ($search as $item) {
      $search[$i]['img'] = false;
      if (
  file_exists(
  _PS_IMG_DIR_.
  'scenes/'.
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-0-img.jpg'
  )
  ) {
        $search[$i]['img'] =
  'ndkcf/configs/'.
  $item['id_user'].
  '/'.
  $item['id_product'].
  '/'.
  $item['id_ndk_customization_field_configuration'].
  '-0-img.jpg';
      }

      ++$i;
    }

    if (sizeof($search) > 0) {
      return $search;
    }
  }

  public function rrmdir($dir)
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ('.' != $object && '..' != $object) {
          if ('dir' == filetype($dir.'/'.$object)) {
            self::rrmdir($dir.'/'.$object);
          } else {
            unlink($dir.'/'.$object);
          }
        }
      }
      reset($objects);
      rmdir($dir);
    }
  }

  public static function setDefaultConfig(
  $id_ndk_customization_field_configuration
  ) {
    $config = new NdkCfConfig((int) $id_ndk_customization_field_configuration);
    $id_lang = Context::getContext()->language->id;
    if (0 == $config->default_config) {
      Db::getInstance()->execute(
  'UPDATE '.
  _DB_PREFIX_.
  'ndk_customization_field_configuration SET default_config = 0 WHERE id_product = '.
  (int) $config->id_product.
  ' AND id_lang_default='.
  (int) $id_lang
  );
    }
    Db::getInstance()->execute(
  'UPDATE '.
  _DB_PREFIX_.
  'ndk_customization_field_configuration SET default_config = 
   case
  when default_config = 0 then 1
   else 0
  end 
   WHERE id_ndk_customization_field_configuration = '.
  (int) $id_ndk_customization_field_configuration
  );
    NdkCfConfig::clearAllCache();
  }

  public static function getConfigurationByIdCustomization($id_customization)
  {
    $id = (int) self::getIdConfigurationByIdCustomization($id_customization);

    return new NdkCfConfig($id);
  }

  public static function getIdConfigurationByIdCustomization($id_customization)
  {
    return (int) Db::getInstance()->getValue(
  'SELECT id_ndk_customization_field_configuration 
  FROM '._DB_PREFIX_.'ndk_customization_field_configuration
  WHERE id_customization = '.(int) $id_customization
  );
  }

  public static function getDefaultConfigPrice($id_product)
  {
    $id_lang = Context::getContext()->language->id;
    $search = Db::getInstance()->getRow(
  'SELECT price FROM '.
  _DB_PREFIX_.
  'ndk_customization_field_configuration 
  WHERE id_product = '.
  (int) $id_product.
  ' AND default_config = 1 AND id_lang_default = '.
  (int) $id_lang
  );

    if ($search && $search['price'] > 0) {
      $id_address = (int) Context::getContext()->cart->id_address_invoice;
      $address = Address::initialize($id_address, true);
      $tax_manager = TaxManagerFactory::getManager(
  $address,
  Product::getIdTaxRulesGroupByIdProduct(
  (int) $id_product,
  Context::getContext()
  )
  );
      $product_tax_calculator = $tax_manager->getTaxCalculator();
      $usetax = Group::getPriceDisplayMethod(
  Group::getPriceDisplayMethod(
  Context::getContext()->customer->id_default_group
  )
  );
      if (0 == Product::$_taxCalculationMethod) {
        $usetax = true;
      } else {
        $usetax = false;
      }
      $price = $product_tax_calculator->addTaxes($search['price']);

      return $price;
    } else {
      return false;
    }
  }

  public static function clearAllCache()
  {
    Db::getInstance()->Execute(
  'TRUNCATE TABLE `'._DB_PREFIX_.'ndk_customization_field_cache`'
  );
  }
}
