<?php (defined('BASEPATH')) OR exit('Acceso Denegado');

/* define the modules base path */
define('MODBASE', APPPATH.'modules/');

/* define the offset from application/controllers */
define('MODOFFSET', '../modules/');

/**
 * Modular Extensions - PHP5
 *
 * Adapted from the CodeIgniter Core Classes
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @link		http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter router class.
 *
 * Install this file as application/libraries/MY_Router.php
 *
 * @copyright 	Copyright (c) Wiredesignz 2009-04-25
 * @version 	5.2.08
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MY_Router extends CI_Router
{
	public function _validate_request($segments)
	{
		(isset($segments[1])) OR $segments[1] = NULL;
	
		/* locate the module controller */
		list($module, $controller) = Router::locate($segments);

		/* not a module controller */
		if ($controller === FALSE) 
			return parent::_validate_request($segments);
		
		/* set the module path */
		$path = ($module) ? MODOFFSET.$module.'/controllers/' : NULL;
						
		$this->set_directory($path);

		/* remove the directory segment */
		if ($module != $controller AND $module != FALSE)
			$segments = array_slice($segments, 1);

		return $segments;
	}
}

class Router
{
	public static $path;
	
	/** Locate the controller **/
	public static function locate($segments) {		
		list($module, $controller) = $segments;
	
		($controller == NULL) AND $controller = $module;
		
		/* module? */
		if ($module AND is_dir(MODBASE.$module)) {
			
			self::$path = $module;
			
			/* module sub-controller? */
			if(is_file(MODBASE.$module.'/controllers/'.$controller.EXT))			
				return array($module, $controller);
				
			/* module controller? */
			return array($module, $module);
		}
			
		/* not a module controller */
		return array(FALSE, FALSE);
	}
}