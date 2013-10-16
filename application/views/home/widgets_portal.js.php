<script type="text/javascript">
	/* var tools = [{
        id:'gear',
        handler: function(){
            Ext.Msg.alert('Message', 'The Settings tool was clicked.');
        }
    },{
        id:'close',
        handler: function(e, target, panel){
            panel.ownerCt.remove(panel, true);
        }
    }];*/
	
	<?=$widgets_portal['widgets_common_js_snippets']?>
	
	
		
	var <?=WIDGETS_PORTAL?>={
		xtype:'portal',
		region:'center',
		id:'portal_id',
		bodyStyle: "background-image:url(/assets/img/tile.jpg) !important",
		<?=$widgets_portal['widgets_portal_config']?>
		/*
		* Uncomment this block to test handling of the drop event. You could use this
		* to save portlet position state for example. The event arg e is the custom 
		* event defined in Ext.ux.Portal.DropZone.
		*/
		,listeners:{
		'drop': function(e){
				console.log(arguments);
				var ops={
					url:BASE_URL+e.panel.position_url+'/'+e.panel.widget_id+'/'+e.columnIndex+'/'+e.position,
					method:'GET'
				}
				Ext.Ajax.request(ops);
				//Ext.Msg.alert('Widget Dropped', e.panel.id + '<br />Column: ' + e.columnIndex + '<br />Position: ' + e.position);	
				
			}
		}	
	}			
</script>	