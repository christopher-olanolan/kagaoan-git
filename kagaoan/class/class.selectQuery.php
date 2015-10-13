<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

class Select extends mySQL {

	/**
	 * Contruct select option from database query.
	 * 
	 * @param $table
	 *            table name
	 * @param $name and $id
	 *			  select name='$name' and id='$id'
	 * @param $value
	 *            field name for option value
	 * @param $name
	 *            field name for option name
	 * @param $selected
	 *            default selected value
	 * @param $where
	 *            query condition(s)
	 * @param $order
	 *            'order by' field name 
	 * @param $dir
	 *            sort order 'asc' or 'desc'
	 * @param $class
	 *            class value(s)
	 * @param $default
	 *            default null option name 'Choose option...'
	 * @param $type
	 *            select type 1 = multiple or 0 = single
	 */
	function option_query($table, $id, $name, $key, $value, $selected, $where='', $order='', $dir='', $class='', $default='', $type='0', $group='0'){
		$where = $where != "" ? " WHERE $where ":"";
		$order = $order != "" ? " ORDER BY $order $dir ":"";
		
		if (is_array($value)):
			$c = 0;
			$val = "";
			for ($n=0;$n<count($value);$n++):
				$c++;
				$val .= $value[$n];
				$val .= $c==count($value) ? "" : ",";
			endfor;
		else:
			$val = $value;
		endif;
		
		$distinct = $group == "1" ? " DISTINCT ($val) ":"";
		$group = $group == "1" ? " GROUP BY $val ":"";
		
		$options = $this->open_select($id, $name, $class, $default, $type);
		$row = $this->get_array_result("SELECT $distinct $key, $val FROM $table $where $group $order");
		$count = count($row);
		$invalid = array ("+","=");
		
		if (is_array($value)):
		endif;
		
		for ($x=0;$x<$count;$x++):
			$active = $selected == $row[$x][$key] ? 'selected="selected"':'';
			
			if (is_array($value)):
				$option_name = "";
				$c = 0;
				for ($n=0;$n<count($value);$n++):
					$c++;
				
					if ($value[$n] == "transaction_date"):
						$transaction_date = strtotime($row[$x][$value[$n]]);
						$date = date('F d, Y', $transaction_date);
						$option_name .= "(".$date.")";
					elseif ($value[$n] == "source"):
						$source = $this->single_result_array("SELECT location FROM location WHERE id = '{$row[$x][$value[$n]]}'");
						$option_name .= $source['location'];
					elseif ($value[$n] == "destination"):
						$destination = $this->single_result_array("SELECT location FROM location WHERE id = '{$row[$x][$value[$n]]}'");
						$option_name .= "&mdash; " . $destination['location'];
					elseif ($value[$n] == "truck_id"):
						$truck_id = $this->single_result_array("SELECT plate FROM truck WHERE id = '{$row[$x][$value[$n]]}'");
						$option_name .= $truck_id['plate'];
					else:
						$option_name .= str_replace($invalid,'',$row[$x][$value[$n]]);
					endif;
					
					$option_name .= $c==count($value) ? "" : " ";
				endfor;
			else:
				$option_name = str_replace($invalid,'',$row[$x][$value]);
			endif;
			
			$options .= '<option value="'.$row[$x][$key].'" '.$active.'>'.$option_name."</option>";	
		endfor;

		$options .= $this->close_select();
		
		return $options;
	}
	
	/**
	 * Contruct select option from database query.
	 * 
	 * @param $table
	 *            table name
	 * @param $join
	 *            join table name
	 * @param $join_condition
	 *            join condition
	 * @param $name and $id
	 *			  select name='$name' and id='$id'
	 * @param $value
	 *            field name for option value
	 * @param $name
	 *            field name for option name
	 * @param $selected
	 *            default selected value
	 * @param $where
	 *            query condition(s)
	 * @param $order
	 *            'order by' field name 
	 * @param $dir
	 *            sort order 'asc' or 'desc'
	 * @param $class
	 *            class value(s)
	 * @param $default
	 *            default null option name 'Choose option...'
	 * @param $type
	 *            select type 1 = multiple or 0 = single
	 */
	function option_query_join($table, $join, $join_condition, $id, $name, $key, $value, $selected, $where='', $order='', $dir='', $class='', $default='', $type='0', $group='0'){
		$where = $where != "" ? " WHERE $where ":"";
		$order = $order != "" ? " ORDER BY $order $dir ":"";
		$join = $join != "" ? " LEFT JOIN $join AS t2 ON $join_condition ":"";
		
		if (is_array($value)):
			$c = 0;
			$val = "";
			for ($n=0;$n<count($value);$n++):
				$c++;
				$val .= $value[$n];
				$val .= $c==count($value) ? "" : ",";
			endfor;
		else:
			$val = $value;
		endif;
		
		$distinct = $group == "1" ? " DISTINCT ($key), ":"";
		$group = $group == "1" ? " GROUP BY $key ":"";
		
		// logger("\"SELECT {$distinct} {$key}, {$val} FROM {$table} AS t1 {$join} {$where} {$group} {$order}\"");
		
		$options = $this->open_select($id, $name, $class, $default, $type);

		$row = $this->get_array_result("SELECT $distinct $key, $val FROM $table AS t1 $join $where $group $order");
		$count = count($row);
		$invalid = array ("+","=");
		
		if (is_array($value)):
		endif;
		
		for ($x=0;$x<$count;$x++):
			$active = $selected == $row[$x][$key] ? 'selected="selected"':'';
			
			if (is_array($value)):
				$option_name = "";
				$c = 0;
				for ($n=0;$n<count($value);$n++):
					$c++;
					$option_name .= str_replace($invalid,'',$row[$x][$value[$n]]);
					$option_name .= $c==count($value) ? "" : " ";
				endfor;
			else:
				$option_name = str_replace($invalid,'',$row[$x][$value]);
			endif;
			
			$options .= '<option value="'.$row[$x][$key].'" '.$active.'>'.$option_name."</option>";	
		endfor;

		$options .= $this->close_select();
		
		return $options;
	}
	
	/**
	 * Contruct select option from variable array.	
	 *
	 * @param $data
	 *			array ('value'=>'value','name'=>'name')
	 *			use 'value' key for option value and 'name' key for option name
	 * @param $name and $id
	 *			select name='$name' and id='$id'
	 * @param $selected
	 *			default selected value
	 * @param $class
	 *          class value(s)
	 * @param $default
	 *          default null option name 'Choose option...'
	 * @param $type
	 *          select type 1 = multiple or 0 = single
	 */
	function option_array($data, $id, $name, $selected, $class='', $default='', $type='0'){
		if (!is_array($data)) return "Error: Not valid data";
		
		$options = $this->open_select($id, $name, $class, $default, $type);
		$count = count($data);

		for ($x=0;$x<$count;$x++):
			$active = $selected == $data[$x]['value'] ? 'selected="selected"':'';
			$options .= '<option value="'.$data[$x]['value'].'" '.$active.'>'.$data[$x]['name']."</option>";	
		endfor;

		$options .= $this->close_select();
		
		return $options;
	}
	
	function open_select($id, $name, $class, $default, $type){
		$multiple = $type == '1' ? 'multiple="multiple"':'';
		$name = $type == '1' ? 'name="'.$name.'[]"': 'name="'.$name.'"';
		
		$open  = "<select $name id=\"$id\" class=\"$class\" $multiple>";
		$open .= $default != "" || $default != NULL ? "<option value=\"0\">$default</option>":"";
		return $open;
	}
	
	function close_select(){
		$close = "</select>";
		return $close;
	}
	
}
?>