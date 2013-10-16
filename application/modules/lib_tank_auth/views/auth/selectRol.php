<?php echo form_open('auth/login') ?>
<?php echo form_dropdown('selectRol', $roles) ?>
<?php echo form_submit() ?>
<?php echo form_close() ?>
