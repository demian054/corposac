<?php
if (!defined('BASEPATH'))	exit('Acceso Denegado');

/**
 * Widget_avances_campos_dinamicos class
 * 
 * @package		widgets
 * @subpackage	controllers
 * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v1.0 24/01/12 05:00 PM
 * */

require_once(WIDGETS_SUPERCLASS);

class Widget_avances_campos_dinamicos extends Widgets_superclass  implements Widgets_interface {
	
	
	function __construct($widgetDBAttr) {
		parent::__construct(array('mode'=>'controller','widget_model'=>'widget_avances_campos_dinamicos_model'));
		if(!empty($widgetDBAttr))$this->prepare($widgetDBAttr);
	}
	
	
	public function render() {
		$rol_type = self::$CI->session->userdata('chk_role_type');	
		$cpId=((self::$CI->session->userdata('chk_role_type'))=='CGP')?"": self::$CI->session->userdata('cuerpo_policial_id');		
		$data["rowset"] = $this->widget_model->getPorcentajeAvanceCampos($cpId);
		$data['totalRows'] = count($data['rowset']);	
		
		if($this->checkDynaViewsInstance()){
			if($rol_type=='CGP')
				$tempView= $this->dyna_views->buildGroupingGrid(false, 'avanceCampos',$data,false,"orden1",false, "ASC", "orden2",$groupRenderer="", true, array('height'=>$this->height, 'bbarOff'=>true, 'forceFit'=>'false'));
			else
				$tempView= $this->dyna_views->buildGrid(false, 'avanceCampos', $data, false, false, true, array('height'=>$this->height, 'bbarOff'=>true));
					
			$viewData['js_generated']=$tempView['js_generated'];
			$viewData['opId']=$this->dyna_views->operationData->id;
			$viewData['rol_type'] = $rol_type; 
			$tempView['js_generated']= self::$CI->load->view('widgets/widget_avances_campos_dinamicos.js.php', $viewData,true);
		}else $tempView=$this->unableDVmsg();
		return $tempView;
		
	}	
	
	
	public function process() {
		$data["rowset"] = $this->widget_model->getPorcentajeAvanceCampos();
		$data['totalRows'] = count($data['rowset']);
		die(json_encode($data));		
	}
	
	public function display(){}//This function is used when the content
}
?>
