var noAsocStore = new Ext.data.JsonStore({
		url: BASE_URL+'combo_loader/getRolesNoAsocWidget',
		method:'GET',
		autoLoad: true ,
		autoDestroy:true,
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			url: BASE_URL+'combo_loader/getRolesNoAsocWidget'
		}),
		fields: ['value', 'label']
 });
 
 var asocStore = new Ext.data.JsonStore({
		url: BASE_URL+'combo_loader/getRolesAsocWidget',
		method:'GET',
		autoLoad: true ,
		autoDestroy:true,
		proxy : new Ext.data.HttpProxy({
			method: 'GET',
			url: BASE_URL+'combo_loader/getRolesAsocWidget'
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
			 legend:'Roles No Asociados',
			 store:noAsocStore,
			 displayField:   'label',
			 valueField:     'value',
			},
			{width: 300,
			 height: 350,
			 id:'multi2',
			 legend:'Roles Asociados',
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


var upperPanel = new Ext.Panel({ 
	layout:'fit',
	id:'upper_panel',
	name:'upper_panel',
	title:'Widget',
	html:['<table width="100%" cellspacing="2" border="0" cellpadding="2" align="left"><tr><td width="50%"><b>Nombre:             </b><?= $nombre; ?><br /><b>Descripción:        </b><?= $descripcion; ?><br /><b>Roles Aplicables:   </b><?= $chk_role_type_widget; ?><br /><b> </b><br /></td></tr> </table>'],
	frame:false
});


    // Ventana donde se cargan los FieldSet
    var windowAssociateRole = new Ext.Window({

        id: 'windowAssociateRole',
        shadow: true,
        title: 'Asociar Roles a Widgets',
        collapsible: true,
        maximizable: true,
        minWidth: 300,
		width:700,
        minHeight: 200,
        layout: 'auto',
        modal:true,
        autoScroll: true,
        overflow:'auto',
        plain: true,
        bodyStyle: 'padding:5px;',
        buttonAlign: 'center',
        items: [upperPanel,formAssociateRole],
        buttons: [new Ext.Button({ 
        
                                    id: 'button_aceptar',
                                    text: 'Guardar',
                                    icon: '<?=base_url()?>'+'/assets/img/icons/save.gif',
                                    type: 'submit',
                                    standardSubmit:true,
                                    handler: function() {
				    
				          showMask();	
				    
					  formAssociateRole.getForm().submit({

						url: 'user/listRolesWidgets/process',
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
    
        mask.show();
        
        Ext.each(windowAssociateRole.buttons, function(button) {
            button.disable();
        });
    }
    
    // Funcion para ocultar la mascara
    function hideMask() {
    
        mask.hide();
        
        Ext.each(windowAssociateRole.buttons, function(button) {
            button.enable();
        });
    }
    
    