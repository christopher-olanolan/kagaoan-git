		<?php
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			$filter_type = $filter_type=="" || $filter_type=='0' ? "all":$filter_type;
			
			$query_search = $filter_search != "" ? ' AND (t1.firstname LIKE "%'.$filter_search.'%" OR t1.lastname LIKE "%'.$filter_search.'%" OR t1.empno LIKE "%'.$filter_search.'%") ' : ' ';
			$filter_type_query = $filter_type=="all" ? " ": ' AND t1.type = "'.$filter_type.'"';
			
			$query_total = isset($sort_limit) ? $sort_limit : __LIMIT__;
			$sort_limit = $query_total;
			$page = isset($page) ? $page : '0';
			$query_page = $page*$sort_limit;
			$limit = $sort_limit == "all"? '':'LIMIT '.$query_page.','.$query_total;
			
			$query = "
				SELECT 
					t1.*,
					t2.*,
					t3.type_name
				FROM personnel as t1
					LEFT JOIN access_status AS t2 ON t1.active = t2.access_status_id
					LEFT JOIN personnel_type AS t3 ON t1.type = t3.id
				WHERE
					t1.id != '' "
					.$query_search
					.$filter_type_query;
			
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
			
			$select = new Select();
			$select_type =  $select->option_query(
				'personnel_type',				// table name
				'filter_type',  				// name='$name' 
				'filter_type',	 				// id='$id'
				'id',							// value='$value'
				'type_name',					// option name
				$filter_type,					// default selected value
				'active = "1"',					// query condition(s)  
				'type_name',					// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption thin_select pt_8',// css class
				'All',							// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);						
		?>
			<script type="text/javascript">
			function loadURL(page,bottom){
				var input_filter_search = $('#filter_search').val();
				var input_filter_sort = $('#filter_sort option:selected').val();
				var input_filter_dir = $('#filter_dir option:selected').val();					
				var input_filter_type = $('#filter_type option:selected').val();
				
				var input_sort_limit = bottom == 0 ? $('#sort_limit option:selected').val() : $('#sort_limit_bottom option:selected').val();
				input_sort_limit = input_sort_limit == undefined ? "<?=__LIMIT__?>":input_sort_limit;
				
				var getURL ="";
				getURL +="<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>"
				getURL +="&filter_search="+input_filter_search;
				getURL +="&filter_sort="+input_filter_sort;
				getURL +="&filter_dir="+input_filter_dir;
				getURL +="&filter_type="+input_filter_type;
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
                    innerHtml: 'Click to edit personnel.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deletePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to delete personnel.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activatePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to activate personnel.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.delete').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Personnel deleted.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.active').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Personnel active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activeMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Set <img src="images/ico_checked.png" align="absmiddle" /> personnel(s) to active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deleteMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Delete <img src="images/ico_checked.png" align="absmiddle" /> personnel(s).', 
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
				
				$('#btn-filter').click(function() {
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
					ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-personnel","GET");
				});
				
				$('#add-type').click(function() {
					$('#filter_type').val(0);
					
					if ($('#add_type_container').hasClass('hidden')){
						$('#add_type_container').removeClass('hidden');
						$('#edit_type_container').addClass('hidden');
					} else {
						$('#add_type_container').addClass('hidden');
					}
				});
				
				$('#filter_type').change(function() {
					if ($(this).val() != 0){
						var selected = $(this).find('option:selected').text();
						
						$('#edit_type_container').removeClass('hidden');
						$('#add_type_container').addClass('hidden');
						$('#edit_type').val(selected);
						$('#type_id').val($(this).val());
					} else {
						$('#edit_type_container').addClass('hidden');
						$('#add_type_container').addClass('hidden');
						$('#edit_type').val('');
						$('#type_id').val(0);
					}
				});
				
				$('#submit-add-type').click(function() {
					var add_type = $('#add_type').val();
					var getURL ="";
					getURL +="&type_name="+add_type;
					
					if (add_type != ''){
						window.location = "<?=__ROOT__?>/index.php?file=action&action=add-personnel-type"+getURL;
					} else {
						$.noti('error', 'Please enter new personnel type.');
					}
				});
				
				$('#submit-edit-type').click(function() {
					var edit_type = $('#edit_type').val();
					var type_id = $('#type_id').val();
					
					var getURL ="";
					getURL +="&type_name="+edit_type;
					getURL +="&type_id="+type_id;
					
					if (edit_type != ''){
						window.location = "<?=__ROOT__?>/index.php?file=action&action=edit-personnel-type"+getURL;
					} else {
						$.noti('error', 'Please enter new personnel type.');
					}
				});
				
				$('.download').click(function() {									
					var input_filter_search = $('#filter_search').val();
					var input_filter_sort = $('#filter_sort option:selected').val();
					var input_filter_dir = $('#filter_dir option:selected').val();
					var input_filter_type = $('#filter_type option:selected').val();
					
					var getURL ="";
					getURL +="&filter_search="+input_filter_search;
					getURL +="&filter_sort="+input_filter_sort;
					getURL +="&filter_dir="+input_filter_dir;
					getURL +="&filter_type="+input_filter_type;
				
					window.location = "<?=__ROOT__?>/index.php?file=download&download=personnel"+getURL;
				});
				
				$('.print').click(function(){
					var input_filter_search = $('#filter_search').val();
					var input_filter_sort = $('#filter_sort option:selected').val();
					var input_filter_dir = $('#filter_dir option:selected').val();
					var input_filter_type = $('#filter_type option:selected').val();
					
					var getURL ="";
					getURL +="&filter_search="+input_filter_search;
					getURL +="&filter_sort="+input_filter_sort;
					getURL +="&filter_dir="+input_filter_dir;
					getURL +="&filter_type="+input_filter_type;

					var w = screen.width;
					var h = screen.height;
					var left = (screen.width/2)-(w/2);
					var top = (screen.height/2)-(h/2);
					  
					window.open("<?=__ROOT__?>/index.php?file=print&print=personnel" + getURL, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=" + top + ", left=" + left + ", width=" + w + ", height=" + (h - 110));
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
							<div style="margin-left: 28px;" class="green"><em>Search by firstname, lastname or personnel no.</em></div>
                        </div>
                        
                        <div class="float_right px_10" style="width:50%;">
	                        Sort:&nbsp;
	                        <select id="filter_sort" name="filter_sort" class="selectoption thin_select pt_8">
	                            <? for ($i=0;$i<$personelsortdatacount;$i++): ?>
	                            <option value="<?=$personelsortdata[$i]['value']?>" <?=$filter_sort==$personelsortdata[$i]['value']?"selected=selected":""?>><?=$personelsortdata[$i]['name']?></option>
	                            <? endfor; ?>
	                        </select>
	                        <select id="filter_dir" name="filter_dir" class="selectoption thin_select pt_8">
	                            <option value="ASC" <?=$filter_dir=="ASC"?"selected=selected":""?>>Ascending</option>
	                            <option value="DESC" <?=$filter_dir=="DESC"?"selected=selected":""?>>Descending</option>
	                        </select>
                        </div>
                        
                        <div class="spacer_20 clean"><!-- SPACER --></div>
                        
                        <div id="advanced" class="float_left" style="margin-left: 22px;" align="left">
	                    	<div class="float_left px_10 marg_right_5 marg_left_5">
	                        	Personnel Type:<br />
	                    		<?=$select_type?> 
                                <input type="button" class="button small_button" value="Go" id="btn-filter"> 
	                    		<input type="button" class="button small_button" value="+ Add Type" id="add-type">
                                <span id="add_type_container" class="hidden">
                                    <input id="add_type" name="add_type" type="text" class="inputtext thin_inputtext" maxlength="50" value="" placeholder="Add personnel type..." />
                                    <input type="button" class="button small_button" value="Submit" id="submit-add-type">
                                </span>
                                <span id="edit_type_container" class="hidden">
                                	<input type="hidden" id="type_id" name="type_id">
                                    <input id="edit_type" name="edit_type" type="text" class="inputtext thin_inputtext" maxlength="50" value="" />
                                    <input type="button" class="button small_button" value="Edit" id="submit-edit-type">
                                </span>
	                    	</div>
	                    	<div class="spacer_5 clean"><!-- SPACER --></div>
                    	</div>
                    </td>
                </tr>
            </table>
            
            <form action="<?=__ROOT__?>/index.php?file=process&process=manage-personnel" method="post" enctype="multipart/form-data">
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
						
						<input type="button" class="button small_button add_user" name="add_user" id="add_user" value="Add New Personnel" />
                        <input type="button" class="button small_button download" value="Download" id="download">
						<input name="print" id="print" type="button" value="Print" class="small_button button print" />
                    </td>
                    <td align="right">
                    	<?=paginate($page,$prev,$next,$max,$end,"top")?>
                    </td>
                </tr>
            </table>

			<div id="PrintArea" class="PrintArea">
	        <div class="table_title" align="center">
            	<div align="left" class="px_16 float_left table_title_header">Personnel Management</div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center">&nbsp;</th>
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center"></th>
	        		<th width="1%" class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">No.</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Firstname</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Lastname</th>
	        		<th width="1%" class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Position</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Date Hired</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Action</th>
	    		</tr>
            <?
            if ($query_list_count > 0):
	            for($x=0;$x<$query_list_count;$x++):
	                // GENERAL INFORMATION
	                $user_id = $query_list[$x]['id'];
	                $empno = $query_list[$x]['empno'];
					$firstname = $query_list[$x]['firstname'];
	                $lastname = $query_list[$x]['lastname'];
	                $user_email = $query_list[$x]['sendmail'];
	                
	                // USER TYPE
	                $user_type = $query_list[$x]['type_name'];
	                
	                // USER STATUS
					$user_status = $query_list[$x]['active'];
					$status = $user_status == 1 ? 'Active':'Inactive';
					$user_status_id = $query_list[$x]['access_status_id'];
	                $user_status_class = $query_list[$x]['access_class'];
					
	                // USER CREATED
	                $user_hired = strtotime($query_list[$x]['hire']);
					$user_hired = date('F d, Y', $user_hired);
	                $user_created = strtotime($query_list[$x]['created']);
					$user_created = date('F d, Y', $user_created);
					
	                // BUBBLE INFO
					$info  = "<div style='width:250px;' class='pt_8'>";
	                $info .= "<b class='orange'>Name:</b> $firstname $lastname <br />";
					$info .= $user_email != "" ? "<b class='orange'>Email:</b> $user_email <br />" : "";
	                
					$info .= "<div class='spacer_5 clean'><!-- SPACER --></div>";
					$info .= "<b class='orange'>Type:</b> $user_type <br />";
					$info .= "<b class='orange'>Status:</b> $status <br />";
					$info .= "<b class='orange'>Hire Date:</b> $user_hired <br />";
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
								ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=edit-personnel&empno=<?=$empno?>","GET");
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
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$empno?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=stringLimit($firstname)?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($lastname)?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$user_type?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$user_hired?></td>
						<td width="75" class="table_solid_right table_solid_bottom px_11 unselectable <?=$x==$query_list_count-1?'bottom-right-radius':''?>" align="center">
						<? if($user_status == '1'): ?>
	                        	<input type="submit" class="clean float_right deletePop ico ico_delete confirm" name="action[single-delete]" value="<?=$user_id?>" id="delete_<?=$user_id?>" title="Are you sure you want to delete this personnel?" />
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
	            </div> <!-- PRINT -->
                
	            <table width="100%" border="0" cellpadding="6" cellspacing="0">
	                <tr class="line_20">
	                    <td width="1.1%" align="center"><input type="checkbox" id="checkbox" /></td>
	                    <td align="left" class="px_10">
	                    	<span class="marg_right_10"><strong>Status:</strong></span>
	                        <input type="submit" class="clean activeMultiPop ico ico_active" name="action[multi-active]" id="multi-active" value="true" disabled="disabled" /> Active &nbsp;&nbsp; 
	                        <input type="submit" class="clean deleteMultiPop ico ico_delete confirm" name="action[multi-delete]" id="multi-delete" value="true" disabled="disabled" title="Are you sure you want to delete selected user(s)?" /> Delete &nbsp;&nbsp;
	                    </td>
	                    <td align="right">
	                    	<input type="button" class="button small_button add_user" name="add_user" id="add_user" value="Add New Personnel" />
                            <input type="button" class="button small_button download" value="Download" id="download">
							<input name="print" id="print" type="button" value="Print" class="small_button button print" />
	                    </td>
	                </tr>
	            </table>
	            
			<? else: ?>
            	<table width="100%" border="0" cellpadding="6" cellspacing="0" class="list">
	                <tr class="line_20">
	                	<td align="center" class="table_solid_left table_solid_right table_solid_top table_solid_bottom error shadow pt_8 bottom-right-radius bottom-left-radius"><strong>No Result Found</strong></td>
	                </tr>
				</table>
                </div> <!-- PRINT -->
            <? endif; ?>
            
            </form>
            </div>
        <?