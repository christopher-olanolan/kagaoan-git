<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "1":$filter_plate;
//$filter_soa = $filter_soa=="" || $filter_soa=='0' ? "all":$filter_soa;
$filter_from = $filter_from=="" ? date("Y-m-").'01':str_replace(' ','',$filter_from);
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
		t1.paid = 1 AND t1.delivered = 1 AND t2.active = 1 "
.$filter_plate_query
.$filter_date_query;

$order_by = " ORDER BY t1.transaction_date DESC ";

$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);

$today = date("Y-m-d his");
$csv = array (
	array ('SOA No.','Date','URC Document','Source','Destination','Plate No.','No. of CS','Rate','Total')
);
	
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

// CREATED
$transaction_date = strtotime($query_list[$x]['transaction_date']);
$date = date('F d, Y', $transaction_date);

$csv[$r] = array ($soa,$date,$urc_doc,$source,$destination,$plate,$cs,$rate,$total);
endfor;

$r = $r+1;
$csv[$r] = array ('','','','','','Total Collections:',$total_cs,'',$total_transaction);
	
$r = $r+1;
$csv[$r][0] = "";
	
$r = $r+1;
$csv[$r] =  array('Deduction','Description','Price');
	
$salestax = 0.02 * $total_transaction;
$r = $r+1;
$csv[$r] = array ('Sales Tax','2% Tax',$salestax);
	
$savings = 0.03 * $total_transaction;
$r = $r+1;
$csv[$r] = array ('Savings','3% Savings',$savings);
	
$fund = 0.01 * $total_transaction;
$r = $r+1;
$csv[$r] = array ('Fund','1% Fund',$fund);
	
$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ": ' AND DATE(t1.consumption_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'") ';
	
$query = "
	SELECT
		t1.*
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
	
$total_consumption = 0;
	
if ($query_list_count > 0):
	for($x=0;$x<$query_list_count;$x++):
		$liters = $query_list[$x]['liters'];
		$price = $query_list[$x]['price'];
		$total = $liters * $price;
		
		$total_consumption = $query_list[$x]['active'] == 1 ? $total_consumption + $total:$total_consumption;
		$total_liters = $query_list[$x]['active'] == 1 ? $total_liters + $liters:$total_liters;
	endfor;
endif;
	
$r = $r+1;
$csv[$r] = array ('Gasoline Consumptions',$range, $total_consumption);

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
	
$total_deduction = 0;
	
if ($query_list_count > 0):
	$r = $r+1;
	for($x=0,$r=$r;$x<$query_list_count;$x++,$r++):
		$type_name = $query_list[$x]['type_name'];
		$description = $query_list[$x]['description'];
		$total = $query_list[$x]['price'];
		$total_deduction = $query_list[$x]['active'] == 1 ? $total_deduction + $total:$total_deduction;
			
		$date_from = strtotime($query_list[$x]['date_from']);
		$date_to = strtotime($query_list[$x]['date_to']);
		$date = date('F d, Y', $date_from) .' - '. date('F d, Y', $date_to);
		$description = $description == '' || $description == 'D' ? $date:$description;
			
		if ($query_list[$x]['deduction_id'] == 3):
			$description = 'Payroll for '. $query_list[$x]['firstname'] .' '.$query_list[$x]['lastname'] . ' - ' .$description;
		endif;
		
		$csv[$r] = array ($type_name,$description, $total);
	endfor;
endif;
	
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
	
if ($requisition_list_count > 0):
	$r = $r+1;
	for($x=0,$r=$r;$x<$requisition_list_count;$x++,$r++):
		$name = $requisition_list[$x]['name'];
		$brand_name = $requisition_list[$x]['brand_name'];
		$type_name = $name . '(' . $brand_name .') ' . $requisition_list[$x]['description'];
			
		// CREATED
		$requisition_date = strtotime($requisition_list[$x]['requisition_date']);
		$date = date('F d, Y', $requisition_date);
		$unit_price = $requisition_list[$x]['unit_price'];
		$qty = $requisition_list[$x]['active'] == 1 ? $requisition_list[$x]['qty']:0;
		$price = $unit_price . ' x '.$qty;
		$description = $price. ' on '.$date;
		
		$total = $requisition_list[$x]['total'];
			
		$total_requisition = $requisition_list[$x]['active'] == 1 ? $total_requisition + $total:$total_requisition;
			
		$csv[$r] = array ($type_name,$description, $total);
	endfor;
endif;

$total_deductions = $total_deduction + $total_consumption + $salestax + $savings + $fund + $total_requisition;
$r = $r+1;
$csv[$r] = array ('','Total Deductions:',$total_deductions);
	
$r = $r+1;
$csv[$r][0] = "";

$net = $total_transaction-$total_deductions;
$r = $r+1;
$csv[$r] = array ('','Net Amount:',$net);
	
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

if ($query_list_count > 0):
	$r = $r+1;
	$csv[$r][0] = "";
	
	$r = $r+1;
	$csv[$r] =  array('Plate No.','Date','Liters', 'Price Per Liter','Total');
	$total_consumption = 0;
	
	$r = $r+1;
	for($x=0,$r=$r;$x<$query_list_count;$x++,$r++):
		$plate = $query_list[$x]['plate'];
		 
		$liters = $query_list[$x]['liters'];
		$price = $query_list[$x]['price'];
		$total = $liters * $price;
		
		$total_consumption = $query_list[$x]['active'] == 1 ? $total_consumption + $total:$total_consumption;
		$total_liters = $query_list[$x]['active'] == 1 ? $total_liters + $liters:$total_liters;
		 
		// CREATED
		$consumption_date = strtotime($query_list[$x]['consumption_date']);
		$date = date('F d, Y', $consumption_date);
			
		$csv[$r] =  array($plate,$date,$liters,$price,$total);
	endfor;
	
	$r = $r+1;
	$csv[$r] =  array('','Total Consumptions:',$total_liters,'',$total_consumption);
endif;
	
$filename = "collection-statement-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Trucker Collection Statement \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
	
fclose($fp);
?>