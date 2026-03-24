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

function upgrade_module_1_3_4($module)
{
    if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
        $filename = __PS_BASE_URI__.'modules'
            .DIRECTORY_SEPARATOR.'lgproductmove'
            .DIRECTORY_SEPARATOR.'AdminLGProductMove.php';
    } else {
        $filename = _PS_MODULE_DIR_ . 'lgproductmove'
            .DIRECTORY_SEPARATOR.'AdminLGProductMove.php';
    }

    if (file_exists($filename)) {
        unlink($filename);
    }
    return $module;
}
