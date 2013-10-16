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
 * Vista que provee las generacion de un campo tipo field dentro del toolbars para ser anaexada dentro de los componentes
 * grid o groupingGrid dentro de dyna_views.
 */

//Se el tipo de componenete search es 'S' creamos un search Tipo textField
if ($searchType == 'S'): ?>

    var search_<?= $opId ?> = new Ext.app.SearchField({
        store: <?= $storeName . $opId ?>,
        params: {start: 0, limit: LONG_LIMIT },
        width: 180,
        id: 'fieldSearch_<?= $opId ?>',
        name: 'searchfield',
        vtype: 'valid_alpha_numeric_space'
    });

<?php
//Si el Tipo de componenete es 'D' es un search Tipo DateField
elseif ($searchType == 'D'):
?>

    var search_<?= $opId ?> = new Ext.app.SearchFieldDate({
        store: <?= $storeName . $opId ?>,
        params: {start: 0, limit: LONG_LIMIT },
        width: 180,
        id: 'fieldSearch_<?= $opId ?>',
        name: 'searchfield'
    });

<?php endif; ?>

<?php if(!empty($searchType)): ?>
//Obtenermos el compoennete Tbar y agregamos el tipo de search creado.
var tBar= Ext.getCmp('topBar_<?= $opId ?>');
if(!empty(tBar.items))
    <?= $elem . $opId ?>.getTopToolbar().add('-',search_<?= $opId ?>);
else
    <?= $elem . $opId ?>.getTopToolbar().add(search_<?= $opId ?>);
//console.log(tBar.items);
<?php
endif;

/* END View search.js      */
/* END of file search.js.php */
/* Location: ./application/modules/lib_dyna_views/views/search.js.php */
?>