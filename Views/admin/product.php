<form action='' method='post'>
<input type="hidden" name="action" value="edit_product"> 
<input type="hidden" name="id" <?php echo "value='".$product->id."'" ?>"> 
<table class="table table-hover col-md-6">
	<tr class="bg-primary" style="background-color: #337ab7" ><td colspan="2">Product Details</th></tr>
	<tr ">
		<td>Product name</td>
		<td><input type="text" name="name" <?php echo "value='".$product->name."'" ?>></td>
	</tr>
	<tr ">
		<td>Price</td>
		<td><input type="text" name="price" <?php echo "value='".$product->price."'" ?>></td>
	</tr>
	<tr ">
		<td>Category</td>
		<td>
			<select name="parent_id">
			<?php
			 foreach ($this->cat_list as $key => $value) { 
					echo "<option value='".$key."'' ".($_REQUEST['parent_id'] == $key ? "selected='selected'" : '' ).">".$value."</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr ">
		<td>Description:</td>
		<td><input type="textarea" name="description" <?php echo "value='".$product->description."'" ?>></td>
	</tr>
</table>
<input class="btn" type="submit" value="save">
</form>