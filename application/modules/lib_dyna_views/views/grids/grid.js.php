<?php
/**
 * @package lib_dyna_views
 * @subpackage views/grids
 *
 * @author      Jesus Farias Lacroix <jesus.farias@gmail.com>,
 * 		Juan C. Lopez <jlopez@rialfi.com>,
 * 		Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de grid dentro de dyna_views.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php endif ?>
// Creamos el store con la data
var  GridStore_<?= $opId ?>   = new Ext.data.JsonStore({
    totalProperty:  'totalRows',
    root:           'rowset',
    fields:         <?= $fields ?>,
    data:           <?= $data ?>,
    id:           'GridStore_<?= $opId ?>',
    //remoteSort:     true,
    //autoDestroy:true,
    proxy: new Ext.data.HttpProxy({
        url:    BASE_URL+'<?= $url ?>',
        method: 'POST',
        //params: { start: 0, limit: LONG_LIMIT }
        baseParams: { start: 0, limit: LONG_LIMIT }
    })
});

<?php if (strpos($replace, 'replaceCenterContent')===0) : ?>
//reestablece el storemanager para cuando se trata de un nuevo replaceCenter
Ext.StoreMgr.clear();
<?php endif; ?>

//Registramos el store dentro del manager store.
Ext.StoreMgr.register( GridStore_<?= $opId ?>);

<?php
//Convertimos los toolbars en arreglos y llamamos a la vista toolbars.
$dataToolBar = array('tbar' => $tbar, 'bbar' => $bbar, 'opId' => $opId, 'searchType' => $searchType);
$this->load->view('lib_dyna_views/toolbar.js.php', $dataToolBar);

if (empty($bbarOff)):

//Creamos el componenete pagingToolBar y asociamos los stores del grid y el paging.
    ?>
        
        var paginBar_<?= $opId ?> = new Ext.PagingToolbar({
            pageSize:   LONG_LIMIT,
            store:      GridStore_<?= $opId ?>,
            displayInfo: true,
            displayMsg: '<?= $this->lang->line('found_result') ?>',
            emptyMsg: '<?= $this->lang->line('not_found_result') ?>',
        });
        
        paginBar_<?= $opId ?>.store.baseParams= GridStore_<?= $opId ?>.baseParams;

        GridStore_<?= $opId ?>.on('load', function(){
            var aux= GridStore_<?= $opId ?>.baseParams;
            Ext.apply(aux,GridStore_<?= $opId ?>.lastOptions.params);
            if (paginBar_<?= $opId ?>.store != null)
                Ext.apply(paginBar_<?= $opId ?>.store.baseParams, aux);
        });

        GridStore_<?= $opId ?>.on('reload', function(){
            paginBar_<?= $opId ?>.store.baseParams= GridStore_<?= $opId ?>.baseParams;
        });
        
<?php endif; ?>

//Creamos el componenete grid de Ext JS.
var Grid_<?= $opId ?> = new Ext.grid.GridPanel({
    id:         'Grid_<?= $opId ?>',
    height:     <? echo (empty($height)) ? "'100%'" : "'$height'" ?>,
    layout:     'anchor',
    frame:      false,
    border:     true,
    stripeRows: true,
    autoScroll: true,
    colModel: new Ext.grid.ColumnModel({
        defaults: {
            width: 120
        },
        columns:        <?= $columns ?>
    }),
    store:      GridStore_<?= $opId ?>,
    loadMask:   false,
    title:      '<?= $gridTitle ?>',
    style:      'margin:0 auto;',
    tbar:       topBar_<?= $opId ?>,
    bbar: <?= (empty($bbarOff)) ? "paginBar_$opId" : 'false' ?>
});

<?php
// Creamos el searchField para el grid.
$dataSearch = array('opId' => $opId, 'searchType' => $searchType, 'elem' => 'Grid_', 'storeName' => 'GridStore_');
$this->load->view('lib_dyna_views/search.js.php', $dataSearch);

//Verificamos el componente a ser reemplazado.
if ($replace == 'window') {
    $wdata['w_item'] = 'Grid_' . $opId;
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
/* END View grid.js      */
/* END of file grid.js.php */
/* Location: ./application/modules/lib_dyna_views/views/grids/grid.js.php */
?>