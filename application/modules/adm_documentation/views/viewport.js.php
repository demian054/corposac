<script type="text/javascript">
	var layout_main = new Ext.Viewport({
	layout: 	'border', 
	renderTo: 	Ext.getBody(),
	items: [
		{region: 	'north', 
		 height: 	90, 
		 border: 	true,
		 html: 		'<?=$this->load->view('header.js.php')?>'
		}, 
        {region:			'west', 
		 xtype:				'panel', 
		 layout:			'fit',
		 width:				300, 
		 border:			true, 
		 margins:			'0 0 0 5', 
		 //collapseMode: 	'mini',
		 //split:			true,
		 items:				[west_menu]
	    },
        {xtype:		'panel',
         region:	'center',
         id:		'center_content_documentation',
         width:		'100%',
		 height:	'100%',
         layout:	'fit',
		 autoScroll:true,
		 border:	true
        },
        {region:	'south',
         autoHeight:true, 
		 autoHeight:true, 
		 border:	true,
		 html:		'<?=$this->load->view('footer.js.php')?>',//Cambiar para que apunte footer original
		 margins:	'0 0 5 0'
		 //collapseMode: 	'mini',
		 //split: 	 true
		}                 
	]
	});
    CENTER_CONTENT_DOCUMENTATION = Ext.getCmp('center_content_documentation');
	layout_main.show();	
</script>