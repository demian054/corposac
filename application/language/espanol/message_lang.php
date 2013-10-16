<?php
$lang['message_operation_error'] = 'La operación no finalizó de manera satisfactoria, vuelva a intentarlo o comuníquese con Sistemas.';
$lang['message_update_success'] = 'El registro fue modificado correctamente.';
$lang['message_create_success'] = 'El registro fue ingresado correctamente.';
$lang['message_delete_success'] = 'El registro fue eliminado correctamente.';
$lang['message_deactivate_success'] = 'El registro fue desactivado correctamente';
$lang['message_operation_no_permission'] = 'No posee permisos para realizar esta operación.';
$lang['message_invalidad_operation'] = 'Está realizando una operación inválida.';
$lang['message_operation_success'] = 'La operación fue realizada satisfactoriamente.';
$lang['message_email_success'] = 'El correo fue enviado correctamente.';
$lang['deletion_token_expired'] = 'La Sesion de Eliminación ha Expirado. Debe Generar un Nuevo Correo.';
$lang['deletion_invalid_token'] = 'Token inválido';
$lang['required_ffin_experiencia_policial'] = 'El campo Fecha Fin es obligatorio.';
$lang['export_csv'] = 'Descargar el documento';
$lang['message_not_unique'] = 'No se pudo ingresar el registro porque esta duplicado.';
$lang['rol_change_password_denied'] = 'El rol asociado al usuario no posee permisos para solicitud de Recuperaci&oacute;n de Contrase&ntilde;a.<br/>Comun&iacute;quese con Sistemas.';
$lang['message_deactivate_user_twice'] = 'Este usuario ya se encuentra desactivado.';
$lang['message_activate_user_twice'] = 'Este usuario ya se encuentra activado.';
$lang['message_activate_success'] = 'El registro fue activado correctamente';
$lang['message_asign_rol_success'] = 'Los roles han sido asignados satisfactoriamente.';
$lang['message_asign_operation_success'] = 'Las operaciones han sido asignadas satisfactoriamente.';
$lang['username_unique'] = 'El Nombre de Usuario se encuentra repetido.';
$lang['email_unique'] = 'El Correo Electrónico se encuentra repetido.';
$lang['document_unique'] = 'La Cédula de Identidad se encuentra repetida.';
$lang['non_equal_password'] = 'El Campo Contraseña no coincide con el Campo Confirmar Contraseña.';
$lang['message_categoria'] = 'Lista de Opciones Vacía o Insuficiente (Min 2)';
$lang['self_deleted_user'] = 'El Usuario que se encuentra en sesión no puede ser Eliminado.';
$lang['self_activated_user'] = 'El Usuario que se encuentra en sesión no puede ser Activado.';
$lang['self_deactivated_user'] = 'El Usuario que se encuentra en sesión no puede ser Desactivado.';
$lang['message_create_rol'] = ' Para completar el proceso, debe asignar al menos un rol al usuario creado, a través del botón Gestionar Roles de Usuarios.';
$lang['auth_incorrect_password'] = 'Contraseña Actual Incorrecta.';
$lang['message_unassign_rol_success'] = 'Los roles han sido desasignados satisfactoriamente.';
$lang['message_empty_multiple_selector'] = 'Debe seleccionar algun elemento de la lista.';
$lang['file_not_found'] = 'El archivo deseado no existe.';
$lang['missing_file_in_directory'] = 'El archivo deseado no se encuentra en el directorio.';
$lang['categoria_elimination_error'] = 'No se puede eliminar la categoría ya que es utilizada por los siguientes campos: ';
$lang['message_valor_repeat'] = 'Los valores de las categorias no se deben repetir.';
$lang['message_categoria_undelete'] = 'No se puede eliminar la opción porque la categoria debe tener 2 o mas opciones.';
$lang['message_tree_success'] = 'Los elementos seleccionados serán guardados cuando finalice el proceso';
$lang['message_export_success'] = 'Los registros seleccionados se exportaron satisfactoriamente';
$lang['message_maintenance_window'] = 'Se ha programado un mantenimiento comprendido entre el día (inicio) hasta (fin), durante este período el acceso a la aplicación estará restringido.';
$lang['message_maintenance_date_error'] = 'La Fecha que desea ingresar colisiona con una fecha de mantenimiento existente.';
$lang['message_maintenance'] = 'En este momento el sistema se encuentra en mantenimiento.';
$lang['message_maintenance_end_date'] = 'El mismo finalizará el día ';
$lang['message_maintenance_apology'] = 'Estamos trabajando para que usted disfrute de un mejor servicio, disculpe las molestias ocasionadas.';
$lang['message_maintenance_undefined'] = 'El mismo finalizará en las próximas horas.';
$lang['message_csv_cp_error'] = 'Debe seleccionar al menos una opción para poder exportar el documento csv.';
$lang['message_csv_note'] = '<br /> <b>Nota:</b> Recuerde configurar su hoja de calculo a una codificación tipo unicode para que pueda visualizar correctamente el archivo.';
$lang['role_name_unique'] = 'El Nombre del Rol se encuentra repetido';
$lang['has_associated_users'] = 'El Rol posee usuarios asociados. Para poder eliminar un Rol primero debe eliminar los usuarios asociados.';
$lang['self_deleted_role'] = 'El Rol que se encuentra en sesión no puede ser Eliminado.';
$lang['self_activated_role'] = 'El Rol que se encuentra en sesión no puede ser Activado.';
$lang['self_deactivated_role'] = 'El Rol que se encuentra en sesión no puede ser Desactivado.';



/* Modificar el texto en funcion de los upload de uroborus */
$lang['file_upload_success'] = 'El archivo se ha subido al servidor satisfactoriamente.';
$lang['file_upload_delete_success'] = 'El archivo ha sido eliminado satisfactoriamente.';
$lang['file_upload_update_success'] = 'Los datos han sido actualizados satisfactoriamente.';
$lang['file_single_upload_error'] = 'No puede cargar otro archivo a este campo, por favor primero elimine el actual.';
$lang['file_load_error'] = 'No se puede cargar el archivo';
/* FIN Modificar el texto en funcion de los upload de uroborus */


// User messages
$lang['identification_unique'] = 'El campo %s debe ser único.';
$lang['failure_password_comparison'] = 'El campo %s debe ser igual al campo Repetir Contraseña.';

// User messages


// CRUD Operaciones
$lang['m_operation_unique_url'] = 'La URL ya está siendo utilizada por otra Operación';
$lang['m_operation_has_childrens'] = 'La Operación no puede ser eliminada porque tiene hijos<br />';
$lang['m_operation_has_roles'] = 'La Operación no puede ser eliminada porque tiene roles activos asociados<br />';
$lang['m_operation_has_fields'] = 'La Operación no puede ser eliminada porque tiene campos activos asociados<br />';
$lang['m_operation_move_node_in_node'] = 'La Operación no puede ser Movida dentro de si misma<br />';
$lang['m_operation_move_node_in_parent'] = 'La Operación ya es hija del nodo selecionado<br />';
$lang['m_operation_move_node_equal_parent'] = 'La Operación no se puede mover a un padre que no es similar<br />';
$lang['m_operation_move_node_inception'] = 'La Operación no se puede mover dentro de uno de sus decendientes<br />';
$lang['message_move_success'] = 'El registro fue movido correctamente.';


//Virtualizacion y CRUD 
$lang['virtualization_failure_on_create'] = 'Ha ocurrido un error durante la virtualización<br><br>';
$lang['virtualization_title_on_create'] = 'Virtualización de Entidades';
$lang['virtualization_success_on_create'] = 'La virtualización de entidades se ha efectuado satisfactoriamente.<br> para visualizar los cambios ejecutados debe reiniciar su sesión.<br><br>';
$lang['virtualization_empty_on_create'] = 'No se ha virtualizado ninguna entidad.<br><br> Para virtualizar una entidad debe seleccionar que operaciones <br>quiere hacer sobre la misma haciendo click sobre ellas.';




/*
$lang['message_delete_org'] = 'El elemento que desea eliminar tiene departamentos asociados a él.
                               Debe primero eliminar los departamentos asociados.';
$lang['message_create_2collateral_error'] = 'El registro no se puede ingresar porque ya el 
                                     	     departamento posee dos (2) relaciones colaterales.';
$lang['message_create_collateral_error'] ='El registro no se puede ingresar, porque un departamento Colateral 
					   no puede tener departamentos Colaterales,Staff ni Subordinados asociados a él.';
$lang['message_create_staff_error'] = 'El registro no se puede ingresar, porque un departamento Staff 
				       no puede tener departamentos Colaterales ó Staff asociados a él.';
$lang['message_delete_funcionario'] = 'El departamento que desea eliminar tiene funcionarios asociados a él.<br> 
                                   Debe primero moverlos a otro departamento o crear un departamentos donde asociarlos.';
$lang['message_exist_miembro'] = 'La persona ya es miembro de la junta directiva.';
$lang['message_exist_cargo'] = 'El cargo ya existe en la junta directiva.';
$lang['message_no_exist_funcionario'] = 'El funcionario no existe para este cuepo policial.';
$lang['message_is_funcionario'] = 'No se puede agregar a familiares por que la persona es un funcionario.';
$lang['message_not_funcionario'] = 'No se puede agregar a familiares policiales por que la persona no es un funcionario.';
$lang['message_repeat_familiar'] = 'Esta persona ya esta asociada como familiar del funcionario.';
$lang['message_responsable_instalacion_success'] = 'El funcionario se ha ingresado correctamente a la instalación.';
$lang['message_date_comparison_experiencia_policial'] = 'La fecha de inicio no puede ser mayor a la fecha de finalización 
                                                         de la experiencia policial.';
$lang['message_ambito_cuerpo_policial_municipio'] = 'Ya existe un Cuerpo Policial para ese Municipio.';
$lang['message_ambito_cuerpo_policial_estado'] = 'Ya existe un Cuerpo Policial para ese Estado.';
$lang['message_select_ubicacion'] = 'Debe seleccionar un ambito y ubicacion adecuada.'; 
$lang['message_calcule_campos_success'] = 'Calculado para el Periodo - Año: ';
$lang['message_calcule_campos_empty'] = 'No se calculó el valor porque no se encontró el valor de la variable fuente.';
$lang['message_calcule_campos_politica'] = 'Según la politica de calculo de no calcular indicador para otro periodo.';
$lang['message_calcule_no_disponible'] = 'No se dispone del cálculo del indicador.';

$lang['already_repaired_experiencia_policial'] = 'La Experiencia Policial ya ha sido reparada.';
$lang['already_updated_experiencia_policial'] = 'La Experiencia Policial ya ha sido actualizada. Si desea hacer alguna modificación al registro actual seleccione Reparar.';
$lang['required_empty_ffin_experiencia_policial'] = 'El campo Fecha Fin, Causal de Egreso y Detalle de Egreso no pueden ser modificados en el registro de experiencia policial actual.'; 
$lang['message_has_director'] = 'La Junta Directiva ya posee un Director.';
$lang['message_required_cargo'] = 'El campo Cargo es obligatorio.'; 
$lang['message_delete_root_error'] = 'No se puede eliminar el departamento raíz. Solo se permite su actualización.';
$lang['message_delete_cargo_error'] = 'El cargo que desea eliminar tiene funcionarios asociados a él. <br> Debe primero moverlos a otro cargo';

$lang['valid_date_instalacion'] = 'El campo Fecha de Creación debe ser una fecha mayor a la Fecha de Fundación del Cuerpo Policial.';
$lang['message_rotacion_close'] = 'Esta Rotación está Cerrada, no se puede editar';

$lang['required_estado'] = 'El campo Estado es obligatorio.';
$lang['required_municipio'] = 'El campo Municipio es obligatorio.'; 
$lang['message_operation_no_count_funcionarios'] = 'No se puede realizar esta operación, porque no existen funcionarios asociados a este departamento.';
$lang['files_for_repaired_experience'] = 'No puede asociar archivos a una experiencia policial que ha sido reparada.';
$lang['message_matricula_exist'] = 'La matricula ya pertenece a otro vehículo.';
$lang['message_carroceria_exist'] = 'El serial de la carroceria ya pertenece a otro vehículo.';
$lang['message_motor_exist'] = 'El serial del motor ya pertenece a otro vehículo.';
$lang['message_arma_no_operativa'] = 'No se puede asignar un arma No Operativa.';
$lang['message_indicador_no_delete'] = 'El campo dinámico no se puede eliminar porque esta asociado a uno o varios indicadores';
$lang['message_indicador_no_deactivate'] = 'El campo dinámico no se puede desactivar porque esta asociado a uno o varios indicadores';
$lang['validate_sql_words'] = 'El Campo SQL tiene palabras inválidos, sólo se pueden ejecutar consultas';
$lang['validate_sql_anio_required'] = 'Se requiere el valor para el anio';
$lang['validate_sql_anio_variable'] = 'Debe estar presente la variable $anio en el sql';
$lang['validate_sql_periodo_i_required'] = 'Se requiere el valor para el periodo inicial';
$lang['validate_sql_periodo_i_variable'] = 'Debe estar presente la variable $periodo_inicial_id en el sql';
$lang['validate_sql_periodo_f_required'] = 'Se requiere el valor para el periodo final';
$lang['validate_sql_periodo_f_variable'] = 'Debe estar presente la variable $periodo_final_id en el sql';
$lang['validate_sql_cp_required'] = 'Se requiere el valor para el cuerpo policial';
$lang['validate_sql_cp_variable'] = 'Debe estar presente la variable $cuerpo_policial_id en el sql'; 
$lang['validate_formula_field'] = 'El Campo debe ser un valor válido';
$lang['validate_formula_num_cociente'] = 'Debe seleccionar un numerador para el cociente.';
$lang['validate_formula_den_cociente'] = 'Debe seleccionar un denominador para el cociente.';
$lang['validate_formula_num_porcentaje'] = 'Debe seleccionar un numerador para el porcentaje.';
$lang['validate_formula_den_porcentaje'] = 'Debe seleccionar un denominador para el porcentaje.';
$lang['validate_formula_valor_directo'] = 'Debe seleccionar un valor directo'; 
$lang['message_edit_root_error'] = 'El departamento Raíz sólo permite para el campo Tipo de Adscripción el valor \'N/A\'';
$lang['message_edit_root2_error'] = 'Sólo el departamento Raíz permite el valor \'N/A\' para el campo Tipo de Adscripción';
$lang['message_calculate_success'] = 'El proceso de cálculo finalizó correctamente.'; 
$lang['message_serial_cilindro_unique'] = 'El número de serial del cilindro se encuentra asignado a otra arma.';
$lang['message_serial_estructura_unique'] = 'El número de serial de estructura se encuentra asignado a otra arma.';
$lang['message_repeat_idiomas'] = 'El idioma ya existe para el funcionario';
$lang['cedula_funcionario_unique'] = 'La Cédula de Identidad que ingresó pertenece a un Funcionario.<br/>Si desea crear un usuario Funcionario hágalo a través de la opción Crear Usuario Funcionario. ';
$lang['non_assigned_cuerpo_policial'] = 'El usuario no posee un Cuerpo Policial Asociado.<br/>Para asociar un Tipo de Rol CP el usuario debe poseer un Cuerpo Policial Asociado.';
$lang['message_arma_no_operativa_2'] = 'Esta arma ya se encuentra desactivada';
$lang['arma_status_error'] = 'El estatus del arma no tiene concordancia con su operatividad';
$lang['message_tree_error'] = 'Debe seleccionar al menos un Indicador';
$lang['message_tree_error_paso2'] = 'Debe seleccionar al menos un elemento en el botón: Habilitar Areas de Estandarización';
$lang['message_ranking_required'] = 'Debe seleccionar un ranking para luego proceder con este paso';
$lang['message_ranking_repeat'] = 'Ya se han definido anteriormente parámetros para este ranking, si desea cambiarlos debe ir al Paso 3 del Menú';
$lang['message_ranking_no_indicadores'] = 'El ranking que desea crear no tiene Indicadores asociados para la Frecuencia de Actualización seleccionada.';
$lang['message_ranking_habilitar_ind'] = 'Debe habilitar los indicadores para el Ranking: ';
$lang['message_ranking_definir_area'] = 'Debe definir las ponderaciones de las Áreas de Estandarización del Ranking: ';
$lang['message_ranking_definir_estandar'] = 'Debe definir las varianzas para las Estandares del Ranking: ';
$lang['message_ranking_definir_indicador'] = 'Debe definir las cargas factoriales para los Indicadores del Ranking: ';
$lang['message_ranking_definir_model'] = 'Debe definir los valores del CP Modelo para el Ranking: ';
$lang['message_ranking_code_unique'] = 'El valor del campo Código, ya existe';
$lang['message_code_unique'] = 'El valor del campo Código, ya existe';
$lang['message_date_rotation'] = 'La Fecha que desea ingresar colisiona con una fecha de transferencia existente ';
$lang['message_cargo_unique'] = 'El nombre del cargo ya existe dentro del Departamento.<br/>Los Nombres de los cargos son únicos';
$lang['message_ranking_ponderacion'] = 'Las ponderaciones se registraron exitosamente';
$lang['message_ranking_varianza'] = 'Las Varianzas se registraron exitosamente';
$lang['message_ranking_carga_factorial'] = 'Las Cargas Factoriales se registraron exitosamente';
$lang['message_ranking_modelo'] = 'Los valores de los Modelos se registraron exitosamente';
$lang['message_estudios_promocion_error'] = 'El puesto en la promoción no debe ser mayor al total de graduandos.';
$lang['message_date_create_instalación_error'] = 'La fecha de la Transferencia no puede ser menor a la Fecha de Creación de la instalación';
$lang['message_parentesco_unique_error'] = 'El Parentesco que desea asociar ya esta en uso.';
$lang['message_parentesco_abuelo_error'] = 'El Parentesco que desea asociar supera el número de asignaciones.<br/>Máximo se pueden asociar cuatro (4) abuelos.';
$lang['message_name_depart_error'] = 'El Nombre del Departamento ya existe dentro del Cuerpo Policial.<br/>Los Nombres de los Departamento son únicos por Cuerpo Policial. ';
$lang['message_cd_nombre_unique'] = 'El Campo Nombre se encuentra repetido.';
$lang['message_cd_etiqueta_unique'] = 'El Campo Etiqueta Nombre se encuentra repetido.';
$lang['valid_date_departamento'] = 'El campo Fecha de Creación debe ser una fecha mayor a la Fecha de Fundación del Cuerpo Policial.';
$lang['valid_date_cargo'] = 'El campo Fecha de Creación debe ser una fecha mayor a la Fecha de Creación del Departamento al cual pertenece.';
$lang['valid_date_cargo_departamento'] = 'El campo Fecha de Creación debe ser una fecha menor a la Fecha <br>de Creación de los Cargos asociados al Departamento.';
$lang['message_estado_error'] = 'El Municipio que desea eliminar tiene Parroquias asociadas a él.<br/> Debe eliminar las Parroquias asociadas a este Municipio.';
$lang['message_municipio_error'] = 'La Parroquia que desea eliminar tiene Direcciones asociadas a ella.<br/> Debe modificar todas las direcciones asociadas a esta Parroquia.';
$lang['valid_date_miembro'] = 'El campo Fecha de Nombramiento debe ser una fecha mayor a la Fecha de Fundación del Cuerpo Policial.';
$lang['message_pais_error'] = 'El Estado que desea eliminar tiene Municipios asociados a él.<br/> Debe eliminar los Municipios asociadas a este Estado.'; 
$lang['message_search_value_indicador'] = 'Ha finalizado la consulta.';
$lang['header_form_deactivate_funcionario'] = 'Indique la información solicitada en el siguiente Formulario';
*/

// Inicio Dyna_View

$lang['message_failure'] = 'Error en la petición al servidor.';
$lang['message_failure_title'] = 'Error!';

$lang['validation_error_title'] = 'Error de Validación.';
$lang['validation_error_message'] = 'El Formulario presenta errores de validación. Verifique que los datos del formulario sean correctos.';

$lang['disclaimer_acept_title'] = 'Verificación.';
$lang['disclaimer_acept'] = 'Debe Aceptar el Contrato.';

$lang['cancel_button'] = 'Cancelar.';

$lang['not_found_result'] = 'No hay registros que mostrar.';
$lang['found_result'] = 'Mostrando Registros {0} - {1} de un total de {2}.';

//Fin Dyna_View
?>