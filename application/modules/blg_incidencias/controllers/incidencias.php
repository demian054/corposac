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
class incidencias extends MY_Crud {
    
    function __construct() {
        parent::__construct();
    }
    
    public function capture($params) {
        //codigo para la captura desde la consola de elastix
        //cargando elementos del layout
        $this->load->view('home/main_layout.js.php', array('capture' => true));
        
        $getVars = $this->input->get();
        
        //obtener datos:
        
        $call_data = $this->model_class->getDataElastix($getVars['cid']);
        
        $this->load->view('capture/layout.js.php', array(
            'datos' => $getVars,
            'call_data' => $call_data
        ));
        //echo('x');
    }

    public function CL_reload() {
        $getVars = json_encode($this->input->get());
        die('<script type="text/javascript">
        window.parent.newCallEntry('.$getVars.');
        
        </script>');
    }

    /**
     * <b>Method:	create()</b>
     * method		Metodo que permite crear un Contenido
     * @author		Juan Carlos López
     * */
    function create($params) {
        if (!$this->input->post()) {
            //obtenemos los datos de la consola de agente
            $getVars = $this->input->get();
            
            //si no llegan datos de la consola, no se puede crear el ticket
            if ($getVars['phone'] == null) {
                die('window.CU.showAlertError("No hay información de llamada disponible, inicie sesión en la consola de agentes");');
            }
            
            //verificamos si el cid ya generó una incidencia:
            $dataP = $this->model_class->getByField('_name', $getVars['cid']);
            
            if ($dataP) {
                $last_id = $dataP['id'];
                $dataP['category_status_incidencia_id'] = SAC_INCIDENCIA_ABIERTA;
                //$dataP['personas_id'] = 1;
            } else {
            
                //nueva modalidad, guarda el registro de una vez para no perder la traza por la consola de agentes
                $user_id = $this->session->userdata('user_id');
                $newR = array(
                    'category_status_incidencia_id' => SAC_INCIDENCIA_INCOMPLETA,
                    '_name' => $getVars['cid'],
                    'telefono_contacto' => $getVars['phone'],
                    'usuario_id' => $user_id,
                    'created_by' => $user_id,
                );
                $resultado = $this->model_class->create($newR);
                if (!$resultado) {
                    die('<b>Error al crear la incidencia</b>');
                }
                $last_id = $this->db->insert_id();
                $dataP = array(
                    'id' => $last_id,
                    //'personas_id' => 1,
                    '_name' => $getVars['cid'],         //usamos name para guardar el Caller_id
                    'telefono_contacto' => $getVars['phone'],
                    'created_at' => date('Y-m-d h:i:s a'),
                    'usuario_id' => $user_id,
                    'category_status_incidencia_id' => SAC_INCIDENCIA_ABIERTA,
                );
            }
            //die(var_dump($dataP));
            if (isset($getVars['reload'])) {
                $getVars['insertId'] = $last_id;
                $getVars['createAt'] = date('Y-m-d h:i:s a');
                die('<script type="text/javascript">
                    window.parent.newCallEntry(' . json_encode($getVars) . ');
                </script>');
            }
            
            
                    
            $opId = $this->dyna_views->operationData->id;
            $this->load->view('home/main_layout.js.php', array('capture' => true));
            $this->logger->createLog(ACCESS, $opId);
            $params = array('title'     => 'Registrar Incidencia ', 
                            'name'      => 'genericForm', 
                            'replace'   => '', 
                            'data'      => $dataP, 
                            'scriptTags'=> TRUE, 
                            'return_view' => FALSE, 
                            'extraOptions' => array(
                                'autoWidth' => false,
                                'buttonsInBbar' => true,
                                'CancelButton' => false,
                            ),
                            'preBuildFields' => FALSE
                            );            
            $this->dyna_views->buildForm($params);
            
        

            //obtener datos:

            $call_data = $this->model_class->getDataElastix($getVars['cid']);

            $this->load->view('capture/layout.js.php', array(
                'datos' => $getVars,
                'call_data' => $call_data,
                'opId' => $opId
            ));

        } elseif (!empty($params) && $params[0] == 'process') {

            $result = FALSE;
            $data_process = $this->dyna_views->processForm();
            $extra = array();
            if ($data_process['result']) {
                
                if ($data_process['data']['id'] == '') {
                    $resultado = $this->model_class->create($data_process['data']);
//                    $last_id = $this->db->insert_id();
//                    $opIdCreate = 512;
//                    $extra['newView'] = "Ext.getCmp('id_{$opIdCreate}_1975').setValue('$last_id');";
                } else {
                    $resultado = $this->model_class->update($data_process['data']);
                }
                
                if ($resultado) {
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
                'success' => true,
                "extra_vars" => $extra
            );
                       
            throwResponse($params);
        }
    }

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
    function listAll() {
        $opId = $this->dyna_views->operationData->id;
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
            $this->logger->createLog(ACCESS, $opId);
            $params = array(
                'title' => 'Listado de '.humanize($this->entity), 
                'name' => 'contenido',
                'data' => $data,
                'replace' => '',//center
                'scriptTags'=> FALSE, 
                'return_view' => true, //false
                'extraOptions' => FALSE,
                'preBuildFields' => FALSE
            );
            $grid = $this->dyna_views->buildGrid($params);
            
            $estado_list = $this->model_class->getCategoryList('status_incidencia');
            $region_list = $this->model_class->getCategoryList('region');
            
            $panel = $this->load->view('filtro_incidencia.js.php', array(
                    'opId' => $this->dyna_views->operationData->id, 
                    'estado_list'=> $estado_list, 
                    'region_list'=> $region_list,
                    'scriptTags'=> false, 
                    //'pre_filters' => $pre_filters
                ), true);
            //Paneles a ser agregados al panel general.
            $panels2A = array(
                'p1' => $panel,//$this->_getLogForm($form_name),
                'type1' => 'filtro_',//$form_name . ';//',
                'p2' => $grid,
                'type2' => 'Grid_',
                'panelType' => '2AA',
                'heightP1' => '125',
                'heightP2' => .99999,
                'collapsible' => false
            );
            
            $panel_params = array(
                //'title' => $this->dyna_views->operationData->_name,
                'name' => $this->dyna_views->operationData->_name,
                'data' => $panels2A,
                'replace' => 'center',//'Tab_'.$opId,
                'returnView' => FALSE,
                'scriptTags' => false
                //'preBuildFields' => FALSE  
            );
            
            $this->dyna_views->buildPanel($panel_params);
            
            $this->load->view('generals/color_prefilter.js.php', array(
                'opId' => $opId,
                'fieldId' => '1985',
                //'pre_filters' => $pre_filters,
                'scriptTags' => false
            ));
            
        }
    }

   

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
    
    public function CL_getSubtipoReclamo() {
        
        die(json_encode($this->model_class->CL_getSubtipoReclamo($this->input->get('id'))));
    }
}