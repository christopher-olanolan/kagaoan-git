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
            	<div align="left" class="px_16 float_left table_title_header"><?=$filter_plate=="" || $filter_plate=='0' ? $filter_plate:""?> Deduction for <?=$range?></div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Date Range</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Type</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Description</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Price</th> 
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Action</th>
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
					$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
 
	            ?>
	                <tr class="line_20">
						<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><?=$plate?></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$date?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($type_name)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($description)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($price, 2, '.', ',')?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$status?></span></td> 
	                </tr>
	            <?
	            endfor;
	            ?>   
	            	<tr class="line_20">
		            	<td class="table_print_right px_11 unselectable darkgray" align="right" colspan="4"><b>Total Deduction:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_print_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_deduction, 2, '.', ',')?></b></td>
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