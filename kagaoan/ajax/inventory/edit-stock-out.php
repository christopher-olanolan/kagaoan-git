		<?php
			$data = $connect->single_result_array("
				SELECT
					t1.*,
					
					t2.description,
					t2.unit_price,
					t2.unit,
					t2.stocks,
					
					t3.brand_name
				FROM 
					requisition AS t1
					LEFT JOIN inventory AS t2 ON t1.stock_id = t2.id
					LEFT JOIN brand AS t3 ON t2.brand = t3.id
				WHERE 
					t1.id = $id
			");
			
			$select = new Select();
			$select_inventory =  $select->option_query(
				'inventory', 					// table name
				'stock_id',  					// name='$name' 
				'stock_id', 					// id='$id'
				'id',							// value='$value'
				'name',							// option name
				$data['stock_id'],				// default selected value
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
				$data['truck_id'],				// default selected value
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
				$data['personnel_id'],			// default selected value
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
						truck_id = $('#truck_id option:selected').val();
						description = $('#description'),
						brand = $('#brand'),
						price = $('#price'),
						stock = $('#stock'),
						stock_in = $('#stock_in'),
						
						orig_stock = $('#orig_stock').val(),
						orig_qty = $('#orig_qty').val(),
						orig_stock_id = $('#orig_stock_id').val(),
						orig_truck_id = $('#orig_truck_id').val();
						
					if (stock_id != 0){
						$.getJSON('<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-stock-id&stock_id='+stock_id, function(data){
							new_stock = (orig_truck_id != truck_id) && (stock_id == orig_stock_id) ? parseInt(data.stock) +  parseInt(orig_qty):data.stock;
							
	    					description.html(data.description);
							brand.html(data.brand);
							price.html(data.price + ' per ' + data.unit);
							stock.html(new_stock);
							stock_in.val(new_stock);
						});
					} else {
						description.html('');
						brand.html('');
						price.html('0.00');
						stock.html('0');
						stock_in.val(0);
					}
				});
				
				$('#truck_id').change(function() {
					var truck_id = $(this).val(),
						stock_id = $('#stock_id option:selected').val();
						description = $('#description'),
						brand = $('#brand'),
						price = $('#price'),
						stock = $('#stock'),
						stock_in = $('#stock_in'),
						
						orig_stock = $('#orig_stock').val(),
						orig_qty = $('#orig_qty').val(),
						orig_stock_id = $('#orig_stock_id').val(),
						orig_truck_id = $('#orig_truck_id').val();
					
					if (stock_id == orig_stock_id){
						$.getJSON('<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-stock-id&stock_id='+stock_id, function(data){
							new_stock = orig_truck_id != truck_id ? parseInt(data.stock) +  parseInt(orig_qty):data.stock;
							
	    					description.html(data.description);
							brand.html(data.brand);
							price.html(data.price + ' per ' + data.unit);
							stock.html(new_stock);
							stock_in.val(new_stock);
						});
					}
				});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-stock-out" id="editForm">
            <input type="hidden" id="id" name="id" value="<?=$id?>" />
            <input type="hidden" id="stock_in" name="stock_in" value="<?=$data['stocks']?>" />
            <input type="hidden" id="orig_stock" name="orig_stock" value="<?=$data['stocks']?>" />
            <input type="hidden" id="orig_qty" name="orig_qty" value="<?=$data['qty']?>" />
            <input type="hidden" id="orig_stock_id" name="orig_stock_id" value="<?=$data['stock_id']?>" />
            <input type="hidden" id="orig_truck_id" name="orig_truck_id" value="<?=$data['truck_id']?>" />
            
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Update Stock Out</div>
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
                        <td><input id="requisition_date" name="requisition_date" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=$data['requisition_date']?>" /></td>
                        <td><label for="requisition_date" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Inventory: </td>
                        <td><?=$select_inventory?></td>
                        <td><label for="stock_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Description: </td>
                        <td><span id="description"><?=$data['description']?></span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Brand: </td>
                        <td><span id="brand"><?=$data['brand_name']?></span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Price: </td>
                        <td>&#8369; <span id="price"><?=$data['unit_price']?> per <?=$data['unit']?></span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Stock: </td>
                        <td><span id="stock"><?=$data['stocks']?></span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Quantity: </td>
                        <td><input id="qty" name="qty" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['qty']?>" /></td>
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
	                <input name="update" type="submit" value="Update" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#editForm").validate({
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
						
					description.html('<?=$data['description']?>');
					brand.html('<?=$data['brand_name']?>');
					price.html('<?=$data['unit_price']?> per <?=$data['unit']?>');
					stock.html('<?=$data['stocks']?>');
					stock_in.val('<?=$data['stocks']?>');
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=stock-out","GET");
			    });
			});
            </script>
            <?