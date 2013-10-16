<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Role_model class
 * @package			user
 * @subpackage		models
 * @author			Eliel Parra <elielparra@gmail.com>, Reynaldo Rojas <reyre8@gmail.com>
 * @copyright		Por definir
 * @license			Por definir
 * @version			v1.0 14/11/11 10:42 AM
 * */
class Role_model extends CI_Model implements Model_interface {

	var $table = 'rbac.role';
	var $table_user = 'rbac.user_role';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * <b>Method:	create()</b>
	 * Permite crear un nuevo rol
	 * @param		Array $data Arreglo con los detalles del nuevo rol
	 * @return		Boolean TRUE en caso de que la insercion se ejecute de forma satisfactoria, FALSE en caso de error
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:46 AM
	 * */
	function create($data) {
		$this->db->trans_start();

		$this->_format($data, 'INSERT');

		// Creacion del rol
		$this->db->insert($this->table, $data);
		$rol_id = $this->db->insert_id();

		// Obtener las operaciones por defecto que debe tener el rol
		$array_default_operations = $this->getDefaultOperations();

		foreach ($array_default_operations AS $key => $element)
			$this->db->insert('rbac.operation_role', array('operation_id' => $element['id'], 'role_id' => $rol_id, 'created_by' => $data['created_by']));

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	getById()</b>
	 * Retorna los datos del rol seleccionado
	 * @param		Integer $record_id Numero identificador del detalle de roles
	 * @return		Array $query->row_array() Arreglo con los detalles del rol seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:46 AM
	 * */
	function getById($record_id, $eliminado = '0') {

		$this->db->where('id', $record_id);
		$this->db->where('deleted', $eliminado);

		// Condicion para evitar mostrar el rol principal del sistema
		rootRoleRestriction();

		$query = $this->db->get($this->table);
		if ($query->num_rows() > 0)
			return $query->row_array();
		else
			return FALSE;
	}

	/**
	 * <b>Method:	getAll()</b>
	 * Retorna todos los registros de roles
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
		$this->db->select("_name");
		$this->db->select("(CASE activated WHEN 1 THEN 'Sí' WHEN 0 THEN 'No' END) AS activated");
		
		// Consulta de roles
		$this->db->from($this->table);

		// Condicion para verificar si se debe filtrar por rol eliminado
		if ($deleted !== FALSE)
			$this->db->where('deleted', $deleted);

		// Condicion para filtrar de acuerdo al campo de busqueda
		if (!empty($search_field))
			$this->db->where("_name ILIKE '%$search_field%'");

		// Orden de los registros
		$this->db->order_by('activated desc, _name asc');		
		
		// Condicion para evitar mostrar el rol principal del sistema
		rootRoleRestriction();

		// Ejecucion de la consulta
		$query = $this->db->get();

		if ($query->num_rows() > 0)
			return $query->result_array();
		else
			return FALSE;
	}

	/**
	 * <b>Method:	update()</b>
	 * Actualiza los valores de un registro de rol
	 * @param		Array $data Valores a actualizar en el registro
	 *				Integer $id Identificador del rol
	 * @return		Boolean TRUE en caso de hacer la actualizacion de manera satisfactoria, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:46 AM
	 * */
	function update($data, $id = false) {

		$role_edit_id = $data['id'];
		$this->_format($data, 'UPDATE');
		$this->db->where('id', $role_edit_id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * <b>Method:	delete()</b>
	 * Elimina de forma booleana el registro de rol seleccionado
	 * @param		Integer $record id del registro que se desea eliminar
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:46 AM
	 * */
	function delete($role_id) {

		$data = array('deleted' => '1');

		// Se eliminan las operaciones asociadas al rol
		$this->db->where('role_id', $role_id);
		$this->db->update('rbac.operation_role', $data);

		// Se elimina el rol
		$this->db->where('id', $role_id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * <b>Method:	deactivate()</b>
	 * Desactiva el registro de rol seleccionado
	 * @param		Integer $record id del registro que se desea desactivar
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:46 AM
	 * */
	function deactivate($record_id) {

		$this->db->trans_start();

		// Si se realiza la desactivacion se deben eliminar los usuarios asociados a el rol eliminado
		// que se encuentran en sesion
		$this->db->where('id', $record_id);
		$this->db->update($this->table, array('activated' => '0'));

		// Consulta de usuarios asociados al rol
		$this->db->where('deleted', '0');
		$this->db->where('role_id', $record_id);

		// Condicion para evitar mostrar el rol principal del sistema
		rootUserRestriction();
		$query = $this->db->get($this->table_user);
		$result = $query->result();

		foreach ($result AS $element) {

			//Reiniciar la session de los usuarios asociados al rol desactivado
			$this->db->where("user_data ilike '%\"$element->user_id\";s:8%'");
			$this->db->delete('rbac.ci_sessions');
		}

		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	activate()</b>
	 * Activa el registro de rol seleccionado
	 * @param		Integer $record id del registro que se desea desactivar
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 10:46 AM
	 * */
	function activate($record_id) {

		$data = array('activated' => '1');
		$this->db->where('id', $record_id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * <b>Method:	asociarOperacion()</b>
	 * Asocia la operacion seleccionada al rol seleccionado
	 * @param		integer $role_id Identificador del rol
	 * @param		array $arr_operation Arreglo que posee las operaciones que van a ser asignadas al rol
	 * @return		Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v2.0 14/12/11 12:39 PM
	 * */
	function associateOperation($role_id, $arr_operation) {

		$this->db->trans_start();

		// Se obtienen las operaciones por defecto que no deben ser borradas del rol (operaciones upload y widget)
		$array_id = array();
		$result = $this->getDefaultOperations();

		// Cadena con las operaciones que no se deben borrar
		foreach ($result AS $key => $element)
			array_push($array_id, $element['id']);
		$not_in_id = implode($array_id, ', ');

		// Se eliminan las operaciones asociadas al rol exceptuando las que no se deben borrar
		$this->db->where("operation_id NOT IN ($not_in_id)");
		$this->db->delete('rbac.operation_role', array('role_id' => $role_id));

		// Se agregar las operaciones seleccionadas al rol
		foreach ($arr_operation AS $op_element)
			$this->db->insert('rbac.operation_role', array('role_id' => $role_id, 'operation_id' => $op_element, 'created_by' => $this->session->userdata('user_id')));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/**
	 * <b>Method:	getChildOperacion()</b>
	 * Funcion recursiva que busca los hijos de la operacion seleccionada
	 * @param		integer $op_id Identificador de la operacion
	 * @return		array $arr_operaciones Arreglo con las operaciones hijos de la operacion seleccionada
	 * @author		Eliel Parra
	 * @version		v1.0 15/11/11 02:49 PM
	 * */
	function getChildOperacion($op_id, $arr_children = array()) {

		$this->db->where('operation_id', $op_id);
		$this->db->where('deleted', '0');
		$query = $this->db->get('rbac.operation');
		$result = $query->result_array();
		if ($result->num_rows() > 0) {
			foreach ($result AS $element)
				$arr_children = $this->getChildOperacion($element, $arr_children);
		}
		array_push($arr_children, $op_id);
		return $arr_children;
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
		if ($type == 'INSERT')
			$data['created_by'] = $this->session->userdata('user_id');

		// En caso de update indicar el usuario que modifica el registro
		if ($type == 'UPDATE') {
			$data['updated_by'] = $this->session->userdata('user_id');
			$data['updated_at'] = "now()";
			unset($data['id']);
		}

		if (isset($data['observaciones']) && (empty($data['observaciones'])))
			$data['observaciones'] = NULL;
	}

    /**
     * <b>Method:  getAllOperations()</b>
     * Devuelve las operaciones existentes en el sistema en funcion de sus parametros.
     * @param   Boolena $not_user_session   Indica la condicion a ser empleada para obtener las operaciones.
     *              TRUE  ->  Se toman todas las operaciones dentro del sistema. Inclusive las eliminadas.
     *    (Default) FALSE ->  Se toman las operaciones pertenecientes al usuario en session. Sin Incluir las eliminadas.
     *          String  $deleted    Indica el tipo de operaciones que deseamos obtener (default '0'). Si se pasa FALSE se obtendran 
     *                              todas las operacviones sin distinción alguna.
     * @return  string  Cadena en formato JSON con las operaciones.
     * @author  Nohemi Rojas, Eliel Parra, Reynaldo Rojas, Jose Rodriguez
     * @version	v1.1 18/09/12 10:43 AM
     */
    public function getAllOperations($not_user_session = FALSE, $deleted = '0') {
        $table_operation = 'rbac.operation';
        $table_operation_role = 'rbac.operation_role';
        $table_category = 'virtualization.category';

        //Select
        $this->db->select($table_operation . '.id');
        $this->db->select($table_operation . '.operation_id');
        $this->db->select($table_operation . '._name AS operation_name');
        $this->db->select($table_operation . '._order');

        $this->db->select($table_category . '._name AS category_name');

        //From and Inners
        $this->db->from($table_operation);
        $this->db->join($table_category, $table_category . '.id = ' . $table_operation . '.category_component_type_id');

        //Where
        $this->db->where($table_operation . '.operation_id IS NULL', '', FALSE);
        $this->db->where($table_category . '._name', 'Module');

        //Evaluamos si se desea buscar por el usuario en sesion.
        if ($not_user_session !== TRUE) {
            
            //Agregamos los select, joins y where necesarios.
            $this->db->select($table_operation_role . '.role_id');
            $session_join = $table_operation_role . '.operation_id = ' . $table_operation . '.id ';
            $session_join .= " AND $table_operation_role.role_id = {$this->session->userdata('role_edit_id')}";
            $this->db->join($table_operation_role, $session_join, 'left');
            
        }
        
        //Evaluamos si debemos aplicar el filtro de borrado.
        if($deleted !== FALSE)
            $this->db->where($table_operation . '.deleted', $deleted);
            
        //Order
        $this->db->order_by($table_operation . '._order', 'ASC');

        //Execute Query
        $query = $this->db->get();
        //die ('<pre>'.print_r($this->db->last_query(),TRUE).'</pre>');
        //die('<pre>'.print_r($query->result(),TRUE).'</pre>');
        $results = $query->result();

        $children = $this->_buildChildrenArray($results, $not_user_session);
        $tree = array('id' => '0', 'text' => 'Operaciones', 'expanded' => true, 'children' => $children);
        return json_encode($tree);
    }

    /**
     * <b>Method:  _buildChildrenArray()</b>
     * Metodo Recursivo que permite construir las operaciones hijas de otras operaciones y las coloca en un arreglo.
     * @param  array $results arreglo con las operaciones
     * @param   Boolena $not_user_session   Indica la condicion a ser empleada para obtener las operaciones.
     *              TRUE  ->  Se toman todas las operaciones dentro del sistema. Inclusive las eliminadas.
     *    (Default) FALSE ->  Se toman las operaciones pertenecientes al usuario en session. Sin Incluir las eliminadas.
     * @return array arreglo con los valores del arbol
     * @author Nohemi Rojas, Jose Rodriguez
     * @version	v1.1  18/09/2012 11:53 AM
     */
    private function _buildChildrenArray($results, $not_user_session = FALSE) {

        //Iteramos las operaciones.
        foreach ($results as $value) {
            //Avaluamos si not_user_session y si es verdadera eliminamos el valor del role.
            //Esto para que no esten check las hojas del arbol.
            if ($not_user_session) {
                $value->role_id = NULL;
            }
            
            $result = $this->getChildrenByFather($value->id, $not_user_session);
            $children = $this->_buildChildrenArray($result, $not_user_session);
            if ($children == NULL)
                $tree[] = array('id' => $value->id,
                    'text' => $value->operation_name,
                    'leaf' => true,
                    'checked' => !empty($value->role_id) ? true : false);
            else
                $tree[] = array('id' => $value->id,
                    'text' => $value->operation_name,
                    'children' => $children,
                    'checked' => !empty($value->role_id) ? true : false);
        }
        return $tree;
    }

    /**
     * <b>Method: getChildrenByFather()</b>
     * Metodo para obtener las operaciones hijas de otra operacion. Al que tiene permiso el usuario en session
     * @param  integer $operation_id Identificador del registro del padre
     * @param   Boolena $not_user_session   Indica la condicion a ser empleada para obtener las operaciones.
     *              TRUE  ->  Se toman todas las operaciones dentro del sistema. Inclusive las eliminadas.
     *    (Default) FALSE ->  Se toman las operaciones pertenecientes al usuario en session. Sin Incluir las eliminadas.
     * @return array Arreglo que contiene los registros de las operaciones hijas.
     * @author Nohemi Rojas, Eliel Parra, Reynaldo Rojas
     * @version	v1.0 07/11/11 07:04 PM
     */
    function getChildrenByFather($operation_id, $not_user_session = FALSE) {
        $table_operation = 'rbac.operation';
        $table_operation_role = 'rbac.operation_role';
        $table_category = 'virtualization.category';

        //Select
        $this->db->select($table_operation . '.id');
        $this->db->select($table_operation . '.operation_id');
        $this->db->select($table_operation . '._name AS operation_name');
        $this->db->select($table_operation . '._order');

        $this->db->select($table_category . '._name AS category_name');

        //From and Inners
        $this->db->from($table_operation);
        $this->db->join($table_category, $table_category . '.id = ' . $table_operation . '.category_visual_type_id');

        //Where
        $this->db->where($table_operation . '.operation_id', $operation_id);

        //Evaluamos si se desea buscar por el usuario en sesion.
        if ($not_user_session !== TRUE) {
            
            //Agregamos los select, joins y where necesarios.
            $this->db->select($table_operation_role . '.role_id');
            $session_join = $table_operation_role . '.operation_id = ' . $table_operation . '.id ';
            $session_join .= " AND $table_operation_role.role_id = {$this->session->userdata('role_edit_id')}";
            $this->db->where($table_operation . '.deleted', '0');
            $this->db->join($table_operation_role, $session_join, 'left');
        }

        //Order
        $this->db->order_by($table_operation . '._order', 'ASC');

        //Execute Query
        $query = $this->db->get();
        //die ('<pre>'.print_r($this->db->last_query(),TRUE).'</pre>');
        //die('<pre>'.print_r($query->result(),TRUE).'</pre>');
        return $query->result();
    }

	/**
	 * <b>Method: getDefaultOperations()</b>
	 * Retorna un arreglo con los identificadores de las operaciones que son obligatorias para todos los tipos
	 * 			de roles (Ejemplo: Widget, Upload)
	 * @return	Array arreglo con los identificadores de las operaciones
	 * @author	Reynaldo Rojas
	 * @version v-1.0 23/05/12 04:38 PM
	 * */
	function getDefaultOperations() {

		// Consulta de operaciones relacionadas a widget y fileupload
		// Estas operaciones no deben ser eliminadas del rol
		//$this->db->where("chk_component_type = 'Widget'");
		$this->db->or_where("url ilike 'adm_upload%'");
		$this->db->select("id");
		$query = $this->db->get('rbac.operation');

		return $query->result_array();
	}

	/**
	 * <b>Method: isRoleNameUnique()</b>
	 * Verifica si el nombre del rol es unico
	 * @param	String $role_name nombre del role
	 * 			String $role_edit_id identificador del rol en caso de edicion
	 * @return	Boolean TRUE en caso de ser unico, de lo contrario retorna FALSE
	 * @author	Reynaldo Rojas
	 * @version	v-1.0 24/05/12 04:43 PM
	 * */
	function isRoleNameUnique($role_name, $role_edit_id = FALSE) {

		// Si es en modo edicion
		if (!empty($role_edit_id))
			$this->db->where('id != ', $role_edit_id);

		$this->db->where(array('LOWER(_name)' => strtolower($role_name), 'deleted' => '0'));
		$result = $this->db->get($this->table);

		if ($result->num_rows() > 0) {
			$this->form_validation->set_message('isRoleNameUnique', 'El campo %s debe ser único.');
			return FALSE;
		}
		else
			return TRUE;
	}

	/**
	 * <b>Method: hasAssociatedUsers()</b>
	 * Verifica si un rol posee usuarios asociados
	 * @param	Integer $role_id identificador del rol
	 * @return	Boolean TRUE en caso que posea usuarios asociados
	 * 			Boolean FALSE en caso contrario
	 * @author	Reynaldo Rojas
	 * @version v-1.0 06/06/12 11:33 AM
	 * */
	function hasAssociatedUsers($role_id) {

		$this->db->join($this->table_user, "$this->table_user.role_id = $this->table.id AND $this->table_user.deleted = '0' AND $this->table.deleted = '0'");
		$result = $this->db->get_where($this->table, array("$this->table.id" => $role_id, "$this->table.deleted" => '0'));
		return ($result->num_rows() > 0);
	}
}

// END user model Class
// End of file role_model.php
// Location modules/user/models/role_model.php
?>
