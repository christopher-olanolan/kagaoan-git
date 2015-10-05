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
		case 'stock':
			?>Stock Availability<?
		break;

		case 'sales':
			?>Sales Report<?
		break;
		
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>