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
 * Vista que provee las generacion de un panel que divida el espacio total en dos filas y una de las filas dividas a la mitad
 * dentro de la vista generada por Dyan_views.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php
endif;

//@todo Implementar toolbars
//    $dataToolBar = array('tbar'=>$tbar, 'bbar'=>$bbar, 'opId'=>$opId);
//    $this->load->view('generals/toolbar.js.php', $dataToolBar);
//Imprimimos el resultadio del panel
print($panelData['p1']);

//La variable JS generada la asociamos a una nueva variable JS
print('var panel1 = ' . $panelData['type1'] . $opId);

//Imprimimos el resultadio del panel
print($panelData['p2']);
//La variable JS generada la asociamos a una nueva variable JS
print('var panel2 = ' . $panelData['type2'] . $opId);

//Imprimimos el resultadio del panel
print($panelData['p3']);
//La variable JS generada la asociamos a una nueva variable JS
print('var panel3 = ' . $panelData['type3'] . $opId);
?>

var panel_<?= $opId ?> = new Ext.Panel({
    layout: 'border',
    id:     'panel_<?= $opId ?>',
    frame:  true,
    height: '98%',
    title:  '<?= $panelTitle ?>',
    items: [{
            region: 'west',
            xtype: 'panel',
            autoHeight: true,
            border: false,
            margins: '0 0 5 0',
            items: [panel1]
        },{
            region: 'east',
            xtype: 'panel',
            autoHeight: true,
            border: false,
            margins: '0 0 5 0',
            items: [panel2]
        },{
            region: 'center',
            xtype: 'panel',
            autoHeight: true,
            border: false,
            margins: '0 0 5 0',
            items: [panel3]
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

/* END View 3C.js      */
/* END of file 3C.js.php */
/* Location: ./application/modules/lib_dyna_views/views/panels/3C.js.php */
?>