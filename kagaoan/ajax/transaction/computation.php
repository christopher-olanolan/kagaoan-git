<?
$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);

if(empty($_GET['id']) || !is_numeric($_GET['id'])):
	redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=shipment");
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

// printr($compute);

$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;
			
$filter_link  = "";
$filter_link .= "&id=".$id;
$filter_link .= $filter_search != '' ? "&filter_search=".$filter_search:"";
$filter_link .= $filter_sort != '' ? "&filter_sort=".$filter_sort:"";
$filter_link .= $filter_dir != '' ? "&filter_dir=".$filter_dir:"";
$filter_link .= $sort_limit != '' ? "&sort_limit=".$sort_limit:"";
		
$query_search = $filter_search != "" ? ' AND (
	t3.material LIKE "%'.$filter_search.'%" OR
	t3.description LIKE "%'.$filter_search.'%"
) ' : ' ';
 
$query_total = isset($sort_limit) ? $sort_limit : __LIMIT__;
$sort_limit = $query_total;
$page = isset($page) ? $page : '0';
$query_page = $page*$sort_limit;
$limit = $sort_limit == "all"? '':'LIMIT '.$query_page.','.$query_total;
		
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

$order_by = " ORDER BY ".$filter_sort." ".$filter_dir." ".$limit;

$query_max_list_count = $connect->count_records($query);	
$query_list = $connect->get_array_result($query.$order_by);
$query_list_count = count($query_list);

// printr($query_list);
// echo $query.$order_by;
		
$prev = $page == 'all' ? '0':($page)-1;
$next = $page == 'all' ? '0':($page)+1;
$max = $query_max_list_count / $query_total;
$end = (int)$max;
$end = $end == $max ? $end-1:$end;
?>

<script type="text/javascript">
function loadURL(page,bottom){
	var input_filter_search = $('#filter_search').val();
	var input_filter_sort = $('#filter_sort option:selected').val();
	var input_filter_dir = $('#filter_dir option:selected').val();	
	
	var input_sort_limit = bottom == 0 ? $('#sort_limit option:selected').val() : $('#sort_limit_bottom option:selected').val();
	input_sort_limit = input_sort_limit == undefined ? "<?=__LIMIT__?>":input_sort_limit;

	var getURL ="";
	getURL +="<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>"
	getURL +="&id=<?=$shipment_id?>";
	getURL +="&filter_search="+input_filter_search;
	getURL +="&filter_sort="+input_filter_sort;
	getURL +="&filter_dir="+input_filter_dir;
	getURL +="&sort_limit="+input_sort_limit;
	
	getURL += page==null ?"":"&page="+page;

	return getURL;
}

function getURL(url){
	var input_filter_sort = $('#filter_sort option:selected').val();
	var input_filter_dir = $('#filter_dir option:selected').val();	
	
	var getURL ="";
	getURL += url
	getURL +="&id=<?=$shipment_id?>";
	getURL +="&filter_sort="+input_filter_sort;
	getURL +="&filter_dir="+input_filter_dir;

	return getURL;
}


$(document).ready(function() {
	$('.download').click(function() {
		var url = "<?=__ROOT__?>/index.php?file=download&download=computation";
		window.location = getURL(url);
	});
				
	$('.print').click(function(){
		var url = "<?=__ROOT__?>/index.php?file=print&print=computation";
		var w = screen.width;
		var h = screen.height;
		var left = (screen.width/2)-(w/2);
		var top = (screen.height/2)-(h/2);
		  
		window.open(getURL(url), "_blank", "toolbar=no, scrollbars=yes, resizable=no, top=" + top + ", left=" + left + ", width=" + w + ", height=" + (h - 110));
	});
	
    $('#checkbox').click(function(){
        var isChecked = $(this).attr('checked')?true:false;
        if(isChecked){
			$("#multi-active").removeAttr('disabled');
			$("#multi-delete").removeAttr('disabled');
			$(".list").find("input:checkbox").attr('checked',$(this).is(":checked"));
        } else {
			$("#multi-active").attr('disabled','disabled');
			$("#multi-delete").attr('disabled','disabled');
			$(".list").find("input:checkbox").removeAttr('checked');
        }
    });
    
    $('.checkbox').click(function(){
        var isChecked = false;

        $("input.checkbox").each( function() {
			if ($(this).attr("checked") == 'checked'){
			    isChecked = true;
			}
        });
        
        if (isChecked == true){
			$("#multi-active").removeAttr('disabled');
			$("#multi-delete").removeAttr('disabled');
			$("#checkbox").attr('checked',"checked");
        } else {
			$("#multi-active").attr('disabled','disabled');
			$("#multi-delete").attr('disabled','disabled');
			$("#checkbox").removeAttr('checked');
        }
    });
    
    $('.editPop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to edit item.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });

    $('.addPop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to add item.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.deletePop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to delete item.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.activatePop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to activate item.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('div.delete').CreateBubblePopup({
        position: 'right',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Item deleted.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('div.active').CreateBubblePopup({
        position: 'right',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Item active.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.activeMultiPop').CreateBubblePopup({
        position: 'top',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Set <img src="images/ico_checked.png" align="absmiddle" /> item(s) to active.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.deleteMultiPop').CreateBubblePopup({
        position: 'top',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Delete <img src="images/ico_checked.png" align="absmiddle" /> item(s).', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
				
	// FILTER
	$('#filter_sort, #filter_dir').change(function() {
		ajaxLoad(loadURL(null,0),"GET");
	});
	
	$('#filter_sort, #filter_dir').keyup(function() {
		ajaxLoad(loadURL(null,0),"GET");
	});
	// EOF FILTER

	// SORT LIMIT
	$('#sort_limit').change(function() {
		ajaxLoad(loadURL(null,0),"GET");
	});
	
	$('#sort_limit').keyup(function() {
		ajaxLoad(loadURL(null,0),"GET");
	});
	
	$('#sort_limit_bottom').change(function() {
		ajaxLoad(loadURL(null,1),"GET");
	});
	
	$('#sort_limit_bottom').keyup(function() {
		ajaxLoad(loadURL(null,1),"GET");
	});
	// EOF SORT LIMIT
	
	// PAGINATION
	$('.nav-prev').click(function() {
		ajaxLoad(loadURL(<?=$prev?>,0),"GET");
	});

	$('.nav-next').click(function() {
		ajaxLoad(loadURL(<?=$next?>,0),"GET");
	});
	
	$('.nav-end').click(function() {
		ajaxLoad(loadURL(<?=$end?>,0),"GET");
	});
	
	$('.nav-first').click(function() {
		ajaxLoad(loadURL(0,0),"GET");
	});

	// ACTION BUTTON
	$('#btn-search, #btn-filter').click(function() {
		ajaxLoad(loadURL(null,0),"GET");
	});
	
	$('#filter_search').bind('keypress', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if(code == 13) { //Enter keycode
			ajaxLoad(loadURL(null,0),"GET");
		 }
	});

	$('#clear-search').click(function() {
		ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>&id=<?=$shipment_id?>","GET");
	});
	
	$('.add').click(function() {
		ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-computation&shipment_id=<?=$shipment_id?>","GET");
	});
});
</script>

<div style="width:100%;" align="left">
<table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr class="line_20">
        <td align="right" class="px_10" valign="top">
			<div id="advanced" align="left">
				<div class="float_left px_10">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" width="150" class="table_solid_top px_12 unselectable darkgray" align="center">Shipment No.:</th>
							<th width="150" class="table_solid_top table_solid_right px_12 unselectable darkgray" align="center"><?=$data['shipment']?></th>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top px_12 unselectable darkgray" align="center">Source:</th>
							<td class="table_solid_top table_solid_right px_12 unselectable" align="center"><?=$data['source_name']?></td>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top px_12 unselectable darkgray" align="center">Destination:</th>
							<td class="table_solid_top table_solid_right px_12 unselectable" align="center"><?=$data['destination_name']?></td>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top px_12 unselectable darkgray" align="center">Base Rate:</th>
							<td class="table_solid_top table_solid_right px_12 unselectable" align="center">&#8369; <?=number_format($data['rate'], 2, '.', ',')?></td>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top px_12 unselectable darkgray" align="center">Incentive Rate:</th>
							<td class="table_solid_top table_solid_right px_12 unselectable" align="center">&#8369; <span id="incentive_rate"><?=number_format($compute['incentive'], 2, '.', ',')?></span></td>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top table_solid_bottom px_12 unselectable darkgray" align="center">Total Rate:</th>
							<td class="table_solid_top table_solid_right table_solid_bottom px_12 unselectable" align="center">&#8369; <span id="total_rate"><?=number_format($compute['total'], 2, '.', ',')?></span></td>
						</tr>
					</table>
	        	</div>
	        	
	        	<div class="float_right px_10 marg_right_5">
	        		<table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
						<tr style="background-color:#D7D7D7;" class="line_20">
							<th width="150" class="table_solid_top px_12 unselectable darkgray" align="center">Delivery Parameters</th>
							<th width="150" class="table_solid_top px_12 unselectable darkgray" align="center">Total</th>
							<th width="150" class="table_solid_top px_12 unselectable darkgray" align="center">Incentive Ratio</td>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top px_12 unselectable darkgray" align="center">Weight:</th>
							<td style="background-color:<?=$compute['ratio_weight'] > $compute['ratio_volume'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_solid_top px_12 unselectable" align="center"><span id="total_weight"><?=number_format($compute['total_weight'], 2, '.', ',')?></span></td>
							<td style="background-color:<?=$compute['ratio_weight'] > $compute['ratio_volume'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_solid_top table_solid_left table_solid_right px_12 unselectable" align="center"><span id="ratio_weight"><?=number_format($compute['ratio_weight'], 2, '.', ',')?></span></td>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top px_12 unselectable darkgray" align="center">Volume:</th>
							<td style="background-color:<?=$compute['ratio_volume'] > $compute['ratio_weight'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_solid_top px_12 unselectable" align="center"><span id="total_volume"><?=number_format($compute['total_volume'], 2, '.', ',')?></span></td>
							<td style="background-color:<?=$compute['ratio_volume'] > $compute['ratio_weight'] ? '#339900':'#ff6666'?>; color:#FFFFFF;" class="table_solid_top table_solid_left table_solid_right px_12 unselectable" align="center"><span id="ratio_volume"><?=number_format($compute['ratio_volume'], 2, '.', ',')?></span></td>
						</tr>
						<tr class="line_20">
							<th style="background-color:#D7D7D7;" class="table_solid_top table_solid_bottom px_12 unselectable darkgray" align="center">Cases:</th>
							<td class="table_solid_top table_solid_bottom px_12 unselectable" align="center"><span id="total_case"><?=number_format($compute['total_case'], 0, '.', ',')?></span></td>
							<td style="background-color:#FFFFFF;" class="table_solid_top table_solid_left px_12 unselectable" align="center"></td>
						</tr>
					</table>
	        	</div>
        		<div class="spacer_5 clean"><!-- SPACER --></div>
        	</div>
        	
        	<div class="spacer_20 clean"><!-- SPACER --></div>
        	
        	<div class="float_left px_10" style="width:50%;" align="left">
				Filter:&nbsp;
				<input id="filter_search" name="filter_search" type="text" class="inputtext thin_inputtext" maxlength="50" value="<?=$filter_search?>" /> 
				<input type="button" class="button small_button" value="Search" id="btn-search"> 
				<input type="button" class="button small_button" value="Reset" id="clear-search">
				
				<div class="spacer_0 clean"><!-- SPACER --></div>
				<div style="margin-left: 28px;" class="green"><em>Search Item code, Item description</em></div>
			</div>

			<div class="float_right px_10" style="width:50%;">
				Sort:&nbsp;
				<select id="filter_sort" name="filter_sort" class="selectoption thin_select pt_8">
				    <? for ($i=0;$i<$computationsortdatacount;$i++): ?>
				    <option value="<?=$computationsortdata[$i]['value']?>" <?=$filter_sort==$computationsortdata[$i]['value']?"selected=selected":""?>><?=$computationsortdata[$i]['name']?></option>
				    <? endfor; ?>
				</select>
				<select id="filter_dir" name="filter_dir" class="selectoption thin_select pt_8">
				    <option value="ASC" <?=$filter_dir=="ASC"?"selected=selected":""?>>Ascending</option>
				    <option value="DESC" <?=$filter_dir=="DESC"?"selected=selected":""?>>Descending</option>
				</select>
			</div>

			<div class="spacer_0 clean"><!-- SPACER --></div>
        </td>
    </tr>
</table>

<form action="<?=__ROOT__?>/index.php?file=process&process=activate" method="post" enctype="multipart/form-data">
<input type="hidden" value="<?=$shipment_id?>" name="shipping_id" />
<input type="hidden" value="&panel=transaction&section=computation&id=<?=$shipment_id?>" name="return_link" />
<input type="hidden" value="<?=$filter_link?>" name="filter_link" />
<input type="hidden" value="computation" name="table" />

<div style="width: 100%; height: 22px;" align="right" class="line_22">
	<span class="wrap px_10"><b><?=$query_max_list_count?></b> Result(s) found</span>
</div>

<div class="spacer_5 clean"><!-- SPACER --></div>

<table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr class="line_20">
        <td align="left" valign="top">
        	<span class="px_10">Display #</span>
        	<select id="sort_limit" name="sort_limit" class="selectoption thin_number_select pt_8 sort_limit">
			    <? for ($i=0;$i<$dataLimitcount;$i++): ?>
			    <option value="<?=$dataLimit[$i]['value']?>" <?=$sort_limit==$dataLimit[$i]['value']?"selected=selected":""?>><?=$dataLimit[$i]['name']?></option>
			    <? endfor; ?>
			</select>
						
			<input type="button" class="button small_button add" name="add" id="add" value="New Item" />
			<input type="button" class="button small_button download" value="Download" id="download">
			<input name="print" id="print" type="button" value="Print" class="small_button button print" />
        </td>
        <td align="right">
        	<?=paginate($page,$prev,$next,$max,$end,"top")?>
        </td>
    </tr>
</table>

<div id="PrintArea" class="PrintArea">
	<div class="table_title" align="center">
		<div align="left" class="px_16 float_left table_title_header">Volume and Weight Computation</div>
	</div>
			
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
		<tr style="background-color:#D7D7D7;" class="line_20">
			<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center">&nbsp;</th>
			<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center"></th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Item Code</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Description</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Cases Loaded</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Weight (kg)</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Volume (m<sup>3</sup>)</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Action</th>
		</tr>
	
<?
if ($query_list_count > 0):
	for($x=0;$x<$query_list_count;$x++):
	    // GENERAL INFORMATION
	    $cid = $query_list[$x]['id'];
	    $material = $query_list[$x]['material'];
	    $description = $query_list[$x]['description'];
	    $case = number_format($query_list[$x]['case'],0,'.',',');
	    $gross_weight = number_format($query_list[$x]['gross_weight'],2,'.',',');
	    $volume = number_format($query_list[$x]['volume'],2,'.',',');
	    
	    // STATUS
	    $data_status = $query_list[$x]['active'];
		$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
		$status_id = $query_list[$x]['access_status_id'];
	    $status_class = $query_list[$x]['access_class'];
					
	    // BUBBLE INFO
		$info  = "<div style='width:250px;' class='pt_8'>";
		
		$info .= "<b class='orange'>Item Code.:</b> $material <br />";
		$info .= "<b class='orange'>Description:</b> $description <br />";
					
	    $info .= "<div class='spacer_5 clean'><!-- SPACER --></div>";
		$info .= "<b class='orange'>Cases Loaded:</b> $case <br />";
	    $info .= "<b class='orange'>Weight (kg):</b> $gross_weight <br />";
	    $info .= "<b class='orange'>Volume (m<sup>3</sup>):</b> $volume <br />";
		$info .= "</div>";
 
		?>
	    <script type="text/javascript">
		$(document).ready(function() {
			$('#info_<?=$cid?>').CreateBubblePopup({
			    position: 'right',
			    align: 'left',
			    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'left'},
			    innerHtml: "<?=$info?>", 
			    themeName: 'all-black',
			    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
			});
	
			$('#edit_<?=$cid?>').click(function(){
				ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=edit-computation&shipment_id=<?=$shipment_id?>&id=<?=$cid?>","GET");
			});
		});
	    </script>
	    <tr class="line_20">
	        <td width="1%" class="table_solid_left table_solid_right table_solid_bottom <?=$x==$query_list_count-1?'bottom-left-radius':''?>" align="center">
				<input type="checkbox" class="checkbox" name="action[checkbox][]" value="<?=$cid?>" />
			</td>
	        <td width="1%" class="table_solid_right table_solid_bottom" align="center">
				<div class="<?=$status_class?> block"></div>
			</td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($material)?></span></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><?=stringLimit($description)?></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$case?></span></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$gross_weight?></span></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$volume?></span></td> 
			<td width="75" class="table_solid_right table_solid_bottom px_11 unselectable <?=$x==$query_list_count-1?'bottom-right-radius':''?>" align="center">
				<? 
				if($data_status == '1'): 
					?><input type="submit" class="clean float_right deletePop ico ico_delete confirm" name="action[single-delete]" value="<?=$cid?>" id="delete_<?=$cid?>" title="Are you sure you want to delete this data?" /><? 
				else: 
					?><input type="submit" class="clean float_right activatePop ico ico_active" name="action[single-active]" value="<?=$cid?>" id="active_<?=$cid?>" /><? 
				endif; 
				?>
				<input type="button" class="clean float_right marg_right_5 editPop ico ico_edit" name="action[single-edit]" value="<?=$cid?>" id="edit_<?=$cid?>" />
				<input type="button" class="clean float_right marg_right_5 editPop ico ico_preview" name="info_<?=$cid?>" id="info_<?=$cid?>" />
			</td>
	    </tr>
	<? endfor; ?>   
	</table>
</div>  <!-- END PRINT -->

<table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr class="line_20">
        <td width="1.1%" align="center"><input type="checkbox" id="checkbox" /></td>
        <td align="left" class="px_10">
        	<span class="marg_right_10"><strong>Status:</strong></span>
			<input type="submit" class="clean activeMultiPop ico ico_active" name="action[multi-active]" id="multi-active" value="true" disabled="disabled" /> Active &nbsp;&nbsp; 
			<input type="submit" class="clean deleteMultiPop ico ico_delete confirm" name="action[multi-delete]" id="multi-delete" value="true" disabled="disabled" title="Are you sure you want to delete selected user(s)?" /> Delete &nbsp;&nbsp;
        </td>
        <td align="right">
        	<input type="button" class="button small_button add" name="add" id="add" value="New Item" />
    		<input type="button" class="button small_button download" value="Download" id="download">
			<input name="print" id="print" type="button" value="Print" class="small_button button print" />
        </td>
    </tr>
</table>
<? else: ?>
	<table width="100%" border="0" cellpadding="6" cellspacing="0" class="list">
	    <tr class="line_20">
	    	<td align="center" class="table_solid_left table_solid_right table_solid_top table_solid_bottom error shadow pt_8 bottom-right-radius bottom-left-radius"><strong>No Result Found</strong></td>
	    </tr>
	</table>
</div> <!-- END PRINT -->
<? endif; ?>
</form>
</div>
