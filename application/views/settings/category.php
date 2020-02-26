<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Settings</h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-9 col-offset-sm-12">
		    <div class="box box-primary">
		        <div class="box-body">
		        	<h4>List Category</h4>
		        	<p>Drag and sort category</p>
					<ul class="items">
						<?php foreach($categories as $category) {
							echo "<li class='item first-level'>".ucwords($category["category_name"])."</li>";
							foreach($category["child"] as $child) {
								echo "<li class='item second-level'>".ucwords($child["category_name"])."</li>";
							}
						} ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-offset-sm-12">
			<div class="box">
				<div class="box-header with-border">
					Add/Edit Category
				</div>
				<div class="box-body">
					<form method="POST" action="<?php echo base_url('api/addCategory'); ?>">
						<div class="form-group">
							<label>Name Category</label>
							<input type="text" name="name" class="form-control" placeholder="ex. Personal">
						</div>
						<div class="form-group">
							<label>Parent</label>
							<select name="parent" class="form-control select2">
								<option></option>
								<option value="1">- ROOT -</option>
								<?php foreach($categories as $category) {
									echo "<option value='".$category["category_id"]."'>".ucwords($category["category_name"])."</option>";
								} ?>
							</select>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary pull-right btn-flat">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
