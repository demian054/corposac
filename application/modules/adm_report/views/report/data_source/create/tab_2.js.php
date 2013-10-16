//<script>

	/**
	 * <b>Function: generateNorthInnerPanel()</b>
	 * @description Permite generar un panel que posee un combobox de tablas y un combobox de campos. El combobox de
	 *				campos se genera dependiendo de la tabla seleccionada en el combobox de tablas
	 * @params      String id_inner_panel Identificador del panel
	 *		        String id_combobox_table Identificador del combobox de tablas
	 *			    String id_combobox_field Identificador del combobox de campos
	 * @return		Ext.Panel Panel contenedor
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function generateNorthInnerPanel(id_inner_panel, id_combobox_table, id_grid_field) {
		
		// Creacion del combobox que posee las tablas seleccionadas
		var combobox_table = new Ext.form.ComboBox({
			id: id_combobox_table,
			typeAhead: true,
			triggerAction: 'all',
			forceSelection: true,
			lazyRender:true,
			mode: 'local',
			editable: false,
			fieldLabel: 'Tabla',
			allowBlank: false,
			store:new Ext.data.JsonStore({
				fields: ['value', 'label']
			}),
			valueField: 'value',
			displayField: 'label',
			listeners: {
				'select': function(element){
					Ext.getCmp(id_grid_field).store.removeAll();
					Ext.getCmp(id_grid_field).store.load({
						params:{store_entities:element.value}
					});
				}
			}
		});
		
		// Creacion del grid que posee los campos dependiendo de la tabla seleccionada
		var list_view = new Ext.grid.GridPanel({
			id: id_grid_field,
			selModel: new Ext.grid.RowSelectionModel({singleSelect: true}),
			frame: true,
			height: 225,
			width: 265,
			style: {marginTop: '100px'},
			store: new Ext.data.JsonStore({
				fields: ['id', 'field_name'],
				proxy: new Ext.data.HttpProxy({
					method: 'GET',
					url: BASE_URL + 'adm_report/data_source/CL_getEntitiesColumns'
				})
			}),
			columns: [{
					header: 'Campos',
					dataIndex: 'field_name',
					width: 233
				}]
		});
		
		// Panel contenedor de los comboboxes de tablas y campos
		return new Ext.Panel({
			id: id_inner_panel,
			height: 275,
			border: false,
			frame: true,
			layout: 'form',
			style: {margin: 'auto'},
			labelWidth: 60,
			defaults: {
				labelStyle: 'font-weight: bold;',
				style: { margin: '0 0 5px 0'}
			},
			items: [combobox_table, list_view]
		});
	}

	// Panel que contiene los selectores de las tablas y campos que se van a asociar
	var north_panel = {
		id: 'north_panel',
		region: 'north',
		height: 275,
		layout: 'border',
		border: false,
		items:[
			{
				id: 'north_west_panel',
				region: 'west',
				width: '50%',
				border: false,
				items: [generateNorthInnerPanel('north_west_inner_panel', 'north_west_combobox_table', 'north_west_grid_field')]
			},
			{
				id: 'north_center_panel',
				region: 'center',
				width: '50%',
				border: false,
				items: [generateNorthInnerPanel('north_east_inner_panel', 'north_east_combobox_table', 'north_east_grid_field')]
			}
		]
	}			

	// Creacion del combobox que posee el tipo de relaciones
	var combobox_relation_type = new Ext.form.ComboBox({
		id: 'combobox_relation_type',
		typeAhead: true,
		triggerAction: 'all',
		forceSelection: true,
		lazyRender:true,
		mode: 'local',
		editable: false,
		allowBlank: false,
		fieldLabel: 'Tipo de Relación',
		store: new Ext.data.JsonStore({
			fields: ['value', 'label'],
			proxy: new Ext.data.HttpProxy({
				method: 'GET',
				url: BASE_URL + 'adm_report/data_source/CL_getRelationType'
			})
		}),
		valueField: 'value',
		displayField: 'label'
	});

	// Espacio html
	var html_space = {html: '&nbsp;&nbsp;&nbsp;'};
	
	// Creacion de boton para generar las relaciones entre las tablas
	var button_relation = new Ext.Button({
		id: 'button_relation',
		text: 'Generar Relación',
		icon: BASE_ICONS + 'link_add.png',
		handler: generateRelation
	});

	// Panel contenedor del combobox de tipos de relacion
	var center_inner_panel = new Ext.Panel({
		id: 'center_inner_panel',
		height: 50,
		border: false,
		frame: true,
		layout: 'border',
		items:[{
				layout: 'form',
				region: 'west',
				width: '55%',
				style: {margin: 'auto'},
				labelWidth: 105,
				defaults: {
					labelStyle: 'font-weight: bold;',
					style: { margin: '0 0 5px 0'}
				},
				items: [combobox_relation_type]
			},
			{
				region: 'center',
				width: '45%',
				items: [button_relation]
			}]
	});

	// Panel que contiene el tipo de union entre las tablas
	var center_panel = {
		id: 'center_panel',
		region: 'center',
		height: 50,
		border: false,
		items: [center_inner_panel]
	}
	
	//Data store de los filtros
	var join_store = new Ext.data.ArrayStore({
		id: 'join_store',
		fields: [
			{name: 'grid_filter_delete', type: 'text'},
			{name: 'grid_filter_detail', type: 'text'},
			{name: 'grid_filter_table_pivot', type: 'text'},
		]
	});
	
	// Grid que contiene los filtros generados por el usuario
	var grid_join_panel = new Ext.grid.GridPanel({
		id: 'grid_join_panel',
		frame: true,
		loadMask: false,
		height:	150,
		columns:[
			{xtype: 'actioncolumn',
				id: 'grid_filter_delete',
				name: 'grid_filter_delete',
				width: 40,
				items: [{
						icon   : BASE_ICONS + 'minus-circle.png',
						tooltip: 'Eliminar Filtro',
						handler: function(grid, rowIndex, colIndex) {
							record = join_store.getAt(rowIndex);
							join_store.remove(record);
							changeSaveButtonAvailability();
						}
					}
				]
			},
			{id: 'grid_filter_detail',
				name: 'grid_filter_detail',
				header: 'Filtro',
				width: 530
			}
		],
		store: join_store
	});
	
	// Panel que contiene el resultado de la union de las tablas
	var south_panel = {
		id: 'south_panel',
		region: 'south',
		height: 150,
		border: false,
		items: [grid_join_panel]
	}

	// Panel General de relaciones
	var relation_panel = new Ext.FormPanel({
		id: 'relation_panel',
		layout: 'border',
		height: 475,
		border: false,
		items: [north_panel, center_panel, south_panel]
	});

	// Creacion del tab_2
	var tab_panel_2 = 
		{ 
		id: 'tab_panel_2', 
		title: 'Relaciones', 
		disabled: true,
		border: false,
		items: relation_panel
	};

	/**
	 * <b>Function: resetGenerateRelation()</b>
	 * @description Permite restaurar los elementos del formulario una vez generada una nueva relacion
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function resetGenerateRelation() {
		Ext.getCmp('relation_panel').getForm().reset();
		Ext.getCmp('north_west_grid_field').store.removeAll();
		Ext.getCmp('north_east_grid_field').store.removeAll();
	}

	/**
	 * <b>Function: cleanTab2()</b>
	 * @description Permite restaurar los elementos del tab de relaciones
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function cleanTab2() {
		resetGenerateRelation();
		join_store.removeAll();
		changeSaveButtonAvailability();
	}

	/**
	 * <b>Function: activeTab2()</b>
	 * @description Observer que permite manejar los eventos una vez el tab se encuentra activo
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function activeTab2() {
		
		// Reiniciar los componentes del formulario
		cleanTab2();
		
		// Carga de stores de los comboboxes del formulario
		Ext.getCmp('north_west_combobox_table').store = Ext.getCmp('itemselector_business_logic_2').store;
		Ext.getCmp('north_east_combobox_table').store = Ext.getCmp('itemselector_business_logic_2').store;
		Ext.getCmp('combobox_relation_type').store.reload();
	}

	/**
	 * <b>Function: generateRelation()</b>
	 * @description Permite generar una relacion entre dos tablas
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 19/10/12 10:10 AM
	 * */	
	function generateRelation() {
		
		// Verificacion de formulario
		if(Ext.getCmp('relation_panel').getForm().isValid()) {
		
			// Verificacion de seleccion de campos
			if(!((Ext.getCmp('north_west_grid_field').getSelectionModel().getSelected() == undefined) || (Ext.getCmp('north_east_grid_field').getSelectionModel().getSelected() == undefined))) {
		
				// Generar el nuevo registro
				var new_record = new join_store.recordType({
					grid_filter_delete : '',
					grid_filter_detail: generateJoinString(),
					grid_filter_table_pivot: getTablePivot()
				});

				// Añadir el registro al store
				join_store.insert((join_store.getCount()), new_record);
		
				// Verificar si se debe habilitar/deshabilitar el boton guardar
				changeSaveButtonAvailability();
		
				// Reiniciar los campos del tab actual
				resetGenerateRelation();
			}
			else {
				buildMessageBox('Validación', '<?= $this->lang->line('message_field_validation') ?>', true);
				return false;
			}
		}
		else
			return false;
	}

	/**
	 * <b>Function: generateJoinString()</b>
	 * @description Permite generar el string de relacion de las tablas
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 19/10/12 12:52 PM
	 * */	
	function generateJoinString() {
		
		// Valores para construir la relacion entre tablas
		var record_west_combobox_field = Ext.getCmp('north_west_grid_field').getSelectionModel().getSelected();
		var record_east_combobox_field = Ext.getCmp('north_east_grid_field').getSelectionModel().getSelected();
		var relation_type = Ext.getCmp('combobox_relation_type').getValue();
		
		// Creacion del string de relacion
		var relation = relation_type+' '+
			record_east_combobox_field.json.entity_schema+'.'+
			record_east_combobox_field.json.entity_name+' ON '+
			record_west_combobox_field.json.name+' = '+
			record_east_combobox_field.json.name;
		
		return relation;
	}
	
	/**
	 * <b>Function: getTablePivot()</b>
	 * @description Permite obtener la tabla que va a ser considerada como el from de la consulta
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 23/10/12 02:02 PM
	 * */	
	function getTablePivot() { 
		var record_west_combobox_field = Ext.getCmp('north_west_grid_field').getSelectionModel().getSelected();
		var table_pivot = record_west_combobox_field.json.entity_schema+'.'+record_west_combobox_field.json.entity_name;
		return table_pivot;
	}


	/**
	 * <b>Function: changeSaveButtonAvailability()</b>
	 * @description Permite cambiar el boton guardar enable/disable dependiendo de si existen filtros definidos
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 23/10/12 02:07 PM
	 * */
	function changeSaveButtonAvailability() {
		if(join_store.getCount() > 0)
			Ext.getCmp('button_save').enable();
		else
			Ext.getCmp('button_save').disable();
	}

	/**
	 * <b>Method:	getJoins()</b>
	 * @method		Retorna los joins generados por el usuario
	 * @return		filter
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 27/01/12 11:57 AM
	 **/
	function getJoins() {
		var join = new Array();
		if(join_store.getCount()<=0)return false;
		join_store.each(function(item){
			join.push(item.data.grid_filter_detail);
		});
		if(join.length<=0)return false;
		return join;
	}

	//</script>