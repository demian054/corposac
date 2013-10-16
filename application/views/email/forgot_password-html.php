<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<? $url = base_url() . 'assets/img/logo_login.png'; ?>
<html>
	<body>
		<div style="max-width: 800px; margin: 0; padding: 30px 0;">
			<table width="80%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="5%"></td>
					<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
						<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">¿Olvidó su Contraseña?</h2>
			
						Para actualizarlo sólo debe hacer click en el siguiente enlace:<br />
						<br />
						<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?= site_url('lib_tank_auth/auth/reset_password/' . $user_id . '/' . $new_pass_key); ?>">Crear nueva contraseña</a></b></big><br />
						<br />
						Si no funciona el enlace, copie y pegue la siguiente dirección en la barra del navegador:<br />
						<?= site_url('lib_tank_auth/auth/reset_password/' . $user_id . '/' . $new_pass_key); ?><br />
						<br />
					
						Usted ha recibido este correo, por requerimiento de un usuario del Sistema <a href="<?= site_url(''); ?>" style="color: #3366cc;"><?= $site_name; ?></a>. 
						Este procedimiento es parte de la creación de una nueva contraseña en el Sistema. 
						Si usted NO HA REQUERIDO una nueva contraseña, por favor ignore este correo y su contraseña seguirá siendo la misma.<br />
						<br />
						Gracias,<br />
						Sistema <?= $site_name; ?><br />
						<IMG SRC="<?= $url; ?>" WIDTH=210 HEIGHT=110 BORDER=0 ALIGN="left"/>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>