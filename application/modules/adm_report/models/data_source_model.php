<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Data_source_model class
 * @package			adm_report
 * @subpackage		models
 * @author			Nohemi Rojas <nohemir@gmail.com>
 * @copyright		Por definir
 * @license			Por definir
 * @version			v1.0 11/10/12 11:43 AM
 * */
class Data_source_model extends MY_Model implements Model_interface {

	private $table = 'reports.data_source';

	public function __construct() {
		parent::__construct();
	}

	function getAll($search_field) {
		
		// Condicion para filtrar de acuerdo al campo de busqueda
		if (!empty($search_field))
			$this->db->where("(_name ILIKE '%$search_field%' OR code ILIKE '%$search_field%')");
		
		$this->db->select("*");
		$this->db->from($this->table);
		$query = $this->db->get();
		return ($query->num_rows() > 0) ? $query->result_array() : FALSE;
	}

	/**
	 * <b>Method: create($data)</b>
	 * Permite crear un registro de fuente de datos
	 * @param 		Array $data datos necesarios para generar el registro
	 * @return      Boolean true en caso de ser satisfactorio. En caso contrario retorna false
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	V-1.0 22/10/12 04:52 PM 
	 * */
	function create($data) {

		$this->_format($data, 'INSERT');

		// Creacion del registro de fuente de datos
		return $this->db->insert($this->table, $data);
	}

	/**
	 * <b>Method: validateSqlQuery()</b>
	 * Permite verificar si la consulta generada es correcta
	 * @param 		Array $sql_params Parametros necesarios para generar la consulta
	 * 				Array $form_data Parametro por referencia para asignar el valor sql al campo
	 * 				Array $pg_error Parametro por referencia para asignar error generado en la consulta
	 * @return      Boolean true en caso de ser satisfactoria. En caso contrario retorna false
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	V-1.0 22/10/12 04:52 PM 
	 * */
	function validateSqlQuery($sql_params, &$form_data, &$pg_error) {

		// Definicion del select
		$query_string.= ' SELECT * ';

		// Definicion del from
		$query_string .= $query_base = " FROM $sql_params->table_pivot ";

		// Definicion de joins
		if (!empty($sql_params->joins)) {
			$arr_joins = json_decode($sql_params->joins);
			$arr_joins = implode($arr_joins, ' ');
			$query_string .= $arr_joins;
			$query_base .= $arr_joins;
		}

		// Definicion del limite de la consulta
		$query_string .= ' LIMIT 1 ';

		// Si la consulta no se ejecuta se debe visualizar el error
		if (!$this->db->query($query_string)) {
			$pg_error = pg_last_error();
			return false;
		} else {
			$form_data->sql = $query_base;
			return true;
		}
	}

    function getById($id) {
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->row_array() : FALSE;
    }

	/**
	 * <b>Method:	update($id, $data)</b>
	 * Define el nombre del metodo que asumira la edición de datos.
	 * @param  		integer $id Identificador del registro
	 * @param  		array $data arreglo con los valores a actualizar
	 * @return 		boolean V/F en caso de exito o fracaso de la actualizacion del registro
	 * @author		
	 * @version		v-1.0 14/08/12 41:21 PM
	 * */
	function update($id, $data) {
		
	}

	/**
	 * <b>Method:	delete($id)</b>
	 * Define el nombre del metodo que asumira la elimicación de datos.
	 * @param 		integer $id Identificador del registro.
	 * @return 		boolean V/F en caso de exito o fracaso de la actualizacion del registro
	 * @author		
	 * @version		v-1.0 14/08/12 41:21 PM
	 * */
	function delete($id) {
		
	}

	/**
	 * <b>Method:	_format($data, $type)</b>
	 * Define el nombre del metodo que Limpiara los datos del formulario para que sea compatible con el insert y el update.
	 * @param		array $data arreglo con data original.
	 * @author		
	 * @version		v-1.0 14/08/12 41:21 PM
	 * */
	function _format(&$data, $type = false) {

		// Convertir el objeto de datos a arreglo
		$data = (array) $data;

		// Eliminar botones del formulario
		unset($data['submit']);
		unset($data['reset']);

		// Formato de datos al registro
		$data['code'] = $data['textfield_codigo'];
		$data['_name'] = $data['textfield_nombre'];
		$data['entity_source'] = $data['itemselector_business_logic'];
		$data['description'] = $data['textarea_observaciones'];

		// Eliminar elementos adicionales del arreglo de datos		
		unset($data['textfield_codigo']);
		unset($data['textfield_nombre']);
		unset($data['itemselector_business_logic']);
		unset($data['textarea_observaciones']);

		// En caso de insert indicar el usuario que crea el registro
		if ($type == 'INSERT')
			$data['created_by'] = $this->session->userdata('user_id');

		// En caso de update indicar el usuario que modifica el registro
		if ($type == 'UPDATE') {
			$data['updated_by'] = $this->session->userdata('user_id');
			$data['updated_at'] = "now()";
			unset($data['id']);
		}

		if (isset($data['description']) && (empty($data['description'])))
			$data['description'] = NULL;
	}

	/**
	 * <b>Method: getTables()</b>
	 * Permite obtener las tablas permitidas para la generacion de reportes (business_logic schema)
	 * @return Arreglo con las tablas
	 * @author Reynaldo Rojas <rrojas@rialfi.com>
	 * @version v-1.0 04/10/12 03:15 PM
	 * */
	function getTables($param) {

		$this->db->select('id AS value');
		$this->db->select('_name AS label');
		$this->db->select('_schema AS schema');
		$this->db->from('virtualization.entity');
		$this->db->where(array('deleted' => '0', '_schema' => 'business_logic'));
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * <b>Method:	getEntitiesColumns($entities)</b>
	 * @method		Obtiene las columnas pertenecientes a las tablas indicadas
	 * @param		String $entities Contiene los ids de las tablas separado por comas
	 * @return		$columns data set de las columnas devueltas por la consulta
	 * @author		Jesús Farías Lacroix
	 * @version		v1.0 23/12/11 12:45 PM
	 * */
	function getEntitiesColumns($entities) {

		$arr_entities = array();
		$arr_entities = explode(',', $entities);

		$str_query = "
				SELECT
						vf.id AS id,
						vf.entity_id,
						vf._name AS field_name,
						ve._schema ||'.'||ve._name ||'.'|| vf._name AS name,
						vf._label AS header,
						vc._name AS data_type,
						false as visible,
						'' AS field_type,
						-- field_type generar equivalencia,
						'' AS sql_function,
						'' AS order_by,
						ve._name as entity_name,
						ve._schema as entity_schema
							
				FROM
						virtualization.entity AS ve
				INNER JOIN  virtualization.field vf ON vf.entity_id = ve.id
				INNER JOIN  virtualization.category vc ON vf.category_data_type_id = vc.id AND vc.deleted = '0'
		
			    WHERE	vc.deleted = '0' 
						AND ve.deleted = '0'
						-- AND ((vf._name NOT LIKE '%_id')
                        AND vf._name != 'id'
						-- AND dc.tipo_campo NOT IN ('fileupload', 'fileuploadplus') <- field_type validacion equivalencia,
						AND ve.id IN($entities)
				ORDER BY 2, 1";
		$query = $this->db->query($str_query);
		return $query->result();
	}

	/**
	 * <b>Method: getRelationType()</b>
	 * Permite obtener las opciones permitidas para relaciones entre tablas
	 * @return Arreglo con los elementos
	 * @author Reynaldo Rojas <rrojas@rialfi.com>
	 * @version v-1.0 18/10/12 04:02 PM
	 * */
	function getRelationType($param) {

		$this->db->select('_name AS value');
		$this->db->select('_name AS label');
		$this->db->from('virtualization.category');
		$this->db->where(array('deleted' => '0', '_table' => 'sql_relation_type'));
		$query = $this->db->get();
		return $query->result_array();
	}
    
    
    /**
	 * <b>Method: getGroupByFunction()</b>
	 * Permite obtener las funciones de agregacion permitidas en sql
	 * @return Arreglo con las funciones permitidas
	 * @author Reynaldo Rojas <rrojas@rialfi.com>
	 * @version v-1.0 04/10/12 03:15 PM
	 * */
	function getGroupByFunction($data_type) {
		
		// Filtro para consultar las funciones de agregacion segun tipo de dato
		if(!empty($data_type)) {
			$this->db->join('virtualization.category_category cc', 'cc.category_child_id = ca.id');
			$this->db->join('virtualization.category ca_parent', "ca_parent.id = cc.category_parent_id AND ca_parent._table = 'data_type' AND ca_parent._name = '$data_type' AND ca_parent.deleted = '0'");
		}
			
		// Consulta de funciones de agregacion
		$this->db->select('ca.alternative_value AS value');
		$this->db->select('ca._name AS label');
		$this->db->from('virtualization.category ca');
		$this->db->where(array('ca.deleted' => '0', 'ca._table' => 'sql_group_by_function'));
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * <b>Method: isCodeUnique()</b>
	 * @method	verifica si el codigo de un reporte es unico
	 * @param	String $code codigo de la fuente de datos
	 * @return	Boolean true en caso de ser unico, en caso contrario retorna false
	 * @author	Reynaldo Rojas
	 * @version v1.0 16/03/12 02:29 PM
	 * */
	function isCodeUnique($code) {
		$query = $this->db->get_where($this->table, array('code' => $code, 'deleted' => '0'));
		if($query->num_rows() > 0)
			return false;
		else
			return true;
	}
}

// END data_source_model Class
// End of file data_source_model.php
// Location modules/reporte/models/data_source_model.php
?>
