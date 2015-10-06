			<?
			if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");
			
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
						
			$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "1":$filter_plate;
			//$filter_soa = $filter_soa=="" || $filter_soa=='0' ? "all":$filter_soa;
			$filter_from = $filter_from=="" ? date("Y").'-01-01':str_replace(' ','',$filter_from);
			$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
			$filter_type_query = $filter_type=="all" ? " ": ' AND t1.deduction_id = "'.$filter_type.'"';
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			//$filter_soa_query = $filter_soa=="all" ? " ": ' AND t1.soa = "'.$filter_soa.'"';
			$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
				DATE(t1.transaction_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'")
			) ';
			
			$from = strtotime($filter_from);
	        $date_from = date('F d, Y', $from);
	        $to = strtotime($filter_to);
	        $date_to = date('F d, Y', $to);       
	        $range = $date_from == $date_to ? $date_from:$date_from .' &mdash; '. $date_to;
			
			$query = "
				SELECT 
					t1.*,
					t2.plate,
					t3.location AS source_name,
					t4.location AS destination_name
				FROM transaction as t1
					LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
					LEFT JOIN location AS t3 ON t1.source = t3.id
					LEFT JOIN location AS t4 ON t1.destination = t4.id
				WHERE
					t1.paid = 1 AND t1.delivered = 1 AND t2.active = 1 "
					.$filter_plate_query
					.$filter_date_query;

			$order_by = " ORDER BY t1.transaction_date DESC ";

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
            	<div align="left" class="float_left table_title_header">Trucker Collection Statement for <?=$range?></div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">SOA No.</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Date</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">URC Document</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Source</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Destination</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">No. of CS</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Rate</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Total</th>
	    		</tr>
            <?
            if ($query_list_count > 0):
            	$total_transaction = 0;
            	$total_cs = 0;
            	            	
	            for($x=0;$x<$query_list_count;$x++):
	                // GENERAL INFORMATION
	                $id = $query_list[$x]['id'];
	                $plate = $query_list[$x]['plate'];
	                $soa = $query_list[$x]['soa'];
	                $urc_doc = $query_list[$x]['urc_doc'];
	                $source = $query_list[$x]['source_name'];
	                $destination = $query_list[$x]['destination_name'];
	                
	                $cs = $query_list[$x]['cs'];
	                $rate = $query_list[$x]['rate'];
	                $total = $cs * $rate;

	                $total_transaction = $query_list[$x]['active'] == 1 ? $total_transaction + $total:$total_transaction;
	                $total_cs = $query_list[$x]['active'] == 1 ? $total_cs + $cs:$total_cs;
	                
	                // CREATED
	                $transaction_date = strtotime($query_list[$x]['transaction_date']);
	                $date = date('F d, Y', $transaction_date);
 
	            ?>
	                <tr class="line_20">
						<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($soa)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=$date?></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($urc_doc)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$source?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$destination?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$plate?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($cs, 0, '', ',')?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($rate, 2, '.', ',')?></span></td>
						<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
	                </tr>
	            <?
	            endfor;
	            ?>   
	            	<tr class="line_20">
		            	<td class="table_print_right px_11 unselectable darkgray" align="right" colspan="6"><b>Total Collections:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_print_bottom px_11 unselectable darkgray" align="center"><b><?=number_format($total_cs, 0, '', ',')?></b></td>
		            	<td class="table_print_right px_11 unselectable  darkgray" align="left">&nbsp;</td>
		            	<td style="background-color:#D7D7D7;" class="table_print_bottom table_print_right px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_transaction, 2, '.', ',')?></b></td>
	            	</tr>
	            </table>
	            
			<? else: ?>
            	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	                <tr class="line_20">
	                	<td align="center" class="table_print_left table_print_right table_print_top table_print_bottom error shadow pt_8"><strong>No Result Found</strong></td>
	                </tr>
				</table>
            <? endif; ?>

	    	<div class="spacer_20 clean"><!-- SPACER --></div>
			
			<?
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			$filter_date_query = $filter_from=="" || $filter_to=="" ? " ": ' AND DATE(t1.consumption_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'") ';
			
			$query = "
				SELECT 
					t1.*,
					t2.plate
				FROM consumptions as t1
					LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
				WHERE
					t1.active = 1 AND t2.active = 1 "
					.$filter_plate_query
					.$filter_date_query;

			$order_by = " ORDER BY t1.consumption_date DESC ";

			$query_max_list_count = $connect->count_records($query);	
			$query_list = $connect->get_array_result($query.$order_by);
			$query_list_count = count($query_list);
			?>
			<div id="gas" class="gas"> 
                <div style="width:100%" align="center">
                    <div align="left" style="margin-left:15px; line-height: 42px;">Gasoline Consumption Details</div>
                </div>
			
                <table width="100%" border="0" cellpadding="5" cellspacing="0">
                    <tr style="background-color:#D7D7D7;" class="line_20">
                        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
                        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Date</th>
                        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Liters</th>
                        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Price Per Liter</th>
                        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Total</th>
                    </tr>
                    <?
                if ($query_list_count > 0):
                    $total_consumption = 0;
                    $total_liters = 0;
                                    
                    for($x=0;$x<$query_list_count;$x++):
                        // GENERAL INFORMATION
                        $plate = $query_list[$x]['plate'];
                        
                        $liters = $query_list[$x]['liters'];
                        $price = $query_list[$x]['price'];
                        $total = $liters * $price;
        
                        $total_consumption = $query_list[$x]['active'] == 1 ? $total_consumption + $total:$total_consumption;
                        $total_liters = $query_list[$x]['active'] == 1 ? $total_liters + $liters:$total_liters;
                        
                        // CREATED
                        $consumption_date = strtotime($query_list[$x]['consumption_date']);
                        $date = date('F d, Y', $consumption_date);
     
                    ?>
                        <tr class="line_20">
                            <td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$plate?></span></td>
                            <td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=$date?></td>
                            <td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($liters, 0, '', ',')?> L</span></td>
                            <td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($price, 2, '.', ',')?></span></td>
                            <td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
                        </tr>
                <?
                    endfor;
                ?>   
                        <tr class="line_20">
                            <td class="table_print_right px_11 unselectable darkgray" align="right" colspan="2"><b>Total Consumptions:</b></td>
                            <td style="background-color:#D7D7D7;" class="table_print_bottom px_11 unselectable darkgray" align="center"><b><?=number_format($total_liters, 0, '', ',')?> L</b></td>
                            <td class="table_print_right px_11 unselectable  darkgray" align="left">&nbsp;</td>
                            <td style="background-color:#D7D7D7;" class="table_print_bottom table_print_right px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_consumption, 2, '.', ',')?></b></td>
                        </tr>
                    </table>
                    
                <? else: ?>
                    <table width="100%" border="0" cellpadding="6" cellspacing="0">
                        <tr class="line_20">
                            <td align="center" class="table_print_left table_print_right table_print_bottom error shadow pt_8"><strong>No Result Found</strong></td>
                        </tr>
                    </table>
                <? endif; ?>
            
            	<div class="spacer_20 clean"><!-- SPACER --></div>
			</div>
			
	        <div style="width:100%" align="center">
            	<div align="left" style="margin-left:15px; line-height: 42px;">Deductions:</div>
			</div>
			
			<?
				$salestax = 0.02 * $total_transaction;
				$savings = 0.03 * $total_transaction;
				$fund = 0.01 * $total_transaction;
			?>
			
			<table width="100%" border="0" cellpadding="5" cellspacing="0">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Deduction</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Description</th>
	        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Price</th>
	    		</tr>
	    		<tr>
	    			<td class="table_print_right table_print_left table_print_bottom px_12 unselectable" align="center">Sales Tax</td>
	    			<td class="table_print_right table_print_bottom px_12 unselectable" align="center">2% Tax</td>
	    			<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($salestax, 2, '.', ',')?></span></td> 
	    		</tr>
	    		<tr>
	    			<td class="table_print_right table_print_left table_print_bottom px_12 unselectable" align="center">Savings</td>
	    			<td class="table_print_right table_print_bottom px_12 unselectable" align="center">3% Savings</td>
	    			<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($savings, 2, '.', ',')?></span></td> 
	    		</tr>
	    		<tr>
	    			<td class="table_print_right table_print_left table_print_bottom px_12 unselectable" align="center">Fund</td>
	    			<td class="table_print_right table_print_bottom px_12 unselectable" align="center">1% Fund</td>
	    			<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($fund, 2, '.', ',')?></span></td> 
	    		</tr>
	    		<tr>
	    			<td class="table_print_right table_print_left table_print_bottom px_12 unselectable" align="center">Gasoline Consumptions</td>
	    			<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=$range?></td>
	    			<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total_consumption, 2, '.', ',')?></span></td> 
	    		</tr>
	    		<?
				$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
				$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
					"'.$filter_from.'" <= t1.date_to AND "'.$filter_to.'" >= t1.date_from
				) ';
				
				$query = "
					SELECT 
						t1.*,
						t2.plate,
						t3.type_name,
						t4.firstname,
						t4.lastname
					FROM deduction as t1
						LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
						LEFT JOIN deduction_type AS t3 ON t1.deduction_id = t3.id
						LEFT JOIN personnel AS t4 ON t1.personnel_id = t4.id
					WHERE
						t1.active = 1 "
						.$filter_plate_query
						.$filter_date_query;
	
				$order_by = " ORDER BY t1.date_from DESC ";
	
				$query_max_list_count = $connect->count_records($query);	
				$query_list = $connect->get_array_result($query.$order_by);
				$query_list_count = count($query_list);
				
				/* requisition */
				
				$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
				$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
					DATE(t1.requisition_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'")
				) ';
			
				$query = "
					SELECT 
						t1.*,
						
						t2.name,
						t2.description,
						t2.unit_price,
						t2.unit,
						t2.stocks,
						
						t3.brand_name,
						
						(t1.qty * t2.unit_price) AS total
					FROM requisition as t1
						LEFT JOIN inventory AS t2 ON t1.stock_id = t2.id
						LEFT JOIN brand AS t3 ON t2.brand = t3.id
					WHERE
						t1.active = 1 "
						.$filter_plate_query
						.$filter_date_query;

				$order_by = " ORDER BY t1.requisition_date DESC ";
	
				$query_max_list_count = $connect->count_records($query);	
				$requisition_list = $connect->get_array_result($query.$order_by);
				$requisition_list_count = count($requisition_list);
				$total_requisition = 0;
				
				if ($query_list_count > 0):
            		$total_deduction = 0;
            	            	
		            for($x=0;$x<$query_list_count;$x++):
		            	// GENERAL INFORMATION	
		            	$type_name = $query_list[$x]['type_name'];
		            	$description = $query_list[$x]['description'];             
		                $total = $query_list[$x]['price'];
						
		                $total_deduction = $query_list[$x]['active'] == 1 ? $total_deduction + $total:$total_deduction;
		                
		                // CREATED
		                $date_from = strtotime($query_list[$x]['date_from']);
		                $date_to = strtotime($query_list[$x]['date_to']);
		                $date = date('F d, Y', $date_from) .' &mdash; '. date('F d, Y', $date_to);
	 					$description = $description == '' || $description == 'D' ? $date:$description;
	 					
	 					if ($query_list[$x]['deduction_id'] == 3):
	 						$description = 'Payroll for <i>'. $query_list[$x]['firstname'] .' '.$query_list[$x]['lastname'] . '</i><br />' .$description;
	 					endif;
		            	?>
		                <tr class="line_20">
							<td class="table_print_right table_print_left table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$type_name?></span></td>
							<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$description?></span></td>
							<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
		                </tr>
						<?
		            endfor;
					
					if ($requisition_list_count > 0):
 	
						for($x=0;$x<$requisition_list_count;$x++):
							// GENERAL INFORMATION	
							$name = $requisition_list[$x]['name'];
							$brand_name = $requisition_list[$x]['brand_name'];
							$type_name = $name . '(' . $brand_name .') ' . $requisition_list[$x]['description'];
							
							// CREATED
							$requisition_date = strtotime($requisition_list[$x]['requisition_date']);
							$date = date('F d, Y', $requisition_date);
							$unit_price = $requisition_list[$x]['unit_price'];
							$qty = $requisition_list[$x]['active'] == 1 ? $requisition_list[$x]['qty']:0;
							$price = ' &#8369; ' . number_format($unit_price, 2, '.', ',') . ' x '.$qty;
							$description = $price. ' on '.$date;
							           
							$total = $requisition_list[$x]['total'];
							
							$total_requisition = $requisition_list[$x]['active'] == 1 ? $total_requisition + $total:$total_requisition;
							?>
							<tr class="line_20">
								<td class="table_print_right table_print_left table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$type_name?></span></td>
								<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$description?></span></td>
								<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
							</tr>
							<?
						endfor;
					endif;

		            $total_deductions = $total_deduction + $total_consumption + $salestax + $savings + $fund + $total_requisition;
		            
	        		?>   
	            	<tr class="line_20">
		            	<td class="table_print_left table_print_bottom table_print_right px_11 unselectable darkgray" align="right" colspan="2"><b>Total Deductions:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_print_bottom table_print_right px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_deductions, 2, '.', ',')?></b></td>
	            	</tr>
	            	<tr class="line_20">
		            	<td style="background-color:#D7D7D7;" class="table_print_left table_print_bottom table_print_right px_11 unselectable darkgray" align="right" colspan="2"><b>Net Amount:</b></td>
		            	<td class="table_print_bottom table_print_right px_11 unselectable darkgray red" align="center"><b>&#8369; <?=number_format($total_transaction-$total_deductions, 2, '.', ',')?></b></td>
	            	</tr>
	            </table>
				<? 
				else: 
					if ($requisition_list_count > 0):
 	
						for($x=0;$x<$requisition_list_count;$x++):
							// GENERAL INFORMATION	
							$name = $requisition_list[$x]['name'];
							$brand_name = $requisition_list[$x]['brand_name'];
							$type_name = $name . '(' . $brand_name .') ' . $requisition_list[$x]['description'];
							
							// CREATED
							$requisition_date = strtotime($requisition_list[$x]['requisition_date']);
							$date = date('F d, Y', $requisition_date);
							$unit_price = $requisition_list[$x]['unit_price'];
							$qty = $requisition_list[$x]['active'] == 1 ? $requisition_list[$x]['qty']:0;
							$price = ' &#8369; ' . number_format($unit_price, 2, '.', ',') . ' x '.$qty;
							$description = $price. ' on '.$date;
							           
							$total = $requisition_list[$x]['total'];
							
							$total_requisition = $requisition_list[$x]['active'] == 1 ? $total_requisition + $total:$total_requisition;
							?>
							<tr class="line_20">
								<td class="table_print_right table_print_left table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$type_name?></span></td>
								<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$description?></span></td>
								<td class="table_print_right table_print_bottom px_12 table_print_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
							</tr>
							<?
						endfor;
					endif;
					
					$total_deductions = $total_deduction + $total_consumption + $salestax + $savings + $fund + $total_requisition;
				?>
					<tr class="line_20">
		            	<td class="table_print_left table_print_bottom table_print_right px_11 unselectable darkgray" align="right" colspan="2"><b>Total Deductions:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_print_bottom table_print_right px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_deductions, 2, '.', ',')?></b></td>
	            	</tr>
	            	<tr class="line_20">
		            	<td style="background-color:#D7D7D7;" class="table_print_left table_print_bottom table_print_right px_11 unselectable darkgray" align="right" colspan="2"><b>Net Amount:</b></td>
		            	<td class="table_print_bottom table_print_right px_11 unselectable darkgray red" align="center"><b>&#8369; <?=number_format($total_transaction-$total_deductions, 2, '.', ',')?></b></td>
	            	</tr>
				</table>
            	<? endif; ?>
            </div>
            	
            <div class="spacer_20 clean"><!-- SPACER --></div>
        <?