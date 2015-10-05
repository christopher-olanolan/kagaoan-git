			<?php
			// MANAGE DEDUCTION
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			
			$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
			$filter_type = $filter_type=="" || $filter_type=='0' ? "all":$filter_type;
			$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
			$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
			$filter_link  = "";
			$filter_link .= $filter_search != '' ? "&filter_search=".$filter_search:"";
			$filter_link .= $filter_sort != '' ? "&filter_sort=".$filter_sort:"";
			$filter_link .= $sort_limit != '' ? "&sort_limit=".$sort_limit:"";
			$filter_link .= $filter_dir != '' ? "&filter_dir=".$filter_dir:"";
			$filter_link .= $filter_plate != '' ? "&filter_plate=".$filter_plate:"";
			$filter_link .= $filter_type != '' ? "&filter_type=".$filter_type:"";
			$filter_link .= $filter_from != '' ? "&filter_from=".$filter_from:"";
			$filter_link .= $filter_to != '' ? "&filter_to=".$filter_to:"";
			
			$filter_type_query = $filter_type=="all" ? " ": ' AND t1.deduction_id = "'.$filter_type.'"';
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
				"'.$filter_from.'" <= t1.date_to AND "'.$filter_to.'" >= t1.date_from
			) ';
			
			$query_search = $filter_search != "" ? ' AND (
				t2.plate LIKE "%'.$filter_search.'%"
			) ' : ' ';
			
			$from = strtotime($filter_from);
	        $date_from = date('F d, Y', $from);
	        $to = strtotime($filter_to);
	        $date_to = date('F d, Y', $to);       
	        $range = $date_from == $date_to ? $date_from:$date_from .' &mdash; '. $date_to;
	             
			$query_total = isset($sort_limit) ? $sort_limit : __LIMIT__;
			$sort_limit = $query_total;
			$page = isset($page) ? $page : '0';
			$query_page = $page*$sort_limit;
			$limit = $sort_limit == "all"? '':'LIMIT '.$query_page.','.$query_total;
			
			$query = "
				SELECT 
					t1.*,
					t2.plate,
					t3.type_name,
					t4.access_status_id,
					t4.access_class
				FROM deduction as t1
					LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
					LEFT JOIN deduction_type AS t3 ON t1.deduction_id = t3.id
					LEFT JOIN access_status AS t4 ON t1.active = t4.access_status_id
				WHERE
					t1.id != '' "
					.$query_search
					.$filter_plate_query
					.$filter_type_query
					.$filter_date_query;

			$order_by = " ORDER BY ".$filter_sort." ".$filter_dir." ".$limit;

			$query_max_list_count = $connect->count_records($query);	
			$query_list = $connect->get_array_result($query.$order_by);
			$query_list_count = count($query_list);

			// printr($query_list);
			// echo $query.$order_by;
			
			$prev = $page == 'all' ? '0':($page)-1;
			$next = $page == 'all' ? '0':($page)+1;
			$max = $query_max_list_count / $query_total;
			$end = (int)$max;
			$end = $end == $max ? $end-1:$end;		

			$select = new Select();
			$select_plate =  $select->option_query(
				'truck', 						// table name
				'filter_plate',  				// name='$name' 
				'filter_plate', 				// id='$id'
				'id',							// value='$value'
				'plate',						// option name
				$filter_plate,					// default selected value
				'active = "1"',					// query condition(s)  
				'plate',						// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption thin_select pt_8',// css class
				'All',							// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
			$select_type =  $select->option_query(
				'deduction_type', 				// table name
				'filter_type',  				// name='$name' 
				'filter_type', 					// id='$id'
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
				var input_filter_plate = $('#filter_plate option:selected').val();
				var input_filter_from = $('#filter_from').val();
				var input_filter_to = $('#filter_to').val();
				
				var input_sort_limit = bottom == 0 ? $('#sort_limit option:selected').val() : $('#sort_limit_bottom option:selected').val();
				input_sort_limit = input_sort_limit == undefined ? "<?=__LIMIT__?>":input_sort_limit;
				
				var getURL ="";
				getURL +="<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>"
				getURL +="&filter_search="+input_filter_search;
				getURL +="&filter_sort="+input_filter_sort;
				getURL +="&filter_dir="+input_filter_dir;
				getURL +="&sort_limit="+input_sort_limit;

				getURL +="&filter_type="+input_filter_type;
				getURL +="&filter_plate="+input_filter_plate;
				getURL +="&filter_from="+input_filter_from;
				getURL +="&filter_to="+input_filter_to;
				
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
                    innerHtml: 'Click to edit deduction.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });

                $('.addPop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to add deduction.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deletePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to delete deduction.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activatePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to activate deduction.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.delete').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Truck deduction deleted.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.active').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Truck deduction active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activeMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Set <img src="images/ico_checked.png" align="absmiddle" /> deduction(s) to active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deleteMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Delete <img src="images/ico_checked.png" align="absmiddle" /> deduction(s).', 
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
				$('#btn-search, #btn-filter').click(function() {
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
				
				$('.add').click(function() {
					ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-deduction","GET");
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
						window.location = "<?=__ROOT__?>/index.php?file=action&action=add-deduction-type"+getURL;
					} else {
						$.noti('error', 'Please enter new deduction type.');
					}
				});
				
				$('#submit-edit-type').click(function() {
					var edit_type = $('#edit_type').val();
					var type_id = $('#type_id').val();
					
					var getURL ="";
					getURL +="&type_name="+edit_type;
					getURL +="&type_id="+type_id;
					
					if (edit_type != ''){
						window.location = "<?=__ROOT__?>/index.php?file=action&action=edit-deduction-type"+getURL;
					} else {
						$.noti('error', 'Please enter new deduction type.');
					}
				});

				// $(".datepicker").datetimepicker( "option", "showButtonPanel", true );
				
				$('.download').click(function() {				
					var input_filter_search = $('#filter_search').val();
					var input_filter_sort = $('#filter_sort option:selected').val();
					var input_filter_dir = $('#filter_dir option:selected').val();	
	
					var input_filter_type = $('#filter_type option:selected').val();
					var input_filter_plate = $('#filter_plate option:selected').val();
					var input_filter_from = $('#filter_from').val();
					var input_filter_to = $('#filter_to').val();
					
					var getURL ="";
					getURL +="&filter_search="+input_filter_search;
					getURL +="&filter_sort="+input_filter_sort;
					getURL +="&filter_dir="+input_filter_dir;
	
					getURL +="&filter_type="+input_filter_type;
					getURL +="&filter_plate="+input_filter_plate;
					getURL +="&filter_from="+input_filter_from;
					getURL +="&filter_to="+input_filter_to;
				
					window.location = "<?=__ROOT__?>/index.php?file=download&download=deduction"+getURL;
				});
				
				$('.print').click(function(){
					var input_filter_search = $('#filter_search').val();
					var input_filter_sort = $('#filter_sort option:selected').val();
					var input_filter_dir = $('#filter_dir option:selected').val();	
	
					var input_filter_type = $('#filter_type option:selected').val();
					var input_filter_plate = $('#filter_plate option:selected').val();
					var input_filter_from = $('#filter_from').val();
					var input_filter_to = $('#filter_to').val();
					
					var getURL ="";
					getURL +="&filter_search="+input_filter_search;
					getURL +="&filter_sort="+input_filter_sort;
					getURL +="&filter_dir="+input_filter_dir;
	
					getURL +="&filter_type="+input_filter_type;
					getURL +="&filter_plate="+input_filter_plate;
					getURL +="&filter_from="+input_filter_from;
					getURL +="&filter_to="+input_filter_to;

					var w = screen.width;
					var h = screen.height;
					var left = (screen.width/2)-(w/2);
					var top = (screen.height/2)-(h/2);
					  
					window.open("<?=__ROOT__?>/index.php?file=print&print=deduction" + getURL, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=" + top + ", left=" + left + ", width=" + w + ", height=" + (h - 110));
				});
				
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1;
				var yyyy = today.getFullYear();
				var maxdate = yyyy + '-' + mm + '-' + dd;
				    
				$("#filter_from").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false,
		    		maxDate: $("#filter_to").val(),
		    		onSelect: function() {
			            var date = $(this).datepicker('getDate');
			            date.setDate(date.getDate());
			            $("#filter_to").datepicker( "option", "minDate", date);
			        }
		    	});

				$("#filter_to").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false,
		    		minDate: $("#filter_from").val(),
		    		onSelect: function() {
			            var date = $(this).datepicker('getDate');
			            date.setDate(date.getDate());
			            $("#filter_from").datepicker( "option", "maxDate", date);
			        }
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
							<div style="margin-left: 28px;" class="green"><em>Search plate no.</em></div>
                        </div>
                        
                        <div class="float_right px_10" style="width:50%;">
	                        Sort:&nbsp;
	                        <select id="filter_sort" name="filter_sort" class="selectoption thin_select pt_8">
	                            <? for ($i=0;$i<$deductionsortdatacount;$i++): ?>
	                            <option value="<?=$deductionsortdata[$i]['value']?>" <?=$filter_sort==$deductionsortdata[$i]['value']?"selected=selected":""?>><?=$deductionsortdata[$i]['name']?></option>
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
	                    		Plate No.:<br />
	                    		<?=$select_plate?>
	                    	</div>
	                    	<div class="float_left px_10 marg_right_5">
	                        	Deduction Type:<br />
	                    		<?=$select_type?> 
	                    		<input type="button" class="button small_button" value="+ Add deduction type" id="add-type">
                                <span id="add_type_container" class="hidden">
                                    <input id="add_type" name="add_type" type="text" class="inputtext thin_inputtext" maxlength="50" value="" placeholder="Add deduction type..." />
                                    <input type="button" class="button small_button" value="Add" id="submit-add-type">
                                </span>
                                <span id="edit_type_container" class="hidden">
                                	<input type="hidden" id="type_id" name="type_id">
                                    <input id="edit_type" name="edit_type" type="text" class="inputtext thin_inputtext" maxlength="50" value="" />
                                    <input type="button" class="button small_button" value="Edit" id="submit-edit-type">
                                </span>
	                    	</div>
	                    	
	                    	<div class="spacer_5 clean"><!-- SPACER --></div>
	                    	
	                        <div class="float_left px_10 marg_right_5 marg_left_5">
	                        	From:<br />
	                    		<input id="filter_from" name="filter_from" type="text" class="inputtext thin_mid_inputtext datepicker" maxlength="50" value="<?=$filter_from?>" />
	                    	</div>
	                    	<div class="float_left px_10 marg_right_5">
	                    		To:<br />
	                    		<input id="filter_to" name="filter_to" type="text" class="inputtext thin_mid_inputtext datepicker" maxlength="50" value="<?=$filter_to?>" /> 
	                    		<input type="button" class="button small_button" value="Go" id="btn-filter"> 
	                    	</div>
	                    	<div class="spacer_5 clean"><!-- SPACER --></div>
                    	</div>
                    </td>
                </tr>
            </table>
            <form action="<?=__ROOT__?>/index.php?file=process&process=activate" method="post" enctype="multipart/form-data">
            <input type="hidden" value="&panel=transaction&section=deduction" name="return_link" />
            <input type="hidden" value="<?=$filter_link?>" name="filter_link" />
            <input type="hidden" value="deduction" name="table" />
            
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
						
						<input type="button" class="button small_button add" name="add" id="add" value="New Deduction" />
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
            	<div align="left" class="px_16 float_left table_title_header">Deduction for <?=$range?></div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center">&nbsp;</th>
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center"></th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Date Range</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Type</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Description</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Price</th> 
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Action</th>
	    		</tr>
            <?
            if ($query_list_count > 0):
            	$total_deduction = 0;
            	
	            for($x=0;$x<$query_list_count;$x++):
	                // GENERAL INFORMATION
	                $id = $query_list[$x]['id'];
	                $plate = $query_list[$x]['plate'];
	                $type_name = $query_list[$x]['type_name'];
	                $description = $query_list[$x]['description'];
	                $price = $query_list[$x]['price'];

	                $total_deduction = $query_list[$x]['active'] == 1 ? $total_deduction + $price:$total_deduction;
	                
	                // CREATED
	                $date_from = strtotime($query_list[$x]['date_from']);
	                $date_to = strtotime($query_list[$x]['date_to']);
	                $date = $date_from == $date_to ? date('F d, Y', $date_from):date('F d, Y', $date_from) .' to '. date('F d, Y', $date_to);
	                
	                // STATUS
	                $consumption_status = $query_list[$x]['active'];
					$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
					$status_id = $query_list[$x]['access_status_id'];
	                $status_class = $query_list[$x]['access_class'];
					
	                // BUBBLE INFO
					$info  = "<div style='width:250px;' class='pt_8'>";
	                $info .= "<b class='orange'>Plate:</b> $plate <br />";
	                $info .= "<b class='orange'>Date Range:</b> $date <br />";
	                
					$info .= "<div class='spacer_5 clean'><!-- SPACER --></div>";
					$info .= "<b class='orange'>Type:</b> $type_name <br />";
					$info .= $description == "" ? "":"<b class='orange'>Description:</b> $description <br />";
					$info .= "<b class='orange'>Price:</b> &#8369; " . number_format($price, 2, '.', ',') . "<br />";
					$info .= "</div>";
 
	            ?>
	                <script type="text/javascript">
	                    $(document).ready(function() {
	                        $('#info_<?=$id?>').CreateBubblePopup({
	                            position: 'right',
	                            align: 'left',
	                            innerHtmlStyle: {color:'#FFFFFF', 'text-align':'left'},
	                            innerHtml: "<?=$info?>", 
	                            themeName: 'all-black',
	                            themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
	                        });
	                        
	                        $('#edit_<?=$id?>').click(function(){
								ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=edit-deduction&id=<?=$id?>&filter_plate=<?=$filter_plate?>&filter_type=<?=$filter_type?>&filter_from=<?=$filter_from?>&filter_to=<?=$filter_to?>","GET");
	                        });
	                    });
	                </script>
	                <tr class="line_20">
	                    <td width="1%" class="table_solid_left table_solid_right table_solid_bottom" align="center">
	                        <input type="checkbox" class="checkbox" name="action[checkbox][]" value="<?=$id?>" />
	                    </td>
	                    <td width="1%" class="table_solid_right table_solid_bottom" align="center">
                    		<div class="<?=$status_class?> block"></div>
						</td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$plate?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$date?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($type_name)?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($description)?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($price, 2, '.', ',')?></span></td> 
						<td width="75" class="table_solid_right table_solid_bottom px_11 unselectable" align="center">
							<? 
								if($consumption_status == '1'): 
									?><input type="submit" class="clean float_right deletePop ico ico_delete confirm" name="action[single-delete]" value="<?=$id?>" id="delete_<?=$id?>" title="Are you sure you want to delete this data?" /><? 
								else: 
									?><input type="submit" class="clean float_right activatePop ico ico_active" name="action[single-active]" value="<?=$id?>" id="active_<?=$id?>" /><? 
	                        	endif; 
	                        ?>
							<input type="button" class="clean float_right marg_right_5 editPop ico ico_edit" name="action[single-edit]" value="<?=$id?>" id="edit_<?=$id?>" />
							<input type="button" class="clean float_right marg_right_5 editPop ico ico_preview" name="info_<?=$id?>" id="info_<?=$id?>" />
						</td>
	                </tr>
	            <?
	            endfor;
	            ?>   
	            	<tr class="line_20">
		            	<td class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray bottom-left-radius" align="right" colspan="6"><b>Total Deduction:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_deduction, 2, '.', ',')?></b></td>
		            	<td class="table_solid_right table_solid_bottom px_11 unselectable  darkgray bottom-right-radius" align="left">&nbsp;</td>
	            	</tr>
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
	                    	<input type="button" class="button small_button add" name="add" id="add" value="New Deduction" />
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
		// EOF DEDUCTION