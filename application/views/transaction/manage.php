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
                        <li id="tr" class="active" data-tab="default"><a href="javascript:void(0)">Transaction</a></li>
                        <li id="iv" data-tab="investment"><a href="javascript:void(0)">Investment</a></li>
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
                                    <button class="btn btn-primary btn-flat btn-block">Save Transaction</button>
                                </div>
                            <?php echo form_close(); ?>
                        </div>
                        <div id="investment-transaction">
                            <?php echo form_open(base_url()."transaction/manageInvestment", 'class="form"'); ?>
                                <div class="form-group">
                                    <label>Date *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <?php echo form_input($date); ?>
                                    </div>
                                </div>
                                <div id="input-category-investment" class="form-group">
                                    <label>Category *</label>
                                    <?php // echo form_dropdown('category', $category_investment["list"], $category_investment["value"], $category_investment["tag"]); ?>
                                    <select class="form-control select2" name="category">
                                        <option></option>
                                        <?php
                                        foreach ($category_investment as $category) {
                                            $dataUnit = "";
                                            if ($category["unit"] != null) $dataUnit = " data-unit='".$category["unit"]."'";
                                            echo "<option value=".$category["category_id"] . $dataUnit.">".ucwords($category["category_name"])."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Amount *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">Rp.</div>
                                        <?php echo form_input($amount); ?>
                                    </div>
                                </div>
                                <div id="input-value" class="form-group hide">
                                    <label>Value</label>
                                    <div class="input-group">
                                        <input type="number" name="value" class="form-control text-right" value="0" step="0.001" />
                                        <div id="label-unit-investment-category" class="input-group-addon">-</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Manager</label>
                                    <input type="text" name="manager" class="form-control" placeholder="Investment Manager">
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <?php echo form_input($description); ?>
                                </div>
                                <div class="form-group no-margin" style="padding-top: 8px">
                                    <button class="btn btn-primary btn-flat btn-block">Save Investment</button>
                                </div>
                            <?php echo form_close(); ?>
                            <!-- date, category, amount, value, invest, description -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->