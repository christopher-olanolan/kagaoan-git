<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
$query_search = $filter_search != "" ? ' AND (
	t1.material LIKE "%'.$filter_search.'%" OR
	t1.description LIKE "%'.$filter_search.'%"
) ' : ' ';

$query = "
	SELECT 
		t1.*,
		t2.access_status_id,
		t2.access_class
	FROM materials as t1
		LEFT JOIN access_status AS t2 ON t1.active = t2.access_status_id
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
		<div align="left" class="px_16 float_left table_title_header">Materials</div>
	</div>
	
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr style="background-color:#D7D7D7;" class="line_20">
			<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Item Code</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Description</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Gross Weight</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Volume</th>
	        <th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Status</th>
	    </tr>
		<?
		if ($query_list_count > 0):
			for($x=0;$x<$query_list_count;$x++):
				// GENERAL INFORMATION
				$id = $query_list[$x]['id'];
				$material = $query_list[$x]['material'];
			    $description = $query_list[$x]['description'];
			    $gross_weight = $query_list[$x]['gross_weight'];
				$volume = $query_list[$x]['volume'];
				
				// STATUS
				$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
				?>
				<tr class="line_20">
					<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><?=$material?></td>
					<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($description)?></span></td>
					<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($gross_weight, 2, '.', ',')?></span></td>
					<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($volume,3,'.',',')?></span></td>
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
