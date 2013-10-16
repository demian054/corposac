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
 * Vista que provee las generacion de un panelHtml dentro de dyna_views.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php
endif;
//    $dataToolBar = array('tbar'=>$tbar, 'bbar'=>$bbar, 'opId'=>$opId);
//    $this->load->view('generals/toolbar.js.php', $dataToolBar);
?>

var bottomBar=false;

<?php if (!empty($extraOptions['backButtonUrl']) && !empty($extraOptions['backButtonObjId'])): ?>

    var buttonText='<?= (!empty($extraOptions['backButtonText'])) ? $extraOptions['backButtonText'] : 'Volver' ?>';

    var backButton={
        xtype:'button',
        text: buttonText,
        icon: BASE_ICONS+'application_go_back.png',
        handler:function(){
            getCenterContent('<?= $extraOptions['backButtonUrl'] ?>', '<?= $extraOptions['backButtonObjId'] ?>');
        }
    }

    bottomBar= new Ext.Toolbar({
        items:[backButton]
    });

    <?php
endif;

//Instancia de la clase panael
?>
var PanelHtml_<?= $opId ?> = new Ext.Panel({
    layout: 'fit',
    id:     'panel_html<?= $opId ?>',
    frame:      false,
    autoHeight: true,
    title:  '<?= $panelTitle ?>',
    bbar:    bottomBar,
    html: ['<?= $pHtml ?>']

});

<?php
//Indica donde se va a colocar el componente.
if ($replace == 'window') {
    $wdata['w_item'] = 'PanelHtml_' . $opId;
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
/* END View panelHtml.js      */
/* END of file panelHtml.js.php */
/* Location: ./application/modules/lib_dyna_views/views/panelHtml.js.php */
?>