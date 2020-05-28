    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small box -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-xs-12">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>Rp. <?php echo number_format($amountYatim); ?></h3>
                        <p>Yatim</p>
                    </div>
                    <a href="investment/list/yatim" class="small-box-footer">More Info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-xs-12">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>Rp. <?php echo number_format($amountInvestment); ?></h3>
                        <p>Investment</p>
                    </div>
                    <a href="investment/portfolio" class="small-box-footer">More Info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Content -->
    	<div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div id="chart-transactions"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Last Transaction</h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <table id="datatable-last-transaction" class="table table-bordered table-striped table-hovered">
                                <thead>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Rp</th>
                                    <th class="text-center"></th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->