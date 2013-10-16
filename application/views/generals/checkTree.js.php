<?php
/**
 * @package views
 * @subpackage generals
 *
 * @author      Jesus Farias Lacroix <jesus.farias@gmail.com>,
 * 		Juan C. Lopez <jlopez@rialfi.com>,
 * 		Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de checkTree dentro de dyna_views.
 */
?>
<script type="text/javascript">
    Ext.onReady(function() {

        //Creamos las configuraciones generales del Nodo Padre
        var rootNode = {
            id: 'RootNode',
            text: 'Root Node',
            expanded: true,
            nodeType:'async',
            uiProvider:false,

            children:[
                {id: 1, text: 'Child 1', children: [
                        {id: 2, text: 'Grand Child 1-1', leaf: true},
                        {id: 3,	text: 'Grand Child 1-2', leaf: true}]},
                {id: 4, text: 'Child 2', leaf: true},
                {id: 5, text: 'Child 3', children: [
                        {id: 6, text: 'Grand Child 3-1', leaf: true},
                        {id: 7, text: 'Grand Child 3-2', leaf: true}]}
            ]
        };

        //Instanciamos el componente customizado checkTree de ExtJS
        var tree = new Ext.ux.tree.CheckTreePanel({
            title: 'Experimental Tree',
            //isFormField:true,
            height: 300,
            width: 400,
            useArrows:true,
            expandOnCheck: true,
            autoScroll:true,
            animate:true,
            //enableDD:true,
            containerScroll: true,
            rootVisible: false,
            //frame: true,
            //root: rootNode,
            root: rootNode,
            cascadeCheck: 'all',
            bubbleCheck: 'all'
        });

        //Instanciamos el componenete window de Ext JS.
        var winLogin = new Ext.Window({
            title: 'EXPERIMENTAL',
            id: 'winLogin',
            layout: 'fit',
            width: 500,
            height: 600,
            y: 100,
            resizable: false,
            closable: false,
            items: tree
        });

        //Mostramos el componete window
        winLogin.show();

        //Expandimos el root
        tree.getRootNode().expand(true);

    });
</script>
<?php
/* END View checkTree      */
/* END of file checkTree.js.php */
/* Location: ./application/views/general/checkTree.js.php */
?>