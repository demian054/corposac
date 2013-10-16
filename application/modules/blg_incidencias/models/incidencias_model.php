<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once(APPPATH.'core/MY_Crud_model'.EXT);

/**
 * @subpackage		models
 * @author		Juan Carlos López, Maycol Alvarez <malvarez@rialfi.com>
 * */
class incidencias_model extends MY_Crud_model {
    
    public function __construct() {
        parent::__construct();
    }

    private function getPDOElastix() {
        $host = 'localhost';
        $user = 'root';
        $pass = 'abc123';
        $dbms = 'mysql';
        $port = 3306;
        $dbname = 'call_center';
        
        $con = new PDO("$dbms:host=$host;port=$port;dbname=$dbname", $user, $pass);
        if (!$con) {
            return false;
        } else {
            return $con;
        }
    }
    
    public function getDataElastix($caller_id) {
        $econ = $this->getPDOElastix();
        
        if (! $econ) {
            return false;
        } else {
            
            $sql = "Select * from call_entry where id = :call_id";
            $sth = $econ->prepare($sql);
            $sth->execute(array(':call_id' => $caller_id));
            $data = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $data[0];
            
        }
        
    }

    /**
     * <b>Method:	create()</b>
     * method	Permite crear un nuevo registro
     * @param	Array $data Arreglo con los datos a insertar
     * @return	Boolean TRUE en caso de que la insercion se ejecute de forma satisfactoria, FALSE en caso de error
     * @author	Juan Carlos López
     * */
    /*function create($data) {
        $this->_format($data, 'INSERT');
        return $this->db->insert($this->table, $data);
    }*/

    /**
     * <b>Method:	getById()</b>
     * method	Retorna los datos asociados a un ID
     * @param	Integer $record_id Numero identificador del detalle de roles
     * @return	Array $query->row_array() Arreglo con los detalles del rol seleccionado
     * @author	Juan Carlos López
     * */
    /*function getById($record_id, $eliminado = '0') {
        $this->db->where('id', $record_id);
        $this->db->where('deleted', $eliminado);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0){
            return $query->row_array();
        } else {
            return FALSE;
        }
    }*/
    
    /**
     * <b>Method:	getByField()</b>
     * method	Retorna los datos asociados a un ID
     * @param	Integer $record_id Numero identificador del detalle de roles
     * @return	Array $query->row_array() Arreglo con los detalles del rol seleccionado
     * @author	Juan Carlos López
     * */
    function getByField($fieldname, $value, $eliminado = '0') {
        $this->db->where($fieldname, $value);
        $this->db->where('deleted', $eliminado);
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0){
            return $query->row_array();
        } else {
            return FALSE;
        }
    }

    /**
     * <b>Method:	getAll()</b>
     * method	Rotorna todos los registros
     * @return	Array $query->result() Arreglo de objetos con los detalles de todos los contenidos del sistema
     * @author	Juan Carlos López, Maycol Alvarez <malvarez@rialfi.com>
     * @param int $limit
     * @param int $start
     * @param int $eliminado
     * @param bool $get_count determina si devuelve el total de registros
     * @return boolean
     */
    function getAll($limit, $start, $eliminado = '0', $get_count = false) {
        $this->db->from($this->table .' t');
        $this->db->where('t.deleted','0');
        
        $concat = " || '-' || ";
        $schema = 'business_logic';
        $cat = 'virtualization.category';
        
        $this->db->join("$schema.personas p", 'p.id = t.personas_id', 'left');
        $this->db->join("$cat e", 'e.id = t.category_status_incidencia_id');
        $this->db->join("$cat tr", 'tr.id = t.category_tipo_reclamo_id');
        $this->db->join("$cat sr", 'sr.id = t.category_subtipo_reclamo_id');
        $this->db->join("$cat r", 'r.id = t.category_region_id');
        $this->db->join("$cat a", 'a.id = t.category_alcance_id');
        $this->db->join("rbac.users ut", 'ut.id = t.usuario_id');
        
                    //filtros:
        $search_data = $this->input->post('search_data', false);
        if ($search_data){
            $search_data = json_decode($search_data, true);
            if (! empty($search_data['id'])) {
                $this->db->where('t.id ', $search_data['id']);
            }
            if (! empty($search_data['n_reclamo'])) {
                $this->db->where('t.n_reclamo ', $search_data['n_reclamo']);
            }
            if (! empty($search_data['fecha_ini'])) {
                $this->db->where('t.created_at >=', $search_data['fecha_ini']);
            }
            if (! empty($search_data['fecha_fin'])) {
                $this->db->where('t.created_at <=', $search_data['fecha_fin']);
            }
            if (! empty($search_data['category_region_id'])) {
                $this->db->where('t.category_region_id', $search_data['category_region_id']);
            }
            if (! empty($search_data['category_status_incidencia_id'])) {
                $this->db->where('t.category_status_incidencia_id', $search_data['category_status_incidencia_id']);
            }
            if (! empty($search_data['cuenta_contrato'])) {
                $this->db->where('p.cuenta_contrato ILIKE ', '%'.addslashes($search_data['cuenta_contrato']) . '%');
            }
            if (! empty($search_data['category_tipo_reclamo_id'])) {
                $this->db->where('t.category_tipo_reclamo_id', $search_data['category_tipo_reclamo_id']);
            }
            if (! empty($search_data['category_subtipo_reclamo_id'])) {
                $this->db->where('t.category_subtipo_reclamo_id', $search_data['category_subtipo_reclamo_id']);
            }
//            if (! empty($search_data['n_cuenta'])) {
//                $this->db->where('p.unidades_ejecutoras_id', $search_data['n_cuenta']);
//            }
        }
        
        

        if (! $get_count) {
            $this->db->select("t.*,COALESCE(p.cuenta_contrato,'') $concat p.cedula_rif $concat p.apellido $concat p._name as persona", false);
            $this->db->select("ut.last_name $concat ut.first_name as teleoperador");
            $this->db->select('e._name as status, tr._name as tipo_reclamo, sr._name as subtipo_reclamo, r._name as region, a._name as alcance');
            

            
            $this->db->limit($limit, $start);            	 
        } else {
            //se asume que toda tabla al menos tiene el ID
            $this->db->select('count(t.id) as c');
            $query = $this->db->get();
            $r = $query->row_array();
            return $r['c'];
        }
        $query = $this->db->get();
        //die($this->db->last_query());
        if ($query->num_rows() > 0){
            return $query->result_array();
        } else {
            return FALSE;
        }
    }

    /**
     * <b>Method:	update()</b>
     * method	Actualiza los valores de un registro
     * @param	Array $data Valores a actualizar en el registro
     * @return	Boolean TRUE en caso de hacer la actualizacion de manera satisfactoria, FALSE en caso contrario
     * @author	Juan Carlos Lopez
     * */
    /*function update($data) {
        $this->_format($data, 'UPDATE');
        $this->db->where('id', $data['id']);
        return $this->db->update($this->table, $data);
    }*/

    /**
     * <b>Method:	delete()</b>
     * method	Elimina de forma booleana el registro seleccionado
     * @param	Integer $record id del registro que se desea eliminar
     * @return	Boolean TRUE en caso de que la eliminacion booleana sea exitosa, FALSE en caso contrario
     * @author	Juan Carlos López
     * */
    /*function delete($record_id) {
        $data = array('deleted' => '1');
        $this->db->where('id', $record_id);
        return $this->db->update($this->table, $data);
    }*/


    /**
     * <b>Method:	_format()</b>
     * method	Limpia el arreglo que viene del formulario para que sea compatible con el insert y el update
     * @param	Array $data
     * @param	String $type Tipo de formateo, posibles opciones 'INSERT', 'UPDATE'. 
     * @return	Array $data formateado
     * @author	Juan Carlos López
     * */
    protected function _format(&$data, $type) {            
        $created_by = $this->session->userdata('user_id');
        unset($data['submit']);
        unset($data['reset']);
        unset($data['created_at']);
        if (isset($data['id']) && ($type == 'INSERT')){
                unset($data['id']);
                $data['created_by'] = $created_by;
        }
        if ($type == 'UPDATE'){
                $data['updated_by'] = $created_by;
        }
        foreach($data as $key => $value){
            if(empty($value) && $value != '0'){
                $data[$key] = null;
            }
        }
    }
    
     public function getCategoryList($_table, $_na = 'No Asignar') {
        $this->db->select("id as value,  _name as label");
        $this->db->where('deleted','0');
        $this->db->where('_table',$_table);
        $this->db->from('virtualization.category');
        $this->db->order_by('_order');

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            $r =  $query->result_array();
            array_unshift($r, array (
                'value' => '0',
                'label' => $_na
            ));
            return ($r);
        } else {
            return FALSE;
        }
    }
    
    public function CL_getSubtipoReclamo($tipo, $_na = 'No Asignar') {
        $this->db->select("c.id as value,  c._name as label");
        $this->db->where('c.deleted','0');
        $this->db->where('c._table', 'subtipo_reclamo');
        $this->db->where('cc.category_parent_id',$tipo);
        $this->db->from('virtualization.category c');
        $this->db->join('virtualization.category_category cc', 'cc.category_child_id = c.id');
        $this->db->order_by('c._order');

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            $r =  $query->result_array();
            array_unshift($r, array (
                'value' => '0',
                'label' => $_na
            ));
            return ($r);
        } else {
            return FALSE;
        }
    }
    
    public function CL_getEstadosIncidencia() {
        $this->db->select("id as value,  _name as label");
        $this->db->where('deleted','0');
        $this->db->where('_table','status_incidencia');
        $this->db->from('virtualization.category');
        $this->db->order_by('_order');

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            $r =  $query->result_array();
            array_unshift($r, array (
                'value' => '0',
                'label' => 'No asignar'
            ));
            return ($r);
        } else {
            return FALSE;
        }
    }
        
}

?>
