<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');

/**
 * roles_model
 * @package	SIETPOL
 * @subpackage	model
 * @author	mrosales <mrosales@rialfi.com>
 * @copyright	Por definir
 * @license	Por definir
 * @since	V-1.0 12/08/2011
 * */
class Roles extends CI_Model {

    private $table_name = 'role';
    private $rol_user_table_name = 'user_role';

    function __construct() {
        parent::__construct();
        $ci = & get_instance();
        $this->table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->table_name;
        $this->rol_user_table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->rol_user_table_name;
    }

    /**
     * <b>Method: createRole($data)</b>
     *
     * @method Metodo para insertar un registro en la BD
     * @param array $data Arreglo con todos los datos a insertar en el registro
     *
     */
    function createRole($data) {
        if ($this->db->insert($this->table_name, $data)) {
            return $this->db->insert_id();
        } else
            return FALSE;
    }

    function getRole($id = '') {
        if (!empty($id))
            $query = $this->db->get_where($this->table_name, array($this->table_name . '.id' => $id, 'deleted' => '0', 'activated' => 1));
        else
            $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 1)
            return $query->result();
        else if ($query->num_rows() === 1)
            return $query->row();
        else
            return FALSE;
    }

    function getRolesByUser($user_id) {
		
        $this->db->select($this->table_name . '.id');
        $this->db->select($this->table_name . '._name');
        $this->db->from($this->table_name);
        $this->db->join($this->rol_user_table_name, $this->rol_user_table_name . '.role_id = ' . $this->table_name . '.id');
        $this->db->where($this->rol_user_table_name . '.user_id = ' . $user_id);
        $this->db->where($this->rol_user_table_name . '.deleted', '0');
        $this->db->where($this->table_name . '.deleted', '0');
        $this->db->where($this->table_name . '.activated', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
            return $query->result();
        return FALSE;
    }

}

/* End of file roles_model.php */
/*Location: ./application/helpers/roles_model.php*/