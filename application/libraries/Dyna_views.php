<?php

/**
 * Dyna_views Class
 *
 * @package     libraries
 * @author      Jesus Farias Lacroix <jesus.farias@gmail.com>,
 * 				Juan C. Lopez <jlopez@rialfi.com>,
 * 				Reynaldo Rojas <rrojas@rialfi.com>
 *
 * @version     v1.0 16/02/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * @license  Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,_label
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *  * */
if (!defined('BASEPATH'))
    exit('Acceso Denegado');

class Dyna_views {

    //public static $CI; //instancia del singleton de Code Igniter
    public $operationData; //Datos de la operacion
    private $operation_id; //Id de la operacion en curso
    private $operation_url; // Url de la operacion en curso
    private $fieldsDb = array(); //Campos asociados en DB a la operacion
    private $pre_filtered_combos = array(); //Data inicial para combos prefiltrados
    private $comboStore = array(); //Imagen de los data stores de cada combo box o item selector
    private $allways_return_views; //Si este flag esta encendido DynaViews siempre retornara las vistas en lugar de incluirlas
    //NUEVO
    private $ci = NULL;

    public function __construct($_params) {
//        if (empty($_params['CI']))
//            self::$CI = &get_instance();
//        else
//            self::$CI = &$_params['CI'];
        $this->allways_return_views = @$_params['allways_return_views'];
        $this->operation_id = @$_params['operation_id'];
        $this->operation_url = @$_params['operation_url'];
        define('BASE_ICONS', ((base_url()) . 'assets/img/icons/'));

        $this->ci = &get_instance();
        $this->ci->load->library('form_validation');
        $this->ci->load->library('table');
        
        if(!empty($this->operation_id)){
    //        $this->ci->combo_loader_model->init($this->ci);
            $this->ci->load->model($this->ci->config->item('dyna_views_model'));

            $this->ci->metadata_model->setId($this->operation_id);
            $this->operationData = $this->ci->metadata_model->getOperationData($this->operation_id);
        }
    }

    /**
     * <b>Method:   buildGrid($params)</b>
     * Construye un grid con los datos proporcionados.
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del grid.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  mixed      [data]     Datos pertenecientes al modulo ejecutado.
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [return_view]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al form.
     *                  mixed      [preBuildFields]   Arreglo con campos y configuraciones preconstruidos a ser renderizados.
     * @author      Jesus Farias
     *              Juan C. Lopez
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 29/08/2012 10:00
     * */
    public function buildForm($params) {
        extract($params);
        $operation_id = $this->operation_id;

        //Preguntamos si hay campos preconstruidos.
        if (!empty($preBuildFields))
            $fields = $preBuildFields;
        else {
            if (empty($extraOptions['reporte_id'])) {
                $this->fieldsDb = $this->ci->metadata_model->getFieldsByOperation($operation_id);
                //$this->getFieldsByOperation($opId);
            } else {
                $this->getFieldsByReporte($extraOptions['reporte_id'], 'form', $extraOptions['report_type']);
                $formType = 'reporte';
            }

            //Evalua si el formulario tiene campos asociados en bd.
            if (empty($this->fieldsDb))
                return FALSE;

            //Una vez obtenidos los campos se tranforman un su respectivo componente.
            $fields = $this->buildFields($this->fieldsDb, $data, FALSE, $formType);
        }

        $replace = $this->setReplaceContent('form', $replace);

        $addUploadModule = FALSE;

        //Pregunta por los botones hijos de tipo 'parent' de la operacion en curso
        //$opParentChilds = $this->getChildsByOperationId($this->operation_id, 'Button', TRUE, 'parent');
        $operation_parent_children =
            $this->ci->metadata_model->getChildrenByOperation($this->operation_id, 'Button', TRUE, 'parent');

        //con los botones hijos creamos los botones
        //el parametro TRUE indica que se debe agregar la opcion cancel
        $Cancel_button = ($extraOptions['CancelButton'] == '0') ? '0' : '1';

        //Construye los botones pertenecientes a otras operaciones (Ejm: controller/create/process)
        $form_button = $this->buildButtons($operation_parent_children, $Cancel_button, $replace);

        //Obtenemos los diferentes toolbars
        $tbar_button = $bbar_button = '';
        $tbar_button = $this->buildToolBar();
        $bbar_button = $this->buildToolBar('bbar');
        $autoWidth = (isset($extraOptions['autoWidth'])) ? $extraOptions['autoWidth'] : TRUE;
        $buttonsInBbar = (isset($extraOptions['buttonsInBbar'])) ? $extraOptions['buttonsInBbar'] : FALSE;
        
        if ($buttonsInBbar) {
            $bbar_button = $form_button;
            $form_button = '[]';
        }

        //Indica la aliniacion de las etiquetas.
        $label_align = (!empty($this->allways_return_views)) ? "labelAlign:'top'," : '';

        //Arreglo de datos a ser pasados a la vista.
        $view_data = array(
            'url' => $this->operation_url,
            'formTitle' => $title,
            'name' => $name,
            'data' => '',
            'fields' => $fields,
            'buttons' => $form_button,
            'tbar' => $tbar_button,
            'bbar' => $bbar_button,
            'extraData' => '',
            'replace' => $replace,
            'opId' => $operation_id,
            'addUploadModule' => $addUploadModule,
            'scriptTags' => $scriptTags,
            'labelAlign' => $label_align,
            'autoWidth' => $autoWidth,
            'height' => @$extraOptions['height'],
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        //Referente a la carga de combos.
        $view_data['dBStore'] = FALSE;
        if (!empty($this->comboStore))
            $view_data['dBStore'] = ' var combo_store = ' . json_encode($this->comboStore);

        //Evaluamos si la vista debe ser retornada como string o impresa.
        if (!empty($return_view) || !empty($this->allways_return_views)) {
            $preBuildView = $this->ci->load->view(
                $this->ci->config->item('dyna_views_form_path') . 'form.js.php', $view_data, TRUE
            );

            //Preguntamos siempre se debe retornar la vista.
            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => "form_$this->operation_id");
            else
                return $preBuildView;
        }

        //Hacemos el llamado y mostramos la vista generada.
        $this->ci->load->view($this->ci->config->item('dyna_views_form_path') . 'form.js.php', $view_data);
    }

    /**
     * <b>Method:   buildGrid($params)</b>
     * Construye un grid con los datos proporcionados.
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del grid.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  mixed      [data]     Datos pertenecientes al modulo ejecutado.
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [returnView]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al grid.
     *                  mixed      [pre_build_fields]   Indica si el grid sera construido con campos preformateados.
     * @author      Jesus Farias
     *              Juan C. Lopez
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 29/08/2012 10:00
     * */
    public function buildGrid($params) {
        extract($params);
        if (empty($pre_build_fields))
            $this->fieldsDb = $this->ci->metadata_model->getFieldsByOperation($this->operation_id);
        //$this->getFieldsByOperation($this->operation_id);
        else
            //$this->getFieldsByReporte($extraOptions['reporte_id'], 'grid', $extraOptions['report_type']);
            $this->fieldsDb = $pre_build_fields;

        if (empty($this->fieldsDb))
            return FALSE;

        //Inicializamos las variables a ser empleadas mas adelante.
        $tbar_button = $bbar_button = '';
        $store_fields = array();
        $columns = '[';
        $colon = '';
        $hasDisclaimer = (!empty($extraOptions['disclaimer'])) ? TRUE : FALSE;

        //Contruimos los botones del grid.
        $columns.=$this->makeGridButtons('Grid_', $name, $hasDisclaimer);

        //Recorremos la metadata de los campos en base de datos.
        foreach ($this->fieldsDb as $field) {

            //Definimos el valor del Label
            $label = $this->getLabel($field);

            // Definir formato segun tipo de campo
            $renderer = $this->renderFormatField($field->ext_component);

            $servername = ($field->alias == NULL) ? $field->server_name : $field->alias;
            $width = ($field->visual_width == NULL) ? '' : "width:$field->visual_width,";
            
            $store_fields[]['name'] = $servername;
            if ($field->hidden != "1") {
                //$colum = $coma . "{ $renderer header:'$field->_label', dataIndex:'$field->server_name', sortable:true}";
                //@todo Implementar sortable
                $colum = $colon . "{ $renderer $width header:'$label', dataIndex:'$servername', id:'col_{$this->operation_id}_{$field->id}', menuDisabled: true}";
                $colon = ',';
                $columns.=$colum;
            }
        }
        $columns .= ']';

        $replace = $this->setReplaceContent('Grid', $replace);

        //Obtenemos los diferentes toolbars
        $tbar_button = $bbar_button = '';
        $tbar_button = $this->buildToolBar();
        $bbar_button = $this->buildToolBar('bbar');

        //
        $searchType = '';
        if (!empty($extraOptions['searchType']))
            $searchType = $extraOptions['searchType'];

        $bbar_off = (!empty($extraOptions['bbarOff'])) ? TRUE : FALSE;

        //Seteamos las diferentes configuraciones de la vista.
        $view_data = array(
            'gridTitle' => $title,
            'name' => $name,
            'fields' => json_encode($store_fields),
            'data' => json_encode($data),
            'columns' => $columns,
            'tbar' => $tbar_button,
            'bbar' => $bbar_button,
            'searchType' => $searchType,
            'extraData' => $data['extraData'],
            'opId' => $this->operation_id,
            'url' => $this->operation_url,
            'scriptTags' => $scriptTags,
            'bbarOff' => $bbar_off,
            'replace' => $replace,
            'height' => @$extraOptions['height'],
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        //Validamos si la vista debe retornanrse.
        if (!empty($returnView) || !empty($this->allways_return_views)) {
            $preBuildView = $this->ci->load->view(
                $this->ci->config->item('dyna_views_grid_path') . 'grid.js.php', $view_data, TRUE
            );

            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => "Grid_$this->operation_id");
            else
                return $preBuildView;
        }

        //Hacemos el llamdo de la vista a ser cargada.
        $this->ci->load->view($this->ci->config->item('dyna_views_grid_path') . 'grid.js.php', $view_data);
    }

    /**
     * <b>Method:   buildModalWindow($params) </b>
     * Crea un componente ExtJs de Tipo ModalWindow (Creo no esta implemntado realmente)
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del Modal Window.
     *                  Array      [content]    ******.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al ModalWindow.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 18:22
     * */
    public function buildModalWindow($params) {
        //Extraemos los parametros a ser utilizados dentro del metodo.
        extract($params);

        //se realiza las configuraciones necesarias para el modal win
        //es decir que si height, width, y parametros de configuracion extras que se necesiten
        //para customizar una ventana modal
        //$this->load->view('generals/window.js.php', $content);
        $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'window.js.php', $content);
    }

    /**
     * <b>Method:   buildGroupingGrid($params) </b>
     * Crea un componente ExtJs de Tipo GroupingGrid
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del grid.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  mixed      [data]     Datos pertenecientes al modulo ejecutado.
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [returnView]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al grid.
     *                  String     [groupField]     Indica la columna de agrupacion.
     *                  String     [direction]      La direccion de ordenamiento de la agrupacion. Default 'ASC'.
     *                  String     [sortField]      Campo sensibles al ordenamiento. Default FALSE
     *                  String     [groupRenderer]      Define un render especial para el grouping. Default = ''
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 18:27
     * */
    public function buildGroupingGrid($params) {

        //Definimos los Valores por defecto.
        $direction = 'ASC';
        $groupRenderer = '';

        //Extraemos los parametros
        extract($params);

        $this->fieldsDb = $this->ci->metadata_model->getFieldsByOperation($this->operation_id);

        if (empty($this->fieldsDb))
            return FALSE;

        $storeFields = array();
        $columns = '[';
        $colon = '';
        $hasDisclaimer = (!empty($extraOptions['disclaimer'])) ? TRUE : FALSE;
        $columns.=$this->makeGridButtons('groupingGrid_', $name, $hasDisclaimer);

        foreach ($this->fieldsDb as $field) {

            //definimos el Label
            $label = $this->getLabel($field);

            // Definir formato segun tipo de campo
            $renderer = $this->renderFormatField($field->data_type);
            $servername = ($field->alias == NULL) ? $field->server_name : $field->alias;
            $storeFields[]['name'] = $servername;
            $colum = $colon . "{
                header:'$label',
                dataIndex:'$servername',
                sortable:true,";
            $colum .= $renderer;
            $colum .= (!empty($field->renderer)) ? "renderer:$field->renderer," : '';
            $colum .= 'hidden:' . (($field->hidden == "1") ? 'true' : 'false') . '}';
            $colon = ',';
            $columns .= $colum;
        }
        $columns .= ']';

        $replace = $this->setReplaceContent('groupingGrid', $replace);

        //Obtenemos los diferentes toolbars
        $tbar_button = $bbar_button = '';
        $tbar_button = $this->buildToolBar();
        $bbar_button = $this->buildToolBar('bbar');

        $searchType = '';
        if (!empty($extraOptions['searchType']))
            $searchType = $extraOptions['searchType'];
        $bbarOff = (!empty($extraOptions['bbarOff'])) ? TRUE : FALSE;

        $view_data = array(
            'gridTitle' => $title,
            'name' => $name,
            'fields' => json_encode($storeFields),
            'data' => json_encode($data),
            //'data' => json_encode($data['rowset']),
            'columns' => $columns,
            'groupField' => $groupField,
            'direction' => $direction,
            'sortField' => $sortField || $groupField,
            'groupRenderer' => $groupRenderer,
            'extraData' => $data['extraData'],
            'replace' => $replace,
            'opId' => $this->operation_id,
            'url' => $this->operation_url,
            'tbar' => $tbarButton,
            'scriptTags' => $scriptTags,
            'searchType' => $searchType,
            'bbarOff' => $bbarOff,
            'bbar' => $bbarButton,
            'forceFit' => @$extraOptions['forceFit'],
            'height' => @$extraOptions['height'],
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        //Preguntamos si se debe retornar la vista enn un string.
        if (!empty($returnView) || !empty($this->allways_return_views)) {

            $preBuildView = $this->ci->load->view(
                $this->ci->config->item('dyna_views_grid_path') . 'groupingGrid.js.php', $view_data, TRUE
            );

            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => "groupingGrid_$this->operation_id");
            else
                return $preBuildView;
        }

        $this->ci->load->view($this->ci->config->item('dyna_views_grid_path') . 'groupingGrid.js.php', $view_data);
    }

    /**
     * <b>Method:   buildTabs($params) </b>
     * Crea un componente ExtJs de Tipo Tabs
     *                  string     [title]    Titulo del grid.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [returnView]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al grid.
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 18:44
     * */
    public function buildTabs($params) {

        //Obtenemos los hijos de la operacion.
        $operations = $this->ci->metadata_model->getChildrenByOperation(FALSE, 'Tab');

        //Validamos que la operacion tenga hijos.
        if (empty($operations))
            return FALSE;

        //Extraemos las configuraciones del componente
        extract($params);

        $id = $extraOptions['id'];

        //No se Usa se deja para futuras implemnteaciones.
        //$my_Tabs = $tab = array();

        $items = '[';
        $colon = '';
        $c = 0;
        foreach ($operations as $operaion) {
            $item = $colon . "{
                id:'Tab_$operaion->id',
                name:'Tab_$operaion->id',
                title:'$operaion->_name',
                layout: 'fit',
                listeners:{
                    activate: function(p){
                        p.load({
                            url:'$operaion->url',
                            method: 'GET',
                            params:{parentId:'Tab_$operaion->id',id:'$id'},
                            scripts:true
                        });
                    }
                },
                iconCls:'$operaion->icon'
            }";
            $c++;
            $colon = ',';
            $items.=$item;
        }
        $items.=']';

        $replace = $this->setReplaceContent('tabPanel', $replace);

        $viewData = array(
            'title' => $title,
            'name' => $name,
            'opId' => $this->operation_id,
            'items' => $items,
            'extraData' => '',
            'replace' => $replace,
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        //Evaluamos si se debu retornar la vista en un string
        if (!empty($returnView) || !empty($this->allways_return_views)) {
            //$preBuildView = self::$CI->load->view("generals/tabs.js.php", $viewData, true);
            $preBuildView = $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'tabs.js.php', $viewData, TRUE);
            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => "tabPanel_$this->operation_id");
            else
                return $preBuildView;
        }

        $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'tabs.js.php', $viewData);
    }

    /**
     * <b>Method:   buildOptionsStore($field_metadata, $operation_id, $config, $dyna_flag = FALSE) </b>
     * Crea las configuraciones refrentes a un data store de ExtJS.
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $pre_built_data    ****. Default FALSE.
     * @param       Array     $multi_select   *****. Default ''
     * @return      String    Componente en formato JSON.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 03/09/2012 11:00
     * */
    private function buildOptionsStore($field_metadata, $pre_built_data = FALSE, $multi_select = '') {
        $data = FALSE;
        $options_store = '';

        //
        if (!empty($pre_built_data) && is_array($pre_built_data))
            $data = json_encode($pre_built_data);

        else if ($multi_select == '_multi_to')
            $data = json_encode(array());

        else if (empty($field_metadata->hidden) && empty($field_metadata->disabled))
            $data = $this->getStoreData($field_metadata);

        $combo_loader = $field_metadata->custom_loader;
        if ($combo_loader != '') {
            if (!strpos($combo_loader, '/')) {
                $uri_parts = explode('/', $this->ci->uri->uri_string());
                $combo_loader = $uri_parts[0] . '/' . $uri_parts[1] . '/' . $combo_loader;
            }
        } else {
            $combo_loader = 'combo_loader';
        }
        //obtener el controller actual

        $options_store.="new Ext.data.JsonStore({
                url:'" . (base_url()) . "$combo_loader',
                method:'GET',
                autoLoad: false ,
                autoDestroy:true,
                proxy : new Ext.data.HttpProxy({
                    method: 'GET',
                    url: '" . (base_url()) . "$combo_loader'
                }),
                baseParams:{field:'" . json_encode($field_metadata) . "'},
                fields: ['value', 'label']";
        $options_store.= (!empty($data)) ? ",data:$data" : '';

        //if(!empty($field->hidden) || !empty($field->disabled)){
        $options_store.=",
                listeners:{
                    load: function(){
                        try{
                            var combo=Ext.getCmp('combo_$this->operation_id" . "_$field_metadata->id');
                            //combo.show();
                            if (combo != null)
                                combo.enable();
                        }catch(e){
                            //do nothing
                        }
                    }
                }";
        //}
        //End options Store
        $options_store .= ' })';

        //Varable comboStore definida al inicio de la libreria.
        $this->comboStore['combo_' . $this->operation_id . '_' . $field_metadata->id . $multi_select] = json_decode($data);

        return $options_store;
    }

    /**
     * <b>Method:   getStoreData($field, $parentId = FALSE) </b>
     * Crea un componente ExtJs tipo dataStore con los datos a ser cagados en el combo.
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $parent_id      Identificador de la operacion padre. Default FALSE
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 date
     * */
    public function getStoreData($field_metadata, $parent_id = FALSE) {
        $data_options = FALSE;
        $matches = NULL;

        //Determinamos si el campo es prefiltrado. (Asumo para combos dependientes).
        if (empty($parent_id) && !empty($this->pre_filtered_combos[$field_metadata->id]))
            $parent_id = $this->pre_filtered_combos[$field_metadata->id];

        //Condicion de proyecto SIETPOL para Campo Dinamico.
//        if (!empty($field_metadata->categoria_id))
//            $data_options = $this->getOptionsByCategory($field_metadata, $parent_id);
        //Determinamos si es un campo alias.
        //@todo Implementar getOptionsByTableALias
        elseif (!empty($field_metadata->entidad_alias_de) && !empty($field_metadata->alias_de))
            $data_options = $this->getOptionsByTableAlias($field_metadata, $parent_id);

        //Determinamos si el campo posee Custom Loader Asocioado
        elseif (!empty($field_metadata->custom_loader))
            $data_options = $this->getOptionsByCustomLoader($field_metadata, $parent_id);

        else {
            $field_name = (empty($field_metadata->alias_de)) ? $field_metadata->server_name : $field_metadata->alias_de;

            //Utilizamos expresiones regulares para evaluar de donde se obtienen los datos.
            //Pueden ser de categorias (category) o una tabla de regla de negocio.
            //Patron para determinar si es es un categoria.
            $category_patter = '/^category_(\w*)_id$/';

            //Patron para determinar si es una tabla.
            $table_patter = '/(\w*)_id$/';

            //Aplicamos el filtro de Categorias
            if (preg_match($category_patter, $field_name, $matches)) {

                //$data_options = $this->getCategories($field_metadata, $matches[1], $field_name, $parent_id);
                $data_options = $this->getCategories($matches[1], $parent_id);
            }

            //Aplicamos el filtro de tabla si el combo no se carga de categorias.
            elseif (preg_match($table_patter, $field_name, $matches)) {
                //$data_options = $this->getOptionsByTable($field_metadata, $parent_id);
                if (!empty($field_metadata->parent_filter))
                    $filter = array(
                        'column' => $field_metadata->parent_filter,
                        'value' => $parent_id
                    );

                $data_options = $this->getOptionsByTable($matches[1], $filter);
            }
        }

        //$this->comboStore['combo_' . $this->operation_id . '_' . $field->id] = json_decode($dataOptions);
        return $data_options;
    }

    /**
     * <b>Method:   getCategories($field_metadata, $operation_id, $config, $dyna_flag = FALSE) </b>
     * Hace el llamado a los datos de categorias y los formatea en formato JSON.
     * @param       String    $table    Nombre de la tabla normalizada dentro de la tabla category.
     * @param       Integer   $operation_id      Identificador de la operacion en curso. Default FALSE
     * @return      String    Datos en formato JSON
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 03/08/2012 16:00
     * */
    private function getCategories($table, $parent_id = FALSE) {
        $data = $this->ci->metadata_model->getCategories($table, $parent_id);
        if (!empty($data))
            return json_encode($data);
        else
            return FALSE;
    }

    /**
     * <b>Method:   getOptionsByTableAlias($field_metadata, $parent_id) </b>
     * Obtitne los resultados de tablas por sus alias.
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $parent_id        Identificador por donde se filtraran los resultados.
     * @return      String    Los datos en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/02/2012 16:30
     * */
    private function getOptionsByTableAlias($field_metadata, $parent_id = '') {
        $originTable = $field_metadata->entidad_alias_de;
        $tableAlias = $field_metadata->entidad_nombre;
        //$originField=$field->alias_de;
        //$fieldAlias=$field->nombre_s;
        $auxWhere = (!empty($parent_id) && !empty($field_metadata->parent_filter)) ? " AND $tableAlias.$field_metadata->parent_filter='$parent_id'" : '';
        //@todo mejorar esta cagada hardcodeada
        if ($field_metadata->id == 1852)
            $auxWhere.=" AND $tableAlias.$field_metadata->parent_filter='Estado'";

        $str_query = "SELECT
						$tableAlias.id AS value,
						$tableAlias.nombre AS label
					FROM
						$originTable AS $tableAlias
					WHERE
						$tableAlias.eliminado='0'
						$auxWhere
					ORDER BY $tableAlias.nombre";
        $query = self::$CI->db->query($str_query);
        $result = $query->result();
        if (empty($result))
            return false;
        //foreach ($result as &$item) $item->label=utf8_encode($item->label);
        return json_encode($result);
    }

    /**
     * <b>Method:   getOptionsByTable</b>
     * Obtiene los datos de una tabla de reglas de negocio.
     * @param       String    $table    Nombre de la tabla de negocio.
     * @param       Array     $filter   Filtro a ser aplicado a la tabla de regla de negocio. Default array().
     * @return      String    Datos de la tabla de regla de negocio en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 date
     * */
    private function getOptionsByTable($table, $filter = array()) {
        //($field_metadata, $parent_id = '') {
        $result = $this->ci->metadata_model->getOptionsByTable($table, $filter);
        if (!empty($result))
            return json_encode($result);

        //Si result es vacio return FALSE;
        return FALSE;
    }

    /**
     * <b>Method:  getOptionsByCustomLoader($field_metadata, $parent_id) </b>
     *
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $parent_id      Identificador de la operacion padre.
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Maycol Alvarez
     * @version     1.1 05/08/2012
     * */
    private function getOptionsByCustomLoader($field_metadata, $parent_id) {

        $method = $field_metadata->custom_loader;
        try {
            if (!empty($field_metadata->pattern))
                $parent_id = $field_metadata->pattern;

            //separa los componentes del controlador
            //si tiene 3 partes es un módulo HMVC, si no es un controlador normal
            //se carga el modelo para esta operación
            $parts = explode('/', $method);

            if (count($parts) == 1) {
                $uri_parts = explode('/', $this->ci->uri->uri_string());
                $this->ci->load->model($uri_parts[0] . '/' . $uri_parts[1] . '_model');
                $class_model = $uri_parts[1];
                $_method = $parts[0];
            } else if (count($parts) == 3) {
                $this->ci->load->model($parts[0] . '/' . $parts[1] . '_model');
                $class_model = $parts[1];
                $_method = $parts[2];
            } else {
                $this->ci->load->model($parts[0] . '_model');
                $class_model = $parts[0];
                $_method = $parts[1];
            }
            $class_model .= '_model';
            $result = $this->ci->$class_model->$_method($parent_id);
        } catch (Exception $exc) {
            $result = FALSE;
        }

        if (empty($result))
            return FALSE;
        return json_encode($result);
    }

    /**
     * <b>Method:   makeGridButtons($grid_type, $subject, $has_disclaimer = false) </b>
     * Crear Botones pertenecientes a los grid
     * @param       String    $grid_type    Prefijo con el tipo de Grid.
     * @param       String    $subject      Nombre.
     * @param       Boolean   $has_disclaimer   Indica creara un disclaimer. Default FALSE
     * @return      String    Botonos a ser vistos dentro de los grid.
     * @author      Jesus Farias
     *              Juan C. Lopez
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 29/08/2012 18:45:59
     * */
    private function makeGridButtons($grid_type, $subject, $has_disclaimer = FALSE) {
        $grid_id = $grid_type . $this->operation_id;
        $buttons_columns = '';

        //Evaluamos si existe id de la operacion en curso.
        if(empty($this->operation_id))
            return FALSE;
        
        //Obtenemos las operaciones hijas.
        $operations = $this->ci->metadata_model->getChildrenByOperation($this->operation_id, 'Button', TRUE, 'parent');

        //Valiamos que la operacion tenga hijas.
        if (empty($operations))
            return FALSE;

        foreach ($operations as $operation) {

            //Verificamos si la operacion hija es visible.
            if (!empty($operation->visible)) {

                $view_params['operation_name'] = $operation->_name;
                $view_params['icon'] = BASE_ICONS . $operation->icon;

                //Indicamos la funcion JS a ser llamada por la imagen.
                //Validamos es un boton de tipo borrar.
                if ($operation->visual_type == 'Button_D' && empty($has_disclaimer))
                    $view_params['onclick'] =
                        "confirmAction(\'{$operation->url}\', \'{0}\', \'$grid_id\', \'$subject\', \'delete\')";

                //Validamos si es un boton desactivar
                elseif ($operation->visual_type == 'Button_DI' && empty($has_disclaimer))
                    $view_params['onclick'] =
                        "confirmAction(\'{$operation->url}\', \'{0}\', \'$grid_id\', \'$subject\', \'deactivate\')";

                //Validamos si es un boton activar
                elseif ($operation->visual_type == 'Button_AC')
                    $view_params['onclick'] =
                        "confirmAction(\'{$operation->url}\', \'{0}\', \'$grid_id\', \'$subject\', \'activate\')";

                //Si no se cumple ningua de las validaciones anteriores reemplazamos el Contenedor centrar
                else
                    $view_params['onclick'] = "getCenterContent(\'{$operation->url}\', \'{0}\', event)";

                $buttons_columns .= $this->ci->load->view(
                    $this->ci->config->item('dyna_views_grid_snippet_path') . 'grid_button.js.php', $view_params, TRUE
                );
            }
        }
        return $buttons_columns;
    }

    /**
     * <b>Method:   buildButtons($operation_children, $cancel_button, $replace) </b>
     * Crea un componente ExtJs de Tipo Buttons.
     * @param       Object    $operation_children   Datos de la operacion hija.
     * @param       Integer   $cancel_button        Indica si se emprime el boton cancelar.
     * @param       Array     $replace   Si contiene el valor window cierra la ventana.
     * @return      String    Componente en formato JSON.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 17:22
     * */
    private function buildButtons($operation_children, $cancel_button, $replace) {
        $buttons = '[';
        $colon = '';

        $button_params = array(
            'operation_id' => $this->operation_id, //Identificador de la operación Padre
            'replace' => $replace, //Indica donde se reemplaza el componenete de la operación padre.
        );

        //Recorremos los datos de las operaciones Hijas.
        foreach ($operation_children as $operation) {

            $button_params['operation_name'] = $operation->_name;   //Nombre de la operación Actual.
            $button_params['operation_icon'] = $operation->icon;    //Icono de la operación Actual.
            $button_params['operation_url'] = $operation->url;      //URL de la operacion Actual.

            $button = $colon . $this->ci->load->view(
                    $this->ci->config->item('dyna_views_form_snippet_path') . 'form_action_buttons.js.php', $button_params, TRUE
            );
            $colon = ',';
            $buttons .= $button;
        }

        if ($cancel_button == '1')
            $buttons .= $colon . $this->ci->load->view(
                    $this->ci->config->item('dyna_views_form_snippet_path') . 'form_reset_button.js.php', $button_params, TRUE
            );

        $buttons .= ']';

        return $buttons;
    }

    /**
     * <b>Method:   buildToolbarButtons($buttons)</b>
     * Coloca los botones de los toolbars
     * @param   Array    $buttons   Arreglo con los datos de las operaciones a ser llamadas por los diferentes botones del toolbar.
     * @return      String   Botones construidos en formato JSON.
     * @author      Jesus Farias
     *              Juan C. Lopez
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1  29/08/2012 17:00
     * */
    private function buildToolbarButtons($buttons /* , $align = 'rigth' */) {
        if (empty($buttons)) {
            return FALSE;
        } else {
            $items = '[';
            $colon = $separator = '';
            foreach ($buttons as $button) {
                if (!empty($button->visible)) {
                    $item = $colon . $separator . "{
                        id: 'btn_tbar_{$this->operationData->id}_{$button->id}',
                        text: '$button->_name',
                        icon: BASE_ICONS + '$button->icon',
                        handler:  function(btn){
                            btn.disable();
                            Ext.Ajax.request({
                                url: '$button->url',
                                method: 'GET',
                                success: function(response){ eval(response.responseText); btn.enable(); },
                                failure: function(){
                                    Ext.Msg.alert('" .
                                        $this->ci->lang->line('message_failure_title') . "','" .
                                        $this->ci->lang->line('message_failure') . "'
                                    );
                                    btn.enable();
                                }
                            });
                        }
                    }";
                    $colon = ',';
                    $separator = "'-',";
                    $items .= $item;
                }
            }
            $items .= ']';

            return $items;
        }
    }

    /**
     * <b>Method:   processForm() </b>
     * Ejecuta las validaciones de campo configuradas en el servidor.
     * @param       mixed   $params   Arreglo con las parametros descritos a continucación:
     *                  array     [prebuild_validation]   Proporciona las validaciones preconstruidas. Default FALSE
     *                  Boolean   [type]   Indica si la validacion es de reportes.
     *                  Boolean   [flag_type]   ********.
     *                  Boolean   [self]    Indica si los campos a validar pertenecen a la misma operación.
     * @return      Array     Arreglo con el resultado de las validaciones.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 date
     * */
    public function processForm($params = NULL) {
        //Estraemos los parametos de a ser empleados en la vista.
        extract($params);

        //Arreglo formatuado a las necesidades de un request de ajax.
        $output = array();

        //Preguntamos si existen validaciones preconstruidas.
        if (empty($prebuild_validation)) {

            //Verificamos si los campos a validar pertenecer a la operacion padre o a la misma operación.
            $parent_id = (empty($self)) ? $this->operationData->operation_id : FALSE;

            //Evaluamos si los datos a consultar son de reportes o no.
            if (empty($type))
                $this->fieldsDb = $this->ci->metadata_model->getFieldsByOperation($this->operation_id, $parent_id);
            else
                $this->fieldsDb = $this->getFieldsByReporte($type, 'form', $flag_type);
        }else
            $this->fieldsDb = $prebuild_validation;

        //Arreglo que contiene los datos a ser guardados.
        $data_insert = array();

        foreach ($this->fieldsDb as $data) {
            if ($data->ext_component != 'label') {
                //$data_ins[$dat->server_name] = self::$CI->input->post($dat->server_name);
                $data_insert[$data->server_name] = $this->ci->input->post($data->server_name);

                //definimos el label
                $label = $this->getLabel($data);

                //Llamamos al form validation de CI.
                $this->ci->form_validation->set_rules($data->server_name, $label, $data->validation);
            }
        }

        if ($this->ci->form_validation->run() == FALSE) {
            $output['result'] = false;
            $output['msg'] = preg_replace('[\n|\r|\n\r]', ' ', validation_errors());
        } else {
            $output['data'] = $data_insert;
            $output['result'] = true;
        }

        return $output;
    }

    /**
     * <b>Method:   buildPanel($params) </b>
     * Crea un componente ExtJs de Tipo Panel
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del grid.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  mixed      [data]      Configuraciones y panales a ser mostrados entro de tipo de panel.
     *                              array(
     *                                  'panelType' => '1A',
     *                                  'type' => $this->load->view(view_path,TRUE), 
     *                                  'p1' => 'panel_'
     *                              )
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [returnView]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al grid.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 18:53
     * */
    public function buildPanel($params) {

        //@todo validar el tipo de panel.
        extract($params);

        //Tipos de Paneles
        //1A,
        //2A, 2B,
        //3A, 3B, 3C, 3D

        $replace = $this->setReplaceContent('panel', $replace);

        $my_view = $data['panelType'];

        $view_data = array(
            'url' => $this->operation_url,
            'panelTitle' => $title,
            'name' => $name,
            'panelData' => $data,
            'tbar' => $tbarButton,
            'bbar' => $bbarButton,
            'extraData' => '',
            'replace' => $replace,
            'opId' => $this->operation_id,
            'scriptTags' => $scriptTags,
            'panelType' => $data['panelType'],
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        //Validamos si se debe retornar la vista.
        if (!empty($returnView) || !empty($this->allways_return_views)) {
            //$preBuildView = self::$CI->load->view("generals/panels/$my_view.js.php", $view_data, TRUE);
            $preBuildView = $this->ci->load->view(
                $this->ci->config->item('dyna_views_panel_path') . "$my_view.js.php", $view_data, TRUE
            );
            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => "panel_$this->operation_id");
            else
                return $preBuildView;
        }

        //self::$CI->load->view("generals/panels/$my_view.js.php", $view_data);
        $this->ci->load->view($this->ci->config->item('dyna_views_panel_path') . "$my_view.js.php", $view_data);
    }

    /**
     * <b>Method:   buildTablePanel($data) </b>
     * Crea un componente ExtJs de Tipo TablePanel
     * @param       Array    $data    Datos a ser mostrados dentro de la tabla.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 18:58
     * */
    public function buildTablePanel($data) {
        $this->fieldDb = $this->ci->metadata_model->getFieldsByOperation($this->operation_id);

        if (empty($this->fieldsDb))
            return FALSE;

        //self::$CI->table->set_heading('<b>Etiqueta</b>', '<b>Valor</b>');
        $this->ci->table->set_heading('<b>Etiqueta</b>', '<b>Valor</b>');

        //Recorremos campos de la operacion.
        foreach ($this->fieldsDb as $field) {
            $field_label = '<b>' . $this->getLabel($field) . ':</b>';
            $field_value = $data[$field->server_name];
            //self::$CI->table->add_row($etiqueta_campo, $valor_campo);
            $this->ci->table->add_row($field_label, $field_value);
        }

        $tmpl = array(
            'table_open' =>
            '<table align="center" border="0" style=" margin-top:3px; font:normal 12px Arial; background-color:#666666;">',
            'heading_row_start' => '<tr style="font-size:16px; background-color:#666666; color:#FFFFFF;">',
            'heading_row_end' => '</tr>',
            'heading_cell_start' => '<th align="left" style="padding:5px;" >',
            'heading_cell_end' => '</th>',
            'row_start' => '<tr style="background-color:#FFFFFF;">',
            'row_end' => '</tr>',
            'cell_start' => '<td align="left" style="padding:5px;">',
            'cell_end' => '</td>',
            'row_alt_start' => '<tr style="background-color:#66CCFF;">',
            'row_alt_end' => '</tr>',
            'cell_alt_start' => '<td align="left" style="padding:5px";>',
            'cell_alt_end' => '</td>',
            'table_close' => '</table>'
        );

        //self::$CI->table->set_template($tmpl);
        $this->ci->table->set_template($tmpl);

        //$panelHtml = preg_replace("[\n|\r|\n\r]", '', self::$CI->table->generate());
        $panel_html = preg_replace('[\n|\r|\n\r]', '', $this->ci->table->generate());
        return $panel_html;
    }

    /**
     * <b>Method:   buildPanelHtml($params) </b>
     * Crea un componente ExtJs de Tipo PanelHtml
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del panelHtml.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  mixed      [data]     Datos pertenecientes al modulo ejecutado.
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [returnView]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al panelHTML.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 19:03
     * */
    public function buildPanelHtml($params) {
        extract($params);

        $replace = $this->setReplaceContent('PanelHtml', $replace);

        $view_data = array(
            'url' => $this->operation_url,
            'panelTitle' => $title,
            'name' => $name,
            'pHtml' => $data,
            //'tbar' => $tbarButton,
            //'bbar' => $bbarButton,
            'extraOptions' => $extraOptions,
            'replace' => $replace,
            'opId' => $this->operation_id,
            'scriptTags' => $scriptTags,
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        if (!empty($returnView) || !empty($this->allways_return_views)) {
            //$preBuildView = self::$CI->load->view("generals/panelHtml.js.php", $view_data, true);
            $preBuildView = $this->ci->load->view(
                $this->ci->config->item('dyna_views_path') . 'panelHtml.js.php', $view_data, TRUE
            );
            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => "PanelHtml_$this->operation_id");
            else
                return $preBuildView;
        }

        //self::$CI->load->view("generals/panels/panelHtml.js.php", $view_data);
        $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'panelHtml.js.php', $view_data);
    }

    /**
     * <b>Method:   buildMessageBox($params) </b>
     * Crea un componente ExtJs de Tipo MessageBox
     * @param   Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *             string  [title]    Titulo del MessageBox.
     *             string  [type]     Tipo de MessageBox. Valores prompt, confirm, alert.
     *             string  [msg]     Mensaje a ser visualizado en el Componente.
     *             String  [callback] Funcion Js a ser ejecutada.
     *             string  [buttons]  Botones a ser mostrados. Valores OK(default), CANCEL, OKCANCEL, YESNO, YESNOCANCEL.
     *             string  [icon]     Icono a ser visualizado en el componente. Valores WARNING(default), QUESTION, INFO, ERROR.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 19:15
     * */
    public function buildMessageBox($params) {
        extract($params);

        //Valimos los valores permitidos de buttons, si son invalidos colocamos uno por defecto.
        $patter = '/^(WARNING|INFO|ERROR|QUESTION)$/';
        if (!preg_match($patter, strtoupper($icon))) {
            $icon = 'WARNING';
        }

        //Valimos los valores permitidos de buttons, si son invalidos colocamos uno por defecto.
        $patter = '/^(CANCEL|OK|OKCANCEL|YESNOCANCEL)$/';
        if (!preg_match($patter, strtoupper($buttons))) {
            $buttons = 'OK';
        }

        $view_data['type'] = strtolower($type);
        $view_data['msg'] = $msg;
        $view_data['title'] = $title;
        $view_data['icon'] = $icon;
        $view_data['buttons'] = $buttons;
        $view_data['callback'] = $callback;

        //self::$CI->load->view("generals/messageBox.js.php", $view_data);
        $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'messageBox.js.php', $view_data);
    }

    /**
     * <b>Method:   buildOrganizationChart($params) </b>
     * Contruye un Organigrama
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del grid.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  mixed      [data]     Datos pertenecientes al modulo ejecutado.
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [returnView]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al grid.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 18:27
     * */
    public function buildOrganizationChart($params) {

        extract($oarams);

        $replace = $this->setReplaceContent('panel_organigrama', $replace);
        //@todo Refactorizar bien esto.
        $tbar_button = $bbar_button = '';
        //echo $this->operation_id;
        $operation_tbar_buttons = $this->getChildsByOperationId('35', 'Button', true, 'tbar');
        $operation_bbar_buttons = $this->getChildsByOperationId($this->operation_id, 'Button', true, 'bbar');
        if (!empty($operation_tbar_buttons))
            $firstSep = ($align == 'left') ? '' : '->';
        $items = "['$firstSep',";
        $colon = $separator = '';
        foreach ($operation_tbar_buttons as $button) {
            $item = $colon . "'$separator', {
                text: '$button->_name',
                icon: BASE_ICONS + '$button->icon',
                handler:  function(){
                        Ext.Ajax.request({
                            url: '$button->url',
                            method: 'GET',
//                            success: function(response){ eval(response.responseText); },
//                            failure: function(){ Ext.Msg.alert('Falla','fallo la respuesta AJAX'); }
                        })
                    }
            }";
            $colon = ',';
            $separator = '-';
            $items .= $item;
        }
        $items .= ']';

        $tbar_button = $items;
        if (!empty($operation_bbar_buttons))
            $bbar_button = $this->buildToolbarButtons($operation_bbar_buttons, 'left');

        $view_data = array(
            'title' => $title,
            'name' => $name,
            'results' => $data,
            'tbar' => $tbar_button,
            'bbar' => $bbar_button,
            'opId' => '35',
            'url' => $this->operation_url,
            'scriptTags' => $scriptTags,
            'replace' => $replace,
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        if (!empty($returnView) || !empty($this->allways_return_views)) {
            //$preBuildView = self::$CI->load->view("generals/organigrama.js.php", $view_data, true);
            $preBuildView = $this->ci->load->view(
                $this->ci->config->item('dyna_views_path') . 'organigrama.js.php', $view_data, TRUE
            );
            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => 'panel_organigrama');
            else
                return $preBuildView;
        }
        $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'organigrama.js.php', $view_data);
    }

    /**
     * <b>Method:   buildGeneralDisclaimer($param) </b>
     * Metodo que genera un disclaimer general
     * @param       Array    $param     ********.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 20:00
     * */
    function buildGeneralDisclaimer($param) {

        //@todo Preguntar a reynaldo la diferencia entre disclaimer y el parametro general.
        //$operation_parent_children = $this->getChildsByOperationId($this->operation_id, 'Button', true, 'general');
        $operation_parent_children = $this->ci->metadata_model->getChildrenByOperation(
            $this->operation_id, 'Button', TRUE, 'general'
        );

        $param['opId'] = $this->operation_id;
        $param['url'] = $operation_parent_children[0]->url;
        $param['buttonName'] = $operation_parent_children[0]->_name;
        $param['icon'] = BASE_ICONS . $operation_parent_children[0]->icon;
        $param['parentId'] = $this->operationData->operation_id;
        //self::$CI->load->view("generals/generalDisclaimer.js.php", $param);
        $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'generalDisclaimer.js.php', $param);
    }

    /**
     * <b>Method: getFieldsDB()</b>
     * Getter del atributo fieldsDB.
     * @return		Array Arreglo de datos con los campos solicitados.
     * @author		Mirwing Rosales
     * @version		v-1.0 19/10/11 04:10 PM
     * */
    function getFieldsDB() {
        return $this->fieldsDb;
    }

    /* <b>Method: buildUploadWindow()</b>
     * Construye el window desde donde se manejan los archivos.
     * @param		$dataUpload
     * @return
     * @author		Juan Carlos Lopez
     * @version		v1.0 03/11/11 10:50 AM
     * */

    function buildUploadWindow($dataUpload) {
        //self::$CI->load->view("generals/upload.js.php", $dataUpload);
        $this->ci->load->view($this->ci->config->item('dyna_views_path') . 'upload.js.php', $dataUpload);
    }

    /**
     * <b>Method:   buildFields($params)</b>
     * Realiza el llamado a los diferentes metodos para crear cada tipo de campo.
     * @param       Object   $fields_metadata  Metadata de los campos a ser renderizados.
     * @param       Array    $data  Datos de los campos. Default FALSE.
     * @param       Array    $dynamic_flag  Default FALSE
     * @author      Jesus Farias
     *              Juan C. Lopez
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 29/08/2012 10:00
     * */
    function buildFields($fields_metadata, $data = FALSE, $dynamic_flag = FALSE) {
        $field = $colon = '';
        $str_fields = '[';
        $field_conf = array();
        //var_dump($fields_metadata);
        if (empty($fields_metadata))
            return $str_fields.=']';
        else {
            $c = 0;

            //recorremos la metadata de los campos.
            foreach ($fields_metadata as $field_metadata) {
                $server_name = $field_metadata->server_name;
                $field_conf['server_name'] = $server_name;

                //Determinamos si el campo de tipo textfield posee mas de 27 caracteres, de ser asi lo comvertimos a textarea.
                if ($field_metadata->ext_component == 'textfield'
                    && !empty($data[$server_name]) && strlen($data[$server_name]) > 27)
                    $field_metadata->ext_component = 'textarea';

                //Agregamos el valor del campo si existe
                if (!empty($data[$server_name])) {
                    $field_conf['fieldValue'] = "value: '$data[$server_name]',";
                    $field_conf['rawValue'] = $data[$server_name];
                } else if (!empty($dynamic_flag)) {
                    $field_conf['fieldValue'] = "value: '$field_metadata->valor',";
                    $field_conf['rawValue'] = $field_metadata->valor;
                    $field_conf['server_name'] = "valor[$c]";
                } else {
                    $field_conf['fieldValue'] = '';
                    $field_conf['rawValue'] = '';
                }

                //Convertimos en arreglo todas las validaciones pertenecientes al campo.
                $validaArray = explode('|', $field_metadata->validation);

                //Evaluamos si tiene un blank_text y si tiene el campo es permite estar vacio.
                if (in_array('required', $validaArray)) {
                    $field_conf['allowBlank'] = 'allowBlank:false,';
                    $field_conf['blankText'] = "blankText:  'El campo $field_metadata->_label es obligatorio',";
                } else {
                    $field_conf['allowBlank'] = '';
                    $field_conf['blankText'] = '';
                }

                $field_conf['maxLenght'] = '';
                $field_conf['maxLenghtText'] = '';
                $field_conf['minLenght'] = '';
                $field_conf['minLenghtText'] = '';

                foreach ($validaArray as $value) {
                    $toSearch = substr($value, 0, 10);
                    if ($toSearch == 'max_length') {

                        $num = preg_replace('/\D/', '', $value);
                        $field_conf['maxLenght'] = "maxLength: $num,";
                        $field_conf['maxLengthText'] = "maxLengthText:  'El campo $field_metadata->_label no puede exceder los $num caracteres',";
                    }
                    if ($toSearch == 'min_length') {
                        $num2 = preg_replace('/\D/', '', $value);
                        $field_conf['minLenght'] = "minLength: $num2,";
                        $field_conf['minLenghtText'] = "minLengthText:  'El campo $field_metadata->_label debe tener al menos $num2 caracteres',";
                    }
                }

                if (!empty($field_metadata->_label) AND empty($field_metadata->read_only)) {
                    $field_conf['emptyText'] = "emptyText:'$field_metadata->_label',";
                } else {
                    $field_conf['emptyText'] = '';
                }

                $validVType = $this->ci->metadata_model->getVtypeValidations();
                $coinciden = array_intersect($validaArray, $validVType);
                $vType = array_shift($coinciden);

                if (!empty($vType)) {
                    $field_conf['vtype'] = "vtype:'$vType'";
                } else {
                    $field_conf['vtype'] = '';
                }


                //Evaluamos si tiene ayuda.
                if (empty($field_metadata->help))
                    $field_conf['tooltip'] = '';
                else
                    $field_conf['tooltip'] = "listeners: {
                        render: function(c){
                            new Ext.ToolTip({
                                target: c.getEl(),
                                anchor: 'left',
                                trackMouse: false,
                                html: '$field_metadata->help'
                            });
                        },
                    },";

                //Evaluamos el tipo de componente defino en la tabla category.
                switch ($field_metadata->ext_component) {
                    case 'textfield' :
                        $field = $this->buildTextfield($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'htmleditor':
                        //@todo No funciona.
                        $field = $this->buildHtmlEditor($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'checkbox' :
                        $field = $this->buildCheckbox($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'phonefield' :
                        $field = $this->buildPhoneField($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'floatfield' :
                        $field = $this->buildFloatField($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'textarea' :
                        $field = $this->buildTextarea($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'hidden' :
                        $field = $this->buildHidden($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'datefield' :
                        $field = $this->buildDatefield($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'combobox' :
                        $field = $this->buildCombobox($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'itemselector' :
                        $field = $this->buildItemselector($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'fileupload' :
                        //Juan Carlos
                        $field = $this->buildFileupload($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'password' :
                        $field = $this->buildPasswordfield($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                    case 'label' : $field = $this->buildLabel($field_metadata, $this->operation_id);
                        break;
                    case 'datetime' :
                        $field = $this->buildDateTimeField($field_metadata, $this->operation_id, $field_conf, $dynamic_flag);
                        break;
                }

                $str_fields .= $colon . $field;
                $colon = ',';
                if (!empty($dynamic_flag)) {
                    $field_conf['server_name'] = "hidden[$c]";
                    $field_conf['fieldValue'] = "value: '$field_metadata->valor_id',";
                    $str_fields.=$colon . ($this->buildHidden($field_metadata, $this->operation_id, $field_conf, $dynamic_flag));
                }
                $c++;
            }
            $str_fields.=']';
            return $str_fields;
        }
    }

    /**
     * <b>Method:   buildHtmleditor($field_metadata, $operation_id, $config) </b>
     * Construye un componente ExtJS HtmlEditor.
     * @param       Object    $field_metadata   Objeto con la metadata de los campos.
     * @param       Integer   $operation_id     Identificador de la operacion en curso.
     * @param       paramType1    $config   descripcion1
     * @return      String    Componente en formato JSON
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 16:12:00
     * */
    function buildHtmlEditor($field_metadata, $operation_id, $config) {
        $field_id = $this->formatFieldId($field_metadata);

        //Definimos el Label del campo.
        $label = $this->getLabel($field_metadata);

        extract($config);
        $field = "{
            xtype:      'htmleditor',
            id:         '$field_id',
            fieldLabel: '$label',
            name:       '$server_name',
            width:      600,
            height:     300,
            $fieldValue
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden),
            readOnly:   Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $emptyText
            $tooltip
            $vtype
        }";

        return $field;
    }

    /**
     * <b>Method:   buildCheckBox($field_metadata, $operation_id, $config)</b>
     * Construye un componente extjs checkbox
     * @param       Object      $field_metadata   Metadata de los campo ser creado.
     * @param       Integer     $operation_id   Identificador de la operacion en curso.
     * @param       Array       $config   datos de configuracion del campo.
     * @return      String    Componente en formato JSON.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 16:53:00
     * */
    function buildCheckbox($field_metadata, $operation_id, $config) {
        $field_id = $this->formatFieldId($field_metadata);

        //Definimos el Label del campo.
        $label = $this->getLabel($field_metadata);

        extract($config);

        //Preguntamos si tiene el valor actual.
        if (!empty($fieldValue))
            $checked = 'checked: true,';
        else {
            $checked = 'checked: false,';
            $fieldValue = 'value: 1,';
        }

        $field = "{
            xtype:      'checkbox',
            id:         '$field_id',
            fieldLabel: '$label',
            name:       '$server_name',
            $checked
            $fieldValue
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden),
            readOnly:   Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $tooltip
            validateField:  true
        }";

        return $field;
    }

    /**
     * <b>Method:   buildTextfield($field_metadata, $operation_id, $config, $dynamic_flag = false) </b>
     * Construye componentes ExtJS de tipo textField
     * @param       Object        $field_metadata Metadata del campo a ser renderizado.
     * @param       Integer       $operation_id   Identificadior de la operacion en curso.
     * @param       Array         $config         Configuraciones de campo.
     * @param       paramType1    $dynamic_flag   ****** Default FALSE
     * @return      String    Componente ExtJS.textField en formato JSON.
     * @author      Jesus Farias
     *              Juan C. Lopez
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 15:00
     * */
    function buildTextfield($field_metadata, $operation_id, $config, $dynamic_flag = FALSE) {
        //Creamos el id del campo.
        $field_id = $this->formatFieldId($field_metadata);

        //Definimos el Label del campo.
        //$field_label = (!empty($dynamic_flag)) ? (($field_metadata->valor_anio === 'No Aplica') ? 'Valor \u00DAnico' : $field_metadata->valor_anio . ' / ' . $field_metadata->periodo_nombre) : $field_metadata->_label;
        $field_label = $this->getLabel($field_metadata);

        extract($config);

        $field = "{
            xtype:'textfield',
            id : '$field_id',
            fieldLabel:'$field_label',
            name:'$server_name',
            width: 200,
            $fieldValue
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden),
            readOnly:   Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $emptyText
            $maxLenght
            $maxLenghtText
            $minLenght
            $minLenghtText
            $tooltip
            $vtype
	}";

        //die('<pre>' . print_r($field, TRUE) . '</pre>');
        return $field;
    }

    /**
     * <b>Method:   buildPhoneField($field_metadata, $operation_id, $config, $dynamicFlag = false) </b>
     * Descripcion_leve
     * @param       Object     $field_metadata   Metadata de los campos pertenecientes a la operacion.
     * @param       Integer    $operation_id     Identificador de la operacion en curso.
     * @param       Array      $config           Configuraciones por defecto de campo de la operacion.
     * @param       Boolean    $dynamic_flag     ******* Default FALSE
     * @return      String    Con un textField Formateado para telefonos.
     * @author      Jesus Farias
     *              Juan Lopez
     *              Reynaldo Rojas
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 16:28:00
     * */
    function buildPhoneField($field_metadata, $operation_id, $config, $dynamic_flag = false) {
        $field_id = $this->formatFieldId($field_metadata);

        //obtenemos la etiqueta del campo.
        //$field_label = (!empty($dynamic_flag)) ? (($field_metadata->valor_anio === 'No Aplica') ? 'Valor \u00DAnico' : $field_metadata->valor_anio . ' / ' . $field_metadata->periodo_nombre) : $field_metadata->_label;
        $field_label = $this->getLabel($field_metadata);

        //Extraemos la configuraciones generales del campo.
        extract($config);

        // Si esta en modo de visualizacion se debe generar una mascara para mostrar el telefono
        if (!empty($field_metadata->read_only)) {
            if (preg_match("/^(02|04)\d{9}((x)\d{1,4})?$/", $rawValue)) {
                $extension = substr($rawValue, 11);
                $extension = (!empty($extension)) ? ' ' . $extension : '';
                $rawValue = '(' . substr($rawValue, 0, 4) . ') '
                    . substr($rawValue, 4, 3) . '.' . substr($rawValue, 7, 4) . $extension;
                $fieldValue = "value: '$rawValue',";
                unset($vtype);
            }
        }

        //Definimos el campo.
        $field = "{
            xtype:'textfield',
            id: '$field_id',
            fieldLabel:'$field_label',
            name:'$server_name',
            width: 200,
            $fieldValue
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden),
            readOnly:   Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $emptyText
            $tooltip
            $vtype
        }";

        return $field;
    }

    /**
     * <b>Method:   buildFloatField($field_metadata, $operation_id, $config)</b>
     * Construye un componente extjs checkbox
     * @param       Object      $field_metadata   Metadata de los campo ser creado.
     * @param       Integer     $operation_id   Identificador de la operacion en curso.
     * @param       Array       $config   datos de configuracion del campo.
     * @return      String    Componente en formato JSON.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 16:53:00
     * */
    function buildFloatField($field_metadata, $operation_id, $config, $dynamic_flag = false) {
        $field_id = $this->formatFieldId($field_metadata);

        //obtenemos la etiqueta del campo.
        //$field_label = (!empty($dynamic_flag)) ? (($field_metadata->valor_anio === 'No Aplica') ? 'Valor \u00DAnico' : $field_metadata->valor_anio . ' / ' . $field_metadata->periodo_nombre) : $field_metadata->_label;
        $field_label = $this->getLabel($field_metadata);

        //Extraemos la configuraciones generales del campo.
        extract($config);

        // Si esta en modo de visualizacion se debe generar una mascara para mostrar el numero
        if (!empty($field_metadata->read_only)) {
            $rawValue = number_format($rawValue, 2, ',', '.');
            $fieldValue = "value: '$rawValue',";
            unset($vtype);
        }

        //Definimos el campo.
        $field = "{
            xtype:'textfield',
            id: '$field_id',
            fieldLabel:'$field_label',
            name:'$server_name',
            width: 200,
            $fieldValue
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden),
            readOnly:   Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $emptyText
            $maxLenght
            $maxLenghtText
            $minLenght
            $minLenghtText
            $tooltip
            $vtype
        }";

        return $field;
    }

    /**
     * <b>Method:   buildPasswordField($field_metadata, $operation_id, $config, $dyna_flag = FALSE) </b>
     * Crea un componente ExtJs de Tipo PasswordField
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $operation_id      Identificador de la operacion en curso.
     * @param       Array     $config   Configuraciones generales del campo en cuestion.
     * @param       Boolean   $dyna_flag   Indica si el formulario es sobre campo dinamico. Default FALSE
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 17:26
     * */
    function buildPasswordfield($field_metadata, $operation_id, $config, $dynamic_flag = FALSE) {
        $field_id = $this->formatFieldId($field_metadata);

        //obtenemos la etiqueta del campo.
        //$field_label = (!empty($dynamic_flag)) ? (($field_metadata->valor_anio === 'No Aplica') ? 'Valor \u00DAnico' : $field_metadata->valor_anio . ' / ' . $field_metadata->periodo_nombre) : $field_metadata->_label;
        $field_label = $this->getLabel($field_metadata);

        //Extramos configuraciones generales del campo.
        extract($config);

        //definimos el campo.
        $field = "{
            xtype:'textfield',
            id: '$field_id',
            fieldLabel:'$field_label',
            name:'$server_name',
            width: 200,
            inputType: 'password',
            $fieldValue
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden),
            readOnly:   Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $emptyText
            $maxLenght
            $maxLenghtText
            $minLenght
            $minLenghtText
            $tooltip
            $vtype
        }";

        return $field;
    }

    /**
     * <b>Method:   buildTextarea($field_metadata, $operation_id, $config)</b>
     * Construye un componente extjs textArea
     * @param       Object      $field_metadata   Metadata de los campo ser creado.
     * @param       Integer     $operation_id   Identificador de la operacion en curso.
     * @param       Array       $config   datos de configuracion del campo.
     * @return      String    Componente en formato JSON.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 16:53:00
     * */
    function buildTextarea($field_metadata, $operation_id, $config) {
        $field_id = $this->formatFieldId($field_metadata);

        //obtenemos la etiqueta del campo.
        $field_label = $this->getLabel($field_metadata);

        //Extraemos las configuraciones generales del campo.
        extract($config);

        $server_value = preg_replace('/\r|\n|\n\r/', ' ', addslashes($rawValue));

        //Preguntamos si el campo tiene valor.
        if (!empty($server_value))
            $fieldValue2 = "value: '$server_value',";

        //Creamos el campo.
        $field = "{
            xtype:'textarea',
            id:'$field_id',
            fieldLabel:'$field_label',
            name:'$server_name',
            width: 200,
            height: 83,
            autoScroll:true,
            $fieldValue2
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden),
            readOnly:  Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $emptyText
            $maxLenght
            $maxLenghtText
            $minLenght
            $minLenghtText
            $tooltip
            $vtype
        }";

        return $field;
    }

    /**
     * <b>Method:   buildHidden($field_metadata, $operation_id, $config, $dyna_flag = FALSE) </b>
     * Crea un componente ExtJs de Tipo Hidden
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $operation_id      Identificador de la operacion en curso.
     * @param       Array     $config   Configuraciones generales del campo en cuestion.
     * @param       Boolean   $dyna_flag   Indica si el formulario es sobre campo dinamico. Default FALSE
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 15:10:00
     * */
    function buildHidden($field_metadata, $operation_id, $config, $dyna_flag = FALSE) {
        $field_id = $this->formatFieldId($field_metadata);

        //Extraemos las configuraciones enerales del campo.
        extract($config);

        //Creamos el campo.
        $field = "{
            xtype:'hidden',
            id: '$field_id',
            $fieldValue
            name:'$server_name'
        }";

        return $field;
    }

    /**
     * <b>Method:   buildDateField($field_metadata, $operation_id, $config) </b>
     * Crea un componente ExtJs de Tipo DateField
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $operation_id      Identificador de la operacion en curso.
     * @param       Array     $config   Configuraciones generales del campo en cuestion.
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 17:15:21
     * */
    function buildDatefield($field_metadata, $operation_id, $config) {
        $field_id = $this->formatFieldId($field_metadata);

        //obtenemos la etiqueta del campo.
        $field_label = $this->getLabel($field_metadata);

        //Extraemos las configuraciones generales del campo.
        extract($config);

        //Creamos el campo.
        $field = "{
            xtype:'datefield',
            fieldLabel:'$field_label',
            id: '$field_id',
            name:'$server_name',
            width: 100,
            $fieldValue
            disabled: Boolean($field_metadata->disabled),
            hidden:   Boolean($field_metadata->hidden),
            readOnly: Boolean($field_metadata->read_only),
            $allowBlank
            $blankText
            $emptyText
            $tooltip
            format: 'd/m/Y',
            $vtype
        }";

        return $field;
    }

    function buildFileupload($field, $opId, $config) {
        extract($config);
        $botonId = 'BotonUpl_' . $opId . '_' . $field->id;
        $fielId = $this->formatFieldId($field);
        $fileType = 'upload'; //detail
//        $rawValue = $field_conf['rawValue'];
        $campo = "{
                    xtype:'hidden',
                    readOnly:   Boolean(1),
                    id: '$fielId',
                    value: '$rawValue',
                    name:'$server_name'
                    },{
                    xtype:      'button',
                    fieldLabel: '$field->_label',
                    id:         '$botonId',
                    text:       'Subir archivo',
                    icon:       BASE_ICONS + 'photos.png',
                    handler:    function(btn){
                        //btn.disable();
                        var galId = Ext.getCmp('$fielId').value;
                        Ext.Ajax.request({
                            url: BASE_URL + 'adm_upload/upload/winUpload',
                            method: 'GET',
                            params: {
                                fieldId:$field->id,
                                opId:$opId,
                                fileType:'$fileType',
                                gallerie_id: galId,
                                dom_gallerie_id : '$fielId'
                            },
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
                        //btn.enable();
                    }
		}";
        return $campo;
    }

    /**
     * <b>Method:   buildComboBox($field_metadata, $operation_id, $config, $dyna_flag = FALSE) </b>
     * Crea un componente ExtJs de Tipo ComboBox
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $operation_id      Identificador de la operacion en curso.
     * @param       Array     $config   Configuraciones generales del campo en cuestion.
     * @param       Boolean   $dyna_flag   Indica si el formulario es sobre campo dinamico. Default FALSE
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 date
     * */
    function buildCombobox($field_metadata, $operation_id, $config, $dynamic_flag = false) {
        $field_id = (empty($dynamic_flag)) ? "id:'combo_$operation_id" . "_$field_metadata->id'," : '';
//        $field_id = $this->formatFieldId($field_metadata);
//
        //obtenemos la etiqueta del campo.
        //$field_label = (!empty($dynamic_flag)) ? (($field_metadata->valor_anio === 'No Aplica') ? 'Valor \u00DAnico' : $field_metadata->valor_anio . ' / ' . $field_metadata->periodo_nombre) : $field_metadata->_label;
        $field_label = $this->getLabel($field_metadata);

        //Extraemos las configuraciones generales para el campo.
        extract($config);

        //Evaluamos si el campo tiene hijos.
        if (!empty($field_metadata->child))
            $child = ", child:'combo_{$operation_id}_{$field_metadata->child}'";

        // Condicion para habilitar el combobox tipo textarea cuando se encuentra en modo lectura
        unset($autoCreate);
        if (!empty($field_metadata->read_only)) {
            $autoCreate = "autoCreate:     {tag: 'textarea'},";
            unset($emptyText);
        }

        //Creamos y agregamos configuraciones al campo.
        $field = "{
                tpl: '<tpl for=\".\"><div ext:qtip=\"{label}\" class=\"x-combo-list-item\">{label}</div></tpl>',
                xtype:          'combo',
                $field_id
                name:           '$server_name',
                fieldLabel:     '$field_label',
                width:          200,
                lazyRender:     true,
                editable:	false,
                $emptyText
                $allowBlank
                mode:           'local',
                triggerAction:  'all',
                hiddenName:     '$server_name',
                displayField:   'label',
                valueField:     'value',
                $autoCreate
                disabled:       Boolean($field_metadata->disabled),
                bdDisabled:     Boolean($field_metadata->disabled),
                hidden:         Boolean($field_metadata->hidden),
                bdHidden:       Boolean($field_metadata->hidden),
                readOnly:       Boolean($field_metadata->read_only),
                bdReadOnly:     Boolean($field_metadata->read_only),
                store:" . $this->buildOptionsStore($field_metadata);
        $field .= $child;
        $colon = '';
        $field .= ', listeners:{';

        //Si el combo tiene hijos
        if (!empty($field_metadata->child)) {
            $field .= 'select: function(combo, record){loadChildCombo(combo, record);}';
            $colon = ',';
            if (!empty($rawValue))
                $this->pre_filtered_combos[$field_metadata->child] = $rawValue;
        }

        //Preguntamos si existe tooltip. De ser asi lo creamos.
        if (!empty($tooltip)) {
            $field .=$colon . " render: function(c){
                new Ext.ToolTip({
                    target: c.getEl(),
                    anchor: 'left',
                    trackMouse: false,
                    html: '$field_metadata->help'
                });
            }";
        }
        $field .= '}';

        //Indica el valor actual.
        if (!is_null($rawValue) && ($rawValue != ''))
            $field .= ", value:'$rawValue'";
        $field .= '}';

        //return las configuraciones del campo
        return $field;
    }

    /**
     * <b>Method:   buildItemSelector($field_metadata, $operation_id, $config) </b>
     * Crea un componente ExtJs de Tipo ItemSelector
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $operation_id      Identificador de la operacion en curso.
     * @param       Array     $config   Configuraciones generales del campo en cuestion.
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 30/08/2012 17:26:30
     * */
    function buildItemselector($field_metadata, $operation_id, $config) {
        //La variable $field_id no es utilizada dentro del metodo. No la borro porque pùede ser necesaria para futuro
        //$field_id = $field_metadata->server_name . '_' . $operation_id . '_' . $field_metadata->id;
        //obtenemos la etiqueta del campo.
        $field_label = $this->getLabel($field_metadata);

        //Extramos las configuraciones genreales del campo.
        extract($config);

        $block_condition = ($field_metadata->read_only);
        $block_condition2 = !$block_condition;

        //Creamos el campo.
        $field = "{
            xtype: 'itemselector',
            id:             'combo_$operation_id" . "_$field_metadata->id',
            name:           '$server_name',
            fieldLabel:     '$field_label',
            disabled:       Boolean($field_metadata->disabled),
            hidden:         Boolean($field_metadata->hidden),
            hideNavIcons:   Boolean($block_condition) ,
            bdDisabled:     Boolean($field_metadata->disabled),
            bdReadOnly:     Boolean($block_condition),
            bdHidden:       Boolean($field_metadata->hidden),
            dynaViews:      true,
            imagePath:      BASE_ICONS,
            multiselects: [
                {
                    width: 200,
                    id: 'multi_combo_$operation_id" . "_$field_metadata->id',
                    height: 150,
                    droppable: (Boolean($block_condition2)),
                    draggable: (Boolean($block_condition2)),
                    listeners: {
                        render: function(multi) {
                            new Ext.ToolTip({
                                target: multi.el,
                                renderTo: document.body,
                                delegate: 'dl',
                                trackMouse: true,
                                listeners: {
                                    beforeshow: function(tip) {
                                        tip.body.dom.innerHTML = tip.triggerElement.innerHTML;
                                    }
                                }
                            });
                        }
                    },
                    store:" . $this->buildOptionsStore($field_metadata, FALSE, '_multi_from');
        $field.=",
                    displayField:   'label',
                    valueField:     'value',
                },{
                    width: 200,
                    id:    'multi_combo2_$operation_id" . "_$field_metadata->id',
                    height: 150,
                    droppable: (Boolean($block_condition2)),
                    draggable: (Boolean($block_condition2)),
                    displayField:   'label',
                    valueField:     'value',
                    listeners: {
                        render: function(multi) {
                            new Ext.ToolTip({
                                target: multi.el,
                                renderTo: document.body,
                                delegate: 'dl',
                                trackMouse: true,
                                listeners: {
                                    beforeshow: function(tip) {
                                        tip.body.dom.innerHTML = tip.triggerElement.innerHTML;
                                    }
                                }
                            });
                        }
                    },
                store:" . $this->buildOptionsStore($field_metadata, $rawValue, '_multi_to');

        $field.='}
            ]
        }';

        return $field;
    }

    /**
     * <b>Method:   buildLabel($field_metadata, $operation_id) </b>
     * Crea un componente ExtJs de Tipo Label
     * @param       Object    $field_metadata    Metadata del campo en cuestion.
     * @param       Integer   $operation_id      Identificador de la operacion en curso.
     * @return      String    Componente en formato JSON
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 04/08/2012 17:36
     * */
    private function buildLabel($field_metadata, $operation_id) {

        //obtenemos la etiqueta del campo.
        $field_label = $this->getLabel($field_metadata);

        $field = "{
            xtype:'label',
            id: 'label_$operation_id" . "_$field_metadata->id',
            width: 200,
            html:'<p><font style=\"font-weight:bold;\">$field_label</font></p>',
            disabled:   Boolean($field_metadata->disabled),
            hidden:     Boolean($field_metadata->hidden)
        }";
        return $field;
    }

    //@todo Migrar a reporte
    private function getFieldsByReporte($reporte_id, $type, $flag_type = false) {

        if ($type == 'form')
            $filter = " AND ((rc.tipo <= 1 AND rc.placeholder = '1') OR rc.tipo IS NULL)";
        elseif ($type == 'grid')
            $filter = " AND rc.tipo >= 1 ";

        // Condicion para definir el nombre servidor de los campos segun el tipo de reporte
        if (empty($flag_type))
            $n_servidor = "LOWER((CASE WHEN rc.placeholder = '0' THEN dc.nombre WHEN rc.placeholder = '1' THEN rc.valor END)) AS nombre_s,";
        else
            $n_servidor = "LOWER((CASE WHEN rc.placeholder = '0' THEN (CASE WHEN dc.dinamico = '0' THEN de.nombre||'_'||dc.nombre WHEN dc.dinamico = '1' THEN 'CD_'||dc.id END) WHEN rc.placeholder = '1' THEN rc.valor END)) AS nombre_s,";

        $str_query = "(SELECT
						de.esquema ||'.'|| de.nombre ||'.'|| dc.nombre AS nombre_c,
						$n_servidor
						(CASE WHEN rc.placeholder = '0' THEN (dc.id::VARCHAR) WHEN rc.placeholder = '1' THEN (dc.id::VARCHAR)||'_'||rc.valor END) AS id,
						dc.entidad_id,
						(CASE WHEN rc.tipo = 2 THEN rc.etiqueta WHEN rc.tipo IS NULL THEN rc.etiqueta WHEN rc.tipo = 0 THEN
						CASE WHEN rc.operador IS NULL THEN de.nombre||' . '||rc.etiqueta WHEN rc.operador IS NOT NULL THEN de.nombre||' . '||rc.etiqueta|| ' ( ' ||rc.operador || ' )' END
						END) AS etiqueta,
						dc.tipo_campo,
						dc.categoria_id,
						dc.validaciones_cli,
						dc.validaciones_ser,
						rc.chk_periodicidad,
						dc.desc_tipo_dato,
						dc.empty_text,
						dc.mandatorio,
						dc.longitud,
						dc.ayuda,
						(CASE WHEN rc.placeholder = '0' THEN '' WHEN rc.placeholder = '1' THEN dc.nombre END) AS alias_de,
						de.id AS entidad_id,
						de.nombre AS entidad_nombre,
						de.esquema AS esquema,
						de.alias_de AS entidad_alias_de,
						dc.vtype,
						dc.invalid_text,
						dc.blank_text,
						dc.regex
				FROM		estatico.reporte_campos rc
			   LEFT JOIN		dinamico.campos AS dc ON rc.campo_id = dc.id
			  INNER JOIN		dinamico.entidades AS de ON dc.entidad_id = de.id
			       WHERE		rc.reporte_id = $reporte_id
				 AND		rc.eliminado = '0'
				 AND		dc.eliminado = '0'
				 AND		de.eliminado = '0'
				$filter
			    ORDER BY		rc.orden ASC) ";

        $query = self::$CI->db->query($str_query);
        $this->fieldsDb = $query->result();
    }

    /**
     * <b>Method:   renderFormatField($data_type) </b>
     * Determina el tipo de render que tener un campo
     * @param       String    $data_type    Tipo de campo.
     * @return      String    Tipo de render para el campo en formato JSON.
     * @author      Jesus Farias.
     *              Juan C Lopez.
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.1 date
     * */
    function renderFormatField($data_type) {
        unset($renderer);
        switch (strtolower($data_type)) {

            // Condicion de formato para los campos tipo phonefield
            case 'phonefield':
                $renderer = " renderer: function(value) {
                    if((/^(02|04)\d{9}((x)\d{1,4})?$/).test(value)) {
                        extension = value.substr(11);
                        if(extension != '')
                            extension = ' '+extension;
                        return '('+value.substr(0,4)+') '+value.substr(4,3)+'.'+value.substr(7,4)+extension;
                    } else
                        return value;
                }, ";
                break;

            // Condicion de formato para los campos tipo floatfield
            case 'floatfield':
                $renderer = " renderer: function(value) {
                    return number_format(value, 2, ',', '.');
                }, ";
                break;

            // Condicion de formato para los campos tipo datefield
            case 'datefield':
                $renderer = " renderer: function(value) {
                    if((value != '') && (value != undefined) && (value != null)) {
                        value = value.replace('-', '/');
                        value = value.replace('-', '/');
                        date = new Date(value);
                        return date.format('d/m/Y');
                    } else
                        return value;
                }, ";
                break;
            case 'datetime':
                $renderer = " renderer: function(value) {
                    if((value != '') && (value != undefined) && (value != null)) {
                        var cp = value.indexOf('.');
                        if (cp != -1) value = value.substring(0,cp);
                        value = value.replace('-', '/');
                        value = value.replace('-', '/');
                        date = new Date(value);
                        return date.format('d/m/Y h:i:s a');
                    } else
                        return value;
                }, ";
                break;
//            case 'datetimepg':
//                $renderer = " renderer: function(value) {
//                    if((value != '') && (value != undefined) && (value != null)) {
//                        var cp = value.indexOf('.');
//                        if (cp != -1) value = value.substring(0,cp);
//                        value = value.replace('-', '/');
//                        value = value.replace('-', '/');
//                        date = new Date(value);
//                        return date.format('d/m/Y h:i:s a');
//                    } else
//                        return value;
//                }, ";
//                break;
        }

        return $renderer;
    }

    /**
     * <b>Method: buildDateTimeField()<b/>
     * 		Se encarga de configurar un campo del tipo xdatetime.
     * @param		array $field_metadata  Arreglo de datos con la informacion extra para la configuracion del campo.
     * @param		integer $operation_id   Identificador de la operacion a la que pertenece el campo.
     * @param		array $config Arreglo de datos con la configuracion comun de los campos.
     * @return		string $campo Cadena de texto JSON con la configuracion completa del campo DateTime.
     * @version 	v-1.0 Mirwing Rosales 05/06/2012 09:18 am
     */
    function buildDateTimeField($field_metadata, $operation_id, $config) {
        $field_id = $this->formatFieldId($field_metadata);

        //obtenemos la etiqueta del campo.
        $field_label = $this->getLabel($field_metadata);
        
        //extraemos configuraciones generales del campo.
        extract($config);

        //@todo Crear la validacion de rango de fechas.
        /* if($field->vtype === 'daterange' AND !empty($field->child)){
          $suffix = explode('_', $field->nombre_s);
          if(preg_match('/^(finicio)\w*$/', $field->nombre_s))
          $attrRange = count($suffix) < 2 ? "endDateField: 'ffin_{$opId}_{$field->child}'," : "endDateField: 'ffin_{$suffix[1]}_{$opId}_{$field->child}',";
          elseif (preg_match('/^(ffin)\w*$/', $field->nombre_s)) {
          $attrRange = count($suffix) < 2 ? "startDateField: 'finicio_{$opId}_{$field->child}'," : "startDateField: 'finicio_{$suffix[1]}_{$opId}_{$field->child}',";
          }
          } */
        
        $field = "{
            xtype:'xdatetime',
            fieldLabel:'$field_label',
            id: '$field_id',
            name:'$server_name',
            $fieldValue
            timeFormat: 'h:i a',
            timeConfig:{
                $allowBlank
                readOnly: Boolean($field_metadata->read_only)
            },
            dateFormat: 'd/m/Y',
            dateConfig:{
                $allowBlank
                readOnly: Boolean($field_metadata->read_only)
            },
            disabled: Boolean($field_metadata->disabled),
            hidden:   Boolean($field_metadata->hidden),
            readOnly: Boolean($field_metadata->read_only),
            $blankText
            $emptyText
            $tooltip
            $vtype
        }";
        return $field;
    }

    /**
     * <b>Method:   buildToolBar($tool_bar_type)</b>
     * Construye el toolbar a partir de los parametros dados
     * @param       String    $tool_bar_type   Tipo de Toolbar a crear. Default 'tbar'
     * @return      String    JSON con las configuraciones del toolbar.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 30/08/2012 11:56:50
     * */
    private function buildToolBar($tool_bar_type = 'tbar') {
        //Evaluamos si existe id de la operacion.
        if(empty($this->operation_id))
            return FALSE;
        
        $BUTTON = 'Button';
        return $this->buildToolbarButtons(
                $this->ci->metadata_model->getChildrenByOperation($this->operation_id, $BUTTON, TRUE, $tool_bar_type)
        );
    }

    /**
     * <b>Method:   setReplaceContent($prefix, $replace)</b>
     * Crea el codigo JS que indica donde se va crear el nuevo componente.
     * @param       String    $prefix   prefijo de la operacion.
     * @param       String    $replace  Indica que se va a sustituir
     * @return      String    Codigo Js genreado.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 04/08/2012
     * */
    private function setReplaceContent($prefix, $replace) {
        $param = $prefix . '_' . $this->operation_id;

        //evaluamos donde vamos a sustituir el componente.
        if ($replace == 'center')
            $replace = "replaceCenterContent($param);";
        elseif ($replace == 'window')
            $replace = 'window';
        elseif (!empty($replace))
            $replace = "replaceChild('$replace',$param);";

        return $replace;
    }

    /**
     * <b>Method:   getLabel($field_metadata)</b>
     * Setea el label del campo.
     * @param       Object    $field_metadata   Datos con la metadatafa del campo.
     * @return      String    Etiqueta a ser asociada al campo.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 04/08/2012 17:30
     * */
    private function getLabel($field_metadata) {
        return (empty($field_metadata->_label)) ? $field_metadata->field_label : $field_metadata->_label;
    }

    //TreeGrid

    /**
     * <b>Method:   buildTreeGrid($params)</b>
     * Construye un Treegrid con los datos proporcionados.
     * @param       Array    $params   Difrentes configuraiones a ser tomadas en concideracion por el metodo.
     *                  string     [title]    Titulo del grid.
     *                  string     [name]     nombre del modulo en cuestion.
     *                  mixed      [data]     Datos pertenecientes al modulo ejecutado.
     *                  string     [replace]        Indica el contenedor a reemplazar.
     *                  boolean    [scriptTags]     Indica si se imprime el tag HTML <script>.
     *                  boolean    [returnView]     Indica si se retorna el resultado del metodo.
     *                  mixed      [extraOptions]   Inidica parametros adicionales a ser enviados al grid.
     * @author      Jesus Farias
     *              Juan C. Lopez
     *              Jose Rodriguez <josearodrigueze@gmail.com>
     *              Maycol Alvarez <malvarez@rialfi.com>
     * @version     1.0 12/09/2012 10:00 AM
     * */
    public function buildTreeGrid($params) {
        extract($params);
        if (empty($extraOptions['reporte_id']))
            $this->fieldsDb = $this->ci->metadata_model->getFieldsByOperation($this->operation_id);
        //$this->getFieldsByOperation($this->operation_id);
        else
            $this->getFieldsByReporte($extraOptions['reporte_id'], 'grid', $extraOptions['report_type']);

        if (empty($this->fieldsDb))
            return FALSE;

        //Inicializamos las variables a ser empleadas mas adelante.
        $tbar_button = $bbar_button = '';
        $store_fields = array();
        $columns = '[';
        $colon = '';
        $hasDisclaimer = (!empty($extraOptions['disclaimer'])) ? TRUE : FALSE;


        //Recorremos la metadata de los campos en base de datos.
        foreach ($this->fieldsDb as $field) {

            if ($field->hidden != "1") {
                //Definimos el valor del Label
                $label = $this->getLabel($field);
                $length = intval($field->length);
                if ($label < 0)
                    $length = 100;
                $columns .= "{
                    header: '$label',
                    dataIndex: '" . $field->server_name . "',
                    width: $length

                },";
            }
        }

        //Contruimos los botones del grid.
        $columns.=$this->makeTreeGridButtons('tree_', $name, $hasDisclaimer, $replace, @$root_template_ignores_visual_type, @$p_buttons);
        $columns .= ']';

        $replace = $this->setReplaceContent('tree', $replace);

        //Obtenemos los diferentes toolbars
        $tbar_button = $bbar_button = '';
        $tbar_button = $this->buildToolBar();
        $bbar_button = $this->buildToolBar('bbar');

        //
        $searchType = '';
        if (!empty($extraOptions['searchType']))
            $searchType = $extraOptions['searchType'];

        $bbar_off = (!empty($extraOptions['bbarOff'])) ? TRUE : FALSE;

        if (!$tree_data_is_json) {
            $tree_data = json_encode($tree_data);
        }

        //Seteamos las diferentes configuraciones de la vista.
        $view_data = array(
            'formTitle' => $title,
            'name' => $name,
            'fields' => json_encode($store_fields),
            'tree_data' => $tree_data,
            'columns' => $columns,
            'tbar' => $tbar_button,
            'bbar' => $bbar_button,
            'searchType' => $searchType,
            'extraData' => $tree_data['extraData'],
            'opId' => $this->operation_id,
            'url' => $this->operation_url,
            'scriptTags' => $scriptTags,
            'bbarOff' => $bbar_off,
            'replace' => $replace,
            'height' => @$extraOptions['height'],
            'winWidth' => @$extraOptions['winWidth'],
            'winHeight' => @$extraOptions['winHeight']
        );

        //Validamos si la vista debe retornanrse.
        if (!empty($returnView) || !empty($this->allways_return_views)) {
            $preBuildView = $this->ci->load->view(
                $this->ci->config->item('dyna_views_grid_path') . 'treegrid.js.php', $view_data, TRUE
            );

            if (!empty($this->allways_return_views))
                return array('js_generated' => $preBuildView, 'ui_component' => "Grid_$this->operation_id");
            else
                return $preBuildView;
        }

        //Hacemos el llamdo de la vista a ser cargada.
        $this->ci->load->view($this->ci->config->item('dyna_views_grid_path') . 'treegrid.js.php', $view_data);
    }

    /**
     * <b>Method:   makeGridButtons($tree_grid_type, $subject) </b>
     * Crear Botones pertenecientes a los grid
     * @param       String    $tree_grid_type    Prefijo con el tipo de TreeGrid.
     * @param       String    $subject      Nombre.
     * @param       Array     Visual Types ignorados en el Root
     * @return      String    Botonos a ser vistos dentro de los TreeGrid.
     * @author      Maycol Alvarez <malvarez@rialfi.com>
     * @version     1.0 12/09/2012 10:00 AM
     * */
    public function makeTreeGridButtons($tree_grid_type, $subject, $has_disclaimer = null, $replace = null, $root_template_ignores_visual_type = null, $p_buttons = null) {
        $grid_id = $tree_grid_type . $this->operation_id;
        $buttons_columns = '';

        //Obtebemos las operaciones hijas.
        $operations = $this->ci->metadata_model->getChildrenByOperation($this->operation_id, 'Button', TRUE, 'parent');

        //Valiamos que la operacion tenga hijas.
        if (empty($operations))
            return FALSE;

        foreach ($operations as $operation) {


            $button_html = "<div style=\"cursor:pointer\"><img class=\"defx-tree-node-icon\" title=\"$operation->_name\" src=\"" . BASE_ICONS . $operation->icon . "\" onClick=\"";
            //Indicamos la funcion JS a ser llamada por la imagen.
            //Validamos es un boton de tipo borrar.
            $url = base_url() . $operation->url;
            if ($operation->visual_type == 'Button_D' && empty($has_disclaimer)) {
                $button_html .= "confirmAction(\'" . $url . "\',{id}, \'$grid_id\', \'({text})\', \'delete\')\"/>";
            }
            //Validamos si es un boton desactivar
            elseif ($operation->visual_type == 'Button_DI' && empty($has_disclaimer)) {
                $button_html .= "confirmAction(\'" . $url . "\',{id}, \'$grid_id\', \'({text})\', \'deactivate\')\"/>";
                //$button_html = "<tpl if=\"id != \\'-1\\'\">" . $button_html . '</tpl>';
            }
            //Validamos si es un boton activar
            elseif ($operation->visual_type == 'Button_AC') {
                $button_html .= "confirmAction(\'" . $url . "\',{id}, \'$grid_id\', \'({text})\', \'activate\')\"/>";
                //$button_html = "<tpl if=\"id != \\'-1\\'\">" . $button_html . '</tpl>';
            }
            //Validamos si es un boton personalizable
            elseif ($operation->visual_type == 'Button_P') {
                //die(json_encode($p_buttons[$operation->url]));
                $arr_operation = $p_buttons[$operation->url];
                if ($arr_operation == null) {
                    $arr_operation = array(
                        'params' => array('id' => '{id}')
                    );
                }
                if ($replace == 'window') {
                    $arr_operation['wClose'] = 'w_' . $this->operation_id;
                }
                $arr_operation['url'] = $operation->url;
                $obj = str_replace('"', "\'", json_encode($arr_operation));

                $button_html.="getContent($obj)\"/>";
                //$button_html .= "confirmAction(\'" . $url . "\',{id}, \'$grid_id\', \'({text})\', \'activate\')\"/>";
                //$button_html = "<tpl if=\"id != \\'-1\\'\">" . $button_html . '</tpl>';
            }
            //Si no se cumple ningua de las validaciones anteriores reemplazamos el Contenedor centrar
            else
                $button_html.="getCenterContent(\'" . $operation->url . "\',\'{id}\', event)\"/>";

            if ($root_template_ignores_visual_type) {
                if (!in_array($operation->visual_type, $root_template_ignores_visual_type)) {
                    $button_html = "<tpl if=\"id != \\'-1\\'\">" . $button_html . '</tpl>';
                }
            }

            $buttons_columns.=$comma . "{
                width: 30,
                align: 'center',
                fixed:true,
                hideable:false,
                menuDisabled: true,
                dataIndex:'id',
                tpl: new Ext.XTemplate('$button_html')
            }";
            if ($comma == '')
                $comma = ',';
        }
        return $buttons_columns;
    }

    //ENd TreeGrid

    /**
     * <b>Method:   formatFieldId($field_metadata)</b>
     * Formateana el id(Identificador HTML) del campo.
     * @param       Object    $field_metadata   Informacion del campo en cuestión.
     * @return      String    identificador del campo formateado.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 24/09/2012 15:00
     * */
    private function formatFieldId($field_metadata) {
        if (empty($field_metadata))
            return FALSE;

        return $field_id = $field_metadata->server_name . '_' . $this->operation_id . '_' . $field_metadata->id;
    }

}

/* END Class (Libraty) Dyna_views      */
/* END of file dyna_views.php */
/* Location: ./application/libraries/dyna_views.php */