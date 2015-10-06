<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
	
$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_type = $filter_type=="" || $filter_type=='0' ? "all":$filter_type;
$filter_from = $filter_from=="" ? date("Y-m-").'01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
	
$filter_type_query = $filter_type=="all" ? " ": ' AND t1.deduction_id = "'.$filter_type.'"';
$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
	"'.$filter_from.'" <= t1.date_to AND "'.$filter_to.'" >= t1.date_from
) ';
	
$query_search = $filter_search != "" ? ' AND (
	t2.plate LIKE "%'.$filter_search.'%"
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
		t3.type_name,
		t4.access_status_id,
		t4.access_class
	FROM deduction as t1
		LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
		LEFT JOIN deduction_type AS t3 ON t1.deduction_id = t3.id
		LEFT JOIN access_status AS t4 ON t1.active = t4.access_status_id
	WHERE
		t1.active = 1 "
.$query_search
.$filter_plate_query
.$filter_type_query
.$filter_date_query;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('Plate No.','Date Range','Type','Description','Price')
);
	
if ($query_list_count > 0):
	$total_deduction = 0;

	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$id = $query_list[$x]['id'];
		$plate = $query_list[$x]['plate'];
		$type_name = $query_list[$x]['type_name'];
		$description = $query_list[$x]['description'];
		$price = $query_list[$x]['price'];
		
		$total_deduction = $query_list[$x]['active'] == 1 ? $total_deduction + $price:$total_deduction;
		 
		// CREATED
		$date_from = strtotime($query_list[$x]['date_from']);
		$date_to = strtotime($query_list[$x]['date_to']);
		$date = $date_from == $date_to ? date('F d, Y', $date_from):date('F d, Y', $date_from) .' to '. date('F d, Y', $date_to);
			
		$csv[$r] = array ($plate,$date,$type_name,$description,$price);
	endfor;
	
	$r = $r+1;
	$csv[$r] = array ('','','','Total Deductions:',$total_deduction);
endif;
	
$filename = "deduction-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Deductions \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>