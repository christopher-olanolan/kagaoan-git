<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file']) || empty($_GET['process'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
	include dirname(__FILE__) . "/loading.php";

	$connect = new MySQL();
	$connect->connect(
		$config['DB'][__SITE__]['USERNAME'],
		$config['DB'][__SITE__]['PASSWORD'],
		$config['DB'][__SITE__]['DATABASE'],
		$config['DB'][__SITE__]['HOST']
	);	
	
	switch($process):			
		// LOGOUT
		case 'logout':
			$log_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$log_date = date("Y-m-d H:i:s");
			
			$data = array(
				'user_id'=>$log_id,
				'created'=>$log_date,
				'module'=>'access',
				'action'=>'logout'
			);
			
			$json = json_encode($data,true);
			$data['json'] = htmlentities($json, ENT_QUOTES);
			
			$connect->insert($data, audit_log);
					
			unset($_SESSION[__SITE__.'_ENCRYPT_ID']);
			unset($_SESSION[__SITE__.'_ENCRYPT_USERNAME']);
			unset($_SESSION[__SITE__.'_ENCRYPT_PASSWORD']);
			unset($_SESSION[__SITE__.'_ENCRYPT_LOGIN']);
			unset($_SESSION[__SITE__.'_ENCRYPT_NAME']);
			unset($_SESSION[__SITE__.'_ENCRYPT_EMAIL']);
			unset($_SESSION[__SITE__.'_LOGIN_SESSION']);

			// $_SESSION[__SITE__.'_MESSAGE'] = "<span class='px_10 line_24 wrap'>You are now logged off.</span>";
			$_SESSION[__SITE__.'_MESSAGE'] = "You are now logged off.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "info";
			redirect(0,__ROOT__."/index.php");
			exit();
		break;
		// EOF LOGOUT
		
		// INVALID
		case 'invalid':
			$log_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$log_date = date("Y-m-d H:i:s");
			
			$data = array(
				'user_id'=>$log_id,
				'created'=>$log_date,
				'module'=>'access',
				'action'=>'invalid'
			);
			
			$json = json_encode($data,true);
			$data['json'] = htmlentities($json, ENT_QUOTES);
			
			$connect->insert($data, audit_log);
			
			unset($_SESSION[__SITE__.'_ENCRYPT_ID']);
			unset($_SESSION[__SITE__.'_ENCRYPT_USERNAME']);
			unset($_SESSION[__SITE__.'_ENCRYPT_PASSWORD']);
			unset($_SESSION[__SITE__.'_ENCRYPT_LOGIN']);
			unset($_SESSION[__SITE__.'_ENCRYPT_NAME']);
			unset($_SESSION[__SITE__.'_ENCRYPT_EMAIL']);
			unset($_SESSION[__SITE__.'_LOGIN_SESSION']);
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Invalid Access! You are automatically logged off.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
			redirect(0,__ROOT__."/index.php");
			exit();
		break;
		// EOF INVALID
		
		// LOGIN
		case 'login':
			if (empty($username) || empty($password) || !isset($username) || !isset($password)):
				$_SESSION[__SITE__.'_MESSAGE'] = "Invalid login details. Please enter a valid login access.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php");
				exit();
			else:	
				$username = htmlentities($username, ENT_QUOTES);
				$md5password = md5($password);
				
				$check_access = $connect->single_result_array("
					SELECT * FROM access
					WHERE 
						username = '$username' OR 
						sendmail = '$username'
				");
				
				/*
				printr($check_access);
				exit();
				*/
				
				if ($check_access['id'] == 'D' || empty($check_access['id'])):
					$_SESSION[__SITE__.'_MESSAGE'] = "Invalid login details. Please enter a valid login access";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
					redirect(0,__ROOT__."/index.php?file=login");
					exit();
				elseif($check_access['password'] != $md5password):
					$_SESSION[__SITE__.'_MESSAGE'] = "Invalid password. Please enter a valid login access";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
					redirect(0,__ROOT__."/index.php?file=login");
					exit();
				elseif($check_access['active'] != "1"):
					$_SESSION[__SITE__.'_MESSAGE'] = "Your account is disabled. Please contact administrator";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
					redirect(0,__ROOT__."/index.php?file=login");
					exit();
				else:
					$user_id = $check_access['id'];
					$username = $check_access['username'];
					$sendmail = $check_access['sendmail'];
					$login = $check_access['login'] + 1;
					$session = $SESSION_ID;
					$last = date("Y-m-d H:i:s");
					
					$data = array(
						'last_login'=> $last,
						'login'=>$login,
						'session'=>$session
					);
					
					$connect->update($data, access, "id = '$user_id'");

					$json = json_encode($data,true);
			
					$data = array(
						'user_id'=>$user_id,
						'created'=>$last,
						'module'=>'access',
						'action'=>'login',
						'json' => htmlentities($json, ENT_QUOTES) 
					);
					
					$connect->insert($data, audit_log);
			
					$_SESSION[__SITE__.'_ENCRYPT_ID'] = encryption($user_id);
					$_SESSION[__SITE__.'_ENCRYPT_USERNAME'] = encryption($username);
					$_SESSION[__SITE__.'_ENCRYPT_PASSWORD'] = encryption($password);
					$_SESSION[__SITE__.'_ENCRYPT_NAME'] = encryption($username);
					$_SESSION[__SITE__.'_ENCRYPT_EMAIL'] = encryption($sendmail);
	
					redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=profile");
					exit();
				endif;
			endif;
		break;
		// EOF LOGIN
		
		// MANAGE USERS
		case 'manage-user':
			$cbox = count($action['checkbox']);
			
			// SINGLE DELETE
			if (isset($action['single-delete'])):
				$id = $action['single-delete'];
				$data = array('active' => '2');
				$connect->update($data, access, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'user',
					'action' => 'delete',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = "User successfully <strong>deleted</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			
			// SINGLE ACTIVATE
			elseif (isset($action['single-active'])):
				$id = $action['single-active'];
				$data = array('active' => '1');
				$connect->update($data, access, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'user',
					'action' => 'active',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
							
				$_SESSION[__SITE__.'_MESSAGE'] = "User successfully <strong>activated</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
			elseif ($cbox > 0):
				// MULTIPLE ACTIVATE
				if ($action['multi-active'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '1');
						$connect->update($data, access, "id = '$id'");
					endfor;
				
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'user',
						'action' => 'multi-active',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);
						
					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox user(s) successfully set to <strong>active</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
				// MULTIPLE DELETE
				elseif ($action['multi-delete'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '2');
						$connect->update($data, access , "id = '$id'");
					endfor;
					
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'user',
						'action' => 'multi-delete',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);

					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox user(s) successfully <strong>deleted</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				endif;
			endif;

			redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=manage");
			exit();
		break;
		// EOF MANAGE USERS
		
		// MANAGE TRUCK
		case 'manage-truck':
			$cbox = count($action['checkbox']);
			
			// SINGLE DELETE
			if (isset($action['single-delete'])):
				$id = $action['single-delete'];
				$data = array('active' => '2');
				$connect->update($data, truck, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'truck',
					'action' => 'delete',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = "Truck successfully <strong>deleted</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			
			// SINGLE ACTIVATE
			elseif (isset($action['single-active'])):
				$id = $action['single-active'];
				$data = array('active' => '1');
				$connect->update($data, truck, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'truck',
					'action' => 'active',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
							
				$_SESSION[__SITE__.'_MESSAGE'] = "Truck successfully <strong>activated</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
			elseif ($cbox > 0):
				// MULTIPLE ACTIVATE
				if ($action['multi-active'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '1');
						$connect->update($data, truck, "id = '$id'");
					endfor;
				
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'truck',
						'action' => 'multi-active',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);
						
					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox truck(s) successfully set to <strong>active</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
				// MULTIPLE DELETE
				elseif ($action['multi-delete'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '2');
						$connect->update($data, truck , "id = '$id'");
					endfor;
					
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'truck',
						'action' => 'multi-delete',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);

					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox truck(s) successfully <strong>deleted</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				endif;
			endif;

			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=manage");
			exit();
		break;
		// EOF MANAGE TRUCK
		
		// MANAGE PERSONNEL
		case 'manage-personnel':
			$cbox = count($action['checkbox']);
			
			// SINGLE DELETE
			if (isset($action['single-delete'])):
				$id = $action['single-delete'];
				$data = array('active' => '2');
				$connect->update($data, personnel, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'personnel',
					'action' => 'delete',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = "Personnel successfully <strong>deleted</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			
			// SINGLE ACTIVATE
			elseif (isset($action['single-active'])):
				$id = $action['single-active'];
				$data = array('active' => '1');
				$connect->update($data, personnel, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'personnel',
					'action' => 'active',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
							
				$_SESSION[__SITE__.'_MESSAGE'] = "Personnel successfully <strong>activated</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
			elseif ($cbox > 0):
				// MULTIPLE ACTIVATE
				if ($action['multi-active'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '1');
						$connect->update($data, personnel, "id = '$id'");
					endfor;
				
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'personnel',
						'action' => 'multi-active',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);
						
					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox personnel(s) successfully set to <strong>active</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
				// MULTIPLE DELETE
				elseif ($action['multi-delete'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '2');
						$connect->update($data, personnel , "id = '$id'");
					endfor;
					
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'personnel',
						'action' => 'multi-delete',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);

					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox personnel(s) successfully <strong>deleted</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				endif;
			endif;

			redirect(0,__ROOT__."/index.php?file=panel&panel=personnel&section=manage");
			exit();
		break;
		// EOF MANAGE PERSONNEL
		
		// MANAGE DRIVER
		case 'manage-driver':
			$cbox = count($action['checkbox']);
			
			// SINGLE DELETE
			if (isset($action['single-delete'])):
				$id = $action['single-delete'];
				$data = array('active' => '2');
				$connect->update($data, truck_driver, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'driver',
					'action' => 'delete',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = "Assigned driver successfully <strong>deleted</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			
			// SINGLE ACTIVATE
			elseif (isset($action['single-active'])):
				$id = $action['single-active'];
				$data = array('active' => '1');
				$connect->update($data, truck_driver, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'driver',
					'action' => 'active',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
							
				$_SESSION[__SITE__.'_MESSAGE'] = "Assigned driver successfully <strong>activated</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
			elseif ($cbox > 0):
				// MULTIPLE ACTIVATE
				if ($action['multi-active'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '1');
						$connect->update($data, truck_driver, "id = '$id'");
					endfor;
				
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'driver',
						'action' => 'multi-active',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);
						
					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox assigned driver(s) successfully set to <strong>active</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
				// MULTIPLE DELETE
				elseif ($action['multi-delete'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '2');
						$connect->update($data, truck_driver , "id = '$id'");
					endfor;
					
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'driver',
						'action' => 'multi-delete',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);

					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox assigned driver(s) successfully <strong>deleted</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				endif;
			endif;

			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=driver");
			exit();
		break;
		// EOF MANAGE DRIVER
		
		// MANAGE CONSUMPTION
		case 'manage-consumption':
			$cbox = count($action['checkbox']);
			
			// SINGLE DELETE
			if (isset($action['single-delete'])):
				$id = $action['single-delete'];
				$data = array('active' => '2');
				$connect->update($data, consumptions, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'consumption',
					'action' => 'delete',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = "Truck consumption successfully <strong>deleted</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			
			// SINGLE ACTIVATE
			elseif (isset($action['single-active'])):
				$id = $action['single-active'];
				$data = array('active' => '1');
				$connect->update($data, consumptions, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => 'consumption',
					'action' => 'active',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
							
				$_SESSION[__SITE__.'_MESSAGE'] = "Truck consumption successfully <strong>activated</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
			elseif ($cbox > 0):
				// MULTIPLE ACTIVATE
				if ($action['multi-active'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '1');
						$connect->update($data, consumptions, "id = '$id'");
					endfor;
				
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'consumption',
						'action' => 'multi-active',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);
						
					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox truck consumption(s) successfully set to <strong>active</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
				// MULTIPLE DELETE
				elseif ($action['multi-delete'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '2');
						$connect->update($data, consumptions , "id = '$id'");
					endfor;
					
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => 'consumption',
						'action' => 'multi-delete',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);

					$_SESSION[__SITE__.'_MESSAGE'] = "$cbox truck consumption(s) successfully <strong>deleted</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				endif;
			endif;
			
			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=consumption&filter_plate=".$filter_plate."&filter_from=".$filter_from."&filter_to=".$filter_to);
			exit();
		break;
		// EOF MANAGE CONSUMPTION
		
		// ACTIVATE/DEACTIVATE DATA
		case 'stock-out':
			$cbox = count($action['checkbox']);
			$inv = new Inventory();
			
			// SINGLE DELETE
			if (isset($action['single-delete'])):
				$id = $action['single-delete'];
				$inv->select($id, $connect, '0');
				
				$data = array('active' => '2');
				$connect->update($data, $table, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => $table,
					'action' => 'delete',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = ucfirst($table) ." successfully <strong>deleted</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			
			// SINGLE ACTIVATE
			elseif (isset($action['single-active'])):
				$id = $action['single-active'];
				$inv->select($id, $connect, '1');
				
				$data = array('active' => '1');
				$connect->update($data, $table, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => $table,
					'action' => 'active',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
							
				$_SESSION[__SITE__.'_MESSAGE'] = ucfirst($table) ." successfully <strong>activated</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
			elseif ($cbox > 0):
				// MULTIPLE ACTIVATE
				if ($action['multi-active'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$inv->select($id, $connect, '1');
						
						$data = array('active' => '1');
						$connect->update($data, $table, "id = '$id'");
					endfor;
				
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => $table,
						'action' => 'multi-active',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);
						
					$_SESSION[__SITE__.'_MESSAGE'] = "{$cbox} {$table}(s) successfully set to <strong>active</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
				// MULTIPLE DELETE
				elseif ($action['multi-delete'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$inv->select($id, $connect, '0');
						
						$data = array('active' => '2');
						$connect->update($data, $table, "id = '$id'");
					endfor;
					
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => $table,
						'action' => 'multi-delete',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);

					$_SESSION[__SITE__.'_MESSAGE'] = "{$cbox} {$table}(s) successfully <strong>deleted</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				endif;
			endif;
			
			redirect(0,__ROOT__."/index.php?file=panel".$return_link.$filter_link);
			exit();
		break;
		// EOF ACTIVATE/DEACTIVATE DATA
		
		// ACTIVATE/DEACTIVATE DATA
		case 'activate':
			$cbox = count($action['checkbox']);
			$title = str_replace('_', ' ', $table);
				
			// SINGLE DELETE
			if (isset($action['single-delete'])):
				$id = $action['single-delete'];
				$data = array('active' => '2');
				$connect->update($data, $table, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => $table,
					'action' => 'delete',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = ucfirst($title) ." successfully <strong>deleted</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			
			// SINGLE ACTIVATE
			elseif (isset($action['single-active'])):
				$id = $action['single-active'];
				$data = array('active' => '1');
				$connect->update($data, $table, "id = '$id'");
				
				$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$date = date("Y-m-d H:i:s");
				$json = json_encode($_POST,true);
				
				$data = array(
					'user_id' => $user_id,
					'created' => $date,
					'module' => $table,
					'action' => 'active',
					'json' => htmlentities($json, ENT_QUOTES)
				);
				
				$connect->insert($data, audit_log);
							
				$_SESSION[__SITE__.'_MESSAGE'] = ucfirst($title) ." successfully <strong>activated</strong>.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
			elseif ($cbox > 0):
				// MULTIPLE ACTIVATE
				if ($action['multi-active'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '1');
						$connect->update($data, $table, "id = '$id'");
					endfor;
				
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => $table,
						'action' => 'multi-active',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);
						
					$_SESSION[__SITE__.'_MESSAGE'] = "{$cbox} {$title}(s) successfully set to <strong>active</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				
				// MULTIPLE DELETE
				elseif ($action['multi-delete'] == 'true'):
					for($x=0; $x<$cbox; $x++):
						$id = $action['checkbox'][$x];
						$data = array('active' => '2');
						$connect->update($data, $table, "id = '$id'");
					endfor;
					
					$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
					$date = date("Y-m-d H:i:s");
					$json = json_encode($_POST,true);
						
					$data = array(
						'user_id' => $user_id,
						'created' => $date,
						'module' => $table,
						'action' => 'multi-delete',
						'json' => htmlentities($json, ENT_QUOTES)
					);
					
					$connect->insert($data, audit_log);

					$_SESSION[__SITE__.'_MESSAGE'] = "{$cbox} {$title}(s) successfully <strong>deleted</strong>.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
				endif;
			endif;
			
			redirect(0,__ROOT__."/index.php?file=panel".$return_link.$filter_link);
			exit();
		break;
		// EOF ACTIVATE/DEACTIVATE DATA
		
		default:
			redirect(0,__ROOT__."/index.php?file=process&process=invalid");
			exit();
		break;
	endswitch;
endif;
?>