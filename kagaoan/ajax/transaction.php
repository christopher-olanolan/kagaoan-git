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
		// AJAX: ADD LOCATION
		case  'add-location':
			$query = $connect->single_result_array("SELECT id FROM location WHERE LOWER(location) = LOWER('{$location}') ");
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;

			if (!$exist):
				$data = array(
					'location' => $location,
					'active' => 1
				);
	
				$connect->insert($data, location);	
			endif;
			
			$json = $connect->get_array_result("
				SELECT
					id, location
				FROM 
					location
				WHERE 
					 active = 1
				ORDER BY
					location ASC 
			");
					 
			exit(json_encode($json));
		break;
		
		// AJAX: ADD DEDUCTION TYPE
		case  'add-deduction-type':
			$query = $connect->single_result_array("SELECT id FROM deduction_type WHERE LOWER(type_name) = LOWER('{$type_name}') ");
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;

			if (!$exist):
				$data = array(
					'type_name' => $type_name,
					'active' => 1
				);
	
				$connect->insert($data, deduction_type);	
			endif;
			
			$json = $connect->get_array_result("
				SELECT
					id, type_name
				FROM 
					deduction_type
				WHERE 
					 active = 1
				ORDER BY
					type_name ASC 
			");
					 
			exit(json_encode($json));
		break;
		
		// AJAX: GET ASSIGNED DRIVER 
		case 'get-assigned-driver':
			$truck_id = empty($truck_id) || $truck_id == "" ? "":" AND t1.truck_id = {$truck_id} ";
			$json = $connect->get_array_result("
				SELECT
					DISTINCT(t2.id),
					t2.id AS driver_id,
					t2.firstname,
					t2.lastname
				FROM 
					truck_driver AS t1
					LEFT JOIN personnel AS t2 ON t1.driver_id = t2.id
				WHERE 
					 t1.active = 1
					 $truck_id
				GROUP BY
					t2.id
				ORDER BY
					t2.lastname ASC 
			");
			exit(json_encode($json));
		break;
		
		// AJAX: GET ALL DRIVER 
		case 'get-all-driver':
			$json = $connect->get_array_result("
				SELECT
					id AS driver_id,
					firstname,
					lastname
				FROM 
					personnel
				WHERE 
					 active = 1
					 AND type = 2
				ORDER BY
					lastname ASC 
			");
			exit(json_encode($json));
		break;
		
		case 'manage': // MANAGE TRANSACTION
		case 'add-transaction': // ADD TRANSACTION
		case 'edit-transaction': // EDIT TRANSACTION
			include (str_replace('//','/',dirname(__FILE__).'/') . 'transaction/'.$control.'.php');
		break;		
		
		case 'deduction': // MANAGE DEDUCTION
		case 'add-deduction': // ADD DEDUCTION
		case 'edit-deduction': // EDIT DEDUCTION
			include (str_replace('//','/',dirname(__FILE__).'/') . 'transaction/'.$control.'.php');	
		break;

		case 'report':
			include (str_replace('//','/',dirname(__FILE__).'/') . 'transaction/'.$control.'.php');
		break;
		
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>