<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
	
$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_brand = $filter_brand=="" || $filter_brand=='0' ? "all":$filter_brand;
$filter_from = $filter_from=="" ? date("Y-m-").'01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
	
$filter_brand_query = $filter_brand=="all" ? " ": ' AND t2.brand = "'.$filter_brand.'"';
$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
	DATE(t1.requisition_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'")
) ';
	
$query_search = $filter_search != "" ? ' AND (
	t2.name LIKE "%'.$filter_search.'%" OR
	t3.brand_name LIKE "%'.$filter_search.'%" OR
	t4.plate LIKE "%'.$filter_search.'%"
) ' : ' ';
 
$from = strtotime($filter_from);
$date_from = date('F d, Y', $from);
$to = strtotime($filter_to);
$date_to = date('F d, Y', $to);
$range = $date_from == $date_to ? $date_from:$date_from .' - '. $date_to;
	
$query = "
	SELECT
		t1.*,

		t2.name,
		t2.description,
		t2.unit_price,
		t2.unit,
		t2.stocks,

		t3.brand_name,
		t4.plate,

		t5.firstname,
		t5.lastname,

		(t1.qty * t2.unit_price) AS total
	FROM requisition as t1
		LEFT JOIN inventory AS t2 ON t1.stock_id = t2.id
		LEFT JOIN brand AS t3 ON t2.brand = t3.id
		LEFT JOIN truck AS t4 ON t1.truck_id = t4.id
		LEFT JOIN personnel AS t5 ON t1.personnel_id = t5.id
	WHERE
		t1.id != '' "
.$query_search
.$filter_plate_query
.$filter_brand_query
.$filter_date_query;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('Requisition ID','Date','Plate','Name','Description','Brand','Price','Quantity','Stocks','Total','Released By')
);
	
if ($query_list_count > 0):
	$total_requisition = 0;
	
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$id = $query_list[$x]['id'];
		$plate = $query_list[$x]['plate'];
		$name = $query_list[$x]['name'];
		$description = $query_list[$x]['description'];
		$brand_name = $query_list[$x]['brand_name'];
			
		$released_by = $query_list[$x]['firstname'] .' '. $query_list[$x]['lastname'];
		$unit = $query_list[$x]['unit'] == '' ? 'unit':$query_list[$x]['unit'];
		$unit_price = $query_list[$x]['unit_price'] .' per '.$unit;
		 
		$stocks = $query_list[$x]['stocks'];
		$qty = $query_list[$x]['active'] == 1 ? $query_list[$x]['qty']:0;
		$total = $query_list[$x]['total'];
		
		$total_requisition = $query_list[$x]['active'] == 1 ? $total_requisition + $total:$total_requisition;
			
		// CREATED
		$requisition_date = strtotime($query_list[$x]['requisition_date']);
		$date = date('F d, Y', $requisition_date);
			
		$csv[$r] = array ($id,$date,$plate,$name,$description,$brand_name,$unit_price,$unit,$stocks,$total,$released_by);
	endfor;
	
	$r = $r+1;
	$csv[$r] = array ('','','','','','','','','Total Requisition:',$total_requisition);
endif;
	
$filename = "stock-out-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Stock Out from ".$range."\n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>