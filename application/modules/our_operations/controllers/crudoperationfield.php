<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Documentacion class
 * 
 * @package		Operations
 * @subpackage	Administración Ouroboros
 * @author		Maycol Alvarez <malvarez@rialfi.com>
 * @copyright     Copyright (c) RIALFI CONSULTING C.A./DSS
 * @license		Por definir
 * @version		v1.0 10/09/12 11:00 AM
 * */
class CrudOperationField extends MY_Controller implements Controller_interface {

    function __construct() {
        parent::__construct();
        $this->load->model('crudoperationfield_model');
    }

    /**
     * <b>Method: index()</b>
     * 
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 11/09/12 09:00 AM
     */
    public function index() {
        
    }

    /**
     * <b>Method: create()</b>
     * @param type $params 
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 11/09/12 09:00 AM
     */
    public function create($params) {
        if (! $this->input->post()) {
            $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
            $data = array();
            $data['operation_id'] = $this->session->userdata('child_operation_id');
            $this->dyna_views->buildForm(array(
                'title' => 'Crear Campo de la Operación',
                'name' => 'nombrec',
                'data' => $data,
                'replace' => 'window',
            ));
        } elseif( !empty($params) && $params[0] == 'process') {
            $result = TRUE;
            $extra = array();
            //$this->form_validation->set_message('unique_url', $this->lang->line('m_operation_unique_url'));
            $data_process = $this->dyna_views->processForm();
            if ($data_process['result']) {
                
                if ($this->crudoperationfield_model->operationFieldExists(
                    $data_process['data']['operation_id'],
                    $data_process['data']['field_id']
                )) {
                    $result = FALSE;
                    $msg = 'Ya existe el campo en la operación';//$this->lang->line('m_operation_has_childrens');                    
                }
                if ($result) {
                    if ($this->crudoperationfield_model->create($data_process['data'])) {
                        $result = TRUE;
                        $msg = $this->lang->line('message_create_success');
                        $this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $data_process['data']); 
                    } else {
                        $result = FALSE;
                        $msg = $this->lang->line('message_operation_error');
                        $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, $data_process['data']);
                    }
                }
            } else
                $msg = $data_process['msg'];

            throwResponse(array(
                "title" => "Crear Campo de Operación",
                "result" => $result,
                "msg" => $msg,
                'success' => TRUE,
                "extra_vars" => $extra
            ));
        }
    }

    /**
     * <b>Method: delete()</b>
     * 
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 11/09/12 09:00 AM
     */
    public function delete() {
//        $id = $this->input->post('id');
//        if (! $id) return false;
//        $msg = '';
//        $extra = array();
//        $result = true;
//        //validar si tiene hijos:
//        if ($this->crudoperation_model->operationHasChildrens($id)){
//            $result = FALSE;
//            $msg = $this->lang->line('m_operation_has_childrens');
//        }
//        
//        //validar si tiene roles:
//        if ($this->crudoperation_model->operationHasRoles($id)){
//            $result = FALSE;
//            $msg .= $this->lang->line('m_operation_has_roles');
//        }
//        
//        //validar si tiene campos:
//        if ($this->crudoperation_model->operationHasFields($id)){
//            $result = FALSE;
//            $msg .= $this->lang->line('m_operation_has_fields');
//        }
//        
//        
//        if ($result) {
//            if (!$this->crudoperation_model->delete($id)){
//                $result = FALSE;
//            } else {
//                $msg = $this->lang->line('message_delete_success');
//                $extra['newView'] = 'getCenterContent("our_operations/crudoperation/index", null, null);';
//            }
//        }
//        if ($result) {
//            $this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, array('id' => $id));
//        } else {
//            $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('id' => $id));
//        }
//        //$extra['newView'] = 'getCenterContent("our_operations/crudoperation/index?expand_node=' . $padre . '", null, null);';
//        throwResponse(array(
//            "title" => "Eliminar Operación",
//            "result" => $result,
//            "msg" => $msg,
//            'success' => TRUE,
//            "extra_vars" => $extra
//        ));
    }

    /**
     * <b>Method: detail()</b>
     * 
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 11/09/12 09:00 AM
     */
    public function detail() {
//        $id = $this->input->get('id');
//        $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $id));
//        if (! $id) return false;
//
//        $data = $this->crudoperation_model->getById($id, true);
//
////        $data['category_ext_visual_type_id'] = $data['category_visual_type_id'];
////        unset($data['category_visual_type_id']);
//
////        if ($data['operation_id'] == -1) {
////            //$data['operation_id'] = ' ';
////            $data['parent_name'] = 'No aplica';
////        } else {
//            //Buscamos el padre de la Operación para obtener el nombre
//            $padre = $this->crudoperation_model->getById($data['operation_id']);               
//            $data['parent_name'] = "$padre->_name ($padre->url)";
////        }
//
//        $this->dyna_views->buildForm(array(
//            'title' => 'Detalles de la Operación',
//            'name' => 'nombre',
//            'data' => $data,
//            'replace' => 'window',
//            'extraOptions' => array('CancelButton' => '0')
//        ));
    }

    /**
     * <b>Method: edit()</b>
     * 
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 11/09/12 09:00 AM
     */
    public function edit($params) {
        if (! $this->input->post()) {
            $id = $this->input->get('id');
            $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $id));
            if (! $id) return false;
            $id = explode($this->crudoperationfield_model->separator_operator, $id);
            $operation_id = $id[0];
            $field_id = $id[1];
            
            $data = $this->crudoperationfield_model->getById($operation_id, $field_id, true);

            $this->dyna_views->buildForm(array(
                'title' => 'Editar Campo de Operación',
                'name' => 'nombref_e',
                'data' => $data,
                'replace' => 'window',
            ));
        } elseif( !empty($params) && $params[0] == 'process') {
            $result = FALSE;
            $extra = array();
            
            $data_process = $this->dyna_views->processForm();
            if ($data_process['result']) {
                if ($this->crudoperationfield_model->update(
                    $this->input->post('operation_id'),
                    $this->input->post('field_id'),
                    $data_process['data'])
                ) {
                    $result = TRUE;
                    $msg = $this->lang->line('message_update_success');
                    $this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $data_process['data']);
                } else {
                    $result = FALSE;
                    $msg = $this->lang->line('message_operation_error');
                    $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, $data_process['data']);
                }
            } else
                $msg = $data_process['msg'];

            throwResponse(array(
                "title" => "Editar Campo de Operación",
                "result" => $result,
                "msg" => $msg,
                'success' => TRUE,
                "extra_vars" => $extra
            ));
        }
    }

    /**
     * <b>Method: listAll()</b>
     * 
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 11/09/12 09:00 AM
     */
    public function listAll() {
        
        $id = $this->input->get('id');
        
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->session->set_userdata('child_operation_id', $id);
        } else{
            $id = $this->session->userdata('child_operation_id');
        }
        $data = array();
       		// Definicion de los datos mostrados en el grid
		$data["rowset"] =  $this->crudoperationfield_model->getOperationFields($id);
		$data['totalRows'] = count($data['rowset']); 
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            die(json_encode($data));
        
        $this->dyna_views->buildGrid(array(
            'title' => 'Campos de la Operación',
            'name' => 'nombre',
            'data' => $data,
            'replace' => 'window',
        ));
    }
    
    public function CL_getVirtualizationFields() {
        
       $id = $this->input->get('id');
       $data = $this->crudoperationfield_model->CL_getVirtualizationFields($id);
       die(json_encode($data));
    }
    
    public function CL_getVirtualizationEntities() {
        
       $id = $this->input->get('id');
       $data = $this->crudoperationfield_model->CL_getVirtualizationEntities($id);
       die(json_encode($data));
    }

    public function CL_getVirtualizationSchemas() {
        
       $data = $this->crudoperationfield_model->CL_getVirtualizationSchemas($id);
       die(json_encode($data));
    }
}

?>