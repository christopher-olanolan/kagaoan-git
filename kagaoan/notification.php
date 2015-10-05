<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly"); 

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
	?>
	<!-- NOTIFICATION -->
	<div id="notification" align="center">
		<div id="notification-width">
			<div id="notification-inner"><div id="notification-container"></div></div>
		</div>
	</div>
	<!-- EOF NOTIFICATION -->
	<?
endif;
?>