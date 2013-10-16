<?php if(!empty($scriptTags)): ?> 
<script type="text/javascript">
<?php endif; ?>
var _colmodel = Ext.getCmp('Grid_<?=$opId?>').colModel;

_colmodel.setRenderer(_colmodel.getIndexById('col_<?=$opId?>_<?=$fieldId?>'),function(_value, _metadata, _record, _rowIndex, _colIndex, _store) {
   
   _metadata.css = _metadata.css + ' our_status ' + _value;
   //console.log(_metadata);
   return _value;
});

 <?php if(!empty($scriptTags)): ?> 
</script>
<?php endif; ?>
