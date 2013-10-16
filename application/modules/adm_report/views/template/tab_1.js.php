//<script>
    // Variables iniciales
    var arr_1 = new Array();

    var store_grid_panel_1 = new Ext.data.JsonStore({
        autoLoad:   false,
        autoDestroy:    true,
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
        ],
        data: <?= $store_columns?>
        ,
        proxy: new Ext.data.HttpProxy({
            url: BASE_URL + 'adm_report/data_source/CL_getEntitiesColumns',
            method:'GET'
        })
    });


    //Grid de columnas
    var grid_panel_1 = new Ext.grid.EditorGridPanel({
        id: 'grid_panel_1',
        title:'Columnas',
        region: 'north',
        trackMouseOver:1,
        //height: 295,
        disableSelection:false,
        loadMask: false,
        store: store_grid_panel_1,
        clicksToEdit: 1,
        columns:[
            {xtype: 'actioncolumn',
                header: '',
                width: 75,
                align: 'center',
                fixed: true,
                frame:true,
                hideable: false,
                menuDisabled:true,
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
                fixed:true,
                hideable: false,
                menuDisabled:true
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
                menuDisabled:true,
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
                menuDisabled:true
            },
            {header: 'Función',
                dataIndex: 'sql_function',
                width: 99,
                align: 'center',
                fixed:true,
                menuDisabled:true,
                editor: new Ext.form.ComboBox({
                    id: 'combobox_sql_function',
                    typeAhead: true,
                    triggerAction: 'all',
                    forceSelection: true,
                    lazyRender:true,
                    mode: 'local',
                    editable: false,
                    store: new Ext.data.JsonStore({
                        fields: ['name', 'label'],
                        proxy: new Ext.data.HttpProxy({
                            method: 'GET',
                            url: BASE_URL + 'adm_report/data_source/CL_getGroupByFunction'
                        })
                    }),
                    valueField: 'value',
                    displayField: 'label'
                }),
                listeners: {
                    'click': function(element, grid, rowIndex) {

                        // Recargar el store de columnas
                        element.editor.store.load({
                            params:{data_type:store_grid_panel_1.getAt(rowIndex).data.data_type}
                        });
                    }
                }
            },
            {header: 'Orden',
                dataIndex: 'order_by',
                width: 85,
                align: 'center',
                fixed:true,
                menuDisabled:true,
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

    /**
     * <b>Method:	setGroupBy()</b>
     * @method		Fija el group by a los elementos que no tienen funciones de agregacion
     * @author		Eliel Parra
     * @version		v1.0 09/02/12 11:30 AM
     **/
    function setGroupBy() {

        flag_group_by = false;
        store_grid_panel_1.each(function(rec){
            if((rec.data.sql_function) && (rec.data.visible == true))
                flag_group_by = true;
        });
        if(flag_group_by == true){
            store_grid_panel_1.each(function(rec){
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
    function getColumns(){
		arr_1 = Array();
        Ext.getCmp('grid_panel_1').store.each(function(rec){
            if(rec.data.visible)
                arr_1.push(rec.data);
        });

        if(arr_1.length<=0)
            return false;
        else
            return arr_1;
    }