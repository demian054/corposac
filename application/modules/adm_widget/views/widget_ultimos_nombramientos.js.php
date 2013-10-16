<?=$js_generated?>
	 var myGrid=Ext.getCmp('Grid_<?=$opId?>');
<? if($rol_type=='CGP'):?>
	 myGrid.getColumnModel().setColumnWidth(0, 350);	
<? else:?>	
	 myGrid.getColumnModel().setHidden(0, true);	
<? endif; ?>