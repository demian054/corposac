<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Template Class
 * @package         adm_report
 * @subpackage      controllers
 * @author          Nohemi Rojas <nohemir@gmai.com>, Reynaldo Rojas<rrojas@rialfi.com>, Jose Rodriguez<jrodriguez@rialfi.com>
 * @copyright       Por definir
 * @license         Por definir
 * @version			v1.0 11/10/12 11:32 AM
 *  * */
class Template extends MY_Controller implements Controller_interface {

    function __construct() {
        parent::__construct();
        $this->load->model('template_model');
        $this->load->model('data_source_model');

        $this->load->library('encrypt');
    }

    /**
     * <b>Method:	index()</b>
     * Metodo por defecto a ser accedido al por el controlador.
     * @param		array $params arreglo que contiene una accion a ejecutarse 
     * @author		
     * @version		v-1.0 11/10/12 04:21 PM
     * */
    function index() {
        
    }

    /**
     * <b>Method:	create()</b>
     * Metodo que crea la plantilla
     * @param		array $params arreglo que contiene una accion a ejecutarse .
     * @author		Nohemi Rojas
     * @version		1.0
     * */
    function create($params) {
        $id = $this->input->get('id');
        $data = $this->data_source_model->getbyId($id);

        $encrypted_sql = $this->encrypt->encode($data['sql']);

        $this->session->set_userdata('datasource', $encrypted_sql);

        $entities = $data['entity_source'];
        $store_columns = $this->data_source_model->getEntitiesColumns($entities);

        $params['store_columns'] = json_encode($store_columns);
        $this->load->view('template/main.js.php', $params);
    }

    /**
     * <b>Method:	listAll()</b>
     * Lista las todas las plantillas
     * @author		Nohemi Rojas
     * @version		1.0
     * */
    function listAll() {

        $data["rowset"] = $this->template_model->getAll();
        $data['totalRows'] = count($data['rowset']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            die(json_encode($data));
        else {
            $extra_options = array('bbarOff' => TRUE, 'searchType' => 'S');
            $grid_params = array('data' => $data, 'replace' => $this->input->get('parentId'), 'extraOptions' => $extra_options, 'scriptTags' => true);
            $this->dyna_views->buildGrid($grid_params);
        }
    }

    /**
     * <b>Method:	edit($params)</b>
     * Define el nombre del metodo que asumira la edición de datos.
     * @param		array $params arreglo que contiene una accion a ejecutarse.
     * @author		
     * @version		
     * */
    function edit($params) {
        
    }

    /**
     * <b>Method:	delete()</b>
     * Define el nombre del metodo que asumira la elimicación de datos.
     * @author		
     * @version		
     * */
    function delete() {
        
    }

    /**
     * <b>Method:	detail()</b>
     * Define el nombre del metodo que proveera detalles de los datos.
     * @author		
     * @version		
     * */
    function detail() {
        
    }

    /**
     * <b>Method:	associate()</b>
     * Metodo que lista los data source para ser seleccionado uno para la plantilla
     * @author		Nohemi Rojas
     * @version		1.0
     * */
    function associate() {

        $search_field = $this->input->post('searchfield');
        $data['totalRows'] = $this->data_source_model->getAll();
        $data['rowset'] = $this->data_source_model->getAll();
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            die(json_encode($data));
        $extra_options = array('searchType' => 'S');
        $grid_params = array('data' => $data, 'replace' => 'window', 'extraOptions' => $extra_options);
        $this->dyna_views->buildGrid($grid_params);
    }

    /**
     * <b>Method:	PL_getExecuteColumns()</b>
     * Metodo que ejecuta la consulta de las columnas seleccionadas
     * @author		Nohemi Rojas, Jose Rodriguez
     * @version		1.0
     * */
    function PL_getExecuteColumns() {
        //se carga dyna_views pq no esta instanciada
        $this->load->library('dyna_views'); //Grid_

        $encrypted_sql = $this->session->userdata('datasource');
        $from = $this->encrypt->decode($encrypted_sql);
        $columns = $this->input->post('columns');
        $columns = json_decode($columns);

        //Variables SQL para generar sql a partir del datasource.
        $arr_columns = array();
        $arr_select = array();
        $j = 1;
        foreach ($columns as $key => $column) {
            $name = explode('.', $column->name);
            $arr_columns[$key]['_label'] = $column->header;
            $arr_columns[$key]['id'] = $column->id;
            $arr_columns[$key]['_order'] = $key;
            $arr_columns[$key]['ext_component'] = 'textfield';
            $arr_columns[$key]['server_name'] = $name[2];

            //Caso update_by, created_by
            if ($name[2] == 'updated_by' || $name[2] == 'created_by') {
                $arr_columns[$key]['server_name'] = "user_$name[2]$j";
                $j++;
            }
            //caso categoria
            if (strstr($name[2], 'category_')) {
                $arr_columns[$key]['server_name'] = "$name[2]$j";
                $j++;
            }

            array_push($arr_select, $column->name);
        }

        $sql = $this->template_model->buildQuery($arr_select, $from);

        $data['rowset'] = $this->template_model->executeQuery($sql, $limit = 10);

        //caso en que la consulta sea exitosa 
        if (is_array($data['rowset'])) {

            $json = json_encode($arr_columns);
            $arr_columns = json_decode($json);

            $grid_params = array('pre_build_fields' => $arr_columns, 'returnView' => true, 'data' => $data);
            $grid = $this->dyna_views->buildGrid($grid_params);

            $arr = $grid;
            die($arr);
        }
        //caso en que retorne un mensaje de error de base de datos por inconsistencia de forma en la estructura 
        else {
            $msg = array();
            $this->dyna_views->buildMessageBox($msg);
            die();
        }
    }

}

// END Template Class
// End of file template.php
// Location modules/adm_report/controllers/template.php
?>
