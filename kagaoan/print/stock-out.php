		<?php
			if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");
			
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			
			$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
			$filter_brand = $filter_brand=="" || $filter_brand=='0' ? "all":$filter_brand;
			$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
			$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
			$filter_brand_query = $filter_brand=="all" ? " ": ' AND t2.brand = "'.$filter_brand.'"';
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
				DATE(t1.requisition_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'")
			) ';
			
			$query_search = $filter_search != "" ? ' AND (
				t2.name LIKE "%'.$filter_search.'%" OR
				t3.brand_name LIKE "%'.$filter_search.'%" OR 
				t4.plate LIKE "%'.$filter_search.'%"
			) ' : ' ';
	          
			$from = strtotime($filter_from);
	        $date_from = date('F d, Y', $from);
	        $to = strtotime($filter_to);
	        $date_to = date('F d, Y', $to);       
	        $range = $date_from == $date_to ? $date_from:$date_from .' &mdash; '. $date_to;
			
			$query = "
				SELECT 
					t1.*,
					
					t2.name,
					t2.description,
					t2.unit_price,
					t2.unit,
					t2.stocks,
					
					t3.brand_name,
					t4.plate,
					
					t5.firstname,
					t5.lastname,
					
					t6.access_status_id,
					t6.access_class,
					
					(t1.qty * t2.unit_price) AS total
				FROM requisition as t1
					LEFT JOIN inventory AS t2 ON t1.stock_id = t2.id
					LEFT JOIN brand AS t3 ON t2.brand = t3.id
					LEFT JOIN truck AS t4 ON t1.truck_id = t4.id
					LEFT JOIN personnel AS t5 ON t1.personnel_id = t5.id
					LEFT JOIN access_status AS t6 ON t1.active = t6.access_status_id
				WHERE
					t1.id != '' "
					.$query_search
					.$filter_plate_query
					.$filter_brand_query
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
            	<div align="left" class="px_16 float_left table_title_header">Stock Out (<?=$range?>)</div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
            	<tr style="background-color:#D7D7D7;" class="line_20">
                    <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Date</th>
                    <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Plate</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Name</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Description</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Brand</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Price</th>
                    <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Quantity</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Stocks</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Total</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Status</th>
	    		</tr>
            <?
            if ($query_list_count > 0):
            	$total_requisition = 0;
            	            	
	            for($x=0;$x<$query_list_count;$x++):
	                // GENERAL INFORMATION
	                $id = $query_list[$x]['id'];
					$plate = $query_list[$x]['plate'];
	                $name = $query_list[$x]['name'];
	                $description = $query_list[$x]['description'];
	                $brand_name = $query_list[$x]['brand_name'];
					
					$released_by = $query_list[$x]['firstname'] .' '. $query_list[$x]['lastname'];
	                $unit_price = $query_list[$x]['unit_price'];
	                $unit = $query_list[$x]['unit'] == '' ? 'unit':$query_list[$x]['unit'];
	                $stocks = $query_list[$x]['stocks'];
					$qty = $query_list[$x]['active'] == 1 ? $query_list[$x]['qty']:0;
	                $total = $query_list[$x]['total'];

	                $total_requisition = $query_list[$x]['active'] == 1 ? $total_requisition + $total:$total_requisition;
	                
	                // CREATED
	                $requisition_date = strtotime($query_list[$x]['requisition_date']);
	                $date = date('F d, Y', $requisition_date);
	                
	                // STATUS
	                $item_status = $query_list[$x]['active'];
					$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
					$status_id = $query_list[$x]['access_status_id'];
	                $status_class = $query_list[$x]['access_class'];
 
	            ?>
	                <tr class="line_20">
                        <td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($date)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($plate)?></span></td>
                        <td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($name)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($description)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($brand_name)?></span></td>
                        <td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($unit_price, 2, '.', ',')?> per <?=$unit?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$qty?><? /* <?=$unit?> <?=$qty > 1 ? "s":"" */ ?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$stocks?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td>
                        <td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$status?></span></td>
	                </tr>
	            <?
	            endfor;
	            ?>   
	            	<tr class="line_20">
		            	<td class="table_print_right px_11 unselectable darkgray" align="right" colspan="8"><b>Total Requisition:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_print_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_requisition, 2, '.', ',')?></b></td>
		            	<td class="px_11 unselectable  darkgray" align="left">&nbsp;</td>
	            	</tr>
	            </table>
			</div>  <!-- END PRINT -->	            
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