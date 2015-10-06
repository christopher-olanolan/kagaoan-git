<?
define('__CONTROL__',true);

// SYSTEM INCLUDES
include dirname(__FILE__) . "/system/init.php";
include dirname(__FILE__) . "/system/config.php";
include dirname(__FILE__) . "/system/define.php";
include dirname(__FILE__) . "/system/function.php";
include dirname(__FILE__) . "/system/session.php";
include dirname(__FILE__) . "/system/var.php";
include dirname(__FILE__) . "/system/json.php";
include dirname(__FILE__) . "/ckeditor/ckeditor.php";

// CLASS INCLUDES
include dirname(__FILE__) . "/class/class.mySQL.php";
include dirname(__FILE__) . "/class/class.selectQuery.php";
include dirname(__FILE__) . "/class/class.mailNotification.php";
include dirname(__FILE__) . "/class/class.HTML2Text.php";
include dirname(__FILE__) . "/class/class.SMTP.php";
include dirname(__FILE__) . "/class/class.UnicodeReplace.php";
include dirname(__FILE__) . "/class/class.Inventory.php";

if(!empty($file)):
	if(file_exists($file.'.php')):
		switch($file):
			case 'login':
			case 'forgot':
			case 'panel':
			case 'process':
			case 'action':
			case 'ajax':
			case 'download':
			case 'print':	
				include dirname(__FILE__) .'/'. $file.'.php';
			break;
			default:
				$_GET['file'] = "error";
				$file = $_GET['file'];
				include dirname(__FILE__) .'/'. $file.'.php';
			break;
		endswitch;
		// end file switch
	else:
		$_GET['file'] = "login";
		$file = $_GET['file'];
		include dirname(__FILE__) .'/'. $file.'.php';
	endif;
	// end file_exists
else:
	$_GET['file'] = "login";
	$file = $_GET['file'];
	include dirname(__FILE__) .'/'. $file.'.php';
endif;
?>
