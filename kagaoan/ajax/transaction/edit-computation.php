		<?php
			if(empty($_GET['shipment_id']) || !is_numeric($_GET['shipment_id'])):
				redirect(0,__ROOT__."/index.php?file=panel&panel=transaction&section=shipment");
				exit();
			endif;
		
			$data = $connect->single_result_array("SELECT * FROM computation WHERE id = '{$id}'");
			
			$shipment = $connect->single_result_array("
					SELECT
						t1.*,
						t2.plate,
						t3.location AS source_name,
						t4.location AS destination_name
			
					FROM shipment AS t1
						LEFT JOIN truck AS t2 ON t1.truck_id = t2.id
						LEFT JOIN location AS t3 ON t1.source = t3.id
						LEFT JOIN location AS t4 ON t1.destination = t4.id
					WHERE t1.id = '{$shipment_id}'");
			
			$select = new Select();
			$option_name = array('material','description');
			$select_material =  $select->option_query(
				'materials', 					// table name
				'material_id',  				// name='$name' 
				'material_id', 					// id='$id'
				'id',							// value='$value'
				'description',					// option name
				$data['material_id'],			// default selected value
				'active = "1"',					// query condition(s)  
				'description',					// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose item...',				// default null option name 'Choose option...'	
				'0'								// select type 1 = multiple or 0 = single
			);
		?>
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-computation" id="editForm">
        	<input type="hidden" name="id" value="<?=$id?>" />
        	<input type="hidden" name="shipment_id" value="<?=$shipment_id?>" />
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Shipment : Edit Item</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Item Information:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="220" class="pad_left_15 line_30">Shipment No.: </td>
                        <td><?=$shipment['shipment']?></td>
                        <td width="420"></td>
                    </tr>
                	<tr>
                    	<td class="pad_left_15">Item: </td>
                        <td><?=$select_material?></td>
                        <td><label for="material_id" generated="false" class="error"></label></td>
                    </tr>
                	<tr>
                    	<td class="pad_left_15">No of case(s): </td>
                        <td><input id="cases" name="cases" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$data['case']?>" /></td>
                        <td><label for="cases" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="update" type="submit" value="Update Item" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#editForm").validate({
					rules: {
						material_id: {
							required: true,
							notEqual: 0
						},
						cases : {
							required: true,
							isNumeric: true
						}
					},
					messages: {
						material_id: {
							required: "Please select item.",
							notEqual: "Please select item."
						},
						cases : {
							required: "Please provide no. of case(s)",
							isNumeric: "Please enter numeric input."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=computation&id=<?=$shipment_id?>","GET");
			    });
			});
            </script>
        <?