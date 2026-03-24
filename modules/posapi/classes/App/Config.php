<?php

/**
 *
 * LICENCE
 * ALL RIGHTS RESERVED.
 * YOU ARE NOT ALLOWED TO COPY/EDIT/SHARE/WHATEVER.
 * IN CASE OF ANY PROBLEM CONTACT AUTHOR.
 * @author    Łukasz Szpak (szpaaaaq@gmail.com)
 * @Copyright 2018 SzpaQ
 * @license   ALL RIGHTS RESERVED
 *
 * * */

namespace App;

class Config {
    public static $CONFIG_FILE = false;
    public static $DATA = false;
    public function __construct(string $file) {
        $this->setData($file);
    }
    private function setData($file) {
        Config::$CONFIG_FILE = $file;
        if(false !== Config::$DATA) {
            return;
        }
        Config::$DATA = array();
        if(Files::Exists($file)) {
            $category = 'default';
            $handle = fopen($file, "r");
            if($handle) {
                while (($line = fgets($handle)) !== false) {
                    if(preg_match('/\[([a-zA-Z0-9_]+)\]/', $line, $category_tmp)) {
                        $category = $category_tmp[1];
                    } else {
                        if(!isset(Config::$DATA[$category])) {
                            Config::$DATA[$category] = array();
                        }
                        $array = explode(':', $line, 2);

                        if($key = trim($array[0])) {
                            if(isset($array[1]) && $value = trim($array[1])) {
                                Config::$DATA[$category][$key] = $value;
                            } else {
                                Config::$DATA[$category][$key] = false;
                            }
                        }
                    }
                }
            }
        } else {
            Files::Save($file, "[application]\n\n");
            if(false === Files::Exists($file)) {
                Throw new Exception('Unable to create config file "'. basename($file) .'" in"'.dirname($file).'". Directory is not writable. You must put it there on your own, enable access to directory. Or choose different file. ');
            }
        }
/*
        if(!isset(Config::$DATA['application']['path'])) {
            Config::Set('application', 'path', defined("PATH") ? PATH : __DIR__ .'/../');
        }
        if(!isset(Config::$DATA['application']['controllersDir'])) {
            Config::Set('application', 'controllersDir', PATH .'/Controllers/');
        }
        if(!isset(Config::$DATA['application']['modelsDir'])) {
            Config::Set('application', 'modelsDir', PATH .'/Models/');
        }
        if(!isset(Config::$DATA['application']['configDir'])) {
            Config::Set('application', 'configDir', dirname($file).'/');
        }
        if(!isset(Config::$DATA['application']['viewsDir'])) {
            Config::Set('application', 'viewsDir', PATH .'/Views/');
        }
        if(!isset(Config::$DATA['application']['cacheDir'])) {
            Config::Set('application', 'cacheDir', PATH .'/Cache/');
        }
        if(!isset(Config::$DATA['application']['moduleDir'])) {
            Config::Set('application', 'moduleDir', PATH .'/Modules/');
        }
        if(!isset(Config::$DATA['application']['uploadDir'])) {
            Config::Set('application', 'uploadDir', PATH .'/../Upload/');
        }
        if(!isset(Config::$DATA['application']['thumbnailDir'])) {
            Config::Set('application', 'thumbnailDir', PATH .'../Upload/Thumbnails/');
        }
        if(!isset(Config::$DATA['website']['url'])) {
            Config::Set('website', 'url', 'http://'.$_SERVER['HTTP_HOST'].'/');
        }
        */
    }

    public static function Get(string $category, string $key = null) {
        if(isset(Config::$DATA[$category])) {
            if(null !== $key) {
                return isset(Config::$DATA[$category][$key]) ? Config::$DATA[$category][$key] : false;
            } else {
                return (object) Config::$DATA[$category];
            }
        }
        return false;
    }

    public static function Set(string $category, string $key, string $value ) : string {

        if(false === Config::$DATA) {
            $config = new Config(Config::$CONFIG_FILE);
        }
        if(isset(Config::$DATA[$category])) {

            Config::$DATA[$category][$key] = $value;

            $file = '';

            foreach(Config::$DATA as $k => $v)  {
                $file .= "[$k]\n";
                foreach($v as $name => $val) {
                    $file .= "$name: $val\n";
                }

                $file .= "\n\n";
            }
            Files::Save(Config::$CONFIG_FILE, $file);
        } else {
            $file = trim(Files::Get(Config::$CONFIG_FILE));
            $file .= PHP_EOL.PHP_EOL;
            $file .= '['. $category .']' .PHP_EOL;
            $file .= $key.': '.$value;
            Files::Save(Config::$CONFIG_FILE, $file);

        }
        return $value;
    }
}
