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
                        <li class="active" data-tab="default"><a>Transaction</a></li>
                        <li data-tab="investment"><a>Investment</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="default-transaction">
                            <?php echo form_open(base_url()."transaction/manageTransaction", 'class="form"', $form_hidden); ?>
                                <div class="form-group">
                                    <label>Date *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <?php echo form_input($date); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Category *</label>
                                    <?php echo form_dropdown('category', $category["list"], $category["value"], $category["tag"]); ?>
                                </div>
                                <div class="form-group">
                                    <label>Amount *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">Rp.</div>
                                        <?php echo form_input($amount); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <?php echo form_input($description); ?>
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
                            <?php echo form_close(); ?>
                        </div>
                        <div id="investment-transaction" class="hide">
                            <?php echo form_open("../add", 'class="form"', $form_hidden); ?>
                            <!-- date, category, amount, value, invest, description -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->