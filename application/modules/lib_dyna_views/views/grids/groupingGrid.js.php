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
 * Vista que provee las generacion de groupingGrid dentro de dyna_views.
 */
if (!empty($scriptTags)):
    ?>
    <script type="text/javascript">
<?php endif ?>

<?php
//Convertimos los toolbars en arreglos y llamamos a la vista toolbars.
$dataToolBar = array('tbar' => $tbar, 'bbar' => $bbar, 'opId' => $opId, 'searchType' => $searchType);
$this->load->view('lib_dyna_views/toolbar.js.php', $dataToolBar);
?>

//Creamos un JsonRedae.
var myReader_<?= $opId ?> = new Ext.data.JsonReader({
    totalProperty:  'totalRows',
    root: 'rowset',
    fields: <?= $fields ?>
});

//Creamos un storeData
var groupingGridStore_<?= $opId ?> = new Ext.data.GroupingStore({
    reader:     myReader_<?= $opId ?>,
    data:       <?= $data ?>,
    groupField: '<?= $groupField ?>',
    id: 'groupingGridStore_<?= $opId ?>',
    // sortInfo:{field: '<? //=$sortField    ?>', direction: '<? //=$direction    ?>'},
    //groupOnSort : true,
    autoDestroy:true,
    //remoteSort:true,

    //Definimos un proxy
    proxy: new Ext.data.HttpProxy({
        url: BASE_URL+'<?= $url ?>',
        method: 'POST'
    })
});

<?php if (strpos($replace, 'replaceCenterContent')===0) : ?>
//reestablece el storemanager para cuando se trata de un nuevo replaceCenter
Ext.StoreMgr.clear();
<?php endif; ?>

//Registramos a store dentro del storemanager
Ext.StoreMgr.register(groupingGridStore_<?= $opId ?>);

<?php if (empty($bbarOff)): ?>
    var paginBar_<?= $opId ?> = new Ext.PagingToolbar({
        pageSize:   LONG_LIMIT,
        store:      groupingGridStore_<?= $opId ?>,
        displayInfo:true
    });
<?php endif ?>

//Instanciamos un grid panel.
var groupingGrid_<?= $opId ?> = new Ext.grid.GridPanel({
    id:	'groupingGrid_<?= $opId ?>',
    layout: 'fit',
    height: <? echo (empty($height)) ? "'100%'" : $height ?>,
    frame: false,
    border: true,
    stripeRows: true,
    autoScroll: true,
    colModel:   new Ext.grid.ColumnModel({
        defaults: {width: 120,menuDisabled: true},
        columns:  <?= $columns ?>
    }),
    store: 	groupingGridStore_<?= $opId ?>,
    loadMask: 	false,
    title:      '<?= $gridTitle ?>',
    style: 	'margin:0 auto;',
    tbar:       topBar_<?= $opId ?>,

    bbar: <?= (empty($bbarOff)) ? "paginBar_$opId" : 'false' ?> ,

    view: new Ext.grid.GroupingView({
        forceFit: <?= (isset($forceFit)) ? $forceFit : true ?> ,
        enableGroupingMenu:false,
        enableNoGroups:false,
        hideGroupedColumn : true,
        showGroupName : false,
        groupTextTpl: '{text} ({[values.rs.length]})'
    })
});

<?php
// Creamos el searchField para el grid.
$dataSearch = array('opId' => $opId, 'searchType' => $searchType, 'elem' => 'groupingGrid_', 'storeName' => 'groupingGridStore_');
$this->load->view('lib_dyna_views/search.js.php', $dataSearch);

if ($replace == 'window') {
    $wdata['w_item'] = 'groupingGrid_' . $opId;
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

 endif
/* END View groupingGrid.js      */
/* END of file groupingGrid.js.php */
/* Location: ./application/modules/lib_dyna_views/views/grids/groupingGrid.js.php */
?>