<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t2.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
$query_search = $filter_search != "" ? ' AND (
	t1.plate LIKE "%'.$filter_search.'%" OR
	t1.truck_model LIKE "%'.$filter_search.'%" OR
	t1.truck_type LIKE "%'.$filter_search.'%" OR
	t3.firstname LIKE "%'.$filter_search.'%" OR
	t3.lastname LIKE "%'.$filter_search.'%"
) ' : ' ';
	
$query = "
	SELECT
		t1.*,
		t2.id AS truck_driver_id,
		t2.driver_id,
		t2.assigned,
		t2.active AS driver_status,
		t3.firstname,
		t3.lastname
	FROM truck as t1
		LEFT JOIN truck_driver AS t2 ON t1.id = t2.truck_id
		LEFT JOIN personnel AS t3 ON t2.driver_id = t3.id
	WHERE
		t1.id != '' "
.$query_search;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('Plate No.','Truck Model','Truck Type','Assigned Driver','Date Assigned','Status')
);
	
if ($query_list_count > 0):
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$truck_id =  $query_list[$x]['id'];
		$id = $query_list[$x]['truck_driver_id'];
		$driver_id =  $query_list[$x]['driver_id'];
		$plate = $query_list[$x]['plate'];
		$truck_type = $query_list[$x]['truck_type'];
		$truck_model = $query_list[$x]['truck_model'];
		
		if ($id == '' || $id == 'D'):
			$driver = "No assigned driver!";
			$created = "No assigned date!";
		else:
			$driver_firstname = $query_list[$x]['firstname'];
			$driver_lastname = $query_list[$x]['lastname'];
			$driver = $driver_firstname . ' ' . $driver_lastname;
			// CREATED
			$created = strtotime($query_list[$x]['assigned']);
			$created = date('F d, Y', $created);
		endif;
			
		$driver_status = $query_list[$x]['driver_status'];
		$status = $driver_status == 1 ? 'Active':'Inactive';
			
		$csv[$r] = array ($plate,$truck_model,$truck_type,$driver,$created,$status);
	endfor;
endif;
	
$filename = "driver-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Drivers \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>