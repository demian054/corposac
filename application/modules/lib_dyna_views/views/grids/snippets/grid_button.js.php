<?php
/**
 * @package lib_dyna_views
 * @subpackage views/grids/snippets
 *
 * @author      Jose A. Rodriguez E. <jrodriguez@rialfi.com>
 *
 * @version     v1.1 05/09/12 02:53 PM
 * @copyright 	Copyright (c) RIALFI CONSULTING C.A./DSS 2011-07-01
 *
 * Vista que provee las generacion de botones para grids dentro de dyna_views.
 */
?>

    {
        width:30,
        align:'center',
        fixed:true,
        hideable:false,
        menuDisabled:true,
        dataIndex:'id',
        renderer: function(value, metadata, record, rowIndex, colIndex, store){
            var button = '<div style="cursor: pointer">\n\
                <img title="<?= $operation_name?>" src="<?= $icon?>" onclick="<?= $onclick?>" />\n\
            </div>';
            return String.format(button, value);
        }
    },

<?php
/* END View grid_button.js      */
/* END of file grid_button.js.php */
/* Location: ./application/modules/lib_dyna_views/views/forms/snippets/grid_button.js.php */
?>