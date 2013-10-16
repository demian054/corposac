<?php
/*
 * añadir las siguientes lineas en el archivo (/var/www/html/modules/agent_console/themes/default/js/javascript.js) de la consola de elastix
 * LINEA 750:
			//RIALFI
			$('#elastix-callcenter-cejillas-contenido').tabs( "option", "selected", 3);
			//RIALFI
 * esto con el objeto de que se enfoque automaticamente el formulario del SAC
 */
?>

<script type="text/javascript">

function newCallEntry(p) {
   //alert(p);
   //limpiamos el form
   var _form = Ext.getCmp('form_<?=$opId?>');
   _form.getForm().reset();
   Ext.getCmp('cont_form_<?=$opId?>').body.scrollTo('top',0);
   //alert(p.insertId);
   
   Ext.getCmp('id_<?=$opId?>_1975').setValue(p.insertId);
   Ext.getCmp('created_at_<?=$opId?>_1990').setValue(p.createAt);   
   Ext.getCmp('telefono_contacto_<?=$opId?>_1978').setValue(p.phone);
   Ext.getCmp('telefono_contacto_<?=$opId?>').setValue(p.phone);
   setContacto({_name:'', apellido:'', cedula_rif: ''});
}

function setContacto(p) {
    Ext.getCmp('personas_id_<?=$opId?>_1977').setValue(p.id);
    Ext.getCmp('cedula_rif_<?=$opId?>').setValue(p.cedula_rif);
    Ext.getCmp('cuenta_contrato_<?=$opId?>').setValue(p.cuenta_contrato);
    Ext.getCmp('nombre_cliente_<?=$opId?>').setValue(p.cedula_rif + '-' + p.apellido + ', ' + p._name);
    var _region = Ext.getCmp('combo_<?=$opId?>_1986');
    _region.setValue(p.category_region_id);
    
}

window.ecapture = {
    _form : form_<?=$opId?> ,
    searchClient: function () {
        //obteniendo datos:
        var _cedula = Ext.getCmp('cedula_rif_<?=$opId?>').getValue();
        var _cc = Ext.getCmp('cuenta_contrato_<?=$opId?>').getValue();
        
        if(_cedula == '' && _cc == '') {
            CU.showAlertError('Debe especificar datos para buscar.');
            return;
        }
        
        ecapture.contacStore.load({
            params: {cedula: _cedula, cc: _cc, start:0, limit: LONG_LIMIT},
            callback: function () {
                window.ecapture.contacWin.show();
            }
        });
        
        //
    },
    setContactGrid: function(p){
        var _store = window.ecapture.contacStore;
        setContacto(_store.getAt(_store.find('id',p)).data);
        window.ecapture.contacWin.hide();
    }
};

window.ecapture._form.width = 680;
window.ecapture._form.autoScroll = true;
window.ecapture._vp = new Ext.Viewport({
   id: 'view_port_main_layout',
   layout: 'anchor',
   //autoScroll: true,
   layoutConfig: {
        //align : 'stretch',
        pack  : 'start'
   },
   items: [
       {
           id: 'center_content',
           xtype: 'panel',
           width: 700,
           title: 'SAC - Datos del Contacto',
           bbar: new Ext.Toolbar({
                //buttonAlign:'center',
                items:[
                    {
                        text: 'Buscar Cliente',
                        icon: BASE_URL + '/assets/img/icons/zoom.png',
                        handler: window.ecapture.searchClient
                    },
                    {
                        text: 'Registrar Nuevo Cliente',
                        icon: BASE_URL + '/assets/img/icons/add.png',
                        handler: function() {
                            getCenterContent('blg_personas/personas/create');
                        }
                    }
                ]
           }),
           items: [
                {
                    xtype: 'form',
                    layout: 'column',
                    frame: true,
                    border:         false,
                    items: [
                        {
                            layout: 'form',
                            bodyStyle:      {paddingTop: '5px', paddingLeft: '5px' },
//                            style:          {margin: 'auto'},
//                            defaults:       {
//                                labelStyle: 'font-weight: bold;',
//                                style:      { margin: '0 0  5px 0', padding: '2px' }
//                            },
                            items: [
                                {
                                    id: 'cedula_rif_<?=$opId?>',
                                    xtype: 'textfield',
                                    //readOnly: true,
                                    fieldLabel: 'Cédula/Rif'
                                },
                                {
                                    id: 'cuenta_contrato_<?=$opId?>',
                                    xtype: 'textfield',
                                    //readOnly: true,
                                    fieldLabel: 'Cuenta Contrato'
                                }
                            ]
                        },{
                            layout: 'form',
                            bodyStyle:      {paddingTop: '5px', paddingLeft: '5px' },
//                            style:          {margin: 'auto'},
//                            defaults:       {
//                                labelStyle: 'font-weight: bold;',
//                                style:      { margin: '0 0  5px 0', padding: '2px' }
//                            },
                            items: [
                                {
                                    id: 'telefono_contacto_<?=$opId?>',
                                    xtype: 'textfield',
                                    readOnly: true,
                                    fieldLabel: 'Telf. Contacto',
                                    style: {
                                       color: 'red'
                                    },
                                    value: '<?=$datos['phone']?>'
                                },
                                {
                                    id: 'nombre_cliente_<?=$opId?>',
                                    xtype: 'textfield',
                                    readOnly: true,
                                    style: {
                                       color: 'red'
                                    },
                                    fieldLabel: 'Contacto'
                                }
                            ]
                        }
                    ]
                }
           ]
       },
       {
           id: 'cont_form_<?=$opId?>',
           autoScroll:true,
           width: 700,
           height: 290,
           layout: 'anchor',
           items: [
                window.ecapture._form
           ],
           bbar: bottomBar_<?=$opId?>
       },
       {
            xtype: 'panel',
            hidden: true,
            html: '<iframe name="ext_frm" id="ext_frm" width="200" height="50"></iframe>'
       }
   ]
   //renderTo: 	Ext.getBody()
});


window.ecapture.contacStore = new Ext.data.JsonStore({
    totalProperty: 'totalRows',
    root: 'rowset',
    fields: [{name: '_name'}, {name: 'id'}, {name: 'cedula_rif'}, {name: '_name'},, {name: 'apellido'}, {name: 'category_region_id'}, {name: 'cuenta_contrato'}],
    id: 'contacStore<?=$opId?>',
    //data: {},
    proxy: new Ext.data.HttpProxy({
        url: BASE_URL + 'blg_personas/personas/listAll',
        method: 'POST',
        baseParams: { start: 0, limit: LONG_LIMIT }
    }),
    listeners: {
        load: function(){
            var aux= window.ecapture.contacStore.baseParams;
            Ext.apply(aux,window.ecapture.contacStore.lastOptions.params);
            if (window.ecapture.paginBar.store != null)
                Ext.apply(window.ecapture.paginBar.store.baseParams, aux);
        },
        reload: function() {
            window.ecapture.paginBar.store.baseParams= window.ecapture.contacStore.contacStore.baseParams;
        }
    }
});

window.ecapture.paginBar = new Ext.PagingToolbar({
    pageSize:   LONG_LIMIT,
    store:      window.ecapture.contacStore,
    displayInfo: true,
    displayMsg: 'Mostrando Registros {0} - {1} de un total de {2}.',
    emptyMsg: 'No hay registros que mostrar.'
});

window.ecapture.paginBar.store.baseParams= window.ecapture.contacStore.baseParams;

ecapture.contacGrid = new Ext.grid.GridPanel({
    id:         '<?=$opId?>',
    height:     '100%',
    layout:     'anchor',
    frame:      false,
    border:     true,
    stripeRows: true,
    autoScroll: true,
    colModel: new Ext.grid.ColumnModel({
        defaults: {
            width: 120
        },
        columns:[
            {
                width:30,
                align:'center',
                fixed:true,
                hideable:false,
                menuDisabled:true,
                dataIndex:'id',
                renderer: function(value, metadata, record, rowIndex, colIndex, store){
                    var button = '<div style="cursor: pointer">\n\
                        <img title="Editar Personas" src="' + BASE_URL + '/assets/img/icons/pencil.png" onclick="window.ecapture.setContactGrid(\'{0}\', event)" />\n\
                    </div>';
                    return String.format(button, value);
                }
            },
            {menuDisabled:true, dataIndex: 'cedula_rif', header: 'Cédula/RIF'}
        ]
    }),
    store:      ecapture.contacStore,
    loadMask:   false,
    //title:      'Listado de Personas',
    style:      'margin:0 auto;',
    bbar: ecapture.paginBar
    }
);

window.ecapture.contacWin = new Ext.Window({
   id: 'contactoWin_<?=$opId?>',
    shadow: true,
    title: 'Seleccione a un Contacto',
    collapsible: true,
    maximizable: true,
    //width: 667,
    width: 600,
    //height: 500,
    height: 280,
    minWidth: 300,
    minHeight: 200,
    layout: 'fit',
    modal:true,
    autoScroll: true,
    overflow:'auto',
    plain: true,
    bodyStyle: 'padding:5px;',
    buttonAlign: 'center',
    items: ecapture.contacGrid,
    autoDestroy: false,
    listeners: {
        beforeClose: function(e){
            this.hide();
            return false;
        }
    }
});



//foco
var _focus = Ext.getCmp('cedula_rif_<?=$opId?>');
_focus.focus(false,600);
//var _focus = Ext.getCmp('bottomBar_<?=$opId?>');
//_focus.width = 700;
//_focus.doLayout();


//compatibilidad ouroboros:
var layout_main = window.ecapture._vp;
CENTER_CONTENT=Ext.getCmp('center_content');
//window.ecapture.show();
</script>
