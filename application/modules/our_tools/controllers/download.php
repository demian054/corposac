<?php
/**
 * Provee funcionabilida de descarga de archivos forzando que los mismo sean descargados por el navegador.
 *
 * @author Jose A. Rodriguez E. <josearodrigueze@gmail.com>
 */
class Download extends MY_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * <b>Method:   FD_csv($param)</b>
     * Porporciona los header para forzar la descarga de archivos csv a partir de una ruta conocida.
     * @param       Array    $param   Arreglo que en la posicion '0' ruta del archivo csv a ser descargado.
     * @author      Jose Rodriguez <josearodrigueze@gmail.com>
     * @version     1.0 03/10/2012 15:50
     **/
    function FD_csv($param) {
        $path = $this->config->item('temp_dir_log');
        $download = $param[0];

        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $download . '"');

        die(file_get_contents($path . $download));
    }
}

/* END Class download      */
/* END of file download.php */
/* Location: ./application/modules/our_tools/controllers/download.php */