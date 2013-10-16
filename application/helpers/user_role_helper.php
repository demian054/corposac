<?php

/**
 * Ouroboros User Role Helper
 * @package	Ouroboros
 * @subpackage	helpers
 * @author	Reynaldo Rojas <rrojas@rialfi.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		V-1.0 14/09/12 05:27 PM
 * */

/**
 * <b>Method:	rootRoleRestriction()</b>
 * @method	Permite restringir la visualizacion de registros relacionados al ROOT_ROLE
 * @param	String $field_text nombre alias del campo role_id
 * @return	Condición adicional que debe ser añadida a la consulta para no mostrar los registros asociados al rol ROOT
 * @author	Reynaldo Rojas <rrojas@rialfi.com>
 * @version	 v1.0 
 * */
if (!function_exists('rootRoleRestriction')) {
	function rootRoleRestriction($field_text = 'id') {

		$ci = & get_instance();
		
		// Condicion para evitar mostrar el rol principal del sistema
		if ($ci->session->userdata('role_id') != ROOT_ROLE)
			return $ci->db->where("$field_text !=", ROOT_ROLE);
	}
}

/**
 * <b>Method:	rootUserRestriction()</b>
 * @method	Permite restringir la visualizacion de registros relacionados al ROOT_USER
 * @param	String $field_text nombre alias del campo user_id
 * @return	Condición adicional que debe ser añadida a la consulta para no mostrar los registros asociados al usuario ROOT
 * @author	Reynaldo Rojas <rrojas@rialfi.com>
 * @version	 v1.0 
 * */
if (!function_exists('rootUserRestriction')) {
	function rootUserRestriction($field_text = 'id') {

		$ci = & get_instance();
		
		// Condicion para evitar mostrar el usuario principal del sistema
		if ($ci->session->userdata('user_id') != ROOT_USER)
			return $ci->db->where("$field_text !=", ROOT_USER);
	}
}

?>