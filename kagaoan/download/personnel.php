<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
$filter_type = $filter_type=="" || $filter_type=='0' ? "all":$filter_type;
$query_search = $filter_search != "" ? ' AND (t1.firstname LIKE "%'.$filter_search.'%" OR t1.lastname LIKE "%'.$filter_search.'%" OR t1.empno LIKE "%'.$filter_search.'%") ' : ' ';
$filter_type_query = $filter_type=="all" ? " ": ' AND t1.id = "'.$filter_type.'"';
	
$query = "
	SELECT
		t1.*,
		t2.*,
		t3.type_name
	FROM personnel as t1
		LEFT JOIN access_status AS t2 ON t1.active = t2.access_status_id
		LEFT JOIN personnel_type AS t3 ON t1.type = t3.id
	WHERE
		t1.id != '' "
.$query_search
.$filter_type_query;
	
$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;
	
$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('No.','Firstname','Lastname','Gender','Address','Mobile','Landline','SSS','PAGIBIG','TIN No.','Position','Date Hired','Status')
);
	
if ($query_list_count > 0):
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$user_id = $query_list[$x]['id'];
		$empno = $query_list[$x]['empno'];
		$firstname = $query_list[$x]['firstname'];
		$lastname = $query_list[$x]['lastname'];
		$user_email = $query_list[$x]['sendmail'];
		 
		// INFORMATION
		$gender = $query_list[$x]['gender'];
		$address = $query_list[$x]['address'];
		$mobile = $query_list[$x]['mobile'];
		$landline = $query_list[$x]['landline'];
		 
		$sss = $query_list[$x]['sss'];
		$pagibig = $query_list[$x]['pagibig'];
		$tin = $query_list[$x]['tin'];
		 
		// USER TYPE
		$user_type = $query_list[$x]['type_name'];
		 
		// USER STATUS
		$user_status = $query_list[$x]['active'];
		$status = $user_status == 1 ? 'Active':'Inactive';
			
		// USER CREATED
		$user_hired = strtotime($query_list[$x]['hire']);
		$user_hired = date('F d, Y', $user_hired);
			
		$csv[$r] = array ($empno,$firstname,$lastname,$gender,$address,$mobile,$landline,$sss,$pagibig,$tin,$user_type,$user_hired,$status);
	endfor;
endif;
	
$filename = "personnnel-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Personnel \n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>