<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

// TIMESTAMP
if (isset($_SESSION[__SITE__.'_TIMESTAMP']) && (time()-$_SESSION[__SITE__.'_TIMESTAMP']>__IDLETIME__)):
    unset($_SESSION[__SITE__.'_ENCRYPT_ID']);
	unset($_SESSION[__SITE__.'_ENCRYPT_EMAIL']);
	unset($_SESSION[__SITE__.'_ENCRYPT_PASSWORD']);
	unset($_SESSION[__SITE__.'_ENCRYPT_LOGIN']);

	$_SESSION[__SITE__.'_LOGIN_TIMEOUT'] = false;
else:
	$_SESSION[__SITE__.'_LOGIN_TIMEOUT'] = true;
endif;

$_SESSION[__SITE__.'_TIMESTAMP'] = time();

// MESSAGE
$MESSAGE = $_SESSION[__SITE__.'_MESSAGE'];
unset($_SESSION[__SITE__.'_MESSAGE']);
$MESSAGETYPE = $_SESSION[__SITE__.'_MESSAGETYPE'];
unset($_SESSION[__SITE__.'_MESSAGETYPE']);

// LOGIN
if (isset($_SESSION[__SITE__.'_ENCRYPT_ID']) && 
	isset($_SESSION[__SITE__.'_ENCRYPT_USERNAME']) && 
	!empty($_SESSION[__SITE__.'_ENCRYPT_ID']) && 
	!empty($_SESSION[__SITE__.'_ENCRYPT_USERNAME'])):
	$_SESSION[__SITE__.'_LOGIN_SESSION'] = true;
else:
	$_SESSION[__SITE__.'_LOGIN_SESSION'] = false;
endif;

// SESSION ID
$SESSION_ID = session_id();
?>