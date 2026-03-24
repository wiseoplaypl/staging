<?php

/* *
 * @author    Łukasz Szpak (info@dev-bot.pl)
 * @Copyright 2019 SzpaQ
 * @license   ALL RIGHTS RESERVED
 * * */

namespace CsvObject;

class Row
{
    public $__rowNumber = 0;
    public function getArray()
    {
        return (array) $this;
    }
    public function getRowNumber()
    {
        return $this->__rowNumber;
    }
    public function setRowNumber($row)
    {
        $this->__rowNumber = $row;
        return true;
    }
    public function __construct($data = array())
    {
        if (!count($data)) {
            foreach ($data as $k => $v) {
                $this->$k = $v;
            }
            \CsvObject::$rowNumber = \CsvObject::$rowNumber + 1;
            $this->__rowNumber = \CsvObject::$rowNumber;
        }
    }
}
