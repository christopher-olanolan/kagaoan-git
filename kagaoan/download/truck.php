<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

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
		t2.lastname AS o_lastname
	FROM truck as t1
		LEFT JOIN personnel AS t2 ON t1.operator = t2.id
	WHERE
		t1.id != '' "
.$query_search;
	
$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;
	
$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('Plate No.','Truck Model','Truck Type','Truck Operator','Status')
);
	
if ($query_list_count > 0):
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$id = $query_list[$x]['id'];
		$plate = $query_list[$x]['plate'];
		$truck_type = $query_list[$x]['truck_type'];
		$truck_model = $query_list[$x]['truck_model'];
			
		$owner_firstname = $query_list[$x]['o_firstname'];
		$owner_lastname = $query_list[$x]['o_lastname'];
		$owner = $owner_firstname . ' ' . $owner_lastname;
			
		/*
		 $driver_firstname = $query_list[$x]['d_firstname'];
		 $driver_lastname = $query_list[$x]['d_lastname'];
		 $driver = $driver_firstname . ' ' . $driver_lastname;
		 */
			
		// STATUS
		$truck_status = $query_list[$x]['active'];
		$status = $truck_status == 1 ? 'Active':'Inactive';
			
		// CREATED
		$created = strtotime($query_list[$x]['d_create']);
		$created = date('F d, Y', $created);
			
		$csv[$r] = array ($plate,$truck_model,$truck_type,$owner,$status);
	endfor;
endif;
	
$filename = "truck-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Truck \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>