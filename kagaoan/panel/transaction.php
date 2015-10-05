<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['panel']) || empty($_GET['section'])): 
	include dirname(__FILE__) . "/error.php";
else:
	switch($section):
		// TRANSACTION
		case 'deduction':
		case 'manage':
		case 'report':	
			$filter  = "";
			$filter .= $filter_search != '' ? "&filter_search=".$filter_search:"";
			$filter .= $filter_sort != '' ? "&filter_sort=".$filter_sort:"";
			$filter .= $sort_limit != '' ? "&sort_limit=".$sort_limit:"";
			$filter .= $filter_dir != '' ? "&filter_dir=".$filter_dir:"";
			$filter .= $filter_soa != '' ? "&filter_soa=".$filter_soa:"";
			$filter .= $filter_plate != '' ? "&filter_plate=".$filter_plate:"";
			$filter .= $filter_type != '' ? "&filter_type=".$filter_type:"";
			$filter .= $filter_from != '' ? "&filter_from=".$filter_from:"";
			$filter .= $filter_to != '' ? "&filter_to=".$filter_to:"";
		?>	
		<script type="text/javascript">
			$(document).ready(function() {
				ajaxLoad("<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$panel?>&control=<?=$section?><?=$filter?>","GET");
			});
		</script>
		
		<div id="loadajax" align="center">
			<div class="spacer_100 clean"><!-- SPACER --></div>
			<img src="<?=__IMAGE__?>load.gif" class="clean" />
			<div class="spacer_5 clean"><!-- SPACER --></div>
			<span class="shadow pt_8">Please wait...</span>
		</div>
		
		<div id="ajax" class="hidden"></div>
		<?
		break;
		// EOF TRANSACTION
		
		default:
			include dirname(__FILE__) . "/error.php";
		break;
	endswitch;
endif;
?>


