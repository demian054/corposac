<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<? $this->config->load('other', TRUE); ?>
		<meta http-equiv="expires" content="-1" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/resources/css/ext-all-notheme.css"/>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/resources/css/xtheme-gray.css"/>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/style.css"/>		
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/doc.css"/>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/MultiSelect.css"/>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/RowEditor.css"/>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/adapter/ext/ext-base.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/ext-all.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/plugins/statusbar/StatusBar.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/commons_utilities.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/searchfield.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/src/locale/ext-lang-es.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/form_validation.js"></script>    
		<script type="text/javascript" src="<?= base_url() ?>assets/js/fileUpload/FileUploadField.js"></script>
		<script type="text/javascript" src="<?= base_url() ?>assets/js/treeFilterExtension.js"></script>

		<script type="text/javascript">
			var BASE_URL 	= '<?= base_url() ?>';
			var BASE_ICONS 	= '<?= base_url() ?>' + 'assets/img/icons/';
			Ext.onReady(function() {
				Ext.QuickTips.init();
				Ext.BLANK_IMAGE_URL = BASE_URL + 'assets/js/libraries/ExtJs/3.4/resources/images/default/s.gif';
				Ext.form.Field.prototype.msgTarget = 'side';
			});
		</script>    
		<title><?= $title ?></title>
	</head>
	<body>

		<?php
		$data['tree'] = $result;
		$this->load->view('west_menu.js.php', $data);
		$this->load->view('viewport.js.php');
		?>
    </body>
</html>