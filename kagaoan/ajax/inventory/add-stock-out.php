		<?php
			$select = new Select();
			$select_inventory =  $select->option_query(
				'inventory', 					// table name
				'stock_id',  					// name='$name' 
				'stock_id', 					// id='$id'
				'id',							// value='$value'
				'name',							// option name
				'0',							// default selected value
				'active = "1"',					// query condition(s)  
				'name',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Select stock item...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
			$select_plate = $select->option_query(
				'truck', 						// table name
				'truck_id',  					// name='$name' 
				'truck_id', 					// id='$id'
				'id',							// value='$value'
				'plate',						// option name
				'0',							// default selected value
				'active = "1"',					// query condition(s)  
				'plate',						// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Select plate...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
			
			$option_name = array('firstname','lastname');
			$select_personnel = $select->option_query(
				'personnel', 					// table name
				'personnel_id',  				// name='$name' 
				'personnel_id', 				// id='$id'
				'id',							// value='$value'
				$option_name,					// option name
				'0',							// default selected value
				'active = "1"',					// query condition(s)  
				'firstname',					// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Select personnel...',			// default null option name 'Choose option...'	
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
				    
				$("#requisition_date").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false
		    	});
			});
			
			$('#stock_id').change(function() {
					var stock_id = $(this).val(),
						description = $('#description'),
						brand = $('#brand'),
						price = $('#price'),
						stock = $('#stock'),
						stock_in = $('#stock_in');
						
					if (stock_id != 0){
						$.getJSON('<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-stock-id&stock_id='+stock_id, function(data){
	    					description.html(data.description);
							brand.html(data.brand);
							price.html(data.price + ' per ' + data.unit);
							stock.html(data.stock);
							stock_in.val(data.stock);
						});
					} else {
						description.html('');
						brand.html('');
						price.html('0.00');
						stock.html('0');
						stock_in.val(0);
					}
				});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=add-stock-out" id="addForm">
            <input type="hidden" id="stock_in" value="0" />
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Add Stock Out</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Details:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                    <tr>
                    	<td width="220" class="pad_left_15">Plate: </td>
                        <td><?=$select_plate?></td>
                        <td width="420"><label for="truck_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Requisition Date: </td>
                        <td><input id="requisition_date" name="requisition_date" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=date("Y-m-d")?>" /></td>
                        <td><label for="requisition_date" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Inventory: </td>
                        <td><?=$select_inventory?></td>
                        <td><label for="stock_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Description: </td>
                        <td><span id="description"></span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Brand: </td>
                        <td><span id="brand"></span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Price: </td>
                        <td>&#8369; <span id="price">0.00</span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Stock: </td>
                        <td><span id="stock">0</span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Quantity: </td>
                        <td><input id="qty" name="qty" type="text" class="inputtext default_inputtext" maxlength="50" value="1" /></td>
                        <td><label for="qty" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Released by: </td>
                        <td><?=$select_personnel?></td>
                        <td><label for="personnel_id" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Add" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#addForm").validate({
					rules: {
						truck_id: {
							required: true,
							notEqual: 0
						},
						requisition_date : {
							required: true
						},
						stock_id : {
							required: true,
							notEqual: 0
						},
						qty : {
							required: true,
							notEqual: 0,
							isNumeric: true,
							lessThan: $('#stock_in')
						}
					},
					messages: {
						truck_id: {
							required: "Please select plate no.",
							notEqual: "Please select plate no."
						},
						requisition_date : {
							required:  "Please requisition date."
						},
						stock_id : {
							required: "Please select stock item.",
							notEqual: "Please select stock item."
						},
						qty : {
							required: "Please enter quantity.",
							notEqual: "Please enter quantity.",
							isNumeric: "Please enter numeric value.",
							lessThan: "No stock available."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);
					
					var description = $('#description'),
						brand = $('#brand'),
						price = $('#price'),
						stock = $('#stock'),
						stock_in = $('#stock_in');
						
					description.html('');
					brand.html('');
					price.html('0.00');
					stock.html('0');
					stock_in.val(0);
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=stock-out","GET");
			    });
			});
            </script>
            <?