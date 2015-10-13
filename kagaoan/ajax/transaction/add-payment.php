		<?php
			$select = new Select();
			$option_name = array('soa','transaction_date', 'source', 'destination', 'truck_id');
			$select_transaction =  $select->option_query(
				'transaction', 					// table name
				'transaction_id',  				// name='$name'
				'transaction_id', 				// id='$id'
				'id',							// value='$value'
				$option_name,					// option name
				'0',							// default selected value
				'active = "1" AND soa != ""',	// query condition(s)
				'soa',							// 'order by' field name
				'ASC',							// sort order 'asc' or 'desc'
				'selectoption default_select',	// css class
				'Choose transaction...',		// default null option name 'Choose option...'
				'0'								// select type 1 = multiple or 0 = single
			);
		?>
			<script type="text/javascript">
			$(document).ready(function() {
				$("#payment_date").datepicker({
					alwaysSetTime: false,
					timepicker: false,
		    		dateFormat: "yy-mm-dd",
		    		showSecond: false,
		    		showMinute: false,
		    		showHour: false,
		    		showTime: false
		    	});
			});

			$('#transaction_id').change(function() {
				var transaction_id = $(this).val(),
					credit = $('#credit'),
					payment = $('#payed');
					credit_value = 0;
					payment_value = 0;
					
				if (transaction_id != 0){
					$.getJSON('<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-payment&transaction_id='+transaction_id, function(data){
						if (data.payment == null || data.payment == ''){
							payment.html('0.00');
							payment_value = 0;
						} else {
							payment.html(data.payment);
							payment_value = parseInt(data.payment);
						}

						$.getJSON('<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=get-credit&transaction_id='+transaction_id, function(data){
							var cs = parseInt(data.cs), rate = parseInt(data.rate);
							credit_value = (cs * rate) - payment_value;
							credit.html(credit_value);
						});
					});
				} else {
					credit.html('0.00');
					payment.html('0.00');
				}
			});
		    </script>
		    
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=add-payment" id="addForm">
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Add Payment</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Payment Details:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="120" class="pad_left_15">Transaction: </td>
                        <td><?=$select_transaction?></td>
                        <td width="320"><label for="transaction_id" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Credit: </td>
                        <td>&#8369; <span id="credit">0.00</span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15 line_30">Previous Payment: </td>
                        <td>&#8369; <span id="payed">0.00</span></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">URC Document: </td>
                        <td><input id="urc_doc" name="urc_doc" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="urc_doc" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Payment Date: </td>
                        <td><input id="payment_date" name="payment_date" type="text" class="inputtext default_inputtext datepicker" maxlength="50" value="<?=date("Y-m-")?>01" /></td>
                        <td><label for="payment_date" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Payment: </td>
                        <td><input id="payment" name="payment" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td><label for="payment" generated="false" class="error"></label></td>
                    </tr>
                </table>
                    
            	<div class="spacer_20 clean"><!-- SPACER --></div>
            	
                <div style="width:100%;" align="left">
	                <input name="back" id="back" type="button" value="Back" class="back button" />
	                <input name="clear" type="reset" value="Reset Form" class="button" />
	                <input name="insert" type="submit" value="Add Payment" class="button" />
                </div>
            </div>
            </form>
            
            <script type="text/javascript">
            $(document).ready(function() {
            	$("#addForm").validate({
					rules: {
						transaction_id : {
							required: true,
							notEqual: 0
						},
						urc_doc : {
							required: true
						},
						payment_date: {
							required: true
						},
						payment : {
							required: true
						}
					},
					messages: {
						transaction_id : {
							required: "Please select transaction.",
							notEqual: "Please select transaction."
						},
						urc_doc: {
							required: "Please enter URC document."
						},
						payment_date: {
							required: "Please enter payment date."
						},
						payment: {
							required: "Please enter payment."
						}
					},
					onkeyup: false,
			  		onblur: true
				});
				
				$('input[type="reset"]').click(function(){
			        clearForm(this.form);
			    });
			    
			    $('.back').click(function(){
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=payment","GET");
			    });
			});
            </script>
            <?