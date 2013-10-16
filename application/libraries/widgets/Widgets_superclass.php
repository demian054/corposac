<?php
/**
 * Description of Widgets_superclass
 *
 * @author jfarias
 * 
 */

require_once(WIDGETS_DAO);
require_once(WIDGETS_INTERFACE);
require_once(DYNA_VIEWS);

class Widgets_superclass extends CI_Controller implements Widgets_interface {
	
	
	public    static $widgets_collection; //Collection of $this instances objects.
	public	  static $CI;                 //CodeIgniter instance.
	public    static $widgets_dao;		  //Widgets Data access object (interaction layer with DB).	
	public	  $serializable;			  //properties compatibles with ExtJs portlets objects (Does Not contain parent class properties).
	public    $dyna_views;				  //The DynaView Object.
	public    $snippet_js;				  //The JavaScript code generated by DynaViews, required to rendering the widget sub item UI component.
	public    $widget_model;              //The particular model class for each widget
	public    $operation_data;			  //Multi-array with the widget operations info 	
	
	
	protected $mode;              //Used to emulating the __constuct overload, this is a polymorphic class.	
	protected $widget_id;		  //Unique widget id. 
	protected $user_id;			  //current user on session, (this user have widgets associated).  
	protected $rol_id;			  //user current role.
	protected $descripcion;		  //the widget description.
	protected $html;              //widget html attribute.
	protected $file_name;         //controller class file, contains the widget implementation.
	protected $title;			  //widget title attribute.
	protected $finstalacion;	  //installation date (when the user did install the widget).
	protected $fdesinstalacion;   //deinstallation date (when the user did uninstall the widget).
	protected $extra_config;      //widget extra config attributes.
	protected $rows;			  //number of rows that widget must display (only applies when it's a grid widget).
	protected $width;			  //widget width attribute.
	protected $height;			  //widget height attribute.
	protected $position_x;        //column to which the widget belongs.
	protected $position_y;	      //order of the widget in its column.
	protected $layout;			  //rendering layout used into the portlet.	
	protected $id;				  //ExtJs portlet (widget) id attribute, (used for compatibility reasons) .
	protected $items;			  //ExtJs portlet childs.		
	protected $operation_id;	  //operation id that represent the widget.
	protected $operation_url;     //The operation url (used for compatibility with DynaViews).
	
	
	function __construct($_params) {
		$this->mode=@$_params['mode'];
		if($this->mode=='controller'){
			parent::__construct();
			if(empty(self::$CI)) self::$CI=&get_instance();	
			if(!empty($_params['widget_model'])){
				self::$CI->load->model(WIDGETS_MODULE_DIR.$_params['widget_model']);
				$this->widget_model=&self::$CI->{$_params['widget_model']};
				$this->widget_model->init(self::$CI);
			}			
		}
		self::$widgets_dao= new Widgets_dao(self::$CI);		
	}
	
	/*--------------------------------------------------------------------------------------------------------------------------
	 * All the Common methods for each widget begin here, their implementation logic are the same for everyone, 
	 * thus they are encapsulated on this parent class.
	 --------------------------------------------------------------------------------------------------------------------------*/
	protected function prepare($widgetDBAttr,$my_model=''){
		$dbObjVars=get_object_vars($widgetDBAttr);
		foreach($dbObjVars as $key=>$value)	{
			if(property_exists($this,$key)){
				if($key=='extra_config'){
					$extraOpt=json_decode($value);
					$this->extra_config=(!empty($extraOpt))?$extraOpt:false;					
				}else $this->{$key}=$value;
				$this->serializable[$key]=$this->{$key};				
			}
			
		}
		
		$this->serializable['config_url']=WIDGETS_MODULE_DIR.$this->file_name.'/config';
		$this->serializable['position_url']=WIDGETS_MODULE_DIR.$this->file_name.'/setPosition';
		$operationData=self::$widgets_dao->getWidgetOperation($this->widget_id,$this->user_id, $this->rol_id);
		if(!empty($operationData)){
			$dynaViewsOpt['CI']=&self::$CI;		
			$dynaViewsOpt['allways_return_views']=true;
			$dynaViewsOpt['operation_id']  = $this->serializable['operation_id'] = $this->operation_id  = $operationData[0]->operation_id;
			$dynaViewsOpt['operation_url'] = $this->serializable['operation_url']= $this->operation_url = $operationData[0]->operation_url;	
			$this->dyna_views = new Dyna_views($dynaViewsOpt);			
		}
		$snippet=$this->render();
		if(is_array($snippet) && !empty($snippet['js_generated']) && !empty($snippet['ui_component'])){			
			$this->snippet_js=$snippet['js_generated'];
			$this->serializable['items']=$snippet['ui_component'];
			$this->serializable['border']='false';
			unset($this->serializable['html']);
		}else{
			$this->snippet_js=false;
			$this->serializable['html']=$snippet;
		}
	}
	
	
	public function getSerializable() {
		return $this->serializable;		
	}
	
	public function getWidgetsDBAttributes() {
		return (self::$widgets_dao->getWidgetsByUserInSession());
	}	
	
	public function getPosition(){
		return array('x'=> $this->position_x, 'y'=> $this->position_y);
	}
	
	public function  setPosition($widgetId, $posX, $posY){
		self::$widgets_dao->setPosition($widgetId, $posX, $posY);
	}
	
	public function checkDynaViewsInstance(){
		return (!empty($this->dyna_views));
	}
	
	//unable to start the visual template motor for the widget (DV), probably due to inconsistent operation permissions.
	public function unableDVmsg(){
		$msg="<b>Probablemente se le han denegado permisos en el sistema<br>necesarios para el funcionamiento de este mini-asplicativo.</b>";
		return $msg;
	}
	
	
	/*--------------------------------------------------------------------------------------------------------------------------
	 * defined methods that are common for all widgets follow here, however their implementation logic could be diferent 
	 * to each widget. The Widgets_interface contract ensures the compatibility between them, if you want to know the functionality
	 * of each method, please check it out on '/application/libraries/widgets/Widgets_interface.php'
	 --------------------------------------------------------------------------------------------------------------------------*/
	public function render(){}
	public function process(){}
}

?>
