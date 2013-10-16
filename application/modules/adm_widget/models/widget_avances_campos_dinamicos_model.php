<?php

if (!defined('BASEPATH'))	exit('No direct script access allowed');

/**
 * Widget_avances_campos_dinamicos_model  class
 * @package		widgets
 * @subpackage	models
 * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v-1.0 24/11/11 10:06 AM
 * */
class Widget_avances_campos_dinamicos_model  extends CI_Model{
	
	public $CI;	
	public function __construct() {
		parent::__construct();	
	}
	
	public function init(&$CI){
		$this->CI=&$CI;		
	}
   
       public function  getPorcentajeAvanceCampos($cpId=NULL,$anio=NULL){
		$strQuery = "SELECT * FROM dinamico.fnc_porcentaje_avance_campos";
		$strQuery.= (empty($anio))?"(NULL,NULL)":"('$anio', NULL)";  
		$strQuery.= (empty($cpId))?"":" WHERE id=$cpId ";
		$result = $this->CI->db->query($strQuery);
		return $result->result_array();		
	}
     
	
	
}

?>
