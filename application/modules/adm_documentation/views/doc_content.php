<?if(!$bienvenida):?>
<div class = "doc_header"><?= $operacion->nombre ?></div>
<div class = "doc_content"><?= $operacion->descripcion ?>

	<div class="doc_campos">
		<ol class ="doc_ol">
			<? foreach ($campos as $element): ?>
				<li><?= $element->etiqueta ?>: <span class="doc_campo_ayuda"><?= $element->ayuda ?></span></li>
			<? endforeach; ?>
		</ol>
	</div>
	<?if(file_exists('assets/img/screenshots/' . $operacion->id . '.png')):?>
	<div class="doc_screenshot"><p>Capturas de Pantalla:</p><br />
		<span class ="doc_span_link"><img src="<?= base_url() . 'assets/img/screenshots/' . $operacion->id . '.png' ?>" 
			 alt="<?= $operacion->nombre ?>" 
			 onClick="showScreenshot('<?= $operacion->id ?>', '<?= $operacion->nombre ?>')" 
			 width="100"/></span>
	</div>
	<?endif?>
	<div class="doc_navegacion">
		<? if ($padre->nombre): ?>
			<div class="doc_table_title">Mi operación padre es:</div>
			<div class="doc_table_content"> <span class="doc_span_link" onClick="getCenterDoc('<?= $padre->id ?>')"> <?= $padre->nombre ?></span></div>
		<? endif; ?>
		<? if (!empty($hijos)):?>	
			<div class="doc_table_title">Mis operaciones hijos son:</div>
			<? foreach ($hijos AS $element): ?>
				<div class="doc_table_content"> <span class="doc_span_link" onClick="getCenterDoc('<?= $element->id ?>')"> <?= $element->nombre ?></span></div>
			<? endforeach; ?>
		<? endif; ?>
	</div>
</div>
<?elseif($bienvenida):?>
<div class = "doc_header">Documentaci&oacute;n RABSA Sistema de Presupuesto</div>
<div class = "doc_content">
	Aqu&iacute; Ud. encontrar&aacute; toda la informaci&oacute;n referente al Sistema de Control Presupuestario para la Red de Abastos Bicentenario (RABSA).<br /><br />
	Utilice el men&uacute; lateral para navegar a trav&eacute;s de la documentaci&oacute;n.<br /><br />
	Presione el bot&oacute;n <b>Versi&oacute;n Imprimible</b> para obtener la documentaci&oacute;n en el formato que Ud. desee.
</div>
<? endif; ?>
