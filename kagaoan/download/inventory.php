<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
	
$filter_brand = $filter_brand=="" || $filter_brand=='0' ? "all":$filter_brand;
$filter_from = $filter_from=="" ? date("Y-m-").'01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
	
$filter_brand_query = $filter_brand=="all" ? " ": ' AND t1.brand = "'.$filter_brand.'"';
$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
	DATE(t1.purchase_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'")
) ';
	
$query_search = $filter_search != "" ? ' AND (
	t1.name LIKE "%'.$filter_search.'%" OR
	t1.description LIKE "%'.$filter_search.'%" OR
	t2.brand_name LIKE "%'.$filter_search.'%" OR
	t1.supplier LIKE "%'.$filter_search.'%"
) ' : ' ';
 
$from = strtotime($filter_from);
$date_from = date('F d, Y', $from);
$to = strtotime($filter_to);
$date_to = date('F d, Y', $to);
$range = $date_from == $date_to ? $date_from:$date_from .' - '. $date_to;
	
$query = "
	SELECT
		t1.*,
		t2.brand_name
	FROM inventory as t1
		LEFT JOIN brand AS t2 ON t1.brand = t2.id
	WHERE
		t1.id != '' "
.$query_search
.$filter_brand_query
.$filter_date_query;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;


$query_max_list_count = $connect->count_records($query);
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
	
$today = date("Y-m-d his");
$csv = array (
	array ('Name','Description','Brand','Supplier','Purchase Date','Price','Per Unit','Stock','Total')
);
	
if ($query_list_count > 0):
	$total_inventory = 0;
	
	for($x=0,$r=1;$x<$query_list_count;$x++,$r++):
		$id = $query_list[$x]['id'];
		$name = $query_list[$x]['name'];
		$description = $query_list[$x]['description'];
		$brand_name = $query_list[$x]['brand_name'];
		$supplier = $query_list[$x]['supplier'];
			
		$unit_price = $query_list[$x]['unit_price'];
		$unit = $query_list[$x]['unit'];
		$stocks = $query_list[$x]['stocks'];
		$total = $unit_price * $stocks;
		
		$total_inventory = $query_list[$x]['active'] == 1 ? $total_inventory + $total:$total_inventory;
		 
		// CREATED
		$purchase_date = strtotime($query_list[$x]['purchase_date']);
		$date = date('F d, Y', $purchase_date);
		 
		// STATUS
		$inventory_status = $query_list[$x]['active'];
			
		$csv[$r] = array ($name,$description,$brand_name,$supplier,$date,$unit_price,$unit,$stocks,$total);
	endfor;
	
	$r = $r+1;
	$csv[$r] = array ('','','','','','','','Total Inventory:',$total_inventory);
endif;
	
$filename = "inventory-".$today.".xls";
$delimeter = "\t";
$invalid = array ('"',"+","=");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename);
header("Pragma: no-cache");
header("Expires: 0");
$fp = fopen('php://output', 'w');
	
print "Inventory from ".$range."\n\n";
	
foreach ($csv as $fields):
	$fields = str_replace($invalid, '', $fields);
	fputcsv($fp, $fields, $delimeter);
endforeach;
?>