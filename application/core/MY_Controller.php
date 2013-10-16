<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');

require_once CONTROLLER_INTERFACE;

/**
 * <b>MY_Controller </b>
 * Controlador puente que facilita la implementacion de metodos
 * comunes a todos los controladores del sistema, eliminando la 
 * reescritura de codigo redundante en cada controlador y facilitando 
 * el mantenimiento de este codigo comun.
 * @package      Application
 * @subpackage   Core
 * @author       Reynaldo Rojas, Jose Rodriguez
 * @copyright    Por definir
 * @license      Por definir
 * @version      v-1 Version 16/08/2012 05:00 PM
 * */
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    
    /**
     * <b>Method:	_remap($method, $params = array())</b>
     * Invoca a al metodo de has_permisions de la libreria Tank Auth, que verifica el acceso a un metodo dependiendo de un 
     * tipo de rol.
     * @param       String $method Nombre del metodo invocado.
     * @param       Array  $params Parametros adicionales a la funcionabilida de has_permisions().
     * @return		String.
     * @author		Otros, Reynaldo Rojas, Jose Rodriguez
     * @version		v1.0 16/08/12 03:28 PM
     */
    public function _remap($method, $params = array()) {
        $this->tank_auth->has_permissions($this->getModule(), $this, $method, $params);
    }

    /**
     * <b>Method:	getModule()</b>
     * Obtiene el nombre del modulo al cual pertence el controlador ejecutado.
     * @return		String.
     * @author		Reynaldo Rojas, Jose Rodriguez
     * @version		v1.0 16/08/12 03:28 PM
     */
    protected final function getModule() {
        $reflector = new ReflectionClass(get_class($this));
        return array_pop(explode('/', dirname(dirname($reflector->getFileName()))));
    }

}

/* END Class MY_Controller       */
/* END of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */