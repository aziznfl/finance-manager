<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Transaction History</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li id="month" class="active" data-tab="month"><a href="javascript:void(0)">Filter by Month</a></li>
            <li id="Category" data-tab="category"><a href="javascript:void(0)">Filter by Category</a></li>
        </ul>
        <div class="tab-content">
            <div id="month-tab">
		        <div class="box-body">
					<div class="form-horizontal">
						<div class="card-box">
						<?php
							foreach ($total_month_transaction->result() as $trans) {
								$time = strtotime($trans->month.'/01/'.$trans->year);
								$date = date("M Y", $time);
								?>
								<div class="card-view" id="card-<?php echo $trans->year."-".$trans->month; ?>" onclick="changeDate(<?php echo $trans->year.", ".$trans->month?>)">
									<h4 class="card-title"><?php echo $date; ?></h4>
									<div class="card-body">
										<h2><b>Rp. <?php echo number_format($trans->total_monthly); ?></b></h2>
										<div><?php echo number_format($trans->count_monthly); ?> item(s)</div>
									</div>
								</div>
						<?php } ?>
					</div>
					<div class="borderless"></div>
					<div class="row">
						<div class="col-md-4">
							<div class="table-responsive" style="margin-bottom: 32px;">
								<table id="datatable-top-transaction" class="table table-striped table-hover" style="cursor: pointer;">
									<thead>
										<th width="10">No</th>
										<th>Category</th>
										<th class="text-right">Rp. (%)</th>
									</thead>
								</table>
							</div>
						</div>
						<div class="col-md-8">
							<h3 style="margin: 0 0 20px;">
								List Transaction
								<small class="pull-right">
									<a class="btn btn-primary btn-sm" id="buttonAddTransaction">
										<i class="fa fa-plus"></i> Add Transaction
									</a>
								</small>
							</h3>
							<div class="table-responsive">
								<table id="datatable-month-transaction" class="table table-bordered table-striped table-hover">
									<thead>
										<th width="10">No</th>
										<th class="text-center" width="200">Date</th>
										<th class="text-center">Category</th>
										<th class="text-center">Rp.</th>
										<th class="text-center"></th>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="category-tab" class="hide">
				<div class="row">
					<div class="col-md-4">
						<div class="table-responsive" style="margin-bottom: 32px;">
							<table id="datatable-top-transaction" class="table table-bordered table-striped table-hover" style="cursor: pointer;">
								<thead>
									<th class="text-center">No</th>
									<th class="text-center">Category</th>
									<th class="text-center">Rp. (%)</th>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /.content -->
