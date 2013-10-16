<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf extends TCPDF {

	function __construct() {
		parent::__construct();
	}

	// Atributos utilizados para la generacion de tablas
	var $widths;
	var $aligns;
	// Fondo del rectangulo de cada columna en la tabla
	var $fill;

	// Funcion get del atributo $rect_color
	function getFill() {
		return $this->fill;
	}

	// Funcion set del atributo $rect_color
	function setFill($_fill) {
		$this->fill = $_fill;
	}

	// Ancho de la tabla que muestra los valores en el reporte
	var $max_table_width = 180;

	/**
	 * <b>Method:	header()</b>
	 * @method		Header redefinido para Sietpol
	 * @param		$param
	 * @return		return
	 * @author		Eliel Parra
	 * @version		v1.0 19/12/11 02:56 PM
	 * */
	function Header($param) {

		$this->SetFont('helvetica', 'B', 10, '', true);
		$this->SetY(20);
		$this->Cell(183, 5, 'SIETPOL: Sistema de Información Estratégica y Transparencia Policial', 0, 1, 'C', 0);
		$this->ln(1);
		$this->SetLineWidth(0.3);
		$this->Line(15, 26, 195, 26);
	}

	/**
	 * <b>Method:	SetWidths()</b>
	 * @method		Permite definir el ancho de las columnas de una tabla
	 * @param		Array $w arreglo que posee el ancho de cada una de las columnas de la tabla
	 * @author		Oliver - Scripts FPDF (www.fpdf.com)
	 * @version		v1.0 07/03/12 01:44 PM
	 * */
	function SetWidths($w) {
		//Set the array of column widths
		$this->widths = $w;
	}

	/**
	 * <b>Method:	SetWidths()</b>
	 * @method		Permite definir la alineacion de las columnas de una tabla
	 * @param		Array $a arreglo que posee la alineacion de cada una de las columnas de la tabla
	 * @author		Oliver - Scripts FPDF (www.fpdf.com)
	 * @version		v1.0 07/03/12 01:45 PM
	 * */
	function SetAligns($a) {
		//Set the array of column alignments
		$this->aligns = $a;
	}

	/**
	 * <b>Method:	Row()</b>
	 * @method		Permite definir una fila de la tabla
	 * @param		Array $data arreglo que posee la informacion de las columnas de la tabla
	 * 				String $fill indica si la fila debe poseer color de fondo
	 * 				Array $table_header variable que se utiliza para generar la cabecera de la tabla cada vez que
	 * 				se ejecute un salto de pagina
	 * @author		Oliver - Scripts FPDF (www.fpdf.com)
	 * 				Reynaldo Rojas (Implementacion de variables: $fill, $table_header)
	 * @version		v1.1 07/03/12 01:49 PM
	 * */
	function Row($data, $fill = false, $table_header = false) {
		//Calculate the height of the row
		$nb = 0;
		for ($i = 0; $i < count($data); $i++)
			$nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
		$h = 5 * $nb;

		//Issue a page break first if needed
		$this->CheckPagesBreak($h, $table_header);
		//Draw the cells of the row
		for ($i = 0; $i < count($data); $i++) {
			$w = $this->widths[$i];
			$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			//Draw the border
			$this->Rect($x, $y, $w, $h, $fill, false);
			//Print the text
			$this->MultiCell($w, 5, $data[$i], 0, $a);
			//Put the position to the right of the cell
			$this->SetXY($x + $w, $y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	/**
	 * <b>Method:	CheckPagesBreak()</b>
	 * @method		Permite verificar si se debe o no generar un salto de pagina dependiendo del ancho 
	 * 				de la fila a generar
	 * @param		Numeric $h Alto de la fila a generar
	 * 				Array $table_header variable que se utiliza para generar la cabecera de la tabla cada vez que
	 * 				se ejecute un salto de pagina
	 * @author		Oliver - Scripts FPDF (www.fpdf.com)
	 * 				Reynaldo Rojas (Implementacion de variable: $table_header)
	 * @version		v1.1 07/03/12 01:56 PM
	 * */
	function CheckPagesBreak($h, $table_header = false) {
		//If the height h would cause an overflow, add a new page immediately
		if ($this->GetY() + $h > $this->PageBreakTrigger) {
			$this->AddPage($this->CurOrientation);

			// Salto de linea
			$this->Ln(3);

			// Generar la cabecera de la tabla al momento agregar una nueva pagina
			if (!empty($table_header))
				$this->Row($table_header, $this->fill);
		}
	}

	/**
	 * <b>Method:	NbLines()</b>
	 * @method		Determina el alto de una columna dependiendo de la longitud de su contenido y el ancho establecido 
	 * 				para la columna
	 * @param		Numeric $w Ancho de la fila a generar
	 * 				String $txt Contenido de la columna
	 * @author		Oliver - Scripts FPDF (www.fpdf.com)
	 * 				Jesus Farias (Traduccion en codigo ASCII de la variable $c con el metodo ord())
	 * @version		v1.0 07/03/12 02:00 PM
	 * */
	function NbLines($w, $txt) {
		//Computes the number of lines a MultiCell of width w will take
		$cw = &$this->CurrentFont['cw'];
		if ($w == 0)
			$w = $this->w - $this->rMargin - $this->x;
		$wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;

		$s = str_replace("\r", '', $txt);
		$nb = strlen($s);
		if ($nb > 0 and $s[$nb - 1] == "\n")
			$nb--;
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while ($i < $nb) {
			$c = $s[$i];

			if ($c == "\n") {
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				continue;
			}
			if ($c == ' ')
				$sep = $i;

			// El caracter es traducido a su equivalente en codigo ASCII
			$l+=$cw[(ord($c))];

			if ($l > $wmax) {
				if ($sep == -1) {
					if ($i == $j)
						$i++;
				}
				else
					$i=$sep + 1;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}

	/**
	 * <b>Method: getImageProportion($image_path, $max_width, $max_height)</b>
	 * @method Calcula la proporcion de una imagen segun los limites establecidos en los parametros $max_width y $max_height
	 * @param String $image_path ruta de la imagen
	 * @param Integer $max_width Ancho maximo
	 * @param Integer $max_height Alto maximo
	 * @return Array $arr_proportion Arreglo con los parametros $arr_proportion[0] => width y $arr_proportion[1] => height 
	 * @author Maycol Alvarez, Reynaldo Rojas
	 * @version 1.0 14/03/12 10:33 AM
	 * */
	public function getImageProportion($image_path, $max_width, $max_height) {

		$arr_image = getimagesize($image_path);

		$arr_proportion = array($max_width, $max_height);

		if ($arr_image[0] > $arr_image[1]) {
			$height = (($arr_image[1] * $max_height) / $arr_image[0]);
			$arr_proportion[1] = intval($height);
		} elseif ($arr_image[0] < $arr_image[1]) {
			$width = (($arr_image[0] * $max_width) / $arr_image[1]);
			$arr_proportion[0] = intval($width);
		}
		return $arr_proportion;
	}

}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */
?>