<?=$js_generated?>
	 
<? if($rol_type=='CGP'):?>
	var myColumnModel=Ext.getCmp('groupingGrid_<?=$opId?>').getColumnModel();
	
	myColumnModel.setColumnWidth(1, 85);	
	myColumnModel.setColumnWidth(2, 250);	
	myColumnModel.setColumnWidth(3, 175);	
	myColumnModel.getColumnById(myColumnModel.getColumnId(3)).align='center';
	myColumnModel.setColumnTooltip(3,'Porcentaje de Campos dinamicos llenos en la entidad de Cuerpos policiales'); 
		
	myColumnModel.setColumnWidth(4, 175);	
	myColumnModel.getColumnById(myColumnModel.getColumnId(4)).align='center';
	myColumnModel.setColumnTooltip(4,'Porcentaje de Campos dinamicos llenos en la entidad Departamentos.'); 
	
	myColumnModel.setColumnWidth(5, 175);
	myColumnModel.getColumnById(myColumnModel.getColumnId(5)).align='center';
	myColumnModel.setColumnTooltip(5,'Porcentaje de Campos dinamicos llenos en la entidad Instalaciones'); 
	
	myColumnModel.setColumnWidth(6, 175);
	myColumnModel.getColumnById(myColumnModel.getColumnId(6)).align='center';
	myColumnModel.setColumnTooltip(6,'Porcentaje de Campos dinamicos llenos en la entidad Funcionarios'); 
<? else:?>	
	var myColumnModel=Ext.getCmp('Grid_<?=$opId?>').getColumnModel();
	myColumnModel.setHidden(0, true); 
	myColumnModel.setColumnWidth(1, 250);	
	
	myColumnModel.setColumnWidth(2, 175);	
	myColumnModel.getColumnById(myColumnModel.getColumnId(2)).align='center';
	myColumnModel.setColumnTooltip(2,'Porcentaje de Campos dinamicos llenos en la entidad de Cuerpos policiales');	
	
	myColumnModel.setColumnWidth(3, 175);	
	myColumnModel.getColumnById(myColumnModel.getColumnId(3)).align='center';
	myColumnModel.setColumnTooltip(3,'Porcentaje de Campos dinamicos llenos en la entidad Departamentos.'); 
	
	myColumnModel.setColumnWidth(4, 175);
	myColumnModel.getColumnById(myColumnModel.getColumnId(4)).align='center';
	myColumnModel.setColumnTooltip(4,'Porcentaje de Campos dinamicos llenos en la entidad Instalaciones'); 
	
	myColumnModel.setColumnWidth(5, 175);
	myColumnModel.getColumnById(myColumnModel.getColumnId(5)).align='center';
	myColumnModel.setColumnTooltip(5,'Porcentaje de Campos dinamicos llenos en la entidad Funcionarios'); 
	
<? endif; ?>