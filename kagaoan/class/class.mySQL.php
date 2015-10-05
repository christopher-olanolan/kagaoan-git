<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

class MySQL {
	var $UN = ""; 
	var $PW = "";
	var $DBHOST = "";
	var $DBNAME = "";

	function connect($DB_un = "", $DB_pw = "", $DB_name = "", $host = "") {
		if (!empty($DB_un)) $this->UN = $DB_un;
		if (!empty($DB_pw)) $this->PW = $DB_pw;
		if (!empty($DB_name)) $this->DBNAME = $DB_name; 
		if (!empty($host)) $this->DBHOST = $host;
		
		$this->link = mysql_connect($this->DBHOST, $this->UN, $this->PW);
		mysql_select_db($this->DBNAME);
		
		return $this->link;
	}
	
	function result_to_array(&$result) {
		if ($result):
			$arrResultData=array(); $rCnt=0;
			while ($row = mysql_fetch_assoc($result)):
				for ($i = 0; $i < mysql_num_fields($result); $i++):
					$table_fieldname=mysql_field_name($result, $i);
					$arrResultData[$rCnt][$table_fieldname] = $this->revert_quotes($row[$table_fieldname]);
				endfor;
				$rCnt++;
			endwhile;
			$retVal=$arrResultData;
		else:
			$retVal = "DB Error: ".mysql_error();
		endif;
		
		return $retVal;
	}
	
	function get_array_result($query) {
		$result=mysql_query($query);
		return $this->result_to_array($result);
	}
	
	function single_result_array($query) {
		if (!empty($query)):
			$result=mysql_query($query);
			if (!empty($result) && mysql_num_rows($result)>0):
				$retval=mysql_fetch_assoc($result);
			else:
				$retval = "DB Error: ".mysql_error();
			endif;	
		else:
			$retval = "DB Error: Empty Query String.";
		endif;
		
		return $retval;
	}
	
	/* $ins_FnV = Insert Fields and Value
		Example: 	$arrInsert[fieldname] = "value";
	*/
	function insert($ins_FnV, $tablename, $allowed_tags='') {
		if (!is_array($ins_FnV)) return "Error: Not valid Inserts";
		
		$table_fields=$this->get_fields($tablename);
		$arrTMP_=$this->match_field($ins_FnV,$tablename);
		
		$fields=""; $values="";
		foreach($arrTMP_ as $ik => $iv):
			if (!empty($fields)) $fields.=","; $fields.="`".$ik."`";
			if (!empty($values)) $values.=","; $values.= $iv;
		endforeach;
		
		mysql_query("INSERT INTO ".$tablename."($fields) VALUES($values);",$this->link);
		return mysql_error($this->link);
	}
	
	//same as function insert()
	function update($ins_FnV, $tablename, $condition) {
		if (!is_array($ins_FnV)) return "Error: Not valid Inserts";
		if (empty($condition)) return "Error: null conditions are now allowed";
		
		$table_fields=$this->get_fields($tablename);
		$arrTMP_=$this->match_field($ins_FnV,$tablename);
		
		if (is_array($arrTMP_) && (count($arrTMP_)>0)):
			$sets="";
			foreach($arrTMP_ as $ik => $iv):
				if (!empty($sets)) $sets.=",";
				$sets.="`".$ik."`=".$iv;
			endforeach;
			
			$sql="UPDATE ".$tablename." set ".$sets." where ".$condition."; \n"; //print_r($sql); 
			mysql_query($sql,$this->link);
			$retval=mysql_error($this->link);
		else:
			$retval="DB Error: No field updated.";
		endif;
		
		return $retval;
	}
	
	//For multiple records update
	function update_multiple($rs_update) {
		if (!is_array($rs_update)) return "Error: Not a valid record type to update";
		
		$sql="";
		for($rx=0; $rx<count($rs_update); $rx++):
			if (!isset($rs_update[$rx][fields])):
				$err="Error: Field values are not present at array index[$rx]"; break;
			endif;
			if (!is_array($rs_update[$rx][fields])):
				$err="Error: Fields are not in array form at array index[$rx]"; break;
			endif;
			if (!isset($rs_update[$rx][dbtable])):
				$err="Error: DB Table is not present for array index[$rx]"; break;
			endif;
			if (!isset($rs_update[$rx][condition])):
				$err="Error: SQL condition is not present for array index[$rx]"; break;
			endif;
			
			$sets="";
			foreach($rs_update[$rx][fields] as $ik => $iv):
				if (!empty($sets)) $sets.=",";
				$sets.="`".$ik."`=".$iv;
			endforeach;
			$sql.="UPDATE ".$rs_update[$rx][dbtable]." set ".$sets." where ".$rs_update[$rx][condition]."; \n";
		endfor;
		
		if (isset($err)):
			return $err;
		else:
			mysql_query($sql,$this->link);
			return mysql_error($this->link);
		endif;
	}
	
	function get_fields($DBTable) {
		$query=mysql_query("SHOW COLUMNS FROM ".$DBTable,$this->link);
		
		$retval=$this->result_to_array($query);
		return $retval;
	}
	
	function put_quote($fieldtype,$value) {
		$ftype=preg_split("/[\s\(\)]/",$fieldtype);
		switch(strtolower($ftype[0])):
			case 'char':
			case 'time':
			case 'tinyblob':
			case 'tinytext':
			case 'blob':
			case 'text':
			case 'mediumtext':
			case 'mediumblob':
			case 'longblob':
			case 'longtext':
			case 'enum':
			case 'set':
			case 'datetime':
			case 'date':
			case 'varchar':
				if (!empty($value)):
					$retval=$this->quoted_format(stripslashes($value));
				else:
					$retval="''";
				endif;	
			break;
			default:
				if (empty($value)):
					$retval="0";
				else:
					$retval=$value;
				endif;	
			break;
		endswitch;
		
		return $retval;
	}
	
	function clean_quotes($data) {
		$match=array("'",'"'); $replacement=array("�","�");
		$retval=str_replace($match,$replacement,$data);
		return $retval;
	}
	
	function revert_quotes($data) {
		$match=array("�","�"); $replacement=array("'",'"');
		$retval=str_replace($match,$replacement,$data);
		return $retval;
	}
	
	function string_quote($string) {
		if (($string{0}=="'") && ($string{strlen($string)-1}=="'")):
			$string=$this->clean_quotes(stripslashes($string));
		else:
			$string="'".$this->clean_quotes(stripslashes($string))."'";
		endif;
			
		return $string;
	}
	
	function quoted_format($val='',$separator=null) {
		if (!is_null($separator)):
			$arrVal=explode($separator,$val); $retval="";
			foreach($arrVal as $value):
				if (!empty($retval)) $retval.=$separator;
				$retval.=$this->string_quote($value);
			endforeach;
		else:
			$retval=$this->string_quote($val);
		endif;
		
		return $retval;
	}
	
	function match_field($arr_data,$db_table,$quoted_out=1) {
		$table_fields=$this->get_fields($db_table); 
		for ($i = 0; $i < count($table_fields); $i++):
			if (isset($arr_data[$table_fields[$i][Field]])):
				if ($quoted_out):
					$retval[$table_fields[$i][Field]] = $this->put_quote($table_fields[$i][Type],$arr_data[$table_fields[$i][Field]]);
				else:
					$retval[$table_fields[$i][Field]] = $arr_data[$table_fields[$i][Field]];
				endif;
			endif;
		endfor;
		
		return $retval;
	}
	
	function execute($query) {
		mysql_query($query,$this->link);
		return mysql_error($this->link);
	}
	
	function count_records($query) {
		$query = mysql_query($query,$this->link);
		$count = mysql_num_rows($query);
		if (!$count):
			return 0;
		else:
			return $count;
		endif;
	}
	
	function clean_query($string) { 
		if (get_magic_quotes_gpc()):
			$string = stripslashes($string); 
		endif;
		
		if (phpversion() >= '4.3.0'):
			$string = mysql_real_escape_string($string); 
		else:
			$string = mysql_escape_string($string); 
		endif;
		
		return $string;
	}
	
	function get_last_id($tablename,$field_id){
		$query = $this->single_result_array("SELECT max($field_id) AS last_id FROM $tablename");
		
		return $query['last_id'];
	}
	
	function get_next_id($tablename,$field_id){
		$query = $this->single_result_array("SELECT max($field_id) AS next_id FROM $tablename");

		return $query['next_id']+1;
	}
	
	function increment($tablename,$field_id,$increment){
		$query = $this->single_result_array("SELECT max($field_id), $increment AS increment_field FROM $tablename");

		return $query['increment_field']++;
	}
	
	/* $login_data = Login Fields and Value
		Example: array('table_field'=>'value')
	*/
	function check_login($tablename,$field_id,$login_data){
		if (!is_array($login_data)) return "Error: Not valid login value";
		
		$arrTMP_=$this->match_field($login_data,$tablename);
		$sets="";
		
		foreach($arrTMP_ as $lk => $lv):
			if (!empty($sets)) $sets.=" and "; $sets.= $lk."=".$lv;
		endforeach;
		
		$query = $this->single_result_array("select $field_id from $tablename where $sets");
		
		if($query[$field_id] == 'D' || empty($query[$field_id])):
			return false;
		else:
			return $query[$field_id];
		endif;
	}
	
	function get_data($tablename,$field_id,$field_column){
		if (!is_array($field_id)) return "Error: Not valid field value";
		if (!is_array($field_column)) return "Error: Not valid column value";
		
		$arrTMP_=$this->match_field($field_id,$tablename);
		$sets="";
		$fields="";
		$result = array();
		
		foreach($arrTMP_ as $lk => $lv):
			if (!empty($sets)) $sets.=" and "; $sets.= $lk."=".$lv;
		endforeach;

		foreach ($field_column as $value):
    		if (!empty($fields)) $fields.=", "; $fields.=$value;
		endforeach;
		
		$query = $this->single_result_array("select $fields from $tablename where $sets");

		foreach ($field_column as $key => $value):
    		$result[$key] = $query[$value];
		endforeach;
		
		return $result;
	}

}
?>