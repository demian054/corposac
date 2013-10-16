<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Upload extends MY_Controller {

	private $module;

	function __construct() {
		parent::__construct();
		$this->load->model('upload_model');
		$this->module = 'adm_upload';
                $this->load->helper('file');
	}

//	public function _remap($method, $params = array()) {
//		$this->tank_auth->has_permissions($this->module, get_class(), $method, $params);
//	}

	/*
	  <b>Method:	winUpload()</b>
	 * Levanta la ventana de upload, espera dos parametros opId, y fieldId
	 * @param		
	 * @return		return
	 * @author		Juan Carlos Lopez
	 * @version		v1.0 03/11/11 10:45 AM
	 * */

	function winUpload() {
            
//            $salida = $this->upload_model->clearUnliked();
//            print_r($salida);
		$opId           = $this->input->get('opId');
		$fieldId        = $this->input->get('fieldId');
		$fileType       = $this->input->get('fileType');
		$gallerie_id    = $this->input->get('gallerie_id');
		$dom_gallerie_id    = $this->input->get('dom_gallerie_id');
		
		$fileType       = $this->upload_model->getOperationType($opId);

		$dataUpload     = array(
                                        'title'     => 'Manejador de Archivos',
                                        'name'      => 'wUpload',
                                        'opId'      => $opId,
                                        'fieldId'   => $fieldId,
                                        'fileType'  => $fileType,
                                        'gallerie_id'  => $gallerie_id,
                                        'dom_gallerie_id'  => $dom_gallerie_id
                                );
		$this->dyna_views->buildUploadWindow($dataUpload);
	}

	function do_upload($params) {
		if (!empty($params) && $params[0] == 'process') {
                    
			$result = FALSE;
                        //Genera una galeria y se recupera su id en caso de no existir la galeria
                        //ademas devuelve el path donde se alojaran las imagenes
                        $created_by = $this->session->userdata('user_id');
                        $file_field_id = $this->input->post('file_field_id');
                        $gallerie_id = $this->input->post('gallerie_id');
                        $title = $this->input->post('title');
                        $opId = $this->input->post('opId');
                                                
                        if(empty($gallerie_id))
                            $gallerie_id = $this->upload_model->createGallerie($file_field_id, $created_by);
                        
                        //Preparamos la direccion donde se guardaran los archivos, 
                        //determinamos cuantos archivos hay en la carpeta $file_field_id
                        //verificando que no se tengan mas de $max_file_folder de los que se puedan contener
                        //si se pasa hay que crear otra carpeta con la añaduria de -n, 
                        //para esta operacion usamos la function makeNewFolder
                        //que se ocupa de crear el nuevo nombre de carpeta y verificar si se pasa de $max_file_folder
                        
                        $max_file_folder = '2';
                        $dataFile = get_dir_file_info(  
                                                        'files/uploads/'.$created_by.'/'.$file_field_id.'/', 
                                                        $top_level_only = TRUE
                                                        );
                        if(count($dataFile) > $max_file_folder ){
                            $file_field_id_address = $this->makeNewFolder($created_by, $file_field_id, 0, $max_file_folder);
                        } else {
                            $file_field_id_address = $file_field_id;
                        }
                        
                        $path = 'files/uploads/'.$created_by.'/'.$file_field_id_address.'/'.$gallerie_id;
                        
                        if (!file_exists($path)) {
                                if (!mkdir($path, 0755, TRUE)) {
                                        $result = TRUE;
                                        $msg = $this->lang->line('file_load_error');
                                        $throwResVars = array(
                                            "title"     => "Administración de Archivos", 
                                            "result"    => $result, 
                                            "msg"       => $msg, 
                                            "success"   => TRUE
                                            );
                                        throwResponse($throwResVars);
                                        die;
                                } 
                        }
                        
                        $config['upload_path'] = $path;
                        $config['allowed_types'] = 'jpg|jpeg|png|gif|doc|txt|xls|ods|pps|ppt|odt|odp|pdf|rtf|docx|xlsx|pptx|csv';
                        $config['max_size'] = 1024 * 10;
                        $config['file_name'] = $nombre_archivo;
                        $config['remove_spaces'] = TRUE;

                        $this->load->library('upload', $config);
                        if (!$this->upload->do_upload()) {
                            $tmp_gallerie_id = $this->input->post('gallerie_id');
                                if(empty($tmp_gallerie_id))
                                    $this->upload_model->destroyGallerie($gallerie_id);
                                
                                $error = $this->upload->display_errors();
                                $error = preg_replace("[\n|\r|\n\r|<p>]", " ", $error);
                                $error = preg_replace("[</p>]", "<br>", $error);
                                $upload_data = $this->upload->data();
                                $msg = '';
                                $msg .= $error;
                                $msg .= 'Ha ocurrido algun error al subir su archivo al servidor, verifique que ha podido pasar!<br><br>';

                                foreach ($upload_data as $item => $value) {
                                        $msg .= $item . ": " . $value . "<br>";
                                }
                                $result = TRUE;
                        } else {

                                $upload_data = $this->upload->data();
                                $file_name = $upload_data['file_name'];
                                $file_type = $upload_data['file_type'];
                                $file_ext = $upload_data['file_ext'];
                                $file_size = $upload_data['file_size'];
                                $is_image = $upload_data['is_image'];
                                $image_width = $upload_data['image_width'];
                                $image_height = $upload_data['image_height'];

                                if ($is_image == 1)
                                        $this->create_img($path, $image_width, $image_height, $file_name);

                                if ($file_ext == ".flv") {
                                        $video = $path . "/" . $file_name;
                                        $out = $this->create_video_thum($video, $path);
                                        chmod($out . "1.jpg", 0777);
                                }

                                //Generar el arreglo para el insert en la tabla archivos
                                $dataFileInsert = array(
                                                            'gallerie_id'   => $gallerie_id,
                                                            'title'         => $title,
                                                            'file_type'     => $file_ext,
                                                            'file_name'     => $file_name,
                                                            'file_path'     => $path,
                                                            'file_size'     => $file_size,
                                                            'deleted'       => '0',
                                                            'created_by'    => $created_by
                                                            );
                                if($this->upload_model->insertFile($dataFileInsert)){
                                    $result = TRUE;
                                    $msg = $this->lang->line('file_upload_success');
                                    $extra_vars['gallerie_id'] = $gallerie_id;
                                } else {
                                    $result = TRUE;
                                    $msg = $this->lang->line('message_operation_error');
                                }
                                
			}
                        $throwResVars = array(
                                            "title"     => "Subida de Archivo", 
                                            "result"    => $result, 
                                            "msg"       => $msg, 
                                            "success"   => TRUE,
                                            "extra_vars" => $extra_vars
                                            );
                        throwResponse($throwResVars);
		}
	}
        
        
        private function makeNewFolder($created_by, $file_field_id, $n, $max_file_folder){
            $n = $n+1;
            $file_field_id_add = $file_field_id.'-'.$n;
            $dataFile = get_dir_file_info(  
                                'files/uploads/'.$created_by.'/'.$file_field_id_add.'/', 
                                $top_level_only = TRUE
                                );
            $canti = count($dataFile);

            if($canti > $max_file_folder){
                $se = explode('-', $file_field_id_add);
                return $this->makeNewFolder($created_by, $se[0], $se[1], $max_file_folder);
            } else {
                return $file_field_id_add;
            }
        }

	private function create_video_thum($video, $direccion_upload) {
		$in = $video;
		$pedazos = explode("/", $in);
		$cant = count($pedazos);
		$arch_ext = $pedazos[$cant - 1];
		$nom_jpg_explo = explode(".", $arch_ext);
		$nom_jpg = $nom_jpg_explo[0];
		$out = $direccion_upload . "/" . $nom_jpg;
		$time = "00:00:10"; //momento desde donde tomar los cuadros ejm. 00:00:01
		$frames = "1"; //cantidad de cuadros q queremos sacar del video
		$tamanio = "130x98"; // tamanio del video ejm. 320x240
		$ffmpegPath = "/usr/bin/ffmpeg";
		$flvtool2Path = "/usr/bin/flvtool2";
		//sacar cuadros de un video de cualquier extencion
		$shell = $ffmpegPath;
		$shell .= " -i " . $in;
		if (empty($time)) {
			$shell .= " -an -ss 00:00:01";
		} else {
			$shell .= " -an -ss " . $time;
		}
		if (empty($frames)) {
			$shell .= " -an -r 1 -vframes 1 -y ";
		} else {
			$shell .= " -an -r 1 -vframes " . $frames . " -y ";
		}
		if (empty($tamanio)) {
			
		} else {
			$shell .= "-s " . $tamanio;
		}
		$shell .= " " . $out . "%d.jpg"; //varios cuadros
		shell_exec($shell);
		return $out;
	}

	private function create_img($path, $image_width, $image_height, $file_name) {
		if ($image_width > $image_height) {
			$width = "130";
			$hight = floor($width / 1.3333);
		} else {
			$hight = "120";
			$width = floor($hight / 1.3333);
		}

		if ($image_width > $image_height) {
			$width_med = "800";
			$hight_med = floor($width_med / 1.3333);
		} else {
			$hight_med = "800";
			$width_med = floor($hight_med / 1.3333);
		}

		$config1['image_library'] = 'GD2';
		$config1['source_image'] = $path . '/' . $file_name;
		$config1['new_image'] = $path . '/' . 'med_' . $file_name;
		$config1['create_thumb'] = FALSE;
		$config1['maintain_ratio'] = FALSE;
		$config1['width'] = $width_med;
		$config1['height'] = $hight_med;
		$config1['quality'] = "90%";
		$this->load->library('image_lib', $config1);
		$this->image_lib->resize();
		$this->image_lib->clear();

		$config['image_library'] = 'GD2';
		$config['source_image'] = $path . '/' . $file_name;
		$config['new_image'] = $path . '/' . 'peq_' . $file_name;
		$config['create_thumb'] = FALSE;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $width;
		$config['height'] = $hight;
		$config['quality'] = "90%";
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		$this->image_lib->clear();
	}

	private function makeFolder() {
		$dir_upload = "./uploads/" . $nom_tabla . "/";
		if (!file_exists($dir_upload)) {
			mkdir($dir_upload, 0755);
			chmod($dir_upload, 0755);
		}
	}

	function listAll() {

            $start = isset($_GET['start']) ? $_GET['start'] : 0;
            $limit = isset($_GET['limit']) ? $_GET['limit'] : $this->config->item('thumb_limit');

            $gallerie_id = $this->input->get('gallerie_id');
            if(!empty($gallerie_id)){
                $salida = $this->upload_model->listAll($limit, $start, $gallerie_id);
                echo json_encode($salida);
            }
	}

	function descargarArchivo() {
		$this->load->helper('download');
		$data = file_get_contents($this->inptu->post('vinc'));
		$name = $this->inptu->post('name');
		force_download($name, $data);
	}

	/*
	 * <b>Method:	deleteFile()</b>
	 * Elimina Archivo, de formas binaria
	 * @author		Juan Carlos Lopez, Eliel Parra
	 * @version		v2.0 04/11/11 01:08 PM
	 * */

	function deleteFile() {

		if ($this->upload_model->deleteFile($this->input->post('id'))) {
			$salida = array(
				'success' => TRUE,
                'gallerie_id' => $this->input->post('gallerie_id'),
				'msg' => $this->lang->line('file_upload_delete_success'),
				'title' => 'Acci&oacute;n sobre Archivos'
			);
		} else {
			$salida = array(
				'success' => TRUE,
				'msg' => $this->lang->line('message_operation_error'),
				'title' => 'Acci&oacute;n sobre Archivos'
			);
		}
		echo json_encode($salida);
	}

	/*
	  <b>Method:	fileEdit()</b>
	 * Muestra detalles de el archivo y permite su edicion
	 * @param		$params
	 * @return		return
	 * @author		Juan Carlos Lopez
	 * @version		v1.0 01/11/11 12:01 PM
	 * */

	function fileEdit($params) {
		$id = $this->input->post('id');
                $opId = $this->dyna_views->operationData->operation_id;
		if (!empty($params) && $params[0] == 'process') {
			$dataProcess = $this->dyna_views->processForm();                        
			if ($this->upload_model->updateFile($id, $dataProcess['data'])) {
				$result = TRUE;
				$msg = $this->lang->line('file_upload_update_success');
			} else {
				$result = FALSE;
				$msg = $this->lang->line('message_operation_error');
			}
                        $windowToClose = "w_".$opId;
                        $extra_vars = array('newView' => "$windowToClose.close();");
                        $throwResVars = array(
                                            "title"     => "Edición de Detalles de Archivo", 
                                            "result"    => $result, 
                                            "msg"       => $msg, 
                                            "success"   => TRUE,
                                            "extra_vars" => $extra_vars
                                            );
                        throwResponse($throwResVars);
		} else {
			$data = $this->upload_model->fileDetail($id);
                                                
			$titulo = $data['title'];
			$nombre = $data['file_name'];
			$extension = $data['file_type'];
			$ruta = $data['file_path'];
			$observaciones = $data['description'];
			switch ($extension) {
				case '.jpg':
				case '.png':
				case '.jpeg':
				case '.gif':
					$direccion = base_url() . $ruta . '/peq_' . $nombre;
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.doc':
				case '.docx':
				case '.odt':
				case '.rtf':
					$direccion = base_url() . 'assets/img/icon_file/MS-Office-2003-Word-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.ods':
				case '.xls':
				case '.xlsx':
				case '.csv':
					$direccion = base_url() . 'assets/img/icon_file/MS-Office-2003-Excel-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.pdf':
					$direccion = base_url() . 'assets/img/icon_file/PDF-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.txt':
					$direccion = base_url() . 'assets/img/icon_file/Document-Copy-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.pps':
				case '.ppt':
				case '.odp':
				case '.pptx':
					$direccion = base_url() . 'assets/img/icon_file/MS-Office-2003-PowerPoint-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
			}

			$panelData['panelType'] = '2B';

			$datFile = "";
			$datFile .= '<div style="padding:5px; border:1px solid grey; margin-right:8px; background-color:#FFFFFF;"><div style=" margin-bottom:3px;"><a href="' . $direccion_download . '" target="_blank"><img width="100" height="100" src="' . $direccion . '" alt="' . $titulo . '" title="' . $titulo . '"></a></div><div align="center" class="bot_holder"><a href="' . $direccion_download . '" target="_blank" class="download_bot round_corner"><img src="' . base_url() . 'assets/img/icons/arrow_down.png" align="center" alt="Descargar"> Descargar</a></div></div>';

                       
                        $params1 = array(
                                    'title' => 'Archivo',
                                    'name' => 'archivo',
                                    'data' => $datFile,
                                    'replace' => '',
                                    'scriptTags' => false,
                                    'returnView' => true,
                                    'extraOptions' => $extraOptions
                                    );                        
			$elHtml = $this->dyna_views->buildPanelHtml($params1);
			$panelData['p1'] = $elHtml;
			$panelData['type1'] = 'PanelHtml_';
                        
                        $params2 = array(
                                    'title' => 'Editar de Archivo',
                                    'name' => 'editDetail',
                                    'data' => $data,
                                    'replace' => '',
                                    'returnView' => true,
                                    );                        
			$elForm = $this->dyna_views->buildForm($params2);
                        
			$panelData['p2'] = $elForm;
			$panelData['type2'] = 'form_';
                        
                        $params3 = array(
                                    'title' => '',
                                    'name' => '',
                                    'data' => $panelData,
                                    'replace' => 'window',
                                    'scriptTags' => false,
                                    'returnView' => false
                                    );
			$this->dyna_views->buildPanel($params3);
		}
	}

	/*
	  <b>Method:	fileDetail()</b>
	 * Permite la visulaizacion de los datos del archivo
	 * @param		
	 * @return		return
	 * @author		Juan Carlos Lopez
	 * @version		v1.0 02/11/11 03:26 PM
	 * */

	function fileDetail() {
		$id = $this->input->post('id');
		$data = $this->upload_model->fileDetail($id);
                                                
			$titulo = $data['title'];
			$nombre = $data['file_name'];
			$extension = $data['file_type'];
			$ruta = $data['file_path'];
			$observaciones = $data['description'];
			switch ($extension) {
				case '.jpg':
				case '.png':
				case '.jpeg':
				case '.gif':
					$direccion = base_url() . $ruta . '/peq_' . $nombre;
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.doc':
				case '.docx':
				case '.odt':
				case '.rtf':
					$direccion = base_url() . 'assets/img/icon_file/MS-Office-2003-Word-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.ods':
				case '.xls':
				case '.xlsx':
				case '.csv':
					$direccion = base_url() . 'assets/img/icon_file/MS-Office-2003-Excel-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.pdf':
					$direccion = base_url() . 'assets/img/icon_file/PDF-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.txt':
					$direccion = base_url() . 'assets/img/icon_file/Document-Copy-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
				case '.pps':
				case '.ppt':
				case '.odp':
				case '.pptx':
					$direccion = base_url() . 'assets/img/icon_file/MS-Office-2003-PowerPoint-icon.png';
					$direccion_download = base_url() . $ruta . '/' . $nombre;
					break;
			}

			$panelData['panelType'] = '2B';
                        $extraOptions['CancelButton'] = '0';

			$datFile = "";
			$datFile .= '<div style="padding:5px; border:1px solid grey; margin-right:8px; background-color:#FFFFFF;"><div style=" margin-bottom:3px;"><a href="' . $direccion_download . '" target="_blank"><img width="100" height="100" src="' . $direccion . '" alt="' . $titulo . '" title="' . $titulo . '"></a></div><div align="center" class="bot_holder"><a href="' . $direccion_download . '" target="_blank" class="download_bot round_corner"><img src="' . base_url() . 'assets/img/icons/arrow_down.png" align="center" alt="Descargar"> Descargar</a></div></div>';

                       
                        $params1 = array(
                                    'title' => 'Archivo',
                                    'name' => 'archivo',
                                    'data' => $datFile,
                                    'replace' => '',
                                    'scriptTags' => false,
                                    'returnView' => true
                                    );                        
			$elHtml = $this->dyna_views->buildPanelHtml($params1);
			$panelData['p1'] = $elHtml;
			$panelData['type1'] = 'PanelHtml_';
                        
                        $params2 = array(
                                    'title' => 'Editar de Archivo',
                                    'name' => 'editDetail',
                                    'data' => $data,
                                    'replace' => '',
                                    'returnView' => true,
                                    'extraOptions' => $extraOptions
                                    );                        
			$elForm = $this->dyna_views->buildForm($params2);
                        
			$panelData['p2'] = $elForm;
			$panelData['type2'] = 'form_';
                        
                        $params3 = array(
                                    'title' => '',
                                    'name' => '',
                                    'data' => $panelData,
                                    'replace' => 'window',
                                    'scriptTags' => false,
                                    'returnView' => false
                                    );
			$this->dyna_views->buildPanel($params3);
	}
        
        	/*
	  <b>Method:	clearUnliked()</b>
	 * Permite limpiar todas las galerias e imagenes 
                                * asociadas que no esten vinculadas a un campo fileupload
	 * @param		
	 * @return		return TRUE/FALSE
	 * @author		Juan Carlos Lopez
	 * @version		v1.0 03/11/11 10:45 AM
	 * */

	function clearUnliked() {
//            $salida = $this->upload_model->clearUnliked();
        }
        

}

?>
