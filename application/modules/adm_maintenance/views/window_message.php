<script type="text/javascript">
Ext.onReady(function() {

	var aux = new Ext.Panel({
		layout:'column',
		border:false,
		bodyStyle: 'background:#e8e8e8;',
		items: [
			{width: 180,
			 border:false,
			 html:'<div style="background-color:#e8e8e8; "><img src="'+BASE_URL+'assets/img/logo_login.png"/></div>'
			}
			,		
			{
				border:false,
				width:420,
				height:160,
				padding: '10',
				bodyStyle: 'background:#e8e8e8;font-weight:bold;font-size:14px;text-align:justify',
				html:'<?= $message ?>',
				buttons: [
					{
						text:'Cerrar',
						icon:BASE_ICONS + 'cancel.png',
						handler:function(){
							Ext.getCmp('winMaintenance').destroy();
						}
					}
				]
			}
			
		]
	});

	var winLogin = new Ext.Window({
		title: 'Notificacion de Mantenimiento',
		id: 'winMaintenance',
		layout: 'fit',
		modal: true,
		width: 620,
		height: 200,
		y: 80,
		resizable: false,
		closable: true,
		items:[aux]

	});

	winLogin.show();
});
</script>
