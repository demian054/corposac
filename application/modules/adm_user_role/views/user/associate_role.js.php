var noAsocStore = new Ext.data.JsonStore({
		url: BASE_URL+'adm_user_role/user/CL_getAssociatedRoles',
		method:'GET',
		autoLoad: true ,
		autoDestroy:true,
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			url: BASE_URL+'adm_user_role/user/CL_getAssociatedRoles'
		}),
		fields: ['value', 'label']
 });
 
 var asocStore = new Ext.data.JsonStore({
		url: BASE_URL+'adm_user_role/user/CL_getAssociatedRoles/true',
		method:'GET',
		autoLoad: true ,
		autoDestroy:true,
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			url: BASE_URL+'adm_user_role/user/CL_getAssociatedRoles/true'
		}),
		fields: ['value', 'label']
 });	
	
var selectorRoles={
		xtype: 'itemselector',
		id:             'selectorRoles',
		name:           'selectorRoles',
		fieldLabel:     'Roles',
		hideLabel: true,
		imagePath: BASE_ICONS,
		disabled:false,
		allowBlank:false,
		multiselects: [
			{width: 300,
			 height: 350,
			 id:'multi1',
			 legend:'No Asociados',
			 store:noAsocStore,
			 displayField:   'label',
			 valueField:     'value',
			},
			{width: 300,
			 height: 350,
			 id:'multi2',
			 legend:'Asociados',
			 displayField:   'label',
			 valueField:     'value',
			 store: asocStore
			}
		]
	}
	
	
var formAssociateRole = new Ext.form.FormPanel({ 

    id: 'formAssociateRole',
    name: 'formAssociateRole',
    items: [selectorRoles]

});


    // Ventana donde se cargan los FieldSet
    var windowAssociateRole = new Ext.Window({

        id: 'windowAssociateRole',
        shadow: true,
        title: 'Administrar Roles',
        collapsible: true,
        maximizable: true,
        minWidth: 300,
		width:650,
        minHeight: 200,
        layout: 'auto',
        modal:true,
        autoScroll: true,
        overflow:'auto',
        plain: true,
        bodyStyle: 'padding:5px;',
        buttonAlign: 'center',
        items: [formAssociateRole],
        buttons: [new Ext.Button({ 
        
                                    id: 'button_aceptar',
                                    text: 'Guardar',
                                    icon: '<?=base_url()?>'+'/assets/img/icons/save.gif',
                                    type: 'submit',
                                    standardSubmit:true,
                                    handler: function() {
				    
				          showMask();	
				    
					  formAssociateRole.getForm().submit({

						url: 'adm_user_role/user/listRoles/process',
						success: function(formAssociateRole, action) {

						    hideMask();
						    var icon = Ext.MessageBox.ERROR;
						    var obj = Ext.util.JSON.decode(action.response.responseText);

						    // Si el proceso de logica de negocio es exitoso
						    if (obj.response.result) {
							icon = Ext.MessageBox.INFO;
							windowAssociateRole.close();
						    }

						    Ext.Msg.show({   
							    title: obj.response.title,
							    msg: obj.response.msg,
							    buttons: Ext.Msg.OK,
							    icon: icon,
							    minWidth: 300
						    });
						},
						failure: function(formAssociateRole, action) {
						    hideMask();
						    Ext.Msg.show({   
							    title: 'Error',
							    msg: 'Error en la Peticion al Servidor',
							    buttons: Ext.Msg.OK,
							    icon: Ext.MessageBox.ERROR,
							    minWidth: 300
						    });
						}
					    });
				    }
                                }),
                  new Ext.Button({ 
        
                                    id: 'button_limpiar',
                                    text: 'Limpiar',
				    icon: '<?=base_url()?>'+'/assets/img/icons/broom-minus-icon.png',
                                    handler: function() { 
					Ext.getCmp('multi1').store.reload();
					Ext.getCmp('multi2').store.reload();                                        
                                    }
                                })]
    });
    
    windowAssociateRole.show();
    
    
    // Creacion de la mascara
    mask = new Ext.LoadMask(Ext.getCmp('windowAssociateRole').body, { msg: 'Cargando' });
    
    // Funcion para mostrar la mascara
    function showMask() {
    
        //mask.show();
        
        Ext.each(windowAssociateRole.buttons, function(button) {
            button.disable();
        });
    }
    
    // Funcion para ocultar la mascara
    function hideMask() {
    
        //mask.hide();
        
        Ext.each(windowAssociateRole.buttons, function(button) {
            button.enable();
        });
    }
    
    