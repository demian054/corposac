<?php
if (!defined('BASEPATH'))	exit('Acceso Denegado');

//require_once('../../libraries/widgets/Widgets_superclass.php');
/**
 * Widget_avance_cd_cm class
 * 
 * @package		widgets
 * @subpackage	controllers
  * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v1.0 24/01/12 05:00 PM
 * */

require_once(WIDGETS_SUPERCLASS);

class Widget_ficha_cp extends Widgets_superclass implements Widgets_interface{

	function __construct($widgetDBAttr) {
		parent::__construct(array('mode'=>'controller','widget_model'=>'widget_ficha_cp_model'));
		if(!empty($widgetDBAttr))$this->prepare($widgetDBAttr);
	}
		
	
	public function render() {	
		if ((self::$CI->session->userdata('chk_role_type'))=='CGP') $cpId=$this->extra_config->cp_id;//loaded from widget extra_config 
		else $cpId = self::$CI->session->userdata('cuerpo_policial_id');
		$data = $this->widget_model->getCpById($cpId);
		list($data['telefonos'], $data['telefono_2'], $data['telefono_3']) = explode('|', $data['telefonos']);
		if($this->checkDynaViewsInstance()){
			$tempView= $this->dyna_views->buildForm("", "genericForm", false, $data, false, array('height'=>$this->height,'CancelButton'=>false));		
			$viewData['js_generated']=$tempView['js_generated'];
			$viewData['opId']=$this->dyna_views->operationData->id;
			$viewData['rol_type'] = self::$CI->session->userdata('chk_role_type');	
			$tempView['js_generated']= self::$CI->load->view('widgets/widget_ficha_cp.js.php', $viewData,true);
		}else $tempView=$this->unableDVmsg();
		return $tempView;		
	}
	
	public function process() {
		$cpId=$this->input->get('cpId');
		$data = $this->widget_model->getCpById($cpId);
		list($data['telefonos'], $data['telefono_2'], $data['telefono_3']) = explode('|', $data['telefonos']);
		die(json_encode($data));	
	}

	public function display(){}//This function is used when the content
}
?>
