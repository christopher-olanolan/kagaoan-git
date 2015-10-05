<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

if(empty($_GET['file'])): 
	include dirname(__FILE__) . "/error.php";
	exit();
else:
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		/*
		$("ul.tabs li").click(function() {
			$("ul.tabs li").removeClass("active");
			$(this).addClass("active");
			$(".tab_content").hide("slide", { direction: "right" }, 450);
			// $(".tab_content").fadeOut(400);
			var activeTab = $(this).find("a").attr("href");
			$(activeTab).show("slide", { direction: "left" }, 600);
			return false;
		});
		*/
	});
	</script>

	<!-- MENU -->
	<div class="menus" align="center">
		<div class="spacer_17 clean"><!-- SPACER --></div>
        
        <!-- MAIN MENU -->
        <ul class="tabs">
        	<li class="<?=$panel=='user'?'active':''?>"><a href="<?=__ROOT__?>/index.php?file=panel&panel=user&section=profile#user" class="main-menu">Users</a></li>
        	<li class="<?=$panel=='personnel'?'active':''?>"><a href="<?=__ROOT__?>/index.php?file=panel&panel=personnel&section=manage#personnel" class="main-menu">Personnel</a></li>
        	<li class="<?=$panel=='truck'?'active':''?>"><a href="<?=__ROOT__?>/index.php?file=panel&panel=truck&section=manage#truck" class="main-menu">Trucker</a></li>
        	<li class="<?=$panel=='transaction'?'active':''?>"><a href="<?=__ROOT__?>/index.php?file=panel&panel=transaction&section=manage#transaction" class="main-menu">Transaction</a></li>
            <li class="<?=$panel=='inventory'?'active':''?>"><a href="<?=__ROOT__?>/index.php?file=panel&panel=inventory&section=manage#inventory" class="main-menu">Inventory</a></li>
            <li class="<?=$panel=='settings'?'active':''?>"><a href="<?=__ROOT__?>/index.php?file=panel&panel=settings&section=manage#settings" class="main-menu">Settings</a></li>
			<? /* <li class="<?=$panel=='report'?'active':''?>"><a href="#report" class="main-menu">Report</a></li> */ ?>
        </ul>
        <!-- EOF MAIN MENU -->
        
        <!-- SUBMENU -->
        <div class="tab_container" align="left">
        	<div id="user" class="tab_content <?=$panel=='user'?'block':'hidden'?>">
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=user&section=profile" class="sub-menu <?=$panel=='user' && $section=='profile'?'active':''?>">Profile</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=user&section=manage" class="sub-menu <?=$panel=='user' && $section=='manage'?'active':''?>">User Management</a>
            </div>
            <div id="personnel" class="tab_content <?=$panel=='personnel'?'block':'hidden'?>">
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=personnel&section=manage" class="sub-menu <?=$panel=='personnel' && $section=='manage'?'active':''?>">Management</a>
            </div>
            <div id="truck" class="tab_content <?=$panel=='truck'?'block':'hidden'?>">
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=truck&section=manage" class="sub-menu <?=$panel=='truck' && $section=='manage'?'active':''?>">Management</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=truck&section=driver" class="sub-menu <?=$panel=='driver' && $section=='driver'?'active':''?>">Driver Management</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=truck&section=consumption" class="sub-menu <?=$panel=='truck' && $section=='consumption'?'active':''?>">Diesel Consumption</a>
            	<? /* 
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=truck&section=transaction" class="sub-menu <?=$panel=='truck' && $section=='transaction'?'active':''?>">New Transaction</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=truck&section=report" class="sub-menu <?=$panel=='truck' && $section=='report'?'active':''?>">Trucking Report</a> 
            	*/ ?>
            </div>
            <div id="transaction" class="tab_content <?=$panel=='transaction'?'block':'hidden'?>">
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=transaction&section=manage" class="sub-menu <?=$panel=='transaction' && $section=='manage'?'active':''?>">Management</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=transaction&section=deduction" class="sub-menu <?=$panel=='transaction' && $section=='deduction'?'active':''?>">Deduction</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=transaction&section=report" class="sub-menu <?=$panel=='transaction' && $section=='report'?'active':''?>">Trucker Collection Statement</a>
            </div>
            <div id="inventory" class="tab_content <?=$panel=='inventory'?'block':'hidden'?>">
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=inventory&section=manage" class="sub-menu <?=$panel=='inventory' && $section=='manage'?'active':''?>">Management</a>
                <a href="<?=__ROOT__?>/index.php?file=panel&panel=inventory&section=stock-out" class="sub-menu <?=$panel=='inventory' && $section=='stock-out'?'active':''?>">Stock Out</a>
            	<? /* <a href="<?=__ROOT__?>/index.php?file=panel&panel=inventory&section=report" class="sub-menu <?=$panel=='inventory' && $section=='report'?'active':''?>">Report</a> */ ?>
            </div>
            <div id="settings" class="tab_content <?=$panel=='settings'?'block':'hidden'?>">
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=settings&section=manage" class="sub-menu <?=$panel=='settings' && $section=='manage'?'active':''?>">Settings</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=settings&section=deduction" class="sub-menu <?=$panel=='deduction' && $section=='deduction'?'active':''?>">Deduction Settings</a>
            </div>
            <? /* 
            <div id="report" class="tab_content <?=$panel=='report'?'block':'hidden'?>">
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=report&section=stock" class="sub-menu <?=$panel=='report' && $section=='stock'?'active':''?>">Stock Availability</a>
            	<a href="<?=__ROOT__?>/index.php?file=panel&panel=report&section=sales" class="sub-menu <?=$panel=='report' && $section=='sales'?'active':''?>">Sales Report</a>
            </div>
            */ ?>
        </div>
        <!-- EOF SUBMENU -->
    </div>
	<!-- EOF MENU -->
<?
endif;
?>