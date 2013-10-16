<?php
/**
 * @package lib_dyna_views
 * @subpackage views/forms
 *
 * @author      Jesus Farias Lacroix <jesus.farias@gmail.com>,
 * 		Juan C. Lopez <jlopez@rialfi.com>,
 * 		Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de formularios dentro de dyna_views.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php endif; ?>

<?= $dBStore ?>

<?php
$dataToolBar = array('tbar' => $tbar, 'bbar' => $bbar, 'opId' => $opId);
$this->load->view('lib_dyna_views/toolbar.js.php', $dataToolBar);

//Creamos una instancia del objeto FromPanel de ExtJS.
?>
var form_<?= $opId ?>= new Ext.FormPanel({
    id: 'form_<?= $opId ?>',
<?= $labelAlign ?>
    labelWidth:     150,
    buttonAlign:    'center',
    frame:          true,
    tbar:           topBar_<?= $opId ?>,
    bbar:           bottomBar_<?= $opId ?>,
<?php if ($replace != 'window'): ?>
        title: 		'<?= $formTitle ?>',
<?php endif ?>
    autoScroll:     true,
<?php if ($autoWidth == TRUE): ?>
    width: 		'100%',
<?php endif ?>

<?php if (!empty($height)): ?>
        height:'<?= $height ?>',
<?php endif; ?>
    items: 		<?= $fields ?>,
    border:         false,
    bodyStyle:      {paddingTop: '10px', paddingLeft: '35px' },
    style:          {margin: 'auto'},
    defaults:       {
        labelStyle: 'font-weight: bold;',
        style:      { margin: '0 0  5px 0', padding: '2px' }
    },
    buttons:        <?= $buttons ?>
});

<?php
//Verificamos el componente a ser reemplazado.
if ($replace == 'window') {
    $wdata['w_item'] = 'form_' . $opId;
    $wdata['width'] = $winWidth;
    $wdata['height'] = $winHeight;
    $this->load->view('lib_dyna_views/window.js.php', $wdata);
} else {
    echo $replace;
}
?>
Ext.QuickTips.init();
<?php if (!empty($scriptTags)): ?>
    </script>
    <?php
endif;
/* END View form.js      */
/* END of file form.js.php */
/* Location: ./application/modules/lib_dyna_views/views/forms/form.js.php */
?>