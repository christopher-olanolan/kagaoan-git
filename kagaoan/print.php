<?php
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

// printr($_SESSION);

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
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="https://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?=__TITLE__?> - <?=ucfirst($print)?></title>
		<? include dirname(__FILE__) . "/scripts.php"; ?>
	</head>
	<body id="printer">
		<div style="width:100%;" align="center">
			<div style="margin-left:10px; margin-right:10px;">
			<?
			if(!empty($print) || $print != ''):
				if(file_exists(str_replace('//','/',dirname(__FILE__).'/') . 'print/'.$print.'.php')):
					switch($print):
						case 'personnel':
							
						case 'truck':
						case 'driver':
						case 'consumption':
							
						case 'transaction':
						case 'deduction':
						case 'collection':
									
						case 'inventory':
						case 'stock-out':
							include (str_replace('//','/',dirname(__FILE__).'/') . 'print/'.$print.'.php');	
						break;
						default:
							redirect(0,__ROOT__."/index.php?file=process&process=invalid");
							exit();
						break;
					endswitch;
					// end file
				else:
					redirect(0,__ROOT__."/index.php?file=process&process=invalid");
					exit();
				endif;
				// end file_exists
			else:
				redirect(0,__ROOT__."/index.php?file=process&process=invalid");
				exit();
			endif;
			?>
			</div>
		</div>
	</body>
	</html>
	<?
endif;
?>