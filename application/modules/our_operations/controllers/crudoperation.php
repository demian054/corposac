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
class CrudOperation extends MY_Controller implements Controller_interface {

    function __construct() {
        parent::__construct();
        $this->load->model('crudoperation_model');
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
            
            $data = array('visible' => true);
            $data['operation_id'] = $this->input->get('id');
            if ($data['operation_id'] == -1) {
                //$data['operation_id'] = ' ';
                $data['parent_name'] = 'No aplica';
            } else {
                //Buscamos el padre de la Operación para obtener el nombre
                $padre = $this->crudoperation_model->getById($data['operation_id']);               
                $data['parent_name'] = "$padre->_name ($padre->url)";
            }
            
            $this->dyna_views->buildForm(array(
                'title' => 'Crear Operación',
                'name' => 'nombre',
                'data' => $data,
                'replace' => 'window',
            ));
            $this->load->view('our_operations/customCombo.js.php', array(
                'icon_combo' => '39_75'
            ));
        } elseif( !empty($params) && $params[0] == 'process') {
            $result = FALSE;
            $extra = array();
            $this->form_validation->set_message('unique_url', $this->lang->line('m_operation_unique_url'));
            $data_process = $this->dyna_views->processForm();
            if ($data_process['result']) {

                if ($this->crudoperation_model->create($data_process['data'])) {
                    $result = TRUE;
                    if (isset($data_process['data']['operation_id'])) {
                        $extra['newView'] = 'getCenterContent("our_operations/crudoperation/listAll?expand_node=' . $data_process['data']['operation_id'] . '", null, null);';
                    }
                    $msg = $this->lang->line('message_create_success');
                    $this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $data_process['data']); 
                } else {
                    $result = FALSE;
                    $msg = $this->lang->line('message_operation_error');
                    $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, $data_process['data']);
                }
            } else
                $msg = $data_process['msg'];

            throwResponse(array(
                "title" => "Crear Operación",
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
        $id = $this->input->post('id');
        if (! $id) return false;
        $msg = '';
        $extra = array();
        $result = true;
        //validar si tiene hijos:
        if ($this->crudoperation_model->operationHasChildrens($id)){
            $result = FALSE;
            $msg = $this->lang->line('m_operation_has_childrens');
        }
        
        //validar si tiene roles:
        if ($this->crudoperation_model->operationHasRoles($id)){
            $result = FALSE;
            $msg .= $this->lang->line('m_operation_has_roles');
        }
        
        //validar si tiene campos:
        if ($this->crudoperation_model->operationHasFields($id)){
            $result = FALSE;
            $msg .= $this->lang->line('m_operation_has_fields');
        }
        
        
        if ($result) {
            if (!$this->crudoperation_model->delete($id)){
                $result = FALSE;
            } else {
                $msg = $this->lang->line('message_delete_success');
                $extra['newView'] = 'getCenterContent("our_operations/crudoperation/listAll", null, null);';
            }
        }
        if ($result) {
            $this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, array('id' => $id));
        } else {
            $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('id' => $id));
        }
        //$extra['newView'] = 'getCenterContent("our_operations/crudoperation/index?expand_node=' . $padre . '", null, null);';
        throwResponse(array(
            "title" => "Eliminar Operación",
            "result" => $result,
            "msg" => $msg,
            'success' => TRUE,
            "extra_vars" => $extra
        ));
    }
    
    /**
     * <b>Method: deleteAll()</b>
     * Borra las operaciones en cascada
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 04/10/12 09:00 AM
     */
    public function deleteAll() {
        $id = $this->input->post('id');
        if (! $id) return false;
        $msg = '';
        $extra = array();
        $result = true;
        
        
        
        //validar si tiene roles:
        if ($this->crudoperation_model->operationHasRoles($id)){
            $result = FALSE;
            $msg .= $this->lang->line('m_operation_has_roles');
        }
        
        //validar si tiene campos:
        if ($this->crudoperation_model->operationHasFields($id)){
            $result = FALSE;
            $msg .= $this->lang->line('m_operation_has_fields');
        }
        
        //validar operacion en Lote:
        $data = $this->crudoperation_model->getDeleteAllOperations($id);
        if ($data['errors'] != 0) {
            $result = FALSE;
            $msg .= 'No se puede ' . count($data['operation_id']);//$this->lang->line('m_operation_has_childrens');
        }
        
        
        if ($result) {
            if (!$this->crudoperation_model->deleteAll($id, $data['operation_id'])){
                $result = FALSE;
            } else {
                $msg = $this->lang->line('message_delete_success');
                $extra['newView'] = 'getCenterContent("our_operations/crudoperation/listAll", null, null);';
            }
        }
//        if ($result) {
//            $this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, array('id' => $id));
//        } else {
//            $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('id' => $id));
//        }
        //$extra['newView'] = 'getCenterContent("our_operations/crudoperation/index?expand_node=' . $padre . '", null, null);';
        throwResponse(array(
            "title" => "Eliminar Operación en Cascada",
            "result" => $result,
            "msg" => $msg,
            'success' => TRUE,
            "extra_vars" => $extra
        ));
    }

    /**
     * <b>Method: detail()</b>
     * 
     * @author Maycol Alvarez <malvarez@gmail.com>
     * @version	v1.0 11/09/12 09:00 AM
     */
    public function detail() {
        $id = $this->input->get('id');
        $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $id));
        if (! $id) return false;

        $data = $this->crudoperation_model->getById($id, true);

//        $data['category_ext_visual_type_id'] = $data['category_visual_type_id'];
//        unset($data['category_visual_type_id']);

//        if ($data['operation_id'] == -1) {
//            //$data['operation_id'] = ' ';
//            $data['parent_name'] = 'No aplica';
//        } else {
            //Buscamos el padre de la Operación para obtener el nombre
            $padre = $this->crudoperation_model->getById($data['operation_id']);               
            $data['parent_name'] = "$padre->_name ($padre->url)";
//        }

        $this->dyna_views->buildForm(array(
            'title' => 'Detalles de la Operación',
            'name' => 'nombre',
            'data' => $data,
            'replace' => 'window',
            'extraOptions' => array('CancelButton' => '0')
        ));
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
                        
            $data = $this->crudoperation_model->getById($id, true);
            
//            $data['category_ext_visual_type_id'] = $data['category_visual_type_id'];
//            unset($data['category_visual_type_id']);
            
            if ($data['operation_id'] == -1) {
                //$data['operation_id'] = ' ';
                $data['parent_name'] = 'No aplica';
            } else {
                //Buscamos el padre de la Operación para obtener el nombre
                $padre = $this->crudoperation_model->getById($data['operation_id']);               
                $data['parent_name'] = "$padre->_name ($padre->url)";
            }

            $this->dyna_views->buildForm(array(
                'title' => 'Editar Operación',
                'name' => 'nombre',
                'data' => $data,
                'replace' => 'window',
            ));
            $this->load->view('our_operations/customCombo.js.php', array(
                'icon_combo' => '41_75'
            ));
        } elseif( !empty($params) && $params[0] == 'process') {
            $result = FALSE;
            $extra = array();
            $this->form_validation->set_message('unique_url', $this->lang->line('m_operation_unique_url'));
            $data_process = $this->dyna_views->processForm();
            if ($data_process['result']) {
                if ($this->crudoperation_model->update($this->input->post('id'),$data_process['data'])) {
                    $result = TRUE;
                    if (isset($data_process['data']['operation_id'])) {
                        $extra['newView'] = 'getCenterContent("our_operations/crudoperation/listAll?expand_node=' . $data_process['data']['operation_id'] . '", null, null);';
                    }
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
                "title" => "Editar Operación",
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
        $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
        $data = $this->crudoperation_model->getAll('json', $this->input->get('expand_node', null));
        
        $this->dyna_views->buildTreeGrid(array(
            'title' => 't',
            'name' => 'n',
            'replace' => 'center',
            'tree_data' => $data,
            'tree_data_is_json' => true,
            'root_template_ignores_visual_type' => array(
                'Button_A',
            )
        ));        
    }
    
    /**
     *
     * @param type $str
     * @return type 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function unique_url($str) {
        return ! $this->crudoperation_model->urlExists($str, $this->input->post('id'));
    }
    
    /**
     *
     * @param type $param
     * @return boolean 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function move($param) {
        if (! $this->input->post()) {
            
            $id = $this->input->get('id');
            $this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $id));
            $node = $this->crudoperation_model->getById($id);
            
            $data = $this->crudoperation_model->getAll('json', $this->input->get('expand_node', null));

            $this->dyna_views->buildTreeGrid(array(
                'title' => "Mover Operación: $node->_name ($node->url)",
                'name' => 'Nombre',
                'replace' => 'window',
                'tree_data' => $data,
                'tree_data_is_json' => true,
                'root_template_ignores_visual_type' => array(
                    'Button_P',
                ),
                'p_buttons' => array(
                    'our_operations/crudoperation/move/process' => array(
                        'params' => array(
                            'pid' => '{id}',
                            'id' => $id                            
                        ),
                        'method' => 'POST',
                        'confirm' => '¿Desea mover?'
                    )
                )
            ));
        } else {
            $id = $this->input->post('id');
            $pid = $this->input->post('pid');
            if (! $id) return false;
            
            $nodo = $this->crudoperation_model->getById($id);
            //$new_parent = null;
            
            if ($pid == -1) { //root
                $pid = null;
            } //else {
                //$new_parent = $this->crudoperation_model->getById($pid);
            //}
            
            
            $extra = array();
            $result = true;
            
            
            //validar si trata de moverla a si misma:
            if ($id == $pid){
                $result = FALSE;
                $msg = $this->lang->line('m_operation_move_node_in_node');
            }
            
            //validar si trata de moverla a su mismo padre:
            if ($pid == $nodo->operation_id){
                $result = FALSE;
                $msg .= $this->lang->line('m_operation_move_node_in_parent');
            }
            
            //validar si los padres son coincidentes:
            $inception = false;
            $extra_data = $this->crudoperation_model->equalParents($nodo, $pid, $inception);
            if ($extra_data === FALSE){
                $result = FALSE;
                if ($inception) {
                    $msg .= $this->lang->line('m_operation_move_node_inception');
                } else {
                    $msg .= $this->lang->line('m_operation_move_node_equal_parent');
                }
            }
            //validar si tiene hijos:
//            if ($this->crudoperation_model->operationHasChildrens($id)){
//                $result = FALSE;
//                $msg = 'La Operación no puede ser Movida porque tiene hijos\\n';
//            }

            //validar si tiene roles:
//            if ($this->crudoperation_model->operationHasRoles($id)){
//                $result = FALSE;
//                $msg .= 'La Operación no puede ser Movida porque tiene roles activos asociados\\n';
//            }


            if ($result) {
                if (!$this->crudoperation_model->move($id, $pid)){
                    $result = FALSE;
                    $msg .= $this->lang->line('message_operation_error');
                    $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('id' => $id, 'new_parent_id' => $pid));
                } else {
                    $msg = $this->lang->line('message_move_success');
                    $extra['newView'] = 'getCenterContent("our_operations/crudoperation/listAll?expand_node=' . $id . '", null, null);';
                    $this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, array('id' => $id, 'new_parent_id' => $pid));
                }
            } else {
                $this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('id' => $id, 'new_parent_id' => $pid));
            }
            
            throwResponse(array(
                "title" => "Editar Operación",
                "result" => $result,
                "msg" => $msg,
                'success' => TRUE,
                "extra_vars" => $extra
            ));
        }
    }
    
    


}

?>