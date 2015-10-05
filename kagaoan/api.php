<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
	redirect(0,__ROOT__."/index.php?file=process&process=invalid");
	exit();
else:
	include dirname(__FILE__) . "/loading.php";
	
	if (!isset($request_id) || !isset($usersite) || !isset($epms_id) || empty($request_id) || empty($usersite) || empty($epms_id)):
		$_SESSION[__SITE__.'_MESSAGE'] = "<span class='px_10 line_24 wrap error'>You don't have permission to access the link/file on this server.</span>";
		redirect(0,__ROOT__."/index.php?file=login");
		exit();
	endif;
	
	$connect = new MySQL();
	$connect->connect(
		$config['DB']['EPMS']['USERNAME'],
		$config['DB']['EPMS']['PASSWORD'],
		$config['DB']['EPMS']['DATABASE'],
		$config['DB']['EPMS']['HOST']
	);
	
	$epms_id = decryption($epms_id);
	$request_id = decryption($request_id);
	
	$epmsdata = $connect->single_result_array("SELECT user_id,user_site FROM epms_users WHERE epms_id = '$epms_id'");
	extract($epmsdata);
	
	$connect->connect(
		$config['DB'][$user_site]['USERNAME'],
		$config['DB'][$user_site]['PASSWORD'],
		$config['DB'][$user_site]['DATABASE'],
		$config['DB'][$user_site]['HOST']
	);
	
	$get_access = $connect->single_result_array("
		SELECT 
			t1.user_name,
			t1.user_login,
			t1.user_firstname,
			t1.user_lastname,
			t1.user_email,
			t1.user_group_id,
			t1.user_status,
			t2.user_group_controlpanel,
			t2.user_group_status
		FROM cpanel_user AS t1 
			LEFT JOIN cpanel_user_group AS t2 ON t1.user_group_id = t2.user_group_id 
		WHERE 
			t1.user_id = '$user_id'
	");
	
	if (sizeof($get_access) < 1):
		$_SESSION[__SITE__.'_MESSAGE'] = "<span class='px_10 line_24 wrap error'>Invalid User</span>";
		redirect(0,__ROOT__."/index.php?file=login");
		exit();
	endif;

	$user_login = $get_access['user_login']+1;
	$user_session_id = $SESSION_ID;
	$user_last_login = date("Y-m-d h:i:s");

	$data = array(
		'user_last_login'=>$user_last_login,
		'user_login'=>$user_login,
		'user_session_id'=>$user_session_id
	);
	
	$connect->update($data, cpanel_user, "user_id = '$user_id'");

	$data = array(
		'user_id'=>$user_id,
		'log_date'=>$user_last_login,
		'log_module'=>'api',
		'log_action'=>'login'
	);
	
	$connect->insert($data, cpanel_user_log);
	
	$user_name = $get_access['user_name'];
	$user_firstname = $get_access['user_firstname'];
	$user_lastname = $get_access['user_lastname'];
	$user_email = $get_access['user_email'];
	$user_group_id = $get_access['user_group_id'];

	
	if ($user_firstname != ''):
		$lastname = $user_lastname != '' ? ' '.$user_lastname:'';
		$user_fullname = $user_firstname.$lastname;
	else:
		$user_fullname = $user_name;
	endif;

	$_SESSION[__SITE__.'_ENCRYPT_ID'] = encryption($user_id);
	$_SESSION[__SITE__.'_ENCRYPT_GROUP_ID'] = encryption($user_group_id);
	$_SESSION[__SITE__.'_ENCRYPT_USERNAME'] = encryption($user_name);
	$_SESSION[__SITE__.'_ENCRYPT_NAME'] = encryption($user_fullname);
	$_SESSION[__SITE__.'_ENCRYPT_EMAIL'] = encryption($user_email);

	switch($type_id):
		case 1: $section = "request-product"; break;	
		case 2: $section = "change-product"; break;
		case 3: $section = "request-category"; break;
		case 4: $section = "change-category"; break;
	endswitch;		
	
	$_SESSION[__SITE__.'_REQUEST_ID'] = $request_id;
	
	redirect(0,__ROOT__."/index.php?file=epms&panel=epms&section=".$section);
endif;
?>