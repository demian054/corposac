<?php

/**
 * Sietpol      Postgres Helper
 * @package	Sietpol
 * @subpackage	helpers
 * @author	rrojas <reyre8@gmail.com>	
 * @copyright	Por definir
 * @license	Por definir
 * @version	V-1.0 15/11/11 09:35 AM
 * */
/**
 * <b>Method:	formatPostgresMessage()</b>
 * @method	Permite dar el formato adecuado a los mensajes de error enviados por el manejador de Base de Datos (en este 
 *              caso POSTGRES). Este mensaje es utilizado posteriormente para traducir el mensaje a lenguaje usuario a traves
 *              del uso del archivo de mensajes /language/espanol/message_lang.php
 * @param	String $msg mensaje enviado por el manejador de Base de Datos
 * @return	String nombre del mensaje que debe ser ubicado en el el archivo /language/espanol/message_lang.php. Si el
 *              metodo no encuentra un formato de mensaje envia el nombre de mensaje de error definido por defecto.
 * @author	Reynaldo Rojas
 * @version	v1.0 
 * */
if (!function_exists('formatPostgresMessage')) {

    function formatPostgresMessage($msg) {
	preg_match_all('/«(.*)»/', $msg, $array_matches);

	$message = $array_matches[1][0];

	if (!empty($message))
	    return $message;
	else
	    return 'message_operation_error';
    }

}
?>
