<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Upload_model Class
 * 
 * @package          Upload
 * @subpackage       Controllers
 * @author           Juan Carlos Lopez
 * @copyright        Por definir
 * @license          Por definir
 * @version          v1.0 26/10/11 04:25 PM
 *  * */
class Upload_model extends CI_Model {

	var $table = 'estatico.archivos';
	var $table_operations = 'rbac.operation';

	public function __construct() {
		parent::__construct();
                $this->load->helper('file');
	}

	/**
	 * <b>Method:	create()</b>
	 * Crea el registro de un nuevo archivo
	 * @param		array $data Detalles del archivo a crear
	 * @return		integer $this->db->insert_id() Numero identificador del registro creado
	 * @author		Eliel Parra / Juan Carlos Lopez Guillot
	 * @version		v1.0 01/11/11 07:00 PM
	 * */
	function create($data) {
		$this->_format($data, 'INSERT');
		if ($this->db->insert($this->table, $data))
			return $this->db->insert_id();
	}


	
	/**
	 * <b>Method:	createUpdateArray()</b>
	 * Genera el arreglo con los datos a modificar en un archivo
	 * @param		integer $archivo_id Identificador del archivo
	 * @param		array $data Arreglo con los datos a modificar
	 * @return		boolean TRUE/FALSE
	 * @author		Eliel Parra / Juan Carlos Lopez Guillot
	 * @version		v1.0 04/11/11 01:53 PM
	 **/
	function updateFile($archivo_id, $data) {
            $this->db->where('id', $archivo_id);
            return $this->db->update('files.file', $data);
	}
	
	
	/**
	 * <b>Method:	getTipoCampo($id)</b>
	 * Retorna el tipo de campo si es fileupload o fileuploadplus. 
	 * @param		Integer $id identificador del campo en la tabla campos al cual esta asociado el archivo
	 * @return		String nombre del directorio raiz
	 * @author		Eliel Parra / Reynaldo Rojas
	 * @version		v1.0 03/11/11 02:46 PM
	 * */
	function getTipoCampo($id) {
		
		$this->db->where('deleted', '0');
		$this->db->where('id', $id);
		$this->db->select('tipo_campo');
		$query = $this->db->get('dinamico.campos');
		$result = $query->row();
		return $result->tipo_campo;
	}

	/**
	 * <b>Method:	listAll()</b>
	 * Mustra todas las imagenes asociadas al campo y a la instancia seleccionada
	 * @param		integer $limit Valor del limit para el query
	 * @param		integer $start Offset para el query
	 * @param		integer $campo_id Identificador del campo
	 * @param		integer $instancia_id Identificador de la instancia
	 * @return		array $salida Arreglo con los datos asociados a las imagenes seleccionadas
	 * @author		Juan Carlos Lopez / Eliel Parra
	 * @version		v2.0 02/11/11 04:09 PM
	 * */
	function listAll($limit, $start, $gallerie_id) {
		$query = "	SELECT	arch.*
					FROM files.file arch
					JOIN files.gallerie gale
					ON arch.gallerie_id = gale.id
					WHERE gale.id = $gallerie_id
					AND gale.deleted = '0'
					AND arch.deleted = '0'
					LIMIT $limit
					OFFSET $start";
		$result = $this->db->query($query);
                
                $query2 = "	SELECT	arch.*
					FROM files.file arch
					JOIN files.gallerie gale
					ON arch.gallerie_id = gale.id
					WHERE gale.id = $gallerie_id
					AND gale.deleted = '0'
					AND arch.deleted = '0'";
                $result2 = $this->db->query($query2);
                $numRows = $result2->num_rows();
                
		if ($result->num_rows() > 0) {
			$lasImagenes = array();
			foreach ($result->result() as $row) {
				switch ($row->file_type) {
					case '.jpg':
					case '.png':
					case '.jpeg':
					case '.gif':
						$arch = array(
							'name' => $row->title,
							'thumb_url' => base_url() . $row->file_path . '/peq_' . $row->file_name,
							'id' => $row->id
						);
						break;
					case '.doc':
					case '.docx':
					case '.odt':
					case '.rtf':
						$arch = array(
							'name' => $row->title,
							'thumb_url' => base_url() . 'assets/img/icon_file/MS-Office-2003-Word-icon.png',
							'id' => $row->id
						);
						break;
					case '.ods':
					case '.xls':
					case '.xlsx':
					case '.csv':
						$arch = array(
							'name' => $row->title,
							'thumb_url' => base_url() . 'assets/img/icon_file/MS-Office-2003-Excel-icon.png',
							'id' => $row->id
						);
						break;
					case '.pdf':
						$arch = array(
							'name' => $row->title,
							'thumb_url' => base_url() . 'assets/img/icon_file/PDF-icon.png',
							'id' => $row->id
						);
						break;
					case '.txt':
						$arch = array(
							'name' => $row->title,
							'thumb_url' => base_url() . 'assets/img/icon_file/Document-Copy-icon.png',
							'id' => $row->id
						);
						break;
					case '.pps':
					case '.ppt':
					case '.odp':
					case '.pptx':
						$arch = array(
							'name' => $row->title,
							'thumb_url' => base_url() . 'assets/img/icon_file/MS-Office-2003-PowerPoint-icon.png',
							'id' => $row->id,
						);
						break;
				}
				$lasImagenes[] = $arch;
			}
			$salida = array('result' => $numRows, 'images' => $lasImagenes);
		} else {
			$salida = 'No hay archivos que mostrar';
		}
		return $salida;
	}
	
	

	/**
	 * <b>Method:	deleteFile()</b>
	 * Elimina los archivos seleccionados
	 * @return		boolean TRUE/FALSE
	 * @author		Eliel Parra / Juan Carlos Lopez Guillot
	 * @version		v1.0 04/11/11 01:09 PM
	 **/
	function deleteFile($id) {		
            $data = array('deleted' => '1');
            $this->db->where('id', $id);
            return $this->db->update('files.file', $data);
	}
	

	/**
	 * <b>Method:	fileDetail()</b>
	 * Seleccionamos los datos de un archivo desde su id
	 * @param		$id
	 * @return		return
	 * @author		Juan Carlos Lopez
	 * @version		v1.0 01/11/11 02:06 PM
	 * */

	function fileDetail($id) {
		$this->db->from('files.file');
		$this->db->where('id', $id);
		$this->db->where('deleted', '0');
		$query = $this->db->get();
		return $query->row_array();
	}

	/**
	 * <b>Method:	unsetSessionFile()</b>
	 * Metodo para reiniciar los valores de las variables de sesion que manejan el proceso de archivo
	 * @author		Eliel Parra / Reynaldo Rojas / Juan Carlos Lopez Guillot
	 * @version		v1.0 03/11/11 10:10 AM
	 * */

	function unsetSessionFile() {
		$this->session->unset_userdata('archivos');
		$this->session->unset_userdata('relaciones_archivos');
		$this->session->unset_userdata('delete_archivos');
		$this->session->unset_userdata('update_archivos');
	}
	

	
	/**
	 * <b>Method:	_format($arr_format)</b>
	 * Limpia el arreglo que viene del formulario para que sea compatible con el insert y el update
	 * @param		array $arr_format arreglo con data original
	 * @param		string $type tipo de formato: 'INSERT', 'UPDATE'
	 * @return		array $arr_format arreglo formateado
	 * @author		Eliel Parra / Juan Carlos Lopez Guillot
	 * @version		v1.0 19/10/11 03:25 PM
	 * */
	private function _format(&$arr_format, $type) {

		if ($type == 'INSERT')
			unset($arr_format['id']);
		$arr_format['extension'] = strtolower($arr_format['extension']);
		unset($arr_format['submit']);
		unset($arr_format['reset']);
		if (isset($arr_format['observaciones']) && (empty($arr_format['observaciones'])))
			$arr_format['observaciones'] = NULL;
	}
	
	/**
	 * <b>Method:	getOperationType()</b>
	 * Retorna el tipo de operacion Edit o Ver
	 * @param		$op_id
	 * @return		$op_type
	 * @author		Eliel Parra / Juan Carlos Lopez Guillot
	 * @version		v1.0 03/08/12 03:50 PM
	 **/
	function getOperationType($opId) {
		
		$this->db->where('id', $opId);
		$this->db->where('deleted', '0');
		$query = $this->db->get($this->table_operations);
		if(!$query)
			return FALSE;
		$row = $query->row();
                $url = $row->url;
                $expl_url = explode('/', $url);
                if($expl_url == 'detail'){
                    $fileType = 'detail';
                } else {
                    $fileType = 'upload';
                }
		return $fileType;
	}
        
        	/**
	 * <b>Method:           createGallerie()</b>
	 * Retorna el id de la galeria a usuario y el path donde guardar
	 * @param		$dataProcess
	 * @return		$file_data
	 * @author		Juan Carlos Lopez Guillot
	 * @version		v1.0 06/09/12 03:50 PM
	 **/
        function createGallerie($file_field_id, $created_by){
            $dataInsert = array(
                                'field_id' => $file_field_id,
                                'deleted' => '0',
                                'created_by' => $created_by
                                );
            $this->db->insert('files.gallerie', $dataInsert);
            $gallerie_id = $this->db->insert_id();
            return $gallerie_id;
        }
        
        
        
 	/**
	 * <b>Method:           destroyGallerie()</b>
	 * Destruye la galeria temporal si ha ocurrido un error al subir el archivo
	 * @param		$gallerie_id
	 * @return		TRUE or FALSE
	 * @author		Juan Carlos Lopez Guillot
	 * @version		v1.0 07/09/12 03:50 PM
	 **/           
        function destroyGallerie($gallerie_id){
            $this->db->where('id', $gallerie_id);
            return $this->db->delete('files.gallerie');
        }
        
        
  	/**
	 * <b>Method:           insertFile()</b>
	 * Inserta archivo en la tabla file, asociado a una galeria
	 * @param		$dataFileInsert
	 * @return		TRUE or FALSE
	 * @author		Juan Carlos Lopez Guillot
	 * @version		v1.0 07/09/12 03:50 PM
	 **/        
        function insertFile($dataFileInsert){
            return $this->db->insert('files.file', $dataFileInsert);
        }
        
        
        
        
         /**
	 * <b>Method:           clearUnliked()</b>
	 * Elimina de la base de datos y de las carpetas todas las galerias y archivos asociados 
                                * que no esten vinculados a un campo de tipo fileupload
	 * @param		
	 * @return		TRUE or FALSE
	 * @author		Juan Carlos Lopez Guillot
	 * @version		v1.0 07/09/12 03:50 PM
	 **/     
        function clearUnliked(){
            
            $clave = "fileupload";
            
            $sql = "SELECT vf._name AS field_name, ve._name AS entity_name, ve._schema AS entity_schema
                    FROM rbac.operation_field AS rb_of
                    JOIN virtualization.field AS vf ON rb_of.field_id = vf.id
                    JOIN virtualization.entity AS ve ON vf.entity_id = ve.id
                    WHERE rb_of.category_ext_component_id = (
                                                    SELECT cat.id AS id FROM virtualization.category AS cat 
                                                    WHERE cat._table = 'ext_component' 
                                                    AND cat._name = '$clave'
                                                    ) 
                    AND rb_of.deleted = '0' 
                    AND vf.deleted = '0' 
                    AND ve.deleted = '0' 
                    GROUP BY field_name, entity_schema, entity_name";
            $result = $this->db->query($sql);
            
            //Construimos la linea de id separados por , que se introducira en el NOT IN para sanear las tablas galerias y files
            $notIn = array();
            foreach($result->result() as $row){
                $field_name     = $row->field_name;
                $entity_schema  = $row->entity_schema;
                $entity_name    = $row->entity_name;
                $sql2 = "SELECT $field_name FROM $entity_schema.$entity_name";
                $result2 = $this->db->query($sql2);
                foreach($result2->result() as $row2){
                    $notIn[] = $row2->$field_name;
                }
            }
            
            //Seleccion de los path que se van a eliminar en funcion de los fields que tienen galerias asociada
            $sql3 = "SELECT f.file_path AS  FROM files.gallerie AS g
                    JOIN files.file AS f ON f.gallerie_id = g.id
                    WHERE g.id NOT IN ( implode(', ', $notIn) )
                    AND g.created_at > now()::timestamp without time zone -'2 day'::interval
                    AND g.deleted = '0'
                    AND f.deleted = '0'
                    GROUP BY f.file_path";
            $result3 = $this->db->query($sql3);
            foreach($result3->result() as $row3){
//                delete_files('./path/to/directory/', TRUE);
                delete_files($row3->file_path, TRUE);                
            }
            
            //Eliminar definitivamente las gallerias y los files asociados en cascada mayor de dos antes
            $sqlDel = "DELETE FROM files.gallerie AS g
                    WHERE g.id NOT IN ( implode(', ', $notIn) ) 
                    AND g.created_at > now()::timestamp without time zone -'2 day'::interval
                    AND g.deleted = '0'";
            $this->db->query($sqlDel);
            
        }
}

?>