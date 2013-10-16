<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');

/**
 * Controller_interface - 
 * Interfas con los metodos bases presentes en la mayoria de los controladores.
 * @package      Application.Libraries
 * @subpackage   interfaces
 * @author       Reynaldo Rojas, Jose Rodriguez
 * @copyright    Por definir
 * @license      Por definir
 * @version      v-1 Version 16/08/2012 05:00 PM
 */
interface Controller_interface {

    /**
     * <b>Method:	index()</b>
     * Metodo por defecto a ser accedido al por el controlador.
     * @param		array $params arreglo que contiene una accion a ejecutarse 
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    function index();

    /**
     * <b>Method:	create()</b>
     * Define el nombre del metodo que asumira la creación de datos.
     * @param		array $params arreglo que contiene una accion a ejecutarse .
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    function create($params);

    /**
     * <b>Method:	listAll()</b>
     * Define el nombre del metodo que listara todos los datos de la instacia.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    function listAll();

    /**
     * <b>Method:	edit($params)</b>
     * Define el nombre del metodo que asumira la edición de datos.
     * @param		array $params arreglo que contiene una accion a ejecutarse.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    function edit($params);

    /**
     * <b>Method:	delete()</b>
     * Define el nombre del metodo que asumira la elimicación de datos.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    function delete();

    /**
     * <b>Method:	detail()</b>
     * Define el nombre del metodo que proveera detalles de los datos.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    function detail();

    /**
     * <b>Method:	deactivate()</b>
     * Define el nombre del metodo encargado de desactivar la instancia.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    //function deactivate();

    /**
     * <b>Method:	activate()</b>
     * Define el nombre del metodo encargado de activar la instancia.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 04:21 PM
     * */
    //function activate();
}

/* END Interface Controller_interface       */
/* END of file Controller_interface.php */
/* Location: ./application/libraries/interfaces/Controller_interface.php */