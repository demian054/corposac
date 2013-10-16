
Ext.QuickTips.init();
var msg = function(title, msg){
        Ext.Msg.show({
            title: title,
            msg: msg,
            minWidth: 200,
            modal: true,
            icon: Ext.Msg.INFO,
            buttons: Ext.Msg.OK
        });
    };  

var store_<?=$opId.$fieldId?> = new Ext.data.Store({
            reader: new Ext.data.JsonReader({
                root:'images', 
                totalProperty: 'result'
                }, 
                [ 'name', 'thumb_url', 'id' ]
            ),
            baseParams:{gallerie_id:'<?=$gallerie_id?>'},
			
            proxy: new Ext.data.HttpProxy({
                url: BASE_URL + 'adm_upload/upload/listAll/',
                method: 'GET'
            })

        });

var pagingbar_<?=$opId.$fieldId?> = new Ext.PagingToolbar({
                style:          'border:1px solid #99BBE8;',
                store:          store_<?=$opId.$fieldId?>,
                pageSize:       THUMB_LIMIT,
                displayInfo:    true
	});
        
store_<?=$opId.$fieldId?>.load({params:{ gallerie_id:'<?=$gallerie_id?>'}});

var tpl = new Ext.XTemplate(
	'<tpl for=".">',
		'<div class="thumb-wrap" id="{name}">',
		'<div class="thumb"><img src="{thumb_url}" title="{name}"></a></div>',
		<?php if($fileType == 'upload'){ ?>
			'<span class="x-editable"><img id="{id}" name="det" src="<?=base_url()?>assets/img/icons/zoom.png" title="Detalle"> <img id="{id}" name="edi" src="<?=base_url()?>assets/img/icons/pencil.png" title="Editar"> <img id="{id}" name="eli" src="<?=base_url()?>assets/img/icons/cancel.png" title="Eliminar"> </span></div>',
		<?php } else { ?>
			'<span class="x-editable"><img id="{id}" name="det" src="<?=base_url()?>assets/img/icons/zoom.png" title="Detalle"></span></div>',
		<?php } ?>
	'</tpl>',
	'<div class="x-clear"></div>'
);
         

var datav_<?=$opId.$fieldId?> = new Ext.DataView({
            autoScroll:     true, 
            store:          store_<?=$opId.$fieldId?>, 
            tpl:            tpl,
            id:             'datav_<?=$opId.$fieldId?>',
            autoHeight:     false, 
            height:         145, 
            multiSelect:    true,
            overClass:      'x-view-over', 
            itemSelector:   'div.thumb-wrap',
            emptyText:      'No hay imágenes que mostrar',
            style:          'border:1px solid #99BBE8; border-top-width: 0;',
            listeners:{
				click: function(dataView, index, node, e ){
					var target = e.getTarget();
					if(target.name == "det"){
                                                detalleArchivo(target.id);
					} 
                                        if(target.name == "edi"){
                                                editarArchivo(target.id);
                                        } 
                                        if(target.name == "eli"){
                                                Ext.MessageBox.buttonText.yes = "Si";
                                                Ext.Msg.show({
                                                        title: 'Confirmar Eliminar',
                                                        msg: 'Esta seguro de Eliminar este archivo',
                                                        buttons: Ext.Msg.YESNO,
                                                        fn: function(btn){
                                                                if(btn=='yes') eliminarArchivo(target.id);
                                                        },
                                                        minWidth: 300,
                                                        icon: Ext.MessageBox.QUESTION
                                                });
                                        }
				}
			}
        })        

        
 function detalleArchivo(id){
   Ext.Ajax.request({
                    url: BASE_URL + 'adm_upload/upload/fileDetail',
                    method: 'POST',
                    params: {id:id},
                    success: function(action, request) {
                    eval(action.responseText);
                    },
                    failure: function(action, request) {
                    var obj = Ext.util.JSON.decode(action.responseText);
                               Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'Ha ocurrido un error en la conexi&oacute;n con el servidor',
                                            minWidth: 200,
                                            modal: true,
                                            icon: Ext.Msg.INFO,
                                            buttons: Ext.Msg.OK
                                        });         
                    } 
                });
}
 
function editarArchivo(id){
    Ext.Ajax.request({
                    url: BASE_URL + 'adm_upload/upload/fileEdit',
                    method: 'POST',
                    params: {id:id, gallerie_id:'<?=$gallerie_id?>'},
                    success: function(action, request) {
                    eval(action.responseText);
                    },
                    failure: function(action, request) {
                    var obj = Ext.util.JSON.decode(action.responseText);
                               Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'Ha ocurrido un error en la conexi&oacute;n con el servidor',
                                            minWidth: 200,
                                            modal: true,
                                            icon: Ext.Msg.INFO,
                                            buttons: Ext.Msg.OK
                                        });         
                    } 
                });
}

function eliminarArchivo(id){
	
	var galId = Ext.getCmp('gallerie_id_<?=$fieldId?>').getValue();
    Ext.Ajax.request({
                    url: BASE_URL + 'adm_upload/upload/deleteFile',
                    method: 'POST',
                    params: {id:id, gallerie_id:galId},
                    success: function(action, request) {
                    var obj = Ext.util.JSON.decode(action.responseText);
                            Ext.Msg.show({
                                            title: obj.title,
                                            msg: obj.msg,
                                            minWidth: 200,
                                            modal: true,
                                            icon: Ext.Msg.INFO,
                                            buttons: Ext.Msg.OK
                                        });                    
                    Ext.getCmp('datav_<?=$opId.$fieldId?>').store.removeAll();                    
                    store_<?=$opId.$fieldId?>.load({params:{gallerie_id: obj.gallerie_id }});
                    },
             failure: function(action, request) {
             var obj = Ext.util.JSON.decode(action.responseText);
                           Ext.Msg.show({
                                            title: 'Error',
                                            msg: 'Ha ocurrido un error en la conexi&oacute;n con el servidor',
                                            minWidth: 200,
                                            modal: true,
                                            icon: Ext.Msg.INFO,
                                            buttons: Ext.Msg.OK
                                        });  
                    } 
                });
}
        
var pFile_<?=$opId.$fieldId?> = new Ext.Panel({
                id:         'images-view',
                frame:      true,
                height:     200,
                autoHeight: true,
                layout:     'auto',
		title:      'Listado de Archivos',
                items:      [pagingbar_<?=$opId.$fieldId?>,datav_<?=$opId.$fieldId?>]
	}); 


var fUpload_<?=$opId.$fieldId?> = new Ext.FormPanel({
            id: 'fUpload_<?=$opId.$fieldId?>',
            fileUpload: true,
            width: '100%',
            frame: true,
            title: 'Formulario de subir archivos',
            autoHeight: true,
            bodyStyle: 'padding: 10px 10px 0 10px;',
            labelWidth: 50,
            defaults: {
                anchor: '90%', 
                allowBlank: false,
                msgTarget: 'side'
            },
            items: [{
                xtype:'hidden', 
		id:'gallerie_id_<?=$fieldId?>',
                name:'gallerie_id',
                value:'<?=$gallerie_id?>'
            },  {
                xtype:'hidden', 
		id:'operationId_<?=$fieldId?>',
                name:'opId',
                value:'<?=$opId?>'
            },  {
                xtype:'hidden', 
		id:'file_field_id_<?=$fieldId?>',
                name:'file_field_id',
		value:'<?=$fieldId?>'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Titulo',
                id: 'title_file_<?=$fieldId?>',
                name: 'title',
                allowBlank: false,
                blankText:  'El campo Titulo es obligatorio',
                emptyText:  'Titulo del Archivo',
                listeners: {
                            render: function(c) {                                      
                                    new Ext.ToolTip({
                                        target: c.getEl(),
                                        anchor: 'left',
                                        trackMouse: true,
                                        html: 'El campo Titulo es obligatorio, solo debe contener caracteres alfanumericos y espacios'
                                    });
                                },
                              },
                vtype:'valid_alpha_numeric_space'
            },{
                xtype: 'fileuploadfield',
                id: 'userfile_<?=$fieldId?>',
                emptyText: 'Select an image',
                fieldLabel: 'Archivo',
                name: 'userfile',
                allowBlank: false,
                blankText:  'El campo Archivo es obligatorio',
                emptyText:  'Archivo a subir',
                listeners: {
                            render: function(c) {                                      
                                    new Ext.ToolTip({
                                        target: c.getEl(),
                                        anchor: 'left',
                                        trackMouse: true,
                                        html: 'El campo Archivo es obligatorio, solo debe contener caracteres alfanumericos, las extensiones de archivo permitida son .jpg, .png, ,gif, .doc, .odt, .xls, .ods, .pdf, .txt, .rtf.'
                                    });
                                },
                              },
                vtype:      '',	
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload-icon'
                }
            }
        ],
            buttons: [{
                text: 'Subir Archivo',
                icon: BASE_ICONS + 'arrow_up.png',
                handler: function(){
                    if(fUpload_<?=$opId.$fieldId?>.getForm().isValid()){
                        fUpload_<?=$opId.$fieldId?>.getForm().submit({
                            url:  BASE_URL + 'adm_upload/upload/do_upload/process',
                            method: 'POST',
                            waitMsg: 'Subiendo Archivo ...',
                                success: function(fUpload_<?=$opId.$fieldId?>, action){                                
                                    var obj = Ext.util.JSON.decode(action.response.responseText);
                                    Ext.Msg.show({   
                                                    title: obj.response.title,
                                                    msg: obj.response.msg,
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.INFO,
                                                    minWidth: 300
                                                    });                                                    
                                    Ext.getCmp('<?=$dom_gallerie_id?>').setValue(obj.response.extra_vars.gallerie_id);
                                    Ext.getCmp('gallerie_id_<?=$fieldId?>').setValue(obj.response.extra_vars.gallerie_id);
                                    store_<?=$opId.$fieldId?>.load({params:{gallerie_id: obj.response.extra_vars.gallerie_id }});
                                    store_<?=$opId.$fieldId?>.setBaseParam('gallerie_id', obj.response.extra_vars.gallerie_id);
                                },
                                failure: function(fUpload_<?=$opId.$fieldId?>, action){                                
                                    var obj = Ext.util.JSON.decode(action.response.responseText);
                                    Ext.Msg.show({
                                                    title: 'Error!',
                                                    msg: 'Error en la Peticion al Servidor',
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.ERROR,
                                                    minWidth: 300
                                                    });					
                                }
                        });
                    }
                }
            },{
                text: 'Limpiar',
                icon: BASE_ICONS + 'broom-minus-icon.png',
                handler: function(){
                    fUpload_<?=$opId.$fieldId?>.getForm().reset();
                }
            }]
    });

  
  var pUpload_<?=$opId.$fieldId?> = new Ext.Panel({
                layout: 'border',
                id:     'pUpload_<?=$opId.$fieldId?>',
                frame:      true,
                height: '100%',
		title:  '',
		items: [{
                            region: 'north',
                            xtype: 'panel',
                            layout: 'fit',
                            autoHeight:true,
                            border: false,
                            margins: '0 0 5 0',
                            items: [pFile_<?=$opId.$fieldId?>]
                        },{            
                           region: 'center',
                            xtype: 'panel',
                            border: false,
                            layout: 'fit',
                            //autoHeight:true,
                            margins: '0 0 5 0',
                            items: [<?php echo(($fileType == 'upload')?"fUpload_$opId$fieldId":"")?> ]
                        }]
	});
  
var wUpload_<?=$opId.$fieldId?> = new Ext.Window({
                id: 'wUpload_<?=$opId.$fieldId?>',
                shadow: true,
                title: 'Ventana de Archivos',
                collapsible: true,
                maximizable: true,
                width: 430,
                height: <?php echo(($fileType == 'upload')?395:265)?>,
                layout: 'fit',
                modal:true,
                autoScroll: true,
                overflow:'auto',
                plain: true,
                bodyStyle: 'padding:3px;',
                buttonAlign: 'center',
                closeAction:'destroy',
				frame:true,
                items: pUpload_<?=$opId.$fieldId?>,
                listeners:{
                    beforehide:function(){
                        Ext.getCmp('file_field_id_<?=$fieldId?>').setValue('');
                    },
                    beforclose:function(){
                        Ext.getCmp('file_field_id_<?=$fieldId?>').setValue('');
                    }
                }
        });

        wUpload_<?=$opId.$fieldId?>.show();
        
        