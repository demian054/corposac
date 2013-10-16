<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Documentacion class
 * 
 * @package		documentacion
 * @subpackage	controller
 * @author		Nohemi Rojas <nrojas@rialfi.com> ,  Eliel Parra <eparra@rialfi.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v1.0 16/09/11 04:29 PM
 * */
class Documentacion extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('documentacion_model');
	}

	/**
	 * <b>Method: index()</b>
	 * @method Metodo para obtener el indice de la documentacion
	 * @author Nohemi Rojas Eliel Parra
	 * @version	v1.0 16/09/11 04:47 PM
	 */
	function index() {
		if ($this->tank_auth->is_logged_in()) {
			$data['result'] = $this->documentacion_model->getAll('json');
			$data['content'] = 'index';
			$data['title'] = 'SIETPOL - Documentaci&oacute;n';
			$this->load->view('main_layout_documentacion.js.php', $data);
		}
		else
			redirect('/auth/login/');
	}

	/**
	 * <b>Method: printable()</b>
	 * @method Metodo para obtener el contenido de la documentacion
	 * @author Nohemi Rojas Eliel Parra
	 * @version	v1.0 16/09/11 04:47 PM
	 */
	function printable() {
		if ($this->tank_auth->is_logged_in()) {
			$data['result'] = $this->documentacion_model->getAll('content');
			$data['content'] = 'content';
			$this->load->view('main_layout', $data);
		}
		else
			redirect('/auth/login/');
	}

	/**
	  <b>Method: detail()</b>
	 * @method	Metodo para obtener el detalle
	 * @author	Eliel Parra
	 * @version	v1.0 16/09/11 04:47 PM
	 * */

	function detail($param) {

		if($param == '0'){
			$data['bienvenida'] = TRUE;
			$result = $this->load->view('doc_content', $data, TRUE);
			die($result); 
		}else{
			$data['operacion'] = $this->documentacion_model->getById($param);
			$data['campos'] = $this->documentacion_model->getFields($param);
			$data['padre'] = $this->documentacion_model->getById($data['operacion']->operation_id);
			$data['hijos'] = $this->documentacion_model->getChildrenByFather($param);
			$result = $this->load->view('doc_content', $data, TRUE);
			die($result);
		}
	}

}

/* END Documentacion Class       */
/* End of file documentacion.php */
/* Location: ./application/modules/documentacion/controllers/documentacion.php */
?>