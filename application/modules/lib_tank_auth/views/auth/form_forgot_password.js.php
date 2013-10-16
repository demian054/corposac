<script type="text/javascript">
	
    Ext.onReady(function() {
	
		/**
		 * Formulario que permite enviar un correo a un usuario para recuperar la contraseña.
		 * El formulario posee un campo donde se puede inidicar el nombre de usuario o correo electronico
		 * al cual sera enviado el correo de recuperacion
		 * */
		var form_forgot_password = new Ext.FormPanel({
			frame: false, 
			border: false, 
			buttonAlign: 'center',
			url: BASE_URL + 'lib_tank_auth/auth/forgot_password', 
			method: 'POST', 
			id: 'form_forgot_password',
			bodyStyle: 'padding: 20px 8px 8px 8px; background:#e8e8e8;',
			width: 350, 
			labelWidth: 100,
			// Definicion de campos pertenecientes al formulario
			items: [{
					xtype: 'textfield',
					fieldLabel: 'Usuario &oacute; Correo',
					name: 'login',
					id: 'login',
					blankText:  'El campo Correo Electr&oacute;nico es obligatorio',
					listeners: {
						render: function(c) {
							new Ext.ToolTip({
								target: c.getEl(),
								anchor: 'left',
								trackMouse: true,
								html: 'Debe colocar en este espacio el Usuario o el Correo Electr&oacute;nico que posee asociado en el Sistema'
							});
						}
					},
					allowBlank: false
				}
			],
			// Definicion de botones pertenecientes al formulario
			buttons: [
				{ text: 'Enviar', icon: BASE_ICONS + 'accept.png', handler: formForgotPasswordClick },
				{ text: 'Volver', icon: BASE_ICONS + 'house_go.png', handler: function() {
						window.location = BASE_URL;
					}
				}
			],
			keys: [
				{ key: [Ext.EventObject.ENTER], handler: formForgotPasswordClick }
			]
		});

		/**
		 * <b>Function: formForgotPasswordClick()</b>
		 * @description Observer que permite ejecutar el procesamiento del formulario form_forgot_password
		 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
		 * @version     V-1.0 31/08/12 11:44 AM
		 * */
		function formForgotPasswordClick() {
			
			// Antes de procesar el formulario se debe mostrar la mascara
			Ext.getCmp('form_forgot_password').on({
				beforeaction: function() {
					if (Ext.getCmp('form_forgot_password').getForm().isValid()) {
						Ext.getCmp('window_forgot_password').body.mask();
						Ext.getCmp('status_bar_window_forgot_password').showBusy();
					}
				}
			});
			
			// Al procesar el formulario se debe verificar si el usuario posee uno o varios roles
			Ext.getCmp('form_forgot_password').getForm().submit({
				
				// Si el request no presenta errores
				success: function(form, action) {
		    
					Ext.getCmp('status_bar_window_forgot_password').hide();
					Ext.getCmp('window_forgot_password').body.unmask();
			   
					var obj = Ext.util.JSON.decode(action.response.responseText);

					// Si se presentan errores de validacion mostrar mensaje de error
					if (obj.situation == 'no_valido') {
						Ext.getCmp('status_bar_window_forgot_password').setStatus({
							text: obj.msn,
							iconCls: 'x-status-error'
						});
						Ext.getCmp('status_bar_window_forgot_password').show();
					} 
					
					// Si el usurio posee un solo rol accede al sistema de forma directa
					else if(obj.situation == 'directo') {
			
						Ext.Msg.show({   
							title: 'Confirmaci&oacute;n',
							msg: obj.msn,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO,
							minWidth: 300,
							fn: function(){ 
								window.location = BASE_URL;
							}
						});
					}
				},
				
				// Si existe falla en la respuesta del request
				failure: function(form, action) {

					Ext.getCmp('window_forgot_password').body.unmask();
					
					// Si existe una falla en el servidor
					if (action.failureType == 'server') {
						obj = Ext.util.JSON.decode(action.response.responseText);
						Ext.getCmp('status_bar_window_forgot_password').setStatus({
							text: obj.msn,
							iconCls: 'x-status-error'
						});
					} 
					
					// Si no existe falla en el servidor se debe verificar la validez del formulario
					else {
						
						// Si el formulario no presenta fallas significa que no se puede contactar al servidor
						if (Ext.getCmp('form_forgot_password').getForm().isValid()) {
							Ext.getCmp('status_bar_window_forgot_password').setStatus({
								text: '<?=$this->lang->line('auth_failure_unreachable_server'); ?>',
								iconCls: 'x-status-error'
							});
						} 
						
						// En este caso la falla es por error en el formulario
						else {
							Ext.getCmp('status_bar_window_forgot_password').setStatus({
								text: '<?=$this->lang->line('auth_failure_validation_form'); ?>',
								iconCls: 'x-status-error'
							});
						}
					}
				}
			});
		}

		/**
		 * Panel principal que contiene el formulario form_forgot_password y una imagen que representa el logo del sistema
		 * */
		var panel_forgot_password = new Ext.Panel({
			layout:'column',
			border:false,
			bodyStyle: 'background:#e8e8e8;',
			items: [
				{width: 180,
					border:false,
					html:'<div style="background-color:#e8e8e8; ">&nbsp;&nbsp;&nbsp;<img src="'+BASE_URL+'assets/img/logo_login.png"/></div>'
				},		
				form_forgot_password
			]
		});

		/**
		 * Ventana que contiene el elemento panel_forgot_password
		 * Ademas posee una barra de estado donde se muestran mensajes de validacion, estado mientras se ejecuta un
		 * request, entre otros.
		 * */
		var window_forgot_password = new Ext.Window({
			title: 'Ouroboros',
			id: 'window_forgot_password',
			layout: 'fit',
			width: 550,
			height: 160,
			y: 100,
			resizable: false,
			closable: false,
			items: [panel_forgot_password],
			bbar: new Ext.ux.StatusBar({
				id: 'status_bar_window_forgot_password'
			})
		}).show();
    });
	
</script>
