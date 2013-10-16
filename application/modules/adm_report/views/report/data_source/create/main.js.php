<?
// Generacion de tabs individuales para la creacion de data source
for ($tab_number = 1; $tab_number <= 3; $tab_number++):
	$this->load->view("report/data_source/create/tab_$tab_number.js.php");
endfor;

// Generacion del toolbar general de data source
$this->load->view("report/data_source/create/toolbar.js.php");
?>

//<script>

	// Creacion del componente contenedor de tabs de data source
	var tab_panel_data_source = new Ext.TabPanel({
		name: 'tab_panel_data_source',
		id: 'tab_panel_data_source',
		activeTab: 0,
		autoWidth: true,
		enableTabScroll: true,
		monitorResize: true,
		height: 530,
		items: [tab_panel_1, tab_panel_2],
		bbar: toolbar_data_source
	});

	// Generacion de la ventana para el contenedor de tabs de data source
	new Ext.Window({
        id: 'window_create_data_source',
        shadow: true,
        title: 'Crear Reporte',
        collapsible: true,
        maximizable: true,
		width: 650,
		height: 575,
        modal: true,
        plain: true,
        bodyStyle: 'padding:5px;',
		items: [tab_panel_data_source]
	}).show();

	// Observer para habilitar/deshabilitar el boton guardar/siguiente cuando se seleccione solo una tabla
	Ext.getCmp('itemselector_business_logic_2').store.on('add', enableDisableSaveButton);
	Ext.getCmp('itemselector_business_logic_2').store.on('remove', enableDisableSaveButton);

	//</script>