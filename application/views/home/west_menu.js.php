<script type="text/javascript">

	// Variable que contiene las operaciones en formato JSON
    var menu_data=<?= $menu_data ?>
	/**
	 * Panel tipo acordeon que contiene las operaciones del sistema
	 * */
	var west_menu = new Ext.Panel({
		title: 'Modulos',
		layout:'accordion',
		defaults: {
			autoHeight: true
		},
		layoutConfig: {
			titleCollapse: true,
			animate: true,
			activeOnTop: false
		}
	});

	/**
	 * <b>Function: buildWestMenu()</b>
	 * @description Permite cargar en el menu las operaciones y sub-operaciones del sistema
	 * @params		west_menu panel donde se cargan las operaciones
	 * @author	    Jesus Farias <jfarias@rialfi.com>
	 * @version     V-1.0 07/09/12 03:06 PM
	 * */
	function buildWestMenu(west_menu) {

		var accordion = accordion_item = new Array();

		// Ciclo para agregar las operaciones y sub-operaciones al menu
		for (var index=0 ; index<menu_data.length; index++) {
			var menu = menu_item = new Array();
			for(index_2 in menu_data[index]['menu']){
				var submenu = submenu_item = new Array();
				if(menu_data[index]['menu'][index_2]['submenu']!=undefined){
					for(index_3 in menu_data[index]['menu'][index_2]['submenu']){
						submenu_item= {
							id: menu_data[index]['menu'][index_2]['submenu'][index_3]['id'],
							text: menu_data[index]['menu'][index_2]['submenu'][index_3]['_name'],
							icon: menu_data[index]['menu'][index_2]['submenu'][index_3]['icon'],
							url: menu_data[index]['menu'][index_2]['submenu'][index_3]['url']
						};
						submenu_item.handler= function(submenu_item){getCenterContent(arguments[0]);}
						submenu.push(submenu_item);
					}
				}
				menu_item={
					id:   		menu_data[index]['menu'][index_2]['id'],
					text: 		menu_data[index]['menu'][index_2]['_name'],
					icon: 		menu_data[index]['menu'][index_2]['icon'],
					url: 		menu_data[index]['menu'][index_2]['url'],
					split:		false,
					iconAlign: 	'left',
					showSeparator:false,
					height: 	28,
					minWidth:	150,
					width:		'100%',
					style: {textAlign:'left'},
					menuAlign:	'br',
					menu:		false,
					handler:	false
				}
				if(submenu.length)menu_item.menu=submenu;
				else menu_item.handler= function(menu_item){getCenterContent(arguments[0]);};
				menu.push(menu_item);
			}

			// Elemento tipo acordeon que se agrega al menu
			accordion_item={
				id:menu_data[index]['id'],
				title:menu_data[index]['_name'],
				icon:menu_data[index]['icon'],
				tbar: new Ext.Toolbar({
					autoWidth: true,
					layout:	'vbox',
					border: false,
					items: 	[menu],
					height: (28*menu.length)+menu.length+4
				})
			}

			// Agregar el acordeon al contenedor
			accordion.push(accordion_item);
		}
		west_menu.removeAll();
		west_menu.add(accordion);
		west_menu.doLayout();
	};

	// Si la variable menu_data esta definida se genera el menu de operaciones
	if(menu_data!=undefined && menu_data!=false && menu_data!=null && menu_data!="") buildWestMenu(west_menu);

</script>