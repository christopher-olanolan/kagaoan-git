<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

$dataReport = array (
	array ('value'=>'all','name'=>'All'),
	array ('value'=>'1','name'=>'Year'),
	array ('value'=>'2','name'=>'Month'),
	array ('value'=>'3','name'=>'Week'),
	array ('value'=>'4','name'=>'Day'),
	array ('value'=>'5','name'=>'Date Range')
);
$dataReportcount = count($dataReport);

$dataMonth = array (
	array ('value'=>'1','name'=>'January'),
	array ('value'=>'2','name'=>'February'),
	array ('value'=>'3','name'=>'March'),
	array ('value'=>'4','name'=>'April'),
	array ('value'=>'5','name'=>'May'),
	array ('value'=>'6','name'=>'June'),
	array ('value'=>'7','name'=>'July'),
	array ('value'=>'8','name'=>'August'),
	array ('value'=>'9','name'=>'September'),
	array ('value'=>'10','name'=>'October'),
	array ('value'=>'11','name'=>'November'),
	array ('value'=>'12','name'=>'December')
);
$dataMonthcount = count($dataMonth);

$dataMonthZero = array (
	array ('value'=>'01','name'=>'January'),
	array ('value'=>'02','name'=>'February'),
	array ('value'=>'03','name'=>'March'),
	array ('value'=>'04','name'=>'April'),
	array ('value'=>'05','name'=>'May'),
	array ('value'=>'06','name'=>'June'),
	array ('value'=>'07','name'=>'July'),
	array ('value'=>'08','name'=>'August'),
	array ('value'=>'09','name'=>'September'),
	array ('value'=>'10','name'=>'October'),
	array ('value'=>'11','name'=>'November'),
	array ('value'=>'12','name'=>'December')
);
$dataMonthZerocount = count($dataMonthZero);

$dataLimit = array (	
	array ('value'=>'all','name'=>'All'),
	// array ('value'=>'5','name'=>'5'),
	array ('value'=>'10','name'=>'10'),
	array ('value'=>'20','name'=>'20'),
	array ('value'=>'30','name'=>'30'),
	array ('value'=>'40','name'=>'40'),
	array ('value'=>'50','name'=>'50')
	// array ('value'=>'100','name'=>'100')
);
$dataLimitcount = count($dataLimit);

$yesnodata = array (
	array ('value'=>'all','name'=>'All'),
	array ('value'=>'yes','name'=>'Yes'),
	array ('value'=>'no','name'=>'No')
);
$yesnodatacount = count($yesnodata);


$genderdata = array (
	array ('value'=>'0','name'=>'Choose gender...'),
	array ('value'=>'M','name'=>'Male'),
	array ('value'=>'F','name'=>'Female')
);
$genderdatacount = count($genderdata);

$statusdata = array (
	array ('value'=>'all','name'=>'All'),
	array ('value'=>'1','name'=>'Active'),
	array ('value'=>'0','name'=>'Inactive')
);
$statusdatacount = count($statusdata);

$personneldata = array (
	array ('value'=>'all','name'=>'All'),
	array ('value'=>'1','name'=>'Owner'),
	array ('value'=>'2','name'=>'Driver'),
	array ('value'=>'3','name'=>'Others')
);
$personneldatacount = count($personneldata);

$paymentstatusdata = array (
	array ('value'=>'Paid','name'=>'Paid'),
	array ('value'=>'Unpaid','name'=>'Unpaid')
);
$paymentstatusdatacount = count($paymentstatusdata);

$userstatusdata = array(
	array ('value'=>'01','name'=>'Pending'),
	array ('value'=>'02','name'=>'Active'),
	array ('value'=>'03','name'=>'Delete')
);
$userstatusdatacount = count($userstatusdata);

$usersortdata = array (
	array ('value'=>'id','name'=>'Default'),
	array ('value'=>'username','name'=>'Username'),	
	array ('value'=>'sendmail','name'=>'Email'),
	array ('value'=>'last_login','name'=>'Last login'),
	array ('value'=>'login','name'=>'No. of login')
);
$usersortdatacount = count($usersortdata);

$personelsortdata = array (
	array ('value'=>'id','name'=>'Default'),
	array ('value'=>'empno','name'=>'Personnel No.'),
	array ('value'=>'firstname','name'=>'Firstname'),	
	array ('value'=>'lastname','name'=>'Lastname'),
	array ('value'=>'hire','name'=>'Date Hired')
);
$personelsortdatacount = count($personelsortdata);

$trucksortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t1.plate','name'=>'Plate No.'),
	array ('value'=>'t1.truck_model','name'=>'Truck Model'),
	array ('value'=>'t1.truck_type','name'=>'Truck Type'),	
	array ('value'=>'t2.firstname','name'=>'Operator')
);
$trucksortdatacount = count($trucksortdata);

$driversortdata = array (
	array ('value'=>'t2.id','name'=>'Default'),
	array ('value'=>'t1.plate','name'=>'Plate No.'),
	array ('value'=>'t1.truck_model','name'=>'Truck Model'),
	array ('value'=>'t1.truck_type','name'=>'Truck Type'),	
	array ('value'=>'t3.firstname','name'=>'Driver'),
	array ('value'=>'t2.assigned','name'=>'Date')
);
$driversortdatacount = count($driversortdata);

$consumptionsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t2.plate','name'=>'Plate No.'),
	array ('value'=>'t1.consumption_date','name'=>'Date'),
	array ('value'=>'t1.liters','name'=>'Liters'),	
	array ('value'=>'t1.price','name'=>'Price')
);
$consumptionsortdatacount = count($consumptionsortdata);

$deductionsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t2.plate','name'=>'Plate No.'),
	array ('value'=>'t3.type_name','name'=>'Type')
);
$deductionsortdatacount = count($deductionsortdata);

$shipmentsortdata = array (
		array ('value'=>'t1.id','name'=>'Default'),
		array ('value'=>'t1.shipment','name'=>'Shipment No.'),
		array ('value'=>'t1.rate','name'=>'Rate'),
		array ('value'=>'t1.shipment_date','name'=>'Date'),
		array ('value'=>'t2.plate','name'=>'Plate No.'),
		array ('value'=>'t3.location','name'=>'Source'),
		array ('value'=>'t4.location','name'=>'Destination')
);
$shipmentsortdatacount = count($shipmentsortdata);

$transactionsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t1.soa','name'=>'SOA No.'),
	array ('value'=>'t1.urc_doc','name'=>'URC Document'),
	array ('value'=>'t2.plate','name'=>'Plate No.'),
	array ('value'=>'t3.location','name'=>'Source'),
	array ('value'=>'t4.location','name'=>'Destination')
);
$transactionsortdatacount = count($transactionsortdata);

$paymentsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t2.soa','name'=>'SOA No.'),
	array ('value'=>'t1.urc_doc','name'=>'URC Document'),
	array ('value'=>'t3.location','name'=>'Source'),
	array ('value'=>'t4.location','name'=>'Destination'),
	array ('value'=>'t6.plate','name'=>'Plate No.')	
);
$paymentsortdatacount = count($paymentsortdata);

$computationsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t3.material','name'=>'Item Code'),
	array ('value'=>'t3.description','name'=>'Item Description'),
	array ('value'=>'t1.case','name'=>'Cases Loaded')
);
$computationsortdatacount = count($computationsortdata);

$inventorysortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t1.name','name'=>'Name'),
	array ('value'=>'t2.brand_name','name'=>'Brand'),
	array ('value'=>'t1.stocks','name'=>'Stocks'),
	array ('value'=>'t1.purchase_date','name'=>'Purchase Date'),
	array ('value'=>'t1.supplier','name'=>'Supplier')
);
$inventorysortdatacount = count($inventorysortdata);

$materialsortdata = array (
		array ('value'=>'t1.id','name'=>'Default'),
		array ('value'=>'t1.material','name'=>'Material'),
		array ('value'=>'t1.description','name'=>'Description'),
		array ('value'=>'t1.gross_weight','name'=>'Gross Weight'),
		array ('value'=>'t1.volume','name'=>'Volume')
);
$materialsortdatacount = count($materialsortdata);

$stockoutsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t2.name','name'=>'Name'),
	array ('value'=>'t4.plate','name'=>'Plate no.'),
	array ('value'=>'t3.brand_name','name'=>'Brand'),
	array ('value'=>'t2.stocks','name'=>'Stocks'),
	array ('value'=>'t1.requisition_date','name'=>'Date')
);
$stockoutsortdatacount = count($stockoutsortdata);

$personneltypesortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t1.type_name','name'=>'Type Name')
);
$personneltypesortdatacount = count($personneltypesortdata);

$deductionpercentsortdata = array (
		array ('value'=>'id','name'=>'Default'),
		array ('value'=>'display_name','name'=>'Name'),
		array ('value'=>'display_value','name'=>'Value')
);
$deductionpercentsortdatacount = count($deductionpercentsortdata);

$locationsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t1.location','name'=>'Location')
);
$locationsortdatacount = count($locationsortdata);

$brandsortdata = array (
	array ('value'=>'t1.id','name'=>'Default'),
	array ('value'=>'t1.brand_name','name'=>'Brand Name')
);
$brandsortdatacount = count($brandsortdata);
?>