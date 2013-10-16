var e_icon_combo = Ext.getCmp('combo_<?= $icon_combo ?>');
e_icon_combo.tpl = '<tpl for=\".\"><div ext:qtip=\"{label}\" class=\"x-combo-list-item\"><img style=\"vertical-align:middle;\" src=\"<?= base_url() ?>assets/img/icons/{label}\" alt="=\"{label}\" /> {label}</div></tpl>';
e_icon_combo.setEditable(true);
