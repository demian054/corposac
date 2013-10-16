<?=$js_generated?>
	var cpCombo=Ext.getCmp('combo_<?=$opId?>_3040');

<? if($rol_type=='CGP'):?>
	
	function setCpData(response){
		Ext.getCmp('form_<?=$opId?>').getForm().setValues(Ext.decode(response.responseText));
	}	
	cpCombo.show();
	cpCombo.setEditable(true);
	cpCombo.setWidth(350); 
	cpCombo.on('select', function(){
		var ops={
				method:'GET',
				url:BASE_URL+'widgets/widget_ficha_cp/process',
				success:setCpData,
				params:{
					cpId:cpCombo.getValue()
				}				
			}
		Ext.Ajax.request(ops);	
	});
<? else: ?>
	cpCombo.hide();
<? endif; ?>