<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;

$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);

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
$range = $date_from == $date_to ? $date_from:$date_from .' - '. $date_to;

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
	array ('Transaction No.','Date', 'Source','Destination','Plate No.','Rate')
);
	
if ($query_list_count > 0):
	$total_transaction = 0;
	$total_cs = 0;
	
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$id = $query_list[$x]['id'];
		$plate = $query_list[$x]['plate'];
	    $shipment = $query_list[$x]['shipment'];
	    $source = $query_list[$x]['source_name'];
	    $destination = $query_list[$x]['destination_name'];
	    $rate = $query_list[$x]['rate'];
	    
	    // CREATED
	    $data_date = strtotime($query_list[$x]['shipment_date']);
	    $date = date('F d, Y', $data_date);
			
		$csv[$r] = array ($shipment,$date,$source,$destination,$plate,$rate);
	endfor;
endif;
	
$filename = "shipment-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Shipment \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>