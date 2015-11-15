<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;

$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
$filter_link  = "";
$filter_link .= $filter_search != '' ? "&filter_search=".$filter_search:"";
$filter_link .= $filter_sort != '' ? "&filter_sort=".$filter_sort:"";
$filter_link .= $filter_dir != '' ? "&filter_dir=".$filter_dir:"";
$filter_link .= $sort_limit != '' ? "&sort_limit=".$sort_limit:"";

$filter_link .= $filter_plate != '' ? "&filter_plate=".$filter_plate:"";
$filter_link .= $filter_from != '' ? "&filter_from=".$filter_from:"";
$filter_link .= $filter_to != '' ? "&filter_to=".$filter_to:"";

$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
(DATE(t1.shipment_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'"))
	OR ((DATE(t1.shipment_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'"))) 
)';
		
$query_search = $filter_search != "" ? ' AND (
	t1.shipment LIKE "%'.$filter_search.'%" OR
	t2.plate LIKE "%'.$filter_search.'%" OR 
	t3.location LIKE "%'.$filter_search.'%" OR
	t4.location LIKE "%'.$filter_search.'%"
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
		t3.location AS source_name,
		t4.location AS destination_name,
		t5.access_status_id,
		t5.access_class
	FROM shipment as t1
		LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
		LEFT JOIN location AS t3 ON t1.source = t3.id
		LEFT JOIN location AS t4 ON t1.destination = t4.id
		LEFT JOIN access_status AS t5 ON t1.active = t5.access_status_id
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
			<div align="left" class="px_16 float_left table_title_header">Shipment for <?=$range?></div>
		</div>
				
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr style="background-color:#D7D7D7;" class="line_20">
				<th class="table_print_top table_solid_bottom px_11 darkgray unselectable" align="center">Shipment No.</th>
				<th class="table_print_top table_solid_bottom px_11 darkgray unselectable" align="center">Date</th>
				<th class="table_print_top table_solid_bottom px_11 darkgray unselectable" align="center">Source</th>
				<th class="table_print_top table_solid_bottom px_11 darkgray unselectable" align="center">Destination</th>
				<th class="table_print_top table_solid_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
				<th class="table_print_top table_solid_bottom px_11 darkgray unselectable" align="center">Rate</th>
			</tr>
		
	<?
	if ($query_list_count > 0):
		$total_transaction = 0;
		$total_cs = 0;
			
		for($x=0;$x<$query_list_count;$x++):
		    // GENERAL INFORMATION
		    $id = $query_list[$x]['id'];
		    $plate = $query_list[$x]['plate'];
		    $shipment = $query_list[$x]['shipment'];
		    $source = $query_list[$x]['source_name'];
		    $destination = $query_list[$x]['destination_name'];
		    $rate = $query_list[$x]['rate'];
		    
		    // CREATED
		    $data_date = strtotime($query_list[$x]['shipment_date']);
		    $date = date('F d, Y', $data_date);
			?>
		    <tr class="line_20">
				<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($shipment)?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$date?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$source?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$destination?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$plate?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($rate, 2, '.', ',')?></span></td>
		    </tr>
		<? endfor; ?>   
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
