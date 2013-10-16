<?php
	if (!defined('BASEPATH')) exit('Access Denied');	
	/*---------------------------------------------------------------------------------------------------------------------
	 * Global constants necesary for the  proper working of the widgets component
	 * Never change these ones, unless it is necessary to change the widgets location files (because you have wished so)
	---------------------------------------------------------------------------------------------------------------------*/
	//This is the path to the widgets main libraries directory in application/libraries/widgets (relative to index.php)
		if(!defined('WIDGETS_CORE_PATH'))
			define('WIDGETS_CORE_PATH', APPPATH .'libraries/widgets/');
	//HMVC module directory where all widgets implementation have been placed (relative to index.php)
		if(!defined('WIDGETS_MODULE_DIR'))
			define('WIDGETS_MODULE_DIR', 'widgets/');
		if(!defined('WIDGETS_MODULE_PATH'))
			define('WIDGETS_MODULE_PATH', APPPATH.'modules/'.WIDGETS_MODULE_DIR.'controllers/');		
	//the widgets interface contract (relative to index.php) points to application/libraries/widgets/Widgets_interface.php
	//changing this requires rename the file and interface name	
		if(!defined('WIDGETS_INTERFACE'))
			define('WIDGETS_INTERFACE', WIDGETS_CORE_PATH.'Widgets_interface.php');		
	//the widgets data access object, an interaction layer with the DB 
	//(relative to index.php) points to application/libraries/widgets/Widgets_dao.php
	//changing this requires rename the file and class name	
		if(!defined('WIDGETS_DAO'))
			define('WIDGETS_DAO', WIDGETS_CORE_PATH.'Widgets_dao.php');		
	//the widgets superclass (relative to index.php)
	//changing this requires rename the file and class name	
		if(!defined('WIDGETS_SUPERCLASS'))
			define('WIDGETS_SUPERCLASS', WIDGETS_CORE_PATH.'Widgets_superclass.php');		
	//the widgets engine relative to (controllers/home.php), points to application/libraries/widgets/Widgets_engine.php
	//changing this requires rename the file and class name
		if(!defined('WIDGETS_ENGINE'))
			define('WIDGETS_ENGINE', 'widgets/widgets_engine');
	//the widgets portal view relative to (controllers/home.php), points to application/views/home/widgets_portal.js.php
	//changing this requires rename the file	
		if(!defined('WIDGETS_PORTAL_VIEW'))
			define('WIDGETS_PORTAL_VIEW', 'home/widgets_portal.js.php');
	//the  Extjs portal name (panel where the portal will render) used by application/views/home/viewport.js.php
		if(!defined('WIDGETS_PORTAL'))
			define('WIDGETS_PORTAL', 'widgets_portal');	
		if(!defined('DYNA_VIEWS'))
			define('DYNA_VIEWS', APPPATH .'libraries/Dyna_views.php');	
	//--------------------------------------------------------------------------------------------------------------------


    /*---------------------------------------------------------------------------------------------------------------------
	 * Configurable component attributes
	 *---------------------------------------------------------------------------------------------------------------------*/	
	//Sets whether widget engine is enabled or disabled
		$config['widgets_engine_enabled']=false;
	//Widgets portal general attributes placed here
		$config['widgets_portal_config']=array();
		$config['widgets_portal_config']['margins']='35 5 5 0';
	//each column with its specific attributes are placed here. Meanwhile there are only two columns, 
	//however the number of columns could be increased by adding a new array entry
	//column 0
		$config['widgets_portal_config']['items'][0]['columnWidth']='.50';
		$config['widgets_portal_config']['items'][0]['style']='padding:10px 0 10px 10px';
		$config['widgets_portal_config']['items'][0]['items']= array();
	//column 1
		$config['widgets_portal_config']['items'][1]['columnWidth']='.50';
		$config['widgets_portal_config']['items'][1]['style']='padding:10px 0 10px 10px';
		$config['widgets_portal_config']['items'][1]['items']= array();		
?>
