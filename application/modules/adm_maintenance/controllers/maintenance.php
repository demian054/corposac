<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * maintenance
 * @package		Administracion
 * @subpackage	controllers
 * @author		Mirwing Rosales
 * @copyright	Por definir
 * @license		Por definir
 * @version		v-1.0 31/05/2012 11:03 am
**/

class maintenance extends CI_Controller{

	private $module;

	function __construct() {
		parent::__construct();
		$this->load->model('maintenance_model');
		$this->module = 'administracion';
	}

	public function _remap($method, $params = array()) {
		$this->tank_auth->has_permissions($this->module, get_class(), $method, $params);
	}

	/**
	 * <b>Method: 	create()</b>
	 * @method		Metodo que permite crear una ventana de mantenimiento.
	 * @param		array $param Arreglo con los parametros extras enviados por URL.
	 * @return		void
	 * @author		Mirwing Rosales
	 * @version		v-1.0 31/05/2012 11:08 am
	 */
	public function create ($param) {
		if(!$this->input->post()){
			$this->createLog();

            $title = 'Programar Ventana de Mantenimiento';
			$data = array('message' => $this->lang->line('message_maintenance_window'));
			//$this->dyna_views->buildForm($title,'genericForm','window', $data);
            $this->_dateForm($title, $data);
            
		}elseif(!empty($param) AND $param[0] == 'process'){
			$result = FALSE;
			$data = $this->dyna_views->ProcessForm();
			if($data['result']){
				$msg = $this->maintenance_model->verifyDate($data['data']);
				if(empty($msg)){
                  $extra_vars['newView'] = "w_{$this->dyna_views->operationData->operation_id}.destroy(); for(var i=0; i<Ext.StoreMgr.items.length; i++){	Ext.StoreMgr.items[i].reload();}";
					if($this->maintenance_model->create($data['data'])){
						$result = TRUE;
						$msg = $this->lang->line('message_create_success');
						$this->createLog('Éxito', $data['data']);
					}else{
						$msg = $this->lang->line('message_operation_error');
						$this->createLog('Fracaso', $data['data']);
					}
				}
			} else {
				$msg = $data['msg'];
			}
			throwResponse($this->dyna_views->operationData->name, $result, $msg, $extra_vars);
		}
	}

	/**
	* <b>Method:	listAll()</b>
	* @method		Permite listar toda la programacion de ventanas de mantenimiento.
	* @author		Mirwing Rosales
	* @version		v-1.0 31/05/2012 11:15 am
	* */
	function listAll() {

		$search_field = $this->input->post('searchfield');
		$start = isset($_POST['start']) ? $_POST['start'] : 0;
		$limit = isset($_POST['limit']) ? $_POST['limit'] : $this->config->item('long_limit');

		$data['rowset'] = $this->maintenance_model->getAll($start, $limit,$search_field);
		$data['totalRows'] = $this->maintenance_model->getAll($start, $limit, $search_field, TRUE);

		$this->createLog('Acceso', array('searchfield' => $search_field));

		$extra_options['searchType'] = 'S';

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
			die(json_encode($data));
		$this->dyna_views->buildGrid('Listado de Ventanas de Mantenimiento Programadas', 'el registro', $data, 'center', FALSE, FALSE, $extra_options);
	}

	/**
	 * <b>Method:   detail()</b>
	 * @method      Muestra los datos de la ventana de mantenimiento programada.
	 * @param       array $param Arreglo con los parametros extras enviados por URL.
	 * @author      Mirwing Rosales
	 * @version     v-1.0 31/05/2012 2:59 pm
	 * */
	function detail() {
		$this->createLog('Acceso', array('maintenance_id' => $this->input->get('id')));
		$maintenance_id = $this->input->get('id');
		$data = $this->maintenance_model->getById($maintenance_id);
		$title = 'Detalle de Ventana de Mantenimiento Programada';
		$name = 'Maintenance';
		$replace = 'window';
		$scriptTags = FALSE;
		$extraOptions = array('CancelButton' => 0);
		$this->dyna_views->buildForm($title, $name, $replace, $data, $scriptTags, $extraOptions);
	}

	/**
	 * <b>Method:   edit()</b>
	 * @method      Permite editar la programacion de un ventanas de mantenimiento.
	 * @param       array $param Arreglo con los parametros extras enviados por URL.
	 * @author      Mirwing Rosales
	 * @version     v-1.0 31/05/2012 2:52 pm
	 * */
	function edit($param) {
		if(!$this->input->post()){
			$maintenance_id = $this->input->get('id');

			$this->session->set_userdata('maintenance_id',$maintenance_id);
			$this->createLog('Acceso', array('maintenance_id',$maintenance_id));

			$data = $this->maintenance_model->getById($maintenance_id);

			if($data['finicio_maintenance'] <= date('Y-m-d H:i:s')){
				$type  = 'show';
				$title = 'Operación Invalida' ;
				$result = FALSE;
				$msg = 'No es posible editar la programación de una ventana de mantenimiento debido a que la misma ya fue ejecutada.';
				$icon = 'INFO';
				$this->dyna_views->buildMessageBox($type,$title,$msg,$icon);
			}else{
				$title = 'Editar Ventana de Mantenimiento Programada';
				//$this->dyna_views->buildForm($title,'genericForm','window', $data);
                $this->_dateForm($title, $data);
			}
		}elseif(!empty($param) AND $param[0] == 'process'){
			$result = FALSE;
			$data = $this->dyna_views->ProcessForm();
			if($data['result']){
				$msg = $this->maintenance_model->verifyDate($data['data']);
				if(empty($msg)){
                  $extra_vars['newView'] = "w_{$this->dyna_views->operationData->operation_id}.destroy(); for(var i=0; i<Ext.StoreMgr.items.length; i++){	Ext.StoreMgr.items[i].reload();}";
					if($this->maintenance_model->update($this->session->userdata('maintenance_id'),$data['data'])){
						$result = TRUE;
						$msg = $this->lang->line('message_create_success');
						$this->createLog('Éxito', $data['data'], TRUE);
						$this->session->unset_userdata('maintenance_id');
					}else{
						$msg = $this->lang->line('message_operation_error');
						$this->createLog('Fracaso', $data['data'], TRUE);
					}
				}
			} else {
				$msg = $data['msg'];
			}
			throwResponse($this->dyna_views->operationData->name, $result, $msg, $extra_vars);
		}
	}

	/**
	 * <b>Method:   delete()</b>
	 * @method      Permite eliminar la programacion de un ventanas de mantenimiento.
	 * @param       array $param Arreglo con los parametros extras enviados por URL.
	 * @author      Jose Rodriguez
	 * @version     v-1.0 04/06/2012 10:52 am
	 * */
	function delete() {
		$result = FALSE;

		$maintenance_id = $this->input->post('id');
		$data = $this->maintenance_model->getById($maintenance_id);

		if($data['finicio_maintenance'] <= date('Y-m-d H:i:s')){
			$title = 'Operación Invalida' ;
			$msg = 'No es posible borrar la programación de una ventana de mantenimiento debido a que la misma ya fue ejecutada.';
		}else{
			$title = 'Eliminacion de Ventana Programada';
			if ($this->maintenance_model->delete($maintenance_id, TRUE)) {
				$result = TRUE;
				$msg = $this->lang->line('message_delete_success');
				$this->createLog('Éxito', array('maintenance_id' => $maintenance_id));
			} else {
				$msg = $this->lang->line('message_operation_error');
				$this->createLog('Fracaso', array('maintenance_id' => $maintenance_id));
			}
		}
		throwResponse($title, $result, $msg);
	}

	/**
	* <b>Method:	createLog()</b>
	* @method		Procesa y guarda los log
	* @param		string $action Nombre de la accion
	* @param		array $log arreglo que contiene los valores para almacenar
	* @author		Mirwing Rosales, Jose Rodriguez
	* @version		v1.0 31/05/12 03:28 PM
	*/
	private function createLog($action = 'Acceso', $log = array(), $id = FALSE) {
		if ($id)
			$log['maintenance_id'] = $this->session->userdata('maintenance_id');
		$this->logger->createLog("$action: " . $this->dyna_views->operationData->name, $this->dyna_views->operationData->id, json_encode($log));
	}

    /**
	* <b>Method:	_dateForm()</b>
	* @method		Construye de create y edit para la ventana de mantenimiento.
	* @param		string $title Titulo de la ventana.
	* @param		array $data Datos a ser visualizados dentro de la ventana.
	* @author		Jose Rodriguez
	* @version		v1.0 08/06/12 09:55 AM
	*/
    function _dateForm($title,$data) {
      $dataView['dynaFrom'] = $this->dyna_views->buildForm($title, 'genericForm', 'window', $data, FALSE, FALSE, TRUE);
      $this->load->view('maintenance/form.js.php',$dataView);
    }
}
/* End of file maintenance.php */
/*Location: ./application/modules/administracion/controllers/maintenance.php*/