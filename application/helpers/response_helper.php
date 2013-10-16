<?php

/**
 * Sietpol Response Helper
 * @package	Sietpol
 * @subpackage	helpers
 * @author	jfarias <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		V-1.1 28/09/11 11:36 AM
 * */
//Evaluamo si ya existe creado un metodo con el nombre throwResponse.
if (!function_exists('throwResponse')) {

    /**
     * <b>Method:	throwResponse()</b>
     * Este metodo se encarga de enviar una respuesta estandar a los request de ajax desde el cliente. Es importante tener en cuenta
     * que luego de ser llamado este metodo no se ejecutara ninguna otra instrucción php, ya que el mismo emplea un el
     * metodo nativo de php <b>die()</b>.
     * @param	mixed   Arreglo de parameto con las configuraciones a ser empleadas dentro del metodo.
     *              Boolean [result] Bandera que indica si la transaccion fue exitosa.
     *              String  [msg] Mensaje de la exito o error producto de la transaccion.
     *              Array   [extra_vars] Arreglo opcional de variables extras para pasar al cliente como parte de la respuesta.
     *              Boolean [success] Bandera que indica si la transaccion fue exitosa. Default true.
     *              String   [title] Nombre de la operación.
     * @return	 return false
     * @author	 Jesus Farias, Jose Rodriguez <josearodrigueze@gmail.com>
     * @version	 v1.1 17/09/2012 10:35
     * */
    function throwResponse($params) {
        //Obtenemos los parametros para ser empleados en la repuesta.
        extract($params);

        //Escribimos el titulo de la ventana de alerta.
        $title = ($result===FALSE) ? "Error! - " .$title :  $title;

        //setamoss por defecto la variable success.
        $success = ($success===FALSE) ? false : true;

        $array_response = array(
            'title' => $title,
            'result' => $result,
            'msg' => $msg,
            'extra_vars' => $extra_vars
        );

        //Finalizamos la ejecucion del script php y manamos la respuesta en formato json.
        die(json_encode(array('success' => $success, 'response' => $array_response)));
    }

}

//Determinamos si existe el helper declarado.
if (!function_exists('tankAuthResponse')) {

    /**
     * <b>Method: tankAuthResponse()</b>
     * Se encarga de enviar una respuesta estandar a los request de ajax desde el cliente para tank_auth
     * @param	Boolean success valor booleano esperado por el submit que indica si la operacion es exitosa o no
     *          String situation situacion especifica del usuario al momento de ingresar al sistema
     *                 directo   -> cuando el usuario posee un accede de forma directa con el rol
     *                 valido    -> cuando no presenta errores de acceso
     *                 no_valido -> cuando presenta errores de acceso
     *          String msn Mensaje que se debe mostrar
     *          Array $estra_vars parametros adicionales
     * @author	Reynaldo Rojas <rrojas@rialfi.com>
     * @version V-1.0 05/09/12 12:02 PM
     * */
    function tankAuthResponse($success, $situation, $msn, $extra_vars = array()) {
        echo json_encode(array('success' => $success, 'situation' => $situation, 'msn' => $msn, 'extra_vars' => $extra_vars));
    }

}
?>