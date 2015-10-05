<?php 
	$data = $connect->single_result_array("SELECT * FROM settings WHERE id = '{$id}'");
	$name = $data['display_name'];
	$value = $data['value'];
?>

<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-deduction-setting" id="editForm">

<input type="hidden" name="id" value="<?=$data['id']?>" />
<input type="hidden" name="name" value="<?=$data['name']?>" />
<input type="hidden" name="table" value="settings" />

<div style="width:100%;" align="left">
	<div style="width:100%;" align="left">
		<input name="back" id="back" type="button" value="Back" class="back button" />
	</div>
                
	<div class="spacer_20 clean"><!-- SPACER --></div>
                
    <div class="table_title" align="center">
    	<div align="left" class="px_16 float_left table_title_header">Settings : EDIT DEDUCTION SETTING</div>
	</div>	
	<table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
		<tr>
			<td width="120" class="pad_left_15 line_30">Name: </td>
			<td><?=$name?></td>
			<td width="420">&nbsp;</td>
		</tr>
		<tr>
			<td class="pad_left_15 line_30">Value: </td>
			<td><input id="value" name="value" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$value?>" /></td>
			<td><label for="value" generated="false" class="error"></label></td>
		</tr>
	</table>
                
    <div class="spacer_20 clean"><!-- SPACER --></div>
                
	<div style="width:100%;" align="left">
		<input name="back" id="back" type="button" value="Back" class="back button" />
		<input name="clear" type="reset" value="Reset Form" class="button" />
		<input name="update" type="submit" value="Save" class="button" />
	</div>
</div>
</form>

<script type="text/javascript">
$(document).ready(function() {
	$("#editForm").validate({
		rules: {
			value : {
				required: true 
			}
		},
		messages: {
			value : {
				required: "Please provide a precentage value."
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