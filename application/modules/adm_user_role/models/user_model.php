<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * User_model class
 * @package			module: user
 * @subpackage		models
 * @author			Eliel Parra <elielparra@gmail.com>, Reynaldo Rojas <reyre8@gmail.com>
 * @copyright		Por definir
 * @license			Por definir
 * @version			v1.0 10/11/11 12:05 PM
 * */
class User_model extends CI_Model {

	var $table = 'rbac.users';
	var $table_role = 'rbac.user_role';
	var $widget_role = 'rbac.widgets_roles';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * <b>Method:	create</b>
	 * 		Permite crear un nuevo usuario 
	 * @param		Array $data Arreglo con los detalles del nuevo usuario
	 * @return		Boolean TRUE en caso de que la insercion se ejecute de forma satisfactoria, FALSE en caso de error
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 12:10 PM
	 * */
	function create($data) {

		$this->db->trans_start();
		$this->_format($data, 'INSERT');
		$this->db->insert($this->table, $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	getById()</b>
	 * 		Retorna los datos del usuario seleccionado
	 * @param		Integer $record_id Numero identificador del detalle de users
	 * @return		Array $query->row_array() Arreglo con los detalles del usuario seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:23 PM
	 * */
	function getById($record_id) {

		$this->db->select("id");
		$this->db->select("username");
		$this->db->select("password");
		$this->db->select("email");
		$this->db->select("activated");
		$this->db->select("banned");
		$this->db->select("ban_reason");
		$this->db->select("new_password_key");
		$this->db->select("new_password_requested");
		$this->db->select("new_email");
		$this->db->select("new_email_key");
		$this->db->select("last_ip");
		$this->db->select("(last_login::date) AS last_login");
		$this->db->select("(created::date) AS created");
		$this->db->select("(modified::date) as modified");
		$this->db->select("first_name");
		$this->db->select("last_name");
		$this->db->select("_document");
		$this->db->select("phone_number");
		$this->db->from($this->table);
		$this->db->where('id', $record_id);
		$this->db->where('deleted', '0');

		// Condicion para evitar mostrar el rol principal del sistema
		rootUserRestriction();

		$query = $this->db->get();
		if ($query->num_rows() > 0)
			return $query->row_array();
		else
			return FALSE;
	}

	/**
	 * <b>Method:	getAll()</b>
	 * Retorna todos los registros de usuarios
	 * @param		String  $search_field indica el valor por el cual se deben filtrar los resultados del grid
	 * @param		Char    $deleted indica si deben mostrar los registros existentes o eliminados.
	 * 					    '0'   -> No eliminado (valor por default)
	 * 					    '1'   -> Eliminado
	 * 				Boolean FALSE -> Indica si se deben mostrar todos los roles sin filtro de eliminacion
	 * @return		Array $query->result() Arreglo de objetos con los detalles de todos los roles del sistema.
	 *                    En caso contrario el metodo retorna el valor booleano FALSE
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:46 AM
	 * */
	function getAll($search_field, $deleted = '0') {

		// Campos de la consulta
		$this->db->select("id");
		$this->db->select("username");
		$this->db->select("email");
		$this->db->select("first_name");
		$this->db->select("last_name");
		$this->db->select("_document");
		$this->db->select("phone_number");
		$this->db->select("last_login");
		$this->db->select("(CASE activated WHEN 1 THEN 'Sí' WHEN 0 THEN 'No' END) AS activated");

		// Consulta de usuarios
		$this->db->from($this->table);

		// Condicion para verificar si se debe filtrar por usuario eliminado
		if ($deleted !== FALSE)
			$this->db->where('deleted', $deleted);

		// Condicion para filtrar de acuerdo al campo de busqueda
		if (!empty($search_field)) {
			$this->db->where("(username ILIKE '%$search_field%'
								OR first_name ILIKE '%$search_field%'
								OR last_name ILIKE '%$search_field%'
								OR _document ILIKE '%$search_field%'
								OR email ILIKE '%$search_field%')");
		}

		// Condicion para evitar mostrar el rol principal del sistema
		rootUserRestriction();

		// Orden de los registros
		$this->db->order_by('activated desc, username asc');

		// Ejecucion de la consulta
		$query = $this->db->get();

		if ($query->num_rows() > 0)
			return $query->result_array();
		else
			return FALSE;
	}

	/**
	 * <b>Method:	update()</b>
	 * 		Actualiza los valores de un registro de usuario
	 * @param		Array $data Valores a actualizar en el registro
	 * 				Integer $id Identificador del usuario
	 * @return		Boolean TRUE en caso de hacer la actualizacion de manera satisfactoria, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:35 PM
	 * */
	function update($data, $id = false) {

		$user_id = $data['id'];
		$this->_format($data, 'UPDATE');
		$this->db->where('id', $user_id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * <b>Method:	delete()</b>
	 * 		Elimina de forma booleana el registro de usuario seleccionado
	 * @param		Integer $record id del registro que se desea eliminar
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:36 PM
	 * */
	function delete($record_id) {

		$this->db->trans_start();
		$data = array('deleted' => '1');
		$this->db->where('id', $record_id);

		if ($this->db->update($this->table, $data)) {
			//Reiniciar la session de este usuario
			$this->db->where("user_data ilike '%\"$record_id\";s:8%'");
			$this->db->delete('rbac.ci_sessions');
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	deactivate()</b>
	 * 		Desactiva el registro de usuario seleccionado
	 * @param		Integer $record id del registro que se desea desactivar
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:36 PM
	 * */
	function deactivate($record_id) {

		$this->db->trans_start();

		$data = array('activated' => '0');
		$this->db->where('id', $record_id);

		if ($this->db->update($this->table, $data)) {
			//Reiniciar la session de este usuario
			$this->db->where("user_data ilike '%\"$record_id\";s:8%'");
			$this->db->delete('rbac.ci_sessions');
		}
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	activate()</b>
	 * 		Activa el registro de usuario seleccionado
	 * @param		Integer $record id del registro que se desea desactivar
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:36 PM
	 * */
	function activate($record_id) {

		$data = array('activated' => '1');
		$this->db->where('id', $record_id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * <b>Method:	_format()</b>
	 * Limpia el arreglo que viene del formulario para que sea compatible con el insert y el update
	 * @param		Array $roles_id
	 * @param		String $type Tipo de formateo, posibles opciones 'INSERT', 'UPDATE'.
	 * @return		Array $roles_id formateado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:45 AM
	 * */
	function _format(&$data, $type = false) {

		// Eliminar botones del formulario
		unset($data['submit']);
		unset($data['reset']);

		// En caso de insert indicar el usuario que crea el registro
		if ($type == 'INSERT') {

			//Formatear data para el Tank Auth
			$extra_params = array('first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'_document' => $data['_document'],
				'phone_number' => $data['phone_number'],
				'created_by' => $this->session->userdata('user_id')
			);
			return $extra_params;
		}

		// En caso de update indicar el usuario que modifica el registro
		if ($type == 'UPDATE') {
			$data['updated_by'] = $this->session->userdata('user_id');
			$data['modified'] = "now()";
			unset($data['id']);
		}

		if (isset($data['observaciones']) && (empty($data['observaciones'])))
			$data['observaciones'] = NULL;
	}

	/**
	 * <b>Method:	associateRole()</b>
	 * 		Asocia el rol seleccionado al usuario seleccionado
	 * @param		integer $user Identificador de usuario
	 * @param		Array $raw_role_array arreglo que posee los roles iniciales del usuario
	 * @param		Array $modified_role_array arreglo que posee los roles finales del usuario
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra
	 * @version		v1.0 10/11/11 07:52 PM
	 * */
	function associateRole($user, $raw_role_array, $modified_role_array) {

		$array_result = $this->buildRoleArrayProcess($raw_role_array, $modified_role_array);

		$this->db->trans_start();

		foreach ($array_result['insert'] AS $array_element)
			$this->db->insert($this->table_role, array('user_id' => $user, 'role_id' => $array_element, 'date_start' => 'now()', 'created_by' => $this->session->userdata('user_id')));

		foreach ($array_result['delete'] AS $array_element) {
			$this->db->where('user_id', $user);
			$this->db->where('role_id', $array_element);
			$this->db->update($this->table_role, array('date_end' => 'now()', 'deleted' => '1', 'updated_by' => $this->session->userdata('user_id'), 'updated_at' => 'now()'));
		}

		//Reiniciar la session de este usuario
		$this->db->where("user_data ilike '%\"$user\";s:8%'");
		$this->db->delete('rbac.ci_sessions');

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	asociarRolWidget()</b>
	 * 		Asocia el rol seleccionado al widget seleccionado
	 * @param		integer $widget Identificador de Widget
	 * @param		Array $raw_role_array arreglo que posee los roles iniciales del Widget
	 * @param		Array $modified_role_array arreglo que posee los roles finales del Widget
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Heiron Contreras
	 * @version		v1.0 18/01/12 02:43 PM
	 * */
	function asociarRolWidget($widget, $raw_role_array, $modified_role_array) {

		$array_result = $this->buildRoleArrayProcess($raw_role_array, $modified_role_array);
		$this->db->trans_start();

		foreach ($array_result['insert'] AS $array_element)
			$this->db->insert($this->widget_role, array('widget_id' => $widget, 'rol_id' => $array_element, 'finicio' => 'now()'));

		foreach ($array_result['delete'] AS $array_element) {
			$this->db->where('widget_id', $widget);
			$this->db->where('rol_id', $array_element);
			$this->db->update($this->widget_role, array('ffin' => 'now()', 'eliminado' => '1'));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	getRolesByUserId()</b>
	 * 	Permite obtener los roles asociados a un usuario
	 * @param	Integer $user_id identificador del usuario en la tabla rbc.users
	 * @return	Array arreglo con los datos asociados a los roles del usuario
	 * @author	Eliel Parra, Reynaldo Rojas
	 * @version	v-1.1 21/11/11 12:00 PM
	 * */
	function getRolesByUserId($user_id) {

		$this->db->select("(CASE activated WHEN 1 THEN 'Sí' WHEN 0 THEN 'No' END) AS activated");
		$this->db->select('ro._name AS _name');
		$this->db->from('rbac.role ro');
		$this->db->join('rbac.user_role ur', 'ur.role_id = ro.id');
		$this->db->where('ur.date_end IS NULL');
		$this->db->where('ur.deleted', '0');
		$this->db->where('ro.deleted', '0');
		$this->db->where('ur.user_id', $user_id);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * <b>Method: getRoleById()</b>
	 * 	Permite obtener la informacion de un rol especifico
	 * @param	Integer $role_id identificador del rol en la tabla rbac.roles
	 * @return	Array $result arreglo con la informacion relacionada al rol
	 * @author	Eliel Parra, Reynaldo Rojas
	 * @version v-1.0 21/11/11 10:49 AM
	 * */
	function getRoleById($role_id) {

		$this->db->where('id', $role_id);
		$this->db->where('deleted', '0');
		$query = $this->db->get('rbac.role');
		return $query->row();
	}

	/**
	 * <b>Method: formatRoleArray()</b>
	 * 	Permite dar formato al arreglo de roles original para poder ser comparado con el arreglo de roles procesado
	 * @param	Array $role_array arreglo con los roles iniciales del usuario
	 * @return	Array $raw_role_array
	 * @author	Reynaldo Rojas
	 * @version v1.0 07/12/11 01:31 PM
	 * */
	function formatRoleArray($role_array) {

		$raw_role_array = array();
		foreach ($role_array AS $elementRoleArray)
			array_push($raw_role_array, $elementRoleArray->value);

		return $raw_role_array;
	}

	/**
	 * <b>Method: buildRoleArrayProcess()</b>
	 * 	Permite establecer los tipos de roles que deben ser insertados y eliminados en un usuario
	 * @param	Array $raw_role_array arreglo con los roles iniciales del usuario
	 * 			Array $modified_role_array arreglo con los roles finales del usuario
	 * @return	Array arreglo asociativo que contiene 2 elementos:
	 * 			Primer elemento (indice "insert"): posee un arreglo con los roles que deben ser insertados
	 *          Segundo elemento (indice "delete"): posee un arreglo con los roles que deben ser eliminados
	 * @author	Reynaldo Rojas
	 * @version v1,0 07/12/11 01:48 PM
	 * */
	function buildRoleArrayProcess($raw_role_array, $modified_role_array) {

		$result_array = array();
		$result_array['insert'] = array_diff($modified_role_array, $raw_role_array);
		$result_array['delete'] = array_diff($raw_role_array, $modified_role_array);
		return $result_array;
	}

	/**
	 * <b>Method: isIdentificationUnique()</b>
	 * Verifica si el numero identificador del usuario es unico
	 * @param	String $_document numero identificador del usuario
	 * 			String $user_id identificador del usuario en caso de edicion
	 * @return	Boolean TRUE en caso de ser unico, de lo contrario retorna FALSE
	 * @author	Reynaldo Rojas
	 * @version	v-1.0 24/05/12 04:43 PM
	 * */
	function isIdentificationUnique($_document, $user_id = FALSE) {

		// Si es en modo edicion
		if (!empty($user_id))
			$this->db->where('id != ', $user_id);

		$this->db->where(array('LOWER(_document)' => strtolower($_document), 'deleted' => '0'));
		$result = $this->db->get($this->table);

		if ($result->num_rows() > 0) {
			$this->form_validation->set_message('isIdentificationUnique', $this->lang->line('identification_unique'));
			return FALSE;
		}
		else
			return TRUE;
	}

	/**
	 * <b>Method:	isPasswordComparison()</b>
	 * 		permite validar que los campos contraseña y repetir contraseña sean iguales
	 * @param		$String $password contraseña seleccionada
	 *              $String $repeated_password contraseña de confirmacion
	 * @return		Boolean TRUE si el campo contraseña y validar contraseña son iguales. Retorna FALSE en caso contrario.
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 16/11/11 06:17 PM
	 * */
	function isPasswordComparison($password, $repeated_password) {

		if ((String) $password != (String) $repeated_password) {
			$this->form_validation->set_message('isPasswordComparison', $this->lang->line('failure_password_comparison'));
			return FALSE;
		}
		else
			return TRUE;
	}

	/**
	 * <b>Method:	getAssociatedRoles()</b>
	 * 	Retorna los roles asociados a un usuario
	 * @param		Array $params arreglo que controla si se deben buscar los roles asociados o no asociados al usuarios
	 * 				$params['0'] = true => Roles asociados
	 * 				$params['0'] = false => Roles no asociados
	 * @return		Array arreglo con el detalle de los roles
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 16/11/11 06:17 PM
	 * */
	function getAssociatedRoles($params) {

		// Definir el operador para buscar los roles asociados o no asociados al usuario
		$operator = 'NOT';
		if (!empty($params['0']))
			unset($operator);

		// Creacion de la consulta
		$query = "SELECT id AS value,
							_name AS label
						FROM rbac.role
						WHERE deleted = '0'
						AND id $operator IN 
									(SELECT role_id 
									FROM rbac.user_role
									WHERE user_id = {$this->session->userdata('user_edit_id')}
									AND deleted = '0'
									AND date_end IS NULL)
						ORDER BY label";

		// Ejecucion de la consulta					
		$result = $this->db->query($query);

		return $result->result();
	}

}

// END user model Class
// End of file user_model.php
// Location modules/user/models/user_model.php
?>
