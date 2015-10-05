<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if ($ajax=="" || empty($ajax)):
	include dirname(__FILE__) . "/error.php";
else:
	$connect = new MySQL();
	$connect->connect(
		$config['DB'][__SITE__]['USERNAME'],
		$config['DB'][__SITE__]['PASSWORD'],
		$config['DB'][__SITE__]['DATABASE'],
		$config['DB'][__SITE__]['HOST']
	);
	
	switch($control):
		// AJAX: ADD PERSONNEL TYPE
		case  'add-personnel-type':
			$query = $connect->single_result_array("SELECT id FROM personnel_type WHERE LOWER(type_name) = LOWER('{$type}') ");
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;

			if (!$exist):
				$created = date("Y-m-d H:i:s");
			
				$data = array(
					'type_name' => $type,
					'active' => 1
				);
	
				$connect->insert($data, personnel_type);	
			endif;
			
			$json = $connect->get_array_result("
				SELECT
					id, type_name
				FROM 
					personnel_type
				WHERE 
					 active = 1
				ORDER BY
					type_name ASC 
			");
					 
			exit(json_encode($json));
		break;
		
		// AJAX: CHECK EMPNO
		case 'empno':
			$query = $connect->single_result_array("SELECT id FROM personnel WHERE empno = '$empno'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		// EOF AJAX: CHECK EMPNO
		
		// AJAX: EDIT PERSONNEL ID
		case 'personnel-empno':
			$query = $connect->single_result_array("SELECT id FROM personnel WHERE empno = '$empno' AND id != '$id'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		
		case 'manage': // MANAGE PERSONNEL
		case 'add-personnel': // ADD PERSONNEL
		case 'edit-personnel': // EDIT PERSONNEL
			include (str_replace('//','/',dirname(__FILE__).'/') . 'personnel/'.$control.'.php');
		break;
		
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>