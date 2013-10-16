<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Logger
 * @package	SIETPOL
 * @subpackage	NombreSubPaquete
 * @author	mrosales <mrosales@rialfi.com>
 * @copyright	Por definir
 * @license	Por definir
 * @since	17/08/2011
 * */
class Logger {

    private $ip = '';
    private $user_agent = '';
    private $os = '';
    private $user_id = '';
    private $ci = '';
    private $initialize = FALSE;

    /**
     * <b>Method: __construct()</b>
     * @method Constructor de la clase
     * @author mrosales <mrosales@rialfi.com>
     */
    function __construct() {
        $this->ci = & get_instance();
        $this->initialize = TRUE;
        $this->ci->load->library('user_agent');
        $this->ci->load->model('adm_log/log_model');
        $this->ip = $this->ci->input->ip_address();
        $this->user_agent = $this->ci->input->user_agent();
        $this->os = $this->ci->agent->platform();
        $this->user_id = $this->ci->session->userdata('user_id');
    }

    /**
     * <b>Method: createLog($action, $operation_id, $content_after)</b>
     * Metodo para crear un registro en la tabla log.
     * @param string $action Cadena de texto con la descipcion de la accion ejecutada.
     * @param string $operation_id Contenido el identificador de la operación que se esta ejecutando. Default NULL
     * @param string $content_after Contenido del registro antes de ser actualizado debe ser en formato JSON. Default NULL
     * @author mrosales <mrosales@rialfi.com>
     */
    function createLog($action, $operation_id = NULL, $content_after = NULL) {
        
        $data = array(
            'user_id' => (!empty($this->user_id)) ? $this->user_id : $this->ci->session->userdata('user_id'),
            'category_log_type_id' => $action,
            'operation_id' => $operation_id,
            'ip' => $this->ip,
            'user_agent' => $this->user_agent,
            'os' => $this->os,
            'mac_address' => $this->getMacAddress($this->ip),
            'content_after' => (!empty($content_after)) ? json_encode($content_after) : NULL,
            //'date_action' => date('Y-m-d H:i:s a')
        );
        $this->ci->log_model->create($data);
    }

    /**
     * <b>Method: getMacAddres($ip)</b>
     * @method Permite obtener la mac address del cliente que se esta conectando a la aplicacion.
     * @param string $ip Cadena de texto con la ip del usuario que esta conectado.
     * @return mixed En caso de exito retorna la mac adrress del usuario, en caso contrario retorna NULL.
     * @author mrosales <mrosales@rialfi.com>
     */
    private function getMacAddress($ip) {
        $mac = array();
        $comando = `/usr/sbin/arp $ip`;
        preg_match("/((?:[0-9a-f]{2}[:-]){5}[0-9a-f]{2})/i", $comando, $mac);
        if (!empty($mac))
            return $mac[0];
        else
            return NULL;
    }

}

/* End of file newPHPClass.php */
/* Location: ./ruta/newPHPClass.php */
