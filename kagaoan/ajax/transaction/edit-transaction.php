		<?php
			$data = $connect->single_result_array("SELECT * FROM transaction WHERE id = '{$id}'");
			
			$select = new Select();
			$select_plate =  $select->option_query(
				'truck', 						// table name
				'truck_id',  					// name='$name' 
				'truck_id', 					// id='$id'
				'id',							// value='$value'
				'plate',						// option name
				$data['truck_id'],				// default selected value
				'active = "1" ',				// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose plate...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
			$option_name = array("firstname","lastname");
			$select_driver =  $select->option_query_join(
				'truck_driver', 				// table name
				'personnel',					// join table name
				"t1.driver_id = t2.id",			// join condition
				'driver_id',  					// name='$name' 
				'driver_id',	 				// id='$id'
				'driver_id',					// value='$value'
				$option_name,					// option name
				$data['driver_id'],				// default selected value
				't1.active = "1" ',				// query condition(s)  
				'lastname',						// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Assign driver...',				// default null option name 'Choose option...'	
				'0',							// select type 1 = multiple or 0 = single
				'1'								// distinct
			);
			
			$select_source =  $select->option_query(
				'location', 					// table name
				'source',  						// name='$name' 
				'source', 						// id='$id'
				'id',							// value='$value'
				'location',						// option name
				$data['source'],				// default selected value
				'active = "1" ',				// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose source...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
			$select_destination =  $select->option_query(
				'location', 					// table name
				'destination',  				// name='$name' 
				'destination', 					// id='$id'
				'id',							// value='$value'
				'location',						// option name
				$data['destination'],			// default selected value
				'active = "1" ',				// query condition(s)  
				'id',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose destination...',		// default null option name 'Choose option...'	
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
				    
				$("#transaction_date").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false
		    	});

				$("#delivered_date").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false
		    	});

				$('#truck_id').change(function() {					
					if ($(this).val() > 0){
						$('#tr_driver_id').removeClass('hidden');	
						
						$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-assigned-driver&truck_id=" + $(this).val(), function(data){
							populateOption(data);	    					
						});
					} else {
						$('#tr_driver_id').addClass('hidden');
					}
				});		

				function populateOption(data){
					var len = data.length;
					var option = "<option val='0'>Assign driver...</option>";
					
					if (len > 0){
    					for (x=0;x<len;x++){
    						option += '<option value="' + data[x].driver_id + '">' + data[x].firstname + ' ' + data[x].lastname + '</option>';
    					}
					} else {
						$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-all-driver", function(data){
							populateOption(data);	    					
						});
					}

					$('#driver_id').html(option);
				}

				$('#add-location').click(function() {					
					if ($('#add_location_container').hasClass('hidden')){
						$('#add_location_container').removeClass('hidden');
					} else {
						$('#add_location_container').addClass('hidden');
					}
				});
				
				$('#submit-add-location').click(function() {
					var add_location = $('#add_location').val();
										
					if (add_location != ''){
						$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-location&location="+ add_location, function(data){
							populateLocationOption(data);	    					
						});
					} else {
						$.noti('error', 'Please enter new location.');
					}
				});

				function populateLocationOption(data){
					var len = data.length;
					var option = "";
					var source_option = "<option val='0'>Choose source...</option>";
					var destination_option = "<option val='0'>Choose location...</option>";
					
					if (len > 0){
    					for (x=0;x<len;x++){
    						source_selected = data[x].location == $('#add_location').val() ? "selected='selected'":"";
        					source_option += '<option ' + source_selected + ' value="' + data[x].id + '">' + data[x].location + '</option>';

        					destination_selected = data[x].id == "<?=$data['destination']?>" ? "selected='selected'":"";
        					destination_option += '<option ' + destination_selected + ' value="' + data[x].id + '">' + data[x].location + '</option>';
    					}
					}

					$('#source').html(source_option);
					$('#destination').html(destination_option);
					
					$('#add_location').val('');
					$('#add_location_container').addClass('hidden');
				}	
			});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-transaction" id="editForm">
        	<input type="hidden" name="id" value="<?=$id?>" />
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Edit Transaction</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Transaction Details:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                    <tr>
                    	<td width="120" class="pad_left_15">Plate no.: </td>
                        <td><?=$select_plate?></td>
                        <td><label for="truck_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr id="tr_driver_id" >
                    	<td class="pad_left_15">Driver: </td>
                        <td><?=$select_driver?></td>
                        <td><label for="driver_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30" valign="top">Source: </td>
                        <td>
                        	<?=$select_source?>
                        	<input type="button" class="button small_button" value="+ Add Location" id="add-location">
							<span id="add_location_container" class="hidden">
								<input id="add_location" name="add_location" type="text" class="inputtext default_inputtext marg_top_5" maxlength="50" value="" placeholder="Add new location..." />
								<input type="button" class="button small_button marg_top_5" value="Submit" id="submit-add-location">
							</span>
                        </td>
                        <td class="line_30" valign="top"><label for="source" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Destination: </td>
                        <td><?=$select_destination?></td>
                        <td><label for="destination" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Transaction Date: </td>
                        <td><input id="transaction_date" name="transaction_date" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=$data['transaction_date']?>" /></td>
                        <td><label for="transaction_date" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Delivered: </td>
                        <td><input type="checkbox" id="delivered" name="delivered" class="checkbox" <?=$data['delivered'] == 1 ? 'checked="checked"':''?> /></td>
                        <td><label for="delivered" generated="false" class="error"></label></td>
                    </tr>
                    <tr id="tr_delivered_date" class=<?=$data['delivered'] == 1?"":"hidden"?>>
                    	<td class="pad_left_15">Delivered Date: </td>
                        <td><input id="delivered_date" name="delivered_date" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=$data['delivered_date']?>" /></td>
                        <td><label for="delivered_date" generated="false" class="error"></label></td>
                    </tr>
                    <tr  id="tr_soa" class=<?=$data['delivered'] == 1?"":"hidden"?>>
                    	<td class="pad_left_15">SOA No.: </td>
                        <td><input id="soa" name="soa" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['soa']?>" /></td>
                        <td width="420"><label for="soa" generated="false" class="error"></label></td>
                    </tr>
                    <tr id="tr_urc_doc" class=<?=$data['delivered'] == 1?"":"hidden"?>>
                    	<td class="pad_left_15">URC Document: </td>
                        <td><input id="urc_doc" name="urc_doc" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['urc_doc']?>" /></td>
                        <td><label for="urc_doc" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">No. of CS: </td>
                        <td><input id="cs" name="cs" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['cs']?>" /></td>
                        <td><label for="cs" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Rate: </td>
                        <td><input id="rate" name="rate" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['rate']?>" /></td>
                        <td><label for="rate" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Update Transaction" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#editForm").validate({
					rules: {
						soa: {
							required: false
						},
						transaction_date: {
							required: true
						},
						urc_doc: {
							required: true
						},
						source : {
							required: true,
							notEqual: 0
						},
						destination : {
							required: true,
							notEqual: 0
						},
						truck_id : {
							required: true,
							notEqual: 0
						},
						driver_id : {
							required: true,
							notEqual: 0
						},
						cs : {
							required: true
						},
						rate : {
							required: true
						}
					},
					messages: {
						soa: {
							required: "Please enter SOA No."
						},
						transaction_date: {
							required: "Please enter transaction date"
						},
						urc_doc: {
							required: "Please enter URC document"
						},
						source : {
							required: "Please select source",
							notEqual: "Please select source"
						},
						destination : {
							required: "Please select destination",
							notEqual: "Please select destination"
						},
						truck_id : {
							required: "Please select plate no.",
							notEqual: "Please select plate no."
						},
						driver_id : {
							required: "Please assign driver",
							notEqual: "Please assign driver"
						},
						cs : {
							required: "Please enter no. of cs."
						},
						rate : {
							required: "Please enter rate"
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);

			        <? if ($data['delivered'] == 1) { ?>
			        	$('#delivered').attr("checked","checked");
				        $('#tr_delivered_date').removeClass('hidden');
					    $('#tr_soa').removeClass('hidden');
					    $('#tr_urc_doc').removeClass('hidden');
			        <? } else { ?>
			        	$('#delivered').attr("checked","");
				        $('#tr_delivered_date').addClass('hidden');
						$('#tr_soa').addClass('hidden');
						$('#tr_urc_doc').addClass('hidden');
			        <? }?>
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=manage","GET");
			    });

			    $('#delivered').click(function() {
			    	if ($(this).attr("checked") == 'checked'){
					    $('#tr_delivered_date').removeClass('hidden');
					    $('#tr_soa').removeClass('hidden');
					    $('#tr_urc_doc').removeClass('hidden');

					    // TODO: validate true on active (not working)
					    $("#addForm").validate({
							rules: {
								soa: {
									required: true
								}
							},
							messages: {
								soa: {
									required: "Please enter SOA No."
								}
							}
					    });	
						
			    	} else {
						$('#tr_delivered_date').addClass('hidden');
						$('#tr_soa').addClass('hidden');
						$('#tr_urc_doc').addClass('hidden');
					}
				});
			});
            </script>
            <?