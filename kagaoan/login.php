<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
endif;

if (isset($_SESSION[__SITE__.'_ENCRYPT_ID'])){
	redirect(0,__ROOT__."/index.php?file=panel&panel=user&section=profile");
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=ucfirst(__TITLE__)?> - <?=ucfirst($file)?></title>
	<? include dirname(__FILE__) . "/scripts.php"; ?>
	<script type="text/javascript">	
	function setSite(site){
		$.ajax({
			type: "GET",
			url: "<?=__ROOT__?>/index.php?file=ajax&ajax=ajax&control=set-site",
			data: {site : site},
			dataType: "html"
		});
	}
	
	$(document).ready(function() {
		var remember = $.cookie($.base64Encode('remember'));
		
		if (remember == 'true') {
			var cookie_username = $.base64Decode($.cookie($.base64Encode('username')));
			var cookie_password = $.base64Decode($.cookie($.base64Encode('password')));
			
			// autofill the fields
			setSite('<?=$_SITE?>');
			
			$('#username').val(cookie_username);
			$('#password').val(cookie_password);
			$('#remember').attr('checked',true);
		} else {
			$('#username').val('');
			$('#password').val('');
			$('#remember').attr('checked',false);
		}
		
		$('input[type="submit"]').click(function(){
			var username = $('#username').val();
			var password = $('#password').val();
			
			if (username != '' && password != '') {
		        if ($('#remember').attr('checked')) {
					// set cookies to expire in 14 days
					$.cookie($.base64Encode('username'), $.base64Encode(username), { expires: 14 });
					$.cookie($.base64Encode('password'), $.base64Encode(password), { expires: 14 });
					$.cookie($.base64Encode('remember'), true, { expires: 14 });
				} else {
					// reset cookies
					$.cookie($.base64Encode("username"), null);
					$.cookie($.base64Encode("password"), null);
					$.cookie($.base64Encode("remember"), false);
				}
			}
	    });

		$('input[type="reset"]').click(function(){
	        clearForm(this.form);
	    });
	    
		$("#loginForm").validate({
			rules: {
				username : {
					required: true
				},	
				password : {
					required: true
				}
			},
			messages: {
				username : {
					required: "Please enter your login ID.",
				},	
				password : {
					required: "Please provide a password."
				}
			},
			onkeyup: false,
	  		onblur: true
		});
	});
	</script>
</head>
<body>
<div style="width:100%;" align="center">
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
				<div class="spacer_40 clean"><!-- SPACER --></div>
			</div>
		</div>
		<!-- EOF LOGO -->
		
		<!-- MENU -->
		<div style="width: 100%; height: 88px;" align="left">
		</div>
		<!-- EOF MENU -->
		
		<div class="spacer_15 clean clear"><!-- SPACER --></div>
		
		<!-- CONTENT -->
		<div style="width: 100%;" align="center" id="main-height">
			<div style="width: 520px;" align="center">
                
                <form id="loginForm" action="<?=__ROOT__?>/index.php?file=process&process=login" method="post">
	                <div class="table_title" align="center">
	                	<div style="margin-left:15px; width:50%; line-height: 42px; float: left;" align="left" class="px_16 float_left">Login</div>
					</div>
	                <div class="table_container">
	                	<div class="spacer_15 clean clear"><!-- SPACER --></div>
	                	<table cellpadding="0" cellspacing="10" border="0" width="100%">
			                <tr height="65">
			                    <td align="left" width="1%" valign="top">
			                        Login ID
			                        <div class="spacer_2 clean clear"><!-- SPACER --></div>
			                        <input id="username" name="username" type="text" class="inputtext mid_inputtext" maxlength="50" />
			                        <label for="username" generated="false" class="error"></label>
			                    </td>
			                    <td align="left" width="1%" valign="top">
			                        Password
			                        <div class="spacer_2 clean clear"><!-- SPACER --></div>
			                        <input id="password" name="password" type="password" class="inputtext mid_inputtext" maxlength="50" /><br />
			                        <label for="password" generated="false" class="error"></label>
			                    </td>
			                    <td align="right" valign="top">
			                    	<div class="spacer_20 clean clear"><!-- SPACER --></div>
			                        <input name="login" type="submit" value="Login" class="button" />
			                        <input name="clear" type="reset" value="Clear" class="button" />
			                    </td>
			                </tr>
			                <tr>
			                    <td class="pt_8" colspan="3">
		                        	<div class="marg_right_10 float_left" style="width: 40%; text-align: left;" align="left">
		                        		<input type="checkbox" name="remember" id="remember" class="float_left pad_top_0 marg_top_0 marg_right_5" /> <strong>Remember access?</strong>
		                        	</div>
		                        	<div class="float_right hidden" style="width: 40%; text-align: right;" align="right">
		                        		<a href="<?=__ROOT__?>/index.php?file=forgot"><img src="images/icn-bullet.png" align="absmiddle" class="marg_right_5" /><strong>Can't access your account?</strong></a>
		                        	</div>
			                    </td>
			                </tr>
			            </table>
	                	<div class="spacer_15 clean clear"><!-- SPACER --></div>
	                </div>
                </form>
                
                <div class="spacer_55 clean clear"><!-- SPACER --></div>
            </div>
		</div>
		<!-- EOF CONTENT -->
	</div>

    <? include (str_replace('//','/',dirname(__FILE__).'/') . 'footer.php'); ?>
</div>

<? include (str_replace('//','/',dirname(__FILE__).'/') . 'notification.php'); ?>
</body>
</html>