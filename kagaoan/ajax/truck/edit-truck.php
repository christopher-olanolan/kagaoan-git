			<?php
			$data = $connect->single_result_array("SELECT * FROM truck WHERE id = '{$id}'");
			
			$select = new Select();
			$option_name = array('firstname','lastname');
			$select_operator =  $select->option_query(
				'personnel', 					// table name
				'operator',  					// name='$name' 
				'operator', 					// id='$id'
				'id',							// value='$value'
				$option_name,					// option name
				$data['operator'],				// default selected value
				'active = "1" AND type = "1" ',	// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose operator...',			// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
		?>
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-truck" id="addForm">
        	<input type="hidden" name="id" value="<?=$data['id']?>" />
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Truck : Add Truck</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Truck Information:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="220" class="pad_left_15">Plate no.: </td>
                        <td><input id="plate" name="plate" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['plate']?>" /></td>
                        <td width="420"><label for="plate" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Truck Model: </td>
                        <td><input id="truck_model" name="truck_model" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['truck_model']?>" /></td>
                        <td ><label for="truck_model" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Truck Type: </td>
                        <td><input id="truck_type" name="truck_type" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['truck_type']?>" /></td>
                        <td ><label for="truck_type" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Operator: </td>
                        <td><?=$select_operator?></td>
                        <td><label for="operator" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Update Truck" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#addForm").validate({
					rules: {
						plate : {
							required: true,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=plate-id&id=<?=$data['id']?>" 
						},
						truck_tpe : {
							required: true
						},
						operator: {
							required: true,
							notEqual: 0
						}
					},
					messages: {
						plate : {
							required: "Please provide a plate no.",
							remote: "Plate no. already exist."
						},
						truck_tpe : {
							required: "Please provide truck type."
						},
						operator: {
							required: "Please select operator.",
							notEqual: "Please select operator."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=manage","GET");
			    });
			});
            </script>
        <?