<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Template_model class
 * @package			adm_report
 * @subpackage		models
 * @author			Nohemi Rojas <nohemir@gmai.com>
 * @copyright		Por definir
 * @license			Por definir
 * @version			v1.0 11/10/12 11:43 AM
 * */
class Template_model extends MY_Model implements Model_interface {

    private $table = 'reports.template';

    public function __construct() {
        parent::__construct();
    }

    /**
     * <b>Method:	getAll()</b>
     * Metodo que consulta todos los registros de la base de datos 
     * @return 		array registros encontrados
     * @author		
     * @version		v-1.0 14/08/12 41:21 PM
     * */
    function getAll() {
        $this->db->select("_name");
        $this->db->from($this->table);
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : FALSE;
    }

    function create($data) {
        
    }

    function getById($id) {
        
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
        
    }

    /**
     * <b>Method:	buildQuery($select, $from)</b>
     * Metodo que construye el query, agrega JOIN's, from, formatea los campos
     * @param		array $select arreglo con las columnas que se agregaran al query, formateados como schema.table.field
     * @param		string $from cadena que contiene el from y joins necesarios para el query 
     * @return      string cadena que contien el query armado listo para ejecutar
     * @author		Nohemi Rojas
     * @version		v-1.0 22/10/12 02:21 PM
     * */
    function buildQuery($select, $from = '') {

        $join = '';
        $j = 1;
        foreach ($select as $key => $field) {
            $name = explode('.', $field);
            //Caso update_by, created_by
            if ($name[2] == 'updated_by' || $name[2] == 'created_by') {
                $join.= " LEFT JOIN rbac.users AS user$j ON $field = user$j.id";
                //reemplaza el campo antes id para el valor real de la referencia
                $select[$key] = "user$j.username AS user_$name[2]$j";
                //variable $j diferencia los campos iguales
                $j++;
            }
            //caso categoria
            elseif (strstr($name[2], 'category_')) {
                //limpia el campo de las palabras category y id 
                $name_result = preg_replace('/category_|_id/i', '', $name[2]);
                //reemplaza el campo antes id para el valor real de la referencia
                $join.= " LEFT JOIN virtualization.category AS category$j ON $field = category$j.id AND category$j._table = '$name_result'";
                //variable $j diferencia los campos iguales
                $select[$key] = "category$j._name AS $name[2]$j";
                $j++;
            }
        }

        $sql = 'SELECT ' . implode(', ', $select) . ' ' . $from . ' ' . $join;
        return $sql;
    }

    /**
     * <b>Method:	executeQuery($sql, $limit)</b>
     * Metodo que ejecuta el query que se encuentra en $sql con el limite indicado
     * @param		string $sql cadena que contiene el query a ejecutarse
     * @param		integer $limit cantidad de elementos a obtener
     * @return      array Arreglo con resultados de la consulta
     * @author		Nohemi Rojas
     * @version		v-1.0 22/10/12 02:21 PM
     * */
    function executeQuery($sql, $limit = NULL) {
        if ($limit)
            $sql.= " LIMIT $limit";

        $query = $this->db->query($sql);
        //evalua si el query es valido, de lo contrario saca el error de BD, para ello debe estar apagado el debug de BD
        return ($query) ? $query->result() : $this->db->_error_message();
    }

}

// END template_model Class
// End of file template_model.php
// Location modules/reporte/models/template_model.php
?>
