<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');

/**
 * model_interface - 
 * Interfas con los metodos bases presentes en la mayoria de los modelos.
 * @package      Application.Libraries
 * @subpackage   interfaces
 * @author       Reynaldo Rojas, Jose Rodriguez
 * @copyright    Por definir
 * @license      Por definir
 * @version      v-1 Version 16/08/2012 05:00 PM
 */
interface Model_interface {

    /**
     * <b>Method:	create()</b>
     * Define el nombre del metodo que asumira la creación de datos.
     * @param		array $params Arreglo con los datos a ser persistidos.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 41:21 PM
     * */
    function create($params);

    /**
     * <b>Method:	getById($id)</b>
     * Define el nombre del metodo que listara todos los datos de la instacia.
     * @param  		integer $id Identificador del registro
     * @return 		array registro con los datos de la instancia seleccionado.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 41:21 PM
     * */
    function getById($id);

    /**
     * <b>Method:	update($id, $data)</b>
     * Define el nombre del metodo que asumira la edición de datos.
     * @param  		integer $id Identificador del registro
     * @param  		array $data arreglo con los valores a actualizar
     * @return 		boolean V/F en caso de exito o fracaso de la actualizacion del registro
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 41:21 PM
     * */
    function update($id, $data);

    /**
     * <b>Method:	delete($id)</b>
     * Define el nombre del metodo que asumira la elimicación de datos.
     * @param 		integer $id Identificador del registro.
     * @return 		boolean V/F en caso de exito o fracaso de la actualizacion del registro
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 41:21 PM
     * */
    function delete($id);

    /**
     * <b>Method:	_format($data, $type)</b>
     * Define el nombre del metodo que Limpiara los datos del formulario para que sea compatible con el insert y el update.
     * @param		array $data arreglo con data original.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 41:21 PM
     * */
    function _format(&$data, $type = false);

    /**
     * <b>Method:	getAll()</b>
     * Define el nombre del metodo encargado de desactivar la instancia.
     * @param		integer $start Registro por se inicia la busqueda.
     * @param		integer $limit Limite de registros a consultar.
     * @param		string  $search_field cadena de texto a buscar.
     * @param		boolean $count indica si se quiere contar la cantidad de registros.
     * @return		array arraglo dque contiene los datos consultados en BD.
     * @return		array Arreglo de datos retornado por la consulta, FALSE en caso contrario.
     * @author		José Rodríguez
     * @version		v-1.0 14/08/12 41:21 PM
     * */
    //function getAll($start,$limit,$search_field, $count = FALSE);
}

/* END Interface Model_interface   */
/* END of file Model_interface.php */
/* Location: ./application/libraries/interfaces/Model_interface.php */