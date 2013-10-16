<?php
if (!defined('BASEPATH'))	exit('Acceso Denegado');

/**
 * Widget_mensaje_usuario class
 * 
 * @package		widgets
 * @subpackage	controllers
 * @author		Jesus Farias Lacroix <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		v1.0 24/01/12 05:00 PM
 * */

require_once(WIDGETS_SUPERCLASS);

class Widget_mensaje_usuario  extends Widgets_superclass  implements Widgets_interface {

	function __construct($widgetDBAttr) {
		parent::__construct(array('mode'=>'controller','widget_model'=>'widget_mensaje_usuario_model'));
		if(!empty($widgetDBAttr))$this->prepare($widgetDBAttr);
	}

	
	public function render() {
		if($this->checkDynaViewsInstance()){
			$tempView= $this->dyna_views->buildForm("", "genericForm2", false, false, false, array('height'=>$this->height));		
			$viewData['js_generated']=$tempView['js_generated'];
			$viewData['opId']=$this->dyna_views->operationData->id;
			$viewData['rol_type'] = self::$CI->session->userdata('chk_role_type');	
			$tempView['js_generated']= self::$CI->load->view('widgets/widget_mensaje_usuario', $viewData,true);
		}else $tempView=$this->unableDVmsg();
		return $tempView;
		
		
		
		
	}
	
	public function process() {
		$bolResult=$msg=false;
		$postMessage=$this->input->post();
		if(empty($postMessage['usuario_id'])) $msg="Debe seleccionar el Usuario al que desea enviar el mensaje";
		elseif(empty($postMessage['cuerpo_mensaje'])) $msg="El Contenido del Mensaje no debe estar vacío";
		else{
			$bolResult = $this->widget_model->sendMessageToUser($postMessage['usuario_id'],$postMessage['cuerpo_mensaje']);
			if(!empty($bolResult)) $msg="El mensaje se ha enviado satisfactoriamente";
			else $msg="Ocurrió un error enviando el mensaje al usuario";			
		}
		throwResponse("Envío de mensaje a usuario", $bolResult, $msg);
	}
	
	public function display(){}//This function is used when the content
	
}
?>
