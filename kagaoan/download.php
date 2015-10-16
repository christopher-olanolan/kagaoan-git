<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
	$connect = new MySQL();
	$connect->connect(
		$config['DB'][__SITE__]['USERNAME'],
		$config['DB'][__SITE__]['PASSWORD'],
		$config['DB'][__SITE__]['DATABASE'],
		$config['DB'][__SITE__]['HOST']
	);
	
	switch ($download):
		case 'collection-statement': // COLLECTION STATEMENT
		case 'transaction': // TRANSACTION
		case 'deduction': // DEDUCTION
		case 'stock-out': // STOCK OUT
		case 'inventory': // INVENTORY
		case 'truck': // TRUCK
		case 'driver': // DRIVER
		case 'consumption': // CONSUMPTION
		case 'personnel': // PERSONNEL
		case 'payment': // PAYMENT
			include (str_replace('//','/',dirname(__FILE__).'/') . 'download/'.$download.'.php');
		break;
		
		default:
			include dirname(__FILE__) . "/error.php";
			exit(); 
		break;
	endswitch;
endif;
?>