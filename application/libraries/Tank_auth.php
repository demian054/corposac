<?php

if (!defined('BASEPATH'))	exit('Acceso Denegado');

require_once('phpass-0.1/PasswordHash.php');

define('STATUS_ACTIVATED', '1');
define('STATUS_NOT_ACTIVATED', '0');

/**
 * Tank_auth
 *
 * Authentication library for Code Igniter.
 *
 * @package		Tank_auth
 * @author		Ilya Konyukhov (http://konyukhov.com/soft/)
 * @version		1.0.9
 * @based on	DX Auth by Dexcell (http://dexcell.shinsengumiteam.com/dx_auth)
 * @license		MIT License Copyright (c) 2008 Erick Hartanto
 */
class Tank_auth {

	private $error = array();

	function __construct() {
		$this->ci = & get_instance();

		$this->ci->load->config('tank_auth', TRUE);

		$this->ci->load->library('session');
		$this->ci->load->database();
		$this->ci->load->model('lib_tank_auth/tank_auth/users');
		$this->ci->load->model('lib_tank_auth/tank_auth/roles');
		$this->ci->load->model('lib_tank_auth/tank_auth/operations');

		// Try to autologin
		$this->autologin();
	}

	/**
	 * Login user on the site. Return TRUE if login is successful
	 * (user exists and activated, password is correct), otherwise FALSE.
	 *
	 * @param	string	(username or email or both depending on settings in config file)
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function login($login, $password, $remember, $login_by_username, $login_by_email) {
		if ((strlen($login) > 0) AND (strlen($password) > 0)) {

			// Which function to use to login (based on config)
			if ($login_by_username AND $login_by_email) {
				$get_user_func = 'get_user_by_login';
			} else if ($login_by_username) {
				$get_user_func = 'get_user_by_username';
			} else {
				$get_user_func = 'get_user_by_email';
			}

			if (!is_null($user = $this->ci->users->$get_user_func($login))) { // login ok
				// Does password match hash in database?
				$hasher = new PasswordHash(
						$this->ci->config->item('phpass_hash_strength', 'tank_auth'),
						$this->ci->config->item('phpass_hash_portable', 'tank_auth'));
				if ($hasher->CheckPassword($password, $user->password)) {  // password ok
					if ($user->banned == 1) {  // fail - banned
						$this->error = array('banned' => $user->ban_reason);
					} else {
						$this->ci->session->set_userdata(array(
							'user_id' => $user->id,
							'username' => $user->username,
							'status' => ($user->activated == 1) ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED,
							'email' => $user->email,
							'nombre' => $user->first_name,
							'apellidos' => $user->last_name
						));

						if ($user->activated == 0) { // fail - not activated
							$this->error = array('not_activated' => '');
						} else {  // success
							if ($remember) {
								$this->create_autologin($user->id);
							}

							$this->clear_login_attempts($login);

							$this->ci->users->update_login_info(
								$user->id, $this->ci->config->item('login_record_ip', 'tank_auth'), $this->ci->config->item('login_record_time', 'tank_auth'));
							return TRUE;
						}
					}
				} else { // fail - wrong password
					$this->increase_login_attempt($login);
					$this->error = array('password' => 'auth_incorrect_password');
				}
			} else { // fail - wrong login
				$this->increase_login_attempt($login);
				$this->error = array('login' => 'auth_incorrect_login');
			}
		}
		return FALSE;
	}

	/**
	 * Logout user from the site
	 *
	 * @return	void
	 */
	function logout() {
		$this->delete_autologin();

		// See http://codeigniter.com/forums/viewreply/662369/ as the reason for the next line
		$this->ci->session->set_userdata(array('user_id' => '', 'username' => '', 'status' => ''));

		$this->ci->session->sess_destroy();
	}

	/**
	 * Check if user logged in. Also test if user is activated or not.
	 *
	 * @param	bool
	 * @return	bool
	 */
	function is_logged_in($activated = TRUE) {
		return $this->ci->session->userdata('status') === ($activated ? STATUS_ACTIVATED : STATUS_NOT_ACTIVATED);
	}

	/**
	 * Get user_id
	 *
	 * @return	string
	 */
	function get_user_id() {
		return $this->ci->session->userdata('user_id');
	}

	/**
	 * Get username
	 *
	 * @return	string
	 */
	function get_username() {
		return $this->ci->session->userdata('username');
	}

	/**
	 * Create new user on the site and return some data about it:
	 * user_id, username, password, email, new_email_key (if any).
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @param   array
	 * @return	array
	 */
	function create_user($username, $email, $password, $email_activation, $extra, &$error = false) {
		if ((strlen($username) > 0) AND !$this->ci->users->is_username_available($username)) {
			$this->error = array('error' => 'username_unique');
		} elseif (!$this->ci->users->is_email_available($email)) {
			$this->error = array('error' => 'email_unique');
		} else {
			// Hash password using phpass
			$hasher = new PasswordHash(
					$this->ci->config->item('phpass_hash_strength', 'tank_auth'),
					$this->ci->config->item('phpass_hash_portable', 'tank_auth'));
			$hashed_password = $hasher->HashPassword($password);

			$data = array(
				'username' => $username,
				'password' => $hashed_password,
				'email' => $email,
				'last_ip' => $this->ci->input->ip_address(),
				'first_name' => $extra['first_name'],
				'last_name' => $extra['last_name'],
				'_document' => $extra['_document'],
				'phone_number' => $extra['phone_number'],
				'created_by' => $extra['created_by']
			);

			if ($email_activation) {
				$data['new_email_key'] = md5(rand() . microtime());
			}
			if (!is_null($res = $this->ci->users->create_user($data, !$email_activation))) {
				$data['user_id'] = $res['user_id'];
				$data['password'] = $password;
				unset($data['last_ip']);
				return $data;
			}
		}
		$error = $this->error;
		return NULL;
	}

	/**
	 * Check if username available for registering.
	 * Can be called for instant form validation.
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_username_available($username, $user_id = FALSE) {
		return ((strlen($username) > 0) AND $this->ci->users->is_username_available($username, $user_id));
	}

	/**
	 * Check if email available for registering.
	 * Can be called for instant form validation.
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_email_available($email, $user_id = FALSE) {
		return ((strlen($email) > 0) AND $this->ci->users->is_email_available($email, $user_id));
	}

	/**
	 * Change email for activation and return some data about user:
	 * user_id, username, email, new_email_key.
	 * Can be called for not activated users only.
	 *
	 * @param	string
	 * @return	array
	 */
	function change_email($email) {
		$user_id = $this->ci->session->userdata('user_id');

		if (!is_null($user = $this->ci->users->get_user_by_id($user_id, FALSE))) {

			$data = array(
				'user_id' => $user_id,
				'username' => $user->username,
				'email' => $email,
			);
			if (strtolower($user->email) == strtolower($email)) {  // leave activation key as is
				$data['new_email_key'] = $user->new_email_key;
				return $data;
			} elseif ($this->ci->users->is_email_available($email)) {
				$data['new_email_key'] = md5(rand() . microtime());
				$this->ci->users->set_new_email($user_id, $email, $data['new_email_key'], FALSE);
				return $data;
			} else {
				$this->error = array('email' => 'auth_email_in_use');
			}
		}
		return NULL;
	}

	/**
	 * Activate user using given key
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function activate_user($user_id, $activation_key, $activate_by_email = TRUE) {
		$this->ci->users->purge_na($this->ci->config->item('email_activation_expire', 'tank_auth'));

		if ((strlen($user_id) > 0) AND (strlen($activation_key) > 0)) {
			return $this->ci->users->activate_user($user_id, $activation_key, $activate_by_email);
		}
		return FALSE;
	}

	/**
	 * Set new password key for user and return some data about user:
	 * user_id, username, email, new_pass_key.
	 * The password key can be used to verify user when resetting his/her password.
	 *
	 * @param	string
	 * @return	array
	 */
	function forgot_password($login) {
		if (strlen($login) > 0) {
			if (!is_null($user = $this->ci->users->get_user_by_login($login))) {

				$data = array(
					'user_id' => $user->id,
					'username' => $user->username,
					'email' => $user->email,
					'new_pass_key' => md5(rand() . microtime()),
				);

				$this->ci->users->set_password_key($user->id, $data['new_pass_key']);
				return $data;
			} else {
				$this->error = array('login' => 'auth_incorrect_email_or_username');
			}
		}
		return NULL;
	}

	/**
	 * Check if given password key is valid and user is authenticated.
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function can_reset_password($user_id, $new_pass_key) {
		if ((strlen($user_id) > 0) AND (strlen($new_pass_key) > 0)) {
			return $this->ci->users->can_reset_password(
					$user_id, $new_pass_key, $this->ci->config->item('forgot_password_expire', 'tank_auth'));
		}
		return FALSE;
	}

	/**
	 * Replace user password (forgotten) with a new one (set by user)
	 * and return some data about it: user_id, username, new_password, email.
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function reset_password($user_id, $new_pass_key, $new_password) {
		if ((strlen($user_id) > 0) AND (strlen($new_pass_key) > 0) AND (strlen($new_password) > 0)) {

			if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

				// Hash password using phpass
				$hasher = new PasswordHash(
						$this->ci->config->item('phpass_hash_strength', 'tank_auth'),
						$this->ci->config->item('phpass_hash_portable', 'tank_auth'));
				$hashed_password = $hasher->HashPassword($new_password);

				if ($this->ci->users->reset_password(
						$user_id, $hashed_password, $new_pass_key, $this->ci->config->item('forgot_password_expire', 'tank_auth'))) { // success
					// Clear all user's autologins
					$this->ci->load->model('tank_auth/user_autologin');
					$this->ci->user_autologin->clear($user->id);

					return array(
						'user_id' => $user_id,
						'username' => $user->username,
						'email' => $user->email,
						'new_password' => $new_password,
					);
				}
			}
		}
		return NULL;
	}

	/**
	 * Change user password (only when user is logged in)
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function change_password($old_pass, $new_pass) {
		$user_id = $this->ci->session->userdata('user_id');

		if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

			// Check if old password correct
			$hasher = new PasswordHash(
					$this->ci->config->item('phpass_hash_strength', 'tank_auth'),
					$this->ci->config->item('phpass_hash_portable', 'tank_auth'));
			if ($hasher->CheckPassword($old_pass, $user->password)) {   // success
				// Hash new password using phpass
				$hashed_password = $hasher->HashPassword($new_pass);

				// Replace old password with new one
				$this->ci->users->change_password($user_id, $hashed_password);
				return TRUE;
			} else { // fail
				$this->error = array('old_password' => 'auth_incorrect_password');
			}
		}
		return FALSE;
	}

	/**
	 * Change user email (only when user is logged in) and return some data about user:
	 * user_id, username, new_email, new_email_key.
	 * The new email cannot be used for login or notification before it is activated.
	 *
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	function set_new_email($new_email, $password) {
		$user_id = $this->ci->session->userdata('user_id');

		if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

			// Check if password correct
			$hasher = new PasswordHash(
					$this->ci->config->item('phpass_hash_strength', 'tank_auth'),
					$this->ci->config->item('phpass_hash_portable', 'tank_auth'));
			if ($hasher->CheckPassword($password, $user->password)) {   // success
				$data = array(
					'user_id' => $user_id,
					'username' => $user->username,
					'new_email' => $new_email,
				);

				if ($user->email == $new_email) {
					$this->error = array('email' => 'auth_current_email');
				} elseif ($user->new_email == $new_email) {  // leave email key as is
					$data['new_email_key'] = $user->new_email_key;
					return $data;
				} elseif ($this->ci->users->is_email_available($new_email)) {
					$data['new_email_key'] = md5(rand() . microtime());
					$this->ci->users->set_new_email($user_id, $new_email, $data['new_email_key'], TRUE);
					return $data;
				} else {
					$this->error = array('email' => 'auth_email_in_use');
				}
			} else { // fail
				$this->error = array('password' => 'auth_incorrect_password');
			}
		}
		return NULL;
	}

	/**
	 * Activate new email, if email activation key is valid.
	 *
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function activate_new_email($user_id, $new_email_key) {
		if ((strlen($user_id) > 0) AND (strlen($new_email_key) > 0)) {
			return $this->ci->users->activate_new_email(
					$user_id, $new_email_key);
		}
		return FALSE;
	}

	/**
	 * Delete user from the site (only when user is logged in)
	 *
	 * @param	string
	 * @return	bool
	 */
	function delete_user($password) {
		$user_id = $this->ci->session->userdata('user_id');

		if (!is_null($user = $this->ci->users->get_user_by_id($user_id, TRUE))) {

			// Check if password correct
			$hasher = new PasswordHash(
					$this->ci->config->item('phpass_hash_strength', 'tank_auth'),
					$this->ci->config->item('phpass_hash_portable', 'tank_auth'));
			if ($hasher->CheckPassword($password, $user->password)) {   // success
				$this->ci->users->delete_user($user_id);
				$this->logout();
				return TRUE;
			} else { // fail
				$this->error = array('password' => 'auth_incorrect_password');
			}
		}
		return FALSE;
	}

	/**
	 * Get error message.
	 * Can be invoked after any failed operation such as login or register.
	 *
	 * @return	string
	 */
	function get_error_message() {
		return $this->error;
	}

	/**
	 * Save data for user's autologin
	 *
	 * @param	int
	 * @return	bool
	 */
	private function create_autologin($user_id) {
		$this->ci->load->helper('cookie');
		$key = substr(md5(uniqid(rand() . get_cookie($this->ci->config->item('sess_cookie_name')))), 0, 16);

		$this->ci->load->model('tank_auth/user_autologin');
		$this->ci->user_autologin->purge($user_id);

		if ($this->ci->user_autologin->set($user_id, md5($key))) {
			set_cookie(array(
				'name' => $this->ci->config->item('autologin_cookie_name', 'tank_auth'),
				'value' => serialize(array('user_id' => $user_id, 'key' => $key)),
				'expire' => $this->ci->config->item('autologin_cookie_life', 'tank_auth'),
			));
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Clear user's autologin data
	 *
	 * @return	void
	 */
	private function delete_autologin() {
		$this->ci->load->helper('cookie');
		if ($cookie = get_cookie($this->ci->config->item('autologin_cookie_name', 'tank_auth'), TRUE)) {

			$data = unserialize($cookie);

			$this->ci->load->model('tank_auth/user_autologin');
			$this->ci->user_autologin->delete($data['user_id'], md5($data['key']));

			delete_cookie($this->ci->config->item('autologin_cookie_name', 'tank_auth'));
		}
	}

	/**
	 * Login user automatically if he/she provides correct autologin verification
	 *
	 * @return	void
	 */
	private function autologin() {
		if (!$this->is_logged_in() AND !$this->is_logged_in(FALSE)) {   // not logged in (as any user)
			$this->ci->load->helper('cookie');
			if ($cookie = get_cookie($this->ci->config->item('autologin_cookie_name', 'tank_auth'), TRUE)) {

				$data = unserialize($cookie);

				if (isset($data['key']) AND isset($data['user_id'])) {

					$this->ci->load->model('tank_auth/user_autologin');
					if (!is_null($user = $this->ci->user_autologin->get($data['user_id'], md5($data['key'])))) {

						// Login user
						$this->ci->session->set_userdata(array(
							'user_id' => $user->id,
							'username' => $user->username,
							'status' => STATUS_ACTIVATED,
						));

						// Renew users cookie to prevent it from expiring
						set_cookie(array(
							'name' => $this->ci->config->item('autologin_cookie_name', 'tank_auth'),
							'value' => $cookie,
							'expire' => $this->ci->config->item('autologin_cookie_life', 'tank_auth'),
						));

						$this->ci->users->update_login_info(
							$user->id, $this->ci->config->item('login_record_ip', 'tank_auth'), $this->ci->config->item('login_record_time', 'tank_auth'));
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

	/**
	 * Check if login attempts exceeded max login attempts (specified in config)
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_max_login_attempts_exceeded($login) {
		if ($this->ci->config->item('login_count_attempts', 'tank_auth')) {
			$this->ci->load->model('tank_auth/login_attempts');
			return $this->ci->login_attempts->get_attempts_num($this->ci->input->ip_address(), $login)
				>= $this->ci->config->item('login_max_attempts', 'tank_auth');
		}
		return FALSE;
	}

	/**
	 * Increase number of attempts for given IP-address and login
	 * (if attempts to login is being counted)
	 *
	 * @param	string
	 * @return	void
	 */
	private function increase_login_attempt($login) {
		if ($this->ci->config->item('login_count_attempts', 'tank_auth')) {
			if (!$this->is_max_login_attempts_exceeded($login)) {
				$this->ci->load->model('tank_auth/login_attempts');
				$this->ci->login_attempts->increase_attempt($this->ci->input->ip_address(), $login);
			}
		}
	}

	/**
	 * Clear all attempt records for given IP-address and login
	 * (if attempts to login is being counted)
	 *
	 * @param	string
	 * @return	void
	 */
	private function clear_login_attempts($login) {
		if ($this->ci->config->item('login_count_attempts', 'tank_auth')) {
			$this->ci->load->model('tank_auth/login_attempts');
			$this->ci->login_attempts->clear_attempts(
				$this->ci->input->ip_address(), $login, $this->ci->config->item('login_attempt_expire', 'tank_auth'));
		}
	}

	/**
	 * <b>Method: get_roles_by_user($user_id)</b>
	 * Obtiene todos los roles asociados a un usario especifico.
	 * @param int $user_id Identificador del usuario del que se quieren obtener los roles.
	 * @return mixed arreglo con los roles del usuario.
	 * @author mrosales <mrosales@rialfi.com>
	 */
	function get_roles_by_user($user_id) {
		$roles = FALSE;
		if (!empty($user_id))
			$roles = $this->ci->roles->getRolesByUser($user_id);
		return $roles;
	}

	/**
	 * <b>Method: get_operations()</b>
	 * Obtiene todas las operaciones asociadas a un rol.
	 * @param int $role_id Identificador del rol del que se quieren obtener las operaciones.
	 * @param string $label Cadena de texto para el indice del arreglo asociativo de las operaciones.
	 * @return mixed arreglo multidimensional con las operaciones que puede hacer el rol.
	 * @author mrosales <mrosales@rialfi.com>
	 */
	function get_operations($role_id, $label) {
		
		if (!empty($role_id) && !empty($label) && is_int($role_id)) {
			$operations = array();
			$oper = $this->ci->operations->getAllOperationsByRole($role_id);
			if ($oper) {
				foreach ($oper as $value2) {
					if (!empty($value2->url))
						$operations[$label][$value2->id] = $value2->url;
				}
				$this->set_sess_operations($oper);
				return $operations;
			} else
				return FALSE;
		} else
			return FALSE;
	}

	/**
	 * <b>Method: get_permissions()</b>
	 * Por Definir.
	 * @author mrosales <mrosales@rialfi.com>
	 */
	function get_permissions() {
		$permissions = array();
		$perms = $this->ci->operations->getAllPermissionsByRole('1');
	}

    /**
     * <b>Method: has_permissions($method,$class,$params)</b>
     * method Metodo que verifica si el usuario actual tiene permisos para acceder a una operacion determinada.
     * @param string $method Cadena de texto con el nombre del metodo al que se desea acceder.
     * @param object $classInstance Instancia del controlador actual para modularizar y obtneer la Cadena de texto con el nombre del controlador en donde fue invocado el metodo.
     * @param array $label arreglo con los parametros pasados al controlador.
     * @author mrosales <mrosales@rialfi.com>
     * @version v-1.2 08/06/2012 11:15 am
     */
    function has_permissions($module, $classInstance, $method, $params) {
//        $this->ci->load->model('adm_maintenance/maintenance_model');
//		if($this->ci->config->item('maintenance')){
//			$this->ci->maintenance_model->cleanSessions();
//		}
//
//		//$maintenance = $this->ci->session->userdata('maintenance');
//		$maintenance = $this->ci->maintenance_model->getNextMaintenance();
//		if(is_array($maintenance)){
//            $date = date('Y-m-d H:i:s');
//            if($maintenance['finicio_maintenance'] <= $date AND $maintenance['ffin_maintenance'] >= $date){
//				$this->ci->load->model('administracion/maintenance_model');
//				$this->ci->maintenance_model->cleanSessions();
//			}
//		}

        if ($this->is_logged_in()) {// logged in
            //verificacion de prefijos:
            $url_exceptions = array(
                '/^CL_/' => array(
                    'method' => 'GET',
                    'ajax'  => true
                ),
                '/^PL_/' => array(
                    'method' => 'POST',
                    'ajax'  => true
                ),
                '/^FU_/' => array(
                    'method' => false,
                    'ajax' => false
                ),
                '/^FD_/' => array( //FD = File Download 
                    'method' => 'GET',
                    'ajax' => false
                ),
            );
            foreach ($url_exceptions as $pattern => $prop) {
                if (preg_match($pattern, $method)) {
                    if (($prop['method'] !== false) && !($_SERVER['REQUEST_METHOD'] == $prop['method'])) {
                        break;
                    }
                    if ($prop['ajax'] && !($this->ci->input->is_ajax_request())) {
                        break;                           
                    }
//                    $this->ci->$method($params);
                    call_user_func_array(array($classInstance, $method), array($params));
                    exit;
                }
            }
            
            if (!$this->ci->session->userdata('role_id'))
                $this->logout();
            $op_url = strtolower($module . '/' . get_class($classInstance)) . '/' . $method;
            $op_url.= ( count($params)) ? '/' . implode('/', $params) : '';
            $permisos = $this->ci->session->userdata('permissions');
            if (empty($permisos)) {
                $this->logout();
                $this->ajaxRedirect('/auth/login/');
            } else if (in_array($op_url, $permisos[$this->ci->session->userdata('role_name')])) {
                if (false != ($op_id = $this->get_operation_id($op_url)))
                    $this->ci->load->library('Dyna_views', array('operation_id' => $op_id, 'operation_url' => $op_url));
                

                call_user_func_array(array($classInstance, $method), array($params));
            } else {
                $this->ajaxRedirect('home/index');
            }
        } elseif ($this->is_logged_in(FALSE)) {// logged in, not activated
            $this->ajaxRedirect('/auth/send_again/');
        } else {
            if ($this->ci->input->is_ajax_request())
                echo 'window.location.reload();';
            else
                $this->ajaxRedirect('/auth/login/');
        }
    }

	/**
	 * <b>Method: is_rol_type($rol_type,$rol_id)</b>
	 * Indica si el $rol_id es del tipo especificado por $roltype.
	 * @param string $rol_type Cadena de texto con el nombre del tipo de rol (ej. CGP,CP,F).
	 * @param int $rol_id Identificador del rol que se quiere verificar.
	 * @author mrosales <mrosales@rialfi.com>
	 */
	function is_rol_type($rol_type, $rol_id) {
		if (!empty($rol_type) && !empty($rol_id)) {
			$result = $this->ci->roles->getRole($rol_id);
			if ($result->chk_role_type == $rol_type)
				return TRUE;
			else
				return FALSE;
		}
		return FALSE;
		//show_error('Los parametros no deben estar vacios');
	}

	/**
	 * <b>Method: get_permissions()</b>
	 * Por Definir.
	 * @author mrosales <mrosales@rialfi.com>
	 */
	function set_information() {
	}

	function build_menu($operations = array()) {

	}

	/**
	 * <b>Method: valid_role($rol_id)</b>
	 * Verifica que el rol que se pasa por parametro le pertenece al usuario que esta logueado.
	 * @param int $role_id Identificador del role que se quiere verificar.
	 * @author mrosales <mrosales@rialfi.com>
	 */
	function valid_role($role_id) {
		$flag = FALSE;
		$roles = $this->get_roles_by_user($this->ci->session->userdata('user_id'));
		foreach ($roles as $value) {
			//echo 'fuera del if';
			if ($value->id == $role_id) {
				//echo 'dentro del if';
				$flag = TRUE;
				break;
			}
		}
		return $flag;
	}

	function set_sess_operations($operations = array()) {
		$this->ci->session->set_userdata('operations', $operations);
	}

	function get_operation_id($op_url) {
		$permissions = $this->ci->session->userdata('permissions');
		$operations_id = array_keys($permissions[$this->ci->session->userdata('role_name')], $op_url);
		if (empty($operations_id) || count($operations_id) > 1)
			return false;
		return ($operations_id[0]);
	}

	/**
	 * <b>Method: getTypeRoleByLogin()</b>
	 * @method		Obtiene el role del usuario especificado por el login en el sistema.
	 * @param		$login
	 * @return		mixed string con el tipo de role del usuario, FALSE en caso de no encontrar coincidencias.
	 * @author		Mirwing Rosales
	 * @version		v-1.0 26/10/11 04:34 PM
	 * */
	function getTypeRoleByLogin($login) {
		return $this->ci->users->getTypeRoleByLogin($login);
	}

	/**
	 * <b>Method: isDeleted()</b>
	 * @method	Verifica si un usuario se encuentra eliminado o no
	 * @param	String $username username del usuario en la tabla rbac.users
	 * @return	Boolean TRUE si el usuario se encuentra eliminado, retorna FALSE en caso contrario
	 * @author	Reynaldo Rojas
	 * @version v-1.0 22/11/11 03:30 PM
	 * */
	function isEliminated($username) {
		return $this->ci->users->getEliminatedStatus($username);
	}

    /**
     * Redirecciona via Javascript si la petición es AJAX (envía window.location=url), si no ejecuta un redirect tradicional de CI
     * @param string $url Url para redireccionar
     * @author malvarez <malvarez@rialfi.com>
     * @version v-1.0 31/08/2012 10:44 am
     */
    public function ajaxRedirect($url) {
//        $gv = $_GET;
//        if (count($gv) > 0) {
//            $url .= '?';
//            foreach ($gv as $k => $v) {
//                $url .= $k . '=' . urlencode($v) . '&';
//            }
//            $url .= 'ccl=true';
//        }
//        //die(print_r($url));
        if ($this->ci->input->is_ajax_request()) {
            die('window.location = "' . $url . '"');
        } else {
            redirect($url);
        }
    }

}

/* End of file Tank_auth.php */
/* Location: ./application/libraries/Tank_auth.php */
