<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
$query_search = $filter_search != "" ? ' AND (
	t1.plate LIKE "%'.$filter_search.'%" OR 
	t1.truck_model LIKE "%'.$filter_search.'%" OR 
	t1.truck_type LIKE "%'.$filter_search.'%" OR 
	t2.firstname LIKE "%'.$filter_search.'%" OR 
	t2.lastname LIKE "%'.$filter_search.'%" 
) ' : ' ';

$query = "
	SELECT 
		t1.*,
		t2.firstname AS o_firstname,
		t2.lastname AS o_lastname,
		t3.access_status_id,
		t3.access_class
	FROM truck as t1
		LEFT JOIN personnel AS t2 ON t1.operator = t2.id
		LEFT JOIN access_status AS t3 ON t1.active = t3.access_status_id
	WHERE
		t1.id != '' "
		.$query_search;

$order_by = $group_by . " ORDER BY ".$filter_sort." ".$filter_dir;

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
		<div align="left" class="px_16 float_left table_title_header">Trucking Management</div>
	</div>
	
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr style="background-color:#D7D7D7;" class="line_20">
			<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Truck Model</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Truck Type</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Truck Operator</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Status</th>
	    </tr>
		<?
		if ($query_list_count > 0):
			for($x=0;$x<$query_list_count;$x++):
				// GENERAL INFORMATION
				$id = $query_list[$x]['id'];
				$plate = $query_list[$x]['plate'];
				$truck_type = $query_list[$x]['truck_type'];
				$truck_model = $query_list[$x]['truck_model'];
				
				$owner_firstname = $query_list[$x]['o_firstname'];
				$owner_lastname = $query_list[$x]['o_lastname'];
				$owner = $owner_firstname . ' ' . $owner_lastname;
				
				// STATUS
				$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
				
				// CREATED
				$created = date('F d, Y', strtotime($query_list[$x]['d_create']));
				?>
				<tr class="line_20">
					<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><?=$plate?></td>
					<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($truck_model)?></span></td>
					<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($truck_type)?></span></td>
					<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($owner)?></span></td>
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
