<script type="text/javascript">

	Ext.onReady(function() {

		/**
		 * Formulario que permite iniciar sesion en el sistema. El formulario posee los siguientes campos
		 * Usuario: Nombre de Usuario para acceder al sistema
		 * Contraseña: Contraseña del usuario para acceder al sistema
		 * Adicionalmente posee un hipervinculo "¿ Olvido su Contraseña ?" para recuperar la contraseña del usuario
		 * */
		var form_login = new Ext.FormPanel({
			frame: false,
			border: false,
			buttonAlign: 'center',
			url: BASE_URL + 'lib_tank_auth/auth/login',
			method: 'POST',
			id: 'form_login',
			bodyStyle: 'padding: 8px 8px 8px 8px; background:#e8e8e8;',
			width: 350,
			labelWidth: 80,
			// Definicion de campos pertenecientes al formulario
			items: [{
					xtype: 'textfield',
					fieldLabel: 'Usuario',
					name: 'login',
					id: 'login',
					blankText:  'El campo Usuario es obligatorio',
					listeners: {
						render: function(c) {
							new Ext.ToolTip({
								target: c.getEl(),
								anchor: 'left',
								trackMouse: false,
								html: 'Debe colocar en este espacio su usuario, debe contener solo caracteres alfanumericos'
							});
						}
					},
					allowBlank: false
				}, {
					xtype: 'textfield',
					fieldLabel: 'Contrase&ntilde;a',
					name: 'password',
					id: 'password',
					allowBlank: false,
					blankText:  'El campo Contrase&ntilde;a es obligatorio',
					listeners: {
						render: function(c) {
							new Ext.ToolTip({
								target: c.getEl(),
								anchor: 'left',
								trackMouse: false,
								html: 'Debe colocar en este espacio su contrase&ntilde;a, debe contener solo caracteres alfanumericos'
							});
						}
					},
					inputType: 'password'
				},
				{
					xtype: 'container',
					html: "<a href='<?= base_url() ?>lib_tank_auth/auth/forgot_password' style='text-decoration: none; color: #666; font-weight: bold; display: block; margin-left: 95px;'>&iquest; Olvid&oacute; su Contrase&ntilde;a ?</a>"
				}
			],
			// Definicion de botones pertenecientes al formulario
			buttons: [
				{ text: 'Entrar', icon: BASE_ICONS + 'accept.png', handler: formLoginClick },
				{ text: 'Borrar', icon: BASE_ICONS + 'minus-circle.png', handler: function() {
						form_login.getForm().reset();
					}
				}
			],
			keys: [
				{ key: [Ext.EventObject.ENTER], handler: formLoginClick }
			]
		});

		/**
		 * <b>Function: formLoginClick()</b>
		 * @description Observer que permite ejecutar el procesamiento del formulario form_login
		 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
		 * @version     V-1.0 31/08/12 11:44 AM
		 * */
		function formLoginClick() {

			// Antes de procesar el formulario se debe mostrar la mascara
			Ext.getCmp('form_login').on({
				beforeaction: function() {
					if (form_login.getForm().isValid()) {
						Ext.getCmp('window_login').body.mask();
						Ext.getCmp('status_bar_window_login').showBusy();
					}
				}
			});

			// Al procesar el formulario se debe verificar si el usuario posee uno o varios roles
			form_login.getForm().submit({

				// Si el request no presenta errores
				success: function(form, action) {
					Ext.getCmp('window_login').body.unmask();

					var obj = Ext.util.JSON.decode(action.response.responseText);

					// Si se presentan errores de validacion mostrar mensaje de error
					if (obj.situation == 'no_valido') {
						Ext.getCmp('status_bar_window_login').setStatus({
							text: obj.msn,
							iconCls: 'x-status-error'
						});
						Ext.Msg.alert('<?= $this->lang->line('auth_validation_form_title_login'); ?>', obj.msn)
					}

					// Si el usurio posee un solo rol accede al sistema de forma directa
					else if(obj.situation == 'directo'){
						window.location = BASE_URL;
					}

					// Si el usuario posee mas de un rol se debe mostrar un panel para seleccionar el tipo de rol
					else {
						window_login.hide();
						createRolePanel(obj.extra_vars.user_id, obj.extra_vars.name, obj.extra_vars.last_name, obj.extra_vars.role_type);
					}
				},

				// Si existe falla en la respuesta del request
				failure: function(form, action) {
					
					Ext.getCmp('window_login').body.unmask();

					// Si existe una falla en el servidor
					if (action.failureType == 'server') {
						obj = Ext.util.JSON.decode(action.response.responseText);
						Ext.getCmp('status_bar_window_login').setStatus({
							text: obj.msn,
							iconCls: 'x-status-error'
						});
					}

					// Si no existe falla en el servidor se debe verificar la validez del formulario
					else {

						// Si el formulario no presenta fallas significa que no se puede contactar al servidor
						if (form_login.getForm().isValid()) {
							Ext.getCmp('status_bar_window_login').setStatus({
								text: '<?= $this->lang->line('auth_failure_unreachable_server'); ?>',
								iconCls: 'x-status-error'
							});
						}

						// En este caso la falla es por error en el formulario
						else {
							Ext.getCmp('status_bar_window_login').setStatus({
								text: '<?= $this->lang->line('auth_failure_validation_form'); ?>',
								iconCls: 'x-status-error'
							});
						}
					}
				}
			});
		}

		/**
		 * Panel principal que contiene el formulario form_login y una imagen que representa el logo del sistema
		 * */
		var panel_login = new Ext.Panel({
			layout:'column',
			border:false,
			bodyStyle: 'background:#e8e8e8;',
			items: [
				{width: 180,
					border:false,
					html:'<div style="background-color:#e8e8e8; ">&nbsp;&nbsp;&nbsp;<img src="'+BASE_URL+'assets/img/logo_login.png"/></div>'
				},
				form_login
			]
		});

		/**
		 * Ventana que contiene el elemento panel_login
		 * Ademas posee una barra de estado donde se muestran mensajes de validacion, estado mientras se ejecuta un
		 * request, entre otros.
		 * */
		var window_login = new Ext.Window({
			title: 'Ouroboros',
			id: 'window_login',
			layout: 'fit',
			width: 550,
			height: 160,
			y: 100,
			resizable: false,
			closable: false,
			items: [panel_login],
			bbar: new Ext.ux.StatusBar({
				id: 'status_bar_window_login'
			})
		}).show();

		/**
		 * <b>Function: createRolePanel()</b>
		 * @description Permite crear un panel donde se indican los roles que pueden ser seleccionados por un usuario
		 *              para iniciar sesion
		 * @param	    Integer id_user Identificador del usuario que inicia sesion
		 *              String name Nombre del usuario que inicia sesion
		 *              String last_name Apellido del usuario que inicia sesion
		 *              JSON Collection role_type objeto en formato JSON tipo etiqueta-valor que contiene los posibles
		 *              roles del usuario
		 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
		 * @version     V-1.0 30/08/12 05:05 PM
		 * */
		function createRolePanel(id_user, name, last_name, role_type) {

			// Creacion del formulario que contiene la informacion relacionada a los roles
			var form_role_type = new Ext.FormPanel({
				frame: false,
				border: false,
				buttonAlign: 'center',
				url: BASE_URL + 'lib_tank_auth/auth/login',
				method: 'POST',
				id: 'form_role_type',
				bodyStyle: 'padding:10px 10px 15px 15px;background:#e8e8e8;border:0px;',
				width: 200,
				labelWidth: 130,
				// Definicion de campos pertenecientes al formulario
				items: [{
						xtype: 'panel',
						html: '<div style="padding:6px;background:#e8e8e8;"><p style="line-height:15px"><b>Nombre: </b>' + name + '</p><p><b>Apellido: </b>' + last_name + '</p></div>'
					},{
						xtype: 'spacer',
						height: 10
					},{
						//@todo brecha de seguridad el id de ususario no puede ser hidden;
						xtype: 'hidden',
						height: 10,
						id:'id_user',
						name:'id_user',
						value:id_user
					},
					{
						xtype: 'combo',
						emptyText: 'Seleccione',
						autoload: true,
						width: 170,
						store: new Ext.data.JsonStore({
							fields: ['value', 'label'],
							data : role_type
						}),
						mode:'local',
						fieldLabel: 'Tipo de Usuario',
						hiddenName: 'select_rol',
						ref: 'ptu',
						displayField: 'label',
						valueField: 'value',
						triggerAction: 'all',
						allowBlank: false
					},
				],
				// Definicion de botones pertenecientes al formulario
				buttons: [
					{ text: 'Entrar', icon: BASE_ICONS + 'accept.png', handler: formRoleTypeClick },
					{ text: 'Borrar', icon: BASE_ICONS + 'broom-minus-icon.png', handler: function() {
							form_role_type.getForm().reset();
						}
					},
					{ text: 'Conectarse como otro Usuario', icon: BASE_ICONS + 'user_go.png', handler: function() {
							window.location = BASE_URL;
						}
					}
				]

			});

			// Definicion de la ventana que contiene el formulario de roles
			var window_role_type = new Ext.Window({
				title: 'Escojer Tipo de Usuario',
				id: 'window_role_type',
				layout: 'fit',
				width: 370,
				height: 180,
				y: 100,
				resizable: false,
				closable: false,
				items: [form_role_type]
			}).show();
		}

		/**
		 * <b>Function: formRoleTypeClick()</b>
		 * @description Observer que permite ejecutar el procesamiento del formulario form_role_type
		 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
		 * @version     V-1.0 31/08/12 11:44 AM
		 * */
		function formRoleTypeClick() {

			// Antes de procesar el formulario se debe mostrar la mascara
			Ext.getCmp('form_role_type').on({
				beforeaction: function() {
					if (Ext.getCmp('form_role_type').getForm().isValid()) {
						Ext.getCmp('window_role_type').body.mask();
					} else {
						Ext.getCmp('window_role_type').body.unmask();
					}
				}
			});

			// Procesamiento del formulario de roles
			Ext.getCmp('form_role_type').getForm().submit({
				success: function(form, action) {
					Ext.getCmp('window_role_type').body.unmask();
					var obj = Ext.util.JSON.decode(action.response.responseText);

					// Si se presentan errores de validacion mostrar mensaje de error
					if (obj.situation == 'no_valido')
						Ext.Msg.alert('<?= $this->lang->line('auth_validation_title_role_type'); ?>', obj.msn)

					// Si no existe error de validacion, se debe redireccionar al home para acceder al menu de operaciones
					else
						window.location = BASE_URL;
				}
			})
		}
	});

</script>