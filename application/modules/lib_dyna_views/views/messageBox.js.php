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
 * Vista que provee las generacion de messageBox dentro de dyna_views.
 */
//Definimos el label de los botonos
?>

Ext.MessageBox.buttonText.yes = "Sí";
Ext.MessageBox.buttonText.cancel = "Cancelar";

<?php
switch ($type):
    case 'prompt':break;
    case 'confirm':break;

    case 'alert':
?>
        Ext.Msg.alert('<?= $title ?>', '<?= $msg ?>');
<?php
        break;
    default:
        ?>
        Ext.Msg.show({
            minWidth: 300,
            title:'<?= $title ?>',
            msg: '<?= $msg ?>',
            buttons: Ext.Msg.<?= $buttons ?>,
            fn: <?= (!empty($callback)) ? $callback : 'false' ?>
            icon: Ext.MessageBox.<?= $icon ?>
        });
<?php
        break;
endswitch;

/* END Class messageBox.js      */
/* END of file messageBox.js.php */
/* Location: ./application/modules/lib_dyna_views/views/messageBox.js.php */
?>