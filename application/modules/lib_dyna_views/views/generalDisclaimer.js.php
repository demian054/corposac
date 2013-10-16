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
 * Vista que provee las generacion de disclaimer dentro de dyna_views.
 */
?>
// Arreglo que contiene los fieldSet que se agregan en la ventana
var arrayObject = new Array();

// Si se va a agregar el fieldSet adicional
<? if (!empty($fieldTSetTitle2)): ?>

    // Se genera el formulario que se va a agregar en el fieldSet2
    <?= $formDisclaimer ?>

    var fieldSet2_<?= $opId ?> = new Ext.form.FieldSet({
        id:         'fieldSet2_<?= $opId ?>',
        title:      '<?= $fieldTSetTitle2 ?>',
        minHeight:  300,
        maxHeight:  500,
        autoScroll: true,
        items:      [form_<?= $opId ?>]
    });

    // Se agrega el fieldSet adicional al arreglo
    arrayObject.unshift(fieldSet2_<?= $opId ?>);

<? else: ?>
            var form_<?= $opId ?>;
<? endif; ?>

// Datos del FieldSet Principal
var checkBox_<?= $opId ?> = new Ext.form.Checkbox({
    id: 'checkBox_<?= $opId ?>',
    name:   'checkbox'
});

//Creamos un panel ExtJs
var panel1_<?= $opId ?> = new Ext.Panel({
    layout:     'fit',
    id:         'panel1_<?= $opId ?>',
    frame:      true,
    minHeight:  200,
    maxHeight:  300,
    autoScroll: true,
    title:      '<?= $panelTitle1 ?>',
    html:       '<?= $contentHtml1 ?>',
    bbar: new Ext.Toolbar({
        items: [
            checkBox_<?= $opId ?>,
            {text: 'Acepto el Contrato',disabled: true}
        ]
    }),
});

//Creamos nu fielSet
var fieldSet1_<?= $opId ?> = new Ext.form.FieldSet({
    id: 'fieldSet1_<?= $opId ?>',
    title:  '<?= $fieldTSetTitle1 ?>',
    items:  [panel1_<?= $opId ?>]
});

// Se agrega el fieldSet principal al arreglo
arrayObject.unshift(fieldSet1_<?= $opId ?>);

// Ventana donde se cargan los FieldSet
var window_<?= $opId ?> = new Ext.Window({
    id: 'window_<?= $opId ?>',
    shadow: true,
    title: '<?= $windowTitle ?>',
    collapsible: true,
    maximizable: true,
    width: 640,
    minWidth: 300,
    minHeight: 200,
    layout: 'auto',
    modal:true,
    autoScroll: true,
    overflow:'auto',
    plain: true,
    bodyStyle: 'padding:5px;',
    buttonAlign: 'center',
    items: [arrayObject],
    buttons: [new Ext.Button({
        id: 'button_aceptar_<?= $opId ?>',
        text: '<?= $buttonName ?>',
        icon: '<?= $icon ?>',
        type: 'submit',
        standardSubmit:true,
        handler: function() {

            // Verificacion del checkbox
            if(checkBox_<?= $opId ?>.checked) {
                showMask();

                // Si el formulario del disclaimer existe
                if(form_<?= $opId ?>) {
                    form_<?= $opId ?>.getForm().submit({
                        url: '<?= $url ?>',
                        success: function(form_<?= $opId ?>, action) {
                            hideMask();
                            var icon = Ext.MessageBox.ERROR;
                            var obj = Ext.util.JSON.decode(action.response.responseText);

                            // Si el proceso de logica de negocio es exitoso
                            if (obj.response.result) {
                                Ext.getCmp('Grid_<?= $parentId ?>').store.reload();
                                icon = Ext.MessageBox.INFO;
                                window_<?= $opId ?>.close();
                            }

                            Ext.Msg.show({
                                title: obj.response.title,
                                msg: obj.response.msg,
                                buttons: Ext.Msg.OK,
                                icon: icon,
                                minWidth: 300
                            });
                        },
                        failure: function(form_<?= $opId ?>, action) {
                            hideMask();
                            Ext.Msg.show({
                                title: '<?= $this->lang->line('message_failure_title')?>',
                                msg: '<?= $this->lang->line('message_failure') ?>',
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR,
                                minWidth: 300
                            });
                        }
                    });
                }

                // Si el formulario no existe debe enviar la peticion al controlador especifico
                else {
                    Ext.Ajax.request({
                        url: '<?= $url ?>',
                        method: 'POST',
                        success: function(response) {
                            hideMask();
                            var icon = Ext.MessageBox.ERROR;
                            var obj = Ext.util.JSON.decode(response.responseText);

                            // Si el proceso de logica de negocio es exitoso
                            if (obj.response.result) {
                                Ext.getCmp('Grid_<?= $parentId ?>').store.reload();
                                icon = Ext.MessageBox.INFO;
                                window_<?= $opId ?>.close();
                            }

                            Ext.Msg.show({
                                title: obj.response.title,
                                msg: obj.response.msg,
                                buttons: Ext.Msg.OK,
                                icon: icon,
                                minWidth: 300
                            });
                        },
                        failure: function(){
                            hideMask();
                            Ext.Msg.show({
                                title: '<?= $this->lang->line('message_failure_title') ?>',
                                msg: '<?= $this->lang->line('message_failure') ?>',
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR,
                                minWidth: 300
                            });
                        }
                    });
                }
            } else {
                Ext.Msg.show({
                    title: '<?= $this->lang->line('disclaimer_acept_title')?>',
                    msg: '<?= $this->lang->line('disclaimer_acept')?>',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR,
                    minWidth: 300
                });
            }
        }
    }), <?php /* Cierro boton 1.*/ ?>
    new Ext.Button({
        id: 'button_cancelar_<?= $opId ?>',
        text: '<?= $this->lang->line('cancel_button')?>',
        handler: function() {
            window_<?= $opId ?>.close();
        }
    })] <?php /* Cierro boton 2. Cierro Buttons.*/ ?>
}); <?php /* Cierro window .*/ ?>

window_<?= $opId ?>.show();

// Creacion de la mascara
mask = new Ext.LoadMask(Ext.getCmp('window_<?= $opId ?>').body);

// Funcion para mostrar la mascara
function showMask() {
    mask.show();
    Ext.each(window_<?= $opId ?>.buttons, function(button) {
        button.disable();
    });
}

// Funcion para ocultar la mascara
function hideMask() {
    mask.hide();
    Ext.each(window_<?= $opId ?>.buttons, function(button) {
        button.enable();
    });
}
<?php
/* END View generalDisclaimer.js      */
/* END of file generalDisclaimer.js.php */
/* Location: ./application/modules/lib_dyna_views/views/generalDisclaimer.js.php */
?>