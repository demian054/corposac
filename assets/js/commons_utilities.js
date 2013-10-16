
function do_logout() {
	Ext.Ajax.request({
		url: BASE_URL + 'lib_tank_auth/auth/logout',
		method: 'POST',
		success: function(xhr) {
			window.location = BASE_URL + 'lib_tank_auth/auth/login';
		}
	});
}

function replaceCenterContent(centerElement){
	CENTER_CONTENT.removeAll();
	CENTER_CONTENT.add(centerElement);
	Ext.getCmp('center_card').layout.setActiveItem(0);
	CENTER_CONTENT.doLayout();
}

function replaceChild(tabId,contentElement){
	var myTab=Ext.getCmp(tabId);
	myTab.removeAll();
	myTab.add(contentElement);
	myTab.doLayout();
}

function getCenterContent(operation, id, triggerObj){
	var tempOnClick=false;
	if(!empty(triggerObj)){
		tempOnClick=triggerObj.target.onclick;
		triggerObj.target.onclick=null;
	}
	if(empty(operation)) return;
	var url=BASE_URL;
	url+=(typeof(operation)=='object')?operation.url:operation;
	var params=(empty(id))?false:({
		id:id
	});

	// Funcionalidad para verificar si el identificador contempla multiples parametros para la url
	arr_extra_params = Ext.decode(id)
	if(typeof(arr_extra_params) == 'object') {
		arr_params = new Array();
		for(index in arr_extra_params)
			arr_params.push(index+'='+arr_extra_params[index]);
		params = arr_params.join('&');
	}

	var ops={
		url: url,
		params: params,
		method: 'GET',
		success: function(response) {
			eval(response.responseText);
			if(!empty(tempOnClick)){
				triggerObj.target.onclick=tempOnClick;
			}

		}
	}
	Ext.Ajax.request(ops);
}

function evalTabContent(){
	eval(arguments[2].responseText);
	return false;
}


function empty(val){
	if(val==null) return true;
	switch(typeof(val)){
		case 'number':
			return (val==0);
		case 'boolean':
			return (val==false);
		case 'string':
			if (val=="" || val.match(/^\s*$/) ) return true;
			else return false;
		case 'object':
			return (val.length==0 );
		case 'function':
			return false;
		case 'undefined':
			return true;
	}
}


function loadChildCombo(combo, record){
	var childCombo=Ext.getCmp(combo.child);
	if(empty(childCombo.multiselects)){
		clearChildCombos(childCombo);
		childCombo.store.load({
			params:{
				id:combo.value
				}
			});
}else {
	multiChild=Ext.getCmp('multi_'+combo.child);
	multiChild.store.removeAll();
	multiChild.store.load({
		params:{
			id:combo.value
			}
		});
}
}


function clearChildCombos(combo){
	try{
		combo.clearValue();
                //if (combo.store != null)
		combo.store.removeAll();
		combo.disable();
	}catch(e){
	//do nothing
	}
	if(!empty(combo.child)){
		var childCombo=Ext.getCmp(combo.child);
		clearChildCombos(childCombo);
	}

}



function confirmAction(url, recordId, gridId, subject, confType){
	var title = message = '';
	switch(confType){
		case 'delete':
			title='Confirmar Eliminacion';
			message='Esta seguro(a) de Eliminar '+subject+'?';
			break;

		case 'deactivate':
			title='Confirmar Desactivacion';
			message='Esta seguro(a) de Desactivar '+subject+'?';
			break;

		case 'activate':
			title='Confirmar Activacion';
			message='Esta seguro(a) de Activar '+subject+'?';
			break;
	}
	Ext.MessageBox.buttonText.yes = "Si";
	Ext.Msg.show({
		title: title,
		msg: message,
		buttons: Ext.Msg.YESNO,
		fn: function(btn){
			if(btn=='yes') deleteDBRecord(url, recordId, gridId);
		},
		minWidth: 300,
		icon: Ext.MessageBox.QUESTION
	});
}

function deleteDBRecord(url, recordId, gridId){
	var deleteDBRecordConn = new Ext.data.Connection();
	deleteDBRecordConn.request({
		url: url,
		method: 'POST',
		params:{
			id:recordId
		},
		//success: function(resp,opt){
		success: function(resp,opt){
			var icon = Ext.MessageBox.ERROR;
			var obj = Ext.util.JSON.decode(resp.responseText);
                        var func = false; //extra_vars
			if (obj.response.result){
				icon = Ext.MessageBox.INFO;
                                //Ext.getCmp(gridId).store.reload();
                                var cmpObj = Ext.getCmp(gridId);
                                if (cmpObj.store != undefined) {
                                    cmpObj.store.reload();
                                }
				//implementando extra_vars                                
                                try {
                                    if(!empty(obj.response.extra_vars)){
                                        if(!empty(obj.response.extra_vars.newView)){
                                            func= function(){eval(obj.response.extra_vars.newView);}
                                        }
                                        if(!empty(obj.response.extra_vars.redirect)){
                                            func= function(){
                                                if(!empty(obj.response.extra_vars.redirect.window)) Ext.getCmp(obj.response.extra_vars.redirect.window).close();
                                                getCenterContent(obj.response.extra_vars.redirect.url,obj.response.extra_vars.redirect.id);
                                            }
                                        }
                                    }
                                }catch (exception) { /*do nothing*/ }
                                //fin implementación extra_vars
			//console.log(Ext.getCmp(gridId).store);

			}
			Ext.Msg.show({
				title: obj.response.title,
				msg: obj.response.msg,
				buttons: Ext.Msg.OK,
				icon: icon,
				minWidth: 300,
                                fn: func //extra_vars
			});

		},
		failure: function(resp,opt){
			Ext.Msg.show({
				title: 'Error!',
				msg: 'Error en la Peticion al Servidor',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR,
				minWidth: 300
			});
		}
	});
}

function renderCountry(value, metaData, record, rowIndex, colIndex, store){
	if(value=='1') return 'Venezuela';
	else return value;
}


function deleteOption(index){
	Ext.getCmp('optionListGrid').store.removeAt(index);
	return;
}

//window.history.go(1);
//if (top.location != self.location) top.location = self.location.href;

function raiseMsgError(message){
	Ext.Msg.show({
		title: 'Error',
		msg: message,
		buttons: Ext.Msg.OK,
		icon: Ext.MessageBox.ERROR,
		minWidth: 300
	});
}

function closeAndReload(windowId,gridId){
	try{
		//Ext.getCmp(gridId).store.reload();
                var cmpObj = Ext.getCmp(gridId);
                if (cmpObj.store != undefined) {
                    cmpObj.store.reload();
                }
		Ext.getCmp(windowId).close();
	}catch(e){
	//do nothing
	}
}

function goHome(){
	Ext.getCmp('center_card').layout.setActiveItem(1);
}


// Formats a number with grouped thousands
//
// version: 1109.2015
// discuss at: http://phpjs.org/functions/number_format
// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// +     bugfix by: Michael White (http://getsprink.com)
// +     bugfix by: Benjamin Lupton
// +     bugfix by: Allan Jensen (http://www.winternet.no)
// +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
// +     bugfix by: Howard Yeend
// +    revised by: Luke Smith (http://lucassmith.name)
// +     bugfix by: Diogo Resende
// +     bugfix by: Rival
// +      input by: Kheang Hok Chin (http://www.distantia.ca/)
// +   improved by: davook
// +   improved by: Brett Zamir (http://brett-zamir.me)
// +      input by: Jay Klehr
// +   improved by: Brett Zamir (http://brett-zamir.me)
// +      input by: Amir Habibi (http://www.residence-mixte.com/)
// +     bugfix by: Brett Zamir (http://brett-zamir.me)
// +   improved by: Theriault
// +      input by: Amirouche
// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// *     example 1: number_format(1234.56);
// *     returns 1: '1,235'
// *     example 2: number_format(1234.56, 2, ',', ' ');
// *     returns 2: '1 234,56'
// *     example 3: number_format(1234.5678, 2, '.', '');
// *     returns 3: '1234.57'
// *     example 4: number_format(67, 2, ',', '.');
// *     returns 4: '67,00'
// *     example 5: number_format(1000);
// *     returns 5: '1,000'
// *     example 6: number_format(67.311, 2);
// *     returns 6: '67.31'
// *     example 7: number_format(1000.55, 1);
// *     returns 7: '1,000.6'
// *     example 8: number_format(67000, 5, ',', '.');
// *     returns 8: '67.000,00000'
// *     example 9: number_format(0.9, 0);
// *     returns 9: '1'
// *    example 10: number_format('1.20', 2);
// *    returns 10: '1.20'
// *    example 11: number_format('1.20', 4);
// *    returns 11: '1.2000'
// *    example 12: number_format('1.2000', 3);
// *    returns 12: '1.200'
// *    example 13: number_format('1 000,50', 2, '.', ' ');
// *    returns 13: '100 050.00'
// Strip all characters but numerical ones.
function number_format (number, decimals, dec_point, thousands_sep) {
	number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
	toFixedFix = function (n, prec) {
		var k = Math.pow(10, prec);
		return '' + Math.round(n * k) / k;
	};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	}
	if ((s[1] || '').length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1).join('0');
	}
	return s.join(dec);
}

function prepareGlobalMask(){
	mask_layout = new Ext.LoadMask(Ext.getCmp('view_port_main_layout').getEl(), {
		msg: 'Cargando'
	});
	Ext.Ajax.on('beforerequest', function(){
		mask_layout.show();
	});
	Ext.Ajax.on('requestcomplete', function(){
		mask_layout.hide();
	});
	Ext.Ajax.on('requestexception', function(){
		mask_layout.hide();
	});
}

function llamada(_url){
    Ext.Ajax.request({
        url: _url,
        success: function(data){
            alert('fino:'+ data.responseText);
        },
        failure: function(data){
            alert('error:'+ data);
        },
        headers: {
            'my-header': 'foo'
        },
        params: { foo: 'bar' }
    });
}

function getContent(json_obj) {
    var call_back = function (jobj) {
        //alert(jobj.method+ ' ' + jobj.params.id + ' hacia ' + jobj.params.pid);
        Ext.Ajax.request({
            url: jobj.url,
            method: jobj.method,
            params: jobj.params,
            success: function(resp,opt){
                var icon = Ext.MessageBox.ERROR;
                var obj = Ext.util.JSON.decode(resp.responseText);
                var func = false; //extra_vars
                if (obj.response.result) {
                        icon = Ext.MessageBox.INFO;
                        //Ext.getCmp(gridId).store.reload();
                        if (jobj.gridId) { 
                            var cmpObj = Ext.getCmp(jobj.gridId);
                            if (cmpObj.store != undefined) {
                                cmpObj.store.reload();
                            }
                        }
                        //implementando extra_vars                                
                        try {
                            if(!empty(obj.response.extra_vars)){
                                if(!empty(obj.response.extra_vars.newView)){
                                    func= function(){eval(obj.response.extra_vars.newView);}
                                }
                                if(!empty(obj.response.extra_vars.redirect)){
                                    func= function(){
                                        if(!empty(obj.response.extra_vars.redirect.window)) Ext.getCmp(obj.response.extra_vars.redirect.window).close();
                                        getCenterContent(obj.response.extra_vars.redirect.url,obj.response.extra_vars.redirect.id);
                                    }
                                }
                            }
                        }catch (exception) { /*do nothing*/ }
                        //fin implementación extra_vars
                //console.log(Ext.getCmp(gridId).store);

                    if (jobj.wClose) {
                        Ext.getCmp(jobj.wClose).close();
                    }
                }
                Ext.Msg.show({
                        title: obj.response.title,
                        msg: obj.response.msg,
                        buttons: Ext.Msg.OK,
                        icon: icon,
                        minWidth: 300,
                        fn: func //extra_vars
                });
            },
            failure: function(resp,opt){
                    Ext.Msg.show({
                            title: 'Error!',
                            msg: 'Error en la Peticion al Servidor',
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR,
                            minWidth: 300
                    });
            }
        });
    };    
    if (json_obj.confirm) {
	Ext.MessageBox.buttonText.yes = "Si";
	Ext.Msg.show({
		title: '',
		msg: json_obj.confirm,
		buttons: Ext.Msg.YESNO,
		fn: function(btn){
			if(btn == 'yes') call_back(json_obj);
		},
		minWidth: 300,
		icon: Ext.MessageBox.QUESTION
	});
    } else {
        
        call_back(json_obj);
        
    }
}



window.CU = {
    floatFieldRenderer: function(value) {
        return number_format(value, 2, ',', '.');
    },
    boolFieldRenderer: function(value) {
        return (value!=0) ? 'Sí' : 'No';
    },
    dateTimeFieldRenderer: function(value) {
        if((value != '') && (value != undefined) && (value != null)) {
            var cp = value.indexOf('.');
            if (cp != -1) value = value.substring(0,cp);
            value = value.replace('-', '/');
            value = value.replace('-', '/');
            date = new Date(value);
            return date.format('d/m/Y h:i:s a');
        } else
            return value;
    },
    showAlertError: function(_msg, _icon) {
        _icon = _icon || Ext.MessageBox.ERROR;
        Ext.Msg.show({
            title: (_icon == Ext.MessageBox.ERROR) ? 'Aviso': 'Información',
            msg: _msg,
            buttons: Ext.Msg.OK,
            icon: _icon,
            minWidth: 300
        });
    },
    showAlert: function(_msg, _title) {
        Ext.Msg.show({
            title: (_title || 'Aviso'),
            msg: _msg,
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox.INFO,
            minWidth: 300
        });
    },
    
    actualizarGrids: function (){
        for(var i=0; i < Ext.StoreMgr.items.length; i++){
            try {
                     Ext.StoreMgr.items[i].reload();
            }
            catch (exception) { /*do nothing*/}
        }
     }  
};