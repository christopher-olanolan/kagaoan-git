<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['panel']) || empty($_GET['section'])): 
	include dirname(__FILE__) . "/error.php";
else:
	switch($section):
		// TRANSACTION
		case 'manage':
		case 'stock-out':
		case 'materials':
			$filter  = "";
			$filter .= $filter_search != '' ? "&filter_search=".$filter_search:"";
			$filter .= $filter_sort != '' ? "&filter_sort=".$filter_sort:"";
			$filter .= $sort_limit != '' ? "&sort_limit=".$sort_limit:"";
			$filter .= $filter_dir != '' ? "&filter_dir=".$filter_dir:"";
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


