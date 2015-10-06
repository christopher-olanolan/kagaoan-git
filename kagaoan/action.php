<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
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

	switch($action):		
		// PROFILE
		case 'profile':
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			
			$data = array(
				'sendmail' => $user_email
			);

			if (!empty($user_password)):
				$profile = $connect->single_result_array("
					SELECT 
						password
					FROM access
					WHERE 
						id = '$login_id'
				");
				
				$db_user_password = $profile['password'];
				$md5_old_password = md5($old_password);
			
				if ($md5_old_password != $db_user_password):
					$_SESSION[__SITE__.'_MESSAGE'] = "Old password mismatch.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
					redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=profile");
					exit();
				elseif ($user_password != $confirm_password):
					$_SESSION[__SITE__.'_MESSAGE'] = "Password confirmation mismatch.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
					redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=profile");
					exit();
				else:
					$data['password'] = md5($user_password);
					$_SESSION[__SITE__.'_ENCRYPT_PASSWORD'] = encryption($user_password);
				endif;
			endif;
			
			$connect->update($data, access, "id = '{$login_id}'");
					
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = date("Y-m-d H:i:s");
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'profile',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Profile successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=profile");
			exit();
			
		break;
		// EOF PROFILE
		
		// ADD USER
		case 'add-user':
			$user_password = md5($user_password);
			$create = date("Y-m-d H:i:s");

			$data = array(
				'username' => $user_name,
				'sendmail' => $user_email,
				'password' => $user_password,
				'last_login' => $create,
				'created' => $create
			);

			$connect->insert($data, access);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $create;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'user',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "User ($user_name) successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=manage");
			exit();
		break;
		// EOF ADD USER
		
		// EDIT USER
		case 'edit-user':
			$data = array(
				'sendmail' => $user_email
			);
			
			if (!empty($user_password)):
				$data['user_password'] = md5($user_password);
			endif;
			
			$connect->update($data, access, "id = '$user_id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = date("Y-m-d H:i:s");
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'user',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
			
			$_SESSION[__SITE__.'_MESSAGE'] = "User successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=manage");
			exit();
		break;
		// EOF EDIT USER
		
		
		// ADD PERSONNEL
		case 'add-personnel':
			$create = date("Y-m-d H:i:s");
			$hire = $hire == '' ? $create : $hire;
			
			$data = array(
				'empno' => $empno,
				'firstname' => $firstname,
				'lastname' => $lastname,
				'middlename' => $middlename,
				'address' => $address,
				'type' => $type,
				'gender' => $gender,
				'mobile' => $mobile,
				'landline' => $landline,
				'sendmail' => $user_email,
				'sss' => $sss,
				'pagibig' => $pagibig,
				'tin' => $tin,
				'hire' => $hire,
				'created' => $create
			);

			$connect->insert($data, personnel);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $create;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'personnel',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);

			$user_name = $firstname . ' ' . $lastname;
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Personnel ($user_name) successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=personnel&section=manage");
			exit();
		break;
		// EOF ADD PERSONNEL
		
		// EDIT PERSONNEL
		case 'edit-personnel':
			$create = date("Y-m-d H:i:s");
			$hire = $hire == '' ? $create : $hire;
			
			$data = array(
				'empno' => $empno,
				'firstname' => $firstname,
				'lastname' => $lastname,
				'middlename' => $middlename,
				'address' => $address,
				'type' => $type,
				'gender' => $gender,
				'mobile' => $mobile,
				'landline' => $landline,
				'sendmail' => $user_email,
				'sss' => $sss,
				'pagibig' => $pagibig,
				'tin' => $tin,
				'hire' => $hire,
				'created' => $create
			);

			$connect->update($data, personnel, "id = '$id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $create;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'personnel',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);

			$user_name = $firstname . ' ' . $lastname;
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Personnel ($user_name) successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=personnel&section=manage");
			exit();
		break;
		// EOF EDIT PERSONNEL
		
		
		// ADD TRUCK
		case 'add-truck':
			$create = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/");
			$plate = str_replace($invalid,'',$plate);
			
			$data = array(
				'plate' => $plate,
				'truck_model' => $truck_model,
				'truck_type' => $truck_type,
				'operator' => $operator,
				'active' => 1,
				'created' => $create
			);

			$connect->insert($data, truck);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $create;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'truck',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Truck ($plate) successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=manage");
			exit();
		break;
		// EOF ADD TRUCK
		
		// EDIT TRUCK
		case 'edit-truck':
			$create = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/");
			$plate = str_replace($invalid,'',$plate);
			
			$data = array(
				'plate' => $plate,
				'truck_model' => $truck_model,
				'truck_type' => $truck_type,
				'operator' => $operator
			);

			$connect->update($data, truck, "id = '$id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $create;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'truck',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Truck ($plate) successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=manage");
			exit();
		break;
		// EOF EDIT TRUCK
		
		
		// ADD DRIVER
		case 'add-driver':
			if (!strtotime($assigned)):
				$_SESSION[__SITE__.'_MESSAGE'] = "Invalid date format.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=driver");
				exit();
			endif;
			
			$assigned = str_replace(' ','',$assigned);
			
			$query = $connect->single_result_array("SELECT id FROM truck_driver WHERE truck_id = '{$truck_id}' AND driver_id = '{$driver_id}' AND assigned LIKE '{$assigned}%'");
			$check = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? true:false;
			
			if (!$check):
				$_SESSION[__SITE__.'_MESSAGE'] = "Driver already assigned to this truck.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=driver");
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				'truck_id' => $truck_id,
				'driver_id' => $driver_id,
				'assigned' => $assigned,
				'created' => $created
			);

			$connect->insert($data, truck_driver);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $create;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'driver',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Driver successfully assigned.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=driver");
			exit();
		break;
		// EOF ADD DRIVER
		
		// EDIT DRIVER
		case 'edit-driver':
			if (!strtotime($assigned)):
				$_SESSION[__SITE__.'_MESSAGE'] = "Invalid date format.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=driver");
				exit();
			endif;
			
			$assigned = str_replace(' ','',$assigned);
			
			$data = array(
				'truck_id' => $truck_id,
				'driver_id' => $driver_id,
				'assigned' => $assigned
			);
		
			if ($id == '' || $id == 'D'):
				ECHO "update";
				$data['created'] = date("Y-m-d H:i:s");
				$action = 'add';
				$connect->insert($data, truck_driver);
			else:
				$query = $connect->single_result_array("SELECT id FROM truck_driver WHERE truck_id = '{$truck_id}' AND driver_id = '{$driver_id}' AND assigned LIKE '{$assigned}%' AND id != '{$id}'");
				$check = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? true:false;
				
				if (!$check):
					$_SESSION[__SITE__.'_MESSAGE'] = "Driver already assigned to this truck.";
					$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
					redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=driver");
					exit();
				endif;
			
				$connect->update($data, truck_driver, "id = '$id'");
				$action = 'edit';
			endif;
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $create;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'driver',
				'action' => $action,
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Driver successfully assigned.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=driver");
			exit();
		break;
		// EOF EDIT DRIVER
		

		// ADD CONSUMPTION
		case 'add-consumption':
			$created = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$liters = str_replace($invalid,'',$liters);
			$price = str_replace($invalid,'',$price);
			
			$data = array(
				'truck_id' => $truck_id,
				'consumption_date' => $consumption_date,
				'liters' => $liters,
				'price' => $price,
				'active' => 1,
				'created' => $created
			);

			$connect->insert($data, consumptions);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'consumption',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Consumption successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=consumption");
			exit();
		break;
		// EOF ADD CONSUMPTION
		
		// EDIT CONSUMPTION
		case 'edit-consumption':
			$created = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$liters = str_replace($invalid,'',$liters);
			$price = str_replace($invalid,'',$price);
			
			$data = array(
				'truck_id' => $truck_id,
				'consumption_date' => $consumption_date,
				'liters' => $liters,
				'price' => $price
			);

			$connect->update($data, consumptions, "id = '$id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'consumption',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Consumption successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=truck&section=consumption");
			exit();
		break;
		// EOF EDIT CONSUMPTION
		
		// ADD TRANSACTION
		case 'add-transaction':
			$created = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$rate = str_replace($invalid,'',$rate);
			$cs = str_replace($invalid,'',$cs);
			$delivered = $delivered == "on" ? 1:0; 
			
			$data = array(
				'delivered' => $delivered,
				'transaction_date' => $transaction_date,
				'source' => $source,
				'destination' => $destination,
				'truck_id' => $truck_id,
				'driver_id' => $driver_id,
				'cs' => $cs,
				'rate' => $rate,
				'active' => 1,
				'created' => $created
			);
				
			if ($delivered > 0){
				$data['soa'] = $soa;
				$data['urc_doc'] = $urc_doc;
				$data['delivered_date'] = $delivered_date;
				$data['status'] = 1;
			} else {
				$data['status'] = 0;
			}
			
			$connect->insert($data, transaction);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'transaction',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
			
			// assign driver
			$assigned = $transaction_date;

			$query = $connect->single_result_array("SELECT id FROM truck_driver WHERE truck_id = '{$truck_id}' AND driver_id = '{$driver_id}' AND assigned LIKE '{$assigned}%'");
			$check = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? true:false;

			if ($check):
				$created = date("Y-m-d H:i:s");
				$data = array(
					'truck_id' => $truck_id,
					'driver_id' => $driver_id,
					'assigned' => $assigned,
					'created' => $created
				);
			
				$connect->insert($data, truck_driver);
			endif;			
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Transaction successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=manage");
			exit();
		break;
		// EOF ADD TRANSACTION
		
		// EDIT TRANSACTION
		case 'edit-transaction':
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$rate = str_replace($invalid,'',$rate);
			$cs = str_replace($invalid,'',$cs);	
			$delivered = $delivered == "on" ? 1:0; 
			
			$data = array(
				'delivered' => $delivered,
				'transaction_date' => $transaction_date,
				'source' => $source,
				'destination' => $destination,
				'truck_id' => $truck_id,
				'driver_id' => $driver_id,
				'cs' => $cs,
				'rate' => $rate,
				'active' => 1,
				'created' => $created
			);
				
			if ($delivered > 0){
				$data['soa'] = $soa;
				$data['urc_doc'] = $urc_doc;
				$data['delivered_date'] = $delivered_date;
				$data['status'] = 1;
			} else {
				$data['status'] = 0;
				$data['soa'] = '';
				$data['urc_doc'] = '';
				$data['delivered_date'] = '';
			}

			$connect->update($data, transaction, "id = '$id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'transaction',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
			
			// assign driver
			$assigned = $transaction_date;

			$query = $connect->single_result_array("SELECT id FROM truck_driver WHERE truck_id = '{$truck_id}' AND driver_id = '{$driver_id}' AND assigned LIKE '{$transaction_date}%'");
			$check = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? true:false;

			if ($check):
				$created = date("Y-m-d H:i:s");
				$data = array(
					'truck_id' => $truck_id,
					'driver_id' => $driver_id,
					'assigned' => $assigned,
					'created' => $created
				);
			
				$connect->insert($data, truck_driver);
			endif;	
			
			$_SESSION[__SITE__.'_MESSAGE'] = "Transaction successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=manage");
			exit();
		break;
		// EOF EDIT TRANSACTION
		
		// ADD DEDUCTION
		case 'add-deduction':
			$created = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$price = str_replace($invalid,'',$price);
			
			$data = array(
				'truck_id' => $truck_id,
				'deduction_id' => $deduction_id,
				'description' => $description,
				'date_from' => $date_from,
				'date_to' => $date_to,
				'price' => $price,
				'active' => 1,
				'created' => $created
			);
			
			if ($deduction_id == 3):
				$data['personnel_id'] = $personnel_id;
			else:
				$data['personnel_id'] = 0;
			endif;

			$connect->insert($data, deduction);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'deduction',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Deduction successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=deduction");
			exit();
		break;
		// EOF ADD DEDUCTION
		
		// EDIT DEDUCTION
		case 'edit-deduction':
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$price = str_replace($invalid,'',$price);
			
			$data = array(
				'truck_id' => $truck_id,
				'deduction_id' => $deduction_id,
				'description' => $description,
				'date_from' => $date_from,
				'date_to' => $date_to,
				'price' => $price
			);

			if ($deduction_id == 3):
				$data['personnel_id'] = $personnel_id;
			else:
				$data['personnel_id'] = 0;
			endif;
			
			$connect->update($data, deduction, "id = '$id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'deduction',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Deduction successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=deduction");
			exit();
		break;
		// EOF EDIT DEDUCTION
		
		// ADD DEDUCTION TYPE
		case 'add-deduction-type':
			$query = $connect->single_result_array("SELECT id FROM deduction_type WHERE LOWER(type_name) = LOWER('{$type_name}') ");
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;

			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "Deduction type <i>'{$type_name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=deduction");
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				'type_name' => $type_name,
				'active' => 1
			);

			$connect->insert($data, deduction_type);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'deduction_type',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Deduction type successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=deduction");
			exit();
		break;
		// EOF ADD DEDUCTION TYPE
		
		// EDIT DEDUCTION TYPE
		case 'edit-deduction-type':
			$count = $connect->count_records("SELECT id FROM deduction_type WHERE id != $type_id AND LOWER(type_name) = LOWER('$type_name') ");
			$exist = $count > 0 ? true:false;

			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "Deduction type <i>'{$type_name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=deduction");
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				'type_name' => $type_name
			);

			$connect->update($data, deduction_type, "id = '$type_id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'deduction_type',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Deduction type successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=deduction");
			exit();
		break;
		// EOF EDIT DEDUCTION TYPE
		
		// ADD PERSONNEL TYPE
		case 'add-personnel-type':
			$query = $connect->single_result_array("SELECT id FROM personnel_type WHERE LOWER(type_name) = LOWER('{$type_name}') ");
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;

			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "Personnel type <i>'{$type_name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=personnel&section=manage");
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				'type_name' => $type_name,
				'status' => 1
			);

			$connect->insert($data, personnel_type);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'personnel_type',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Personnel type successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=personnel&section=manage");
			exit();
		break;
		// EOF ADD DEDUCTION TYPE
		
		// EDIT DEDUCTION TYPE
		case 'edit-personnel-type':
			$count = $connect->count_records("SELECT id FROM personnel_type WHERE id != $type_id AND LOWER(type_name) = LOWER('$type_name') ");
			$exist = $count > 0 ? true:false;

			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "Personnel type <i>'{$type_name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=personnel&section=manage");
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				'type_name' => $type_name
			);

			$connect->update($data, personnel_type, "id = '$type_id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'personnel_type',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Personnel type successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=personnel&section=manage");
			exit();
		break;
		// EOF EDIT DEDUCTION TYPE
		
		
		// ADD BRAND
		case 'add-brand':
			$query = $connect->single_result_array("SELECT id FROM brand WHERE LOWER(brand_name) = LOWER('{$brand_name}') ");
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;

			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "Brand <i>'{$brand_name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=".$section);
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				'brand_name' => $brand_name,
				'active' => 1
			);

			$connect->insert($data, brand);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'brand',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Brand successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=".$section);
			exit();
		break;
		// EOF ADD BRAND
		
		// EDIT BRAND
		case 'edit-brand':
			$count = $connect->count_records("SELECT id FROM brand WHERE id != $brand_id AND LOWER(brand_name) = LOWER('$brand_name') ");
			$exist = $count > 0 ? true:false;

			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "Brand type <i>'{$brand_name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=".$section);
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				'brand_name' => $brand_name
			);

			$connect->update($data, brand, "id = '$brand_id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'brand',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Brand successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=".$section);
			exit();
		break;
		// EOF EDIT BRAND
		
		// ADD SETTING
		case 'add-setting':
			switch ($table):
				case "personnel_type":
				case "deduction_type":
					$column = "type_name";
				break;	
				case "location":
					$column = "location";
				break;	
				case "brand":
					$column = "brand_name";
				break;	
			endswitch;
			
			$where = " LOWER({$column}) = LOWER('{$name}') ";
			$query_string = "SELECT id FROM {$table} WHERE {$where} ";
			$query = $connect->single_result_array($query_string);
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;
			
			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "{$title} <i>'{$name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=settings&section=manage&setting={$setting}");
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				$column => $name,
				'active' => 1
			);

			$connect->insert($data, $table);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => $table,
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "{$title} successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=settings&section=manage&setting={$setting}");
			exit();
		break;
		// EOF ADD SETTING
		
		// EDIT SETTING
		case 'edit-setting':
			switch ($table):
				case "personnel_type":
				case "deduction_type":
					$column = "type_name";
				break;	
				case "location":
					$column = "location";
				break;	
				case "brand":
					$column = "brand_name";
				break;	
			endswitch;
			
			$where = " LOWER({$column}) = LOWER('{$name}') ";
			$query_string = "SELECT id FROM {$table} WHERE {$where} AND id != '{$id}' ";
			$query = $connect->single_result_array($query_string);
			$exist = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ?  false:true;
			
			if ($exist):
				$_SESSION[__SITE__.'_MESSAGE'] = "{$title} <i>'{$name}'</i> already exist!";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=settings&section=manage&setting={$setting}");
				exit();
			endif;
			
			$created = date("Y-m-d H:i:s");
			
			$data = array(
				$column => $name
			);

			$connect->update($data, $table, "id = '$id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => $table,
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "{$title} successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=settings&section=manage&setting={$setting}");
			exit();
		break;
		// EOF EDIT SETTING
		
		// ADD INVENTORY
		case 'add-inventory':
			$created = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$unit_price = str_replace($invalid,'',$unit_price);
			
			$data = array(
				'name' => $name,
				'description' => $description,
				'brand' => $brand,
				'unit_price' => $unit_price,
				'unit' => $unit,
				'stocks' => $stocks,
				'stock_limit' => $stock_limit,
				'purchase_date' => $purchase_date,
				'supplier' => $supplier,
				'active' => 1,
				'created' => $created
			);

			$connect->insert($data, inventory);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'inventory',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Inventory successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=manage");
			exit();
		break;
		// EOF ADD INVENTORY
		
		// EDIT INVENTORY
		case 'edit-inventory':
			$created = date("Y-m-d H:i:s");
			$invalid = array ("+","=","-"," ",",","/","L","l",",");
			$unit_price = str_replace($invalid,'',$unit_price);
			
			$data = array(
				'name' => $name,
				'description' => $description,
				'brand' => $brand,
				'unit_price' => $unit_price,
				'unit' => $unit,
				'stocks' => $stocks,
				'stock_limit' => $stock_limit,
				'purchase_date' => $purchase_date,
				'supplier' => $supplier
			);

			$connect->update($data, inventory, "id = '$id'");
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'inventory',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Inventory successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=manage");
			exit();
		break;
		// EOF EDIT INVENTORY
		
		// ADD INVENTORY STOCK OUT
		case 'add-stock-out':
			$created = date("Y-m-d H:i:s");
			$check = $connect->single_result_array("SELECT stocks FROM  inventory  WHERE id = $stock_id");
			
			if ((int) $qty > (int) $check['stocks']):
				$_SESSION[__SITE__.'_MESSAGE'] = "No stock available.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=stock-out");
				exit();
			endif;
			
			$data = array(
				'stock_id' => $stock_id,
				'truck_id' => $truck_id,
				'personnel_id' => $personnel_id,
				'requisition_date' => $requisition_date,
				'qty' => $qty,
				'created' => $created,
				'active' => 1
			);

			$connect->insert($data, requisition);
			
			$inv = new Inventory();
			$inv->select('0', $connect, '1', $qty, $stock_id);
			
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'requisition',
				'action' => 'add',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Inventory stock out successfully added.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=stock-out");
			exit();
		break;
		// EOF ADD INVENTORY STOCK OUT
		
		// EDIT INVENTORY STOCK OUT
		case 'edit-stock-out':
			$inv = new Inventory();
			$created = date("Y-m-d H:i:s");
			$check = $connect->single_result_array("SELECT stocks FROM  inventory  WHERE id = $stock_id");
			
			$orig_qty = (int) $orig_qty;
			$qty = (int) $qty;
			
			if ($stock_id != $orig_stock_id):
				$check_qty = $qty;
			else:
				if ($orig_qty != $qty):
					$check_qty = $orig_qty > $qty ? ($orig_qty - $qty):($qty - $orig_qty); 
				else:
					$check_qty = $qty;
				endif;
			endif;
			
			if ($check_qty > (int) $check['stocks']):
				$_SESSION[__SITE__.'_MESSAGE'] = "No stock available.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=stock-out");
				exit();
			endif;
			
			$data = array(
				'stock_id' => $stock_id,
				'truck_id' => $truck_id,
				'personnel_id' => $personnel_id,
				'requisition_date' => $requisition_date,
				'qty' => $qty
			);

			$connect->update($data, requisition, "id = $id");
			
			if ($stock_id != $orig_stock_id):
				$inv->select('0', $connect, '0', $orig_qty, $orig_stock_id);
				$inv->select('0', $connect, '1', $qty, $stock_id);
			else:
				if ($orig_qty != $qty):
					if ($orig_qty > $qty):
						$final_qty = $orig_qty - $qty; 
						$inv->select('0', $connect, '0', $final_qty, $stock_id);
					else:
						$final_qty = $qty - $orig_qty; 
						$inv->select('0', $connect, '1', $final_qty, $stock_id);
					endif;
				endif;
			endif;

			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = $created;
			$json = json_encode($data,true);
			
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'requisition',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
			
			$connect->insert($data, audit_log);
				
			$_SESSION[__SITE__.'_MESSAGE'] = "Inventory stock out successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=inventory&section=stock-out");
			exit();
		break;
		// EOF EDIT INVENTORY STOCK OUT
		
		// EDIT DEDUCTION SETTING
		case 'edit-deduction-setting':
			switch ($id):
				case '1':
					$display_value = $value*100 . "% Tax";
				break;
				case '2':
					$display_value = $value*100 . "% Savings";
				break;
				case '3':
					$display_value = $value*100 . "% Fund";
				break;
			endswitch;
			
			$data = array(
				'value' => $value,
				'display_value' => $display_value
			);
			
			$connect->update($data, settings, "id = $id");
		
			$user_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$date = date("Y-m-d H:i:s");
			$json = json_encode($data,true);
				
			$data = array(
				'user_id' => $user_id,
				'created' => $date,
				'module' => 'settings',
				'action' => 'edit',
				'json' => htmlentities($json, ENT_QUOTES)
			);
				
			$connect->insert($data, audit_log);
		
			$_SESSION[__SITE__.'_MESSAGE'] = "Deduction setting successfully updated.";
			$_SESSION[__SITE__.'_MESSAGETYPE'] = "success";
			redirect(0,__ROOT__."/index.php?file=panel&panel=settings&section=deduction");
			exit();
		break;
		// EOF EDIT DEDUCTION SETTING
			
		default:
			redirect(0,__ROOT__."/index.php?file=process&process=invalid");
			exit();
		break;
	endswitch;
endif;
?>