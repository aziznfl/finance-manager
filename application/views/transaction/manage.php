    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage <small>Transaction</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-4">
                <div class="box box-primary">
                    <div class="box-body">
                        <form class="form" action="#">
                            <div class="form-group">
                                <label>Date *</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                    <input name="date" class="form-control datetimepicker">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Amount *</label>
                                <div class="input-group">
                                    <div class="input-group-addon">Rp.</div>
                                    <input type="number" name="amount" class="form-control" value="0" style="text-align: right">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Category *</label>
                                <select name="category" class="form-control select2">
                                    <option></option>
                                    <?php
                                        foreach ($categories as $category) {
                                            echo "<option>".ucfirst($category->category_name)."</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input type="number" name="description" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="number" name="location" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Tag</label>
                                <input type="number" name="tag" class="form-control">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->