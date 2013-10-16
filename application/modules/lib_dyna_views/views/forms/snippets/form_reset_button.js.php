<?php
/**
 * @package lib_dyna_views
 * @subpackage views/forms/snippets
 *
 * @author      Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion del boton limpiar en los formularios de dyna_views.
 */
?>
    {
        id:         'buttonCancelar_<?= $operation_id ?>',
        text:       'Limpiar',
        formBind:   true,
        icon:       BASE_ICONS + 'broom-minus-icon.png',
        itemCls:    'centrado',
        handler:    function(){
            form_<?= $operation_id ?>.items.each(function(element){
                elementType=element.getXType();
                if((elementType == 'combo')) {
                    element.store.suspendEvents(false);
                    if(!empty(element.originalValue)){
                        if((typeof combo_store != 'undefined') && (combo_store != null)) {
                            if(!empty(combo_store[(element.getId())])) {
                                element.store.loadData(combo_store[(element.getId())]);
                                element.enable();
                            }
                        }
                    }

                    if(element.bdDisabled) element.disable();
                    else element.enable();

                    element.setVisible(!element.bdHidden);
                    element.setReadOnly(element.bdReadOnly);
                    element.store.resumeEvents(false);
                }else if(elementType == 'itemselector'){
                    element.suspendEvents(false);
                    var id1=(element.getId())+'_multi_from';
                    var id2=(element.getId())+'_multi_to';
                    if((typeof combo_store != 'undefined') && (combo_store != null)) {
                        var fromData=(empty(combo_store[(id1)]))? []:combo_store[(id1)];
                        var toData=(empty(combo_store[(id2)]))? []:combo_store[(id2)];
                        element.fromMultiselect.store.loadData(fromData);
                        element.toMultiselect.store.loadData(toData);
                    }

                    if(element.bdDisabled) element.disable();
                    else element.enable();

                    element.setVisible(!element.bdHidden);
                    element.setReadOnly(element.bdReadOnly);
                    element.resumeEvents(false);
                }
            });
            form_<?= $operation_id ?>.getForm().reset();
        }
    }
<?php
/* END View form_button.js      */
/* END of file form_button.js.php */
/* Location: ./application/modules/lib_dyna_views/views/forms/snippets/form_button.js.php */
?>