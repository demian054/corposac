<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Role Class
 * @package         adm_user_role
 * @subpackage      controllers
 * @author          Eliel Parra <elielparra@gmail.com>, Reynaldo Rojas <reyre8@gmail.com>
 * @copyright       Por definir
 * @license         Por definir
 * @version			v1.0 10/09/12 11:13 AM
 *  * */
class Role extends MY_Controller implements Controller_interface {

	function __construct() {
		parent::__construct();
		$this->load->model('role_model');
	}

	/**
	 * <b>Method:   index()</b>
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	v-1.0 10/09/12 11:23 AM
	 * */
	function index() {
		
	}

	/**
	 * <b>Method:	create()</b>
	 * Metodo que perimte crear un rol
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 06:08 PM
	 * */
	function create($params) {

		// Condicion para mostrar el formulario
		if (!$this->input->post()) {

			// Parametros para la construccion del formulario
			$params = array(
				'title' => 'Crear Rol',
				'name' => 'Rol',
				'replace' => 'window',
			);
			$this->dyna_views->buildForm($params);

			// Entrada del log
			$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);
		}

		// Condicion para procesar y validar el formulario
		elseif (!empty($params) && $params[0] == 'process') {

			// Carga de validaciones del formulario
			$data_process = $this->dyna_views->processForm();

			// Si no existen errores de validacion, se debe crear el rol
			if ($data_process['result']) {

				if ($this->role_model->create($data_process['data'])) {
					$result = TRUE;
					$msg = $data_process['message'] = $this->lang->line('message_create_success');
					$this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $data_process['data']);
				} else {
					$result = FALSE;
					$msg = $data_process['message'] = $this->lang->line('message_operation_error');
					$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, $data_process['data']);
				}
			}
			// Si existen errores de validacion se deben mostrar los mensajes
			else {
				$msg = $data_process['msg'];
				$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('message' => $msg));
			}
			
			throwResponse(array("title" => "Crear Rol", "result" => $result, "msg" => $msg, 'success' => TRUE));
		}
	}

	/**
	 * <b>Method:	detail()</b>
	 * Muetra los detalles del rol seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 06:08 PM
	 * */
	function detail() {

		// Consulta de datos del rol
		$data = $this->role_model->getById($this->input->get('id'));

		// Parametros para la construccion del formulario
		$params = array(
			'title' => 'Visualizar Rol',
			'name' => 'Rol',
			'replace' => 'window',
			'data' => $data,
			'extraOptions' => array('CancelButton' => '0')
		);
		$this->dyna_views->buildForm($params);

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, $data);
	}

	/**
	 * <b>Method:	listAll()</b>
	 * Muestra todos los roles del sistema
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 06:08 PM
	 * */
	function listAll() {

		// Campo que contiene el valor del filtro que debe ser considerado en el grid
		$search_field = $this->input->post('searchfield');

		// Definicion de los datos mostrados en el grid
		$data["rowset"] = $this->role_model->getAll($search_field);
		$data['totalRows'] = count($data['rowset']);

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, (!empty($search_field) ? array('search_field' => $search_field) : null));

		// Si se ejecuta alguna busqueda se deben enviar los datos consultados
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
			die(json_encode($data));

		// Si no se han enviado datos de busqueda se muestra el grid con datos por defecto
		else {
			$params = array(
				'title' => 'Listado de Roles',
				'name' => 'el Rol',
				'data' => $data,
				'replace' => 'center',
				'extraOptions' => array('bbarOff' => TRUE, 'searchType' => 'S')
			);
			$this->dyna_views->buildGrid($params);
		}
	}

	/**
	 * <b>Method:	listOperaciones()</b>
	 * Muestra todos las operaciones activos del sistema
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:46 PM
	 * */
	function listOperation($params) {

		// Condicion para mostrar el arbol de operaciones
		if (!$this->input->post()) {

			// Definicion de variable de sesion con el rol que se va a editar
			$this->session->set_userdata('role_edit_id', $this->input->get('id'));

			// Consulta para obtener las operaciones relacionadas al rol
			$data['tree'] = $this->role_model->getAllOperations();

			// Entrada del log
			$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('data' => $data['tree']));

			// Construccion del arbol de operaciones relacionadas al rol
			$this->load->view('role/administrar_roles.js.php', $data);
		}

		// Condicion para procesar y validar el formulario
		elseif (!empty($params) && $params[0] == 'process') {

			$arr_operation = json_decode($this->input->post('tree'));

			if ($this->role_model->associateOperation($this->session->userdata('role_edit_id'), $arr_operation)) {
				$result = TRUE;
				$msg = $this->lang->line('message_asign_operation_success');
				$operation_result = SUCCESS;
			} else {
				$result = FALSE;
				$msg = $this->lang->line('message_operation_error');
				$operation_result = FAILURE;
			}

			// Entrada del log
			$this->logger->createLog($operation_result, $this->dyna_views->operationData->id, array('data' => $arr_operation, 'message' => $msg));
			
			throwResponse(array("title" => "Administrar Operaciones Asociadas al Rol", "result" => $result, "msg" => $msg, 'success' => TRUE));
		}
	}

	/**
	 * <b>Method:	edit()</b>
	 * Permite editar los valores de un rol
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:42 PM
	 * */
	function edit($params) {

		// Condicion para mostrar el formulario
		if (!$this->input->post()) {

			// Consulta de datos del rol
			$data = $this->role_model->getById($this->input->get('id'));

			// Parametros para la construccion del formulario
			$params = array(
				'title' => 'Editar Rol',
				'name' => 'Rol',
				'replace' => 'window',
				'data' => $data
			);
			$this->dyna_views->buildForm($params);

			// Entrada del log
			$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, $data);
		}

		// Condicion para procesar y validar el formulario
		elseif (!empty($params) && $params[0] == 'process') {

			// Carga de validaciones del formulario
			$data_process = $this->dyna_views->processForm();

			// Si no existen errores de validacion, se debe crear el rol
			if ($data_process['result']) {

				if ($this->role_model->update($data_process['data'])) {
					$result = TRUE;
					$msg = $data_process['message'] = $this->lang->line('message_update_success');
					$this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $data_process['data']);
				} else {
					$result = FALSE;
					$msg = $data_process['message'] = $this->lang->line('message_operation_error');
					$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, $data_process['data']);
				}
			}
			// Si existen errores de validacion se deben mostrar los mensajes
			else {
				$msg = $data_process['msg'];
				$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('message' => $msg));
			}

			throwResponse(array("title" => "Editar Rol", "result" => $result, "msg" => $msg, 'success' => TRUE));
		}
	}

	/**
	 * <b>Method:	delete()</b>
	 * Elimina de forma booleana un registro de rol seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:40 PM
	 * */
	function delete() {

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $this->input->post('id')));

		// Verificar si el rol a eliminar es distinto al rol en sesion
		if ($this->session->userdata('role_id') != $this->input->post('id')) {

			// Verificar que el Rol no posea usuarios asociados
			if (!$this->role_model->hasAssociatedUsers($this->input->post('id'))) {

				if ($this->role_model->delete($this->input->post('id'))) {
					$result = TRUE;
					$msg = $this->lang->line('message_delete_success');
					$operation_result = SUCCESS;
				} else {
					$result = FALSE;
					$msg = $this->lang->line('message_operation_error');
					$operation_result = FAILURE;
				}
			} else {
				$result = FALSE;
				$msg = $this->lang->line('has_associated_users');
				$operation_result = FAILURE;
			}
		}

		// El rol no se puede eliminar a si mismo
		else {
			$result = FALSE;
			$msg = $this->lang->line('self_deleted_role');
			$operation_result = FAILURE;
		}

		// Entrada del log
		$this->logger->createLog($operation_result, $this->dyna_views->operationData->id, array('id' => $this->input->post('id'), 'message' => $msg));

		throwResponse(array("title" => "Eliminar Rol", "result" => $result, "msg" => $msg, 'success' => TRUE));
	}

	/**
	 * <b>Method:	deactivate()</b>
	 * Desactiva un registro de rol seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:40 PM
	 * */
	function deactivate() {

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $this->input->post('id')));

		// Verificar si el rol a desactivar es distinto al rol en sesion
		if ($this->session->userdata('role_id') != $this->input->post('id')) {

			if ($this->role_model->deactivate($this->input->post('id'))) {
				$result = TRUE;
				$msg = $this->lang->line('message_deactivate_success');
				$operation_result = SUCCESS;
			} else {
				$result = FALSE;
				$msg = $this->lang->line('message_operation_error');
				$operation_result = FAILURE;
			}
		}

		// El rol no se puede desactivar a si mismo
		else {
			$result = FALSE;
			$msg = $this->lang->line('self_deactivated_role');
			$operation_result = FAILURE;
		}

		// Entrada del log
		$this->logger->createLog($operation_result, $this->dyna_views->operationData->id, array('id' => $this->input->post('id'), 'message' => $msg));

		throwResponse(array("title" => "Desactivar Rol", "result" => $result, "msg" => $msg, 'success' => TRUE));
	}

	/**
	 * <b>Method:	activate()</b>
	 * Activa un registro de rol seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:40 PM
	 * */
	function activate() {

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $this->input->post('id')));

		// Verificar si el rol a activar es distinto al rol en sesion
		if ($this->session->userdata('role_id') != $this->input->post('id')) {

			if ($this->role_model->activate($this->input->post('id'))) {
				$result = TRUE;
				$msg = $this->lang->line('message_activate_success');
				$operation_result = SUCCESS;
			} else {
				$result = FALSE;
				$msg = $this->lang->line('message_operation_error');
				$operation_result = FAILURE;
			}
		}

		// El rol no se puede activar a si mismo
		else {
			$result = FALSE;
			$msg = $this->lang->line('self_activated_role');
			$operation_result = FAILURE;
		}

		// Entrada del log
		$this->logger->createLog($operation_result, $this->dyna_views->operationData->id, array('id' => $this->input->post('id'), 'message' => $msg));
		
		throwResponse(array("title" => "Activar Rol", "result" => $result, "msg" => $msg, 'success' => TRUE));
	}

	/**
	 * <b>Method:  isRoleNameUnique()</b>
	 * Metodo callback que permite verificar si el nombre del role es unico
	 * @return 		Boolean TRUE en caso que el nombre sea unico. En caso contrario retorna FALSE
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	V-1.0 12/09/12 03:56 PM
	 * */
	function isRoleNameUnique() {
		return $this->role_model->isRoleNameUnique($this->input->post('_name'), $this->input->post('id'));
	}

}

// END Role Class
// End of file role.php
// Location ./aplication/modules/user/controllers/user.php