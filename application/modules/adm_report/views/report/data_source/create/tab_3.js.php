//<script>

	// Variables iniciales
	var arr_tab_3 = new Array();

	//Retorna las columnas segun las entidades seleccionadas en el tab 1
	var proxy = new Ext.data.HttpProxy({
		api: {
			read: {
				url: BASE_URL + 'adm_report/data_source/CL_getEntitiesColumns',
				method:'GET'
			}        
		}
	});

	//Reader del proxy
	var reader = new Ext.data.JsonReader({
		fields: [  
			{name: 'id'},
			{name: 'entity_id'},
			{name: 'name'},
			{name: 'visible', type: 'bool'},
			{name: 'header'},
			{name: 'order'},
			{name: 'data_type'},
			{name: 'field_type'},
			{name: 'sql_function'},
			{name: 'order_by'},
			{name: 'group_by'},
			{name: 'entity_name'}
		]
	});
	
	// Writer del proxy
	var writer = new Ext.data.JsonWriter({
		encode: true,
		writeAllFields: false
	});

	//Data Store de las columnas segun las entidades seleccionadas
	var store_grid_panel_3 = new Ext.data.Store({
		proxy: proxy,
		reader: reader,
		writer: writer,
		autoSave: false
	});
	
	//Grid de columnas
	var grid_panel_3 = new Ext.grid.EditorGridPanel({
		id: 'grid_panel_3',
		trackMouseOver:1,
		disableSelection:false,
		loadMask: false,
		store: store_grid_panel_3,
		clicksToEdit: 1,
		columns:[		
			{xtype: 'actioncolumn',
				header: '',
				width: 75,
				align: 'center',
				fixed: true,
				frame:true,
				hideable: false,
				sortable: false,
				items: [{
						// Subir al tope
						icon:BASE_ICONS + 'arrow_double_up.png',
						tooltip: 'Subir al Inicio',
						handler: function(grid, rowIndex){
						
							// Si es el primer registro no debe subir
							if(rowIndex == 0)
								return false;
						
							// Se obtiene el registro seleccionado
							selected_record = grid.store.getAt(rowIndex);
								
							// Se elimina el registro de la posicion actual
							grid.store.removeAt(rowIndex);
													
							// Se agrega el registro al inicio del store
							grid.store.insert(0,selected_record);
						}
					},{
						// Subir una posicion
						icon:BASE_ICONS + 'arrow_up_yellow.png',
						tooltip: 'Subir',
						handler: function(grid, rowIndex){
						
							// Si es el primer registro no debe subir
							if(rowIndex == 0)
								return false;
						
							// Se obtiene el registro seleccionado
							selected_record = grid.store.getAt(rowIndex);
								
							// Se elimina el registro de la posicion actual
							grid.store.removeAt(rowIndex);
													
							// Se agregan el registro en la posicion anterior
							grid.store.insert(rowIndex-1,selected_record);
						}
					},{
						// Bajar una posicion
						icon:BASE_ICONS + 'arrow_down_yellow.png',
						tooltip: 'Bajar',
						handler: function(grid, rowIndex){
						
							// Si es el ultimo registro no puede bajar
							if((grid.store.getCount()-1) == rowIndex)
								return false;
								
							// Se obtiene el registro seleccionado
							selected_record = grid.store.getAt(rowIndex);
								
							// Se elimina el registro de la posicion actual
							grid.store.removeAt(rowIndex);
													
							// Se agregan el registro en la posicion siguiente
							grid.store.insert(rowIndex+1,selected_record);
						}
					},{
						// Bajar al fondo
						icon:BASE_ICONS + 'arrow_double_down.png',
						tooltip: 'Bajar al Final',
						handler: function(grid, rowIndex){
						
							// Si es el ultimo registro no puede bajar
							if((grid.store.getCount()-1) == rowIndex)
								return false;
								
							// Se obtiene el registro seleccionado
							selected_record = grid.store.getAt(rowIndex);
								
							// Se elimina el registro de la posicion actual
							grid.store.removeAt(rowIndex);
													
							// Se agregan el registro en la ultima posicion
							grid.store.insert((grid.store.getCount()),selected_record);
						}
					}]
			},
			{header: 'Visible',
				xtype: 'checkcolumn',
				dataIndex: 'visible',
				width: 45,
				align: 'center',
				sortable:false,
				fixed:true,
				hideable:false
			},
			{header: 'Group By',
				xtype: 'checkcolumn',
				dataIndex: 'group_by',
				width: 70,
				align: 'center',
				sortable:false,
				fixed:true,
				hideable:false,
				hidden:true
			},
			{header: 'Encabezado', 
				dataIndex: 'header',
				width: 204,
				align: 'left', 
				sortable:true,
				fixed:true,
				hideable:false,
				editor: new Ext.form.TextField({
					maxLength:150,
					autoShow:true,
					vtype: 'valid_alpha_numeric_space',
					allowBlank: false
				})},
			{header: 'Entidad',
				dataIndex: 'entity_name',
				width: 100,
				align: 'left', 
				sortable:true,
				fixed:true,
				hideable:false
			},
			{header: 'Función',
				dataIndex: 'sql_function',
				width: 99,
				align: 'center', 
				sortable:false,
				fixed:true,
				hideable:false,
				editor: new Ext.form.ComboBox({
					id: 'combobox_sql_function',
					typeAhead: true,
					triggerAction: 'all',
					forceSelection: true,
					lazyRender:true,
					mode: 'local',
					editable: false,
					store: new Ext.data.JsonStore({
						fields: ['value', 'label'],
						proxy: new Ext.data.HttpProxy({
							method: 'GET',
							url: BASE_URL + 'adm_report/report/CL_getGroupByFunction'
						})
					}),
					valueField: 'value',
					displayField: 'label'
				}),
				listeners: {
					'click': function(element, grid, rowIndex) {
						
						// Recargar el store de columnas
						element.editor.store.load({
							params:{data_type:store_grid_panel_3.getAt(rowIndex).data.data_type}
						});
					}
				}
			},
			{header: 'Orden',
				dataIndex: 'order_by',
				width: 85,
				align: 'center', 
				sortable:false,
				fixed:true,
				hideable:false,
				editor: new Ext.form.ComboBox({
					id: 'combobox_order_by',
					typeAhead: true,
					triggerAction: 'all',
					lazyRender:true,
					forceSelection: true,
					mode: 'local',
					editable: false,
					store: new Ext.data.JsonStore({
						url: BASE_URL + 'adm_report/report/CL_getOrderBy',
						method:'GET',
						fields: ['value', 'label']
					}),
					valueField: 'value',
					displayField: 'label'
				})
			}
		]
	});
	
	//	//Tab 2
	//	var tab2={
	//		id:'Tab_523_2',
	//		name:'Tab_523_2',
	//		title:'2. Columnas', 
	//		layout: 'fit', 
	//		active:false,
	//		disabled:true,
	//		items:[grid_panel_3],
	//		bbar:new Ext.Toolbar({
	//			items:[
	//				{xtype: 'tbfill'},
	//				{text:'Atrás',
	//					icon: BASE_ICONS + 'arrow_left.png',
	//					itemCls:    'centrado',
	//					handler: function(){
	//						Ext.getCmp('tabs_523').setActiveTab(0);
	//						Ext.getCmp('Tab_523_2').disable();
	//					}
	//				},
	//				'-',
	//				{text:'Siguiente',
	//					icon: BASE_ICONS + 'arrow_right.png',
	//					itemCls:    'centrado',
	//					handler: function(){
	//						setGroupBy();
	//						getColumns(columns);
	//						columnsListStore.commitChanges();
	//						//Cuando no se seleccionan columnas
	//						if(columns==false){
	//							Ext.Msg.show({
	//								minWidth: 300,
	//								title:'Error!',
	//								msg: 'Debe escoger al menos una Columna',				
	//								buttons: Ext.Msg.OK,
	//								icon: Ext.MessageBox.ERROR
	//							});
	//						}else{
	//							Ext.getCmp('Tab_523_3').enable();
	//							Ext.getCmp('tabs_523').setActiveTab(2);
	//							//Limpiar todo el tab 3
	//							resetTab3();
	//							filtro_store.removeAll();
	//							filtro_store.reload();
	//						}
	//					}
	//				},
	//				'-',	   
	//				{text:       'Limpiar',
	//					icon:       BASE_ICONS + 'broom-minus-icon.png',
	//					itemCls:    'centrado',
	//					handler:    function(){ clearColumnsGrid()}
	//				}		
	//			]})
	//	}
	//	
	//	//Agregar el tab columnas en el tab panel
	//	Ext.getCmp('tabs_523').add(tab2);
	//	
	//	//Observer de tab 2 cuando esta activo
	//	Ext.getCmp('Tab_523_2').on('activate',function(){
	//		columns.length = 0;
	//		Ext.getCmp('Tab_499').purgeListeners();
	//		var entidades=Ext.getCmp('combo_499_7343').getValue();
	//		if(!form_499.getForm().isValid() || empty(entidades)){			
	//			Ext.getCmp('tabs_523').setActiveTab(0);			 
	//		}else{
	//			Ext.getCmp('grid_panel_3').store.load({
	//				params:{entities:entidades}
	//			});
	//		}
	//		
	//		Ext.getCmp('Tab_523_3').disable();
	//	});

	// Creacion del tab_3
	var tab_panel_3 = 
		{ 
		id: 'tab_panel_3', 
		title: 'Columnas',
		disabled: true,
		layout: 'fit',
		items: [grid_panel_3]
	};

	/**
	 * <b>Function: submitTab3()</b>
	 * @description Observer que procesa los datos para cambiar del tab 3 al tab 4
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function submitTab3() { 
	
		// Definicion de campos que deben pertenecer al argumento group by
		setGroupBy();
		
		// Definicion de los campos seleccionados en el grid
		setColumns();
		store_grid_panel_3.commitChanges();
	
		// Condicion para verificar la seleccion de al menos un campo
		if(arr_tab_3 == false)
			buildMessageBox('Validación', '<?= $this->lang->line('message_field_validation') ?>');
		else
			return true;
	}

	/**
	 * <b>Function: activeTab3()</b>
	 * @description Observer que permite manejar los eventos una vez el tab se encuentra activo
	 * @author	    Reynaldo Rojas <rrojas@rialfi.com>
	 * @version     V-1.0 31/08/12 11:44 AM
	 * */	
	function activeTab3() { 
		
		// Recargar el store de columnas
		Ext.getCmp('grid_panel_3').store.load({
			params:{store_entities:3} //CABLE
		});
		
		// Recargar el store de opciones order by
		Ext.getCmp('combobox_order_by').store.reload();
		
		// Recargar el store de funciones de agregacion
		Ext.getCmp('combobox_sql_function').store.load();
	}


	/**
	 * <b>Method:	setGroupBy()</b>
	 * @method		Fija el group by a los elementos que no tienen funciones de agregacion
	 * @author		Eliel Parra
	 * @version		v1.0 09/02/12 11:30 AM
	 **/
	function setGroupBy() {
		
		flag_group_by = false;
		store_grid_panel_3.each(function(rec){
			if((rec.data.sql_function) && (rec.data.visible == true)) 
				flag_group_by = true;
		});
		if(flag_group_by == true){
			store_grid_panel_3.each(function(rec){
				if(rec.data.visible == true){
					if(!rec.data.sql_function)
						rec.data.group_by = true;
				}
			});
		}
	}

	/**
	 * <b>Method:	getColumns()</b>
	 * @method		Retorna las columnas seleccionadas por el usuario
	 * @param		columns
	 * @return		columns
	 * @author		Eliel Parra, Reynaldo Rojas
	 * @version		v1.0 27/01/12 11:57 AM
	 **/
	function setColumns(){
		
		Ext.getCmp('grid_panel_3').store.each(function(rec){ 
			if(rec.data.visible)
				arr_tab_3.push(rec.data);
		});
		
		if(arr_tab_3.length<=0)
			return false;
		else		
			return arr_tab_3;
	}

	//</script>
