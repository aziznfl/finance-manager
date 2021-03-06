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
                        <li id="tr" class="active" data-tab="tr"><a href="javascript:void(0)">Transaction</a></li>
                        <li id="iv" data-tab="iv"><a href="javascript:void(0)">Investment</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tr-transaction">
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
                                    <label>Location</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <span class="fa fa-map-marker"></span>
                                        </div>
                                        <?php echo form_input($location); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <?php echo form_input($description); ?>
                                </div>
                                <div class="form-group">
                                    <label>Tag</label>
                                    <?php echo form_input($tag); ?>
                                </div>
                                <div class="form-group no-margin" style="padding-top: 8px">
                                    <button class="btn btn-primary btn-flat btn-block"><span class="fa fa-plus"></span>&nbsp;&nbsp;Save Transaction</button>
                                </div>
                                <?php if ($id != 0 || $id != "") { ?>
                                <hr/>
                                <div class="form-group no-margin" style="padding-top: 8px">
                                    <a class="btn btn-warning btn-flat btn-block" href="<?php echo base_url("transaction/delete?is_deleted=1&id=".$id); ?>">
                                        <span class="fa fa-trash"></span>&nbsp;&nbsp;Delete Transaction
                                    </a>
                                </div>
                                <div class="form-group no-margin" style="padding-top: 8px">
                                    <a class="btn btn-danger btn-flat btn-block" href="<?php echo base_url("transaction/delete?id=".$id); ?>">
                                        <span class="fa fa-remove"></span>&nbsp;&nbsp;Delete Permanently
                                    </a>
                                </div>
                            <?php }
                            echo form_close(); ?>
                        </div>
                        <div id="iv-transaction">
                            <?php echo form_open(base_url()."transaction/manageInvestment", 'class="form"', $form_hidden); ?>
                                <div class="form-group">
                                    <label>Date *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        <?php echo form_input($date_iv); ?>
                                    </div>
                                </div>
                                <div id="input-category-investment" class="form-group">
                                    <label>Category *</label>
                                    <select class="form-control select2" name="category_iv">
                                        <option></option>
                                        <?php
                                        foreach ($category_investment as $category) {
                                            $dataUnit = "";
                                            $selectCategory = "";
                                            if ($category["unit"] != null) $dataUnit = " data-unit='".$category["unit"]."'";
                                            if ($category["category_id"] == $category_iv) $selectCategory = " selected";
                                            echo "<option value=".$category["category_id"].$dataUnit.$selectCategory.">".ucwords($category["category_name"])."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Amount *</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">Rp.</div>
                                        <?php echo form_input($amount_iv); ?>
                                    </div>
                                </div>
                                <div class="form-group hide" id="input-value">
                                    <label>Value</label>
                                    <div class="input-group">
                                        <input type="number" name="value" class="form-control text-right" value="0" step="0.001" />
                                        <div id="label-unit-investment-category" class="input-group-addon">-</div>
                                    </div>
                                </div>
                                <div class="form-group hide" id="input-type">
                                    <input type="radio" name="type" value="outcome" checked="checked">&nbsp;&nbsp;&nbsp;<span style="margin-right: 16px;">Outcome</span>
                                    <input type="radio" name="type" value="income">&nbsp;&nbsp;&nbsp;<span style="margin-right: 16px;">Income</span>
                                    <input type="radio" name="type" value="done">&nbsp;&nbsp;&nbsp;Done
                                </div>
                                <div class="form-group">
                                    <label>Manager</label>
                                    <?php echo form_input($manager); ?>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <?php echo form_input($description_iv); ?>
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