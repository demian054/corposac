<script type="text/javascript">

	var globalText=false;
    var tree = {
		xtype:			'treepanel',
		id:				'doc_treepanel',
		autoScroll:		true,
		root:			<?= $tree ?>,
		loader: new Ext.tree.TreeLoader({
			preloadChildren: true,
			clearOnLoad: false
		}),
		listeners:{ 
	       
			click: function(element){				
				getCenterDoc(element.id);
			}
		}
    }
			
    var west_menu = new Ext.Panel({
		title:			'Documentaci&oacute;n',
		layout:			'fit',
		layoutConfig: {
			titleCollapse:		true,
			animate:			true,
			activeOnTop:		false
		},
		items:			tree,
		tbar: new Ext.Toolbar({
			items: [
				{xtype:'textfield', 
					emptyText:'Buscar...',
					width:'250',
					id:'searchDocTxt',
					enableKeyEvents:true,
					listeners:{
						keyup:function(t){
							searchInTree(t.getValue());
						}
					}
				},
				{xtype:'button', 
					icon:BASE_ICONS+'zoom.png',
					handler:function(){
						var strSearch=Ext.getCmp('searchDocTxt').getValue();
						searchInTree(strSearch);	
					}
				},
			]
        })	
    });
	
	
	function getCenterDoc(id){
		Ext.Ajax.request({
			url: 'documentacion/detail/'+id,
			method: 'POST',
			success: function(response) {
			
				var aux = new Ext.Panel({
					id:    'panelHtml',
					layout:'fit',
					autoHeight:true,
					border:false,
					frame:false,
					html: response.responseText
				});

				CENTER_CONTENT_DOCUMENTATION.removeAll();
				CENTER_CONTENT_DOCUMENTATION.add(aux);
				CENTER_CONTENT_DOCUMENTATION.doLayout();
			},
			failure: function(){
		    

				Ext.Msg.show({   
					title: 'Error',
					msg: 'Error en la Peticion al Servidor',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR,
					minWidth: 300
				});
			}
		});
	
	}
	
	getCenterDoc(0);
	
	function showScreenshot(id, nombre){
		
		var window = new Ext.Window({

			id:				'window',
			shadow:			true,
			title:			nombre,
			resizable:		true,
		
			html:			'<img src= "<?= base_url() ?>assets/img/screenshots/'+id+'.png" />',
			layout:			'fit',
			modal:			true
		});

		window.show();
	}
	
	var tFilter = new Ext.ux.tree.TreeFilterX(Ext.getCmp('doc_treepanel'));
	
	function searchInTree(strSearch){
		
		if(strSearch.length){
			re = new RegExp('.*' + strSearch + '.*', 'i');
			tFilter.clear();
			tFilter.filter(re, 'text');
		}
		else { 
			Ext.getCmp('doc_treepanel').getRootNode().collapseChildNodes(true);
			tFilter.clear();
		}
	}

</script>