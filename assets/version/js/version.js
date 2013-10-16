    function version(){

        var tabs2 = new Ext.TabPanel({
            renderTo: document.body,
            activeTab: 0,
            width:600,
            height:250,
            plain:true,
            defaults:{autoScroll: true},
            tabPosition: 'bottom',
            items:  [{
                        name: 'detalleproducto',                    
                        id: 'detalleproducto',
                        title: 'Detalles del Producto',
                        listeners:{ 
                            activate: function(p){ 
                                p.load({ 
                                    url:'assets/version/product_detail_view.html',
                                    method: 'GET',
                                    scripts:true 
                                }); 
                            } 
                        },
                        iconCls:''
                    },{
                        title: 'Registro de Cambios',
                        name: 'detalleversion',                    
                        id: 'detalleversion',
                        listeners:{ 
                            activate: function(p){ 
                                p.load({ 
                                    url:'assets/version/changelog_view.html',
                                    method: 'GET',
                                    scripts:true 
                                }); 
                            } 
                        },
                        iconCls:''
                    }]              
        });

        var w_changeLog = new Ext.Window({
                id: 'w_changeLog',
                shadow: true,
                layout: 'fit',
                title: 'Versi&oacuten de Software',
                collapsible: true,
                maximizable: true,
                width: 340,
                height: 450,
                modal:true,
                autoScroll: true,
                overflow:'auto',
                plain: true,
    //            bodyStyle: 'padding:5px;',
                closeAction:'destroy',
                items: tabs2
        });

        w_changeLog.show();
    }

    function mifecha(val){
        var fecha = val;
        var sepFecha = fecha.split('-');
        var mifecha = sepFecha[2]+'-'+sepFecha[1]+'-'+sepFecha[0];
        return mifecha;
    }
