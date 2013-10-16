<?php
/**
 * @package lib_dyna_views
 * @subpackage views/panels
 *
 * @author      Jesus Farias Lacroix <jesus.farias@gmail.com>,
 * 		Juan C. Lopez <jlopez@rialfi.com>,
 * 		Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 * 		Mirwing Rosales. <mrosales@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de un panel que divida el espacio total en dos filas dentro de la vista generada.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
    <?php
endif;

//Imprimimos el resultadio del panel
print($panelData['p1']);

//La variable JS generada la asociamos a una nueva variable JS
print('var top = ' . $panelData['type1'] . $opId . ';');

//Imprimimos el resultadio del panel
print($panelData['p2']);
//La variable JS generada la asociamos a una nueva variable JS
print('var bottom = ' . $panelData['type2'] . $opId . ';');
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
            region: 'north',
            itemId: 'panel_<?= $opId ?>1item',
            id: 'panel_<?= $opId ?>1',
            xtype: 'panel',
            layout: 'fit',
            autoHeight:true,
            //autoWidth:true,
            border: false,
            margins: '0 0 5 0',
            items:[top]
<?php
if (!empty($panelData['collapsible'])) {
    echo ",collapsible:true,collapsed:true";
}
?>
                    },{
                        region: 'center',
                        itemId: 'panel_<?= $opId ?>2item',
                        id: 'panel_<?= $opId ?>2',
                        xtype: 'panel',
                        border: false,
                        layout: 'fit',
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

/* END View 2A.js      */
/* END of file 2A.js.php */
/* Location: ./application/modules/lib_dyna_views/views/panels/2A.js.php */
?>