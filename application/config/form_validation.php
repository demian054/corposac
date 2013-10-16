<?php
$config = array(
  /*   * * ********************* Crear Cuerpo policial ********************** */
  'crear_cuerpo_policial' => array(
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre',
	  'rules' => 'required|min_length[3]|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'telefonos',
	  'label' => 'Telefonos',
	  'rules' => 'integer|min_length[7]|max_length[12]'
	),
	array(
	  'field' => 'fax',
	  'label' => 'Fax',
	  'rules' => 'integer|min_length[7]|max_length[12]'
	),
	array(
	  'field' => 'email',
	  'label' => 'Email',
	  'rules' => 'valid_email'
	),
	array(
	  'field' => 'sitio_web',
	  'label' => 'Sitio Web',
	  'rules' => 'valid_url'
	),
	array(
	  'field' => 'ffundacion',
	  'label' => 'Fecha Fundacion',
	  'rules' => 'required|valid_date|valid_date_greater_than_today'
	),
	array(
	  'field' => 'numero_gaceta_fundacion',
	  'label' => 'Nº Gaceta Fundación',
	  'rules' => 'integer|max_length[7]'
	),
	array(
	  'field' => 'instalacion_ubicacion_id',
	  'label' => 'Instalacion Ubicacion',
	  'rules' => 'required|integer|max_length[10]|callback_valid_ubicacion_padre'
	)
  ),
  /*   * * ********************* Editar Cuerpo policial ********************** */
  'editar_cuerpo_policial' => array(
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre',
	  'rules' => 'required|min_length[3]|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'telefonos',
	  'label' => 'Telefonos',
	  'rules' => 'integer|min_length[7]|max_length[12]'
	),
	array(
	  'field' => 'fax',
	  'label' => 'Fax',
	  'rules' => 'integer|min_length[7]|max_length[12]'
	),
	array(
	  'field' => 'email',
	  'label' => 'Email',
	  'rules' => 'valid_email'
	),
	array(
	  'field' => 'sitio_web',
	  'label' => 'Sitio Web',
	  'rules' => 'valid_url'
	),
	array(
	  'field' => 'ffundacion',
	  'label' => 'Fecha Fundacion',
	  'rules' => 'required|valid_date|valid_date_greater_than_today'
	),
	array(
	  'field' => 'numero_gaceta_fundacion',
	  'label' => 'Nº Gaceta Fundación',
	  'rules' => 'integer|max_length[7]'
	),
	array(
	  'field' => 'fmodificacion_inst_const',
	  'label' => 'Fecha Modificación Instrumento Constitutivo',
	  'rules' => 'valid_date|valid_date_greater_than_today'
	),
	array(
	  'field' => 'numero_gaceta_mod',
	  'label' => 'Nº Gaceta Modificación',
	  'rules' => 'integer|max_length[7]'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'sql_injection'
	)
  ),
  /*   * * ********************* Crear Instalaciones ********************** */
  'crear_instalaciones' => array(
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre',
	  'rules' => 'required|min_length[3]|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'ciudad',
	  'label' => 'Ciudad',
	  'rules' => 'min_length[2]|max_length[25]|valid_alpha_space'
	),
	array(
	  'field' => 'telefonos', 'label' => 'Telefonos',
	  'rules' => 'min_length[11]|max_length[150]|numeric'
	),
	array(
	  'field' => 'fax', 'label' => 'Fax',
	  'rules' => 'min_length[11]|max_length[150]|numeric'
	),
	array(
	  'field' => 'email', 'label' => 'Email',
	  'rules' => 'min_length[5]|max_length[150]|valid_email'
	),
	array(
	  'field' => 'fcreacion', 'label' => 'fecha de creacion',
	  'rules' => 'valid_date'
	),
	array(
	  'field' => 'direccion', 'label' => 'Dirección',
	  'rules' => 'min_length[5]|max_length[150]|sql_injection'
	)
  ),
  /*   * * ********************* Crear Junta Directiva ********************** */
  'add_junta_directiva' => array(
	array(
	  'field' => 'fnombramiento',
	  'label' => 'Fecha de nombramiento',
	  'rules' => 'valid_date'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'min_length[3]|sql_injection'),
  ),
  /*   * * ********************* Crear Departamentos ********************** */
  'crear_departamentos' => array(
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre',
	  'rules' => 'required|min_length[3]|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'telefonos',
	  'label' => 'Telefonos',
	  'rules' => 'min_length[11]|max_length[150]|numeric'
	),
	array(
	  'field' => 'fax',
	  'label' => 'Fax',
	  'rules' => 'min_length[11]|max_length[150]|numeric'
	),
	array(
	  'field' => 'email',
	  'label' => 'Email',
	  'rules' => 'min_length[5]|max_length[150]|valid_email'
	),
	array(
	  'field' => 'fcreacion',
	  'label' => 'Fecha de Creación',
	  'rules' => 'exact_length[10]|valid_date'
	)
  ),
  /*   * *********************Crear  Vacaciones ********************** */
  'crear_vacaciones' => array(
	array(
	  'field' => 'fsolicitud',
	  'label' => 'Fecha de Solicitud',
	  'rules' => 'required|valid_date|exact_length[10]|valid_date_greater_than_today'
	),
	array(
	  'field' => 'finicio',
	  'label' => 'Fecha Inicio',
	  'rules' => 'required|valid_date|exact_length[10]|valid_date_greater_than_date2[fsolicitud,Fecha de Solicitud]'
	),
	array(
	  'field' => 'fpago_bono',
	  'label' => 'Fecha Pago de Bono',
	  'rules' => 'valid_date|exact_length[10]|valid_date_greater_than_date2[fsolicitud,Fecha de Solicitud]'
	),
	array(
	  'field' => 'ffin',
	  'label' => 'Fecha Fin',
	  'rules' => 'required|valid_date|exact_length[10]|valid_date_greater_than_date2[finicio,Fecha Inicio]|valid_date_greater_than_date2[fsolicitud,Fecha de Solicitud]'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'min_length[3]|sql_injection'
	)
  ),
  /*   * *********************Crear  Prestaciones ********************** */
  'crear_prestaciones' => array(
	array(
	  'field' => 'chk_tipo_prestaciones',
	  'label' => 'Tipo de Operación',
	  'rules' => 'dropdown[chk_tipo_prestaciones,chk,TRUE]'
	),
	array(
	  'field' => 'fsolicitud',
	  'label' => 'Fecha de Solicitud',
	  'rules' => 'required|valid_date|exact_length[10]|valid_date_greater_than_today'
	),
	array(
	  'field' => 'fotorgamiento',
	  'label' => 'Fecha de Otorgamiento',
	  'rules' => 'valid_date|exact_length[10]|valid_date_greater_than_date2[fsolicitud,Fecha de Solicitud]'
	),
	array(
	  'field' => 'monto_entregado',
	  'label' => 'Monto Entregado',
	  'rules' => 'min_length[1]|max_length[20]|numeric'
	),
	array(
	  'field' => 'monto_revertido',
	  'label' => 'Monto Revertido',
	  'rules' => 'min_length[1]|max_length[20]|numeric'
	),
	array(
	  'field' => 'fregistro_pres',
	  'label' => 'Fecha de Prestamo',
	  'rules' => 'valid_date|exact_length[10]|valid_date_greater_than_date2[fsolicitud,Fecha de Solicitud]'
	),
	array(
	  'field' => 'pres_contabilidad',
	  'label' => 'Monto Contabilidad',
	  'rules' => 'min_length[1]|max_length[20]|numeric'
	),
	array(
	  'field' => 'pres_fideicomiso',
	  'label' => 'Monto Fideicomiso',
	  'rules' => 'min_length[1]|max_length[20]|numeric'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'min_length[3]|sql_injection'
	)
  ),
  /*   * ********************* Crear Persona ********************** */
  'persona' => array(
	array(
	  'field' => 'identificacion', 'label' => 'Identificacion',
	  'rules' => 'required|trim|max_lenght[10]|numeric'
	),
	array(
	  'field' => 'primer_nombre', 'label' => 'Primer Nombre',
	  'rules' => 'required|trim|max_length[25]|valid_alpha_space'
	),
	array(
	  'field' => 'segundo_nombre', 'label' => 'Segundo Nombre',
	  'rules' => 'trim|max_length[25]|valid_alpha_space'
	),
	array(
	  'field' => 'primer_apellido', 'label' => 'Primer Apellido',
	  'rules' => 'required|trim|max_length[25]|valid_alpha_space'
	),
	array(
	  'field' => 'segundo_apellido', 'label' => 'Segundo Apellido',
	  'rules' => 'trim|max_length[25]|valid_alpha_space'
	),
	array(
	  'field' => 'fecha_nacimiento', 'label' => 'Fecha Nacimiento',
	  'rules' => 'required|valid_date'
	),
	array(
	  'field' => 'chk_sexo', 'label' => 'Sexo',
	  'rules' => 'dropdown[chk_sexo,chk,TRUE]'
	),
	array(
	  'field' => 'chk_estado_civil', 'label' => 'Estado civil',
	  'rules' => 'dropdown[chk_estado_civil,chk,TRUE]'
	),
	array(
	  'field' => 'lugar_nacimiento', 'label' => 'Lugar Nacimiento',
	  'rules' => 'required|max_length[50]'
	),
	array(
	  'field' => 'telefono_personal', 'label' => 'Telefono Personal',
	  'rules' => 'required|numeric|exact_length[11]'
	),
	array(
	  'field' => 'telefono_opcional', 'label' => 'Telefono Opcional',
	  'rules' => 'numeric|exact_length[11]'
	),
	array(
	  'field' => 'email', 'label' => 'Email',
	  'rules' => 'valid_email|max_length[50]'
	),
	array(
	  'field' => 'desc_nivel_educativo', 'label' => 'Nivel Educativo',
	  'rules' => 'dropdown[nivel_educativo,desc,1]'
	),
	array(
	  'field' => 'grado_obtenido', 'label' => 'Grado Obtenido',
	  'rules' => 'valid_alpha_space'
	),
	array(
	  'field' => 'desc_grupo_sanguineo', 'label' => 'Grupo Sanguineo',
	  'rules' => 'dropdown[grupo_sanguineo,desc,0]'
	),
	array(
	  'field' => 'rif', 'label' => 'Rif',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'direccion', 'label' => 'Direccion',
	  'rules' => 'required|sql_injection'
	),
	array(
	  'field' => 'ubicacion_id', 'label' => 'Parroquia',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'zona_postal', 'label' => 'Zona Postal',
	  'rules' => 'numeric|exact_length[4]'
	),
  ),
  /*   * ********************* Persona ********************** */
  'search_persona' => array(
	array(
	  'field' => 'cedula', 'label' => 'Cedula',
	  'rules' => 'trim|max_lenght[10]|numeric'
	),
	array(
	  'field' => 'nomape', 'label' => 'Nombre y/o Apellido',
	  'rules' => 'trim|max_lenght[10]|alpha_numeric'
	),
  ),
  /*   * ********************* Editar Funcionario   ********************** */
  'editar_funcionario' => array(
	array(
	  'field' => 'primer_nombre_partida', 'label' => 'Primer Nombre',
	  'rules' => 'required|trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'segundo_nombre_partida', 'label' => 'Segundo Nombre',
	  'rules' => 'trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'primer_apellido_partida', 'label' => 'Primer Apellido',
	  'rules' => 'required|trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'segundo_apellido_partida', 'label' => 'Segundo Apellido',
	  'rules' => 'trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'estatura', 'label' => 'estatura',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'peso', 'label' => 'peso',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'chk_estado_vital', 'label' => 'chk_estado_vital',
	  'rules' => 'dropdown[chk_estado_vital,chk,TRUE]'
	),
	array(
	  'field' => 'chk_grado_licencia', 'label' => 'chk_grado_licencia',
	  'rules' => 'dropdown[chk_grado_licencia,chk,TRUE]'
	),
	array(
	  'field' => 'chk_color_piel', 'label' => 'chk_color_piel',
	  'rules' => 'dropdown[chk_color_piel,chk,TRUE]'
	),
	array(
	  'field' => 'chk_color_iris', 'label' => 'chk_color_iris',
	  'rules' => 'dropdown[chk_color_iris,chk,TRUE]'
	),
	array(
	  'field' => 'chk_talla_camisa', 'label' => 'chk_talla_camisa',
	  'rules' => 'dropdown[chk_talla_camisa,chk,TRUE]'
	),
	array(
	  'field' => 'chk_talla_pantalon', 'label' => 'chk_talla_pantalon',
	  'rules' => 'dropdown[chk_talla_pantalon,chk,TRUE]'
	),
	array(
	  'field' => 'chk_color_cabello', 'label' => 'chk_color_cabello',
	  'rules' => 'dropdown[chk_color_cabello,chk,TRUE]'
	),
	array(
	  'field' => 'chk_talla_calzado', 'label' => 'chk_talla_calzado',
	  'rules' => 'dropdown[chk_talla_calzado,chk,TRUE]'
	),
	array(
	  'field' => 'chk_complexion', 'label' => 'chk_complexion',
	  'rules' => 'dropdown[chk_complexion,chk,TRUE]'
	),
	array(
	  'field' => 'senas_particulares', 'label' => 'senas_particulares',
	  'rules' => 'valid_alpha_space'
	),
	array(
	  'field' => 'acto_nombramiento', 'label' => 'acto_nombramiento',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'senas_particulares', 'label' => 'senas_particulares',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'publicaciones', 'label' => 'publicaciones',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'actividades_deportivas', 'label' => 'actividades_deportivas',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'actividades_docentes', 'label' => 'actividades_docentes',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'activo', 'label' => 'activo',
	  'rules' => 'exact_length[1]'
	),
	array(
	  'field' => 'visibilidad', 'label' => 'visibilidad',
	  'rules' => 'exact_length[1]'
	),
  ),
  /*   * ********************* Crear Funcionario   ********************** */
  'crear_funcionario' => array(
	array(
	  'field' => 'primer_nombre_partida', 'label' => 'Primer Nombre',
	  'rules' => 'required|trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'segundo_nombre_partida', 'label' => 'Segundo Nombre',
	  'rules' => 'trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'primer_apellido_partida', 'label' => 'Primer Apellido',
	  'rules' => 'required|trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'segundo_apellido_partida', 'label' => 'Segundo Apellido',
	  'rules' => 'trim|max_length[25]|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'estatura', 'label' => 'estatura',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'peso', 'label' => 'peso',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'chk_estado_vital', 'label' => 'chk_estado_vital',
	  'rules' => 'dropdown[chk_estado_vital,chk,TRUE]'
	),
	array(
	  'field' => 'chk_grado_licencia', 'label' => 'chk_grado_licencia',
	  'rules' => 'dropdown[chk_grado_licencia,chk,TRUE]'
	),
	array(
	  'field' => 'chk_color_piel', 'label' => 'chk_color_piel',
	  'rules' => 'dropdown[chk_color_piel,chk,TRUE]'
	),
	array(
	  'field' => 'chk_color_iris', 'label' => 'chk_color_iris',
	  'rules' => 'dropdown[chk_color_iris,chk,TRUE]'
	),
	array(
	  'field' => 'chk_talla_camisa', 'label' => 'chk_talla_camisa',
	  'rules' => 'dropdown[chk_talla_camisa,chk,TRUE]'
	),
	array(
	  'field' => 'chk_talla_pantalon', 'label' => 'chk_talla_pantalon',
	  'rules' => 'dropdown[chk_talla_pantalon,chk,TRUE]'
	),
	array(
	  'field' => 'chk_color_cabello', 'label' => 'chk_color_cabello',
	  'rules' => 'dropdown[chk_color_cabello,chk,TRUE]'
	),
	array(
	  'field' => 'chk_talla_calzado', 'label' => 'chk_talla_calzado',
	  'rules' => 'dropdown[chk_talla_calzado,chk,TRUE]'
	),
	array(
	  'field' => 'chk_complexion', 'label' => 'chk_complexion',
	  'rules' => 'dropdown[chk_complexion,chk,TRUE]'
	),
	array(
	  'field' => 'departamento_id', 'label' => 'departamento_id',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'cargo_id', 'label' => 'cargo_id',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'rango_id', 'label' => 'rango_id',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'chk_tipo_inicio', 'label' => 'chk_tipo_inicio',
	  'rules' => 'dropdown[chk_tipo_inicio,chk,TRUE]'
	),
	array(
	  'field' => 'finicio', 'label' => 'finicio',
	  'rules' => 'required|valid_date_greater_than_today'
	),
  ),
  'idiomas_funcionarios' => array(
	array(
	  'field' => 'id',
	  'label' => 'Id',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'funcionario_id',
	  'label' => 'Id Funcionario',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'desc_idiomas',
	  'label' => 'Idioma',
	  'rules' => 'numeric|required|max_length[25]|greater_than[0]'
	),
	array(
	  'field' => 'chk_nivel_lectura',
	  'label' => 'Nivel de Lectura',
	  'rules' => 'required|valid_alpha_numeric_space|max_length[25]'
	),
	array(
	  'field' => 'chk_nivel_escritura',
	  'label' => 'Nivel de Escritura',
	  'rules' => 'required|valid_alpha_numeric_space|max_length[25]'
	),
	array(
	  'field' => 'chk_nivel_habla',
	  'label' => 'Nivel de Escritura',
	  'rules' => 'required|valid_alpha_numeric_space|max_length[25]'
	),
	array(
	  'field' => 'institucion',
	  'label' => 'Institucion',
	  'rules' => 'required|max_length[25]'
	),
	array(
	  'field' => 'calificacion',
	  'label' => 'Calificacion',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'examen',
	  'label' => 'Examen',
	  'rules' => 'valid_alpha_numeric_space|max_length[50]'
	),
	array(
	  'field' => 'escala_min_calificacion',
	  'label' => 'Minima Calificacion',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'escala_max_calificacion',
	  'label' => 'Maxima Calificacion',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'max_length[350]'
	)
  ),
  'familiares_funcionarios' => array(
	array(
	  'field' => 'persona_id',
	  'label' => 'Id Persona',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'funcionario_id',
	  'label' => 'Id Funcionario',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'chk_parentesco',
	  'label' => 'Parentesco',
	  'rules' => 'required|max_length[25]'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'valid_alpha_numeric_space|max_length[350]'
	)
  ),
  'familiares_policiales_funcionarios' => array(
	array(
	  'field' => 'funcionario_familiar_id',
	  'label' => 'Id Funcionario Policial',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'funcionario_id',
	  'label' => 'Id Funcionario',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'chk_parentesco',
	  'label' => 'Parentesco',
	  'rules' => 'required|max_length[25]'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'max_length[350]'
	)
  ),
  'estudios_funcionarios' => array(
	array(
	  'field' => 'id',
	  'label' => 'Id Funcionario Policial',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'funcionario_id',
	  'label' => 'Id Funcionario',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'institucion',
	  'label' => 'Institucion',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'lugar',
	  'label' => 'lugar',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'finicio',
	  'label' => 'Fecha de Inicio',
	  'rules' => 'required|valid_date'
	),
	array(
	  'field' => 'ffin',
	  'label' => 'Fecha Fin',
	  'rules' => 'valid_date'
	),
	array(
	  'field' => 'duracion',
	  'label' => 'Duracion',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'chk_unidad_duracion',
	  'label' => 'Chk Unidad de Duracion',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'calificacion',
	  'label' => 'Calificacion',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'escala_min_calificacion',
	  'label' => 'Escala Minima de Calificacion',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'escala_max_calificacion',
	  'label' => 'Escala Maxima de Calificacion',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'departamento',
	  'label' => 'Departamento',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'reconocimientos_obt',
	  'label' => 'Reconocimientos Obtenidos',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'area_conocimiento',
	  'label' => 'Area de Conocimiento',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'chk_regimen',
	  'label' => 'Chk Regimen',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'nomnbre_promocion',
	  'label' => 'Nombre de Promocion',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'fregistro_titulo',
	  'label' => 'Fecha de Registro de Titulo',
	  'rules' => 'valid_date'
	),
	array(
	  'field' => 'puesto_promocion',
	  'label' => 'Puesto en la Promocion',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'total_graduandos',
	  'label' => 'Total de Graduandos',
	  'rules' => 'numeric'
	),
	array(
	  'field' => 'chk_tipo_estudio',
	  'label' => 'Tipo de Estudio',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'certificado',
	  'label' => 'Certificado',
	  'rules' => 'alpha_numeric'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'max_length[350]'
	)
  ),
  'exportar' => array(
	array(
	  'field' => 'check[0]',
	  'label' => 'Nombre',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[1]',
	  'label' => 'Ámbito',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[2]',
	  'label' => 'Teléfonos',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[3]',
	  'label' => 'Fax',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[4]',
	  'label' => 'Email',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	), array(
	  'field' => 'check[5]',
	  'label' => 'Página Web',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	), array(
	  'field' => 'check[6]',
	  'label' => 'Fecha de Fundación',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[7]',
	  'label' => 'Número de Gaceta de Fundación',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[8]',
	  'label' => 'Fecha de Modificación del Instrumento Constitutivo',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	), array(
	  'field' => 'check[9]',
	  'label' => 'Número de Gaceta de Modificación',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[10]',
	  'label' => 'Ubicación',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[11]',
	  'label' => 'Instalaciones',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	),
	array(
	  'field' => 'check[12]',
	  'label' => 'Organigrama',
	  'rules' => 'valid_alpha_numeric_space|sql_injection|xss_clean'
	)
  ),
  /*   * ********************* Crear Nomina ********************** */
  'crear_detalle_nomina' => array(
	array(
	  'field' => 'ultimo_salario',
	  'label' => 'Ultimo Salario',
	  'rules' => 'required|numeric|max_length[11]'
	),
	array(
	  'field' => 'benef_remunerativos',
	  'label' => 'Beneficios Remunerativos',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'benef_no_remunerativos',
	  'label' => 'Beneficios No Remunerativos',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'deducciones',
	  'label' => 'Deducciones',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'des_bancos',
	  'label' => 'Descriptior de Banco',
	  'rules' => 'is_natural'
	),
	array(
	  'field' => 'numero_cuenta',
	  'label' => 'Nuemero de Cuenta',
	  'rules' => 'min_length[20]|max_length[25]|alpha_dash'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'valid_alpha_numeric_space'
	)
  ),
  /*   * ********************* Crear Experiencia Laboral ********************** */
  'crear_experiencia_laboral' => array(
	array(
	  'field' => 'chk_sector',
	  'label' => 'Sector',
	  'rules' => 'dropdown[chk_sector,chk,1]'
	),
	array(
	  'field' => 'organismo_empresa',
	  'label' => 'Organismo Empresa',
	  'rules' => 'required|valid_alpha_numeric_space'
	),
	array(
	  'field' => 'finicio',
	  'label' => 'Fecha Inicio',
	  'rules' => 'required|valid_date|valid_date_greater_than_today'
	),
	array(
	  'field' => 'ffin',
	  'label' => 'Fecha Fin',
	  'rules' => 'required|valid_date|valid_date_greater_than_today|valid_date_greater_than_date2[finicio,Fecha Inicio]'
	),
	array(
	  'field' => 'ultimo_cargo',
	  'label' => 'Ultimo cargo',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'ultimo_salario_mensual',
	  'label' => 'Ultimo salario mensual',
	  'rules' => 'required|numeric'
	),
	array(
	  'field' => 'causa_retiro',
	  'label' => 'Causa retiro',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'valid_alpha_numeric_space'
	)
  ),
  /*   * ********************* Crear Experiencia Policial ********************** */
  'crear_experiencia_policial' => array(
	array(
	  'field' => 'finicio',
	  'label' => 'Fecha Inicio',
	  'rules' => 'required|valid_date|valid_date_greater_than_today'
	),
	array(
	  'field' => 'ffin',
	  'label' => 'Fecha Fin',
	  'rules' => 'valid_date|valid_date_greater_than_today|valid_date_greater_than_date2[finicio,Fecha Inicio]'
	),
	array(
	  'field' => 'chk_tipo_inicio',
	  'label' => 'Tipo Inicio',
	  'rules' => 'dropdown[chk_tipo_inicio,chk,1]'
	),
	array(
	  'field' => 'departamento_id', // CONVERTIR ESTE CAMPO EN DROPDOWN Y VALIDAR
	  'label' => 'Departamento',
	  'rules' => 'required'
	),
	array(
	  'field' => 'cargo_id', // CONVERTIR ESTE CAMPO EN DROPDOWN Y VALIDAR
	  'label' => 'Cargo',
	  'rules' => 'required'
	),
	array(
	  'field' => 'desc_jornadas_laborales',
	  'label' => 'Jornada Laboral',
	  'rules' => 'dropdown[jornadas_laborales,desc,1]'
	),
	array(
	  'field' => 'rango_id', // CONVERTIR ESTE CAMPO EN DROPDOWN Y VALIDAR
	  'label' => 'Rango',
	  'rules' => 'required'
	),
	array(
	  'field' => 'chk_tipo_ascenso',
	  'label' => 'Tipo Ascenso',
	  'rules' => 'dropdown[chk_tipo_ascenso,chk,0]'
	),
	array(
	  'field' => 'facto',
	  'label' => 'Fecha Acto',
	  'rules' => 'valid_date|valid_date_greater_than_today|valid_date_greater_than_date2[fnotificacion,Fecha Notificacion]'
	),
	array(
	  'field' => 'fnotificacion',
	  'label' => 'Fecha Notificacion',
	  'rules' => 'valid_date|valid_date_greater_than_today'
	),
	array(
	  'field' => 'desc_causales_egresos',
	  'label' => 'Causal Egreso',
	  'rules' => 'dropdown[chk_tipo_ascenso,chk,0]'
	),
	array(
	  'field' => 'detalle_egreso',
	  'label' => 'Detalle Egreso',
	  'rules' => 'valid_alpha_numeric_space'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'valid_alpha_numeric_space'
	),
  ),
  'deactivate_funcionario' => array(
	array(
	  'field' => 'ffin',
	  'label' => 'Fecha de Egreso',
	  'rules' => 'valid_date|valid_date_greater_than_today|required'
	),
	array(
	  'field' => 'desc_causales_egresos',
	  'label' => 'Causal de Egreso',
	  'rules' => 'dropdown[causales_egresos,desc,1]'
	),
	array(
	  'field' => 'detalle_egreso',
	  'label' => 'Detalle del Egreso',
	  'rules' => 'valid_alpha_numeric_space'
	),
  ),
  'crear_campo_dinamico' => array(
	array(
	  'field' => 'entidad_id',
	  'label' => 'Entidad',
	  'rules' => 'required|integer'
	),
	array(
	  'field' => 'agrupador_id',
	  'label' => 'Agrupador',
	  'rules' => 'integer'
	),
	array(
	  'field' => 'etiqueta',
	  'label' => 'Nombre Visual',
	  'rules' => 'valid_alpha_numeric_space|required'
	),
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre Interno',
	  'rules' => 'required|alpha_dash'
	),
	array(
	  'field' => 'ayuda',
	  'label' => 'ToolTip/Ayuda',
	  'rules' => 'valid_alpha_numeric_space|required'
	),
	array(
	  'field' => 'chk_periodicidad',
	  'label' => 'Frecuencia de Actualizacion',
	  'rules' => 'dropdown[chk_periodicidad,chk,TRUE]'
	),
	array(
	  'field' => 'periodo_id',
	  'label' => 'Periodo Inicial',
	  'rules' => 'integer'
	),
	array(
	  'field' => 'anio',
	  'label' => 'Año',
	  'rules' => 'integer|exact_length[4]'
	),
	array(
	  'field' => 'tipo_dato',
	  'label' => 'Tipo de dato del campo',
	  'rules' => 'dropdown[chk_tipo_campo,chk,TRUE]'
	),
	array(
	  'field' => 'tipo_rol',
	  'label' => 'Tipos de Roles',
	  'rules' => 'dropdown[chk_tipo_rol,chk,TRUE]'
	),
  ),
  'crear_campo_auto' => array(
	array(
	  'field' => 'entidad_id',
	  'label' => 'Entidad',
	  'rules' => 'required|integer'
	),
	array(
	  'field' => 'agrupador_id',
	  'label' => 'Agrupador',
	  'rules' => 'integer'
	),
	array(
	  'field' => 'etiqueta',
	  'label' => 'Nombre Visual',
	  'rules' => 'valid_alpha_numeric_space|required|max_length[150]'
	),
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre Interno',
	  'rules' => 'required|alpha_dash|max_length[50]'
	),
	array(
	  'field' => 'ayuda',
	  'label' => 'ToolTip/Ayuda',
	  'rules' => 'valid_alpha_numeric_space|required|max_length[150]'
	),
	array(
	  'field' => 'chk_periodicidad',
	  'label' => 'Frecuencia de Actualizacion',
	  'rules' => 'dropdown[chk_periodicidad,chk,TRUE]'
	),
	array(
	  'field' => 'periodo_id',
	  'label' => 'Periodo Inicial',
	  'rules' => 'integer'
	),
	array(
	  'field' => 'anio',
	  'label' => 'Año',
	  'rules' => 'integer|exact_length[4]'
	),
	array(
	  'field' => 'tipo_rol',
	  'label' => 'Tipos de Roles',
	  'rules' => 'dropdown[chk_tipo_rol,chk,TRUE]'
	),
	array(
	  'field' => 'var_cuerpo_policial_id',
	  'label' => 'Valor a Reemplazar del Cuerpo Policial',
	  'rules' => 'required|integer'
	),
	array(
	  'field' => 'var_periodo_inicial_id',
	  'label' => 'Valor a Reemplazar del periodo inicial',
	  'rules' => 'integer|max_length[2]'
	),
	array(
	  'field' => 'var_periodo_final_id',
	  'label' => 'Valor a Reemplazar del periodo final',
	  'rules' => 'integer|max_length[2]'
	),
	array(
	  'field' => 'var_anio',
	  'label' => 'Valor a Reemplazar del año',
	  'rules' => 'integer|exact_length[4]'
	),
	array(
	  'field' => 'sql',
	  'label' => 'SQL ',
	  'rules' => 'required|callback_validateSql'
	)
  ),
  /*   * ********************* Crear Cargos ********************** */
  'crear_cargos' => array(
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre Cargo',
	  'rules' => 'required|valid_alpha_numeric_space|'
	),
	array(
	  'field' => 'observaciones',
	  'label' => 'Observaciones',
	  'rules' => 'min_length[3]|valid_alpha_numeric_space'
	)
  ),
  /*   * ****************Indicadores****************************** */
  'indicador' => array(
	array(
	  'field' => 'codigo',
	  'label' => 'Codigo',
	  'rules' => 'required|valid_alpha_numeric|min_length[2]|max_length[15]'
	),
	array(
	  'field' => 'nombre',
	  'label' => 'Nombre',
	  'rules' => 'required|valid_alpha_numeric_space|max_length[100]'
	),
	array(
	  'field' => 'nombre_columna',
	  'label' => 'Nombre columna/label',
	  'rules' => 'required|valid_alpha_numeric_space|max_length[25]'
	),
	array(
	  'field' => 'anio',
	  'label' => 'Año',
	  'rules' => 'required|integer|exact_length[4]'
	),
	array(
	  'field' => 'entes_fuentes[]',
	  'label' => 'Entes Fuentes',
	  'rules' => 'required|integer'
	),
	array(
	  'field' => 'unidad_medida',
	  'label' => 'Unidad de medida',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'estandares[]',
	  'label' => 'Estandares',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'chk_agregracion_cp_estadal',
	  'label' => 'Tipo de calculo CP estadal',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'chk_agregracion_cp_municipal',
	  'label' => 'Tipo de calculo CP municipal',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'chk_agregracion_por_estado',
	  'label' => 'Tipo de calculo CP  por Estado',
	  'rules' => 'required|alpha_numeric'
	),
	array(
	  'field' => 'chk_agregracion_por_pais',
	  'label' => 'Tipo de calculo CP por pais',
	  'rules' => 'required|alpha_numeric'
	),
	
	array(
	  'field' => 'chk_formula',
	  'label' => 'Tipo Formula',
	  'rules' => 'required|callback_validateFormula'
	),
  ),
);
?>
