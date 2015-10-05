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
	<title><?=__TITLE__?> - Account Recovery</title>
	<? include dirname(__FILE__) . "/scripts.php"; ?>

<script type="text/javascript">
    function setSite(site){
		$.ajax({
			type: "GET",
			url: "<?=__ROOT__?>/index.php?file=ajax&ajax=ajax&control=set-store",
			data: {store : site},
			dataType: "html"
		});
	}
    
	$(document).ready(function() {
		$('input[type="reset"]').click(function(){
	        clearForm(this.form);
	    });
		    
		$("#loginForm").validate({
			rules: {
				username : {
					required: true,
					remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=user&control=forget-user",
				},	
				email : {
					required: true,
					email: true,
					remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=user&control=forget-email"
				},
				user_store : {
					required: true
				}
			},
			messages: {
				username : {
					required: "Please enter your login ID.",
					remote: "ID not yet registered."
				},
				email : {
					required: "Please enter registered email address.",
					email: "Please enter a valid email address.",
					remote: "Email address not yet registered."
				},
				user_store : {
					required: "Please select a store."
				}
			},
			onkeyup: false,
	  		onblur: true
		});
		
		$('#back').click(function(){
			window.location.href = "<?=__ROOT__?>/index.php";
		});
	});
</script>
</head>
<body>
<div style="width:100%;" align="center">
	<div class="main-width" align="center">
		<!-- MESSAGE -->
		<div id="message" style="position: absolute; left: 50%; height:26px;" align="center" class="hidden"> 
	    	<div class="spacer_20 clean clear"><!-- SPACER --></div>
	    	<div class="gradient box radius_15 shadow px_10 float_left" style="position: relative; left: -50%;">
	    		<div style="margin:3px 0px 3px 4px;"><?=$MESSAGE?></div>
	    	</div>
	    </div>
	    <!-- EOF MESSAGE -->
	    
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
                
                <form id="loginForm" action="<?=__ROOT__?>/index.php?file=process&process=forgot" method="post">
	                <div class="table_title" align="center">
	                	<div style="margin-left:15px; width:50%; line-height: 42px; float: left;" align="left" class="px_16 float_left">Account Recovery</div>
					</div>
	                <div class="table_container">
	                	<div class="spacer_15 clean clear"><!-- SPACER --></div>
	                	<table cellpadding="0" cellspacing="10" border="0" width="100%">
			                <tr height="65">
			                    <td align="left" width="1%" valign="top">
			                        Enter your Login ID
			                        <div class="spacer_2 clean clear"><!-- SPACER --></div>
			                        <input id="username" name="username" type="text" class="inputtext mid_inputtext" maxlength="50" />
			                        <label for="username" generated="false" class="error"></label>
			                    </td>
			                    <td align="left" width="1%" valign="top">
			                        Recovery email address
			                        <div class="spacer_2 clean clear"><!-- SPACER --></div>
			                        <input id="email" name="email" type="text" class="inputtext mid_inputtext" maxlength="50" /><br />
			                        <label for="email" generated="false" class="error"></label>
			                    </td>
			                    <td align="right" valign="top">
			                    	<div class="spacer_20 clean clear"><!-- SPACER --></div>
			                        <input name="login" type="submit" value="Send" class="button" />
			                        <input name="clear" type="reset" value="Clear" class="button" />
			                    </td>
			                </tr>
			                <tr>
			                    <td class="pt_8 shadow" colspan="3" align="right">
			                        <span class="line_13"><a href="<?=__ROOT__?>/index.php"><img src="images/icn-bullet.png" align="absmiddle" class="marg_right_5" /><strong>Back to Login</strong></a></span>
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