		<?php
			$data = $connect->single_result_array("SELECT * FROM personnel WHERE empno = '{$empno}'");
			
			$select = new Select();
			$select_type =  $select->option_query(
				'personnel_type', 				// table name
				'type',  						// name='$name' 
				'type', 						// id='$id'
				'id',							// value='$value'
				'type_name',					// option name
				$data['type'],				// default selected value
				'active = "1"',					// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose position...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
		?>
			<script type="text/javascript">
			$(document).ready(function() {
				$(".datepicker").datetimepicker({
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false,
		    		maxTime: '00:00:00',
		    		minime: '00:00:00',
		    		timeFormat: 'hh:mm:ss'
		    	});

				$('#add-type').click(function() {					
					if ($('#add_type_container').hasClass('hidden')){
						$('#add_type_container').removeClass('hidden');
					} else {
						$('#add_type_container').addClass('hidden');
					}
				});
				
				$('#submit-add-type').click(function() {
					var add_type = $('#add_type').val();
										
					if (add_type != ''){
						$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-personnel-type&type="+ add_type, function(data){
							populateOption(data);	    					
						});
					} else {
						$.noti('error', 'Please enter new position.');
					}
				});

				function populateOption(data){
					var len = data.length;
					var option = "<option val='0'>Choose position...</option>";
					
					if (len > 0){
    					for (x=0;x<len;x++){
        					selected = data[x].type_name == $('#add_type').val() ? "selected='selected'":"";
    						option += '<option ' + selected + ' value="' + data[x].id + '">' + data[x].type_name + '</option>';
    					}
					}

					$('#type').html(option);
					$('#add_type').val('');
					$('#add_type_container').addClass('hidden');
				}
			});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-personnel" id="editForm">
        	<input type="hidden" name="id" value="<?=$data['id']?>" />
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Personnel : Edit Personnel</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">General Information:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right">
                	<tr>
                    	<td width="120" class="pad_left_15">Personnel ID: </td>
                        <td><input id="empno" name="empno" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['empno']?>" /></td>
                        <td width="420"><label for="empno" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Firstname: </td>
                        <td><input id="firstname" name="firstname" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['firstname']?>" /></td>
                        <td ><label for="firstname" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Lastname: </td>
                        <td><input id="lastname" name="lastname" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['lastname']?>" /></td>
                        <td><label for="lastname" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Middlename: </td>
                        <td><input id="middlename" name=""middlename"" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['middlename']?>" /></td>
                        <td><label for=""middlename"" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30" valign="top">Address: </td>
                        <td><textarea id="address" name="address" class="textarea default_textarea"><?=$data['address']?></textarea></td>
                        <td><label for="address" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Gender: </td>
                        <td>
	                        <select id="gender" name="gender" class="selectoption default_select">
	                            <? for ($i=0;$i<$genderdatacount;$i++): ?>
	                            <option value="<?=$genderdata[$i]['value']?>" <?=$data['gender'] == $genderdata[$i]['value'] ? 'selected':''?>><?=$genderdata[$i]['name']?></option>
	                            <? endfor; ?>
							</select>
						</td>
                        <td><label for="gender" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30" valign="top">Position: </td>
                        <td>
                        	<?=$select_type?>
	                    	<input type="button" class="button small_button" value="+ Add Type" id="add-type">
							<span id="add_type_container" class="hidden">
								<input id="add_type" name="add_type" type="text" class="inputtext default_inputtext marg_top_5" maxlength="50" value="" placeholder="Add new position..." />
								<input type="button" class="button small_button marg_top_5" value="Submit" id="submit-add-type">
							</span>
                        </td>
                        <td class="line_30" valign="top"><label for="type" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Hire date: </td>
                        <td><input id="hire" name="hire" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=$data['hire']?> " /></td>
                        <td><label for="hire" generated="false" class="error"></label></td>
                    </tr>
                </table>    
                    
                 <table width="100%" border="0" cellpadding="5" cellspacing="0" class="table_solid_bottom table_solid_left table_solid_right">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Contact Information:</th>
            		</tr>
            	</table>
            	
            	<table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="120"  class="pad_left_15">Mobile No.: </td>
                        <td><input id="mobile" name="mobile" type="text" class="inputtext default_inputtext isNumeric" maxlength="50" value="<?=$data['mobile']?>" /></td>
                        <td width="420"><label for="mobile" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Landline: </td>
                        <td><input id="landline" name="landline" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['landline']?>" /></td>
                        <td><label for="landline" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Email: </td>
                        <td><input id="user_email" name="user_email" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['sendmail']?>" /></td>
                        <td><label for="user_email" generated="false" class="error"></label></td>
                    </tr>
                    
                </table>
                
                <table width="100%" border="0" cellpadding="5" cellspacing="0" class="table_solid_bottom table_solid_left table_solid_right">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Social ID:</th>
            		</tr>
            	</table>
            	
            	<table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="120" class="pad_left_15">SSS: </td>
                        <td><input id="sss" name="sss" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['sss']?>" /></td>
                        <td width="420"><label for="sss" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">PAGIBIG: </td>
                        <td><input id="pagibig" name="pagibig" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['pagibig']?>" /></td>
                        <td><label for="pagibig" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">TIN: </td>
                        <td><input id="tin" name="tin" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['tin']?>" /></td>
                        <td><label for="tin" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Save Personnel" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#editForm").validate({
					rules: {
						empno : {
							required: true,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=personnel-empno&id=<?=$data['id']?>" 
						},
						firstname : {
							required: true
						},
						user_email : {
							email: true
						},
						gender: {
							required: true,
							notEqual: 0
						},
						type: {
							required: true,
							notEqual: 0
						}
					},
					messages: {
						empno : {
							required: "Please provide a personnel ID.",
							remote: "Personnel ID already in use."
						},
						firstname : {
							required: "Please provide a firstname."
						},
						user_email : {
							email: "Please provide a valid email address."
						},
						gender: {
							required: "Please select gender.",
							notEqual: "Please select gender."
						},
						type: {
							notEqual: "Please select a position."
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