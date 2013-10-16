<script type="text/javascript">
  Ext.onReady(function() {

    var formLogin = new Ext.FormPanel({
      frame: false, border: false, buttonAlign: 'center',
      url: BASE_URL + 'auth/backlogin', method: 'POST', id: 'frmLogin',
      bodyStyle: 'padding: 8px 8px 8px 8px; background:#e8e8e8;',
      width: 350, labelWidth: 80,
      items: [{
          xtype: 'textfield',
          fieldLabel: 'Usuario',
          name: 'login',
          id: 'login',
          blankText:  'El campo Usuario es obligatorio',
          listeners: {
            render: function(c) {
              new Ext.ToolTip({
                target: c.getEl(),
                anchor: 'left',
                trackMouse: false,
                html: 'Debe colocar en este espacio su usuario, debe contener solo caracteres alfanumericos'
              });
            }
          },
          allowBlank: false
        }, {
          xtype: 'textfield',
          fieldLabel: 'Contrase&ntilde;a',
          name: 'password',
          id: 'logPassword',
          allowBlank: false,
          blankText:  'El campo Contrase&ntilde;a es obligatorio',
          listeners: {
            render: function(c) {
              new Ext.ToolTip({
                target: c.getEl(),
                anchor: 'left',
                trackMouse: false,
                html: 'Debe colocar en este espacio su contrase&ntilde;a, debe contener solo caracteres alfanumericos'
              });
            }
          },
          inputType: 'password'
        }
      ],
      buttons: [
        { text: 'Entrar', icon: BASE_ICONS + 'accept.png', handler: fnLogin },
        { text: 'Borrar', icon: BASE_ICONS + 'minus-circle.png', handler: function() {formLogin.getForm().reset();}}
      ],
      keys: [{ key: [Ext.EventObject.ENTER], handler: fnLogin}]
    });

    function fnLogin() {
      Ext.getCmp('frmLogin').on({
        beforeaction: function() {
          if (formLogin.getForm().isValid()) {
            Ext.getCmp('winLogin').body.mask();
            Ext.getCmp('sbWinLogin').showBusy();
          }
        }
      });
      formLogin.getForm().submit({
        success: function(form, action) {
          Ext.getCmp('winLogin').body.unmask();

          var obj = Ext.util.JSON.decode(action.response.responseText);

          if (obj.situacion.de_error == 'no_valido') {
            Ext.getCmp('sbWinLogin').setStatus({
              text: obj.errors.reason,
              iconCls: 'x-status-error'
            });
            Ext.Msg.alert('Errores de Validacion', obj.errors.reason)
          } else if(obj.situacion.de_error == 'directo'){
            window.location = BASE_URL;
          }
        },
        failure: function(form, action) {
          Ext.getCmp('winLogin').body.unmask();
          if (action.failureType == 'server') {
            obj = Ext.util.JSON.decode(action.response.responseText);
            Ext.getCmp('sbWinLogin').setStatus({
              text: obj.errors.reason,
              iconCls: 'x-status-error'
            });
          } else {
            if (formLogin.getForm().isValid()) {
              Ext.getCmp('sbWinLogin').setStatus({
                text: 'Imposible contactar al servidor',
                iconCls: 'x-status-error'
              });
            } else {

              Ext.getCmp('sbWinLogin').setStatus({
                text: 'Error en el formulario !',
                iconCls: 'x-status-error'
              });
            }
          }
        }
      });
    }







    function fnAceptar()
    {
      Ext.getCmp('frmAsk').on({
        beforeaction: function() {
          if (formLogin.getForm().isValid()) {
            Ext.getCmp('winAsk').body.mask();
          }
        }
      });

      formAsk.getForm().submit({
        success: function(form, action) {
          Ext.getCmp('winAsk').body.unmask();
          var obj = Ext.util.JSON.decode(action.response.responseText);
          if (obj.situacion.de_error == 'no_valido'){
            //Debe escoger un usuario valido
            Ext.Msg.alert('Errores de Tipo de Usuario', obj.msj.mensaje)
          } else {
            window.location = BASE_URL;
          }
        }
      })
    }

    var aux = new Ext.Panel({
      layout:'column',
      border:false,
      bodyStyle: 'background:#e8e8e8;',
      items: [
        {width: 180,
          border:false,
          html:'<div style="background-color:#e8e8e8; "><img src="'+BASE_URL+'assets/img/logo_login.png"/></div>'
        },
        formLogin

      ]
    });


    var winLogin = new Ext.Window({
      title: 'SIETPOL',
      id: 'winLogin',
      layout: 'fit',
      width: 550,
      height: 160,
      y: 100,
      resizable: false,
      closable: false,
      items: [aux],
      bbar: new Ext.ux.StatusBar({
        id: 'sbWinLogin'
      })
    });

    winLogin.show();
  });
</script>
