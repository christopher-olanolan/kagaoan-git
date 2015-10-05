<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly"); 

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
	?>
	<!-- FOOTER -->
	<div style="width:100%; height: 85px;" class="footer clean" align="center">
		<div class="main-width" align="center">
			<div class="spacer_22 clean"><!-- SPACER --></div>
			<div style="width: 100%;" align="right" class="white pt_8"><? /* Created by drexmod */?></div>
		</div>
	</div>
	<!-- EOF FOOTER -->
	<?
endif;
?>