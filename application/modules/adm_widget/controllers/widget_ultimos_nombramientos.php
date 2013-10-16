<?php
if (!defined('BASEPATH'))	exit('Acceso Denegado');

/**
 * Widget_ultimos_nombramientos class
 * 
 * @package		widgets
 * @subpackage	controllers
 * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v1.0 24/01/12 05:00 PM
 * */

require_once(WIDGETS_SUPERCLASS);

class Widget_ultimos_nombramientos extends Widgets_superclass  implements Widgets_interface {
	
	
	function __construct($widgetDBAttr) {
		parent::__construct(array('mode'=>'controller','widget_model'=>'widget_ultimos_nombramientos_model'));
		if(!empty($widgetDBAttr))$this->prepare($widgetDBAttr);
	}
	
	
	public function render() {
		if($this->checkDynaViewsInstance()){
			$cpId=((self::$CI->session->userdata('chk_role_type'))=='CGP')?"": self::$CI->session->userdata('cuerpo_policial_id');		
			$data["rowset"] = $this->widget_model->getUltimosNombramientos($cpId);
			$data['totalRows'] = count($data['rowset']);	
			$tempView= $this->dyna_views->buildGrid(false, 'ultimosNmCP', $data, false, false, true,array('height'=>$this->height));
			$viewData['js_generated']=$tempView['js_generated'];
			$viewData['opId']=$this->dyna_views->operationData->id;
			$viewData['rol_type'] = self::$CI->session->userdata('chk_role_type');	
			$tempView['js_generated']= self::$CI->load->view('widgets/widget_ultimos_nombramientos.js.php', $viewData,true);
		}else $tempView=$this->unableDVmsg();
		return $tempView;
		
	}	
	
	
	public function process() {
		$data["rowset"] = $this->widget_model->getUltimosNombramientos();
		$data['totalRows'] = count($data['rowset']);
		die(json_encode($data));		
	}
	
	public function display(){}
}
?>
