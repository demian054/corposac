<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');
/*
  | -------------------------------------------------------------------
  | INTERFACES
  | -------------------------------------------------------------------
  | Este archivo define las rutas hacia las inderentes interfaces del
  | que seran implemntadas por los controladores y modeles del sistema.
  |
  | -------------------------------------------------------------------
  | Instructions
  | -------------------------------------------------------------------
  |
 */
//Define la ruta al directorio de librerias.
if (!defined('LIBRARY'))
    define('LIBRARY', APPPATH . 'libraries/');
//Define la ruta al directorio de interfaces.
if (!defined('INTERFACES'))
    define('INTERFACES', LIBRARY . 'interfaces/');
//Provee la ruta a la interfaz Controller_interface.php
if (!defined('CONTROLLER_INTERFACE'))
    define('CONTROLLER_INTERFACE', INTERFACES . 'Controller_interface' . EXT);
//Provee la ruta a la interfaz Model_interface.php
if (!defined('MODEL_INTERFACE'))
    define('MODEL_INTERFACE', INTERFACES . 'Model_interface' . EXT);