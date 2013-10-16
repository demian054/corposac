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
 * Vista que provee las generacion de toolbar dentro de dyna_views.
 */
//Creamos e inicializamos las variables a ser empleadas.
?>
var topBar_<?= $opId ?>    = false;
var bottomBar_<?= $opId ?> = false;

<?php //Añadimos los botones al topbar asi como el searchType
if (!empty($tbar) || !empty($searchType)):
    ?>
    var topBar_<?= $opId ?>  = new Ext.Toolbar({
        id:     'topBar_<?= $opId ?>'
        <?= (!empty($tbar)) ? ",items:$tbar" : '' ?>
    });
<?php
endif;
//Añadimos los botones al bottonbar asi como el searchType
if (!empty($bbar)):
?>
    var bottomBar_<?= $opId ?>  = new Ext.Toolbar({
        id:     'bottomBar_<?= $opId ?>',
        items:  <?= $bbar ?>
    });
<?php
 endif;
/* END View toolbar      */
/* END of file toolbar.js.php */
/* Location: ./application/modules/lib_dyna_views/views/toolbar.js.php */
?>