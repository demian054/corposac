<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');

/**
 * Ruta del directorio para la descarga de los archivos
 */
$config['temp_dir'] = 'assets' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
$config['long_limit'] = 100;
$config['short_limit'] = 25;
$config['thumb_limit'] = 3;

//Csv download
$config['temp_dir_log'] = dirname(tempnam(NULL, '')) . DIRECTORY_SEPARATOR;
$config['download_csv'] = 'our_tools/download/FD_csv/';


define('SAC_INCIDENCIA_ABIERTA', 150);
define('SAC_INCIDENCIA_INCOMPLETA', 156);