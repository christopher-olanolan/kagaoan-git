<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
	
$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_soa = $filter_soa=="" || $filter_soa=='0' ? "all":$filter_soa;
$filter_delivered == "" ? "all":$filter_delivered;
$filter_from = $filter_from=="" ? date("Y-m-").'01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
	
$filter_type_query = $filter_type=="all" ? " ": ' AND t1.deduction_id = "'.$filter_type.'"';
$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
$filter_soa_query = $filter_soa=="all" ? " ": ' AND t1.soa = "'.$filter_soa.'"';
$filter_delivered_query = $filter_delivered=="all" ? " ": ' AND t1.delivered = "'.$filter_delivered.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
	DATE(t1.transaction_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'")
) ';
	
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
$range = $date_from == $date_to ? $date_from:$date_from .' - '. $date_to;

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
		t1.active = 1 "
.$query_search
.$filter_plate_query
.$filter_soa_query
.$filter_delivered_query
.$filter_date_query;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('SOA No.','Date','URC Document','Source','Destination','Plate No.','No. of CS','Rate','Total','Delivery Status')
);
	
if ($query_list_count > 0):
	$total_transaction = 0;
	$total_cs = 0;
	
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
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
			
		$delivery_status = $query_list[$x]['delivered'] == 1 ? 'Delivered':'In-transit';
			
		// CREATED
		$transaction_date = strtotime($query_list[$x]['transaction_date']);
		$date = date('F d, Y', $transaction_date);
			
		$csv[$r] = array ($soa,$date,$urc_doc,$source,$destination,$plate,$cs,$rate,$total,$delivery_status);
	endfor;
	
	$r = $r+1;
	$csv[$r] = array ('','','','','','Total Collections:',$total_cs,'',$total_transaction,'');
endif;
	
$filename = "transaction-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Transactions \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>