<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Virtualization_model class
 * @package		module: adm_virtualization
 * @subpackage		models
 * @author		Juan Carlos Lopez Guillot
 * @copyright		Por definir
 * @license		Por definir
 * @version		v0.1 18/09/12 02:59 PM
 * */
class Virtualization_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->load->library('table');
                $this->load->helper('inflector');
                $this->load->helper('file');
	}
        

	/**
	 * <b>Method:	outOfStock</b>
	 * @method		Consulta las instancias que no existentes en base de datos 
	 * @param		
	 * @return		Array $data Arreglo con los detalles de las instancias
	 * @author		Juan Carlos Lopez Guillot
	 * @version		v1.0 16/12/11 03:31 PM
	 * */
        
         
                     
                
        function virtualizateEntity($tabla_name, $table_schema='business_logic'){
            $created_by = $this->session->userdata('user_id');
            //Seleccionamos las entidades que no han sido virtualizadas
            //Insertamos las entidades en la tabla entidades
            //Seleccionamos los campos con sus atributos de las entidades que no han sido virtualizadas
            //Insertamos los datos formateados de los campos de las entidades que no han sido virtualizadas
            
            $sql = "SELECT ist.table_name, ist.table_schema, descr.description AS comments
                    FROM information_schema.tables AS ist
                    JOIN pg_catalog.pg_class klass ON (ist.table_name = klass.relname and klass.relkind = 'r')
                    JOIN pg_catalog.pg_namespace AS pcn ON (pcn.oid = klass.relnamespace AND pcn.nspname = ist.table_schema)
                    LEFT JOIN pg_catalog.pg_description descr ON (descr.objoid = klass.oid and descr.objsubid = 0)
                    WHERE ist.table_type = 'BASE TABLE'
                    AND ist.table_name = '$tabla_name' 
                    AND ist.table_schema = '$table_schema' 
                    AND ist.table_schema NOT IN ('pg_catalog', 'information_schema')
                    AND ist.table_schema||'.'||ist.table_name NOT IN (  
					    SELECT ve._schema||'.'||ve._name 
					    FROM virtualization.entity AS ve
					    )";
            //die($sql);
            $query = $this->db->query($sql);
            
            foreach($query->result() as $row){
                $schema     = $row->table_schema;
                $table      = $row->table_name;
                $comments   = $row->comments;
                $dataTable = array(
                                  '_name'       => $table,
                                  '_schema'     => $schema,
                                  '_comments'   => $comments,
                                  'deleted'     => '0',
                                  'created_by'  => $created_by
                                );
                if(!$this->db->insert('virtualization.entity', $dataTable)){
                    return FALSE;
                }
                $entity_id = $this->db->insert_id();                
                
                $sql2 = "SELECT COLUMNS.column_name, 
                                COLUMNS.character_maximum_length, 
                                COLUMNS.numeric_precision, 
                                COLUMNS.column_default, 
                                COLUMNS.is_nullable, 
                                pg_description.description, 
                                cat.id AS catid
                        FROM information_schema.COLUMNS
                        JOIN virtualization.category AS cat ON COLUMNS.data_type = cat._name
                        LEFT JOIN pg_class ON COLUMNS.TABLE_NAME::name = pg_class.relname 
                        LEFT JOIN pg_description ON pg_class.oid = pg_description.objoid 
                            AND COLUMNS.ordinal_position::INTEGER = pg_description.objsubid
                        WHERE COLUMNS.table_schema::text = '$schema'::text
                        AND COLUMNS.TABLE_NAME = '$table'";                
                
                $query2 = $this->db->query($sql2);
                foreach($query2->result() as $row2){                    
                    if(empty($row2->character_maximum_length)){
                        $lenght = $row2->numeric_precision;
                    } elseif(empty($row2->numeric_precision)){
                        $lenght = $row2->character_maximum_length;
                    } elseif(empty($row2->character_maximum_length) && empty($row2->numeric_precision)){
                        $lenght = '';
                    }                    
                    $dataColumn = array(                        
                                        'entity_id' => $entity_id,
                                        '_name' => $row2->column_name,
                                        '_label' => humanize($row2->column_name),
                                        '_comments' => $row2->description,
                                        'length' => $lenght,
                                        'category_data_type_id' => $row2->catid,
                                        'deleted' => '0',
                                        'created_by' => $created_by,
                                        '_default'  => $row2->column_default,
                                        '_nullable' => $row2->is_nullable
                                        );                    
                    if(!$this->db->insert('virtualization.field', $dataColumn)){
                        return FALSE;
                    }
                }  
            }
            return TRUE;
        }
            
        function entityInformation(){
            
            $sql2 = "SELECT ent._name AS table_name 
                    FROM virtualization.entity AS ent
                    WHERE ent._schema = 'business_logic'
                    AND ent.deleted = '0'";
            $query2 = $this->db->query($sql2);            
            foreach($query2->result() as $row2){
                $entityInDB[] = $row2->table_name;
            }
                        
            $sql = "SELECT ist.table_name
                    FROM information_schema.tables AS ist
                    JOIN pg_catalog.pg_class klass ON (ist.table_name = klass.relname and klass.relkind = 'r')
                    JOIN pg_catalog.pg_namespace AS pcn ON (pcn.oid = klass.relnamespace AND pcn.nspname = ist.table_schema)
                    WHERE ist.table_type = 'BASE TABLE'
                    AND ist.table_schema IN ('business_logic')";
            $query = $this->db->query($sql);            
            
            $n = 0;
            foreach($query->result() as $row){
                $totalEntity[$n]['_name'] = $row->table_name;
                if(in_array($row->table_name, $entityInDB)){
                    $totalEntity[$n]['virtual'] = true;
                } else {
                    $totalEntity[$n]['virtual'] = false;
                }
                if($this->consultUrlEntity($row->table_name) > 0 ){
                    $totalEntity[$n]['crudt'] = true;
                } else {
                    $totalEntity[$n]['crudt'] = false;
                }
                if($this->consultModuleFolder($row->table_name) > 0){
                    $totalEntity[$n]['module'] = true;
                } else {
                    $totalEntity[$n]['module'] = false;
                }
                if($this->comparaSchemaField($row->table_name, 'listAll') > 0){
                    $totalEntity[$n]['change'] = true;
                } else {
                    $totalEntity[$n]['change'] = false;
                }
                $n = $n+1;
            }            
            return $totalEntity;
        }
        
        
        
        private function consultModuleFolder($table_name){
            $modulo     = 'blg_'.$table_name;
            $folders    = get_dir_file_info('application/modules/', $top_level_only = TRUE);
            if(in_array($modulo, $folders[$modulo])){ 
                $salida = 1; 
            } else {
                $salida = 0;
            }
            return $salida;
        }
        
        
        
        private function consultUrlEntity($table_name){
            $url = "blg_$table_name/$table_name/%";
            $sql = "SELECT url
                    FROM rbac.operation
                    WHERE url LIKE '$url'
                    AND deleted = '0'
                    GROUP BY url";

            $query = $this->db->query($sql);
            return $query->num_rows();
        }
        
        function comparaSchemaField($entity, $consult){
            
            $sql1 = "SELECT COLUMNS.column_name
                    FROM information_schema.COLUMNS
                    JOIN virtualization.category AS cat ON COLUMNS.data_type = cat._name
                    LEFT JOIN pg_class ON COLUMNS.TABLE_NAME::name = pg_class.relname 
                    LEFT JOIN pg_description ON pg_class.oid = pg_description.objoid 
                        AND COLUMNS.ordinal_position::INTEGER = pg_description.objsubid
                    WHERE COLUMNS.table_schema::text = 'business_logic'::text
                    AND COLUMNS.TABLE_NAME = '$entity'";
            $query1 = $this->db->query($sql1);
            
            foreach($query1->result() as $row){
                $sal1[] = $row->column_name;
            }
            
            $sql2 = "SELECT f.id, f._name FROM virtualization.field AS f
                    JOIN virtualization.entity AS t ON t.id = f.entity_id
                    WHERE t._name = '$entity' AND t._schema = 'business_logic'";
            $query2 = $this->db->query($sql2);
            
            foreach($query2->result() as $row){
                $sal2[] = $row->_name;
            }
            
            if($consult == 'listAll'){
                return count(array_diff($sal1, $sal2));
            } elseif($consult == 'create') {
                
            }
        }
        
        function makeVirtualization($data){
            $salida = '';
            $n = 0;
            foreach($data as $row){
                $_name      = $row['_name'];
                $virtual    = $row['virtual'];
                $crudt      = $row['crudt'];//create,read,update,delete and table??????
                $module     = $row['module'];
                
                if($virtual == 'si'){
                   if($this->virtualizateEntity($_name)){
                        $salida[$n]['virtual'] = '<strong>Virtualización</strong> de entidad '.$_name.' creada de forma exitosa';
                   } else {
                        $salida[$n]['virtual'] = 'Ha ocurrido un error virtualizado la entidad '.$_name;
                   }
                } 
                if($crudt == 'si'){
                   if($this->makeCRUDT($_name)){
                       $salida[$n]['crudt'] = '<strong>CRUD</strong> de entidad '.$_name.' creada de forma exitosa';
                   } else {
                       $salida[$n]['crudt'] = 'Ha ocurrido un error creando el CRUD de la entidad '.$_name;
                   }
                }                
                if($module == 'si'){
                   if($this->createModule($_name)){
                       $salida[$n]['module'] = '<strong>Módulo</strong> de entidad '.$_name.' creada de forma exitosa';
                    } else {
                       $salida[$n]['module'] = 'Ha ocurrido un error creando el CRUD de la entidad '.$_name;
                   }
                }
                $n=$n+1;
            }
            return $salida;
        }
        

        private function makeCRUDT($_name){
            $created_by = $this->session->userdata('user_id');
            $parent_id = '18';
            $root_user = '1';
            
            //Seleccionamos los datos de la virtualization.entity y de los viertualization.field
            //con el objeto de preformatearlos para las proximas operaciones.
            
            //La primero es crear las operaciones en la tabla rbac.operation
            //luego agregar permiso de la operacion en tabla rbac.operation_role
            //de ultimo agregamos las los datos de rbac.operation_field
            $sql = "SELECT cat_parent.id AS ext_component_id, cat_valid.alternative_value AS validation, fie.* 
                    FROM virtualization.entity AS ent
                    JOIN virtualization.field AS fie ON ent.id = fie.entity_id
                    JOIN virtualization.category AS cat ON fie.category_data_type_id = cat.id
                    JOIN virtualization.category_category AS catcat ON catcat.category_parent_id = cat.id
                    JOIN virtualization.category AS cat_parent ON cat_parent.id = catcat.category_child_id 
                    AND cat_parent._table = 'ext_component'
                    JOIN virtualization.category_category AS catcat2 ON catcat2.category_parent_id = cat.id
                    JOIN virtualization.category AS cat_valid ON cat_valid.id = catcat2.category_child_id 
                    AND cat_valid._table = 'validation_vtype'
                    WHERE ent._name = '$_name'
                    AND ent._schema = 'business_logic' 
                    AND fie._name NOT IN ('deleted','created_by','created_at','updated_by','updated_at')
                    AND ent.deleted = '0' 
                    AND fie.deleted = '0'";
            $query = $this->db->query($sql);
            
            $operaciones = array(   'listAll'       => array(
                                                            'operation_id' => $parent_id,
                                                            '_name' => 'Listado de '.humanize($_name),
                                                            'category_component_type_id' => '1',
                                                            'url'   => 'blg_'.$_name.'/'.$_name.'/listAll',
                                                            'category_visual_type_id' => '20', 
                                                            '_order' => '1',
                                                            'category_render_on_id' => '93' 
                                                            ), 
                                    'create'        => array(
                                                            'operation_id' => $listAll_id,
                                                            '_name' => 'Nuevo '.humanize($_name),
                                                            'category_component_type_id' => '2',
                                                            'url'   => 'blg_'.$_name.'/'.$_name.'/create',
                                                            'category_visual_type_id' => '9', 
                                                            '_order' => '2',
                                                            'icon' => 'add.png',   
                                                            'category_render_on_id' => '79' 
                                                            ), 
                                    'create/process'=> array(
                                                            'operation_id' => $create_id,
                                                            '_name' => 'Guardar '.humanize($_name),
                                                            'category_component_type_id' => '2',
                                                            'url'   => 'blg_'.$_name.'/'.$_name.'/create/process',
                                                            'category_visual_type_id' => '13', 
                                                            '_order' => '3',
                                                            'icon' => 'save.gif',   
                                                            'category_render_on_id' => '78'
                                                            ), 
                                    'edit'          => array(
                                                            'operation_id' => $listAll_id,
                                                            '_name' => 'Editar '.humanize($_name),
                                                            'category_component_type_id' => '2',
                                                            'url'   => 'blg_'.$_name.'/'.$_name.'/edit',
                                                            'category_visual_type_id' => '14', 
                                                            '_order' => '4',
                                                            'icon' => 'pencil.png',   
                                                            'category_render_on_id' => '78'
                                                            ), 
                                    'edit/process'  => array(
                                                            'operation_id' => $edit_id,
                                                            '_name' => 'Actualizar '.humanize($_name),
                                                            'category_component_type_id' => '2',
                                                            'url'   => 'blg_'.$_name.'/'.$_name.'/edit/process',
                                                            'category_visual_type_id' => '13', 
                                                            '_order' => '5',
                                                            'icon' => 'save.gif',   
                                                            'category_render_on_id' => '78'
                                                            ), 
                                    'detail'        => array(
                                                            'operation_id' => $listAll_id,
                                                            '_name' => 'Detalle de '.humanize($_name),
                                                            'category_component_type_id' => '2',
                                                            'url'   => 'blg_'.$_name.'/'.$_name.'/detail',
                                                            'category_visual_type_id' => '12', 
                                                            '_order' => '6',
                                                            'icon' => 'zoom.png',   
                                                            'category_render_on_id' => '78'
                                                            ), 
                                    'delete'        => array(
                                                            'operation_id' => $listAll_id,
                                                            '_name' => 'Eliminar '.humanize($_name),
                                                            'category_component_type_id' => '2',
                                                            'url'   => 'blg_'.$_name.'/'.$_name.'/delete',
                                                            'category_visual_type_id' => '10', 
                                                            '_order' => '7',
                                                            'icon' => 'cancel.png',   
                                                            'category_render_on_id' => '78'
                                                            )
                                );
            
            $opertionData = array(
                            'visible' => '1',
                            'business_logic' => '0',
                            'ouroboros_admin' => '0',
                            'created_by' => $created_by
                            );
            
            foreach($operaciones as $key => $value){
                if($key == 'listAll'){ $op_id = $parent_id; } else { $op_id = $op_id; }
            	$result = array_merge($value, $opertionData, array('operation_id' => $op_id));
                $this->db->insert('rbac.operation', $result);
                switch ($key) {
                    case 'listAll':
                        $listAll_id = $this->db->insert_id();
                        $operation_id = $listAll_id;
                        $op_id = $listAll_id;
                        break;
                    case 'create':
                        $create_id = $this->db->insert_id();
                        $operation_id = $create_id;
                        $op_id = $create_id;
                        break;
                    case 'create/process':
                        $create_process_id = $this->db->insert_id();
                        $operation_id = $create_process_id;
                        $op_id = $listAll_id;
                        break;
                    case 'edit':
                        $edit_id = $this->db->insert_id();
                        $operation_id = $edit_id;
                        $op_id = $edit_id;
                        break;
                    case 'edit/process':
                        $edit_process_id = $this->db->insert_id();
                        $operation_id = $edit_process_id;
                        $op_id = $listAll_id;
                        break;
                    case 'detail':
                        $detail_id = $this->db->insert_id();
                        $operation_id = $detail_id;
                        $op_id = $listAll_id;
                        break;
                    case 'delete':
                        $delete_id = $this->db->insert_id();
                        $operation_id = $delete_id;
                        break;
                }
                $operationRolData = array(
                            'operation_id' => $operation_id,
                            'role_id' => $root_user,
                            'created_by' => $created_by
                            );
                if(!$this->db->insert('rbac.operation_role', $operationRolData)){
                    return FALSE;
                }
                $withOutOperationField = array('create/process', 'edit/process', 'delete');
                if(in_array($key, $withOutOperationField)){}else{
                    if(!$this->formatingFieldData($query, $key, $operation_id)){
                        return FALSE;
                    }
                }
            }
            return TRUE;
        }
        
        
        private function formatingFieldData($query, $key, $operation_id){
            $created_by = $this->session->userdata('user_id');
            
            foreach($query->result() as $row){
                
                $field_id = $row->id;
                $name = $row->_name;
                $label = $row->_label;
                $comments = $row->_comments;
                $default = $row->_default;
                
                if(preg_match('/_id$/', $name)){
                    $ext_component_id = '26'; //combobox
                } else {
                    $ext_component_id = $row->ext_component_id;
                }
                
                if(!empty($row->length)){
                    $length = '|max_length['.$row->length.']';
                } else {
                    $length = '';
                }    
                
                if(!empty($row->validation)){
                    $validation = '|'.$row->validation;
                } else {
                    $validation = '';
                }  
                
                if($row->_nullable == 'NO'){
                    $required = '|required';
                } else {
                    $required = '';                    
                }
                
                if($name == 'id' AND $key == 'create') {
                    $required = '';
                } 
                
                if(preg_match('/^nextval/', $default)){
                    $hidden = '1';
                } else {
                    $hidden = '0';
                }
                
                if($key == 'detail'){
                    $read_only  = '1';
                    $validation = '';
                    $length     = '';
                    $required   = '';
                } else {
                    $read_only  = '0';
                }
                
                $operationFieldData = array(
                            'operation_id' => $operation_id,
                            'field_id' => $field_id,
                            'category_ext_component_id'  => $ext_component_id,
                            '_label' => $label,
                            'help' => $comments,
                            'read_only' => $read_only,
                            'hidden' => $hidden,
                            'validation' => 'trim'.$validation.$length.$required.'|xss_clean',
                            '_comments' => $comments,
                            'created_by' => $created_by
                            );
                if(!$this->db->insert('rbac.operation_field', $operationFieldData)){
                    return FALSE;
                }
            }
            return TRUE;
        }

        private function createModule($_name){
            
            $salida = TRUE;
            //Crear Controlador              
            $old = umask(0);
            $content = read_file("application/modules/our_virtualization/views/controller_base.php");            
            $entityCap = ($_name);  //TODO: modificar alternativa
//            $entityMod = $_name.'_model';
            $content = str_replace("ELCONTROLLER", $entityCap, $content);
//            $content = str_replace("el_model", $entityMod, $content);
//            $content = str_replace("el_registro", $entityCap, $content);
            $pathFile   = "application/modules/blg_$_name/controllers/$_name.php";
            $pathFolder = "application/modules/blg_$_name/controllers/"; 
            
            if (!mkdir($pathFolder, 0777, TRUE)) {
                echo 'Unable to write the controller folder';
                return FALSE;
            }
            if (!write_file($pathFile, $content)){
                echo 'Unable to write the controller file';
                return FALSE;
            }
            
            //Crear Modelo
            $content = read_file("application/modules/our_virtualization/views/model_base.php");
            
            $entityCap = ($_name).'_model'; //TODO: modificar alternativa humanize
            $content = str_replace("ELMODEL", $entityCap, $content);
//            $content = str_replace("la_entidad", $_name, $content);
            $elmodel = $_name.'_model.php';
            $pathFile   = "application/modules/blg_$_name/models/$elmodel";
            $pathFolder = "application/modules/blg_$_name/models/";            
            if (!mkdir($pathFolder, 0777, TRUE)){
                echo 'Unable to write the model folder';
                return FALSE;
            }
            //die($content);
            if (!write_file($pathFile, $content)){
                echo 'Unable to write the model file';
                return FALSE;
            }
            
            //Crear Vista
            $content = read_file("application/modules/our_virtualization/views/index.html");            
            $pathFile   = "application/modules/blg_$_name/views/index.html";
            $pathFolder = "application/modules/blg_$_name/views/";
            
            if (!mkdir($pathFolder, 0777, TRUE)) {
                echo 'Unable to write the view folder';
                 return FALSE;
            }
            if (!write_file($pathFile, $content)){
                 echo 'Unable to write the view file';
                 return FALSE;
            }
            umask($old);
            return $salida;
        }        
        
        //        function outOfStock(){
//            $created_by = $this->session->userdata('user_id');
//            
//            //Seleccionamos las entidades que no han sido virtualizadas
//            //Insertamos las entidades en la tabla entidades
//            //Seleccionamos los campos con sus atributos de las entidades que no han sido virtualizadas
//            //Insertamos los datos formateados de los campos de las entidades que no han sido virtualizadas
//            
//            $sql = "SELECT ist.table_name, ist.table_schema, descr.description AS comments
//                    FROM information_schema.tables AS ist
//                    JOIN pg_catalog.pg_class klass ON (ist.table_name = klass.relname and klass.relkind = 'r')
//                    JOIN pg_catalog.pg_namespace AS pcn ON (pcn.oid = klass.relnamespace AND pcn.nspname = ist.table_schema)
//                    LEFT JOIN pg_catalog.pg_description descr ON (descr.objoid = klass.oid and descr.objsubid = 0)
//                    WHERE ist.table_type = 'BASE TABLE'
//                    AND ist.table_schema NOT IN ('pg_catalog', 'information_schema')
//                    AND ist.table_schema||'.'||ist.table_name NOT IN (  
//					    SELECT ve._schema||'.'||ve._name 
//					    FROM virtualization.entity AS ve
//					    )";
//            $query = $this->db->query($sql);
//            
//            foreach($query->result() as $row){
//                $schema     = $row->table_schema;
//                $table      = $row->table_name;
//                $comments   = $row->comments;
//                $dataTable = array(
//                                  '_name'       => $table,
//                                  '_schema'     => $schema,
//                                  '_comments'   => $comments,
//                                  'deleted'     => '0',
//                                  'created_by'  => $created_byparam
//                                );
//                $this->db->insert('virtualization.entity', $dataTable);
//                $entity_id = $this->db->insert_id();                
//                
//                $sql2 = "SELECT COLUMNS.column_name, 
//                                COLUMNS.character_maximum_length, 
//                                COLUMNS.numeric_precision, 
//                                COLUMNS.column_default, 
//                                COLUMNS.is_nullable, 
//                                pg_description.description, 
//                                cat.id AS catid
//                        FROM information_schema.COLUMNS
//                        JOIN virtualization.category AS cat ON COLUMNS.data_type = cat._name
//                        LEFT JOIN pg_class ON COLUMNS.TABLE_NAME::name = pg_class.relname 
//                        LEFT JOIN pg_description ON pg_class.oid = pg_description.objoid 
//                            AND COLUMNS.ordinal_position::INTEGER = pg_description.objsubid
//                        WHERE COLUMNS.table_schema::text = '$schema'::text
//                        AND COLUMNS.TABLE_NAME = '$table'";                
//                
//                $query2 = $this->db->query($sql2);
//                foreach($query2->result() as $row2){                    
//                    if(empty($row2->character_maximum_length)){
//                        $lenght = $row2->numeric_precision;
//                    } elseif(empty($row2->numeric_precision)){
//                        $lenght = $row2->character_maximum_length;
//                    } elseif(empty($row2->character_maximum_length) && empty($row2->numeric_precision)){
//                        $lenght = '';
//                    }                    
//                    $dataColumn = array(                        
//                                        'entity_id' => $entity_id,
//                                        '_name' => $row2->column_name,
//                                        '_label' => humanize($row2->column_name),
//                                        '_comments' => $row2->description,
//                                        'length' => $lenght,
//                                        'category_data_type_id' => $row2->catid,
//                                        'deleted' => '0',
//                                        'created_by' => $created_by,
//                                        '_default'  => $row2->column_default,
//                                        '_nullable' => $row2->is_nullable
//                                        );                    
//                    $this->db->insert('virtualization.field', $dataColumn);
//                }  
//            }            
//        }
        
        
        function destroyByEntity($entityArray){
                foreach($entityArray as $entity){
                    //Eliminar en Operation
                    $entityUrl = 'blg_'.$entity.'/'.$entity.'/';
                    $entityModule = 'blg_'.$entity;

                    $sql1 = "DELETE FROM rbac.operation_role AS roles 
                            WHERE roles.operation_id IN (
                                SELECT oper.id FROM rbac.operation AS oper 
                                WHERE oper.url LIKE '$entityUrl%'
                                )";
                    $this->db->query($sql1);                    
                  
                    $sql2 = "DELETE FROM rbac.operation_field  AS field
                            WHERE field.operation_id IN (
                                SELECT oper.id FROM rbac.operation AS oper
                                WHERE oper.url LIKE '$entityUrl%'
                                )";
                    $this->db->query($sql2);

                    $sql3 = "DELETE FROM rbac.operation  AS oper
                             WHERE oper.url LIKE '$entityUrl%'";
                    $this->db->query($sql3);

                    //Eliminar en Virtualization

                    
                    $sql4 = "DELETE FROM virtualization.field AS field
                            WHERE field.entity_id IN (
                                SELECT enti.id FROM virtualization.entity AS enti
                                WHERE enti._name = '$entity'
                                )";
                    $this->db->query($sql4);

                    $sql5 = "DELETE FROM virtualization.entity AS enti 
                            WHERE enti._name = '$entity'";
                    $this->db->query($sql5);

                    //Eliminar Moudules
                    delete_files("application/modules/$entityModule", TRUE);
                    delete_files("application/modules/$entityModule");
    
                }
            }
}            
?>
