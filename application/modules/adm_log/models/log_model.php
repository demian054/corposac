<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Log_model
 * @package	modules
 * @subpackage	adm_log/models
 * @author	mrosales <mrosales@rialfi.com>,
 *              Jose Rodriguez <josearodrigueze@gmail.com>
 * @copyright	Por definir
 * @license	Por definir
 * @since	17/08/2011
 * */
class Log_model extends MY_Model implements Model_interface {

    private $table_name = '';

    /**
     * <b>Method: __construct()</b>
     * Constructor de la clase.
     * @author mrosales <mrosales@rialfi.com>
     */
    function __construct() {
        parent::__construct();
        $this->table_name = 'rbac.log';
    }

    /**
     * <b>Method: create($data)</b>
     * Permite insertar un registro en la tabla rbac.log.
     * @param array $data Arreglo asociativo que contiene los datos que se van a insertar.
     * @return boolean Si el registro es exitoso retorna el TRUE, en caso contrario retorna FALSE.
     * @author mrosales <mrosales@rialfi.com>
     */
    function create($data) {
        if ($this->db->insert($this->table_name, $data))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * <b>Method: 	getById()</b>
     * Metodo para extraer un objeto relacionado con su identificador (para esta tabla oid).
     * @param		Integer $id Identificador del registro.
     * @return		Array con los valores asociados al identificador.
     * @author		Jose Rodriguez
     * @version		v-1.0 10/09/2012 11:55 am
     */
    public function getById($id) {
        //Tables
        $table_user = 'rbac.users';
        $table_operation = 'rbac.operation';
        $table_category = 'virtualization.category';

        //Select
        $this->db->select($this->table_name . '.oid' . ' AS ' . 'id');
        $this->db->select($table_user . '.username');
        $this->db->select($table_user . '.email');
        $this->db->select($table_operation . '._name' . ' AS ' . ' operation');
        $this->db->select($table_category . '._name' . ' AS ' . ' log_type');
        //$this->db->select($this->table_name . '.operation_name');
        $this->db->select($this->table_name . '.date_action');
        $this->db->select($this->table_name . '.ip');
        $this->db->select($this->table_name . '.user_agent');
        $this->db->select($this->table_name . '.os');
        //$this->db->select($this->table_name.'.geoip');
        $this->db->select($this->table_name . '.mac_address');
        $this->db->select($this->table_name . '.content_after');

        //From and joins
        $this->db->from($this->table_name);
        $this->db->join($table_user, $table_user . '.id = ' . $this->table_name . '.user_id');
        $this->db->join($table_operation, $table_operation . '.id = ' . $this->table_name . '.operation_id');
        $this->db->join($table_category, $table_category . '.id = ' . $this->table_name . '.category_log_type_id');

        //Where
        $this->db->where($this->table_name . '.oid', $id);

        //Execute Query
        $query = $this->db->get();
        //die ('<pre>'.print_r($this->db->last_query(),TRUE).'</pre>');
        //die('<pre>'.print_r($query->result(),TRUE).'</pre>');

        if ($query->num_rows() > 0)
            return $query->row_array();
        return FALSE;
    }

    /**
     * <b>Method:   getAll()</b>
     * Obtiene todas los registros para mostrar en el grid.
     * @param   Array   $params Parametros descritos a continuación.
     *              integer [start] Registro por se inicia la busqueda.
     *              integer [limit] Limite de registros a consultar.
     *              string  [search_field] cadena de texto a buscar.
     *              boolean [count] indica si se quiere contar la cantidad de registros.
     *              boolean [csv] Indica si se quiere toda la data y retorna los datos en un array.
     * @return		array arraglo dque contiene los datos consultados en BD.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 date
     * */
    function getAll($params) {
        extract($params);
        //Tables
        $table_user = 'rbac.users';
        $table_operation = 'rbac.operation';
        $table_category = 'virtualization.category';

        //From and joins
        $this->db->from($this->table_name);
        $this->db->join($table_user, $table_user . '.id = ' . $this->table_name . '.user_id');
        $this->db->join($table_operation, $table_operation . '.id = ' . $this->table_name . '.operation_id');
        $this->db->join($table_category, $table_category . '.id = ' . $this->table_name . '.category_log_type_id');

        //Determinamos si se desea contar el numero de registros.
        //Si se asi solcitamos un count del un solo campo a fin de usar la menor cantidad de recursos
        if (!empty($count)) {
            $this->db->select("COUNT($this->table_name.user_id)", TRUE);
        }

        //Obtenermos los datos necesarios para ser mostrados.
        else {

            //Select
            $this->db->select($this->table_name . '.oid' . ' AS ' . 'id');
            $this->db->select($table_user . '.username');
            $this->db->select($table_user . '.email');
            $this->db->select($table_operation . '._name' . ' AS ' . ' operation');
            $this->db->select($table_category . '._name' . ' AS ' . ' log_type');
            //$this->db->select($this->table_name . '.operation_name');
            $this->db->select($this->table_name . '.date_action');
            $this->db->select($this->table_name . '.ip');
            $this->db->select($this->table_name . '.user_agent');
            $this->db->select($this->table_name . '.os');
            //$this->db->select($this->table_name.'.geoip');
            $this->db->select($this->table_name . '.mac_address');
            $this->db->select($this->table_name . '.content_after');

            //Order
            $this->db->order_by($this->table_name . '.date_action', 'DESC');

            //Limit
            if ($csv !== TRUE)
                $this->db->limit($limit, $start);
        }

        //Evaluamos si existe search_field
        if (!empty($search_field)) {
            foreach ($search_field as $key => $value) {

                //Evaluamos que $value contenga valor
                if (!empty($value))
                    switch ($key) {

                        //Campos de la tabla user
                        case 'first_name':
                        case 'last_name':
                        case 'username':
                        case '_document':
                        case 'email':
                            $conditions = $table_user . '.' . $key . ' ILIKE \'%' . $this->db->escape_like_str($value) . '%\'';
                            $this->db->where($conditions, '', FALSE);
                            break;

                        //Campo de la tabla role
                        case 'role_id':
                            $table_operation_role = 'rbac.operation_role';
                            $table_user_role = 'rbac.user_role';

                            $this->db->join($table_operation_role, $table_operation_role . '.operation_id = ' . $table_operation . '.id');
                            $this->db->join($table_user_role, $table_user_role . '.user_id = ' . $table_operation_role . '.role_id');
                            $this->db->where_in($table_operation_role . '.' . $key, $value);

                            break;

                        //Campos de la tabla log
                        case 'ip':
                        case 'mac':
                            $this->db->where($this->table_name . '.' . $key, $value);
                            break;

                        case 'os':
                        case 'user_agent':

                            //Preparamos los valores del arreglo para ser utilizados por el operador ilike.
                            $ar = explode(',', $value);
                            foreach ($ar as &$condition) {
                                $condition = "'%$condition%'";
                            }

                            //Convertimos el arreglo php en un arreglo pgsql
                            $ar = 'ARRAY[' . implode(',', $ar) . ']';

                            //Preparamos la condicion en formato active record.
                            $conditions = "({$this->table_name}.$key ILIKE ANY($ar))";
                            $this->db->where($conditions, '', FALSE);
                            break;

                        case 'operation_id':
                        case 'category_log_type_id':
                            $this->db->where("{$this->table_name}.$key IN ($value)", '', FALSE);
                            break;

                        //Between
                        case 'start_date':
                            $this->db->where($this->table_name . '.date_action >= ', $value);
                            break;

                        case 'end_date':
                            $this->db->where($this->table_name . '.date_action <= ', $value);
                            break;
                    }
            }
        }



        //Si count retonamos un integer con el total de valores de la consulta.
        if (!empty($count))
            return $this->db->count_all_results();

        //Execute Query
        $query = $this->db->get();

        //die('<pre>' . print_r($this->db->last_query(), TRUE) . '</pre>');
        //die('<pre>'.print_r($query->result(),TRUE).'</pre>');
        if ($csv !== TRUE)
            return $query->result();
        
        return $query->result_array();
    }

    /**
     * <b>Method:   getCategories()</b>
     * Obtiene los datos de categoria segun parametro.
     * @param       String  $_table Nombre de la tabla de categorias a ser consultada.
     * @param       String  $value_has_label    Indica si el valor del label represnta el value. Default FALSE.
     * @return      Array   Resultados de la consulta.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 12/09/2012 16:50:00
     * */
    function getCategories($_table, $value_has_label = FALSE) {
        $table_category = 'virtualization.category';

        //Select
        //Evaluamos que campo representara el value.
        (empty($value_has_label)) ?
                $this->db->select($table_category . '.id AS value') : $this->db->select($table_category . '._name AS value');
        $this->db->select($table_category . '._name AS label');

        //From
        $this->db->from($table_category);

        //Where
        $this->db->where($table_category . '._table', strtolower($_table));
        $this->db->where($table_category . '.deleted', '0');

        //Order
        $this->db->order_by($table_category . '._name');

        //Execute Query
        $query = $this->db->get();
        //die ('<pre>'.print_r($this->db->last_query(),TRUE).'</pre>');
        //die('<pre>'.print_r($query->result_array(),TRUE).'</pre>');
        return $query->result_array();
    }

    /**
     * ***  ESTE METODO NO ES NECESARIO PARA LA OPERACION***
     *
     * Insert los campos en la tabla operation field.
     * en necesario conocer el between de los campos en la tabla field.
     *
     */
    function insertOF() {

        $operation_id = 125;
        $ext_component = 39;
        //$arrayCampos = array();

        $this->db->trans_begin();

        //Select
        $this->db->select('id AS field_id');
        $this->db->select('_label');

        //From
        $this->db->from('virtualization.field');

        //Where
        $this->db->where('id BETWEEN 56 AND 68', '', FALSE);

        $this->db->order_by('id');

        //Execute Query
        $query = $this->db->get();
        $array = $query->result_array();

        foreach ($array as &$value) {
            $value['created_by'] = 1;
            $value['operation_id'] = $operation_id;
            $value['category_ext_component_id'] = $ext_component;
            $value['field_id'] = (int) $value['field_id'];
        }

        $this->db->insert_batch('rbac.operation_field', $array);
        //echo('<pre>' . print_r($this->db->last_query(), TRUE) . '</pre>');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo 'rollback';
        } else {
            $this->db->trans_commit();
            echo 'commit';
        }

//        $this->db->trans_rollback();
    }

    //Start Metodos no implementados de la interface model_interces
    public function _format(&$data, $type = false) {
        
    }

    public function delete($id) {
        
    }

    public function update($id, $data) {
        
    }

    //End Metodos no implementados de la interface model_interces
}

/* END Class Log_model      */
/* END of file log_model.php */
/* Location: ./application/modules/adm_log/models/log_model.php */