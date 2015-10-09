<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;

$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_soa = $filter_soa=="" || $filter_soa=='0' ? "all":$filter_soa;
$filter_delivered = $filter_delivered=="" ? "all":$filter_delivered;
$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);

$filter_type_query = $filter_type=="all" ? " ": ' AND t1.deduction_id = "'.$filter_type.'"';
$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
$filter_soa_query = $filter_soa=="all" ? " ": ' AND t1.soa = "'.$filter_soa.'"';
$filter_delivered_query = $filter_delivered=="all" ? " ": ' AND t1.delivered = "'.$filter_delivered.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
(DATE(t1.transaction_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'"))
	OR ((DATE(t1.delivered_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'"))) 
)';
		
$query_search = $filter_search != "" ? ' AND (
	t1.soa LIKE "%'.$filter_search.'%" OR
	t1.urc_doc LIKE "%'.$filter_search.'%" OR
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
	FROM transaction as t1
		LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
		LEFT JOIN location AS t3 ON t1.source = t3.id
		LEFT JOIN location AS t4 ON t1.destination = t4.id
		LEFT JOIN access_status AS t5 ON t1.active = t5.access_status_id
	WHERE
		t1.id != '' "
		.$query_search
		.$filter_plate_query
		.$filter_soa_query
		.$filter_delivered_query
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
			<div align="left" class="px_16 float_left table_title_header">Transaction for <?=$range?></div>
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
				<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Status</th>
				<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Action</th>
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
		    $transaction_date = date('F d, Y', strtotime($query_list[$x]['transaction_date']));
		    
		    // STATUS
		    $delivery_status = $query_list[$x]['delivered'] == 1 ? 'Delivered':'In-transit';
			$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';	 
			?>
		    <tr class="line_20">
				<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($soa)?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><?=$transaction_date?></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($urc_doc)?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$source?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$destination?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$plate?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=number_format($cs, 0, '', ',')?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($rate, 2, '.', ',')?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($total, 2, '.', ',')?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$delivery_status?></span></td> 
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$status?></span></td> 
		    </tr>
		<? endfor; ?>   
			<tr class="line_20">
				<td class="table_print_right px_11 unselectable darkgray" align="right" colspan="6"><b>Total Transaction:</b></td>
				<td style="background-color:#D7D7D7;" class="table_print_right table_print_bottom px_11 unselectable darkgray" align="center"><b><?=number_format($total_cs, 0, '', ',')?></b></td>
				<td class="table_print_right px_11 unselectable darkgray" align="left">&nbsp;</td>
				<td style="background-color:#D7D7D7;" class="table_print_right table_print_bottom px_11 unselectable darkgray" align="center"><b>&#8369; <?=number_format($total_transaction, 2, '.', ',')?></b></td>
				<td class="px_11 unselectable darkgray" align="left" colspan="2">&nbsp;</td>
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
