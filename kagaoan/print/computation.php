<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);

if(empty($_GET['id']) || !is_numeric($_GET['id'])):
	?>
	<script type='text/javascript'>
	$(document).ready(function() {
		window.close();
	});
	</script>
	<?
	exit();
endif;

$shipment_id = $id;

$data = $connect->single_result_array("
		SELECT
		t1.*,
		t2.plate,
		t3.location AS source_name,
		t4.location AS destination_name

		FROM shipment AS t1
		LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
		LEFT JOIN location AS t3 ON t1.source = t3.id
		LEFT JOIN location AS t4 ON t1.destination = t4.id
		WHERE t1.id = '{$shipment_id}'");

		// printr($data);

		$compute = $connect->single_result_array("
		SELECT
		SUM(t1.case) AS total_case,
		SUM(t2.gross_weight * t1.case) AS total_weight,
		SUM(t2.volume * t1.case) AS total_volume
		FROM computation AS t1
		LEFT JOIN materials AS t2 ON t1.material_id = t2.id
		WHERE t1.shipment_id = '{$shipment_id}' AND t1.active = '1'");

$compute['ratio_weight'] = ($compute['total_weight']/13500)-1;
$compute['ratio_volume'] = ($compute['total_volume']/44)-1;

// printr($compute);

if ($compute['ratio_weight'] < 0):
	$compute['ratio_weight'] = 0;
endif;

if ($compute['ratio_volume'] < 0):
	$compute['ratio_volume'] = 0;
endif;

$compute['incentive'] = $data['rate'] * (max($compute['ratio_weight'],$compute['ratio_volume']));
$compute['total'] = $compute['incentive'] + $data['rate'];

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
		
$query = "
	SELECT 
		t1.*,
		t2.shipment,
		t2.rate,

		t3.material,
		t3.description,
		t3.gross_weight * t1.case AS gross_weight,
		t3.volume * t1.case AS volume,
		
		t4.access_status_id,
		t4.access_class
	FROM computation as t1
		LEFT JOIN shipment AS t2 ON t1.shipment_id = t2.id
		LEFT JOIN materials AS t3 ON t1.material_id = t3.id
		LEFT JOIN access_status AS t4 ON t1.active = t4.access_status_id
	WHERE
		t1.shipment_id = '{$shipment_id}' "
		.$query_search;

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir;

$query_max_list_count = $connect->count_records($query);	
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);
?>

<script type="text/javascript">		
$(document).ready(function() {
	$('.print').click(function(){
		$('.PrintArea').printArea({mode : "iframe"});
	});

	$('.close').click(function(){
		window.close();
	});
});
</script>

<div style="width:100%;" align="left">

	<div class="spacer_5 clean"><!-- SPACER --></div>
	<input name="print" id="print" type="button" value="Print" class="small_button button print float_left" />
	<input name="close" id="close" type="button" value="Close" class="small_button button float_right close" />
	<div class="spacer_5 clean"><!-- SPACER --></div>

	<div id="PrintArea" class="PrintArea">
		<div class="spacer_10 clean"><!-- SPACER --></div>
		<div align="left">Volume and Weight Computation</div>
		<div class="spacer_10 clean"><!-- SPACER --></div>
		
		<div id="advanced" align="left">
			<div class="float_left px_10">
				<table width="100%" border="0" cellpadding="5" cellspacing="0">
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" width="150" class="table_print_top px_12 unselectable darkgray" align="center">Shipment No.:</th>
						<th width="150" class="table_print_top table_print_right px_12 unselectable darkgray" align="center"><?=$data['shipment']?></th>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top px_12 unselectable darkgray" align="center">Source:</th>
						<td class="table_print_top table_print_right px_12 unselectable" align="center"><?=$data['source_name']?></td>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top px_12 unselectable darkgray" align="center">Destination:</th>
						<td class="table_print_top table_print_right px_12 unselectable" align="center"><?=$data['destination_name']?></td>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top px_12 unselectable darkgray" align="center">Base Rate:</th>
						<td class="table_print_top table_print_right px_12 unselectable" align="center">&#8369; <?=number_format($data['rate'], 2, '.', ',')?></td>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top px_12 unselectable darkgray" align="center">Incentive Rate:</th>
						<td class="table_print_top table_print_right px_12 unselectable" align="center">&#8369; <span id="incentive_rate"><?=number_format($compute['incentive'], 2, '.', ',')?></span></td>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top table_print_bottom px_12 unselectable darkgray" align="center">Total Rate:</th>
						<td class="table_print_top table_print_right table_print_bottom px_12 unselectable" align="center">&#8369; <span id="total_rate"><?=number_format($compute['total'], 2, '.', ',')?></span></td>
					</tr>
				</table>
        	</div>
	        	
        	<div class="float_left marg_left_20 px_10">
        		<table width="100%" border="0" cellpadding="5" cellspacing="0">
					<tr style="background-color:#D7D7D7;" class="line_20">
						<th width="150" class="table_print_top px_12 unselectable darkgray" align="center">Delivery Parameters</th>
						<th width="150" class="table_print_top px_12 unselectable darkgray" align="center">Total</th>
						<th width="150" class="table_print_top px_12 unselectable darkgray" align="center">Incentive Ratio</td>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top px_12 unselectable darkgray" align="center">Weight:</th>
						<td style="background-color:<?=$compute['ratio_weight'] > $compute['ratio_volume'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_print_top px_12 unselectable" align="center"><span id="total_weight"><?=number_format($compute['total_weight'], 2, '.', ',')?></span></td>
						<td style="background-color:<?=$compute['ratio_weight'] > $compute['ratio_volume'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_print_top table_print_left table_print_right px_12 unselectable" align="center"><span id="ratio_weight"><?=number_format($compute['ratio_weight'], 2, '.', ',')?></span></td>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top px_12 unselectable darkgray" align="center">Volume:</th>
						<td style="background-color:<?=$compute['ratio_volume'] > $compute['ratio_weight'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_print_top px_12 unselectable" align="center"><span id="total_volume"><?=number_format($compute['total_volume'], 2, '.', ',')?></span></td>
						<td style="background-color:<?=$compute['ratio_volume'] > $compute['ratio_weight'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_print_top table_print_left table_print_right px_12 unselectable" align="center"><span id="ratio_volume"><?=number_format($compute['ratio_volume'], 2, '.', ',')?></span></td>
					</tr>
					<tr class="line_20">
						<th style="background-color:#D7D7D7;" class="table_print_top table_print_bottom px_12 unselectable darkgray" align="center">Cases:</th>
						<td class="table_print_top table_print_bottom px_12 unselectable" align="center"><span id="total_case"><?=number_format($compute['total_case'], 0, '.', ',')?></span></td>
						<td style="background-color:#FFFFFF;" class="table_print_top table_print_left px_12 unselectable" align="center"></td>
					</tr>
				</table>
        	</div>
			<div class="spacer_20 clean"><!-- SPACER --></div>
		</div>
		
		<div class="spacer_5 clean"><!-- SPACER --></div>
				
		<table width="100%" border="0" cellpadding="5" cellspacing="0">
			<tr style="background-color:#D7D7D7;" class="line_20">
				<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Item Code</th>
				<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Description</th>
				<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Cases Loaded</th>
				<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Weight (kg)</th>
				<th class="table_print_top table_print_bottom px_11 darkgray unselectable" align="center">Volume (m<sup>3</sup>)</th>
			</tr>
		
	<?
	if ($query_list_count > 0):
		$total_transaction = 0;
		$total_cs = 0;
			
		for($x=0;$x<$query_list_count;$x++):
		    // GENERAL INFORMATION
		    $material = $query_list[$x]['material'];
		    $description = $query_list[$x]['description'];
		    $case = number_format($query_list[$x]['case'],0,'.',',');
		    $gross_weight = number_format($query_list[$x]['gross_weight'],2,'.',',');
		    $volume = number_format($query_list[$x]['volume'],2,'.',',');
			?>
		    <tr class="line_20">
				<td class="table_print_left table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($material)?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($description)?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$case?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$gross_weight?></span></td>
				<td class="table_print_right table_print_bottom px_12 unselectable" align="center"><span class="break"><?=$volume?></span></td>
		    </tr>
		<? endfor; ?>   
		</table>
	</div>  <!-- END PRINT -->
	<? else: ?>
		<table width="100%" border="0" cellpadding="6" cellspacing="0" class="list">
		    <tr class="line_20">
		    	<td align="center" class="table_print_left table_print_right table_print_top table_print_bottom error shadow pt_8"><strong>No Result Found</strong></td>
		    </tr>
		</table>
	</div> <!-- END PRINT -->
	<? endif; ?>
</div>
