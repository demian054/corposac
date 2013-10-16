<?php

// Usuarios
$lang['identification_unique'] = 'El campo %s debe ser único.';
$lang['failure_password_comparison'] = 'El campo %s debe ser igual al campo Repetir Contraseña.';
$lang['username_unique'] = 'El Nombre de Usuario se encuentra repetido.';
$lang['email_unique'] = 'El Correo Electrónico se encuentra repetido.';
$lang['document_unique'] = 'La Cédula de Identidad se encuentra repetida.';
$lang['non_equal_password'] = 'El Campo Contraseña no coincide con el Campo Confirmar Contraseña.';
$lang['message_deactivate_user_twice'] = 'Este usuario ya se encuentra desactivado.';
$lang['message_activate_user_twice'] = 'Este usuario ya se encuentra activado.';
$lang['message_asign_rol_success'] = 'Los roles han sido asignados satisfactoriamente.';
$lang['self_deleted_user'] = 'El Usuario que se encuentra en sesión no puede ser Eliminado.';
$lang['self_activated_user'] = 'El Usuario que se encuentra en sesión no puede ser Activado.';
$lang['self_deactivated_user'] = 'El Usuario que se encuentra en sesión no puede ser Desactivado.';
$lang['message_create_rol'] = ' Para completar el proceso, debe asignar al menos un rol al usuario creado, a través del botón Gestionar Roles de Usuarios.';

// Roles
$lang['role_name_unique'] = 'El Nombre del Rol se encuentra repetido';
$lang['has_associated_users'] = 'El Rol posee usuarios asociados. Para poder eliminar un Rol primero debe eliminar toda relación con Usuarios.';
$lang['self_deleted_role'] = 'El Rol que se encuentra en sesión no puede ser Eliminado.';
$lang['self_activated_role'] = 'El Rol que se encuentra en sesión no puede ser Activado.';
$lang['self_deactivated_role'] = 'El Rol que se encuentra en sesión no puede ser Desactivado.';
$lang['message_asign_operation_success'] = 'Las operaciones han sido asignadas satisfactoriamente.';

// Mi Perfil
$lang['auth_incorrect_password'] = 'Contraseña Actual Incorrecta.';

// Maintenance
$lang['message_maintenance_window'] = 'Se ha programado un mantenimiento comprendido entre el día (inicio) hasta (fin), durante este período el acceso a la aplicación estará restringido.';
$lang['message_maintenance_date_error'] = 'La Fecha que desea ingresar colisiona con una fecha de mantenimiento existente.';
$lang['message_maintenance'] = 'En este momento el sistema se encuentra en mantenimiento.';
$lang['message_maintenance_end_date'] = 'El mismo finalizará el día ';
$lang['message_maintenance_apology'] = 'Estamos trabajando para que usted disfrute de un mejor servicio, disculpe las molestias ocasionadas.';
$lang['message_maintenance_undefined'] = 'El mismo finalizará en las próximas horas.';

// Categorias
$lang['categoria_elimination_error'] = 'No se puede eliminar la categoría ya que es utilizada por los siguientes campos: ';
$lang['message_valor_repeat'] = 'Los valores de las categorias no se deben repetir.';
$lang['message_categoria_undelete'] = 'No se puede eliminar la opción porque la categoria debe tener 2 o mas opciones.';
$lang['message_categoria'] = 'Lista de Opciones Vacía o Insuficiente (Min 2)';

// Reportes
$lang['message_entity_validation'] = 'Debe seleccionar alguna Tabla.';
$lang['message_field_validation'] = 'Debe seleccionar algun Campo.';
$lang['message_create_data_source_success'] = 'La Fuente de Datos ha sido creada de forma satisfactoria.';
$lang['message_data_source_repeated'] = 'El Código debe ser único.';

?>