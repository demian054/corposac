<script type="text/javascript">
/* Custom Unique Username Validation
 * Used in 01. Form Register */

//@todo integrar con tank auth 
//@todo utilizar mensajes con locale es
var usernameErrLength = 'Nombre de Usuario debe tener minimo 4 caracteres !';
var usernameErrUnique = 'Nombre de Usuario ya existe !';
var usernameSuccess = 'Nombre de Usuario disponible';
var emailErrFormat = 'Email invalido !';
var emailErrUnique = 'Email ya existe !';
var emailSuccess = 'Email valido y disponible ';

Ext.apply(Ext.form.VTypes, {
    uniqueusernameMask : /[a-z0-9_\.\-@\+]/i,
	uniqueusername : function(val) {
        if (val.length < 4) {
            Ext.apply(Ext.form.VTypes, {
                uniqueusernameText: usernameErrLength
            });
            return false;
        } else {
            Ext.Ajax.request({
                url: BASE_URL + 'user/ext_is_unique_username',
                method: 'POST',
                params: 'username=' + val,
                success: function(o) {
                    if (o.responseText == 0) {
                        resetUsernameValidator(false);
                        Ext.apply(Ext.form.VTypes, {
                            uniqueusernameText: usernameErrUnique
                        });
                        return false;
                    } else {
                        resetUsernameValidator(true);
                    }
                }
            });
            return true;
        }
	},
	uniqueusernameText : usernameErrUnique,

    uniqueemailMask : /[a-z0-9_\.\-@\+]/i,
    uniqueemail : function(val) {
        var uniqueemail = /^(\w+)([\-+.][\w]+)*@(\w[\-\w]*\.){1,5}([A-Za-z]){2,6}$/;
        if (uniqueemail.test(val)) {
            Ext.Ajax.request({
                url: BASE_URL + 'user/ext_is_unique_email',
                method: 'POST',
                params: 'email=' + val,
                success: function(o) {
                    if (o.responseText == 0) {
                        resetEmailValidator(false);
                        Ext.apply(Ext.form.VTypes, {
                            uniqueemailText: emailErrUnique
                        });
                    } else {
                        resetEmailValidator(true);
                    }
                }
            });
            return true;
        } else {
            return false;
        }

    },
    uniqueemailText : emailErrFormat,

    password : function(val, field) {
        if (field.initialPassField) {
            var pwd = Ext.getCmp(field.initialPassField);
            return (val == pwd.getValue());
        }
        return true;
    },
    passwordText : 'Las Claves no coinciden',

    passwordlength : function(val) {
        if (val.length < 5) {
            return false;
        } else {
            return true;
        }
    },
    passwordlengthText : 'Clave de minimo 5 caracteres'
});

function resetUsernameValidator(is_error) {
	Ext.apply(Ext.form.VTypes, {
		uniqueusername : function(val) {
            if (val.length < 4) {
                Ext.apply(Ext.form.VTypes, {
                    uniqueusernameText: usernameErrLength
                });
                return false;
            } else {
                Ext.Ajax.request({
                    url: BASE_URL + 'user/ext_is_unique_username',
                    method: 'POST',
                    params: 'username=' + val,
                    success: function(o) {
                        if (o.responseText == 0) {
                            resetUsernameValidator(false);
                        } else {
                            resetUsernameValidator(true);
                        }
                    }
                });
                return is_error;
            }
		}
	});
}

function resetEmailValidator(value) {
    Ext.apply(Ext.form.VTypes, {
        uniqueemail : function(val) {
            var uniqueemail = /^(\w+)([\-+.][\w]+)*@(\w[\-\w]*\.){1,5}([A-Za-z]){2,6}$/;
            if (uniqueemail.test(val)) {
                Ext.Ajax.request({
                    url: BASE_URL + 'user/ext_is_unique_email',
                    method: 'POST',
                    params: 'email=' + val,
                    success: function(o) {
                        if (o.responseText == 0) {
                            resetEmailValidator(false);
                            Ext.apply(Ext.form.VTypes, {
                                uniqueemailText: emailErrUnique
                            });
                        } else {
                            resetEmailValidator(true);
                        }
                    }
                });
            } else {
                return false;
            }
            return (value);
        }
    });
}
</script>