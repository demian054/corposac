<?php

if (!defined('BASEPATH'))
	exit('Acceso Denegado');

/**
 * operations_model
 * @package	SIETPOL
 * @subpackage	model
 * @author	mrosales <mrosales@rialfi.com>
 * @copyright	Por definir
 * @license	Por definir
 * @since	15/08/2011
 * */
class Operations extends CI_Model {

	private $table_name = 'operation';
	private $permission_table_name = 'operation_role';
	private $category_table_name = 'virtualization.category';

	function __construct() {
		parent::__construct();
		$ci = & get_instance();
		$this->table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->table_name;
		$this->permission_table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->permission_table_name;
	}

	function createOperation($data) {
		if ($this->db->insert($this->table_name, $data))
			return $this->db->insert_id();
		else
			return FALSE;
	}

	function createPermissions($data) {
		if ($this->db->batch_insert($this->permission_table_name, $data))
			return TRUE;
		else
			return FALSE;
	}

	function getAllOperationsByRole($role_id='') {
		if (!empty($role_id)) {
			//Area Select
			$this->db->select($this->table_name . '.id', FALSE);
			$this->db->select($this->table_name . '._name', FALSE);
			$this->db->select($this->category_table_name . '._name AS category_component_type', FALSE);
			$this->db->select($this->table_name . '.operation_id', FALSE);
			$this->db->select($this->table_name . '.url', FALSE);
			$this->db->select('category2._name AS category_visual_type', FALSE);
			$this->db->select($this->table_name . '._order', FALSE);
			$this->db->select($this->table_name . '.visible', FALSE);
			$this->db->select($this->table_name . '.tooltip', FALSE);
			$this->db->select($this->table_name . '.icon', FALSE);

			//Area From e Inners
			$this->db->from($this->table_name, FALSE);
			$this->db->join($this->permission_table_name, $this->table_name . '.id = ' . $this->permission_table_name . '.operation_id ' . ' AND ' . $this->table_name . '.deleted = \'0\''
			);
			$this->db->join($this->category_table_name, $this->table_name . '.category_component_type_id =' . $this->category_table_name . '.id' .
					' AND ' . $this->category_table_name . '.deleted=\'0\''
			);
			$this->db->join($this->category_table_name . ' AS category2', $this->table_name . '.category_visual_type_id =' . 'category2.id AND category2.deleted = \'0\''
			);
			//Area Where
			$this->db->where($this->permission_table_name . '.role_id', $role_id, FALSE);

			if (ENVIRONMENT == 'production') {
				$this->db->where($this->table_name . '.ouroboros_admin', '0');
			}

			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			} else
				return FALSE;
		} else
			return FALSE;
	}

	function getAllPermissionsByRole($role_id) {
		if (!empty($role_id)) {
			$this->db->select($this->table_name . '.url', FALSE);
			$this->db->select($this->table_name . '._name', FALSE);
			$this->db->select($this->table_name . '.id', FALSE);
			$this->db->from($this->table_name, FALSE);
			$this->db->from($this->permission_table_name, FALSE);
			$this->db->where($this->table_name . '.id', $this->permission_table_name . '.operation_id', FALSE);
			$this->db->where($this->permission_table_name . '.role_id', $role_id, FALSE);
			//$this->db->where($this->table_name.'.type','\'CM\'',FALSE);
			$this->db->where($this->table_name . '.deleted', ' \'0\'', FALSE);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				return $query->result();
			} else
				return FALSE;
		} else
			return FALSE;
	}

}

/* End of file operations_model.php */
/*Location: ./ruta/operations_model.php*/