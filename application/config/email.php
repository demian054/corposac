<?php

if (!defined('BASEPATH'))
	exit('Acceso Denegado');

/*
  | -------------------------------------------------------------------------
  | Email
  | -------------------------------------------------------------------------
  | This file lets you define parameters for sending emails.
  | Please see the user guide for info:
  |
  |	http://codeigniter.com/user_guide/libraries/email.html
  |
 */

$config = array(
  'protocol' => "smtp",
  'smtp_host' => "ssl://smtp.googlemail.com",
  'smtp_port' => "465",
  'smtp_user' => 'sietpol@gmail.com',
  'smtp_pass' => 'sietpol12345',
  'charset' => "utf-8",
  'mailtype' => "html",
  'newline' => "\r\n");


/* End of file email.php */
/* Location: ./application/config/email.php */