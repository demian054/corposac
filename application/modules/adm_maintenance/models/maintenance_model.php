<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * maintenance_model
 * @package		adminstracion
 * @subpackage	models
 * @author		Mirwing Rosales, Jose Rodriguez
 * @copyright	Por definir
 * @license		Por definir
 * @version		v-1.0 29/05/2012 05:08 pm
**/


class maintenance_model extends CI_Model{

	private $table = '';
	function __construct() {
		parent::__construct();
		$this->table = 'rbac.maintenance';
	}

	/**
	 * <b>Method: 	create()</b>
	 * @method		Metodo que permite la creacion de una ventana de manteniminento.
	 * @param		array $data Arreglo de datos con informacion a cargar en la tabla.
	 * @return		mixed identificador de registro en caso de crearlo, false en caso contrario.
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 29/05/2012 05:13 pm
	 */
	public function create ($data) {
			if(empty($data) AND !is_array($data))
				return FALSE;
			$this->format($data);
			if($this->db->insert($this->table, $data))
				return $this->db->insert_id();
			else
				return FALSE;
	}

	/**
	 * <b>Method: 	getAll()</b>
	 * @method		Obtiene todas los registros para mostrar en el grid.
	 * @param		integer $start Registro por se inicia la busqueda.
	 * @param		integer $limit Limite de registros a consultar.
	 * @param		string  $search_field cadena de texto a buscar.
	 * @param		boolean $today indica si se filtran los resultados mayores a hoy.
	 * @param		boolean $count indica si se quiere contar la cantidad de registros.
	 * @return		array arraglo dque contiene los datos consultados en BD.
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 31/05/2012 09:47 am
	 */
	public function getAll ($start,$limit,$search_field, $count = FALSE, $today = FALSE) {
			if($count){
				$this->db->select('COUNT(id)', FALSE);
			}else{
				
				$this->db->select('rbac.maintenance.id');
				$this->db->select('rbac.maintenance.finicio_window');
				$this->db->select('rbac.maintenance.ffin_window');
				$this->db->select('rbac.maintenance.finicio_maintenance');
				$this->db->select('rbac.maintenance.ffin_maintenance');
				$this->db->select('rbac.maintenance.message');
				if($today){
					$this->db->where($this->table.'.ffin_maintenance >= \''. date('Y-m-d H:i').'\'');
					if($this->session->userdata('maintenance_id')){
						$this->db->where('id != '.$this->session->userdata('maintenance_id'));
					}
				} else {
					$this->db->select('(rbac.users.first_name || \' \' || rbac.users.last_name) AS nombre_completo');
					$this->db->join('rbac.users', $this->table.'.user_id = rbac.users.id');
					$this->db->order_by('finicio_window','desc'); 
					$this->db->limit($limit, $start);
				}
			}
			$this->db->from($this->table);
			$this->db->where($this->table.'.eliminado', '0');
			
			if (!empty($search_field)) {
				$this->db->like('message',$search_field);
				//$this->db->or_like('rbac.users.first_name',$search_field);
				//$this->db->or_like('rbac.users.last_name',$search_field);
			}

			if ($count) {
				$query = $this->db->get()->row();
				return $query->count;
			}

			$result = $this->db->get();
			return $result ? $result->result() : FALSE;
	}

	/**
	 * <b>Method: 	getById()</b>
	 * @method		Metodo para extraer un objeto relaciondo con su identificador
	 * @param		inteegr $id Identificador del registro.
	 * @return		array
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 31/05/2012 09:55 am
	 */
	public function getById ($id) {
			if(empty($id))
					return FALSE;
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('id', $id);
			$result = $this->db->get();
			return $result ? $result->row_array() : FALSE;
	}

	/**
	 * <b>Method: 	update()</b>
	 * @method		Actualiza un regitro a partir de un identificador.
	 * @param		interger $id Identificador de registro.
	 * @param		array    $data arreglo con datos a ser actualizados.
	 * @return		boolean
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 31/05/2012 09:58 am
	 */
	public function update ($id,$data) {
			if(empty($id))
					return FALSE;
			$this->format($data);
			$this->db->where('id', $id);
			return $this->db->update($this->table,$data);
	}

	/**
	 * <b>Method: 	delete()</b>
	 * @method		Elimina logicamente un registro de la base de datos.
	 * @param		integer $id Identificador del registro a ser eliminados.
	 * @return		boolean
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 31/05/2012 10:04 am
	 */
	public function delete ($id) {
			if(empty($id))
					return FALSE;
			$this->db->where('id', $id);
			return $this->db->update($this->table,array('eliminado' => '1'));
	}

	/**
	 * <b>Method: 	format()</b>
	 * @method		Este metodo formatea la data a ser insertada en base de datos.
	 * @param		array $data Referencia al arreglo de datos a ser formateado.
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 29/05/2012 06:12 pm
	 */
	private function format (&$data) {
			foreach ($data as &$value) {
					$value = empty($value) ? NULL : $value;
			}
			$data['user_id'] = $this->session->userdata('user_id');
			unset($data['id']);
	}

	/**
	 * <b>Method: 	getNextMaintenance()</b>
	 * @method		Consulta cuando es la proxima fecha de manteniemiento.
	 * @return		array Arreglo de datos con los registros necesarios para la ventana de mantenimiento.
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 06/06/2012 04:52 pm
	 */
	public function getNextMaintenance() {
		$arr = array();
		$this->db->select('finicio_maintenance, message,ffin_maintenance');
		$this->db->select('finicio_window, message,ffin_window');
		$this->db->from($this->table);
		$this->db->where('finicio_window <= \''.date('Y-m-d H:i:s').'\'');
		$this->db->where('eliminado', '0');
		$this->db->limit('1');
		$this->db->order_by('finicio_window DESC');
		$result = $this->db->get();
		if($result){
			$result = $result->row_array();
			$format = '%A, %d de %B de %Y a las %I:%M %P';
			$inicio = $this->formatDate($result['finicio_maintenance'], $format);
			$fin = $this->formatDate($result['ffin_maintenance'], $format);
			$result['message'] = str_replace(array('(inicio)','(fin)'), array($inicio,$fin), $result['message']);
			//$result['finicio_maintenance'] = $result['finicio_maintenance'] <= date('Y-m-d H:i');
			if($result['ffin_maintenance'] <= date('Y-m-d H:i') OR empty($result))
				$result = NULL;
		}
		return $result;
	}

	/**
	 * <b>Method: 	formatDate()</b>
	 * @method		Formate la fecha a una fecha agradable visualmente.
	 * @param		string $date Fecha a se formateada.
	 * @param		string $format Formato como sera mostrada la fecha, se utiliza el formato de strftime. default = %d-%m-%Y
	 * @return		string
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 07/06/2012 11:14 am
	 */
	public function formatDate ($date, $format='%d-%m-%Y') {
		setlocale(LC_ALL,'es_VE.UTF-8');
		$date = new DateTime($date);
		return strftime($format, $date->getTimestamp());
	}

	/**
	 * <b>Method: 	verifyDate()</b>
	 * @method		Verifica que no se produzcan conflictos con las fechas de mantenimiento.
	 * @param		array $dates Arreglo con las fechas que provienen del formulario.
	 * @return		mixed FALSE en caso de no haber problema con las fechas, el mensaje de error en caso contrario.
	 * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 07/06/2012 03:01 pm
	 */
	public function verifyDate ($dates) {
		if(empty($dates) OR !is_array($dates))
			return FALSE;
		$fif = strtotime($dates['finicio_window']);
		$fff = strtotime($dates['ffin_maintenance']);
		$datesBD = $this->getAll(FALSE, FALSE, FALSE, FALSE, TRUE);
		$msg = FALSE;
		foreach ($datesBD as $date) {
			$date->ffin_maintenance = strtotime($date->ffin_maintenance);
			if($fif < $date->ffin_maintenance){
				$date->finicio_window = strtotime($date->finicio_window);
				$msg = $this->lang->line('message_maintenance_date_error');
				if($fff < $date->finicio_window){
					$msg = FALSE;
				}
			}
			if($msg)
				break;
		}
		return $msg;
	}
    
    /**
     * <b>Method: 	cleanSessions()</b>
	 * @method		Elimina todas las sesiones existentes dentro del sistema.
     * return void
     * @author		Mirwing Rosales, Jose Rodriguez
	 * @version		v-1.0 07/06/2012 03:01 pm
     */
    function cleanSessions() {
      $this->db->empty_table('rbac.ci_sessions'); 
    }
}

/* End of file maintenance_model.php */
/*Location: /home/htdocs/sietpol/application/modules/administracion/models/maintenance_model.php*/