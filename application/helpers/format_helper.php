<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * Sietpol Format Helper
 * @package	Sietpol
 * @subpackage	helpers
 * @author	rrojas
 * @copyright	Por definir
 * @license		Por definir
 * @version		V-1.0 09/03/12 02:33 PM
 * */
if (!function_exists('format_indentificador')) {

	/**
	 * <b>Method:get_check($check = '',$enSelect = FALSE)</b> 
	 * 
	 * @method		Helper para dar formato a los campos tipo identificacion segun las siguientes reglas:
	 * 				- Si el identificador es mayor de 80000000 -> E-80000000, en caso contrario -> V-10000000
	 * 				- Formato de numero en miles con punto -> 80.000.000
	 * @param		string $identificador identificador numerico
	 * @return 		string $identificador identificador con el formato aplicado
	 * @author 		Reynaldo Rojas
	 * @version		v-1.0 09/03/12 02:18 PM
	 */
	function format_indentificador($tipo_identificador,$identificador) {
		return $tipo_identificador.'-'.number_format($identificador, 0, ',', '.');
	}

}

/* End of file checks_helper.php */
/*Location: ./application/helpers/checks_helper.php*/ 
