<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Operations_model  class
 * @package		Administracion Ouroboros
 * @subpackage	models
 * @author		Nohemi Rojas, Maycol Alvarez <malvarez@rialfi.com>
 * @copyright     Por definir
 * @license		Por definir
 * @version		v1.0 10/09/12 11:00 AM
 * */
class CrudOperation_model extends CI_Model  {

    var $table = 'rbac.operation';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * <b>Method:  getAll()</b>
	 * Metodo que construye un manual a partir de las operaciones existentes al que tiene permiso el usuario en session
	 * @return string Cadena formateada con la _comments del manual
	 * @author  Nohemi Rojas, Maycol Alvarez <malvarez@rialfi.com>
	 * @version	v1.0 14/09/12 09:00 AM
	 */
	public function getAll($type, $id_node_expand = null) {
		$this->db->select("o._name, o.id, o.operation_id, o._comments, o.category_component_type_id, vt._name as visual_type, o.url as url");
		$this->db->from("rbac.operation as o left join virtualization.category as vt ON (vt.id = o.category_visual_type_id)");
		//$this->db->where('op.role_id', $this->session->userdata('role_id'));
		//$this->db->where('o.operation_id IS NULL');
		//$this->db->where('r.id = o.id');
		$this->db->where("o.category_component_type_id = 3");
		$this->db->where("o.deleted = '0'");
		//$this->db->where("vt.id = o.category_visual_type_id");
		//$this->db->where("op.operation_id = o.id");
		$this->db->order_by("o._order", "ASC");
		$query = $this->db->get();
		$results = $query->result();

		if ($type != 'json')
			return $this->_buildChildren($results, '', $type, '');
		else {
			$children = $this->_buildChildrenArray($results, $id_node_expand);
			$tree = array('id' => '-1', 'text' => 'Operaciones', 'expanded' => true, 'children' => $children['childrens'], 'url' => '');
			return json_encode($tree);
		}
	}

	/**
	 * <b>Method:  _buildChildrenArray($cuerpo_policial_id, $results)</b>
	 * Metodo Recursivo que permite construir las operaciones hijas de otras operaciones y las coloca en un arreglo
	 * @param  array $results arreglo con las operaciones
	 * @param  array $string cadena con el contenido del manual
	 * @param  string $type identifica si el tipo es indice o contenido
	 * @return string $i Cadena que contiene la numeracion
	 * @author  Nohemi Rojas, Maycol Alvarez <malvarez@rialfi.com>
	 * @version	v1.0 14/09/12 09:00 AM
	 */
	private function _buildChildrenArray($results, $id_node_expand = null) {
            $expand = false;
            foreach ($results as $value) {
                $result = $this->getChildrenByFather($value->id);
                $nr = $this->_buildChildrenArray($result, $id_node_expand);
                $children = $nr['childrens'];

                $url = $value->url;
                if ($url == null) $url = '';
    //			//Ocultar las operaciones del tipo 'Guardar'
    //			if ($value->visual_type == 'Button_S')
    //				return FALSE;
                $icon = $value->icon ? base_url() . 'assets/img/icons/' . $value->icon : NULL;
                $node = array('id' => $value->id, 'text' => $value->_name, 'icon' => $icon, 'url' => $url);
                if ($children != NULL) {
                    $node['children'] = $children; 
                } else {
                    $node['leaf'] = true;
                }

                if ($value->id == $id_node_expand || $nr['expand']) {
                    $expand = true;
                    $node['expanded'] = true;
                }
                $tree[] = $node;
            }
            return array(
                'childrens' => $tree,
                'expand' => $expand
            );
	}

	/**
	 * <b>Method:  _buildChildren($cuerpo_policial_id, $results)</b>
	 * Metodo Recursivo que permite construir las operaciones hijas de otras operaciones
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

				
				if ($value->visual_type == 'Menu')
					$string = $string . "<br /><h2> $value->_name</h2><h2><small>$value->_comments </small></h2><br /><br/>";
				//Si es un Modulo 
				if ($value->category_component_type_id == 3){
					$string = $string . "<br /><div class='page-header'><h1> Módulo: $value->_name</h1><h1><small>$value->_comments </small></h1></div>";
				}
				//Si NO es Modulo
				if (($value->category_component_type_id != 3) && ($value->visual_type != 'Button_S')) {
					$string = $string . "<li>" . $num;
					if ($value->icon)
						$string = $string . ' <img src =\'' . base_url() . 'assets/img/icons/' . $value->icon . '\' /> ';
					$string = $string . "<span class = 'operacion'> $value->_name: </span><br />" . "$value->_comments " . "</li>";
					
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
				$string = $string . "<li>" . $num . "<a href='content/#$value->id'> $value->_name </a></li>";

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
	 * Metodo para obtener las operaciones hijas de otra operacion. Al que tiene permiso el usuario en session
	 * @param  integer $operation_id Identificador del registro del padre
	 * @return array Arreglo que contiene los registros de las operaciones hijas. 
	 * @author  Nohemi Rojas, Maycol Alvarez <malvarez@rialfi.com>
	 * @version	v1.0 14/09/12 09:00 AM
	 */
	function getChildrenByFather($operation_id) {
		$this->db->select("o._name, o.id, o.operation_id, o._comments, o.icon, vt._name as visual_type, o.url as url");
		$this->db->from("rbac.operation as o, virtualization.category as vt");
		//$this->db->where('op.role_id', $this->session->userdata('role_id'));
		$this->db->where('o.operation_id', $operation_id);
		//$this->db->where('r.id = o.id');
		$this->db->where("o.deleted = '0'");
		//$this->db->where("op.operation_id = o.id");
		//$this->db->where("o.deleted = '0'");
		$this->db->where("o._name != ' ' ");
		$this->db->where("vt.id = o.category_visual_type_id");
		$this->db->order_by("o._order ASC");
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * <b>Method: getFields()</b>
	 * Metodo para obtener los campos de una operacion determinada
	 * @param  integer $operation_id Identificador de la operacion
	 * @return array Arreglo que contiene los registros de los campos
	 * @author Nohemi Rojas
	 * @version	v1.0 07/11/11 07:04 PM
	 */
	function getFields($operation_id) {
		$this->db->select('cmp.etiqueta, cmp.ayuda');
		$this->db->from('dinamico.campos cmp');
		$this->db->join('rbac.operaciones_campos op', 'op.campo_id = cmp.id');
		$this->db->where('cmp.deleted', '0');
		$this->db->where('op.operation_id', $operation_id);
		$this->db->where('op.deleted', '0');
		$this->db->where('op.hidden', '0');
		$this->db->where("cmp.tipo_campo <> 'label'");
		$this->db->order_by("op._order", 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * <b>Method:	getById()</b>
	 * Retorna la documentacion de una operacion dado su id
	 * @param		$operacion_id identifiador de la operacion
	 * @return		array arreglo de datos
	 * @author		Eliel Parra
	 * @version		v1.0 24/11/11 07:51 PM
	 * */
	function getById($operacion_id, $return_array = false, $selects = null) {
        if ($selects) {
            $this->db->select($selects);
        }        
		$this->db->where('id', $operacion_id);
		$query = $this->db->get($this->table);
        if ($return_array) {
            $row = $query->row_array();
        } else {
            $row = $query->row();
        }
		return $row;
	}

    /**
     * <b>Method:  _format()</b>
     * @param array $data
     * @param string $type INSERT o UPDATE según la operación
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function _format(&$data, $type) {
        unset($data['parent_name']);
        if ($type == 'UPDATE'){
            unset($data['id']);
            unset($data['operation_id']);
        } else if ($type == 'INSERT') {
            unset($data['id']);
            //ROOT
            if ($data['operation_id'] == '-1') {
                unset($data['operation_id']);
            }
        }
        
        $this->empty_format($data['url']);
        $this->empty_format($data['icon']);
        $this->empty_format($data['tooltip']);
        $this->empty_format($data['_comments']);
        $this->empty_format($data['category_render_on_id']);
         
        $data['visible'] = ($data['visible']) ? '1':'0';
        $data['business_logic'] = ($data['business_logic']) ? '1':'0';
        $data['ouroboros_admin'] = ($data['ouroboros_admin']) ? '1':'0';
        
        $data['_order'] = intval($data['_order']);
        
//        $data['category_visual_type_id'] = $data['category_ext_visual_type_id'];
//        unset($data['category_ext_visual_type_id']);
        $data['created_by'] = $this->session->userdata('user_id');
        //die(var_dump($data));
    }
    
    /**
     * <b>Method:  empty_format()</b>
     * Evalua si el parámetro está vacío y lo asigna a null como tal
     * @param array $data 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    private function empty_format(&$data){
        $data = (empty($data)) ? null : $data;
    }

    /**
     * <b>Method:  create()</b>
     * Crea la operación
     * @param type $params 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function create($data) {
        $this->_format($data, 'INSERT');
		return $this->db->insert($this->table, $data);
    }

    /**
     * <b>Method: delete()</b>
     * Elimina la operación
     * @param type $id 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function delete($id) {
        $data = array('deleted' => '1');
		$this->db->trans_start();
        //$this->_format($data, 'UPDATE');
        $this->db->where('id', $id);
		$this->db->update($this->table, $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
    }

    /**
     * <b>Method: update()</b>
     * Actualiza los datos de la Operación
     * @param type $id
     * @param type $data 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function update($id, $data) {
		$this->db->trans_start();
        $this->_format($data, 'UPDATE');
        $this->db->where('id', $id);
		$this->db->update($this->table, $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
    }
    
    /**
     * <b>Method: move()</b>
     * Mueve de padre a la operación
     * @param integer $id id de la operación a mover
     * @param integer $new_parent id de la nueva operación hacia donde será movida
     * @param array $data
     * @return boolean 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function move($id, $new_parent, $data = null) {
        if ($data == null) $data = array();
        $data['operation_id'] = $new_parent;
		$this->db->trans_start();
        //$this->_format($data, 'UPDATE');
        $this->db->where('id', $id);
		$this->db->update($this->table, $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
    }
    
    /**
     * <b>Method: urlExists()</b>
     * Verifica que la URL no se encuentre disponible
     * @param string $url url a buscar
     * @param integer $id_ignore id para ignorar un registro
     * @return boolean 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function urlExists($url, $id_ignore = null) {
        if ($url == '') return false;
        $this->db->select('url');
        $this->db->where('url', $url);
        $this->db->where('deleted', '0');
        if ($id_ignore != null) {
             $this->db->where('id !=', $id_ignore);
        }
		$query = $this->db->get($this->table);
		$total = $query->num_rows();
		return ($total != 0);        
    }
    
    /**
     * <b>Method: operationHasChildrens()</b>
     * Devuelve si la operación tiene hijos
     * @param integer $id
     * @return boolean 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function operationHasChildrens($id) {
         $query = $this->db->query("SELECT count(*) AS total FROM $this->table WHERE operation_id = $id AND deleted = '0'");
         $row = $query->row();
         return ($row->total != 0);
    }
    
    /**
     * <b>Method: ()</b>
     * Devuelve si la operación tiene roles asociados
     * @param type $id
     * @return type 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function operationHasRoles($id) {
         $query = $this->db->query("SELECT count(*) AS total FROM rbac.operation_role WHERE operation_id = $id AND deleted = '0'");
         $row = $query->row();
         return ($row->total != 0);
    }
        
    /**
     * <b>Method: ()</b>
     * Devuelve si la operación tiene campos asociados
     * @param type $id
     * @return type 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function operationHasFields($id) {
         $query = $this->db->query("SELECT count(*) AS total FROM rbac.operation_field WHERE operation_id = $id AND deleted = '0'");
         $row = $query->row();
         return ($row->total != 0);
    }
    
    /**
     * <b>Method: ()</b>
     * Devuelve si los padres de las operaciones padre origen/destino son iguales, además de indicar si se trata de mover hacia un descendiente.
     * @param object $node Registro de la operación actual
     * @param integer $new_parent_id id de la operación a donde será movido
     * @param boolean $inception devuelve por referencia si existe inception
     * @return boolean
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function equalParents($node, $new_parent_id, &$inception) {
        $data = array();
        if ($new_parent_id == null) {
            //convertir a MODULE?
            //$data['category_component_type_id'] = 3;
            //return $data;
            return false;
        }
        if ($node->operation_id == null) return false;
        
        $parent_node = $this->getById ($node->operation_id);
        $new_parent = $this->getById ($new_parent_id);
        
        $parents = $this->getParentsArray($new_parent);
        
        //node inception
        if (in_array($node->id, $parents)) {
            $inception = true;
            return false;
        }         
        
        return ($parent_node->category_component_type_id == $new_parent->category_component_type_id
            &&  $parent_node->category_visual_type_id == $new_parent->category_visual_type_id
        );
    }
    
    /**
     * <b>Method:	getParentsArray()</b>
     * Obtiene Recursivamente los Padres de la Operación
     * @param object $node registro de la operación
     * @param array $array array a llenar, por defecto es un array vacio
     * @return array 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version v1.0 18/09/12 09:00 AM
     */
    public function getParentsArray($node, &$array = array()) {
        $parent = $node->operation_id;
        //die($node->id);
        if ($parent == null) {
            return $array;
        } else {
            $array[] = $parent;
        }
        $nodep = $this->getById($parent, false, 'id, operation_id');
        return $this->getParentsArray($nodep, $array);
    }
    
    public function getDeleteAllOperations($id) {
        $ret = array(
            'errors' => 0
        );
        $this->fillChildsOperationsForDeleted($id, $ret, FALSE);
        //var_dump($ret); die();
        return $ret;
        
    }
    
    private function fillChildsOperationsForDeleted($operation_id, &$array_op, $stop_on_error = true) {
        $data = $this->getChildsOperationsForDeleted($operation_id);
        foreach ($data as $operation) {
            $array_op['operation_id'][] = $operation->id;
            $array_op['errors'] += $operation->total_roles;
            $array_op['errors'] += $operation->total_fields;
            if ($stop_on_error)
                if ($array_op['errors'] != 0) return;
            $this->fillChildsOperationsForDeleted($operation->id, $array_op, $stop_on_error);
        }
    }
    
    private function getChildsOperationsForDeleted($operation_id) {
		$this->db->select("o.id, (SELECT count(*) FROM rbac.operation_role AS opr 
                            WHERE opr.operation_id = o.id AND opr.deleted='0') AS total_roles, 
                            (SELECT count(*) FROM rbac.operation_field AS opf 
                              WHERE opf.operation_id = o.id AND opf.deleted='0') AS total_fields");
		$this->db->from("rbac.operation as o");
		$this->db->where('o.operation_id', $operation_id);
		$this->db->where("o.deleted = '0'");
		$query = $this->db->get();
		return $query->result();
	}
    
    
    /**
     * <b>Method: delete()</b>
     * Elimina la operación en cascada
     * @param type $id 
     * @author  Maycol Alvarez <malvarez@rialfi.com>
     * @version	v1.0 14/09/12 09:00 AM
     */
    public function deleteAll($id, $operation_childs) {
        $operation_childs[] = $id;
        $operations = implode(',', $operation_childs);
        $data = array('deleted' => '1');
		$this->db->trans_start();
        //$this->_format($data, 'UPDATE');
        $this->db->where("id IN ($operations)");
		$this->db->update($this->table, $data);
		$this->db->trans_complete();
		return $this->db->trans_status();
    }

    public function CL_getIcons() {
        $icons = array(
            array(
                'value' => 'zoom.png',
                'label' => 'zoom.png'
            ),
            array(
                'value' => 'pencil.png',
                'label' => 'pencil.png'
            ),
            array(
                'value' => 'add.png',
                'label' => 'add.png'
            ),
            array(
                'value' => 'accept.png',
                'label' => 'accept.png'
            ),
            array(
                'value' => 'cancel.png',
                'label' => 'cancel.png'
            ),
            array(
                'value' => 'delete.png',
                'label' => 'delete.png'
            ),
            array(
                'value' => 'save.gif',
                'label' => 'save.gif'
            ),
            array(
                'value' => 'key.png',
                'label' => 'key.png'
            ),
            array(
                'value' => 'vcard_edit.png',
                'label' => 'vcard_edit.png'
            ),
            array(
                'value' => 'page_white_csv.png',
                'label' => 'page_white_csv.png'
            ),
            array(
                'value' => 'table_add.png',
                'label' => 'table_add.png',
            )
        );
        
        return $icons;
    }
    
}

