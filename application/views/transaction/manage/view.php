<!-- Content Header (Page header) -->
<section class="content-header"></section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <form class="form" method="post" accept-charset="utf-8">
            <div class="col-sm-12 col-md-4">
                <div class="box box-primary">
                    <div class="box-body">
                        <input type="text" name="transactionId" class="hidden" value="">
                        <div class="form-group">
                            <label>Date *</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                <input type="text" name="date" value="<?php echo date('Y-m-d H:i:s'); ?>" class="form-control datetimepicker" placeholder="Now">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" class="form-control select2"></select>
                        </div>
                        <div class="form-group">
                            <label>Amount *</label>
                            <div class="input-group">
                                <div class="input-group-addon">Rp.</div>
                                <input type="number" name="amount" value="0" class="form-control text-right">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Location</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="fa fa-map-marker"></span>
                                </div>
                                <input type="text" name="location" value="" class="form-control" placeholder="Location">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" value="" class="form-control" placeholder="Description">
                        </div>
                        <div class="form-group">
                            <label>Tag</label>
                            <input type="text" name="tag" value="" class="form-control" placeholder="Tag">
                        </div>
                        <div class="form-group no-margin hide" style="padding-top: 8px">
                            <button class="btn btn-primary btn-flat btn-block"><span class="fa fa-plus"></span>&nbsp;&nbsp;Save Transaction</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-8">
                <div class="box box-success">
                    <div class="box-header with-border">
                        Item List
                    </div>
                    <div class="box-body">
                        <table class="table" id="table-list-items">
                            <thead>
                                <th width="250">Item</th>
                                <th class="text-right" width="175">Price</th>
                                <th class="text-center" width="100">Qty</th>
                                <th class="text-right" width="100">Total</th>
                                <th class="text-center" width="50">Action</th>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <th colspan="2" data-tag="counter-text"></th>
                                <th class="text-right">Total</th>
                                <th class="text-right" data-tag="total-text"></th>
                                <th></th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<!-- /.content -->
