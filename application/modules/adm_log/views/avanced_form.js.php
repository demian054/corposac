<?php
/**
 * @package views
 * @subpackage modules/adm_log
 *
 * @author      Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.0 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista particular donde se crean un FormPanel, que contiene un TabPanel dentro y a su vez contiene varios tabs con varias campos
 * de formulario. Esta vista se emplea para generar el formulario de busqueda avanzada dentro del log.
 */
//Denifinimos las que crearan los diferentes componentes ExtJS
?>
    /** configs */
    url_filters = 'adm_log/log/CL_filters';
    filter_field =  'filter';
    filter_by = 'filterBy';
    operation_filter =  filter_by + '=operations&';
    role_filter =  filter_by + ':"roles",';
    
    var funcion_buscar = function(){

            //Anteriormente se definio la funcion que ejecutara la recarga del gridstore en funcion de los parametros definidos
            //dentro del formulario.

            //Si el formulario es valido enviamos los parametros de busqueda.
            if (<?= $form_name ?>.getForm().isValid()){

                //Obtenemos los valores del checkTree y lo seteamos la variable operation_hidden.
                operation_hidden.setValue(Ext.getCmp('tree').getValue());

                //Llamada al metodo submit de ExtJs para enviar el formulario via ajax.
        <?= $form_name ?>.getForm().submit({
                method: 'POST',
                url: BASE_URL + 'adm_log/log/listAll/process',
                success: function(form, action){

                    var obj = Ext.util.JSON.decode(action.response.responseText);
                    //Evaluamos la respuat del servidor a fin de determinar si recargamos el store o mandamos un msg de validación
                    if (obj.response.result){
                        GridStore_<?= $operation_id ?>.load({params:form.getValues()});
                        var t_panel = Ext.getCmp('panel_<?= $operation_id?>1');
                        t_panel.expand(false);
                        t_panel.collapse(false); //no se colapsa si no se ha expandido con su método tradicional
                        
                    }else
                        Ext.Msg.show({
                            title: obj.response.title,
                            msg: obj.response.msg,
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR,
                            minWidth: 300
                        });

                },
                failure: function(form,action){
                    switch (action.failureType) {
                        case Ext.form.Action.CLIENT_INVALID:
                            Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                            break;
                        case Ext.form.Action.CONNECT_FAILURE:
                            Ext.Msg.alert('Failure', 'Ajax communication failed');
                            break;
                        case Ext.form.Action.SERVER_INVALID:
                            Ext.Msg.alert('Failure', action.result.msg);
                            break;
                        default:
                            Ext.Msg.alert('Failure',action.result.msg);
                    }
                }

            });
        } else { //Formulario no valido.
            Ext.Msg.show({
                title: '<?= $this->lang->line('validation_error_title') ?>',
                msg: '<?= $this->lang->line('validation_error_message') ?>',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.WARNING,
                minWidth: 300
            });
        }
    };

    /**
     * <b> buildForm()</b>
     * Crea un componente Ext Js de tipo Form.
     * @params  String  title   Titulo del formulario.
     * @return  Object  Tipo form.
     * @version  1.0    12/09/2012 13:54:00
     **/
    function buildForm(title){
        return new Ext.FormPanel({
            title:title,
            id:'<?= $form_name ?>',
            frame:true,
            keys: [{
                key: [Ext.EventObject.ENTER],
                handler: funcion_buscar
            }]
        });
    }

    /**
     * <b> buildToolBar()</b>
     * Crea un componente Ext Js de tipo ToolBar.
     * @params  String  title   Titulo del formulario.
     * @return  Object  Tipo toolbar.
     * @version  1.0    12/09/2012 13:54:00
     **/
    function buildToolBar(){
        return new Ext.Toolbar({
            //layout:'form',
            autoDestroy: true
        });
    }

    /**
     * <b> buildPanel()</b>
     * Construye un componente Ext Js de tipo Panel.
     * @param   String  title    Titulo del tab Panel.
     * @return  Object  Tipo Panel.
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildPanel(title){
        title = (title) ? title : null;
        return new Ext.Panel({
            layout: 'form',
            title: title
        });
    }

    /**
     * <b> buildTabPanel()</b>
     * Construye un componente Ext Js de tipo TabPanel.
     * @return  Object  Tipo TabPanel.
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildTabPanel(){
        return new Ext.TabPanel({
            plain:true,
            autoHeight:true,
            defaults:{
                bodyStyle:'padding:10px;',
                autoHeight : true
                //,
                //height: 400
            },
            deferredRender: false,
            activeTab: 0,
            autoDestroy: true
        });
    }

    /**
     * <b> buildTextField()</b>
     * Construye un componente Ext Js de tipo textField.
     * @param   String  name    Nombre del campo dentro del formulario.
     * @param   String  label   Etiqueta perteneciente al campo para ser visualizada.
     * @param   Boolean allow_blank   Indica si el campo puede ser vacio. Default true.
     * @param   String  validation   Indica el tipo de validacion(cliente) a ser aplicada al campo. Default null.
     * @param   String  emptyText   Texto a ser mostrado cuando el campo esta vacio.  Default ''.
     * @return  Object   Tipo textField
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildTextField(name, label, allow_blank, validation, emptyText){
        allow_blank = (allow_blank) ? allow_blank : true;
        validation = (validation) ? validation : null;
        emptyText = (emptyText) ? emptyText : '' ;
        return new Ext.form.TextField({
            name: name,
            fieldLabel: label,
            allowBlank: allow_blank,
            vtype: validation,
            emptyText: emptyText,
            width: 200
        });
    }

    /**
     * <b> buildNumberField()</b>
     * Construye un componente Ext Js de tipo NumberField.
     * @param   String  name    Nombre del campo dentro del formulario.
     * @param   String  label   Etiqueta perteneciente al campo para ser visualizada.
     * @param   Boolean allow_blank   Indica si el campo puede ser vacio. Default true.
     * @param   String  emptyText   Texto a ser mostrado cuando el campo esta vacio.  Default ''.
     * @return  Object  Tipo NumberField
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildNumberField(name, label, allow_blank, emptyText){
        allow_blank = (allow_blank) ? allow_blank : true;
        emptyText = (emptyText) ? emptyText : '' ;
        return new Ext.form.NumberField({
            name: name,
            fieldLabel: label,
            allowBlank: allow_blank,
            emptyText: emptyText,
            width: 200
        });
    }

    /**
     * <b> buildComboBox()</b>
     * Genera un componente Ext Js de Tipo combobox.
     * @param   String  name    Nombre del campo dentro del formulario.
     * @param   String  label   Etiqueta perteneciente al campo para ser visualizada.
     * @param   Object  store   datos a ser visualizados dentro del combobox.
     * @param   String  emptyText   Texto a ser mostrado cuando el campo esta vacio.  Default ''.
     * @return  Object  Tipo    ComboBox.
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildComboBox(name, label, store, emptyText){
        emptyText = (emptyText) ? emptyText : '' ;
        return new Ext.form.ComboBox({
            mode: 'local',
            fieldLabel: label,
            name:   name,
            forceSelection: true,
            store:  store,
            width: 200,
            emptyText: emptyText,
            triggerAction:  'all',
            editable:   false,
            valueField: 'value',
            displayField: 'label'
        });
    }

    /**
     * <b> buildDateTime()</b>
     * Genera un componente Ext Js de Tipo DateTime.
     * @param   String  name    Nombre del campo dentro del formulario.
     * @param   String  label   Etiqueta perteneciente al campo para ser visualizada.
     * @return  Object  Tipo DateTime.
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildDateTime(name, label){
        return new Ext.ux.form.DateTime({
            width: 200,
            name:name,
            fieldLabel: label,
            timeFormat: 'h:i a',
            dateFormat: 'd/m/Y'
        });
    }

    /**
     * <b> buildDutton()</b>
     * Genera un componente Ext Js de Tipo Button.
     * @param   String  label   Etiqueta perteneciente al campo para ser visualizada.
     * @param   String  icon    Icono del campo para ser visualizado.
     * @param   callback  fn    Function a ser ejecutada en el handler del boton.
     * @return  Object  Tipo DateTime.
     * @version 1.0 12/09/2012 15:20:30
     **/
    function buildButton(label, icon, fn){
        return new Ext.Button({
            icon: BASE_ICONS + icon,
            text : label,
            handler : fn
        });
    }

    /**
     * <b> buildJsonStore()</b>
     * Genera un componente de tipo JsonStore.
     * @param   JSON    data    Data a ser cagada en el Store.
     * @return  Object  de tipo JsonStore
     * @version 1.0 12/09/2012 16:38
     **/
    function buildJsonStore(data, url){
        url = (url) ? BASE_URL + url : false;
        return new Ext.data.JsonStore({
            root:'rowset',
            autoLoad:   false,
            autoDestroy:    true,
            fields: ['value', 'label'],
            data: data,
            proxy: new Ext.data.HttpProxy({
                method: 'GET',
                url: url,
            })
        });
    }

    /**
     * <b> buildItemSelector</b>
     * Construye un componente de tipo ItemSelector.
     * @param   String  name    Nombre del campo dentro del formulario.
     * @param   String  label   Etiqueta perteneciente al campo para ser visualizada.
     * @param   Object  available   Datos con los valores disponibles.
     * @param   Object  selected    Datos con los valores selecionados. Default null.
     * @return  Object  Tipo itemselector.
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildItemSelector(name, label, available, selected){
        //Store donde se cargaran los campos selecionados
        var selected = (selected) ? selected : buildJsonStore({"rowset":[]});
        var height = 120;
        return new Ext.ux.form.ItemSelector({
            id: name,
            name:   name,
            fieldLabel: label,
            imagePath:  BASE_ICONS,
            multiselects: [
                //ItemSelector1
                {
                    id: name + '_1',
                    width: 200,
                    height: height,
                    legend:'Disponibles',
                    droppable: true,
                    draggable: true,
                    displayField:   'label',
                    valueField:     'value',
                    store: available
                },
                //ItemSelector2
                {
                    id: name + '_2',
                    width: 200,
                    height: height,
                    legend:'Seleccionados',
                    droppable: true,
                    draggable: true,
                    displayField:   'label',
                    valueField:     'value',
                    store: selected
                },
            ]
        });
    }

    /**
     * <b> buildCheckTree()</b>
     * Construye un componente de tipo CheckTree.
     * @param   String  name    Nombre del campo dentro del formulario.
     * @param   String  label   Etiqueta perteneciente al campo para ser visualizada.
     * @param   Object  store   Datos con los valores disponibles.
     * @return  Object  Tipo CehckTree
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildCheckTree(name, label){ //, store){
        return new Ext.ux.tree.CheckTreePanel({
            title: label,
            id: 'tree',
            name: name,
            height: 175,
            width: 525,
            useArrows:true,
            expandOnCheck: true,
            autoScroll:true,
            animate:true,
            containerScroll: true,
            rootVisible: false,
            root: new Ext.tree.AsyncTreeNode(),
            loader: new Ext.tree.TreeLoader({
                url: BASE_URL + 'adm_log/log/CL_filters',
                baseParams: {filterBy: 'operations', filter: '0'},
                requestMethod: 'GET'
            }),
            //nodeType: 'async',
            cascadeCheck: 'all',
            bubbleCheck: 'all'
            //dataUrl: BASE_URL + url_filters
        });
    }

    /**
     * <b> buildHiddenField()</b>
     * Construye un componente Ext Js de tipo hiddenField.
     * @param   String  name    Nombre del campo dentro del formulario.
     * @return  Object  Tipo hiddenField
     * @version 1.0 11/09/2012 12:20:30
     **/
    function buildHiddenField(name){
        return new Ext.form.Hidden({
            name:name
        });
    }

    //
    //Start UserTab
    //Creamos las configuraciones para el tab de Datos de Usuario.
    var user_tab = buildPanel('Datos de usuario.');

    //Creamos los campos para el formulario de datos de Usuario.
    var first_name = buildTextField('first_name','Nombre',true,'valid_alpha_space');
    var last_name = buildTextField('last_name','Apellido',true,'valid_alpha_space');
    var email = buildTextField('email','Email',true,'valid_email');
    var username = buildTextField('username','Nombre de Usuario',true,'alphanum');
    var _document = buildNumberField('_document','Cedula',true);

    //Agregamos los campos del formulario al tab
    user_tab.add(first_name);
    user_tab.add(last_name);
    user_tab.add(email);
    user_tab.add(username);
    user_tab.add(_document);
    //End UserTab

    //
    //Start systemTab
    //Creamos las configuraciones para el tab de Datos del sistema cliente.
    var system_tab = buildPanel('Datos del sistema cliente.');

    //Creamos los campos para el formulario de datos de Usuario.
    var ip = buildTextField('ip','Dirección IP',true,'valid_ip');
    var mac_address = buildTextField('mac_address','Dirección Mac',true,'valid_mac_address');

    //Creamos el ItemSelector de OS y su store.
    var data = <?= $avalible_os_json ?>;
    var avalible_os_store = buildJsonStore(data);
    var operation_system = buildItemSelector('os','Sistema Operativo', avalible_os_store);

    //Creamos el ItemSelector Navigators y su store.
    var data = <?= $avalible_navigators_json ?>;
    var avalible_navigators_store = buildJsonStore(data);
    var user_agent = buildItemSelector('user_agent','Navegador', avalible_navigators_store);

    //Agregamos los campos del formulario al tab
    system_tab.add(ip);
    system_tab.add(mac_address);
    system_tab.add(operation_system);
    system_tab.add(user_agent);
    //End systemTab

    //
    //Start OperationTab
    //Creamos las configuraciones para el tab de Operaciones del Sistema.
    var operation_tab = buildPanel('Operaciones del sistema.');

    var operation_type_radios = new Ext.form.RadioGroup({
        fieldLabel: 'Tipo de Operaciones a ser Listados',
        width: 300,
        columns: 3,
        items:[
            {boxLabel: 'En Sistema', name: filter_field, inputValue: '0', checked: true},
            {boxLabel: 'Eliminados', name: filter_field, inputValue: '1'},
            {boxLabel: 'Todos', name: filter_field, inputValue: 'false'}
        ],
        listeners:{
            change: function(object, checked) {
                if (checked == null) return;
                var tree = Ext.getCmp('tree');
                var params = '?' + operation_filter + checked.getName() + '=' + checked.getGroupValue();
                
                var tloader = tree.getLoader();
                tloader.baseParams['filter'] = checked.getGroupValue();
                tloader.load(tree.root);

                
                
/*
                //tree.getLoader().dataUrl= BASE_URL + url_filters + params;
                //tree.getLoader().load(tree.root);
//                tree.setRootNode(root);

                Ext.Ajax.request({
                    url : BASE_URL + url_filters,
                    method: 'GET',
                    params: operation_filter + checked.getName() + '=' + checked.getGroupValue(),
                    success: function ( result, request ) {
                        //result.responseText;

                        var tree = Ext.getCmp('tree');
                        store = result.responseText.tree_store;

                        tree.setRootNode(new Ext.tree.AsyncTreeNode(store));

                        //tree.getLoader().dataUrl= BASE_URL + url_filters +'?'+operation_filter + checked.getName() + '=' + checked.getGroupValue();
                        //tree.getLoader().load(tree.root);
                        //console.log(treePanel);


                    },
                    failure: function ( result, request ) {
                        Ext.Msg.show({
                           title:'<?= $this->lang->line('message_failure_title')?>',
                           msg: '<?= $this->lang->line('message_failure')?>',
                           buttons: Ext.Msg.YES,
                           icon: Ext.MessageBox.WARNING
                        });
                    }
                });
*/
            }
        }
    });

    //Creamos el store del combo
    var data = <?= $log_type_json ?>;
   
    var log_type_store = buildJsonStore(data);
    //var operations_store = <?= $operations_json ?>;

    //Creamos los campos para el formulario de datos operaciones del sistema.
    var operation_type = buildItemSelector('category_log_type_id','Tipo de Operación', log_type_store);
    var operations_tree = buildCheckTree('operation_tree','Arbol de Operaciones'); //, operations_store);
    var operation_hidden = buildHiddenField('operation_id');

    //Agregamos los campos del formulario al tab
    operation_tab.add(operation_type_radios);
    operation_tab.add(operations_tree);
    operation_tab.add(operation_type);
    operation_tab.add(operation_hidden);
    //End OperationTab

    //
    //Start RoleTab
    //Generamos los radiobuttons para filtrar los roles.
    roles_items_select_name = 'role_id';

    var role_type_radios = new Ext.form.RadioGroup({
        fieldLabel: 'Tipo de Roles a ser Listados',
        width: 300,
        columns: 3,
        items:[
            {boxLabel: 'En Sistema', name: filter_field, inputValue: '0', checked: true},
            {boxLabel: 'Eliminados', name: filter_field, inputValue: '1'},
            {boxLabel: 'Todos', name: filter_field, inputValue: 'false'}
        ],
        listeners:{
            change: function(object, checked) {
                if (! checked) return;
                var srt = 'var params = { params: {' + role_filter + checked.getName() + ':' + checked.getGroupValue() + '}};';
                eval(srt);
                Ext.getCmp(roles_items_select_name + '_1').store.load(params);
            }
        }
    });
    
    //Creamos el ItemSelector Roles y su store.
    var data = <?= $avalible_roles_json ?>;
    var avalible_roles_store = buildJsonStore(data, url_filters);
    var roles = buildItemSelector(roles_items_select_name,'Roles del Sistema', avalible_roles_store);

    //Creamos las configuraciones para el tab de Roles del Sistema.
    var role_tab = buildPanel('Roles del sistema.');

    //Agregamos los campos del formulario al tab
    role_tab.add(role_type_radios);
    role_tab.add(roles);
    //End RoleTab

    //Creamos el tab panel
    var tab = buildTabPanel();

    //Aderimos los conmponentes al tabPanel
    tab.add(user_tab);
    tab.add(system_tab);
    tab.add(operation_tab);
    tab.add(role_tab);

    //Cremos un panel intermedio.
    var intermediate = buildPanel();
    intermediate.bodyStyle = 'padding:3px;';
    //intermediate.hide();
    intermediate.add(tab);

    //
    //Creamos los campos tipo datetime para ser ubicados en el topbar.
    var start_date = buildDateTime('start_date', 'Fecha de Inicio');
    var end_date = buildDateTime('end_date', 'Fecha de Fin');

    //
    //Creamos un toolbar para los campos tipo datetime
    var t_bar = buildToolBar();
    t_bar.add('Fecha de Inicio: ');
    t_bar.add(start_date);
    t_bar.add('-');
    t_bar.add('Fecha de Fin: ');
    t_bar.add(end_date);

    //Aliniamos los botones ala derecha.
    t_bar.add('->');

    //
    //Creamos los botones
    //Primero save
    var button_save = buildButton('Buscar', 'find.png', funcion_buscar);
t_bar.add(button_save);

//Tercero Limpiar
var button_reset = buildButton('Limpiar', 'broom-minus-icon.png', function(){
    //                        intermediate.hide();
    GridStore_<?= $operation_id ?>.load({params:null});
    avanced_form.getForm().reset();
});
t_bar.add(button_reset);

//
//Creamos el componenete de tipo formulario
//var <?= $form_name ?> = buildForm('Formulario avanzado de busqueda dentro del log.');
var <?= $form_name ?> = buildForm('');
    <?= $form_name ?>.add(t_bar);
    <?= $form_name ?>.add(intermediate);
    <?= $form_name ?>.setHeight(450);
    <?= $form_name ?>.setAutoScroll(true);
