		<?php
			$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
			$filter_sort = $filter_sort=="" ? "id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			$filter_type = $filter_type=="" || $filter_type=='0' ? "all":$filter_type;
			
			$query_search = $filter_search != "" ? ' AND (t1.firstname LIKE "%'.$filter_search.'%" OR t1.lastname LIKE "%'.$filter_search.'%" OR t1.empno LIKE "%'.$filter_search.'%") ' : ' ';
			$filter_type_query = $filter_type=="all" ? " ": ' AND t1.type = "'.$filter_type.'"';
			
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
	            	<div align="left" class="px_16 float_left table_title_header">Personnel Management</div>
				</div>
			
	            <table width="100%" border="0" cellpadding="5" cellspacing="0">
	            	<tr style="background-color:#D7D7D7;" class="line_20">
		        		<th width="1%" class="table_print_left table_print_top table_print_bottom px_11 darkgray unselectable" align="center">No.</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Firstname</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Lastname</th>
		        		<th width="1%" class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Position</th>
		        		<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Date Hired</th>
		        		<th width="1%" class="table_print_right table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Status</th>
		    		</tr>
	            <?
	            if ($query_list_count > 0):
		            for($x=0;$x<$query_list_count;$x++):
		                // GENERAL INFORMATION
		                $user_id = $query_list[$x]['id'];
		                $empno = $query_list[$x]['empno'];
						$firstname = $query_list[$x]['firstname'];
		                $lastname = $query_list[$x]['lastname'];
		                
		                // USER TYPE
		                $user_type = $query_list[$x]['type_name'];
		                
		                // USER STATUS
						$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
						
		                // USER HIRED
		                $user_hired = date('F d, Y',strtotime($query_list[$x]['hire']));
	 
		            ?>
	                <tr class="line_20">
						<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><?=$empno?></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=stringLimit($firstname)?></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($lastname)?></span></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=$user_type?></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=$user_hired?></td>
						<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=$status?></td>
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