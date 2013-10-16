<script type="text/javascript">
	
	var tree = {
		xtype:		'treepanel',
		id:			'treepanel',
		autoScroll:	true,
		root:		<?=$tree?>
	}
	
	new Ext.Window({
		title:		'ARBOL',
		height:		200,
		width:		200,
		layout:		'fit',
		border:		'false',
		title:		'Treesito',
		items:		tree
	}).show();
	
</script>
