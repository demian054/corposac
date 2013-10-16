//<script>

	// Variables iniciales
	var arr_tab_1 = new Array();

	// Store que almacena las tablas del esquema de logica de negocio
	var business_logic_table = new Ext.data.JsonStore({
		url: BASE_URL+'adm_report/data_source/CL_getTables',
		method:'GET',
		autoLoad: true,
		autoDestroy:true,
		fields: ['value', 'label', 'schema']
	});

	// Creacion de selector de tablas de logica de negocio
	var itemselector_business_logic = new Ext.ux.form.ItemSelector({
		id: 'itemselector_business_logic',
		name: 'itemselector_business_logic',
		fieldLabel: 'Tablas',
		imagePath: BASE_ICONS,
		multiselects: [
			{
				id:'itemselector_business_logic_1',
				width: 198,
				height: 250,
				legend:'Disponibles',
				displayField: 'label',
				valueField: 'value',
				droppable: true,
				draggable: true,
				store: business_logic_table
			},
			{
				id:'itemselector_business_logic_2',
				width: 198,
				height: 250,
				legend:'Seleccionados',
				displayField: 'label',
				valueField: 'value',
				droppable: true,
				draggable: true,
				store: []
			}
		]
	});

	// Creacion del campo codigo
	textfield_codigo = new Ext.form.TextField({
		id:'textfield_codigo',
		fieldLabel: 'Código',
		width: 200,
		vtype: 'valid_alpha_numeric_space',
		allowBlank: false
	});

	// Creacion del campo nombre
	textfield_nombre = new Ext.form.TextField({
		id:'textfield_nombre',
		fieldLabel: 'Nombre',
		width: 200,
		vtype: 'valid_alpha_numeric_space',
		allowBlank: false
	});

	// Creacion del campo Observaciones
	textarea_observaciones = new Ext.form.TextArea({
		id:'textarea_observaciones',
		fieldLabel: 'Observaciones',
		width: 200,
		height: 83,
		vtype: 'valid_alpha_numeric_space'
	});

	// Creacion del panel con los campos para el tab_1
	form_panel_1 = new Ext.FormPanel({
		id: 'form_panel_1',
		border: false,
		bodyStyle: {paddingTop: '10px', paddingLeft: '35px' },
		style: {margin: 'auto'},
		defaults: {
			labelStyle: 'font-weight: bold;',
			style: { margin: '0 0 5px 0', padding: '2px' }
		},
		autoScroll: true,
		frame: true,
		height: 475,
		items: [textfield_codigo, textfield_nombre, itemselector_business_logic, textarea_observaciones]
	});

	// Creacion del tab_1
	var tab_panel_1 = 
		{ 
		id: 'tab_panel_1', 
		title: 'Datos Básicos',
		items: [form_panel_1]
	};

	/**
	 * <b>Function: submitTab1()</b>
	 * @description Observer que procesa los datos para cambiar del tab 1 al tab 2
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function submitTab1() { 
	
		// Verificacion de formulario
		if(Ext.getCmp('form_panel_1').getForm().isValid()) {
			
			// Verificacion de tablas seleccionadas
			if(Ext.getCmp('itemselector_business_logic_2').store.getCount() > 0) {
				
				// if(VALIDAR UNIQUE)
				
				// Definicion de elementos a procesar en el tab 1
				arr_tab_1['store_entities'] = Ext.getCmp('itemselector_business_logic').getValue();
				arr_tab_1['textfield_codigo'] = Ext.getCmp('textfield_codigo').getValue();
				arr_tab_1['textfield_nombre'] = Ext.getCmp('textfield_nombre').getValue();
				arr_tab_1['textarea_observaciones'] = Ext.getCmp('textarea_observaciones').getValue();
				
				// Implementar enviar datos al tab 2
				return true;
			}
			else
				buildMessageBox('Validación', '<?= $this->lang->line('message_entity_validation') ?>', true);
		}
		else
			return false;
	}

	/**
	 * <b>Function: activeTab1()</b>
	 * @description Observer que permite manejar los eventos una vez el tab se encuentra activo
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function activeTab1() {
		Ext.getCmp('form_panel_1').getForm().reset();
		Ext.getCmp('button_save').disable();
		Ext.getCmp('button_next').enable();
	}

	/**
	 * <b>Function: enableDisableSaveButton()</b>
	 * @description Observer que permite habilitar/deshabilitar el boton guardar/siguiente dependiendo
	 *              de la cantidad de tablas seleccionadas
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */
	function enableDisableSaveButton() { 
		if(Ext.getCmp('itemselector_business_logic_2').store.getCount() == 1) {
			Ext.getCmp('button_save').enable();
			Ext.getCmp('button_next').disable();
		}
		else {
			Ext.getCmp('button_save').disable();
			Ext.getCmp('button_next').enable();
		}
	}

	//</script>