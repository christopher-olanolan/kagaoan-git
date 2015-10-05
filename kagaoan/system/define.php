<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

extract($_COOKIE);
extract($_ENV);
extract($_FILES);
extract($_GET);
extract($_POST);
extract($_REQUEST);
extract($_SESSION);
extract($_SERVER);

$_HOST = explode("/",$SERVER_NAME.$SCRIPT_NAME);

if (!isset($_SESSION['SITE'])):
	$_SET_SITE = !isset($_GET['usersite']) || empty($_GET['usersite']) || $_GET['usersite'] == "" ? 'kagaoan':$_GET['usersite'];
else:
	$_SET_SITE = !isset($_SESSION['SITE']) || empty($_SESSION['SITE']) || $_SESSION['SITE'] == "" ? 'kagaoan':$_SESSION['SITE'];
endif;

$sizeof_HOST = sizeof($_HOST)-1;
$sizeof_CONTROLPANEL = sizeof($_HOST)-2;
$sizeof_PANEL = sizeof($_HOST)-3;

$_SITE = $_SET_SITE;
$_PANEL =  $_HOST[$sizeof_PANEL];
$_CONTROLPANEL =  $_HOST[$sizeof_CONTROLPANEL];
$_PROTOCOL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" : "http://"; 
$_PORT = empty($_SERVER['SERVER_PORT']) ? "" : ":".$SERVER_PORT;
$_ROOT = str_replace("/".$_HOST[$sizeof_HOST], "", $_PROTOCOL.$SERVER_NAME.$_PORT.$SCRIPT_NAME);

// NAMES
define("__PANEL__", $_PANEL);
define("__SITE__", strtoupper($_SITE));
define("__CONTROLPANEL__", $_CONTROLPANEL == 'kagaoan' ?'M.P. Kagaoan Enterprise, Inc.': $_CONTROLPANEL);
define("__TITLE__", __CONTROLPANEL__);
define("__EMAIL__", 'drexmod@gmail.com');
define("__BCC__", 'drexmod@gmail.com');

// URI
define("__ROOT__", $_ROOT);
define("__HOME__", __ROOT__ . '/index.php?file=main');
define("__LOGIN__", __ROOT__ . '/index.php?file=login');
define("__ERROR__", __ROOT__ . '/index.php?file=error');
define("__FORGOT__", __ROOT__ . '/index.php?file=forgot');

// FILES/FOLDERS
define("__SCRIPT__", __ROOT__ . "/scripts/");
define("__STYLE__", __ROOT__ . "/styles/");
define("__IMAGE__", __ROOT__ . "/images/");
define("__UPLOADS__", __ROOT__ . "/uploads/");
define("__CSV__", __ROOT__ . "/downloads/csv/");

// LOGIN
define("__IDLETIME__", '3600'); // 1 hour
define("__ACCESS__", false);

// QUERY DEFAULT
define("__LIMIT__", '10');
?>
