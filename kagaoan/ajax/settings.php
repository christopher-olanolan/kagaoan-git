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
		case 'get-setting':
			$table = str_replace('-', '_', $setting);

			switch ($setting):
				case "personnel-type":
				case "deduction-type":
					$search = " LOWER(type_name) = LOWER('{$name}') ";
				break;	
				case "location":
					$search = " LOWER(location) = LOWER('{$name}') ";
				break;	
				case "brand":
					$search = " LOWER(brand_name) = LOWER('{$name}') ";
				break;	
			endswitch;
			
			$hasid = empty($id) ? "":" AND id != '{$id}'";
			
			$query_string = "SELECT id FROM {$table} WHERE ". $search . $hasid;			
			$query = $connect->single_result_array($query_string);
			
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		
		case 'manage': // MANAGE
		case 'deduction': // DEDUCTION SETTINGS
		case 'edit-deduction':	
			include (str_replace('//','/',dirname(__FILE__).'/') . 'settings/'.$control.'.php');
		break;
		
		case "edit-personnel-type":
		case "edit-location":
		case "edit-deduction-type":
		case "edit-brand":
			include (str_replace('//','/',dirname(__FILE__).'/') . 'settings/edit.php');
		break;
	
		case "add-personnel-type":
		case "add-location":
		case "add-deduction-type":
		case "add-brand":
			include (str_replace('//','/',dirname(__FILE__).'/') . 'settings/add.php');
		break;
		
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>