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
		// AJAX: GET MONTH WEEKS
		case 'get-month-weeks':
			$first_day = date("N",strtotime(date($filter_year.'-'.$filter_month.'-01')));
			$month_days = date("t",strtotime(date($filter_year.'-'.$filter_month.'-01')));
			
			echo floor(($first_day + $month_days-1)/7) + 1;
		break;
		// EOF AJAX: GET MONTH WEEKS
		
		// AJAX: GET MONTH DAYS
		case 'get-month-days':
			echo date("t",strtotime(date($filter_year.'-'.$filter_month.'-01')));
		break;
		// EOF AJAX: GET MONTH DAYS
		
		// AJAX: CHECK DRIVER
		case 'check-driver':		
			$query = $connect->single_result_array("SELECT id FROM truck_driver WHERE truck_id = '{$truck_id}' AND driver_id = '{$driver_id}' AND assigned = '{$assigned}'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		// EOF AJAX: CHECK DRIVER
		
		// AJAX: CHECK DATE
		case 'check-date':
			$result = !strtotime($assigned) ? "false":"true";
			echo $result;
		break;
		// EOF AJAX: CHECK DATE
		
		// AJAX: CHECK PLATE
		case 'plate':
			$query = $connect->single_result_array("SELECT id FROM truck WHERE plate = '$plate'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		// EOF AJAX: CHECK PLATE
		
		// AJAX: CHECK PLATE BY ID
		case 'plate-id':
			$query = $connect->single_result_array("SELECT id FROM truck WHERE plate = '$plate' AND id != '$id'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		
		case 'manage': // MANAGE TRUCK	
		case 'add-truck': // ADD TRUCK	
		case 'edit-truck': // EDIT TRUCK
			include (str_replace('//','/',dirname(__FILE__).'/') . 'truck/'.$control.'.php');
		break;
		// EOF TRUCK
		
		case 'driver': // MANAGE DRIVER
		case 'add-driver': // ADD DRIVER
		case 'edit-driver': // EDIT DRIVER
			include (str_replace('//','/',dirname(__FILE__).'/') . 'truck/'.$control.'.php');	
		break;
		// EOF DRIVER
		
		// MANAGE CONSUMPTION
		case 'consumption':
		case 'add-consumption': // ADD CONSUMPTION		
		case 'edit-consumption': // EDIT CONSUMPTION
			include (str_replace('//','/',dirname(__FILE__).'/') . 'truck/'.$control.'.php');
		break;
		// EOF CONSUMPTION

		case 'report':
			?>Report<?
		break;
		
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>