<!-- Content Header (Page header) -->
<section class="content-header"></section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <form class="form" method="post" accept-charset="utf-8">
            <div class="col-sm-12 col-md-4">
                <div class="box box-primary">
                    <div class="box-body">
                        <input type="text" name="investment_id" class="hidden" value="">
                        <div class="form-group">
                            <label>Date *</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="date" value="<?php echo date('Y-m-d H:i:s'); ?>" class="form-control datetimepicker" placeholder="Now">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Instrument *</label>
                            <select name="instrument" class="form-control select2"></select>
                        </div>
                        <div class="form-group">
                            <label>Manager *</label>
                            <input type="text" name="manager" value="" class="form-control" placeholder="Manager">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" value="" class="form-control" placeholder="Description">
                        </div>
                        <div id="status-form" class="form-group">
                            <label>Status</label>
                            <div class="row">
                                <div class="col-xs-6">
                                    <input type="radio" name="status" value="income" checked> Income
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" name="status" value="done"> Done
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Amount *</label>
                            <div class="input-group">
                                <div class="input-group-addon">Rp.</div>
                                <input type="number" name="amount" value="0" class="form-control text-right">
                            </div>
                        </div>
                        <div class="form-group no-margin" style="padding-top: 8px">
                            <a id="submit" class="btn btn-primary btn-flat btn-block"><span class="fa fa-plus"></span>&nbsp;&nbsp;Save Investment</a>
                        </div>
                        <div id="remove-form" class="form-group no-margin" style="padding-top: 8px">
                            <a class="btn btn-warning btn-block btn-flat" onclick="removeTransaction()">
                                <span class="fa fa-trash"></span>&nbsp;&nbsp;Remove
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<!-- /.content -->
