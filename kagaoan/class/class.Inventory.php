<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

class Inventory {

	function select($id='0', $connect, $type='0', $quantity='0', $stock_id='0'){
		if ($id != '0'):
			if ($type == '0'):
				$result = $this->__sum($id, $connect);
			else:
				$result = $this->__diff($id, $connect);
			endif;
		else:			
			if ($type == '0'):
				$result = $this->__sum($id, $connect, $quantity, $stock_id);
			else:
				$result = $this->__diff($id, $connect, $quantity, $stock_id);
			endif;
		endif;
		
		return $result;
	}
	
	function __sum($id, $connect, $quantity='0', $stock_id='0'){
		if ($id != '0'):
			$requisition = $this->__requisition($id, $connect);
			$qty = $requisition['qty'];
			$stock_id = $requisition['stock_id'];
			$active = $requisition['active'];
		else:
			$active = '1';
			$qty = $quantity;
		endif;
		
		if ($active == '1'):
			$inventory = $this->__inventory($stock_id, $connect);
			$stocks = $inventory['stocks'];
			$total = (int)$stocks + (int)$qty;
			
			$data = array('stocks' => (int) $total);
			return $connect->update($data, inventory, "id = '$stock_id'");
		else:
			return NULL;
		endif;
		
	}
	
	function __diff($id, $connect,  $quantity='0', $stock_id='0'){		
		if ($id != '0'):
			$requisition = $this->__requisition($id, $connect);
			$qty = $requisition['qty'];
			$stock_id = $requisition['stock_id'];
			$active = $requisition['active'];
		else:
			$active = '2';
			$qty = $quantity;
		endif;
		
		if ($active == '2'):
			$inventory = $this->__inventory($stock_id, $connect);
			$stocks = $inventory['stocks'];
			$total = (int)$stocks - (int)$qty;
			
			$data = array('stocks' => (int) $total);
			return $connect->update($data, inventory, "id = '$stock_id'");
		else:
			return NULL;
		endif;
	}
	
	function __requisition($id, $connect){
		$data = $connect->single_result_array("SELECT stock_id, qty, active FROM requisition WHERE id = '{$id}'");
		
		return $data;
	}
	
	function __inventory($id, $connect){
		$data = $connect->single_result_array("SELECT stocks FROM inventory WHERE id = '{$id}'");
		
		return $data;
	}
}
?>