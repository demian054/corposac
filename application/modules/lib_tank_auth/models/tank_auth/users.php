<?php

if (!defined('BASEPATH'))
	exit('Acceso Denegado');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 * @package	Tank_auth
 * @author	Ilya Konyukhov (http://konyukhov.com/soft/)
 */
class Users extends CI_Model {

	private $table_name = 'users';   // user accounts
	private $profile_table_name = 'user_profiles'; // user profiles
	private $role_user_table_name = 'users_roles';

	function __construct() {
		parent::__construct();

		$ci = & get_instance();
		$this->table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->table_name;
		$this->profile_table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->profile_table_name;
		$this->role_user_table_name = $ci->config->item('db_table_prefix', 'tank_auth') . $this->role_user_table_name;
	}

	/**
	 * Get user record by Id
	 *
	 * @param	int
	 * @param	bool
	 * @return	object
	 */
	function get_user_by_id($user_id, $activated) {
		$this->db->where('id', $user_id);
		$this->db->where('activated', $activated ? 1 : 0);
		$this->db->where('deleted', '0');

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1)
			return $query->row();
		return NULL;
	}

	/**
	 * Get user record by login (username or email)
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_login($login) {
		$login = strtolower($login);
		$sql = "SELECT * FROM $this->table_name
                        WHERE deleted = '0'
                        AND ( LOWER(username) = '$login'
                        OR LOWER(email) = '$login'
                        )";
		$query = $this->db->query($sql);

		if ($query->num_rows() == 1)
			return $query->row();
		return NULL;
	}

	/**
	 * Get user record by username
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_username($username) {
		$this->db->where('LOWER(username)=', strtolower($username));
		$this->db->where('deleted', '0');

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1)
			return $query->row();
		return NULL;
	}

	/**
	 * Get user record by email
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_email($email) {
		$this->db->where('LOWER(email)=', strtolower($email));
		$this->db->where('deleted', '0');

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1)
			return $query->row();
		return NULL;
	}

	/**
	 * Check if username available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_username_available($username, $user_id = FALSE) {
		$this->db->select('1', FALSE);
		$this->db->where('deleted', '0');
		if (!empty($user_id))
			$this->db->where('id != ', $user_id);
		
		$this->db->where('LOWER(username)=', strtolower($username));
		$this->db->where('deleted', '0');

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Check if email available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_email_available($email, $user_id = FALSE) {
		$this->db->select('1', FALSE);
		$this->db->where('deleted', '0');
		if (!empty($user_id))
			$this->db->where('id != ', $user_id);

		$this->db->where('LOWER(email)=', strtolower($email));
		$this->db->or_where('LOWER(new_email)=', strtolower($email));
		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Create new user record
	 *
	 * @param	array
	 * @param	bool
	 * @return	array
	 */
	function create_user($data, $activated = TRUE) {
		$data['created'] = date('Y-m-d H:i:s');
		$data['activated'] = $activated ? 1 : 0;

		if ($this->db->insert($this->table_name, $data)) {
			$user_id = $this->db->insert_id();
			//if ($activated)	$this->create_profile($user_id);
			return array('user_id' => $user_id);
		}
		return NULL;
	}

	/**
	 * Activate user if activation key is valid.
	 * Can be called for not activated users only.
	 *
	 * @param	int
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function activate_user($user_id, $activation_key, $activate_by_email) {
		$this->db->select('1', FALSE);
		$this->db->where('id', $user_id);
		if ($activate_by_email) {
			$this->db->where('new_email_key', $activation_key);
		} else {
			$this->db->where('new_password_key', $activation_key);
		}
		$this->db->where('activated', 0);
		$query = $this->db->get($this->table_name);

		if ($query->num_rows() == 1) {

			$this->db->set('activated', 1);
			$this->db->set('new_email_key', NULL);
			$this->db->where('id', $user_id);
			$this->db->update($this->table_name);

			//$this->create_profile($user_id);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Purge table of non-activated users
	 *
	 * @param	int
	 * @return	void
	 */
	function purge_na($expire_period = 172800) {
		$this->db->where('activated', 0);
		$this->db->where('extract(epoch FROM (created)) <', time() - $expire_period);
		$this->db->delete($this->table_name);
	}

	/**
	 * Delete user record
	 *
	 * @param	int
	 * @return	bool
	 */
	function delete_user($user_id) {
		$this->db->where('id', $user_id);
		$this->db->delete($this->table_name);
		if ($this->db->affected_rows() > 0) {
			//$this->delete_profile($user_id);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Set new password key for user.
	 * This key can be used for authentication when resetting user's password.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function set_password_key($user_id, $new_pass_key) {
		$this->db->set('new_password_key', $new_pass_key);
		$this->db->set('new_password_requested', date('Y-m-d H:i:s'));
		$this->db->where('id', $user_id);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Check if given password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	int
	 * @return	void
	 */
	function can_reset_password($user_id, $new_pass_key, $expire_period = 900) {
		$this->db->select('1', FALSE);
		$this->db->where('id', $user_id);
		$this->db->where('new_password_key', $new_pass_key);
		$this->db->where('extract(epoch FROM(new_password_requested)) >', time() - $expire_period);

		$query = $this->db->get($this->table_name);
		if ($query)
			$query = $query->num_rows() == 1;

		return $query;
	}

	/**
	 * Change user password if password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	function reset_password($user_id, $new_pass, $new_pass_key, $expire_period = 900) {
		$this->db->set('password', $new_pass);
		$this->db->set('new_password_key', NULL);
		$this->db->set('new_password_requested', NULL);
		$this->db->where('id', $user_id);
		$this->db->where('new_password_key', $new_pass_key);
		$this->db->where('extract(epoch FROM(new_password_requested)) >=', time() - $expire_period);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Change user password
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function change_password($user_id, $new_pass) {
		$this->db->set('password', $new_pass);
		$this->db->where('id', $user_id);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Set new email for user (may be activated or not).
	 * The new email cannot be used for login or notification before it is activated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function set_new_email($user_id, $new_email, $new_email_key, $activated) {
		$this->db->set($activated ? 'new_email' : 'email', $new_email);
		$this->db->set('new_email_key', $new_email_key);
		$this->db->where('id', $user_id);
		$this->db->where('activated', $activated ? 1 : 0);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Activate new email (replace old email with new one) if activation key is valid.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function activate_new_email($user_id, $new_email_key) {
		$this->db->set('email', 'new_email', FALSE);
		$this->db->set('new_email', NULL);
		$this->db->set('new_email_key', NULL);
		$this->db->where('id', $user_id);
		$this->db->where('new_email_key', $new_email_key);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Update user login info, such as IP-address or login time, and
	 * clear previously generated (but not activated) passwords.
	 *
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	function update_login_info($user_id, $record_ip, $record_time) {
		$this->db->set('new_password_key', NULL);
		$this->db->set('new_password_requested', NULL);

		if ($record_ip)
			$this->db->set('last_ip', $this->input->ip_address());
		if ($record_time)
			$this->db->set('last_login', date('Y-m-d H:i:s'));

		$this->db->where('id', $user_id);
		$this->db->update($this->table_name);
	}

	/**
	 * Ban user
	 *
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function ban_user($user_id, $reason = NULL) {
		$this->db->where('id', $user_id);
		$this->db->update($this->table_name, array(
			'banned' => 1,
			'ban_reason' => $reason,
		));
	}

	/**
	 * Unban user
	 *
	 * @param	int
	 * @return	void
	 */
	function unban_user($user_id) {
		$this->db->where('id', $user_id);
		$this->db->update($this->table_name, array(
			'banned' => 0,
			'ban_reason' => NULL,
		));
	}

	/**
	 * Create an empty profile for a new user
	 *
	 * @param	int
	 * @return	bool
	 */
	/* private function create_profile($user_id)
	  {
	  $this->db->set('user_id', $user_id);
	  return $this->db->insert($this->profile_table_name);
	  }
	 */
	/**
	 * Delete user profile
	 *
	 * @param	int
	 * @return	void
	 */
	/* private function delete_profile($user_id)
	  {
	  $this->db->where('user_id', $user_id);
	  $this->db->delete($this->profile_table_name);
	  } */


	/* function get_roles_by_user($user_id){
	  $this->db->select($this'')
	  } */

	/**
	 * <b>Method: getTypeRoleByLogin()</b>
	 * @method		Obtiene el role del usuario especificado por el login en el sistema.
	 * @param		$login
	 * @return		mixed string con el tipo de role del usuario, FALSE en caso de no encontrar coincidencias.
	 * @author		Mirwing Rosales
	 * @version		v-1.0 26/10/11 04:24 PM
	 * */
	function getTypeRoleByLogin($login) {
		if (empty($login))
			return FALSE;
		$login = strtolower($login);
		$this->db->select('roles.chk_role_type');
		$this->db->from('rbac.users');
		$this->db->from('rbac.users_roles');
		$this->db->from('rbac.roles');
		$this->db->where('users.deleted', '0');
		$this->db->where('users_roles.rol_id = roles.id');
		$this->db->where('users_roles.user_id = users.id');
		$this->db->where("(users.email = '$login' OR users.username = '$login')");

		$result = $this->db->get()->row();

		if (!empty($result))
			return $result->chk_role_type;
		else
			return FALSE;
	}

	/**
	 * <b>Method: isDeleted()</b>
	 * @method	Retorna el estado de un usario en terminos de si se encuentra eliminado o no
	 * @param	String $username username del usuario en la tabla rbac.users
	 * @return	Boolean TRUE si el usuario se encuentra eliminado, retorna FALSE en caso contrario
	 * @author	Reynaldo Rojas
	 * @version v-1.0 22/11/11 03:28 PM
	 * */
	function getEliminatedStatus($username) {
		//sacamos los usuarios elimiandos
		$this->db->where('deleted', '1');
		$this->db->where('username', $username);
		$result1 = $this->db->get('rbac.users');

		$cantEliminados = $result1->num_rows();

		//sacamos cantidad de usuarios no eliminados, solo puede ser 1
		$this->db->where('deleted', '0');
		$this->db->where('username', $username);
		$result2 = $this->db->get('rbac.users');

		$cantNoEliminados = $result2->num_rows();

		if ($cantEliminados > 0 AND $cantNoEliminados == 1) {
			return 0;
		} elseif ($cantEliminados > 0 AND $cantNoEliminados == 0) {
			return 1;
		} elseif ($cantEliminados == 0 AND $cantNoEliminados == 1) {
			return 0;
		}
	}

	function getNoEliminado($username) {
		//sacamos los usuarios elimiandos
		$this->db->where('deleted', '0');
		$this->db->where('username', $username);
		$result1 = $this->db->get('rbac.users');
		$cantEliminados = $result1->num_rows();
		return ($cantEliminados == 1);
	}

}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */