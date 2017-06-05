<br/>
<form action='/magshop/' method='post'>
	<heading>
		<select name="category">
			<option selected='selected' disabled value=''>Categories</option>
			<option value=''>All</option>
			<?php
				foreach ($this->cat_list as $key => $value) { 
					echo "<option value='".$key."'>".$value."</option>";
				}
			?>
		</select> <input type='submit' value='Filter'>
	</heading>
	<h4>
		<label> Deep Search <input id="deep" type="checkbox" name="deep" value=1></label> <br>
	</h4>
	<h4>
		Categories Found: <?php echo count($categories)?>
	</h4>
</form>
<br/>
<table class="table" border=.4 cellpadding=0 cellspacing=10 style="width:100%">
<tr bgcolor=#f87820 style="color: white">
<td class="col-md-1"><br/><b>ID</b></td>
<td class="col-md-3"><br/><b>Name</b></td>
<td class="col-md-2"><br/><b>Parent Category</b></td>
<td class="col-md-2"><br/><b>Slug</b></td>
<td class="col-md-4"><br/><b>Commands</b></td>
</tr>
</table> 

<!--Display Categories -->
<div style="max-height: 43%; overflow-y: auto">
	<table class="table table-striped" border=.4 cellpadding=10 cellspacing=10 style="width:100%">

	<?php
		$i=0;
		foreach ($categories as $category) 
		{ ?>
			
			<tr valign=center>
			<td class="col-md-1"><form method='post' action="/magshop/category/<?php echo $category->slug?>"><input type="submit" value="<?php echo $category->id?>&nbsp;"></form></td>
			<form action='' method =post>
			<input type='hidden' name='id' value='<?php echo $category->id?>'>
			<input type="hidden" name="action" value="edit_category">
			<td class="col-md-3"><b><input type='text' name='name' value='<?php echo $category->name?>'></b></td>
			<td class="col-md-2">
				<select name="parent_id">
							<option <?php echo ($category->parent_id == null ? 'selected="selected"' : '')?> value=''>none</option>
							<?php
								foreach ($this->cat_list as $key => $value) { 
									echo "<option value='".$key."' ".($category->parent_id == $key ? 'selected="selected"' : '').">".$value."</option>";
								}
							?>
				</select>
			&nbsp;
			</td>
			<td class="col-md-2"><?php echo $category->slug?>&nbsp;</td>

			<td class="col-md-2"><input type =submit value='Save Changes'></td>
			</form>
			<td class="col-md-2">
			<form action='' method='post'>
			 <input type='hidden' name='id' value='<?php echo $category->id?>'>
			 <input type='hidden' name='parent_id' value='<?php echo $category->parent_id?>'>
			 <input type="hidden" name="action" value="delete_category">
				<input type =submit value='Delete Entry'>
			</form>
			</td>
			</tr>
			<?php
			$i++;
		}

		echo "<tr valign=bottom>";
	        echo "<td style='' bgcolor=#fb7922 colspan=7></td>";
	        echo "</tr>";


	?>

	</table>
</div>

<div class="col-md-4">
	<h2>Add Category</h2>

	<form action='' method=post>
	<input type="hidden" name="action" value="edit_category">
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>Name:</td><td><input type=text size=20 name=name></td></tr>
	<tr><td>Parent Category:</td>
		<td> <select name="parent_id">
						<option selected='selected' value=''>none</option>
						<?php
							foreach ($this->cat_list as $key => $value) { 
								echo "<option value='".$key."'>".$value."</option>";
							}
						?>
			</select></td></tr>
	<tr><td></td><td><input type=submit border=0 value="Submit"></td></tr>
	</table>
	</form>
</div>
<div class="col-md-4">
	<h2>Add Product</h2>

	<form action='' method=post>
	<input type="hidden" name="action" value="edit_product">
	<table border=0 cellpadding=0 cellspacing=0>
	<tr><td>Name:</td><td><input type=text size=20 name=name></td></tr>
	<tr><td>Price:</td><td><input type=text size=20 name=price></td></tr>
	<tr><td>Category:</td>
		<td> <select name="parent_id">
						<?php
							foreach ($this->cat_list as $key => $value) { 
								echo "<option value='".$key."'' ".(isset($_RESOURCE['category_id']) && $_RESOURCE['category_id'] == $key ? "selected='selected'" : '' ).">".$value."</option>";
							}
						?>
			</select></td></tr>
	<tr><td></td><td><input type=submit border=0 value="Submit"></td></tr>
	</table>
	</form>
</div>

<!-- Display products for current category -->

<?php 

if(isset($products) && count($products) > 0){
	?>
	<div>
	<h4>
		Products Found: <?php echo count($products)?>
	</h4>
	</div>
	<table class="table " border=.4 cellpadding=10 cellspacing=10 style="width:100%">
	<tr bgcolor=#f87820 style="color: white">		
		<td class="col-md-1"><br/><b>ID</b></td>
		<td class="col-md-2"><br/><b>Name</b></td>
		<td class="col-md-2"><br/><b>Description</b></td>
		<td class="col-md-1"><br/><b>Price</b></td>
		<td class="col-md-2"><br/><b>Category</b></td>
		<td class="col-md-1"><br/><b>Images</b></td>
		<td class="col-md-3" colspan=2><br/><b>Commands</b></td>		
	</tr>
	</table> 
	<div style="max-height: 65%; overflow-y: auto">
	<table class="table table-striped" border=.4 cellpadding=10 cellspacing=10 style="width:100%">

	<?php
		$i=0;
		foreach ($products as $product) 
		{

			?>
			
			<tr valign=center>
				<td class="col-md-1">
					<b><?php echo $product->id?></b>
				</td>
				<td class="col-md-2">
					<b><?php echo $product->name?></b>
				</td>
				<td class="col-md-2" style="overflow: auto">
					<b><?php echo $product->description?></b>
				</td>
				<td class="col-md-1">
					<?php echo $product->price?>&nbsp;
				</td>
				<td class="col-md-2">
					<?php echo $this->cat_list[$product->category_id]?>&nbsp;
				</td>
				<td class="col-md-1">
					<?php echo $product->images?>&nbsp;
				</td>
				<td class="col-md-1">
					<form action="/magshop/product/<?php echo $product->id?>">
					<input type="submit" value="EDIT&nbsp;"></form>
				</td>
				<td class="col-md-2">
					<form action='' method='post'>
					 <input type='hidden' name='category_id' value='<?php echo $product->category_id?>'>
					 <input type='hidden' name='id' value='<?php echo $product->id?>'>
					 <input type="hidden" name="action" value="delete_product">
						<input type =submit value='Delete Entry'>
					</form>
				</td>
			</tr>
			<?php
			$i++;
		}

		echo "<tr valign=bottom>";
	        echo "<td style='' bgcolor=#fb7922 colspan=8></td>";
	        echo "</tr>";


	?>

	</table>
	</div>
	<?php
	}

?>