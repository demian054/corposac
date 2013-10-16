<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH.'core/MY_Crud'.EXT);

/**
 * Contenido Class
 * @package         contenido
 * @subpackage      controllers
 * @author          Juan Carlos López, Maycol Alvarez <malvarez@rialfi.com>
 *  * */
class historial_incidencias extends MY_Crud {
    
    function __construct() {
        parent::__construct();
    }
    
    /**
     * <b>Method:	create()</b>
     * method		Metodo que permite crear un Contenido
     * @author		Juan Carlos López
     * */
    /*function create($params) {
        if (!$this->input->post()) {
            $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
            $params = array('title'     => 'Crear '.humanize($this->entity), 
                            'name'      => 'genericForm', 
                            'replace'   => 'window', 
                            'data'      => '', 
                            'scriptTags'=> FALSE, 
                            'return_view' => FALSE, 
                            'extraOptions' => FALSE,
                            'preBuildFields' => FALSE
                            );            
            $this->dyna_views->buildForm($params);

        } elseif (!empty($params) && $params[0] == 'process') {

            $result = FALSE;
            $data_process = $this->dyna_views->processForm();
            
            if ($data_process['result']) {                
                if ($this->model_class->create($data_process['data'])) {
                    $result = true;
                    $msg = $this->lang->line('message_create_success');
                } else {
                    $result = false;
                    $msg = $this->lang->line('message_operation_error');
                }			
            } else {
                $result = false;
                $msg = $data_process['msg'];    
            }
            $this->logger->createLog(($result ? SUCCESS:FAILURE), $this->dyna_views->operationData->id);	
            $params = array(
                            "title" => 'Crear '.humanize($this->entity), 
                            "result" => $result, 
                            "msg" => $msg,
                            'success' => true
                            );
                       
            throwResponse($params);
        }
    }*/

    /**
     * <b>Method:	detail()</b>
     * method		Muetra los detalles del registro seleccionado
     * @author		Juan Carlos López
     * */
    /*function detail() {
        $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
        $data = $this->model_class->getById($this->input->get('id'));
        $params = array(
            'title' => 'Detalle de '.humanize($this->entity), 
            'name' => 'detalle',
            'data' => $data,
            'replace' => 'window',
            'extraOptions' => array('CancelButton' => '0')
        );
        $this->dyna_views->buildForm($params);
    }*/

    /**
     * <b>Method:	listAll()</b>
     * method		Metodo que perimte listar los registros
     * @author		Juan Carlos López, Maycol Alvarez <malvarez@rialfi.com>
     * */
    /*function listAll() {
        $extra_options['tbarOff'] = FALSE;
        $extra_options['bbarOff'] = FALSE;
        $extra_options['searchType'] = 'S';
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $limit = isset($_POST['limit']) ? $_POST['limit'] : $this->config->item('long_limit');
        $data["rowset"] = $this->model_class->getAll($limit, $start);
        //$data['totalRows'] = count($data['rowset']);
        $data['totalRows'] = $this->model_class->getAll($limit, $start, null, TRUE);
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            die(json_encode($data));
        else {
            $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
            $params = array(
                'title' => 'Listado de '.humanize($this->entity), 
                'name' => 'contenido',
                'data' => $data,
                'replace' => 'center',
                'scriptTags'=> FALSE, 
                'return_view' => FALSE, 
                'extraOptions' => FALSE,
                'preBuildFields' => FALSE
            );
            $this->dyna_views->buildGrid($params);
        }
    }*/

   

    /**
     * <b>Method:	edit()</b>
     * method		Permite editar los valores de un contenido
     * @author		Juan Carlos López
     * */
    /*function edit($params) {
        if (!$this->input->post()) {
            $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
            $data = $this->model_class->getById($this->input->get('id'));            
            $params = array(
                'title' => 'Editar '.humanize($this->entity), 
                'name' => 'contenido',
                'data' => $data,
                'replace' => 'window',
                'extraOptions' => $extra_options
            );
            $this->dyna_views->buildForm($params);            
        } elseif (!empty($params) && $params[0] == 'process') {
            $result = FALSE;
            $data_process = $this->dyna_views->processForm();
            
            if ($data_process['result']) {
                if ($this->model_class->update($data_process['data'])) {
                    $result = TRUE;
                    $msg = $this->lang->line('message_create_success');
                } else {
                    $result = FALSE;
                    $msg = $this->lang->line('message_operation_error');
                }
            } else {
                $result = false;
                $msg = $data_process['msg'];    
            }
            $this->logger->createLog(($result ? SUCCESS:FAILURE), $this->dyna_views->operationData->id);
            $params = array(
                            "title" => 'Editar '.humanize($this->entity), 
                            "result" => $result, 
                            "msg" => $msg, 
                            'success' => TRUE
                            );
            throwResponse($params);
        }
    }*/

    /**
     * <b>Method:	delete()</b>
     * method		Elimina de forma booleana un registro seleccionado
     * @author		Juan Carlos López
     * */
    /*function delete() {
        if ($this->model_class->delete($this->input->post('id'))) {
            $result = TRUE;
            $msg = $this->lang->line('message_delete_success');
        } else {
            $result = FALSE;
            $msg = $this->lang->line('message_operation_error');
        }
        $this->logger->createLog(($result ? SUCCESS:FAILURE), $this->dyna_views->operationData->id);
        $params = array(
                        "title" => 'Eliminar '.humanize($this->entity), 
                        "result" => $result, 
                        "msg" => $msg,
                        'success' => TRUE
                        );
        throwResponse($params);
    }*/
}