<?

if ($out_of_stock->num_rows() > 0):
	echo "<strong>Se han Virtualizado las siguientes Tablas:</strong><br /><br />";

	// Tablas virtualizadas
	foreach ($out_of_stock->result() as $key => $value):
		echo "$key->table_schema.$key->table_name" . br();
	endforeach;
	echo br() . "y sus respectivos campos.";

else:
	echo "<strong>No existen Tablas para virtualizar</strong>";
endif;
?>