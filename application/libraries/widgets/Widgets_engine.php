<?php
if (!defined('BASEPATH'))	exit('Acceso Denegado');
/**
 * Widgets_engine Class
 * 
 * @package          libraries
 * @subpackage       none
 * @author           Jesus Farias Lacroix 
 * @copyright        
 * @license          
 * @version          v1.0 23/01/12 04:40 PM
 *  * */

require_once(WIDGETS_SUPERCLASS);

class Widgets_engine {	
	
	public     $CI;                         //CodeIgniter main instance
	public     $widgets_portal_config;      //general config for widgets portal viewport	
	public     $widgets_common_js_snippets; //general config for widgets portal viewport	
	protected  $widgets_superclass;         //Widgets Data access object (interaction layer with DB)	
	
	public function __construct($params) {
		$this->CI = & get_instance();		
		if(!empty($params['_widgets_portal_config']))$this->widgets_portal_config=$params['_widgets_portal_config'];
		else {/*raise CodeIgniter Error and exit*/}
		Widgets_superclass::$CI=& get_instance();
		$this->widgets_superclass= new Widgets_superclass(array('mode'=>'abstraction_layer'));		
	}
	
	/**------------------------------------------------------------------------------------------------------
	 * <b>Method:	getWidgetsRendering()</b>
	 * @method		Gets the whole viewport config with its columns and each widget placed in portlets,
	 *			 	everything ready for the rendering  into the home view
	 * @author		Jesus Farias Lacroix
	 * @version     v1.0 05/03/12 02:54 PM
	*-------------------------------------------------------------------------------------------------------*/	
	public function getWidgetsRendering(){
		$widgetsDbAttr=$this->widgets_superclass->getWidgetsDBAttributes();
		if(!empty($widgetsDbAttr)){
			foreach($widgetsDbAttr as $aWidgetDBAttr){
				require_once(WIDGETS_MODULE_PATH .$aWidgetDBAttr->file_name.'.php');	
				if(class_exists(ucfirst($aWidgetDBAttr->file_name))){
					eval ('$tempWidgetObjec= new '.(ucfirst($aWidgetDBAttr->file_name)).'($aWidgetDBAttr);');
					$this->widgets_common_js_snippets.=" ".$tempWidgetObjec->snippet_js;
					$widgetSerializable=$tempWidgetObjec->getSerializable();
					if(!empty($this->widgets_portal_config['items'][$widgetSerializable['position_x']])){
						$countItems=count($this->widgets_portal_config['items'][$widgetSerializable['position_x']]['items']);
						$forcedPosY=($widgetSerializable['position_y']>$countItems)?$countItems:$widgetSerializable['position_y'];
						$this->widgets_portal_config['items'][$widgetSerializable['position_x']]['items'][$forcedPosY]=$widgetSerializable;
					}else{
						$forcedPosX=count($this->widgets_portal_config['items'])-1;
						array_push($this->widgets_portal_config['items'][$forcedPosX]['items'], $widgetSerializable);					
					}
				}else continue;
			}
			$this->widgets_portal_config=trim(json_encode($this->widgets_portal_config),'{}');			
		}else return false;
		return array('widgets_portal_config'=>$this->widgets_portal_config, 'widgets_common_js_snippets'=>$this->widgets_common_js_snippets);		
	}
	
}
?>