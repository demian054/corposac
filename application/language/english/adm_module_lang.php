<?php

// Usuarios
$lang['identification_unique'] = '%s must be unique.';
$lang['failure_password_comparison'] = '%s does not match Confirm Password.';
$lang['username_unique'] = 'Username is already in use.';
$lang['email_unique'] = 'Email is already in use.';
$lang['document_unique'] = 'Document is already in use.';
$lang['non_equal_password'] = 'Password does not match Confirm Password.';
$lang['message_deactivate_user_twice'] = 'User is already deactivated.';
$lang['message_activate_user_twice'] = 'User is already activated.';
$lang['message_asign_rol_success'] = 'Roles have been assigned successfully.';
$lang['self_deleted_user'] = 'Logged user cannot be deleted.';
$lang['self_activated_user'] = 'Logged user cannot be activated.';
$lang['self_deactivated_user'] = 'Logged user cannot be deactivated.';
$lang['message_create_rol'] = "In order to complete the process, first you must assign at least one role to the user through the Manage User Roles button.";
	
// Roles
$lang['role_name_unique'] = 'Name must be unique';
$lang['has_associated_users'] = 'The role has associated users.In order to delete the role first there must be no relation with any user.';
$lang['self_deleted_role'] = 'Logged role cannot be deleted.';
$lang['self_activated_role'] = 'Logged role cannot be activated.';
$lang['self_deactivated_role'] = 'Logged role cannot be deactivated.';
$lang['message_asign_operation_success'] = 'Operations have been asigned successfully.';

// Mi Perfil
$lang['auth_incorrect_password'] = 'Incorrect current Password.';

// Maintenance
$lang['message_maintenance_window'] = 'Maintenance process has been set up from (begin) to (end). During this period the application access is forbidden.';
$lang['message_maintenance_date_error'] = 'Date of Maintenance collides with an existing date.';
$lang['message_maintenance'] = 'Application under Maintenance.';
$lang['message_maintenance_end_date'] = 'It will end at ';
$lang['message_maintenance_apology'] = 'sorry we are currently performing maintenance.';
$lang['message_maintenance_undefined'] = 'It will end in the next few hours';

// Categorias
$lang['categoria_elimination_error'] = 'No se puede eliminar la categoría ya que es utilizada por los siguientes campos: ';
$lang['message_valor_repeat'] = 'Category values must be unique.';
$lang['message_categoria_undelete'] = 'Category must have at least two options.';
$lang['message_categoria'] = "Empty option list.";

?>