
//<script>
<?php
$this->load->view('template/tab_1.js.php');
$this->load->view('template/tab_2.js.php');
?>
    
    tab_panel = new Ext.TabPanel({
        activeTab:0,
        items:[grid_panel_1, panel_tab_2],
        height:295,
        bbar: new Ext.Toolbar({
            items:[
                '->',
                {
                    text:'boton',
                    handler:function(btn){
                        var columns = Ext.util.JSON.encode(getColumns());
                        
                        Ext.Ajax.request({ 
                            url: BASE_URL + 'adm_report/template/PL_getExecuteColumns',
                            method:'POST', 
                            params:{columns:columns},
                            success: function(response,options){ 
                                
                                eval(response.responseText);
                                var panel_2 = Ext.getCmp('panel_2');
                                Grid_.setHeight(205);
                                panel_2.removeAll();
                                panel_2.add(Grid_);
                                panel_2.doLayout();
                                
                            },
                            failure: function(response,options){  
                                Ext.Msg.Show('Error','Error de peteción al servidor.');
                            },
                            scope:this 
                        });
                    }
                }
            ]
        })
    });

    
    //</script>
