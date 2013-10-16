<?php
if (!defined('BASEPATH'))  exit('Acceso Denegado');

class Maintenance extends CI_Controller {
   public    $CI;

   function __construct() {
        parent::__construct();
		$this->CI =&get_instance();
   }

	function index() {
    if($this->config->item('maintenance')){
      $this->load->view('home/maintenance');
    } else {

      	//$maintenance = $this->session->userdata('maintenance');
        $this->load->model('administracion/maintenance_model');
        $maintenance = $this->maintenance_model->getNextMaintenance();

        if(is_array($maintenance)){
          $date = date('Y-m-d H:i:s');
          if($maintenance['finicio_maintenance'] <= $date AND $maintenance['ffin_maintenance'] >= $date){
            $this->maintenance_model->cleanSessions();
            $this->load->view('home/maintenance');
          } else {
            redirect('/');
          }
        } else {
          redirect('/');
        }
    }
  }
}