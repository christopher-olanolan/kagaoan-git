<?php 
	$table = str_replace('-', '_', $setting);
	$title = str_replace('-', ' ', $setting);
	$data = $connect->single_result_array("SELECT * FROM {$table} WHERE id = '{$id}'");
	
	switch ($setting):
		case "personnel-type":
		case "deduction-type":
			$name = $data['type_name'];
		break;	
		case "location":
			$name = $data['location'];
		break;	
		case "brand":
			$name = $data['brand_name'];
		break;	
	endswitch;
?>

<form method="post" enctype="multipart/form-data" action="<?=__ROOT__?>/index.php?file=action&action=edit-setting" id="editForm">

<input type="hidden" name="id" value="<?=$data['id']?>" />
<input type="hidden" name="setting" value="<?=$setting?>" />
<input type="hidden" name="table" value="<?=$table?>" />
<input type="hidden" name="title" value="<?=$title?>" />

<div style="width:100%;" align="left">
	<div style="width:100%;" align="left">
		<input name="back" id="back" type="button" value="Back" class="back button" />
	</div>
                
	<div class="spacer_20 clean"><!-- SPACER --></div>
                
    <div class="table_title" align="center">
    	<div align="left" class="px_16 float_left table_title_header">Settings : EDIT <?=$title?></div>
	</div>	
	<table width="100%" border="0" cellpadding="0" cellspacing="5" class="table_solid_bottom table_solid_left table_solid_right radius_bottom_10">
		<tr>
			<td width="120" class="pad_left_15">Name: </td>
			<td><input id="name" name="name" type="text" class="inputtext default_inputtext" maxlength="50" value="<?=$name?>" /></td>
			<td width="420"><label for="name" generated="false" class="error"></label></td>
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
			name : {
				required: true,
				remote: "<?=__ROOT__?>/index.php?file=ajax&ajax=<?=$ajax?>&control=get-setting&setting=<?=$setting?>&id=<?=$data['id']?>" 
			}
		},
		messages: {
			name : {
				required: "Please provide a name.",
				remote: "Name already in use."
			}
		},
		onkeyup: false,
  		onblur: true
	});
	
	$('input[type="reset"]').click(function(){
        clearForm(this.form);
    });
			    
    $('.back').click(function(){
        ajaxLoad("<?=__ROOT__?>/index.php?file=<?=$file?>&ajax=<?=$ajax?>&control=manage&setting=<?=$setting?>","GET");
    });
});
</script>