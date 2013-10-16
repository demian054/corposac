<?php

if (!defined('BASEPATH'))
    exit('Acceso Denegado');

$_checks['chk_estado_vital'] = array('Vivo' => 'Vivo', 'Fallecido' => 'Fallecido', 'Desaparecido' => 'Desaparecido');
$_checks['chk_grado_licencia'] = array('1ra' => '1ra', '2da' => '2da', '3ra' => '3ra', '4ta' => '4ta', '5ta' => '5ta',
    'No tiene' => 'No tiene');
$_checks['chk_color_piel'] = array('Albina' => 'Albina', 'Amarilla' => 'Amarilla', 'Blanca' => 'Blanca', 'Morena' => 'Morena',
    'Negra' => 'Negra', 'Rojiza' => 'Rojiza', 'Triguena' => 'Triguena', 'Desconocido' => 'Desconocido');
$_checks['chk_color_iris'] = array('Albino' => 'Albino', 'Ambar' => 'Ambar', 'Azul' => 'Azul', 'Cafe' => 'Cafe', 'Gris' => 'Gris',
    'Marron' => 'Marron', 'Negro' => 'Negro', 'Verdes' => 'Verdes', 'Desconocido' => 'Desconocido');
$_checks['chk_color_cabello'] = array('Blanco' => 'Blanco', 'Castaño claro' => 'Castaño claro', 'Castaño medio' => 'Castaño medio',
    'Castaño oscuro' => 'Castaño oscuro', 'Gris' => 'Gris', 'Negro' => 'Negro', 'Rojo' => 'Rojo', 'Rubio' => 'Rubio',
    'Desconocido' => 'Desconocido');
$_checks['chk_talla_camisa'] = array('XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL', 'XXL' => 'XXL',
    'XXXL' => 'XXXL');
$_checks['chk_talla_pantalon'] = array('4' => '4', '6' => '6', '8' => '8', '10' => '10', '12' => '12', '14' => '14', '16' => '16',
    '18' => '18', '20' => '20', '22' => '22', '24' => '24', '26' => '26', '28' => '28', '30' => '30', '32' => '32', '34' => '34',
    '36' => '36', '38' => '38', '40' => '40', '42' => '42', '44' => '44', '46' => '46', '48' => '48');
$_checks['chk_talla_calzado'] = array('28' => '28', '28.5' => '28.5', '29' => '29', '29.5' => '29.5', '30' => '30',
    '30.5' => '30.5', '31' => '31', '31.5' => '31.5', '32' => '32', '32.5' => '32.5', '33' => '33', '33.5' => '33.5', '34' => '34',
    '34.5' => '34.5', '35' => '35', '35.5' => '35.5', '36' => '36', '36.5' => '36.5', '37' => '37', '37.5' => '37.5', '38' => '38',
    '38.5' => '38.5', '39' => '39', '39.5' => '39.5', '40' => '40', '40.5' => '40.5', '41' => '41', '41.5' => '41.5', '42' => '42',
    '42.5' => '42.5', '43' => '43', '43.5' => '43.5', '44' => '44', '44.5' => '44.5', '45' => '45', '45.5' => '45.5', '46' => '46',
    '46.5' => '46.5', '47' => '47', '47.5' => '47.5', '48' => '48', '48.5' => '48.5', '49' => '49', '49.5' => '49.5', '50' => '50',
    '50,5' => '50,5', '51' => '51', '51,5' => '51,5', '52' => '52', '52.5' => '52.5', '53' => '53', '53.5' => '53.5', '54' => '54',
    '54.5' => '54.5', '55' => '55');
$_checks['chk_complexion'] = array('Hectomorfica (Delgada)' => 'Hectomorfica (Delgada)',
    'Mesomorfica (Media)' => 'Mesomorfica (Media)', 'Endomorfica (Robusta)' => 'Endomorfica (Robusta)',
    'Desconocido' => 'Desconocido');
$_checks['chk_parentesco'] = array('Hijo(a)' => 'Hijo(a)', 'Padre' => 'Padre', 'Madre' => 'Madre', 'Tio(a)' => 'Tio(a)',
    'Sobrino(a)' => 'Sobrino(a)', 'Primo(a)' => 'Primo(a)', 'Hermano(a)' => 'Hermano(a)', 'Abuelo(a)' => 'Abuelo(a)',
    'Nieto(a)' => 'Nieto(a)', 'Conyuge' => 'Conyuge');
$_checks['chk_condicion_ciudadana'] = array('Civil' => 'Civil', 'Militar' => 'Militar');
$_checks['chk_ubicaciones'] = array('Estado' => 'Estado', 'Municipio' => 'Municipio', 'Pais' => 'Pais');
$_checks['chk_operatividad'] = array('Operativa' => 'Operativa', 'No operativa' => 'No operativa');
$_checks['chk_estatus'] = array('Asignada' => 'Asignada', 'En parque de armas' => 'En parque de armas',
    'Extraviada' => 'Extraviada', 'Robada' => 'Robada', 'En averiguacion' => 'En averiguacion');
$_checks['chk_tipo_reconocimientos'] = array('Felicitacion' => 'Felicitacion', 'Condecoracion' => 'Condecoracion',
    'Diploma' => 'Diploma', 'Placa' => 'Placa', 'Otro' => 'Otro');
$_checks['chk_unidad_duracion'] = array('Horas' => 'Horas', 'Dias' => 'Dias', 'Semanas' => 'Semanas', 'Meses' => 'Meses',
    'Años' => 'Años');
$_checks['chk_regimen'] = array('Mensual' => 'Mensual', 'Trimestral' => 'Trimestral', 'Semestral' => 'Semestral',
    'Anual' => 'Anual', 'Especial' => 'Especial');
$_checks['chk_tipo_estudio'] = array('Diplomado' => 'Diplomado', 'Curso' => 'Curso', 'Ponencia' => 'Ponencia', 'TSU' => 'TSU',
    'Licenciatura' => 'Licenciatura', 'Especialidad' => 'Especialidad', 'Maestria' => 'Maestria', 'Doctorado' => 'Doctoroado',
    'Postgrado' => 'Postgrado');
$_checks['chk_sector'] = array('Publico' => 'Publico', 'Privado' => 'Privado');
$_checks['chk_tipo_inicio'] = array('Nuevo' => 'Nuevo', 'Reincorporado' => 'Reincorporado');
$_checks['chk_tipo_ascenso'] = array('Ascenso' => 'Ascenso', 'Homologacion' => 'Homologacion');
$_checks['chk_tipo_identificacion'] = array('V' => 'V', 'E' => 'E', 'P' => 'P');
$_checks['chk_tipo_referencia'] = array('Personal' => 'Personal', 'Profesional' => 'Profesional');
$_checks['chk_sexo'] = array('M' => 'M', 'F' => 'F');
$_checks['chk_estado_civil'] = array('Soltero(a)' => 'Soltero(a)', 'Casado(a)' => 'Casado(a)',
    'Concubino(a)' => 'Concubino(a)', 'Viudo(a)' => 'Viudo(a)', 'Divorciado(a)' => 'Divorciado(a)');
$_checks['chk_ambito_politico_ter'] = array('Nacional' => 'Nacional', 'Estadal' => 'Estadal', 'Municipal' => 'Municipal');
$_checks['chk_tipo_jerarquia'] = array('Collateral' => 'Colateral', 'Staff' => 'Staff', 'Subordinate' => 'Subordinado', 'N/A' => 'N/A');
$_checks['chk_nivel_lectura'] = array('Malo' => 'Malo', 'Regular' => 'Regular', 'Bueno' => 'Bueno', 'Muy Bueno' => 'Muy Bueno',
    'Excelente' => 'Excelente');
$_checks['chk_nivel_escritura'] = array('Malo' => 'Malo', 'Regular' => 'Regular', 'Bueno' => 'Bueno', 'Muy Bueno' => 'Muy Bueno',
    'Excelente' => 'Excelente');
$_checks['chk_nivel_habla'] = array('Malo' => 'Malo', 'Regular' => 'Regular', 'Bueno' => 'Bueno', 'Muy Bueno' => 'Muy Bueno',
    'Excelente' => 'Excelente');
$_checks['chk_tipo_identificacion_persona'] = array('V' => 'V', 'E' => 'E', 'P' => 'P');
$_checks['chk_role_type'] = array('CGP' => 'CGP', 'CP' => 'CP', 'F' => 'F');
$_checks['chk_role_type_widget'] = array('CGP' => 'CGP', 'CP' => 'CP', 'F' => 'F', 'CGCP' => 'CGP/CP', 'T' => 'CGP/CP/F');
$_checks['chk_columna'] = array('1ra' => '1ra', '2da' => '2da', '3ra' => '3ra');
$_checks['chk_tipo_prestaciones'] = array('Adelanto' => 'Adelanto', 'Prestamo' => 'Préstamo');
$_checks['chk_component_type'] = array('Module' => 'Module', 'Controller' => 'Controller', 'Method' => 'Method',
    'Task' => 'Task', 'NA' => 'NA');
$_checks['chk_visual_type'] = array('Accordion' => 'Accordion', 'Menu' => 'Menu', 'Submenu' => 'Submenu',
    'Tab' => 'Tab', 'Panel' => 'Panel',
    'Button_A' => 'Button_A', 'Button_C' => 'Button_C', 'Button_D' => 'Button_D', 'Button_U' => 'Button_U', 'Button_R' => 'Button_R',
    'Button_F' => 'Button_F', 'Button_L' => 'Button_L', 'Button_S' => 'Button_S', 'NA' => 'NA');
$_checks['chk_periodicidad_campo'] = array('Mensual' => 'Mensual', 'Trimestral' => 'Trimestral', 'Semestral' => 'Semestral',
    'Anual' => 'Anual', 'N/A' => 'N/A');
$_checks['chk_periodicidad'] = array('Mensual' => 'Mensual', 'Trimestral' => 'Trimestral', 'Semestral' => 'Semestral',
    'Anual' => 'Anual');
$_checks['chk_tipo_campo'] = array('Entero' => 'Entero', 'Decimal' => 'Decimal', 'Lista de Valores' => 'Lista de Valores');
$_checks['chk_tipo_rol'] = array('CGP' => 'CGP', 'CP' => 'CP');
$_checks['chk_tipo_calculo'] = array('Promedio Simple' => 'Promedio Simple',
    'Promedio Ponderado por Habitantes' => 'Promedio Ponderado por Habitantes',
    'Promedio Ponderado por Pie de Fuerza' => 'Promedio Ponderado por Pie de Fuerza',
    'Frecuencia' => 'Frecuencia');
$_checks['chk_formula'] = array('cociente' => 'cociente', 'porcentaje' => 'porcentaje', 'valor_directo' => 'valor_directo');
$_checks['chk_relacion'] = array('numerador' => 'numerador', 'denominador' => 'denominador', 'directo' => 'directo');
$_checks['chk_ambito'] = array('CP Municipales' => 'CP Municipales', 'CP Estadales' => 'CP Estadales', 'Por Estado' => 'Por Estado', 'Por Pais' => 'Por Pais');
$_checks['chk_tipo_usuario'] = array('Administrativo (No Funcionario)' => 'Administrativo (No Funcionario)', 'Funcionario' => 'Funcionario');
$_checks['chk_reporte_tipo_archivo'] = array('pdf' => 'PDF', 'csv' => 'CSV');
$_checks['chk_visibilidad'] = array('Todos' => 'Todos', 'CGP' => 'CGP', 'CP' => 'CP', 'F' => 'F');
$_checks['chk_tipo_ranking'] = array('Global' => 'Global', 'Por area de estandarizacion' => 'Por área de estandarización');
$_checks['chk_type_report'] = array('1' => 'Porcentaje/Promedio', '2' => 'Número Promedio', '3' => 'Número y Porcentaje', '3' => 'Listado', '4' => 'Tasa');
$_checks['chk_tipo_ubicacion'] = array ('Pais'=>'Nacional', 'Estado' => 'Estadal', 'Municipio' => 'Municipal');
$_checks['chk_ambpolterr'] = array('Por Pais' => 'Por País', 'Por Estado'=>'Por Estado', 'Nacional'=>'Nacionales', 'Estadal' => 'Estadales', 'Municipal' => 'Municipales');
/* End of file checks.php */
/* Location: ./application/config/checks.php */
