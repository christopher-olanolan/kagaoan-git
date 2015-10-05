<? 
	if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");
	// printr($_GET);
	// $setting = $setting=="" ? "personnel-type":$setting;	
?>
<div style="width:100%;" align="left">
	<div class="tab_container" align="left">
		<div class="tab_content block">
			<a rel="personnel-type" class="sub-menu setting-menu <?=$setting=="personnel-type"?'active':''?>">Personnel Type</a>&nbsp;&nbsp;&nbsp;
			<a rel="location" class="sub-menu setting-menu <?=$setting=="location"?'active':''?>">Location</a>&nbsp;&nbsp;&nbsp;
			<a rel="deduction-type" class="sub-menu setting-menu <?=$setting=="deduction-type"?'active':''?>">Deduction Type</a>&nbsp;&nbsp;&nbsp;
			<a rel="brand" class="sub-menu setting-menu <?=$setting=="brand"?'active':''?>">Brand</a>
		</div>
	</div>
</div>
<div class="spacer_20 clean"></div>
<script type="text/javascript">
$(document).ready(function() {
	$('.setting-menu').click(function() {		
		ajaxLoad("<?=__ROOT__?>/index.php?file=ajax&ajax=settings&control=manage&setting=" + $(this).attr('rel'),"GET");
	});
});
</script>
<?
$connect = new MySQL();
$connect->connect(
	$config['DB'][__SITE__]['USERNAME'],
	$config['DB'][__SITE__]['PASSWORD'],
	$config['DB'][__SITE__]['DATABASE'],
	$config['DB'][__SITE__]['HOST']
);

switch ($setting):
	case "personnel-type":
	case "location":
	case "deduction-type":
	case "brand":			
		include (str_replace('//','/',dirname(__FILE__).'/') . $setting . '.php');
	break;
	
	case "edit-personnel-type":
	case "edit-location":
	case "edit-deduction-type":
	case "edit-brand":
		include (str_replace('//','/',dirname(__FILE__).'/') . 'edit.php');
	break;	
	
	default:
		include dirname(__FILE__) . "/error.php";
	break;
endswitch;
?>