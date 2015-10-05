		<?php
			$select = new Select();
			$select_plate =  $select->option_query(
				'truck', 						// table name
				'truck_id',  					// name='$name' 
				'truck_id', 					// id='$id'
				'id',							// value='$value'
				'plate',						// option name
				'0',							// default selected value
				'active = "1" ',				// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose plate...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
			$select_type =  $select->option_query(
				'deduction_type', 				// table name
				'deduction_id',  				// name='$name' 
				'deduction_id', 				// id='$id'
				'id',							// value='$value'
				'type_name',					// option name
				'0',							// default selected value
				'active = "1"',					// query condition(s)  
				'type_name',					// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose type...',				// default null option name 'Choose option...'	
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
				
				var today = new Date();
				var dd = today.getDate();
				var mm = today.getMonth()+1;
				var yyyy = today.getFullYear();
				var maxdate = yyyy + '-' + mm + '-' + dd;
				    
				$("#date_from").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false,
		    		maxDate: $("#date_to").val(),
		    		onSelect: function() {
			            var date = $(this).datepicker('getDate');
			            date.setDate(date.getDate());
			            $("#date_to").datepicker( "option", "minDate", date);
			        }
		    	});

				$("#date_to").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false,
		    		minDate: $("#date_from").val(),
		    		onSelect: function() {
			            var date = $(this).datepicker('getDate');
			            date.setDate(date.getDate());
			            $("#date_from").datepicker( "option", "maxDate", date);
			        }
		    	});

				$('#add-deduction').click(function() {					
					if ($('#add_deduction_container').hasClass('hidden')){
						$('#add_deduction_container').removeClass('hidden');
					} else {
						$('#add_deduction_container').addClass('hidden');
					}
				});
				
				$('#submit-add-deduction').click(function() {
					var add_deduction = $('#type_name').val();
										
					if (add_deduction != ''){
						$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-deduction-type&type_name="+ add_deduction, function(data){
							populateOption(data);	    					
						});
					} else {
						$.noti('error', 'Please enter new deduction.');
					}
				});

				function populateOption(data){
					var len = data.length;
					var option = "<option val='0'>Choose deduction...</option>";
					
					if (len > 0){
    					for (x=0;x<len;x++){
        					selected = data[x].type_name == $('#type_name').val() ? "selected='selected'":"";
    						option += '<option ' + selected + ' value="' + data[x].id + '">' + data[x].type_name + '</option>';
    					}
					}

					$('#deduction_id').html(option);
					
					$('#type_name').val('');
					$('#add_deduction_container').addClass('hidden');
				}	
			});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=add-deduction" id="addForm">
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Add Deduction</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Deduction Details:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="120" class="pad_left_15">Plate no.: </td>
                        <td><?=$select_plate?></td>
                        <td width="320"><label for="truck_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30" valign="top">Deduction type: </td>
                        <td>
                        	<?=$select_type?>
                        	<input type="button" class="button small_button" value="+ Add Deduction Type" id="add-deduction">
							<span id="add_deduction_container" class="hidden">
								<input id="type_name" name="type_name" type="text" class="inputtext default_inputtext marg_top_5" maxlength="50" value="" placeholder="Add new deduction type..." />
								<input type="button" class="button small_button marg_top_5" value="Submit" id="submit-add-deduction">
							</span>
                        </td>
                        <td class="line_30" valign="top"><label for="deduction_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30" valign="top">Description: </td>
                        <td><textarea id="description" name="description" class="textarea default_textarea"></textarea></td>
                        <td><label for="description" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Date From: </td>
                        <td><input id="date_from" name="date_from" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=date("Y-m-")?>01" /></td>
                        <td><label for="date_from" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Date To: </td>
                        <td><input id="date_to" name="date_to" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=date("Y-m-d")?>" /></td>
                        <td><label for="date_to" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Price: </td>
                        <td><input id="price" name="price" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="price" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Add Deduction" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#addForm").validate({
					rules: {
						truck_id : {
							required: true,
							notEqual: 0
						},
						deduction_id : {
							required: true,
							notEqual: 0
						},
						date_from: {
							required: true
						},
						date_to: {
							required: true
						},
						price : {
							required: true
						}
					},
					messages: {
						truck_id : {
							required: "Please select plate no.",
							notEqual: "Please select plate no."
						},
						deduction_id: {
							required: "Please select deduction type.",
							notEqual: "Please select deduction type."
						},
						date_from: {
							required: "Please enter date start."
						},
						date_to: {
							required: "Please enter date end."
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
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=deduction","GET");
			    });
			});
            </script>
            <?