<head>
	<?php echo meta(array('name' => 'Content-type', 'content' => 'text/html; charset=utf-8', 'type' => 'equiv')) ?>
	<meta http-equiv="expires" content="-1" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/resources/css/ext-all-notheme.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/resources/css/xtheme-gray.css"/>
	<? //require('assets/css/style.css.php') ?>
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/plugins/statusbar/css/statusbar.css"/>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/ext-all.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/libraries/ExtJs/3.4/plugins/statusbar/StatusBar.js"></script>
	<script type="text/javascript" src="<?= base_url() ?>assets/js/form_validation.js"></script>
	<script type="text/javascript" src="<?=base_url()?>assets/js/libraries/ExtJs/3.4/src/locale/ext-lang-es.js"></script>
	<script type="text/javascript">
		var BASE_URL 	= '<?= base_url() ?>';
		var BASE_ICONS 	= '<?= base_url() ?>' + 'assets/img/icons/';
		Ext.onReady(function() {
			Ext.QuickTips.init();
			Ext.BLANK_IMAGE_URL = BASE_URL + 'assets/js/libraries/ExtJs/3.4/resources/images/default/s.gif';
			Ext.form.Field.prototype.msgTarget = 'side';
		});
	</script>
</head>
<body class="bglogin">
	<?
	$this->load->view('backdoor/form_login.js.php');

	$this->load->view('generals/browser_compatibility.php');
	?>
</body>
</html>