<?php
/**
 * @package lib_dyna_views
 * @subpackage views
 *
 * @author      Jesus Farias Lacroix <jesus.farias@gmail.com>,
 * 		Juan C. Lopez <jlopez@rialfi.com>,
 * 		Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de window dentro de dyna_views.
 */
$width = (!empty($width)) ? $width : 667;
$height = (!empty($height)) ? $height : 500;
?>

var w_<?= $opId ?> = new Ext.Window({
    id: 'w_<?= $opId ?>',
    shadow: true,
    title: '<?= $formTitle ?>',
    collapsible: true,
    maximizable: true,
    //width: 667,
    width: <?= $width ?>,
    //height: 500,
    height: <?= $height ?>,
    minWidth: 300,
    minHeight: 200,
    layout: 'fit',
    modal:true,
    autoScroll: true,
    overflow:'auto',
    plain: true,
    bodyStyle: 'padding:5px;',
    buttonAlign: 'center',
    items:<?= $w_item ?>,
    autoDestroy: true
});

w_<?= $opId ?>.show();

<?php
/* END View windows.js      */
/* END of file windows.js.php */
/* Location: ./application/modules/lib_dyna_views/views/windows.js.php */
?>