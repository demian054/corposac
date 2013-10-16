<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * MY_Form_validation class
 * @package		module: libraries
 * @subpackage	        n/a
 * @author		Eliel Parra <eparra@rialfi.com>, Reynaldo Rojas <rrojas@rialfi.com>
 * @copyright	        Por definir
 * @license		Por definir
 * @since		v1.0 07/09/11 03:45 PM
 * */
class MY_Form_validation extends CI_Form_validation {

    var $CI;

    function __construct($config) {
        parent::__construct($config);
        $this->CI = & get_instance();
    }

    /**
     * <b>Method:valid_url($str)</b>
     *
     * Validar un campo con formato URL valido
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas
     * @version v1.0 07/09/11 03:45 PM
     */
    function valid_url($str) {
        if (!empty($str)) {
            $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
            if (preg_match($pattern, $str))
                return TRUE;
            else {
                $this->CI->form_validation->set_message('valid_url', 'El campo %s debe ser una URL valida.');
                return FALSE;
            }
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_date($str)</b>
     *
     * Validar un campo con formato de fecha valido (dd/mm/yyyy)
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas
     * @version v1.0 07/09/11 03:45 PM
     */
    function valid_date($str) {
        if (!empty($str)) {

            if (preg_match('/([0-3][0-9])\/([0-9]{1,2})\/([1-2][0-9]{3})/', $str, $date)) {
                if (checkdate($date[2], $date[1], $date[3]))
                    return TRUE;
                else {
                    $this->CI->form_validation->set_message('valid_date', 'El campo %s debe ser una fecha valida.');
                    return FALSE;
                }
            } else {
                $this->CI->form_validation->set_message('valid_date', 'El campo %s debe poseer el formato dd/mm/yyyy.');
                return FALSE;
            }
        }
        else
            return TRUE;
    }

    
    
        /**
     * <b>Method:valid_date_time($str)</b>
     *
     * Validar un campo con formato de fecha y hora valido (dd/mm/yyyy HH:ii)
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas
     * @version v1.0 07/09/11 03:45 PM
     */
    function valid_date_time($str) {
//        if (!empty($str)) {
//
//            if (preg_match('/([0-3][0-9])\/([0-9]{1,2})\/([1-2][0-9]{3})/', $str, $date)) {
//                if (checkdate($date[2], $date[1], $date[3]))
//                    return TRUE;
//                else {
//                    $this->CI->form_validation->set_message('valid_date_time', 'El campo %s debe ser una fecha y hora valida.');
//                    return FALSE;
//                }
//            } else {
//                $this->CI->form_validation->set_message('valid_date_time', 'El campo %s debe poseer el formato dd/mm/yyyy HH:ii.');
//                return FALSE;
//            }
//        }
//        else
            return TRUE;
    }
    
    
    
    
    /**
     * <b>Method:valid_date_greater_than_today($str)</b>
     *
     * Validar un campo con formato de fecha no mayor al dia de hoy
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas
     * @version v1.0 07/09/11 03:45 PM
     */
    function valid_date_greater_than_today($str) {

        if (!empty($str)) {

            $error_msg = "El campo %s debe ser una fecha no mayor a la fecha de hoy.";
            list($d, $m, $y) = preg_split('/\//', $str);

            // Verificacion de que la fecha no sea mayor al ano 2038 - bug PHP #20130 - strtotime return -1 for date more than year 2038
            if ($y > 2038) {
                $this->CI->form_validation->set_message('valid_date_greater_than_today', "El campo %s posee un año invalido.");
                return FALSE;
            } else {
                $date = strtotime("$y-$m-$d");
                if ($date <= strtotime(date('Y-m-d')))
                    return TRUE;
                else {
                    $this->CI->form_validation->set_message('valid_date_greater_than_today', $error_msg);
                    return FALSE;
                }
            }
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_date_greater_than_date2($date,$date2)</b>
     *
     * Validar un campo con formato de fecha no mayor a otro campo de fecha
     * @param  string $date Fecha a comparar
     *         string $params 2 Parametros que indican
     *                $date2 Fecha con la que se va a comparar la fecha $date
     *                $label Nombre del Campo en el formulario al cual corresponde el valor $date2
     * @return boolean TRUE en caso de que el valor de $date sea menor o igual al valor de $date2
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas, Jose Rodriguez
     * @version v2.0 07/09/11 03:45 PM
     */
    function valid_date_greater_than_date2($date, $params) {
        if (!empty($date)) {

            list($date2, $label) = explode(',', $params);
            //Valor de la fecha del formulario con la que se va a comparar
            $date2 = $this->CI->input->post($date2);

            if (!empty($date2)) {
                $date = new DateTime(preg_replace('/([0-9]+)\/([0-9]+)\/([0-9]+)/', '$2/$1/$3', $date));
                $date2 = new DateTime(preg_replace('/([0-9]+)\/([0-9]+)\/([0-9]+)/', '$2/$1/$3', $date2));

                $y1 = $date->format('Y');
                $y2 = $date2->format('Y');

                // Verificacion de que la fecha no sea mayor al ano 2038 - bug PHP #20130 - strtotime return -1 for date more than year 2038
                if (($y1 > 2038) || ($y2 > 2038)) {
                    $this->CI->form_validation->set_message('valid_date_greater_than_date2', "El campo %s posee un año invalido.");
                    return FALSE;
                } else {
                    $date = $date->getTimestamp();
                    $date2 = $date2->getTimestamp();

                    if ($date2 <= $date) {
                        return TRUE;
                    } else {
                        $this->CI->form_validation->set_message('valid_date_greater_than_date2', "El campo %s debe ser mayor al campo $label.");
                        return FALSE;
                    }
                }
            } else {
                return FALSE;
            }
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_alpha_numeric_space($str)</b>
     *
     * Validar un campo con letras, numeros y espacios
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas
     * @version v1.0 07/09/11 03:45 PM
     */
    function valid_alpha_numeric_space($str) {
        if (!empty($str)) {

            if (!preg_match("/^[A-Za-z0-9ÑñÁáÉéÍíÓóÚúäëïöüÿÄËÏÖÜŸ\.\,\s\-\_\/()]+$/", $str)) {
                $this->CI->form_validation->set_message('valid_alpha_numeric_space', 'El Campo %s debe poseer letras/numeros.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:dropdown($str, $check)</b>
     *
     * Permite validar que un campo tipo dropdown sea obligatorio o no y que los valores sean los indicados segun el tipo
     *         de fuente (check/descriptor) con el que se ha construido el dropdown
     * @param  $str valor del campo a validar
     *         $params: $params 2 Parametros que indican
     *         String $source: nombre del check en caso de ser check o nombre de pseudotabla en caso de ser descriptor
     *         String $type: tipo de formato 'chk' si es check o 'desc' si es descriptor
     *         Boolean $required: indica si el campo es obligatorio 1 obligatorio 0 opcional
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas
     * @version v1.0 07/09/11 03:45 PM
     */
    function dropdown($str, $params) {

        list($source, $type, $required) = explode(',', $params);

        // Verificacion de campo obligatorio
        if ((empty($str)) && (!empty($required))) {
            $this->CI->form_validation->set_message('dropdown', 'El Campo %s es obligatorio.');
            return FALSE;
        } else {

            // Verificacion si el campo esta vacio
            // Validar dependiendo del tipo de fuente con la cual se construye el dropdown
            switch ($type) {

                // Si es tipo check
                case 'chk':
                    $str = empty($str) ? 'Seleccione...' : $str;
                    $arr_data = get_check($source, TRUE);
                    break;

                // Si es tipo descriptor
                case 'desc':
                    $this->CI->load->model('Descriptor_model');
                    $arr_data = $this->CI->Descriptor_model->getDescriptor($source, TRUE);
                    break;
            }

            $flag_exist = FALSE;
            // Recorriendo el arreglo para verificar si el valor se encuentra en el arreglo - inconveniente con in_array()
            foreach ($arr_data AS $key_element => $element_arr_data) {
                if ((String) $key_element == (String) $str) {
                    $flag_exist = TRUE;
                    break;
                }
            }

            // Verificacion de que el dato seleccionado en el dropdown pertenezca al tipo de datos validos segun la fuente de datos
            if ($flag_exist)
                return TRUE;
            else {
                $this->CI->form_validation->set_message('dropdown', 'El Campo %s debe ser un tipo válido.');
                return FALSE;
            }
        }
    }

    /**
     * <b>Method:sql_injection($str)</b>
     *
     * Validar un campo de posible inyeccion sql
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Angelo Tamarones, Nohemi Rojas
     * */
    function sql_injection($str) {

        if (!empty($str)) {

            if (preg_match("/[*\"\´\'\\\]/", $str)) {
                $this->CI->form_validation->set_message('sql_injection', 'El Campo %s tiene caracteres inválidos.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_alpha_space($str)</b>
     *
     * Validar un campo con letras y espacios en blanco
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas, Nohemi Rojas, Angelo Tamarones
     */
    function valid_alpha_space($str) {
        if (!empty($str)) {

            if (!preg_match("/^[A-zÑñÁáÉéÍíÓóÚúäëïöüÿÄËÏÖÜŸ\s]+$/", $str)) {
                $this->CI->form_validation->set_message('valid_alpha_space', 'El Campo %s debe poseer letras.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_alpha_space_ampersan_point($str)</b>
     *
     * Validar un campo con letras, espacios en blanco ampersan y puntos
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Eliel Parra, Reynaldo Rojas, Nohemi Rojas, Angelo Tamarones, Jose Rodriguez
     */
    function valid_alpha_space_ampersan_point($str) {
        if (!empty($str)) {

            if (!preg_match("/^[A-zÑñÁáÉéÍíÓóÚúäëïöüÿÄËÏÖÜŸ.&\s]+$/", $str)) {
                $this->CI->form_validation->set_message('valid_alpha_space_ampersan_point', 'El Campo %s debe poseer letras.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_ano_four($str)</b>
     *
     * Validar un campo de ano con cuatro digitos en un rango del 2000 al 2099
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Angelo Tamarones
     */
    function valid_ano_four($str) {
        if (!empty($str)) {

            if (!preg_match("/^(20|20)\d\d$/", $str)) {
                $this->CI->form_validation->set_message('valid_ano_four', 'Debe ingresar sólo cuatro (4) números en un rango del 2000 al 2099.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method: valid_author</b>
     *
     * Valida que el campo de autor solo contengan letras y comas.
     * @param 	$str Valor del campo a examinar.
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria, FALSE en caso contrario.
     * @author Mirwing Rosales
     * */
    function valid_author($str) {
        if (empty($str))
            return FALSE;
        if (!preg_match('/^[A-zÑñÁáÉéÍíÓóÚúäëïöüÿÄËÏÖÜŸ\s,]+$/', $str)) {
            $this->CI->form_validation->set_message('valid_author', 'El campo %s debe poseer letras letras/coma.');
            return FALSE;
        } else
            return TRUE;
    }

    /**
     * <b>Method:valid_alpha_numeric_asterisk($str)</b>
     *
     * Validar un campo con letras, numeros y asteriscos
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Reynaldo Rojas
     * @version v1.0 08/03/12 03:14 PM
     */
    function valid_alpha_numeric_asterisk($str) {
        if (!empty($str)) {

            if (!preg_match("/^[A-Z0-9\*]+$/", $str)) {
                $this->CI->form_validation->set_message('valid_alpha_numeric_asterisk', 'El Campo %s debe poseer letras/numeros.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method: 	valid_rif()</b>
     * Validacion del rif en venezuela.
     * @param		string $str Cadena de caracteres con el valor del campo
     * @return		boolean T/F en caso de exito
     * @author		Mirwing Rosales
     * @version		v-1.0 28/05/2012 11:02 am
     */
    public function valid_rif($str) {
        if (!empty($str)) {

            if (!preg_match("/^(J|G|V)\-\d{5,8}\-\d{1}+$/", $str)) {
                $this->CI->form_validation->set_message('valid_rif', 'El campo %s debe poseer un rif valido Ej. J-00000000-0.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method: 	valid_date_time()</b>
     * Validacion de campos con de fecha y tiempo.
     * @param	string $datetime Cadena de caracteres con el valor del campo.
     * @return	boolean T/F en caso de exito.
     * @author	Jose Rodriguez
     * @version	v-1.0 06/06/2012 10:00 am
     */
    function valid_datetime($datetime) {
        $band = FALSE;
        $matches = NULL;
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $datetime, $matches))
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                $this->CI->form_validation->set_message('valid_datetime', 'El Campo %s debe contener fecha y hora valida.');
                $band = TRUE;
            }
        return $band;
    }

    /**
     * <b>Method:valid_date_comparison_today($str,$param)</b>
     *
     * Validar un campo con formato de fecha con respecto al dia de hoy.
     * @param string $str valor del campo a validar.
     * @param string $param indica el tipo de validacion a realizar
     *                          LESS = Fecha menor a hoy (default)
     *                          GREATER = Fecha mayor a hoy
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Jose Rodriguez, Mirwing Rosales
     * @version v1.1 20/07/2012 09:03 am
     */
    function valid_date_comparison_today($str, $param = 'LESS') {

        if (!empty($str)) {

            $error_msg = 'El campo %s debe ser una fecha';
            $date = new DateTime(preg_replace('/([0-9]+)\/([0-9]+)\/([0-9]+)/', '$2/$1/$3', $str));
            $y = $date->format('Y');

            // Verificacion de que la fecha no sea mayor al ano 2038 - bug PHP #20130 - strtotime return -1 for date more than year 2038
            if ($y > 2038) {
                $this->CI->form_validation->set_message('valid_date_comparison_today', "El campo %s posee un año invalido.");
                return FALSE;
            } else {

                $rs = FALSE;
                $param = strtoupper($param);
                $date = $date->getTimestamp();
                $today = strtotime(date('Y-m-d'));

                if (strcmp('LESS', $param) == 0) {
                    $error_msg .= ' menor';
                    if ($date <= $today)
                        $rs = TRUE;
                } elseif (strcmp('GREATER', $param) == 0) {
                    $error_msg .= ' mayor';
                    if ($date >= $today)
                        $rs = TRUE;
                }

                $error_msg.= ' a la fecha de hoy.';

                if ($rs)
                    return TRUE;
                else {
                    $this->CI->form_validation->set_message('valid_date_comparison_today', $error_msg);
                    return FALSE;
                }
            }
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_numeric_colon($str)</b>
     * Validar un campo con numeros y comas.
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Jose Rodriguez <josearodrigueze@gmail.com>
     * @version 1.0 17/09/2012 17:11
     */
    function valid_numeric_colon($str) {
        if (!empty($str)) {

            if (!preg_match("/^[0-9,]+$/", $str)) {
                $this->CI->form_validation->set_message('valid_numeric_colon', 'El Campo %s debe poseer solo numeros y comas.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

    /**
     * <b>Method:valid_alpha_sapce_colon($str)</b>
     * Validar un campo con letras, espacios en blanco y comas.
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Jose Rodriguez <josearodrigueze@gmail.com>
     * @version 1.0 17/09/2012 17:11
     */
    function valid_alpha_sapce_colon($str) {
        if (!empty($str)) {

            if (!preg_match("/^[A-zÑñÁáÉéÍíÓóÚúäëïöüÿÄËÏÖÜŸ,\s]+$/", $str)) {
                $this->CI->form_validation->set_message('valid_alpha_sapce_colon', 'El Campo %s debe poseer solo letras, espacios y comas.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }
    
    /**
     * <b>Method:valid_alpha_sapce_colon($str)</b>
     * Validar un campo que posee una direccion mac valida.
     * @param string $str valor del campo a validar
     * @return boolean TRUE en caso de que la validacion haya sido satisfactoria
     *         boolean FALSE en caso contrario
     * @author Jose Rodriguez <josearodrigueze@gmail.com>
     * @version 1.0 17/09/2012 17:11
     */
    function valid_mac_address($str) {
        if (!empty($str)) {

            if (!preg_match('/^(?:[[:xdigit:]]{2}([-:]))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/', $str)) {
                $this->CI->form_validation->set_message('valid_mac_address', 'El Campo %s debe poseer una dirección mac valida.');
                return FALSE;
            }
            else
                return TRUE;
        }
        else
            return TRUE;
    }

}

/* END Class MY_Form_validation      */
/* END of file MY_Form_validation.php */
/* Location: ./application/libraries/MY_Form_validation.php */