    var tree = new Ext.ux.tree.CheckTreePanel({
        title: 'Arbol de Operaciones',
		//isFormField:true,
		id: 'tree',
		name: 'tree',
		height: 300,
		width: 400,
		useArrows:true,
		expandOnCheck: true,
		autoScroll:true,
		animate:true,
		//enableDD:true,
		containerScroll: true,
		rootVisible: false,
		//frame: true,
		root: <?=$tree?>,
		cascadeCheck: 'all',
		bubbleCheck: 'all'
    });
    
    var formRoleTree = new Ext.form.FormPanel({ 
		id: 'formRoleTree',
		name: 'formRoleTree',
		items: [tree]
    });

    // Ventana donde se cargan los FieldSet
    var windowRoleTree = new Ext.Window({

        id: 'windowRoleTree',
        shadow: true,
        title: 'Administrar Operaciones Asociadas al Rol',
        collapsible: true,
        maximizable: true,
        minWidth: 400,
		width: 430,
        minHeight: 600,
        layout: 'auto',
        modal:true,
        autoScroll: true,
        overflow:'auto',
        plain: true,
        bodyStyle: 'padding:5px;',
        buttonAlign: 'center',
        items: [formRoleTree],
        buttons: [new Ext.Button({ 
        
						id: 'button_aceptar',
						text: 'Guardar',
						icon: '<?=base_url()?>'+'assets/img/icons/save.gif',
						type: 'submit',
						standardSubmit:true,
						handler: function() {
				    				    
					    showMask();				    
		  
					    Ext.Ajax.request({
					       url: '<?=base_url()?>'+'adm_user_role/role/listOperation/process',
					       method: 'POST',
					       params: {tree: Ext.encode(Ext.getCmp('tree').getValue())},
					       success: function(response){

						    hideMask();
						    var icon = Ext.MessageBox.ERROR;
						    var obj = Ext.util.JSON.decode(response.responseText);

						    // Si el proceso de logica de negocio es exitoso
						    if (obj.response.result) {
							icon = Ext.MessageBox.INFO;
							windowRoleTree.close();
						    }

						    Ext.Msg.show({   
							    title: obj.response.title,
							    msg: obj.response.msg,
							    buttons: Ext.Msg.OK,
							    icon: icon,
							    minWidth: 300
						    });
					       },
					       failure: function(){

						    hideMask();
						    Ext.Msg.show({   
							    title: 'Error',
							    msg: 'Error en la Peticion al Servidor',
							    buttons: Ext.Msg.OK,
							    icon: Ext.MessageBox.ERROR,
							    minWidth: 300
						    });
					       }
					    })
				    }
                                })
				
                  //,new Ext.Button({	id: 'button_limpiar',
				  //				text: 'Limpiar',
				  //				icon: ' base_url() '+'assets/img/icons/broom-minus-icon.png',
                  //                handler: function() { 
				  //					tree.root.reload();
				  //					tree.root.collapse(true, false);
                  //                }
                  //             })
				]
    });
    
    windowRoleTree.show();
    tree.getRootNode().expand(false);
    
    // Creacion de la mascara
    mask = new Ext.LoadMask(Ext.getCmp('windowRoleTree').body, { msg: 'Cargando' });
    
    // Funcion para mostrar la mascara
    function showMask() {
    
        //mask.show();
        
        Ext.each(windowRoleTree.buttons, function(button) {
            button.disable();
        });
    }
    
    // Funcion para ocultar la mascara
    function hideMask() {
    
        //mask.hide();
        
        Ext.each(windowRoleTree.buttons, function(button) {
            button.enable();
        });
    }