<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<? $url = base_url().'img/sietpol.jpg'; ?>
<html>
<head><title>Su nueva direccion de correo en <?= $site_name; ?></title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Su nueva direccion de correo en <?= $site_name; ?></h2>
Usted ha cambiado su Email para <?= $site_name; ?>.<br />
Para confirmar el cambio solo haz click a este enlace:<br />
<br />
<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;">Confirmar su nuevo Email</a></b></big><br />
<br />
No funciona este Link? Copia la siguiente direccion en la barra del navegador:<br />
<nobr><a href="<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?></a></nobr><br />
<br />
<br />
Su nuevo email: <?php echo $new_email; ?><br />
<br />
<br />
Usted ha recibido este correo, por requerimiento de un usuario del Sistema <a href="<?= site_url(''); ?>" style="color: #3366cc;"><?= $site_name; ?></a>. Ai usted ha recibido este correo por error, Por favor NO HAGA CLICK en el link de confirmacion y simplemente elimine este correo. Automaticamente en cierto tiempo, el requerimiento sera removido del Sistema.<br />
<br />
<br />
Gracias,<br />
Equipo <?= $site_name; ?><br />
<IMG SRC="<?= $url; ?>" WIDTH=210 HEIGHT=110 BORDER=0 ALIGN="left"/>
</td>
</tr>
</table>
</div>
</body>
</html>