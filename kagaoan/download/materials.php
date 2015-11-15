<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t2.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
$query_search = $filter_search != "" ? ' AND (
	t1.material LIKE "%'.$filter_search.'%" OR
	t1.description LIKE "%'.$filter_search.'%"
) ' : ' ';

$query = "
	SELECT 
		t1.*,
		t2.access_status_id,
		t2.access_class
	FROM materials as t1
		LEFT JOIN access_status AS t2 ON t1.active = t2.access_status_id
	WHERE
		t1.id != '' "
		.$query_search;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('Item Code','Description','Gross Weight','Volume','Status')
);
	
if ($query_list_count > 0):
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$truck_id =  $query_list[$x]['id'];
		$material = $query_list[$x]['material'];
	    $description = $query_list[$x]['description'];
	    $gross_weight = $query_list[$x]['gross_weight'];
		$volume = $query_list[$x]['volume'];
			
		$driver_status = $query_list[$x]['driver_status'];
		$status = $driver_status == 1 ? 'Active':'Inactive';
			
		$csv[$r] = array ($material,$description,$gross_weight,$volume,$status);
	endfor;
endif;
	
$filename = "materials-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Materials \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>