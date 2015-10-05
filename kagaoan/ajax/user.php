<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if ($ajax=="" || empty($ajax)):
	include dirname(__FILE__) . "/error.php";
else:
	$connect = new MySQL();
	$connect->connect(
		$config['DB'][__SITE__]['USERNAME'],
		$config['DB'][__SITE__]['PASSWORD'],
		$config['DB'][__SITE__]['DATABASE'],
		$config['DB'][__SITE__]['HOST']
	);
	
	switch($control):
		// AJAX: FORGET PASSWORD
		case 'forget-user':
			$query = $connect->single_result_array("SELECT id FROM access WHERE username = '{$username}'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "false":"true";
			echo $result;
		break;
		
		case 'forget-email':
			$query = $connect->single_result_array("SELECT id FROM access WHERE sendmail = '$user_email'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "false":"true";
			echo $result;
		break;
		// EOF AJAX: FORGET PASSWORD
		
		// AJAX: USER NAME
		case 'username':
			$query = $connect->single_result_array("SELECT id FROM access WHERE username = '$user_name'");
			$result = empty($query['id']) || $query['id'] == 'D' || $query['id'] == "" ? "true":"false";
			echo $result;
		break;
		// EOF AJAX: USER NAME
		
		// AJAX: EMAIL
		case 'email':
			$query = $connect->single_result_array("SELECT sendmail FROM access WHERE sendmail = '$user_email'");
			$result = empty($query['sendmail']) || $query['sendmail'] == 'D' || $query['sendmail'] == "" ? "true":"false";
			echo $result;
		break;
		// EOF AJAX: EMAIL
		
		// AJAX: EDIT PROFILE EMAIL
		case 'profile-email':
			$query = $connect->single_result_array("SELECT sendmail FROM access WHERE sendmail = '$user_email' AND id != '$id'");
			$result = empty($query['sendmail']) || $query['sendmail'] == 'D' || $query['sendmail'] == "" ? "true":"false";
			echo $result;
		break;
		
		// AJAX: OLD PASSWORD
		case 'old-password':
			$md5password = md5($old_password);
			$query = $connect->single_result_array("SELECT password FROM access WHERE id = '$id'");
			$result = $query['password'] == $md5password ? "true":"false";
			echo $result;
		break;
		// EOF AJAX: OLD PASSWORD
		
		// USER MANAGEMENT
		case 'manage':
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			$query_search = $filter_search != "" ? ' AND (t1.username LIKE "%'.$filter_search.'%" OR t1.sendmail LIKE "%'.$filter_search.'%") ' : ' ';
			
			$query_total = isset($sort_limit) ? $sort_limit : __LIMIT__;
			$sort_limit = $query_total;
			$page = isset($page) ? $page : '0';
			$query_page = $page*$sort_limit;
			$limit = $sort_limit == "all"? '':'LIMIT '.$query_page.','.$query_total;
			
			$query = "
				SELECT 
					t1.*,
					t2.*
				FROM access as t1
					LEFT JOIN access_status AS t2 ON t1.active = t2.access_status_id
				WHERE
					t1.id != '{$login_id}' "
					.$query_search;
			
			$order_by = " ORDER BY ".$filter_sort." ".$filter_dir." ".$limit;
			
			$query_max_list_count = $connect->count_records($query);	
			$query_list = $connect->get_array_result($query.$order_by);
			$query_list_count = count($query_list);
			
			// printr($query_list);
			
			$prev = $page == 'all' ? '0':($page)-1;
			$next = $page == 'all' ? '0':($page)+1;
			$max = $query_max_list_count / $query_total;
			$end = (int)$max;
			$end = $end == $max ? $end-1:$end;						
		?>
			<script type="text/javascript">
			function loadURL(page,bottom){
				var input_filter_search = $('#filter_search').val();
				var input_filter_sort = $('#filter_sort option:selected').val();
				var input_filter_dir = $('#filter_dir option:selected').val();					

				var input_sort_limit = bottom == 0 ? $('#sort_limit option:selected').val() : $('#sort_limit_bottom option:selected').val();
				input_sort_limit = input_sort_limit == undefined ? "<?=__LIMIT__?>":input_sort_limit;
				
				var getURL ="";
				getURL +="<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>"
				getURL +="&filter_search="+input_filter_search;
				getURL +="&filter_sort="+input_filter_sort;
				getURL +="&filter_dir="+input_filter_dir;
				getURL +="&sort_limit="+input_sort_limit;
				getURL += page==null ?"":"&page="+page;

				return getURL;
			}
			
            $(document).ready(function() {
                $('#checkbox').click(function(){
                    var isChecked = $(this).attr('checked')?true:false;
                    if(isChecked){
                        $("#multi-active").removeAttr('disabled');
                        $("#multi-delete").removeAttr('disabled');
                        $(".list").find("input:checkbox").attr('checked',$(this).is(":checked"));
                    } else {
                        $("#multi-active").attr('disabled','disabled');
                        $("#multi-delete").attr('disabled','disabled');
                        $(".list").find("input:checkbox").removeAttr('checked');
                    }
                });
                
                $('.checkbox').click(function(){
                    var isChecked = false;
                                                
                    $("input.checkbox").each( function() {
                        if ($(this).attr("checked") == 'checked'){
                            isChecked = true;
                        }
                    });
        
                    if (isChecked == true){
                        $("#multi-active").removeAttr('disabled');
                        $("#multi-delete").removeAttr('disabled');
                        $("#checkbox").attr('checked',"checked");
                    } else {
                        $("#multi-active").attr('disabled','disabled');
                        $("#multi-delete").attr('disabled','disabled');
                        $("#checkbox").removeAttr('checked');
                    }
                });
                
                $('.editPop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to edit user.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deletePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to delete user.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activatePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to activate user.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.delete').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Account deleted.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.active').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Account active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activeMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Set <img src="images/ico_checked.png" align="absmiddle" /> account(s) to active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deleteMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Delete <img src="images/ico_checked.png" align="absmiddle" /> account(s).', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
				
				// FILTER
				$('#filter_sort, #filter_dir').change(function() {
					ajaxLoad(loadURL(null,0),"GET");
				});
				
				$('#filter_sort, #filter_dir').keyup(function() {
					ajaxLoad(loadURL(null,0),"GET");
				});
				// EOF FILTER

				// SORT LIMIT
				$('#sort_limit').change(function() {
					ajaxLoad(loadURL(null,0),"GET");
				});
				
				$('#sort_limit').keyup(function() {
					ajaxLoad(loadURL(null,0),"GET");
				});
				
				$('#sort_limit_bottom').change(function() {
					ajaxLoad(loadURL(null,1),"GET");
				});
				
				$('#sort_limit_bottom').keyup(function() {
					ajaxLoad(loadURL(null,1),"GET");
				});
				// EOF SORT LIMIT
				
				// PAGINATION
				$('.nav-prev').click(function() {
					ajaxLoad(loadURL(<?=$prev?>,0),"GET");
				});
				
				$('.nav-next').click(function() {
					ajaxLoad(loadURL(<?=$next?>,0),"GET");
				});
				
				$('.nav-end').click(function() {
					ajaxLoad(loadURL(<?=$end?>,0),"GET");
				});
				
				$('.nav-first').click(function() {
					ajaxLoad(loadURL(0,0),"GET");
				});

				// ACTION BUTTON
				$('#btn-search').click(function() {
					ajaxLoad(loadURL(null,0),"GET");
				});
				
				$('#filter_search').bind('keypress', function(e) {
					var code = (e.keyCode ? e.keyCode : e.which);
					if(code == 13) { //Enter keycode
						ajaxLoad(loadURL(null,0),"GET");
					 }
				});
				
				$('#clear-search').click(function() {
					ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>","GET");
				});
				
				$('.add_user').click(function() {
					ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-user","GET");
				});
            });
            
            </script>
            <div style="width:100%;" align="left">
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                <tr class="line_20">
                    <td align="right" class="px_10" valign="top">
                    	<div class="float_left px_10" style="width:50%;" align="left">
	                        Filter:&nbsp;
	                        <input id="filter_search" name="filter_search" type="text" class="inputtext thin_inputtext" maxlength="50" value="<?=$filter_search?>" /> 
	                        <input type="button" class="button small_button" value="Search" id="btn-search"> 
	                        <input type="button" class="button small_button" value="Reset" id="clear-search">
	                        
	                        <div class="spacer_0 clean"><!-- SPACER --></div>
							<div style="margin-left: 28px;" class="green"><em>Search by Username or Email</em></div>
                        </div>
                        
                        <div class="float_right px_10" style="width:50%;">
	                        Sort:&nbsp;
	                        <select id="filter_sort" name="filter_sort" class="selectoption thin_select pt_8">
	                            <? for ($i=0;$i<$usersortdatacount;$i++): ?>
	                            <option value="<?=$usersortdata[$i]['value']?>" <?=$filter_sort==$usersortdata[$i]['value']?"selected=selected":""?>><?=$usersortdata[$i]['name']?></option>
	                            <? endfor; ?>
	                        </select>
	                        <select id="filter_dir" name="filter_dir" class="selectoption thin_select pt_8">
	                            <option value="ASC" <?=$filter_dir=="ASC"?"selected=selected":""?>>Ascending</option>
	                            <option value="DESC" <?=$filter_dir=="DESC"?"selected=selected":""?>>Descending</option>
	                        </select>
                        </div>
                    </td>
                </tr>
            </table>
            
            <form action="<?=__ROOT__?>/index.php?file=process&process=manage-user" method="post" enctype="multipart/form-data">
            <input type="hidden" value="<?=$QUERY_STRING?>" name="return_link" />
            
			<div style="width: 100%; height: 22px;" align="right" class="line_22">
            	<span class="wrap px_10"><b><?=$query_max_list_count?></b> Result(s) found</span>
            </div>
            
            <div class="spacer_5 clean"><!-- SPACER --></div>
            
            <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr class="line_20">
                    <td align="left" valign="top">
                    	<span class="px_10">Display #</span>
                    	<select id="sort_limit" name="sort_limit" class="selectoption thin_number_select pt_8 sort_limit">
                            <? for ($i=0;$i<$dataLimitcount;$i++): ?>
                            <option value="<?=$dataLimit[$i]['value']?>" <?=$sort_limit==$dataLimit[$i]['value']?"selected=selected":""?>><?=$dataLimit[$i]['name']?></option>
                            <? endfor; ?>
						</select>
						
						<input type="button" class="button small_button add_user" name="add_user" id="add_user" value="Add New User" />
                    </td>
                    <td align="right">
                    	<?=paginate($page,$prev,$next,$max,$end,"top")?>
                    </td>
                </tr>
            </table>

	        <div class="table_title" align="center">
            	<div align="left" class="px_16 float_left table_title_header">User Management</div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center">&nbsp;</th>
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center"></th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Username</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Email</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Last Login</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">No. of Login</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Created</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Action</th>
	    		</tr>
            <?
            if ($query_list_count > 0):
	            for($x=0;$x<$query_list_count;$x++):
	                // GENERAL INFORMATION
	                $user_id = $query_list[$x]['id'];
					$user_name = $query_list[$x]['username'];
	                $user_email = $query_list[$x]['sendmail'];
	                
	                // USER STATUS
					$user_status = $query_list[$x]['active'];
					$status = $user_status == 1 ? 'Active':'Inactive';
					$user_status_id = $query_list[$x]['access_status_id'];
	                $user_status_class = $query_list[$x]['access_class'];
					
	               	// LOGIN INFO
	               	$totallogin = $query_list[$x]['login'];
	               	$lastlogin = strtotime($query_list[$x]['last_login']);
	               	$lastlogin = getTimeDifference($lastlogin);
	                
	                // USER CREATED
	                $user_created = strtotime($query_list[$x]['created']);
					$user_created = date('F d, Y h:i:s A', $user_created);
	                
	                // BUBBLE INFO
					$info  = "<div style='width:250px;' class='pt_8'>";
	                $info .= "<b class='orange'>Alias:</b> $user_name <br />";
					$info .= "<b class='orange'>Email:</b> $user_email <br />";
	                
					$info .= "<div class='spacer_5 clean'><!-- SPACER --></div>";
					$info .= "<b class='orange'>Last login:</b> $lastlogin <br />";
					$info .= "<b class='orange'>No. of login:</b> $totallogin <br />";
					
					$info .= "<div class='spacer_5 clean'><!-- SPACER --></div>";
					$info .= "<b class='orange'>Status:</b> $status <br />";
					$info .= "<b class='orange'>Created:</b> $user_created <br />";
					$info .= "</div>";
					
					// printr($user_list);
 
	            ?>
	                <script type="text/javascript">
	                    $(document).ready(function() {
	                        $('#info_<?=$user_id?>').CreateBubblePopup({
	                            position: 'right',
	                            align: 'left',
	                            innerHtmlStyle: {color:'#FFFFFF', 'text-align':'left'},
	                            innerHtml: "<?=$info?>", 
	                            themeName: 'all-black',
	                            themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
	                        });
	                        
	                        $('#edit_<?=$user_id?>').click(function(){
								ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=edit-user&user_id=<?=$user_id?>","GET");
	                        });
	                    });
	                </script>
	                <tr class="line_20">
	                    <td width="1%" class="table_solid_left table_solid_right table_solid_bottom <?=$x==$query_list_count-1?'bottom-left-radius':''?>" align="center">
	                        <input type="checkbox" class="checkbox" name="action[checkbox][]" value="<?=$user_id?>" />
	                    </td>
	                    <td width="1%" class="table_solid_right table_solid_bottom" align="center">
                    		<div class="<?=$user_status_class?> block"></div>
						</td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=stringLimit($user_name)?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=stringLimit($user_email)?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$lastlogin?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$totallogin?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$user_created?></td>
						<td width="75" class="table_solid_right table_solid_bottom px_11 unselectable <?=$x==$query_list_count-1?'bottom-right-radius':''?>" align="center">
						<? if($user_status == '1'): ?>
	                        	<input type="submit" class="clean float_right deletePop ico ico_delete confirm" name="action[single-delete]" value="<?=$user_id?>" id="delete_<?=$user_id?>" title="Are you sure you want to delete this user?" />
	                        <? else: ?>
	                        	<input type="submit" class="clean float_right activatePop ico ico_active" name="action[single-active]" value="<?=$user_id?>" id="active_<?=$user_id?>" />
	                        <? endif; ?>
							<input type="button" class="clean float_right marg_right_5 editPop ico ico_edit" name="action[single-edit]" value="<?=$user_id?>" id="edit_<?=$user_id?>" />
							<input type="button" class="clean float_right marg_right_5 editPop ico ico_preview" name="info_<?=$user_id?>" id="info_<?=$user_id?>" />
						</td>
	                </tr>
	            <?
	            endfor;
	            ?>   
	            </table>
	            
	            <table width="100%" border="0" cellpadding="6" cellspacing="0">
	                <tr class="line_20">
	                    <td width="1.1%" align="center"><input type="checkbox" id="checkbox" /></td>
	                    <td align="left" class="px_10">
	                    	<span class="marg_right_10"><strong>Status:</strong></span>
	                        <input type="submit" class="clean activeMultiPop ico ico_active" name="action[multi-active]" id="multi-active" value="true" disabled="disabled" /> Active &nbsp;&nbsp; 
	                        <input type="submit" class="clean deleteMultiPop ico ico_delete confirm" name="action[multi-delete]" id="multi-delete" value="true" disabled="disabled" title="Are you sure you want to delete selected user(s)?" /> Delete &nbsp;&nbsp;
	                    </td>
	                    <td align="right">
	                    	<input type="button" class="button small_button add_user" name="add_user" id="add_user" value="Add New User" />
	                    </td>
	                </tr>
	            </table>
	            
			<? else: ?>
            	<table width="100%" border="0" cellpadding="6" cellspacing="0" class="list">
	                <tr class="line_20">
	                	<td align="center" class="table_solid_left table_solid_right table_solid_top table_solid_bottom error shadow pt_8 bottom-right-radius bottom-left-radius"><strong>No Result Found</strong></td>
	                </tr>
				</table>
            <? endif; ?>
            
            </form>
            </div>
        <?
		break;
		// EOF USER MANAGEMENT
		
		// ADD USER
		case 'add-user':
		?>
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=add-user" id="adduserForm">
        	<div style="width:100%;" align="left">
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Users : Add User</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">General Information:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="220" class="pad_left_15">Username: </td>
                        <td><input id="user_name" name="user_name" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td width="420"><label for="user_name" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Email: </td>
                        <td><input id="user_email" name="user_email" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="user_email" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Password: </td>
                        <td><input id="user_password" name="user_password" type="password" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="user_password" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Confirm Password: </td>
                        <td><input id="confirm_password" name="confirm_password" type="password" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="confirm_password" generated="false" class="error"></label></td>
                    </tr>
                </table>

                <div class="spacer_20 clean"><!-- SPACER --></div>
                
                <div style="width:100%;" align="left">
                <input name="back" id="back" type="button" value="Back" class="button" />
                <input name="clear" type="reset" value="Reset Form" class="button" />
                <input name="update" type="submit" value="Add User" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#adduserForm").validate({
					rules: {
						user_name : {
							required: true,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=username"
						},
						user_email : {
							required: true,
							email: true,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=email" 
						},
						user_password : {
							required: true
						},
						confirm_password : {
							required: true,
							equalTo: "#user_password"
						}
					},
					messages: {
						user_name : {
							required: "Please provide a username.",
							remote: "Username not available."
						},
						user_email : {
							required: "Please provide your email address.",
							email: "Please provide a valid email address.",
							remote: "Email already registered." 
						},
						user_password : {
							required: "Please provide your password"
						},
						confirm_password : {
							required: "Please retype your password",
							equalTo: "Password did not match."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);
			    });
			    
			    $('#back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=manage","GET");
			    });
			});
            </script>
        <?
		break;
		// EOF ADD USER
		
		
		// EDIT USER
		case 'edit-user':		
			$profile = $connect->single_result_array("SELECT * FROM access WHERE id = '{$user_id}'");
			
			$user_name = $profile['username'];
			$user_email = $profile['sendmail'];

			?>
			<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-user" id="editUserForm">
			<input type="hidden" name="user_id" value="<?=$user_id?>" />	
        	<div style="width:100%;" align="left">
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Users : Edit User</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">General Information:</th>
            		</tr>
            	</table>
                
                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td class="pad_left_15">Username: </td>
                        <td class="line_30" colspan="2"><?=$user_name?></td>
                    </tr>
                    <tr>
                    	<td width="220" class="pad_left_15">Email: </td>
                        <td><input id="user_email" name="user_email" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$user_email?>" /></td>
                        <td width="420"><label for="user_email" generated="false" class="error"></label></td>
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
                <input name="back" type="button" value="Back" class="button" id="back" />
                <input name="clear" type="reset" value="Reset Form" class="button" />
                <input name="save" type="submit" value="Save" class="button" />
                </div>
            </div>
            </form>
            <script type="text/javascript">
				$(document).ready(function() {
					$('#back').click(function(){
						ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=manage","GET");
					});
					
					$('input[type="reset"]').click(function(){
			            clearForm(this.form);
			        });
			        
					$("#editUserForm").validate({
						rules: {
							user_email : {
								required: true,
								email: true,
								remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=profile-email&id=<?=$user_id?>"
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
		// EOF EDIT USER
		
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>