<?php

/* *
 * @author    Łukasz Szpak (info@dev-bot.pl)
 * @Copyright 2019 SzpaQ
 * @license   ALL RIGHTS RESERVED
 * * */

require_once dirname(__FILE__).'/CsvObject/Row.php';

use CsvObject\Row;

class CsvObject
{
    public $file;
    public $row = 0;
    public $total_rows = false;
    public $keys = false;
    public $handle = false;
    public $header = true;
    public $current = false;
    public static $rowNumber = 0;

    public function __construct($file = null)
    {
        if ($file) {
            $this->setFile($file);
        }
    }
    public function __destruct()
    {
        if ($this->handle) {
            fclose($this->handle);
        }
    }
    public function setFile($file) : bool
    {
        $this->file = $file;
        if (!file_exists($this->file)) {
            Throw new Exception('File '. $this->file .' does not exist.');
        }
        if ($this -> handle = fopen($this->file, 'r')) {
            $this->getTotalRows();
            fclose($this->handle);
            $this -> handle = fopen($this->file, 'r');
            return true;
        }
        return false;
    }
    public function getKeys() : array
    {
        if (!$this->handle) {
            Throw new Exception('File '. $this->file .' is not opened.');
        }
        if ($this->keys == false && $this->row == 0) {
            $this->keys = fgetcsv($this->handle);
            $this->row++;
        } elseif ($this->keys == false && $this->row > 0) {
            Throw new Exception('Columns unknnown.');
        } else {
            return array();
        }
        return $this->keys;
    }
    public function getRow()
    {
        if ($this->row == 0 && $this->header == true) {
            $this->getKeys();
        }
        $row = fgetcsv($this->handle);
        if (!$row) {
            return false;
        }
        $this->current = new Row;
        $this->current->setRowNumber($this->row);
        if ($this->keys) {
            foreach ($this->keys as $k => $v) {
                if (isset($row[$k])) {
                    $this->current->$v = $row[$k];
                }
            }
        } else {
            $column = 'A';
            foreach ($row as $k => $v) {
                $this->current->{$column++} = $v;
            }
        }
        $this->row++;
        return $this->current;
    }
    public function getTotalRows()
    {
        if ($this->total_rows === false) {
            $lines = true === $this->header ? -1 : 0;
            while (!feof($this->handle)) {
                $lines += substr_count(fread($this->handle, 8192), "\n");
            }
            $this->total_rows = (int) $lines;
        }
        return $this->total_rows;
    }
    public function addRow(CsvObject\Row $row)
    {
        if (!$this->file) {
            $this->file = tmpfile();
            $this->handle = fopen($this->file, 'w');
        }
        fputcsv($this->file, (array) $row);
        $this->total_rows = false;
    }
}
