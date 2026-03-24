<?php

/**
 * @author Łukasz Szpak ( info@dev-bot.pl )
 * @Copyright 2019 SzpaQ <dev-bot.pl>
 * @license ALL RIGHTS RESERVED
 * */
$directories = [
    dirname(__FILE__) .'/classes/',
    dirname(__FILE__) .'/classes/Importers/',
];

foreach ($directories as $directory) {
    foreach (scandir($directory) as $v) {
        $file = $directory.$v;
        if (is_dir($file) || !file_exists($file)) {
            continue;
        }
        require_once $file;
    }
}
