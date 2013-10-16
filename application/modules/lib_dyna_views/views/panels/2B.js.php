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
 * Vista que provee las generacion de un panel que divida el espacio total en dos columnas dentro de la vista generada por
 * Dyan_views.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php
endif;

//Imprimimos el resultadio del panel
print($panelData['p1']);

//La variable JS generada la asociamos a una nueva variable JS
print('var top = ' . $panelData['type1'] . $opId);

//Imprimimos el resultadio del panel
print($panelData['p2']);
//La variable JS generada la asociamos a una nueva variable JS
print('; var bottom = ' . $panelData['type2'] . $opId);
?>

var panel_<?= $opId ?> = new Ext.Panel({
    layout: 'border',
    id:     'panel_<?= $opId ?>',
    frame:  true,
    height: '98%',
<?php if ($replace != 'window'): ?>
        title:  '<?= $panelTitle ?>',
<?php endif; ?>
    items: [{
            region: 'west',
            xtype: 'panel',
            autoHeight: true,
            border: false,
            margins: '0 0 5 0',
            items:[top]
        },{
            region: 'center',
            xtype: 'panel',
            autoHeight: true,
            border: false,
            margins: '0 0 5 0',
            items: [bottom]
        }]
});

<?php
//Indica donde se va a colocar el componente.
if ($replace == 'window') {
    $wdata['w_item'] = 'panel_' . $opId;
    $wdata['width'] = $winWidth;
    $wdata['height'] = $winHeight;
    $wdata['formTitle'] = $panelTitle;
    $this->load->view('lib_dyna_views/window.js.php', $wdata);
} else {
    echo $replace;
}
?>

<?php if (!empty($scriptTags)): ?>
    </script>
    <?php
endif;

/* END View 2B      */
/* END of file 2B.php */
/* Location: ./application/modules/lib_dyna_views/views/panels/2B.php */
?>