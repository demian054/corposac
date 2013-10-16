<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once(APPPATH.'core/MY_Crud_model'.EXT);

/**
 * @subpackage		models
 * @author		Juan Carlos López, Maycol Alvarez <malvarez@rialfi.com>
 * */
class historial_incidencias_model extends MY_Crud_model {
    
    public function __construct() {
        parent::__construct();
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
    /*function getAll($limit, $start, $eliminado = '0', $get_count = false) {
        $this->db->from($this->table);
        $this->db->where('deleted','0');

        if (! $get_count) {
            $this->db->limit($limit, $start);            	 
        } else {
            //se asume que toda tabla al menos tiene el ID
            $this->db->select('count(id) as c');
            $query = $this->db->get();
            $r = $query->row_array();
            return $r['c'];
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return $query->result_array();
        } else {
            return FALSE;
        }
    }*/

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
    /*protected function _format(&$data, $type) {            
        $created_by = $this->session->userdata('user_id');
        unset($data['submit']);
        unset($data['reset']);
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
    }*/
        
}

?>
