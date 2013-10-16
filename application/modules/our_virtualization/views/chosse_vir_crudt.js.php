
    var checkGroup = {
        xtype: 'fieldset',
        title: 'Entidades de Regla de negocios',
        autoHeight: true,
        layout: 'form',
        collapsed: false,  
        collapsible: true,
        items: [ <?=$bloque?> ]
    };
    
    
    var fp = new Ext.FormPanel({
        title: 'Virtualización y Generación de CRUD-T para entidades de regla de negocios',
        frame: true,
        labelWidth: 160,
        width: 650,
        bodyStyle: 'padding:0 10px 0;',
        items: [ checkGroup ],
        buttons: [{
            text: 'Crear Virtualizacion y CRUD-T',
            handler: function(){
               if(fp.getForm().isValid()){      
                    //Ext.getCmp('fp').getEl().mask();
                    fp.getForm().submit({
                        method: 'POST',
                        url: BASE_URL + 'our_virtualization/virtualization/create',
                        submitEmptyText:false,
                        success: function(fp, action){
                            //Ext.getCmp('fp').getEl().unmask();
                            var obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.show({
                                title: obj.response.title,
                                msg: obj.response.msg,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.INFO,
                                minWidth: 300
                            });
                        },
                        failure: function(fp, action){
                            //Ext.getCmp('fp').getEl().unmask();
                            var obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.show({
                                title: obj.response.title,
                                msg: obj.response.msg,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR,
                                minWidth: 300
                            });
                        }
                    });    
                }
            }
        },{
            text: 'Resetear',
            handler: function(){
                fp.getForm().reset();
            }
        }]
    });
