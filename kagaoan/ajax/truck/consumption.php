		<?php
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
			
			$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
			$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
			$filter_report = $filter_report=="" || $filter_report=='0' ? "all":$filter_report;
			$filter_year = $filter_year=="" ? date("Y"):$filter_year;
			$filter_month = $filter_month=="" ? date("m"):$filter_month;
			$filter_day = $filter_day=="" ? date("d"):$filter_day;
			
			$first_day = date("N",strtotime(date($filter_year.'-'.$filter_month.'-01')));
			$month_day = date("j",strtotime(date($filter_year.'-'.$filter_month.'-'.$filter_day)));
			$month_week = floor(($first_day + $month_day-1)/7) + 1; // week number of a given day and month
			
			$filter_week = $filter_week=="" ? $month_week:$filter_week;
			
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			
			$month_days = date("t",strtotime(date($filter_year.'-'.$filter_month.'-01'))); // no of days in a month
			$filter_weeks = floor(($first_day + $month_days-1)/7) + 1; // no of weeks in a month
			
			switch ($filter_report):
				case 1:
					// YEAR FILTER
					$filter_date_from = $filter_year.'-01-01';
					$filter_date_to = $filter_year.'-12-31';
				breaK;
				case 2:
					// MONTH FILTER
					$filter_date_from = $filter_year.'-'.$filter_month.'-01';
					$filter_date_to = $filter_year.'-'.$filter_month.'-'.date("t", strtotime(date($filter_year.'-'.$filter_month.'-01')));
				break;	
				case 3:
					// TODO: fix week filter
					$target_day = "sunday";
					
					switch ($filter_week):
						case 1:
							$day_string = "first ".$target_day;
						break;
						case 2:
							$day_string = "second ".$target_day;
						break;
						case 3:
							$day_string = "third ".$target_day;
						break;
						case 4:
							$day_string = "fourth ".$target_day;
						break;
						case 5:
							$day_string = "fifth ".$target_day;
						break;
						case 6:
							$day_string = "sixth ".$target_day;
						break;
						case 7:
							$day_string = "seventh ".$target_day;
						break;
					endswitch;
					
					$month_year = date('F Y', strtotime(date($filter_year.'-'.$filter_month.'-01')));
					$firstmonday = (int) date('d', strtotime($month_year." first ".$target_day)) -1;

					$dt = new DateTime();
					$dt->setDate($filter_year, date('m', strtotime($month_year." ".$day_string)), date('d', strtotime($month_year." ".$day_string)));
					$dt->modify('-'.$firstmonday.' days');
					$filter_from = $dt->format('Y-m-d');
					$getDay = $dt->format('d');
					
					$dt = new DateTime();
					$dt->setDate($filter_year, $filter_month, $getDay);
					$dt->modify('+6 days');
					
					if ($dt->format('m') != $filter_month):
						$dt->modify('-'.(int) $dt->format('d').' days');
					endif;
					
					$filter_to = $dt->format('Y-m-d');
					
					$filter_date_from = $filter_from;
					$filter_date_to = $filter_to;
				break;	
				case 4:
					// DAY FILTER
					$filter_date_from = $filter_year.'-'.$filter_month.'-'.$filter_day.' 00:00:00';
					$filter_date_to = $filter_year.'-'.$filter_month.'-'.$filter_day. ' 23:59:59';
				break;
				case 5:
					// DATE RANGE FILTER
					$filter_date_from = $filter_from;
					$filter_date_to = $filter_to;
				break;
				default:
					$filter_date_from = $filter_from;
					$filter_date_to = $filter_to;
				break;
			endswitch;
			
			$filter_from = $filter_date_from;
			$filter_to = $filter_date_to;
			
			$filter_date_query = $filter_report != 'all' ? ' AND DATE(t1.consumption_date) BETWEEN DATE("'.$filter_date_from.'") AND DATE("'.$filter_date_to.'") ':'';

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
					t3.access_status_id,
					t3.access_class
				FROM consumptions as t1
					LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
					LEFT JOIN access_status AS t3 ON t1.active = t3.access_status_id
				WHERE
					t1.id != '' "
					.$query_search
					.$filter_plate_query
					.$filter_date_query;

			// echo $query;
			
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
		?>
			<script type="text/javascript">
			function loadURL(page,bottom){
				var input_filter_search = $('#filter_search').val();
				var input_filter_sort = $('#filter_sort option:selected').val();
				var input_filter_dir = $('#filter_dir option:selected').val();					
				var input_filter_plate = $('#filter_plate option:selected').val();
				var input_filter_from = $('#filter_from').val();
				var input_filter_to = $('#filter_to').val();

				var input_filter_report = $('#filter_report option:selected').val();
				var input_filter_year = $('#filter_year option:selected').val();
				var input_filter_month = $('#filter_month option:selected').val();
				var input_filter_week = $('#filter_week').val();
				var input_filter_day = $('#filter_day option:selected').val();
								
				var input_sort_limit = bottom == 0 ? $('#sort_limit option:selected').val() : $('#sort_limit_bottom option:selected').val();
				input_sort_limit = input_sort_limit == undefined ? "<?=__LIMIT__?>":input_sort_limit;
				
				var getURL ="";
				getURL +="<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>"
				getURL +="&filter_search="+input_filter_search;
				getURL +="&filter_sort="+input_filter_sort;
				getURL +="&filter_dir="+input_filter_dir;
				getURL +="&sort_limit="+input_sort_limit;
				
				getURL +="&filter_plate="+input_filter_plate;
				getURL +="&filter_from="+input_filter_from;
				getURL +="&filter_to="+input_filter_to;

				getURL +="&filter_report="+input_filter_report;
				getURL +="&filter_year="+input_filter_year;
				getURL +="&filter_month="+input_filter_month;
				getURL +="&filter_week="+input_filter_week;
				getURL +="&filter_day="+input_filter_day;
				
				getURL += page==null ?"":"&page="+page;

				return getURL;
			}
			
            $(document).ready(function() {
				$('.download').click(function() {									
					var input_filter_search = $('#filter_search').val();
					var input_filter_sort = $('#filter_sort option:selected').val();
					var input_filter_dir = $('#filter_dir option:selected').val();					
					var input_filter_plate = $('#filter_plate option:selected').val();
					var input_filter_from = $('#filter_from').val();
					var input_filter_to = $('#filter_to').val();

					var input_filter_report = $('#filter_report option:selected').val();
					var input_filter_year = $('#filter_year option:selected').val();
					var input_filter_month = $('#filter_month option:selected').val();
					var input_filter_week = $('#filter_week').val();
					var input_filter_day = $('#filter_day option:selected').val();
					
					var getURL ="";
					getURL +="&filter_search="+input_filter_search;
					getURL +="&filter_sort="+input_filter_sort;
					getURL +="&filter_dir="+input_filter_dir;
					
					getURL +="&filter_plate="+input_filter_plate;
					getURL +="&filter_from="+input_filter_from;
					getURL +="&filter_to="+input_filter_to;

					getURL +="&filter_report="+input_filter_report
					getURL +="&filter_year="+input_filter_year;
					getURL +="&filter_month="+input_filter_month;
					getURL +="&filter_week="+input_filter_week;
					getURL +="&filter_day="+input_filter_day;

					window.location = "<?=__ROOT__?>/index.php?file=download&download=consumption"+getURL;
				});

				function checkChangeData(){
					var isDataChange = $('#isDataChange').val();

					console.log(isDataChange);
					return isDataChange;
				}
				
				$('.print').click(function(){
					var input_filter_search = $('#filter_search').val();
					var input_filter_sort = $('#filter_sort option:selected').val();
					var input_filter_dir = $('#filter_dir option:selected').val();					
					var input_filter_plate = $('#filter_plate option:selected').val();
					var input_filter_from = $('#filter_from').val();
					var input_filter_to = $('#filter_to').val();

					var input_filter_report = $('#filter_report option:selected').val();
					var input_filter_year = $('#filter_year option:selected').val();
					var input_filter_month = $('#filter_month option:selected').val();
					var input_filter_week = $('#filter_week').val();
					var input_filter_day = $('#filter_day option:selected').val();
					
					var getURL ="";
					getURL +="&filter_search="+input_filter_search;
					getURL +="&filter_sort="+input_filter_sort;
					getURL +="&filter_dir="+input_filter_dir;
					
					getURL +="&filter_plate="+input_filter_plate;
					getURL +="&filter_from="+input_filter_from;
					getURL +="&filter_to="+input_filter_to;

					getURL +="&filter_report="+input_filter_report
					getURL +="&filter_year="+input_filter_year;
					getURL +="&filter_month="+input_filter_month;
					getURL +="&filter_week="+input_filter_week;
					getURL +="&filter_day="+input_filter_day;

					var w = screen.width;
					var h = screen.height;
					var left = (screen.width/2)-(w/2);
					var top = (screen.height/2)-(h/2);

					console.log(checkChangeData());
					
					if (checkChangeData()){  
						window.open("<?=__ROOT__?>/index.php?file=print&print=consumption" + getURL, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=" + top + ", left=" + left + ", width=" + w + ", height=" + (h - 110));
					}
				});
				
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
                    innerHtml: 'Click to edit truck consumption.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });

                $('.addPop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to add truck consumption.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deletePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to delete truck consumption.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activatePop').CreateBubblePopup({
                    position: 'left',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Click to activate truck consumption.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.delete').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Truck consumption deleted.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('div.active').CreateBubblePopup({
                    position: 'right',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Truck consumption active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.activeMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Set <img src="images/ico_checked.png" align="absmiddle" /> truck consumption(s) to active.', 
                    themeName: 'all-black',
                    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
                });
                
                $('.deleteMultiPop').CreateBubblePopup({
                    position: 'top',
                    align: 'center',
                    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
                    innerHtml: 'Delete <img src="images/ico_checked.png" align="absmiddle" /> truck consumption(s).', 
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
				$('#btn-search, #btn-filter, .btn-filter').click(function() {
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
					ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-consumption","GET");
				});

				// $(".datepicker").datetimepicker( "option", "showButtonPanel", true );
				
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
			        },
			        beforeShow: function(e,t) {
		    			$('#ui-datepicker-div').removeClass('hide-calendar');
		    			$('#ui-datepicker-div').removeClass('HideTodayButton');
		    			$('#ui-datepicker-div').removeClass('MonthPicker');
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
		    		maxDate: maxdate,
		    		onSelect: function() {
			            var date = $(this).datepicker('getDate');
			            date.setDate(date.getDate());
			            $("#filter_from").datepicker( "option", "maxDate", date);
			        },
			        beforeShow: function(e,t) {
		    			$('#ui-datepicker-div').removeClass('hide-calendar');
		    			$('#ui-datepicker-div').removeClass('HideTodayButton');
		    			$('#ui-datepicker-div').removeClass('MonthPicker');
		    		}
		    	});

				$('#filter_month, #filter_year').change(function() {
					adjustFilterData();
				});

				$('#filter_month, #filter_year').keyup(function() {
					adjustFilterData();
				});

				function adjustFilterData(){
					$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-month-days&filter_year=" + $('#filter_year option:selected').val() + "&filter_month=" + $('#filter_month option:selected').val(), function(data){
						var option = "";
						
						for (x=0,i=1;x<parseInt(data);x++,i++){
							val = i<10 ? "0" + i:i;
							option += '<option value="' + val + '">' + i + '</option>';
    					}

						$('#filter_day').html(option);
					});

					$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-month-weeks&filter_year=" + $('#filter_year option:selected').val() + "&filter_month=" + $('#filter_month option:selected').val(), function(data){
						var option = "";
						
						for (x=0,i=1;x<parseInt(data);x++,i++){
							val = i<10 ? "0" + i:i;
							option += '<option value="' + val + '">' + i + '</option>';
    					}

						$('#filter_week').html(option);
					});
				}
					
				$('#filter_report').change(function() {
					filterReport();
				});
				
				$('#filter_report').keyup(function() {
					filterReport();
				});

				function filterReport(){
					var report = $('#filter_report option:selected').val();
					var btn_filter = $('#btn-filter');
					
					var filter_report_option = $('#filter_report_option');
					var filter_report_range = $('#filter_report_range');
					
					var filter_report_year = $('#filter_report_year');
					var filter_report_month = $('#filter_report_month');
					var filter_report_week = $('#filter_report_week');
					var filter_report_day = $('#filter_report_day');

					if (report == 'all'){
						btn_filter.removeClass('hidden').addClass('button small_button btn-filter');
						filter_report_option.addClass('hidden');
						filter_report_range.addClass('hidden');
					} else {
						btn_filter.addClass('hidden').removeClass('button small_button btn-filter');
						
					}

					if (report == 5){
						filter_report_range.removeClass('hidden');
					} else {
						filter_report_range.addClass('hidden');
					}

					if (report < 5 && report != 'all'){
						filter_report_option.removeClass('hidden');
					} else {
						filter_report_option.addClass('hidden');
					}
					
					if (report == 1){
						filter_report_year.removeClass('hidden');
						filter_report_month.addClass('hidden');
						filter_report_week.addClass('hidden');
						filter_report_day.addClass('hidden');
					}

					if (report == 2){
						filter_report_year.removeClass('hidden');
						filter_report_month.removeClass('hidden');
						filter_report_week.addClass('hidden');
						filter_report_day.addClass('hidden');
					}

					if (report == 3){
						filter_report_year.removeClass('hidden');
						filter_report_month.removeClass('hidden');
						filter_report_week.removeClass('hidden');
						filter_report_day.addClass('hidden');
					}

					if (report == 4){
						filter_report_year.removeClass('hidden');
						filter_report_month.removeClass('hidden');
						filter_report_week.addClass('hidden');
						filter_report_day.removeClass('hidden');
					}
				}
            });
            
            </script>
            <div style="width:100%;" align="left">
            <input type="hidden" id="isDataChange" value="0" />
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
	                            <? for ($i=0;$i<$consumptionsortdatacount;$i++): ?>
	                            <option value="<?=$consumptionsortdata[$i]['value']?>" <?=$filter_sort==$consumptionsortdata[$i]['value']?"selected=selected":""?>><?=$consumptionsortdata[$i]['name']?></option>
	                            <? endfor; ?>
	                        </select>
	                        <select id="filter_dir" name="filter_dir" class="selectoption thin_select pt_8">
	                            <option value="ASC" <?=$filter_dir=="ASC"?"selected=selected":""?>>Ascending</option>
	                            <option value="DESC" <?=$filter_dir=="DESC"?"selected=selected":""?>>Descending</option>
	                        </select>
                        </div>
                        
                        <div class="spacer_10 clean"><!-- SPACER --></div>

                        <div id="advanced" class="float_left" style="margin-left: 22px;" align="left">
                        	<div class="float_left px_10 marg_right_5 marg_left_5">
	                    		Plate No.:<br />
	                    		<?=$select_plate?>
	                    	</div>
	                    	<div class="float_left px_10 marg_right_5">
	                        	Date Range:<br />
	                        	<select id="filter_report" name="filter_report" class="selectoption thin_select pt_8">
	                            <? for ($i=0;$i<$dataReportcount;$i++): ?>
	                            	<option value="<?=$dataReport[$i]['value']?>" <?=$filter_report==$dataReport[$i]['value']?"selected=selected":""?>><?=$dataReport[$i]['name']?></option>
	                            <? endfor; ?>
	                        	</select>
	                        	<input type="button" class="<?=$filter_report != "all" ?'hidden':'button small_button btn-filter'?>" value="Go" id="btn-filter">
	                    	</div>
	                    	
	                    	<div class="spacer_10 clean"><!-- SPACER --></div>
	                    	
	                    	<div id="filter_report_option" class="float_left <?=$filter_report == '5' || $filter_report == 'all'?'hidden':''?>">	                    
	                    		<!-- YEAR FILTER -->
	                        	<div id="filter_report_year" class="float_left px_10 marg_right_5 marg_left_5">
		                        	Year:<br />
		                        	<select id="filter_year" name="filter_year" class="selectoption thin_year_select pt_8">
		                            <? for ($i=1970;$i<=date("Y");$i++): ?>
		                            	<option value="<?=$i?>" <?=$filter_year==$i?"selected=selected":""?>><?=$i?></option>
		                            <? endfor; ?>
		                        	</select>		                        			                    	
		                    	</div>
		                    	
		                    	<!-- MONTH FILTER -->
	                        	<div id="filter_report_month" class="float_left px_10 marg_right_5 <?=$filter_report < 2 ?'hidden':''?>">
		                        	Month:<br />
		                        	<select id="filter_month" name="filter_month" class="selectoption thin_select pt_8">
		                            <? for ($i=0;$i<$dataMonthZerocount;$i++): ?>
		                            	<option value="<?=$dataMonthZero[$i]['value']?>" <?=$filter_month==$dataMonthZero[$i]['value']?"selected=selected":""?>><?=$dataMonthZero[$i]['name']?></option>
		                            <? endfor; ?>
		                        	</select>
		                    	</div>
		                    	
		                    	<!-- WEEK FILTER -->
		                    	<div id="filter_report_week" class="float_left px_10 marg_right_5 <?=$filter_report != '3' ?'hidden':''?>">
		                        	Week:<br />
		                        	<select id="filter_week" name="filter_week" class="selectoption thin_number_select pt_8">
		                            <? 
		                            for ($i=1;$i<=$filter_weeks;$i++): 
		                            	?><option value="<?=$i?>" <?=$filter_week==$i?"selected=selected":""?>><?=$i?></option><?
		                            endfor; 
		                            ?>
		                            </select>
		                    	</div>
		                    	
		                    	<!-- DAY FILTER -->
		                    	<div id="filter_report_day" class="float_left px_10 marg_right_5 <?=$filter_report != '4' ?'hidden':''?>">
		                        	Day:<br />
		                        	<select id="filter_day" name="filter_day" class="selectoption thin_number_select pt_8">
		                            <? 
		                            for ($i=1;$i<=$month_days;$i++): 
		                            	?><option value="<?=$i?>" <?=$filter_day==$i?"selected=selected":""?>><?=$i?></option><?
		                            endfor; 
		                            ?>
		                            </select>
		                    	</div>
		                    	
		                    	<div class="float_left px_10 marg_right_5">
	                        		<br /> <input type="button" class="button small_button btn-filter" value="Go">
	                    		</div>
		                    	
		                    	<div class="spacer_10 clean"><!-- SPACER --></div>
	                    	</div>
                        	                        	
                        	<div id="filter_report_range" class="float_left <?=$filter_report < 5 || $filter_report == 'all'?'hidden':''?>">
		                        <div class="float_left px_10 marg_right_5 marg_left_5">
		                        	From:<br />
		                    		<input id="filter_from" name="filter_from" type="text" class="inputtext thin_mid_inputtext datepicker" maxlength="50" value="<?=$filter_from?>" />
		                    	</div>
		                    	<div class="float_left px_10 marg_right_5">
		                    		To:<br />
		                    		<input id="filter_to" name="filter_to" type="text" class="inputtext thin_mid_inputtext datepicker" maxlength="50" value="<?=$filter_to?>" /> 
		                    	</div>
		                    	
		                    	<div class="float_left px_10 marg_right_5">
	                        		<br /> <input type="button" class="button small_button btn-filter" value="Go">
	                    		</div>
		                    	<div class="spacer_5 clean"><!-- SPACER --></div>
	                    	</div>
                    	</div>
                    </td>
                </tr>
            </table>
            <form action="<?=__ROOT__?>/index.php?file=process&process=manage-consumption&filter_plate=<?=$filter_plate?>&filter_from=<?=$filter_from?>&filter_to=<?=$filter_to?>" method="post" enctype="multipart/form-data">
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
						
						<input type="button" class="button small_button add" name="add" id="add" value="New Diesel Consumption" />
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
            	<div align="left" class="px_16 float_left table_title_header">Diesel Consumption for <?=$range?></div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center">&nbsp;</th>
	        		<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center"></th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Date</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Liters</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Price Per Liter</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Total</th> 
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Action</th>
	    		</tr>
            <?
            if ($query_list_count > 0):
            	$total_consumption = 0;
            	$total_liters = 0;
            	$total_price = 0;
            	
	            for($x=0;$x<$query_list_count;$x++):
	                // GENERAL INFORMATION
	                $id = $query_list[$x]['id'];
	                $plate = $query_list[$x]['plate'];
	                $liters = $query_list[$x]['liters'];
	                $price = $query_list[$x]['price'];
	                $total = $liters * $price;

	                $total_consumption = $query_list[$x]['active'] == 1 ? $total_consumption + $total:$total_consumption;
	                $total_liters = $query_list[$x]['active'] == 1 ? $total_liters + $liters:$total_liters;
	                $total_price = $query_list[$x]['active'] == 1 ? $total_price + $price:$total_price;
	                
	                // CREATED
	                $consumption_date = strtotime($query_list[$x]['consumption_date']);
	                $date = date('F d, Y', $consumption_date);
	                
	                // STATUS
	                $consumption_status = $query_list[$x]['active'];
					$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
					$status_id = $query_list[$x]['access_status_id'];
	                $status_class = $query_list[$x]['access_class'];
					
	                // BUBBLE INFO
					$info  = "<div style='width:250px;' class='pt_8'>";
	                $info .= "<b class='orange'>Plate:</b> $plate <br />";
	                $info .= "<b class='orange'>Date:</b> $date <br />";
	                
					$info .= "<div class='spacer_5 clean'><!-- SPACER --></div>";
					$info .= "<b class='orange'>Liters:</b> $liters <br />";
					$info .= "<b class='orange'>Price per liter:</b> $price <br />";
					$info .= "<b class='orange'>Total:</b> $total <br />";
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
								ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=edit-consumption&id=<?=$id?>&filter_plate=<?=$filter_plate?>&filter_from=<?=$filter_from?>&filter_to=<?=$filter_to?>","GET");
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
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($liters, 2, '.', ',')?> L</span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($price, 2, '.', ',')?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
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
	            
	            $average_price = $total_price/$query_list_count;
	            ?>   
	            	<tr class="line_20">
		            	<td class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray bottom-left-radius" align="right" colspan="4"><b>Total Consumption:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_bottom px_11 unselectable darkgray" align="center"><b><?=number_format($total_liters, 2, '.', ',')?> L</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_left table_solid_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($average_price, 2, '.', ',')?></b></td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_left table_solid_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_consumption, 2, '.', ',')?></b></td>
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
	                    	<input type="button" class="button small_button add" name="add" id="add" value="New Diesel Consumption" />
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