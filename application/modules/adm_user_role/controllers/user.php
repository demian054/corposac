<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * User Class
 * @package         user
 * @subpackage      controllers
 * @author          Eliel Parra <elielparra@gmail.com>, Reynaldo Rojas <reyre8@gmail.com>
 * @copyright       Por definir
 * @license         Por definir
 * @version			v1.0 10/11/11 02:48 PM
 *  * */
class User extends MY_Controller implements Controller_interface {

	private $module;
	private $error;

	function __construct() {
		parent::__construct();
		$this->load->model('user_model');
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
	 * Metodo que permite crear un usuario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 06:08 PM
	 * */
	function create($params) {

		// Condicion para mostrar el formulario
		if (!$this->input->post()) {

			// Parametros para la construccion del formulario
			$params = array(
				'title' => 'Crear Usuario',
				'name' => 'Usuario',
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

			// Si no existen errores de validacion, se debe crear el usuario
			if ($data_process['result']) {
				
				// Formato de datos adicionales para crear al usuario
				$extra_params = $this->user_model->_format($data_process['data'], 'INSERT');
				
				// Verificacion de creacion de usuario a traves de la libreria tank_auth
				if ($this->tank_auth->create_user($data_process['data']['username'], $data_process['data']['email'], $data_process['data']['password'], FALSE, $extra_params, $this->error)) {
					$result = TRUE;
					$msg = $data_process['message'] = $this->lang->line('message_create_success');
					$this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $data_process['data']);
				}

				// Si existe error en la creacion se deben mostrar los mensajes
				else {
					$result = FALSE;

					// Definicion del mensaje de error
					$msg = $data_process['message'] = $this->lang->line($this->error['error']);
					if (empty($msg))
						$msg = $data_process['message'] = $this->lang->line('message_operation_error');

					$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, $data_process['data']);
				}
			}
			// Si existen errores de validacion se deben mostrar los mensajes
			else {
				$msg = $data_process['msg'];
				$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('message' => $msg));
			}

			throwResponse(array("title" => "Crear Usuario", "result" => $result, "msg" => $msg, 'success' => TRUE));
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
		$data = $this->user_model->getById($this->input->get('id'));

		// Parametros para la construccion del formulario
		$params = array(
			'title' => 'Visualizar Usuario',
			'name' => 'Usuario',
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
	 * 		Muestra todos los usuarios del sistema
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:46 PM
	 * */
	function listAll() {

		// Campo que contiene el valor del fis_email_availableiltro que debe ser considerado en el grid
		$search_field = $this->input->post('searchfield');

		// Definicion de los datos mostrados en el grid
		$data["rowset"] = $this->user_model->getAll($search_field);
		$data['totalRows'] = count($data['rowset']);

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, (!empty($search_field) ? array('search_field' => $search_field) : null));

		// Si se ejecuta alguna busqueda se deben enviar los datos consultados
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
			die(json_encode($data));

		// Si no se han enviado datos de busqueda se muestra el grid con datos por defecto
		else {
			$params = array(
				'title' => 'Listado de Usuarios',
				'name' => 'el Usuario',
				'data' => $data,
				'replace' => 'center',
				'extraOptions' => array('bbarOff' => TRUE, 'searchType' => 'S')
			);
			$this->dyna_views->buildGrid($params);
		}
	}

	/**
	 * <b>Method:	listRoles()</b>
	 * 		Muestra todos los roles activos del sistema
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:46 PM
	 * */
	function listRoles($params) {

		// Condicion para mostrar el formulario
		if (!$this->input->post()) {

			// Definicion del identificador del usuario a modificar
			$this->session->set_userdata('user_edit_id', $this->input->get('id'));

			// Seleccion de roles asociados al usuario antes de ser modificados
			$this->session->set_userdata('raw_role_array', $this->user_model->getAssociatedRoles(array(true)));

			// Carga de vista particular
			$this->load->view('adm_user_role/user/associate_role.js.php');

			// Entrada del log
			$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $this->session->userdata('user_edit_id')));
		}

		// Condicion para procesar y validar el formulario
		elseif (!empty($params) && $params[0] == 'process') {

			// Roles modificados
			$role = $this->input->post('selectorRoles');

			// Definicion de roles modificados
			$modified_role_array = array();
			if (!empty($role))
				$modified_role_array = explode(',', $role);

			// Formato de roles no modificados
			$raw_role_array = $this->user_model->formatRoleArray($this->session->userdata('raw_role_array'));

			// Verificacion de asignacion de roles
			if ($this->user_model->associateRole($this->session->userdata('user_edit_id'), $raw_role_array, $modified_role_array)) {
				$result = TRUE;
				$msg = $this->lang->line('message_asign_rol_success');
				$this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, array('user_id' => $this->session->userdata('user_edit_id'), 'roles' => $this->input->post('roles')));
			} 
			
			// Si ocurre algun error generar mensajes de error
			else {
				$msg = $this->lang->line('message_operation_error');
				$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('user_id' => $this->session->userdata('user_edit_id'), 'roles' => $this->input->post('roles'), 'message' => $msg));
			}

			throwResponse(array("title" => "Asociar Roles a Usuario", "result" => $result, "msg" => $msg, 'success' => TRUE));
		}
	}

	/**
	 * <b>Method:	listRolesWidgets()</b>
	 * 		Muestra todos los roles asignados a un Widget
	 * @author		Heiron Contreras
	 * @version		v1.0 18/01/12 02:46 PM
	 * */
	function listRolesWidgets($params) {

		if (!$this->input->post()) {
			$this->session->set_userdata('widget_id', $this->input->get('id'));
			$this->load->model('combo_loader_model');
			$this->session->set_userdata('raw_role_array', $this->combo_loader_model->getRolesAsocWidget());
			$this->load->model('widgets/widgets_crud_model');
			$data_widget = $this->widgets_crud_model->getById($this->input->get('id'));
			$this->load->view('user/associate_role_widgets.js.php', $data_widget);
			$data['user_id'] = $this->session->userdata('user_edit_id');

			$this->logger->createLog('Acceso: ' . $this->dyna_views->operationData->name, $this->dyna_views->operationData->id, json_encode($data));
		} elseif (!empty($params) && $params[0] == 'process') {

			$role = $this->input->post('selectorRoles');

			$modified_role_array = array();
			if (!empty($role))
				$modified_role_array = explode(',', $role);

			$raw_role_array = $this->user_model->formatRoleArray($this->session->userdata('raw_role_array'));

			$result = FALSE;
			if ($this->user_model->asociarRolWidget($this->session->userdata('widget_id'), $raw_role_array, $modified_role_array)) {
				$result = TRUE;
				$msg = $this->lang->line('message_asign_rol_success');
				$this->logger->createLog('Exito: ' . $this->dyna_views->operationData->name, $this->dyna_views->operationData->id, json_encode(array('widget_id' => $this->session->userdata('widget_id'), 'roles' => $this->input->post('roles'))));
			} else {
				$msg = $this->lang->line('message_operation_error');
				$this->logger->createLog('Fracaso: ' . $this->dyna_views->operationData->name, $this->dyna_views->operationData->id, json_encode(array('widget_id' => $this->session->userdata('widget_id'), 'error' => $msg)));
			}

			throwResponse("Asociar Roles a Widget", $result, $msg);
		}
	}

	/**
	 * <b>Method:	edit()</b>
	 * Metodo que perimte crear un usuario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 06:08 PM
	 * */
	function edit($params) {

		// Condicion para mostrar el formulario
		if (!$this->input->post()) {

			// Condicion para verificar si se encuentra en modo edicion de todos los perfiles o de mi perfil
			if ($params[0] == 'allProfile') {
				$form_title = 'Editar Usuario';
				$user_id = $this->input->get('id');
				$replace = 'window';
			} elseif ($params[0] == 'myProfile') {
				$form_title = 'Mi Perfil';
				$user_id = $this->session->userdata('user_id');
				$replace = 'center';
			}

			// Consulta de datos del usuario
			$data = $this->user_model->getById($user_id);

			// Parametros para la construccion del formulario
			$params = array(
				'title' => $form_title,
				'name' => 'Usuario',
				'replace' => $replace,
				'data' => $data
			);

			$this->dyna_views->buildForm($params);

			// Entrada del log
			$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, $data);
		}

		// Condicion para procesar y validar el formulario
		elseif (!empty($params) && $params[1] == 'process') {

			// Carga de validaciones del formulario
			$data_process = $this->dyna_views->processForm();

			// Si no existen errores de validacion, se debe crear el usuario
			if ($data_process['result']) {

				// Verificacion de edicion de usuario
				if ($this->user_model->update($data_process['data'])) {
					$result = TRUE;
					$msg = $data_process['message'] = $this->lang->line('message_create_success');
					$this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id, $data_process['data']);

					// Condicion para refrescar el formulario
					if ($params[0] == 'myProfile') {
						$extra_vars['redirect']['url'] = "adm_user_role/user/edit/myProfile";
						$extra_vars['redirect']['id'] = "{$this->session->userdata('user_id')}";
					}
				}

				// Si existe error en la edicion se deben mostrar los mensajes
				else {
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

			throwResponse(array("title" => "Editar Usuario", "result" => $result, "msg" => $msg, 'success' => TRUE, 'extra_vars' => $extra_vars));
		}
	}

	/**
	 * <b>Method:	delete()</b>
	 * Elimina de forma booleana un registro de usuario seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:40 PM
	 * */
	function delete() {

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $this->input->post('id')));

		// Verificar si el usuario a eliminar es distinto al usuario en sesion
		if ($this->session->userdata('user_id') != $this->input->post('id')) {
			if ($this->user_model->delete($this->input->post('id'))) {
				$result = TRUE;
				$msg = $this->lang->line('message_delete_success');
				$operation_result = SUCCESS;
			} else {
				$result = FALSE;
				$msg = $this->lang->line('message_operation_error');
				$operation_result = FAILURE;
			}
		}

		// El usuario no se puede eliminar a si mismo
		else {
			$result = FALSE;
			$msg = $this->lang->line('self_deleted_user');
			$operation_result = FAILURE;
		}

		// Entrada del log
		$this->logger->createLog($operation_result, $this->dyna_views->operationData->id, array('id' => $this->input->post('id'), 'message' => $msg));

		throwResponse(array("title" => "Eliminar Usuario", "result" => $result, "msg" => $msg, 'success' => TRUE));
	}

	/**
	 * <b>Method:	deactivate()</b>
	 * Desactiva un registro de usuario seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:40 PM
	 * */
	function deactivate() {

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $this->input->post('id')));

		// Verificar si el usuario a desactivar es distinto al usuario en sesion
		if ($this->session->userdata('user_id') != $this->input->post('id')) {

			if ($this->user_model->deactivate($this->input->post('id'))) {
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
			$msg = $this->lang->line('self_deactivated_user');
			$operation_result = FAILURE;
		}

		// Entrada del log
		$this->logger->createLog($operation_result, $this->dyna_views->operationData->id, array('id' => $this->input->post('id'), 'message' => $msg));

		throwResponse(array("title" => "Desactivar Rol", "result" => $result, "msg" => $msg, 'success' => TRUE));
	}

	/**
	 * <b>Method:	activate()</b>
	 * Activa un registro de usuario seleccionado
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:40 PM
	 * */
	function activate() {

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id, array('id' => $this->input->post('id')));

		// Verificar si el usuario a activar es distinto al usuario en sesion
		if ($this->session->userdata('user_id') != $this->input->post('id')) {

			if ($this->user_model->activate($this->input->post('id'))) {
				$result = TRUE;
				$msg = $this->lang->line('message_activate_success');
				$operation_result = SUCCESS;
			} else {
				$result = FALSE;
				$msg = $this->lang->line('message_operation_error');
				$operation_result = FAILURE;
			}
		}

		// El usuario no se puede activar a si mismo
		else {
			$result = FALSE;
			$msg = $this->lang->line('self_activated_user');
			$operation_result = FAILURE;
		}

		// Entrada del log
		$this->logger->createLog($operation_result, $this->dyna_views->operationData->id, array('id' => $this->input->post('id'), 'message' => $msg));

		throwResponse(array("title" => "Activar Usuario", "result" => $result, "msg" => $msg, 'success' => TRUE));
	}

	/**
	 * <b>Method: listAllUserRoles</b>
	 * 	Permite visualizar todos los Roles del Usuario
	 * @author	Eliel Parra, Reynaldo Rojas
	 * @version v-1.0 18/11/11 04:24 PM
	 * */
	function listAllUserRoles() {

		// Definicion de los datos mostrados en el grid
		$data["rowset"] = $this->user_model->getRolesByUserId($this->session->userdata('user_id'));
		$data['totalRows'] = count($data['rowset']);

		// Entrada del log
		$this->logger->createLog(ACCESS, $this->dyna_views->operationData->id);

		// Si se ejecuta alguna busqueda se deben enviar los datos consultados
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
			die(json_encode($data));

		// Si no se han enviado datos de busqueda se muestra el grid con datos por defecto
		else {
			$params = array(
				'title' => 'Listado de Roles de Usuario',
				'name' => 'Roles',
				'data' => $data,
				'replace' => 'window',
				'extraOptions' => array('bbarOff' => TRUE)
			);
			$this->dyna_views->buildGrid($params);
		}
	}

	/**
	 * <b>Method:	create()</b>
	 * Metodo que permite cambiar la contraseña de un usuario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 14/11/11 06:08 PM
	 * */
	function changePassword($params) {

		// Condicion para mostrar el formulario
		if (!$this->input->post()) {

			// Parametros para la construccion del formulario
			$params = array(
				'title' => 'Cambiar Contraseña',
				'name' => 'Contraseña',
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

			// Si no existen errores de validacion, se debe crear el usuario
			if ($data_process['result']) {

				// Verificacion de cambio de contraseña a traves de la libreria tank_auth
				if ($this->tank_auth->change_password($data_process['data']['new_password_key'], $data_process['data']['password'])) {
					$result = TRUE;
					$msg = $data_process['message'] = $this->lang->line('message_create_success');
					$this->logger->createLog(SUCCESS, $this->dyna_views->operationData->id);
				}

				// Si existe error en el cambio de contraseña se deben mostrar los mensajes
				else {
					$result = FALSE;

					// Captura del mensaje de error de tank_auth
					$this->error = $this->tank_auth->get_error_message();

					// Definicion del mensaje de error
					$msg = $data_process['message'] = $this->lang->line($this->error['old_password']);
					if (empty($msg))
						$msg = $data_process['message'] = $this->lang->line('message_operation_error');

					$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id);
				}
			}
			// Si existen errores de validacion se deben mostrar los mensajes
			else {
				$msg = $data_process['msg'];
				$this->logger->createLog(FAILURE, $this->dyna_views->operationData->id, array('message' => $msg));
			}

			throwResponse(array("title" => "Cambiar Contraseña", "result" => $result, "msg" => $msg, 'success' => TRUE));
		}
	}

	/**
	 * <b>Method:  isIdentificationUnique()</b>
	 * Metodo callback que permite verificar si el numero identificador del usuario es unico
	 * @return 		Boolean TRUE en caso que el nombre sea unico. En caso contrario retorna FALSE
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	V-1.0 12/09/12 03:56 PM
	 * */
	function isIdentificationUnique() {
		return $this->user_model->isIdentificationUnique($this->input->post('_document'), $this->input->post('id'));
	}

	/**
	 * <b>Method:  isPasswordComparison()</b>
	 * Metodo callback que permite verificar si el password ingresado por el usuario es igual al password repetido
	 * @return 		Boolean TRUE en caso que el nombre sea unico. En caso contrario retorna FALSE
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	V-1.0 12/09/12 03:56 PM
	 * */
	function isPasswordComparison() {
		return $this->user_model->isPasswordComparison($this->input->post('password'), $this->input->post('repeated_password'));
	}

	/**
	 * <b>Method:  isEmailDuplicated()</b>
	 * Metodo callback que permite verificar si el email esta disponible
	 * @return 		Boolean TRUE en caso que el nombre sea unico. En caso contrario retorna FALSE
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	V-1.0 12/09/12 03:56 PM
	 * */
	function isEmailAvailable() {
		if (!$this->tank_auth->is_email_available($this->input->post('email'), $this->input->post('id'))) {
			$this->form_validation->set_message('isEmailAvailable', $this->lang->line('email_unique'));
			return FALSE;
		}
		else
			return TRUE;
	}

	/**
	 * <b>Method:  isUserNameAvailable()</b>
	 * Metodo callback que permite verificar si el usuario esta disponible
	 * @return 		Boolean TRUE en caso que el nombre sea unico. En caso contrario retorna FALSE
	 * @author 		Reynaldo Rojas <rrojas@rialfi.com>
	 * @version 	V-1.0 12/09/12 03:56 PM
	 * */
	function isUserNameAvailable() {
		if (!$this->tank_auth->is_username_available($this->input->post('username'), $this->input->post('id'))) {
			$this->form_validation->set_message('isUserNameAvailable', $this->lang->line('username_unique'));
			return FALSE;
		}
		else
			return TRUE;
	}

	/**
	 * <b>Method: CL_getAssociatedRoles() </b>
	 * @method	Permite obtener los roles asociados a un usuario
	 * @return	Arreglo con los roles
	 * @author	Reynaldo Rojas
	 * @version	v1.0 07/12/11 10:52 AM
	 */
	public function CL_getAssociatedRoles($params) {
		$data = $this->user_model->getAssociatedRoles($params);
		die(json_encode($data));
	}

}

// END users Class
// End of file user.php
// Location modules/user/controllers/user.php
?>