<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=__TITLE__?> - <?=ucfirst(isset($process)?$process:$action)?></title>
	<? include dirname(__FILE__) . "/scripts.php"; ?>
</head>
<body>
<div style="width:100%;" align="center">
	<div class="main-width" align="center">
		<!-- LOGO -->
		<div style="width: 100%; height: 110px;" align="left" class="clean">
			<div style="width: 50%;" class="float_left clean" align="left">
				<div class="spacer_20 clean"><!-- SPACER --></div>
				<strong class="lightbrown pt_20"><?=$site['company']?></strong>
				<div class="spacer_0 clean"><!-- SPACER --></div>
				<span class="lighterbrown">
					<i><?=$site['address']?></i>
					<div class="spacer_0 clean"><!-- SPACER --></div>
					<?=$site['phone']?>
				</span>
			</div>
			
			<div style="width: 30%; color: #D4D4D4;" class="float_right clean px_12" align="right">
				<div class="spacer_10 clean"><!-- SPACER --></div>
				<div style="width: 100%; height: 10px;" class="SETDate"></div>
				<div class="spacer_40 clean"><!-- SPACER --></div>
			</div>
		</div>
		<!-- EOF LOGO -->
		
		<!-- MENU -->
		<div style="width: 100%; height: 88px;" align="left">
		</div>
		<!-- EOF MENU -->
		
		<div class="spacer_15 clean clear"><!-- SPACER --></div>
		
		<!-- CONTENT -->
		<div style="width: 100%;" align="center" id="main-height">
			<div class="spacer_100 clean"><!-- SPACER --></div>
			<img src="images/load.gif" class="clean" />
			<div class="spacer_8 clean"><!-- SPACER --></div>
			<span class="shadow pt_8">loading...</span>
		</div>
		<!-- EOF CONTENT -->
	</div>
	
	<? include (str_replace('//','/',dirname(__FILE__).'/') . 'footer.php'); ?>
</div>
</body>
</html>		
<?
endif;
?>