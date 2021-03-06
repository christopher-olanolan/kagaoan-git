			<?
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
						
			$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "1":$filter_plate;
			//$filter_soa = $filter_soa=="" || $filter_soa=='0' ? "all":$filter_soa;
			$filter_from = $filter_from=="" ? date("Y").'-01-01':str_replace(' ','',$filter_from);
			$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
			$filter_type_query = $filter_type=="all" ? " ": ' AND t1.deduction_id = "'.$filter_type.'"';
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			//$filter_soa_query = $filter_soa=="all" ? " ": ' AND t1.soa = "'.$filter_soa.'"';
			$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
			(DATE(t1.transaction_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'"))
				OR ((DATE(t1.delivered_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'"))) 
			)';
			
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
					t1.paid > 1 AND t1.delivered = 1 AND t2.active = 1 "
					.$filter_plate_query
					.$filter_date_query;

			$order_by = " ORDER BY t1.transaction_date DESC ";

			$query_max_list_count = $connect->count_records($query);	
			$query_list = $connect->get_array_result($query.$order_by);
			$query_list_count = count($query_list);

			// printr($query_list);	

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
				'Select plate no.',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
			$select_soa =  $select->option_query(
				'transaction', 					// table name
				'filter_soa',  					// name='$name' 
				'filter_soa',	 				// id='$id'
				'soa',							// value='$value'
				'soa',							// option name
				$filter_soa,					// default selected value
				'',								// query condition(s)  
				'soa',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption thin_select pt_8',// css class
				'All',							// default null option name 'Choose option...'	
				'0',							// select type 1 = multiple or 0 = single
				'1'								// distinct
			);
		?>
			<script type="text/javascript">
			function loadURL(page,bottom){
				var input_filter_plate = $('#filter_plate option:selected').val();
				// var input_filter_soa = $('#filter_soa option:selected').val();
				var input_filter_from = $('#filter_from').val();
				var input_filter_to = $('#filter_to').val();
				
				var getURL ="";
				getURL +="<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>"
				// getURL +="&filter_soa="+input_filter_soa;
				getURL +="&filter_plate="+input_filter_plate;
				getURL +="&filter_from="+input_filter_from;
				getURL +="&filter_to="+input_filter_to;

				return getURL;
			}
			
            $(document).ready(function() {				
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

				$('#btn-filter').click(function() {
					ajaxLoad(loadURL(null,0),"GET");
				});

				$('.btn-details').click(function() {
					if ($('.gas').hasClass('hidden')){
						$('.gas').removeClass('hidden');
						$('.btn-details').val('Hide');
					} else {
						$('.gas').addClass('hidden');
						$('.btn-details').val('Details');
					}	
				});
				
				$('.download').click(function() {
					var input_filter_plate = $('#filter_plate option:selected').val();
					// var input_filter_soa = $('#filter_soa option:selected').val();
					var input_filter_from = $('#filter_from').val();
					var input_filter_to = $('#filter_to').val();
					
					var getURL ="";
					// getURL +="&filter_soa="+input_filter_soa;
					getURL +="&filter_plate="+input_filter_plate;
					getURL +="&filter_from="+input_filter_from;
					getURL +="&filter_to="+input_filter_to;
				
					window.location = "<?=__ROOT__?>/index.php?file=download&download=collection-statement"+getURL;
				});
				
				$('.print').click(function(){
					var input_filter_plate = $('#filter_plate option:selected').val();
					// var input_filter_soa = $('#filter_soa option:selected').val();
					var input_filter_from = $('#filter_from').val();
					var input_filter_to = $('#filter_to').val();
					
					var getURL ="";
					// getURL +="&filter_soa="+input_filter_soa;
					getURL +="&filter_plate="+input_filter_plate;
					getURL +="&filter_from="+input_filter_from;
					getURL +="&filter_to="+input_filter_to;

					var w = screen.width;
					var h = screen.height;
					var left = (screen.width/2)-(w/2);
					var top = (screen.height/2)-(h/2);
					  
					window.open("<?=__ROOT__?>/index.php?file=print&print=collection" + getURL, "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=" + top + ", left=" + left + ", width=" + w + ", height=" + (h - 110));
				});
            });
            
            </script>
            <div style="width:100%;" align="left">
            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                <tr class="line_20">
                    <td align="right" class="px_10" valign="top">

                        <div id="advanced" class="float_left" align="left">
                        	<div class="float_left px_10 marg_right_5 marg_left_5">
	                    		Plate No.:<br />
	                    		<?=$select_plate?>
	                    	</div>
	                    	
	                        <div class="float_left px_10 marg_right_5">
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
                        
                        <div class="float_right px_10 marg_right_5 marg_left_5 marg_top_25">
                            <input type="button" class="button small_button download" value="Download" id="download">
                            <input name="print" id="print" type="button" value="Print" class="small_button button print" />
                        </div>
                    </td>
                </tr>
            </table>

			<div class="spacer_20 clean"><!-- SPACER --></div>
			
            <div id="PrintArea" class="PrintArea">
	        <div class="table_title" align="center">
            	<div align="left" class="float_left table_title_header">Trucker Collection Statement for <?=$range?></div>
			</div>
			
            <table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">SOA No.</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Date</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">URC Document</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Source</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Destination</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">No. of CS</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Rate</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Total</th>
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
						<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($soa)?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><?=$date?></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($urc_doc)?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$source?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$destination?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$plate?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($cs, 0, '', ',')?></span></td>
						<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($rate, 2, '.', ',')?></span></td>
						<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
	                </tr>
	            <?
	            endfor;
	            ?>   
	            	<tr class="line_20">
		            	<td class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray bottom-left-radius" align="right" colspan="6"><b>Total Collections:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_bottom px_11 unselectable darkgray" align="center"><b><?=number_format($total_cs, 0, '', ',')?></b></td>
		            	<td class="table_solid_right table_solid_bottom px_11 unselectable  darkgray" align="left">&nbsp;</td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_bottom table_solid_right bottom-right-radius px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_transaction, 2, '.', ',')?></b></td>
	            	</tr>
	            </table>
	            
			<? else: ?>
            	<table width="100%" border="0" cellpadding="6" cellspacing="0" class="list">
	                <tr class="line_20">
	                	<td align="center" class="table_solid_left table_solid_right table_solid_top table_solid_bottom error shadow pt_8 bottom-right-radius bottom-left-radius"><strong>No Result Found</strong></td>
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
			
			// printr($query_list);
			?>
			<div id="gas" class="gas hidden"> 
                <div style="width:100%" class="table_title"align="center">
                    <div align="left" style="margin-left:15px; line-height: 42px;">Gasoline Consumption Details: &nbsp;&nbsp;&nbsp;<input type="button" class="button small_button btn-details" value="Hide" id="btn-details"></div>
                </div>
			
                <table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
                    <tr style="background-color:#D7D7D7;" class="line_20">
                        <th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
                        <th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Date</th>
                        <th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Liters</th>
                        <th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Price Per Liter</th>
                        <th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Total</th>
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
                            <td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$plate?></span></td>
                            <td class="table_solid_bottom px_12 unselectable" align="center"><?=$date?></td>
                            <td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($liters, 0, '', ',')?> L</span></td>
                            <td class="table_solid_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($price, 2, '.', ',')?></span></td>
                            <td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
                        </tr>
                <?
                    endfor;
                ?>   
                        <tr class="line_20">
                            <td class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray bottom-left-radius" align="right" colspan="2"><b>Total Consumptions:</b></td>
                            <td style="background-color:#D7D7D7;" class="table_solid_bottom px_11 unselectable darkgray" align="center"><b><?=number_format($total_liters, 0, '', ',')?> L</b></td>
                            <td class="table_solid_right table_solid_bottom px_11 unselectable  darkgray" align="left">&nbsp;</td>
                            <td style="background-color:#D7D7D7;" class="table_solid_bottom table_solid_right bottom-right-radius px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_consumption, 2, '.', ',')?></b></td>
                        </tr>
                    </table>
                    
                <? else: ?>
                    <table width="100%" border="0" cellpadding="6" cellspacing="0" class="list">
                        <tr class="line_20">
                            <td align="center" class="table_solid_left table_solid_right table_solid_top table_solid_bottom error shadow pt_8 bottom-right-radius bottom-left-radius"><strong>No Result Found</strong></td>
                        </tr>
                    </table>
                <? endif; ?>
            
            	<div class="spacer_20 clean"><!-- SPACER --></div>
			</div>
			
	        <div style="width:100%" class="table_title"align="center">
            	<div align="left" style="margin-left:15px; line-height: 42px;">Deductions:</div>
			</div>
			
			<?
				$settings = $connect->get_array_result("SELECT * FROM settings");
				
				$sales_percent = $settings[0]['value'];
				$sales_name = $settings[0]['display_name'];
				$sales_value = $settings[0]['display_value'];
				
				$savings_percent = $settings[1]['value'];
				$savings_name = $settings[1]['display_name'];
				$savings_value = $settings[1]['display_value'];
				
				$fund_percent = $settings[2]['value'];
				$fund_name = $settings[2]['display_name'];
				$fund_value = $settings[2]['display_value'];
			
				$salestax = $sales_percent * $total_transaction;
				$savings = $savings_percent * $total_transaction;
				$fund = $fund_percent * $total_transaction;
			?>
			
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
            	<tr style="background-color:#D7D7D7;" class="line_20">
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Deduction</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Description</th>
	        		<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Price</th>
	    		</tr>
	    		<tr>
	    			<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><?=$sales_name?></td>
	    			<td class="table_solid_bottom px_12 unselectable" align="center"><?=$sales_value?></td>
	    			<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($salestax, 2, '.', ',')?></span></td> 
	    		</tr>
	    		<tr>
	    			<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><?=$savings_name?></td>
	    			<td class="table_solid_bottom px_12 unselectable" align="center"><?=$savings_value?></td>
	    			<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($savings, 2, '.', ',')?></span></td> 
	    		</tr>
	    		<tr>
	    			<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><?=$fund_name?></td>
	    			<td class="table_solid_bottom px_12 unselectable" align="center"><?=$fund_value?></td>
	    			<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($fund, 2, '.', ',')?></span></td> 
	    		</tr>
	    		<tr>
	    			<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center">Gasoline Consumptions <input type="button" class="button small_button btn-details" value="Details" id="btn-details"></td>
	    			<td class="table_solid_bottom px_12 unselectable" align="center"><?=$range?></td>
	    			<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total_consumption, 2, '.', ',')?></span></td> 
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
				
				// printr($query_list);
				
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
							<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$type_name?></span></td>
							<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$description?></span></td>
							<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
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
								<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$type_name?></span></td>
								<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$description?></span></td>
								<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
							</tr>
							<?
						endfor;
					endif;

		            $total_deductions = $total_deduction + $total_consumption + $salestax + $savings + $fund + $total_requisition;
		            
	        		?>   
	            	<tr class="line_20">
		            	<td class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray" align="right" colspan="2"><b>Total Deductions:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_bottom table_solid_right px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_deductions, 2, '.', ',')?></b></td>
	            	</tr>
	            	<tr class="line_20">
		            	<td style="background-color:#D7D7D7;" class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray bottom-left-radius" align="right" colspan="2"><b>Net Amount:</b></td>
		            	<td class="table_solid_bottom table_solid_right bottom-right-radius px_11 unselectable darkgray red" align="center"><b>&#8369; <?=number_format($total_transaction-$total_deductions, 2, '.', ',')?></b></td>
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
								<td class="table_solid_left table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$type_name?></span></td>
								<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$description?></span></td>
								<td class="table_solid_bottom px_12 table_solid_right unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td> 
							</tr>
							<?
						endfor;
					endif;
					
					$total_deductions = $total_deduction + $total_consumption + $salestax + $savings + $fund + $total_requisition;
				?>
					<tr class="line_20">
		            	<td class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray" align="right" colspan="2"><b>Total Deductions:</b></td>
		            	<td style="background-color:#D7D7D7;" class="table_solid_bottom table_solid_right px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_deductions, 2, '.', ',')?></b></td>
	            	</tr>
	            	<tr class="line_20">
		            	<td style="background-color:#D7D7D7;" class="table_solid_left table_solid_bottom table_solid_right px_11 unselectable darkgray bottom-left-radius" align="right" colspan="2"><b>Net Amount:</b></td>
		            	<td class="table_solid_bottom table_solid_right bottom-right-radius px_11 unselectable darkgray red" align="center"><b>&#8369; <?=number_format($total_transaction-$total_deductions, 2, '.', ',')?></b></td>
	            	</tr>
				</table>
            	<? endif; ?>
            </div>
            	
            <div class="spacer_20 clean"><!-- SPACER --></div>
            
            <div class="float_right px_10 marg_right_5 marg_left_5">
                <input type="button" class="button small_button download" value="Download" id="download">
                <input name="print" id="print" type="button" value="Print" class="small_button button print" />
            </div>
                    
            <div class="spacer_20 clean"><!-- SPACER --></div>
        <?