<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Documentacion_model  class
 * @package		documentacion
 * @subpackage	models
 * @author		Nohemi Rojas <nrojas@rialfi.com>, Eliel Parra<eparra@rialfi.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v1.0 07/11/11 04:36 PM
 * */
class Documentacion_model extends CI_Model {

	var $table = 'documentacion.operations';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * <b>Method:  getAll()</b>
	 * @method Metodo que construye un manual a partir de las operaciones existentes al que tiene permiso el usuario en session
	 * @return string Cadena formateada con la descripcion del manual
	 * @author  Nohemi Rojas
	 * @version	v1.0 07/11/11 06:59 PM
	 */
	public function getAll($type) {
		$this->db->select("o.nombre, o.id, o.operation_id, o.descripcion, r.chk_component_type");
		$this->db->from("documentacion.operations as o, rbac.operation_roles as op, rbac.operations as r");
		$this->db->where('op.rol_id', $this->session->userdata('role_id'));
		$this->db->where('o.operation_id IS NULL');
		$this->db->where('r.id = o.id');
		$this->db->where("r.chk_component_type = 'Module'");
		$this->db->where("r.eliminado = '0'");
		$this->db->where("op.operation_id = r.id");
		$this->db->order_by("o.orden", "ASC");
		$query = $this->db->get();
		$results = $query->result();

		if ($type != 'json')
			return $this->_buildChildren($results, '', $type, '');
		else {
			$children = $this->_buildChildrenArray($results);
			$tree = array('id' => '0', 'text' => 'Documentación', 'expanded' => true, 'children' => $children);
			return json_encode($tree);
		}
	}

	/**
	 * <b>Method:  _buildChildrenArray($cuerpo_policial_id, $results)</b>
	 * @method Metodo Recursivo que permite construir las operaciones hijas de otras operaciones y las coloca en un arreglo
	 * @param  array $results arreglo con las operaciones
	 * @param  array $string cadena con el contenido del manual
	 * @param  string $type identifica si el tipo es indice o contenido
	 * @return string $i Cadena que contiene la numeracion
	 * @author Nohemi Rojas
	 * @version	v1.0  07/11/11 07:02 PM
	 */
	private function _buildChildrenArray($results) {

		foreach ($results as $value) {

			$result = $this->getChildrenByFather($value->id);
			$children = $this->_buildChildrenArray($result);
			//Ocultar las operaciones del tipo 'Guardar'
			if ($value->chk_visual_type == 'Button_S')
				return FALSE;
			$icon = $value->icon ? base_url() . 'assets/img/icons/' . $value->icon : NULL;
			if ($children == NULL)
				$tree[] = array('id' => $value->id, 'text' => $value->nombre, 'icon' => $icon, 'leaf' => true);
			else
				$tree[] = array('id' => $value->id, 'text' => $value->nombre, 'icon' => $icon, 'children' => $children);
		}
		return $tree;
	}

	/**
	 * <b>Method:  _buildChildren($cuerpo_policial_id, $results)</b>
	 * @method Metodo Recursivo que permite construir las operaciones hijas de otras operaciones
	 * @param  array $results arreglo con las operaciones
	 * @param  array $string cadena con el contenido del manual
	 * @param  string $type identifica si el tipo es indice o contenido
	 * @return string $i Cadena que contiene la numeracion
	 * @author Nohemi Rojas
	 * @version	v1.0  07/11/11 07:02 PM
	 */
	private function _buildChildren($results, $string, $type, $i) {

		$string = $string . '<ol>';
		$cont = 1;

		foreach ($results as $value) {

			$num = ($i == '') ? $cont : $i . '.' . $cont;

			//Si es tipo CONTENIDO
			if ($type == 'content') {

				
				if ($value->chk_visual_type == 'Menu')
					$string = $string . "<br /><h2> $value->nombre</h2><h2><small>$value->descripcion </small></h2><br /><br/>";
				//Si es un Modulo 
				if ($value->chk_component_type == 'Module'){
					$string = $string . "<br /><div class='page-header'><h1> Módulo: $value->nombre</h1><h1><small>$value->descripcion </small></h1></div>";
				}
				//Si NO es Modulo
				if (($value->chk_component_type != 'Module') && ($value->chk_visual_type != 'Button_S')) {
					$string = $string . "<li>" . $num;
					if ($value->icon)
						$string = $string . ' <img src =\'' . base_url() . 'assets/img/icons/' . $value->icon . '\' /> ';
					$string = $string . "<span class = 'operacion'> $value->nombre: </span><br />" . "$value->descripcion " . "</li>";
					
					$fields = $this->getFields($value->id);

					//Si tiene campos
					if ($fields) {
						$string = $string . "<div class='span12'><table><thead><tr><th>";
						foreach ($fields as $field)
							$string = $string . "<li><b> $field->etiqueta: </b><em>$field->ayuda</em> </li>";
						$string = $string . "</th></tr></thead></table></div>";
					}
					else
						$string = $string . "<br/>";
					//Screen shot
					if(file_exists("assets/img/screenshots/".$value->id.".png"))
						$string = $string . "<br /><div><img style = 'max-width:700px; margin-top:2em; margin-bottom:3em' src = '" . base_url() . "assets/img/screenshots/".$value->id.".png' /></div>";
				}
			}

			//Si es tipo INDEX
			if ($type == 'index')
				$string = $string . "<li>" . $num . "<a href='content/#$value->id'> $value->nombre </a></li>";

			//Llamada a la funcion recursiva
			$result = $this->getChildrenByFather($value->id);
			$string = $this->_buildChildren($result, $string, $type, $num);
			$cont++;
		}
		$string = $string . '</ol>';
		return $string;
	}

	/**
	 * <b>Method: getChildrenByFather()</b>
	 * @method Metodo para obtener las operaciones hijas de otra operacion. Al que tiene permiso el usuario en session
	 * @param  integer $operation_id Identificador del registro del padre
	 * @return array Arreglo que contiene los registros de las operaciones hijas. 
	 * @author Nohemi Rojas
	 * @version	v1.0 07/11/11 07:04 PM
	 */
	function getChildrenByFather($operation_id) {
		$this->db->select("o.nombre, o.id, o.operation_id, o.descripcion, o.icon, o.chk_visual_type");
		$this->db->from("documentacion.operations as o, rbac.operation_roles as op, rbac.operations as r");
		$this->db->where('op.rol_id', $this->session->userdata('role_id'));
		$this->db->where('o.operation_id', $operation_id);
		$this->db->where('r.id = o.id');
		$this->db->where("r.eliminado = '0'");
		$this->db->where("op.operation_id = r.id");
		$this->db->where("o.eliminado = '0'");
		$this->db->where("o.nombre != ' ' ");
		$this->db->order_by("orden ASC");
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * <b>Method: getFields()</b>
	 * @method Metodo para obtener los campos de una operacion determinada
	 * @param  integer $operation_id Identificador de la operacion
	 * @return array Arreglo que contiene los registros de los campos
	 * @author Nohemi Rojas
	 * @version	v1.0 07/11/11 07:04 PM
	 */
	function getFields($operation_id) {
		$this->db->select('cmp.etiqueta, cmp.ayuda');
		$this->db->from('dinamico.campos cmp');
		$this->db->join('rbac.operaciones_campos op', 'op.campo_id = cmp.id');
		$this->db->where('cmp.eliminado', '0');
		$this->db->where('op.operation_id', $operation_id);
		$this->db->where('op.eliminado', '0');
		$this->db->where('op.hidden', '0');
		$this->db->where("cmp.tipo_campo <> 'label'");
		$this->db->order_by("op.orden", 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * <b>Method:	getById()</b>
	 * @method		Retorna la documentacion de una operacion dado su id
	 * @param		$operacion_id identifiador de la operacion
	 * @return		array arreglo de datos
	 * @author		Eliel Parra
	 * @version		v1.0 24/11/11 07:51 PM
	 * */
	function getById($operacion_id) {

		$this->db->where('id', $operacion_id);
		$query = $this->db->get($this->table);
		$row = $query->row();
		return $row;
	}

}

// END Documentacion_model Class       
// End of file documentacion_model.php 
// Location: ./application/modules/documentacion/models/documentacion_model.php 



		