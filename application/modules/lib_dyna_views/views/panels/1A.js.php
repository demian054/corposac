<?php
/**
 * @package lib_dyna_views
 * @subpackage views/panels
 *
 * @author      Jesus Farias Lacroix <jesus.farias@gmail.com>,
 * 		Juan C. Lopez <jlopez@rialfi.com>,
 * 		Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de un panel que ocupa todo el espacio dentro de la vista generada.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php
endif;

//    $dataToolBar = array('tbar'=>$tbar, 'bbar'=>$bbar, 'opId'=>$opId);
//    $this->load->view('generals/toolbar.js.php', $dataToolBar);

print($panelData['p1']);

//La variable JS generada la asociamos a una nueva variable JS
print('var top = ' . $panelData['type1'] . $opId . ';');
?>

//Crea una instancia de un panel
var panel_<?= $opId ?> = new Ext.Panel({
    layout: 'border',
    id:     'panel_<?= $opId ?>',
    frame:      true,
    height: '98%',
    title:  '<?= $panelTitle ?>',
    items: [{
            region: 'center',
            xtype: 'panel',
            autoHeight: true,
            border: false,
            margins: '0 0 5 0',
            items: [top]
        }]
});

<?php
//Indica donde se va a colocar el componente.
if ($replace == 'window') {
    $wdata['w_item'] = 'panel_' . $opId;
    $wdata['width'] = $winWidth;
    $wdata['height'] = $winHeight;
    $this->load->view('lib_dyna_views/window.js.php', $wdata);
} else {
    echo $replace;
}
?>

<?php if (!empty($scriptTags)): ?>
    </script>
    <?php
endif;

/* END View 1A.js      */
/* END of file 1A.js.php */
/* Location: ./application/modules/lib_dyna_views/views/panels/1A.js.php */
?>
