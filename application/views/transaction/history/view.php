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
								<table id="datatable-month-transaction" class="table table-bordered table-striped table-hover" style="cursor: pointer;">
									<thead>
										<th width="50">No</th>
										<th class="text-center">Detail</th>
										<th class="text-center">Rp.</th>
										<th class="text-center"></th>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /.content -->

<!-- .modal -->
<div id="modal-transaction-detail" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="exampleModalLabel">Transaction Detail</h4>
			</div>
			<div class="modal-body">
				<div>
					<span id="modal-detail-transaction-date" class="text-secondary"></span>
				</div>
				<div>
					<div id="modal-detail-transaction-category"></div>
					<p id="modal-detail-transaction-description"></p>
				</div>
				<table id="modal-detail-transaction-item-table" class="table table-bordered table-hovered table-striped no-margin">
				</table>
			</div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>
<!-- /.modal -->
