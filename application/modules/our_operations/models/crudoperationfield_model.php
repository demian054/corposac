<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Operations_model  class
 * @package		Administracion Ouroboros
 * @subpackage	models
 * @author		Nohemi Rojas, Maycol Alvarez <malvarez@rialfi.com>
 * @copyright     Por definir
 * @license		Por definir
 * @version		v1.0 10/09/12 11:00 AM
 * */
class CrudOperationField_model extends CI_Model  {

    protected $table = 'rbac.operation_field';
    public $separator_operator = '-';
    
	public function __construct() {
		parent::__construct();
	}

	/**
	 * <b>Method:	getById()</b>
	 * Retorna la documentacion de una operacion dado su id
	 * @param		$operacion_id identifiador de la operacion
	 * @return		array arreglo de datos
	 * @author		Eliel Parra
	 * @version		v1.0 24/11/11 07:51 PM
	 * */
	function getById($operacion_id, $field_id, $return_array = false) {
//        if ($selects) {
//            $this->db->select($selects);
//        }
        $this->db->select('of.*, f._name AS _name, e._name AS entity_name, e._schema AS _schema');
		$this->db->where('of.operation_id', $operacion_id);
		$this->db->where('of.field_id', $field_id);
        
        $this->db->from('rbac.operation_field AS of 
                         INNER JOIN virtualization.field AS f ON (of.field_id = f.id) 
                         INNER JOIN virtualization.entity AS e ON (f.entity_id = e.id)');
        
		$query = $this->db->get();
        if ($return_array) {
            $row = $query->row_array();
            foreach ($row as $key => $value) {
                $row[$key] = addslashes($value); //escapado de comillas caso de CRUD operaciones
            }
        } else {
            $row = $query->row();
        }
		return $row;
	}

    
    function getOperationFields($id){
        $this->db->select("(of.operation_id || '$this->separator_operator' || of.field_id) as id, of.*, f._name AS _name, e._name AS entity_name, e._schema AS _schema");
        
        $this->db->from('rbac.operation_field AS of 
                         INNER JOIN virtualization.field AS f ON (of.field_id = f.id) 
                         INNER JOIN virtualization.entity AS e ON (f.entity_id = e.id)');
        $this->db->where('of.operation_id', $id);
        $this->db->where('of.deleted', '0');
        $this->db->order_by('of._order ASC');
		$query = $this->db->get();
        $ret = $query->result();
        //die($this->db->last_query());
		return ($ret);
        
        
    }
    
    function CL_getVirtualizationFields($id){
        $this->db->select('id as value, _name as label');
        $this->db->where('deleted', '0');
        $this->db->where('entity_id', $id);
        //$this->db->order_by('_order ASC');
        
		$query = $this->db->get('virtualization.field');
        
		return ($query->result_array());
    }
    
    public function CL_getVirtualizationEntities($id){
        $this->db->select('id as value, _name as label');
        $this->db->where('deleted', '0');
        if ($id) $this->db->where('_schema', $id);
        //$this->db->order_by('_order ASC');
        
		$query = $this->db->get('virtualization.entity');
        
		return ($query->result_array());
    }
    
    public function CL_getVirtualizationSchemas($id){
        $this->db->select('_schema as value, _schema as label');
        
        //$this->db->order_by('_order ASC');
        $this->db->group_by('_schema');
		$query = $this->db->get('virtualization.entity');
        
        
		return ($query->result_array());
    }
    
    public function CL_getFields($id = null) {
        if ($id == null)
            $id = $this->session->userdata('child_operation_id');
        
        $this->db->select('field_id as value, _label as label');
        $this->db->where('deleted', '0');
        $this->db->where('operation_id', $id);
        
		$query = $this->db->get('rbac.operation_field');
        $ret = $query->result_array();
        $ret[] = array(
            'label' => '- No Aplica -',
            'value' => ''
        );
        return ($ret);
    }
    
    
    /**
     * <b>Method:  _format()</b>
     * @param array $data
     * @param string $type INSERT o UPDATE según la operación
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function _format(&$data, $type) {
        if ($type == 'UPDATE'){
            unset (
                $data['_schema'],
                $data['entity_name'],
                $data['_name'],
                $data['operation_id'],
                $data['field_id']
            );
        } else if ($type == 'INSERT') {
            unset (
                $data['_schema'],
                $data['entity_id']
            );
        }
        
        $this->empty_format($data['help']);
        $this->empty_format($data['_order']);
        $this->empty_format($data['regex']);
        $this->empty_format($data['validation']);
        $this->empty_format($data['child']);
        $this->empty_format($data['parent_filter']);
        $this->empty_format($data['custom_loader']);
        $this->empty_format($data['renderer']);
        $this->empty_format($data['_comments']);
        $this->empty_format($data['alias']);
        $this->empty_format($data['visual_width']);

        $data['disabled'] = ($data['disabled']) ? '1':'0';
        $data['read_only'] = ($data['read_only']) ? '1':'0';
        $data['hidden'] = ($data['hidden']) ? '1':'0';
        $data['created_by'] = $this->session->userdata('user_id');
        //die(var_dump($data));
    }
    
    
    /**
     * <b>Method:  empty_format()</b>
     * Evalua si el parámetro está vacío y lo asigna a null como tal
     * @param array $data 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    private function empty_format(&$data){
        $data = (empty($data)) ? null : $data;
    }
    
    /**
     * <b>Method: update()</b>
     * Actualiza los datos de la Operación
     * @param type $id
     * @param type $data 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function update($operation_id, $field_id, $data) {
		$this->db->trans_start();
        $this->_format($data, 'UPDATE');
        $this->db->where('operation_id', $operation_id);
        $this->db->where('field_id', $field_id);
		$this->db->update($this->table, $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
    }
    
    /**
     * <b>Method:  create()</b>
     * Crea la operación
     * @param type $params 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function create($data) {
        $this->_format($data, 'INSERT');
		return $this->db->insert($this->table, $data);
    }
    
    public function operationFieldExists($operation_id, $field_id) {
        $operation_id = intval($operation_id);
        $field_id = intval($field_id);
        $query = $this->db->query("SELECT count(*) AS total FROM $this->table WHERE operation_id = $operation_id AND field_id = $field_id AND deleted = '0'");
        $row = $query->row();
        return ($row->total != 0);
    }
}

