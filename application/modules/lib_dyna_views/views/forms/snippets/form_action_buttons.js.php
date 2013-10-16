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
 * Vista que provee las generacion de botones para formularios dentro de dyna_views.
 */
?>
    {
        id: '<?= $operation_name?>',
        text: '<?= $operation_name?>',
        icon: BASE_ICONS + '<?= $operation_icon?>',
        type:'submit',
        standardSubmit:true,
        handler:function(btn){
            if (form_<?= $operation_id?>.getForm().isValid()){
                try {
                    btn.disable();
                    Ext.getCmp('form_<?= $operation_id?>').getEl().mask();
                }
                catch (exception) { /*do nothing*/ }
                form_<?= $operation_id?>.getForm().timeout = 0;
                form_<?= $operation_id?>.getForm().submit({
                    submitEmptyText: false,
                    url: BASE_URL + '<?= $operation_url?>',
                    success: function(form_<?= $operation_id?>, action){
                        Ext.getCmp('form_<?= $operation_id?>').getEl().unmask();
                        var icon = Ext.MessageBox.ERROR;
                        var obj = Ext.util.JSON.decode(action.response.responseText);
                        if (obj.response.result){
                            icon = Ext.MessageBox.INFO;
                            <?php if($replace == 'window'):?>
                            Ext.getCmp('w_<?= $operation_id?>').close();
                            try {
                                for(var i=0; i < Ext.StoreMgr.items.length; i++){
                                    //actualiza solo el store necesitado
                                    
                                    //if ('GridStore_<?= $operation_id?>' == Ext.StoreMgr.items[i].storeId){

                                         Ext.StoreMgr.items[i].reload();
                                    
                                    //}
                                       
                                }
                            }
                            catch (exception) { /*do nothing*/}
                            <?php endif;?>
                        }
                        var func=false;
                        try {
                            if(!empty(obj.response.extra_vars)){
                                if(!empty(obj.response.extra_vars.newView)){
                                    func= function(){eval(obj.response.extra_vars.newView);}
                                }
                                if(!empty(obj.response.extra_vars.redirect)){
                                    func= function(){
                                        if(!empty(obj.response.extra_vars.redirect.window))
                                            Ext.getCmp(obj.response.extra_vars.redirect.window).close();
                                        getCenterContent(obj.response.extra_vars.redirect.url,obj.response.extra_vars.redirect.id);
                                    }
                                }
                            }
                        }catch (exception) { /*do nothing*/ }
                        Ext.Msg.show({
                            title: obj.response.title,
                            msg: obj.response.msg,
                            buttons: Ext.Msg.OK,
                            icon: icon,
                            minWidth: 300,
                            fn:func
                        });
                        try { btn.enable();}
                        catch (exception) { /*do nothing*/ }
                    },
                    failure: function(form_<?= $operation_id?>, action){
                        Ext.getCmp('form_<?= $operation_id?>').getEl().unmask();
                        Ext.Msg.show({
                            title: ' <?= $this->lang->line('message_failure_title')?>',
                            msg: ' <?= $this->lang->line('message_failure')?>',
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR,
                            minWidth: 300
                        });
                        try {btn.enable();}
                        catch (exception) { /*do nothing*/}
                    }
                });
            } else {
                Ext.Msg.show({
                    title: '<?= $this->lang->line('validation_error_title')?>',
                    msg: '<?= $this->lang->line('validation_error_message')?>',
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.WARNING,
                    minWidth: 300
                });
            }
        }
    }
<?php
/* END View form_button.js      */
/* END of file form_button.js.php */
/* Location: ./application/modules/lib_dyna_views/views/forms/snippets/form_button.js.php */
?>