<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Combo_loader_model class
 * @package		SIETPOL
 * @subpackage	models
 * @author		Jesus Farias <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		1.0 20/10/11 04:44 PM
 * */
class Combo_loader_model extends CI_Model {

    public $CI;
    public $db;

    function __construct() {
        parent::__construct();
    }

    function init(&$CI) {
        $this->CI = &$CI;
        $this->db = &$this->CI->db;
    }

    /**
     * <b>Method: getDepartamentoByCp()</b>
     * @method Metodo para obtener el/los departamento(s) de un determinado cuerpo policial
     *          Si se encuentra un $depart_id, traerá todos los departamentos excepto ese
     * @return Registro que contiene la cantidad de departamentos de un cuerpo policial. 
     * @author Angelo Tamarones
     * @version	v1.0 16/09/11 04:47 PM
     */
    public function getDepartamentoByCp() {
        $cp_id = $this->CI->session->userdata('cuerpo_policial_id');
        $departamento_id = $this->CI->session->userdata('departamento_id');
        $str_departamento_id = $this->getAllByDepartamento($cp_id, $departamento_id);
        if (empty($cp_id))
            return false;
        $this->db->select('id AS value, nombre AS label');
        $this->db->from('estatico.departamentos');
        $this->db->where('eliminado', '0');
        if (!empty($str_departamento_id))
            $this->db->where('id not in (' . $str_departamento_id . ')');
        $this->db->where('activo', '1');
        $this->db->where('cuerpo_policial_id', $cp_id);
        $this->db->order_by('label', 'asc');
        $query = $this->db->get();
        //$this->session->unset_userdata('departamento_id');
        if (($query->num_rows) > 0)
            return $query->result();
        else {
            return (array(array('value' => 'N/A', 'label' => 'N/A')));
        }
    }

    /**
     * <b>Method:	getCuerposPoliciales()</b>
     * @method		Obtiene todos los cuerpos policiales
     * @return		array Arreglo de objetos con los datos de los cuerpos policiales
     * @author		Nohemi Rojas
     * @version		v1.0 26/10/11 04:47 PM
     * */
    function getCuerposPoliciales() {

        $this->db->select('id AS value, nombre AS label');
        $this->db->from('estatico.cuerpos_policiales');
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $this->db->order_by('nombre');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getPeriodoByPeriodicidad()</b>
     * @method		Obtiene todos los periodos de una determinada periodicidad
     * @return		array Arreglo de objetos con los datos de los periodos
     * @author		Angelo Tamarones
     * @version		v1.0 26/10/11 04:47 PM
     * */
    public function getPeriodoByPeriodicidad($periodicidad = '') {
        if ($periodicidad == '') 
            return FALSE; 
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('chk_periodicidad', $periodicidad);
        $this->db->order_by('orden', 'asc');
        $query = $this->db->get('dinamico.periodos');
        return $query->result();
    }

    /**
     * <b>Method:	getEstandares()</b>
     * @method		Obtiene todos los estandares
     * @return		array Arreglo de objetos con los datos de los cuerpos policiales
     * @author		Nohemi Rojas
     * @version		v1.0 26/10/11 04:47 PM
     * */
    function getEstandares() {

        $this->db->select('id AS value, nombre AS label');
        $this->db->from('dinamico.estandares');
        $this->db->where('eliminado', '0');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:getAreasEstandarizacion() </b>
     * @method	Metodo para obtener las areas de estandarizacion
     * @return	arreglo con las areas de estandarizacion
     * @author	Nohemi Rojas & Angelo Tamarones
     * @version	v1.0 30/09/11 10:53 AM
     */
    function getAreasEstandarizacion() {
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('eliminado', '0');
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get('dinamico.areas_estandarizacion');
        return $query->result();
    }

    /**
     * <b>Method:getInstalacionesByCuerpoPolicial() </b>
     * @method	Metodo para obtener los estandares para una determinada area de estandarizacion
     * @param   integer $area_id identificador del area de estandarizacion
     * @return	arreglo con los estandares
     * @author	Nohemi Rojas & Angelo Tamarones
     * @version	v1.0 30/09/11 10:34 AM
     */
    function getInstalacionesByCuerpoPolicial() {
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('cuerpo_policial_id', $this->session->userdata('cuerpo_policial_id'));
        $this->db->where('eliminado', '0');
        $query = $this->db->get('estatico.instalaciones');
        return $query->result();
    }

    /**
     * <b>Method:getByAreasEstandarizacion() </b>
     * @method	Metodo para obtener los estandares para una determinada area de estandarizacion
     * @param   integer $area_id identificador del area de estandarizacion
     * @return	arreglo con los estandares
     * @author	Nohemi Rojas & Angelo Tamarones
     * @version	v1.0 30/09/11 10:34 AM
     */
    function getEstandarByAreasEstandarizacion($area_id = NULL) {
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('area_estandarizacion_id', $area_id);
        $this->db->where('eliminado', '0');
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get('dinamico.estandares');
        return $query->result();
    }

    /**
     * <b>Method:	getIndicadoresByEstandar()  </b>
     * @method      Metodo para obtener todos los indicadores asociados a un estandar
     * @param		integer $estandar_id identificador del estandar
     * @return      arreglo con los campos
     * @author      Nohemi Rojas & Angelo Tamarones
     * @version     v1.1 03/10/11 03:34 PM
     */
    function getIndicadoresByEstandar($estandar_id) {
        $this->db->select('i.id AS value, i.nombre AS label');
        $this->db->from('dinamico.indicadores as i,dinamico.indicadores_estandares as ie ');
        $this->db->where('i.id = ie.indicador_id');
        $this->db->where('ie.estandar_id', $estandar_id);
        $this->db->where('i.eliminado', '0');
        $this->db->where('i.activo', '1');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getIndicadores()  </b>
     * @method      Metodo para obtener todos los indicadores
     * @return      arreglo con los campos
     * @author      Nohemi Rojas & Angelo Tamarones
     * @version     v1.1 03/10/11 03:34 PM
     */
    function getIndicadores($parentId = '') {
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('eliminado', '0');
        $this->db->where('chk_periodicidad', $parentId);
        $this->db->where('activo', '1');
        $query = $this->db->get('dinamico.indicadores');
        return $query->result();
    }

    /**
     * <b>Method:getEntidadesCD() </b>
     * @method	Metodo para obtener solo las entidades a las que se pueden relacionar agrupadores y campos dinamicos tales entidades
     * por ahora son: cuerpos_policiales, funcionarios, departamentos, instalaciones
     * @return	arreglo con las entidades mencionadas
     * @author	Jesus Farias 
     * @version	v1.0 31/10/11 2:26 PM
     */
    function getEntidadesCD() {
        $this->db->select('id AS value, nombre AS label');
        $this->db->from('dinamico.entidades');
        $this->db->where('esquema', 'estatico');
        $this->db->where('id IN (40,41,55,53)'); //cableado con las entidades correspondientes
        $this->db->where('eliminado', '0');
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getEntidades()</b>
     * @method		Obtiene todas las entidades del sistema filtrando por el esquema que se pase por parametro.
     * @param		string $param Cadena de texto con el esquema por el que se desea filtrar.
     * @return      mixed Arreglo con los registros que se encontraron, FALSE en caso de no encontrar registros.
     * @author		Mirwing Rosales
     * @version     v-1.0 28/10/11 04:47 PM
     * */
    function getEntidades() {
        $this->db->select('entidades.nombre AS label, entidades.id AS value');
        $this->db->from('dinamico.entidades');
        $this->db->where('entidades.esquema = \'estatico\'');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return FALSE;
    }

    /**
     * <b>Method:	getFrecuenciaByIndicador()  </b>
     * @method      Metodo para obtener la frecuencia asociada a un indicador
     * @param		integer $indicador_id identificador del indicador
     * @return      arreglo con los campos
     * @author      Nohemi Rojas
     * @version     v1.0 01/11/11 11:56 AM
     */
    function getFrecuenciaByIndicador($indicador_id) {
        $this->db->select('chk_periodicidad as value, chk_periodicidad as label');
        $this->db->from('dinamico.indicadores as i');
        $this->db->where('id ', $indicador_id);
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getAllByDepartamento()</b>
     * @method Metodo para obtener el organigrama de un determinado cuerpo policial.
     * @param  integer $cuerpo_policial_id Identificador del registro del cuerpo policial
     * @param  integer $root Identificador del registro padre
     * @return arreglo array con los identificadores de los departamentos hijos de un departamento padre. 
     * @author Angelo Tamarones
     * @version	v1.0 31/10/11 04:47 PM
     */
    public function getAllByDepartamento($cuerpo_policial_id, $root) {
        $arr_root = array('0' => $root);
        $results = $this->getChildrenByFather($cuerpo_policial_id, $root);
        $children = $this->_buildIdChildren($cuerpo_policial_id, $results, $arr_root);
        $tree = implode(",", $children);
        return $tree;
    }

    /**
     * <b>Method:  _buildIdChildren()</b>
     * @method Metodo Recursivo que permite construir los departamentos (collateral,staff y subordinate) de un determinado
     *         departamento.
     * @param  integer $cuerpo_policial_id Identificador del registro del cuerpo policial
     * @param  array $results arreglo con los departamentos (collateral,staff y subordinate) del departamento
     * @return array $tree arreglo que contiene los departamentos construidos de un cuerpo policial pertenecientes a un padre.
     * @author Angelo Tamarones & Nohemi Rojas
     * @version	v1.0 16/09/11 04:47 PM
     */
    private function _buildIdChildren($cuerpo_policial_id, $results, $arr_root, $band = FALSE, $i = NULL) {
        if (!$band) {
            $tree = array();
            $tree = array_merge($arr_root, $tree);
        }else
            $tree = $arr_root;

        $i = $i + 1;

        foreach ($results as $value) {
            //Evalua si no es subordinado
            if ($value->chk_tipo_jerarquia == 'Collateral')
                $tree = array_merge($tree, array($i => $value->id));

            if ($value->chk_tipo_jerarquia == 'Staff') {
                $result = $this->getChildrenByFather($cuerpo_policial_id, $value->id);
                $tree = array_merge($tree, array($i => $value->id));
                if ($result) {
                    $children = $this->_buildIdChildren($cuerpo_policial_id, $result, $tree, TRUE);
                    $tree = $children;
                }
            }

            if ($value->chk_tipo_jerarquia == 'Subordinate') {
                $result = $this->getChildrenByFather($cuerpo_policial_id, $value->id);
                $tree = array_merge($tree, array($i => $value->id));
                if ($result) {
                    $children = $this->_buildIdChildren($cuerpo_policial_id, $result, $tree, TRUE);
                    $tree = $children;
                }
            }
            $i = $i + 1;
        }
        return $tree;
    }

    /**
     * <b>Method: getChildrenByFather()</b>
     * @method Metodo para obtener el organigrama de los hijos, subordinados y colaterales de un padre de 
     *         un determinado cuerpo policial.
     * @param  integer $cuerpo_policial_id Identificador del registro del cuerpo policial
     * @param  integer $padre_id Identificador del registro del padre
     * @return array Arreglo que contiene los departamentos jerarquizados de un cuerpo policial pertenecientes a un padre. 
     * @author Angelo Tamarones & Nohemi Rojas
     * @version	v1.0 16/09/11 04:47 PM
     */
    function getChildrenByFather($cuerpo_policial_id, $padre_id) {
        $this->db->select('dp.id, dp.nombre, dp.chk_tipo_jerarquia, dp.padre_id, dp.cuerpo_policial_id');
        $this->db->from('estatico.departamentos dp');
        $this->db->from('estatico.cuerpos_policiales cpo');
        $this->db->where('cpo.id = ', $cuerpo_policial_id);
        $this->db->where('cpo.id = dp.cuerpo_policial_id');
        $this->db->where('cpo.activo', '1');
        $this->db->where('dp.padre_id', $padre_id);
        $this->db->where('cpo.eliminado', '0');
        $this->db->where('dp.eliminado', '0');
        $this->db->where('dp.activo', '1');
        $this->db->order_by('dp.chk_tipo_jerarquia', 'asc');
        $results = $this->db->get();
        return $results->result();
    }

    /**
     * <b>Method:   getCategorias() </b>
     * @method	Obtiene las categorias padres disponibles
     * @return	arreglo con los valores
     * @author	Nohemi Rojas, Angelo Tamarones
     * @version	v1.0 04/11/11 6:16 PM
     */
    function getCategorias() {
        $this->db->select('id AS value, etiqueta AS label');
        $this->db->from('dinamico.categorias');
        $this->db->where('categoria_id IS NULL');
        $this->db->where('eliminado', '0');
        $this->db->order_by('etiqueta', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getModeloVehiculo()</b>
     * @method		Obtiene los modelos de los vehiculos a partir de una marca.
     * @param		integer $marca_id Identificador del regsitro de la marca.
     * @return		mixed Array con los valores para cargar el comboboix.
     * @author		Mirwing Rosales
     * @version		v-1.0 07/11/11 10:09 AM
     * */
    function getModeloVehiculo($marca_id) {
        if (empty($marca_id))
            return FALSE;
        $this->db->select('modelos_vehiculos.id AS value, modelos_vehiculos.nombre AS label');
        $this->db->from('estatico.modelos_vehiculos');
        $this->db->where('desc_vehiculos_marcas', $marca_id);
        $this->db->order_by('modelos_vehiculos.nombre ASC');
        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result() : FALSE;
    }

    /**
     * <b>Method:	getCuerposPolicialesByRol()</b>
     * @method	Obtiene todos los cuerpos policiales segun el tipo de rol
     * @return	array Arreglo de objetos con los datos de los cuerpos policiales
     * @author	Angelo Tamarones
     * @version	v1.0 09/11/11 11:47 PM
     * */
    function getCuerposPolicialesByRol() {
        $cp_id = $this->CI->session->userdata('cuerpo_policial_id');
        $rol = $this->tank_auth->is_rol_type('CGP', $this->session->userdata('role_id'));
        $this->db->select('cp.id AS value, cp.nombre AS label');
        $this->db->from('estatico.cuerpos_policiales cp');
        $this->db->where('cp.eliminado', '0');
        $this->db->where('cp.activo', '1');
        $this->db->order_by('cp.nombre', 'asc');

        if (!$rol)
            $this->db->where('cp.id', $cp_id);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getAllOperaciones()</b>
     * @method		Rotorna todos los registros de operaciones
     * @return		Array $query->result() Arreglo de objetos con los detalles de todos los roles del sistema
     * @author		Eliel Parra, Reynaldo Rojas
     * @version		v1.0 14/11/11 10:46 AM
     * */
    function getAllOperaciones() {

        $rol_id = $this->session->userdata('role_edit_id');

        $this->db->select('id AS value');
        $this->db->select('name AS label');
        $this->db->order_by('name', 'asc');
        $this->db->where('regla_negocio !=', 'Placeholder');
        $this->db->where('eliminado', '0');
        $this->db->where("id NOT in (SELECT operation_id FROM rbac.operation_roles WHERE rol_id = $rol_id)");
        $query = $this->db->get('rbac.operations');
        if ($query->num_rows() > 0)
            return $query->result_array();
        else
            return FALSE;
    }

    /**
     * <b>Method:	getRoles()</b>
     * @method		Retorna los roles activos en el sistema
     * @param		string $eliminado '0' (por defecto) para registros NO eliminado, '1' para registros eliminados
     * @return		array Arreglo con los detalles de los roles activos del sistema, FALSE en caso de no haber registros
     * @author		Eliel Parra, Reynaldo Rojas
     * @version		v1.1 28/11/11 11:13 AM
     * */
    function getRoles() {

        $selected_user_id = $this->session->userdata('user_edit_id');
        $user_id = $this->session->userdata('user_id');

        //Buscar informacion del usuario seleccionado
        $this->db->where('id', $selected_user_id);
        $query = $this->db->get('rbac.users');
        $resultado = $query->row();

        //Si el usuario seleccionado es tipo CP o F
        if (!empty($resultado->cuerpo_policial_id)) {
            $where = "	AND ((rol.cuerpo_policial_id = $resultado->cuerpo_policial_id)
						OR (rol.chk_role_type IN ('CP','F') AND rol.cuerpo_policial_id IS NULL))";
        }
        //Si el usuario seleccionado es tipo CGP
        else
            $where = " AND rol.chk_role_type = 'CGP'";

        $query = "SELECT	id AS value,
							(chk_role_type||' - '||\"name\") AS label
						FROM rbac.roles rol 
						WHERE rol.eliminado = '0'
						$where
						AND rol.id NOT IN 
									(SELECT usr.rol_id 
									FROM rbac.users_roles usr 
									WHERE usr.user_id = $selected_user_id
									AND usr.eliminado = '0'
									AND usr.ffin IS NULL)
						ORDER BY label";
        $result = $this->db->query($query);
        return $result->result();
    }

    /**
     * <b>Method:	getRolesNoAsocWidget()</b>
     * @method		Retorna los roles no asociados a un Widget
     * @return		array Arreglo con los detalles de los roles asociados a un Widget, FALSE en caso de no haber registros
     * @author		Heiron Contreras
     * @version		v1.0 18/01/12 02:55 PM
     * */
    function getRolesNoAsocWidget() {

        $widget_id = $this->session->userdata('widget_id');

        //Buscar informacion del usuario seleccionado
        $this->db->where('id', $widget_id);
        $query = $this->db->get('dinamico.widgets');
        $resultado = $query->row();


        switch ($resultado->chk_role_type_widget) {
            case "CGP":
                $where = " AND rol.chk_role_type = 'CGP'";
                break;
            case "CP":
                $where = " AND rol.chk_role_type = 'CGP'";
                break;
            case "F":
                $where = " AND rol.chk_role_type = 'F'";
                break;
            case "CGCP":
                $where = " AND rol.chk_role_type IN ('CGP','CP')";
                break;
            case "T":
                $where = " AND rol.chk_role_type IN ('CP','F', 'CGP')";
                break;
        }

        $query = "SELECT	id AS value,
							(chk_role_type||' - '||\"name\") AS label
						FROM rbac.roles rol 
						WHERE rol.eliminado = '0'
						$where
						AND rol.id NOT IN 
									(SELECT wid.rol_id 
									FROM rbac.widgets_roles wid 
									WHERE wid.widget_id = $widget_id
									AND wid.eliminado = '0')
						ORDER BY label";
        $result = $this->db->query($query);
        return $result->result();
    }

    /**
     * <b>Method:	getRolesType()</b>
     * @method		Retorna los tipos de roles
     * @return		array Arreglo con los detalles de los tipos roles activos del sistema
     * @author		Reynaldo Rojas
     * @version		v1.0 23/11/11 03:38 PM
     * */
    function getRolesType() {

        $array_role_type = get_check('chk_role_type');
        if ($this->tank_auth->is_rol_type('CP', $this->session->userdata('role_id')))
            unset($array_role_type['CGP']);

        $array_result = array();

        foreach ($array_role_type AS $key_element => $element) {

            $array_aux = array('label' => $key_element, 'value' => $element);
            array_push($array_result, $array_aux);
        }

        return $array_result;
    }

    /**
     * <b>Method:	getUserRoles()</b>
     * @method		Retorna los roles activos de un usuario
     * @return		array Arreglo con el detalle de los roles activos del usuario
     * @author		Reynaldo Rojas
     * @version		v1.0 28/11/11 10:46 AM
     * */
    function getUserRoles() {

        if ($this->tank_auth->is_rol_type('CP', $this->session->userdata('role_id')))
            $where = "AND rol.chk_role_type = 'CP'";

        $user_id = $this->session->userdata('user_edit_id');
        $query = "SELECT	id AS value,
							(chk_role_type||' - '||\"name\") AS label
						FROM rbac.roles rol 
						WHERE rol.eliminado = '0'
						$where
						AND rol.id IN 
									(SELECT usr.rol_id 
									FROM rbac.users_roles usr 
									WHERE usr.user_id = $user_id 
									AND usr.eliminado = '0'
									AND usr.ffin IS NULL)
						ORDER BY label";
        $result = $this->db->query($query);
        return $result->result();
    }

    /**
     * <b>Method:	getRolesAsocWidget()</b>
     * @method		Retorna los roles activos de un Widget
     * @return		array Arreglo con el detalle de los roles activos del Widget
     * @author		Heiron Contreras
     * @version		v1.0 18/01/12 02:54 PM
     * */
    function getRolesAsocWidget() {
        $widget_id = $this->session->userdata('widget_id');

        $query = "SELECT	id AS value,
							(chk_role_type||' - '||\"name\") AS label
						FROM rbac.roles rol 
						WHERE rol.eliminado = '0'
						AND rol.id IN 
									(SELECT wid.rol_id 
									FROM rbac.widgets_roles wid 
									WHERE wid.widget_id = $widget_id
									AND wid.eliminado = '0')
						ORDER BY label";
        $result = $this->db->query($query);
        return $result->result();
    }

    /**
     * <b>Method: getAgrupadorByCuerpoPolicial()</b>
     * @method	 Metodo para obtener los agrupadores pertenecientes a la entidad cuerpo policial
     * @return	 array Arreglo con los agrupadores del cuerpo policial
     * @author	 Nohemi Rojas
     * @version	 v1.0 28/11/11 12:09 PM
     * */
    function getAgrupadorByCuerpoPolicial() {
        $this->db->select('id AS value');
        $this->db->select('nombre AS label');
        $this->db->order_by('nombre', 'asc');
        $this->db->where('eliminado', '0');
        $this->db->where("entidad_id", 40);
        $query = $this->db->get('dinamico.agrupadores');
        return $query->result_array();
    }

    /**
     * <b>Method: getInstalacionesByCuerpoPolicialAndUbicacion($ubicacionId) </b>
     * @method	Metodo para obtener las Instalaciones de un CP asociados a una ubicacion 
     * @param   integer $ubicacionId identificador de la ubicacion
     * @return	arreglo con los estandares
     * @author	Jesus Farias Lacroix 
     * @version	v1.0 28/11/11 6:30 pm
     */
    function getInstalacionesByCuerpoPolicialAndUbicacion($ubicacionId) {
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('cuerpo_policial_id', $this->session->userdata('cuerpo_policial_id'));
        $this->db->where('ubicacion_id', $ubicacionId);
        $this->db->where('eliminado', '0');
        $this->db->order_by('label', 'asc');
        $query = $this->db->get('estatico.instalaciones');
        return $query->result();
    }

    /**
     * <b>Method: getDptosNoAsoc($inst_id) </b>
     * @method	Metodo para obtener los departamentos NO asociados a una instalacion 
     * @param   integer $inst_id identificador de la instalacion
     * @return	arreglo con los estandares
     * @author	Jesus Farias Lacroix 
     * @version	v1.0 28/11/11 6:30 pm
     */
    function getDptosNoAsoc($inst_id) {
        $cp_id = $this->CI->session->userdata('cuerpo_policial_id');
        $this->db->select('id AS value, nombre AS label');
        $this->db->from('estatico.departamentos');
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $this->db->where('cuerpo_policial_id', $cp_id);
        $this->db->where("id NOT in (SELECT departamento_id FROM estatico.departamentos_instalaciones WHERE instalacion_id = $inst_id)");
        $this->db->order_by('label', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getDptosAsoc($inst_id) </b>
     * @method	Metodo para obtener los departamentos asociados a una instalacion 
     * @param   integer $inst_id identificador de la instalacion
     * @return	arreglo con los estandares
     * @author	Jesus Farias Lacroix
     * @version	v1.0 28/11/11 6:30 pm
     */
    function getDptosAsoc($inst_id) {
        $cp_id = $this->CI->session->userdata('cuerpo_policial_id');
        $this->db->select('id AS value, nombre AS label');
        $this->db->from('estatico.departamentos');
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $this->db->where('cuerpo_policial_id', $cp_id);
        $this->db->where("id  in (SELECT departamento_id FROM estatico.departamentos_instalaciones WHERE instalacion_id = $inst_id)");
        $this->db->order_by('label', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getInstNoAsoc($departamento_id) </b>
     * @method	Metodo para obtener las instalaciones NO asociados a un departamento
     * @param   integer $departamento_id identificador del departamento
     * @return	arreglo con los estandares
     * @author	Jesus Farias Lacroix 
     * @version	v1.0 28/11/11 6:30 pm
     */
    function getInstNoAsoc($departamento_id) {
        $this->db->select('id AS value, nombre AS label, ubicacion_id AS ubc_id');
        $this->db->where('cuerpo_policial_id', $this->session->userdata('cuerpo_policial_id'));
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $this->db->where("id NOT in (SELECT instalacion_id FROM estatico.departamentos_instalaciones WHERE departamento_id = $departamento_id AND activo ='1' AND eliminado = '0')");
        $query = $this->db->get('estatico.instalaciones');
        return $query->result();
    }

    /**
     * <b>Method: getInstNoAsoc($departamento_id) </b>
     * @method	Metodo para obtener las instalaciones asociados a un departamento
     * @param   integer $departamento_id identificador del departamento
     * @return	arreglo con los estandares
     * @author	Jesus Farias Lacroix 
     * @version	v1.0 28/11/11 6:30 pm
     */
    function getInstAsoc($departamento_id) {
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('cuerpo_policial_id', $this->session->userdata('cuerpo_policial_id'));
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $this->db->where("id in (SELECT instalacion_id FROM estatico.departamentos_instalaciones WHERE departamento_id = $departamento_id AND activo ='1' AND eliminado = '0')");
        $query = $this->db->get('estatico.instalaciones');
        return $query->result();
    }

    /**
     * <b>Method: getMarcasVehiculos()</b>
     * @method		Obtiene las marcas de vehiculos dependiendo del tipo.
     * @param		$typeId
     * @return		array Arreglo de objetos con los resultados encontrados
     * @author		Mirwing Rosales
     * @version		v-1.0 29/11/11 10:59 AM
     * */
    function getMarcasVehiculos($typeId) {
        if (empty($typeId))
            return FALSE;
        $this->db->select('mv.id AS value, mv.descriptor AS label');
        $this->db->from('estatico.descriptores mv');
        $this->db->from('estatico.modelos_vehiculos');
        $this->db->where('modelos_vehiculos.desc_vehiculos_marcas = mv.id');
        $this->db->where('modelos_vehiculos.desc_vehiculos_tipo', $typeId);
        $this->db->order_by('mv.descriptor ASC');
        $this->db->group_by('mv.id, mv.descriptor');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getFieldsForIndicador($periodicidad)</b>
     * @method	Obtiene los campos del que se puede alimentar la formula del indicador
     * @param	string $periodicidad periodicidad del indicador
     * @author	Nohemi Rojas, Jesus Farias 
     * @version	v1.0 05/12/11 7:45 pm
     */
    function getFieldsForIndicador($periodicidad = "") {
        $this->db->select('id AS value, etiqueta AS label');
        $this->db->from('dinamico.campos  c');
        $this->db->where('c.entidad_id', '40');
        $this->db->where('c.eliminado', '0');
        $this->db->where('c.activo', '1');
        $this->db->where('c.dinamico', '1');
        if ($periodicidad == 'Mensual')
            $this->db->where("c.chk_periodicidad = 'Mensual'");
        if ($periodicidad == 'Trimestral')
            $this->db->where("(c.chk_periodicidad = 'Mensual' OR c.chk_periodicidad = 'Trimestral')");
        if ($periodicidad == 'Semestral')
            $this->db->where("(c.chk_periodicidad = 'Mensual' OR c.chk_periodicidad = 'Trimestral' OR c.chk_periodicidad = 'Semestral' )");
        if ($periodicidad == 'Anual')
            $this->db->where("(c.chk_periodicidad = 'Mensual' OR c.chk_periodicidad = 'Trimestral' OR c.chk_periodicidad = 'Semestral' OR c.chk_periodicidad = 'Anual')");
        $this->db->or_where("(chk_periodicidad IS NULL AND sql IS NOT NULL) OR chk_periodicidad = 'N/A'");
        $this->db->order_by('label');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getPeriodosByIndicador()</b>
     * @method		Permite Obtener los periodos asociados a un Indicador especifico.
     * @param		inetger $id Identificador del registro del Indicador.
     * @return		array Arreglo de objetos para recargar el combobox de Visualizacion de Indicadores.
     * @author		Mirwing Rosales
     * @version		v-1.0 09/12/11 05:58 PM
     * */
    function getPeriodoByIndicador($id) {
        if (empty($id))
            $id = $this->session->userdata('indicador_id');
        $this->db->select('periodos.id AS value, periodos.nombre AS label');
        $this->db->from('dinamico.periodos, dinamico.indicadores');
        $this->db->where('indicadores.chk_periodicidad = periodos.chk_periodicidad ');
        $this->db->where('indicadores.id', $id);
        $this->db->order_by('label');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getPeriodosByRanking($id)</b>
     * @method		Permite Obtener los periodos asociados a un Ranking especifico.
     * @param		integer $id Identificador del registro del ranking.
     * @return		array Arreglo de objetos para recargar el combobox de Visualizacion de Ranking.
     * @author		Angelo Tamarones & Nohemi Rojas
     * @version		v1.0 15/12/11 11:45 AM
     * */
    function getPeriodosByRanking($id) {

        $this->session->set_userdata('raking_id_tmp', $id);
        $this->db->select('periodos.id AS value, periodos.nombre AS label');
        $this->db->from('dinamico.periodos, dinamico.rankings');
        $this->db->where('rankings.chk_periodicidad = periodos.chk_periodicidad ');
        $this->db->where('rankings.id', $id);
        $this->db->order_by('orden');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getRankings()  </b>
     * @method      Metodo para obtener todos los ranking
     * @return      arreglo con los campos
     * @author      Angelo Tamarones & Nohemi Rojas  
     * @version     v1.0 15/12/11 11:56 AM
     */
    function getRankings() {
        $this->db->select('id AS value, nombre AS label');
        $this->db->from('dinamico.rankings');
        $this->db->where('activo', '1');
        $this->db->where('eliminado', '0');
        $this->db->order_by("codigo");
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getRankingsCalculate()  </b>
     * @method      Metodo para obtener todos los ranking activos para el calculo de rankings segun su periodo_id
     * @return      arreglo con los campos
     * @author      Angelo Tamarones & Nohemi Rojas  
     * @version     v1.0 15/12/11 11:56 AM
     */
    function getRankingsCalculate($anio) {
         if (!is_numeric($anio))
            return FALSE;
        $periodo_id = $this->session->userdata('periodo_tmp_id');
        $this->db->select('rd.id AS value, r.nombre AS label');
        $this->db->from('dinamico.rankings r, dinamico.rankings_definiciones rd');
        $this->db->where('r.id = rd.ranking_id');
        $this->db->where('rd.periodo_id', $periodo_id);
        $this->db->where('r.activo', '1');
        $this->db->where('rd.anio', $anio);
        $this->db->where('r.eliminado', '0');
        $this->db->where('rd.activo', '1');
        $this->db->where('rd.eliminado', '0');
        $this->db->order_by("r.codigo");
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getPeriodoByAnio($periodo_id)  </b>
     * @method      Metodo para obtener todos los ranking activos para el calculo de rankings segun su periodo_id
     * @return      arreglo con los campos
     * @author      Angelo Tamarones & Nohemi Rojas  
     * @version     v1.0 15/12/11 11:56 AM
     */
    function getPeriodoByAnio($periodo_id) {
       if (empty($periodo_id))
            return FALSE;
        $this->session->set_userdata('periodo_tmp_id', $periodo_id);
        $this->db->select('DISTINCT rd.anio AS value, rd.anio AS label', false);
        $this->db->from('dinamico.rankings r, dinamico.rankings_definiciones rd');
        $this->db->where('r.id = rd.ranking_id');
        $this->db->where('rd.periodo_id', $periodo_id);
        $this->db->where('r.activo', '1');
        $this->db->where('r.eliminado', '0');
        $this->db->where('rd.activo', '1');
        $this->db->where('rd.eliminado', '0');
        $this->db->order_by("value ASC");
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:getAreasEstandarizacionByRanking($area_id) </b>
     * @method	Metodo para obtener las areas de estandarizacion que posean estandares asociados
     * @param	string $chk_periodicidad periodicidad
     * @return	arreglo con las areas de estandarizacion
     * @author	Angelo Tamarones & Nohemi Rojas  
     * @version	v1.0 23/12/11 04:10 PM
     */
    function getAreasEstandarizacionByRanking($chk_periodicidad) {
        $this->db->select('DISTINCT areas_estandarizacion.id as value,areas_estandarizacion.nombre as label', false);
        $this->db->from('dinamico.indicadores_estandares, dinamico.estandares, dinamico.indicadores, dinamico.areas_estandarizacion');
        $this->db->where('indicadores_estandares.indicador_id = indicadores.id');
        $this->db->where('estandares.id = indicadores_estandares.estandar_id');
        $this->db->where('estandares.area_estandarizacion_id = areas_estandarizacion.id');

        if (($chk_periodicidad == 'Semestral') OR ($chk_periodicidad == 'Trimestral') OR ($chk_periodicidad == 'Mensual')) {
            if ($chk_periodicidad == 'Semestral')
                $this->db->where("(indicadores.chk_periodicidad = 'Mensual' OR indicadores.chk_periodicidad = 'Trimestral' OR indicadores.chk_periodicidad = 'Semestral')");
            else if ($chk_periodicidad == 'Trimestral')
                $this->db->where("(indicadores.chk_periodicidad= 'Mensual' OR indicadores.chk_periodicidad = 'Trimestral')");
            else if ($chk_periodicidad == 'Mensual')
                $this->db->where('indicadores.chk_periodicidad', 'Mensual');
        }

        $this->db->where('indicadores_estandares.eliminado', '0');
        $this->db->where('estandares.eliminado', '0');
        $this->db->where('indicadores.eliminado', '0');
        $this->db->where('areas_estandarizacion.eliminado', '0');
        $this->db->order_by('label');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * <b>Method:getEntidadesReporte() </b>
     * @method	Metodo para obtener solo las entidades  que se pueden relacionar con reportes
     * por ahora son: cuerpos_policiales, funcionarios, departamentos, instalaciones, armas, vehiculos
     * @return	arreglo con las entidades mencionadas
     * @author	Jesus Farias 
     * @version	v1.0 22/12/11 6:17 PM
     */
    function getEntidadesReporte() {
        $this->db->select('id AS value, nombre AS label');
        $this->db->from('dinamico.entidades');
        $this->db->where('esquema', 'estatico');
        $this->db->where('id IN (36,40,41,55,53,73)'); //cableado con las entidades correspondientes
        $this->db->where('eliminado', '0');
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getUbicacionByAmbito()  </b>
     * @method      Metodo para obtener las ubicaciones dependiendo del Ambito Politico Territorial
     * @return      arreglo con los campos
     * @author      Mirwing Rosales
     * @version     v1.0 27/01/12 12:43 PM
     */
    function getUbicacionByAmbito($ambito) {
        if (empty($ambito) OR $ambito == 'Estado' OR $ambito == 'Pais')
            return FALSE;
        $this->db->select('id AS value, nombre AS label, chk_tipo_ubicacion');
        $this->db->from('estatico.ubicaciones');
        $this->db->where('chk_tipo_ubicacion', 'Estado');
        $this->db->order_by("nombre ASC");
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:getReportesForTipo() </b>
     * @method	Metodo  que obtiene los tipos de reportes definidos y que puede ser usado por un indicador
     * @return	arreglo con los registros
     * @author	Nohemi Rojas, Angelo Tamarones
     * @version	v1.2 17/02/12 03:51 PM
     */
    function getReportesForTipo() {
        $indicador_id = $this->session->userdata('indicador_id');
        $arr_ind = $this->getIndicadorById($indicador_id);
        //Tipo %
        if ($arr_ind['chk_formula'] == 'porcentaje') {
            if ($this->getCategoriaByIndicador($indicador_id, 'porcentaje'))
                $this->db->or_where('r.parametro', 'Porcentaje_128');
            $this->db->or_where('r.parametro', 'Porcentaje');
        }//Tipo VD
        if ($arr_ind['chk_formula'] == 'valor_directo') {
            if ($this->getCategoriaByIndicador($indicador_id, 'valor_directo'))
                $this->db->where('r.parametro', 'ValorDirecto_128');
            else
                $this->db->where('r.parametro', 'Unico');
        }//Tipo Cociente
        if ($arr_ind['chk_formula'] == 'cociente') {
            $this->db->where('r.parametro', 'Unico');
        }
        $this->db->select("id as value, nombre as label");
        $this->db->from('estatico.reportes r');
        $this->db->or_where('r.parametro', 'Normal');
        $this->db->where('r.sql', NULL);
        $this->db->where('r.eliminado', '0');
        $this->db->order_by('label', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:getIndicadorById()  </b>
     * @method	Metodo para obtener un indicador determinado
     * @param	integer $id identificador del registro
     * @param	boolean $activo indica si se desea buscar un indicador activo o no
     * @return	arreglo con los campos
     * @author	Nohemi Rojas & Angelo Tamarones
     * @version	v1.0 03/10/11 02:52 PM
     */
    private function getIndicadorById($id, $activo = '1') {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->where('eliminado', '0');
        $this->db->where('activo', $activo);
        $query = $this->db->get('dinamico.indicadores');
        return $query->row_array();
    }

    /**
     * <b>Method:getIndicador()  </b>
     * @method        Metodo para obtener un indicador
     * @return        arreglo con los campos
     * @author        Nohemi Rojas & Angelo Tamarones
     * @version        v1.0 25/01/12 03:34 PM
     */
    function getIndicador() {
        $this->session->userdata('indicador_id');
        $this->db->select('id AS value, nombre AS label');
        $this->db->where('id', $this->session->userdata('indicador_id'));
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $query = $this->db->get('dinamico.indicadore s');
        return $query->row();
    }

    /**
     * <b>Method: getPeriodoByReporteIndicador()</b>
     * @method		Permite Obtener los periodos asociados a un reporte de un indicador
     * @return		array Arreglo de con los registros
     * @author		Angelo Tamarones & Nohemi Rojas
     * @version		v1.0 25/01/12 02:48 PM
     * */
    function getPeriodoByReporteIndicador() {

        $reporte_ind_id = $this->session->userdata('reporte_ind_id');
        $this->db->select('p.id AS value, p.nombre AS label');
        $this->db->from('dinamico.periodos as p, dinamico.indicadores as i, estatico.reporte_indicadores as re');
        $this->db->where('i.chk_periodicidad = p.chk_periodicidad ');
        $this->db->where('i.id = re.indicador_id ');
        $this->db->where('re.id', $reporte_ind_id);
        $this->db->order_by('label');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method: getUbicacionByAmbito()</b>
     * @method		Obtiene las ubicaciones asociadas un registro dependiendo el ambito que abarque.
     * @param		integer $id Identificador del registro del cual se desea averiguar la ubicacion.
     * @return		array Arreglo de datos con la informacion recolectada en la consulta.
     * @author		Mirwing Rosales, Jesús Farías Lacroix <jesus.farías@gmail.com>
     * @version		v-1.0 22/02/12 5:16 pm
     * */
    function getUbicacionByPadre($id, $bPIntersect = false) {
        if (empty($id))
            return false;

        $strWhereAux = "";
        if (!empty($bPIntersect))
            $strWhereAux = " WHERE id IN( SELECT estatico.practicas_ubicaciones.ubicacion_id			  
										FROM estatico.practicas_ubicaciones
										WHERE estatico.practicas_ubicaciones.buena_practica_id=$bPIntersect
							) ";


        $query = "WITH RECURSIVE sub_ubicaciones AS(
					SELECT * FROM estatico.ubicaciones WHERE id IN ($id)
					UNION ALL  
					SELECT u.* FROM estatico.ubicaciones AS u INNER JOIN sub_ubicaciones AS su
					ON u.padre_id = su.id
				)
			SELECT DISTINCT	id AS value,
				COALESCE(padre_id,0) AS padre_id,
				CASE chk_tipo_ubicacion 
					WHEN 'Estado' THEN '-(' || (substring(chk_tipo_ubicacion from '^...')) || ')-' || nombre
					WHEN 'Municipio' THEN '--(' || (substring(chk_tipo_ubicacion from '^...')) || ')-' || nombre
					WHEN 'Parroquia' THEN '---(' || (substring(chk_tipo_ubicacion from '^...')) || ')-' || nombre
					ELSE '(' || (substring(chk_tipo_ubicacion from '^...')) || ')-' || nombre
				END AS label  
			FROM sub_ubicaciones 
			$strWhereAux
			ORDER BY padre_id, id";
        $result = $this->db->query($query);
        $dbUbicaciones = $result->result();
        if (empty($bPIntersect)) {
            $responseUbicaciones = array();
            $this->findUbicacionesChild($dbUbicaciones[0], $dbUbicaciones, $responseUbicaciones);
            return $responseUbicaciones;
        }else
            return $dbUbicaciones;
    }

    private function findUbicacionesChild($current, &$dbUbc, &$responseUbc) {
        $responseUbc[] = $current;
        foreach ($dbUbc as $item) {
            if ($item->padre_id == $current->value)
                $this->findUbicacionesChild($item, $dbUbc, $responseUbc);
        }
    }

    /**
     * <b>Method:	getCuerposPolicialesByAmbito()</b>
     * @method		Obtiene todos los cuerpos policiales por ambito politico territorial
     * @return		array Arreglo de objetos con los datos de los cuerpos policiales
     * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
     * @version		v1.0 01/02/12 6:00 PM
     * */
    function getCuerposPolicialesByAmbito($param) {
        if (empty($param))
            return false;
        switch ($param) {
            case 'Pais' : $this->db->where('chk_ambito_politico_ter', 'Nacional');
                break;
            case 'Estado' : $this->db->where('chk_ambito_politico_ter', 'Estadal');
                break;
            case 'Municipio' : $this->db->where('chk_ambito_politico_ter', 'Municipal');
                break;
            default:
                if (is_numeric($param)) {
                    $arrUb = array();
                    $arr_ubc = $this->getUbicacionByPadre($param);
                    array_pop($arr_ubc);
                    foreach ($arr_ubc as $key => $value)
                        $arrUb[] = $value->value;
                    $arrUb = implode(',', $arrUb);
                    $this->db->where('chk_ambito_politico_ter', 'Municipal');
                    $this->db->where("ubicacion_id IN ($arrUb)");
                }else
                    return false;
                break;
        }
        $this->db->select('id AS value, nombre AS label');
        $this->db->from('estatico.cuerpos_policiales');
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $this->db->order_by('nombre');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getCpsWithUbications()</b>
     * @method		Obtiene todos los cuerpos policiales por una lista de ids, 
     *              y como atributo value obtiene es ubicacion_id
     * @return		array Arreglo de objetos con los datos de los cuerpos policiales
     * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
     * @version		v1.0 02/02/12 4:35 PM
     * */
    function getCpsWithUbications($cpsIds) {
        if (empty($cpsIds))
            return false;
        $this->db->select('ubicacion_id AS value, nombre AS label, chk_ambito_politico_ter as ambito');
        $this->db->from('estatico.cuerpos_policiales');
        $this->db->where("id IN ($cpsIds)");
        $this->db->where('eliminado', '0');
        $this->db->where('activo', '1');
        $this->db->order_by('nombre');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getCategoriaByIndicador()</b>
     * @method		Permite obtener si existe o no un indicador que sea valor directo y su campo de alimentacion boolean
     * @param		integer $indicador_id identificador del indicador
     * @param		string $formula formula del indicador
     * @return		boolean V/F en caso de que existan registro o no 
     * @author		Angelo Tamarones & Nohemi Rojas
     * @version		v1.0 16/02/12 4:00 PM
     * */
    private function getCategoriaByIndicador($indicador_id, $formula) {
        $this->db->from('dinamico.indicadores as i');
        $this->db->from('dinamico.indicadores_campos');
        $this->db->from('dinamico.campos');
        $this->db->where('indicadores_campos.indicador_id = i.id');
        $this->db->where('indicadores_campos.campo_id = campos.id');
        $this->db->where("i.chk_formula = '$formula'");
        $this->db->where("(categoria_id = 128 OR sql ilike '%c.id %' )");
        $this->db->where('i.id', $indicador_id);
        return ($this->db->count_all_results() > 0);
    }

    /**
     * <b>Method:	getCPByAmbitoPolTerr()</b>
     * @method		Obtiene todos los cuerpos policiales por ambito politico territorialtributo value obtiene es ubicacion_id
     * @return		array Arreglo de objetos con los datos de los cuerpos policiales
     * @author		Angelo Tamarones
     * @version		v1.0 22/02/12 3:35 PM
     * */
    function getCPByAmbitoPolTerr($param) {
        if (empty($param))
            return false;
        if ($param == 'Por Estado') {
            $this->db->where('ubic.chk_tipo_ubicacion', 'Estado');

            $this->db->from('estatico.ubicaciones ubic');
            $this->db->from('estatico.cuerpos_policiales cp');
        }else
            return FALSE;
        $this->db->select('ubic.id AS value, ubic.nombre AS label');
        $this->db->where('ubic.eliminado', '0');
        $this->db->where('cp.eliminado', '0');
        $this->db->where('cp.ubicacion_id = ubic.id');
        $this->db->where('cp.activo', '1');
        $this->db->order_by('ubic.nombre');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * <b>Method:	getUsersByRoleType()</b>
     * @method		Metodo que trae todos los usuarios por tipo de rol
     * @return		array arreglo Con los usuarios
     * @author		Jesús Farías
     * @version		v1.0 04/06/12 04:47 PM
     * */
    function getUsersByRoleType($param) {

        $cpId = $this->CI->session->userdata('cuerpo_policial_id');
        $auxWhere = (($this->CI->session->userdata('chk_role_type')) != 'CGP') ? " AND u.cuerpo_policial_id= $cpId " : "";
        $auxWhere.=(!empty($param)) ? " AND r.chk_role_type='$param' " : "";

        $strQuery = "SELECT DISTINCT(u.id) AS value,			
						   COALESCE((u.first_name ||' '|| u.last_name), (p.primer_nombre ||' '|| p.primer_apellido)) AS label
					FROM   rbac.users as u
					       INNER JOIN rbac.users_roles  ur ON(ur.user_id=u.id AND ur.eliminado='0')
					       INNER JOIN rbac.roles r ON (r.id=ur.rol_id AND r.eliminado='0')
                           LEFT JOIN estatico.personas p ON (u.persona_id=p.id AND p.eliminado='0')
					WHERE  u.eliminado='0' $auxWhere 
                    ORDER BY 2,1 ; ";
        $result = $this->db->query($strQuery);
        return $result->result();
    }

    /**
     * <b>Method:	getAllDepart()</b>
     * @method		Metodo que trae todos los Departamentos de un cuerpo_policial
     * @param		Integer $cuerpo_policial_id
     * @return		array arreglo Con los registros del los departamentos
     * @author		Angelo Tamarones, Nohemi Rojas
     * @version		v1.0 16/09/11 04:47 PM
     * */
    function getAllDepart() {
        $cp_id = $this->CI->session->userdata('cuerpo_policial_id');
        if (empty($cp_id))
            return false;
        $departamento_id = $this->CI->session->userdata('departamento_id');
        $cuerpo_policial_id = $this->session->userdata('cuerpo_policial_id');
        $this->db->select('dp.id AS value, dp.nombre AS label');
        $this->db->from('estatico.departamentos dp');
        $this->db->where('dp.eliminado', '0');
        $this->db->where('dp.activo', '1');
        $this->db->where('dp.cuerpo_policial_id', $cuerpo_policial_id);
        $this->db->order_by('dp.nombre', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
    
     /**
     * <b>Method:	getAllEstado()</b>
     * @method		Metodo que trae todos los estados del territorio nacional
     * @return		array arreglo Con los registros del los estado
     * @author		Angelo Tamarones, Maycol Alvarez
     * @version		v1.0 08/06/12 02:17 PM
     * */
    function getAllEstado() {
        $this->db->select('u.id AS value, u.nombre AS label');
        $this->db->from('estatico.ubicaciones u');
        $this->db->where('u.chk_tipo_ubicacion', 'Estado');
        $this->db->where('u.eliminado', '0');
        $this->db->order_by('u.nombre', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
    
     /**
     * <b>Method:	getAllMunicipio()</b>
     * @method		Metodo que trae todos los estados del territorio nacional
     * @return		array arreglo Con los registros del los estado
     * @author		Angelo Tamarones, Maycol Alvarez
     * @version		v1.0 08/06/12 02:17 PM
     * */
    function getAllMunicipio($params) {
        if (empty($params))
            return FALSE;
        if (is_numeric($params)){
             $this->db->where('u.padre_id', $params);
        }else {
            $this->db->from('estatico.ubicaciones estado');
            $this->db->where('estado.nombre', $params);
            $this->db->where('estado.id = u.padre_id');
         }     
        $this->db->select('u.id AS value, u.nombre AS label');
        $this->db->from('estatico.ubicaciones u');
        $this->db->where('u.chk_tipo_ubicacion', 'Municipio');
        $this->db->where('u.eliminado', '0');
        $this->db->order_by('u.nombre', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function CLDemoT($id){
       return array(
           array(
               'value' => 0,
               'label' => 'dato1'.$id
           ),   
           array(
               'value' => 1,
               'label' => 'dato2'.$id
           ),     
       );
    }
}
