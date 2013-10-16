<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Data_source Class
 * @package         adm_report
 * @subpackage      controllers
 * @author          Nohemi Rojas <nohemir@gmai.com>
 * @copyright       Por definir
 * @license         Por definir
 * @version			v1.0 11/10/12 11:32 AM
 *  * */
class Data_source extends MY_Controller implements Controller_interface {

	function __construct() {
		parent::__construct();
		$this->load->model('data_source_model');
		//$this->load->library('Pdf');
		//$this->load->helper('file');
	}

	/**
	 * <b>Method:	index()</b>
	 * Metodo por defecto a ser accedido al por el controlador.
	 * @param		array $params arreglo que contiene una accion a ejecutarse 
	 * @author		
	 * @version		v-1.0 11/10/12 04:21 PM
	 * */
	function index() {

		  $html_params = array();
        $tabs_params = array();
        $panel = array('panelType' => '2A', 'type1' => 'PanelHtml_', 'type2' => 'tabs_', 'p1' => $this->dyna_views->buildPanelHtml($html_params), 'p2' => $this->dyna_views->buildTabs($tabs_params));
        $panel_params = array('title' => 'Listado de Reportes', 'replace' => 'center', 'data' => $panel);
        $this->dyna_views->buildPanel($panel_params);
	}

	/**
	 * <b>Method:	create()</b>
	 * Define el nombre del metodo que asumira la creación de datos.
	 * @param		array $params arreglo que contiene una accion a ejecutarse .
	 * @author		
	 * @version		
	 * */
	function create($params) {

		// Condicion para mostrar el formulario
		if (!$this->input->post()) {

			// Entrada del log
			$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
			$this->load->view('report/data_source/create/main.js.php');
		}

		// Condicion para procesar y validar el formulario
		else {

			// Recepcion de la data del formulario
			$post_data = $this->input->post();

			// Formato de los datos generales de la fuente de datos
			$form_data = json_decode($post_data['form_data']);
			$sql_params = json_decode($post_data['sql_params']);

			// Si no existe error en la consulta se debe generar el registro de la fuente de datos
			if ($this->data_source_model->validateSqlQuery($sql_params, $form_data, $pg_error)) {
				
				// Generacion del registro de la fuente de datos
				if ($this->data_source_model->create($form_data)) {
					$result = true;
					$msg = $this->lang->line('message_create_data_source_success');
					$this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $post_data);
				} else {
					$result = false;
					$msg = $this->lang->line('message_operation_error');
					$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, $post_data);
				}
			} else {
				$result = false;
				$msg = $pg_error;
				$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('message' => $msg));
			}
			throwResponse(array("title" => "", "result" => $result, "msg" => $msg, 'success' => true));
		}
	}

	/**
	 * <b>Method:	listAll()</b>
	 * Define el nombre del metodo que listara todos los datos de la instacia.
	 * @author		Nohemi Rojas <nohemir@gmail.com>
	 * @version		
	 * */
	function listAll() {

		// Campo que contiene el valor del filtro que debe ser considerado en el grid
		$search_field = $this->input->post('searchfield');
		
		// Definicion de los datos mostrados en el grid
		$data["rowset"] = $this->data_source_model->getAll($search_field);
		$data['totalRows'] = count($data['rowset']);

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, (!empty($search_field) ? array('search_field' => $search_field) : null));
		
		// Si se ejecuta alguna busqueda se deben enviar los datos consultados
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
			die(json_encode($data));
		
		// Si no se han enviado datos de busqueda se muestra el grid con datos por defectos
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
	 * <b>Method: CL_getTables() </b>
	 * @method	Permite obtener las tablas permitidas para la generacion de reportes (business_logic schema)
	 * @return	Arreglo con las tablas
	 * @author	Reynaldo Rojas
	 * @version	v1.0 04/10/12 02:58 PMd
	 */
	public function CL_getTables() {
		$data = $this->data_source_model->getTables();
		die(json_encode($data));
	}

	/**
	 * <b>Method:	CL_getEntitiesColumns()</b>
	 * @method		Obtiene las columnas pertenecientes a las tablas indicadas
	 * @param		String $entities ids de entidades vienen por GET
	 * @return		json de columnas por entidad
	 * @author		Jesús Farías Lacroix
	 * @version		v1.0 23/12/11 03:15 PM
	 * */
	function CL_getEntitiesColumns() {

		$entities = $this->input->get('store_entities');
		if (empty($entities))
			return;
		$columns = $this->data_source_model->getEntitiesColumns($entities);
		die(json_encode($columns));
	}

	/**
	 * <b>Method: CL_getRelationType() </b>
	 * @method	Permite obtener las opciones permitidas para relaciones entre tablas
	 * @return	Arreglo con los elementos
	 * @author	Reynaldo Rojas
	 * @version	v1.0 04/10/12 02:58 PM
	 */
	public function CL_getRelationType() {
		$data = $this->data_source_model->getRelationType();
		die(json_encode($data));
	}
	 /**
	 * <b>Method: CL_getGroupByFunction() </b>
	 * @method	Permite obtener las funciones de agregacion permitidas en sql
	 * @return	Arreglo con las funciones permitidas
	 * @author	Reynaldo Rojas
	 * @version	v1.0 04/10/12 02:58 PMd
	 */
	public function CL_getGroupByFunction($params) {
		$data_type = $this->input->get('data_type');
		$data = $this->data_source_model->getGroupByFunction($data_type);
		array_unshift($data, array('value' => '', 'label' => '- Vacio -'));
		die(json_encode($data));
		
	}

	/**
	 * <b>Method: CL_isCodeUnique()</b>
	 * @method	verifica si el codigo de un reporte es unico
	 * @param	String $codigo codigo del reporte
	 * @return	Array arreglo que posee en la posicion success true en caso de ser unico, en caso contrario posee false
	 * @author	Reynaldo Rojas
	 * @version v1.0 16/10/12 05:07 PM
	 * */
	function CL_isCodeUnique() {
		$arr_result = array('valid' => $this->data_source_model->isCodeUnique($this->input->get('code')));
		die(json_encode($arr_result));
	}

}

// END Data_source Class
// End of file data_source.php
// Location modules/adm_report/controllers/data_source.php
?>