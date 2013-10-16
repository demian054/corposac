<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Profiles
 * @package	SIETPOL
 * @subpackage	models
 * @author	mrosales <mrosales@rialfi.com>
 * @copyright	Por definir
 * @license	Por definir
 * @since	06/09/2011
 * */
class Profiles extends CI_Model {

    private $table = '';

    function __construct() {
	parent::__construct();
	$this->table = 'estatico.profiles';
    }

    function create($data) {
	if (empty($data))
	    return FALSE;
	if ($this->db->insert($this->table, $data))
	    return $this->db->insert_id();
	else
	    return FALSE;
    }

    function update($data) {
	if (empty($data))
	    return FALSE;
	$this->db->where('id', $data['id']);
	if ($this->db->update($this->table, $data))
	    return $this->db->insert_id();
	else
	    return FALSE;
    }

    function getProfile($id) {
	if (empty($id))
	    return FALSE;
	return $this->db->get_where($this->table, array('id' => $id))->result();
    }

    function document_exist($document) {
	if (empty($document))
	    return FALSE;
	$dc = $this->db->get_where($this->table, array('document' => $document))->num_rows();
	return ($dc > 0) ? TRUE : FALSE;
    }

}

/* End of file profiles.php */
/*Location: ./application/models/profiles.php*/ 
