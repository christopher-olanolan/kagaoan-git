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
		// AJAX: ADD BARAND
		case  'add-brand':
			$query = $connect->single_result_array("SELECT id FROM brand WHERE LOWER(brand_name) = LOWER('{$brand_name}') ");
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;

			if (!$exist):
				$data = array(
					'brand_name' => $brand_name,
					'active' => 1
				);
	
				$connect->insert($data, brand);	
			endif;
			
			$json = $connect->get_array_result("
				SELECT
					id, brand_name
				FROM 
					brand
				WHERE 
					 active = 1
				ORDER BY
					brand_name ASC 
			");
					 
			exit(json_encode($json));
		break;
		
		// AJAX: GET PRODUCT 
		case 'get-stock-id':
			$json = $connect->single_result_array("
				SELECT
					t1.description,
					t1.unit_price AS price,
					t1.unit,
					t1.stocks AS stock,
					t2.brand_name AS brand
				FROM 
					inventory AS t1
					LEFT JOIN brand AS t2 ON t1.brand = t2.id
				WHERE 
					t1.id = $stock_id
			");
			exit(json_encode($json));
		break;
		
		case 'get-unique':
			$edit = $id == "0" ? "":" AND id != $id";
			$string = " SELECT id FROM inventory WHERE 
				name = '{$name}' 
				AND brand = '{$brand}' 
				AND unit_price = '{$unit_price}' 
				AND active = '1' ".$edit ;
			
			$query = $connect->single_result_array($string);
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		
		case 'get-unique-material':
			$edit = $id == "0" ? "":" AND id != $id";
			$query = $connect->single_result_array("SELECT id FROM materials WHERE material = '$material' ". $edit);
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		
		case 'manage': // MANAGE INVETORY
		case 'add-inventory': // ADD INVENTORY
		case 'edit-inventory': // EDIT INVENTORY
			include (str_replace('//','/',dirname(__FILE__).'/') . 'inventory/'.$control.'.php');
		break;
		
		case 'stock-out': // STOCK OUT
		case 'add-stock-out': // ADD STOCK OUT
		case 'edit-stock-out': // ADD STOCK OUT
			include (str_replace('//','/',dirname(__FILE__).'/') . 'inventory/'.$control.'.php');	
		break;
		
		case 'materials': // MANAGE MATERIALS
		case 'add-materials': // ADD MATERIALS
		case 'edit-materials': // EDIT MATERIALS
			include (str_replace('//','/',dirname(__FILE__).'/') . 'inventory/'.$control.'.php');
		break;
			
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>