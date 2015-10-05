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
			
			$option_name = array('firstname','lastname');
			$select_driver =  $select->option_query(
				'personnel', 					// table name
				'driver_id',  					// name='$name' 
				'driver_id', 					// id='$id'
				'id',							// value='$value'
				$option_name,					// option name
				'0',							// default selected value
				'active = "1" AND type = "2" ',	// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Assign driver...',			// default null option name 'Choose option...'	
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
		    		maxTime: '',
		    		minime: '',
		    		timeFormat: ''
		    	});
			});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=add-driver" id="addForm">
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Truck : Assign Driver</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Driver Information:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="220" class="pad_left_15">Plate no.: </td>
                        <td><?=$select_plate?></td>
                        <td width="420"><label for="truck_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Assign Driver: </td>
                        <td><?=$select_driver?></td>
                        <td><label for="driver_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Assign Date: </td>
                        <td><input id="assigned" name="assigned" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=date("Y-m-d")?>" /></td>
                        <td><label for="assigned" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Assign Driver" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#addForm").validate({
					rules: {
						truck_id : {
							required: true,
							notEqual: 0,
							remote: {
						        url: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=check-driver",
						        type: "get",
						        data: {
						        	driver_id: function() {
						            	return $("#driver_id option:selected").val()
									},
									truck_id: function() {
						            	return $("#truck_id option:selected").val()
									},
									assigned: function() {
						            	return $("#assigned").val()
									}
								}
							}
						},
						driver_id : {
							required: true,
							notEqual: 0,
							remote: {
						        url: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=check-driver",
						        type: "get",
						        data: {
						        	driver_id: function() {
						            	return $("#driver_id option:selected").val()
									},
									truck_id: function() {
						            	return $("#truck_id option:selected").val()
									},
									assigned: function() {
						            	return $("#assigned").val()
									}
								}
							}
						},
						assigned: {
							required: true,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=check-date"
						}
					},
					messages: {
						truck_id : {
							required: "Please select plate no.",
							notEqual: "Please select plate no.",
							remote: "Truck already assigned to this driver."
						},
						driver_id : {
							required: "Please assign driver.",
							notEqual: "Please assign driver.",
							remote: "Driver already assigned to this truck."
						},
						assigned: {
							required: "Please choose date.",
							remote: "Invalid date format."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=driver","GET");
			    });
			});
            </script>
        <?