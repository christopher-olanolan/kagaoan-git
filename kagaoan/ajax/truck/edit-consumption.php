			<?php
			$data = $connect->single_result_array("SELECT * FROM consumptions WHERE id = '{$id}'");
			
			$select = new Select();
			$select_plate =  $select->option_query(
				'truck', 						// table name
				'truck_id',  					// name='$name' 
				'truck_id', 					// id='$id'
				'id',							// value='$value'
				'plate',						// option name
				$data['truck_id'],						// default selected value
				'active = "1" ',				// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose plate...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
		?>
			<script type="text/javascript">
			$(document).ready(function() {
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1;
				var yyyy = today.getFullYear();
				var maxdate = yyyy + '-' + mm + '-' + dd;
				
				$(".datepicker").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false,
		    		maxDate: maxdate
		    	});
			});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-consumption" id="editForm">
        	<input type="hidden" name="id" value="<?=$data['id']?>" />
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Edit Diesel Comsumption</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Comsumption Details:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="220" class="pad_left_15">Plate no.: </td>
                        <td><?=$select_plate?></td>
                        <td width="420"><label for="truck_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Date: </td>
                        <td><input id="consumption_date" name="consumption_date" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=$data['consumption_date']?>" /></td>
                        <td><label for="consumption_date" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Liters: </td>
                        <td><input id="liters" name="liters" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['liters']?>" /></td>
                        <td><label for="liters" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Price per liter: </td>
                        <td><input id="price" name="price" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['price']?>" /></td>
                        <td><label for="price" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Update Consumption" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#editForm").validate({
					rules: {
						truck_id : {
							required: true,
							notEqual: 0
						},
						consumption_date: {
							required: true
						},
						liters : {
							required: true
						},
						price : {
							required: true
						},
					},
					messages: {
						truck_id : {
							required: "Please select plate no.",
							notEqual: "Please select plate no."
						},
						consumption_date: {
							required: "Please choose date."
						},
						liters: {
							required: "Please enter liter."
						},
						price: {
							required: "Please enter price."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=consumption","GET");
			    });
			});
            </script>
            <?