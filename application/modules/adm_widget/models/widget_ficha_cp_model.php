<?php

if (!defined('BASEPATH'))	exit('No direct script access allowed');

/**
 * Afiliaciones_model  class
 * @package		funcionario
 * @subpackage	models
 * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v-1.0 24/11/11 10:06 AM
 * */
class Widget_ficha_cp_model  extends CI_Model{
	
	public $CI;	
	public function __construct() {
		parent::__construct();	
	}
	
	public function init(&$CI){
		$this->CI=&$CI;		
	}
		
	public function getCpById($cpId){	
		if(empty($cpId)) return false;
		$this->CI->db->select('estatico.ubicaciones.id AS ubicacion_id,
							   estatico.ubicaciones.chk_tipo_ubicacion AS tipo_ubicacion,
							   estatico.ubicaciones.nombre AS nombre_ubicacion,
							   estatico.ubicaciones.padre_id AS ubicacion_padre_id,
							   estatico.cuerpos_policiales.id AS cuerpo_policial_id,
							   estatico.cuerpos_policiales.nombre AS nombre,
							   estatico.cuerpos_policiales.chk_ambito_politico_ter,
							   estatico.cuerpos_policiales.telefonos, 
							   estatico.cuerpos_policiales.fax, 
							   estatico.cuerpos_policiales.email, 
							   estatico.cuerpos_policiales.sitio_web, 
							   estatico.cuerpos_policiales.ffundacion, 
							   estatico.cuerpos_policiales.numero_gaceta_fundacion, 
							   estatico.cuerpos_policiales.fmodificacion_inst_const, 
							   estatico.cuerpos_policiales.numero_gaceta_mod, 
                               estatico.cuerpos_policiales.fsolicitud_eliminacion,
							   estatico.cuerpos_policiales.activo, 
							   estatico.cuerpos_policiales.observaciones, 
							   estatico.cuerpos_policiales.eliminado');
		$this->CI->db->from('estatico.cuerpos_policiales');
		$this->CI->db->join('estatico.ubicaciones', 'estatico.ubicaciones.id = estatico.cuerpos_policiales.ubicacion_id');
		$this->CI->db->where("estatico.ubicaciones.eliminado = '0'");
		$this->CI->db->where("estatico.cuerpos_policiales.eliminado = '0'");
		$this->CI->db->where("estatico.cuerpos_policiales.id = $cpId");
		$result = $this->CI->db->get();
		return $result->row_array();
	}
}

?>
