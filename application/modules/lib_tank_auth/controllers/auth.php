<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Auth Class
 * @package		lib_tank_auth
 * @subpackage  	controllers
 * @author		Mirwing Rosales <mrosales@rialfi.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v-1.0 07/09/12 12:13 PM
 * */
class Auth extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $this->lang->load('tank_auth');
        $this->load->model('adm_maintenance/maintenance_model');
    }

    function index() {
        $maintenance = $this->maintenance_model->getNextMaintenance();
        $date = ('Y-m-d H:i:s');
        if (is_array($maintenance))
            if ($maintenance['finicio_maintenance'] <= $date AND $maintenance['ffin_maintenance'] >= $date)
                redirect('maintenance');
        if ($message = $this->session->flashdata('success')) {
            $this->session->flashdata('success', $message);
            $this->load->view('main_layout', array('content' => 'welcome'));
        } else
            redirect('/auth/login/');
    }

    /**
     * <b>Method: login()</b>
     * @method	Permite iniciar sesion en el sistema
     * @author	Mirwing Rosales <mrosales@rialfi.com>
     * @version V-1.0 07/09/12 11:55 AM
     * */
    function login() {

        // Codigo Maintenance ////////////////////////////////////
        //    	if($this->config->item('maintenance')) redirect('maintenance');
        //    	$maintenance = $this->maintenance_model->getNextMaintenance();
        //
		//        if(is_array($maintenance)){
        //          $date = date('Y-m-d H:i:s');
        //          if($maintenance['finicio_maintenance'] <= $date AND $maintenance['ffin_maintenance'] >= $date)
        //              redirect('maintenance');
        //
		//        }
        // Codigo Maintenance ////////////////////////////////////
        // Condicion que se satisface cuando el usuario se encuentra en sesion y procesa
        // el formulario relacionado a la seleccion de roles
        if ($this->tank_auth->is_logged_in()) {

            // Identificador del rol seleccionado en el formulario
            $role_id = $this->input->post('select_rol');

            // Verificar si el rol seleccionado es valido dentro del compendio de roles asociados al usuario
            if (!$this->tank_auth->valid_role($role_id)) {
                tankAuthResponse(true, 'no_valido', $this->lang->line('auth_invalid_role'));
            }

            // Verificar si el rol es un numero valido
            elseif (!empty($role_id) and intval($role_id)) {

                // Definicion de variables de sesion relacionadas al usuario y tipo de rol
                $role = $this->roles->getRole($role_id);
                $this->session->set_userdata('permissions', $this->tank_auth->get_operations((int) $role_id, $role->_name));
                $this->session->set_userdata('role_name', $role->_name);
                $this->session->set_userdata('role_id', $role->id);

                //Accesos a log
                //@todo revisar la mejor forma de ahc el acceso.
                $this->logger->createLog(ACCESS);

                tankAuthResponse(true, 'valido', $this->lang->line('auth_message_registration_completed_3'), array('user_id' => $this->session->userdata('user_id')));
            }

            // Si no es un numero valido debe enviarse al inicio de sesion
            else {
                $this->tank_auth->logout();
            }

            // Si no se encuentra ninguna petición en proceso se carga la vista principal de login
            // se verifica la existencia del identificador del rol del usuario. En caso de no existir
            // se envia al inicio de sesion
            if (!$this->input->is_ajax_request()) {
                $role_id = $this->session->userdata('role_id');
                if (empty($role_id))
                    $this->tank_auth->logout();

                redirect('');
            }
        }

        // Si el usuario se encuentra en sesion pero no esta activo, debe ser enviado al inicio de sesion
        elseif ($this->tank_auth->is_logged_in(FALSE)) {
            $this->tank_auth->logout();
        }

        // Si el usuario no se encuentra en sesion
        else {

            // Se indica si el usuario puede hacer sesion con el nombre de usuario o email
            $data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth') AND
                $this->config->item('use_username', 'tank_auth'));
            $data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');

            // Definicion de reglas generales de validacion del formulario login
            $this->form_validation->set_rules('login', 'Login', 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('remember', 'Remember me', 'integer');

            // Se obtiene el login para verificar la cantidad de intentos posibles para acceder al sistema
            if ($this->config->item('login_count_attempts', 'tank_auth') AND ($login = $this->input->post('login'))) {
                $login = $this->security->xss_clean($login);
            } else {
                $login = '';
            }

            // Implementacion de recaptcha en caso de establecer limite de intentos para acceder al sistema
            $data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');

            // Definicion de reglas de validacion para recapcha
            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
                if ($data['use_recaptcha'])
                    $this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
                else
                    $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
            }

            // Defincion de arreglo de errores en validacion
            $data['errors'] = array();

            // Si el formulario no presenta errores de validacion
            if ($this->form_validation->run()) {


                // Verificar si el usuario se encuentra eliminado
                if ($this->tank_auth->isEliminated($this->form_validation->set_value('login'))) {
                    $this->tank_auth->logout();
                    tankAuthResponse(false, 'no_valido', $this->lang->line('auth_invalid_user_password'), array('user_id' => $this->session->userdata('user_id')));
                }

                // Si el usuario puedde acceder al sistema de acuerdo a los parametros establecidos en el metodo
                // login (ver libreria tank_auth)
                if ($this->tank_auth->login(
                        $this->form_validation->set_value('login'), $this->form_validation->set_value('password'), $this->form_validation->set_value('remember'), $data['login_by_username'], $data['login_by_email'])
                ) {

                    // Obtener roles disponibles por usuario
                    $roles = $this->tank_auth->get_roles_by_user($this->session->userdata('user_id'));

                    //Contiene el total de roles asociados a usuario.
                    $total_role = 0;
                    if ($roles != FALSE)
                        $total_role = count($roles);

                    // Si el usuario posee mas de un rol asociado se debe generar un listado de roles.
                    if ($total_role > 1) {
                        foreach ($roles as $value)
                            $array[$value->id] = $value->_name;

                        $user_roles = TRUE;
                    }

                    //Si posee un unico rool asociado o activo.
                    elseif ($total_role == 1) {
                        // Definicion de variables de sesion relacionadas al usuario y tipo de rol
                        $this->session->set_userdata('permissions', $this->tank_auth->get_operations((int) $roles[0]->id, $roles[0]->_name));
                        $this->session->set_userdata('role_name', $roles[0]->_name);
                        $this->session->set_userdata('role_id', $roles[0]->id);

                        //Accesos a log
                        //@todo revisar la mejor forma de ahc el acceso.
                        $this->logger->createLog(ACCESS);

                        tankAuthResponse(true, 'directo', $this->lang->line('auth_message_registration_completed_3'), array('user_id' => $this->session->userdata('user_id')));
                    }

                    // Si no tiene rol.
                    else {
                        $this->tank_auth->logout();
                        tankAuthResponse(false, 'no_valido', $this->lang->line('auth_invalid_user_password'), array('user_id' => $this->session->userdata('user_id')));
                    }
                }

                // En caso de no poder acceder al sistema de acuerdo a los parametros establecidos en el metodo
                // login (ver libreria tank_auth)
                else {

                    tankAuthResponse(false, 'no_valido', $this->lang->line('auth_invalid_user_password'));

                    $errors = $this->tank_auth->get_error_message();

                    // Si el usuario se encuentra suspendido
                    if (isset($errors['banned'])) {
                        $this->_show_message($this->lang->line('auth_message_banned') . ' ' . $errors['banned']);
                    }

                    // Si el usuario no se encuentra activo
                    elseif (isset($errors['not_activated'])) {
                        $this->tank_auth->logout();
                    }

                    // Si se producen errores relacionados al proceso de la libreria tank_auth
                    else {
                        foreach ($errors as $k => $v)
                            $data['errors'][$k] = $this->lang->line($v);
                    }
                }
            }

            // Control de implementacion de captcha
            $data['show_captcha'] = FALSE;

            // Si excede la cantidad de intentos de acceso al sistema debe generar el captcha o recaptcha
            if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
                $data['show_captcha'] = TRUE;
                if ($data['use_recaptcha']) {
                    $data['recaptcha_html'] = $this->_create_recaptcha();
                } else {
                    $data['captcha_html'] = $this->_create_captcha();
                }
            }

            // Si existen multiples roles para un usuario se debe preparar la lista de roles disponibles
            if ($user_roles) {

                // Creacion de la lista de roles
                $role_type = array();
                foreach ($array as $value => $label)
                    array_push($role_type, array('value' => $value, 'label' => $label));

                tankAuthResponse(true, 'valido', '', array('user_id' => $this->session->userdata('user_id'), 'name' => $this->session->userdata('nombre'), 'last_name' => $this->session->userdata('apellidos'), 'role_type' => $role_type));
            }

            // Si no se encuentra ninguna petición en proceso se carga la vista principal de login			
            elseif (!$this->input->is_ajax_request()) {
                $this->load->view('lib_tank_auth/auth/login.js.php', array('maintenance' => $maintenance, 'view' => 'auth/form_login.js.php'));
            }
        }
    }

    /**
     * Logout user
     *
     * @return void
     */
    function logout() {
        $this->tank_auth->logout();

        $this->_show_message($this->lang->line('auth_message_logged_out'));
    }

    /**
     * Register user on the site
     *
     * @return void
     */
    function register() {
        if ($this->tank_auth->is_logged_in()) {  // logged in
            redirect('');
        } elseif ($this->tank_auth->is_logged_in(FALSE)) {   // logged in, not activated
            redirect('/auth/send_again/');
        } elseif (!$this->config->item('allow_registration', 'tank_auth')) { // registration is off
            $this->_show_message($this->lang->line('auth_message_registration_disabled'));
        } else {
            redirect(''); // Temporal para evitar el registro de personas
            $use_username = $this->config->item('use_username', 'tank_auth');
            if ($use_username) {
                $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|min_length[' . $this->config->item('username_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('username_max_length', 'tank_auth') . ']|alpha_dash');
            }
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');

            $captcha_registration = $this->config->item('captcha_registration', 'tank_auth');
            $use_recaptcha = $this->config->item('use_recaptcha', 'tank_auth');
            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
                } else {
                    $this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
                }
            }
            $data['errors'] = array();

            $email_activation = $this->config->item('email_activation', 'tank_auth');

            if ($this->form_validation->run()) { // validation ok
                if (!is_null($data = $this->tank_auth->create_user(
                        $use_username ? $this->form_validation->set_value('username') : '', $this->form_validation->set_value('email'), $this->form_validation->set_value('password'), $email_activation))) {  // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    if ($email_activation) {  // send "activate" email
                        $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                        $this->_send_email('activate', $data['email'], $data);

                        unset($data['password']); // Clear password (just for any case)

                        $this->_show_message($this->lang->line('auth_message_registration_completed_1'));
                    } else {
                        if ($this->config->item('email_account_details', 'tank_auth')) { // send "welcome" email
                            $this->_send_email('welcome', $data['email'], $data);
                        }
                        unset($data['password']); // Clear password (just for any case)

                        $this->_show_message($this->lang->line('auth_message_registration_completed_2') . ' ' . anchor('/auth/login/', 'Login'));
                    }
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            if ($captcha_registration) {
                if ($use_recaptcha) {
                    $data['recaptcha_html'] = $this->_create_recaptcha();
                } else {
                    $data['captcha_html'] = $this->_create_captcha();
                }
            }
            $data['use_username'] = $use_username;
            $data['captcha_registration'] = $captcha_registration;
            $data['use_recaptcha'] = $use_recaptcha;
            $data['content'] = 'auth/register_form';
            $this->load->view('main_layout', $data);
        }
    }

    /**
     * Send activation email again, to the same or new email address
     *
     * @return void
     */
    function send_again() {
        if (!$this->tank_auth->is_logged_in(FALSE)) { // not logged in or activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if (!is_null($data = $this->tank_auth->change_email(
                        $this->form_validation->set_value('email')))) {   // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');
                    $data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

                    $this->_send_email('activate', $data['email'], $data);

                    $this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $data['content'] = 'auth/send_again_form';
            $this->load->view('main_layout', $data);
        }
    }

    /**
     * Activate user account.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function activate() {
        $user_id = $this->uri->segment(3);
        $new_email_key = $this->uri->segment(4);

        // Activate user
        if ($this->tank_auth->activate_user($user_id, $new_email_key)) {  // success
            $this->tank_auth->logout();
            $this->_show_message($this->lang->line('auth_message_activation_completed') . ' ' . anchor('/auth/login/', 'Login'));
        } else {  // fail
            $this->_show_message($this->lang->line('auth_message_activation_failed'));
        }
    }

    /**
     * <b>Method: forgot_password()</b>
     * @method	Permite regenerar la contraseña enviando un formulario via email al usuario 
     * @author	Mirwing Rosales <mrosales@rialfi.com>
     * @version V-1.0 07/09/12 11:55 AM
     * */
    function forgot_password() {

        // Si el usuario se encuentra en sesion
        if ($this->tank_auth->is_logged_in()) {
            redirect('');
        }

        // Si el usuario se encuentra en sesion pero no esta activo, debe ser enviado al inicio de sesion
        elseif ($this->tank_auth->is_logged_in(FALSE)) {
            redirect('/auth/send_again/');
        }

        // Si el usuario no se encuentra en sesion
        else {

            // Si no se ha procesado el formulario se debe enviar al panel principal de envio de correo
            if ($_SERVER['REQUEST_METHOD'] != 'POST') {
                $this->load->view('lib_tank_auth/auth/login.js.php', array('view' => 'auth/form_forgot_password.js.php'));
            }

            // Si se proceso el formulario se debe enviar el correo para la recuperacion de contraseña
            else {

                // Definicion de reglas de validacion
                $this->form_validation->set_rules('login', 'Login &oacute; Correo', 'trim|required|xss_clean|sql_injection');

                $data['errors'] = array();

                // Si el formulario no presenta errores de validacion
                if ($this->form_validation->run()) {

                    // Se debe verificar si al momento de la recuperacion el usuario puede recuperar contraseña
                    if (!is_null($data = $this->tank_auth->forgot_password($this->form_validation->set_value('login')))) {

                        $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                        // Envio de correo para activacion de contraseña
                        $this->_send_email('forgot_password', $data['email'], $data);
                        $msn = $this->lang->line('auth_message_new_password_sent');
                        $situation = "directo";
                    } else {
                        $errors = $this->tank_auth->get_error_message();
                        $array_error = array();
                        foreach ($errors as $msg_error)
                            array_push($array_error, $this->lang->line($msg_error));
                        $msn = array_shift($array_error);
                        $situation = "no_valido";
                    }
                }

                // Si existe errores de validacion en el formulario se debe enviar el conjunto
                // de mensajes de validacion
                else {
                    $error_validacion = validation_errors();
                    $error_validacion = preg_replace("[\n|\r|\n\r]", " ", $error_validacion);
                    $msn = $error_validacion;
                    $situation = "no_valido";
                }

                tankAuthResponse(true, $situation, $msn);
            }
        }
    }

    /**
     * <b>Method: reset_password()</b>
     * @method	Permite reiniciar la contraseña de un usuario
     * @author	Mirwing Rosales <mrosales@rialfi.com>
     * @version V-1.0 07/09/12 11:55 AM
     * */
    function reset_password() {

        // Si no se ha procesado el formulario se debe enviar al panel principal de envio de correo
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {

            // Se obtiene el identificador de usuario y el token enviados desde la url para verificar
            // la autenticidad del usuario
            $data['user_id'] = $this->uri->segment(4);
            $data['new_pass_key'] = $this->uri->segment(5);

            // Se verifica si la contraseña puede ser generada
            if (!$this->tank_auth->can_reset_password($data['user_id'], $data['new_pass_key'])) {
                $data['view'] = 'auth/form_expired_password_message.js.php';
                $data['msg'] = $this->lang->line('auth_message_new_password_failed');
            }
            else
                $data['view'] = 'auth/form_reset_password.js.php';

            $this->load->view('lib_tank_auth/auth/login.js.php', $data);
        }

        // Si se proceso el formulario
        else {

            // Definicion de reglas de validacion
            $this->form_validation->set_rules('new_password', 'Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
            $this->form_validation->set_rules('confirm_new_password', 'Confirmar Password', 'trim|required|xss_clean|matches[new_password]');

            // Si el formulario no presenta errores de validacion
            if ($this->form_validation->run()) {

                // Si no existen errores al momento de generar la contraseña
                if (!is_null($data = $this->tank_auth->reset_password($this->input->post('user_id'), $this->input->post('new_pass_key'), $this->input->post('new_password')))) { // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');
                    $this->_send_email('reset_password', $data['email'], $data);
                    $msn = $this->lang->line('auth_message_new_password_activated');
                    $situation = "directo";
                }

                // Si existe algun error en la generacion de la contraseña
                else {
                    $msn = $this->lang->line('auth_message_new_password_failed');
                    $situation = "no_valido";
                }
            }

            // Si existe errores de validacion en el formulario se debe enviar el conjunto
            // de mensajes de validacion
            else {

                // Verificar si se puede activar al usuario con el password (si no se encuentra activo)
                if ($this->config->item('email_activation', 'tank_auth')) {
                    $this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
                }

                $error_validacion = validation_errors();
                $error_validacion = preg_replace("[\n|\r|\n\r]", " ", $error_validacion);
                $msn = $error_validacion;
                $situation = "no_valido";
            }

            tankAuthResponse(true, $situation, $msn);
        }
    }

    /**
     * Change user password
     *
     * @return void
     */
    function change_password() {
        if (!$this->tank_auth->is_logged_in()) { // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length[' . $this->config->item('password_min_length', 'tank_auth') . ']|max_length[' . $this->config->item('password_max_length', 'tank_auth') . ']|alpha_dash');
            $this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if ($this->tank_auth->change_password(
                        $this->form_validation->set_value('old_password'), $this->form_validation->set_value('new_password'))) { // success
                    $this->_show_message($this->lang->line('auth_message_password_changed'));
                } else { // fail
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $data['content'] = 'auth/change_password_form';
            $this->load->view('main_layout', $data);
        }
    }

    /**
     * Change user email
     *
     * @return void
     */
    function change_email() {
        if (!$this->tank_auth->is_logged_in()) { // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if (!is_null($data = $this->tank_auth->set_new_email(
                        $this->form_validation->set_value('email'), $this->form_validation->set_value('password')))) {   // success
                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with new email address and its activation link
                    $this->_send_email('change_email', $data['new_email'], $data);

                    $this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));
                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $data['content'] = 'auth/change_email_form';
            $this->load->view('main_layout', $data);
        }
    }

    /**
     * Replace user email with a new one.
     * User is verified by user_id and authentication code in the URL.
     * Can be called by clicking on link in mail.
     *
     * @return void
     */
    function reset_email() {
        $user_id = $this->uri->segment(3);
        $new_email_key = $this->uri->segment(4);

        // Reset email
        if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) { // success
            $this->tank_auth->logout();
            $this->_show_message($this->lang->line('auth_message_new_email_activated') . ' ' . anchor('/auth/login/', 'Login'));
        } else {  // fail
            $this->_show_message($this->lang->line('auth_message_new_email_failed'));
        }
    }

    /**
     * Delete user from the site (only when user is logged in)
     *
     * @return void
     */
    function unregister() {
        if (!$this->tank_auth->is_logged_in()) { // not logged in or not activated
            redirect('/auth/login/');
        } else {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

            $data['errors'] = array();

            if ($this->form_validation->run()) { // validation ok
                if ($this->tank_auth->delete_user(
                        $this->form_validation->set_value('password'))) {  // success
                    $this->_show_message($this->lang->line('auth_message_unregistered'));
                } else { // fail
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)
                        $data['errors'][$k] = $this->lang->line($v);
                }
            }
            $data['content'] = 'auth/unregister_form';
            $this->load->view('main_layout', $data);
        }
    }

    /**
     * Show info message
     *
     * @param	string
     * @return	void
     */
    function _show_message($message) {
        $this->session->set_flashdata('success', $message);
        redirect('auth/');
    }

    /**
     * Send email message of given type (activate, forgot_password, etc.)
     *
     * @param	string
     * @param	string
     * @param	array
     * @return	void
     */
    function _send_email($type, $email, &$data) {
        $this->load->library('email');
        $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->to($email);
        $this->email->subject(sprintf($this->lang->line('auth_subject_' . $type), $this->config->item('website_name', 'tank_auth')));
        $this->email->message($this->load->view('email/' . $type . '-html', $data, TRUE));
        $this->email->set_alt_message($this->load->view('email/' . $type . '-txt', $data, TRUE));
        $this->email->send();
    }

    /**
     * Create CAPTCHA image to verify user as a human
     *
     * @return	string
     */
    function _create_captcha() {
        $this->load->helper('captcha');

        $cap = create_captcha(array(
            'img_path' => './' . $this->config->item('captcha_path', 'tank_auth'),
            'img_url' => base_url() . $this->config->item('captcha_path', 'tank_auth'),
            'font_path' => './' . $this->config->item('captcha_fonts_path', 'tank_auth'),
            'font_size' => $this->config->item('captcha_font_size', 'tank_auth'),
            'img_width' => $this->config->item('captcha_width', 'tank_auth'),
            'img_height' => $this->config->item('captcha_height', 'tank_auth'),
            'show_grid' => $this->config->item('captcha_grid', 'tank_auth'),
            'expiration' => $this->config->item('captcha_expire', 'tank_auth'),
            ));

        // Save captcha params in session
        $this->session->set_flashdata(array(
            'captcha_word' => $cap['word'],
            'captcha_time' => $cap['time'],
        ));

        return $cap['image'];
    }

    /**
     * Callback function. Check if CAPTCHA test is passed.
     *
     * @param	string
     * @return	bool
     */
    function _check_captcha($code) {
        $time = $this->session->flashdata('captcha_time');
        $word = $this->session->flashdata('captcha_word');

        list($usec, $sec) = explode(" ", microtime());
        $now = ((float) $usec + (float) $sec);

        if ($now - $time > $this->config->item('captcha_expire', 'tank_auth')) {
            $this->form_validation->set_message('_check_captcha', $this->lang->line('auth_captcha_expired'));
            return FALSE;
        } elseif (($this->config->item('captcha_case_sensitive', 'tank_auth') AND
            $code != $word) OR
            strtolower($code) != strtolower($word)) {
            $this->form_validation->set_message('_check_captcha', $this->lang->line('auth_incorrect_captcha'));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Create reCAPTCHA JS and non-JS HTML to verify user as a human
     *
     * @return	string
     */
    function _create_recaptcha() {
        $this->load->helper('recaptcha');

        // Add custom theme so we can get only image
        $options = "<script>var RecaptchaOptions = {theme: 'custom',lang: 'es', custom_theme_widget: 'recaptcha_widget'};</script>\n";

        // Get reCAPTCHA JS and non-JS HTML
        $html = recaptcha_get_html($this->config->item('recaptcha_public_key', 'tank_auth'));

        return $options . $html;
    }

    /**
     * Callback function. Check if reCAPTCHA test is passed.
     *
     * @return	bool
     */
    function _check_recaptcha() {
        $this->load->helper('recaptcha');

        $resp = recaptcha_check_answer($this->config->item('recaptcha_private_key', 'tank_auth'), $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

        if (!$resp->is_valid) {
            $this->form_validation->set_message('_check_recaptcha', $this->lang->line('auth_incorrect_captcha'));
            return FALSE;
        }
        return TRUE;
    }

}

/* END Auth      */
/* END of file auth.php */
/* Location: ./application/modules/lib_tank_auth/controllers/auth.php */

