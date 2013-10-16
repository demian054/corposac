<!DOCTYPE html>
<html>
	<head>

		<?php echo meta(array('name' => 'Content-type', 'content' => 'text/html; charset=utf-8', 'type' => 'equiv')) ?>
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/style.css"/>	
		<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/prt_doc.css"/>
		<title>Proyecto X</title>
	</head>

	<body>
		<div class="header_cont">
			<div align="left" class="header_img">
				<img src="<?=base_url()?>assets/img/cabecera.png"  />
			</div>
			<div align="right" class="header_info">
				
			</div>
		</div>	

		<br />
		
		<div id="content">
			<?php echo $this->load->view($content) ?>
		</div>
	</body>
