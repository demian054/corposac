<?php

/**
 * Descriptor_model class
 * @package	        models
 * @author		Eliel Parra <eparra@rialfi.com>, Reynaldo Rojas <rrojas@rialfi.com>
 * @copyright		Por definir
 * @license		Por definir
 * @since		v1.0 05/09/11 11:20 AM
 * */
class Descriptor_model extends CI_Model {

    var $table = 'estatico.descriptores';

    public function __construct() {
        parent::__construct();
    }

    /**
     * <b>Method:getDescriptor($tabla) </b>
     * @method	Metodo para obtener elementos de una pseudo tabla de descriptores
     * @param	String $tabla Nombre de la pseudo tabla
     * @param	Boolean $enable_select TRUE si se desea que agregue 'Seleccione' en la primera posicion, FALSE si se desea solo con 
     * 			los datos
     * @return	$descriptores Arreglo con todos los descriptores de la pseudo tabla.
     * @author	Eliel Parra, Reynaldo Rojas
     */
    public function getDescriptor($table, $enable_select = FALSE) {

        $this->db->select('descriptor, id');
        $this->db->where('tabla', $table);
        $this->db->where('super_tipo IS NOT NULL');
        $query = $this->db->get($this->table);
        if ($query->num_rows() > 0) {
            $descriptores = array();
            if ($enable_select)
                $descriptores[0] = 'Seleccione';
            foreach ($query->result() as $row)
                $descriptores[$row->id] = $row->descriptor;
            return $descriptores;
        }else
            return FALSE;
    }
}

?>
