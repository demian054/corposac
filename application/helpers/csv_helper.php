<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * CSV Helpers
 * Inspiration from PHP Cookbook by David Sklar and Adam Trachtenberg
 * 
 * @author        Jérôme Jaglale
 * @link        http://maestric.com/en/doc/php/codeigniter_csv
 */
// ------------------------------------------------------------------------

/**
 * Array to CSV
 *
 * download == "" -> return CSV string
 * download == "toto.csv" -> download file toto.csv
 */
if (!function_exists('array_to_csv')) {

	function array_to_csv($array, $download = "", $delimiter = ';', $path = FALSE) {
		$CI = & get_instance();
		$url = $CI->config->item('temp_dir');
		if($path)
			$url = $path;
		if(empty($download))
			$download = 'documento.csv';
		ob_start();
		$f = fopen($url . $download, 'w+') or show_error("Can't open $url$download");
		$n = 0;
		foreach ($array as $line) {
			$n++;
			if (!fputcsv($f, $line, $delimiter)) {
				show_error("Can't write line $n: $line");
			}
		}
		fclose($f) or show_error("Can't close $url$download");
		ob_end_clean();
		if($path)
			return TRUE;
		else
			return $url.$download;
			//return anchor($url.$download,$CI->lang->line('export_csv'),array('target'=>'_blank'));
	}

}

// ------------------------------------------------------------------------

/**
 * Query to CSV
 *
 * download == "" -> return CSV string
 * download == "toto.csv" -> download file toto.csv
 */
if (!function_exists('query_to_csv')) {

	function query_to_csv($query, $headers = TRUE, $download = "", $delimiter = ';', $path = FALSE) {
		if (!is_object($query) OR !method_exists($query, 'list_fields')) {
			show_error('invalid query');
		}

		$array = array();

		if ($headers) {
			$line = array();
			foreach ($query->list_fields() as $name) {
				$line[] = $name;
			}
			$array[] = $line;
		}

		foreach ($query->result_array() as $row) {
			$line = array();
			foreach ($row as $item) {
				$line[] = $item;
			}
			$array[] = $line;
		}

		return array_to_csv($array, $download, $delimiter, $path);
	}

}