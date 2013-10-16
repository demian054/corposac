<?php

/**
 * Metadata_model Class
 *
 * @package models
 * @subpackage modules/lib_dyna_views
 * @author  Jose A. Rodriguez E. <josearodrigueze@gmail.com>
 *
 * @version     v1.0 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * DAO de la libreria DynaViews
 *
 * Provee el acceso a las operaciones y sus datos. Asi como a los datos con que seran llanados los store de los combos
 * de categorias (Category) y Tablas particulares de regla de negocio.
 */
class Metadata_model extends MY_Model {

    private $id = NULL;
    private $table_name;
    private $table_operation_role;
    private $table_category;

    public function __construct() {
        parent::__construct();
        $this->table_name = 'rbac.operation';
        $this->table_operation_role = 'rbac.operation_role';
        $this->table_category = 'virtualization.category';
    }

    /**
     * <b>Method:   getId()</b>
     * Descripcion_leve
     * @return      Integer    Identificador de la Operacion en curso.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 24/08/2012
     * */
    public function getId() {
        return $this->id;
    }

    /**
     * <b>Method:   setId($id)</b>
     * Permite actualiar el valor del identificador de la operación en curso.
     * @param       Integer    $id   Identificador de la operacion en curso.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 24/08/2012
     * */
    public function setId($id) {
        if (is_int($id))
            $this->id = $id;
    }

    /**
     * <b>Method:   getFieldsByOperation($operation_id, $parentId = FALSE)</b>
     * Obtiene todos los campos que pertenecen a una operaion dada.
     * @param       Integer    $id   Identificador de la operacion en curso.
     * @param       mixed    $parent_id   Identificador de la operacion en padre (default = FALSE).
     * @return      Array    Arreglo que contiene los campos pertenecientes a la operacion.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 date
     * */
    function getFieldsByOperation($id, $parent_id = FALSE) {
        //Garantizamos que $id sea un entero.
        if (empty($id) || !is_int($id))
            return FALSE;
        else {

            //Si existe paranet id lo sustituimos.
            if (!empty($parent_id))
                $id = $parent_id;

            //Constantes Locales
            $DOUBLE_PIPE = ' || ';
            $SQL_POINT = ' \'.\' ';

            //Contiene la concatenación de un para obtener el campo client_name
            $client_name = NULL;

            //Tables
            $table_operation_field = 'rbac.operation_field';
            $table_field = 'virtualization.field';
            $table_entity = 'virtualization.entity';
            //Category like ext_component
            $ext_component_alias = 'ext_component';
            $table_ext_component = $this->table_category . ' AS ' . $ext_component_alias;
            //Category like data_type
            $data_type_alias = 'data_type';
            $table_data_type = $this->table_category . ' AS ' . $data_type_alias;

            //Select
            //$table_operation_field
            $this->db->select($table_operation_field . '.operation_id');
            //$this->db->select($table_operation_field . '.field_id'); // No lo Necesito
            $this->db->select($ext_component_alias . '._name AS ext_component');
            $this->db->select($table_operation_field . '._label');
            $this->db->select($table_operation_field . '.help');
            $this->db->select($table_operation_field . '.disabled');
            $this->db->select($table_operation_field . '.read_only');
            $this->db->select($table_operation_field . '.hidden');
            $this->db->select($table_operation_field . '._order');
            $this->db->select($table_operation_field . '.validation');
            $this->db->select($table_operation_field . '.child');
            $this->db->select($table_operation_field . '.parent_filter');
            $this->db->select($table_operation_field . '.custom_loader');
            $this->db->select($table_operation_field . '.renderer');
            $this->db->select($table_operation_field . '.alias');
            $this->db->select($table_operation_field . '.visual_width');

            //$table_field
            $client_name =
                $table_entity . '._schema' . $DOUBLE_PIPE . $SQL_POINT . $DOUBLE_PIPE . $table_entity . '._name'
                . $DOUBLE_PIPE . $SQL_POINT . $DOUBLE_PIPE . $table_field . '._name';
            $this->db->select($client_name . ' AS client_name');
            $this->db->select($table_field . '.id');
            $this->db->select($table_field . '.entity_id');
            $this->db->select($table_field . '._name AS server_name');
            $this->db->select($table_field . '._label AS field_label');
            $this->db->select($table_field . '.length');
            $this->db->select($table_field . '.alias_de AS field_alias');
            $this->db->select($data_type_alias . '._name AS data_type');


            //$table_entity
            //$this->db->select($table_field . '.entity_id'); // Ya lo tengo
            $this->db->select($table_entity . '._name AS entity_name');
            $this->db->select($table_entity . '._schema');
            $this->db->select($table_entity . '.alias_de AS entity_alias');

            //From and Inners
            $this->db->from($table_operation_field);
            $this->db->join($table_field, $table_operation_field . '.field_id = ' . $table_field . '.id', 'LEFT');
            $this->db->join($table_entity, $table_field . '.entity_id = ' . $table_entity . '.id');



            $this->db->join($table_ext_component, $table_operation_field . '.category_ext_component_id = ' . $ext_component_alias . '.id');
            $this->db->join($table_data_type, $table_field . '.category_data_type_id = ' . $data_type_alias . '.id');

            //Where
            $this->db->where('operation_id', $id);
            $this->db->where($table_operation_field . '.deleted', '0');
            $this->db->where($table_field . '.deleted', '0');
            $this->db->where($table_entity . '.deleted', '0');

            //Order
            $this->db->order_by($table_operation_field . '._order');

            //Execute Query
            $query = $this->db->get();
            //die('<pre>' . print_r($this->db->last_query(), TRUE) . '</pre>');
            //die('<pre>' . print_r($query, TRUE) . '</pre>');
            return $query->result();
        }
    }

    /**
     * <b>Method:   getOperationData($id)</b>
     * Obtienen los datos pertenecientes a la operacion
     * @param       Interger    $id   Identificador de Operacion
     * @return      Array    Contiene los datos de la operacion.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 date
     * */
    function getOperationData($id) {
        $this->db->from($this->table_name);
        $this->db->where('id', $id);
        $this->db->where('deleted', '0');
        $this->restrictidOuroborosAccess();
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * <b>Method:   getChildrenByOperation($operation_id = FALSE, $visualType = FALSE, $partial = false, $renderOn = "parent")</b>
     * Obtiene los hijos de una operacion a partir de su identificador.
     * @param       Integer   $operation_id  Operacion al q se listaran los hijos.
     * @param       Integer   $visual_type   descripcion1
     * @param       Boolean   $partial       descripcion1
     * @param       Integer   $render_on     descripcion1
     * @return      Array     Arreglo que contiene los hijos de la operacion en curso.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 date
     * */
    function getChildrenByOperation($operation_id = FALSE, $visual_type = FALSE, $partial = FALSE, $render_on = 'parent') {
        //die("opId=$opId, visual_type=$visual_type,render_on=$render_on, partial=$partial");

        $component_type_alias = 'component_type';
        $table_component_type = $this->table_category . ' AS ' . $component_type_alias;

        $operation_id = (empty($operation_id)) ? $this->getId() : $operation_id;

        //Select
        $this->db->select($this->table_name . '.id');
        $this->db->select($this->table_name . '.operation_id');
        $this->db->select($this->table_name . '._name');
        //$this->db->select($this->table_name . '.category_component_type_id');
        $this->db->select($component_type_alias . '._name AS component_type');
        $this->db->select($this->table_name . '.url');
        //$this->db->select($this->table_name . '.category_visual_type_id');
        $this->db->select($this->table_name . '._order');
        $this->db->select($this->table_name . '.visible');
        $this->db->select($this->table_name . '.icon');
        //$this->db->select($this->table_name . '.category_render_on_id');
        $this->db->select($this->table_name . '.business_logic');
        $this->db->select($this->table_name . '.tooltip');
        //$this->db->select($this->table_name . '.ouroboros_admin');
        $this->db->select($this->table_name . '._comments');


        //From and joins
        $this->db->from($this->table_name);
        $this->db->join($this->table_operation_role, $this->table_operation_role . '.operation_id = ' . $this->table_name . '.id');
        $this->db->join($table_component_type, $component_type_alias . '.id = ' . $this->table_name . '.category_component_type_id');

        //Where
        $this->db->where($this->table_name . '.operation_id', $operation_id);
        $this->db->where($this->table_operation_role . '.role_id', $this->session->userdata('role_id'));
        $this->db->where($this->table_name . '.deleted', '0');

        $this->restrictidOuroborosAccess();

        //Select, From, Joins and Where para esta condición.
        if (!empty($visual_type)) {
            //Condicional table definition.
            //Category like visual_type
            $visual_type_alias = 'visual_type';
            $table_visual_type = $this->table_category . ' AS ' . $visual_type_alias;

            //SQL
            //Select
            $this->db->select($visual_type_alias . '._name' . ' AS visual_type');

            //Join
            $this->db->join($table_visual_type, $visual_type_alias . '.id = ' . $this->table_name . '.category_visual_type_id');

            //Where
            if (!empty($partial))
                $this->db->like($visual_type_alias . '._name', $visual_type, 'after');
            else
                $this->db->where($visual_type_alias . '._name', $visual_type);

            //Componente visual es un boton.
            if ($visual_type == "Button") {
                //Condicional table definition.
                //Category like render_on
                $render_on_alias = 'render_on';
                $table_render_on = $this->table_category . ' AS ' . $render_on_alias;
                //Select
                $this->db->select($render_on_alias . '._name AS render_on');

                //From y Join
                $this->db->join($table_render_on, $render_on_alias . '.id = ' . $this->table_name . '.category_render_on_id');

                //Where
                $this->db->where($render_on_alias . '._name', $render_on);
            }
        }

        //order
        $this->db->order_by($this->table_name . '._order');

        //Execute Query.
        $query = $this->db->get();
        //die('<pre>' . print_r($this->db->last_query(), TRUE) . '</pre>');
        //die('<pre>' . print_r($query, TRUE) . '</pre>');
        return $query->result();
    }

    /**
     * <b>Method:   restrictidOuroborosAccess()</b>
     * Agrega una condicion a las consultas sql para que no accedan a las operaciones ouroboros.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 29/08/2012 16:00
     * */
    private function restrictidOuroborosAccess() {
        if (ENVIRONMENT == 'production') {
            $this->db->where($this->table_name . '.ouroboros_admin', '0');
        }
    }

    /**
     * <b>Method:   getCategories($table, $parent_id = FALSE) </b>
     * Obtiene la informacion de la tabla category
     * @param       String    $table    Tabla normalizada dentro de la tabla category.
     * @param       Integer   $parent_id   Identificador de padre del combo. Default FALSE;
     * @return      String    Datos obtenidos de las categorias.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 03/09/2012 12:12:56
     * */
    function getCategories($table, $parent_id = FALSE) {
//function getCategories($field_metadata, $table, $field_name, $parent_id = FALSE) {
        //Select
        $this->db->select($this->table_category . '.id' . ' AS ' . 'value');
        $this->db->select($this->table_category . '._name' . ' AS ' . 'label');
        $this->db->select($this->table_category . '.alternative_value');

        //From
        $this->db->from($this->table_category);

        //Where
        $this->db->where($this->table_category . '.deleted', '0');
        $this->db->where($this->table_category . '._table', $table);

        //Verificamos si tiene un padre
        if (!empty($parent_id)) {
            $table_category_category = 'virtualization.category_category';

            $this->db->join($table_category_category, $table_category_category . '.category_child_id' . '=' . $this->table_category . '.id');

            $this->db->where($table_category_category . '.category_parent_id', $parent_id);
        }

        //Order
        $this->db->order_by($this->table_category . '._order');

        //Execute Query
        $query = $this->db->get();
        //die('<pre>' . print_r($this->db->last_query(), TRUE) . '</pre>');
        //die('<pre>' . print_r($query->result(), TRUE) . '</pre>');
        //return $query->result();
        
        //Implementacion de valores alternativos para categorias.
        $categories = $query->result();
        foreach ($categories as &$category) {
            if (!empty($category->alternative_value))
                $category->value = $category->alternative_value;
            else
                break;
        }

        return $categories;
    }

    /**
     * <b>Method:   getOptionsByTable($table, $filter = array())</b>
     * Obtiene los datos basicos (id, _name) de una tabla de reglas de negocio.
     * @param       String      $table   Nombre de la tAbla de regla de negocio.
     * @param       Array       $filter  Filtro a ser aplicado a la tabla de regla de negocio. Default array().
     * @return      Array       Los datos provenientes de la tabla de regla de negocio.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 04/09/2012
     * */
    function getOptionsByTable($table, $filter = array()) {
//function getOptionsByTable($field_metadata, $parent_id = "") {
        $schema = 'business_logic';
        $table = $schema . '.' . $table;
        $id = 'id'; //Identificador de la tabla.
        $label = '_name'; //Campo de donde se obtiene del combo.
        //Select
        $this->db->select($id . ' AS value');
        $this->db->select($label . ' AS label');

        //From
        $this->db->from($table);

        //Where
        $this->db->where($table . '.deleted', '0');
        //Si posee filtros los aplicamos a la consulta.
        if (!empty($filter))
            $this->db->where($table . '.' . $filter['column'], $filter['value']);

        //Order
        $this->db->order_by($label);

        //Execute Query
        $query = $this->db->get();
        //die ('<pre>'.print_r($this->db->last_query(),TRUE).'</pre>');
        //die('<pre>'.print_r($query->result(),TRUE).'</pre>');
        return $query->result();
    }
    
    function getVtypeValidations(){
        $this->db->select('alternative_value');
        $this->db->where('_table', 'validation_vtype');
        $this->db->from('virtualization.category');
        $query = $this->db->get();
        foreach($query->result() as $row){
            $validation_vtype[] = $row->alternative_value;
        }
        return $validation_vtype;
    }

}

/* END Class Operation      */
/* END of file operation.php */
/* Location: ./application/lib_dyna_views/models/operation.php */