<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');

/*
  | -------------------------------------------------------------------------
  | Dyna_VIews
  | -------------------------------------------------------------------------
  | Este archivo define los parametros de configuracion de la libreria Dyna_Views.
  |
 */

$config['dyna_views_path'] = 'lib_dyna_views/';

//  Path de los modelos asociados.
$config['dyna_views_model'] = $config['dyna_views_path'] . 'metadata_model';

//  Path de los archivos de vistas asociados.
$config['dyna_views_form_path']         = $config['dyna_views_path'] . 'forms/';
$config['dyna_views_form_snippet_path'] = $config['dyna_views_form_path'] . 'snippets/';
$config['dyna_views_grid_path']         = $config['dyna_views_path'] . 'grids/';
$config['dyna_views_grid_snippet_path'] = $config['dyna_views_grid_path'] . 'snippets/';
$config['dyna_views_panel_path']        = $config['dyna_views_path'] . 'panels/';

/* End of file dyan_views.php */
/* Location: ./application/config/dyan_views.php */