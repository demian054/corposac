<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Sietpol Checks Helper
 * @package	Sietpol
 * @subpackage	helpers
 * @author	mrosales
 * @copyright	Por definir
 * @license		Por definir
 * @version		V-1.1 21/09/2011 12:10:23 pm
 * */
if (!function_exists('get_check')) {

   /**
	 * <b>Method:get_check($check = '',$enSelect = FALSE)</b> 
	 * 
	 * @method		Helper para cargar los valores de los checks definidos en la base de datos
	 * @param		string $check Nombre del check a cargar este nombre debe ser igual al de la base de datos.
	 * @param 		boolean $enSelect Activa el valor seleccione para armar un dropdown.
	 * @return 		array Arreglo con los valores admitidos por los checks en la base de datos.
	 * @author 		Mirwing Rosales
	 * @version		v-1.1 21/09/2011 12:11:02 pm
	 */
    function get_check($check = '', $enSelect = FALSE) {
        global $_checks;
        if (!is_array($_checks)) {
            if (defined('ENVIRONMENT') AND is_file(APPPATH . 'config/' . ENVIRONMENT . '/checks' . EXT)) {
                include(APPPATH . 'config/' . ENVIRONMENT . '/checks' . EXT);
            } elseif (is_file(APPPATH . 'config/checks' . EXT)) {
                include(APPPATH . 'config/checks' . EXT);
            }
            if (!is_array($_checks)) {
                return FALSE;
            }
        }
        if (!empty($check) && isset($_checks[$check])) {
            $array = array();
            if ($enSelect)
                $_checks[$check][0] = 'Seleccione';
            //ksort($_checks[$check]);
            return $_checks[$check];
        } else {
            return FALSE;
        }
    }

}


/* End of file checks_helper.php */
/*Location: ./application/helpers/checks_helper.php*/ 