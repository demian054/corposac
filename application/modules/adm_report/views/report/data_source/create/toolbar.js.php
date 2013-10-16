//<script>

	/**
	 * <b>Function: generateMessageBox()</b>
	 * @description Observer que permite avanzar entre los tabs para la creacion del data_source
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function buildMessageBox(title, msg, error) {
	
		// Condicion para definir el icono del mensaje
		var icon = Ext.MessageBox.INFO;
		if(error)
			icon = Ext.MessageBox.ERROR;
	
		Ext.Msg.show({
			minWidth: 300,
			title:title,
			msg: msg,
			buttons: Ext.Msg.OK,
			icon: icon
		});
	}

	// Creacion del boton de navegacion atras
	var button_back = new Ext.Button({
		id: 'button_back',
		text: 'Atras',
		icon: BASE_URL+'assets/img/icons/arrow_left.png',
		hidden: true,
		handler: changeTabBackward
	});
	
	// Creacion del separador boton atras
	var button_back_separator = {id: 'button_back_separator', xtype: 'tbseparator', hidden: true};

	// Creacion del boton de navegacion siguiente
	var button_next = new Ext.Button({
		id: 'button_next',
		text: 'Siguiente',
		icon: BASE_URL+'assets/img/icons/arrow_right.png',
		handler: changeTabForward
	});
	
	// Creacion del separador boton siguiente
	var button_next_separator = {id: 'button_next_separator', xtype: 'tbseparator'};

	// Creacion del boton limpiar
	var button_clean = new Ext.Button({
		id: 'button_clean',
		text: 'Limpiar',
		icon: BASE_URL+'assets/img/icons/broom-minus-icon.png',
		handler: cleanTabs
	});

	// Creacion del separador boton limpiar
	var button_clean_separator = {id: 'button_clean_separator', xtype: 'tbseparator'};

	// Creacion del boton guardar
	var button_save = new Ext.Button({
		id: 'button_save',
		text: 'Guardar',
		icon: BASE_URL+'assets/img/icons/save.gif',
		disabled: true,
		handler: saveDataSource
	});
	
	// Creacion del separador boton guardar
//	var button_save_separator = {id: 'button_save_separator', xtype: 'tbseparator', hidden: true};
	
	// Creacion barra inferior de navegacion
	var toolbar_data_source = new Ext.Toolbar({
		autoWidth: true,
		items: ['->', button_back, button_back_separator, button_next, button_next_separator, button_clean, button_clean_separator, button_save]
	});

	/**
	 * <b>Function: changeTabForward()</b>
	 * @description Observer que permite avanzar entre los tabs para la creacion del data_source
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function changeTabForward() {
	
		// Evaluar el cambio de tab dependiendo del tab que se encuentre activo
		switch(Ext.getCmp('tab_panel_data_source').getActiveTab().id) {
	
			// Tab 1
			case 'tab_panel_1':
				
				// Si el formulario es valido se debe activar el siguiente tab
				if(submitTab1()) {
					Ext.getCmp('tab_panel_data_source').setActiveTab(1);
					Ext.getCmp('button_back').show();
					Ext.getCmp('button_back_separator').show();
					Ext.getCmp('button_next').hide();
					Ext.getCmp('button_next_separator').hide();
					Ext.getCmp('tab_panel_1').disable();
					Ext.getCmp('tab_panel_2').enable();
					activeTab2();
				}
				break;

			// Tab 2
			case 'tab_panel_2':
				Ext.getCmp('tab_panel_data_source').setActiveTab(2);
				Ext.getCmp('tab_panel_2').disable();
				Ext.getCmp('tab_panel_3').enable();
				activeTab3();
				break;
		}
	}
	
	/**
	* <b>Function: changeTabBackward()</b>
	* @description Observer que permite retroceder entre los tabs para la creacion del data_source
	* @author	    Reynaldo Rojas <rrojas@rialfi.com>
	* @version     V-1.0 31/08/12 11:44 AM
	* */	
	function changeTabBackward() {

		// Evaluar el cambio de tab dependiendo del tab que se encuentre activo
		switch(Ext.getCmp('tab_panel_data_source').getActiveTab().id) { 

			// Tab 2
			case 'tab_panel_2':
				Ext.getCmp('tab_panel_data_source').setActiveTab(0);
				Ext.getCmp('button_back').hide();
				Ext.getCmp('button_back_separator').hide();
				Ext.getCmp('button_next').show();
				Ext.getCmp('button_next_separator').show();
				Ext.getCmp('tab_panel_1').enable();
				Ext.getCmp('tab_panel_2').disable();
				break;
		}
	}

	/**
	* <b>Function: cleanTabs()</b>
	* @description Observer que permite limpiar el formulario del tab activo
	* @author	    Reynaldo Rojas <rrojas@rialfi.com>
	* @version     V-1.0 31/08/12 11:44 AM
	* */	
	function cleanTabs() {

		// Evaluar el tab que se encuentra activo
		switch(Ext.getCmp('tab_panel_data_source').getActiveTab().id) { 

			// Tab 1
			case 'tab_panel_1':
				activeTab1();
				break;

			// Tab 2
			case 'tab_panel_2':
				cleanTab2();
				break;
		}
	}

	/**
	* <b>Function: saveDataSource()</b>
	* @description Observer que permite guardar la fuente de datos generada
	* @author	   Reynaldo Rojas <rrojas@rialfi.com>
	* @version     V-1.0 22/10/12 11:51 AM
	* */	
	function saveDataSource() {

		// Si se selecciona solo una tabla se debe validar el formulario
		if(Ext.getCmp('tab_panel_data_source').getActiveTab().id == 'tab_panel_1') {
			if(!submitTab1())
				return false;
		}
		
		Ext.Ajax.request({
			url: 'adm_report/data_source/create',
			method: 'POST',
			params: defineParams,
			success: function(response) {
				
				var object_response = Ext.util.JSON.decode(response.responseText);
				var title = 'Crear Fuente de Datos';

				// Verificar si la creacion de la fuente de datos ha sido satisfactoria
				if(object_response.response.result) {
					buildMessageBox(title, object_response.response.msg, false);
					Ext.getCmp('window_create_data_source').close();
				}
				else
					buildMessageBox(title, object_response.response.msg, true);
			},
			failure: function() {
				buildMessageBox('Validación', '<?= $this->lang->line('ajax_request_failure') ?>', true);
			}
		});
	}

	/**
	* <b>Function: defineParams()</b>
	* @description Permite definir los parametros adicionales dependiendo del tab que se encuentre activo
	* @author	    Reynaldo Rojas <rrojas@rialfi.com>
	* @version     V-1.0 22/10/12 04:38 PM
	* */	
	function defineParams() { 
	
		var params = '';
	
		// Evaluar el tab que se encuentra activo
		switch(Ext.getCmp('tab_panel_data_source').getActiveTab().id) { 

			// Tab 1
			case 'tab_panel_1':

			// Definicion de parametros cuando se procesa solo una tabla
			params = {'form_data': Ext.encode(Ext.getCmp('form_panel_1').getForm().getValues()),
					  'sql_params': Ext.encode({'table_pivot': Ext.getCmp('itemselector_business_logic_2').store.data.items[0].json.schema+'.'+
									   						   Ext.getCmp('itemselector_business_logic_2').store.data.items[0].json.label})};
				break;

			// Tab 2
			case 'tab_panel_2':
				
			// Definicion de parametros cuando se procesa mas de una tabla
			params = {'form_data': Ext.encode(Ext.getCmp('form_panel_1').getForm().getValues()),
					  'sql_params': Ext.encode({'table_pivot': join_store.data.items[0].data.grid_filter_table_pivot,
												'joins': Ext.encode(getJoins())})};				
				break;
		}
		
		return params;
	}

	

//</script>
