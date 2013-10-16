<?php
$this->load->view("template/panel_1.js.php");
$this->load->view("template/panel_2.js.php");
$this->load->view("report/data_source/create/toolbar.js.php");
?>

//<script>

	// Creacion del componente contenedor de tabs del template
	var tab_panel_data_source = new Ext.Panel({
		name: 'tab_panel_data_source',
		id: 'tab_panel_data_source',
        //layout: 'border',
		autoWidth: true,
		enableTabScroll: true,
		monitorResize: true,
		height: 530,
		items: [tab_panel, center],//, tab_panel_2],
		bbar: toolbar_data_source
	});

	// Generacion de la ventana para el contenedor del template
	new Ext.Window({
        id: 'window_create_data_source',
        shadow: true,
        title: 'Crear Plantilla',
        collapsible: true,
        maximizable: true,
		width: 650,
		height: 600,
        modal: true,
        plain: true,
        bodyStyle: 'padding:5px;',
		items: [tab_panel_data_source]
	}).show();

//</script>