<script type="text/javascript">
    Ext.onReady(function() {
	
		/**
		 * Panel tipo formulario que permite mostrar el mensaje que indica que la clave del usuario es incorrecta
		 * o ha caducado. El formulario posee un boton para volver al inicio de sesion
		 * */
		var form_expired_password = new Ext.FormPanel({
			frame: false, border: false, buttonAlign: 'center',
			bodyStyle: 'padding: 20px 8px 8px 8px; background:#e8e8e8;',
			width: 350, labelWidth: 85,
			html: '<?= $msg ?>',
			// Definicion de botones pertenecientes al formulario
			buttons: [
				{ text: 'Ir al Inicio', icon: BASE_ICONS + 'accept.png', handler: function() {
						window.location = BASE_URL;
					}
				}
			]
		});

		/**
		 * Panel principal que contiene el formulario form_expired_password y una imagen que representa el logo del sistema
		 * */
		var panel_expired_password = new Ext.Panel({
			layout:'column',
			border:false,
			bodyStyle: 'background:#e8e8e8;',
			items: [
				{width: 180,
					border:false,
					html:'<div style="background-color:#e8e8e8; ">&nbsp;&nbsp;&nbsp;<img src="'+BASE_URL+'assets/img/logo_login.png"/></div>'
				},		
				form_expired_password
			]
		});

		/**
		 * Ventana que contiene el elemento panel_forgot_password
		 * Ademas posee una barra de estado donde se muestran mensajes de validacion, estado mientras se ejecuta un
		 * request, entre otros.
		 * */
		var window_expired_password = new Ext.Window({
			title: 'Ouroboros',
			id: 'window_expired_password',
			layout: 'fit',
			width: 550,
			height: 160,
			y: 100,
			resizable: false,
			closable: false,
			items: [panel_expired_password] }).show();
    });
</script>
