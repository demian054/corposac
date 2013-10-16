<?php
/**
 * @package     views
 * @subpackage grids
 *
 * @author      Maycol Alvarez <malvarez@rialfi.com>,
 *              Jesus Farias Lacroix <jesus.farias@gmail.com>,
 *              Juan C. Lopez <jlopez@rialfi.com>,
 *              Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.0 14/09/12 09:00 PA
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de TreeGrid dentro de dyna_views.
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

//Creamos el componenete pagingToolBar
//var paginBar_<?= $opId ?> = new Ext.PagingToolbar({
//    pageSize:   LONG_LIMIT,
//    store:      GridStore_<?= $opId ?>,
//    displayInfo: true,
//    displayMsg: '<?= $this->lang->line('found_result') ?>',
//    emptyMsg: '<?= $this->lang->line('not_found_result') ?>',
//});

//Creamos el componenete treegrid de Ext JS.

 var tree_data_<?=$opId?> = [<?=$tree_data?>];

    var tree_<?= $opId ?> = new Ext.ux.tree.TreeGrid({
        title: 'Listado de Operaciones',
        id : 'tree_<?= $opId ?>',
        //autoWidth: true,
        layout: 'fit',
        enableSort: false,
        
        //width: 600,
        //height: 300,
        //renderTo: Ext.getBody(),
        //enableDD: true,
        /*loader: new Ext.tree.TreeLoader({
            url: '<?=  base_url()?>our_operations/crudoperation/index?root=1',
            clearOnLoad: false,
            requestMethod: 'GET'
        }),*/
        //dataUrl: '<?=  base_url()?>our_operations/crudoperation/FU_getroot',
        root: new Ext.tree.AsyncTreeNode({
            expanded: true,
            children: tree_data_<?= $opId ?>
        }),
        columns: <?=$columns?>,
        
		

        tbar: topBar_<?= $opId ?>,
        bbar: bottomBar_<?= $opId ?>
        //dataUrl: 'treegrid-data.json'
    });

<?php
// Creamos el searchField para el grid.
//$dataSearch = array('opId' => $opId, 'searchType' => $searchType, 'elem' => 'Grid_', 'storeName' => 'GridStore_');
//$this->load->view('generals/search.js.php', $dataSearch);
?>

<?php
//Verificamos el componente a ser reemplazado.
if ($replace == 'window') {
    $wdata['w_item'] = 'tree_' . $opId;
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
?>
<?php if (strpos($replace, 'replaceCenterContent')===0) : ?>
//reestablece el storemanager para cuando se trata de un nuevo replaceCenter
Ext.StoreMgr.clear();
<?php endif; ?>