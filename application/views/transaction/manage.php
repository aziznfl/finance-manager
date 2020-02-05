    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage <small>Transaction</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12 col-md-4">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a>Transaction</a></li>
                        <li><a>Investment</a></li>
                    </ul>
                    <div class="tab-content">
                        <form class="form" action="<?php echo base_url('transaction/add') ?>" method="POST">
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
                                            echo "<option value='".$category['category_id']."'>".ucfirst($category["category_name"])."</option>";
                                            foreach ($category["child"] as $child) {
                                                echo "<option value='".$child['category_id']."'>- ".ucfirst($child["category_name"])."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input name="description" class="form-control" placeholder="Description">
                            </div>
                            <div class="form-group hide">
                                <label>Location</label>
                                <input name="location" class="form-control">
                            </div>
                            <div class="form-group hide">
                                <label>Tag</label>
                                <input name="tag" class="form-control">
                            </div>
                            <div class="form-group no-margin" style="padding-top: 8px">
                                <button id="input-form-transaction" class="btn btn-primary btn-flat btn-block">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->