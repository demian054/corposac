/********************************* Validaciones de codeigniter  ******************************/

Ext.apply(Ext.form.VTypes, {
	// Validacion Integer
	integerMask: /^[\-+]?[0-9]+$/,
	integer: function(v) {
		return Ext.form.VTypes.integerMask.test(v);
	},
	integerText:'Debe ingresar s\u00F3lo n\u00FAmeros',
	
	// Validacion Numeric
	numericMask: /^[\-+\d\.]+$/,
	numeric: function (v) { 
		return (/^[\-+]?[0-9]*\.?[0-9]+$/).test(v);
	},
	numericText:'Debe ingresar s\u00F3lo n\u00FAmeros',
	
	// Validacion email
	valid_emailMask: Ext.form.VTypes.emailMask,
	valid_email: function(v) {
		return Ext.form.VTypes.email(v);
	},
	valid_emailText: 'Este campo debe ser una direcci\u00F3n de correo electr\u00F3nico con el formato "usuario@dominio.com"',
	
	//Validacion numeros naturales
	is_naturalMask: /^[0-9]+$/,
	is_natural: function (v) {
		return Ext.form.VTypes.is_naturalMask.test(v);
	},
	is_naturalText: 'Debe ingresar s\u00F3lo n\u00FAmeros',
	
	//Validacion Alpha con espacios
	valid_alpha_spaceMask: /^[a-z\u00D1\u00F1\u00C0\u00E0\u00C1\u00E1\u00E9\u00C9\u00CD\u00ED\u00D3\u00F3\u00DA\u00FA\u00C4\u00E4\u00CB\u00EB\u00CF\u00EF\u00D6\u00F6\u00DC\u00FC\u0178\u00FF\s]+$/i,
	valid_alpha_space: function (v) {
		return Ext.form.VTypes.valid_alpha_spaceMask.test(v);
	},
	valid_alpha_spaceText: 'Este campo s\u00F3lo debe contener letras y espacios',
	
	// Validacion Alpha con numeros y espacios
	valid_alpha_numeric_spaceMask:  /^[a-z0-9\u00D1\u00F1\u00C0\u00E0\u00C1\u00E1\u00E9\u00C9\u00CD\u00ED\u00D3\u00F3\u00DA\u00FA\u00C4\u00E4\u00CB\u00EB\u00CF\u00EF\u00D6\u00F6\u00DC\u00FC\u0178\u00FF\.,\s\/\-()]+$/i,
	valid_alpha_numeric_space: function (v) {
		return Ext.form.VTypes.valid_alpha_numeric_spaceMask.test(v);
	},
	valid_alpha_numeric_spaceText: 'Este campo s\u00F3lo debe contener letras, n\u00FAmeros y espacios',
	
	// Validacion de URL
	valid_urlMask: /[\w+&@#\/%\?=\~_|!:,.;]+$/i,
	valid_url: function (v) {
		return (/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%\?=\~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i).test(v);
		//return Ext.form.VTypes.valid_urlMask.test(v);
	},
	valid_urlText: 'Este campo debe ser una URL con el formato "http:/'+'/www.dominio.com"',
	
	// Validacion De fechas
//	valid_dateMask:/([0-3][0-9])\/([0-9]{1,2})\/([1-2][0-9]{3})/,
	valid_date: function (v){
//		if(Ext.form.VTypes.valid_dateMask.test(v))
//			return false;
//		else{
//			var strSeparator = strValue.substring(2,3);
//			var arrayDate = strValue.split(strSeparator); 
//			//create a lookup for months not equal to Feb.
//			var arrayLookup = {
//				'01' : 31,
//				'03' : 31, 
//				'04' : 30,
//				'05' : 31,
//				'06' : 30,
//				'07' : 31,
//				'08' : 31,
//				'09' : 30,
//				'10' : 31,
//				'11' : 30,
//				'12' : 31
//			}
//			var intDay = parseInt(arrayDate[0],10); 
//
//			//check if month value and day value agree
//			if(arrayLookup[arrayDate[1]] != null) {
//				if(intDay <= arrayLookup[arrayDate[1]] && intDay != 0)
//					return true; //found in lookup table, good date
//			}
//    
//			//check for February (bugfix 20050322)
//			//bugfix  for parseInt kevin
//			//bugfix  biss year  O.Jp Voutat
//			var intMonth = parseInt(arrayDate[1],10);
//			if (intMonth == 2) { 
//				var intYear = parseInt(arrayDate[2]);
//				if (intDay > 0 && intDay < 29) {
//					return true;
//				}
//				else if (intDay == 29) {
//					if ((intYear % 4 == 0) && (intYear % 100 != 0) || (intYear % 400 == 0)) {
//						// year div by 4 and ((not div by 100) or div by 400) ->ok
//						return true;
//					}   
//				}
//			}
//		}  
//		return false; //any other values, bad date
	},
//	valid_dateText: 'Debe ingresar una fecha valida con el formato dd/mm/yyyy',


	// Validacion De fechas y Hora
//	valid_date_timeMask:/([0-3][0-9])\/([0-9]{1,2})\/([1-2][0-9]{3})/,
	valid_date_time: function (v){
//		if(Ext.form.VTypes.valid_dateMask.test(v))
//			return false;
//		else{
//			var strSeparator = strValue.substring(2,3);
//			var arrayDate = strValue.split(strSeparator); 
//			//create a lookup for months not equal to Feb.
//			var arrayLookup = {
//				'01' : 31,
//				'03' : 31, 
//				'04' : 30,
//				'05' : 31,
//				'06' : 30,
//				'07' : 31,
//				'08' : 31,
//				'09' : 30,
//				'10' : 31,
//				'11' : 30,
//				'12' : 31
//			}
//			var intDay = parseInt(arrayDate[0],10); 
//
//			//check if month value and day value agree
//			if(arrayLookup[arrayDate[1]] != null) {
//				if(intDay <= arrayLookup[arrayDate[1]] && intDay != 0)
//					return true; //found in lookup table, good date
//			}
//    
//			//check for February (bugfix 20050322)
//			//bugfix  for parseInt kevin
//			//bugfix  biss year  O.Jp Voutat
//			var intMonth = parseInt(arrayDate[1],10);
//			if (intMonth == 2) { 
//				var intYear = parseInt(arrayDate[2]);
//				if (intDay > 0 && intDay < 29) {
//					return true;
//				}
//				else if (intDay == 29) {
//					if ((intYear % 4 == 0) && (intYear % 100 != 0) || (intYear % 400 == 0)) {
//						// year div by 4 and ((not div by 100) or div by 400) ->ok
//						return true;
//					}   
//				}
//			}
//		}  
//		return false; //any other values, bad date
	},
//	valid_date_timeText: 'Debe ingresar una fecha valida con el formato dd/mm/yyyy HH:ii',
	
	// Validacion De fechas
	valid_date_greater_than_todayMask:/([0-3][0-9])\/([0-9]{1,2})\/([1-2][0-9]{3})/,
	valid_date_greater_than_today: function (v){

		arr_date = v.split('/');
		
		date = arr_date[1]+'-'+arr_date[0]+'-'+arr_date[2];
		date = new Date(date);
		
		current_date = new Date();
		
		if(date.getTime() > current_date.getTime())
			return false;
		else
			return true;
	},
	valid_date_greater_than_todayText: 'Debe ingresar una fecha no mayor al dia de Hoy',	
	
	// Validacion alpha_dash de CodeIgniter
	alpha_dashMask: /^([-a-z0-9_-])+$/i,
	alpha_dash: function (v) {
		return Ext.form.VTypes.alpha_dashMask.test(v);
	},
	alpha_dashText: 'Este campo s\u00F3lo debe contener caracteres alfanuméricos, guiones bajos o guiones',
	
	// Validacion alpha_numeric de CodeIgniter
	alpha_numericMask: /^([a-z0-9])+$/i,
	alpha_numeric: function (v){
		return Ext.form.VTypes.alpha_numericMask.test(v);
	},
	alpha_numericText: 'Este campo s\u00F3lo debe contener letras y n\u00FAmeros',
	
	//Validacion de Decimales para atamarones
	valid_two_decimalMask: /^[\d\.]/,
	valid_two_decimal: function(v){
		return (/^\d+(\.\d{1,2})?$/).test(v);
	},
	valid_two_decimalText: 'Este campo debe contener como m\u00E1ximo 2 decimales',
        
    //Validacion de Ano Base que acepta solo 4 digitos del 2000 al 2099 para atamarones
	valid_ano_fourMask: /^\d$/,
	valid_ano_four: function(v){
		return (/^(20|20)\d\d$/).test(v);
	},
	valid_ano_fourText: 'Debe ingresar s\u00F3lo cuatro (4) n\u00FAmeros en un rango del 2000 al 2099.',
        
    //Validacion de autores
    valid_authorMask: /^[a-z\u00D1\u00F1\u00C0\u00E0\u00C1\u00E1\u00E9\u00C9\u00CD\u00ED\u00D3\u00F3\u00DA\u00FA\u00C4\u00E4\u00CB\u00EB\u00CF\u00EF\u00D6\u00F6\u00DC\u00FC\u0178\u00FF\s,]+$/i,
    valid_author: function(v){
	    return (/^[a-z\u00D1\u00F1\u00C0\u00E0\u00C1\u00E1\u00E9\u00C9\u00CD\u00ED\u00D3\u00F3\u00DA\u00FA\u00C4\u00E4\u00CB\u00EB\u00CF\u00EF\u00D6\u00F6\u00DC\u00FC\u0178\u00FF\s,]+$/i).test(v);
	},
	valid_authorText: 'Debe ingresar s\u00F3lo espacios y comas.',
        
    //Validacion de telefono persona donde permite telefonos de tipo (02|04)12312323(x1234)?
	valid_telephone_personaMask: /^([x0-9])+$/,
	valid_telephone_persona: function(v){
		return (/^(((02)\d{9}((x)\d{1,4})?)|((04)\d{9}))$/).test(v);
	},
	valid_telephone_personaText: 'Debe ingresar un n\u00FAmero telef\u00F3nico v\u00E1lido.',
	
    //Validacion de telefono organizacion donde permite telefonos de tipo *811, *171, 0800PACOFUEGO, 08001234567
	valid_telephone_organizacionMask: /^([A-Z0-9(\*)])+$/,
	valid_telephone_organizacion: function(v){
		return (/^\*?[0-9A-Z]{3,16}(x[0-9]{1-4})?$/).test(v);
	},
	valid_telephone_organizacionText: 'Debe ingresar un n\u00FAmero telef\u00F3nico v\u00E1lido.',
	
    //Validacion Alpha con espacios, ampersan y puntos
	valid_alpha_space_ampersan_pointMask: /^[a-z+&.\u00D1\u00F1\u00C0\u00E0\u00C1\u00E1\u00E9\u00C9\u00CD\u00ED\u00D3\u00F3\u00DA\u00FA\u00C4\u00E4\u00CB\u00EB\u00CF\u00EF\u00D6\u00F6\u00DC\u00FC\u0178\u00FF\s,]+$/i,
	valid_alpha_space_ampersan_point: function (v) {
		return Ext.form.VTypes.valid_alpha_space_ampersan_pointMask.test(v);
	},
	valid_alpha_space_ampersan_pointText: 'Este campo s\u00F3lo debe contener letras y espacios.',

	//@todo pasar validacion a sietpol limpia.
	// Validacion del rif
	valid_rifMask:/^(\S)$/,
	valid_rif: function (v) {
		return (/^(J|G|V)\-\d{5,8}\-\d{1}$/).test(v);
	},
	valid_rifText: 'Este campo debe tener un rif valido Ej. J-00000000-0',

	// Validacion de rango de fechas
	daterange : function(val, field) {
		var date = field.parseDate(val);

		if(!date){
			return;
		}
		if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
			var start = Ext.getCmp(field.startDateField);
			start.setMaxValue(date);
			this.dateRangeMax = date;
			start.validate();			
		} else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
			var end = Ext.getCmp(field.endDateField);
			end.setMinValue(date);
			this.dateRangeMin = date;
			end.validate();
			
		}
		/*
		 * Always return true since we're only using this vtype to set the
		 * min/max allowed values (these are tested for after the vtype test)
		 */
		return true;
	},
	daterangeText: 'El valor de la fecha de inicio debe ser menor a la fecha de fin',
        
        //validacion para direcciones MAC.
        valid_mac_address:  function(v) {
            return /^(((\d|([a-f]|[A-F])){2}:){5}(\d|([a-f]|[A-F])){2})$|^(((\d|([a-f]|[A-F])){2}-){5}(\d|([a-f]|[A-F])){2})$|^([0-9a-f]{4}\.[0-9a-f]{4}\.[0-9a-f]{4})$/.test(v);
        },
        valid_mac_addressText: 'Debe ser una dirección Mac valida',
        valid_mac_addressMask: /(\d|([a-f])|\:)/i,
        
        //Validacion para direcciones ip.
        valid_ip:  function(v) {
            return /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/.test(v);
        },
        valid_ipText: 'Debe ser una dirección IP valida',
        valid_ipMask: /[\d\.]/i

});