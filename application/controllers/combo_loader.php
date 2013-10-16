<?php

if (!defined('BASEPATH'))
	exit('Acceso Denegado');

class Combo_loader extends CI_controller {

	private $field;
	private $id;
	private $data;

	function __construct() {
		parent::__construct();
		//$this->load->model('combo_loader_model');
		//$dynaViewsOpt['CI']=&$this;
		$this->load->library('dyna_views');		
	}

	function index($params) {
		$data = array();
		$field = json_decode(utf8_encode($this->input->get("field")));
		$id = $this->input->get("id");
		//if(empty($field) || empty($id)) return false;		
		if (empty($field))
			die(json_encode($data));
		$this->id = $id;
		$this->field = $field;
		//limpiar $params[0]
		$data = $this->handleRequestExceptions($params[0]);
		die($data);
	}

	private function handleRequestExceptions($method) {
		//Excepcion cargar estados por ambito politico territorial del CP
		if ($this->field->operation_id == '4' && $this->field->id == '1852')
			$this->id = 'Estado';
		if(empty($this->field->custom_loader) && !empty($method))
			$this->field->custom_loader=$method;
                $this->load->model($this->config->item('dyna_views_model'));
		$data = $this->dyna_views->getStoreData($this->field, $this->id);
		return $data;
	}

	/**
	 * <b>Method: getRoleAsoc() </b>
	 * Permite obtener los roles asociados a un usuario
	 * @return	arreglo con los estandares
	 * @author	Reynaldo Rojas
	 * @version	v1.0 07/12/11 10:52 AM
	 */
	public function getRoleAsoc() {
		//$this->load->model('combo_loader_model');
		$data = $this->combo_loader_model->getUserRoles();
		die(json_encode($data));
	}

	/**
	 * <b>Method: getRoleNoAsoc() </b>
	 * Permite obtener los roles no asociados a un usuario
	 * @return	arreglo con los estandares
	 * @author	Reynaldo Rojas
	 * @version	v1.0 07/12/11 10:52 AM
	 */
	public function getRoleNoAsoc() {
		//$this->load->model('combo_loader_model');
		$data = $this->combo_loader_model->getRoles();
		die(json_encode($data));
	}
	
	/**
	 * <b>Method: getRolesNoAsocWidget() </b>
	 * Permite obtener los roles no asociados a un Widget
	 * @return	arreglo con los estandares
	 * @author	Heiron Contreras
	 * @version	v1.0 18/01/12 02:58 PM
	 */
	public function getRolesNoAsocWidget() {
		//$this->load->model('combo_loader_model');
		$data = $this->combo_loader_model->getRolesNoAsocWidget();
		die(json_encode($data));
	}
	
	/**
	 * <b>Method: getRolesAsocWidget() </b>
	 * Permite obtener los roles asociados a un Widget
	 * @return	arreglo con los estandares
	 * @author	Heiron Contreras
	 * @version	v1.0 18/01/12 02:59 PM
	 */
	public function getRolesAsocWidget() {
		//$this->load->model('combo_loader_model');
		$data = $this->combo_loader_model->getRolesAsocWidget();
		die(json_encode($data));
	}
	
	
	/**
	 * <b>Method:	getCpsWithUbications()</b>
	 * 	Obtiene todos los cuerpos policiales por una lista de ids, 
	 *              y como atributo value obtiene es ubicacion_id
	 * @return		array Arreglo de objetos con los datos de los cuerpos policiales
	 * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
	 * @version		v1.0 03/02/12 11:33 AM
	 * */
	public function getCpsWithUbications() {
		//$this->load->model('combo_loader_model');
		$cpsIds= $this->input->get("cps_ids");
		$data = $this->combo_loader_model->getCpsWithUbications($cpsIds);
		die(json_encode($data));
	}
}
?>