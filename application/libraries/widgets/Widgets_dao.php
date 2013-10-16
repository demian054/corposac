<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * widgets_model class
 * @package		models/
 * @subpackage	widgets/
 * @author		Jesus Farias <jesus.farias@gmail.com>
 * @copyright	Por definir
 * @license		Por definir
 * @version		1.0 29/02/12 05:42 PM
 * */
//class Widgets_dao extends CI_Model {
class Widgets_dao  {
	public $widgets_colecction;
	protected $CI;
	
	function __construct(&$CI) {
		$this->CI = &$CI;
	}
	
	/**
	 * <b>Method:	getWidgetsByUserInSession()</b>
	 * @method		Gets the widgets that have been associated to the current user on session
	 * @author		Jesus Farias Lacroix
	 * @version     v1.0 23/01/12 04:44 PM
	***/	
	public function getWidgetsByUserInSession() {
		$rol_id  = $this->CI->session->userdata('role_id');
		$user_id = $this->CI->session->userdata('user_id');
		$strQuery=" SELECT 
						wu.widget_id,  wu.user_id,
						wu.rol_id, w.descripcion AS html,
						w.file_name,  w.titulo AS title,  
						wu.finstalacion, wu.fdesinstalacion,
						wu.extra_config, wu.rows,
						wu.width,  wu.height,  
						wu.position_x,  wu.position_y,
						'fit' AS layout, 'widget_'||wu.widget_id AS id
						
					FROM
						widgets.widgets  AS w
						INNER JOIN widgets.widgets_user AS wu ON (wu.widget_id = w.id
						AND wu.user_id=$user_id AND wu.rol_id=$rol_id)
					WHERE
						w.activo='1' AND  w.eliminado='0'
						AND wu.activo='1'AND  wu.eliminado='0'
					ORDER BY wu.position_x,  wu.position_y;	";
		$query = $this->CI->db->query($strQuery);
		return ($query->result());		
	}
	
	public function getWidgetOperation($widgetId, $userId, $rolId) {
		if(empty($widgetId) || empty($userId) || empty($rolId)) return false;				
		$strQuery=" SELECT
						op.id  AS  operation_id,
						op.url AS  operation_url
					FROM
						widgets.widgets w
						INNER JOIN widgets.widgets_user wu  ON (w.id=wu.widget_id)
						INNER JOIN widgets.widgets_roles wr ON (wr.widget_id=wu.widget_id AND wr.rol_id=wu.rol_id)
						INNER JOIN rbac.operations op       ON (op.id=w.operation_id )
						INNER JOIN rbac.operation_roles opr ON (opr.operation_id=op.id AND opr.rol_id=wu.rol_id)
					WHERE
						w.id=$widgetId   AND wu.user_id=$userId AND wu.rol_id=$rolId AND w.eliminado='0'  
						AND w.activo='1' AND wu.eliminado='0'   AND wu.activo='1'    AND op.eliminado='0'";
		$query = $this->CI->db->query($strQuery);
		return ($query->result());		
	}
	
	public function setPosition($widgetId, $newX, $newY){
		$user_id = $this->CI->session->userdata('user_id');
					
		$strQuery=" SELECT wu.position_x, wu.position_y  
					FROM  widgets.widgets_user wu 
					WHERE wu.widget_id=$widgetId  AND wu.user_id=$user_id";
		$result = $this->CI->db->query($strQuery);
		$result=$result->result_array();
		$oldX=$result[0]['position_x'];
		$oldY=$result[0]['position_y'];
		
		$strQuery2=" UPDATE widgets.widgets_user
					 SET position_y=position_y-1 
					 WHERE user_id=$user_id AND position_x = $oldX AND position_y > $oldY  ";
		$result2=$this->CI->db->query($strQuery2);
		
		$strQuery3=" UPDATE widgets.widgets_user
					 SET position_y=position_y+1 
					 WHERE user_id=$user_id AND position_x = $newX AND position_y >= $newY  ";
		$result3=$this->CI->db->query($strQuery3);
		
		$strQuery4=" UPDATE widgets.widgets_user
					 SET position_x = $newX, 
						 position_y = $newY
					 WHERE user_id=$user_id AND widget_id = $widgetId ";
		$result4=$this->CI->db->query($strQuery4);
		
	}
	
	
}
?>
