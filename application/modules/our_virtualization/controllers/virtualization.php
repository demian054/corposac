<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Virtualization Class
 * @package         Virtualization
 * @subpackage      controllers
 * @author          Juan Carlos Lopez Guillot
 * @copyright       Por definir
 * @license         Por definir
 * @version         v0.1 18/09/12 02:59 PM
 *  * */
class Virtualization extends MY_Controller {

	private $module;
	private $error;

	function __construct() {
		parent::__construct();
		$this->load->model('virtualization_model');
	}

	/**
	 * <b>Method:	create()</b>
	 * method		Metodo que perimte crear un usuario
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 10/11/11 02:47 PM
	 * */
	function create(){
            $result = $this->virtualization_model->entityInformation();
            $n = 0;
            foreach($result as $row){
                $_name      = $row['_name'];
                $virtual    = $row['virtual'];
                $crudt      = $row['crudt'];
                $module     = $row['module'];
                
                if(!empty($row['change'])){
                    $reset  = FALSE; //TRUE; TODO: faklta HU para actualizar
                } else {
                    $reset  = FALSE;
                }
                
                $submitedValues[$n]['_name'] = $_name;
                if($virtual == true && !$reset){
                    $submitedValues[$n]['virtual'] = 'no';
                } else {
                    if($this->input->post('virtual_'.$_name) == true){
                        $submitedValues[$n]['virtual'] = 'si';
                    } else {
                        $submitedValues[$n]['virtual'] = 'no';
                    }
                }
                if($crudt == true && !$reset){
                    $submitedValues[$n]['crudt'] = 'no';
                } else {
                    if(($virtual == true OR $this->input->post('virtual_'.$_name) == true) 
                            AND $this->input->post('crudt_'.$_name) == true){
                        $submitedValues[$n]['crudt'] = 'si';
                    } else {
                        $submitedValues[$n]['crudt'] = 'no';
                    }
                }
                if($module == true){
                    $submitedValues[$n]['module'] = 'no';
                } else {
                    if(($virtual == true OR $this->input->post('virtual_'.$_name) == true) 
                            AND $this->input->post('module_'.$_name) == true){
                        $submitedValues[$n]['module'] = 'si';
                    } else {
                        $submitedValues[$n]['module'] = 'no';
                    }
                }
                $n = $n+1;
            }  
            //die(var_dump($submitedValues));          
            $result2 = $this->virtualization_model->makeVirtualization($submitedValues);
            if($result2){
                $salida = '';
                foreach ($result2 as $row){
                    $salida .= $row['virtual'].br();
                    $salida .= $row['crudt'].br();
                    $salida .= $row['module'].br();
                }
                $params = array(
                            "title" => $this->lang->line('virtualization_title_on_create'), 
                            "msg" => $this->lang->line('virtualization_success_on_create').' '.$salida,
                            'result' => true,
                            'success' => true
                            );
            } elseif($result2 === FALSE) {
                $salida = $this->lang->line('virtualization_failure_on_create');
                $params = array(
                            "title" => $this->lang->line('virtualization_title_on_create'), 
                            'result' => false,
                            "msg" => $salida,
                            'success' => false
                            );
            } else {
                $salida = $this->lang->line('virtualization_empty_on_create');
                $params = array(
                            "title" => $this->lang->line('virtualization_title_on_create'), 
                            "msg" => $salida,
                            'result' => true,
                            'success' => true
                            );
            }
            throwResponse($params);
	}
        
        function listAll(){
            //metodo del modelo que desde un arreglo de entidad(es) destruye todo lo que tienen asociado en:
            //virtualization.entity
            //virtualization.field
            //rbac.operation
            //rbac.operation_role
            //rbac.operation_field
            //
//            $entityArray = array('autor');
//            $this->virtualization_model->destroyByEntity($entityArray); die();
//            $result = $this->virtualization_model->comparaSchemaField('cliente');
            
            $result = $this->virtualization_model->entityInformation();
            $bloque = "";
            $cant = count($result);
            $n = 1;
            
            foreach($result as $row){
                
                if(!empty($row['change'])){
                    $flag   = 'assets/img/icons/database_error.png';
                    $alt    = 'Los campos de la entidad necesitan actualización de virtualizacion y operaciones';
                    $reset  = TRUE;
                } else {
                    $flag   = 'assets/img/icons/database_refresh.png';
                    $alt    = 'Los campos de la entidad no necesitan actualización';
                    $reset  = FALSE;
                }
                
                $_name = $row['_name'];
                if(!empty($row['virtual'])){ 
                    if($reset==TRUE){
                        $virtual = ", checked: true, disabled: false"; 
                    } else {
                        $virtual = ", checked: true, disabled: true"; 
                    }
                } else { 
                    $virtual = ", listeners: {check:function(cmp, chk){
                                            if(chk == false){
                                                if(Ext.getCmp('crudt_$_name').checked == true)
                                                        Ext.getCmp('crudt_$_name').setValue(false);
                                                if(Ext.getCmp('module_$_name').checked == true)
                                                        Ext.getCmp('module_$_name').setValue(false);
                                            }
                                            }},";
                }
                if(!empty($row['crudt'])){ 
                    if($reset==TRUE){
                        $crudt = ", checked: true, disabled: false"; 
                    } else {
                        $crudt = ", checked: true, disabled: true"; 
                    }                    
                } else { 
                    $crudt = ", listeners: {check:function(cmp, chk){
                                            if(chk == true){
                                                if(Ext.getCmp('virtual_$_name').checked == false)
                                                        Ext.getCmp('virtual_$_name').setValue(true);
                                            }            
                                            }},";
                } 
                if(!empty($row['module'])){ 
                    $module = ", checked: true, disabled: true";                     
                } else { 
                    $module = ", listeners: {check:function(cmp, chk){
                    
                                             if(chk == true){
                                                if(Ext.getCmp('crudt_$_name').checked == false)
                                                        Ext.getCmp('crudt_$_name').setValue(true);
                                                if(Ext.getCmp('virtual_$_name').checked == false)
                                                        Ext.getCmp('virtual_$_name').setValue(true);
                                            }
                                            }},";
                }                 
                
                
//                listeners: {
//               check: function(cmp, chk){
//                       Ext.getCmp('form_').items.each(function(e){
//                               if(!e.checked && e.id != 'chk_slc_all' && chk){
//                                       e.checked = true;
//                                       document.getElementById(e.id).checked = true;
//                               } else if(e.checked && e.id != 'chk_slc_all' && !chk){
//                                       e.checked = false;
//                                       document.getElementById(e.id).checked = false;
//                               }
//                       });
//               }
//       }
                
                
                
                $bloque .= "{
                                xtype: 'checkboxgroup',
                                fieldLabel: '$_name',
                                items:  [
                                            {   boxLabel: 'Virtualización', 
                                                id: 'virtual_$_name', 
                                                name: 'virtual_$_name' 
                                                $virtual 
                                            },{ 
                                                boxLabel: 'Operaciones', 
                                                id: 'crudt_$_name', 
                                                name: 'crudt_$_name' 
                                                $crudt 
                                            },{ 
                                                boxLabel: 'Módulo', 
                                                id: 'module_$_name', 
                                                name: 'module_$_name' 
                                                $module 
                                            },{
                                                xtype: 'label',
                                                html: '<img alt=\"$alt\" title=\"$alt\" src=\"$flag\">'
                                            }
                                        ]
                            }";
                if($n < $cant){ $bloque .= ','; }
                $n = $n+1;
            }
            $dataView = array('bloque' => $bloque);
            $data = array(
                        'panelType' => '1A',
                        'p1' => $this->load->view('chosse_vir_crudt.js.php', $dataView, TRUE), 
                        'type1' => 'fp;//', 
                        );
            
            $params = array('title'         => 'Virtualización y Generación de CRUDT', 
                            'name'          => 'virtualPanel', 
                            'replace'       => 'center', 
                            'data'          => $data,
                            'scriptTags'    => FALSE, 
                            'return_view'   => FALSE, 
                            'extraOptions'  => FALSE
                            );            
            $this->dyna_views->buildPanel($params);
        }       

}
?>