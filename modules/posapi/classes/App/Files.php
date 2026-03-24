<?php

/**
 * @author    Łukasz Szpak (szpaaaaq@gmail.com)
 * @Copyright 2018 SzpaQ
 * @license   ALL RIGHTS RESERVED
 * */

namespace App;

class Files
{

    const SEPARATOR = '/';
    const FILES = 4;
    const RFILES = 5;
    const RDIR = 6;
    const DIR = 7;
    const ALL = 8;
    const RALL = 9;


    /**
     * get file content
     * @param string file
     * @param bool json (optional) checks if content is in json format
     * @param bool json (optional) if json format specify if return array(true) or object (false) (third param of json_decode)
     * @return mixed string if param json is true try to decode and if there is no error return decoded string. Otherwise throw Exception
     * */
    public static function Get(string $file, $json = false, $array = false) {
        if(file_exists($file)) {
            $content = file_get_contents($file);
            if(true === $json) {
                $content = json_decode($content, $array);
                if(JSON_ERROR_NONE === json_last_error()) {
                    return $content;
                } else {
                    throw new Exception("Content of file \"$file\" is not valid json string");
                }
            } else {
                return $content;
            }
        } else {
            throw new Exception("File \"$file\" doesn't exist.");
        }
    }

    /**
     * Saves content to file. If directory not exists - creates one
     * @param string file filename
     * @param string content Content of file to be saved
     * @param bool json (optional) save as json format
     * @return mixed content return content given in second param, throw exception if couldn't save file
     * */
    public static function Save(string $file, $content, $json = false, $append = false) {
        $fileName = self::TargetDir(dirname($file)).basename($file);
        file_put_contents($fileName, true === $json || is_array($content) || is_object($content) ? json_encode($content) : $content, true === $append ? FILE_APPEND : NULL);
         if(false === Files::Exists($fileName)) {
             Throw new Exception("Unable to save file $file. Check permissions");
         } else {
             return $content;
         }
    }

    /**
     * Check if file exists
     * @param string $file path to file
     * @return bool true if file exists
     * */
    public static function Exists($file) {
        return (bool) file_exists($file);
    }

    /**
     * Require_once file
     * @param string $file path to file
     * @return void throws exception if file doesn't exist
     * */
    public static function Require($file) {
        if(file_exists($file)) {
            require_once($file);
        } else {
            Throw new \Exception("File $file doesn't exist");
        }
    }

    /*
     * Scan directory
     * @param string directory
     * @param bool recursive (optional) if true scan recursive. Default false
     * @param bool excludedir (optional) exclude directories from results. If true it will return only files. Defualt false
     * @return array array of files. Each row is full_path to file
     * */
    public static function Scandir($dir, $option = false, $excludedir = false, $only_directory = false) : array {
        $scan = scandir($dir);
        $files = array();
        if(!is_bool($option)) {
            switch($option){
                case self::ALL:
                    $excludedir = false;
                    $recursive = false;
                break;
                case self::RALL:
                    $excludedir = false;
                    $recursive = true;
                BREAK;
                case self::FILES:
                    $excludedir = true;
                    $recursive = false;
                BREAK;
                case self::RFILES:
                    $excludedir = true;
                    $recursive = true;
                BREAK;
                case self::DIR:
                    $excludedir = false;
                    $recursive = false;
                    $only_directory = true;
                BREAK;
                case self::RDIR:
                    $excludedir = false;
                    $recursive = true;
                    $only_directory = true;
                BREAK;
            }
        } else {
            $recursive = $option;
        }
        if(false === $excludedir && false === $recursive) {
            foreach($scan as $v) {
                if('.' == $v || '..' == $v) {
                    continue;
                }
                if($only_directory == false) {
                    $files[] = $dir.self::SEPARATOR.$v;
                } else{
                    if(is_dir($dir.self::SEPARATOR.$v)) {
                        $files[] = $dir.self::SEPARATOR.$v;
                    }
                }
            }
        } elseif(true === $recursive) {
            foreach($scan as $v) {
                if('.' == $v || '..' == $v) {
                    continue;
                }
                if(!is_dir($dir.self::SEPARATOR.$v)) {
                    if($only_directory === false) {
                        $files[] = $dir.self::SEPARATOR.$v;
                    }
                } elseif(true === $recursive) {
                    $files = array_merge($files, self::Scandir($dir.self::SEPARATOR.$v, $option, $excludedir));
                    if(false === $excludedir) {
                        $files[] = $dir.self::SEPARATOR.$v;
                    }
                }
            }
        } else {
            foreach($scan as $v) {
                if('.' == $v || '..' == $v) {
                    continue;
                }
                if(true === $excludedir && is_dir($dir.self::SEPARATOR.$v)) {
                    continue;
                }
                $files[] = $dir.self::SEPARATOR.$v;
            }
        }
        return $files;
    }

    /**
     * creates directory if not exists and return target directory suffixed with separator /
     * @param string $directory target directory
     * @param int chmod - default 0755
     * @return string target directory
     * */
    public static function TargetDir(string $directory, $chmod = 0777) : string {
        $path = '';
        $ex = explode(self::SEPARATOR, trim($directory));
        $i = 0;
        while($ex) {
            if(!$catalogue = array_shift($ex)) {
                continue;
            }
            $path .= self::SEPARATOR.$catalogue;
            if(!file_exists($path)) {
                if(!mkdir($path)) {
                    Throw new Exception("UNABLE TO CREATE DIRECTORY: ". $path .".");
                }
                chmod($path, $chmod);
            }
        }
        return $path .self::SEPARATOR;
    }


    /**
     * Copy directory recursive
     * @param string $directory Directory to be copied
     * @param string $directory Destination directory
     * @return array copied files array on success
     * */
    public static function Copy($directory, $destination) : array {
        $copied_files = array();
        if(is_dir($directory)) {
            $destination = self::TargetDir($destination,0777);
            $files = self::Scandir($directory);
            foreach($files as $v) {
                if(in_array($v,array('.','..'))) {
                    continue;
                }
                $target_file = $destination.self::SEPARATOR.str_replace($directory, '', $v);
                if(is_dir($v)) {
                    $copied_files = array_merge(self::Copy($v, $target_file));
                } else {
                    copy($v, $target_file);
                    $copied_files[] = $target_file;
                }
            }
            return $copied_files;
        } else {
            copy($directory, $destination);
            return array($destination);
        }
    }

    /**
     * Delete files recursively
     * @param string target target directory/file to be deleted.
     * @return bool true on success
     * */
    public static function Delete(string $target) : bool {
        if(is_dir($target)) {
            $files = self::Scandir($target, self::RALL);
            usort($files,function($a,$b){
                return strlen($b)-strlen($a);
            });
            array_map(function($match){
                if(!is_dir($match)){ if(!unlink($match)) Throw new Exception('Cannot delete file '. $match);}
                else{ if(!rmdir($match)) Throw new Exception("Cannot Delete Directory $match");}
            },$files);
            rmdir($target);
            return true;
        } else {
            if(Files::Exists($target)) {
                unlink($target);
            }
            return true;
        }
    }
}
