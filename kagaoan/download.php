<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
	$connect = new MySQL();
	$connect->connect(
		$config['DB'][__SITE__]['USERNAME'],
		$config['DB'][__SITE__]['PASSWORD'],
		$config['DB'][__SITE__]['DATABASE'],
		$config['DB'][__SITE__]['HOST']
	);
	
	switch ($download):
		// COLLECTION STATEMENT
		case 'collection-statement':
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
					t3.type_name
				FROM deduction as t1
					LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
					LEFT JOIN deduction_type AS t3 ON t1.deduction_id = t3.id
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
		break;
		// EOF COLLECTION STATEMENT
		
		// TRANSACTION
		case 'transaction':
			$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
			$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			
			$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
			$filter_soa = $filter_soa=="" || $filter_soa=='0' ? "all":$filter_soa;
			$filter_delivered == "" ? "all":$filter_delivered;
			$filter_from = $filter_from=="" ? date("Y-m-").'01':str_replace(' ','',$filter_from);
			$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
			$filter_type_query = $filter_type=="all" ? " ": ' AND t1.deduction_id = "'.$filter_type.'"';
			$filter_plate_query = $filter_plate=="all" ? " ": ' AND t1.truck_id = "'.$filter_plate.'"';
			$filter_soa_query = $filter_soa=="all" ? " ": ' AND t1.soa = "'.$filter_soa.'"';
			$filter_delivered_query = $filter_delivered=="all" ? " ": ' AND t1.delivered = "'.$filter_delivered.'"';
			$filter_date_query = $filter_from=="" || $filter_to=="" ? " ":' AND (
				DATE(t1.transaction_date) BETWEEN DATE("'.$filter_from.'") AND DATE("'.$filter_to.'")
			) ';
			
			$query_search = $filter_search != "" ? ' AND (
				t1.soa LIKE "%'.$filter_search.'%" OR
				t1.urc_doc LIKE "%'.$filter_search.'%" OR
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
					t4.location AS destination_name
				FROM transaction as t1
					LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
					LEFT JOIN location AS t3 ON t1.source = t3.id
					LEFT JOIN location AS t4 ON t1.destination = t4.id
				WHERE
					t1.active = 1 "
					.$query_search
					.$filter_plate_query
					.$filter_soa_query
					.$filter_delivered_query
					.$filter_date_query;

			$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

			$query_max_list_count = $connect->count_records($query);	
			$query_list = $connect->get_array_result($query.$order_by);
			$query_list_count = count($query_list);
			
			$today = date("Y-m-d his");
			$csv = array (
				array ('SOA No.','Date','URC Document','Source','Destination','Plate No.','No. of CS','Rate','Total','Delivery Status')
			);
			
			if ($query_list_count > 0):
				$total_transaction = 0;
				$total_cs = 0;
				
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
					
					$delivery_status = $query_list[$x]['delivered'] == 1 ? 'Delivered':'In-transit';
					
					// CREATED
					$transaction_date = strtotime($query_list[$x]['transaction_date']);
					$date = date('F d, Y', $transaction_date);
					
					$csv[$r] = array ($soa,$date,$urc_doc,$source,$destination,$plate,$cs,$rate,$total,$delivery_status);
				endfor;
				
				$r = $r+1;
				$csv[$r] = array ('','','','','','Total Collections:',$total_cs,'',$total_transaction,'');
			endif;
			
			$filename = "transaction-".$today.".xls";
			$delimeter = "\t";
			$invalid = array ('"',"+","=");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=".$filename);
			header("Pragma: no-cache");
			header("Expires: 0");
			$fp = fopen('php://output', 'w');
			
			print "Transactions \n\n";
			
			foreach ($csv as $fields):
				$fields = str_replace($invalid, '', $fields);
			    fputcsv($fp, $fields, $delimeter);
			endforeach;
		break;
		// EOF TRANSACTION
		
		// DEDUCTION
		case 'deduction':
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
		break;
		// EOF DEDUCTION
		
		// STOCK OUT
		case 'stock-out':
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
		break;
		// EOF STOCK OUT
		
		// INVENTORY
		case 'inventory':
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
		break;
		// EOF INVENTORY
		
		// TRUCK
		case 'truck':
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
		break;
		// EOF TRUCK
		
		// DRIVER
		case 'driver':
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
		break;
		// EOF DRIVER
		
		// CONSUMPTION
		case 'consumption':
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
		break;
		// EOF CONSUMPTION
		
		// PERSONNEL
		case 'personnel':
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
		break;
		// EOF PERSONNEL
		
		default:
			include dirname(__FILE__) . "/error.php";
			exit(); 
		break;
	endswitch;
endif;
?>