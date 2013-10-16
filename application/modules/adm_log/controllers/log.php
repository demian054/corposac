<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * maintenance
 * @package	modules
 * @subpackage	adm_log/controllers
 * @author	Jose Rodriguez <josearodrigueze@gmail.com>
 * @copyright	Por definir
 * @license	Por definir
 * @version	v-1.0 10/09/2012 11:03 am
 * */
class Log extends MY_Controller implements Controller_interface {

    //Indica el nombre de la variable de sesion donde se almacena el filtradado de datos.
    private $session_name = '';

    function __construct() {
        parent::__construct();
        $this->load->model('log_model');
        $this->session_name = 'log.search_field';
    }

    /**
     * <b>Method:	listAll()</b>
     * Permite listar toda la programacion de ventanas de mantenimiento.
     * @param   Array   $url_params Arreglo con los parametros de la url. Default FALSE.
     * @author	Jose Rodriguez
     * @version	v-1.0 10/09/2012 11:15 am
     * */
    function listAll($url_params = FALSE) {

        //Inicializamos las variables a ser utilizadas dentro del metodo.
        $search_field = $data = $data_process = array();
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit = isset($_POST['limit']) ? $_POST['limit'] : $this->config->item('long_limit');

        //Preguntamos si peticion proviene del formulario y lo validamos.
        if (!empty($url_params) && $url_params[0] == 'process') {

            //Parametros para validar los datos con proceess form
            $data_process = $this->dyna_views->processForm(array('self' => TRUE));

            //Arreglo de configuraciones para el helper thorwResponse
            $throw_response_params = array('result' => TRUE);

            //Evaluamos el resultado de processFrom
            if (empty($data_process['result'])) {
                $throw_response_params['msg'] = $data_process['msg'];
                $throw_response_params['result'] = FALSE;
                $throw_response_params['operation_name'] = $this->dyna_views->operationData->_name;
            }
        }

        //Obtenemos los valores de la busqueda.
        $data_post = $this->input->post();
        $search_field = (empty($data_post)) ? $data_process : $data_post;

        //Limpiamos los valores vacios del search_field
        $search_field = $this->_cleanNullValues($search_field);

        //Registramos los filtros en session. Esto para el caso que se desee generar un cvs con los filtros de busqueda.
        $this->session->set_userdata($this->session_name, $search_field);

        //Registramos las acciones del usuario dentro del log del sistema.
        //Lo hacemos en este lugar para registar la entrada actual al visulizador de logs.
        $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('searchfield' => $search_field));

        //Preguntamos si peticion proviene del formulario, repetimos este paso para poder registrar la entrada en el log
        //y asi mandar la respuesta de throwResponse
        if (!empty($url_params) && $url_params[0] == 'process') {
            throwResponse($throw_response_params);
        }

        //Arreglos con las configuraciones y parametros de busqueda para obtener los registros.
        $get_all_data_params = array(
            'start' => $start,
            'limit' => $limit,
            'search_field' => $search_field,
        );
        $data['rowset'] = $this->log_model->getAll($get_all_data_params);

        //Arreglo con las configuraciones para obtener la cantidad de valores registrados segun los parametros de busqueda.
        $get_all_count_params = array(
            'count' => TRUE,
            'search_field' => $search_field,
        );
        $data['totalRows'] = $this->log_model->getAll($get_all_count_params);

        //Si la peticion es post es una recarga del grid. y retornamos olo los datos.
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            die(json_encode($data));

        $grid_params = array(
            'returnView' => TRUE,
            'data' => $data
        );
        $grid = $this->dyna_views->buildGrid($grid_params);

        //Indicamos el nombre del formulario dentro de la vista personalizada.
        $form_name = 'avanced_form';

        //Paneles a ser agregados al panel general.
        $panels = array(
            'p1' => $this->_getLogForm($form_name),
            'type1' => $form_name . ';//',
            'p2' => $grid,
            'type2' => 'Grid_',
            'panelType' => '2A',
            'collapsible' => TRUE,
        );

        //Configuraciones y paneles para construir los paneles.
        $panel_params = array(
            'title' => $this->dyna_views->operationData->_name,
            'name' => $this->dyna_views->operationData->_name,
            'data' => $panels,
            'replace' => 'center',
            'returnView' => TRUE
        );

        $view_result = $this->dyna_views->buildPanel($panel_params);

        $data_view = array(
            'result_view' => $view_result,
            'operation_id' => $this->dyna_views->operationData->id
        );
        $this->load->view('log_visualization.js.php', $data_view);
    }

    /**
     * <b>Method:   _jsonFormat()</b>
     * Convierte un array en un formato json y adaptado a las necesidades de la vista particular adm_log/views/avanced_form.js.php
     * @param       Array    $param   Arreglo con la data a ser tranformada.
     * @param       Mixed    $search  Indica que valores del arreglo debe ser reemplazados por [value, label]. Default FALSE.
     * @return      String   Arreglo en Formato JSON.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 13/09/2012 11:08
     * */
    private function _jsonFormat($param, $search = FALSE) {
        //Evaluamos si $param es vacio.
        if (empty($param))
            return FALSE;

        $array = json_encode(array('rowset' => $param));

        //Si se especifica search se sustituiran los valores indicados dentro del arreglo
        //(Obvio los valores de search deben estar dentro del $param)
        if (!empty($search)) {
            $replace = array('value', 'label');
            $array = str_replace($search, $replace, $array);
        }

        return $array;
    }

    /**
     * <b>Method:   CL_filters($param)</b>
     * Realiza el filtro de operacion o roles segun parametros GET.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 02/10/2012
     * */
    function CL_filters() {
        $this->load->model('adm_user_role/role_model');

        $result = NULL;

        $filterBy = $this->input->get('filterBy');
        $filter = $this->input->get('filter');

        //Evaluamos los parametros para determinar el tipo de filtro.
        if (is_numeric($filter))
            $filter = (String) $filter;
        else
            $filter = FALSE;

        switch ($filterBy) {
            case 'roles':

                //Roles Disponibles.
                $result = $this->_jsonFormat($this->role_model->getAll(NULL, $filter), array('id', '_name'));
                break;
            case 'operations':
                $result = $this->role_model->getAllOperations(TRUE, $filter);
                
                //Agregamos al json el parametro result esperado por ExtJs.
//                $result = array(
//                    'tree_store'=> json_decode($result),
//                    'result' => 'true'
//                );
                
                $result = "[$result]";
                break;
            
            default: //die('false');
                break;
        }

        //Imprimimos el los roles obtenidos en formato JSON.
        die($result);
    }

    /**
     * <b>Method:   _getLogForm()</b>
     * Se encarga de obtener la interfaz particular form.js.php perteneciente al modulo de log. Asi mismo, agrega todos los
     * parametros necesarios para el funcionamiento de la misma.
     * @param      String  Indica el nombre del formulario en la vista.
     * @return      String  contiene el resultado de la vista.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 13/09/2012 13:00
     * */
    private function _getLogForm($form_name) {
        //Cargamos role_model para emplear un metodo especifico.
        $this->load->model('adm_user_role/role_model');

        //Roles Disponibles.
        $avalible_roles = $this->role_model->getAll(NULL, '0');
        $search = array('id', '_name');
        $avalible_roles = $this->_jsonFormat($avalible_roles, $search);
        //$operations_store = $this->role_model->getAllOperations(TRUE);

        //Parametros a ser pasados a la vista particular.
        $formParams = array(
            'form_name' => $form_name,
            'log_type_json' => $this->_jsonFormat($this->log_model->getCategories('log_type')),
            'avalible_os_json' => $this->_jsonFormat($this->log_model->getCategories('os', TRUE)),
            'avalible_navigators_json' => $this->_jsonFormat($this->log_model->getCategories('navigator', TRUE)),
            'avalible_roles_json' => $avalible_roles,
            //'operations_json' => $operations_store,
            'operation_id' => $this->dyna_views->operationData->id,
//            'operation_name' => $this->dyna_views->operationData->_name,
//            'operation_parent' => $this->dyna_views->operationData->operation_id
        );

        //Llamado a la vista particular.
        $view = $this->load->view('avanced_form.js.php', $formParams, TRUE);

        //Eliminamos la documentacion de la interfaz particular.
        $view = preg_replace('@/\*(.*)\*/@Us', '', $view);
        //$view = preg_replace('@//(.* )\s@Us', '', $view);
        //die('<pre>' . print_r($view, TRUE) . '</pre>');
        return $view;
    }

    /**
     * <b>Method:   _cleanNullValues($param)</b>
     * Elimina los indices de arreglo con valores nulos para PHP. Ejm NULL,'', 0, '0'.
     * @param       Array    $param   Arreglo de valores
     * @return      Array    Arreglo sin valores nulos.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 18/09/2012 09:54
     * */
    private function _cleanNullValues($param) {
        foreach ($param as $key => $value)
            if (empty($value))
                unset($param[$key]);
        return $param;
    }

    /**
     * <b>Method:   detail()</b>
     * Muestra los datos pertenecientes a una entrada de log expecifica.
     * @author      Jose A Rodriguez E <josearodrigueze@gmail.com>
     * @version     v-1.0 01/10/2012 2:59 pm
     * */
    function detail() {
        $id = $this->input->get('id');
        $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('log_id' => $id));

        //Evaluamos si un identificador valido.
        if (empty($id) AND !is_integer($id))
            return FALSE;

        $data = $this->log_model->getById($id);

        //Parametros ser empleados por la vista.
        $params = array(
            'title' => 'Ver entrada de log.',
            'name' => 'Log',
            'data' => $data,
            'replace' => 'window',
            'scriptTags' => FALSE,
            'extraOptions' => array('CancelButton' => 0),
        );
        $this->dyna_views->buildForm($params);
    }

    /**
     * <b>Method:   exportToCsv()</b>
     * Exporta los resultados del log en formato cvs. Esto a partir de una variable de session actualizada cada vez que ingresamos
     * en el method $this->listAll().
     * @return      String   URL del archivo .cvs.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 02/10/2012 12:00
     * */
    function exportToCsv() {
        $this->load->helper('csv');
        $csv_url = NULL;
        $msg = NULL;

        //Obtenemos los parametros de filtrados del csv.
        $search_field = $this->session->userdata($this->session_name);

        //Registramos las acciones del usuario dentro del log del sistema.
        $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('csvfilter' => $search_field));

        //Parametros para la obtención de los datos.
        $params = array('search_field' => $search_field, 'csv' => TRUE);

        $data = $this->log_model->getAll($params);

        //Validamos Si existe data dentro del log. En la practica siempre deberia haber.
        if (empty($data)) {
            //$msg = 'No existen registros segun los parametros de busqueda establecidos.';
            $msg = $this->lang->line('not_found_result');
        } else {
            $lines = array();

            //Obtenemos las cabeceras de los campos y las colocamos dentro de un arreglo
            array_push($lines, array_keys($data[0]));

            //Recorremos la data y la insertamos en el arreglo que contiene las cabeceras de los campos.
            foreach ($data as $value)
                array_push($lines, $value);

            $csv_name = $this->_getCsvName();
            array_to_csv($lines, $csv_name, ';', $this->config->item('temp_dir_log'));
            $csv_url = $this->config->item('download_csv') . $csv_name;

            $msg = $this->lang->line('csv_message');
            $msg .= anchor($csv_url, $this->lang->line('export_csv'));
            $msg .= $this->lang->line('message_csv_note');
        }

        //Paramentos a ser empleados por la vista.
        $params = array(
            'title' => 'Exportar Log del Sistema.',
            'type' => 'alert',
            'msg' => $msg
        );
        $this->dyna_views->buildMessageBox($params);
    }

    /**
     * <b>Method:   _getCsvName()</b>
     * Devuelve el nombre del archivo csv.
     * @return      String    nombre del archivo csv.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 02/10/2012 17:20
     * */
    function _getCsvName() {
        $this->load->library('encrypt');
        $extension = '.csv';

        //Para mantener la diferencia del usuario quien genera el archivo.
        $difference = $this->encrypt->sha1($this->session->userdata('user_id')); //time();

        $fielname = 'log_' . $difference . $extension;
        return $fielname;
    }

    //No implements Methods
    public function create($params) {
        
    }

    public function delete() {
        
    }

    public function edit($params) {
        
    }

    public function index() {
        
    }

}

/* END Class Log      */
/* END of file log.php */
/* Location: ./application/modules/adm_log/controllers/log.php */