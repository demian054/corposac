<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Log_model
 * @package	SIETPOL
 * @subpackage	model
 * @author	mrosales <mrosales@rialfi.com>
 * @copyright	Por definir
 * @license	Por definir
 * @since	17/08/2011
 * */
class Log_model extends CI_Model {

    private $table_name = '';

    
    /**
     * <b>Method: __construct()</b>
     * @method Constructor de la clase
     * @author mrosales <mrosales@rialfi.com>
     */
    function __construct() {
	parent::__construct();
	$this->table_name = 'rbac.log';
    }

    
     /**
     * <b>Method: create($data)</b>
     * @method Permite insertar un registro en la tabla rbac.log.
     * @param array $data Arreglo asociativo que contiene los datos que se van a insertar.
     * @return boolean Si el registro es exitoso retorna el TRUE, en caso contrario retorna FALSE.
     * @author mrosales <mrosales@rialfi.com>
     */
    function create($data) {
	if ($this->db->insert($this->table_name, $data))
	    return TRUE;
	else
	    return FALSE;
    }

}

/* End of file logger.php */
/*Location: ./ruta/logger.php*/ 
