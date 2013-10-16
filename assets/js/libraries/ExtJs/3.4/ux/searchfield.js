/*
 * Ext JS Library 2.0.2 Copyright(c) 2006-2008, Ext JS, LLC. licensing@extjs.com
 * 
 * http://extjs.com/license
 */

Ext.app.SearchField = Ext.extend(Ext.form.TwinTriggerField, {
			initComponent : function() {
				Ext.app.SearchField.superclass.initComponent.call(this);
				this.on('specialkey', function(f, e) {
							if (e.getKey() == e.ENTER) {
								this.onTrigger2Click();
							}
						}, this);
			},

			validationEvent : false,
			validateOnBlur : false,
			trigger1Class : 'x-form-clear-trigger',
			trigger2Class : 'x-form-search-trigger',
			hideTrigger1 : true,
			width : 180,
			emptyText : 'Buscar ...',
			hasSearch : false,
			paramName : 'searchfield',

			onTrigger1Click : function() {
				if (this.hasSearch) {
					this.el.dom.value = '';
					var o = {
						start : 0,
						limit : LONG_LIMIT
					};
					this.store.baseParams = this.store.baseParams || {};
					this.store.baseParams[this.paramName] = '';
					this.store.reload({
								params : o
							});
					this.triggers[0].hide();
					this.hasSearch = false;
				}
			},

			onTrigger2Click : function() {
				var v = this.getRawValue();
				if (v.length < 1) {
					this.onTrigger1Click();
					return;
				}
				if (!(/^[a-z0-9\u00D1\u00F1\u00C0\u00E0\u00C1\u00E1\u00E9\u00C9\u00CD\u00ED\u00D3\u00F3\u00DA\u00FA\u00C4\u00E4\u00CB\u00EB\u00CF\u00EF\u00D6\u00F6\u00DC\u00FC\u0178\u00FF\.,\s\/\-()]+$/i).test(v)) {
					Ext.Msg.show({
						minWidth: 300,
						title:'Error!',
						msg: 'Este campo s\u00F3lo debe contener letras, n\u00FAmeros y espacios',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
					this.onTrigger1Click();
					return;
				}
				var o = {
					start : 0,
					limit : LONG_LIMIT
				};
				this.store.baseParams = this.store.baseParams || {};
				this.store.baseParams[this.paramName] = v;
				this.store.reload({
							params : o
						});
				this.hasSearch = true;
				this.triggers[0].show();
			}
		});
		
Ext.app.SearchFieldDate = Ext.extend(Ext.form.DateField, {
			initComponent : function() {
				Ext.app.SearchFieldDate.superclass.initComponent.call(this);
				this.on('specialkey', function(f, e) {
							if (e.getKey() == e.ENTER) {
								this.onTrigger2Click();
							}
						}, this);
						
				this.on('select', function() {
								this.onTrigger2Click();
						}, this);
			},

			validationEvent : false,
			validateOnBlur : false,
			trigger1Class : 'x-form-clear-trigger',
			trigger2Class : 'x-form-search-trigger',
			hideTrigger1 : true,
			width : 180,
			editable: false,
			emptyText : 'Buscar ...',
			hasSearch : false,
			paramName : 'searchfield',
			autoShow: false,

			onTrigger1Click : function() {
				if (this.hasSearch) {
					this.el.dom.value = '';
					var o = {
						start : 0,
						limit : LONG_LIMIT
					};
					this.store.baseParams = this.store.baseParams || {};
					this.store.baseParams[this.paramName] = '';
					this.store.reload({
								params : o
							});
					this.triggers[0].hide();
					this.hasSearch = false;
				}
			},

			onTrigger2Click : function() {
				var v = this.getRawValue();
				if (v.length < 1) {
					this.onTrigger1Click();
					return;
				}
				var o = {
					start : 0,
					limit : LONG_LIMIT
				};
				this.store.baseParams = this.store.baseParams || {};
				this.store.baseParams[this.paramName] = v;
				this.store.reload({
							params : o
						});
				this.hasSearch = true;
				this.triggers[0].show();
			}
		});