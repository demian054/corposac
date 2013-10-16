<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php if(!empty($scriptTags)):?>
    <script type="text/javascript">
<?php endif; ?>

  var estado_list_<?=$opId?>         = <?=json_encode($estado_list);?>;
  var region_list_<?=$opId?>     = <?=json_encode($region_list);?>;
  
  //alert(Ext.encode(region_list));
  
var estado_store_<?=$opId?> = new Ext.data.JsonStore({
                autoLoad : true,
                autoDestroy : true,
                fields:   ['value','label'],
                data: estado_list_<?=$opId?>
                });
                
var tipo_reclamo_store_<?=$opId?> = new Ext.data.JsonStore({
                autoLoad : true,
                autoDestroy : true,
                fields:   ['value','label'],
                data: <?=json_encode($this->model_class->getCategoryList('tipo_reclamo'));?>
                });
                
var subtipo_reclamo_store_<?=$opId?> = new Ext.data.JsonStore({
                autoLoad : false,
                autoDestroy : true,
                fields:   ['value','label'],
                //data: [],
                proxy : new Ext.data.HttpProxy({
                    method: 'GET',
                    url: BASE_URL + 'blg_incidencias/incidencias/CL_getSubtipoReclamo'
                }),
                listeners:{
                    load: function(){
                        try{
                            var combo=Ext.getCmp('category_subtipo_reclamo_id');
                            //combo.show();
                            if (combo != null)
                                combo.enable();
                        }catch(e){
                            //do nothing
                        }
                    }
                }
});
                
var region_store_<?=$opId?> = new Ext.data.JsonStore({
                autoLoad : true,
                autoDestroy : true,
                fields:   ['value','label'],
                data: region_list_<?=$opId?>
                });
            //"label":"No asignar","value":"0"
  //console.log(estado_store_<?=$opId?>);      
  var search_acction_<?=$opId?> = new Ext.Action({
                text: 'Buscar',
                handler: function(){
                    var form = Ext.getCmp("form_filtro_<?=$opId?>").getForm();
                    var store = Ext.getCmp('Grid_<?=$opId?>').store;
                    if (form.isValid()){
                        var search_data         = Ext.encode(form.getValues());
                        //store.baseParams = null;
                        store.load(
                            {
                                params: {'search_data':search_data, start:0, limit: null}
                            });
                    } else {
                        window.CU.showAlertError('Existen errores en el formulario, coriijalos para filtrar.');
                    }

                },
                icon: BASE_URL+'/assets/img/icons/find.png'
            });
            
    var clear_acction_<?=$opId?> = new Ext.Action({
             text: 'Limpiar',
             handler: function(){
                var form = Ext.getCmp("form_filtro_<?=$opId?>").getForm();
                var store = Ext.getCmp('Grid_<?=$opId?>').store;
                form.reset();
                var search_data  = Ext.encode(form.getValues());
                //store.baseParams = null;
                store.load(
                    {
                        params: {'search_data':search_data, start:0, limit: null}
                    });
                 

             },
             icon: BASE_URL+'/assets/img/icons/cancel.png'
         });

var form_filtro_<?=$opId?> = new Ext.FormPanel({
    //itle: 'Busqueda',
    id: 'form_filtro_<?=$opId?>',
    labelWidth:     120,
    buttonAlign:    'center',
    frame:          true,
//    tbar:           topBar_530,
//    bbar:           bottomBar_530,
 //   autoScroll:     true,
    layout:'hbox',
    ///width: 		'100%',
    autoHeigth: 	true,
    
    items: [
        {
        //width: 350,
        layout:'form',
        defaults: {
             // applied to each contained item
             width: 150,
             msgTarget: 'side'
        },
        items: [
             {
               xtype:'combo',
               store: estado_store_<?=$opId?>,
               id : 'category_status_incidencia_id',
               name : 'category_status_incidencia_id',
               hiddenName: 'category_status_incidencia_id',
               fieldLabel:'Estado Incidencia',
               displayField : 'label',
               valueField : 'value',
               triggerAction: 'all',
               lazyRender: true,
               editable: false,
               mode: 'local',
               disabled : Boolean(<?=(count($estado_list) == 0)?>),
               bdDisabled : Boolean(0),
               hidden : Boolean(0),
               bdHidden : Boolean(0),
               readOnly : Boolean(0),

               tpl: '<tpl for="."><div ext:qtip="{label}" class="x-combo-list-item our_status {label}">{label}</div></tpl>',
               listeners: {
                   beforeselect: function(e, _record){
                       //console.log(e.getEl().dom.className);
                       e.getEl().dom.className = 'x-form-text x-form-field our_status ' + _record.get('label');
                   }
               },
               bdReadOnly : Boolean(0)
               //value: unidad_ejecutora,
  //                    disabled:   Boolean(0),
  //                    hidden:     Boolean(0)
           },{
               xtype:'combo',
               store: region_store_<?=$opId?>,
               id : 'category_region_id',
               name:'category_region_id',
               fieldLabel:'Región',
               displayField : 'label',
               valueField : 'value',
               triggerAction: 'all',
               hiddenName: 'category_region_id',
               lazyRender: true,
               editable: false,
               mode: 'local',


               //displayTpl: '<tpl for="."><div ext:qtip="{label}" class="x-combo-list-item rabsa_status {label}">{label}</div></tpl>',
               //tplWriteMode: '<div class="x-combo-list-item rabsa_status {label}">{label}</div>',
               disabled : 0,
               bdDisabled : 0,
               hidden : 0,
               bdHidden : 0,
               readOnly : 0,
               bdReadOnly : 0
               //value: unidad_ejecutora,
  //                    disabled:   Boolean(0),
  //                    hidden:     Boolean(0)
           },{
               xtype:'datefield',
               id : 'fecha_ini',
               name : 'fecha_ini',
               fieldLabel:'Fecha Inicio',
               //value: fecha_ini,
               disabled:   Boolean(0),
               hidden:     Boolean(0)

           }]
        },
        {
        //width: 350,
        style:{
            marginLeft: '10px'
        },
        layout:'form',
        defaults: {
            // applied to each contained item
            width: 150,
            msgTarget: 'side'
        },
        items: [
            {
                xtype:'textfield',
                id : 'id',
                name : 'id',
                fieldLabel:'COD',
                //value: n_solicitud,

                disabled:   Boolean(0),
                hidden:     Boolean(0)

            },
            {
                xtype:'textfield',
                id : 'cuenta_contrato',
                name : 'cuenta_contrato',
                fieldLabel:'Cuenta Contrato',
                //value: n_cuenta,
                disabled:   Boolean(0),
                hidden:     Boolean(0)

            },{
                xtype:'datefield',
                id : 'fecha_fin',
                name : 'fecha_fin',
                fieldLabel:'Fecha Fin',

                dateFormat: 'd/m/Y',
                //value: fecha_fin,
                disabled:   Boolean(0),
                hidden:     Boolean(0)

            }
        ]
     },
        {
        //width: 350,
        style:{
            paddingLeft: '20px'
        },
        layout:'form',
        defaults: {
            // applied to each contained item
            width: 150,
            msgTarget: 'side'
        },
        items: [
            {
                xtype:'textfield',
                id : 'n_reclamo',
                name : 'n_reclamo',
                fieldLabel:'Número Reclamo',
                //value: n_cuenta,
                disabled:   Boolean(0),
                hidden:     Boolean(0)

            },
            {
                xtype:'combo',
                store: tipo_reclamo_store_<?=$opId?>,

                id : 'category_tipo_reclamo_id',
                name : 'category_tipo_reclamo_id',
                fieldLabel:'Tipo Reclamo',
                displayField : 'label',
                valueField : 'value',
                triggerAction: 'all',
                hiddenName: 'category_tipo_reclamo_id',
                lazyRender: true,
                editable: false,
                mode: 'local',


                tpl: '<tpl for="."><div ext:qtip="{label}" class="x-combo-list-item our_status {label}">{label}</div></tpl>',
                //tplWriteMode: '<div class="x-combo-list-item rabsa_status {label}">{label}</div>',
                disabled : 0,
                bdDisabled : 0,
                hidden : 0,
                bdHidden : 0,
                readOnly : 0,
                bdReadOnly : 0,
                child:'category_subtipo_reclamo_id',
                listeners:{
                    select: function(combo, record){
                        
//                        var childCombo=Ext.getCmp(combo.child);
//                        if(empty(childCombo.multiselects)){
//                            clearChildCombos(childCombo);
//                            childCombo.store.load({
//                                params:{
//                                    id: combo.value
//                                }
//                            });
//                        }
                        
                        
                        loadChildCombo(combo, record);
                    }
                }
                
            },
            
            {
                xtype:'combo',
                store: subtipo_reclamo_store_<?=$opId?>,

                id : 'category_subtipo_reclamo_id',
                name : 'category_subtipo_reclamo_id',
                fieldLabel:'Subtipo Reclamo',
                displayField : 'label',
                valueField : 'value',
                triggerAction: 'all',
                hiddenName: 'category_subtipo_reclamo_id',
                lazyRender: true,
                editable: false,
                mode: 'local',


                tpl: '<tpl for="."><div ext:qtip="{label}" class="x-combo-list-item our_status {label}">{label}</div></tpl>',
                //tplWriteMode: '<div class="x-combo-list-item rabsa_status {label}">{label}</div>',
                disabled : 1,
                bdDisabled : 0,
                hidden : 0,
                bdHidden : 0,
                readOnly : 0,
                bdReadOnly : 0

            },
            /*,
            {
                xtype:'textfield',
                id : 'category_alcance_id',
                name : 'category_alcance_id',
                fieldLabel:'Alcance',
                name:'numero_cuenta',
                //value: n_cuenta,
                disabled:   Boolean(0),
                hidden:     Boolean(0)

            }*/
        ]
     }
    ]
//    ,
//    border:         false,
//    bodyStyle:      {paddingTop: '10px', paddingLeft: '35px' },
//    style:          {margin: 'auto'},
//    defaults:       {
//        labelStyle: 'font-weight: bold;',
//        style:      { margin: '0 0  5px 0', padding: '2px' }
//    }
    ,
    buttons:        []});



var filtro_<?=$opId?>= new Ext.Panel({
//    title : 'Busqueda',
    height:   'auto',
    tbar: {items:[search_acction_<?=$opId?>, clear_acction_<?=$opId?>]},
    items : [form_filtro_<?=$opId?>]
    
  //  'html':'hola mundo 2'
});


<?php if(!empty($scriptTags)):?>
	</script>
<?php endif; ?>