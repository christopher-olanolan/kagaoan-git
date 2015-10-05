<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['panel']) || empty($_GET['section'])): 
	include dirname(__FILE__) . "/error.php";
else:
	switch($section):
		// PROFILE
		case 'profile':
			$profile = $connect->single_result_array("
				SELECT * FROM access
				WHERE 
					id = '{$login_id}'"
				);
			
			$user_id = $login_id;
			$sendmail = $profile['sendmail'];
			$username = $profile['username'];

		?>
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=profile" id="profileForm">
        	<div style="width:100%;" align="left">
        		<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">User Profile</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">General Information:</th>
            		</tr>
            	</table>
            	
                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="220" class="pad_left_15">Username: </td>
                        <td class="line_30" colspan="2"><?=$username?> </td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Email: </td>
                        <td><input id="user_email" name="user_email" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$sendmail?>" /></td>
                        <td width="420"><label for="user_email" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Old Password: </td>
                        <td><input id="old_password" name="old_password" type="password" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="old_password" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">New Password: </td>
                        <td><input id="user_password" name="user_password" type="password" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="user_password" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Confirm New Password: </td>
                        <td><input id="confirm_password" name="confirm_password" type="password" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="confirm_password" generated="false" class="error"></label></td>
                    </tr>
                </table>

                <div class="spacer_20 clean"><!-- SPACER --></div>
                <div class="px_11"><strong>Note:</strong> <em>Leave the password blank, if don't want to change the user's current password.</em></div>
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
                <div style="width:100%;" align="left">
                <input name="clear" type="reset" value="Reset Form" class="button" />
                <input name="update" type="submit" value="Update" class="button" />
                </div>
            </div>
            </form>
            
            <script>
            $(document).ready(function() {
            	$("#profileForm").validate({
					rules: {
						user_email : {
							required: true,
							email: true,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$panel?>&control=profile-email&id=<?=$user_id?>"
						},
						old_password : {
							required: false,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$panel?>&control=old-password&id=<?=$user_id?>"
						},
						confirm_password : {
							required: false,
							equalTo: "#user_password"
						},
						user_password : {
							required: false
						}
					},
					messages: {
						user_email : {
							required: "Please provide your email address",
							email: "Please provide a valid email address",
							remote: "Email already registered." 
						},
						old_password : {
							remote: "Please provide the correct password."
						},
						confirm_password : {
							equalTo: "Password did not match."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
			});
            </script>
        <?
		break;
		// EOF PROFILE
		
		// MANAGE
		case 'manage':
			$page = isset($page) ? $page : '0';
		?>	
		<script type="text/javascript">
			$(document).ready(function() {
				ajaxLoad("<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$panel?>&control=<?=$section?>&page=<?=$page?>","GET");
			});
		</script>
		
		<div id="loadajax" align="center">
			<div class="spacer_100 clean"><!-- SPACER --></div>
			<img src="<?=__IMAGE__?>load.gif" class="clean" />
			<div class="spacer_5 clean"><!-- SPACER --></div>
			<span class="shadow pt_8">Please wait...</span>
		</div>
		
		<div id="ajax" class="hidden"></div>
		<?
		break;
		// EOF MANAGE
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>


