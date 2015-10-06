<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_from = $filter_from=="" ? date("Y-m-").'01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
	
$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ": ' AND DATE(t1.consumption_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'") ';
	
$query_search = $filter_search != "" ? ' AND (
	t2.plate LIKE "%'.$filter_search.'%"
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
		t3.access_status_id,
		t3.access_class
	FROM consumptions as t1
		LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
		LEFT JOIN access_status AS t3 ON t1.active = t3.access_status_id
	WHERE
		t1.active = 1 "
.$query_search
.$filter_plate_query
.$filter_date_query;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('Plate No.','Date','Liters','Price Per Liter','Total')
);
	
if ($query_list_count > 0):
	$total_consumption = 0;
	
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		// GENERAL INFORMATION
		$id = $query_list[$x]['id'];
		$plate = $query_list[$x]['plate'];
		$liters = $query_list[$x]['liters'];
		$price = $query_list[$x]['price'];
		$total = $liters * $price;
		
		$total_consumption = $query_list[$x]['active'] == 1 ? $total_consumption + $total:$total_consumption;
		$total_liters = $query_list[$x]['active'] == 1 ? $total_liters + $liters:$total_liters;
		$total_price = $query_list[$x]['active'] == 1 ? $total_price + $price:$total_price;
		 
		// CREATED
		$consumption_date = strtotime($query_list[$x]['consumption_date']);
		$date = date('F d, Y', $consumption_date);
			
		$csv[$r] = array ($plate,$date,$liters,$price,$total);
	endfor;

	$average_price = $total_price/$query_list_count;
	
	$r = $r+1;
	$csv[$r] = array ('','Total Consumption:',$total_liters,$average_price,$total_consumption);
endif;
	
$filename = "consumption-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Diesel Consumption for " .$range. " \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>