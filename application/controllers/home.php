<?php
if (!defined('BASEPATH'))
	exit('Acceso Denegado');

/**
 * Home class
 * @package		controllers
 * @author		Jesus Farias <jfarias@rialfi.com> 
 * @copyright	Por definir
 * @license		Por definir
 * @version		v1.0 29/08/12 05:18 PM
 * */
class Home extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->ci = &get_instance();
		
		// Codigo Maintenance ////////////////////////////////////
		//$this->load->model('adm_maintenance/maintenance_model');
		// Codigo Maintenance ////////////////////////////////////
	}

	function index() {
		
		// Codigo Maintenance ////////////////////////////////////		
		//   		// Verify if maintenance is active
		//        $maintenance = $this->maintenance_model->getNextMaintenance();
		//        $this->session->set_userdata('maintenance', $maintenance);
		//        if(is_array($maintenance)){
		//          $date = date('Y-m-d H:i:s');
		//          if($maintenance['finicio_maintenance'] <= $date AND $maintenance['ffin_maintenance'] >= $date)
		//            redirect('maintenance');
		//        }
		// Codigo Maintenance ////////////////////////////////////
		
		// Verificacion de sesion
		if (!$this->session->userdata('role_id'))
			$this->tank_auth->logout();

		// Si el usuario ne se encuentra en sesion se redirecciona al login donde se ejecuta
		// el proceso de inicio del sistema
		if (!$this->tank_auth->is_logged_in())
			redirect('lib_tank_auth/auth/login/');
		else {

			$data = array();
			$data['title'] = "Ouroboros";

			// Se carga la libreria encargada de generar el menu de operaciones principales del sistema
			$this->load->library('Menu_builder');
			$data['menu_data'] = $this->menu_builder->getMenuOperations();


			/* -----------------------ch--------------------------------------------------------------------------------------------
			 * Widgets stuff (look at widgets_config file in application/config/widgets_config.php for more details)
			 * the widgets_config is autoloaded on  application/config/autoload.php $autoload['config']
			  ------------------------------------------------------------------------------------------------------------------ */
			if ($this->config->item('widgets_engine_enabled')) {
				$this->load->library(WIDGETS_ENGINE, array('_widgets_portal_config' => $this->config->item('widgets_portal_config'), 'CI' => &$this->ci));
				$data['widgets_portal'] = $this->widgets_engine->getWidgetsRendering();
				if (!empty($data['widgets_portal']))
					$data['widgets_on'] = true;
			}
			//--------------------------------------------------------------------------------end of widgets component integration
			
			// Se carga el contenedor principal html donde se agregan los componentes viewport, west_menu, header, footer
			$this->load->view('home/main_layout.js.php', $data);
		}
	}
}

/* END Home Class       */
/* End of file home.php */
/* Location: ./application/controllers/home.php */
?>