
        	<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=add-materials" id="addForm">
        	<div style="width:100%;" align="left">
        		<div style="width:100%;" align="left">
                	<input name="back" id="back" type="button" value="Back" class="back button" />
                </div>
                
                <div class="spacer_20 clean"><!-- SPACER --></div>
                
            	<div class="table_title" align="center">
            		<div align="left" class="px_16 float_left table_title_header">Materials : Add Material</div>
				</div>
            	<table width="100%" border="0" cellpadding="5" cellspacing="0">
            		<tr style="background-color:#D7D7D7;" class="line_20">
            			<th class="table_solid_bottom darkgray unselectable pad_left_15" align="left">Information:</th>
            		</tr>
            	</table>

                <table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
                	<tr>
                    	<td width="220" class="pad_left_15">Item Code: </td>
                        <td><input id="material" name="material" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td width="420"><label for="material" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Description: </td>
                        <td><input id="description" name="description" type="text" class="inputtext default_inputtext" maxlength="50" value="" /></td>
                        <td ><label for="description" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Gross Weight: </td>
                        <td><input id="gross_weight" name="gross_weight" type="text" class="inputtext default_inputtext" maxlength="50" value="0.00" /></td>
                        <td ><label for="gross_weight" generated="false" class="error"></label></td>
                    </tr>
                    <tr>
                    	<td class="pad_left_15">Volume: </td>
                        <td><input id="volume" name="volume" type="text" class="inputtext default_inputtext" maxlength="50" value="0.000" /></td>
                        <td ><label for="volume" generated="false" class="error"></label></td>
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
						material : {
							required: true,
							isNumbers: true,
							remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=get-unique-material&id=0" 
						},
						description : {
							required: true
						},
						gross_weight : {
							required: true,
							isNumeric: true
						},
						volume : {
							required: true,
							isNumeric: true
						}
					},
					messages: {
						material : {
							required: "Please provide material no.",
							isNumeric: "Please enter numeric input.",
							remote: "Material no. already exist."
						},
						description : {
							required: "Please provide description."
						},
						gross_weight : {
							required: "Please provide gross weight.",
							isNumeric: "Please enter numeric input."
						},
						volume : {
							required: "Please provide volume.",
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
			        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=materials","GET");
			    });
			});
            </script>
        <?