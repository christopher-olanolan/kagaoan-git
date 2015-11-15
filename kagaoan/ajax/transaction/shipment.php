<?
$login_id = decryption($_SESSION[__SITE__.'_ENCRYPT_ID']);
$filter_sort = $filter_sort=="" ? "t1.id":$filter_sort;
$filter_dir = $filter_dir=="" ? "ASC":$filter_dir;

$filter_plate = $filter_plate=="" || $filter_plate=='0' ? "all":$filter_plate;
$filter_from = $filter_from=="" ? date("Y-").'01-01':str_replace(' ','',$filter_from);
$filter_to = $filter_to=="" ? date("Y-m-d"):str_replace(' ','',$filter_to);
			
$filter_link  = "";
$filter_link .= $filter_search != '' ? "&filter_search=".$filter_search:"";
$filter_link .= $filter_sort != '' ? "&filter_sort=".$filter_sort:"";
$filter_link .= $filter_dir != '' ? "&filter_dir=".$filter_dir:"";
$filter_link .= $sort_limit != '' ? "&sort_limit=".$sort_limit:"";

$filter_link .= $filter_plate != '' ? "&filter_plate=".$filter_plate:"";
$filter_link .= $filter_from != '' ? "&filter_from=".$filter_from:"";
$filter_link .= $filter_to != '' ? "&filter_to=".$filter_to:"";

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
$range = $date_from == $date_to ? $date_from:$date_from .' &mdash; '. $date_to;
 
$query_total = isset($sort_limit) ? $sort_limit : __LIMIT__;
$sort_limit = $query_total;
$page = isset($page) ? $page : '0';
$query_page = $page*$sort_limit;
$limit = $sort_limit == "all"? '':'LIMIT '.$query_page.','.$query_total;
		
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
		t1.id != '' "
		.$query_search
		.$filter_plate_query
		.$filter_date_query;

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

$select = new Select();
$select_plate =  $select->option_query(
	'truck', 						// table name
	'filter_plate',  				// name='$name' 
	'filter_plate', 				// id='$id'
	'id',							// value='$value'
	'plate',						// option name
	$filter_plate,					// default selected value
	'active = "1"',					// query condition(s)  
	'plate',						// 'order by' field name
	'ASC',							// sort order 'asc' or 'desc'
	'selectoption thin_select pt_8',// css class
	'All',							// default null option name 'Choose option...'	
	'0'								// select type 1 = multiple or 0 = single
);
?>

<script type="text/javascript">
function loadURL(page,bottom){
	var input_filter_search = $('#filter_search').val();
	var input_filter_sort = $('#filter_sort option:selected').val();
	var input_filter_dir = $('#filter_dir option:selected').val();	

	var input_filter_plate = $('#filter_plate option:selected').val();
	var input_filter_from = $('#filter_from').val();
	var input_filter_to = $('#filter_to').val();
	
	var input_sort_limit = bottom == 0 ? $('#sort_limit option:selected').val() : $('#sort_limit_bottom option:selected').val();
	input_sort_limit = input_sort_limit == undefined ? "<?=__LIMIT__?>":input_sort_limit;

	var getURL ="";
	getURL +="<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>"
	getURL +="&filter_search="+input_filter_search;
	getURL +="&filter_sort="+input_filter_sort;
	getURL +="&filter_dir="+input_filter_dir;
	getURL +="&sort_limit="+input_sort_limit;

	getURL +="&filter_plate="+input_filter_plate;
	getURL +="&filter_from="+input_filter_from;
	getURL +="&filter_to="+input_filter_to;
	
	getURL += page==null ?"":"&page="+page;

	return getURL;
}

function getURL(url){
	var input_filter_search = $('#filter_search').val();
	var input_filter_sort = $('#filter_sort option:selected').val();
	var input_filter_dir = $('#filter_dir option:selected').val();	

	var input_filter_plate = $('#filter_plate option:selected').val();
	var input_filter_from = $('#filter_from').val();
	var input_filter_to = $('#filter_to').val();
	
	var getURL ="";
	getURL += url
	getURL +="&filter_search="+input_filter_search;
	getURL +="&filter_sort="+input_filter_sort;
	getURL +="&filter_dir="+input_filter_dir;

	getURL +="&filter_plate="+input_filter_plate;
	getURL +="&filter_from="+input_filter_from;
	getURL +="&filter_to="+input_filter_to;

	return getURL;
}


$(document).ready(function() {
	$('.download').click(function() {
		var url = "<?=__ROOT__?>/index.php?file=download&download=shipment";
		window.location = getURL(url);
	});
				
	$('.print').click(function(){
		var url = "<?=__ROOT__?>/index.php?file=print&print=shipment";
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

    $('.statPop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Add/Edit items to shipment.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.editPop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to edit shipment.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });

    $('.addPop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to add shipment.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.deletePop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to delete shipment.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.activatePop').CreateBubblePopup({
        position: 'left',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Click to activate shipment.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('div.delete').CreateBubblePopup({
        position: 'right',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Shipment deleted.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('div.active').CreateBubblePopup({
        position: 'right',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Shipment active.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.activeMultiPop').CreateBubblePopup({
        position: 'top',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Set <img src="images/ico_checked.png" align="absmiddle" /> shipment(s) to active.', 
        themeName: 'all-black',
        themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
    });
    
    $('.deleteMultiPop').CreateBubblePopup({
        position: 'top',
        align: 'center',
        innerHtmlStyle: {color:'#FFFFFF', 'text-align':'center'},
        innerHtml: 'Delete <img src="images/ico_checked.png" align="absmiddle" /> shipment(s).', 
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
		ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=<?=$control?>","GET");
	});
	
	$('.add').click(function() {
		ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-shipment","GET");
	});
				
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;
	var yyyy = today.getFullYear();
	var maxdate = yyyy + '-' + mm + '-' + dd;
				    
	$("#filter_from").datepicker({
		alwaysSetTime: false,
		timepicker: false,
    	dateFormat: "yy-mm-dd",
    	showSecond: false,
    	showMinute: false,
    	showHour: false,
    	showTime: false,
    	maxDate: $("#filter_to").val(),
    	onSelect: function() {
			var date = $(this).datepicker('getDate');
			date.setDate(date.getDate());
			$("#filter_to").datepicker( "option", "minDate", date);
		}
	});

	$("#filter_to").datepicker({
		alwaysSetTime: false,
		timepicker: false,
		dateFormat: "yy-mm-dd",
		showSecond: false,
		showMinute: false,
		showHour: false,
		showTime: false,
		minDate: $("#filter_from").val(),
		onSelect: function() {
			var date = $(this).datepicker('getDate');
			date.setDate(date.getDate());
			$("#filter_from").datepicker( "option", "maxDate", date);
		}
	});
});
</script>

<div style="width:100%;" align="left">
<table width="100%" border="0" cellpadding="5" cellspacing="0">
    <tr class="line_20">
        <td align="right" class="px_10" valign="top">
        	<div class="float_left px_10" style="width:50%;" align="left">
				Filter:&nbsp;
				<input id="filter_search" name="filter_search" type="text" class="inputtext thin_inputtext" maxlength="50" value="<?=$filter_search?>" /> 
				<input type="button" class="button small_button" value="Search" id="btn-search"> 
				<input type="button" class="button small_button" value="Reset" id="clear-search">
				
				<div class="spacer_0 clean"><!-- SPACER --></div>
				<div style="margin-left: 28px;" class="green"><em>Search plate no., Shipment no., source and destination</em></div>
			</div>

			<div class="float_right px_10" style="width:50%;">
				Sort:&nbsp;
				<select id="filter_sort" name="filter_sort" class="selectoption thin_select pt_8">
				    <? for ($i=0;$i<$shipmentsortdatacount;$i++): ?>
				    <option value="<?=$shipmentsortdata[$i]['value']?>" <?=$filter_sort==$shipmentsortdata[$i]['value']?"selected=selected":""?>><?=$shipmentsortdata[$i]['name']?></option>
				    <? endfor; ?>
				</select>
				<select id="filter_dir" name="filter_dir" class="selectoption thin_select pt_8">
				    <option value="ASC" <?=$filter_dir=="ASC"?"selected=selected":""?>>Ascending</option>
				    <option value="DESC" <?=$filter_dir=="DESC"?"selected=selected":""?>>Descending</option>
				</select>
			</div>

			<div class="spacer_20 clean"><!-- SPACER --></div>

			<div id="advanced" class="float_left" style="margin-left: 22px;" align="left">
				<div class="float_left px_10 marg_right_5 marg_left_5">
					Plate No.:<br />
					<?=$select_plate?>
	        	</div>
        	
        		<div class="spacer_5 clean"><!-- SPACER --></div>
        	
				<div class="float_left px_10 marg_right_5 marg_left_5">
					From:<br />
					<input id="filter_from" name="filter_from" type="text" class="inputtext thin_mid_inputtext datepicker" maxlength="50" value="<?=$filter_from?>" />
        		</div>
        		<div class="float_left px_10 marg_right_5">
					To:<br />
					<input id="filter_to" name="filter_to" type="text" class="inputtext thin_mid_inputtext datepicker" maxlength="50" value="<?=$filter_to?>" /> 
					<input type="button" class="button small_button" value="Go" id="btn-filter"> 
        		</div>
        		<div class="spacer_5 clean"><!-- SPACER --></div>
        	</div>
        </td>
    </tr>
</table>

<form action="<?=__ROOT__?>/index.php?file=process&process=activate" method="post" enctype="multipart/form-data">
<input type="hidden" value="&panel=transaction&section=shipment" name="return_link" />
<input type="hidden" value="<?=$filter_link?>" name="filter_link" />
<input type="hidden" value="shipment" name="table" />

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
						
			<input type="button" class="button small_button add" name="add" id="add" value="New Shipment" />
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
		<div align="left" class="px_16 float_left table_title_header">Shipment for <?=$range?></div>
	</div>
			
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="list">
		<tr style="background-color:#D7D7D7;" class="line_20">
			<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center">&nbsp;</th>
			<th width="1%" class="table_solid_bottom px_10 darkgray unselectable" align="center"></th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Shipment No.</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Date</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Source</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Destination</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Plate No.</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Rate</th>
			<th class="table_solid_top table_solid_bottom px_11 darkgray unselectable" align="center">Action</th>
		</tr>
	
<?
if ($query_list_count > 0):
	for($x=0;$x<$query_list_count;$x++):
	    // GENERAL INFORMATION
	    $id = $query_list[$x]['id'];
	    $plate = $query_list[$x]['plate'];
	    $shipment = $query_list[$x]['shipment'];
	    $source = $query_list[$x]['source_name'];
	    $destination = $query_list[$x]['destination_name'];
	    $rate = $query_list[$x]['rate'];
	    
	    // CREATED
	    $data_date = strtotime($query_list[$x]['shipment_date']);
	    $date = date('F d, Y', $data_date);
	    
	    // STATUS
	    $data_status = $query_list[$x]['active'];
		$status = $query_list[$x]['active'] == 1 ? 'Active':'Inactive';
		$status_id = $query_list[$x]['access_status_id'];
	    $status_class = $query_list[$x]['access_class'];
					
	    // BUBBLE INFO
		$info  = "<div style='width:250px;' class='pt_8'>";
		
		$info .= "<b class='orange'>Shipment No.:</b> $shipment <br />";
					
	    $info .= "<div class='spacer_5 clean'><!-- SPACER --></div>";
		$info .= "<b class='orange'>Plate:</b> $plate <br />";
	    $info .= "<b class='orange'>Date:</b> $date <br />";
	    $info .= "<b class='orange'>Source:</b> $source <br />";
	    $info .= "<b class='orange'>Destination:</b> $destination <br />";
					
		$info .= "<div class='spacer_10 clean'><!-- SPACER --></div>";
		$info .= "<b class='orange'>Rate:</b> &#8369; " . number_format($rate, 2, '.', ',') . "<br />";
		$info .= "</div>";
 
		?>
	    <script type="text/javascript">
		$(document).ready(function() {
			$('#info_<?=$id?>').CreateBubblePopup({
			    position: 'right',
			    align: 'left',
			    innerHtmlStyle: {color:'#FFFFFF', 'text-align':'left'},
			    innerHtml: "<?=$info?>", 
			    themeName: 'all-black',
			    themePath: '<?=__IMAGE__?>jquerybubblepopup-theme/'
			});
	
			$('#edit_<?=$id?>').click(function(){
				ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=edit-shipment&id=<?=$id?>&filter_search=<?=$filter_search?>&filter_plate=<?=$filter_plate?>&filter_from=<?=$filter_from?>&filter_to=<?=$filter_to?>","GET");
			});

			$('#stat_<?=$id?>').click(function(){
				var url = "<?=__ROOT__?>/index.php?file=panel&panel=transaction&section=computation&id=<?=$id?>";
				window.location = url;
			});
		});
	    </script>
	    <tr class="line_20">
	        <td width="1%" class="table_solid_left table_solid_right table_solid_bottom <?=$x==$query_list_count-1?'bottom-left-radius':''?>" align="center">
				<input type="checkbox" class="checkbox" name="action[checkbox][]" value="<?=$id?>" />
			</td>
	        <td width="1%" class="table_solid_right table_solid_bottom" align="center">
				<div class="<?=$status_class?> block"></div>
			</td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=stringLimit($shipment)?></span></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><?=$date?></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$source?></span></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$destination?></span></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break"><?=$plate?></span></td>
			<td class="table_solid_bottom px_12 unselectable" align="center"><span class="break">&#8369; <?=number_format($rate, 2, '.', ',')?></span></td> 
			<td width="100" class="table_solid_right table_solid_bottom px_11 unselectable <?=$x==$query_list_count-1?'bottom-right-radius':''?>" align="center">
				<? 
				if($data_status == '1'): 
					?><input type="submit" class="clean float_right deletePop ico ico_delete confirm" name="action[single-delete]" value="<?=$id?>" id="delete_<?=$id?>" title="Are you sure you want to delete this data?" /><? 
				else: 
					?><input type="submit" class="clean float_right activatePop ico ico_active" name="action[single-active]" value="<?=$id?>" id="active_<?=$id?>" /><? 
				endif; 
				?>
				
				<input type="button" class="clean float_right marg_right_5 editPop ico ico_edit" name="action[single-edit]" value="<?=$id?>" id="edit_<?=$id?>" />
				<input type="button" class="clean float_right marg_right_5 statPop ico ico_stat" name="stat_<?=$id?>" value="<?=$id?>" id="stat_<?=$id?>" />
				<input type="button" class="clean float_right marg_right_5 editPop ico ico_preview" name="info_<?=$id?>" id="info_<?=$id?>" />
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
        	<input type="button" class="button small_button add" name="add" id="add" value="New Shipment" />
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
