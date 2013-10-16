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
class Widget_ultimos_nombramientos_model  extends CI_Model{
	
	public $CI;	
	public function __construct() {
		parent::__construct();	
	}
	
	public function init(&$CI){
		$this->CI=&$CI;		
	}
		
	public function  getUltimosNombramientos($cpId=""){
		
		$auxWhere=(empty($cpId))?"":" ep.cuerpo_policial_id=$cpId  AND ";
		
		$strQuery = "SELECT
						ep.id AS id,
						cp.nombre AS cuerpo_policial,
						per.primer_apellido,	per.primer_nombre,  			
						per.chk_tipo_identificacion ||'-'|| per.identificacion AS identificacion,
						ra.nombre AS nombre_rango,  dep.nombre AS nombre_departamento,
						ca.nombre AS nombre_cargo,  ep.finicio, ep.facto
					FROM
						estatico.experiencias_policiales ep
						INNER JOIN estatico.cuerpos_policiales cp ON (ep.cuerpo_policial_id = cp.id)
						INNER JOIN estatico.departamentos dep ON (ep.departamento_id = dep.id)
						INNER JOIN estatico.cargos ca ON (ep.cargo_id = ca.id)
						INNER JOIN estatico.rangos ra ON (ep.rango_id = ra.id)
						INNER JOIN estatico.funcionarios fun ON (ep.funcionario_id = fun.id)
						INNER JOIN estatico.personas per ON (per.id=fun.id)
					WHERE
						$auxWhere ep.eliminado = '0'  AND dep.eliminado='0'				
						AND ep.actual = '1'  AND ep.ffin ISNULL AND (ep.finicio >=CURRENT_DATE-30)
						AND ca.eliminado='0' AND ra.eliminado='0'    AND fun.eliminado = '0'
						AND fun.activo = '1' AND per.eliminado='0'				
					ORDER BY finicio DESC ";
		$result = $this->CI->db->query($strQuery);
		return $result->result_array();		
	}
	
	
}

?>
