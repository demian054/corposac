<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
    <? $this->config->load('other', TRUE); ?>
	<meta http-equiv="expires" content="-1" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<!--Style  Sheets-->
	
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/js/libraries/ExtJs/3.4/resources/css/ext-all-notheme.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/js/libraries/ExtJs/3.4/resources/css/xtheme-gray.css"/>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/style.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/empty.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/icons.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/Portal.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/Ext.ux.tree.CheckTreePanel.css"/>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/fileUpload/fileuploadfield.css"/>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/MultiSelect.css"/>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/RowEditor.css"/>	
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/Ext.ux.grid.GroupSummary.css"/>
    <!-- treeGrid -->
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/treegrid.css"/>
    
	<!--Javascript libraries-->	
    <script type="text/javascript" src="<?=base_url()?>assets/version/js/jquery-1.7.1.min.js"></script>        
    <script type="text/javascript" src="<?=base_url()?>assets/version/js/version.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ext-all.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/plugins/statusbar/StatusBar.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/commons_utilities.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/src/locale/ext-lang-es.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/raphael-min.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/fileUpload/FileUploadField.js"></script>	
	<script type="text/javascript" src="<?=base_url()?>assets/js/form_validation.js"></script>        
	<!-- all Extjs Extensions where moved to 'assets/js/libraries/ExtJs/3.4/ux/' folder for better organization <jesus.farias@gmail.com> -->
	<script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/searchfield.js"></script>    
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/MultiSelect.js"></script>	
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/ItemSelector.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/RowEditor.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.tree.CheckTreePanel.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.tree.TreeFilterX.js"></script>	
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.grid.GroupSummary.js"></script>	
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.CheckColumn.js"></script>		
	<script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.grid.Portal.js"></script>	
	<script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.grid.PortalColumn.js"></script>	
	<script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.grid.Portlet.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/Ext.ux.form.DateTime.js"></script>
    <!-- treeGrid -->
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/treegrid/TreeGridSorter.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/treegrid/TreeGridColumnResizer.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/treegrid/TreeGridNodeUI.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/treegrid/TreeGridLoader.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/treegrid/TreeGridColumns.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/treegrid/TreeGrid.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/ux/RowLayout.js"></script>
    <!-- end treeGrid -->
	<!--Global javascript settings-->	
    <script type="text/javascript">
    var BASE_URL 	= '<?=base_url()?>';
	var BASE_ICONS 	= '<?=base_url()?>' + 'assets/img/icons/';
	var LONG_LIMIT	= <?= $this->config->item('long_limit') ?>;
	var SHORT_LIMIT	= <?= $this->config->item('short_limit') ?>;
	var THUMB_LIMIT = <?= $this->config->item('thumb_limit') ?>;
        Ext.onReady(function() {
            Ext.QuickTips.init();
            Ext.BLANK_IMAGE_URL = BASE_URL + 'assets/js/libraries/ExtJs/3.4/resources/images/default/s.gif';
            Ext.form.Field.prototype.msgTarget = 'side';
			
			// Mascara global que se muestra cada vez que se ejecuta un ajax request
			prepareGlobalMask();
			
			// Parametro para extender el tiempo de ejecucion de un ajax request
			Ext.Ajax.timeout = 2400000;
        });
	</script>    
    <title><?=$title?></title>
</head>
<body>
<?php

if (!isset($capture)) {
  // Carga de la vista que genera el menu principal de operaciones
  $this->load->view('home/west_menu.js.php');
  
  // Si los widgets se encuentran habilitados se debe cargar el portal que los muestra
  if(!empty($widgets_on))$this->load->view(WIDGETS_PORTAL_VIEW);
  
  // Carga del contenedor principal del sistema
  $this->load->view('home/viewport.js.php');
} /*else {
    echo '<div id="view_port_main_layout"></div>';
}*/
?>
</body>
</html>
