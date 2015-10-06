		<?php
			if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");
		
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
			$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
			$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			$filter_date_query = $filter_from=="" || $filter_to=="" ? " ": ' AND DATE(t1.consumption_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'") ';
			
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

			$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

			$query_max_list_count = $connect->count_records($query);	
			$query_list = $connect->get_array_result($query.$order_by);
			$query_list_count = count($query_list);

		?>
			<script type="text/javascript">			
            $(document).ready(function() {
				$('.print').click(function(){
					$('.PrintArea').printArea({mode : "iframe"});
				});

				$('.close').click(function(){
					window.close();
				});
            });
            
            </script>
            <div style="width:100%;" align="left">

				<div class="spacer_5 clean"><!-- SPACER --></div>
				<input name="print" id="print" type="button" value="Print" class="small_button button print float_left" />
				<input name="close" id="close" type="button" value="Close" class="small_button button float_right close" />
				<div class="spacer_5 clean"><!-- SPACER --></div>
		
				<div id="PrintArea" class="PrintArea">
		        <div align="center">
	            	<div align="left" class="px_16 float_left table_title_header"><?=$filter_plate=="" || $filter_plate=='0' ? $filter_plate:""?> Diesel Consumption for <?=$range?></div>
				</div>
				
	            <table width="100%" border="0" cellpadding="5" cellspacing="0">
	            	<tr style="background-color:#D7D7D7;" class="line_20">
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Date</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Liters</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Price Per Liter</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Total</th> 
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Action</th>
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
						$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
	 
		            ?>
		                <tr class="line_20">
							<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><?=$plate?></td>
							<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$date?></span></td>
							<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($liters, 2, '.', ',')?> L</span></td>
							<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($price, 2, '.', ',')?></span></td>
							<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
							<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$status?></span></td> 
		                </tr>
		            <?
		            endfor;
		            
		            $average_price = $total_price/$query_list_count;
		            ?>   
		            	<tr class="line_20">
			            	<td class="table_print_right px_11 unselectable darkgray" align="right" colspan="2"><b>Total Consumption:</b></td>
			            	<td style="background-color:#D7D7D7;" class="table_print_right table_print_bottom px_11 unselectable darkgray" align="center"><b><?=number_format($total_liters, 2, '.', ',')?> L</b></td>
			            	<td style="background-color:#D7D7D7;" class="table_print_right table_print_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($average_price, 2, '.', ',')?></b></td>
			            	<td style="background-color:#D7D7D7;" class="table_print_right table_print_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_consumption, 2, '.', ',')?></b></td>
			            	<td class="px_11 unselectable  darkgray" align="left">&nbsp;</td>
		            	</tr>
		            </table>
		            </div> <!-- PRINT -->
	            <? else: ?>
						<table width="100%" border="0" cellpadding="6" cellspacing="0" class="list">
						    <tr class="line_20">
						    	<td align="center" class="table_print_left table_print_right table_print_top table_print_bottom error shadow pt_8"><strong>No Result Found</strong></td>
						    </tr>
						</table>
					</div> <!-- END PRINT -->
				<? endif; ?>
            </div>
        <?