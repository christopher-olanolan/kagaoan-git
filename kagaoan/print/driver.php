		<?php
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "t2.id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			$query_search = $filter_search != "" ? ' AND (
				t1.plate LIKE "%'.$filter_search.'%" OR 
				t1.truck_model LIKE "%'.$filter_search.'%" OR 
				t1.truck_type LIKE "%'.$filter_search.'%" OR 
				t3.firstname LIKE "%'.$filter_search.'%" OR 
				t3.lastname LIKE "%'.$filter_search.'%" 
			) ' : ' ';
			
			$query = "
				SELECT 
					t1.*,
					t2.id AS truck_driver_id,
					t2.driver_id,
					t2.assigned,
					t2.active AS driver_status,
					t3.firstname,
					t3.lastname,
					t4.access_status_id,
					t4.access_class
				FROM truck as t1
					LEFT JOIN truck_driver AS t2 ON t1.id = t2.truck_id
					LEFT JOIN personnel AS t3 ON t2.driver_id = t3.id
					LEFT JOIN access_status AS t4 ON t2.active = t4.access_status_id
				WHERE
					t1.id != '' "
					.$query_search;

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
	            	<div align="left" class="px_16 float_left table_title_header">Driver Management</div>
				</div>
				
	            <table width="100%" border="0" cellpadding="5" cellspacing="0">
	            	<tr style="background-color:#D7D7D7;" class="line_20">
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Truck Model</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Truck Type</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Assigned Driver</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Date Assigned</th> 
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Status</th>
		    		</tr>
            <?
            if ($query_list_count > 0):
	            for($x=0;$x<$query_list_count;$x++):
	                // GENERAL INFORMATION
	                $truck_id =  $query_list[$x]['id'];
	                $id = $query_list[$x]['truck_driver_id'];
	                $driver_id =  $query_list[$x]['driver_id'];
	                $plate = $query_list[$x]['plate'];
	                $truck_type = $query_list[$x]['truck_type'];
	                $truck_model = $query_list[$x]['truck_model'];

	                if ($id == '' || $id == 'D'):
	                	$driver = "<em class='red'>No assigned driver!</em>";
	                	$created = "<em class='red'>No assigned date!</em>";
	                else:
	                	$driver_firstname = $query_list[$x]['firstname'];
	                	$driver_lastname = $query_list[$x]['lastname'];
	                	$driver = $driver_firstname . ' ' . $driver_lastname;
	                	
	                	// CREATED
		                $created = date('F d, Y',strtotime($query_list[$x]['assigned']));
	                endif;
	                
	                // STATUS
					$status = $query_list[$x]['driver_status'] == 1 ? 'Active':'Inactive';
 
	            ?>
	                <tr class="line_20">
						<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><?=$plate?></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($truck_model)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($truck_type)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($driver)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$created?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$status?></span></td>
	                </tr>
	            <? endfor; ?>   
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