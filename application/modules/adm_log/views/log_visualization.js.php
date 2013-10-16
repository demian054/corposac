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
 * Vista particular donde se imprime el resultados de varios componentes visuales de tipo EXtJS.
 * La misma existe para poder agregar un titulo a la barra de collapse del visualizados de log.
 */
echo $result_view;
?>
var panel_title = Ext.get('panel_<?= $operation_id ?>1-xcollapsed').dom.innerHTML='<span class="x-panel-header">Formulario de Busqueda.</span>';




//Ext.get('panel_<?= $operation_id ?>1-xcollapsed').dom.on('mouseover', function() {alert('');});

//Ext.get('panel_<?= $operation_id ?>1').on('beforeCollapse', function() {
//    console.log('<?= $operation_id ?> --');
    //return false;
//});



var mostrar_form = function (){
    var t_panel = Ext.getCmp('panel_<?= $operation_id ?>1');
    t_panel.expand();
};

start_date.df.on('select', mostrar_form);
start_date.tf.on('select', mostrar_form);
end_date.df.on('select', mostrar_form);
end_date.tf.on('select', mostrar_form);




//console.log(start_date);

<?php
/* END View log_visualization      */
/* END of file log_visualization.js.php */
/* Location: ./application/modules/adm_log/views/log_visualization.js.php */
?>