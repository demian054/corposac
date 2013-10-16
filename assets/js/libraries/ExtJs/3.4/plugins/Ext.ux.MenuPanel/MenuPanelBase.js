Ext.onReady(function(){
     // Menus can be prebuilt and passed by reference
    var dateMenu = new Ext.menu.DateMenu({
        handler : function(dp, date){
            Ext.example.msg('Date Selected', 'You chose {0}.', date.format('M j, Y'));
        }
    });

    var colorMenu = new Ext.menu.ColorMenu({
        handler : function(cm, color){
            Ext.example.msg('Color Selected', 'You chose {0}.', color);
        }
    });

    var menu = new Ext.menu.Menu({
        id: 'mainMenu',
        items: [
            {
                text: 'I like Ext',
                checked: true,       // when checked has a boolean value, it is assumed to be a CheckItem
                checkHandler: onItemCheck
            },
            {
                text: 'Ext for jQuery',
                checked: true,
                checkHandler: onItemCheck
            },
            {
                text: 'I donated!',
                checked:false,
                checkHandler: onItemCheck
            }, '-', {
                text: 'Radio Options',
                menu: {        // <-- submenu by nested config object
                    items: [
                        // stick any markup in a menu
                        '<b class="menu-title">Choose a Theme</b>',
                        {
                            text: 'Aero Glass',
                            checked: true,
                            group: 'theme',
                            checkHandler: onItemCheck
                        }, {
                            text: 'Vista Black',
                            checked: false,
                            group: 'theme',
                            checkHandler: onItemCheck
                        }, {
                            text: 'Gray Theme',
                            checked: false,
                            group: 'theme',
                            checkHandler: onItemCheck
                        }, {
                            text: 'Default Theme',
                            checked: false,
                            group: 'theme',
                            checkHandler: onItemCheck
                        }
                    ]
                }
            },{
                text: 'Choose a Date',
                iconCls: 'calendar',
                menu: dateMenu // <-- submenu by reference
            },{
                text: 'Choose a Color',
                menu: colorMenu // <-- submenu by reference
            }
        ]
    });
    var p = new Ext.ux.MenuPanel({
        title: 'My MenuPanel',
        collapsible:true,
        renderTo: 'container',
        width:400,
        menu: menu
    });
    
      // functions to display feedback
    function onButtonClick(btn){
        //Ext.example.msg('Button Click','You clicked the "{0}" button.', btn.text);
    }

    function onItemClick(item){
        //Ext.example.msg('Menu Click', 'You clicked the "{0}" menu item.', item.text);
    }

    function onItemCheck(item, checked){
        //Ext.example.msg('Item Check', 'You {1} the "{0}" menu item.', item.text, checked ? 'checked' : 'unchecked');
    }

    function onItemToggle(item, pressed){
        //Ext.example.msg('Button Toggled', 'Button "{0}" was toggled to {1}.', item.text, pressed);
    }

});