<?php

if (!defined('BASEPATH'))
	exit('Acceso Denegado');

/**
 * Auth Class
 * @package		libraries
 * @author		Jesus Farias <jfarias@rialfi.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v-1.0 07/09/12 12:16 PM
 * */
class Menu_builder {

	public static $menu;

	public function __construct() {
		$this->CI = & get_instance();
	}

	/**
	 * <b>Method: compareOrder()</b>
	 * @method	Permite establecer el orden en que se muestran las operaciones del sistema
	 * @return	Integer $a
	 * @return	Integer $b
	 * @return	Integer 1 en caso de cambiar el orden
	 *                 -1 en caso contrario
	 * @author	Jesus Farias <jfarias@rialfi.com>
	 * @version V-1.0 07/09/12 11:54 AM
	 * */
	static function compareOrder($a, $b) {
		return ($a['_order'] <= $b['_order']) ? -1 : 1;
	}

	/**
	 * <b>Method: getMenuOperations()</b>
	 * @method	Permite establecer una estructura con el menu de operaciones principales del sistema
	 * @return	String $menu_operations cadena en formato JSON que contiene las operaciones principales de forma jerarquica
	 * @author	Jesus Farias <jfarias@rialfi.com>
	 * @version V-1.0 07/09/12 11:54 AM
	 * */
	public function getMenuOperations() {

		$menu_operations = $sub_menu = array();
		$operations = $this->CI->session->userdata('operations');
		if (empty($operations))
			return false;

		// Asignacion de formato a cada operacion dentro del arreglo de operaciones
		foreach ($operations as $item) {

			// Si el componente es de primer nivel (no posee padre)
			if ($item->category_visual_type == "Accordion") {
				$menu_operations[$item->id]['id'] = $item->id;
				$menu_operations[$item->id]['_name'] = $item->_name;
				$menu_operations[$item->id]['icon'] = $item->icon; //@todo definir ruta de iconos
				$menu_operations[$item->id]['_order'] = $item->_order; //@todo definir ruta de iconos
			}

			// Si el componente es de segundo nivel
			else if ($item->category_visual_type == "Menu" && !empty($item->operation_id)) {
				$menu_operations[$item->operation_id]['menu'][$item->id]['id'] = $item->id;
				$menu_operations[$item->operation_id]['menu'][$item->id]['_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;' . $item->_name;
				$menu_operations[$item->operation_id]['menu'][$item->id]['icon'] = $item->icon;
				$menu_operations[$item->operation_id]['menu'][$item->id]['url'] = $item->url;
				$menu_operations[$item->operation_id]['menu'][$item->id]['operation_id'] = $item->operation_id;
			}

			// Si el componente es de tercer nivel
			else if ($item->category_visual_type == "Submenu" && !empty($item->operation_id)) {
				$sub_menu[$item->operation_id][$item->id]['id'] = $item->id;
				$sub_menu[$item->operation_id][$item->id]['_name'] = $item->_name;
				$sub_menu[$item->operation_id][$item->id]['icon'] = $item->icon;
				$sub_menu[$item->operation_id][$item->id]['url'] = $item->url;
				$sub_menu[$item->operation_id][$item->id]['operation_id'] = $item->operation_id;
			}else
				continue;
		}

		// Asignar operaciones a cada menu
		foreach ($operations as $item)
			if ($item->category_visual_type == "Menu" && !empty($item->operation_id) && !empty($sub_menu[$item->id]))
				$menu_operations[$item->operation_id]['menu'][$item->id]['submenu'] = $sub_menu[$item->id];

		// Aplicar ordenamiento a los elementos del menu de operaciones
		usort($menu_operations, "self::compareOrder");
		return json_encode($menu_operations);
	}
}

?>