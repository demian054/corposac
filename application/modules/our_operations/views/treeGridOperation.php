<?php if (!empty($scriptTags)): ?>
    <script type="text/javascript">
<?php endif ?>

/*!
 * Ext JS Library 3.4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */

    Ext.QuickTips.init();
    
   var tree_data_<?=$op_id?> = [<?=$tree_data?>];

    var tree_<?= $op_id ?> = new Ext.ux.tree.TreeGrid({
        title: 'Listado de Operaciones',
        //width: 500,
        //height: 300,
        //renderTo: Ext.getBody(),
        //enableDD: true,

        columns: <?=$columns?>,
		root: new Ext.tree.AsyncTreeNode({
            expanded: true,
            children: tree_data_<?= $op_id ?>
        }),
        listeners: {
            beforemovenode: function(tree, node, oldParent, newParent, index ) {
                
                
                //return false;
            },
            beforenodedrop: function(ee){
                //alert(ee);
                Ext.Ajax.request({
                    method: 'GET',
                    url: '<?=base_url()?>our_operations/crudoperation/FU_conf',
                    success: function(data){
                        //alert('s');
                        if (data.responseText == 'si') {
                            ee.target.appendChild(ee.dropNode);
                            Ext.Msg.alert('','Operación Movida con éxito');
                        } else {
                            Ext.Msg.alert('','No se puede mover la operación');
                        }
                         ee.dropNode.attributes['hidden'] = false;
                        //ee.cancel = true;
                    },
                    headers: {
                        'my-header': 'foo'
                    },
                    params: { foo: 'bar' }
                });
                ee.dropNode.attributes['hidden'] = true;
                ee.cancel = true;
            }
        }
        //dataUrl: 'treegrid-data.json'
    });

replaceCenterContent(tree_<?= $op_id ?>);

<?php if (!empty($scriptTags)): ?>
    </script>
    <?php
 endif;
/* END View grid.js      */
/* END of file grid.js.php */
/* Location: ./application/views/generals/grid.js.php */
?>