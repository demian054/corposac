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
 * Vista que provee las generacion tabs dentro de dyna_views.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php endif ?>

var tabs_<?= $opId ?> = new Ext.TabPanel({
    name:       'tabs_<?= $opId ?>',
    id:         'tabs_<?= $opId ?>',
    activeTab:  0,
    //autoHeight: true,
    autoWidth: true,
    enableTabScroll:true,
    //deferredRender:false,
    //forceLayout :true,
    height:     '100%',
    //width:      '100%',
    monitorResize:true,
    title:      '<?= $title ?>',
    //defaults:   {autoScroll: true},
    items:      <?= $items ?>
});

//Creamos el componete tabPanel en formato JSON
var tabPanel_<?= $opId ?> = {
    xtype:      'panel',
    layout:     'fit',
    id:         'tabPanel_<?= $opId ?>',
    width:      '100%',
    autoHeight: false,
    //height:     500,
<?php if ($replace != 'window'): ?>
        title:      '<?= $title ?>',
<?php endif; ?>
    defaults:   {autoScroll: true},
    items:      [tabs_<?= $opId ?>]
};

<?php
//Indica donde se va a colocar el componente.
if ($replace == 'window') {
    $wdata['w_item'] = 'tabPanel_' . $opId;
    $wdata['width'] = $winWidth;
    $wdata['height'] = $winHeight;
    $wdata['formTitle'] = $title;
    $this->load->view('lib_dyna_views/window.js.php', $wdata);
} else {
    echo $replace;
}
?>

<?php if (!empty($scriptTags)): ?>
    </script>
    <?php
endif;

/* END View tabs.js      */
/* END of file tabs.js.php */
/* Location: ./application/modules/lib_dyna_views/views/tabs.js.php */
?>