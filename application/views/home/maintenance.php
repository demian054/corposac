<html>
    <head>
        <title>Proyecto X - MANTENIMIENTO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/style.css"/>
    </head>
    <body  class="bglogin">
     <div style="height:100%; position:relative;">
        <div id="maintenance_body" class="browsers">
            <div id="maintenance_logo_sietpol">
              <img src="<?=base_url()?>assets/img/logo_login.png">
            </div>
            <div id="maintenance_message">
                <?= $this->lang->line('message_maintenance')?>
            </div>
            <div id="maintenance_date">
              <?php
                if($this->config->item('maintenance')){
                  echo $this->lang->line('message_maintenance_undefined');
                } else {
                  $maintenance = $this->session->userdata('maintenance');
                  setlocale(LC_ALL,'es_VE.UTF-8');
                  $date = new DateTime($maintenance['ffin_maintenance']);
                  $format = '%A, %d de %B de %Y a las %I:%M %P';
                  $maintenance['ffin_maintenance'] = strftime($format, $date->getTimestamp());
                  echo $this->lang->line('message_maintenance_end_date'),' ', $maintenance['ffin_maintenance'];
                }

              ?>
            </div>
            <div id="maintenance_apology"><?=$this->lang->line('message_maintenance_apology') ?></div>
        </div>
     </div>
    </body>
</html>
