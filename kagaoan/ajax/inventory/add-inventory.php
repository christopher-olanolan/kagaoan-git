		<?php
			$select = new Select();
			$select_brand =  $select->option_query(
				'brand', 						// table name
				'brand',  						// name='$name' 
				'brand', 						// id='$id'
				'id',							// value='$value'
				'brand_name',					// option name
				'0',							// default selected value
				'active = "1"',					// query condition(s)  
				'brand_name',					// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose brand...',				// default null option name 'Choose option...'	
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
				    
				$("#purchase_date").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false
		    	});

				$('#add-brand').click(function() {					
					if ($('#add_brand_container').hasClass('hidden')){
						$('#add_brand_container').removeClass('hidden');
					} else {
						$('#add_brand_container').addClass('hidden');
					}
				});

				$('#submit-add-brand').click(function() {
					var add_brand = $('#add_brand').val();
										
					if (add_brand != ''){
						$.getJSON("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=add-brand&brand_name="+ add_brand, function(data){
							populateOption(data);	    					
						});
					} else {
						$.noti('error', 'Please enter new brand.');
					}
				});
				
				function populateOption(data){
					var len = data.length;
					var option = "<option val='0'>Choose brand...</option>";
					
					if (len > 0){
    					for (x=0;x<len;x++){
    						selected = data[x].brand_name == $('#add_brand').val() ? "selected='selected'":"";
    						option += '<option ' + selected + ' value="' + data[x].id + '">' + data[x].brand_name + '</option>';
    					}
					}

					$('#brand').html(option);
					$('#add_brand').val('');
					$('#add_brand_container').addClass('hidden');
				}	
			});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=add-inventory" id="addForm">
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Add Inventory</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Inventory Details:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="120" class="pad_left_15">Name: </td>
                        <td><input id="name" name="name" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td width="420"><label for="name" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30" valign="top">Description: </td>
                        <td><textarea id="description" name="description" class="textarea default_textarea"></textarea></td>
                        <td><label for="description" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30" valign="top">Brand: </td>
                        <td>
                        	<?=$select_brand?>
                        	<input type="button" class="button small_button" value="+ Add Brand" id="add-brand">
							<span id="add_brand_container" class="hidden">
								<input id="add_brand" name="add_brand" type="text" class="inputtext default_inputtext marg_top_5" maxlength="50" value="" placeholder="Add new brand..." />
								<input type="button" class="button small_button marg_top_5" value="Submit" id="submit-add-brand">
							</span>
                        </td>
                        <td class="line_30" valign="top"><label for="brand" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Supplier: </td>
                        <td><input id="supplier" name="supplier" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="supplier" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Purchase Date: </td>
                        <td><input id="purchase_date" name="purchase_date" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=date("Y-m-d")?>" /></td>
                        <td><label for="purchase_date" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Price: </td>
                        <td><input id="unit_price" name="unit_price" type="text" class="inputtext default_inputtext" maxlength="50" value="1.00" /></td>
                        <td><label for="unit_price" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Unit: <i class="pt_8">i.e. (kg, lbs)</i> </td>
                        <td><input id="unit" name="unit" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="unit" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Stocks: </td>
                        <td><input id="stocks" name="stocks" type="text" class="inputtext default_inputtext" maxlength="50" value="1" /></td>
                        <td><label for="stocks" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Stock Limit: </td>
                        <td><input id="stock_limit" name="stock_limit" type="text" class="inputtext default_inputtext" maxlength="50" value="1" /></td>
                        <td><label for="stock_limit" generated="false" class="error"></label></td>
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
						name: {
							required: true,
							remote: {
								url: "<?=__ROOT__?>/index.php",
								type: "post",
								data: {
									file:"ajax",
									ajax:"<?=$ajax?>",
									control:"get-unique",
									id:"0",
									brand: function() {
							            return $("#brand").val();
						          	},
						          	unit_price: function() {
							            return $("#unit_price").val();
						          	}
								}
							} 
						},
						brand : {
							required: true,
							notEqual: 0,
							remote: {
								url: "<?=__ROOT__?>/index.php",
								type: "post",
								data: {
									file:"ajax",
									ajax:"<?=$ajax?>",
									control:"get-unique",
									id:"0",
									name: function() {
							            return $("#name").val();
						          	},
						          	unit_price: function() {
							            return $("#unit_price").val();
						          	}
								}
							}
						},
						unit_price : {
							required: true,
							remote: {
								url: "<?=__ROOT__?>/index.php",
								type: "post",
								data: {
									file:"ajax",
									ajax:"<?=$ajax?>",
									control:"get-unique",
									id:"0",
									name: function() {
							            return $("#name").val();
						          	},
						          	brand: function() {
							            return $("#brand").val();
						          	}
								}
							}
						},
						stocks : {
							required: true
						}
					},
					messages: {
						name: {
							required: "Please enter name",
							remote: "Inventory already exist."
						},
						brand : {
							required: "Please select brand",
							notEqual: "Please select brand",
							remote: "Inventory already exist."
						},
						unit_price : {
							required: "Please enter price",
							remote: "Inventory already exist."
						},
						stocks : {
							required: "Please enter stock"
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