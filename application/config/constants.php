<?php  if ( ! defined('BASEPATH')) exit('Acceso Denegado');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
  | -------------------------------------------------------------------
  | INTERFACES
  | -------------------------------------------------------------------
  | Esta seccion define las rutas hacia las diferentes interfaces que 
  | seran implementadas por los controladores y modeles del sistema.
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

/*
  | -------------------------------------------------------------------
  | Variables de Log
  | -------------------------------------------------------------------
  | Esta seccion define los valores de acceso a las diferentes partes del 
  | sistema registrados en log, segun su respectivo valor en las tabla
  | category (tabla normalizada: Log_type)
  |
 */

if (!defined('ACCESS'))
    define('ACCESS', 75);

if (!defined('SUCCESS'))
    define('SUCCESS', 76);

if (!defined('FAILURE'))
    define('FAILURE', 77);


/* End of file constants.php */
/* Location: ./application/config/constants.php */