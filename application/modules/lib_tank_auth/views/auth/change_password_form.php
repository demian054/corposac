<?php
$old_password = array(
	'name'	=> 'old_password',
	'id'	=> 'old_password',
	'value' => set_value('old_password'),
	'size' 	=> 30,
);
$new_password = array(
	'name'	=> 'new_password',
	'id'	=> 'new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_new_password = array(
	'name'	=> 'confirm_new_password',
	'id'	=> 'confirm_new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size' 	=> 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<table>
    <tr>
        <td colspan ="2"><h2>Recuperar Password</h2></td>
    </tr>
    <tr>
        <td><?= form_label('Antiguo Password', $old_password['id']); ?></td>
	<td><?= form_password($old_password); ?></td>
	<td style="color: red;"><?= form_error($old_password['name']); ?><?= isset($errors[$old_password['name']])?$errors[$old_password['name']]:''; ?></td>
    </tr>
    <tr>
        <td><?= form_label('Nuevo Password', $new_password['id']); ?></td>
	<td><?= form_password($new_password); ?></td>
	<td style="color: red;"><?= form_error($new_password['name']); ?><?= isset($errors[$new_password['name']])?$errors[$new_password['name']]:''; ?></td>
    </tr>
    <tr>
        <td><?= form_label('Confirmar Nuevo Password', $confirm_new_password['id']); ?></td>
	<td><?= form_password($confirm_new_password); ?></td>
	<td style="color: red;"><?= form_error($confirm_new_password['name']); ?><?= isset($errors[$confirm_new_password['name']])?$errors[$confirm_new_password['name']]:''; ?></td>
    </tr>
</table>
<?= form_submit('change', 'Cambiar Password'); ?>
<?= form_close(); ?>