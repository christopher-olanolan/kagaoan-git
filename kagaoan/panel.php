<?
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
			
	if ($_SESSION[__SITE__.'_LOGIN_TIMEOUT'] == false):
		$log_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
		$log_date = date("Y-m-d h:i:s");
		$data = array(
			'id'=>$log_id,
			'created'=>$log_date,
			'module'=>'logout',
			'action'=>'timeout'
		);
		
		$connect->insert($data, audit_log);
		
		$_SESSION[__SITE__.'_MESSAGE'] = "No activity for " . (__IDLETIME__/3600) . " hour; please log in again.";
		$_SESSION[__SITE__.'_MESSAGETYPE'] = "warning";
		redirect(0,__ROOT__."/index.php?file=login");
		exit();
	else:		
		if ($_SESSION[__SITE__.'_LOGIN_SESSION'] == true):
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$login_username = decryption($_SESSION[__SITE__.'_ENCRYPT_USERNAME']);
			$login_session_id = $SESSION_ID;
			
			$user_access = $connect->single_result_array("
				SELECT * FROM access
				WHERE 
					id = '$login_id' AND 
					username = '$login_username' AND
					active = '1'
			");
			
			/*
			printr($user_access);
			exit();
			*/
			
			if ($user_access['id'] == 'D' || empty($user_access['id'])):
				$log_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$log_date = date("Y-m-d h:i:s");
				$data = array(
					'user_id'=>$log_id,
					'created'=>$log_date,
					'module'=>'logout',
					'action'=>'invalid'
				);
				
				$connect->insert($data, audit_log);
		
				$_SESSION[__SITE__.'_MESSAGE'] = "You don't have permission to access the link/file on this server.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=login");
				exit();
			elseif($user_access['type'] != '0'):
				$log_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
				$log_date = date("Y-m-d h:i:s");
				$data = array(
					'user_id'=>$log_id,
					'created'=>$log_date,
					'module'=>'logout',
					'action'=>'type access'
				);
				
				$connect->insert($data, audit_log);
				
				$_SESSION[__SITE__.'_MESSAGE'] = "Your user group don't have permission to access the link/file on this server.";
				$_SESSION[__SITE__.'_MESSAGETYPE'] = "error";
				redirect(0,__ROOT__."/index.php?file=login");
				exit();
			else:
				define("__ACCESS__", true);		
				$login_name = $user_access['username'];
			?>
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="https://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title><?=__TITLE__?> - <?=ucfirst($panel)?> <?=ucfirst($section)?></title>
				<? include dirname(__FILE__) . "/scripts.php"; ?>
			</head>
			<body>
				<div style="width:100%; position:absolute; top:0;" align="center">
					<div class="main-width" align="center">					    
						<!-- LOGO -->
						<div style="width: 100%; height: 110px;" align="left" class="clean">
							<div style="width: 50%;" class="float_left clean" align="left">
								<div class="spacer_20 clean"><!-- SPACER --></div>
								<strong class="lightbrown pt_20"><?=$site['company']?></strong>
								<div class="spacer_0 clean"><!-- SPACER --></div>
								<span class="lighterbrown">
									<i><?=$site['address']?></i>
									<div class="spacer_0 clean"><!-- SPACER --></div>
									<?=$site['phone']?>
								</span>
							</div>
							
							<div style="width: 30%; color: #D4D4D4;" class="float_right clean px_12" align="right">
								<div class="spacer_10 clean"><!-- SPACER --></div>
								<div style="width: 100%; height: 10px;" class="SETDate"></div>
								<div class="spacer_30 clean"><!-- SPACER --></div>
								Welcome, <em><?=$login_name?></em>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?=__ROOT__?>/index.php?file=process&process=logout" class="logout">Logout</a>
							</div>
						</div>
						<!-- EOF LOGO -->
						
						<!-- MENU -->
						<div style="width: 100%; height: 88px;" align="left">
							<? include dirname(__FILE__) . "/menu.php"; ?>
						</div>
						<!-- EOF MENU -->
						
						<div class="spacer_15 clean clear"><!-- SPACER --></div>
						
						<!-- CONTENT -->
						<div style="width: 100%;" align="center" id="main-height">
							
							<div id="loadpage" align="center">
								<div class="spacer_100 clean"><!-- SPACER --></div>
								<img src="<?=__IMAGE__?>load.gif" class="clean" />
								<div class="spacer_5 clean"><!-- SPACER --></div>
								<span class="shadow pt_8">loading...</span>
							</div>
							
							<div style="width:100%; height:35px; color:#B1732A; border-bottom:1px solid #C1B972; text-align:left; font-variant:small-caps;" class="pt_20 line_24">
								<?=ucfirst($panel)?> : <?=ucfirst($section)?>
							</div>
							
							<div class="spacer_15 clean clear"><!-- SPACER --></div>
							
							<div id="contents" class="hidden">
								<?
								if(!empty($panel) || $panel != ''):
									if(file_exists(str_replace('//','/',dirname(__FILE__).'/') . 'panel/'.$panel.'.php')):
										switch($panel):
											case 'user':
											case 'personnel':
											case 'truck':
											case 'transaction':
											case 'inventory':
											case 'settings':
											case 'report':
												include (str_replace('//','/',dirname(__FILE__).'/') . 'panel/'.$panel.'.php');	
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
							
							<div class="spacer_25 clean clear"><!-- SPACER --></div>
						</div>
						<!-- EOF CONTENT -->
					</div>
				
				    <? include (str_replace('//','/',dirname(__FILE__).'/') . 'footer.php'); ?>
				</div>
				<img src="<?=__IMAGE__?>blank.gif" class="clean hidden" onload="fileLoaded();" />
				
				<? include (str_replace('//','/',dirname(__FILE__).'/') . 'notification.php'); ?>
			</body>
			</html>
			<?
			endif;
		else:
			redirect(0,__ROOT__."/index.php?file=process&process=invalid");
			exit();
		endif;
	endif;
endif;
?>