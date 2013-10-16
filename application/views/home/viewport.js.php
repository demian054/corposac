<script type="text/javascript">

	/**
	 * Contenedor principal del sistema. El componente posee cuatro regiones
	 * north  -> Region donde se carga el elemento header
	 * west   -> Region donde se carga el menu principal de operaciones
	 * center -> Region central donde se interactua con las operaciones del sistema
	 * south  -> Region donde se coloca el elemento footer
	 * */
	var layout_main = new Ext.Viewport({
		id: 'view_port_main_layout',
		layout: 	'border',
		renderTo: 	Ext.getBody(),
		items: [
			// Definicion region north
			{region: 	'north',
				height: 	90,
				border: 	true,
				html: 		'<?= $this->load->view('home/header.js.php') ?>'
			},
			// Definicion region west
			{region: 	'west',
				xtype: 	'panel',
				layout:	'fit',
				width: 	200,
				border: 	true,
				margins:	'0 0 0 5',
				collapseMode: 	'mini',
				split: 	true,
				items:		[west_menu]
			},
			// Definicion region center
			{xtype:'panel',
				region:'center',
				id:'center_card',
				width:'100%',
				height:'690px',
				layout:'card',
				border:true,
				activeItem:1,
				border:false,
				defaults: {border:false},
				items:[
					{id:'center_content',
						layout:'fit',
						width:'100%',
						height:'690px'
					},
				<? if (!empty($widgets_on)): ?>
					<?= WIDGETS_PORTAL ?>
				<? endif; ?>
								]
							},
							// Definicion region south
							{region:'south',
								autoHeight: true,
								autoHeight: true,
								border: 	 true,
								hidden:true,
								html: 		 '<?= $this->load->view('home/footer.js.php') ?>',
								margins: 	 '0 0 5 0'
							}
						]
					});
					CENTER_CONTENT=Ext.getCmp('center_content');
					layout_main.show();
</script>