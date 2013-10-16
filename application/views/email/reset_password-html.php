<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<body>
		<div style="max-width: 800px; margin: 0; padding: 30px 0;">
			<table width="80%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="5%"></td>
					<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
						<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">
							Su nueva contrase&ntilde;a en <?php echo $site_name; ?></h2>
						Usted ha cambiado su contrase&ntilde;a satisfactoriamente.<br />
						Por favor conserve su contrase&ntilde;a en un lugar seguro.<br />
						<br />
						<?php if (strlen($username) > 0) { ?>Su Usuario: <?php echo $username; ?><br /><?php } ?>
						Su Correo Electr&oacute;nico: <?php echo $email; ?><br />
						<br />
						Gracias,<br />
						Sistema <?php echo $site_name; ?>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>