<?= $dynaFrom; ?>

var ffin_window = Ext.getCmp('ffin_window_664_3059') || Ext.getCmp('ffin_window_665_3059');
ffin_window.on('blur',function(){
	var finicio_maintenance = Ext.getCmp('finicio_maintenance_664_3060') || Ext.getCmp('finicio_maintenance_665_3060');
	finicio_maintenance.setValue(ffin_window.getValue());
});